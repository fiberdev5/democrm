<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Il;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class InvoicesController extends Controller
{
    public function AllInvoice(Request $request, $tenant_id) {
        $invoices = Invoice::where('firma_id',$tenant_id)->where('durum', 1)->orderBy('id','desc')->get();
        $musteriler = Customer::where('firma_id', $tenant_id)->orderBy('adSoyad', 'ASC')->get();
        $firma = Tenant::where('id', $tenant_id)->first();

        if ($request->ajax()) {           
            $data = Invoice::with('customer')->where('durum',1);
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
            ->addColumn('odemeDurum', function($row){
                if($row->faturaDurumu == 'sent'){
                    return '<a class="t-link editInvoice" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editInvoiceModal"><div class="mobileTitle">Durum:</div><div style="color: green; display: inline-block;font-weight:700;">Gönderildi</div></a>';
                }elseif($row->faturaDurumu == 'draft'){
                    return '<a class="t-link editInvoice" href="javascript:void(0);" data-bs-id="{{$item->id}}" data-bs-toggle="modal" data-bs-target="#editInvoiceModal"><div class="mobileTitle">Durum:</div><div style="color: red; display: inline-block;font-weight:700;">Beklemede</div></a>';
                }
            })
            ->addColumn('actions', function($row){
                $deleteUrl = route('delete.invoices', [$row->firma_id,$row->id]);
                $earsivButton = '<a href="'.$row->faturaPdf.'" target="_blank" class="btn btn-primary btn-sm mobilBtn mbuton1" title="Faturayı görüntüle"><i class="far fa-eye"></i></a>';
                $editButton = '';
                $deleteButton = '';
                $editButton = '<a href="javascript:void(0);" data-bs-id="'.$row->id.'" class="btn btn-warning btn-sm editInvoice mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editInvoiceModal" title="Düzenle"><i class="fas fa-edit"></i></a>';
                $deleteButton = '<a href="'.$deleteUrl.'" class="btn btn-danger btn-sm mobilBtn" id="delete" title="Sil"><i class="fas fa-trash-alt"></i></a>';
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

        return view('frontend.secure.invoices.all_invoices',compact('musteriler','invoices','firma'));
    }

    public function GetInvoices(Request $request, $tenant_id)
    {  
        $data = Invoice::query();
        
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $data->whereBetween('faturaTarihi', [
                $request->input('from_date'),
                $request->input('to_date')
            ]);
        }

        if($request->filled('musteri')){
            $data->where('mid', $request->input('musteri'));
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
        
        $filteredData = $data->where('firma_id',$tenant_id)->where('durum',1)->get();
        
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
            
            if ($item->odemeSekli == 7) {
                $response['toplamNakitTL'] += $item->toplam;
                $response['kdvNakitTL'] += $item->kdv;
                $response['genelNakitTL'] += $item->genelToplam;
            } elseif ($item->odemeSekli == 8) {
                $response['toplamHavaleTL'] += $item->toplam;
                $response['kdvHavaleTL'] += $item->kdv;
                $response['genelHavaleTL'] += $item->genelToplam;
            } elseif ($item->odemeSekli == 9) {
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
        $payment_methods = PaymentMethod::where('firma_id', $tenant_id)->get();
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
            ->where('firma_id',$tenant_id)
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

        return response()->json($veriler, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
