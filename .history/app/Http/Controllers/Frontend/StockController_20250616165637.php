<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function AllStocks($tenant_id, Request $request) {
    if (!Auth::check()) {
        return redirect()->route('giris')->with('error', 'Lütfen giriş yapınız.');
    }
    $user = Auth::user();
    if ($tenant_id == null || $user->tenant->id != $tenant_id) {
        return redirect()->route('giris')->with([
            'message' => 'Bayilere erişiminiz yoktur.',
            'alert-type' => 'danger',
        ]);
    }
    $firma = Tenant::findOrFail($tenant_id);
    $dealerRole = Role::find(259); // bayi rolü ID'si
    $dealers = User::where('tenant_id', $tenant_id)
        ->whereHas('roles', function ($query) use ($dealerRole) {
            $query->where('id', $dealerRole->id);
        })
        ->get();

    return view('frontend.secure.dealers.all_dealers', compact('dealers', 'firma'));
}

}
