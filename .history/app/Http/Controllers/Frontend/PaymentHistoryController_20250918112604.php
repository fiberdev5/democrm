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

        $tenant = Tenant::where('id',$tenant_id)->first();
        
        / Filtreleme parametreleri
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $paymentMethod = $request->get('payment_method');
        $status = $request->get('status');
        $type = $request->get('type'); // subscription, storage, all
        
        // Abonelik ödemeleri
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
                    'invoice_path' => $payment->invoice_path,
                    'created_at' => $payment->created_at,
                    'paid_at' => $payment->paid_at,
                    'transaction_id' => $payment->transaction_id,
                    'gateway' => $payment->gateway,
                    'currency' => $payment->currency,
                    'tenant_id' => $payment->tenant_id,
                    'subscription_id' => $payment->subscription_id,
                    'payment_id' => $payment->payment_id,
                ];
            });

        // Depolama satın almaları
        $storagePurchases = $tenant->storagePurchases()
            ->when($dateFrom, function($query) use ($dateFrom) {
                return $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
            })
            ->when($dateTo, function($query) use ($dateTo) {
                return $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
            })
            ->when($paymentMethod, function($query) use ($paymentMethod) {
                return $query->whereJsonContains('payment_response', ['payment_method' => $paymentMethod]);
            })
            ->when($status, function($query) use ($status) {
                return $query->where('status', $status);
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
                return [
                    'id' => $purchase->id,
                    'type' => 'storage',
                    'type_label' => 'Depolama',
                    'description' => $this->getStorageDescription($purchase),
                    'amount' => $purchase->amount,
                    'payment_method' => $this->extractPaymentMethod($purchase->payment_response),
                    'status' => $purchase->status,
                    'status_label' => $this->getStatusLabel($purchase->status),
                    'invoice_path' => $purchase->invoice_path,
                    'created_at' => $purchase->created_at,
                    'storage_gb' => $purchase->storage_gb,
                    'expires_at' => $purchase->expires_at,
                ];
            });

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
            'expired' => 'Süresi Doldu'
        ];

        return view('tenant.payment-history.index', compact(
            'pagination', 
            'paymentMethods', 
            'statuses',
            'dateFrom',
            'dateTo',
            'paymentMethod',
            'status',
            'type'
        ));
    }

    public function downloadInvoice($type, $id)
    {
        abort_if(!auth()->user()->hasRole('patron'), 403);

        $tenant = tenant();
        
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
        
        if ($payment->subscription_id) {
            $description .= " (Abonelik ID: {$payment->subscription_id})";
        }
        
        if ($payment->transaction_id) {
            $description .= " - İşlem: {$payment->transaction_id}";
        }
        
        if ($payment->gateway) {
            $description .= " via {$payment->gateway}";
        }
        
        return $description;
    }

    private function getStorageDescription($purchase)
    {
        return "Ek Depolama Alanı - {$purchase->storage_gb} GB";
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'active' => 'Aktif',
            'completed' => 'Tamamlandı',
            'pending' => 'Beklemede',
            'cancelled' => 'İptal Edildi',
            'expired' => 'Süresi Doldu'
        ];

        return $labels[$status] ?? $status;
    }

    private function extractPaymentMethod($paymentResponse)
    {
        if (is_string($paymentResponse)) {
            $paymentResponse = json_decode($paymentResponse, true);
        }

        return $paymentResponse['payment_method'] ?? 'Belirtilmemiş';
    }

    private function getPaymentMethods($tenant)
    {
        // Benzersiz ödeme yöntemlerini al
        $subscriptionMethods = $tenant->subscriptionPayments()
            ->whereNotNull('payment_method')
            ->pluck('payment_method')
            ->unique();

        $storageMethods = $tenant->storagePurchases()
            ->whereNotNull('payment_response')
            ->get()
            ->map(function($purchase) {
                return $this->extractPaymentMethod($purchase->payment_response);
            })
            ->filter()
            ->unique();

        return $subscriptionMethods->concat($storageMethods)->unique()->sort()->values();
    }
}
