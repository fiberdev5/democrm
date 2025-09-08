<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use App\Models\UserImpersonation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session; // Session facade'i ekliyoruz
use Spatie\Permission\PermissionRegistrar; // Spatie Permission Registrar'ı ekliyoruz

class ImpersonationController extends Controller
{
    public function start(Request $request, $userId)
    {
        $currentUser = Auth::user();
        $targetUser = User::find($userId);

        // Validasyonlar
        if (!$targetUser) {
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcı bulunamadı.'
            ], 404);
        }

        // İzin kontrolü
        if (!$currentUser->canImpersonate($targetUser)) {
            return response()->json([
                'success' => false,
                'message' => 'Bu kullanıcıyı impersonate etme yetkiniz yok.'
            ], 403);
        }

        // Hedef kullanıcı impersonate edilebilir mi?
        if (!$targetUser->canBeImpersonated()) {
            return response()->json([
                'success' => false,
                'message' => 'Bu kullanıcı impersonate edilemez.'
            ], 403);
        }

        // // Aynı tenant kontrolü
        // if ($currentUser->tenant_id !== $targetUser->tenant_id) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Sadece aynı firmadaki kullanıcıları impersonate edebilirsiniz.'
        //     ], 403);
        // }

        // Zaten impersonation aktif mi kontrol et
        if (session()->has('impersonated_user_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Zaten başka bir kullanıcı olarak giriş yapmışsınız.'
            ], 400);
        }

        // Aktif impersonation session'u varsa sonlandır
        $activeImpersonation = $currentUser->getActiveImpersonation();
        if ($activeImpersonation) {
            $activeImpersonation->update([
                'ended_at' => now()
            ]);
        }

        // Yeni impersonation kaydı oluştur
        $impersonation = UserImpersonation::create([
            'impersonator_id' => $currentUser->user_id,
            'impersonated_id' => $targetUser->user_id,
            'tenant_id' => $targetUser->tenant_id,
            'started_at' => now(),
            'ip_address' => $request->ip(),
            'reason' => $request->input('reason', 'Admin tarafından başlatıldı')
        ]);

        // Session bilgilerini ayarla
        session([
            'impersonator_id' => $currentUser->user_id,
            'impersonated_user_id' => $targetUser->user_id,
            'impersonation_id' => $impersonation->id,
            'impersonation_started_at' => now()
        ]);

        // Auth kullanıcısını değiştir ve cache'i temizle
        Auth::setUser($targetUser);

        // ÖNEMLI: Logout/Login simulasyonu yap
        Auth::logout();
        Auth::login($targetUser);
                
        // Spatie Permission cache'ini temizle
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Kullanıcı rollerini yeniden yükle
        $targetUser->load('roles', 'permissions');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$targetUser->name} olarak giriş yapıldı.",
                'redirect_url' => route('secure.home', ['tenant_id' => $targetUser->tenant_id])
            ]);
        }

        $notification = [
            'message' => "{$targetUser->name} olarak giriş yapıldı.",
            'alert-type' => 'success'
        ];

        return redirect()->route('secure.home', ['tenant_id' => $targetUser->tenant_id])->with($notification);
    }

       public function stop(Request $request)
    {
        if (!Session::has('impersonator_id') || !Session::has('impersonated_user_id') || !Session::has('impersonation_id')) {
            $notification = [
                'message' => 'Aktif impersonation oturumu bulunamadı.',
                'alert-type' => 'info'
            ];
            return redirect()->route('giris')->with($notification); 
        }

        $impersonatorId = Session::get('impersonator_id');
        $impersonationRecordId = Session::get('impersonation_id');

        if ($impersonationRecordId) {
            $impersonation = UserImpersonation::find($impersonationRecordId);
            if ($impersonation) {
                $impersonation->update([
                    'ended_at' => now()
                ]);
            }
        }

        $originalUser = User::find($impersonatorId);

        Session::forget([
            'impersonator_id',
            'impersonated_user_id', 
            'impersonation_id',
            'impersonation_started_at'
        ]);

        if ($originalUser) {
            Auth::guard()->login($originalUser);
            app(PermissionRegistrar::class)->forgetCachedPermissions();
            $originalUser->load('roles', 'permissions');

            // --- Yönlendirme Mantığını BURADA Ayırıyoruz ---
            $redirectUrl = null;
            $message = 'Kendi hesabınıza geri döndünüz.';
            $alertType = 'info';

            if ($originalUser->isSuperAdmin()) {
                $redirectUrl = route('super.admin.dashboard');
                $message = 'Super Admin paneline geri döndünüz.';
                $alertType = 'success';
            } else {
                // Normal kullanıcılar için tenant_id kontrolü
                if ($originalUser->tenant_id) {
                    $redirectUrl = route('secure.home', ['tenant_id' => $originalUser->tenant_id]);
                } else {
                    // Tenant_id'si olmayan normal kullanıcılar için bir yedek rota
                    // Bu senaryonun olmaması gerekir, ama güvenlik için ekliyorum.
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    $redirectUrl = route('giris');
                    $message = 'Hesabınızın tenant bilgisi bulunamadı. Lütfen tekrar giriş yapın.';
                    $alertType = 'danger';
                }
            }
            // --- Yönlendirme Mantığı Sonu ---

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect_url' => $redirectUrl
                ]);
            }

            return redirect($redirectUrl)->with(['message' => $message, 'alert-type' => $alertType]);

        } else {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $notification = [
                'message' => 'Orijinal kullanıcı bulunamadı. Güvenlik nedeniyle oturumunuz kapatıldı. Lütfen tekrar giriş yapın.',
                'alert-type' => 'danger'
            ];
            return redirect()->route('giris')->with($notification);
        }
    }

    public function getUsersForImpersonation(Request $request, $tenantId)
    {
          $currentUser = Auth::user();

    //Yetki kontrolü
    if (!$currentUser->canImpersonate()) {
        return response()->json([
            'success' => false,
            'message' => 'İmpersonation yetkiniz yok.'
        ], 403);
    }

    // Tenant kontrolü 
    // if ($currentUser->tenant_id != $tenantId) {
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Sadece kendi firmanızdaki kullanıcıları görebilirsiniz.'
    //     ], 403);
    // }

    $users = $currentUser->getImpersonatableUsers($tenantId); // $tenantId'yi buraya parametre olarak gönderin

    // Tenant bilgilerini de döndürelim, modal başlığında kullanmak için
    $tenant = Tenant::find($tenantId);

    return response()->json([
        'success' => true,
        'tenant' => $tenant ? ['id' => $tenant->id, 'firma_adi' => $tenant->firma_adi] : null, // Firma adını döndür
        'users' => $users->map(function($user) {
            return [
                'user_id' => $user->user_id,
                'name' => $user->name,
                'username' => $user->username,
                'roles' => $user->roles->pluck('name'),
                'is_active' => $user->status == 1,
                'last_login' => $user->last_login_at ?? null,
                'ayrilma_tarihi' => $user->ayrilmaTarihi, // Ayrılma tarihini de ekleyelim
                'can_be_impersonated' => $user->canBeImpersonated() // Burası ÖNEMLİ!
            ];
        })
    ]);
}

    public function getImpersonationHistory(Request $request)
    {
        $currentUser = Auth::user();
        
        $query = UserImpersonation::with(['impersonator', 'impersonated', 'tenant'])
                                 ->where('tenant_id', $currentUser->tenant_id);

        // Sadece kendi yaptıklarını görmek istiyorsa
        if ($request->get('my_only')) {
            $query->where('impersonator_id', $currentUser->user_id);
        }

        $impersonations = $query->recent(30)
                               ->orderBy('started_at', 'desc')
                               ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $impersonations
        ]);
    }

    public function checkStatus()
    {
        $isImpersonating = session()->has('impersonator_id');
        
        if ($isImpersonating) {
            $impersonatorId = session('impersonator_id');
            $impersonatedUserId = session('impersonated_user_id');
            
            return response()->json([
                'is_impersonating' => true,
                'impersonator' => User::find($impersonatorId)?->only(['user_id', 'name']),
                'impersonated' => User::find($impersonatedUserId)?->only(['user_id', 'name']),
                'started_at' => session('impersonation_started_at')
            ]);
        }

        return response()->json([
            'is_impersonating' => false
        ]);
    }
}