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
        $this->baseUrl = 'https://api.heroku-staging.parasut.com';
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
        $url = "{$this->baseUrl}/v4/{$this->companyId}/{$endpoint}";
        
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
        // Önce müşteriyi ara
        $existingCustomer = $this->findCustomer($customerData);
        
        if ($existingCustomer) {
            Log::info('Müşteri Paraşüt\'te zaten mevcut', [
                'customer_id' => $existingCustomer['id'],
                'name' => $existingCustomer['attributes']['name'] ?? 'N/A'
            ]);
            
            return [
                'success' => true,
                'customer_id' => $existingCustomer['id'],
                'action' => 'found',
                'message' => 'Müşteri zaten Paraşüt\'te kayıtlı'
            ];
        }

        Log::info('Yeni müşteri Paraşüt\'e ekleniyor', ['name' => $customerData['adSoyad']]);
        
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

        Log::info('Müşteri Paraşüt\'e eklendi', ['customer_id' => $response['data']['id']]);

        return [
            'success' => true,
            'customer_id' => $response['data']['id'],
            'action' => 'created',
            'message' => 'Müşteri Paraşüt\'e yeni eklendi'
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
 * Müşteri ara - Geliştirilmiş versiyon
 */
protected function findCustomer(array $customerData)
{
    try {
        $searchCriteria = [];
        
        // 1. Vergi numarası ile ara (Kurumsal müşteriler için)
        if (!empty($customerData['vergiNo'])) {
            Log::info('Vergi numarası ile müşteri aranıyor', ['vergiNo' => $customerData['vergiNo']]);
            
            $response = $this->makeRequest('get', 'contacts', [
                'filter[tax_number]' => $customerData['vergiNo']
            ]);

            if (!empty($response['data']) && count($response['data']) > 0) {
                Log::info('Müşteri vergi numarası ile bulundu', [
                    'customer_id' => $response['data'][0]['id']
                ]);
                return $response['data'][0];
            }
        }

        // 2. TC No ile ara (Bireysel müşteriler için)
        if (!empty($customerData['tcNo'])) {
            Log::info('TC No ile müşteri aranıyor', ['tcNo' => $customerData['tcNo']]);
            
            $response = $this->makeRequest('get', 'contacts', [
                'filter[tax_number]' => $customerData['tcNo']
            ]);

            if (!empty($response['data']) && count($response['data']) > 0) {
                Log::info('Müşteri TC No ile bulundu', [
                    'customer_id' => $response['data'][0]['id']
                ]);
                return $response['data'][0];
            }
        }

        // 3. İsim ve telefon ile ara (son şans)
        if (!empty($customerData['adSoyad']) && !empty($customerData['tel1'])) {
            Log::info('İsim ile müşteri aranıyor', ['name' => $customerData['adSoyad']]);
            
            $response = $this->makeRequest('get', 'contacts', [
                'filter[name]' => $customerData['adSoyad']
            ]);

            if (!empty($response['data'])) {
                // Telefon numarası eşleşmesi kontrolü
                foreach ($response['data'] as $customer) {
                    $phone = $customer['attributes']['phone'] ?? '';
                    // Telefon numarasını temizle (boşluk, tire vb. kaldır)
                    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                    $cleanCustomerPhone = preg_replace('/[^0-9]/', '', $customerData['tel1']);
                    
                    if ($cleanPhone === $cleanCustomerPhone) {
                        Log::info('Müşteri isim ve telefon ile bulundu', [
                            'customer_id' => $customer['id']
                        ]);
                        return $customer;
                    }
                }
            }
        }

        Log::info('Müşteri Paraşüt\'te bulunamadı, yeni müşteri olarak eklenecek');
        return null;
        
    } catch (Exception $e) {
        Log::warning('Müşteri arama hatası: ' . $e->getMessage());
        return null;
    }
}

    /**
     * ✅ Ürünleri Paraşüt'e ekle (ESKİ KOD GİBİ)
     */
    protected function syncProducts(array $items): array
    {
        $syncedProducts = [];
        
        foreach ($items as $item) {
            try {
                // Önce ürünü ara
                $existingProduct = $this->findProduct($item['aciklama']);
                
                if ($existingProduct) {
                    $syncedProducts[] = $existingProduct['id'];
                    Log::info('Ürün bulundu', [
                        'product_id' => $existingProduct['id'],
                        'name' => $item['aciklama']
                    ]);
                    continue;
                }

                // Ürün yoksa oluştur
                $productData = [
                    'data' => [
                        'type' => 'products',
                        'attributes' => [
                            'code' => '', // Boş bırakabilirsiniz
                            'name' => $item['aciklama'],
                            'vat_rate' => 20, // KDV oranı
                        ]
                    ]
                ];

                $response = $this->makeRequest('post', 'products', $productData);
                $productId = $response['data']['id'];
                
                $syncedProducts[] = $productId;
                
                Log::info('Ürün oluşturuldu', [
                    'product_id' => $productId,
                    'name' => $item['aciklama']
                ]);

                // API rate limit için kısa bekleme
                usleep(200000); // 0.2 saniye
                
            } catch (Exception $e) {
                Log::error('Ürün senkronizasyon hatası', [
                    'product_name' => $item['aciklama'],
                    'error' => $e->getMessage()
                ]);
                // Ürün eklenemese bile devam et
                $syncedProducts[] = null;
            }
        }

        return $syncedProducts;
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
        Log::info('Fatura oluşturuluyor', [
            'invoice_number' => $invoiceData['faturaNumarasi'] ?? null
        ]);

        // 1. Müşteriyi senkronize et
        $customerSync = $this->syncCustomer($invoiceData['customer']);
        
        if (!$customerSync['success']) {
            throw new Exception('Müşteri senkronizasyonu başarısız: ' . ($customerSync['error'] ?? 'Bilinmeyen hata'));
        }

        // Müşteri durumunu logla
        Log::info('Müşteri durumu', [
            'customer_id' => $customerSync['customer_id'],
            'action' => $customerSync['action'],
            'message' => $customerSync['message'] ?? ''
        ]);

        // 2. Ürünleri senkronize et
        $productIds = $this->syncProducts($invoiceData['items']);

        // 3. Fatura detaylarını hazırla
        $details = [];
        foreach ($invoiceData['items'] as $index => $item) {
            $detail = [
                'type' => 'sales_invoice_details',
                'attributes' => [
                    'quantity' => (float) $item['miktar'],
                    'unit_price' => (float) $item['fiyat'],
                    'vat_rate' => (float) ($invoiceData['kdvTutar'] ?? 20),
                    'description' => $item['aciklama'] ?? '',
                ]
            ];

            // Ürün ID'sini ekle
            if (!empty($productIds[$index])) {
                $detail['relationships'] = [
                    'product' => [
                        'data' => [
                            'id' => $productIds[$index],
                            'type' => 'products'
                        ]
                    ]
                ];
            }

            $details[] = $detail;
        }

        // 4. Tevkifat oranını hesapla (Paraşüt formatına uygun)
        $withholding_rate = 0;
        if (!empty($invoiceData['vat_withholding_rate']) && $invoiceData['vat_withholding_rate'] > 0) {
            // Örn: 2/10 = %20, 3/10 = %30, 5/10 = %50
            $withholding_rate = ($invoiceData['vat_withholding_rate'] * 10);
        }

        // 5. Faturayı oluştur
        $parasutInvoiceData = [
            'data' => [
                'type' => 'sales_invoices',
                'attributes' => [
                    'item_type' => 'invoice',
                    'description' => $invoiceData['faturaAciklama'] ?? ($invoiceData['faturaNumarasi'] ?? 'Servis Faturası'),
                    'issue_date' => $invoiceData['faturaTarihi'],
                    'due_date' => $invoiceData['faturaTarihi'],
                    'currency' => 'TRL',
                    'withholding_rate' => 0, // Stopaj oranı (genelde 0)
                    'vat_withholding_rate' => $withholding_rate,
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
                        'data' => $details
                    ]
                ]
            ]
        ];

        Log::info('Paraşüt API\'ye fatura gönderiliyor', [
            'customer_id' => $customerSync['customer_id'],
            'items_count' => count($details),
            'total' => $invoiceData['genelToplam'] ?? 0
        ]);

        $response = $this->makeRequest('post', 'sales_invoices', $parasutInvoiceData);

        $invoiceId = $response['data']['id'];
        
        Log::info('Fatura Paraşüt\'te oluşturuldu', [
            'invoice_id' => $invoiceId,
            'invoice_no' => $response['data']['attributes']['invoice_no'] ?? null
        ]);
       

        return [
            'success' => true,
            'invoice_id' => $invoiceId,
            'invoice_number' => $response['data']['attributes']['invoice_no'] ?? null,
            'customer_action' => $customerSync['action'], // 'found' veya 'created'
            'data' => $response['data']
        ];

    } catch (Exception $e) {
        Log::error('Paraşüt fatura oluşturma hatası: ' . $e->getMessage(), [
            'invoice_data' => $invoiceData,
            'trace' => $e->getTraceAsString()
        ]);
        
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

public function findInvoiceByNumber(string $invoiceNumber): ?array
{
    try {
        Log::info('Paraşüt\'te fatura aranıyor', ['invoice_number' => $invoiceNumber]);
        
        $response = $this->makeRequest('get', 'sales_invoices', [
            'filter[invoice_no]' => $invoiceNumber,
            'page[size]' => 1
        ]);

        if (!empty($response['data']) && count($response['data']) > 0) {
            Log::info('Fatura bulundu', [
                'parasut_id' => $response['data'][0]['id']
            ]);
            return $response['data'][0];
        }

        Log::warning('Fatura bulunamadı', ['invoice_number' => $invoiceNumber]);
        return null;

    } catch (Exception $e) {
        Log::error('Fatura arama hatası: ' . $e->getMessage());
        return null;
    }
}

/**
 * Fatura ID'sini getir veya ara
 */
public function getInvoiceId($invoice): ?string
{
    // Zaten kaydedilmiş ID varsa döndür
    if (!empty($invoice->integration_invoice_id)) {
        return $invoice->integration_invoice_id;
    }

    // Yoksa Paraşüt'te ara
    $parasutInvoice = $this->findInvoiceByNumber($invoice->faturaNumarasi);
    
    if ($parasutInvoice) {
        $parasutId = $parasutInvoice['id'];
        
        // Bulunan ID'yi veritabanına kaydet
        $invoice->integration_invoice_id = $parasutId;
        $invoice->save();
        
        Log::info('Paraşüt fatura ID\'si veritabanına kaydedildi', [
            'invoice_id' => $invoice->id,
            'parasut_id' => $parasutId
        ]);
        
        return $parasutId;
    }

    return null;
}

public function addPayment(string $invoiceId, array $paymentData): array
{
    try {
        Log::info('Faturaya ödeme ekleniyor', [
            'invoice_id' => $invoiceId,
            'amount' => $paymentData['amount']
        ]);

        $parasutPaymentData = [
            'data' => [
                'type' => 'payments',
                'attributes' => [
                    'description' => $paymentData['description'] ?? 'Tahsilat',
                    'account_id' => $paymentData['account_id'],
                    'date' => $paymentData['date'],
                    'amount' => (float) $paymentData['amount'],
                    'exchange_rate' => $paymentData['exchange_rate'] ?? null,
                ]
            ]
        ];

        $response = $this->makeRequest('post', "sales_invoices/{$invoiceId}/payments", $parasutPaymentData);

        Log::info('Ödeme başarıyla eklendi', [
            'payment_id' => $response['data']['id']
        ]);

        return [
            'success' => true,
            'payment_id' => $response['data']['id'],
            'data' => $response['data']
        ];

    } catch (Exception $e) {
        Log::error('Paraşüt ödeme ekleme hatası: ' . $e->getMessage());
        
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Kasa/Banka hesaplarını getir
 */
public function getAccounts(): array
{
    try {
        $response = $this->makeRequest('get', 'accounts', [
           
        ]);

        return [
            'success' => true,
            'accounts' => $response['data'] ?? []
        ];

    } catch (Exception $e) {
        Log::error('Hesaplar getirme hatası: ' . $e->getMessage());
        
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'accounts' => []
        ];
    }
}

/**
 * Faturanın ödemelerini getir
 */
public function getInvoicePayments(string $invoiceId): array
{
    try {
        $response = $this->makeRequest('get', "sales_invoices/{$invoiceId}", [
            'include' => 'payments'
        ]);

        $payments = [];
        if (!empty($response['included'])) {
            foreach ($response['included'] as $item) {
                if ($item['type'] === 'payments') {
                    $payments[] = $item;
                }
            }
        }

        return [
            'success' => true,
            'payments' => $payments,
            'remaining_amount' => $response['data']['attributes']['remaining'] ?? 0
        ];

    } catch (Exception $e) {
        Log::error('Fatura ödemeleri getirme hatası: ' . $e->getMessage());
        
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'payments' => []
        ];
    }
}

public function deleteInvoicePayment($invoiceId, $paymentId)
{
    try {
        Log::info('Tahsilat silme işlemi başlatıldı', [
            'invoice_id' => $invoiceId,
            'payment_id' => $paymentId,
            'payment_id_type' => gettype($paymentId)
        ]);

        // Önce tahsilat detaylarını transaction ile birlikte al
        $url = "{$this->baseUrl}/v4/{$this->companyId}/sales_invoices/{$invoiceId}?include=payments.transaction";
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken, // ✅ DÜZELTİLDİ
            'Accept' => 'application/json',
        ])->get($url);

        usleep(200000); // Rate limit
        
        if (!$response->successful()) {
            Log::error('Paraşüt tahsilat bilgisi alınamadı', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return [
                'success' => false,
                'message' => 'Tahsilat bilgisi alınamadı'
            ];
        }

        $data = $response->json();
        
        // Payment'ı ve transaction'ı bul
        $transactionId = null;
        $foundPayment = null;
        
        if (isset($data['included'])) {
            foreach ($data['included'] as $item) {
                if ($item['type'] === 'payments') {
                    // ✅ Hem string hem integer karşılaştırması
                    if ($item['id'] == $paymentId || (string)$item['id'] === (string)$paymentId) {
                        $foundPayment = $item;
                        // Payment'ın transaction relationship'ini bul
                        if (isset($item['relationships']['transaction']['data']['id'])) {
                            $transactionId = $item['relationships']['transaction']['data']['id'];
                            break;
                        }
                    }
                }
            }
        }

        if (!$transactionId) {
            Log::error('Transaction ID bulunamadı', [
                'payment_id' => $paymentId,
                'invoice_id' => $invoiceId,
                'found_payment' => $foundPayment,
                'all_payments' => array_filter($data['included'] ?? [], function($item) {
                    return $item['type'] === 'payments';
                })
            ]);
            
            return [
                'success' => false,
                'message' => 'Tahsilat işlemi bulunamadı. Payment ID eşleşmedi.'
            ];
        }

        Log::info('Transaction bulundu', [
            'transaction_id' => $transactionId,
            'payment_id' => $paymentId
        ]);

        // Transaction'ı sil
        $deleteUrl = "{$this->baseUrl}/v4/{$this->companyId}/transactions/{$transactionId}";
        
        $deleteResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken, // ✅ DÜZELTİLDİ
            'Accept' => 'application/json',
        ])->delete($deleteUrl);

        usleep(200000); // Rate limit

        if ($deleteResponse->successful() || $deleteResponse->status() === 204) {
            Log::info('Paraşüt tahsilat silindi', [
                'payment_id' => $paymentId,
                'transaction_id' => $transactionId
            ]);
            
            return [
                'success' => true,
                'message' => 'Tahsilat başarıyla silindi',
                'transaction_id' => $transactionId
            ];
        }

        Log::error('Paraşüt tahsilat silinemedi', [
            'status' => $deleteResponse->status(),
            'body' => $deleteResponse->body()
        ]);

        return [
            'success' => false,
            'message' => 'Tahsilat silinemedi: ' . $deleteResponse->body()
        ];

    } catch (\Exception $e) {
        Log::error('Paraşüt tahsilat silme hatası: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        
        return [
            'success' => false,
            'message' => 'Bir hata oluştu: ' . $e->getMessage()
        ];
    }
}

/**
 * Müşterinin e-Fatura mükellefiyetini kontrol et
 */
public function checkCustomerEInvoiceStatus(string $contactId): array
{
    try {
        $response = $this->makeRequest('get', "contacts/{$contactId}");
        
        // Paraşüt'te e-fatura mükellefiyeti kontrolü
        $isEInvoiceUser = $response['data']['attributes']['e_invoice_user'] ?? false;
        
        Log::info('Müşteri e-fatura durumu kontrol edildi', [
            'contact_id' => $contactId,
            'is_e_invoice_user' => $isEInvoiceUser
        ]);
        
        return [
            'success' => true,
            'is_e_invoice_user' => $isEInvoiceUser,
            'type' => $isEInvoiceUser ? 'e-invoice' : 'e-archive'
        ];
        
    } catch (Exception $e) {
        Log::error('E-fatura durumu kontrol hatası: ' . $e->getMessage());
        
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'is_e_invoice_user' => false,
            'type' => 'e-archive' // Varsayılan olarak e-arşiv
        ];
    }
}

/**
 * e-Fatura olarak resmileştir
 */
public function formalizeAsEInvoice(string $invoiceId): array
{
    try {
        Log::info('Fatura e-Fatura olarak resmileştiriliyor', ['invoice_id' => $invoiceId]);
        
        $response = $this->makeRequest('post', "sales_invoices/{$invoiceId}/e_document", [
            'data' => [
                'type' => 'e_invoices'
            ]
        ]);
        
        Log::info('Fatura e-Fatura olarak resmileştirildi', [
            'invoice_id' => $invoiceId
        ]);
        
        return [
            'success' => true,
            'type' => 'e-invoice',
            'message' => 'Fatura e-Fatura olarak resmileştirildi'
        ];
        
    } catch (Exception $e) {
        Log::error('e-Fatura resmileştirme hatası: ' . $e->getMessage());
        
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * e-Arşiv olarak resmileştir
 */
public function formalizeAsEArchive(string $invoiceId): array
{
    try {
        Log::info('Fatura e-Arşiv olarak resmileştiriliyor', ['invoice_id' => $invoiceId]);
        
        $response = $this->makeRequest('post', "sales_invoices/{$invoiceId}/e_document", [
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
        
        Log::info('Fatura e-Arşiv olarak resmileştirildi', [
            'invoice_id' => $invoiceId
        ]);
        
        return [
            'success' => true,
            'type' => 'e-archive',
            'message' => 'Fatura e-Arşiv olarak resmileştirildi'
        ];
        
    } catch (Exception $e) {
        Log::error('e-Arşiv resmileştirme hatası: ' . $e->getMessage());
        
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Resmileştirme durumunu kontrol et
 */
public function checkFormalizationStatus(string $invoiceId): array
{
    try {
        $response = $this->makeRequest('get', "sales_invoices/{$invoiceId}", [
            'include' => 'e_document'
        ]);
        
        $eDocument = null;
        if (!empty($response['included'])) {
            foreach ($response['included'] as $item) {
                if (in_array($item['type'], ['e_invoices', 'e_archives'])) {
                    $eDocument = $item;
                    break;
                }
            }
        }
        
        if ($eDocument) {
            $status = $eDocument['attributes']['status'] ?? 'unknown';
            $type = $eDocument['type'] === 'e_invoices' ? 'e-invoice' : 'e-archive';
            
            // Paraşüt durum değerleri: waiting_for_approval, approved, rejected
            $statusMap = [
                'waiting_for_approval' => 'pending',
                'approved' => 'sent',
                'rejected' => 'error',
                'unknown' => 'pending'
            ];
            
            return [
                'success' => true,
                'formalized' => true,
                'status' => $statusMap[$status] ?? 'pending',
                'type' => $type,
                'pdf_url' => $eDocument['attributes']['pdf_url'] ?? null
            ];
        }
        
        return [
            'success' => true,
            'formalized' => false,
            'status' => null,
            'type' => null
        ];
        
    } catch (Exception $e) {
        Log::error('Resmileştirme durumu kontrol hatası: ' . $e->getMessage());
        
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
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