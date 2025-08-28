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

    // Yeni verileri view'e gönder
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
            
            // Günlük servis sayıları (bunlar aynı kalabilir)
            'today_services' => DB::table('services')
                ->where('kayitTarihi', $today->format('Y-m-d'))
                ->where('durum', '!=', 0)
                ->where('firma_id', $tenant_id)
                ->count(),
                
            'yesterday_services' => DB::table('services')
                ->where('kayitTarihi', Carbon::yesterday()->format('Y-m-d'))
                ->where('durum', '!=', 0)
                ->where('firma_id', $tenant_id)
                ->count(),
                
            'previous_services' => DB::table('services')
                ->where('kayitTarihi', $today->copy()->subDays(2)->format('Y-m-d'))
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
// YENİ: Son servis taleplerini getiren fonksiyon
private function getLastServices($tenant_id)
{
    // Veritabanından son 5 servisi çekiyoruz
    $services_query = DB::table('services as s')
        ->join('customers as c', 's.musteri_id', '=', 'c.id')
        // Teknisyen 'pid' sütunu ile 'tb_user' tablosundan geliyor. Atanmamış olabilir diye leftJoin kullanıyoruz.
        ->leftJoin('tb_user as u', 's.pid', '=', 'u.user_id')
        ->select(
            's.id as service_id',
            'c.adSoyad as customer_name', // Müşteri adı 'adSoyad' sütunundan
            's.cihazAriza as service_description', // Servis açıklaması 'cihazAriza' sütunundan
            'u.name as technician_name', // Teknisyen adı 'tb_user' tablosundaki 'name' sütunundan
            's.musaitTarih as estimated_date', // Tahmini bitiş için en uygun sütun 'musaitTarih'
            's.servisDurum as status_id' // Durumun ID'si (1, 2, 3 vb.)
        )
        ->where('s.firma_id', $tenant_id)
        ->orderBy('s.created_at', 'desc')
        ->take(5)
        ->get();

    // Servis durum ID'lerini metne ve CSS sınıfına çeviren bir harita (map) oluşturalım.
    // NOT: Buradaki ID'leri ve metinleri kendi sisteminizdeki durumlara göre düzenlemelisiniz.
    $statusMap = [
        1 => ['name' => 'Yeni Servis', 'class' => 'status-high'],
        2 => ['name' => 'İşlemde', 'class' => 'status-medium'],
        3 => ['name' => 'Tamamlandı', 'class' => 'status-completed'],
        4 => ['name' => 'Beklemede', 'class' => 'status-pending'],
        5 => ['name' => 'İptal Edildi', 'class' => 'status-cancelled'], // Örnek
        // ... diğer durumlarınız
        'default' => ['name' => 'Bilinmiyor', 'class' => 'status-pending']
    ];

    // Çektiğimiz verilere durum metnini ve CSS sınıfını ekleyelim
    foreach ($services_query as $service) {
        $service->status_info = $statusMap[$service->status_id] ?? $statusMap['default'];
    }

    return $services_query;
}

// YENİDEN DÜZENLENDİ: Kritik stokları getiren fonksiyon
private function getStockAlerts($tenant_id)
{
    // ÖNEMLİ VARSAYIMLAR:
    // 1. Ürün bilgilerinin (ad, kategori, kritik seviyeler) tutulduğu bir 'products' tablonuz olduğunu varsayıyorum.
    // 2. 'stock_actions' tablosunun 'product_id' ile 'products' tablosuna bağlı olduğunu varsayıyorum.
    // 3. 'adet' sütunu, stok girişi için pozitif (+), çıkışı için negatif (-) değerler alıyor.

    try {
        // Her bir ürünün mevcut stoğunu 'stock_actions' tablosundan hesaplayalım
        $currentStock = DB::table('stock_actions')
            ->select('product_id', DB::raw('SUM(adet) as current_stock'))
            ->where('firma_id', $tenant_id)
            ->groupBy('product_id');

        // Hesaplanan stokları ürün bilgileriyle birleştirip kritik ve düşük seviyeleri bulalım
        // EĞER products tablonuzun adı farklıysa (örn: urunler), burayı güncelleyin.
        $products = DB::table('products as p')
            ->joinSub($currentStock, 'stock', function ($join) {
                $join->on('p.id', '=', 'stock.product_id');
            })
            ->where('p.firma_id', $tenant_id)
            // Sadece düşük veya kritik seviyedeki ürünleri getir
            ->where('stock.current_stock', '<=', DB::raw('p.low_stock_level')) // low_stock_level sütununuz olmalı
            ->select(
                'p.id',
                'p.product_name', // product_name sütununuz olmalı
                'p.category',     // category sütununuz olmalı
                'stock.current_stock',
                'p.critical_stock_level' // critical_stock_level sütununuz olmalı
            )
            ->orderBy('stock.current_stock', 'asc')
            ->get();

        $alerts = [
            'critical' => [],
            'low' => []
        ];

        foreach ($products as $product) {
            // Mevcut stok, kritik seviyenin altındaysa 'critical' listesine ekle
            if ($product->current_stock <= $product->critical_stock_level) {
                $alerts['critical'][] = $product;
            } else {
                // Değilse 'low' listesine ekle
                $alerts['low'][] = $product;
            }
        }
        
        // Her listeden en fazla 2'şer tane göster
        $alerts['critical'] = array_slice($alerts['critical'], 0, 2);
        $alerts['low'] = array_slice($alerts['low'], 0, 2);

        return $alerts;

    } catch (\Illuminate\Database\QueryException $e) {
        // Eğer 'products' veya 'stock_actions' tablosu bulunamazsa hata vermemesi için boş bir array döndür.
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
