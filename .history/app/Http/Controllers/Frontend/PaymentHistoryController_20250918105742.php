<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;


class PaymentHistoryController extends Controller
{
    public function index(Request $request)
    {
        // Sadece patron rollü kullanıcılar erişebilir
        abort_if(!auth()->user()->hasRole('Patron'), 403);

        $tenant = tenant();
        
        // Filtreleme parametreleri
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $paymentMethod = $request->get('payment_method');
        $status = $request->get('status');
        $type = $request->get('type'); // subscription, storage, all
        
        // Abonelik ödemeleri
        $subscriptions = $tenant->subscriptions()
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
            ->map(function($subscription) {
                return [
                    'id' => $subscription->id,
                    'type' => 'subscription',
                    'type_label' => 'Abonelik',
                    'description' => $this->getSubscriptionDescription($subscription),
                    'amount' => $subscription->amount ?? 0,
                    'payment_method' => $subscription->payment_method,
                    'status' => $subscription->status,
                    'status_label' => $this->getStatusLabel($subscription->status),
                    'invoice_path' => $subscription->invoice_path,
                    'created_at' => $subscription->created_at,
                    'starts_at' => $subscription->starts_at,
                    'ends_at' => $subscription->ends_at,
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
        $payments = $subscriptions->concat($storagePurchases)
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

        return view('frontend.secure.payment_history.history_index', compact(
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
        abort_if(!auth()->user()->hasRole('Patron'), 403);

        $tenant = tenant();
        
        if ($type === 'subscription') {
            $payment = $tenant->subscriptions()->findOrFail($id);
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

    private function getSubscriptionDescription($subscription)
    {
        $description = 'Abonelik Planı';
        
        if ($subscription->plan_id) {
            $description .= " (Plan ID: {$subscription->plan_id})";
        }
        
        if ($subscription->starts_at && $subscription->ends_at) {
            $description .= " - " . Carbon::parse($subscription->starts_at)->format('d.m.Y') . 
                          " / " . Carbon::parse($subscription->ends_at)->format('d.m.Y');
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
        $subscriptionMethods = $tenant->subscriptions()
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
