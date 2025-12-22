<?php

namespace App\Services;

use App\Models\IntegrationPurchase;
use App\Models\VerimorWebphoneToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class VerimorSantralService
{
    protected $apiKey;
    protected $extension;
    protected $tenantId;
    protected $baseUrl = 'https://api.verimor.com.tr/switch';
    
    public function __construct($tenantId)
    {
        $this->tenantId = $tenantId;
        
        // Verimor Santral entegrasyonunu bul
        $purchase = IntegrationPurchase::where('tenant_id', $tenantId)
            ->whereHas('integration', function($q) {
                $q->where('slug', 'verimor-santral'); // Slug'ı kontrol et
            })
            ->where('status', 'completed')
            ->where('is_active', true)
            ->first();
        
        if ($purchase && $purchase->credentials) {
            $credentials = is_string($purchase->credentials) 
                ? json_decode($purchase->credentials, true) 
                : $purchase->credentials;
            
            $this->apiKey = $credentials['api_key'] ?? null;
            $this->extension = $credentials['extension'] ?? null;
        }
    }
    
    /**
     * Dahili için webphone token al
     * Token 1 gün geçerlidir
     */
    public function getWebphoneToken($extension = null)
    {
        // Parametre yoksa credentials'dan al
        $extension = $extension ?? $this->extension;
        
        if (!$this->apiKey || !$extension) {
            return [
                'success' => false,
                'message' => 'API Key veya dahili numarası eksik'
            ];
        }
        
        // Önce mevcut geçerli token var mı kontrol et
        $existingToken = VerimorWebphoneToken::where('tenant_id', $this->tenantId)
            ->where('extension', $extension)
            ->first();
        
        // Token varsa ve geçerliyse onu döndür
        if ($existingToken && $existingToken->isValid()) {
            Log::info('Verimor: Mevcut token kullanılıyor', [
                'tenant_id' => $this->tenantId,
                'extension' => $extension,
                'expires_at' => $existingToken->expires_at
            ]);
            
            return [
                'success' => true,
                'token' => $existingToken->token,
                'expires_at' => $existingToken->expires_at,
                'from_cache' => true
            ];
        }
        
        // Yeni token al
        try {
            Log::info('Verimor: Yeni token isteniyor', [
                'url' => $this->baseUrl . '/webphone-tokens',
                'extension' => $extension
            ]);
            
            $response = Http::timeout(10)
                ->get($this->baseUrl . '/webphone-tokens', [
                    'api_key' => $this->apiKey,
                    'extension' => $extension
                ]);
            
            Log::info('Verimor API Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $token = $data['token'] ?? $data['data']['token'] ?? null;
                
                if (!$token) {
                    return [
                        'success' => false,
                        'message' => 'API\'den token alınamadı',
                        'response' => $data
                    ];
                }
                
                // Token'ı veritabanına kaydet (1 gün geçerli)
                VerimorWebphoneToken::updateOrCreate(
                    [
                        'tenant_id' => $this->tenantId,
                        'extension' => $extension
                    ],
                    [
                        'token' => $token,
                        'expires_at' => now()->addDay() // 24 saat
                    ]
                );
                
                Log::info('Verimor: Token başarıyla alındı ve kaydedildi', [
                    'tenant_id' => $this->tenantId,
                    'extension' => $extension
                ]);
                
                return [
                    'success' => true,
                    'token' => $token,
                    'expires_at' => now()->addDay(),
                    'from_cache' => false
                ];
            }
            
            // HTTP hatası
            return [
                'success' => false,
                'message' => 'API hatası: ' . $response->status(),
                'status' => $response->status(),
                'error' => $response->body()
            ];
            
        } catch (\Exception $e) {
            Log::error('Verimor token alma hatası', [
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
     * Test bağlantısı
     */
    public function testConnection()
    {
        $result = $this->getWebphoneToken();
        
        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'Verimor Santral bağlantısı başarılı! ✓',
                'token' => substr($result['token'], 0, 20) . '...'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Bağlantı başarısız',
            'details' => $result['message'] ?? 'Bilinmeyen hata'
        ];
    }
    
    /**
     * iframe URL'ini oluştur
     */
    public function getIframeUrl($token = null)
    {
        if (!$token) {
            $result = $this->getWebphoneToken();
            if (!$result['success']) {
                return null;
            }
            $token = $result['token'];
        }
        
        return "https://oim.verimor.com.tr/webphone?token={$token}";
    }
    
    /**
     * iframe HTML'ini oluştur
     */
    public function getIframeHtml($width = 275, $height = 700)
    {
        $result = $this->getWebphoneToken();
        
        if (!$result['success']) {
            return '<div class="alert alert-danger">Web telefonu yüklenemedi: ' . $result['message'] . '</div>';
        }
        
        $url = $this->getIframeUrl($result['token']);
        
        return sprintf(
            '<iframe style="border: none;" src="%s" width="%spx" height="%spx" allow="microphone"></iframe>',
            $url,
            $width,
            $height
        );
    }
}