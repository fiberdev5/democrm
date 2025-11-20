<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TescomService
{
    protected $apiUrl;
    protected $username;
    protected $password;
    protected $originator;

    public function __construct()
    {
        $this->apiUrl = config('sms.tescom.api_url');
        $this->username = config('sms.tescom.username');
        $this->password = config('sms.tescom.password');
        $this->originator = config('sms.tescom.originator', 'SERBIS');
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
            // Telefon numarasını temizle (boşlukları kaldır)
            $cleanPhone = str_replace(' ', '', $phoneNumber);
            
            // Başında 0 varsa kaldır, 90 ekle
            if (substr($cleanPhone, 0, 1) === '0') {
                $cleanPhone = '90' . substr($cleanPhone, 1);
            } elseif (substr($cleanPhone, 0, 2) !== '90') {
                $cleanPhone = '90' . $cleanPhone;
            }

            // Tescom API isteği
            $response = Http::asForm()->post($this->apiUrl, [
                'username' => $this->username,
                'password' => $this->password,
                'originator' => $this->originator,
                'destination' => $cleanPhone,
                'message' => $message,
            ]);

            Log::info('SMS Gönderim Denemesi', [
                'phone' => $cleanPhone,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            if ($response->successful()) {
                $responseBody = $response->body();
                
                // Tescom API genellikle XML veya text yanıt döner
                // Başarılı yanıt kontrolü (API'ye göre değişebilir)
                if (strpos($responseBody, 'OK') !== false || strpos($responseBody, 'success') !== false) {
                    return [
                        'success' => true,
                        'message' => 'SMS başarıyla gönderildi',
                        'response' => $responseBody
                    ];
                }
                
                return [
                    'success' => false,
                    'message' => 'SMS gönderilemedi',
                    'response' => $responseBody
                ];
            }

            return [
                'success' => false,
                'message' => 'SMS servisi ile bağlantı kurulamadı',
                'error' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('SMS Gönderim Hatası', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'SMS gönderiminde hata oluştu: ' . $e->getMessage()
            ];
        }
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
}