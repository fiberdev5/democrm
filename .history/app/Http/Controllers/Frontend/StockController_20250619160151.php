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
use App\Models\StockAction;
use App\Models\PersonelStock;
use App\Models\ServisStockk;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;



class StockController extends Controller
{


     public function AllStocks($tenant_id, Request $request){
                    if ($request->ajax()) {
                        $query = Stock::with(['raf', 'marka', 'cihaz','sonHareket'])
                            ->where('firma_id', $tenant_id);


                    //Arama filtresi
                    if ($request->has('search') && $request->search['value']) {
                        $search = $request->search['value'];
                        $query->where(function ($q) use ($search) {
                            $q->where('urunAdi', 'like', "%{$search}%")
                            ->orWhere('urunKodu', 'like', "%{$search}%")
                            ->orWhereHas('marka', function ($mq) use ($search) {
                                $mq->where('marka', 'like', "%{$search}%");
                            })
                            ->orWhereHas('cihaz', function ($cq) use ($search) {
                                $cq->where('cihaz', 'like', "%{$search}%");
                            })
                            ->orWhereHas('raf', function ($rq) use ($search) {
                                $rq->where('raf_adi', 'like', "%{$search}%");
                            });
                        });
                    }    
                    // TOPLAM HESAPLAMALARI
                    $stocks = $query->get();
                    $toplamAdet = 0;
                    $toplamFiyat = 0;

                    foreach ($stocks as $stock) {
                        if ($stock->sonHareket) {
                            $adet = $stock->sonHareket->adet ?? 0;
                            $fiyatBirim = $stock->fiyat ?? 0;

                            $toplamAdet += $adet;
                            $toplamFiyat += $adet * $fiyatBirim;
                        }
                    }

                    return DataTables::of($query)  //adet bilgisi 
                        ->addColumn('adet', function ($stock) {
                            return $stock->sonHareket ? $stock->sonHareket->adet : '-';
                        })
                        // Birim fiyat bilgisi
                        ->addColumn('fiyatBirim', function ($stock) {
                            return $stock->fiyatBirim ? number_format($stock->fiyatBirim, 2) . ' ₺' : '-';
                        })
                        // Toplam tutar (adet * birim fiyat)
                        ->addColumn('toplamTutar', function ($stock) {
                            if ($stock->sonHareket && $stock->fiyatBirim) {
                                $tutar = $stock->sonHareket->adet * $stock->fiyatBirim;
                                return number_format($tutar, 2) . ' ₺';
                            }
                            return '-';
                        })
                        ->addColumn('raf_adi', function ($stock) {
                            return $stock->raf ? $stock->raf->raf_adi : '-';
                        })
                        ->addColumn('marka_cihaz', function ($stock) {
                            $marka = $stock->marka ? $stock->marka->marka : '';
                            $cihaz = $stock->cihaz ? $stock->cihaz->cihaz : '';
                            return trim($marka . ' / ' . $cihaz, ' / ');
                        })
                        ->editColumn('created_at', function($row) {
                            return $row->created_at->format('d.m.Y H:i'); //tarih formatı
                        })
->addColumn('action', function($row) use ($tenant_id) {
    $deleteUrl = route('delete.stock', [$tenant_id, $row->id]);
    $editButton = '<a href="javascript:void(0);" data-bs-id="'.$row->id.'" class="btn btn-warning btn-sm editStock mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editStockModal" title="Düzenle"><i class="fas fa-edit"></i></a>';
    $deleteButton = '<a href="'.$deleteUrl.'" class="btn btn-danger btn-sm mobilBtn" id="delete" title="Sil"><i class="fas fa-trash-alt"></i></a>';
    return $editButton . ' ' . $deleteButton;
})

              
                    ->rawColumns(['action'])
                    // Toplam değerleri DataTable'a gönder
                    ->with([
                        'toplamAdet' => number_format($toplamAdet),
                        'toplamFiyat' => number_format($toplamFiyat, 2) . ' ₺',
                        'toplamAdetRaw' => $toplamAdet,
                        'toplamFiyatRaw' => $toplamFiyat
                    ])
                        ->make(true);

                }
                $firma = Tenant::findOrFail($tenant_id);
                $personeller = User::where('tenant_id', $tenant_id)->get();
                $rafListesi = StockShelf::where('firma_id', $tenant_id)->get();
                $markalar = DeviceBrand::where('firma_id', $tenant_id)->get();
                $cihazlar = DeviceType::where('firma_id', $tenant_id)->get();

                return view('frontend.secure.stocks.all_stocks', compact('firma', 'personeller', 'markalar', 'cihazlar', 'rafListesi'));
    }


    public function AddStock($tenant_id){

            $firma = Tenant::findOrFail($tenant_id);
            $rafListesi = StockShelf::where('firma_id', $tenant_id)->get();
            $markalar = DeviceBrand::where('firma_id', $tenant_id)->get();
            $cihazlar = DeviceType::where('firma_id', $tenant_id)->get();

            return view('frontend.secure.stocks.add_stock', compact('firma','rafListesi', 'markalar', 'cihazlar', 'tenant_id'));
        }

    public function StoreStock(Request $request, $tenant_id){
            $firma = Tenant::findOrFail($tenant_id);
            if (!$firma) {
            $notification = [
                'message' => 'Firma bulunamadı.',
                'alert-type' => 'danger',
            ];
            return redirect()->route('giris')->with($notification);
            }

            $personel_id = Auth::user()->user_id;

            $stock = new Stock();
            $stock->firma_id  = $firma->id;
            $stock->pid         = $personel_id; 
            $stock->urunAdi   = $request->urunAdi;
            $stock->urunKodu  = $request->urunKodu;
            $stock->aciklama  = $request->aciklama;
            $stock->urunDepo = $request->raf_id;
            $stock->fiyat      = $request->fiyat;
            $stock->fiyatBirim = $request->fiyatBirim;
            $stock->stok_marka  = $request->marka_id;   // ilişkili marka tablosu id'si
            $stock->stok_cihaz  = $request->cihaz_id;   // ilişkili cihaz tablosu id'si
            $stock->save();

            // İlk stok hareketini kaydet
            $action = new \App\Models\StockAction(); 
            $action->firma_id   = $firma->id;
            $action->pid   = $personel_id; 
            $action->stokId     = $stock->id;
            $action->adet       = $request->adet;
            $action->fiyat      = $request->fiyat;
            $action->fiyatBirim = $request->fiyatBirim;
            $action->islem      = 1; // 1 = giriş
            $action->save();

            $notification = [
                'message' => 'Stok başarıyla kaydedildi.',
                'alert-type' => 'success'
            ];

            return redirect()->route('stocks', $tenant_id)->with($notification);
        }

        public function EditStock($tenant_id, $id) {
            $firma = Tenant::findOrFail($tenant_id);
            $stock = Stock::with(['raf', 'marka', 'cihaz', 'sonHareket'])->findOrFail($id);

            $rafListesi = StockShelf::where('firma_id', $tenant_id)->get();
            $markalar = DeviceBrand::where('firma_id', $tenant_id)->get();
            $cihazlar = DeviceType::where('firma_id', $tenant_id)->get();
            $html = view('frontend.secure.stocks.edit_stock', compact('firma', 'stock', 'rafListesi', 'markalar', 'cihazlar'))->render();

            return response()->json([
                'html' => $html,
                'urunAdi' => $stock->urunAdi,
            ]);
        }

        public function UpdateStock(Request $request, $tenant_id, $id){
            $firma = Tenant::findOrFail($tenant_id);
            $personel_id = Auth::user()->user_id;

            $stock = Stock::findOrFail($id);

            $stock->urunAdi   = $request->urunAdi;
            $stock->urunKodu    = $request->urunKodu;
            $stock->urunDepo    = $request->raf_id;
            $stock->aciklama  = $request->aciklama;
            $stock->fiyat       = $request->fiyat;
            $stock->stok_marka  = $request->marka_id;
            $stock->stok_cihaz  = $request->cihaz_id;
            $stock->save();

            // Son hareket güncelle
            $hareket = StockAction::where('stokId', $stock->id)->latest()->first();
            if ($hareket) {
                $hareket->adet       = $request->adet;
                $hareket->fiyat      = $request->fiyat;
                $hareket->save();
            }

            $notification = [
                'message' => 'Ürün bilgileri başarıyla güncellendi.',
                'alert-type' => 'success'
            ];

            return redirect()->back()->with($notification);
        }

        public function DeleteStock($tenant_id, $id) {
            // İlgili stok kaydını tenant_id ve id'ye göre al
            $stock = Stock::where('firma_id', $tenant_id)->where('id', $id)->first();

            if (is_null($stock)) {
                $notification = [
                    'message' => 'Silmek istediğiniz stok bulunamadı.',
                    'alert-type' => 'danger'
                ];
                return redirect()->back()->with($notification);
            }

            try {
                $stock->delete();

                $notification = [
                    'message' => 'Stok başarıyla silindi.',
                    'alert-type' => 'success'
                ];
            } catch (\Exception $e) {
                // Silme sırasında hata olursa
                $notification = [
                    'message' => 'Silme işlemi sırasında bir hata oluştu.',
                    'alert-type' => 'danger'
                ];
            }

            return redirect()->back()->with($notification);
        }

/////////////////////////////////////////////STOCK ACTION////////////////////////////////////////////////////////////////////////////////////////


public function StokActions($tenant_id, $stock_id)
{
    $stock = Stock::with(['marka', 'cihaz', 'raf'])
        ->where('firma_id', $tenant_id)
        ->findOrFail($stock_id);

    // Stok hareketlerini join ile getir
    $stokHareketleri = StockAction::select('stock_actions.*', 'stock_suppliers.tedarikci', 'tb_user.name')
        ->leftJoin('stock_suppliers', 'stock_suppliers.id', '=', 'stock_actions.tedarikci')
        ->leftJoin('tb_user', 'tb_user.user_id', '=', 'stock_actions.pid')
        ->where('stock_actions.stokId', $stock_id)
        ->orderBy('stock_actions.id', 'desc')
        ->get();

    return view('frontend.secure.stocks.action_stock', compact('stock', 'stokHareketleri'));
}

public function StoreStockAction(Request $request, $tenant_id)
{
    $firma = Tenant::findOrFail($tenant_id);

    $validated = $request->validate([
        'stok_id'    => 'required|integer',
        'islem'      => 'required|in:1,2,3',
        'adet'       => 'required|integer|min:1',
        'fiyat'      => 'required|numeric',
        'tedarikci'  => 'nullable|string|max:255',
    ]);

    $personel_id = auth()->id();

    // İlk olarak stok hareketini kaydet
    $stockAction = new StockAction();
    $stockAction->firma_id   = $firma->id;
    $stockAction->pid        = $personel_id;
    $stockAction->stokId     = $request->stok_id;
    $stockAction->islem      = $request->islem;
    $stockAction->adet       = $request->adet;
    $stockAction->fiyat      = $request->fiyat;
    $stockAction->fiyatBirim = 'TL'; // varsayılan
    $stockAction->tedarikci  = $request->tedarikci;

    // Önce kaydet, sonra ilişkilendirilecek ID varsa güncelle
    $stockAction->save();

    // İşleme göre ilgili tabloya kayıt
    if ($request->islem == 2) {
        // Servis kullanım
        $servisStok = \App\Models\ServisStok::create([
            'stok_id'   => $request->stok_id,
            'firma_id'  => $firma->id,
            'kid'       => $personel_id,
            'adet'      => $request->adet,
            'plan_id'   => null, // gerekiyorsa ekle
            'created_at' => now()
        ]);

        // StockAction'a servis id eklemek istersen:
        $stockAction->servisid = $servisStok->id;
        $stockAction->save();

    } elseif ($request->islem == 3) {
        // Personel'e gönder
        $personelStok = \App\Models\PersonelStock::create([
            'stok_id'   => $request->stok_id,
            'firma_id'  => $firma->id,
            'pid'       => $personel_id,
            'adet'      => $request->adet,
            'created_at' => now()
        ]);

        $stockAction->perStokId = $personelStok->id;
        $stockAction->personel  = $personel_id; // log tutmak için
        $stockAction->save();
    }

    return redirect()->back()->with([
        'message'     => 'Stok hareketi başarıyla eklendi.',
        'alert-type'  => 'success',
    ]);
}
















}
