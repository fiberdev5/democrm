<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Kullanıcı giriş yapmış mı?
        if (!Auth::check()) {
            return redirect()->route('login')->with([
                'message' => 'Lütfen giriş yapınız.',
                'alert-type' => 'warning'
            ]);
        }

        $user = Auth::user();

        // Impersonation durumunda orijinal kullanıcıyı kontrol et
        if (session()->has('impersonator_id')) {
            $originalUser = User::find(session('impersonator_id'));
            if (!$originalUser || !$originalUser->isSuperAdmin()) {
                return redirect()->route('login')->with([
                    'message' => 'Super Admin yetkisi gereklidir.',
                    'alert-type' => 'error'
                ]);
            }
        } else {
            // Normal durum - mevcut kullanıcıyı kontrol et
            if (!$user->isSuperAdmin()) {
                abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
            }
        }

        return $next($request);
    }
}