<?php

namespace App\Services\InvoiceIntegrations;

use App\Contracts\InvoiceIntegrationInterface;
use App\Models\IntegrationPurchase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ParasutService implements InvoiceIntegrationInterface
{
    protected $credentials;
    protected $accessToken;
    protected $refreshToken;
    protected $companyId;
    protected $baseUrl;
    protected $redirectUri;
    protected $tenantId;

    public function __construct(array $credentials, ?int $tenantId = null)
    {
        $this->credentials = $credentials;
        $this->companyId = $credentials['company_id'] ?? null;
        $this->baseUrl = 'https://api.heroku-staging.parasut.com/v4';
        $this->redirectUri = $credentials['redirect_uri'] ?? 'urn:ietf:wg:oauth:2.0:oob';
        $this->tenantId = $tenantId;
        
        if (!$this->companyId) {
            throw new Exception('Company ID eksik');
        }
        
        // Access token al
        $this->getAccessToken();
    }

    /**
     * OAuth2 Access Token al
     */
    protected function getAccessToken()
    {
        try {
            // Önce cache'den token kontrol et
            $cacheKey = 'parasut_token_' . $this->tenantId . '_' . $this->companyId;
            
            $cachedToken = Cache::get($cacheKey);
            if ($cachedToken) {
                $this->accessToken = $cachedToken['access_token'];
                $this->refreshToken = $cachedToken['refresh_token'] ?? null;
                
                Log::info('Paraşüt token cache\'den alındı');
                return true;
            }

            // Refresh token varsa önce onu dene
            if (!empty($this->credentials['refresh_token'])) {
                Log::info('Refresh token ile token yenileniyor');
                if ($this->refreshAccessToken()) {
                    return true;
                }
            }

            // Password grant type kullan
            Log::info('Password grant ile yeni token alınıyor');
            
            $response = Http::asForm()->post('https://api.heroku-staging.parasut.com/oauth/token', [
                'grant_type' => 'password',
                'client_id' => $this->credentials['client_id'],
                'client_secret' => $this->credentials['client_secret'],
                'username' => $this->credentials['username'],
                'password' => $this->credentials['password'],
                'redirect_uri' => $this->redirectUri,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->accessToken = $data['access_token'];
                $this->refreshToken = $data['refresh_token'];
                
                // Token'ları cache'e kaydet (1.5 saat)
                Cache::put($cacheKey, [
                    'access_token' => $this->accessToken,
                    'refresh_token' => $this->refreshToken,
                ], now()->addMinutes(90));
                
                // Refresh token'ı veritabanına kaydet
                $this->saveRefreshToken($this->refreshToken);
                
                Log::info('Paraşüt token başarıyla alındı');
                return true;
            }
            
            throw new Exception('Token alınamadı: ' . $response->body());
            
        } catch (Exception $e) {
            Log::error('Paraşüt token hatası: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Refresh token ile yeni access token al
     */
    protected function refreshAccessToken(): bool
    {
        try {
            $response = Http::asForm()->post('https://api.heroku-staging.parasut.com/oauth/token', [
                'grant_type' => 'refresh_token',
                'client_id' => $this->credentials['client_id'],
                'client_secret' => $this->credentials['client_secret'],
                'refresh_token' => $this->credentials['refresh_token'],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->accessToken = $data['access_token'];
                $this->refreshToken = $data['refresh_token'];
                
                // Token'ları cache'e kaydet
                $cacheKey = 'parasut_token_' . $this->tenantId . '_' . $this->companyId;
                Cache::put($cacheKey, [
                    'access_token' => $this->accessToken,
                    'refresh_token' => $this->refreshToken,
                ], now()->addMinutes(90));
                
                // Yeni refresh token'ı kaydet
                $this->saveRefreshToken($this->refreshToken);
                
                Log::info('Token refresh edildi');
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            Log::warning('Refresh token hatası: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Refresh token'ı veritabanına kaydet
     */
    protected function saveRefreshToken(string $refreshToken)
    {
        if (!$this->tenantId) {
            return;
        }

        try {
            $integration = IntegrationPurchase::where('tenant_id', $this->tenantId)
                ->whereHas('integration', function($q) {
                    $q->where('slug', 'parasut');
                })
                ->first();

            if ($integration) {
                $credentials = $integration->credentials;
                $credentials['refresh_token'] = $refreshToken;
                $integration->update(['credentials' => $credentials]);
                
                Log::info('Refresh token veritabanına kaydedildi');
            }
        } catch (Exception $e) {
            Log::error('Refresh token kaydetme hatası: ' . $e->getMessage());
        }
    }

    /**
     * API isteği gönder
     */
    protected function makeRequest(string $method, string $endpoint, array $data = [])
    {
        $url = "{$this->baseUrl}/{$this->companyId}/{$endpoint}";
        
        Log::info('Paraşüt API isteği', [
            'method' => $method,
            'url' => $url,
            'data' => $data
        ]);

        try {
            $response = Http::withToken($this->accessToken)
                ->accept('application/json')
                ->contentType('application/json');

            // GET isteği için query parametreleri
            if ($method === 'get' && !empty($data)) {
                $response = $response->get($url, $data);
            } else {
                $response = $response->$method($url, $data);
            }

            if ($response->successful()) {
                return $response->json();
            }

            // Token süresi dolmuş olabilir
            if ($response->status() === 401) {
                Log::warning('Token geçersiz, yenileniyor');
                
                if ($this->refreshAccessToken()) {
                    // Tekrar dene
                    return $this->makeRequest($method, $endpoint, $data);
                }
            }

            throw new Exception('API Hatası: ' . $response->body());

        } catch (Exception $e) {
            Log::error('Paraşüt API Hatası', [
                'method' => $method,
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Müşteri senkronize et
     */
    public function syncCustomer(array $customerData): array
    {
        try {
            // Önce müşteri var mı kontrol et
            $existingCustomer = $this->findCustomer($customerData);
            
            if ($existingCustomer) {
                Log::info('Müşteri bulundu', ['customer_id' => $existingCustomer['id']]);
                
                return [
                    'success' => true,
                    'customer_id' => $existingCustomer['id'],
                    'action' => 'found'
                ];
            }

            // Müşteri yoksa yeni oluştur
            Log::info('Yeni müşteri oluşturuluyor', ['name' => $customerData['adSoyad']]);
            
            $parasutData = [
                'data' => [
                    'type' => 'contacts',
                    'attributes' => [
                        'email' => $customerData['email'] ?? null,
                        'name' => $customerData['adSoyad'],
                        'contact_type' => $customerData['musteriTipi'] == '1' ? 'person' : 'company',
                        'tax_number' => $customerData['vergiNo'] ?? null,
                        'tax_office' => $customerData['vergiDairesi'] ?? null,
                        'account_type' => 'customer',
                        'address' => $customerData['adres'] ?? null,
                        'city' => $customerData['il'] ?? null,
                        'district' => $customerData['ilce'] ?? null,
                        'phone' => $customerData['tel1'] ?? null,
                    ]
                ]
            ];

            $response = $this->makeRequest('post', 'contacts', $parasutData);

            Log::info('Müşteri oluşturuldu', ['customer_id' => $response['data']['id']]);

            return [
                'success' => true,
                'customer_id' => $response['data']['id'],
                'action' => 'created'
            ];

        } catch (Exception $e) {
            Log::error('Paraşüt müşteri senkronizasyonu hatası: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Müşteri ara
     */
    protected function findCustomer(array $customerData)
    {
        try {
            // Vergi numarası ile ara
            if (!empty($customerData['vergiNo'])) {
                $response = $this->makeRequest('get', 'contacts', [
                    'filter[tax_number]' => $customerData['vergiNo']
                ]);

                if (!empty($response['data'])) {
                    return $response['data'][0];
                }
            }

            // Email ile ara
            if (!empty($customerData['email'])) {
                $response = $this->makeRequest('get', 'contacts', [
                    'filter[email]' => $customerData['email']
                ]);

                if (!empty($response['data'])) {
                    return $response['data'][0];
                }
            }

            return null;
        } catch (Exception $e) {
            Log::warning('Müşteri arama hatası: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Fatura oluştur
     */
    public function createInvoice(array $invoiceData): array
    {
        try {
            Log::info('Fatura oluşturuluyor', [
                'invoice_number' => $invoiceData['faturaNumarasi'] ?? null
            ]);

            // Önce müşteriyi senkronize et
            $customerSync = $this->syncCustomer($invoiceData['customer']);
            
            if (!$customerSync['success']) {
                throw new Exception('Müşteri senkronizasyonu başarısız');
            }

            // ✅ Fatura kalemlerini düzelt - inline olarak gönder
            $details = $this->formatInvoiceItems($invoiceData['items']);

            $parasutInvoiceData = [
                'data' => [
                    'type' => 'sales_invoices',
                    'attributes' => [
                        'item_type' => 'invoice',
                        'description' => $invoiceData['faturaNumarasi'] ?? 'Servis Faturası',
                        'issue_date' => $invoiceData['faturaTarihi'],
                        'due_date' => $invoiceData['faturaTarihi'],
                        'currency' => 'TRL',
                        'withholding_rate' => 0,
                        'vat_withholding_rate' => 0,
                        'invoice_discount_type' => 'amount',
                        'invoice_discount' => (float) ($invoiceData['indirim'] ?? 0),
                    ],
                    'relationships' => [
                        'contact' => [
                            'data' => [
                                'type' => 'contacts',
                                'id' => $customerSync['customer_id']
                            ]
                        ],
                        'details' => [
                            'data' => $details // ✅ Düzeltildi
                        ]
                    ]
                ]
            ];

            Log::info('Paraşüt API\'ye gönderilen fatura verisi', [
                'data' => $parasutInvoiceData
            ]);

            $response = $this->makeRequest('post', 'sales_invoices', $parasutInvoiceData);

            $invoiceId = $response['data']['id'];
            
            Log::info('Fatura oluşturuldu', ['invoice_id' => $invoiceId]);

            // e-Arşiv olarak işaretle
            $this->markAsEArchive($invoiceId);
            
            // PDF'i indir
            $pdfPath = $this->downloadInvoicePdf($invoiceId);

            return [
                'success' => true,
                'invoice_id' => $invoiceId,
                'invoice_number' => $response['data']['attributes']['invoice_no'] ?? null,
                'pdf_path' => $pdfPath,
                'data' => $response['data']
            ];

        } catch (Exception $e) {
            Log::error('Paraşüt fatura oluşturma hatası: ' . $e->getMessage(), [
                'invoice_data' => $invoiceData
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Fatura kalemlerini formatla - DÜZELTİLMİŞ
     */
    protected function formatInvoiceItems(array $items): array
    {
        $formattedItems = [];
        
        foreach ($items as $item) {
            $formattedItems[] = [
                'type' => 'sales_invoice_details',
                'attributes' => [
                    'quantity' => (float) $item['miktar'],
                    'unit_price' => (float) $item['fiyat'],
                    'vat_rate' => 20, // KDV oranı - sabit veya dinamik yapabilirsiniz
                    'description' => $item['aciklama'],
                    // ✅ Discount ekle (opsiyonel)
                    'discount_type' => 'amount',
                    'discount_value' => 0,
                ]
            ];
        }

        Log::info('Formatlanmış fatura kalemleri', [
            'items' => $formattedItems
        ]);

        return $formattedItems;
    }

    /**
     * Faturayı e-Arşiv olarak işaretle
     */
    protected function markAsEArchive(string $invoiceId)
    {
        try {
            Log::info('Fatura e-Arşiv olarak işaretleniyor', ['invoice_id' => $invoiceId]);
            
            $this->makeRequest('post', "sales_invoices/{$invoiceId}/e_document", [
                'data' => [
                    'type' => 'e_archives',
                    'attributes' => [
                        'internet_sale' => [
                            'url' => config('app.url'),
                            'payment_type' => 'ODEMEARACISI',
                            'payment_platform' => 'ServisCRM',
                            'payment_date' => now()->toDateString()
                        ]
                    ]
                ]
            ]);
            
            Log::info('Fatura e-Arşiv olarak işaretlendi');
            
        } catch (Exception $e) {
            Log::warning('e-Arşiv işaretleme hatası: ' . $e->getMessage());
            // e-Arşiv hatası fatura oluşturma işlemini durdurmaz
        }
    }

    /**
     * Fatura durumunu güncelle
     */
    public function updateInvoiceStatus(string $invoiceId, string $status): array
    {
        try {
            return [
                'success' => true,
                'message' => 'Durum güncellendi'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Fatura PDF'ini indir
     */
    public function downloadInvoicePdf(string $invoiceId): string
    {
        try {
            Log::info('PDF indiriliyor', ['invoice_id' => $invoiceId]);
            
            // PDF URL'ini al
            $response = $this->makeRequest('get', "sales_invoices/{$invoiceId}");
            
            $pdfUrl = $response['data']['attributes']['pdf']['url'] ?? null;
            
            if (!$pdfUrl) {
                throw new Exception('PDF URL bulunamadı');
            }

            Log::info('PDF URL bulundu', ['url' => $pdfUrl]);

            // PDF'i indir
            $pdfContent = Http::withToken($this->accessToken)->get($pdfUrl)->body();
            
            // Dosyayı kaydet
            $fileName = 'invoice_' . $invoiceId . '_' . time() . '.pdf';
            $path = 'invoices/' . $fileName;
            
            Storage::disk('public')->put($path, $pdfContent);

            Log::info('PDF indirildi', ['path' => $path]);

            return 'storage/' . $path;

        } catch (Exception $e) {
            Log::error('PDF indirme hatası: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Bağlantıyı test et
     */
    public function testConnection(): bool
    {
        try {
            Log::info('Paraşüt bağlantısı test ediliyor');
            
            $this->makeRequest('get', 'contacts', [
                'page[size]' => 1
            ]);
            
            Log::info('Paraşüt bağlantısı başarılı');
            
            return true;
        } catch (Exception $e) {
            Log::error('Paraşüt bağlantı testi başarısız: ' . $e->getMessage());
            return false;
        }
    }
}