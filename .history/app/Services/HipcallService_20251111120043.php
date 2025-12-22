<?php

namespace App\Services;

use App\Models\IntegrationPurchase;
use App\Models\HipcallCallLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HipcallService
{
    protected $apiKey;
    protected $baseUrl = 'https://use.hipcall.com.tr/api/v3'; // Gerçek base URL
    
    public function __construct($tenantId)
    {
        $purchase = IntegrationPurchase::where('tenant_id', $tenantId)
            ->whereHas('integration', function($q) {
                $q->where('slug', 'hipcall');
            })
            ->where('status', 'completed')
            ->where('is_active', true)
            ->first();
        
        if ($purchase && $purchase->credentials) {
            $credentials = is_string($purchase->credentials) 
                ? json_decode($purchase->credentials, true) 
                : $purchase->credentials;
            
            $this->apiKey = $credentials['api_key'] ?? null;
        }
    }
    
    /**
     * Çağrı kayıtlarını çek (basit versiyon)
     */
    public function getCalls($limit = 20)
    {
        if (!$this->apiKey) {
            return [
                'success' => false,
                'message' => 'API Key eksik. Lütfen entegrasyon ayarlarından API Key girin.'
            ];
        }
        
        try {
            Log::info('Hipcall API Request', [
                'url' => $this->baseUrl . '/calls',
                'api_key_exists' => !empty($this->apiKey)
            ]);
            
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get($this->baseUrl . '/calls', [
                    'limit' => $limit,
                    'sort' => 'started_at.desc'
                ]);
            
            Log::info('Hipcall API Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'data' => $data,
                    'calls' => $data['data'] ?? $data['calls'] ?? $data ?? []
                ];
            }
            
            // Hata durumu
            return [
                'success' => false,
                'message' => 'Hipcall API yanıt vermedi',
                'status_code' => $response->status(),
                'error' => $response->body()
            ];
            
        } catch (\Exception $e) {
            Log::error('Hipcall API Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Bağlantı hatası: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Test bağlantısı - sadece API key'in geçerli olup olmadığını kontrol et
     */
    public function testConnection()
    {
        if (!$this->apiKey) {
            return [
                'success' => false,
                'message' => 'API Key eksik'
            ];
        }
        
        // Basit bir GET isteği ile test et
        $result = $this->getCalls(1);
        
        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'Hipcall API bağlantısı başarılı! ✓'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'API Key geçersiz veya bağlantı hatası',
            'details' => $result['message'] ?? 'Bilinmeyen hata'
        ];
    }
}