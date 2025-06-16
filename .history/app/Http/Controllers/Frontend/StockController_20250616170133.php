<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function AllStocks($tenant_id, Request $request) {
    
    $firma = Tenant::findOrFail($tenant_id);
    $personeller = User::where('tenant_id', $tenant_id)->get();
    $rafListesi = Raf::where('tenant_id', $tenant_id)->get();
    $markalar = Marka::all();
    $cihazlar = Cihaz::all();

    return view('frontend.secure.stocks.all_stocks', compact('firma', 'personeller', 'rafListesi', 'markalar', 'cihazlar'));
}

}
