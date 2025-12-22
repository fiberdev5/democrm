<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TescomService
{
    protected $hostname;
    protected $username;
    protected $password;
    protected $sender;

    public function __construct()
    {
        $this->hostname = config('sms.tescom.api_url');
        $this->username = config('sms.tescom.username');
        $this->password = config('sms.tescom.password');
        $this->sender = config('sms.tescom.originator', 'FIBER MEDYA');
    }

    /**
     * Tescom API üzerinden SMS gönderir
     *
     * @param string $phoneNumber Telefon numarası (5xxxxxxxxx formatında)
     * @param string $message Gönderilecek mesaj
     * @return array
     */
    public function sendSms($phoneNumber, $message)
    {
        try {
            // Credentials kontrolü
            if (empty($this->username) || empty($this->password)) {
                Log::error('SMS Credentials Eksik!', [
                    'username' => empty($this->username) ? 'BOŞ' : 'DOLU',
                    'password' => empty($this->password) ? 'BOŞ' : 'DOLU',
                ]);
                
                return [
                    'success' => false,
                    'message' => 'SMS API credentials eksik! .env dosyasını kontrol edin.'
                ];
            }

            // Telefon numarasını formatla
            $cleanPhone = $this->formatPhoneNumber($phoneNumber);

            // Tescom API endpoint
            $endpoint = "http://{$this->hostname}";
            
            // Basic Authentication token oluştur
            // Format: username:password -> base64 encode
            $authToken = base64_encode($this->username . ':' . $this->password);

            Log::info('SMS Gönderim Başlıyor', [
                'endpoint' => $endpoint,
                'phone' => $cleanPhone,
                'username' => $this->username,
                'auth_token' => substr($authToken, 0, 20) . '...',
            ]);

            // Request payload
            $payload = [
                'sender' => $this->sender,
                'title' => 'Dogrulama',
                'content' => $message,
                'number' => (int) $cleanPhone,
                'encoding' => 0, // 0: Normal, 1: Unicode
            ];

            // Tescom API isteği (Basic Authentication ile)
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $authToken,
            ])->post($endpoint, $payload);

            Log::info('SMS API Yanıtı', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                // Tescom başarılı yanıt kontrolü (err == null)
                if (isset($responseData['err']) && $responseData['err'] === null) {
                    Log::info('SMS Başarıyla Gönderildi', [
                        'message_id' => $responseData['pkgID'] ?? null,
                        'phone' => $cleanPhone
                    ]);
                    
                    return [
                        'success' => true,
                        'message' => 'SMS başarıyla gönderildi',
                        'message_id' => $responseData['pkgID'] ?? null,
                        'response' => $responseData
                    ];
                }
                
                // Hata varsa
                $errorInfo = $responseData['err'] ?? [];
                Log::error('SMS API Hatası', [
                    'error' => $errorInfo,
                    'phone' => $cleanPhone
                ]);
                
                return [
                    'success' => false,
                    'message' => $errorInfo['message'] ?? 'SMS gönderilemedi',
                    'error_code' => $errorInfo['code'] ?? null,
                    'error_status' => $errorInfo['status'] ?? null,
                    'response' => $responseData
                ];
            }

            // HTTP hatası
            Log::error('SMS HTTP Hatası', [
                'status' => $response->status(),
                'body' => $response->body(),
                'phone' => $cleanPhone
            ]);

            return [
                'success' => false,
                'message' => 'SMS servisi ile bağlantı kurulamadı',
                'status_code' => $response->status(),
                'error' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('SMS Gönderim İstisnası', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'SMS gönderiminde hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    private function formatPhoneNumber($phoneNumber)
    {
        // Boşlukları ve özel karakterleri kaldır
        $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Başında 0 varsa kaldır
        if (substr($cleanPhone, 0, 1) === '0') {
            $cleanPhone = substr($cleanPhone, 1);
        }
        
        // Başında 90 yoksa ekle
        if (substr($cleanPhone, 0, 2) !== '90') {
            $cleanPhone = '90' . $cleanPhone;
        }
        
        return $cleanPhone;
    }

    /**
     * Doğrulama kodu SMS'i gönderir
     *
     * @param string $phoneNumber
     * @param string $code
     * @return array
     */
    public function sendVerificationCode($phoneNumber, $code)
    {
        $message = "Serbis CRM doğrulama kodunuz: {$code}\n\nBu kodu kimseyle paylaşmayınız.";
        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Genel bilgilendirme SMS'i gönderir
     *
     * @param string $phoneNumber
     * @param string $message
     * @return array
     */
    public function sendNotification($phoneNumber, $message)
    {
        return $this->sendSms($phoneNumber, $message);
    }

     public function getAuthToken()
    {
        return base64_encode($this->username . ':' . $this->password);
    }
}