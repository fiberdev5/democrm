<?php

namespace App\Services;

use App\Models\IntegrationPurchase;
use App\Models\HipcallCallLog;
use Illuminate\Support\Facades\DB;
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
 * Hipcall'dan tüm rehber kişilerini çek
 */
public function getContacts($page = 1, $perPage = 100)
{
    if (!$this->apiKey) {
        return [
            'success' => false,
            'message' => 'API Key eksik'
        ];
    }
    
    try {
        Log::info('Hipcall Get Contacts Request', [
            'url' => $this->baseUrl . '/contacts',
            'page' => $page,
            'per_page' => $perPage
        ]);
        
        $response = Http::timeout(15)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])
            ->get($this->baseUrl . '/contacts', [
                'page' => $page,
                'per_page' => $perPage
            ]);
        
        Log::info('Hipcall Get Contacts Response', [
            'status' => $response->status(),
            'body' => substr($response->body(), 0, 500)
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            
            return [
                'success' => true,
                'data' => $data,
                'contacts' => $data['data'] ?? $data['contacts'] ?? $data ?? []
            ];
        }
        
        return [
            'success' => false,
            'message' => "Rehber çekilemedi (HTTP {$response->status()})",
            'status' => $response->status(),
            'error' => $response->body()
        ];
        
    } catch (\Exception $e) {
        Log::error('Hipcall getContacts error', [
            'error' => $e->getMessage()
        ]);
        
        return [
            'success' => false,
            'message' => 'Bağlantı hatası: ' . $e->getMessage()
        ];
    }
}

/**
 * Hipcall rehberindeki kişileri SerbisERP'ye senkronize et
 */
public function syncContactsToSerbis($tenantId, $selectedContacts = [])
{
    $result = [
        'success' => 0,
        'failed' => 0,
        'skipped' => 0,
        'errors' => []
    ];
    
    foreach ($selectedContacts as $contact) {
        try {
            // Telefon numarasını al
            $phone = $contact['phone'] ?? $contact['phone_number'] ?? null;
            
            if (!$phone) {
                $result['skipped']++;
                continue;
            }
            
            // Temiz telefon numarası
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            
            // Zaten var mı kontrol et
            $existingCustomer = DB::table('customers')
                ->where('firma_id', $tenantId)
                ->where(function($q) use ($phone, $cleanPhone) {
                    $q->where('tel1', 'LIKE', "%{$cleanPhone}%")
                })
                ->first();
            
            if ($existingCustomer) {
                $result['skipped']++;
                Log::info('Müşteri zaten var, atlanıyor', [
                    'phone' => $phone,
                    'customer_id' => $existingCustomer->id
                ]);
                continue;
            }
            
            // Yeni müşteri oluştur
            $customerId = DB::table('customers')->insertGetId([
                'firma_id' => $tenantId,
                'adSoyad' => $contact['first_name'] ?? 'Hipcall',
                'tel1' => $phone,
                'eposta' => $contact['email'] ?? null,
                'firma' => $contact['company'] ?? null,
                'adres' => $contact['address'] ?? null,
                'not' => 'Hipcall rehberinden aktarıldı - ' . now()->format('d.m.Y H:i'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $result['success']++;
            
            Log::info('Hipcall rehberinden müşteri oluşturuldu', [
                'customer_id' => $customerId,
                'phone' => $phone,
                'name' => ($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? '')
            ]);
            
        } catch (\Exception $e) {
            $result['failed']++;
            $result['errors'][] = [
                'contact' => $contact['first_name'] ?? 'Bilinmeyen',
                'error' => $e->getMessage()
            ];
            
            Log::error('Hipcall contact sync error', [
                'contact' => $contact,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    return $result;
}

/**
 * Tüm sayfaları çekerek rehberin tamamını getir
 */
public function getAllContacts()
{
    $allContacts = [];
    $page = 1;
    $perPage = 100;
    
    do {
        $result = $this->getContacts($page, $perPage);
        
        if (!$result['success']) {
            break;
        }
        
        $contacts = $result['contacts'];
        $allContacts = array_merge($allContacts, $contacts);
        
        // Pagination kontrolü (API'ye göre değişebilir)
        $hasMore = count($contacts) >= $perPage;
        $page++;
        
        // Sonsuz döngüyü önle
        if ($page > 100) {
            break;
        }
        
        // Rate limit için kısa bekleme
        usleep(200000); // 200ms
        
    } while ($hasMore);
    
    return [
        'success' => true,
        'contacts' => $allContacts,
        'total' => count($allContacts)
    ];
}
    
}