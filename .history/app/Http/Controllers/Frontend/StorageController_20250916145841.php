<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\StoragePackage;
use App\Models\StoragePurchase;
use App\Models\Tenant;
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

        // Benzersiz ödeme token'ı oluştur
        $paymentToken = 'STORAGE_' . $tenant_id . '_' . time() . '_' . Str::random(10);

        // Storage satın alımını kaydet
        $purchase = StoragePurchase::create([
            'tenant_id' => $tenant_id,
            'storage_package_id' => $package->id,
            'payment_token' => $paymentToken,
            'amount' => $package->price,
            'storage_gb' => $package->storage_gb,
            'status' => 'pending'
        ]);

        // PayTR token oluştur
        $merchant_id = config('services.paytr.merchant_id');
        $merchant_key = config('services.paytr.merchant_key');
        $merchant_salt = config('services.paytr.merchant_salt');
        $user_ip = request()->ip();
        $payment_amount = $package->price * 100; // kuruş cinsinden

        // PayTR hash oluştur
        $hash_str = $merchant_id . $user_ip . $paymentToken . $payment_amount . 'TL';
        $paytr_token = base64_encode(hash_hmac('sha256', $hash_str, $merchant_key, true));

        // User basket
        $user_basket = base64_encode(json_encode([
            [$package->name, $package->price, 1]
        ]));

        // PayTR entegrasyonu için gerekli veriler
        $paytrData = [
            'merchant_id' => $merchant_id,
            'user_ip' => $user_ip,
            'merchant_oid' => $paymentToken,
            'email' => $firma->eposta ?: 'noreply@example.com',
            'payment_amount' => $payment_amount,
            'paytr_token' => $paytr_token,
            'user_name' => $firma->firma_adi ?: 'Kullanıcı',
            'user_address' => $firma->adres ?: 'Adres Belirtilmemiş',
            'user_phone' => $firma->tel1 ?: '0000000000',
            'user_basket' => $user_basket,
            'debug_on' => config('app.debug') ? 1 : 0,
            'test_mode' => config('services.paytr.test_mode', 1),
            'no_installment' => 1,
            'max_installment' => 0,
            'currency' => 'TL',
            'merchant_ok_url' => route('storage.packages', $tenant_id) . '?payment_check=1',
            'merchant_fail_url' => route('storage.packages', $tenant_id),
            'timeout_limit' => 30,
            'lang' => 'tr'
        ];

        return view('frontend.secure.storage.storage_payment', compact('firma', 'package', 'purchase', 'paytrData'));
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
