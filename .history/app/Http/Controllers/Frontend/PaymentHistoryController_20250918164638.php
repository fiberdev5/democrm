<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;


class PaymentHistoryController extends Controller
{
    public function index(Request $request, $tenant_id)
    {
        // Sadece patron rollü kullanıcılar erişebilir
        abort_if(!auth()->user()->hasRole('Patron'), 403);

        $tenant = Tenant::where('id', $tenant_id)->first();
        
        // Eğer hiç parametre gönderilmemişse veya clear parametresi varsa, varsayılan son 1 ay aralığını kullan
        $isDefaultRequest = !$request->hasAny(['date_from', 'date_to', 'payment_method', 'status', 'type']) || $request->has('clear');
        
        // Filtreleme parametreleri - Varsayılan olarak son 1 ay
        $dateFrom = $isDefaultRequest ? now()->subMonth()->format('Y-m-d') : $request->get('date_from', now()->subMonth()->format('Y-m-d'));
        $dateTo = $isDefaultRequest ? now()->format('Y-m-d') : $request->get('date_to', now()->format('Y-m-d'));
        $paymentMethod = $request->get('payment_method');
        $status = $request->get('status');
        $type = $request->get('type', 'all'); // subscription, storage, all - varsayılan 'all'
        
        // Abonelik ödemeleri - Model ilişki kontrolü
        $subscriptionPayments = collect();
        if (method_exists($tenant, 'subscriptionPayments')) {
            $subscriptionPayments = $tenant->subscriptionPayments()
                ->when($dateFrom, function($query) use ($dateFrom) {
                    return $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
                })
                ->when($dateTo, function($query) use ($dateTo) {
                    return $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
                })
                ->when($paymentMethod, function($query) use ($paymentMethod) {
                    return $query->where('payment_method', $paymentMethod);
                })
                ->when($status, function($query) use ($status) {
                    return $query->where('status', $status);
                })
                ->when($type && $type !== 'all', function($query) use ($type) {
                    if ($type === 'storage') {
                        return $query->whereRaw('1=0'); // Hiç sonuç döndürme
                    }
                    return $query;
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($payment) {
                    return [
                        'id' => $payment->id,
                        'type' => 'subscription',
                        'type_label' => 'Abonelik',
                        'description' => $this->getSubscriptionPaymentDescription($payment),
                        'amount' => $payment->amount ?? 0,
                        'payment_method' => $payment->payment_method,
                        'status' => $payment->status,
                        'status_label' => $this->getStatusLabel($payment->status),
                        'invoice_path' => $payment->invoice_path ?? null,
                        'created_at' => $payment->created_at,
                        'paid_at' => $payment->paid_at ?? null,
                        'transaction_id' => $payment->transaction_id ?? null,
                        'gateway' => $payment->gateway ?? null,
                        'currency' => $payment->currency ?? 'TL',
                        'tenant_id' => $payment->tenant_id,
                        'subscription_id' => $payment->subscription_id ?? null,
                        'payment_id' => $payment->payment_id ?? null,
                    ];
                });
        }

        // Depolama satın almaları
        // Depolama satın almaları
$storagePurchases = collect();
if (method_exists($tenant, 'storagePurchases')) {
    $storagePurchases = $tenant->storagePurchases()
        ->when($dateFrom, function($query) use ($dateFrom) {
            return $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
        })
        ->when($dateTo, function($query) use ($dateTo) {
            return $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
        })
        ->when($paymentMethod, function($query) use ($paymentMethod) {
            return $query->whereJsonContains('payment_response->payment_type', 'card')
                         ->orWhereJsonContains('payment_response->payment_method', $paymentMethod);
        })
        ->when($status, function($query) use ($status) {
            // JSON içindeki status'u kontrol et
            return $query->where('status', $status)
                         ->orWhereJsonContains('payment_response->status', $status);
        })
        ->when($type && $type !== 'all', function($query) use ($type) {
            if ($type === 'subscription') {
                return $query->whereRaw('1=0'); // Hiç sonuç döndürme
            }
            return $query;
        })
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($purchase) {
            $paymentResponse = is_string($purchase->payment_response) 
                ? json_decode($purchase->payment_response, true) 
                : $purchase->payment_response;
                
            return [
                'id' => $purchase->id,
                'type' => 'storage',
                'type_label' => 'Depolama',
                'description' => $this->getStorageDescription($purchase),
                'amount' => $purchase->amount,
                'payment_method' => $this->extractPaymentMethodUnified($purchase),
                'status' => $purchase->status,
                'status_label' => $this->getStatusLabel($purchase->status),
                'invoice_path' => $purchase->invoice_path ?? null,
                'created_at' => $purchase->created_at,
                'storage_gb' => $purchase->storage_gb ?? 0,
                'expires_at' => $purchase->expires_at ?? null,
                // JSON'dan ek bilgiler
                'transaction_id' => $paymentResponse['merchant_oid'] ?? null,
                'gateway' => 'PayTR', // JSON'a göre
                'currency' => $paymentResponse['currency'] ?? 'TL',
                'payment_amount' => $paymentResponse['payment_amount'] ?? $purchase->amount,
                'test_mode' => $paymentResponse['test_mode'] ?? '0',
            ];
        });
}

        // İki collection'ı birleştir ve tarihe göre sırala
        $payments = $subscriptionPayments->concat($storagePurchases)
            ->sortByDesc('created_at')
            ->values();

        // Sayfalama
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $paymentsForPage = $payments->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $pagination = new \Illuminate\Pagination\LengthAwarePaginator(
            $paymentsForPage,
            $payments->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Filtreleme için gerekli veriler
        $paymentMethods = $this->getPaymentMethods($tenant);
        $statuses = [
            'active' => 'Aktif',
            'completed' => 'Tamamlandı',
            'pending' => 'Beklemede',
            'cancelled' => 'İptal Edildi',
            'expired' => 'Süresi Doldu',
            'failed' => 'Başarısız'
        ];

        return view('frontend.secure.payment_history.history_index', compact(
            'pagination', 
            'paymentMethods', 
            'statuses',
            'dateFrom',
            'dateTo',
            'paymentMethod',
            'status',
            'type',
            'tenant'
        ));
    }

    public function downloadInvoice($type, $id)
    {
        abort_if(!auth()->user()->hasRole('Patron'), 403);

        $tenant = tenant(); // Bu fonksiyonun nasıl çalıştığına bağlı olarak değişebilir
        
        if ($type === 'subscription') {
            $payment = $tenant->subscriptionPayments()->findOrFail($id);
        } else {
            $payment = $tenant->storagePurchases()->findOrFail($id);
        }

        if (!$payment->invoice_path || !file_exists(storage_path('app/' . $payment->invoice_path))) {
            abort(404, 'Fatura bulunamadı');
        }

        return response()->download(
            storage_path('app/' . $payment->invoice_path),
            'fatura_' . $payment->id . '.pdf'
        );
    }

    private function getSubscriptionPaymentDescription($payment)
    {
        $description = 'Abonelik Ödemesi';
        
        if (!empty($payment->subscription_id)) {
            $description .= " (Abonelik ID: {$payment->subscription_id})";
        }
        
        if (!empty($payment->transaction_id)) {
            $description .= " - İşlem: {$payment->transaction_id}";
        }
        
        if (!empty($payment->gateway)) {
            $description .= " via {$payment->gateway}";
        }
        
        return $description;
    }

    private function getStorageDescription($purchase)
    {
        return "Ek Depolama Alanı - " . ($purchase->storage_gb ?? 0) . " GB";
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'active' => 'Aktif',
            'completed' => 'Tamamlandı',
            'pending' => 'Beklemede',
            'cancelled' => 'İptal Edildi',
            'expired' => 'Süresi Doldu',
            'failed' => 'Başarısız',
            'paid' => 'Ödendi'
        ];

        return $labels[$status] ?? ucfirst($status);
    }

    private function extractPaymentMethod($paymentResponse)
{
    if (is_string($paymentResponse)) {
        $paymentResponse = json_decode($paymentResponse, true);
    }

    if (is_array($paymentResponse)) {
        // payment_type field'ını kontrol et (JSON'dan)
        if (isset($paymentResponse['payment_type'])) {
            return $this->formatPaymentType($paymentResponse['payment_type']);
        }
        
        // Fallback olarak payment_method kontrol et
        if (isset($paymentResponse['payment_method'])) {
            return $this->formatPaymentType($paymentResponse['payment_method']);
        }
    }

    return 'Belirtilmemiş';
}
private function formatPaymentType($paymentType)
{
    $types = [
        'card' => 'Kredi Kartı',
        'credit_card' => 'Kredi Kartı',
        'bank_transfer' => 'Banka Havalesi',
        'eft' => 'EFT',
        'cash' => 'Nakit',
        'paytr' => 'PayTR',
        'iyzico' => 'Iyzico'
    ];

    return $types[$paymentType] ?? ucfirst(str_replace('_', ' ', $paymentType));
}

    public function export(Request $request, $tenant_id)
{
    abort_if(!auth()->user()->hasRole('Patron'), 403);

    $tenant = Tenant::where('id', $tenant_id)->first();
    
    // Aynı filtreleme mantığını kullan
    $dateFrom = $request->get('date_from', now()->subMonth()->format('Y-m-d'));
    $dateTo = $request->get('date_to', now()->format('Y-m-d'));
    $paymentMethod = $request->get('payment_method');
    $status = $request->get('status');
    $type = $request->get('type', 'all');

    // Abonelik ödemeleri
    $subscriptionPayments = collect();
    if (method_exists($tenant, 'subscriptionPayments')) {
        try {
            $subscriptionPayments = $tenant->subscriptionPayments()
                ->when($dateFrom, function($query) use ($dateFrom) {
                    return $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
                })
                ->when($dateTo, function($query) use ($dateTo) {
                    return $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
                })
                ->when($paymentMethod, function($query) use ($paymentMethod) {
                    return $query->where('payment_method', $paymentMethod);
                })
                ->when($status, function($query) use ($status) {
                    return $query->where('status', $status);
                })
                ->when($type && $type !== 'all', function($query) use ($type) {
                    if ($type === 'storage') {
                        return $query->whereRaw('1=0');
                    }
                    return $query;
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($payment) {
                    return [
                        'id' => $payment->id,
                        'type_label' => 'Abonelik',
                        'description' => $this->getSubscriptionPaymentDescription($payment),
                        'amount' => number_format($payment->amount ?? 0, 2),
                        'currency' => $payment->currency ?? 'TL',
                        'payment_method' => $payment->payment_method ?: 'Belirtilmemiş',
                        'status_label' => $this->getStatusLabel($payment->status),
                        'created_at' => $payment->created_at->format('d.m.Y H:i'),
                        'paid_at' => $payment->paid_at ? $payment->paid_at->format('d.m.Y H:i') : '-',
                        'transaction_id' => $payment->transaction_id ?: '-',
                        'gateway' => $payment->gateway ?: '-',
                        'has_invoice' => !empty($payment->invoice_path) && file_exists(storage_path('app/' . $payment->invoice_path)) ? 'Mevcut' : 'Bekleniyor'
                    ];
                });
        } catch (\Exception $e) {
            \Log::error('Subscription payments export error: ' . $e->getMessage());
            $subscriptionPayments = collect();
        }
    }

    // Depolama satın almaları
    $storagePurchases = collect();
    if (method_exists($tenant, 'storagePurchases')) {
        try {
            $storagePurchases = $tenant->storagePurchases()
                ->when($dateFrom, function($query) use ($dateFrom) {
                    return $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
                })
                ->when($dateTo, function($query) use ($dateTo) {
                    return $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
                })
                ->when($paymentMethod, function($query) use ($paymentMethod) {
                    // Formatlanmış payment method ile karşılaştır
                    return $query->where(function($q) use ($paymentMethod) {
                        $q->whereJsonContains('payment_response->payment_type', 'card')
                          ->orWhere('payment_method', $paymentMethod)
                          ->orWhere('payment_type', $paymentMethod);
                    });
                })
                ->when($status, function($query) use ($status) {
                    return $query->where('status', $status)
                                 ->orWhereJsonContains('payment_response->status', $status);
                })
                ->when($type && $type !== 'all', function($query) use ($type) {
                    if ($type === 'subscription') {
                        return $query->whereRaw('1=0');
                    }
                    return $query;
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($purchase) {
                    $paymentResponse = is_string($purchase->payment_response) 
                        ? json_decode($purchase->payment_response, true) 
                        : ($purchase->payment_response ?? []);
                        
                    return [
                        'id' => $purchase->id,
                        'type_label' => 'Depolama',
                        'description' => $this->getStorageDescription($purchase),
                        'amount' => number_format($purchase->amount ?? 0, 2),
                        'currency' => $paymentResponse['currency'] ?? 'TL',
                        'payment_method' => $this->formatPaymentType($paymentResponse['payment_type'] ?? 'Belirtilmemiş'),
                        'status_label' => $this->getStatusLabel($purchase->status),
                        'created_at' => $purchase->created_at->format('d.m.Y H:i'),
                        'paid_at' => isset($purchase->purchased_at) ? $purchase->purchased_at->format('d.m.Y H:i') : '-',
                        'transaction_id' => $paymentResponse['merchant_oid'] ?? ($purchase->payment_token ?? '-'),
                        'gateway' => isset($paymentResponse['payment_type']) ? 'PayTR' : 'Depolama Sistemi',
                        'has_invoice' => !empty($purchase->invoice_path) && file_exists(storage_path('app/' . $purchase->invoice_path)) ? 'Mevcut' : 'Bekleniyor'
                    ];
                });
        } catch (\Exception $e) {
            \Log::error('Storage purchases export error: ' . $e->getMessage());
            $storagePurchases = collect();
        }
    }

    // İki collection'ı birleştir
    $payments = $subscriptionPayments->concat($storagePurchases)
        ->sortByDesc('created_at')
        ->values();

    // CSV olarak export et
    $filename = 'odeme-gecmisi-' . ($tenant->name ?? 'tenant') . '-' . now()->format('Y-m-d') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        'Cache-Control' => 'no-cache, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0'
    ];

    $callback = function() use ($payments) {
        $file = fopen('php://output', 'w');
        
        // BOM for UTF-8 Excel compatibility
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Header
        fputcsv($file, [
            'ID',
            'Tür', 
            'Açıklama',
            'Tutar',
            'Para Birimi',
            'Ödeme Yöntemi',
            'Durum',
            'Oluşturma Tarihi',
            'Ödeme Tarihi',
            'İşlem ID',
            'Gateway',
            'Fatura Durumu'
        ], ';');

        // Data
        foreach ($payments as $payment) {
            try {
                fputcsv($file, [
                    $payment['id'] ?? '',
                    $payment['type_label'] ?? '',
                    $payment['description'] ?? '',
                    $payment['amount'] ?? '0,00',
                    $payment['currency'] ?? 'TL',
                    $payment['payment_method'] ?? 'Belirtilmemiş',
                    $payment['status_label'] ?? '',
                    $payment['created_at'] ?? '',
                    $payment['paid_at'] ?? '-',
                    $payment['transaction_id'] ?? '-',
                    $payment['gateway'] ?? '-',
                    $payment['has_invoice'] ?? 'Bekleniyor'
                ], ';');
            } catch (\Exception $e) {
                \Log::error('CSV row error: ' . $e->getMessage());
                // Hatalı satırı atla, devam et
                continue;
            }
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

/**
 * Depolama ödemeleri için ödeme yöntemi extraction'ı
 */
private function extractPaymentMethodForExport($purchase)
{
    // Önce doğrudan field'leri kontrol et
    if (!empty($purchase->payment_method)) {
        return $purchase->payment_method;
    }
    
    if (!empty($purchase->payment_type)) {
        return $purchase->payment_type;
    }
    
    // Sonra JSON response'u kontrol et
    if (!empty($purchase->payment_response)) {
        $paymentResponse = is_string($purchase->payment_response) 
            ? json_decode($purchase->payment_response, true) 
            : $purchase->payment_response;
        
        if (is_array($paymentResponse)) {
            if (isset($paymentResponse['payment_method'])) {
                return $paymentResponse['payment_method'];
            }
            if (isset($paymentResponse['payment_type'])) {
                return $paymentResponse['payment_type'];
            }
        }
    }
    
    return 'Belirtilmemiş';
}
private function extractPaymentMethodUnified($purchase)
{
    // Önce doğrudan field'leri kontrol et
    if (!empty($purchase->payment_method)) {
        return $this->formatPaymentType($purchase->payment_method);
    }
    
    if (!empty($purchase->payment_type)) {
        return $this->formatPaymentType($purchase->payment_type);
    }
    
    // JSON response'u kontrol et
    if (!empty($purchase->payment_response)) {
        $paymentResponse = is_string($purchase->payment_response) 
            ? json_decode($purchase->payment_response, true) 
            : $purchase->payment_response;
        
        if (is_array($paymentResponse)) {
            if (isset($paymentResponse['payment_type'])) {
                return $this->formatPaymentType($paymentResponse['payment_type']);
            }
            if (isset($paymentResponse['payment_method'])) {
                return $this->formatPaymentType($paymentResponse['payment_method']);
            }
        }
    }
    
    return 'Belirtilmemiş';
}
    private function getPaymentMethods($tenant)
{
    $methods = collect();

    // Abonelik ödeme yöntemlerini al
    if (method_exists($tenant, 'subscriptionPayments')) {
        $subscriptionMethods = $tenant->subscriptionPayments()
            ->whereNotNull('payment_method')
            ->pluck('payment_method')
            ->unique()
            ->map(function($method) {
                return $this->formatPaymentType($method);
            });
        $methods = $methods->concat($subscriptionMethods);
    }

    // Depolama satın alma ödeme yöntemlerini al
    if (method_exists($tenant, 'storagePurchases')) {
        $storagePurchases = $tenant->storagePurchases()
            ->whereNotNull('payment_response')
            ->get();
            
        $storageMethods = $storagePurchases->map(function($purchase) {
            return $this->extractPaymentMethodUnified($purchase);
        })->filter()->unique();
        
        $methods = $methods->concat($storageMethods);
    }

    return $methods->unique()->sort()->values();
}

}
