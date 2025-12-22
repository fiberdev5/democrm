<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User; // User modelini ekleyin

class ImpersonationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Impersonation aktif mi kontrol et
        if (session()->has('impersonated_user_id') && session()->has('impersonator_id')) {
            $impersonatedUserId = session('impersonated_user_id');
            $impersonatorId = session('impersonator_id'); // Impersonator ID'yi de alıyoruz

            // İmpersonate edilen kullanıcıyı bul
            $impersonatedUser = User::find($impersonatedUserId);
            
            if ($impersonatedUser) {
                // ÖNEMLİ: Auth::login() kullanın, sadece Auth::setUser() yeterli değildir.
                // Bu, session'ı ve guard'ı doğru şekilde güncelleyecektir.
                Auth::login($impersonatedUser); 
                
                // Spatie Permission cache'ini temizle
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                
                // Kullanıcı rollerini fresh yükle
                $impersonatedUser->load('roles', 'permissions');
                
                // Request'e impersonation bilgilerini ekle
                $request->merge([
                    'is_impersonating' => true,
                    'impersonator_id' => $impersonatorId,
                    'original_user' => User::find($impersonatorId) // Orijinal kullanıcıyı da request'e ekliyoruz
                ]);
            } else {
                // Impersonated kullanıcı bulunamazsa session'ı temizle
                session()->forget([
                    'impersonator_id',
                    'impersonated_user_id', 
                    'impersonation_id',
                    'impersonation_started_at'
                ]);
            }
        } 
        // Impersonation durdurulduysa veya hiç aktif değilse, orijinal kullanıcı zaten Auth::user() olmalıdır.
        // Bu yüzden burada ekstra bir şey yapmaya gerek yok, çünkü stop metodunda zaten ele alınıyor.

        return $next($request);
    }
}