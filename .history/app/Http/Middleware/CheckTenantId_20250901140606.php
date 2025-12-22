<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class CheckTenantId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $tenantId = $request->route('tenant_id');

        // SuperAdmin her tenant'a erişebilir (impersonation olmadığında)
        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin() && !session()->has('impersonated_user_id')) {
            return $next($request);
        }

        // Eğer impersonation aktifse, impersonated user'ın tenant_id'sini kullan
        if (session()->has('impersonated_user_id')) {
            $impersonatedUser = User::find(session('impersonated_user_id'));
            if ($impersonatedUser) {
                // İmpersonated user'ın tenant_id'si ile route'daki tenant_id uyuşmalı
                if ($impersonatedUser->tenant_id != $tenantId) {
                    // Impersonation'ı sonlandır ve uygun tenant'a yönlendir
                    return redirect()->route('secure.home', ['tenant_id' => $impersonatedUser->tenant_id])
                                   ->with('warning', 'Yanlış tenant sayfasına yönlendirildiniz. Doğru sayfaya yönlendiriliyorsunuz.');
                }
            }
        } else {
            // Normal kullanıcı için tenant kontrolü
            if ($user && $user->tenant_id != $tenantId) {
                abort(403, 'Bu firmaya erişim yetkiniz yok.');
            }
        }

        // Tenant var mı kontrol et
        $tenant = DB::table('tenants')->where('id', $tenantId)->first();

        if (!$tenant) {
            return redirect()->route('giris')->withErrors(['error' => 'Geçersiz tenant ID.']);
        }

        // Tenant bilgisini paylaş
        $request->attributes->set('tenant', $tenant);

        return $next($request);
    }
}