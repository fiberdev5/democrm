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
            $cacheKey = 'parasut_token_' . $this->tenantId . '_' . $this->companyId;
            
            $cachedToken = Cache::get($cacheKey);
            if ($cachedToken) {
                $this->accessToken = $cachedToken['access_token'];
                $this->refreshToken = $cachedToken['refresh_token'] ?? null;
                Log::info('Paraşüt token cache\'den alındı');
                return true;
            }

            if (!empty($this->credentials['refresh_token'])) {
                if ($this->refreshAccessToken()) {
                    return true;
                }
            }

            Log::info('Password grant ile yeni token alınıyor');
            
            $response = Http::asForm()->post($this->baseUrl . '/oauth/token', [
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
                
                Cache::put($cacheKey, [
                    'access_token' => $this->accessToken,
                    'refresh_token' => $this->refreshToken,
                ], now()->addMinutes(90));
                
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

    protected function refreshAccessToken(): bool
    {
        try {
            $response = Http::asForm()->post($this->baseUrl . '/oauth/token', [
                'grant_type' => 'refresh_token',
                'client_id' => $this->credentials['client_id'],
                'client_secret' => $this->credentials['client_secret'],
                'refresh_token' => $this->credentials['refresh_token'],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->accessToken = $data['access_token'];
                $this->refreshToken = $data['refresh_token'];
                
                $cacheKey = 'parasut_token_' . $this->tenantId . '_' . $this->companyId;
                Cache::put($cacheKey, [
                    'access_token' => $this->accessToken,
                    'refresh_token' => $this->refreshToken,
                ], now()->addMinutes(90));
                
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

    protected function makeRequest(string $method, string $endpoint, array $data = [])
    {
        $url = "{$this->baseUrl}/{$this->companyId}/{$endpoint}";
        
        Log::info('Paraşüt API isteği', [
            'method' => $method,
            'url' => $url
        ]);

        try {
            $response = Http::withToken($this->accessToken)
                ->accept('application/json')
                ->contentType('application/json');

            if ($method === 'get' && !empty($data)) {
                $response = $response->get($url, $data);
            } else {
                $response = $response->$method($url, $data);
            }

            if ($response->successful()) {
                return $response->json();
            }

            if ($response->status() === 401) {
                Log::warning('Token geçersiz, yenileniyor');
                if ($this->refreshAccessToken()) {
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
            $existingCustomer = $this->findCustomer($customerData);
            
            if ($existingCustomer) {
                Log::info('Müşteri bulundu', ['customer_id' => $existingCustomer['id']]);
                return [
                    'success' => true,
                    'customer_id' => $existingCustomer['id'],
                    'action' => 'found'
                ];
            }

            Log::info('Yeni müşteri oluşturuluyor', ['name' => $customerData['adSoyad']]);
            
            $parasutData = [
                'data' => [
                    'type' => 'contacts',
                    'attributes' => [
                        'email' => $customerData['email'] ?? null,
                        'name' => $customerData['adSoyad'],
                        'contact_type' => $customerData['musteriTipi'] == '1' ? 'person' : 'company',
                        'tax_number' => $customerData['vergiNo'] ?? $customerData['tcNo'] ?? null,
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

            // TC No ile ara
            if (!empty($customerData['tcNo'])) {
                $response = $this->makeRequest('get', 'contacts', [
                    'filter[tax_number]' => $customerData['tcNo']
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
     * ✅ Ürünleri Paraşüt'e ekle (ESKİ KOD GİBİ)
     */
     protected function syncProduct($name)
    {
        $existing = $this->makeRequest('get', 'products', ['filter[name]' => $name]);
        
        if (!empty($existing['data'])) {
            return $existing['data'][0];
        }

        $payload = [
            'data' => [
                'type' => 'products',
                'attributes' => [
                    'name' => $name,
                    'vat_rate' => 20 // Varsayılan KDV, faturada override edilir.
                ]
            ]
        ];

        $response = $this->makeRequest('post', 'products', $payload);
        return $response['data'];
    }

    protected function getPDFFallback($invoiceId)
    {
        // PDF hemen oluşmayabilir, bu yüzden URL'yi almak için basit bir get isteği
        try {
            $response = $this->makeRequest('get', "sales_invoices/{$invoiceId}");
            return $response['data']['attributes']['pdf']['url'] ?? null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Ürün ara
     */
    protected function findProduct(string $productName)
    {
        try {
            $response = $this->makeRequest('get', 'products', [
                'filter[name]' => $productName
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
     * ✅ Fatura oluştur (ESKİ KOD MANTIĞI)
     */
    public function createInvoice(array $invoiceData): array
    {
        try {
            // 1. Müşteri Senkronizasyonu
            $contact = $this->syncCustomer($invoiceData['customer']);
            $contactId = $contact['id'];
            $isEInvoiceUser = $this->checkEInvoiceUser($invoiceData['customer']['vergiNo'] ?? $invoiceData['customer']['tcNo']);

            // 2. Ürünlerin Hazırlanması ve Fatura Detayları
            $details = [];
            $tevkifatOrani = isset($invoiceData['tevkifatOrani']) ? (int)$invoiceData['tevkifatOrani'] : 0; // Örn: 2
            
            foreach ($invoiceData['items'] as $item) {
                // Ürünü bul veya oluştur
                $product = $this->syncProduct($item['aciklama']);
                
                $lineItem = [
                    'type' => 'sales_invoice_details',
                    'attributes' => [
                        'quantity' => (float) $item['miktar'],
                        'unit_price' => (float) $item['fiyat'],
                        'vat_rate' => (float) $item['kdv_orani'] ?? 20,
                        'description' => $item['aciklama']
                    ],
                    'relationships' => [
                        'product' => ['data' => ['id' => $product['id'], 'type' => 'products']]
                    ]
                ];

                // Eğer Tevkifat varsa satır bazında eklenmeli (Oran * 10)
                if ($tevkifatOrani > 0) {
                    $lineItem['attributes']['vat_withholding_rate'] = $tevkifatOrani * 10; // 2 ise 20 gönderilir
                }

                $details[] = $lineItem;
            }

            // 3. Satış Faturası Oluştur (Sales Invoice)
            $salesInvoicePayload = [
                'data' => [
                    'type' => 'sales_invoices',
                    'attributes' => [
                        'item_type' => 'invoice',
                        'description' => $invoiceData['faturaAciklama'] ?? '',
                        'issue_date' => $invoiceData['faturaTarihi'],
                        'due_date' => $invoiceData['faturaTarihi'],
                        'currency' => 'TRL',
                        'invoice_discount_type' => 'amount',
                        'invoice_discount' => (float) ($invoiceData['indirim'] ?? 0),
                    ],
                    'relationships' => [
                        'contact' => ['data' => ['id' => $contactId, 'type' => 'contacts']],
                        'details' => ['data' => $details]
                    ]
                ]
            ];

            $invoiceResponse = $this->makeRequest('post', 'sales_invoices', $salesInvoicePayload);
            $invoiceId = $invoiceResponse['data']['id'];

            Log::info("Satış faturası oluşturuldu. ID: $invoiceId. Şimdi e-belge oluşturulacak.");

            // 4. E-Belge Oluştur (e-Fatura veya e-Arşiv)
            $eDocumentResult = $this->createEDocument($invoiceId, $invoiceData, $isEInvoiceUser);

            // 5. PDF İndir (İsteğe bağlı, URL döner)
            // PDF hemen oluşmayabilir, job kuyruğuna almak daha doğrudur ama burada linki almayı deneyelim.
            $pdfUrl = $this->getPDFFallback($invoiceId);

            return [
                'success' => true,
                'invoice_id' => $invoiceId,
                'integration_invoice_id' => $invoiceId,
                'pdf_path' => $pdfUrl,
                'message' => $isEInvoiceUser ? 'e-Fatura gönderildi.' : 'e-Arşiv gönderildi.'
            ];

        } catch (Exception $e) {
            Log::error('Paraşüt Entegrasyon Hatası: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

     protected function createEDocument($invoiceId, $data, $isEInvoiceUser)
    {
        $endpoint = $isEInvoiceUser ? 'e_invoices' : 'e_archives';
        $type = $isEInvoiceUser ? 'e_invoices' : 'e_archives';
        
        $attributes = [
            'note' => $data['faturaNotu'] ?? ($data['faturaAciklama'] ?? ''),
        ];

        // Gelen Kutusu (Sadece e-Fatura için)
        if ($isEInvoiceUser) {
             // Müşterinin ilk gelen kutusunu al (Genelde tek olur)
             // Not: checkEInvoiceUser metodunda gelen veriyi cacheleyip burada kullanmak performansı artırır.
             $inbox = $this->getEInvoiceInbox($data['customer']['vergiNo'] ?? $data['customer']['tcNo']);
             if ($inbox) {
                 $attributes['to'] = $inbox;
             }
             $attributes['scenario'] = 'basic'; // Temel Fatura (Ticari için 'commercial')
        }

        // --- SENARYOLAR ---

        // 1. Tevkifatlı Fatura Senaryosu
        if (isset($data['tevkifatOrani']) && $data['tevkifatOrani'] > 0) {
            if (empty($data['tevkifatKodu'])) {
                throw new Exception('Tevkifat oranı var ama Tevkifat Kodu seçilmemiş!');
            }

            // Tevkifat için fatura detay ID'lerine ihtiyacımız var.
            // Faturayı detaylarıyla tekrar çekiyoruz.
            $invoiceDetails = $this->makeRequest('get', "sales_invoices/{$invoiceId}?include=details");
            $lines = $invoiceDetails['data']['relationships']['details']['data'];
            
            // Tevkifat parametrelerini hazırla
            $vatWithholdingParams = [];
            foreach ($lines as $line) {
                $vatWithholdingParams[] = [
                    'detail_id' => $line['id'],
                    'vat_withholding_code' => $data['tevkifatKodu'], // Örn: 601
                    // 'vat_withholding_rate' burada tekrar gönderilmez, faturada tanımlandı.
                ];
            }
            
            $attributes['vat_withholding_params'] = $vatWithholdingParams;
        }
        // 2. İstisna (KDV 0) Senaryosu
        elseif (isset($data['kdvTutar']) && (float)$data['kdvTutar'] == 0 && !empty($data['kdvKodu'])) {
             $attributes['vat_exemption_reason_code'] = $data['kdvKodu']; // Örn: 301
             $attributes['vat_exemption_reason'] = $data['kdvAciklama'] ?? 'Muafiyet';
        }

        $payload = [
            'data' => [
                'type' => $type,
                'attributes' => $attributes,
                'relationships' => [
                    $isEInvoiceUser ? 'invoice' : 'sales_invoice' => [
                        'data' => ['id' => $invoiceId, 'type' => 'sales_invoices']
                    ]
                ]
            ]
        ];

        return $this->makeRequest('post', $endpoint, $payload);
    }

    /**
     * Müşterinin e-Fatura mükellefi olup olmadığını kontrol et
     */
    protected function checkEInvoiceUser($vkn)
    {
        if (empty($vkn)) return false;
        
        $response = $this->makeRequest('get', 'e_invoice_inboxes', ['filter[vkn]' => $vkn]);
        
        return !empty($response['data']);
    }

    protected function getEInvoiceInbox($vkn)
    {
        $response = $this->makeRequest('get', 'e_invoice_inboxes', ['filter[vkn]' => $vkn]);
        return $response['data'][0]['attributes']['e_invoice_address'] ?? null;
    }

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
        }
    }

    public function updateInvoiceStatus(string $invoiceId, string $status): array
    {
        return [
            'success' => true,
            'message' => 'Durum güncellendi'
        ];
    }

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