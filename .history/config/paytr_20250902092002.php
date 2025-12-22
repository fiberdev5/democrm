<?php

return [
    'merchant_id' => env('PAYTR_MERCHANT_ID'),
    'merchant_key' => env('PAYTR_MERCHANT_KEY'),
    'merchant_salt' => env('PAYTR_MERCHANT_SALT'),
    'test_mode' => env('PAYTR_TEST_MODE', true),
    'currency' => env('PAYTR_CURRENCY', 'TL'),
    
    // Callback URL'leri
    'success_url' => env('APP_URL') . '/subscription/payment/success',
    'fail_url' => env('APP_URL') . '/subscription/payment/fail',
    'callback_url' => env('APP_URL') . '/subscription/payment/callback',
];