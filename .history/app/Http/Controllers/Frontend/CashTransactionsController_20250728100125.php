<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CashTransaction;
use App\Models\Customer;
use App\Models\DeviceBrand;
use App\Models\DeviceType;
use App\Models\PaymentMethod;
use App\Models\PaymentType;
use App\Models\Service;
use App\Models\StockSupplier;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class CashTransactionsController extends Controller
{
    public function Filter(Request $request, $tenant_id)
    {   
        $firma = Tenant::where('id', $tenant_id)->first();
        if (!$firma) {
            return redirect()->route('giris')->with([
                'message' => 'Firma bulunamadı.',
                'alert-type' => 'danger',
            ]);
        }
        $cash_transactions = CashTransaction::with('perso_nel','islem_yapan','odemeturu','payment_method','servisler')->where('firma_id', $tenant_id)->get();
        $payment_types = PaymentType::orderBy('odemeTuru', 'ASC')->get();
        $payment_methods = PaymentMethod::orderBy('odemeSekli','asc')->get();
        $personel = User::where('tenant_id', $tenant_id)->whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['Bayi', 'Admin']);
        })->where('status', 1)->orderBy('name', 'asc')->get();
        $musteriler = Customer::where('firma_id', $tenant_id)->orderBy('adSoyad', 'ASC')->get();
        $bayiler = User::where('tenant_id', $tenant_id)->role('Bayi')->where('status', 1)->orderBy('name', 'asc')->get();
        $tedarikciler = StockSupplier::where('firma_id', $tenant_id)->get();
        $markalar = DeviceBrand::where('firma_id', $tenant_id)->get();
        $cihazlar = DeviceType::where('firma_id', $tenant_id)->get();

        if ($request->ajax()) {
            
            $data = CashTransaction::with('perso_nel','islem_yapan','odemeturu','payment_method','servisler');
            if ($request->filled('from_date') && $request->filled('to_date')) {
                $data->whereDate('created_at', '>=', $request->from_date)
                     ->whereDate('created_at', '<=', $request->to_date);
            }

            if($request->get('odemeSekil')){
                $data->where('odemeSekli', $request->get('odemeSekil'));
            }

            if($request->get('staff')){
                $data->where('personel', $request->get('staff'));
            }

            if($request->get('tedarikci')){
                $data->where('tedarikci', $request->get('tedarikci'));
            }

            if($request->get('marka')){
                $data->where('marka', $request->get('marka'));
            }

            if($request->get('cihaz')){
                $data->where('cihaz', $request->get('cihaz'));
            }

            if($request->get('odemeYonu')){
                $data->where('odemeYonu', $request->get('odemeYonu'));
            }

            // Sıralama işlemi
            if ($request->has('order')) {
                $order = $request->get('order')[0];
                $columns = $request->get('columns');
                $orderColumn = $columns[$order['column']]['data'];
                $orderDir = $order['dir'];
                
                if($orderColumn == 'odemeTuru'){
                    $data->leftJoin('payment_types', 'cash_transactions.odemeTuru', '=', 'payment_types.id')
                    ->addSelect(['cash_transactions.*', 'payment_types.odemeTuru as odemeType'])
                    ->orderBy('payment_types.odemeTuru',$orderDir);
                }
                else {
                    $data->orderBy($orderColumn, $orderDir);
                }
            } else {
                $data->orderBy('created_at','desc');
            }
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('id', function($row){
                 
                    return '<a class="t-link editCashTransactions address idWrap" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCashTransactionsModal">'.$row->id.'</a>';
                
                })
                ->addColumn('created_at', function($row){
                    $sontarih = Carbon::parse($row->created_at)->format('d/m/Y');
                    return '<a class="t-link editCashTransactions address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCashTransactionsModal"><div class="mobileTitle">Tarih:</div>'.$sontarih.'</a>';
                
                })
                 ->addColumn('pid', function($row){
                    return '<a class="t-link editCashTransactions " href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCashTransactionsModal"><div class="mobileTitle">Tarih:</div>'.$row->islem_yapan?->name.'</a>';
                
                })
                ->addColumn('odemeTuru', function($row){
                 
                    return '<a class="t-link editCashTransactions" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCashTransactionsModal"><div class="mobileTitle">Ö. Türü:</div>'.$row->odemeturu?->odemeTuru.'</a>';
                
                })
                ->addColumn('aciklama', function($row){
                    if(!is_null($row->servis)){
                        return '<a class="t-link editCashTransactions address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCashTransactionsModal"><div class="mobileTitle">Açıklama:</div>Servis No: '.$row->servisler?->id.'  ( '.$row->servisler?->asamalar?->asama.' )</a>';
                    }
                    elseif(!is_null($row->personel)){
                        return '<a class="t-link editCashTransactions address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCashTransactionsModal"><div class="mobileTitle">Açıklama:</div>'.$row->perso_nel?->name.' : '.$row->aciklama.'</a>';
                    }elseif(!is_null($row->stokIslem)){
                        return '<a class="t-link editCashTransactions address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCashTransactionsModal"><div class="mobileTitle">Açıklama:</div>Stok Id: '.$row->stok_hareket?->id.'  '.$row->stok_hareket?->stok?->urunAdi.'</a>';
                    }else{
                        return '<a class="t-link editCashTransactions address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCashTransactionsModal"><div class="mobileTitle">Açıklama:</div>'.$row->aciklama.'</a>';
                    }              
                })
                ->addColumn('odemeSekli', function($row){
                 
                    return '<a class="t-link editCashTransactions address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCashTransactionsModal"><div class="mobileTitle">Ö. Şekli:</div>'.$row->payment_method?->odemeSekli.'</a>';
                
                })
                
                ->addColumn('odemeYonuBorc', function($row) {
                    if ($row->odemeYonu == "1") {
                        return '<a class="t-link editCashTransactions address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCashTransactionsModal"><div class="mobileTitle">Borç(Gelen):</div><span style="color: green;font-weight:700;">+ '.number_format($row->fiyat, 2).' TL</span></a>';
                    } else {
                        return '';
                    }
                })
                ->addColumn('odemeYonuAlacak', function($row) {
                    if ($row->odemeYonu == "2") {
                        return '<a class="t-link editCashTransactions address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCashTransactionsModal"><div class="mobileTitle">Alacak(Giden):</div><span style="color: red;font-weight:700;">- '.number_format($row->fiyat, 2).' TL</span></a>';
                    } else {
                        return '';
                    }
                })
                ->addColumn('fiyat', function($row) {
                    $borc = 0;
                    $alacak = 0;
                    if ($row->odemeYonu == "1") {
                        $borc += $row->fiyat;
                    } else if($row->odemeYonu == "2") {
                        $alacak += $row->fiyat;
                    }
                    $bakiye = $borc - $alacak;
                    if($bakiye > 0){
                        return '<a class="t-link editCashTransactions address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCashTransactionsModal"><div class="mobileTitle">Bakiye(Toplam):</div><span style="color: green;font-weight:700;">+ '.number_format($bakiye, 2).' TL</span></a>';
                    }else {
                        return '<a class="t-link editCashTransactions address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCashTransactionsModal"><div class="mobileTitle">Bakiye(Toplam):</div><span style="color: red;font-weight:700;"> '.number_format($bakiye, 2).' TL</span></a>';
                    }

                })
                ->addColumn('action', function($row){
                    $deleteUrl = route('delete.cash.transaction', [$row->firma_id, $row->id]);

                    $editButton = '';
                    $deleteButton = '';

                    if(Auth::user()->can('Kasa Hareketi Düzenleyebilir')){
                        $editButton = '<a href="javascript:void(0);" data-bs-id="'.$row->id.'" class="btn btn-warning btn-sm editCashTransactions mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editCashTransactionsModal" title="Düzenle"><i class="fas fa-edit"></i> <span> Düzenle</span></a>';
                    }
                    if(Auth::user()->can('Kasa Hareketi Silebilir')){
                        $deleteButton = '<a href="'.$deleteUrl.'" class="btn btn-danger btn-sm mobilBtn" id="delete" title="Sil"><i class="fas fa-trash-alt"></i> <span> Sil</span></a>';
                    }
                    return  $editButton . '  ' . $deleteButton;
                })
                ->filter(function ($instance) use ($request){
                    if($request->get('odemeTuru')){
                        $instance->where('odemeTuru', $request->get('odemeTuru'));
                    }

                    if (!empty($request->get('search'))) {
                        $instance->where(function($w) use($request){
                           $search = $request->get('search');
                           $w->where('id', 'LIKE', "%$search%")
                           ->orWhereHas('servis', function($q) use($search) {
                            $q->where('id', 'LIKE', "%$search%");
                         });
                       });
                   }

                   
                })
                ->rawColumns(['id','created_at','pid','odemeTuru','aciklama','odemeSekli','odemeYonuBorc', 'odemeYonuAlacak','fiyat','action'])         
                ->make(true);  
        }
        
        return view('frontend.secure.cash_transactions.all_cash_transaction',compact('cash_transactions', 'payment_types', 'payment_methods' ,'personel','musteriler','firma','bayiler','tedarikciler','markalar','cihazlar'));
        
    }

    public function updateTotalValues(Request $request, $tenant_id)
    {  
        $data = CashTransaction::query()->where('firma_id', $tenant_id);
        
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $data->whereBetween('created_at', [
                $request->input('from_date'),
                $request->input('to_date')
            ]);
        }

        if ($request->filled('odemeTuru')) {
            $data->where('odemeTuru', $request->input('odemeTuru'));
        }

        if ($request->filled('odemeYonu')) {
            $data->where('odemeYonu', $request->input('odemeYonu'));
        }

        if($request->filled('musteri')){
            $data->where('musteri', $request->input('musteri'));
        }

        if($request->filled('odemeSekil')){
            $data->where('odemeSekli', $request->input('odemeSekil'));
        }

        if($request->filled('staff')){
            $data->where('personel', $request->input('staff'));
        }
        
        $filteredData = $data->get();
        
        $response = [
            'gelenNakitTL' => 0.00,
            'gelenHavaleTL' => 0.00,
            'gelenKartTL' => 0.00,
            'gidenNakitTL' => 0.00,
            'gidenHavaleTL' => 0.00,
            'gidenKartTL' => 0.00,
            'gelenToplamTL' => 0.00,
            'gidenToplamTL' => 0.00,
            'genelToplamTL' => 0.00,
        ];
        
        foreach ($filteredData as $item) {
            $fiyatTL = $item->fiyat;
            
            if ($item->odemeYonu == 1) {
                $response['gelenToplamTL'] += $fiyatTL;
                $response['gelenNakitTL'] += ($item->odemeSekli == 7) ? $fiyatTL : 0;
                $response['gelenHavaleTL'] += ($item->odemeSekli == 8) ? $fiyatTL : 0;
                $response['gelenKartTL'] += ($item->odemeSekli == 9) ? $fiyatTL : 0;
            } elseif ($item->odemeYonu == 2) {
                $response['gidenToplamTL'] += $fiyatTL;
                $response['gidenNakitTL'] += ($item->odemeSekli == 7) ? $fiyatTL : 0;
                $response['gidenHavaleTL'] += ($item->odemeSekli == 8) ? $fiyatTL : 0;
                $response['gidenKartTL'] += ($item->odemeSekli == 9) ? $fiyatTL : 0;
            }
        }

        $response['genelToplamTL'] = $response['gelenToplamTL'] - $response['gidenToplamTL'];
        
        foreach ($response as $key => $value) {
            if (strpos($key, 'TL') !== false) {
                $response[$key] = number_format($value, 2, ',', '.') . ' TL';
            }
        }
        return response()->json($response);
    }

    public function AddCashTransaction($tenant_id) {    
        $firma = Tenant::where('id', $tenant_id)->first();
        if (!$firma) {
            return redirect()->route('giris')->with([
                'message' => 'Firma bulunamadı.',
                'alert-type' => 'danger',
            ]);
        }   
        $payment_methods = PaymentMethod::orderBy('id','asc')->get();
        $payment_types = PaymentType::orderBy('odemeTuru','asc')->get();

        return view('frontend.secure.cash_transactions.add_cash_transaction', compact('payment_methods','payment_types','firma'));
    }

    public function GetCashPayment($tenant_id,$id){
        $firma = Tenant::where('id', $tenant_id)->first();
        $cash_payment_id = PaymentType::findOrFail($id);
        $ans = $cash_payment_id->cevaplar;
        
        $answers = explode(", ", $ans);
        $personeller = User::where('tenant_id', $tenant_id)->where('status',1)->get();
        $tedarikciler = StockSupplier::where('firma_id', $tenant_id)->orderBy('id','asc')->get();
        $markalar = DeviceBrand::where('firma_id', $tenant_id)->orderBy('marka', 'asc')->get();
        $cihazlar = DeviceType::where('firma_id', $tenant_id)->orderBy('cihaz','asc')->get();
        return view('frontend.secure.cash_transactions.get_cash_payments',compact('cash_payment_id','answers','personeller','tedarikciler','markalar','cihazlar'));
    }

    public function StoreCashTransaction(Request $request, $tenant_id){
        $firma = Tenant::where('id', $tenant_id)->first();
        $pid = Auth::user()->user_id;
        $odemeYonu = $request->odeme_durum;
        if($odemeYonu=="1"){
			$odemeDurum = $request->odeme_durum;
		}else{
			$odemeDurum = "1";
		}
        $createdAt = Carbon::parse($request->islemTarihi . ' ' . now()->format('H:i:s'));

        $fiyat = str_replace(',', '.', str_replace('.', '', $request->fiyat));

        if (!empty($request->servis)) {
            $servisVarMi = Service::where('id', $request->servis)->where('firma_id', $firma->id)
                ->where('durum', 1)
                ->exists();

            if (!$servisVarMi) {
            return response()->json(['error' => 'Geçersiz servis ID.'], 422);
        }
        }
        $response = CashTransaction::create([
            'firma_id' => $firma->id,
            'pid' => $pid,
            'created_at' => $createdAt,
            'odemeYonu' => $request->odeme_yonu,
            'odemeSekli' => $request->odeme_sekli,
            'odemeTuru' => $request->odeme_turu,
            'odemeDurum' => $odemeDurum,
            'fiyat' => $fiyat,
            'fiyatBirim' => $request->paraBirimi,
            'aciklama' => $request->aciklama,
            'personel' => $request->personeller,
            'servis' => $request->servis,
            'tedarikci' => $request->tedarikciler,
            'marka' => $request->markalar,
            'cihaz' => $request->cihazlar,
        ]);

        return response()->json($response);
    }
}
