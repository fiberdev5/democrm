<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PaymentHistoryController extends Controller
{
public function index(Request $request)
{
    abort_if(!auth()->user()->hasRole('Patron'), 403);

    // Tenant bilgisini auth user'dan al
    $tenant = auth()->user()->tenant; // veya ilişkiye göre ayarlayın
    
    if (!$tenant) {
        abort(404, 'Tenant bulunamadı');
    }
    
    // DataTables AJAX request kontrolü
    if ($request->ajax()) {
        return $this->getDataTablesData($request, $tenant);
    }
    
    // Normal sayfa yükleme
    return view('frontend.secure.payment_history.history_index', compact('tenant'));
}

    private function getDataTablesData(Request $request, $tenant)
    {
        // Filtreleme parametreleri
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $type = $request->get('type', 'all');
        $searchValue = $request->get('search')['value'] ?? '';
        
        // Abonelik ödemeleri
        $subscriptionPayments = collect();
        if (method_exists($tenant, 'subscriptionPayments')) {
            $subscriptionPayments = $tenant->subscriptionPayments()
                ->when($dateFrom, function($query) use ($dateFrom) {
                    return $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
                })
                ->when($dateTo, function($query) use ($dateTo) {
                    return $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
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
                        'payment_id' => $payment->id,
                        'type' => 'subscription',
                        'type_label' => 'Abonelik',
                        'description' => $this->getSubscriptionPaymentDescription($payment),
                        'amount' => $payment->amount ?? 0,
                        'currency' => $payment->currency ?? 'TL',
                        'status' => $payment->status,
                        'status_label' => $this->getStatusLabel($payment->status),
                        'invoice_path' => $payment->invoice_path ?? null,
                        'created_at' => $payment->created_at,
                    ];
                });
        }

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
                        : $purchase->payment_response;
                        
                    return [
                        'id' => $purchase->id,
                        'payment_id' => $purchase->id,
                        'type' => 'storage',
                        'type_label' => 'Depolama',
                        'description' => $this->getStorageDescription($purchase),
                        'amount' => $purchase->amount,
                        'currency' => $paymentResponse['currency'] ?? 'TL',
                        'status' => $purchase->status,
                        'status_label' => $this->getStatusLabel($purchase->status),
                        'invoice_path' => $purchase->invoice_path ?? null,
                        'created_at' => $purchase->created_at,
                    ];
                });
        }

        // İki collection'ı birleştir
        $payments = $subscriptionPayments->concat($storagePurchases)
            ->sortByDesc('created_at')
            ->values();

        // Arama filtresi
        if (!empty($searchValue)) {
            $payments = $payments->filter(function($payment) use ($searchValue) {
                return stripos($payment['description'], $searchValue) !== false ||
                       stripos($payment['type_label'], $searchValue) !== false ||
                       stripos($payment['status_label'], $searchValue) !== false;
            })->values();
        }

        // DataTables pagination
        $totalRecords = $payments->count();
        $start = $request->get('start', 0);
        $length = $request->get('length', 25);
        
        $paymentsForPage = $payments->slice($start, $length)->values();

           // DataTables formatına dönüştür
        $data = $paymentsForPage->map(function($payment) use ($tenant) {
            // Açıklamayı temizle
            $description = $payment['description'];
            $description = preg_replace('/\(Abonelik ID:\s*\d+\)/i', '', $description);
            $description = preg_replace('/via\s+paytr/i', '', $description);
            $description = preg_replace('/via\s+paypal/i', '', $description);
            $description = preg_replace('/\s+/', ' ', trim($description));

            // Durum rengi ve ikonu
            $statusColor = match($payment['status']) {
                'active', 'completed' => '#28a745',
                'pending' => '#fd7e14',
                'cancelled', 'failed' => '#dc3545',
                'expired' => '#6c757d',
                default => '#343a40'
            };
            
            $statusIcon = match($payment['status']) {
                'active', 'completed' => '<i class="fas fa-check-circle me-1"></i>',
                'pending' => '<i class="fas fa-clock me-1"></i>',
                'cancelled', 'failed' => '<i class="fas fa-times-circle me-1"></i>',
                'expired' => '<i class="fas fa-ban me-1"></i>',
                default => ''
            };

            // Fatura durumu - DÜZELTME
            if ($payment['invoice_path']) {
                $downloadUrl = route('payment-history.invoice', [
                    'type' => $payment['type'],
                    'id' => $payment['payment_id']
                ]);
                
                $invoiceButton = '<a href="' . $downloadUrl . '" class="btn btn-sm btn-outline-primary" target="_blank" style="font-size: 11px; padding: 2px 8px;">
                    <i class="fas fa-file-pdf me-1"></i>İndir
                </a>';
            } else {
                $invoiceButton = '<span style="color: #fd7e14; font-weight: 600;">
                    <i class="fas fa-clock me-1"></i>Bekleniyor
                </span>';
            }

            return [
                'id' => '<a href="javascript:void(0);" class="t-link">' . $payment['id'] . '</a>',
                'type_label' => '<a href="javascript:void(0);" class="t-link"><strong>' . $payment['type_label'] . '</strong></a>',
                'description' => '<a href="javascript:void(0);" class="t-link"><strong>' . $description . '</strong></a>',
                'amount' => '<a href="javascript:void(0);" class="t-link"><strong>' . number_format($payment['amount'], 2) . ' ' . strtoupper($payment['currency']) . '</strong></a>',
                'status_label' => '<a href="javascript:void(0);" class="t-link" style="color: ' . $statusColor . ' !important; font-weight: 600;">' . $statusIcon . $payment['status_label'] . '</a>',
                'created_at' => '<a href="javascript:void(0);" class="t-link"><strong>' . $payment['created_at']->format('d.m.Y') . '</strong></a>',
                'invoice_status' => $invoiceButton
            ];
        });

        return response()->json([
            'draw' => intval($request->get('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
        
    } catch (\Exception $e) {
        \Log::error('DataTables Error: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());
        
        return response()->json([
            'draw' => intval($request->get('draw', 0)),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => $e->getMessage()
        ], 500);
    }
}

   public function downloadInvoice($type, $id)
{
    abort_if(!auth()->user()->hasRole('Patron'), 403);

    $tenant = auth()->user()->tenant;
    
    if ($type === 'subscription') {
        $payment = $tenant->subscriptionPayments()->findOrFail($id);
    } else {
        $payment = $tenant->storagePurchases()->findOrFail($id);
    }

    if (!$payment->invoice_path) {
        abort(404, 'Fatura yolu bulunamadı');
    }

    $possiblePaths = [
        public_path($payment->invoice_path),
        public_path('upload/uploads/' . basename($payment->invoice_path)),
        storage_path('app/public/' . $payment->invoice_path),
        storage_path('app/' . $payment->invoice_path)
    ];

    $validPath = null;
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $validPath = $path;
            break;
        }
    }

    if (!$validPath) {
        \Log::error('Invoice file not found', [
            'payment_id' => $payment->id,
            'invoice_path' => $payment->invoice_path,
            'checked_paths' => $possiblePaths
        ]);
        abort(404, 'Fatura dosyası bulunamadı');
    }

    return response()->download($validPath, 'fatura_' . $payment->id . '.pdf');
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
        
        $dateFrom = $request->get('date_from', now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
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
                            'status_label' => $this->getStatusLabel($payment->status),
                            'created_at' => $payment->created_at->format('d.m.Y H:i'),
                            'has_invoice' => !empty($payment->invoice_path) ? 'Mevcut' : 'Bekleniyor'
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
                            'status_label' => $this->getStatusLabel($purchase->status),
                            'created_at' => $purchase->created_at->format('d.m.Y H:i'),
                            'has_invoice' => !empty($purchase->invoice_path) ? 'Mevcut' : 'Bekleniyor'
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
        $filename = 'odeme-gecmisi-' . now()->format('Y-m-d') . '.csv';
        
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
                'Durum',
                'Tarih',
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
                        $payment['status_label'] ?? '',
                        $payment['created_at'] ?? '',
                        $payment['has_invoice'] ?? 'Bekleniyor'
                    ], ';');
                } catch (\Exception $e) {
                    \Log::error('CSV row error: ' . $e->getMessage());
                    continue;
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}