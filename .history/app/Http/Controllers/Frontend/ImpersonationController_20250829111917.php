<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserImpersonation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function start(Request $request, $userId)
    {
        $currentUser = Auth::user();
        $targetUser = User::find($userId);

        if (!$targetUser) {
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcı bulunamadı.'
            ], 404);
        }

        // Super Admin, kendi firmanızdaki kısıtlamalarına tabi değildir.
        // Ancak yine de hedef kullanıcının impersonate edilebilir olması gerekir (canBeImpersonated metodu ile kontrol).
        if ($currentUser->hasRole('Super Admin')) {
            // Super Admin her kullanıcıyı impersonate edebilir, kendi tenant_id kontrolüne takılmaz.
            // Sadece hedef kullanıcının impersonate edilebilir olup olmadığını kontrol et.
            if (!$targetUser->canBeImpersonated()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu kullanıcı impersonate edilemez (örneğin başka bir Super Admin).'
                ], 403);
            }
        } else {
            // Normal adminler için mevcut kısıtlamalar geçerli
            if (!$currentUser->canImpersonate($targetUser)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu kullanıcıyı impersonate etme yetkiniz yok.'
                ], 403);
            }

            if (!$targetUser->canBeImpersonated()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu kullanıcı impersonate edilemez.'
                ], 403);
            }

            if ($currentUser->tenant_id !== $targetUser->tenant_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sadece aynı firmadaki kullanıcıları impersonate edebilirsiniz.'
                ], 403);
            }
        }

        // Zaten impersonation aktif mi kontrol et
        if (session()->has('impersonated_user_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Zaten başka bir kullanıcı olarak giriş yapmışsınız. Önce mevcut impersonation\'ı sonlandırmalısınız.'
            ], 400);
        }

        // Aktif impersonation session'u varsa sonlandır (bu kısım zaten stop metodu ile yönetiliyor, burası kaldırılabilir)
        // Ancak yine de kontrol etmek iyi bir pratik olabilir.
        // $activeImpersonation = UserImpersonation::where('impersonator_id', $currentUser->user_id)
        //     ->whereNull('ended_at')
        //     ->first();
        // if ($activeImpersonation) {
        //     $activeImpersonation->update(['ended_at' => now()]);
        // }


        // Yeni impersonation kaydı oluştur
        $impersonation = UserImpersonation::create([
            'impersonator_id' => $currentUser->user_id,
            'impersonated_id' => $targetUser->user_id,
            'tenant_id' => $targetUser->tenant_id,
            'started_at' => now(),
            'ip_address' => $request->ip(),
            'reason' => $request->input('reason', 'Yönetici tarafından başlatıldı')
        ]);

        // Session bilgilerini ayarla
        session([
            'impersonator_id' => $currentUser->user_id,
            'impersonated_user_id' => $targetUser->user_id,
            'impersonation_id' => $impersonation->id,
            'impersonation_started_at' => now()->toDateTimeString() // Carbon nesnesini string'e çevir
        ]);

        // ÖNEMLI: Logout/Login simulasyonu yap
        Auth::logout();
        Auth::login($targetUser);
        
        // Spatie Permission cache'ini temizle
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        
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
        if (!session()->has('impersonator_id') || !session()->has('impersonated_user_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Aktif impersonation session bulunamadı.'
            ], 400);
        }

        $impersonatorId = session('impersonator_id');
        $impersonationId = session('impersonation_id');
        $impersonatedUserId = session('impersonated_user_id'); // Add this to correctly redirect after stopping

        // Impersonation kaydını sonlandır
        if ($impersonationId) {
            UserImpersonation::find($impersonationId)?->update([
                'ended_at' => now()
            ]);
        }

        // Orijinal kullanıcıya geri dön
        $originalUser = User::find($impersonatorId);
        if ($originalUser) {
            Auth::logout(); // Önce çıkış yap
            Auth::login($originalUser); // Sonra orijinal kullanıcı olarak giriş yap
            
            // Spatie Permission cache'ini temizle
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            
            // Kullanıcı rollerini yeniden yükle
            $originalUser->load('roles', 'permissions');
        }

        // Session'ları temizle
        session()->forget([
            'impersonator_id',
            'impersonated_user_id', 
            'impersonation_id',
            'impersonation_started_at'
        ]);

        // Redirection URL'sini dinamik olarak belirle
        $redirectUrl = route('secure.home', ['tenant_id' => $impersonatedUserId]); // Varsayılan olarak impersonated kullanıcının firmasına dön
        if ($originalUser && $originalUser->hasRole('Super Admin')) {
             $redirectUrl = route('super_admin.tenants.index'); // Eğer Super Admin ise Super Admin paneline dön
        } else if ($originalUser && $originalUser->tenant_id) {
            $redirectUrl = route('all.tenants', ['tenant_id' => $originalUser->tenant_id]); // Kendi firmasındaki tüm firmalara dön
        } else {
            $redirectUrl = route('giris'); // Başka bir senaryo için varsayılan
        }


        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kendi hesabınıza geri döndünüz.',
                'redirect_url' => $redirectUrl
            ]);
        }

        $notification = [
            'message' => 'Kendi hesabınıza geri döndünüz.',
            'alert-type' => 'info'
        ];

        return redirect($redirectUrl)->with($notification);
    }

    public function getUsersForImpersonation(Request $request, $tenantId)
    {
        $currentUser = Auth::user();
        $targetTenant = Tenant::find($tenantId);

        if (!$targetTenant) {
            return response()->json([
                'success' => false,
                'message' => 'Firma bulunamadı.'
            ], 404);
        }

        // Super Admin ise tüm tenant'lardaki kullanıcıları görebilir
        if ($currentUser->hasRole('Super Admin')) {
            // Bu tenant'taki impersonate edilebilir kullanıcıları getir
            $users = User::where('tenant_id', $tenantId)
                         ->where('user_id', '!=', $currentUser->user_id) // Kendi kendini impersonate etmemeli
                         ->get();
        } else {
            // Normal adminler sadece kendi tenant'larındaki kullanıcıları görebilir
            if ($currentUser->tenant_id != $tenantId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sadece kendi firmanızdaki kullanıcıları görebilirsiniz.'
                ], 403);
            }
            $users = $currentUser->getImpersonatableUsersForTenant($tenantId); // Belirli bir tenant için kullanıcıları getiren yeni metod
        }

        return response()->json([
            'success' => true,
            'tenant' => [
                'id' => $targetTenant->id,
                'firma_adi' => $targetTenant->firma_adi,
                'firma_slug' => $targetTenant->firma_slug,
            ],
            'users' => $users->map(function($user) {
                return [
                    'user_id' => $user->user_id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'roles' => $user->roles->pluck('name'),
                    'is_active' => $user->status == 1,
                    'last_login' => $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : null,
                    'ayrilma_tarihi' => $user->ayrilmaTarihi ? $user->ayrilmaTarihi->format('Y-m-d') : null,
                    'can_be_impersonated' => $user->canBeImpersonated(), // Her kullanıcı için kontrol
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