<?php

namespace App\Services;

use App\Models\IntegrationPurchase;
use App\Models\HipcallCallLog;
use Illuminate\Support\Facades\Auth;
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
        $phone = $contact['phone'] ?? null;

        if (!$phone) {
            $result['skipped']++;
            $result['errors'][] = [
                'contact' => ($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''),
                'error' => 'Telefon numarası yok'
            ];
            continue;
        }

        // Telefonu temizle
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (Str::startsWith($cleanPhone, '90')) {
            $cleanPhone = substr($cleanPhone, 2);
        } elseif (Str::startsWith($cleanPhone, '0')) {
            $cleanPhone = substr($cleanPhone, 1);
        }

        // Zaten var mı kontrol et
        $existingCustomer = DB::table('customers')
            ->where('firma_id', $tenantId)
            ->where('tel1', 'LIKE', "%{$cleanPhone}%")
            ->first();

        if ($existingCustomer) {
            $result['skipped']++;
            continue;
        }

        $phone2 = null;
        if (isset($contact['phones']) && is_array($contact['phones']) && count($contact['phones']) > 1) {
            $phone2 = $contact['phones'][1]['number'] ?? null;
        }

        $user_id = Auth::user()->user_id;

        // Yeni müşteri kaydı
        $customerId = DB::table('customers')->insertGetId([
            'firma_id' => $tenantId,
            'adSoyad' => trim(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? '')) ?: 'Hipcall',
            'personel_id' => $user_id,
            'tel1' => $cleanPhone,
            'tel2' => $phone2,
            'not' => 'Hipcall rehberinden aktarıldı - ' . now()->format('d.m.Y H:i'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $result['success']++;

        Log::info('Hipcall rehberinden müşteri oluşturuldu', [
            'customer_id' => $customerId,
            'phone' => $cleanPhone,
            'name' => ($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? '')
        ]);

    } catch (\Exception $e) {
        $result['failed']++;
        $result['errors'][] = [
            'contact' => ($contact['first_name'] ?? 'Bilinmeyen') . ' ' . ($contact['last_name'] ?? ''),
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
/**
 * Tüm sayfaları çekerek rehberin tamamını getir (detaylarıyla)
 */
public function getAllContacts()
{
    $allContacts = [];
    $page = 1;
    $perPage = 50;
    $maxPages = 20;
    
    do {
        Log::info("Hipcall contacts sayfa {$page} çekiliyor...");
        
        // Önce listeyi çek
        $response = Http::timeout(15)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])
            ->get($this->baseUrl . '/contacts', [
                'page' => $page,
                'per_page' => $perPage
            ]);
        
        if (!$response->successful()) {
            break;
        }
        
        $data = $response->json();
        $contactsList = $data['data'] ?? $data['contacts'] ?? [];
        
        // Her contact için detayını çek
        foreach ($contactsList as $contact) {
            $contactId = $contact['id'] ?? null;
            
            if ($contactId) {
                // Detay çek: /api/v3/contacts/{id}
                $detailResponse = Http::timeout(10)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Accept' => 'application/json',
                    ])
                    ->get($this->baseUrl . "/contacts/{$contactId}");
                
                if ($detailResponse->successful()) {
                    $detailData = $detailResponse->json();
                    // API response: {"data": {...}}
                    $fullContact = $detailData['data'] ?? $detailData;
                    
                    // Telefon numarasını phones array'inden çıkar
                    if (isset($fullContact['phones']) && is_array($fullContact['phones']) && count($fullContact['phones']) > 0) {
                        $fullContact['phone'] = $fullContact['phones'][0]['number'] ?? null;
                    }
                    
                    // Email'i emails array'inden çıkar
                    if (isset($fullContact['emails']) && is_array($fullContact['emails']) && count($fullContact['emails']) > 0) {
                        $fullContact['email'] = $fullContact['emails'][0]['email'] ?? null;
                    }
                    
                    // Company name'i çıkar
                    if (isset($fullContact['company']['name'])) {
                        $fullContact['company_name'] = $fullContact['company']['name'];
                    }
                    
                    $allContacts[] = $fullContact;
                } else {
                    $allContacts[] = $contact;
                }
                
                usleep(150000); // 150ms
            } else {
                $allContacts[] = $contact;
            }
        }
        
        Log::info("Sayfa {$page}: " . count($contactsList) . " kişi çekildi. Toplam: " . count($allContacts));
        
        $hasMore = count($contactsList) >= $perPage;
        $page++;
        
        if ($page > $maxPages) {
            break;
        }
        
        if ($hasMore) {
            sleep(1);
        }
        
    } while ($hasMore);
    
    return [
        'success' => true,
        'contacts' => $allContacts,
        'total' => count($allContacts)
    ];
}
    
}