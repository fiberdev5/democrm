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
        // Impersonation route'larında çalışma
        if ($request->is('impersonation/*')) {
            return $next($request);
        }

        // Impersonation aktif mi kontrol et
        if (session()->has('impersonated_user_id') && session()->has('impersonator_id')) {
            $impersonatedUserId = session('impersonated_user_id');
            $impersonatorId = session('impersonator_id');
            
            // Mevcut Auth kullanıcısının ID'si ile session'daki farklı mı?
            $currentAuthUserId = Auth::id();
            
            // Eğer zaten doğru kullanıcı olarak giriş yapılmışsa middleware'i atla
            if ($currentAuthUserId == $impersonatedUserId) {
                // Sadece request'e bilgi ekle, Auth işlemi yapma
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
                // Session'da işaretleme yap ki tekrar çalışmasın
                if (!session()->has('impersonation_middleware_processed')) {
                    session(['impersonation_middleware_processed' => true]);
                    
                    // Auth kullanıcısını değiştir (logout/login olmadan)
                    Auth::setUser($impersonatedUser);
                    
                    // Spatie Permission cache'ini temizle
                    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                    
                    // Kullanıcı rollerini yeniden yükle
                    $impersonatedUser->load('roles', 'permissions');
                }
                
                // Request'e impersonation bilgilerini ekle
                $request->merge([
                    'is_impersonating' => true,
                    'impersonator_id' => $impersonatorId,
                    'original_user' => User::find($impersonatorId)
                ]);
            }
        } else {
            // Impersonation yoksa işaretlemeyi temizle
            session()->forget('impersonation_middleware_processed');
        }

        return $next($request);
    }
}