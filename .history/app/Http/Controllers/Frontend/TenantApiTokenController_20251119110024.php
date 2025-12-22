<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\TenantApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class TenantApiTokenController extends Controller
{
    public function index(Request $request)
    {
        $tenant_id = auth()->user()->firma_id;
        
        $tokens = TenantApiToken::where('tenant_id', $tenant_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.api-tokens.index', compact('tokens'));
    }

    /**
     * Yeni token oluştur
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tenant_id = auth()->user()->firma_id;

        // Plain text token oluştur
        $plainTextToken = Str::random(60);
        
        // Token'ı hashle
        $hashedToken = TenantApiToken::hashToken($plainTextToken);

        // Veritabanına kaydet
        $apiToken = TenantApiToken::create([
            'tenant_id' => $tenant_id,
            'name' => $request->name,
            'token' => $hashedToken,
            'abilities' => ['*'], // Tüm yetkiler
            'is_active' => true,
        ]);

        $notification = array(
            'message' => 'API Token başarıyla oluşturuldu. Token\'ı güvenli bir yerde saklayın, tekrar gösterilmeyecektir.',
            'alert-type' => 'success'
        );

        // Plain text token'ı bir kez göster
        return redirect()->back()
            ->with($notification)
            ->with('new_token', $plainTextToken);
    }

    /**
     * Token sil
     */
    public function destroy($id)
    {
        $tenant_id = auth()->user()->firma_id;

        $token = TenantApiToken::where('id', $id)
            ->where('tenant_id', $tenant_id)
            ->firstOrFail();

        $token->delete();

        $notification = array(
            'message' => 'API Token silindi',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    /**
     * Token aktif/pasif yap
     */
    public function toggle($id)
    {
        $tenant_id = auth()->user()->firma_id;

        $token = TenantApiToken::where('id', $id)
            ->where('tenant_id', $tenant_id)
            ->firstOrFail();

        $token->is_active = !$token->is_active;
        $token->save();

        $notification = array(
            'message' => 'API Token durumu güncellendi',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }
}
