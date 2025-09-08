<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use App\Models\UserImpersonation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    if (!session()->has('impersonator_id') || !session()->has('impersonated_user_id')) {
        return response()->json([
            'success' => false,
            'message' => 'Aktif impersonation session bulunamadı.'
        ], 400);
    }

    $impersonatorId = session('impersonated_user_id');    // ✅ gerçek ID
    $impersonationId = session('impersonator_id');

    // Impersonation kaydını sonlandır
    if ($impersonationId) {
        UserImpersonation::find($impersonationId)?->update([
            'ended_at' => now()
        ]);
    }

    // Orijinal kullanıcıya geri dön
    $originalUser = User::find($impersonatorId);
    if ($originalUser) {
        Auth::logout();
        Auth::login($originalUser);

        // Spatie Permission cache'ini temizle
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

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

    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'Kendi hesabınıza geri döndünüz.'
        ]);
    }

    $notification = [
        'message' => 'Kendi hesabınıza geri döndünüz.',
        'alert-type' => 'info'
    ];

    return redirect()->route('secure.home', ['tenant_id' => $originalUser->tenant_id])
                     ->with($notification);
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