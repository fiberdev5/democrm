<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // User modelini kullanmak için ekle

class ImpersonationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Impersonation aktif mi kontrol et
        if (session()->has('impersonated_user_id') && session()->has('impersonator_id')) {
            $impersonatedUserId = session('impersonated_user_id');
            $impersonatorId = session('impersonator_id');

            // Eğer Auth::user() zaten impersonate edilen kullanıcı değilse,
            // bir sorun var demektir veya session yenilenmemiştir.
            // Bu durumda Auth::login ile kullanıcıyı tekrar set edebiliriz,
            // ancak normalde start() metodu bunu yapmış olmalı.
            // Bu kısım, oturumun doğru kurulduğundan emin olmak için bir güvenlik katmanı görevi görür.
            if (Auth::id() != $impersonatedUserId) {
                $impersonatedUser = User::find($impersonatedUserId);
                if ($impersonatedUser) {
                    Auth::login($impersonatedUser); // Güvenli yeniden giriş
                    // Spatie Permission cache'ini temizle
                    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                    $impersonatedUser->load('roles', 'permissions');
                }
            }
            
            // Request'e impersonation bilgilerini ekle
            // Orijinal kullanıcıyı her istekte DB'den çekmek yerine sadece ID'sini ekleyebiliriz.
            $request->merge([
                'is_impersonating' => true,
                'impersonator_id' => $impersonatorId,
                // İsteğe bağlı: 'impersonator_user' => User::find($impersonatorId)
                // Sadece gerçekten ihtiyacınız olduğunda çekin.
            ]);
        }
        // !!! ÖNEMLİ: ELSE durumunda Auth::user()'ı orijinal kullanıcıya çevirmeyin.
        // ImpersonationController@stop zaten bu işi yapıyor.
        // Middleware'in görevi sadece impersonation aktifken durumu ayarlamaktır.

        return $next($request);
    }
}