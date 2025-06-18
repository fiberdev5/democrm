<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\User;
use App\Models\StockShelf;
use App\Models\DeviceBrand;
use App\Models\DeviceType;
use App\Models\Stock;
use Yajra\DataTables\DataTables;



class StockController extends Controller
{


public function AllStocks($tenant_id, Request $request)
{
    if ($request->ajax()) {
        $query = Stock::with(['raf', 'marka', 'cihaz'])
            ->where('firma_id', $tenant_id);

        return DataTables::of($query)
            ->addColumn('adet', function ($stock) {
                return $stock->adet ?? '-';
            })
            ->addColumn('raf_adi', function ($stock) {
                return $stock->raf ? $stock->raf->raf_adi : '-';
            })
            ->addColumn('marka_cihaz', function ($stock) {
                $marka = $stock->marka ? $stock->marka->marka : '';
                $cihaz = $stock->cihaz ? $stock->cihaz->cihaz : '';
                return trim($marka . ' / ' . $cihaz, ' / ');
            })
            ->addColumn('action', function ($stock) use ($tenant_id) {
                $editButton = '<a href="javascript:void(0);" data-bs-id="'.$stock->id.'" class="btn btn-warning btn-sm editStock mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editStockModal" title="Düzenle"><i class="fas fa-edit"></i></a>';
                $deleteButton = '<a href="javascript:void(0);" class="btn btn-danger btn-sm mobilBtn deleteStock" data-id="'.$stock->id.'" title="Sil"><i class="fas fa-trash-alt"></i></a>';
                return $editButton . ' ' . $deleteButton;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    $firma = Tenant::findOrFail($tenant_id);
    $personeller = User::where('tenant_id', $tenant_id)->get();
    $rafListesi = StockShelf::where('firma_id', $tenant_id)->get();
    $markalar = DeviceBrand::all();
    $cihazlar = DeviceType::all();

    return view('frontend.secure.stocks.all_stocks', compact('firma', 'personeller', 'markalar', 'cihazlar', 'rafListesi'));
}









}
