<?php

namespace App\Http\Middleware;

use App\Models\TenantApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateTenantApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'API token gereklidir'
            ], 401);
        }

        // Token'ı hashle ve veritabanında ara
        $hashedToken = TenantApiToken::hashToken($token);
        
        $apiToken = TenantApiToken::where('token', $hashedToken)
            ->where('is_active', true)
            ->with('tenant')
            ->first();

        if (!$apiToken) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz veya pasif API token'
            ], 401);
        }

        // Tenant bilgisini request'e ekle
        $request->merge([
            'tenant_id' => $apiToken->tenant_id,
            'tenant' => $apiToken->tenant,
            'api_token_id' => $apiToken->id
        ]);

        // Son kullanım tarihini güncelle
        $apiToken->updateLastUsed();

        return $next($request);
    }
}
