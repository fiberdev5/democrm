<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuperAdminInvoice;
use App\Models\SuperAdminInvoiceProduct;
use App\Models\Tenant;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class SuperAdminInvoicesController extends Controller
{
    public function AllInvoice(Request $request) {
        $invoices = SuperAdminInvoice::where('durum', 1)->orderBy('id','desc')->get();
        $tenants = Tenant::where('status', 1)->orderBy('firma_adi', 'ASC')->get();

        if ($request->ajax()) {           
            $data = SuperAdminInvoice::with('tenant')->where('durum', '1');
            
            if ($request->get('firma')) {
                $firmaID = $request->get('firma');
                $data->where('firma_id', $firmaID);
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
                
                if($orderColumn == 'firma_id'){
                    $data->leftJoin('tenants', 'super_admin_invoices.firma_id', '=', 'tenants.id')
                    ->addSelect(['super_admin_invoices.*', 'tenants.firma_adi as firmaAdi'])
                    ->orderBy('tenants.firma_adi',$orderDir);
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
            ->addColumn('firma_id', function($row){
                return '<a class="t-link editInvoice address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editInvoiceModal"><span class="mobileTitle">Firma:</span><strong>'.$row->tenant?->firma_adi.'</strong><br><div style="font-size:12px;">'.$row->tenant?->telefon.'</div></a>';
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
                $deleteUrl = route('super.admin.invoices.delete', $row->id);
                $earsivButton = '<a href="'.asset($row->faturaPdf).'" target="_blank" class="btn btn-outline-primary btn-sm mobilBtn mbuton1" title="Faturayı görüntüle"><i class="far fa-eye"></i></a>';
                $editButton = '<a href="javascript:void(0);" data-bs-id="'.$row->id.'" class="btn btn-outline-warning btn-sm editInvoice mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editInvoiceModal" title="Düzenle"><i class="fas fa-edit"></i></a>';
                $deleteButton = '<a href="'.$deleteUrl.'" class="btn btn-outline-danger btn-sm mobilBtn" id="delete" title="Sil"><i class="fas fa-trash-alt"></i></a>';
                return $earsivButton. '  '.$editButton. '  '.$deleteButton;
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                       $search = $request->get('search');
                       $w->where('id', 'LIKE', "%$search%")
                       ->orWhere('faturaNumarasi', 'LIKE', "%$search%")
                       ->orWhereHas('tenant', function($q) use($search) {
                            $q->where('firma_adi', 'LIKE', "%$search%");
                        });
                   });
                }
            })
            ->rawColumns(['id','faturaTarihi','faturaNumarasi','firma_id','genelToplam','odemeDurum','actions'])
            ->make(true);
        }

        return view('frontend.secure.super_admin.invoices.all_invoices',compact('tenants','invoices'));
    }

    public function GetInvoices(Request $request)
    {  
        $data = SuperAdminInvoice::query();

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $start = Carbon::parse($request->input('from_date'))->startOfDay();
            $end = Carbon::parse($request->input('to_date'))->endOfDay();
            $data->whereBetween('faturaTarihi', [$start, $end]);
        }

        if($request->filled('firma')){
            $data->where('firma_id', $request->input('firma'));
        }
        
        $filteredData = $data->where('durum','1')->get();
        
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

    public function AddInvoice() {
        $tenants = Tenant::where('status', 1)->orderBy('firma_adi', 'ASC')->get();
        $payment_methods = PaymentMethod::whereNull('firma_id')->orderBy('id', 'asc')->get();
        
        return view('frontend.secure.super_admin.invoices.add_invoices',compact('tenants','payment_methods'));
    }

    public function StoreInvoice(Request $request){
        $validateData = $request->validate([
            'document'=> 'max:2000',
        ]);
        
        $document = $request->file('document');
        $extension = $request->file('document')->extension();
        if($extension != "jpg" && $extension != "png" && $extension != "jpeg" && $extension != "pdf"){
            $notification = array(
                'message' => ' Dosya uzantısı sadece jpg,png,jpeg veya pdf olmalı',
                'alert-type' => 'warning'
            );
            return redirect()->back()->with($notification);
        }
        $fileName = time().'.'.$document->getClientOriginalExtension();  
        $save_url = $document->move('upload/uploads', $fileName);

        $createdAt = Carbon::parse($request->faturaTarihi . ' ' . now()->format('H:i:s'));
        $invoice = SuperAdminInvoice::create([
            'firma_id' => $request->firma_id,
            'faturaNumarasi' => $request->faturaNumarasi,
            'faturaTarihi' => $createdAt,
            'odemeSekli' => $request->odemeSekli,
            'toplam' => str_replace(',', '.', str_replace('.', '', $request->toplam)),
            'indirim' => str_replace(',', '.', str_replace('.', '', $request->indirim)),
            'kdv' => str_replace(',', '.', str_replace('.', '', $request->kdv)),
            'kdvTutar' => $request->kdvTutar,
            'genelToplam' => str_replace(',', '.', str_replace('.', '', $request->genelToplam)),
            'toplamYazi' => $request->toplamYazi,
            'kayitAlan' => auth()->user()->id,
            'faturaPdf' => $save_url,
        ]);

        $invoice_id = $invoice->id;
        
        if($invoice){
            // Ürünleri kaydet
            $aciklama = $request->aciklama;
            $miktar = $request->miktar;
            $fiyat = str_replace(',', '.', str_replace('.', '', $request->fiyat));
            $tutar = str_replace(',', '.', str_replace('.', '', $request->tutar));

            foreach($aciklama as $key => $val){
                if(!empty($val)){
                    SuperAdminInvoiceProduct::insert([
                        'faturaid' => $invoice_id,
                        'aciklama' => $val,
                        'miktar' => $miktar[$key],
                        'fiyat' => $fiyat[$key],
                        'tutar' => $tutar[$key],
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

    public function EditInvoice($id) {
        $invoice_id = SuperAdminInvoice::findOrFail($id);
        $tenants = Tenant::where('status', 1)->orderBy('firma_adi', 'ASC')->get();
        $payment_methods = PaymentMethod::whereNull('firma_id')->orderBy('id', 'asc')->get();
        $invoice_products = SuperAdminInvoiceProduct::where('faturaid',$id)->get();
        
        return view('super_admin.invoices.edit_invoices',compact('invoice_id','tenants','payment_methods','invoice_products'));
    }

    private function convertToDecimal($value)
    {
        if (strpos($value, ',') !== false) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        }
        return $value;
    }

    public function UpdateInvoice(Request $request) {
        $invoice_id = $request->id;
        $createdAt = Carbon::parse($request->faturaTarihi . ' ' . now()->format('H:i:s'));

        $invoice = SuperAdminInvoice::findOrFail($invoice_id);
        $invoice->firma_id = $request->firma_id;
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

        $oldProducts = SuperAdminInvoiceProduct::where('faturaid', $invoice_id)->get();
        foreach($oldProducts as $product){
            SuperAdminInvoiceProduct::findOrFail($product->id)->delete();
        }

        // Yeni ürünleri ekle
        $aciklama = $request->aciklama;
        $miktar = $request->miktar;
        $fiyat = $request->fiyat;
        $tutar = $request->tutar;

        foreach ($aciklama as $key => $val) {
            if (!empty($val)) {
                SuperAdminInvoiceProduct::create([
                    'faturaid' => $invoice_id,
                    'aciklama' => $val,
                    'miktar' => $miktar[$key],
                    'fiyat' => $fiyat[$key],
                    'tutar' => $tutar[$key],
                ]);
            }
        }
        
        $notification = array(
            'message' => 'Fatura Bilgileri Başarıyla Güncellendi',
            'alert-type' => 'success'
        );
        return response()->json(['success' => $notification]);
    }

    public function DeleteInvoice($id) {
        $fatura = SuperAdminInvoice::findOrFail($id);

        $eskiUrunler = SuperAdminInvoiceProduct::where('faturaid', $id)->get();
        foreach ($eskiUrunler as $urun) {
            $urun->delete();
        }

        $fatura->delete();

        $notification = [
            'message' => 'Fatura Başarıyla Silindi',
            'alert-type' => 'success'
        ];

        return redirect()->route('super.admin.invoices')->with($notification);
    }

    public function ShowInvoice($id) {
        $invoice_id = SuperAdminInvoice::findOrFail($id);
        return view('super_admin.invoices.show_invoices',compact('invoice_id'));
    }

    public function UploadInvoice(Request $request) {
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
            SuperAdminInvoice::find($invoice_id)->update([
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

    public function DeleteEinvoice($id){
        $invoice_id = SuperAdminInvoice::findOrFail($id);
        
        SuperAdminInvoice::findOrFail($id)->update([
            'faturaPdf' => null,
        ]);

        $notification = array(
            'message' => 'Fatura başarıyla silindi',
            'alert-type' => 'success',
        );
        return response()->json(true);
    }
}