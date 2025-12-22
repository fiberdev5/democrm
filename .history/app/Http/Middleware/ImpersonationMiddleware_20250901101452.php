<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class ImpersonationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Impersonation aktif mi kontrol et
        if (session()->has('impersonated_user_id') && session()->has('impersonator_id')) {
            $impersonatedUserId = session('impersonated_user_id');
            $impersonatorId = session('impersonator_id');

            // İmpersonate edilen kullanıcıyı bul
            $impersonatedUser = User::find($impersonatedUserId);
            
            // Kullanıcı bulunamadıysa veya aktif değilse impersonation'ı sonlandır
            if (!$impersonatedUser || !$impersonatedUser->canBeImpersonated()) {
                // Session'ları temizle
                session()->forget([
                    'impersonator_id',
                    'impersonated_user_id', 
                    'impersonation_id',
                    'impersonation_started_at',
                    'original_user_tenant_id'
                ]);
                
                // Orijinal kullanıcıya dön
                $originalUser = User::find($impersonatorId);
                if ($originalUser) {
                    Auth::login($originalUser, true);
                }
                
                return redirect()->route('secure.home')->with([
                    'message' => 'Impersonation session sonlandırıldı.',
                    'alert-type' => 'warning'
                ]);
            }

            // Mevcut auth kullanıcısı impersonated user değilse değiştir
            if (Auth::id() !== $impersonatedUserId) {
                Auth::setUser($impersonatedUser);
                
                // Spatie Permission cache'ini temizle
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                
                // Kullanıcı rollerini fresh yükle
                $impersonatedUser->load('roles', 'permissions');
            }
                        
            // Request'e impersonation bilgilerini ekle
            $request->merge([
                'is_impersonating' => true,
                'impersonator_id' => $impersonatorId,
                'original_user' => User::find($impersonatorId)
            ]);
        }

        return $next($request);
    }
}