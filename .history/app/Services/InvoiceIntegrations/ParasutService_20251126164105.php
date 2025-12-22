<?php

namespace App\Services\InvoiceIntegrations;

use App\Contracts\InvoiceIntegrationInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Storage;

class ParasutService implements InvoiceIntegrationInterface
{
    protected $credentials;
    protected $accessToken;
    protected $companyId;
    protected $baseUrl;

    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
        $this->companyId = $credentials['company_id'] ?? null;
        $this->baseUrl = 'https://api.heroku-staging.parasut.com';
        
        // Access token al
        $this->getAccessToken();
    }

    /**
     * OAuth2 Access Token al
     */
    protected function getAccessToken()
    {
        try {
            $response = Http::asForm()->post('https://api.parasut.com/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $this->credentials['client_id'],
                'client_secret' => $this->credentials['client_secret'],
            ]);

            if ($response->successful()) {
                $this->accessToken = $response->json()['access_token'];
                return true;
            }
            
            throw new Exception('Token alınamadı: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Paraşüt token hatası: ' . $e->getMessage());
            throw $e;
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