<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Integration;
use App\Models\IntegrationPurchase;
use App\Models\Tenant;
use App\Services\PaytrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class IntegrationMarketplaceController extends Controller
{
    // Entegrasyonlar pazaryeri - Firmalar buradan süper admin'in eklediği entegrasyonları görür
    public function index(Request $request, $tenant_id) {
        $user = Auth::user();
        $tenant = Tenant::where('id', $tenant_id)->first();
        
        if(!$tenant) {
            abort(404, 'Firma bulunamadı.');
        }

        $categories = [
            'all' => 'Tümü',
            'invoice' => 'Fatura',
            'sms' => 'SMS',
            'accounting' => 'Muhasebe',
            'other' => 'Diğer'
        ];

        // Bu firmanın aktif entegrasyonlarını getir
        $activeIntegrationIds = $tenant->activeIntegrations()
            ->pluck('integration_id')
            ->toArray();
        
        $activeIntegrationsCount = count($activeIntegrationIds);

        // Süper admin tarafından eklenen AKTİF entegrasyonları getir
        $query = Integration::where('is_active', 1);
        
        // Sadece aktif entegrasyonları göster filtresi
        if($request->filled('filter') && $request->filter == 'active') {
            $query->whereIn('id', $activeIntegrationIds);
        }
        
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

        return view('frontend.secure.integrations.marketplace', compact(
            'integrations', 
            'categories', 
            'tenant', 
            'activeIntegrationIds',
            'activeIntegrationsCount'
        ));
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

    public function saveSettings(Request $request, $tenant_id, $integration_id)
    {
        try {
            $tenant = Tenant::findOrFail($tenant_id);
            $integration = Integration::findOrFail($integration_id);
            
            // Purchase kaydını bul
            $purchase = IntegrationPurchase::where('tenant_id', $tenant->id)
                ->where('integration_id', $integration_id)
                ->where('status', 'completed')
                ->firstOrFail();

            // Dinamik validasyon kuralları oluştur
            $rules = [];
            $apiFields = [];
            
            if ($integration->api_fields) {
                if (is_string($integration->api_fields)) {
                    $apiFields = json_decode($integration->api_fields, true) ?? [];
                } elseif (is_array($integration->api_fields)) {
                    $apiFields = $integration->api_fields;
                }
                
                // Her alan için validasyon kuralı oluştur
                foreach ($apiFields as $field) {
                    $fieldName = $field['name'] ?? '';
                    $fieldRules = [];
                    
                    if ($field['required'] ?? false) {
                        $fieldRules[] = 'required';
                    }
                    
                    // Tip bazlı validasyon
                    $fieldType = $field['type'] ?? 'text';
                    switch ($fieldType) {
                        case 'email':
                            $fieldRules[] = 'email';
                            break;
                        case 'url':
                            $fieldRules[] = 'url';
                            break;
                        case 'number':
                            $fieldRules[] = 'numeric';
                            break;
                    }
                    
                    if (!empty($fieldRules)) {
                        $rules["credentials.{$fieldName}"] = implode('|', $fieldRules);
                    }
                }
            }

            // Validation yap
            if (!empty($rules)) {
                $validated = $request->validate($rules, [
                    'credentials.*.required' => 'Bu alan zorunludur.',
                    'credentials.*.email' => 'Geçerli bir email adresi giriniz.',
                    'credentials.*.url' => 'Geçerli bir URL giriniz.',
                    'credentials.*.numeric' => 'Sayısal bir değer giriniz.',
                ]);
            }

            // Credentials'ı al ve kaydet
            $credentials = $request->input('credentials', []);
            $settings = $request->input('settings', []);

            // Güncelle
            $purchase->update([
                'credentials' => $credentials,
                'settings' => $settings,
            ]);

            // JSON response
            return response()->json([
                'success' => true,
                'message' => 'API ayarları başarıyla kaydedildi.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lütfen tüm zorunlu alanları doldurun.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Integration settings save error', [
                'tenant_id' => $tenant_id,
                'integration_id' => $integration_id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'API ayarları kaydedilirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

     // Satın alma sayfası
    public function purchase(Request $request, $tenant_id, $integration_id)
    {
        $firma = Tenant::findOrFail($tenant_id);
        $integration = Integration::where('is_active', 1)->findOrFail($integration_id);

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
    $user = Auth::user();
    $tenant_id = $user->tenant_id;
    
    // Cache'den mesajı kontrol et
    $cacheKey = 'integration_payment_success_' . $tenant_id;
    
    if (Cache::has($cacheKey)) {
        $data = Cache::get($cacheKey);
        Cache::forget($cacheKey); // Mesajı kullandıktan sonra sil
        
        $notification = array(
            'message' => $data['message'],
            'alert-type' => 'success'
        );
        
        return redirect()->route('payment-history.index', $data['tenant_id'])
                        ->with($notification);
    }
    
    // Session'dan kontrol et (fallback)
    if (session()->has('integration_payment_success')) {
        $data = session()->get('integration_payment_success');
        
        $notification = array(
            'message' => $data['message'],
            'alert-type' => 'success'
        );
        
        return redirect()->route('payment-history.index', $data['tenant_id'])
                        ->with($notification);
    }

    $notification = array(
        'message' => 'Ödeme işlemi tamamlandı.',
        'alert-type' => 'success'
    );
    
    return redirect()->route('payment-history.index', $tenant_id)
                    ->with($notification);
}

public function paymentFail(Request $request)
{
    $user = Auth::user();
    $tenant_id = $user->tenant_id;
    
    // Cache'den mesajı kontrol et
    $cacheKey = 'integration_payment_error_' . $tenant_id;
    
    if (Cache::has($cacheKey)) {
        $data = Cache::get($cacheKey);
        Cache::forget($cacheKey);
        
        $notification = array(
            'message' => $data['message'] . ' Sebep: ' . ($data['reason'] ?? 'Belirtilmemiş'),
            'alert-type' => 'error'
        );
        
        return redirect()->route('tenant.integrations.marketplace', $data['tenant_id'])
                        ->with($notification);
    }
    
    // Session'dan kontrol et (fallback)
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
    
    return redirect()->route('tenant.integrations.marketplace', $tenant_id)
                    ->with($notification);
}

// Hipcall rehberini görüntüle
public function showHipcallContacts($tenant_id)
{
    try {
        $tenant = Tenant::findOrFail($tenant_id);
        
        // Hipcall'dan rehberi çek
        $hipcallService = new \App\Services\HipcallService($tenant_id);
        $result = $hipcallService->getAllContacts();
        
        if ($result['success']) {
            $contacts = $result['contacts'];
            
            // Her contact için SerbisERP'de var mı kontrol et
            foreach ($contacts as &$contact) {
    // Telefon numarasını farklı olası field'lardan bul
    $phone = $contact['phone'] 
          ?? $contact['phone_number'] 
          ?? $contact['mobile']
          ?? $contact['telephone']
          ?? $contact['gsm']
          ?? null;
    
    // Eğer hala yoksa ve phones array'i varsa
    if (!$phone && isset($contact['phones']) && is_array($contact['phones']) && count($contact['phones']) > 0) {
        $phone = $contact['phones'][0];
    }
    
    if ($phone) {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        $existingCustomer = \DB::table('customers')
            ->where('firma_id', $tenant_id)
            ->where(function($q) use ($phone, $cleanPhone) {
                $q->where('tel1', 'LIKE', "%{$cleanPhone}%")
                  ->orWhere('telefon', 'LIKE', "%{$cleanPhone}%");
            })
            ->first();
        
        $contact['exists_in_serbis'] = $existingCustomer !== null;
        $contact['customer_id'] = $existingCustomer->id ?? null;
    } else {
        $contact['exists_in_serbis'] = false;
    }
}
            
            return view('frontend.secure.integrations.hipcall-contacts', [
                'tenant' => $tenant,
                'contacts' => $contacts,
                'total' => count($contacts),
                'success' => true
            ]);
        } else {
            return view('frontend.secure.integrations.hipcall-contacts', [
                'tenant' => $tenant,
                'contacts' => [],
                'total' => 0,
                'success' => false,
                'error' => $result['message']
            ]);
        }
        
    } catch (\Exception $e) {
        Log::error('Show Hipcall contacts error', [
            'error' => $e->getMessage()
        ]);
        
        return view('frontend.secure.integrations.hipcall-contacts', [
            'tenant' => $tenant,
            'contacts' => [],
            'total' => 0,
            'success' => false,
            'error' => 'Rehber yüklenirken hata oluştu: ' . $e->getMessage()
        ]);
    }
}

// Seçili kontakları SerbisERP'ye aktar
public function importHipcallContacts(Request $request, $tenant_id)
{
    try {
        $selectedContacts = $request->input('contacts', []);
        
        if (empty($selectedContacts)) {
            return response()->json([
                'success' => false,
                'message' => 'Lütfen en az bir kişi seçin'
            ]);
        }
        
        // JSON string ise decode et
        if (is_string($selectedContacts)) {
            $selectedContacts = json_decode($selectedContacts, true);
        }
        
        $hipcallService = new \App\Services\HipcallService($tenant_id);
        $result = $hipcallService->syncContactsToSerbis($tenant_id, $selectedContacts);
        
        $message = "{$result['success']} kişi aktarıldı";
        if ($result['skipped'] > 0) {
            $message .= ", {$result['skipped']} kişi zaten var (atlandı)";
        }
        if ($result['failed'] > 0) {
            $message .= ", {$result['failed']} kişi başarısız";
        }
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'results' => $result
        ]);
        
    } catch (\Exception $e) {
        Log::error('Import Hipcall contacts error', [
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Aktarma hatası: ' . $e->getMessage()
        ], 500);
    }
}

// Hipcall rehberini yenile (AJAX)
public function refreshHipcallContacts($tenant_id)
{
    try {
        $hipcallService = new \App\Services\HipcallService($tenant_id);
        $result = $hipcallService->getAllContacts();
        
        return response()->json($result);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Yenileme hatası: ' . $e->getMessage()
        ], 500);
    }
}
}
