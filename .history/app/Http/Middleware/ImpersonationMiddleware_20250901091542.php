<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class ImpersonationMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Impersonation aktif mi kontrol et
        if (session()->has('impersonated_user_id') && session()->has('impersonator_id')) {
            $impersonatedUserId = session('impersonated_user_id');
            $impersonatorId = session('impersonator_id');
            
            // Mevcut Auth kullanıcısının ID'si ile session'daki farklı mı?
            $currentAuthUserId = Auth::id();
            
            // Eğer zaten doğru kullanıcı olarak giriş yapılmışsa tekrar set etme
            if ($currentAuthUserId == $impersonatedUserId) {
                // Request'e impersonation bilgilerini ekle (tekrar auth işlemi yapmadan)
                $request->merge([
                    'is_impersonating' => true,
                    'impersonator_id' => $impersonatorId,
                    'original_user' => User::find($impersonatorId)
                ]);
                
                return $next($request);
            }

            // İmpersonate edilen kullanıcıyı bul
            $impersonatedUser = User::find($impersonatedUserId);
            
            if ($impersonatedUser) {
                // Auth kullanıcısını değiştir (logout/login olmadan)
                Auth::setUser($impersonatedUser);
                
                // Spatie Permission cache'ini temizle
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                
                // Request'e impersonation bilgilerini ekle
                $request->merge([
                    'is_impersonating' => true,
                    'impersonator_id' => $impersonatorId,
                    'original_user' => User::find($impersonatorId)
                ]);
            }
        }

        return $next($request);
    }
}