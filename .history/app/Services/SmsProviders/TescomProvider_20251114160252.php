<?php
// app/Services/SmsProviders/TescomProvider.php

namespace App\Services\SmsProviders;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TescomProvider implements SmsProviderInterface
{
    protected $credentials;
    protected $apiUrl = 'https://smspanel.tescom.com.tr:9588';

    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
    }

    public function sendBulkSms(array $phones, string $message): array
    {
        try {
            // Credentials'tan gerekli bilgileri al
            $sender = $this->credentials['sender_name'] ?? '';
            $username = $this->credentials['username'] ?? '';
            $password = $this->credentials['password'] ?? '';
            
            // Gateway UUID (opsiyonel)
            $gateway = $this->credentials['gateway'] ?? null;

    

            // Telefon numaralarını formatla
            $formattedPhones = $this->formatPhoneNumbers($phones);

            // Request payload hazırla
            $payload = [
                'type' => 1,
                'sendingType' => 1,
                'title' => 'Toplu SMS - ' . date('Y-m-d H:i'),
                'content' => $message,
                'numbers' => $formattedPhones,
                'encoding' => $this->detectEncoding($message),
                'sender' => $sender,
                'validity' => 1440, // 24 saat
                'commercial' => false,
                'skipAhsQuery' => false,
                'recipientType' => 0,
                'customID' => 'bulk_' . uniqid()
            ];

            // Gateway varsa ekle
            if (!empty($gateway)) {
                $payload['gateway'] = $gateway;
            }

            // Periyodik gönderim ayarları (yüksek adetli gönderimler için)
            if (count($formattedPhones) > 1000) {
                $payload['periodicSettings'] = [
                    'periodType' => 0,
                    'interval' => 1, // 1 dakika aralıklarla
                    'amount' => 1000 // 1000'erli gönder
                ];
            }

            Log::info('Tescom SMS gönderiliyor', [
                'telefon_sayisi' => count($formattedPhones),
                'sender' => $sender
            ]);

            // API isteği gönder
            $response = Http::withBasicAuth($this->credentials['username'], $this->credentials['password'])
            ->timeout(30)
            ->post($this->apiUrl . '/sms/create', $payload);

            $result = $response->json();

            // Başarılı durum kontrolü
            if ($response->successful() && isset($result['data']['pkgID'])) {
                Log::info('Tescom Toplu SMS Başarılı', [
                    'pkgID' => $result['data']['pkgID'],
                    'telefon_sayisi' => count($phones)
                ]);

                return [
                    'success' => true,
                    'message' => count($phones) . ' kişiye SMS başarıyla gönderildi',
                    'response_code' => $result['data']['pkgID'],
                    'package_id' => $result['data']['pkgID']
                ];
            } else {
                $errorMessage = $result['err']['message'] ?? 'Bilinmeyen hata';
                $errorCode = $result['err']['code'] ?? 'UNKNOWN';
                
                Log::error('Tescom SMS Hatası', [
                    'error_code' => $errorCode,
                    'error_message' => $errorMessage,
                    'status' => $result['err']['status'] ?? null
                ]);

                return [
                    'success' => false,
                    'message' => 'SMS gönderilemedi: ' . $errorMessage,
                    'error_code' => $errorCode
                ];
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Tescom SMS Bağlantı Hatası', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'SMS servisine bağlanılamadı. Lütfen daha sonra tekrar deneyin.'
            ];

        } catch (\Exception $e) {
            Log::error('Tescom SMS Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'SMS gönderilirken bir hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function sendSingleSms(string $phone, string $message): array
    {
        return $this->sendBulkSms([$phone], $message);
    }

    /**
     * Telefon numaralarını formatla
     */
    protected function formatPhoneNumbers(array $phones): array
    {
        return array_map(function($phone) {
            // Sadece rakamları al
            $phone = preg_replace('/[^0-9]/', '', $phone);
            
            // 0 ile başlamıyorsa ekle
            if (strlen($phone) == 10 && substr($phone, 0, 1) !== '0') {
                $phone = '0' . $phone;
            }
            
            // +90 ile başlıyorsa sadece 0 ile değiştir
            if (substr($phone, 0, 2) == '90' && strlen($phone) == 12) {
                $phone = '0' . substr($phone, 2);
            }
            
            return $phone;
        }, $phones);
    }

    /**
     * Mesaj içeriğine göre encoding belirle
     */
    protected function detectEncoding(string $message): int
    {
        // Türkçe karakterler var mı kontrol et
        if (preg_match('/[ğüşöçıİĞÜŞÖÇ]/u', $message)) {
            return 1; // Türkçe encoding
        }
        
        // Unicode karakterler var mı kontrol et
        if (!mb_check_encoding($message, 'ASCII')) {
            return 2; // UTF-8 encoding
        }
        
        return 0; // Default encoding
    }

    /**
     * SMS paket durumunu sorgula
     */
    public function checkStatus(string $packageId): array
    {
        try {
            $apiKey = $this->credentials['api_key'] ?? '';
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ])
            ->get($this->apiUrl . '/sms/status/' . $packageId);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'data' => $result['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => 'Durum sorgulanamadı'
            ];

        } catch (\Exception $e) {
            Log::error('Tescom SMS durum sorgulama hatası', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Durum sorgulama hatası: ' . $e->getMessage()
            ];
        }
    }
}