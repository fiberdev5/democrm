<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
        if (session()->has('impersonated_user_id') && session()->has('impersonator_id') && Auth::check()) {
            $impersonatedUserId = session('impersonated_user_id');
            $impersonatorId = session('impersonator_id');

            // Eğer şu anki kullanıcı zaten impersonate edilen kullanıcı değilse
            if (Auth::id() != $impersonatedUserId) {
                 // Impersonate edilen kullanıcıyı bul
                $impersonatedUser = User::find($impersonatedUserId);
                
                if ($impersonatedUser) {
                    // Mevcut kullanıcı bilgilerini session'a kaydet (bu zaten start metodunda yapılıyor olmalı)
                    // session(['original_user_id' => Auth::id()]); // Tekrar kaydetmeye gerek yok

                    // İmpersonate edilen kullanıcı olarak giriş yap
                    Auth::setUser($impersonatedUser);
                    Auth::login($impersonatedUser); // Mevcut oturumu yenile
                    
                    // Spatie Permission cache'ini temizle
                    app()[PermissionRegistrar::class]->forgetCachedPermissions();
                    
                    // Kullanıcı rollerini fresh yükle
                    $impersonatedUser->load('roles', 'permissions');
                    
                    // Request'e impersonation bilgilerini ekle
                    $request->merge([
                        'is_impersonating' => true,
                        'impersonator_id' => $impersonatorId,
                        'original_user' => User::find($impersonatorId) // Orijinal kullanıcıyı da ekle
                    ]);
                } else {
                    // Impersonate edilen kullanıcı bulunamazsa session'ı temizle
                    session()->forget(['impersonator_id', 'impersonated_user_id', 'impersonation_id', 'impersonation_started_at']);
                }
            }
        }


        return $next($request);
    }
}
