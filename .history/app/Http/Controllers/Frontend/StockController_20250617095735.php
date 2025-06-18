<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\User;
use App\Models\StockShelf;
use App\Models\DeviceBrand;
use App\Models\DeviceType;


class StockController extends Controller
{
    public function AllStocks($tenant_id, Request $request) {
    
    $firma = Tenant::findOrFail($tenant_id);
    $personeller = User::where('tenant_id', $tenant_id)->get();
    $rafListesi = StockShelf::where('tenant_id', $tenant_id)->get();
    $markalar = DeviceBrand::all();
    $cihazlar = DeviceType::all();

    return view('frontend.secure.dealers.all_dealers', compact('firma', 'personeller', 'markalar', 'cihazlar'));
}

}
