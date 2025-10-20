<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Il;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\PaymentMethod;
use App\Models\Service;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Services\ActivityLogger;

class InvoicesController extends Controller
{
    public function AllInvoice(Request $request, $tenant_id) {
        $invoices = Invoice::where('firma_id',$tenant_id)->where('durum', 1)->orderBy('id','desc')->get();
        $musteriler = Customer::where('firma_id', $tenant_id)->orderBy('adSoyad', 'ASC')->get();
        $firma = Tenant::where('id', $tenant_id)->first();

        if ($request->ajax()) {           
            $data = Invoice::with('customer')->where('firma_id', $tenant_id)->where('durum', '1');
            if ($request->get('musteri')) {
                $musteriID = $request->get('musteri');
                $data->whereHas('customer', function ($query) use ($musteriID) {
                    $query->where('id', $musteriID);
                });
            }
            

            $data->when($request->filled('from_date') && $request->filled('to_date'), function ($query) use ($request) {
                return $query->whereDate('faturaTarihi', '>=', $request->from_date)
                             ->whereDate('faturaTarihi', '<=', $request->to_date);
            });

            // Sıralama işlemi
            if ($request->has('order')) {
                $order = $request->get('order')[0];
                $columns = $request->get('columns');
                $orderColumn = $columns[$order['column']]['data'];
                $orderDir = $order['dir'];
                
                if($orderColumn == 'mid'){
                    $data->leftJoin('customers', 'invoices.musteriid', '=', 'customers.id')
                    ->addSelect(['invoices.*', 'customers.adSoyad as musAdi'])
                    ->orderBy('customers.adSoyad',$orderDir);
                }
                else {
                    $data->orderBy($orderColumn, $orderDir);
                }
            } else {
                $data->orderBy('faturaTarihi','desc');
            }
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('id', function($row){
                return '<a class="t-link editInvoice idWrap" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editInvoiceModal"><div class="mobileTitle">Id:</div>'.$row->id.'</a>';
            })
            ->addColumn('faturaTarihi', function($row){
                $faturaTarihi = Carbon::parse($row->faturaTarihi)->format('d/m/Y H:i');
                return '<a class="t-link editInvoice" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editInvoiceModal"><div class="mobileTitle">Fatura Tarihi:</div>'.$faturaTarihi.'</a>';
            })
            ->addColumn('faturaNumarasi', function($row){
                return '<a class="t-link editInvoice" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editInvoiceModal"><div class="mobileTitle">F. No:</div>'.$row->faturaNumarasi.'</a>';
            })
            ->addColumn('mid', function($row){
                return '<a class="t-link editInvoice address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editInvoiceModal"><span class="mobileTitle">Müşteri:</span><strong>'.$row->customer?->adSoyad.'</strong><br><div style="font-size:12px;">'.$row->customer?->m_adi.'</div></a>';
            })
            ->addColumn('genelToplam', function($row){
                return '<a class="t-link editInvoice" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editInvoiceModal"><div class="mobileTitle">G. Toplam:</div>'.$row->genelToplam.' ₺</a>';
            })
            ->addColumn('faturaDurumu', function($row){
                if($row->faturaDurumu == 'sent'){
                    return '<a class="t-link editInvoice" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editInvoiceModal"><div class="mobileTitle">Durum:</div><div style="color: green; display: inline-block;font-weight:700;">Gönderildi</div></a>';
                }elseif($row->faturaDurumu == 'draft'){
                    return '<a class="t-link editInvoice" href="javascript:void(0);" data-bs-id="{{$item->id}}" data-bs-toggle="modal" data-bs-target="#editInvoiceModal"><div class="mobileTitle">Durum:</div><div style="color: #216dfd; display: inline-block;font-weight:700;">Beklemede</div></a>';
                }elseif($row->faturaDurumu == 'error'){
                    return '<a class="t-link editInvoice" href="javascript:void(0);" data-bs-id="{{$item->id}}" data-bs-toggle="modal" data-bs-target="#editInvoiceModal"><div class="mobileTitle">Durum:</div><div style="color: red; display: inline-block;font-weight:700;">Gönderilmedi</div></a>';
                }
            })
            ->addColumn('actions', function($row){
                $deleteUrl = route('delete.invoices', [$row->firma_id,$row->id]);
                $earsivButton = '<a href="'.asset($row->faturaPdf).'" target="_blank" class="btn btn-outline-primary btn-sm mobilBtn mbuton1" title="Faturayı görüntüle"><i class="far fa-eye"></i></a>';
                $editButton = '';
                $deleteButton = '';
                $editButton = '<a href="javascript:void(0);" data-bs-id="'.$row->id.'" class="btn btn-outline-warning btn-sm editInvoice mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editInvoiceModal" title="Düzenle"><i class="fas fa-edit"></i></a>';
                $deleteButton = '<a href="'.$deleteUrl.'" class="btn btn-outline-danger btn-sm mobilBtn" id="delete" title="Sil"><i class="fas fa-trash-alt"></i></a>';
                return $earsivButton. '  '.$editButton. '  '.$deleteButton;
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                       $search = $request->get('search');
                       $w->where('id', 'LIKE', "%$search%")
                       ->orWhereHas('customer', function($q) use($search) {
                            $q->where('adSoyad', 'LIKE', "%$search%");
                        });
                   });
                }

            })
            ->rawColumns(['id','faturaTarihi','faturaNumarasi','mid','genelToplam','faturaDurumu','actions'])
            ->make(true);

        }

        return view('frontend.secure.invoices.all_invoices',compact('musteriler','invoices','firma'));
    }

    public function searchMusteri(Request $request)
    {
        $searchField = $request->input('musteriGetir');
        $musteriler = Customer::where('adSoyad', 'like', '%' . $searchField . '%')->where('durum','1')->get();
        return response()->json($musteriler);
    }

    public function GetInvoices(Request $request, $tenant_id)
    {  
        $data = Invoice::query();

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $start = Carbon::parse($request->input('from_date'))->startOfDay();
            $end = Carbon::parse($request->input('to_date'))->endOfDay();

            $data->whereBetween('faturaTarihi', [$start, $end]);
        }

        if($request->filled('musteri')){
            $data->where('musteriid', $request->input('musteri'));
        }

        if ($request->filled('durum')) {
            if($request->get('durum') == 1){
                $data->where('odemeDurum', 1);
            }elseif($request->get('durum') == 0){
                $data->where('odemeDurum', 0);
            }else{
                $data->get();
            }
        }
        
        $filteredData = $data->where('firma_id',$tenant_id)->where('durum','1')->get();
        
        $response = [
            'toplamNakitTL' => 0.00,
            'toplamHavaleTL' => 0.00,
            'toplamKartTL' => 0.00,
            'kdvNakitTL' => 0.00,
            'kdvHavaleTL' => 0.00,
            'kdvKartTL' => 0.00,
            'genelNakitTL' => 0.00,
            'genelHavaleTL' => 0.00,
            'genelKartTL' => 0.00,
            'toplamTutarTL1' => 0.00,
            'toplamTutarTL2' => 0.00,
            'toplamTutarTL3' => 0.00
        ];
        
        
        foreach ($filteredData as $item) {
            $toplamTL = $item->toplam;
            $kdvTL = $item->kdv;
            $genelTL = $item->genelToplam;
            
            if ($item->odemeSekli == 1) {
                $response['toplamNakitTL'] += $item->toplam;
                $response['kdvNakitTL'] += $item->kdv;
                $response['genelNakitTL'] += $item->genelToplam;
            } elseif ($item->odemeSekli == 2) {
                $response['toplamHavaleTL'] += $item->toplam;
                $response['kdvHavaleTL'] += $item->kdv;
                $response['genelHavaleTL'] += $item->genelToplam;
            } elseif ($item->odemeSekli == 3) {
                $response['toplamKartTL'] += $item->toplam;
                $response['kdvKartTL'] += $item->kdv;
                $response['genelKartTL'] += $item->genelToplam;
            }

            $response['toplamTutarTL1'] += $item->toplam;
            $response['toplamTutarTL2'] += $item->kdv;
            $response['toplamTutarTL3'] += $item->genelToplam;
        }

        
        
        foreach ($response as $key => $value) {
            if (strpos($key, 'TL') !== false) {
                $response[$key] = number_format($value, 2, ',', '.') . ' TL';
            }
        }
        return response()->json($response);
    }

    public function AddInvoice($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if (!$firma) {
            return redirect()->route('giris')->with([
                'message' => 'Firma bulunamadı.',
                'alert-type' => 'danger',
            ]);
        }   

        $musteriler = Customer::where('firma_id', $tenant_id)->orderBy('adSoyad', 'ASC')->get();
        $payment_methods = PaymentMethod::where(function ($query) use ($tenant_id) {
            $query->whereNull('firma_id')->orWhere('firma_id', $tenant_id);
        })->orderBy('id', 'asc')->get();
        $countries = Il::orderBy('name', 'ASC')->get();
        return view('frontend.secure.invoices.add_invoices',compact('musteriler','payment_methods','firma','countries'));
    }

    public function musteriGetir(Request $request,$tenant_id)
    {
        $servisId = $request->input('servisId');

        if (!$servisId) {
            return response()->json(['error' => 'Servis ID eksik.'], 400);
        }

        $veriler = DB::table('services')
            ->leftJoin('customers', 'services.musteri_id', '=', 'customers.id')
            ->leftJoin('device_brands', 'services.cihazMarka', '=', 'device_brands.id')
            ->leftJoin('device_types', 'services.cihazTur', '=', 'device_types.id')
            ->where('services.id', $servisId)
            ->where('services.firma_id', $tenant_id) // Tablo adını belirttik
            ->select(
                'services.id',
                'services.musteri_id',
                'customers.musteriTipi',
                'customers.adSoyad',
                'customers.tel1',
                'customers.tel2',
                'customers.il',
                'customers.ilce',
                'customers.adres',
                'customers.tcNo',
                'customers.vergiNo',
                'customers.vergiDairesi',
                'device_brands.marka',
                'device_types.cihaz'
            )
            ->first(); // get() yerine first() kullandık çünkü tek kayıt bekliyoruz

        if ($veriler) {
            return response()->json([
                'success' => true,
                'data' => $veriler
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Servis bulunamadı'
            ], 200);
        }
    }
    public function StoreInvoice(Request $request, $tenant_id){
    $validateData = $request->validate([
        'document'=> 'max:2000',
    ]);

    
    // Sayısal değerleri doğru şekilde dönüştür
    $toplam = $this->convertToDecimal($request->toplam);
    $indirim = $this->convertToDecimal($request->indirim);
    $kdv = $this->convertToDecimal($request->kdv);
    $genelToplam = $this->convertToDecimal($request->genelToplam);
    

    
    $firma = Tenant::where('id', $tenant_id)->first();
    if (!$firma) {
        return response()->json([
            'success' => false,
            'message' => 'Firma bulunamadı'
        ], 404);
    }
    
    $document = $request->file('document');
    
    // Storage kontrolü
    if ($document && !$firma->canUploadFile($document->getSize())) {
        $storageInfo = $firma->getStorageInfo();
        return response()->json([
            'success' => false,
            'message' => "Storage limiti doldu! Dosya boyutu: " . $this->formatBytes($document->getSize()) . 
                        ", Kalan alan: " . $storageInfo['remaining_formatted'],
            'error_type' => 'storage_limit_exceeded'
        ], 422);
    }
    
    $extension = $document->extension();
    if($extension != "jpg" && $extension != "png" && $extension != "jpeg" && $extension != "pdf"){
        return response()->json([
            'success' => false,
            'message' => 'Dosya uzantısı sadece jpg,png,jpeg veya pdf olmalı'
        ], 422);
    }
    
    $fileName = time().'.'.$document->getClientOriginalExtension();  
    $save_url = $document->move('upload/uploads', $fileName);
    
    $createdAt = Carbon::parse($request->faturaTarihi . ' ' . now()->format('H:i:s'));

    $invoice = Invoice::create([
        'firma_id' => $firma->id,
        'servisid' => $request->servisid,
        'musteriid' => $request->mid,
        'faturaNumarasi' => $request->faturaNumarasi,
        'faturaTarihi' => $createdAt,
        'odemeSekli' => $request->odemeSekli,
        'toplam' => $toplam,
        'indirim' => $indirim,
        'kdv' => $kdv,
        'kdvTutar' => $request->kdvTutar,
        'genelToplam' => $genelToplam,
        'toplamYazi' => $request->toplamYazi,
        'kayitAlan' => auth()->user()->user_id,
        'faturaPdf' => $save_url,
    ]);

    $invoice->faturaDurumu = 'sent';

    $invoice_id = $invoice->id;
    if($invoice){

        // Müşteri bilgisini al
        $customer = Customer::find($request->mid);
        $customerName = $customer ? $customer->adSoyad : null;
        
        // Fatura oluşturma log kaydı
        ActivityLogger::logInvoiceCreated($invoice->id, $request->faturaNumarasi, $customerName);
        // 1. Fatura müşterisi olarak işaretle
        Customer::where('id', $request->mid)->update(['faturaMusterisi' => '1']);

        // 2. Ürünleri kaydet
        $aciklama = $request->aciklama;
        $miktar = $request->miktar;
        $fiyat = $request->fiyat;
        $tutar = $request->tutar;

        // Storage warning kontrolü
        $storageWarning = null;
        if (session()->has('storage_warning_info')) {
            $storageInfo = session()->get('storage_warning_info');
            $storageWarning = "Fatura eklendi ancak storage alanınız %{$storageInfo['usage_percentage']} dolu. Kalan alan: {$storageInfo['remaining_formatted']}. Planınızı yükseltmeyi düşünün.";
        }


        foreach($aciklama as $key => $val){
            if(!empty($val)){
                InvoiceProduct::insert([
                    'firma_id' => $firma->id,
                    'faturaid' => $invoice_id,
                    'aciklama' => $val,
                    'miktar' => $miktar[$key],
                    'fiyat' => $this->convertToDecimal($fiyat[$key]),
                    'tutar' => $this->convertToDecimal($tutar[$key]),
                ]);
            }
        }
        
        $notification = array(
            'message' => 'Fatura Başarıyla Eklendi',
            'alert-type' => 'success'
        );
    
        return redirect()->back()->with($notification);
    }else{
        $notification = array(
            'message' => 'Fatura Eklenemedi',
            'alert-type' => 'warning'
        );
    
        return redirect()->back()->with($notification);
    } 
}


// Helper method ekleyin
private function formatBytes($bytes, $precision = 2) 
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}


    public function EditInvoice($tenant_id,$id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if (!$firma) {
            return redirect()->route('giris')->with([
                'message' => 'Firma bulunamadı.',
                'alert-type' => 'danger',
            ]);
        }   
        $invoice_id = Invoice::findOrFail($id);
        $m_id = $invoice_id->musteriid;
        $musteri= Customer::where('id', $m_id)->where('firma_id', $tenant_id)->first();
        $musteriler = Customer::where('firma_id', $tenant_id)->orderBy('adSoyad', 'ASC')->get();
        $payment_methods = PaymentMethod::where(function ($query) use ($tenant_id) {
            $query->whereNull('firma_id')->orWhere('firma_id', $tenant_id);
        })->orderBy('id', 'asc')->get();
        $countries = Il::orderBy('name', 'ASC')->get();
        $invoice_products = InvoiceProduct::where('firma_id', $tenant_id)->where('faturaid',$id)->get();
        return view('frontend.secure.invoices.edit_invoices',compact('invoice_id','musteri', 'musteriler','payment_methods','invoice_products', 'firma','countries'));

    }

private function convertToDecimal($value)
{
    // Boş değer kontrolü
    if (empty($value)) {
        return 0.00;
    }
    
    // String'e çevir
    $value = (string) $value;
    
    // Türkçe format kontrolü (14,40 gibi)
    if (strpos($value, ',') !== false) {
        // Binlik ayracı noktaları kaldır (1.234,56 -> 1234,56)
        if (substr_count($value, '.') > 0 && strpos($value, ',') > strrpos($value, '.')) {
            $value = str_replace('.', '', $value);
        }
        // Virgülü noktaya çevir
        $value = str_replace(',', '.', $value);
    }
    
    // Float'a çevir ve 2 basamağa yuvarla
    return round(floatval($value), 2);
}
   public function UpdateInvoice(Request $request, $tenant_id) {
    $firma = Tenant::where('id', $tenant_id)->first();
    $invoice_id = $request->id;
    $pid = Auth::user()->user_id;
    $createdAt = Carbon::parse($request->faturaTarihi . ' ' . now()->format('H:i:s'));

    // Fatura bilgilerini güncelle
    $invoice = Invoice::where('firma_id', $tenant_id)->findOrFail($invoice_id);
    $invoice->faturaNumarasi = $request->faturaNumarasi;
    $invoice->faturaTarihi = $createdAt;
    $invoice->odemeSekli = $request->odemeSekli;
    $invoice->toplam = $this->convertToDecimal($request->toplam);
    $invoice->indirim = $this->convertToDecimal($request->indirim);
    $invoice->kdv = $this->convertToDecimal($request->kdv);
    $invoice->kdvTutar = $request->kdvTutar;
    $invoice->genelToplam = $this->convertToDecimal($request->genelToplam);
    $invoice->toplamYazi = $request->toplamYazi;
    $invoice->faturaDurumu = $request->faturaDurumu;
    $invoice->save();

    // Müşteri bilgisini al
    $customer = Customer::find($invoice->musteriid);
    $customerName = $customer ? $customer->adSoyad : null;

    // Fatura güncelleme log kaydı
    ActivityLogger::logInvoiceUpdated($invoice_id, $request->faturaNumarasi, $customerName);

    $oldProducts = InvoiceProduct::where('firma_id', $firma->id)->where('faturaid', $invoice_id)->get();
    foreach($oldProducts as $product){
        InvoiceProduct::where('firma_id', $tenant_id)->findOrFail($product->id)->delete();
    }

    // Yeni ürünleri ekle
    $aciklama = $request->aciklama;
    $miktar = $request->miktar;
    $fiyat = $request->fiyat;
    $tutar = $request->tutar;

    foreach ($aciklama as $key => $val) {
        if (!empty($val)) {
            InvoiceProduct::create([
                'firma_id' => $firma->id,
                'faturaid' => $invoice_id,
                'aciklama' => $val,
                'miktar' => $miktar[$key],
                'fiyat' => $this->convertToDecimal($fiyat[$key]),
                'tutar' => $this->convertToDecimal($tutar[$key]),
            ]);
        }
    }
    
    if(isset($invoice->servisid)){
        if(!empty($invoice->servisid)){
            Service::findOrFail($invoice->servisid)->update([
                'faturaNumarasi' => $request->faturaNumarasi,
            ]);
        }
    }
    
    $notification = array(
        'message' => 'Fatura Bilgileri Başarıyla Güncellendi',
        'alert-type' => 'success'
    );
    return response()->json(['success' => $notification]);
}


    public function DeleteInvoice($tenant_id,$id) {
         // Önce faturayı bul
        $fatura = Invoice::where('firma_id', $tenant_id)->findOrFail($id);

        // Müşteri bilgisini al (silmeden önce)
        $customer = Customer::find($fatura->musteriid);
        $customerName = $customer ? $customer->adSoyad : null;
        $invoiceNumber = $fatura->faturaNumarasi;
        $invoiceId = $fatura->id;

        // Faturanın servis numarasını güncelle
        Service::where('id', $fatura->servisid)->update([
            'faturaNumarasi' => null,
        ]);

        // Faturaya bağlı ürünleri sil
        $eskiUrunler = InvoiceProduct::where('firma_id', $tenant_id)
            ->where('faturaid', $id)
            ->get();

        foreach ($eskiUrunler as $urun) {
            $urun->delete();
        }

        // Faturayı sil
        $fatura->delete();

        // Fatura silme log kaydı
        ActivityLogger::logInvoiceDeleted($invoiceId, $invoiceNumber, $customerName);

        $notification = [
            'message' => 'Fatura Başarıyla Silindi',
            'alert-type' => 'success'
        ];

        return redirect()->route('all.invoices', $tenant_id)->with($notification);
    }

    public function ShowInvoice($tenant_id,$id) {
        $invoice_id = Invoice::findOrFail($id);
        $firma = Tenant::where('id', $tenant_id)->first();
        return view('frontend.secure.invoices.show_invoices',compact('invoice_id','firma'));

    }

    public function UploadInvoice(Request $request, $tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $invoice_id = $request->id;
        
        $document = $request->file('pdf');
        if($document) {
            $extension = $request->file('pdf')->extension();
            if($extension != "jpg" && $extension != "png" && $extension != "jpeg" && $extension != "pdf"){
                $notification = array(
                    'message' => ' Dosya  uzantısı sadece jpg,png,jpeg veya pdf olmalı',
                    'alert-type' => 'warning'
                );
                return redirect()->back()->with($notification);
            }
            $fileName = time().'.'.$document->getClientOriginalExtension();  
            $save_url = $document->move('upload/uploads', $fileName);
            Invoice::where('firma_id', $tenant_id)->find($invoice_id)->update([
                'faturaPdf' => $save_url,
            ]);

            return redirect()->back()->with('faturaPdf',$fileName);
        }
        $notification = array(
            'message' => 'Fatura başarıyla yüklendi',
            'alert-type' => 'success',
        );
        return response()->json(['success' => $notification]);
    }

    public function DeleteEinvoice($tenant_id, $id){
        $invoice_id = Invoice::findOrFail($id);
        $doc = $invoice_id->faturaPdf;
        
        Invoice::findOrFail($id)->update([
            'faturaPdf' => null,
        ]);

        $notification = array(
            'message' => 'Fatura başarıyla silindi',
            'alert-type' => 'success',
        );
        return response()->json(true);
    }
}
