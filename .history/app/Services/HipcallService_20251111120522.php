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

    /**
     * Tek bir müşteriyi Hipcall'a gönder
     */
    public function syncContact($customer)
    {
        if (!$this->apiKey) {
            return [
                'success' => false,
                'message' => 'API Key eksik'
            ];
        }
        
        try {
            // Müşteri verisini hazırla
            $contactData = [
                'first_name' => $customer->ad ?? '',
                'last_name' => $customer->soyad ?? '',
                'phone' => $this->formatPhoneNumber($customer->tel1 ?? $customer->telefon),
                'email' => $customer->eposta ?? '',
                'company' => $customer->firma ?? '',
                'notes' => $customer->not ?? '',
                // Ekstra alanlar varsa
                'address' => $customer->adres ?? '',
                'city' => $customer->il ?? '',
                'external_id' => $customer->id, // SerbisERP'deki ID'si
            ];
            
            // Boş değerleri temizle
            $contactData = array_filter($contactData, function($value) {
                return !empty($value);
            });
            
            Log::info('Hipcall Contact Sync Request', [
                'customer_id' => $customer->id,
                'data' => $contactData
            ]);
            
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/contacts', $contactData);
            
            Log::info('Hipcall Contact Sync Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Müşteri Hipcall rehberine eklendi',
                    'data' => $response->json()
                ];
            }
            
            // Eğer müşteri zaten varsa güncellemeyi dene
            if ($response->status() == 409 || $response->status() == 422) {
                return $this->updateContact($customer);
            }
            
            return [
                'success' => false,
                'message' => 'Müşteri eklenemedi',
                'status' => $response->status(),
                'error' => $response->body()
            ];
            
        } catch (\Exception $e) {
            Log::error('Hipcall syncContact error', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Mevcut müşteriyi güncelle
     */
    public function updateContact($customer)
    {
        if (!$this->apiKey) {
            return [
                'success' => false,
                'message' => 'API Key eksik'
            ];
        }
        
        try {
            $contactData = [
                'first_name' => $customer->ad ?? '',
                'last_name' => $customer->soyad ?? '',
                'phone' => $this->formatPhoneNumber($customer->tel1 ?? $customer->telefon),
                'email' => $customer->eposta ?? '',
                'company' => $customer->firma ?? '',
                'notes' => $customer->not ?? '',
            ];
            
            $contactData = array_filter($contactData, function($value) {
                return !empty($value);
            });
            
            // Telefon numarasına göre mevcut contact'ı bul
            $phone = $this->formatPhoneNumber($customer->tel1 ?? $customer->telefon);
            
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->put($this->baseUrl . '/contacts/phone/' . urlencode($phone), $contactData);
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Müşteri güncellendi',
                    'data' => $response->json()
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Güncelleme başarısız',
                'status' => $response->status()
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Güncelleme hatası: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Toplu müşteri senkronizasyonu
     */
    public function syncMultipleContacts($customers, $tenantId)
    {
        $results = [
            'total' => count($customers),
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];
        
        foreach ($customers as $customer) {
            $result = $this->syncContact($customer);
            
            if ($result['success']) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = [
                    'customer_id' => $customer->id,
                    'customer_name' => ($customer->ad ?? '') . ' ' . ($customer->soyad ?? ''),
                    'error' => $result['message']
                ];
            }
            
            // API rate limit için kısa bekleme
            usleep(200000); // 200ms bekle
        }
        
        return $results;
    }

    /**
     * Telefon numarasını formatla (Türkiye için)
     */
    private function formatPhoneNumber($phone)
    {
        if (empty($phone)) {
            return null;
        }
        
        // Sadece rakamları al
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Türkiye formatına çevir
        if (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
            // 05321234567 -> +905321234567
            $phone = '+9' . $phone;
        } elseif (strlen($phone) == 10) {
            // 5321234567 -> +905321234567
            $phone = '+90' . $phone;
        } elseif (strlen($phone) == 11 && substr($phone, 0, 2) == '90') {
            // 905321234567 -> +905321234567
            $phone = '+' . $phone;
        }
        
        return $phone;
    }

    /**
     * Hipcall'dan tüm kontakları çek
     */
    public function getContacts($page = 1, $limit = 50)
    {
        if (!$this->apiKey) {
            return [
                'success' => false,
                'message' => 'API Key eksik'
            ];
        }
        
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get($this->baseUrl . '/contacts', [
                    'page' => $page,
                    'limit' => $limit
                ]);
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Kontaklar çekilemedi'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage()
            ];
        }
    }
}