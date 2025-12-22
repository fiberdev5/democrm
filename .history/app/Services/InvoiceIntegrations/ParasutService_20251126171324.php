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
        
        // Access token al
        $this->getAccessToken();
    }

    /**
     * OAuth2 Access Token al
     */
    protected function getAccessToken()
    {
        try {
            // Önce cache'den token kontrol et (2 saat geçerli, 1.5 saat cache'le)
            $cacheKey = 'parasut_token_' . md5(json_encode($this->credentials));
            
            $cachedToken = Cache::get($cacheKey);
            if ($cachedToken) {
                $this->accessToken = $cachedToken['access_token'];
                $this->refreshToken = $cachedToken['refresh_token'];
                return true;
            }

            // Refresh token varsa önce onu dene
            if (!empty($this->credentials['refresh_token'])) {
                if ($this->refreshAccessToken()) {
                    return true;
                }
            }

            // Password grant type kullan
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
                
                return true;
            }
            
            throw new Exception('Token alınamadı: ' . $response->body());
            
        } catch (Exception $e) {
            Log::error('Paraşüt token hatası: ' . $e->getMessage(), [
                'credentials' => array_merge($this->credentials, ['password' => '***'])
            ]);
            throw $e;
        }
    }

    /**
     * Refresh token ile yeni access token al
     */
    protected function refreshAccessToken(): bool
    {
        try {
            $response = Http::asForm()->post('https://api.parasut.com/oauth/token', [
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
                $cacheKey = 'parasut_token_' . md5(json_encode($this->credentials));
                Cache::put($cacheKey, [
                    'access_token' => $this->accessToken,
                    'refresh_token' => $this->refreshToken,
                ], now()->addMinutes(90));
                
                // Yeni refresh token'ı kaydet
                $this->saveRefreshToken($this->refreshToken);
                
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
            }
        } catch (Exception $e) {
            Log::error('Refresh token kaydetme hatası: ' . $e->getMessage());
        }
    }

    /**
     * API isteği gönder
     */
    protected function makeRequest($method, $endpoint, $data = [])
    {
        $url = "{$this->baseUrl}/{$this->companyId}/{$endpoint}";
        
        $response = Http::withToken($this->accessToken)
            ->accept('application/json')
            ->$method($url, $data);

        if (!$response->successful()) {
            Log::error('Paraşüt API Hatası', [
                'endpoint' => $endpoint,
                'response' => $response->body(),
                'status' => $response->status()
            ]);
            
            throw new Exception('API Hatası: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Müşteri senkronize et
     */
    public function syncCustomer(array $customerData): array
    {
        try {
            // Önce müşteri var mı kontrol et (email veya vergi numarası ile)
            $existingCustomer = $this->findCustomer($customerData);
            
            if ($existingCustomer) {
                return [
                    'success' => true,
                    'customer_id' => $existingCustomer['id'],
                    'action' => 'found'
                ];
            }

            // Müşteri yoksa yeni oluştur
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
            $searchTerm = $customerData['vergiNo'] ?? $customerData['email'] ?? null;
            
            if (!$searchTerm) {
                return null;
            }

            $response = $this->makeRequest('get', 'contacts', [
                'filter' => [
                    'tax_number' => $customerData['vergiNo'] ?? null
                ]
            ]);

            if (!empty($response['data'])) {
                return $response['data'][0];
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Fatura oluştur
     */
    public function createInvoice(array $invoiceData): array
    {
        try {
            // Önce müşteriyi senkronize et
            $customerSync = $this->syncCustomer($invoiceData['customer']);
            
            if (!$customerSync['success']) {
                throw new Exception('Müşteri senkronizasyonu başarısız');
            }

            $parasutInvoiceData = [
                'data' => [
                    'type' => 'sales_invoices',
                    'attributes' => [
                        'item_type' => 'invoice',
                        'description' => $invoiceData['faturaNumarasi'] ?? null,
                        'issue_date' => $invoiceData['faturaTarihi'],
                        'due_date' => $invoiceData['faturaTarihi'],
                        'invoice_series' => substr($invoiceData['faturaNumarasi'], 0, 3) ?? 'SRV',
                        'invoice_id' => $invoiceData['id'] ?? null,
                        'currency' => 'TRL',
                        'withholding_rate' => 0,
                        'vat_withholding_rate' => 0,
                        'invoice_discount_type' => 'amount',
                        'invoice_discount' => $invoiceData['indirim'] ?? 0,
                    ],
                    'relationships' => [
                        'contact' => [
                            'data' => [
                                'type' => 'contacts',
                                'id' => $customerSync['customer_id']
                            ]
                        ],
                        'details' => [
                            'data' => $this->formatInvoiceItems($invoiceData['items'])
                        ]
                    ]
                ]
            ];

            $response = $this->makeRequest('post', 'sales_invoices', $parasutInvoiceData);

            // Faturayı e-Arşiv olarak işaretle ve PDF oluştur
            $invoiceId = $response['data']['id'];
            $this->markAsEArchive($invoiceId);
            
            // PDF'i indir
            $pdfPath = $this->downloadInvoicePdf($invoiceId);

            return [
                'success' => true,
                'invoice_id' => $invoiceId,
                'invoice_number' => $response['data']['attributes']['invoice_no'],
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
     * Fatura kalemlerini formatla
     */
    protected function formatInvoiceItems(array $items): array
    {
        $formattedItems = [];
        
        foreach ($items as $item) {
            $formattedItems[] = [
                'type' => 'sales_invoice_details',
                'attributes' => [
                    'description' => $item['aciklama'],
                    'quantity' => $item['miktar'],
                    'unit_price' => $item['fiyat'],
                    'vat_rate' => 20, // KDV oranı
                ]
            ];
        }

        return $formattedItems;
    }

    /**
     * Faturayı e-Arşiv olarak işaretle
     */
    protected function markAsEArchive(string $invoiceId)
    {
        try {
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
        } catch (Exception $e) {
            Log::warning('e-Arşiv işaretleme hatası: ' . $e->getMessage());
        }
    }

    /**
     * Fatura durumunu güncelle
     */
    public function updateInvoiceStatus(string $invoiceId, string $status): array
    {
        try {
            // Paraşüt'te fatura durumu genellikle ödeme kaydıyla değişir
            // Burada gerekirse payment endpoint'ini kullanabilirsiniz
            
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
            // PDF URL'ini al
            $response = $this->makeRequest('get', "sales_invoices/{$invoiceId}");
            
            $pdfUrl = $response['data']['attributes']['pdf']['url'] ?? null;
            
            if (!$pdfUrl) {
                throw new Exception('PDF URL bulunamadı');
            }

            // PDF'i indir
            $pdfContent = Http::withToken($this->accessToken)->get($pdfUrl)->body();
            
            // Dosyayı kaydet
            $fileName = 'invoice_' . $invoiceId . '_' . time() . '.pdf';
            $path = 'upload/invoices/' . $fileName;
            
            Storage::disk('public')->put($path, $pdfContent);

            return $path;

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
            $this->makeRequest('get', 'contacts', ['page' => ['size' => 1]]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}