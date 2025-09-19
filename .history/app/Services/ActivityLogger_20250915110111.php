<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log($action, $description, $options = [])
    {
        try {
            $user = Auth::user();
            
            $logData = [
                'tenant_id' => $options['tenant_id'] ?? ($user ? $user->tenant_id : null),
                'user_id' => $user ? $user->user_id : null,
                'user_name' => $user ? $user->name : ($options['user_name'] ?? null),
                'user_role' => $user ? $user->getRoleNames()->first() : ($options['user_role'] ?? null),
                'ip_address' => Request::ip(),
                'action' => $action,
                'module' => $options['module'] ?? null,
                'description' => $description,
                'old_values' => isset($options['old_values']) ? json_encode($options['old_values']) : null,
                'new_values' => isset($options['new_values']) ? json_encode($options['new_values']) : null,
                'reference_table' => $options['reference_table'] ?? null,
                'reference_id' => $options['reference_id'] ?? null,
                'user_agent' => Request::header('User-Agent')
            ];

            ActivityLog::create($logData);
        } catch (\Exception $e) {
            // Log hatası sistem loglarını bozmasın diye sessizce geç
            \Log::error('ActivityLogger Error: ' . $e->getMessage());
        }
    }

    // Özel log metodları
    public static function logLogin($user)
    {
        self::log('login', 'Personel Giriş Yapıldı', [
            'module' => 'auth',
            'user_name' => $user->name,
            'user_role' => $user->getRoleNames()->first(),
            'tenant_id' => $user->tenant_id
        ]);
    }

    public static function logLogout($user)
    {
        self::log('logout', 'Personel Çıkış Yapıldı', [
            'module' => 'auth',
            'user_name' => $user->name,
            'user_role' => $user->getRoleNames()->first(),
            'tenant_id' => $user->tenant_id
        ]);
    }

   public static function logServiceCreated($serviceId)
{
    self::log('service_created', "ServisID: {$serviceId} Servis Kaydı Oluşturuldu", [
        'module' => 'service',
        'reference_table' => 'services',
        'reference_id' => $serviceId
    ]);
}

public static function logServiceUpdated($serviceId, $oldData = null, $newData = null)
{
    self::log('service_updated', "ServisID: {$serviceId} Servis Kaydı Güncellendi", [
        'module' => 'service',
        'reference_table' => 'services',
        'reference_id' => $serviceId,
        'old_values' => $oldData,
        'new_values' => $newData
    ]);
}

public static function logServiceDeleted($serviceId)
{
    self::log('service_deleted', "ServisID: {$serviceId} Servis Kaydı Silindi", [
        'module' => 'service',
        'reference_table' => 'services',
        'reference_id' => $serviceId
    ]);
}

    public static function logServiceStatusChanged($serviceId, $oldStatus, $newStatus)
    {
        // Durum ID'lerini metne çevir
        $statusMap = [
            235 => 'Yeni Servisler',
            236 => 'Teknisyen Yönledir',
            237 => 'Cihaz Atölyeye Alındı',
            244 => 'Müşteri İptal Etti',
            252 => 'Teslimata Hazır(Tamamlandı)',
            // Diğer durumları ekleyebilirsiniz
        ];

        $oldStatusText = $statusMap[$oldStatus] ?? "Durum {$oldStatus}";
        $newStatusText = $statusMap[$newStatus] ?? "Durum {$newStatus}";

        self::log('service_status_changed', "ServisID: {$serviceId} Servis Durumu Değiştirildi: {$oldStatusText} -> {$newStatusText}", [
            'module' => 'service',
            'reference_table' => 'services',
            'reference_id' => $serviceId,
            'old_values' => ['status' => $oldStatus],
            'new_values' => ['status' => $newStatus]
        ]);
    }

    public static function logCustomerCreated($customerId)
    {
        self::log('customer_created', "MusteriID: {$customerId} Müşteri Kaydı Oluşturuldu", [
            'module' => 'customer',
            'reference_table' => 'customers',
            'reference_id' => $customerId
        ]);
    }

    public static function logStockAction($stockId, $action, $quantity)
    {
        $actionText = $action == 1 ? 'Stok Girişi' : 'Stok Çıkışı';
        self::log('stock_action', "StokID: {$stockId} {$actionText}: {$quantity} Adet", [
            'module' => 'stock',
            'reference_table' => 'stock_actions',
            'reference_id' => $stockId
        ]);
    }

    public static function logCashTransaction($amount, $type, $description)
    {
        $typeText = $type == 1 ? 'Gelir' : 'Gider';
        self::log('cash_transaction', "Kasa İşlemi: {$typeText} - {$amount} TL - {$description}", [
            'module' => 'cash'
        ]);
    }
}