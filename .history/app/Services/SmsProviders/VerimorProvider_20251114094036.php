<?php

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

            $formattedPhones = $this->formatPhones($phones);

            if (empty($formattedPhones)) {
                return [
                    'success' => false,
                    'message' => 'Geçerli telefon numarası bulunamadı'
                ];
            }

            $messageLength = mb_strlen($message, 'UTF-8');
            if ($messageLength > 1071) {
                return [
                    'success' => false,
                    'message' => 'Mesaj çok uzun (Maksimum 1071 karakter)'
                ];
            }

            $datacoding = $this->detectDatacoding($message);

            $payload = [
                'username' => $username,
                'password' => $password,
                'source_addr' => $sender,
                'valid_for' => '',
                'datacoding' => $datacoding,
                'messages' => [
                    [
                        'dest' => implode(',', $formattedPhones),
                        'msg' => $message
                    ]
                ]
            ];

            Log::info('Verimor SMS İsteği', [
                'telefon_sayisi' => count($formattedPhones),
                'mesaj_uzunlugu' => $messageLength,
                'datacoding' => $datacoding
            ]);

            // DÜZELTME: .json uzantısı KALDIRILDI
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post('https://sms.verimor.com.tr/v2/send', $payload);

            $statusCode = $response->status();
            
            Log::info('Verimor API Response', [
                'status' => $statusCode,
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                // DÜZELTME: Verimor sadece campaign ID döndürür (düz metin)
                $campaignId = trim($response->body());
                
                // Campaign ID sayısal olmalı
                if (is_numeric($campaignId)) {
                    Log::info('Verimor SMS Başarılı', [
                        'campaign_id' => $campaignId,
                        'telefon_sayisi' => count($formattedPhones)
                    ]);

                    return [
                        'success' => true,
                        'message' => count($formattedPhones) . ' kişiye SMS başarıyla gönderildi',
                        'campaign_id' => $campaignId,
                        'response_code' => '200'
                    ];
                } else {
                    // Beklenmeyen response
                    Log::error('Verimor Beklenmeyen Response', [
                        'body' => $campaignId
                    ]);
                    
                    return [
                        'success' => false,
                        'message' => 'Beklenmeyen API yanıtı: ' . $campaignId
                    ];
                }
            } else {
                // DÜZELTME: Verimor hata kodunu düz metin döndürür
                $errorCode = trim($response->body());
                
                Log::error('Verimor SMS Hatası', [
                    'status' => $statusCode,
                    'error_code' => $errorCode
                ]);

                return [
                    'success' => false,
                    'message' => 'SMS gönderilemedi: ' . $this->getErrorMessage($errorCode, $errorCode)
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

    public function sendSingleSms(string $phone, string $message): array
    {
        return $this->sendBulkSms([$phone], $message);
    }

    protected function formatPhones(array $phones): array
    {
        $formatted = [];

        foreach ($phones as $phone) {
            $cleaned = preg_replace('/[^0-9]/', '', $phone);

            if (substr($cleaned, 0, 1) === '0') {
                $cleaned = substr($cleaned, 1);
            }

            if (substr($cleaned, 0, 2) !== '90') {
                $cleaned = '90' . $cleaned;
            }

            // 905 ile başlamalı ve 12 karakter olmalı
            if (strlen($cleaned) == 12 && substr($cleaned, 0, 3) === '905') {
                $formatted[] = $cleaned;
            } else {
                Log::warning('Verimor: Geçersiz telefon numarası', [
                    'original' => $phone,
                    'cleaned' => $cleaned,
                    'length' => strlen($cleaned)
                ]);
            }
        }

        return $formatted;
    }

    protected function detectDatacoding(string $message): int
    {
        // Verimor dokümantasyonu: SADECE bu karakterler datacoding=1
        $turkceKarakterler = ['Ş', 'ş', 'Ğ', 'ğ', 'ı', 'İ'];
        
        foreach ($turkceKarakterler as $karakter) {
            if (mb_strpos($message, $karakter) !== false) {
                return 1; // Türkçe GSM
            }
        }

        // Emoji veya özel unicode kontrolü
        if (preg_match('/[\x{1F600}-\x{1F64F}]/u', $message)) {
            return 2; // Unicode
        }

        // Ö ö Ü ü Ç ç normal GSM ile gönderilebilir
        return 0; // Normal GSM
    }

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
            'MISSING_IYS_BRAND_CODE' => 'Ticari gönderimlerde marka kodu gereklidir',
            'NO_AHS_BRAND_ERROR' => 'İYS\'de kayıtlı marka bulunamadı',
            'COMMERCIAL_SENDING_ERROR_UNDER_150K' => 'Ticari gönderim için yeterli onay sayısı yok',
            'INVALID_IYS_RECIPIENT_TYPE' => 'İYS alıcı tipi BIREYSEL veya TACIR olmalı'
        ];

        return $errorMessages[$errorCode] ?? $defaultMessage;
    }
}