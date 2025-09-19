<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\StoragePackage;
use App\Models\StoragePurchase;
use App\Models\Tenant;
use App\Services\PaytrService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StorageController extends Controller
{
    
    public function packages($tenant_id)
    {
        $firma = Tenant::findOrFail($tenant_id);
        $packages = StoragePackage::where('is_active', true)
                                 ->orderBy('sort_order')
                                 ->orderBy('price')
                                 ->get();
        
        $storageInfo = $firma->getStorageInfo();
        
        return view('frontend.secure.storage.storage_packages', compact('firma', 'packages', 'storageInfo'));
    }

    private function generatePaytrToken($merchant_oid, $amount)
    {
        $merchant_id = config('services.paytr.merchant_id');
        $merchant_key = config('services.paytr.merchant_key');
        $merchant_salt = config('services.paytr.merchant_salt');
        
        $hash_str = $merchant_id . request()->ip() . $merchant_oid . ($amount * 100) . 'TL';
        $paytr_token = base64_encode(hash_hmac('sha256', $hash_str, $merchant_key, true));
        
        return $paytr_token;
    }
    

    public function purchase(Request $request, $tenant_id)
{
    $request->validate([
        'package_id' => 'required|exists:storage_packages,id'
    ]);

    $firma = Tenant::findOrFail($tenant_id);
    $package = StoragePackage::findOrFail($request->package_id);

    // Alfanumerik ödeme token'ı oluştur
    $paymentToken = 'ST' . $tenant_id . time();

    // Storage satın alımını kaydet
    $purchase = StoragePurchase::create([
        'tenant_id' => $tenant_id,
        'storage_package_id' => $package->id,
        'payment_token' => $paymentToken,
        'amount' => $package->price,
        'storage_gb' => $package->storage_gb,
        'status' => 'pending'
    ]);

    // PaytrService için veri hazırla
    $orderData = [
        'order_id' => $paymentToken,
        'amount' => $package->price,
        'email' => $firma->eposta ?: 'test@example.com',
        'user_name' => $this->cleanString($firma->firma_adi ?: 'Test Kullanici'),
        'user_address' => $this->cleanString($firma->adres ?: 'Test Adres'),
        'user_phone' => preg_replace('/[^0-9]/', '', $firma->tel1 ?: '5551234567'),
        'success_url' => route('storage.packages', $tenant_id) . '?payment_check=1',
        'fail_url' => route('storage.packages', $tenant_id),
        'basket' => [
            [$package->name, number_format($package->price, 2, '.', ''), 1]
        ]
    ];

    // PaytrService kullanarak iframe oluştur
    $paytrService = app(PaytrService::class);
    $paytrResponse = $paytrService->createPaymentIframe($orderData);

    if (!$paytrResponse['success']) {
        return redirect()->route('storage.packages', $tenant_id)
                       ->with('error', 'Ödeme sayfası oluşturulamadı: ' . $paytrResponse['error']);
    }

    return view('frontend.secure.storage.storage_payment', compact('firma', 'package', 'purchase', 'paytrResponse'));
}

private function cleanString($str)
{
    $tr = array('ş','Ş','ı','I','İ','ğ','Ğ','ü','Ü','ö','Ö','Ç','ç');
    $en = array('s','S','i','I','I','g','G','u','U','o','O','C','c');
    
    $str = str_replace($tr, $en, $str);
    $str = preg_replace('/[^A-Za-z0-9\s]/', '', $str);
    
    return $str;
}

    public function paymentSuccess(Request $request, $tenant_id, $purchase_id)
    {
        $purchase = StoragePurchase::findOrFail($purchase_id);
        
        // PayTR'dan gelen verileri doğrula
        if ($this->verifyPaytrCallback($request)) {
            $purchase->update([
                'status' => 'completed',
                'purchased_at' => now(),
                'payment_response' => $request->all()
            ]);

            return redirect()->route('storage.packages', $tenant_id)
                           ->with('success', 'Ödeme başarılı! Ek storage alanınız hesabınıza eklendi.');
        }

        return redirect()->route('storage.packages', $tenant_id)
                       ->with('error', 'Ödeme doğrulaması başarısız.');
    }

    public function paymentFail(Request $request, $tenant_id, $purchase_id)
    {
        $purchase = StoragePurchase::findOrFail($purchase_id);
        
        $purchase->update([
            'status' => 'failed',
            'payment_response' => $request->all()
        ]);

        return redirect()->route('storage.packages', $tenant_id)
                       ->with('error', 'Ödeme işlemi başarısız.');
    }

    private function verifyPaytrCallback(Request $request)
    {
        $merchant_key = config('services.paytr.merchant_key');
        $merchant_salt = config('services.paytr.merchant_salt');
        
        $hash = base64_encode(hash_hmac('sha256', 
            $request->merchant_oid . $merchant_salt . $request->status . $request->total_amount, 
            $merchant_key, true));
            
        return hash_equals($hash, $request->hash);
    }
}
