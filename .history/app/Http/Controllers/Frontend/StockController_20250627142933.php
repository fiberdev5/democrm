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
            $toplamCikis = \App\Models\StockAction::where('stokId', $stock->id)->where('islem', 2)->sum('adet');  //personele gonder
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
                $toplamCikis = \App\Models\StockAction::where('stokId', $row->id)->where('islem', 2)->sum('adet');
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
            'message' => 'Ürün içerisinde stok hareket kaydı bulunduğundan dolayı silme işlemi gerçekleştirilemez.',
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
        'islem'      => 'required|in:1,2',
        'adet'       => 'required|integer|min:1',
        'fiyat'      => $request->islem == 1 ? 'required' : 'nullable',
        'fiyatBirim' => 'nullable|numeric',
        'tedarikci'  => 'nullable|string|max:255',
    ]);

    $personel_id = Auth::user()->user_id;
    $stokId = $request->stok_id;

    // Fiyatı temizle (nokta ve virgül fix)
    $fiyat = null;
    if ($request->islem == 1 && $request->filled('fiyat')) {
        $fiyat = floatval(str_replace(['.', ','], ['', '.'], $request->fiyat)); // 1.200,50 → 1200.50
    }

     // Serviste kullanım için yeterli stok var mı?
    if ($request->islem == 4 && $request->adet > $kalanStok) {
        return redirect()->back()->with([
            'message'    => 'Yetersiz stok! Mevcut: ' . $kalanStok . ' adet.',
            'alert-type' => 'error',
        ]);
    }
    // Stok hareketi kaydı
    $stockAction = new StockAction();
    $stockAction->firma_id   = $firma->id;
    $stockAction->pid        = $personel_id;
    $stockAction->stokId     = $stokId;
    $stockAction->islem      = $request->islem;
    $stockAction->adet       = $request->adet;
    $stockAction->fiyat      = $request->islem == 1 ? $fiyat : null;
    $stockAction->fiyatBirim = $request->islem == 1 ? $request->fiyatBirim : null;
    $stockAction->tedarikci  = $request->tedarikci;
    $stockAction->save();

    // Alış işlemi ise stok fiyatını güncelle
    if ($request->islem == 1) {
        $stock = \App\Models\Stock::find($stokId);
        if ($stock) {
            $stock->fiyat += $fiyat;
            $stock->save();
        }
    }

    // Personel'e gönderme işlemi ise
    if ($request->islem == 2) {
        $personelStok = \App\Models\PersonelStock::create([
            'stokid' => $stokId,
            'kid'    => $firma->id,
            'pid'    => $personel_id,
            'adet'   => $request->adet,
        ]);
        $stockAction->perStokId = $personelStok->id;
        $stockAction->personel  = $personel_id;
        $stockAction->save();
    }

    return redirect()->back()->with([
        'message'    => 'Stok hareketi başarıyla eklendi.',
        'alert-type' => 'success',
    ]);
}


public function DeleteStockAction($tenant_id, $id)
{
    $firma = Tenant::findOrFail($tenant_id);
    $stockAction = StockAction::where('firma_id', $firma->id)->where('id', $id)->first();

    if (!$stockAction) {
        $notification = [
            'message' => 'Silmek istediğiniz stok hareketi bulunamadı.',
            'alert-type' => 'danger'
        ];
        return redirect()->back()->with($notification);
    }
    // Stok hareketleri var mı kontrol et
    $stokHareketSayisi = StockAction::where('stokId', $id)->count();

    if ($stokHareketSayisi > 0) {
        $notification = [
            'message' => 'Ürün içerisinde stok hareket kaydı bulunduğundan dolayı silme işlemi gerçekleştirilemez.',
            'alert-type' => 'warning'
        ];
        return redirect()->back()->with($notification);
    }


    try {
        if ($stockAction->islem == 2 && $stockAction->perStokId) {
            \App\Models\PersonelStock::where('id', $stockAction->perStokId)->delete();
        }

        $stockAction->delete();

        $notification = [
            'message' => 'Stok hareketi başarıyla silindi.',
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


//////Personel Stok////////
public function GetPersonelStocks($tenant_id, $stok_id)
{
    $firma = Tenant::findOrFail($tenant_id);

    $hareketler = StockAction::where('firma_id', $firma->id)
        ->where('stokId', $stok_id)
        ->where('islem', 2) // sadece personel'e gönderilenler
        ->orderByDesc('created_at')
        ->get();

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

    // Klasör ve dosya adı
    $today = now()->toDateString(); // 2025-06-27
    $uuid = Str::uuid()->toString() . '.' . $extension;
    $path = "stock_photos/stock_{$request->stock_id}/{$today}";
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
        ->setPaper([0, 0, 141.7, 70.85], 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true, 
            'isRemoteEnabled' => true,
            'dpi' => 300,
            'defaultFont' => 'Arial'
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
        $girisler = \App\Models\StockAction::where('stokId', $stock->id)
        ->whereIn('islem', [1, 4])
        ->get();

        $toplamGirisAdet=0;
        $toplamGirisFiyat = 0;

        foreach ($girisler as $giris) {
            $toplamGirisAdet += $giris->adet;
            $toplamGirisFiyat += $giris->fiyat;
        }
       
        $kalanAdet = $toplamGirisAdet;
        $kalanFiyat = $toplamGirisFiyat;

        $toplamAdet += max($kalanAdet, 0);
        $toplamFiyat += max($kalanFiyat, 0);
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
            $toplamGiris = \App\Models\StockAction::where('stokId', $row->id)
                ->whereIn('islem', [1, 4])->sum('adet');
            return '<a href="javascript:void(0);" class="t-link editConsignment" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editConsignmentModal">' . $toplamGiris . '</a>';
        })
        ->addColumn('toplamTutar', function($row) {
            $girisler = \App\Models\StockAction::where('stokId', $row->id)
                ->whereIn('islem', [1, 4])->get();

            $girisTutar = $girisler->sum(function($item) {
                return $item->fiyat;
            });

        return '<a href="javascript:void(0);" class="t-link editConsignment" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editConsignmentModal">' . number_format($girisTutar, 2, ',', '.') . ' ₺</a>';
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
    $stock = Stock::with(['marka', 'cihaz', 'raf'])
        ->where('firma_id', $tenant_id)
        ->where('urunKategori', 3) // konsinye cihaz
        ->findOrFail($stock_id);

    $stokHareketleri = StockAction::with(['musteri'])
        ->select('stock_actions.*', 'stock_suppliers.tedarikci', 'tb_user.name')
        ->leftJoin('stock_suppliers', 'stock_suppliers.id', '=', 'stock_actions.tedarikci')
        ->leftJoin('tb_user', 'tb_user.user_id', '=', 'stock_actions.pid')
        ->where('stock_actions.stokId', $stock_id)
        ->orderBy('stock_actions.id', 'desc')
        ->get();
    return view('frontend.secure.stocks.consignment_stock_actions', compact('stock', 'stokHareketleri'));
}

public function StoreConsignmentStockAction(Request $request, $tenant_id)
{
    $firma = Tenant::findOrFail($tenant_id);

    $validated = $request->validate([
        'stok_id'    => 'required|integer',
        'islem'      => 'required|in:1,4',
        'adet'       => 'required|integer|min:1',
        'fiyat'      => 'nullable|numeric',
        'fiyatBirim' => 'nullable|numeric',
        'tedarikci'  => 'nullable|string|max:255',

    ]);

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
    $stockAction->servisid = $request->servisid; // Müşteri ID
    $stockAction->islem      = $request->islem;
    $stockAction->adet       = $request->adet;
    $stockAction->fiyat      = $request->fiyat;
    $stockAction->fiyatBirim = $request->fiyatBirim;
    $stockAction->tedarikci  = $request->tedarikci;
    $stockAction->save();


if ($request->islem == 1) {
    $stock = \App\Models\Stock::find($stokId);
    if ($stock) {
        $stock->fiyat += $request->fiyat;
        $stock->save();
    }
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
        return redirect()->back()->with([
            'message' => 'Stok hareketi bulunamadı.',
            'alert-type' => 'danger',
        ]);
    }

    try {
        // if ($stockAction->islem == 2 && $stockAction->servisid) {
        //     \App\Models\ServisStock::where('id', $stockAction->servisid)->delete();
        // }

        $stockAction->delete();

        return redirect()->back()->with([
            'message' => 'Stok hareketi silindi.',
            'alert-type' => 'success',
        ]);
    } catch (\Exception $e) {
        return redirect()->back()->with([
            'message' => 'Silme sırasında hata oluştu.',
            'alert-type' => 'danger',
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
        'resim' => 'required|file|max:5120',
        'stock_id' => 'required|integer'
    ]);

    $image = $request->file('resim');
    $extension = $image->getClientOriginalExtension();

    if (!in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])) {
        return response()->json([
            'message' => 'Sadece jpg ve png uzantılı dosyalar yükleyebilirsiniz.'
        ], 422);
    }

    $name_gen = hexdec(uniqid()) . '.' . $extension;
    $save_path = 'upload/stock_photos/' . $name_gen;
    $full_save_path = public_path($save_path);

    // Genişliği 665px, yüksekliği otomatik ayarla
    Image::make($image->path())
        ->resize(665, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })
        ->save($full_save_path);

    $photo = stock_photos::create([
        'kid' => $tenant_id,
        'stock_id' => $request->stock_id,
        'resimyol' => '/' . $save_path,
    ]);

    return response()->json([
        'id' => $photo->id,
        'resim_yolu' => $photo->resimyol,
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
