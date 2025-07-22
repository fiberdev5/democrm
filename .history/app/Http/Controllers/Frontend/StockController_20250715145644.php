<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Tenant;
use App\Models\User;
use App\Models\StockShelf;
use App\Models\DeviceBrand;
use App\Models\DeviceType;
use App\Models\Stock;
use App\Models\StockAction;
use App\Models\PersonelStock;
use App\Models\ServisStock;
use App\Models\stock_photos;
use App\Models\StockCategory;
use Illuminate\Validation\Rule;
use Image;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Milon\Barcode\Facades\DNS1D;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
public function AllStocks($tenant_id, Request $request)
{
    if (!Auth::check()) {
        return redirect()->route('giris')->with('error', 'Lütfen giriş yapınız.');
    }
    $user = Auth::user();
    if ($tenant_id == null || $user->tenant->id != $tenant_id) {
        return redirect()->route('giris')->with([
            'message' => 'Stoklara erişiminiz yoktur.',
            'alert-type' => 'danger',
        ]);
    }

    $firma = Tenant::findOrFail($tenant_id);

    if ($request->ajax()) {
    $query = Stock::select('stocks.*')
        ->join('stock_categories as kategori', 'kategori.id', '=', 'stocks.urunKategori')
        ->where('stocks.firma_id', $tenant_id)
        ->where('kategori.id', '!=', 3);;


        // Filtreler (personel dahil)
        if ($request->filled('marka')) {
            $query->where('stok_marka', $request->marka);
        }
        if ($request->filled('raf')) {
            $query->where('urunDepo', $request->raf);
        }
        if ($request->filled('cihaz')) {
            $query->where('stok_cihaz', $request->cihaz);
        }
        if ($request->filled('personel')) {
            // Burada personel filtresi stokta doğrudan var ise
            $query->where('pid', $request->personel);
        }

        // Sıralama DataTables yapısına göre
        if ($request->has('order')) {
            $order = $request->get('order')[0];
            $columns = $request->get('columns');
            $orderColumn = $columns[$order['column']]['data'];
            $orderDir = $order['dir'];
            $query->orderBy($orderColumn, $orderDir);
        } else {
            $query->orderBy('id', 'desc');
        }

        // Toplam hesaplamalar (filtreli tüm stoklar için)
        $stocksForTotal = $query->get();

        $toplamAdet = 0;
        $toplamFiyat = 0;

        foreach ($stocksForTotal as $stock) {
            $toplamGiris = \App\Models\StockAction::where('stokId', $stock->id)->where('islem', 1)->sum('adet'); //alış
            $toplamCikis = \App\Models\StockAction::where('stokId', $stock->id)->where('islem', 3)->sum('adet');  //personele gonder
            $kalanAdet = $toplamGiris - $toplamCikis;

            $toplamAdet += max($kalanAdet, 0);
            $toplamFiyat += max($stock->fiyat,0);
        }

        // DataTables
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('urunKodu', function($row) {
                return '<a href="javascript:void(0);" class="t-link editStock" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editStockModal">' . e($row->urunKodu) . '</a>';
            })
            ->addColumn('urunAdi', function($row) {
                return '<a href="javascript:void(0);" class="t-link editStock" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editStockModal">' . e($row->urunAdi) . '</a>';
            })
            ->addColumn('adet', function($row) {
                $toplamGiris = \App\Models\StockAction::where('stokId', $row->id)->where('islem', 1)->sum('adet');
                $toplamCikis = \App\Models\StockAction::where('stokId', $row->id)->where('islem', 3)->sum('adet');
                $kalanAdet = $toplamGiris - $toplamCikis;
                return '<a href="javascript:void(0);" class="t-link editStock" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editStockModal">' . $kalanAdet . '</a>';
            })
            ->addColumn('toplamTutar', function($row) {
                // Sadece ALIŞ (islem = 1) adetlerini topla
                $toplamGiris = \App\Models\StockAction::where('stokId', $row->id)
                                    ->where('islem', 1)
                                    ->sum('adet');

                // Fiyat sadece giriş adediyle hesaplanır
                $tutar = $row->fiyat ?? 0;

                return '<a href="javascript:void(0);" class="t-link editStock" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editStockModal">'
                        . number_format($tutar, 2, ',', '.') . ' ₺</a>';
            })
            ->addColumn('raf_adi', function($row) {
                $raf = $row->raf ? e($row->raf->raf_adi) : '-';
                return '<a href="javascript:void(0);" class="t-link editStock" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editStockModal">' . $raf . '</a>';
            })
            ->addColumn('marka_cihaz', function($row) {
                $marka = $row->marka ? e($row->marka->marka) : '';
                $cihaz = $row->cihaz ? e($row->cihaz->cihaz) : '';
                $text = trim($marka . ' / ' . $cihaz, ' / ');
                return '<a href="javascript:void(0);" class="t-link editStock" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editStockModal">' . $text . '</a>';
            })

            ->editColumn('created_at', function($row) {
                $date = $row->created_at ? $row->created_at->format('d.m.Y H:i') : '';
                return '<a href="javascript:void(0);" class="t-link editStock" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editStockModal">' . $date . '</a>';
            })

            ->addColumn('action', function($row) use ($tenant_id) {
                $deleteUrl = route('delete.stock', [$tenant_id, $row->id]);
                $editBtn = '<a href="javascript:void(0);" class="btn btn-warning btn-sm editStock" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editStockModal" title="Düzenle"><i class="fas fa-edit"></i></a>';
                $delBtn = '<a href="'.$deleteUrl.'" class="btn btn-danger btn-sm" title="Sil" onclick="return confirm(\'Silmek istediğinize emin misiniz?\');"><i class="fas fa-trash-alt"></i></a>';
                return $editBtn . ' ' . $delBtn;
            })
            ->filter(function ($query) use ($request) {
                if ($search = $request->get('search')['value'] ?? null) {
                    $query->where(function ($q) use ($search) {
                        $q->where('urunAdi', 'like', "%{$search}%")
                          ->orWhere('urunKodu', 'like', "%{$search}%");
                    });
                }

            })
            ->rawColumns(['urunKodu', 'urunAdi', 'adet', 'toplamTutar', 'raf_adi', 'marka_cihaz', 'created_at', 'action'])



            ->with([
                'toplamAdet' => number_format($toplamAdet),
                'toplamFiyat' => number_format($toplamFiyat, 2, ',', '.') . ' ₺',
                'toplamAdetRaw' => $toplamAdet,
                'toplamFiyatRaw' => $toplamFiyat,
            ])
            ->make(true);
    }


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

            $kategoriler = StockCategory::where('firma_id', $tenant_id)
                ->where('id', '!=', 3)  // konsinye kategori hariç
                ->get();



            return view('frontend.secure.stocks.add_stock', compact('firma','rafListesi', 'markalar', 'cihazlar', 'kategoriler','tenant_id'));
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

    // Ürün kodu kontrolü
    $existingStock = Stock::where('firma_id', $tenant_id)
                          ->where('urunKodu', $request->urunKodu)
                          ->first();

    if ($existingStock) {
        $notification = [
            'message' => 'Bu ürün kodu zaten mevcut. Lütfen farklı bir ürün kodu girin.',
            'alert-type' => 'warning',
        ];
        return redirect()->back()->withInput()->with($notification);
    }

     $request->validate([
        'urunKodu' => ['required', 'digits:13', 'unique:stocks,urunKodu,NULL,id,firma_id,'.$tenant_id],
        // 'digits:13' => tam 13 rakam olmalı,
        // unique kontrolü firma_id bazlı, yani aynı firmada tekrar olmasın
    ],[
        'urunKodu.required' => 'Ürün kodu zorunludur.',
        'urunKodu.digits' => 'Ürün kodu tam 13 haneli olmalıdır.',
        'urunKodu.unique' => 'Bu ürün kodu zaten mevcut. Lütfen farklı bir ürün kodu girin.',
    ]);
    // Ürün adı kontrolü
    $existingName = Stock::where('firma_id', $tenant_id)
                        ->where('urunAdi', $request->urunAdi)
                        ->first();

    if ($existingName) {
        $notification = [
            'message' => 'Bu ürün adı zaten mevcut. Lütfen farklı bir ürün adı girin.',
            'alert-type' => 'warning',
        ];
        return redirect()->back()->withInput()->with($notification);
    }
    $request->validate([
    'urunKodu' => ['required', 'digits:13', 'unique:stocks,urunKodu,NULL,id,firma_id,'.$tenant_id],
    'urunAdi' => ['required', 'max:255'],
    ],[
        'urunKodu.required' => 'Ürün kodu zorunludur.',
        'urunKodu.digits' => 'Ürün kodu tam 13 haneli olmalıdır.',
        'urunKodu.unique' => 'Bu ürün kodu zaten mevcut. Lütfen farklı bir ürün kodu girin.',
        'urunAdi.required' => 'Ürün adı zorunludur.',
    ]);

            $personel_id = Auth::user()->user_id;

            $stock = new Stock();
            $stock->firma_id  = $firma->id;
            $stock->pid         = $personel_id; 
            $stock->urunAdi   = $request->urunAdi;
            $stock->urunKodu  = $request->urunKodu;
            $stock->urunKategori = $request->urunKategori;
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
            $stock->fiyatBirim = $request->fiyatBirim;
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

            $kategoriler = StockCategory::where('firma_id', $tenant_id)->get();
            $html = view('frontend.secure.stocks.edit_stock', compact('firma', 'stock', 'rafListesi', 'markalar','kategoriler', 'cihazlar'))->render();


            return response()->json([
                'html' => $html,
                'urunAdi' => $stock->urunAdi,
            ]);
        }

public function UpdateStock(Request $request, $tenant_id, $id){
    $firma = Tenant::findOrFail($tenant_id);
    $personel_id = Auth::user()->user_id;
    $stock = Stock::findOrFail($id);

    // Ürün kodu kontrolü
    $existingStock = Stock::where('firma_id', $tenant_id)
                        ->where('urunKodu', $request->urunKodu)
                        ->where('id', '!=', $id)
                        ->first();

    if ($existingStock) {
        $notification = [
            'message' => 'Bu ürün kodu zaten mevcut. Lütfen farklı bir ürün kodu girin.',
            'alert-type' => 'warning',
        ];
        return redirect()->back()->withInput()->with($notification);
    }

    $request->validate([
        'urunKodu' => [
            'required',
            'digits:13',
            Rule::unique('stocks')->ignore($id)->where('firma_id', $tenant_id),
        ],
        // diğer validasyonlar...
    ],[
        'urunKodu.required' => 'Ürün kodu zorunludur.',
        'urunKodu.digits' => 'Ürün kodu tam 13 haneli olmalıdır.',
        'urunKodu.unique' => 'Bu ürün kodu zaten mevcut. Lütfen farklı bir ürün kodu girin.',
    ]);

    // Ürün adı benzersiz mi?
    $existingName = Stock::where('firma_id', $tenant_id)
        ->where('urunAdi', $request->urunAdi)
        ->where('id', '!=', $id)
        ->first();

    if ($existingName) {
        $notification = [
            'message' => 'Bu ürün adı zaten mevcut. Lütfen farklı bir ürün adı girin.',
            'alert-type' => 'warning',
        ];
        return redirect()->back()->withInput()->with($notification);
    }

    $stock->urunAdi   = $request->urunAdi;
    $stock->urunKodu    = $request->urunKodu;
    $stock->urunKategori = $request->urunKategori;
    $stock->urunDepo    = $request->raf_id;
    $stock->aciklama  = $request->aciklama;
    $stock->fiyat       = $request->fiyat;
    $stock->fiyatBirim = $request->fiyatBirim;
    $stock->stok_marka  = $request->marka_id;
    $stock->stok_cihaz  = $request->cihaz_id;
    $stock->save();

    $notification = [
        'message' => 'Ürün bilgileri başarıyla güncellendi.',
        'alert-type' => 'success'
    ];

    return redirect()->back()->with($notification);
}


public function DeleteStock($tenant_id, $id) {
    $stock = Stock::where('firma_id', $tenant_id)->where('id', $id)->first();

    if (is_null($stock)) {
        $notification = [
            'message' => 'Silmek istediğiniz stok bulunamadı.',
            'alert-type' => 'danger'
        ];
        return redirect()->back()->with($notification);
    }

    // Stok hareketleri var mı kontrol et
    $stokHareketSayisi = StockAction::where('stokId', $id)->count();

    if ($stokHareketSayisi > 0) {
        $notification = [
            'message' => 'Ürün içerisinde stok hareket kaydı bulunurken  silme işlemi gerçekleştirilemez.',
            'alert-type' => 'warning'
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

     $firma = Tenant::findOrFail($tenant_id);

    // Stok hareketlerini join ile getir
   $stokHareketleri = StockAction::with(['musteri'])
            ->select(
                'stock_actions.*',
                'stock_suppliers.tedarikci',
                'user_recipient.name as recipient_name', // islem=3 (Personel'e Gönder) için alıcı personel adı
                'user_performer.name as performer_name'  // islem=2 (Serviste Kullanım) için işlemi yapan personel adı
            )
            // Tedarikçi tablosu ile birleştirme
            ->leftJoin('stock_suppliers', 'stock_suppliers.id', '=', 'stock_actions.tedarikci')
            // 'pid' sütunu üzerinden kullanıcı tablosu ile birleştirme (alıcı personel için)
            ->leftJoin('tb_user as user_recipient', 'user_recipient.user_id', '=', 'stock_actions.pid')
            // 'kid' sütunu üzerinden kullanıcı tablosu ile birleştirme (işlemi yapan personel için)
            ->leftJoin('tb_user as user_performer', 'user_performer.user_id', '=', 'stock_actions.kid')
            ->where('stock_actions.stokId', $stock_id)
            ->orderBy('stock_actions.id', 'desc')
            ->get();

        return view('frontend.secure.stocks.action_stock', compact('stock', 'stokHareketleri','firma'));
    }

public function StoreStockAction(Request $request, $tenant_id)
{
    $firma = Tenant::findOrFail($tenant_id);

    // Temel doğrulama kuralları
    $rules = [
        'stok_id'    => 'required|integer',
        'islem'      => 'required|in:1,2,3',
        'adet'       => 'required|integer|min:1',
        'fiyat'      => $request->islem == 1 ? 'required' : 'nullable',
        'fiyatBirim' => 'nullable|numeric',
        'tedarikci'  => 'nullable|string|max:255',
    ];

    // Custom hata mesajları
    $messages = [
        'servisid.required' => 'Serviste kullanım işlemi için servis ID alanı zorunludur.',
        'personel.required' => 'Personele gönderme işlemi için personel alanı zorunludur.',
        'servisid.integer'  => 'Servis ID bir sayı olmalıdır.',
        'personel.integer'  => 'Personel ID bir sayı olmalıdır.',
    ];

    // Doğrulayıcıyı oluştur
    $validator = Validator::make($request->all(), $rules, $messages);

    // islem 2 ise servisid zorunlu
    $validator->sometimes('servisid', 'required|integer', function ($input) {
        return $input->islem == 2;
    });

    // islem 2 için personel alanı zorunlu (stok düşürülecek personel)
    $validator->sometimes('personel', 'required|integer', function ($input) {
        return $input->islem == 2 || $input->islem == 3;
    });

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    $personel_id = Auth::user()->user_id;
    $stokId = $request->stok_id;

    // Fiyatı temizle (nokta ve virgül fix)
    $fiyat = null;
    if ($request->islem == 1 && $request->filled('fiyat')) {
        $fiyat = floatval(str_replace(['.', ','], ['', '.'], $request->fiyat));
    }

    // --- Stok Kontrolleri ---

    if ($request->islem == 2) {
        // Serviste kullanım: Personel stoğundan kontrol et
        $personelStok = PersonelStock::where('firma_id', $firma->id)
            ->where('pid', $request->personel) // stok düşülecek personel
            ->where('stokid', $stokId)
            ->first();

        $kalanStok = $personelStok ? $personelStok->adet : 0;

        if ($request->adet > $kalanStok) {
            return redirect()->back()->with([
                'message' => "Yetersiz personel stoğu! Mevcut: {$kalanStok} adet.",
                'alert-type' => 'error',
            ]);
        }
    }

    if ($request->islem == 3) {
        // Personele gönderme: Genel stoktan kontrol et
        $mevcutStok = StockAction::where('stokId', $stokId)
            ->where('firma_id', $firma->id)
            ->selectRaw("
                SUM(CASE WHEN islem = 1 THEN adet ELSE 0 END) as giren,
                SUM(CASE WHEN islem = 2 THEN adet ELSE 0 END) as serviste_kullanim,
                SUM(CASE WHEN islem = 3 THEN adet ELSE 0 END) as personele_giden
            ")
            ->first();

        $kalanStok = ($mevcutStok->giren ?? 0) - ($mevcutStok->serviste_kullanim ?? 0) - ($mevcutStok->personele_giden ?? 0);

        if ($request->adet > $kalanStok) {
            return redirect()->back()->with([
                'message' => "Yetersiz genel stok! Mevcut: {$kalanStok} adet.",
                'alert-type' => 'error',
            ]);
        }
    }

    // --- Stok Hareketi Kaydı ---

    $stockAction = new StockAction();
    $stockAction->firma_id   = $firma->id;
    $stockAction->stokId     = $stokId;
    $stockAction->islem      = $request->islem;
    $stockAction->adet       = $request->adet;
    $stockAction->fiyat      = $request->islem == 1 ? $fiyat : null;
    $stockAction->fiyatBirim = $request->islem == 1 ? $request->fiyatBirim : null;
    $stockAction->tedarikci  = $request->tedarikci;

    if ($request->islem == 1) {
        // Alış işlemi
        $stockAction->pid = $personel_id; // işlemi yapan kişi
        $stockAction->kid = $personel_id; // stoğa ekleyen personel
        $stockAction->servisid = null;
    } elseif ($request->islem == 2) {
        // Serviste kullanım - personel stoğundan düşme
        $stockAction->pid = $personel_id; // işlemi yapan kişi
        $stockAction->kid = $request->personel; // stoğu düşülecek personel
        $stockAction->servisid = $request->servisid; // servis id zorunlu
        $stockAction->perStokId = $personelStok ? $personelStok->id : null;
    } elseif ($request->islem == 3) {
        // Personele gönderme - genel stoktan düşme, personel stokuna ekleme
        $stockAction->pid = $request->personel; // stoğu alan personel
        $stockAction->kid = $personel_id;       // işlemi yapan kişi
        $stockAction->servisid = null;
    }

    $stockAction->save();

    // --- Stok Güncellemeleri ---
    if ($request->islem == 1) {
        // Alış işlemi: Genel stok fiyatını güncelle
        $stock = \App\Models\Stock::find($stokId);
        if ($stock) {
            $stock->fiyat += $fiyat;
            $stock->save();
        }
    }

    if ($request->islem == 2) {
        // Serviste kullanım: Personel stoğundan düş
        $personelStok = PersonelStock::where('firma_id', $firma->id)
            ->where('pid', $request->personel)
            ->where('stokid', $stokId)
            ->first();

        if ($personelStok) {
            $personelStok->adet -= $request->adet;
            if ($personelStok->adet < 0) {
                $personelStok->adet = 0; // Negatif stok olmasın
            }
            $personelStok->save();
            $perStokId = $personelStok->id;
        }
         $stockAction->perStokId = $perStokId;
         $stockAction->save();

    }

    if ($request->islem == 3) {
        // Personele gönderme: Personel stokuna ekle/güncelle
        $personelStok = PersonelStock::where('firma_id', $firma->id)
            ->where('pid', $request->personel)
            ->where('stokid', $stokId)
            ->first();

        if ($personelStok) {
            $personelStok->adet += $request->adet;
            $personelStok->save();
            $actionMessage = 'Stok başarıyla personele eklendi (mevcut stok güncellendi).';
        } else {
            $personelStok = PersonelStock::create([
                'stokid'   => $stokId,
                'kid'      => $firma->id,
                'firma_id' => $firma->id,
                'pid'      => $request->personel,
                'adet'     => $request->adet,
            ]);
            $actionMessage = 'Stok başarıyla personele gönderildi (yeni kayıt oluşturuldu).';
        }

        // StockAction ile personel stok kaydını ilişkilendir
        $stockAction->perStokId = $personelStok->id;
        $stockAction->save();

        return redirect()->back()->with([
            'message' => $actionMessage,
            'alert-type' => 'success',
        ]);
    }

    // Eğer işlem 2 veya 1 ise başarılı mesajı döndür
    return redirect()->back()->with([
        'message' => 'Stok hareketi başarıyla eklendi.',
        'alert-type' => 'success',
    ]);
}


public function DeleteStockAction(Request $request, $tenant_id, $id)
{
    $firma = Tenant::findOrFail($tenant_id);
    $stockAction = StockAction::where('firma_id', $firma->id)->where('id', $id)->first();

    if (!$stockAction) {
        return response()->json([
            'status' => 'error',
            'message' => 'Silmek istediğiniz stok hareketi bulunamadı.'
        ]);
    }

    // Stok hareketine ait işlem türü kontrolü
    if (in_array($stockAction->islem,[2,4])) {
         return response()->json([
            'status' => 'warning',
            'message' => 'Serviste kullanılmış bir parçayı silemezsiniz. Silmek için servis içerisinden işlem yapmanız gerekmektedir.'
        ]);
    }
    
    if ($stockAction->islem == 1) {
        // Bu alış işleminden sonra çıkış yapılmış mı?
        $girisTarihi = $stockAction->created_at;

        $cikisVarMi = StockAction::where('stokId', $stockAction->stokId)
            ->where('firma_id', $firma->id)
            ->whereIn('islem', [2, 3]) // çıkış işlemleri
            ->where('created_at', '>', $girisTarihi) // bu alıştan sonra yapılmış mı?
            ->exists();

        if ($cikisVarMi) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Alış işleminden sonra çıkış yapıldığı için silinemez.'
            ]);
        }
    }

     if ($stockAction->islem == 3 && $stockAction->perStokId) {
        $servisteKullanildiMi = StockAction::where('firma_id', $firma->id)
            ->where('islem', 2)
            ->where('perStokId', $stockAction->perStokId)
            ->exists();

        if ($servisteKullanildiMi) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Personel stoğu serviste kullanıldığı için silinemez.'
            ]);
        }
    }


   try {
    if ($stockAction->islem == 3 && $stockAction->perStokId) {
        \App\Models\PersonelStock::where('id', $stockAction->perStokId)->delete();
    }

    $stockAction->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Stok hareketi başarıyla silindi.'
    ]);
} catch (\Exception $e) {
    return response()->json([
        'status' => 'error',
        'message' => 'Hata oluştu: ' . $e->getMessage(),
    ]);
}

    return redirect()->back()->with($notification);
}


//////Personel Stok////////
public function GetPersonelStocks($tenant_id, $stok_id)
{
    $firma = Tenant::findOrFail($tenant_id);
$hareketler = StockAction::with('aliciPersonel')
    ->where('firma_id', $firma->id)
    ->where('stokId', $stok_id)
    ->where('islem', 3) // sadece personel'e gönderilenler
    ->get()
    ->groupBy(function ($hareket) {
        return optional($hareket->aliciPersonel)->user_id;
    })
    ->map(function ($grouped) use ($stok_id) {
        $hareket = $grouped->first(); // aynı personelin ilk hareketini al
        $aliciId = $hareket->aliciPersonel->user_id ?? null;

        $hareket->guncel_adet = $aliciId
            ? PersonelStock::where('stokid', $stok_id)
                ->where('pid', $aliciId)
                ->sum('adet')
            : 0;

        return $hareket;
    })
    ->values(); // map sonrası index'leri düzeltir
    return view('frontend.secure.stocks.personel_stocks', compact('hareketler'));
}


//////Stok Fotoğrafları////////
public function getPhotos($tenant_id, $stock_id)
{
    $photos = stock_photos::where('kid', $tenant_id)
                        ->where('stock_id', $stock_id)
                        ->latest()
                        ->get();

    return view('frontend.secure.stocks.stock_photos', compact('photos', 'stock_id', 'tenant_id'));
}

public function uploadPhoto(Request $request, $tenant_id)
{
    $request->validate([
        'resim' => 'required|file|mimes:jpg,jpeg,png|max:5120',
        'stock_id' => 'required|integer'
    ]);


    $image = $request->file('resim');
    $extension = $image->getClientOriginalExtension();

    //İlgili stok bilgisi
    $stock = Stock::findOrFail($request->stock_id);
    $stokAdi = $stock->urunAdi ?? 'bilinmeyen-urun';
    $stokSlug = Str::slug(Str::limit($stokAdi, 50)); 

    // Klasör ve dosya adı
    $today = now()->toDateString(); 
    $uuid = Str::uuid()->toString() . '.' . $extension;
    $path =  "stock_photos/stock_{$stock->id}_{$stokSlug}/{$today}";
    $fullPath = "{$path}/{$uuid}";

    // Resize işlemi (665px genişlik, oran korunsun)
    $resizedImage = Image::make($image->getPathname())
        ->resize(665, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode($extension, 85); // kalite düşürmek istersen burayı değiştir

    // Storage'a kaydet (public disk)
    Storage::disk('public')->put($fullPath, $resizedImage);

   
    // Veritabanına kaydet
    $photo = stock_photos::create([
        'kid' => $tenant_id,
        'stock_id' => $request->stock_id,
        'resimyol' => $fullPath,
        'created_at' => now(),
    ]);

    return response()->json([
        'id' => $photo->id,
        'resim_yolu' => Storage::url($photo->resimyol),
        'message' => 'Fotoğraf başarıyla yüklendi.'
    ]);
} 




public function deletePhoto(Request $request, $tenant_id)
{
    try {
        $photo = stock_photos::where('id', $request->id)
                            ->where('kid', $tenant_id)
                            ->firstOrFail();

        $dosyaYolu = public_path($photo->resimyol);

        if (file_exists($dosyaYolu)) {
            unlink($dosyaYolu);
        }

        $photo->delete();

        return response()->json([
            'message' => 'Fotoğraf başarıyla silindi.',
            'alert_type' => 'success'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Fotoğraf silme işlemi sırasında hata oluştu.',
            'alert_type' => 'danger'
        ], 500);
    }
}

///////////Barkod PDF Oluşturma///////////////////
public function barkodPdf($tenant_id, $id) {
    $stock = Stock::where('firma_id', $tenant_id)->findOrFail($id);

    $pdf = Pdf::loadView('frontend.secure.stocks.stocks_barkod', compact('stock'))
        // 50mm x 25mm boyutları piksel olarak:
        ->setPaper('50mm', '25mm', 'portrait') // setPaper doğrudan mm değerlerini alabilir
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 300,
            'defaultFont' => 'Arial',
            'margin-top' => 0,
            'margin-right' => 0,
            'margin-bottom' => 0,
            'margin-left' => 0,
        ]);

    return $pdf->stream("barkod-{$stock->urunKodu}.pdf");
}
//Ürün Adı Kontolü
public function checkProductName(Request $request, $tenant_id)
{
    $urunAdi = $request->input('urunAdi');

    $stock = Stock::where('firma_id', $tenant_id)
                  ->where('urunAdi', $urunAdi)
                  ->first();

    if ($stock) {
        // Urun kategorisine göre route belirle
        if ($stock->urunKategori == 3) {
            // Konsinye cihaz
            $editUrl = route('edit.consignment.device', ['tenant_id' => $tenant_id, 'id' => $stock->id]);
        } else {
            // Normal stok
            $editUrl = route('edit.stock', ['tenant_id' => $tenant_id, 'id' => $stock->id]);
        }

        return response()->json([
            'exists' => true,
            'edit_url' => $editUrl
        ]);
    }

    return response()->json(['exists' => false]);
}


//////////////////////////////////////////////Konsinye Cihazlar///////////////////////////////////////////////////////////////////////////
public function consignmentDevice($tenant_id)
{
    $firma = Tenant::findOrFail($tenant_id);
    $personeller = User::where('tenant_id', $tenant_id)->get();
    $rafListesi = StockShelf::where('firma_id', $tenant_id)->get();
    $markalar = DeviceBrand::where('firma_id', $tenant_id)->get();
    $cihazlar = DeviceType::where('firma_id', $tenant_id)->get();


    return view('frontend.secure.stocks.consignment_device', compact('firma', 'personeller', 'rafListesi', 'markalar', 'cihazlar'));
}

public function consignmentDeviceData(Request $request, $tenant_id)
{
    $query = Stock::select('stocks.*')
        ->join('stock_categories as kategori', 'kategori.id', '=', 'stocks.urunKategori')
        ->where('stocks.firma_id', $tenant_id)
        ->where('kategori.id', '=', 3);;

    if ($request->filled('marka')) {
        $query->where('stok_marka', $request->marka);
    }
    if ($request->filled('raf')) {
        $query->where('urunDepo', $request->raf);
    }
    if ($request->filled('cihaz')) {
        $query->where('stok_cihaz', $request->cihaz);
    }
    if ($request->filled('personel')) {
        $query->where('pid', $request->personel);
    }

    // Sıralama
    if ($request->has('order')) {
        $order = $request->get('order')[0];
        $columns = $request->get('columns');
        $orderColumn = $columns[$order['column']]['data'];
        $orderDir = $order['dir'];
        $query->orderBy($orderColumn, $orderDir);
    } else {
        $query->orderBy('id', 'desc');
    }

    // Toplamlar hesapla
    $stocksForTotal = $query->get();
    $toplamAdet = 0;
    $toplamFiyat = 0;

    foreach ($stocksForTotal as $stock) {
    $girisAdet  = \App\Models\StockAction::where('stokId', $stock->id)
        ->whereIn('islem', [1, 4])
        ->sum('adet');

    $cikisAdet = \App\Models\StockAction::where('stokId', $stock->id)
        ->whereIn('islem', [2])
        ->sum('adet');

    $kalanStok = $girisAdet - $cikisAdet;

    $toplamAdet += max($kalanStok, 0); // negatifse 0 yap
    $toplamFiyat += max($stock->fiyat, 0);
    }


    return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('urunKodu', function($row) {
            return '<a href="javascript:void(0);" class="t-link editConsignment" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editConsignmentModal">' . e($row->urunKodu) . '</a>';
        })
        ->addColumn('urunAdi', function($row) {
            return '<a href="javascript:void(0);" class="t-link editConsignment" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editConsignmentModal">' . e($row->urunAdi) . '</a>';
        })
        ->addColumn('adet', function($row) {
            $girisAdet = \App\Models\StockAction::where('stokId', $row->id)
                ->whereIn('islem', [1, 4])->sum('adet');
            $cikisAdet = \App\Models\StockAction::where('stokId', $row->id)
                ->whereIn('islem', [2])->sum('adet');
            $kalan = $girisAdet - $cikisAdet;
            return '<a href="javascript:void(0);" class="t-link editConsignment" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editConsignmentModal">' . max($kalan, 0) . '</a>';
        })
        ->addColumn('toplamTutar', function($row) {
            $girisler = \App\Models\StockAction::where('stokId', $row->id)
                ->whereIn('islem', [1, 4])->get();

            $tutar = $row->fiyat ?? 0;

        return '<a href="javascript:void(0);" class="t-link editConsignment" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editConsignmentModal">' . number_format($tutar, 2, ',', '.') . ' ₺</a>';
        })
        ->addColumn('raf_adi', function($row) {
            $raf = $row->raf ? e($row->raf->raf_adi) : '-';
            return '<a href="javascript:void(0);" class="t-link editConsignment" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editConsignmentModal">' . $raf . '</a>';
        })
        ->addColumn('marka_cihaz', function($row) {
            $marka = $row->marka ? e($row->marka->marka) : '';
            $cihaz = $row->cihaz ? e($row->cihaz->cihaz) : '';
            $text = trim($marka . ' / ' . $cihaz, ' / ');
            return '<a href="javascript:void(0);" class="t-link editConsignment" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editConsignmentModal">' . $text . '</a>';
        })
        ->editColumn('created_at', function($row) {
            $date = $row->created_at ? $row->created_at->format('d.m.Y H:i') : '';
            return '<a href="javascript:void(0);" class="t-link editConsignment" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editConsignmentModal">' . $date . '</a>';
        })
        ->addColumn('action', function($row) use ($tenant_id) {
            $deleteUrl = route('delete.stock', [$tenant_id, $row->id]);
            $editBtn = '<a href="javascript:void(0);" class="btn btn-warning btn-sm editConsignment" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editConsignmentModal" title="Düzenle"><i class="fas fa-edit"></i></a>';
            $delBtn = '<a href="'.$deleteUrl.'" class="btn btn-danger btn-sm" title="Sil" onclick="return confirm(\'Silmek istediğinize emin misiniz?\');"><i class="fas fa-trash-alt"></i></a>';
            return $editBtn . ' ' . $delBtn;
        })
        ->filter(function ($query) use ($request) {
            if ($search = $request->get('search')['value'] ?? null) {
                $query->where(function ($q) use ($search) {
                    $q->where('urunAdi', 'like', "%{$search}%")
                      ->orWhere('urunKodu', 'like', "%{$search}%");
                });
            }
        })
        ->rawColumns(['urunKodu', 'urunAdi', 'adet', 'toplamTutar', 'raf_adi', 'marka_cihaz', 'created_at', 'action'])
        ->with([
            'toplamAdet' => number_format($toplamAdet),
            'toplamFiyat' => number_format($toplamFiyat, 2, ',', '.') . ' ₺',
            'toplamAdetRaw' => $toplamAdet,
            'toplamFiyatRaw' => $toplamFiyat,
        ])
        ->make(true);
}
// Konsinye cihaz ekleme 
public function AddConsignmentDevice($tenant_id)
{
    $firma = Tenant::findOrFail($tenant_id);
    $rafListesi = StockShelf::where('firma_id', $tenant_id)->get();
    $markalar = DeviceBrand::where('firma_id', $tenant_id)->get();
    $cihazlar = DeviceType::where('firma_id', $tenant_id)->get();
    $kategoriler = StockCategory::where('firma_id', $tenant_id)->get();

    return view('frontend.secure.stocks.add_consignment_device', compact('firma', 'rafListesi', 'markalar', 'cihazlar', 'kategoriler', 'tenant_id'));
}

// Konsinye cihaz kayıt işlemi
public function StoreConsignmentDevice(Request $request, $tenant_id)
{
    $firma = Tenant::findOrFail($tenant_id);
    if (!$firma) {
    $notification = [
        'message' => 'Firma bulunamadı.',
        'alert-type' => 'danger',
    ];
    return redirect()->route('giris')->with($notification);
    }

 // Ürün kodu kontrolü
    $existingStock = Stock::where('firma_id', $tenant_id)
                          ->where('urunKodu', $request->urunKodu)
                          ->first();

    if ($existingStock) {
        $notification = [
            'message' => 'Bu ürün kodu zaten mevcut. Lütfen farklı bir ürün kodu girin.',
            'alert-type' => 'warning',
        ];
        return redirect()->back()->withInput()->with($notification);
    }

     $request->validate([
        'urunKodu' => ['required', 'digits:13', 'unique:stocks,urunKodu,NULL,id,firma_id,'.$tenant_id],
        // 'digits:13' => tam 13 rakam olmalı,
        // unique kontrolü firma_id bazlı, yani aynı firmada tekrar olmasın
        // diğer alanlar için istersen validation ekleyebilirsin
    ],[
        'urunKodu.required' => 'Ürün kodu zorunludur.',
        'urunKodu.digits' => 'Ürün kodu tam 13 haneli olmalıdır.',
        'urunKodu.unique' => 'Bu ürün kodu zaten mevcut. Lütfen farklı bir ürün kodu girin.',
    ]);
    // Ürün adı kontrolü
    $existingName = Stock::where('firma_id', $tenant_id)
                        ->where('urunAdi', $request->urunAdi)
                        ->first();

    if ($existingName) {
        $notification = [
            'message' => 'Bu ürün adı zaten mevcut. Lütfen farklı bir ürün adı girin.',
            'alert-type' => 'warning',
        ];
        return redirect()->back()->withInput()->with($notification);
    }
    $request->validate([
    'urunKodu' => ['required', 'digits:13', 'unique:stocks,urunKodu,NULL,id,firma_id,'.$tenant_id],
    'urunAdi' => ['required', 'max:255'],
    ],[
        'urunKodu.required' => 'Ürün kodu zorunludur.',
        'urunKodu.digits' => 'Ürün kodu tam 13 haneli olmalıdır.',
        'urunKodu.unique' => 'Bu ürün kodu zaten mevcut. Lütfen farklı bir ürün kodu girin.',
        'urunAdi.required' => 'Ürün adı zorunludur.',
    ]);
    
    $personel_id = Auth::user()->user_id;

    $stock = new Stock();
    $stock->firma_id = $firma->id;
    $stock->pid = $personel_id;
    $stock->urunAdi = $request->urunAdi;
    $stock->urunKodu = $request->urunKodu;
    $stock->urunKategori = 3; // Konsinye kategori ID'si
    $stock->aciklama = $request->aciklama;
    $stock->urunDepo = $request->raf_id;
    $stock->fiyat = $request->fiyat;
    $stock->fiyatBirim = $request->fiyatBirim;
    $stock->stok_marka = $request->marka_id;
    $stock->stok_cihaz = $request->cihaz_id;
    $stock->save();

    // İlk stok hareketi giriş
    $action = new \App\Models\StockAction();
    $action->firma_id = $firma->id;
    $action->pid = $personel_id;
    $action->stokId = $stock->id;
    $action->adet = $request->adet;
    $action->fiyat = $request->fiyat;
    $action->islem = 1; // giriş
    $action->save();

    return redirect()->route('consignmentdevice', $tenant_id)
                     ->with(['message' => 'Konsinye cihaz başarıyla kaydedildi.', 'alert-type' => 'success']);
}

public function EditConsignmentDevice($tenant_id, $id)
{
    $firma = Tenant::findOrFail($tenant_id);
    $stock = Stock::with(['raf', 'marka', 'cihaz', 'sonHareket'])->findOrFail($id);

 
    if ($stock->urunKategori != 3) {  // 3 = konsinye kategori ID'si
        abort(404, "Konsinye cihaz değil.");
    }

    $rafListesi = StockShelf::where('firma_id', $tenant_id)->get();
    $markalar = DeviceBrand::where('firma_id', $tenant_id)->get();
    $cihazlar = DeviceType::where('firma_id', $tenant_id)->get();
    $kategoriler = StockCategory::where('firma_id', $tenant_id)->get();

    $consignmentDevice = $stock;
    $html = view('frontend.secure.stocks.edit_consignment_device', compact('firma', 'consignmentDevice', 'rafListesi', 'markalar', 'kategoriler', 'cihazlar'))->render();

    return response()->json([
        'html' => $html,
        'urunAdi' => $stock->urunAdi,
    ]);

}
public function UpdateConsignmentDevice(Request $request, $tenant_id, $id)
{
    $firma = Tenant::findOrFail($tenant_id);
    $personel_id = Auth::user()->user_id;
    $stock = Stock::findOrFail($id);

    if ($stock->urunKategori != 3) {
        abort(404, "Konsinye cihaz değil.");
    }

    // Ürün kodu kontrolü
    $existingStock = Stock::where('firma_id', $tenant_id)
        ->where('urunKodu', $request->urunKodu)
        ->where('id', '!=', $id)
        ->first();

    if ($existingStock) {
        return redirect()->back()->withInput()->with([
            'message' => 'Bu ürün kodu zaten mevcut. Lütfen farklı bir ürün kodu girin.',
            'alert-type' => 'warning',
        ]);
    }

    // Ürün adı benzersiz mi?
    $existingName = Stock::where('firma_id', $tenant_id)
        ->where('urunAdi', $request->urunAdi)
        ->where('id', '!=', $id)
        ->first();

    if ($existingName) {
        return redirect()->back()->withInput()->with([
            'message' => 'Bu ürün adı zaten mevcut. Lütfen farklı bir ürün adı girin.',
            'alert-type' => 'warning',
        ]);
    }

    // Validation
    $request->validate([
        'urunKodu' => ['required', 'digits:13', 'unique:stocks,urunKodu,'.$id.',id,firma_id,'.$tenant_id],
        'urunAdi' => 'required|max:255',
        'raf_id' => 'required',
    ],[
        'urunKodu.required' => 'Ürün kodu zorunludur.',
        'urunKodu.digits' => 'Ürün kodu tam 13 haneli olmalıdır.',
        'urunKodu.unique' => 'Bu ürün kodu zaten mevcut. Lütfen farklı bir ürün kodu girin.',
        'urunAdi.required' => 'Ürün adı zorunludur.',
    ]);

    $stock->urunAdi = $request->urunAdi;
    $stock->urunKodu = $request->urunKodu;
    $stock->urunKategori = 3;
    $stock->urunDepo = $request->raf_id;
    $stock->aciklama = $request->aciklama;
    $stock->fiyat = $request->fiyat;
    $stock->fiyatBirim = $request->fiyatBirim;
    $stock->stok_marka = $request->marka_id;
    $stock->stok_cihaz = $request->cihaz_id;
    $stock->save();

    return redirect()->back()->with([
        'message' => 'Konsinye cihaz başarıyla güncellendi.',
        'alert-type' => 'success',
    ]);
}


///////////Konsinye Cihaz Stok Haraketleri/////////////////
public function ConsignmentStockActions($tenant_id, $stock_id)
{
    $firma = Tenant::findOrFail($tenant_id);
    $stock = Stock::with(['marka', 'cihaz', 'raf'])
        ->where('firma_id', $tenant_id)
        ->where('urunKategori', 3) // konsinye cihaz
        ->findOrFail($stock_id);

    // Stok hareketlerini join ile getir
    $stokHareketleri = StockAction::with(['musteri'])
            ->select(
                'stock_actions.*',
                'stock_suppliers.tedarikci',
                'user_recipient.name as recipient_name', // islem=3 (Personel'e Gönder) için alıcı personel adı
                'user_performer.name as performer_name'  // islem=2 (Serviste Kullanım) için işlemi yapan personel adı
            )
            // Tedarikçi tablosu ile birleştirme
            ->leftJoin('stock_suppliers', 'stock_suppliers.id', '=', 'stock_actions.tedarikci')
            // 'pid' sütunu üzerinden kullanıcı tablosu ile birleştirme (alıcı personel için)
            ->leftJoin('tb_user as user_recipient', 'user_recipient.user_id', '=', 'stock_actions.pid')
            // 'kid' sütunu üzerinden kullanıcı tablosu ile birleştirme (işlemi yapan personel için)
            ->leftJoin('tb_user as user_performer', 'user_performer.user_id', '=', 'stock_actions.kid')
            ->where('stock_actions.stokId', $stock_id)
            ->orderBy('stock_actions.id', 'desc')
            ->get();
    return view('frontend.secure.stocks.consignment_stock_actions', compact('stock', 'stokHareketleri','firma'));
}

public function StoreConsignmentStockAction(Request $request, $tenant_id)
{
    $firma = Tenant::findOrFail($tenant_id);

    $rules = [
        'stok_id'    => 'required|integer',
        'islem'      => 'required|in:1,2,4',
        'adet'       => 'required|integer|min:1',
        'fiyat'      => 'nullable|numeric',
        'fiyatBirim' => 'nullable|numeric',
        'tedarikci'  => 'nullable|string|max:255',
    ];

  
        $messages = [
            'servisid.required' => 'Serviste kullanım işlemi için servis ID alanı zorunludur.',
            'servisid.integer'  => 'Servis ID bir sayı olmalıdır.',

        ];

        // Laravel'in Validator sınıfını kullanarak doğrulayıcıyı oluştur
        $validator = Validator::make($request->all(), $rules, $messages);

        // islem değeri 2 ise 'servisid' alanını zorunlu yap
        $validator->sometimes('servisid', 'required|integer', function ($input) {
            return $input->islem == 2;
        });


        // Doğrulama başarısız olursa yönlendir
        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput(); // Formda girilen eski değerleri korur
        }
        // $validated = $validator->validated(); // Eğer validated verileri bir değişkene atmak istenirse kullanilabilir


    $stokId = $request->stok_id;
    $personel_id = Auth::user()->user_id;

    $toplamGiris = StockAction::where('stokId', $stokId) ->whereIn('islem', [1,4])->sum('adet'); //alış ve müşteriden iade
    $toplamCikis = StockAction::where('stokId', $stokId)->where('islem', 2)->sum('adet');  //serviste kullanım
    $kalanStok = $toplamGiris - $toplamCikis;

    // Serviste kullanım için yeterli stok var mı?
    if ($request->islem == 2 && $request->adet > $kalanStok) {
        return redirect()->back()->with([
            'message'    => 'Yetersiz stok! Mevcut: ' . $kalanStok . ' adet.',
            'alert-type' => 'error',
        ]);
    }

    $stockAction = new StockAction();
    $stockAction->firma_id   = $firma->id;
    $stockAction->pid        = $personel_id;
    $stockAction->stokId     = $stokId;
    $stockAction->servisid = $request->servisid; 
    $stockAction->islem      = $request->islem;
    $stockAction->adet       = $request->adet;
    $stockAction->fiyat      = $request->fiyat;
    $stockAction->fiyatBirim = $request->fiyatBirim;
    $stockAction->tedarikci  = $request->tedarikci;
    $stockAction->save();

 // Stok adedini güncelle
    $stock = StockAction::find($stokId);
    if ($stock) {
        if (in_array($request->islem, [1,4])) {
            // Alış veya müşteriden geri alma stok artırır
            $stock->adet += $request->adet;
        } elseif ($request->islem == 2) {
            // Serviste kullanım stok azaltır
            $stock->adet -= $request->adet;
        }
        $stock->save();
    }

    return redirect()->back()->with([
        'message' => 'Stok hareketi başarıyla kaydedildi.',
        'alert-type' => 'success',
    ]);
}

public function DeleteConsignmentStockAction($tenant_id, $id)
{
    $firma = Tenant::findOrFail($tenant_id);
    $stockAction = StockAction::where('firma_id', $firma->id)->where('id', $id)->first();

    if (!$stockAction) {
        return response()->json([
            'status' => 'error',
            'message' => 'Silmek istediğiniz stok hareketi bulunamadı.'
        ]);
    }

    // Stok hareketine ait işlem türü kontrolü
    if (in_array($stockAction->islem,[2,4])) {
         return response()->json([
            'status' => 'warning',
            'message' => 'Serviste kullanılmış bir parçayı silemezsiniz. Silmek için servis içerisinden işlem yapmanız gerekmektedir.'
        ]);
    }
    
    if ($stockAction->islem == 1) {
        // Bu alış işleminden sonra çıkış yapılmış mı?
        $girisTarihi = $stockAction->created_at;

        $cikisVarMi = StockAction::where('stokId', $stockAction->stokId)
            ->where('firma_id', $firma->id)
            ->whereIn('islem', [2, 3]) // çıkış işlemleri
            ->where('created_at', '>', $girisTarihi) // bu alıştan sonra yapılmış mı?
            ->exists();
            if ($cikisVarMi) {
                        return response()->json([
                            'status' => 'warning',
                            'message' => 'Alış işleminden sonra çıkış yapıldığı için silinemez.'
                        ]);
                    }
    }
    try {
        $stockAction->delete();
        return response()->json([
        'status' => 'success',
        'message' => 'Stok hareketi başarıyla silindi.'
    ]);
    } catch (\Exception $e) {
        return response()->json([
        'status' => 'error',
        'message' => 'Hata oluştu: ' . $e->getMessage(),
    ]);
    }
    
}


//////Konsinye Cihaz Fotoğrafları////////
public function GetConsignmentPhotos($tenant_id, $stock_id)
{
    $photos = stock_photos::where('kid', $tenant_id)
                        ->where('stock_id', $stock_id)
                        ->latest()
                        ->get();

    return view('frontend.secure.stocks.consignment_device_photos', compact('photos', 'stock_id', 'tenant_id'));
}

public function UploadConsignmentPhoto(Request $request, $tenant_id)
{
     $request->validate([
        'resim' => 'required|file|mimes:jpg,jpeg,png|max:5120',
        'stock_id' => 'required|integer'
    ]);


    $image = $request->file('resim');
    $extension = $image->getClientOriginalExtension();

    //İlgili stok bilgisi
    $stock = Stock::findOrFail($request->stock_id);
    $stokAdi = $stock->urunAdi ?? 'bilinmeyen-urun';
    $stokSlug = Str::slug(Str::limit($stokAdi, 50)); 

    // Klasör ve dosya adı
    $today = now()->toDateString(); 
    $uuid = Str::uuid()->toString() . '.' . $extension;
    $path =  "stock_photos/stock_{$stock->id}_{$stokSlug}/{$today}";
    $fullPath = "{$path}/{$uuid}";

    // Resize işlemi (665px genişlik, oran korunsun)
    $resizedImage = Image::make($image->getPathname())
        ->resize(665, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode($extension, 85); // kalite düşürmek istersen burayı değiştir

    // Storage'a kaydet (public disk)
    Storage::disk('public')->put($fullPath, $resizedImage);

   
    // Veritabanına kaydet
    $photo = stock_photos::create([
        'kid' => $tenant_id,
        'stock_id' => $request->stock_id,
        'resimyol' => $fullPath,
        'created_at' => now(),
    ]);

    return response()->json([
        'id' => $photo->id,
        'resim_yolu' => Storage::url($photo->resimyol),
        'message' => 'Fotoğraf başarıyla yüklendi.'
    ]);
} 
public function DeleteConsignmentPhoto(Request $request, $tenant_id)
{
    try {
        $photo = stock_photos::where('id', $request->id)
                            ->where('kid', $tenant_id)
                            ->firstOrFail();

        $dosyaYolu = public_path($photo->resimyol);

        if (file_exists($dosyaYolu)) {
            unlink($dosyaYolu);
        }

        $photo->delete();

        return response()->json([
            'message' => 'Fotoğraf başarıyla silindi.',
            'alert_type' => 'success'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Fotoğraf silme işlemi sırasında hata oluştu.',
            'alert_type' => 'danger'
        ], 500);
    }
}

public function ConsignmentBarcode($tenant_id, $id) {
    $stock = Stock::where('firma_id', $tenant_id)->findOrFail($id);
    
    $pdf = Pdf::loadView('frontend.secure.stocks.consignment_device_barcode', compact('stock'))
        ->setPaper([0, 0, 141.7, 70.85], 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true, 
            'isRemoteEnabled' => true,
            'dpi' => 300,
            'defaultFont' => 'Arial'
        ]);
    
    return $pdf->stream("barkod-{$stock->urunKodu}.pdf");
}

}
