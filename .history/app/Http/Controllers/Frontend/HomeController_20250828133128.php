<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomeSlide;
use App\Models\Faq;
use App\Models\Misyon;
use App\Models\Category;
use App\Models\Clients;
use App\Models\Feature;
use App\Models\Pricing;
use App\Models\Reference;
use App\Models\Settings;
use App\Models\Tenant;
use App\Models\TenantPrim;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function Index() {
        $slide = HomeSlide::orderBy('id', 'asc')->get();
        $home_about = Faq::find(1);
        $home_section = Misyon::find(1);
        $products = Category::orderBy('id', 'desc')->take(8)->get();
        $settings = Settings::find(1);
        $pricing = Pricing::orderBy('id','asc')->get();
        $references = Reference::get();
        $faqs = Clients::orderBy('job','asc')->get();
        $features = Feature::orderBy('sira','asc')->get();
        return view('frontend.index', compact('slide','references','features' ,'faqs','pricing' ,'home_about','settings', 'home_section','products'));
    }



    public function Pricing() {
        $prices = Pricing::orderBy('id','asc')->get();
        return view('frontend.pages.pricing', compact('prices'));
    }

    public function Seo($s) {
        $tr = array('ş','Ş','ı','I','İ','ğ','Ğ','ü','Ü','ö','Ö','Ç','ç','(',')','/',':',',',"'",'+','_','!','?','.');
        $eng = array('s','s','i','i','i','g','g','u','u','o','o','c','c','','','-','-','','','-','','','','');
        $s = str_replace($tr, $eng, $s);
        $s = strtolower($s);
        $s = preg_replace('/&amp;amp;amp;amp;amp;amp;amp;amp;amp;.+?;/', '', $s);
        $s = preg_replace('/\s+/', '-', $s);
        $s = preg_replace('|-+|', '-', $s);
        $s = preg_replace('/#/', '', $s);
        $s = trim($s, '-');
        return $s;
    }

    protected function generateUserEmail($userEmail, $domain)
    {
        $username = explode('@', $userEmail)[0]; // E-postanın kullanıcı adını alır
        return $username . '@' . $domain; // Kullanıcı adı ve firma domainiyle yeni e-posta oluşturur
    }

    public function Register() {
        return view('frontend.auth.register');
    }

    public function RegisterAction(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'firma_adi' => 'required|string|max:255',
            'tel' => 'required',
            'email' => 'required|email|max:255|unique:tenants,eposta',
            'password' => 'required|min:6',
        ], [
            // Özel hata mesajları
            'name.required' => 'Ad Soyad alanı zorunludur.',
            'firma_adi.required' => 'Firma Adı alanı zorunludur.',
            'tel.required' => 'Telefon alanı zorunludur.',
            'tel.regex' => 'Telefon formatı hatalıdır (örn: 0234 567 8901).',
            'email.required' => 'E-posta alanı zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique' => 'Bu e-posta adresi zaten kayıtlı.',
            'password.required' => 'Şifre alanı zorunludur.',
            'password.min' => 'Şifre en az 6 karakter olmalıdır.',
        ]);
        $baslik = $request->firma_adi;
        $username = $this->Seo($baslik);

        $firmaAdiSlug = Str::slug($request->firma_adi, '-');
        $tenant = new Tenant([
            'name' => $request->name,
            'firma_adi' => $request->firma_adi,
            'firma_slug' => $firmaAdiSlug,
            'tel1' => $request->tel,
            'eposta' => $request->email,
            'username' => strtolower(str_replace(' ', '', $request->firma_adi)) . '.com',
            'kayitTarihi' => Carbon::now(),
            'bitisTarihi' => Carbon::now()->addYear(),
        ]);
        $tenant->save();

        $tenant_id = $tenant->id;

        $user = new User([
            'name' => $request->name,
            'username' => $username,
            'tel' => $request->tel,
            'eposta' => $this->generateUserEmail($username, $tenant->username),
            'tenant_id' => $tenant_id,
            'password' => Hash::make($request->password),
            'status' => '1',
            'baslamaTarihi' => Carbon::now()->format('Y-m-d'),
        ]);
        $user->save();
        $user->syncRoles("Patron");

        TenantPrim::create([
            'firma_id' => $tenant_id,
            'operatorPrim' => 0.00,
            'operatorPrimTutari' => 0,
            'teknisyenPrim' => 0.00,
            'teknisyenPrimTutari' => 0,
            'atolyePrim' => 0.00,
            'atolyePrimTutari' => 0,
        ]);

        $notification = array(
            'message' => 'Hesabınız başarıyla oluşturuldu',
            'alert-type' => 'success'
        );

        return redirect()->route('giris')->with($notification);

    }

    public function Login(){
        return view('frontend.auth.login');
    }

    public function LoginAction(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'g-recaptcha-response' => 'required|recaptcha',
        ], [
            'g-recaptcha-response.required' => 'Güvenlik doğrulaması zorunludur.',
            'g-recaptcha-response.recaptcha' => 'Güvenlik doğrulaması doğrulaması başarısız. Lütfen tekrar deneyin.',
        ]);
    
        // E-posta adresinden domain'i al
        [$username, $domain] = explode('@', $request->email);
    
        // Domain ile tenant'ı doğrula
        $tenant = Tenant::where('username', $domain)->first();
    
        if (!$tenant) {
            $notification = array(
                'message' => 'Geçersiz firma veya kullanıcı!',
                'alert-type' => 'danger'
            );
            return redirect()->back()->with($notification);
        }
    
        // Kullanıcıyı doğrula
        $credentials = [
            'eposta' => $request->email,
            'password' => $request->password,
            'tenant_id' => $tenant->id, 
        ];
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $tenantId = $user->tenant->id;
            $notification = array(
                'message' => 'Başarıyla giriş yapıldı.',
                'alert-type' => 'success'
            );
            return redirect()->route('secure.home', ['tenant_id' => $tenantId])->with($notification);
        }
        else{
            $notification = array(
                'message' => 'Geçersiz giriş bilgileri!',
                'alert-type' => 'danger'
            );
            return redirect()->back()->with($notification);
        }
    
    }

    public function Dashboard($tenant_id) {
    $user = Auth::user();
    if ($user->tenant->id != $tenant_id) {
        $notification = array(
            'message' => 'Yetkisiz erişim yapıldı',
            'alert-type' => 'danger'
        );
        return redirect()->back()->with($notification);
    }

    //verileri view'e gönder
    $last_services = $this->getLastServices($tenant_id);
    $stock_alerts = $this->getStockAlerts($tenant_id);

    return view('frontend.secure.index', compact('user', 'last_services', 'stock_alerts'));
}
// Dashboard istatistikleri
public function getStats($tenant_id)
{
    try {
        $today = Carbon::today();
        $thirtyDaysAgo = $today->copy()->subMonth(); // Son 1 Ay

        // AYLIK kasa hesaplama
        $monthly_income = DB::table('cash_transactions')
            ->where('odemeDurum', 1)
            ->where('odemeYonu', 1)
            ->where('firma_id', $tenant_id)
            ->whereBetween('created_at', [$thirtyDaysAgo, $today->copy()->endOfDay()])
            ->sum('fiyat');

        $monthly_expense = DB::table('cash_transactions')
            ->where('odemeDurum', 1)
            ->where('odemeYonu', 2)
            ->where('firma_id', $tenant_id)
            ->whereBetween('created_at', [$thirtyDaysAgo, $today->copy()->endOfDay()])
            ->sum('fiyat');

         // İptal durumlarını ve Yeni Servis durumunu tanımla
        $cancelled_statuses = [244]; // İptal  //[244, 246, 241, 243, 242]; // İptal, Tamir Edilemiyor, Fiyat Anlaşılamadı vb.
        $new_service_status = [235]; // Yeni Servis
        
        // İşlemde olanları bulmak için hariç tutulacak durumlar
        $excluded_statuses = array_merge($cancelled_statuses, $new_service_status);
            
        $stats = [
            // Aylık servis sayısı
            'total_services' => DB::table('services')
                ->where('durum', '!=', 0)
                ->where('firma_id', $tenant_id) 
                ->whereBetween('kayitTarihi', [$thirtyDaysAgo->format('Y-m-d'), $today->format('Y-m-d')])
                ->count(),
            
            // Aylık yeni müşteri sayısı
            'total_customers' => DB::table('customers')
             ->where('firma_id', $tenant_id)
             ->whereBetween('created_at', [$thirtyDaysAgo, $today->copy()->endOfDay()])
             ->count(),
            
            // Aktif Personel sayısı
            'total_personnel' => DB::table('tb_user')
                ->join('model_has_roles', 'tb_user.user_id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->whereIn('roles.name', ['Patron','Teknisyen', 'Teknisyen Yardımcısı', 'Operatör', 'Atölye Çırağı', 'Atölye Ustası', 'Depocu','Müdür'])
                ->where('tb_user.status', 1) // Sadece aktif olanlar
                ->where('tb_user.tenant_id', $tenant_id)
                ->count(),
            
            // AYLIK kasa toplamı
            'monthly_cash' => [
                'net' => $monthly_income - $monthly_expense
            ],
            
            // Günlük servis sayıları 
            'today_services' => DB::table('services')
                ->where('kayitTarihi', $today->format('Y-m-d'))
                ->where('durum', '!=', 0)
                ->where('firma_id', $tenant_id)
                ->count(),
                
            'today_cancelled_services' => DB::table('services')
                ->where('kayitTarihi', $today->format('Y-m-d'))
                ->whereIn('servisDurum', $cancelled_statuses) // İptal durumundakiler
                ->where('durum', '!=', 0)
                ->where('firma_id', $tenant_id)
                ->count(),
                
            'today_in_process_services' => DB::table('services')
                ->where('kayitTarihi', $today->format('Y-m-d'))
                ->whereNotIn('servisDurum', $excluded_statuses) // Yeni veya İptal olmayanlar
                ->where('durum', '!=', 0)
                ->where('firma_id', $tenant_id)
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Veri alınırken hata oluştu: ' . $e->getMessage()
        ], 500);
    }
}
//Son servis talepleri
private function getLastServices($tenant_id)
{
    //Veritabanından son 4 servis
    $services_query = DB::table('services as s')
        ->join('customers as c', 's.musteri_id', '=', 'c.id')
        ->leftJoin('tb_user as u', 's.kid', '=', 'u.user_id')
        ->select(
            's.id as service_id',
            'c.adSoyad as customer_name',
            's.cihazAriza as service_description',
            'u.name as technician_name',
            's.updated_at as estimated_date',
            's.servisDurum as status_id' 
        )
        ->where('s.firma_id', $tenant_id)
        ->orderBy('s.created_at', 'desc')
        ->take(4)
        ->get();

    //Servis durum ID'lerini metne ve CSS sınıfına çeviren harita.
    $statusMap = [
        // Tamamlandı / Teslimat (Yeşil)
        
        252 => ['name' => 'Teslimata Hazır(Tamamlandı)', 'class' => 'status-completed'],
        253 => ['name' => 'Cihaz Teslim Edildi', 'class' => 'status-completed'],
        254 => ['name' => 'Şikayetçi', 'class' => 'status-completed'],
        255 => ['name' => 'Servisi Sonlandır', 'class' => 'status-completed'],
        256 => ['name' => 'Cihaz Satışı Yapıldı', 'class' => 'status-completed'],
        260 => ['name' => 'Cihaz Teslim Edildi (Parça Takıldı)', 'class' => 'status-completed'],
        272 => ['name' => 'Konsinye Cihaz Geri Alındı', 'class' => 'status-completed'],

        // Sorun / Problem / İptal (Kırmızı)
        241 => ['name' => 'Fiyat Anlaşılamadı', 'class' => 'status-high'],
        242 => ['name' => 'Ürün Garantili Çıktı', 'class' => 'status-high'],
        243 => ['name' => 'Müşteriye Ulaşılamadı', 'class' => 'status-high'],
        244 => ['name' => 'Müşteri İptal Etti', 'class' => 'status-high'],
        246 => ['name' => 'Cihaz Tamir Edilemiyor', 'class' => 'status-high'],
        
        // İşlemde / Atölyede (Turuncu)
        236 => ['name' => 'Teknisyen Yönledir', 'class' => 'status-medium'],
        237 => ['name' => 'Cihaz Atölyeye Alındı', 'class' => 'status-medium'],
        238 => ['name' => 'Parça Talep Et', 'class' => 'status-medium'],
        239 => ['name' => 'Yerinde Bakım Yapıldı', 'class' => 'status-medium'],
        245 => ['name' => 'Parçası Atölyeye Alındı', 'class' => 'status-medium'],
        250 => ['name' => 'Atölyede Tamir Ediliyor', 'class' => 'status-medium'],
        240 => ['name' => 'Atölyeye Aldır', 'class' => 'status-medium'],
        258 => ['name' => 'Tahsilata Gönder', 'class' => 'status-medium'],
        259 => ['name' => 'Parça Teslim Et', 'class' => 'status-medium'],
        261 => ['name' => 'Parça Hazır', 'class' => 'status-medium'],
        262 => ['name' => 'Nakliye Gönder', 'class' => 'status-medium'],
        271 => ['name' => 'Konsinye Cihaz Ata', 'class' => 'status-medium'],

        // Beklemede / Bilgi 
        235 => ['name' => 'Yeni Servisler', 'class' => 'status-pending'],
        247 => ['name' => 'Haber Verecek', 'class' => 'status-pending'],
        248 => ['name' => 'Yeniden Teknisyen Yölendir', 'class' => 'status-pending'],
        249 => ['name' => 'Müşteri Atölyeye Getirdi', 'class' => 'status-pending'],
        251 => ['name' => 'Teknisyen Yönlendir(Teslim Edilecek)', 'class' => 'status-pending'],
        257 => ['name' => 'Parça Takmak İçin Teknisyen Yönlendir', 'class' => 'status-pending'],
        263 => ['name' => 'Parça Siparişte', 'class' => 'status-pending'],
        264 => ['name' => 'Bayiye Gönnder', 'class' => 'status-pending'],
        266 => ['name' => 'Müşteri Para İade Edilecek', 'class' => 'status-pending'],
        267 => ['name' => 'Müşteri Para İade Edildi', 'class' => 'status-pending'],
        268 => ['name' => 'Fiyat Yükseltildi', 'class' => 'status-pending'],
        
        
        
    ];

    //Çektiğimiz verilere, yukarıdaki haritaya göre durum metnini ve CSS sınıfını ekleyelim.
    foreach ($services_query as $service) {
        // Eğer services tablosundan gelen status_id, $statusMap'te varsa onu kullan, yoksa default olanı kullan.
        $service->status_info = $statusMap[$service->status_id] ?? $statusMap['default'];
    }

    return $services_query;
}

//Kritik stoklar
private function getStockAlerts($tenant_id) 
{     
    $critical_level = 3; // Bu seviye ve altı KRİTİK     
    $low_level = 5;      // Bu seviye ve altı DÜŞÜK (ama kritik değil)     
        // Operatör rolüne sahip değilse stok verilerini yükle
    if (!Auth::user()->hasRole('Operatör')) {
        $stock_alerts = $this->getStockAlerts(); // Mevcut stok uyarıları metodunuz
    } 

    try {
        // Önce tüm aktif ürünleri getir
        $allProducts = DB::table('stocks')
            ->where('firma_id', $tenant_id)
            ->where('durum', '1') // Aktif ürünler
            ->where('urunKategori', '!=', 3) // Konsinye kategorisini hariç tut
            ->select('id', 'urunAdi', 'urunKodu', 'urunKategori')
            ->get();

        \Log::info('All active products count: ' . count($allProducts));

        $alerts = [
            'critical' => [],
            'low' => []
        ];

        foreach ($allProducts as $product) {
            // Her ürün için güncel stok hesapla
            $currentStockData = DB::table('stock_actions')
                ->where('stokId', $product->id)
                ->where('firma_id', $tenant_id)
                ->selectRaw('
                    SUM(CASE 
                        WHEN islem = 1 THEN adet 
                        WHEN islem = 2 THEN -adet 
                        ELSE 0 
                    END) as current_stock,
                    COUNT(*) as total_actions,
                    MAX(created_at) as last_action_date
                ')
                ->first();

            $currentStock = $currentStockData ? (int)$currentStockData->current_stock : 0;

            \Log::info("Product ID {$product->id} ({$product->urunAdi}): Current stock = {$currentStock}");

            // Stok 0'dan büyük olmalı (negatif stokları gösterme)
            if ($currentStock > 0) {
                if ($currentStock <= $critical_level) {
                    $product->current_stock = $currentStock;
                    $product->threshold = $critical_level;
                    $product->alert_type = 'critical';
                    $product->total_actions = $currentStockData->total_actions ?? 0;
                    $product->last_action_date = $currentStockData->last_action_date ?? null;
                    
                    $alerts['critical'][] = $product;
                    \Log::info("Added to critical: {$product->urunAdi} (Stock: {$currentStock})");
                    
                } elseif ($currentStock <= $low_level) {
                    $product->current_stock = $currentStock;
                    $product->threshold = $low_level;
                    $product->alert_type = 'low';
                    $product->total_actions = $currentStockData->total_actions ?? 0;
                    $product->last_action_date = $currentStockData->last_action_date ?? null;
                    
                    $alerts['low'][] = $product;
                    \Log::info("Added to low: {$product->urunAdi} (Stock: {$currentStock})");
                }
            } else {
                \Log::info("Product {$product->urunAdi} has zero or negative stock: {$currentStock}");
            }
        }

        // Stok seviyesine göre sırala (en düşük önce)
        usort($alerts['critical'], function($a, $b) {
            return $a->current_stock <=> $b->current_stock;
        });
        
        usort($alerts['low'], function($a, $b) {
            return $a->current_stock <=> $b->current_stock;
        });

        // Her listeden en fazla 2'er tane göster (dashboard'da daha fazla görünür olması için)
        $alerts['critical'] = array_slice($alerts['critical'], 0, 2);
        $alerts['low'] = array_slice($alerts['low'], 0, 2);

        \Log::info('Final Alerts Result:', [
            'critical_count' => count($alerts['critical']),
            'low_count' => count($alerts['low']),
            'total_products_checked' => count($allProducts)
        ]);

        return $alerts;

    } catch (\Exception $e) {
        \Log::error('Stock alerts error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        return ['critical' => [], 'low' => []];
    }
}
    // Dashboard grafik verileri
    public function getChartData(Request $request,$tenant_id)
    {
         // Kullanıcı yetkisi kontrolü
        $user = Auth::user();
        if ($user->tenant->id != $tenant_id) {
            return response()->json([
                'success' => false,
                'message' => 'Yetkisiz erişim'
            ], 403);
        }
        try {
            $period = $request->get('period', 7);
            $type = $request->get('type', 'daily');

           if ($type === 'daily') {
                return $this->getDailyChartData($period, $tenant_id);
            } else {
                return $this->getHourlyChartData($period, $tenant_id);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Grafik verisi alınırken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getDailyChartData($period,$tenant_id)
    {
        $startDate = Carbon::now()->subDays($period - 1);
        $endDate = Carbon::now();

        $services = DB::table('services')
            ->select(
                'kayitTarihi as date',
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('kayitTarihi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('durum', '!=', 0) // Silinmeyenler
            ->where('firma_id', $tenant_id)
            ->groupBy('kayitTarihi')
            ->orderBy('kayitTarihi')
            ->get();

        // Tüm günleri içeren array oluştur
        $labels = [];
        $data = [];
        
        for ($i = 0; $i < $period; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            $labels[] = $currentDate->format('d/m');
            
            // Bu günde servis var mı kontrol et
            $serviceCount = $services->where('date', $currentDate->format('Y-m-d'))->first();
            $data[] = $serviceCount ? $serviceCount->count : 0;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $labels,
                'data' => $data
            ]
        ]);
    }

    private function getHourlyChartData($period,$tenant_id)
    {
        $startDate = Carbon::now()->subDays($period - 1);
        $endDate = Carbon::now();

        // created_at sütunu var, saatlik dağılım için kullanabiliriz
        $services = DB::table('services')
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('durum', '!=', 0)
            ->where('firma_id', $tenant_id)
            ->whereNotNull('created_at')
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('hour')
            ->get();

        // 8-18 saatleri arası
        $labels = [];
        $data = [];
        
        for ($hour = 8; $hour <= 18; $hour++) {
            $labels[] = sprintf('%02d:00', $hour);
            
            $serviceCount = $services->where('hour', $hour)->first();
            $data[] = $serviceCount ? $serviceCount->count : 0;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $labels,
                'data' => $data
            ]
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $notification = array(
            'message' => 'Başarıyla çıkış yapıldı!',
            'alert-type' => 'success'
        );
        return redirect()->route('giris')->with($notification);
    }

    public function getStatesByCountry($countryId)
    {   $cities = DB::table('ilces')->where('sehir_id', $countryId)->orderBy('ilceName','asc')->get();
        return response()->json($cities);
    }

    
}
