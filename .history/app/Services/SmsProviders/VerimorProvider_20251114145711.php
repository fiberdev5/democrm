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

            // Mesaj uzunluğu kontrolü
            $messageLength = mb_strlen($message, 'UTF-8');
            if ($messageLength > 1071) {
                return [
                    'success' => false,
                    'message' => 'Mesaj çok uzun (Maksimum 1071 karakter)'
                ];
            }

            // Datacoding tespiti
            $datacoding = $this->detectDatacoding($message);

            // API payload
            $payload = [
                'username' => $username,
                'password' => $password,
                'source_addr' => $sender,
                'datacoding' => (string)$datacoding, // String olarak gönder
                'custom_id' => uniqid('sms_', true), // Tracking için
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
                'sender' => $sender
            ]);

            // API isteği
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post('https://sms.verimor.com.tr/v2/send.json', $payload);

            $statusCode = $response->status();
            
            Log::info('Verimor API Response', [
                'status' => $statusCode,
                'body' => $response->body()
            ]);

            // Başarılı durum
            if ($response->successful()) {
                // Verimor sadece campaign ID döndürür (düz metin)
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
                        'response_code' => '200',
                        'provider' => 'verimor'
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
            }

            // Hata durumu
            $errorBody = trim($response->body());
            
            Log::error('Verimor SMS Hatası', [
                'status' => $statusCode,
                'error_body' => $errorBody,
                'payload' => $payload
            ]);

            return [
                'success' => false,
                'message' => $this->getErrorMessage($statusCode, $errorBody),
                'response_code' => (string)$statusCode
            ];

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
            // Sadece rakamları al
            $cleaned = preg_replace('/[^0-9]/', '', $phone);

            // Başındaki 0'ı kaldır
            if (substr($cleaned, 0, 1) === '0') {
                $cleaned = substr($cleaned, 1);
            }

            // 90 yoksa ekle
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

        return array_unique($formatted);
    }

    protected function detectDatacoding(string $message): int
    {
        // Türkçe özel karakterler (GSM 7-bit extended'de olmayan)
        // Bunlar varsa datacoding=1 (Turkish) kullan
        $turkceOzelKarakterler = ['Ş', 'ş', 'Ğ', 'ğ', 'ı', 'İ'];
        
        foreach ($turkceOzelKarakterler as $karakter) {
            if (mb_strpos($message, $karakter) !== false) {
                return 1; // Turkish GSM
            }
        }

        // Unicode emoji kontrolü
        if (preg_match('/[\x{1F300}-\x{1F9FF}]/u', $message)) {
            return 2; // Unicode
        }

        // Diğer Unicode karakterler
        if (preg_match('/[^\x00-\x7F]/u', $message)) {
            // Türkçe olmayan unicode var mı kontrol et
            $gsmChars = ['Ö', 'ö', 'Ü', 'ü', 'Ç', 'ç'];
            $hasNonGsm = false;
            
            foreach (mb_str_split($message) as $char) {
                if (ord($char) > 127 && !in_array($char, $gsmChars) && !in_array($char, $turkceOzelKarakterler)) {
                    $hasNonGsm = true;
                    break;
                }
            }
            
            if ($hasNonGsm) {
                return 2; // Unicode
            }
        }

        // Normal GSM 7-bit
        return 0;
    }

    

    /**
     * Bağlantı testi (opsiyonel)
     */
    // public function testConnection(): array
    // {
    //     try {
    //         $username = $this->credentials['username'] ?? '';
    //         $password = $this->credentials['password'] ?? '';

    //         if (empty($username) || empty($password)) {
    //             return [
    //                 'success' => false,
    //                 'message' => 'Kullanıcı adı veya şifre eksik'
    //             ];
    //         }

    //         // Bakiye sorgula
    //         $response = Http::timeout(10)
    //             ->get('https://sms.verimor.com.tr/v2/balance', [
    //                 'username' => $username,
    //                 'password' => $password
    //             ]);

    //         if ($response->successful()) {
    //             $balance = $response->body();
                
    //             return [
    //                 'success' => true,
    //                 'message' => 'Bağlantı başarılı',
    //                 'balance' => $balance
    //             ];
    //         }

    //         return [
    //             'success' => false,
    //             'message' => 'Bağlantı hatası: ' . $this->getErrorMessage($response->status(), $response->body())
    //         ];

    //     } catch (\Exception $e) {
    //         return [
    //             'success' => false,
    //             'message' => 'Test hatası: ' . $e->getMessage()
    //         ];
    //     }
    // }
}