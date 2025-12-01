<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Il;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\KdvKodu;
use App\Models\PaymentMethod;
use App\Models\Service;
use App\Models\Tenant;
use App\Models\TevkifatKodu;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Services\ActivityLogger;
use App\Services\InvoiceIntegrationFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class InvoicesController extends Controller
{
    public function AllInvoice(Request $request, $tenant_id) {
        $invoices = Invoice::where('firma_id',$tenant_id)->where('durum', 1)->orderBy('id','desc')->get();
        //$musteriler = Customer::where('firma_id', $tenant_id)->orderBy('adSoyad', 'ASC')->get();
        $firma = Tenant::where('id', $tenant_id)->first();

        if ($request->ajax()) {           
            $data = Invoice::with('customer')->where('firma_id', $tenant_id)->where('durum', '1');

        // Tarih filtreleme mantığı 
        $hasUserSelectedInvoiceDate = $request->filled('from_date') && $request->filled('to_date') && !$this->isDefaultInvoiceDateRange($request);
        $hasSearchOrOtherFilters = !empty(trim($request->get('search', ''))) || 
                                    $request->filled('musteri') || 
                                    $request->filled('durum');

        if ($hasUserSelectedInvoiceDate) {
            // Fatura sayfasındaki tarih filtresi en yüksek önceliğe sahiptir
            $this->applyMainInvoiceDateRange($data, $request);
        } elseif (!$hasUserSelectedInvoiceDate && !$hasSearchOrOtherFilters) {
            // Hiçbir tarih veya arama/filtre yoksa, varsayılan son 3 günü uygula
            $from = Carbon::today()->subDays(2)->startOfDay();
            $to   = Carbon::today()->endOfDay();
            $data->whereBetween('faturaTarihi', [$from, $to]);
        }
        // Eğer $hasSearchOrOtherFilters true ise ancak tarih filtresi seçilmemişse,
        // herhangi bir tarih kısıtlaması uygulanmaz, bu da tüm kayıtlarda arama yapılmasını sağlar.


            if ($request->get('musteri')) {
                $musteriID = $request->get('musteri');
                $data->whereHas('customer', function ($query) use ($musteriID) {
                    $query->where('id', $musteriID);
                });
            }
            

            // $data->when($request->filled('from_date') && $request->filled('to_date'), function ($query) use ($request) {
            //     return $query->whereDate('faturaTarihi', '>=', $request->from_date)
            //                  ->whereDate('faturaTarihi', '<=', $request->to_date);
            // });

            if ($request->filled('durum')) {
                    $durum = $request->get('durum');
                     // 0, 1, 2, 3 değerlerini kabul et
                    if (in_array($durum, ['error', 'sent', 'draft'])) {
                        $data->where('faturaDurumu', $durum);
                       }
            }

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
            ->addColumn('odemeDurum', function($row){
                if($row->faturaDurumu == 'sent'){
                    return '<a class="t-link editInvoice" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editInvoiceModal"><div class="mobileTitle">Durum:</div><div style="color: green; display: inline-block;font-weight:700;">Gönderildi</div></a>';
                }elseif($row->faturaDurumu == 'draft'){
                    return '<a class="t-link editInvoice" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editInvoiceModal"><div class="mobileTitle">Durum:</div><div style="color: #216dfd; display: inline-block;font-weight:700;">Beklemede</div></a>';
                }elseif($row->faturaDurumu == 'error'){
                    return '<a class="t-link editInvoice" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editInvoiceModal"><div class="mobileTitle">Durum:</div><div style="color: red; display: inline-block;font-weight:700;">Gönderilmedi</div></a>';
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
            ->rawColumns(['id','faturaTarihi','faturaNumarasi','mid','genelToplam','odemeDurum','actions'])
            ->make(true);

        }

        return view('frontend.secure.invoices.all_invoices',compact('invoices','firma'));
    }
    // Helper Methods
    private function isDefaultInvoiceDateRange(Request $request): bool
    {
        if (!$request->filled('from_date') || !$request->filled('to_date')) {
            return false;
        }
        
        try {
            $from = Carbon::createFromFormat('Y-m-d', $request->from_date)->startOfDay();
            $to = Carbon::createFromFormat('Y-m-d', $request->to_date)->endOfDay();
            $defaultFrom = Carbon::today()->subDays(2)->startOfDay();
            $defaultTo = Carbon::today()->endOfDay();
            
            return $from->equalTo($defaultFrom) && $to->equalTo($defaultTo);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function applyMainInvoiceDateRange($query, Request $request): void
    {
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from = Carbon::createFromFormat('Y-m-d', $request->from_date)->startOfDay();
            $to   = Carbon::createFromFormat('Y-m-d', $request->to_date)->endOfDay();
            $query->whereBetween('faturaTarihi', [$from, $to]);
        }
    }

    public function searchMusteri(Request $request, $tenant_id)
    {
        $searchField = $request->input('musteriGetir');
        
        // İlişkili verilerle beraber getir
        $musteriler = Customer::where('firma_id', $tenant_id)
            ->where(function($query) use ($searchField) {
                $query->where('adSoyad', 'like', '%' . $searchField . '%')
                    ->orWhere('tel1', 'like', '%' . $searchField . '%');
            })
            ->with(['state', 'country']) // İl ve ilçe ilişkileri
            ->orderBy('adSoyad', 'ASC')
            ->get();
        
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
        $kdvKodlari = KdvKodu::orderBy('id', 'ASC')->get();
        $tevkifatKodlari = TevkifatKodu::orderBy('id', 'ASC')->get();
        return view('frontend.secure.invoices.add_invoices',compact('musteriler','payment_methods','firma','countries','tevkifatKodlari','kdvKodlari'));
    }

    public function musteriGetir(Request $request, $tenant_id)
    {
        $servisId = $request->input('servisId');
        $musteriAra = $request->input('musteriAra'); // YENİ: Müşteri arama

        // YENİ: Müşteri adına göre arama
        if ($musteriAra) {
            $veriler = DB::table('services')
                ->leftJoin('customers', 'services.musteri_id', '=', 'customers.id')
                ->leftJoin('device_brands', 'services.cihazMarka', '=', 'device_brands.id')
                ->leftJoin('device_types', 'services.cihazTur', '=', 'device_types.id')
                ->where('services.firma_id', $tenant_id)
                ->where('customers.adSoyad', 'LIKE', '%' . $musteriAra . '%')
                ->select(
                    'services.id as servis_id',
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
                    'device_types.cihaz',
                    'services.cihazAriza',
                    'services.kayitTarihi'
                )
                ->orderBy('services.id', 'DESC')
                ->limit(10)
                ->get();

            if ($veriler->count() > 0) {
                return response()->json([
                    'success' => true,
                    'data' => $veriler,
                    'type' => 'multiple' // Birden fazla sonuç
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu müşteriye ait servis bulunamadı'
                ], 200);
            }
        }

        // Servis ID'ye göre arama (mevcut kod)
        if ($servisId) {
            $veriler = DB::table('services')
                ->leftJoin('customers', 'services.musteri_id', '=', 'customers.id')
                ->leftJoin('device_brands', 'services.cihazMarka', '=', 'device_brands.id')
                ->leftJoin('device_types', 'services.cihazTur', '=', 'device_types.id')
                ->where('services.id', $servisId)
                ->where('services.firma_id', $tenant_id)
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
                ->first();

            if ($veriler) {
                return response()->json([
                    'success' => true,
                    'data' => $veriler,
                    'type' => 'single' // Tek sonuç
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Servis bulunamadı'
                ], 200);
            }
        }

        return response()->json(['error' => 'Servis ID veya müşteri adı eksik.'], 400);
    }

    public function StoreInvoice(Request $request, $tenant_id)
    {
        $token = $request->input('form_token');
        
        // Token boş mu kontrol et
        if (empty($token)) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz form token! Lütfen sayfayı yenileyin.'
            ], 403);
        }
        
        // Bu token daha önce kullanıldı mı kontrol et
        $cacheKey = 'invoice_form_token_' . $token;
        
        if (Cache::has($cacheKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Bu fatura zaten gönderildi! Lütfen bekleyin veya sayfayı yenileyin.'
            ], 429);
        }
        
        // Token'ı 10 dakika boyunca sakla
        Cache::put($cacheKey, true, now()->addMinutes(10));
        
        $validateData = $request->validate([
            'document' => 'max:2000',
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
        
        // Dosya türü ve boyut kontrolü
        if ($document) {
            $allowedExtensions = ['jpg', 'png', 'jpeg', 'pdf'];
            $extension = strtolower($document->getClientOriginalExtension());
            
            if (!in_array($extension, $allowedExtensions)) {
                return response()->json([
                    'success' => false,
                    'message' => "Geçersiz dosya türü: .{$extension}. Sadece JPG, JPEG, PNG ve PDF dosyaları kabul edilir."
                ], 422);
            }

            // Dosya boyutu kontrolü (5MB)
            if ($document->getSize() > 5120 * 1024) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dosya boyutu çok büyük. Maksimum 5MB olmalıdır.'
                ], 422);
            }
            
            $fileName = time() . '.' . $extension;  
            $save_url = $document->move('upload/uploads', $fileName);
        } else {
            $save_url = null;
        }
        
        $createdAt = Carbon::parse($request->faturaTarihi . ' ' . now()->format('H:i:s'));

        // ✅ Transaction başlat
        DB::beginTransaction();
        
        try {
            // Faturayı oluştur
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
                'faturaDurumu' => 'draft', 
                'tevkifatOrani' => $request->tevkifatOrani, 
                'tevkifatTutari' => $this->convertToDecimal($request->tevkifatTutari),
                'tevkifatKodu' => $request->tevkifatKodu,
                'kdvKodu' => $request->kdvKodu,
                'kdvAciklama' => $request->kdvAciklama,
                'faturaAciklama' => $request->faturaAciklama,
            ]);

            // Müşteri bilgisini al
            $customer = Customer::with(['country', 'state'])->find($request->mid);
            $customerName = $customer ? $customer->adSoyad : null;
            
            // Fatura oluşturma log kaydı
            ActivityLogger::logInvoiceCreated($invoice->id, $request->faturaNumarasi, $customerName);
            
            // Fatura müşterisi olarak işaretle
            Customer::where('id', $request->mid)->update(['faturaMusterisi' => '1']);

            // Ürünleri kaydet
            $aciklama = $request->aciklama;
            $miktar = $request->miktar;
            $fiyat = $request->fiyat;
            $tutar = $request->tutar;

            $invoiceProducts = [];
            foreach($aciklama as $key => $val) {
                if(!empty($val)) {
                    InvoiceProduct::create([
                        'firma_id' => $firma->id,
                        'faturaid' => $invoice->id,
                        'aciklama' => $val,
                        'miktar' => $miktar[$key],
                        'fiyat' => $this->convertToDecimal($fiyat[$key]),
                        'tutar' => $this->convertToDecimal($tutar[$key]),
                    ]);
                    
                    // Entegrasyon için de sakla
                    $invoiceProducts[] = [
                        'aciklama' => $val,
                        'miktar' => $miktar[$key],
                        'fiyat' => $this->convertToDecimal($fiyat[$key]),
                        'tutar' => $this->convertToDecimal($tutar[$key]),
                    ];
                }
            }

            // Transaction'ı commit et
            DB::commit();

            // ✅ PARAŞÜT ENTEGRASYONU - Senkron olarak gönder
            $integrationMessage = '';
            $integrationSuccess = false;
            
            if (InvoiceIntegrationFactory::hasIntegration($tenant_id)) {
                Log::info('Fatura entegrasyona gönderiliyor (senkron)', [
                    'invoice_id' => $invoice->id,
                    'tenant_id' => $tenant_id
                ]);
                
                try {
                    $integration = InvoiceIntegrationFactory::make($tenant_id);
                    
                    if ($integration) {
                        // Müşteri bilgilerini hazırla
                        $customerData = [
                            'adSoyad' => $customer->adSoyad,
                            'musteriTipi' => $customer->musteriTipi,
                            'email' => $customer->email ?? null,
                            'tel1' => $customer->tel1 ?? null,
                            'vergiNo' => $customer->vergiNo ?? null,
                            'vergiDairesi' => $customer->vergiDairesi ?? null,
                            'tcNo' => $customer->tcNo ?? null,
                            'adres' => $customer->adres ?? null,
                            'il' => $customer->country->name ?? null,
                            'ilce' => $customer->state->ilceName ?? null,
                        ];

                        // Fatura bilgilerini hazırla
                        $invoiceData = [
                            'id' => $invoice->id,
                            'faturaNumarasi' => $invoice->faturaNumarasi,
                            'faturaTarihi' => $invoice->faturaTarihi->format('Y-m-d'),
                            'indirim' => $invoice->indirim,
                            'odemeDurum' => $invoice->odemeDurum, 
                            'toplamTutar' => $invoice->genelToplam, 
                            'genelToplam' => $invoice->genelToplam, 
                            'kasaId' => $request->input('kasa') ?? 1, 
                            'customer' => $customerData,
                            'items' => $invoiceProducts,
                            'vat_rate' => $invoice->kdvKodu,
                            'vat_withholding_rate' => $invoice->tevkifatOrani,
                        ];

                        // Entegrasyona gönder
                        $result = $integration->createInvoice($invoiceData);

                        if ($result['success']) {
                            // Başarılı - Faturayı güncelle
                            $invoice->update([
                                'faturaDurumu' => 'sent',
                                'integration_invoice_id' => $result['invoice_id'],
                                'faturaPdf' => $result['pdf_path'] ?? $invoice->faturaPdf,
                                'integration_error' => null, // Önceki hatayı temizle
                            ]);
                            
                            $integrationSuccess = true;
                            $integrationMessage = ' Fatura Paraşüt\'e başarıyla gönderildi ve e-Arşiv PDF\'i oluşturuldu!';
                            
                            Log::info('Fatura entegrasyona başarıyla gönderildi', [
                                'invoice_id' => $invoice->id,
                                'integration_invoice_id' => $result['invoice_id'],
                                'pdf_path' => $result['pdf_path']
                            ]);
                        } else {
                            // Başarısız - Hata durumunu kaydet
                            $invoice->update([
                                'faturaDurumu' => 'error',
                                'integration_error' => $result['error'] ?? 'Bilinmeyen hata'
                            ]);
                            
                            $integrationMessage = ' UYARI: Fatura kaydedildi ancak Paraşüt\'e gönderilemedi: ' . ($result['error'] ?? 'Bilinmeyen hata');
                            
                            Log::error('Fatura entegrasyona gönderilemedi', [
                                'invoice_id' => $invoice->id,
                                'error' => $result['error'] ?? 'Bilinmeyen hata'
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    // Exception durumu
                    $invoice->update([
                        'faturaDurumu' => 'error',
                        'integration_error' => $e->getMessage()
                    ]);
                    
                    $integrationMessage = ' UYARI: Fatura kaydedildi ancak entegrasyon hatası oluştu: ' . $e->getMessage();
                    
                    Log::error('Entegrasyon exception', [
                        'invoice_id' => $invoice->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Storage warning kontrolü
            $storageWarning = '';
            if (session()->has('storage_warning_info')) {
                $storageInfo = session()->get('storage_warning_info');
                $storageWarning = " Dikkat: Storage alanınız %{$storageInfo['usage_percentage']} dolu. Kalan alan: {$storageInfo['remaining_formatted']}.";
            }

            // Başarı mesajı
            $finalMessage = 'Fatura başarıyla eklendi.' . $integrationMessage . $storageWarning;

            return response()->json([
                'success' => true,
                'message' => $finalMessage,
                'invoice_id' => $invoice->id,
                'integration_success' => $integrationSuccess,
                'integration_invoice_id' => $invoice->integration_invoice_id ?? null
            ]);

        } catch (\Exception $e) {
            // Hata durumunda transaction geri al
            DB::rollBack();
            
            Log::error('Fatura oluşturma hatası', [
                'tenant_id' => $tenant_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Fatura eklenemedi: ' . $e->getMessage()
            ], 500);
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
        $kdvKodlari = KdvKodu::orderBy('id', 'ASC')->get();
        $tevkifatKodlari = TevkifatKodu::orderBy('id', 'ASC')->get();
        return view('frontend.secure.invoices.edit_invoices',compact('invoice_id','musteri', 'musteriler','payment_methods','invoice_products', 'firma','countries','kdvKodlari','tevkifatKodlari'));

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

    public function testIntegration($tenant_id)
{
    try {
        $integration = InvoiceIntegrationFactory::make($tenant_id);
        
        if (!$integration) {
            return response()->json([
                'success' => false,
                'message' => 'Aktif entegrasyon bulunamadı'
            ]);
        }

        $testResult = $integration->testConnection();

        return response()->json([
            'success' => $testResult,
            'message' => $testResult ? 'Bağlantı başarılı' : 'Bağlantı başarısız'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Hata: ' . $e->getMessage()
        ], 500);
    }
}
}