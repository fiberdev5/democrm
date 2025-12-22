<?php

namespace App\Services;

use App\Contracts\InvoiceIntegrationInterface;
use App\Models\IntegrationPurchase;
use App\Services\InvoiceIntegrations\ParasutService;
use Exception;

class InvoiceIntegrationFactory
{
    /**
     * Firma için uygun entegrasyon service'ini döndür
     */
    public static function make(int $tenantId): ?InvoiceIntegrationInterface
    {
        // Firmanın aktif fatura entegrasyonunu bul
        $integration = IntegrationPurchase::where('tenant_id', $tenantId)
            ->whereHas('integration', function($q) {
                $q->where('category', 'invoice') // integration tablosunda category sütunu olmalı
                  ->where('is_active', true);
            })
            ->where('is_active', true)
            // ->where('expires_at', '>', now())
            ->first();

        if (!$integration) {
            return null; // Entegrasyon yok, manuel sistem kullanılacak
        }

        $credentials = $integration->credentials;
        $integrationSlug = $integration->integration->slug;

        return match($integrationSlug) {
            'parasut' => new ParasutService($credentials),
            // 'uyumsoft' => new UyumsoftService($credentials),
            default => throw new Exception('Bilinmeyen entegrasyon: ' . $integrationSlug)
        };
    }

    /**
     * Entegrasyon var mı kontrol et
     */
    public static function hasIntegration(int $tenantId): bool
    {
        return self::make($tenantId) !== null;
    }
}