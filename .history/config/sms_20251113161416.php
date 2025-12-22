<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tescom SMS API Ayarları
    |--------------------------------------------------------------------------
    |
    | Tescom (Türk Telekom) SMS servisi için API bilgileri
    |
    */

    'tescom' => [
        'api_url' => env('TESCOM_API_URL', 'smspanel.tescom.com.tr'),
        'username' => env('TESCOM_USERNAME', ''),
        'password' => env('TESCOM_PASSWORD', ''),
        'originator' => env('TESCOM_ORIGINATOR', 'FIBER MEDYA'), // Gönderici adı (max 11 karakter)
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Doğrulama Ayarları
    |--------------------------------------------------------------------------
    */

    'verification' => [
        'code_length' => 6, // Doğrulama kodu uzunluğu
        'code_expiry_minutes' => 3, // Kodun geçerlilik süresi (dakika)
        'max_attempts' => 3, // Maksimum deneme hakkı
    ],
];