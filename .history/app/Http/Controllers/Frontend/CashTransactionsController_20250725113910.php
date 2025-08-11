<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CashTransaction;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\PaymentType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class CashTransactionsController extends Controller
{
    public function Filter(Request $request, $tenant_id)
    {   $cash_transactions = CashTransaction::with('perso_nel','islem_yapan','odemeturu','payment_method','servisler')->where('firma_id', $tenant_id)->get();
        $payment_types = PaymentType::orderBy('odemeTuru', 'ASC')->get();
        $payment_methods = PaymentMethod::orderBy('odemeSekli','asc')->get();
        $personel = User::where('tenant_id', $tenant_id)->where('status', 1)->orderBy('name', 'asc')->get();
        $musteriler = Customer::where('firma_id', $tenant_id)->orderBy('adSoyad', 'ASC')->get();
        if ($request->ajax()) {
            
            $data = CashTransaction::with('perso_nel','islem_yapan','odemeturu','payment_method','servisler');
            if ($request->filled('from_date') && $request->filled('to_date')) {
                $data->whereDate('islemTarihi', '>=', $request->from_date)
                     ->whereDate('islemTarihi', '<=', $request->to_date);
            }

            if($request->get('odemeSekil')){
                $data->where('odemeSekli', $request->get('odemeSekil'));
            }

            if($request->get('staff')){
                $data->where('personel', $request->get('staff'));
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
                ->addColumn('islemTarihi', function($row){
                 
                    return '<a class="t-link editCashTransactions address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCashTransactionsModal"><div class="mobileTitle">Tarih:</div>'.\Carbon\Carbon::parse($row->created_at)->format('d/m/Y').'</a>';
                
                })
                ->addColumn('odemeTuru', function($row){
                 
                    return '<a class="t-link editCashTransactions" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCashTransactionsModal"><div class="mobileTitle">Ö. Türü:</div>'.$row->odemeturu?->odemeTuru.'</a>';
                
                })
                ->addColumn('aciklama', function($row){
                    if(!is_null($row->servis)){
                        return '<a class="t-link editCashTransactions address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCashTransactionsModal"><div class="mobileTitle">Açıklama:</div>'.$row->servis?->id.' : '.$row->aciklama.'</a>';
                    }
                    elseif(!is_null($row->personel)){
                        return '<a class="t-link editCashTransactions address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCashTransactionsModal"><div class="mobileTitle">Açıklama:</div>'.$row->perso_nel?->name.' : '.$row->aciklama.'</a>';
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
                    $deleteUrl = route('delete.cash.transaction', $row->id);
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
                ->rawColumns(['id','islemTarihi','odemeTuru','aciklama','odemeSekli','odemeYonuBorc', 'odemeYonuAlacak','fiyat','action'])         
                ->make(true);  
        }
        
        return view('frontend.secure.cash_transactions.all_cash_transaction',compact('cash_transactions', 'payment_types', 'payment_methods' ,'personel','musteriler'));
        
    }
}
