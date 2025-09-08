<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaytrService
{
    private $merchantId;
    private $merchantKey;
    private $merchantSalt;
    private $testMode;

    public function __construct()
    {
        $this->merchantId = config('paytr.merchant_id');
        $this->merchantKey = config('paytr.merchant_key');
        $this->merchantSalt = config('paytr.merchant_salt');
        $this->testMode = config('paytr.test_mode', true);
    }

    /**
     * Ödeme iframeini oluştur
     */
    public function createPaymentIframe($orderData)
    {
        // Paytr'a gönderilecek veriler
        $post_vals = [
            'merchant_id' => $this->merchantId,
            'user_ip' => request()->ip(),
            'merchant_oid' => $orderData['order_id'],
            'email' => $orderData['email'],
            'payment_amount' => $orderData['amount'] * 100, // Kuruş cinsinden
            'currency' => 'TL',
            'test_mode' => $this->testMode ? '1' : '0',
            'non_3d' => '0', // 3D Secure zorunlu
            'installment_count' => '0',
            'merchant_ok_url' => $orderData['success_url'],
            'merchant_fail_url' => $orderData['fail_url'],
            'user_name' => $orderData['user_name'],
            'user_address' => $orderData['user_address'],
            'user_phone' => $orderData['user_phone'],
            'user_basket' => base64_encode(json_encode($orderData['basket'])),
            'debug_on' => $this->testMode ? '1' : '0',
            'client_lang' => 'tr',
            'payment_type' => 'card',
        ];

        // Paytr token oluştur
        $paytr_token = $this->generateToken($post_vals);
        $post_vals['paytr_token'] = $paytr_token;

        // Paytr'a POST isteği gönder
        $response = Http::timeout(30)->post('https://www.paytr.com/odeme/api/get-token', $post_vals);

        if ($response->successful()) {
            $result = $response->json();
            
            if ($result['status'] == 'success') {
                return [
                    'success' => true,
                    'token' => $result['token'],
                    'iframe_url' => 'https://www.paytr.com/odeme/guvenli/' . $result['token']
                ];
            } else {
                Log::error('Paytr Error: ' . $result['reason']);
                return [
                    'success' => false,
                    'error' => $result['reason']
                ];
            }
        }

        return [
            'success' => false,
            'error' => 'Ödeme servisiyle bağlantı kurulamadı'
        ];
    }

    /**
     * Callback doğrulaması
     */
    public function verifyCallback($postData)
    {
        // Paytr'dan gelen hash ile bizim oluşturduğumuz hash'i karşılaştır
        $hash = base64_encode(hash_hmac('sha256', 
            $postData['merchant_oid'] . $this->merchantSalt . $postData['status'] . $postData['total_amount'], 
            $this->merchantKey, true));

        return hash_equals($hash, $postData['hash']);
    }

    /**
     * Token oluştur
     */
    private function generateToken($post_vals)
    {
        // Token oluşturmak için gerekli string
        $hash_str = $post_vals['merchant_id'] . 
                   $post_vals['user_ip'] . 
                   $post_vals['merchant_oid'] . 
                   $post_vals['email'] . 
                   $post_vals['payment_amount'] . 
                   $post_vals['payment_type'] . 
                   $post_vals['user_basket'] . 
                   $post_vals['installment_count'] . 
                   $post_vals['currency'] . 
                   $post_vals['test_mode'];
                   $post_vals['non_3d'];

        return base64_encode(hash_hmac('sha256', $hash_str . $this->merchantSalt, $this->merchantKey, true));
    }

    /**
     * Sipariş durumu sorgula
     */
    public function checkOrderStatus($orderId)
    {
        $post_vals = [
            'merchant_id' => $this->merchantId,
            'merchant_oid' => $orderId,
        ];

        $paytr_token = base64_encode(hash_hmac('sha256', 
            $post_vals['merchant_id'] . $post_vals['merchant_oid'] . $this->merchantSalt, 
            $this->merchantKey, true));
            
        $post_vals['paytr_token'] = $paytr_token;

        $response = Http::timeout(30)->post('https://www.paytr.com/odeme/durum-sorgu', $post_vals);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'status' => 'error',
            'message' => 'Durum sorgulanamadı'
        ];
    }
}