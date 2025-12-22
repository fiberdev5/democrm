<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NetgsmService
{
    protected $apiUrl = 'https://api.netgsm.com.tr/sms/send/xml';

    public function topluSmsGonder($telefonlar, $mesaj, $kullanici, $sifre, $gonderici)
    {
        try {
            $xml = $this->xmlOlustur($telefonlar, $mesaj, $kullanici, $sifre, $gonderici);
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/xml',
            ])->send('POST', $this->apiUrl, [
                'body' => $xml
            ]);

            $responseBody = $response->body();
            
            // NetGSM yanıt kodlarını kontrol et
            if ($response->successful()) {
                $code = trim($responseBody);
                
                if (is_numeric($code) && $code > 0) {
                    Log::info('Toplu SMS gönderildi', ['jobid' => $code]);
                    return [
                        'success' => true,
                        'message' => 'Mesajınız başarıyla gönderildi',
                        'jobid' => $code
                    ];
                } else {
                    $errorMessage = $this->getErrorMessage($code);
                    Log::error('SMS gönderme hatası', ['code' => $code, 'message' => $errorMessage]);
                    return [
                        'success' => false,
                        'message' => $errorMessage
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'SMS servisi yanıt vermedi'
            ];

        } catch (\Exception $e) {
            Log::error('SMS gönderme exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'SMS gönderilirken bir hata oluştu: ' . $e->getMessage()
            ];
        }
    }

    protected function xmlOlustur($telefonlar, $mesaj, $kullanici, $sifre, $gonderici)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<mainbody>';
        $xml .= '<header>';
        $xml .= '<company dil="TR">Netgsm</company>';
        $xml .= '<usercode>' . htmlspecialchars($kullanici) . '</usercode>';
        $xml .= '<password>' . htmlspecialchars($sifre) . '</password>';
        $xml .= '<type>1:n</type>';
        $xml .= '<msgheader>' . htmlspecialchars($gonderici) . '</msgheader>';
        $xml .= '</header>';
        $xml .= '<body>';
        $xml .= '<msg><![CDATA[' . $mesaj . ']]></msg>';
        
        foreach ($telefonlar as $tel) {
            $xml .= '<no>' . $tel . '</no>';
        }
        
        $xml .= '</body>';
        $xml .= '</mainbody>';

        return $xml;
    }

    protected function getErrorMessage($code)
    {
        $errors = [
            '20' => 'Mesaj metninde ki problemden dolayı gönderilemediğini veya standart maksimum mesaj karakter sayısını geçtiğini ifade eder. (Standart maksimum karakter sayısı 917 dir.)',
            '30' => 'Geçersiz kullanıcı adı, şifre veya kullanıcınızın API erişim izninin olmadığını gösterir.',
            '40' => 'Mesaj başlığınızın (gönderici adınızın) sistemde tanımlı olmadığını ifade eder.',
            '50' => 'Abone hesabınızın ilgili dönemde çok fazla sayıda yanlış şifre denemesi olduğunu veya API erişiminizin olmadığını gösterir.',
            '51' => 'Kontörünüzün yetersiz olduğunu gösterir.',
            '70' => 'Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan birinin eksik olduğunu ifade eder.',
            '85' => 'Başlık kullanım izniniz yok',
        ];

        return $errors[$code] ?? 'Bilinmeyen bir hata oluştu (Kod: ' . $code . ')';
    }

    public function tekSmsGonder($telefon, $mesaj, $kullanici, $sifre, $gonderici)
    {
        return $this->topluSmsGonder([$telefon], $mesaj, $kullanici, $sifre, $gonderici);
    }
}