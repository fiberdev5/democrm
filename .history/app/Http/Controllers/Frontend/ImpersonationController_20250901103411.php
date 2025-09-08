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

        if (session()->has('impersonated_user_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Zaten başka bir kullanıcı olarak giriş yapmışsınız.'
            ], 400);
        }

        // Aktif impersonation session'u varsa sonlandır
        $activeImpersonation = $currentUser->getActiveImpersonation();
        if ($activeImpersonation) {
            $activeImpersonation->update(['ended_at' => now()]);
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
            'impersonation_started_at' => now(),
            'original_user_tenant_id' => $currentUser->tenant_id
        ]);

        // CRITICAL: Laravel'in session auth mekanizmasını manuel güncelle
        session()->put('login_web_' . sha1('web'), $targetUser->user_id);
        
        // Auth instance'ını force update
        Auth::setUser($targetUser);
        
        // Cache temizle
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$targetUser->name} olarak giriş yapıldı.",
                'redirect_url' => route('secure.home', ['tenant_id' => $targetUser->tenant_id])
            ]);
        }

        return redirect()->route('secure.home', ['tenant_id' => $targetUser->tenant_id])
                        ->with('message', "{$targetUser->name} olarak giriş yapıldı.")
                        ->with('alert-type', 'success');
    }

    public function stop(Request $request)
    {
        if (!session()->has('impersonator_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Aktif impersonation session bulunamadı.'
            ], 400);
        }

        $impersonatorId = session('impersonator_id');
        $impersonationId = session('impersonation_id');
        $originalTenantId = session('original_user_tenant_id');

        // Impersonation kaydını sonlandır
        if ($impersonationId) {
            UserImpersonation::find($impersonationId)?->update(['ended_at' => now()]);
        }

        // Orijinal kullanıcıyı bul
        $originalUser = User::find($impersonatorId);
        
        if (!$originalUser) {
            return response()->json([
                'success' => false,
                'message' => 'Orijinal kullanıcı bulunamadı.'
            ], 404);
        }

        // Session'ları temizle
        session()->forget([
            'impersonator_id',
            'impersonated_user_id', 
            'impersonation_id',
            'impersonation_started_at',
            'original_user_tenant_id'
        ]);

        // CRITICAL: Laravel'in session auth mekanizmasını manuel güncelle
        session()->put('login_web_' . sha1('web'), $originalUser->user_id);
        
        // Auth instance'ını force update
        Auth::setUser($originalUser);
        
        // Cache temizle
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kendi hesabınıza geri döndünüz.',
                'redirect_url' => route('secure.home', ['tenant_id' => $originalTenantId ?? $originalUser->tenant_id])
            ]);
        }

        return redirect()->route('secure.home', ['tenant_id' => $originalTenantId ?? $originalUser->tenant_id])
                        ->with('message', 'Kendi hesabınıza geri döndünüz.')
                        ->with('alert-type', 'info');
    }

    public function getUsersForImpersonation(Request $request, $tenantId)
    {
        $currentUser = Auth::user();

        if (!$currentUser->canImpersonate()) {
            return response()->json([
                'success' => false,
                'message' => 'İmpersonation yetkiniz yok.'
            ], 403);
        }

        $users = $currentUser->getImpersonatableUsers($tenantId);
        $tenant = Tenant::find($tenantId);

        return response()->json([
            'success' => true,
            'tenant' => $tenant ? ['id' => $tenant->id, 'firma_adi' => $tenant->firma_adi] : null,
            'users' => $users->map(function($user) {
                return [
                    'user_id' => $user->user_id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'roles' => $user->roles->pluck('name'),
                    'is_active' => $user->status == 1,
                    'last_login' => $user->last_login_at ?? null,
                    'ayrilma_tarihi' => $user->ayrilmaTarihi,
                    'can_be_impersonated' => $user->canBeImpersonated()
                ];
            })
        ]);
    }

    public function getImpersonationHistory(Request $request)
    {
        $currentUser = Auth::user();
        
        $query = UserImpersonation::with(['impersonator', 'impersonated', 'tenant'])
                                 ->where('tenant_id', $currentUser->tenant_id);

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