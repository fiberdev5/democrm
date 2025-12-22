<?php
// app/Services/SmsProviders/SolvelineProvider.php

namespace App\Services\SmsProviders;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SolvelineProvider implements SmsProviderInterface
{
    private $username;
    private $password;
    private $sender;
    private $apiUrl = 'https://smslogin.nac.com.tr:9588';

    public function __construct(array $credentials)
    {
        $this->username = $credentials['username'] ?? '';
        $this->password = $credentials['password'] ?? '';
        $this->sender = $credentials['sender'] ?? 'SERVISCNTR';
    }

    public function sendSingleSms(string $phone, string $message): array
    {
        try {
            // Telefon numarasını formatla (başındaki 0'ı kaldır)
            $phone = $this->formatPhone($phone);

            $response = Http::withBasicAuth($this->username, $this->password)
                ->withOptions([
                    'verify' => false // SSL sertifika doğrulamasını atla (gerekirse)
                ])
                ->post($this->apiUrl . '/sms/create', [
                    'type' => 1,
                    'sendingType' => 0,
                    'title' => 'smsapi',
                    'content' => $message,
                    'number' => $phone,
                    'encoding' => 1,
                    'sender' => $this->sender
                ]);

            Log::info('Solveline SMS Response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Solveline API response yapısına göre düzenleyin
                // Örnek başarılı response: {"success": true, "pkgID": 12345}
                if (isset($data['pkgID'])) {
                    return [
                        'success' => true,
                        'message' => 'SMS başarıyla gönderildi',
                        'response_code' => $data['pkgID'] ?? null,
                        'data' => $data
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'SMS gönderilemedi',
                'error' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Solveline SMS Gönderme Hatası: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'SMS gönderilirken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    public function sendBulkSms(array $phones, string $message): array
    {
        try {
            $successCount = 0;
            $failCount = 0;
            $results = [];

            // Solveline toplu SMS desteği yoksa, tek tek gönder
            foreach ($phones as $phone) {
                $result = $this->sendSingleSms($phone, $message);
                
                if ($result['success']) {
                    $successCount++;
                } else {
                    $failCount++;
                }
                
                $results[] = [
                    'phone' => $phone,
                    'success' => $result['success'],
                    'response' => $result
                ];

                // API rate limit için kısa bekleme (gerekirse)
                usleep(100000); // 0.1 saniye
            }

            return [
                'success' => $failCount === 0,
                'message' => "$successCount SMS gönderildi, $failCount başarısız",
                'success_count' => $successCount,
                'fail_count' => $failCount,
                'details' => $results
            ];

        } catch (\Exception $e) {
            Log::error('Solveline Toplu SMS Gönderme Hatası: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Toplu SMS gönderilirken hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Telefon numarasını formatla
     * 05074084007 -> 905074084007 (Türkiye için)
     */
    private function formatPhone(string $phone): string
    {
        // Tüm boşluk ve özel karakterleri temizle
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // 0 ile başlıyorsa kaldır ve 90 ekle
        if (substr($phone, 0, 1) === '0') {
            $phone = '9' . $phone;
        }
        
        // 90 ile başlamıyorsa ekle
        if (substr($phone, 0, 2) !== '90') {
            $phone = '90' . $phone;
        }
        
        return $phone;
    }

    /**
     * SMS durumunu sorgula (opsiyonel)
     */
    public function getReport($pkgId): array
    {
        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->withOptions([
                    'verify' => false
                ])
                ->post($this->apiUrl . '/sms/list-item', [
                    'pkgID' => $pkgId,
                    'customID' => '',
                    'target' => '',
                    'state' => 0
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Rapor alınamadı',
                'error' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Solveline SMS Rapor Hatası: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Rapor alınırken hata oluştu: ' . $e->getMessage()
            ];
        }
    }
}