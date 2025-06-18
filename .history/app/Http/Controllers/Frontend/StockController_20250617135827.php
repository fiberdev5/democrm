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

        // Filtreler varsa uygula
        if ($request->filled('durum')) {
            $query->where('durum', $request->durum);
        }
        if ($request->filled('kategori')) {
            $query->where('urunKategori', $request->kategori);
        }
        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $query->where(function($q) use ($search) {
                $q->where('urunAdi', 'like', "%{$search}%")
                  ->orWhere('urunKodu', 'like', "%{$search}%");
            });
        }

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
            ->addColumn('action', function ($stock) {
                return '
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary editStock" data-id="'.$stock->id.'">Düzenle</a> 
                    <a href="javascript:void(0)" class="btn btn-sm btn-danger deleteStock" data-id="'.$stock->id.'">Sil</a>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    // Ajax değilse view render et
    $firma = Tenant::findOrFail($tenant_id);
    $personeller = User::where('tenant_id', $tenant_id)->get();
    $rafListesi = StockShelf::where('firma_id', $tenant_id)->get();
    $markalar = DeviceBrand::all();
    $cihazlar = DeviceType::all();

    return view('frontend.secure.stocks.all_stocks', compact('firma', 'personeller', 'markalar', 'cihazlar', 'rafListesi'));
}







}
