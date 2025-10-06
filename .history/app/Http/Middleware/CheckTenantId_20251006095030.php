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

    // ✅ BUNU EKLE - Asset isteklerini direkt geç
    if ($request->is('backend/assets/*') || 
        $request->is('frontend/*') ||
        $request->is('css/*') ||
        $request->is('js/*') ||
        $request->is('images/*') ||
        str_ends_with($request->path(), '.css') ||
        str_ends_with($request->path(), '.js') ||
        str_ends_with($request->path(), '.jpg') ||
        str_ends_with($request->path(), '.png') ||
        str_ends_with($request->path(), '.gif') ||
        str_ends_with($request->path(), '.svg') ||
        str_ends_with($request->path(), '.woff') ||
        str_ends_with($request->path(), '.woff2') ||
        str_ends_with($request->path(), '.ttf')) {
        return $next($request);
    }
        $user = Auth::user();
        $tenantId = $request->route('tenant_id');

        // SuperAdmin her tenant'a erişebilir
        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return $next($request);
        }

        // Tenant var mı kontrol et
        $tenant = DB::table('tenants')->where('id', $tenantId)->first();

        if (!$tenant) {
            return redirect()->route('giris')->withErrors(['error' => 'Geçersiz tenant ID.']);
        }

        // Kullanıcıya ait tenant mı kontrol et
        if ($user && $user->tenant_id != $tenantId) {
            abort(403, 'Bu firmaya erişim yetkiniz yok.');
        }

        // Tenant bilgisini paylaş
        $request->attributes->set('tenant', $tenant);

        return $next($request);
    }
}
