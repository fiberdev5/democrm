<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Integration;
use App\Models\IntegrationPurchase;
use App\Models\Tenant;
use App\Services\PaytrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IntegrationMarketplaceController extends Controller
{
    // Entegrasyonlar pazaryeri - Firmalar buradan süper admin'in eklediği entegrasyonları görür
    public function index(Request $request, $tenant_id) {
        $user = Auth::user();
        $tenant = Tenant::where('id', $tenant_id)->first();
        
        if(!$tenant) {
            abort(404, 'Firma bulunamadı.');
        }

        // Kategoriler
        $categories = [
            'all' => 'Tümü',
            'invoice' => 'Fatura',
            'sms' => 'SMS',
            'accounting' => 'Muhasebe',
            'other' => 'Diğer'
        ];

        // Süper admin tarafından eklenen AKTİF entegrasyonları getir
        $query = Integration::where('is_active', 1);
        
        // Kategori filtresi
        if($request->filled('category') && $request->category != 'all') {
            $query->where('category', $request->category);
        }

        // Arama filtresi
        if($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $integrations = $query->orderBy('name', 'ASC')->get();

        // Bu firmanın aktif ettiği entegrasyonlar
        // $activeIntegrations = TenantIntegration::where('tenant_id', $tenant->id)
        //     ->where('is_enabled', 1)
        //     ->pluck('integration_id')
        //     ->toArray();

        return view('frontend.secure.integrations.marketplace', compact('integrations', 'categories', 'tenant'));
    }

    // Entegrasyon detayı
    public function show($tenant_id, $slug) {
        $user = Auth::user();
        $tenant = Tenant::where('id', $tenant_id)->first();
        $integration = Integration::where('slug', $slug)->where('is_active', 1)->firstOrFail();
        
        $purchase = $tenant->getIntegrationPurchase($integration->id);
        $isPurchased = $purchase !== null;
        $isActive = $isPurchased && $purchase->isActive();
        return view('frontend.secure.integrations.detail', compact('integration', 'tenant', 'isPurchased', 'isActive', 'purchase'));
    }

     // Satın alma sayfası
    public function purchase(Request $request, $tenant_id, $integration_id)
    {
        $firma = Tenant::findOrFail($tenant_id);
        $integration = Integration::where('is_active', 1)->findOrFail($integration_id);

        // Zaten satın alınmış mı kontrol et
        if ($firma->hasIntegration($integration_id)) {
            $notification = array(
                'message' => 'Bu entegrasyonu zaten satın aldınız.',  
                'alert-type' => 'warning'
            );
            return redirect()->route('tenant.integrations.show', [$tenant_id, $integration->slug])->with($notification);
        }

        // Alfanumerik ödeme token'ı oluştur
        $paymentToken = 'INT' . $tenant_id . time();

        // Entegrasyon satın alımını kaydet
        $purchase = IntegrationPurchase::create([
            'tenant_id' => $tenant_id,
            'integration_id' => $integration->id,
            'tokenPayment' => $paymentToken,
            'amount' => $integration->price,
            'currency' => 'TRY',
            'status' => 'pending'
        ]);

        // Ücretsiz entegrasyonlar için direkt aktifleştir
        if ($integration->price == 0) {
            $purchase->update([
                'status' => 'completed',
                'paid_at' => now(),
                'is_active' => true,
                'activated_at' => now(),
                'transaction_id' => 'FREE-' . $paymentToken,
            ]);

            $notification = array(
                'message' => 'Ücretsiz entegrasyon başarıyla aktifleştirildi!',
                'alert-type' => 'success'
            );

            return redirect()->route('tenant.integrations.marketplace', $tenant_id)->with($notification);
        }

        // PaytrService için veri hazırla
        $orderData = [
            'order_id' => $paymentToken,
            'amount' => number_format($integration->price, 2, '.', ''),
            'email' => $firma->eposta ?: 'test@example.com',
            'user_name' => $this->cleanString($firma->firma_adi ?: 'Test Kullanici'),
            'user_address' => $this->cleanString($firma->adres ?: 'Test Adres'),
            'user_phone' => preg_replace('/[^0-9]/', '', $firma->tel1 ?: '5000000000'),
            'success_url' => route('integration.payment.success'),
            'fail_url' => route('integration.payment.fail'),
            'basket' => [
                [$integration->name, number_format($integration->price, 2, '.', ''), 1]
            ]
        ];

        // PaytrService kullanarak iframe oluştur
        $paytrService = app(PaytrService::class);
        $paytrResponse = $paytrService->createPaymentIframe($orderData);

        if (!$paytrResponse['success']) {
            return redirect()->route('tenant.integrations.show', [$tenant_id, $integration->slug])
                        ->with('error', 'Ödeme sayfası oluşturulamadı: ' . $paytrResponse['error']);
        }

        return view('frontend.secure.integrations.purchase', compact('firma', 'integration', 'purchase', 'paytrResponse'));
    }

    private function cleanString($str)
    {
        $tr = array('ş','Ş','ı','I','İ','ğ','Ğ','ü','Ü','ö','Ö','Ç','ç');
        $en = array('s','S','i','I','I','g','G','u','U','o','O','C','c');
        
        $str = str_replace($tr, $en, $str);
        $str = preg_replace('/[^A-Za-z0-9\s]/', '', $str);
        
        return $str;
    }

    public function paymentSuccess(Request $request)
{
    if (session()->has('integration_payment_success')) {
        $data = session()->get('integration_payment_success');
        
        $notification = array(
            'message' => $data['message'],
            'alert-type' => 'success'
        );
        
        return redirect()->route('tenant.integrations.marketplace', [$data['tenant_id'], $data['integration_id']])
                        ->with($notification);
    }

    $notification = array(
        'message' => 'Ödeme işlemi tamamlandı.',
        'alert-type' => 'success'
    );
    
    return redirect()->back()->with($notification);
}

public function paymentFail(Request $request)
{
    if (session()->has('integration_payment_error')) {
        $data = session()->get('integration_payment_error');
        
        $notification = array(
            'message' => $data['message'] . ' Sebep: ' . ($data['reason'] ?? 'Belirtilmemiş'),
            'alert-type' => 'error'
        );
        
        return redirect()->route('tenant.integrations.marketplace', $data['tenant_id'])
                        ->with($notification);
    }

    $notification = array(
        'message' => 'Ödeme işlemi başarısız oldu.',
        'alert-type' => 'error'
    );
    
    return redirect()->back()->with($notification);
}
}
