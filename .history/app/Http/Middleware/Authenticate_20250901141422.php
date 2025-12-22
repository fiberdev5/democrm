<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Authenticate
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // Eğer impersonation aktifse ve user yoksa, impersonated user'ı yükle
        if (session()->has('impersonated_user_id') && !Auth::check()) {
            $impersonatedUser = User::find(session('impersonated_user_id'));
            if ($impersonatedUser) {
                Auth::login($impersonatedUser);
            }
        }

        // Normal auth kontrolü
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->guest(route('giris'));
        }

        return $next($request);
    }
}