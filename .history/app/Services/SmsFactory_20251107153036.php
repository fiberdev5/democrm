<?php
// app/Services/SmsFactory.php

namespace App\Services;

use App\Models\Integration;
use App\Models\IntegrationPurchase;
use App\Services\SmsProviders\NetgsmProvider;
use App\Services\SmsProviders\TurkTelekomProvider;
use App\Services\SmsProviders\SmsProviderInterface;
use Illuminate\Support\Facades\Log;

class SmsFactory
{
    /**
     * Firma için aktif SMS entegrasyonunu getir ve provider oluştur
     */
    public static function getProviderForTenant($tenantId): ?SmsProviderInterface
    {
        // Firma'nın aktif SMS entegrasyonunu bul
        $purchase = IntegrationPurchase::where('tenant_id', $tenantId)
            ->where('status', 'completed')
            ->whereHas('integration', function($query) {
                $query->where('category', 'sms')
                    ->where('is_active', true);
            })
            ->with('integration')
            ->first();

        if (!$purchase) {
            Log::warning('Firma için aktif SMS entegrasyonu bulunamadı', [
                'tenant_id' => $tenantId
            ]);
            return null;
        }

        // Credentials'ı parse et
        $credentials = is_string($purchase->credentials) 
            ? json_decode($purchase->credentials, true) 
            : $purchase->credentials;

        if (empty($credentials)) {
            Log::error('SMS credentials boş', [
                'purchase_id' => $purchase->id
            ]);
            return null;
        }

        // Integration slug'ına göre provider seç
        $integrationSlug = $purchase->integration->slug;

        return self::createProvider($integrationSlug, $credentials);
    }

    /**
     * Belirli bir entegrasyon için provider oluştur
     */
    public static function createProvider(string $slug, array $credentials): ?SmsProviderInterface
    {
        switch ($slug) {
            case 'netgsm':
                return new NetgsmProvider($credentials);
            // Diğer SMS provider'ları buraya eklenebilir
            
            default:
                Log::error('Bilinmeyen SMS provider', [
                    'slug' => $slug
                ]);
                return null;
        }
    }
}