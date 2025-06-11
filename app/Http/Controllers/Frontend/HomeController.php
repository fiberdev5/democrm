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
        //dd($tenant_id);

        $user = new User([
            'name' => $request->name,
            'username' => $username,
            'tel' => $request->tel,
            'eposta' => $this->generateUserEmail($username, $tenant->username),
            'tenant_id' => $tenant_id,
            'password' => Hash::make($request->password),
            'status' => '1',
        ]);
        $user->save();
        $user->syncRoles("Patron");

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
            'tenant_id' => $tenant->id, // Firma kontrolü
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
                'message' => 'Yetkisiz  erişim yapıldı',
                'alert-type' => 'danger'
            );
            return redirect()->back()->with($notification);
        }
        return view('frontend.secure.index', compact('user'));
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
