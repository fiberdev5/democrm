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

            // Türkçe karakter kontrolü - datacoding belirleme
            $datacoding = $this->detectDatacoding($message);

            // Verimor API'ye POST isteği (TAM FORMAT)
            $payload = [
                'username' => $username,
                'password' => $password,
                'source_addr' => $sender,
                'valid_for' => '', // Boş = varsayılan 24:00
                'datacoding' => $datacoding, // 0: Normal, 1: Türkçe
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
                'datacoding' => $datacoding,
                'payload' => $payload // Debug için
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post('https://sms.verimor.com.tr/v2/send.json', $payload);

            // Response kontrolü
            $statusCode = $response->status();
            
            Log::info('Verimor API Response', [
                'status' => $statusCode,
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                // Verimor başarılı response yapısı
                // Genellikle HTTP 200 dönerse başarılıdır
                // Response'da campaign ID gibi bilgiler olabilir
                
                if ($statusCode == 200) {
                    Log::info('Verimor Toplu SMS Başarılı', [
                        'telefon_sayisi' => count($formattedPhones),
                        'response' => $result
                    ]);

                    // Campaign ID varsa al
                    $campaignId = null;
                    if (is_array($result) && isset($result[0]['id'])) {
                        $campaignId = $result[0]['id'];
                    } elseif (is_string($result)) {
                        // Bazen düz metin olarak ID dönebilir
                        $campaignId = $result;
                    }

                    return [
                        'success' => true,
                        'message' => count($formattedPhones) . ' kişiye SMS başarıyla gönderildi',
                        'response_code' => $statusCode,
                        'campaign_id' => $campaignId
                    ];
                } else {
                    // 200 dışında ama successful - beklenmeyen durum
                    return [
                        'success' => false,
                        'message' => 'Beklenmeyen API yanıtı: ' . $statusCode
                    ];
                }
            } else {
                // HTTP hata kodu (400, 401, 500 vb.)
                $errorBody = $response->body();
                $errorJson = $response->json();
                
                // Verimor hata formatı kontrol et
                $errorMessage = 'SMS gönderilemedi';
                $errorCode = 'UNKNOWN';
                
                if (is_array($errorJson)) {
                    // JSON hata response
                    if (isset($errorJson['error'])) {
                        $errorCode = $errorJson['error']['code'] ?? 'UNKNOWN';
                        $errorMessage = $errorJson['error']['message'] ?? $errorMessage;
                    } elseif (isset($errorJson['message'])) {
                        $errorMessage = $errorJson['message'];
                    }
                } else {
                    // Düz metin hata (eski API tarzı)
                    $errorMessage = $errorBody;
                    $errorCode = $errorBody;
                }
                
                Log::error('Verimor SMS Hatası', [
                    'status' => $statusCode,
                    'error_code' => $errorCode,
                    'error_message' => $errorMessage,
                    'response_body' => $errorBody
                ]);

                return [
                    'success' => false,
                    'message' => 'SMS gönderilemedi: ' . $this->getErrorMessage($errorCode, $errorMessage)
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
                    'cleaned' => $cleaned,
                    'length' => strlen($cleaned)
                ]);
            }
        }

        return $formatted;
    }

    /**
     * Mesaj için datacoding belirle
     * 0: Normal (GSM Basic)
     * 1: Türkçe (GSM Turkish) - Sadece Ş ş Ğ ğ ç ı İ
     * 2: Unicode (UCS2)
     */
    protected function detectDatacoding(string $message): int
    {
        // Türkçe karakterleri kontrol et (Verimor'da sadece Ş ş Ğ ğ ç ı İ türkçe sayılır)
        $turkceKarakterler = ['Ş', 'ş', 'Ğ', 'ğ', 'ı', 'İ'];
        
        foreach ($turkceKarakterler as $karakter) {
            if (mb_strpos($message, $karakter) !== false) {
                return 1; // Türkçe
            }
        }

        // Unicode karakterler var mı? (Emoji, özel karakterler)
        if (preg_match('/[^\x00-\x7F]/', $message)) {
            // Ö ö Ü ü Ç gibi karakterler normal'de gönderilebilir
            $normalKarakterler = ['Ö', 'ö', 'Ü', 'ü', 'Ç', 'ç'];
            $temizMesaj = str_replace($normalKarakterler, '', $message);
            
            if (preg_match('/[^\x00-\x7F]/', $temizMesaj)) {
                return 2; // Unicode
            }
        }

        return 0; // Normal
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
            'MISSING_IYS_BRAND_CODE' => 'Ticari gönderimlerde marka kodu gereklidir',
            'NO_AHS_BRAND_ERROR' => 'İYS\'de kayıtlı marka bulunamadı',
            'COMMERCIAL_SENDING_ERROR_UNDER_150K' => 'Ticari gönderim için yeterli onay sayısı yok',
            'INVALID_IYS_RECIPIENT_TYPE' => 'İYS alıcı tipi BIREYSEL veya TACIR olmalı'
        ];

        return $errorMessages[$errorCode] ?? $defaultMessage;
    }

    /**
     * Hesap bakiyesini sorgula (opsiyonel)
    //  */
    // public function getBalance(): ?float
    // {
    //     try {
    //         $username = $this->credentials['username'] ?? '';
    //         $password = $this->credentials['password'] ?? '';

    //         if (empty($username) || empty($password)) {
    //             return null;
    //         }

    //         $response = Http::get('https://sms.verimor.com.tr/v2/balance', [
    //             'username' => $username,
    //             'password' => $password
    //         ]);

    //         if ($response->successful()) {
    //             $result = $response->json();
    //             return $result['balance'] ?? null;
    //         }

    //         return null;

    //     } catch (\Exception $e) {
    //         Log::error('Verimor Bakiye Sorgu Hatası', [
    //             'error' => $e->getMessage()
    //         ]);
    //         return null;
    //     }
    // }
}