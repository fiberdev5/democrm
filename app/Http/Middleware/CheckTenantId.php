<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckTenantId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // URL'deki {tenant_id} parametresini al
        $tenantId = $request->route('tenant_id');

        // tenant_id'nin doğruluğunu kontrol et
        $tenant = DB::table('tenants')->where('id', $tenantId)->first();

        if (!$tenant) {
            // Eğer tenant_id geçersizse kullanıcıyı giriş ekranına yönlendir
            return redirect()->route('giris')->withErrors(['error' => 'Geçersiz tenant ID.']);
        }

        // Tenant bilgisini istekle paylaş
        $request->attributes->set('tenant', $tenant);
        return $next($request);
    }
}
