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


   public function AllStocks($tenant_id, Request $request) {
    $firma = Tenant::findOrFail($tenant_id);
    $personeller = User::where('tenant_id', $tenant_id)->get();
    $rafListesi = StockShelf::where('firma_id', $tenant_id)->get();
    $markalar = DeviceBrand::all();
    $cihazlar = DeviceType::all();

    return view('frontend.secure.stocks.all_stocks', compact('firma', 'personeller', 'markalar', 'cihazlar', 'rafListesi'));
   }


   


   public function GetStocksAjax($tenant_id, Request $request)
{
    if ($request->ajax()) {
        $data = Stock::with(['personel', 'raf', 'marka', 'cihaz'])
            ->where('firma_id', $tenant_id);

        // Filtreler
        if ($request->filled('personel')) {
            $data->where('personel_id', $request->personel);
        }

        if ($request->filled('raf')) {
            $data->where('raf_id', $request->raf);
        }

        if ($request->filled('marka')) {
            $data->where('marka_id', $request->marka);
        }

        if ($request->filled('cihaz')) {
            $data->where('cihaz_id', $request->cihaz);
        }

        if ($request->filled('durum')) {
            $data->where('durum', $request->durum); // örneğin aktif/pasif ürün durumu
        }

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('tarih', function ($row) {
                return $row->created_at->format('d.m.Y');
            })
            ->addColumn('urun_adi', function ($row) {
                return $row->urun_Adi;
            })
            ->addColumn('urun_kodu', function ($row) {
                return $row->urun_Kodu;
            })
            ->addColumn('fiyat', function ($row) {
                return number_format($row->fiyat, 2) . ' ₺';
            })
            // ->addColumn('adet', function ($row) {
            //     return $row->adet;
            // })
            ->addColumn('raf', function ($row) {
                return $row->raf->raf_adi ?? '-';
            })
            ->addColumn('marka_cihaz', function ($row) {
                $marka = $row->stok_marka ?? '-';
                $cihaz = $row->stok_cihaz ?? '-';
                return "$marka / $cihaz";
            })
            ->addColumn('edit', function ($row) use ($tenant_id) {
                return '<a href="javascript:void(0);" data-bs-id="'.$row->id.'" class="btn btn-warning btn-sm stokEkleBtn" data-bs-toggle="modal" data-bs-target="#stokModal"><i class="fas fa-edit"></i></a>';
            })
            ->addColumn('delete', function ($row) use ($tenant_id) {
                $url = route('stok.sil', [$tenant_id, $row->id]);
                return '<a href="'.$url.'" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>';
            })
            ->rawColumns(['edit', 'delete'])
            ->make(true);
    }
}






}
