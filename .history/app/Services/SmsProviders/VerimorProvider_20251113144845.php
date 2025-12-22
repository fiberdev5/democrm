<?php
// app/Services/SmsProviders/VerimorProvider.php

namespace App\Services\SmsProviders;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VerimorProvider implements SmsProviderInterface
{
    protected $credentials;

    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * Toplu SMS gönderimi
     */
    public function sendBulkSms(array $phones, string $message): array
    {
        try {
            $username = $this->credentials['username'] ?? '';
            $password = $this->credentials['password'] ?? '';
            $sender = $this->credentials['sender_name'] ?? '';

            if (empty($username) || empty($password)) {
                return [
                    'success' => false,
                    'message' => 'SMS API bilgileri eksik'
                ];
            }

            // Telefon numaralarını Verimor formatına çevir (905xxxxxxxxx)
            $formattedPhones = $this->formatPhones($phones);

            if (empty($formattedPhones)) {
                return [
                    'success' => false,
                    'message' => 'Geçerli telefon numarası bulunamadı'
                ];
            }

            // Mesaj boyutunu kontrol et
            $messageLength = mb_strlen($message, 'UTF-8');
            if ($messageLength > 1071) {
                return [
                    'success' => false,
                    'message' => 'Mesaj çok uzun (Maksimum 1071 karakter)'
                ];
            }

            // Verimor API'ye POST isteği (JSON formatında)
            $payload = [
                'username' => $username,
                'password' => $password,
                'source_addr' => $sender,
                'messages' => [
                    [
                        'dest' => implode(',', $formattedPhones),
                        'msg' => $message
                    ]
                ]
            ];

            Log::info('Verimor SMS İsteği', [
                'telefon_sayisi' => count($formattedPhones),
                'mesaj_uzunlugu' => $messageLength
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post('https://sms.verimor.com.tr/v2/send.json', $payload);

            // Verimor JSON response döner
            if ($response->successful()) {
                $result = $response->json();
                
                // Başarılı response kontrolü
                if (isset($result['http_code']) && $result['http_code'] == 200) {
                    Log::info('Verimor Toplu SMS Başarılı', [
                        'telefon_sayisi' => count($formattedPhones),
                        'response' => $result
                    ]);

                    return [
                        'success' => true,
                        'message' => count($formattedPhones) . ' kişiye SMS başarıyla gönderildi',
                        'response_code' => $result['http_code'] ?? null,
                        'campaign_id' => $result['message'] ?? null // Verimor kampanya ID'si döner
                    ];
                } else {
                    // Hata mesajını Verimor'dan gelen bilgiye göre oluştur
                    $errorMessage = $result['error']['message'] ?? 'Bilinmeyen hata';
                    $errorCode = $result['error']['code'] ?? 'UNKNOWN';
                    
                    Log::error('Verimor SMS Hatası', [
                        'error_code' => $errorCode,
                        'error_message' => $errorMessage,
                        'response' => $result
                    ]);

                    return [
                        'success' => false,
                        'message' => 'SMS gönderilemedi: ' . $this->getErrorMessage($errorCode, $errorMessage)
                    ];
                }
            } else {
                Log::error('Verimor HTTP Hatası', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'SMS gönderilemedi. HTTP Hata: ' . $response->status()
                ];
            }

        } catch (\Exception $e) {
            Log::error('Verimor SMS Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'SMS gönderilirken bir hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Tekli SMS gönderimi
     */
    public function sendSingleSms(string $phone, string $message): array
    {
        return $this->sendBulkSms([$phone], $message);
    }

    /**
     * Telefon numaralarını Verimor formatına çevir (905xxxxxxxxx)
     */
    protected function formatPhones(array $phones): array
    {
        $formatted = [];

        foreach ($phones as $phone) {
            // Sadece rakamları al
            $cleaned = preg_replace('/[^0-9]/', '', $phone);

            // 0 ile başlıyorsa kaldır
            if (substr($cleaned, 0, 1) === '0') {
                $cleaned = substr($cleaned, 1);
            }

            // 90 ile başlamıyorsa ekle
            if (substr($cleaned, 0, 2) !== '90') {
                $cleaned = '90' . $cleaned;
            }

            // Uzunluk kontrolü (905xxxxxxxxx = 12 karakter)
            if (strlen($cleaned) == 12) {
                $formatted[] = $cleaned;
            } else {
                Log::warning('Verimor: Geçersiz telefon numarası atlandı', [
                    'original' => $phone,
                    'cleaned' => $cleaned
                ]);
            }
        }

        return $formatted;
    }

    /**
     * Verimor hata kodlarını Türkçe mesaja çevir
     */
    protected function getErrorMessage(string $errorCode, string $defaultMessage): string
    {
        $errorMessages = [
            'INVALID_SOURCE_ADDRESS' => 'Başlık kabul edilmedi',
            'MISSING_MESSAGE' => 'Gönderilecek mesaj girilmemiş',
            'MESSAGE_TOO_LONG' => 'Mesaj çok uzun',
            'INVALID_PERIOD' => 'Mesajın geçerlilik süresi geçersiz',
            'INVALID_DELIVERY_TIME' => 'Gönderim zamanı geçersiz',
            'INVALID_DATACODING' => 'Veri kodlama hatası',
            'MISSING_DESTINATION_ADDRESS' => 'Alıcı telefon numarası girilmemiş',
            'INVALID_DESTINATION_ADDRESS' => 'Alıcı telefon numarası geçersiz',
            'INSUFFICIENT_CREDITS' => 'Yeterli SMS krediniz yok',
            'FORBIDDEN_MESSAGE' => 'Mesaj yasak kelime içeriyor',
            'MESSAGE_COUNT_LIMIT_EXCEEDED' => 'Maksimum mesaj sayısı aşıldı (Max: 50.000)',
            'INVALID_JSON' => 'Geçersiz JSON formatı',
            'INVALID_UTF8' => 'Encoding UTF-8 olmalıdır',
            'MISSING_IYS_BRAND_CODE' => 'Ticari gönderimlerde marka kodu gereklidir'
        ];

        return $errorMessages[$errorCode] ?? $defaultMessage;
    }

    /**
     * Hesap bakiyesini sorgula (opsiyonel - gelecekte kullanılabilir)
     */
    public function getBalance(): ?float
    {
        try {
            $username = $this->credentials['username'] ?? '';
            $password = $this->credentials['password'] ?? '';

            if (empty($username) || empty($password)) {
                return null;
            }

            $response = Http::get('https://sms.verimor.com.tr/v2/balance', [
                'username' => $username,
                'password' => $password
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return $result['balance'] ?? null;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Verimor Bakiye Sorgu Hatası', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}