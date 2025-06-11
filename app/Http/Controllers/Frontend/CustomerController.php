<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Il;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class CustomerController extends Controller
{
    public function AllCustomer($tenant_id, Request $request) {
        // Firma bilgisi
        $firma = Tenant::where('id', $tenant_id)->first();
        if (!$firma) {
            return redirect()->route('giris')->with([
                'message' => 'Firma bulunamadı.',
                'alert-type' => 'danger',
            ]);
        }
        
        $countries = Il::orderBy('name', 'ASC')->get();
        
        $customers = Customer::where('firma_id', $firma->id)->get();

        if ($request->ajax()) {           
            $data = Customer::query();
    
            if ($request->filled('tip')) {
                if ($request->get('tip') == 1) {
                    $data->where('musteriTipi', 1);
                } elseif ($request->get('tip') == 2) {
                    $data->where('musteriTipi', 2);
                } elseif ($request->get('tip') == 2) {                
                }
            }
          
            if ($request->get('il')) {
                $data->where('il', $request->get('il'));
            }

            if ($request->get('ilce')) {
                $data->where('ilce', $request->get('ilce'));
            }

            // Sıralama işlemi
            if ($request->has('order')) {
                $order = $request->get('order')[0];
                $columns = $request->get('columns');
                $orderColumn = $columns[$order['column']]['data'];
                $orderDir = $order['dir'];
                $data->where('firma_id', $firma->id)->orderBy($orderColumn, $orderDir);
            } else {
                $data->where('firma_id', $firma->id)->orderBy('id','desc');
            }
          
            
            $filteredData = $data;
    
            return DataTables::of($filteredData)
                ->addIndexColumn()
                ->addColumn('id', function($row){  
                    return '<a class="t-link editCustomer address idWrap" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCustomerModal">'.$row->id.'</a>'; 
                })
                ->addColumn('name', function($row){
                    return '<a class="t-link editCustomer address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCustomerModal"><div class="mobileTitle">Ad Soyad:</div>'.$row->adSoyad.'</a>';     
                })
                ->addColumn('tel', function($row){     
                    $telefon = $row->tel1;

                    // Eğer telefon numarası başında 0 yoksa ekle
                    if (substr($telefon, 0, 1) !== '0') {
                        $telefon = '0' . $telefon;
                    }
                    return '<a class="t-link editCustomer" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCustomerModal"><div class="mobileTitle">Telefon:</div>'.$telefon.'</div></a>';
                })
                ->addColumn('address', function($row){  
                    $address = (!empty($row->country->name) && !empty($row->state->ilceName)) 
                    ? $row->adres . '  ' .$row->country->name . ' / ' . $row->state->ilceName 
                    : '';
              
                    return '<a class="t-link editCustomer address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCustomerModal"><div class="mobileTitle">Adres:</div>'.$address.'</div></a>';
                })
                ->addColumn('action', function($row){
                    $deleteUrl = route('delete.customer', [$row->firma_id,$row->id]);
                    $editButton = '';
                    $deleteButton = '';
                    $editButton = '<a href="javascript:void(0);" data-bs-id="'.$row->id.'" class="btn btn-warning btn-sm editCustomer mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editCustomerModal" title="Düzenle"><i class="fas fa-edit"></i></a>';
                   
                    $deleteButton = '<a href="'.$deleteUrl.'" class="btn btn-danger btn-sm mobilBtn" id="delete" title="Sil"><i class="fas fa-trash-alt"></i></a>';
                    
                    return $editButton. ' ' .$deleteButton;
                })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function($w) use($request){
                           $search = $request->get('search');
                           $w->where('adSoyad', 'LIKE', "%$search%");                        
                        });
                    }
                })
                ->rawColumns(['id','name','tel','address','action'])
                ->make(true);                      
            }
        return view('frontend.secure.customers.all_customers',compact('firma','customers','countries'));
    }

    public function AddCustomer($tenant_id) {
        $countries = Il::orderBy('name', 'ASC')->get();
        $firma = Tenant::where('id', $tenant_id)->first();
        return view('frontend.secure.customers.add_customer', compact('countries','firma'));
    }

    public function StoreCustomer($tenant_id, Request $request) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }
        $user_id = Auth::user()->user_id;
        Customer::create([
            'firma_id' => $tenant_id,
            'personel_id' => $user_id,
            'musteriTipi' => $request->mTipi,
            'adSoyad' => $request->name,
            'tel1' => $request->tel1,
            'tel2' => $request->tel2,
            'il' => $request->il,
            'ilce' => $request->ilce,
            'adres' => $request->address,
            'tcNo' => $request->tcNo,
            'vergiNo' => $request->vergiNo,
            'vergiDairesi' => $request->vergiDairesi,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Müşteri başarıyla eklendi.',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function EditCustomer($tenant_id, $id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı!',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }

        $customer = Customer::findOrFail($id);
        if(!$customer) {
            $notification = array(
                'message' => 'Müşteri bulunamadı!',
                'alert-type' => 'danger'
            );
            return redirect()->back()->with($notification);
        }

        $countries = Il::orderBy('name','asc')->get();
        return view('frontend.secure.customers.edit_customer', compact('customer','countries','firma'));

    }

    public function UpdateCustomer($tenant_id, $id, Request $request){
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı!',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }

        $customer = Customer::findOrFail($id);
        if(!$customer) {
            $notification = array(
                'message' => 'Müşteri bulunamadı!',
                'alert-type' => 'danger'
            );
            return redirect()->back()->with($notification);
        }
        $user_id = Auth::user()->user_id;
        Customer::findOrFail($customer->id)->update([
            'personel_id' => $user_id,
            'musteriTipi' => $request->mTipi,
            'adSoyad' => $request->name,
            'tel1' => $request->tel1,
            'tel2' => $request->tel2,
            'il' => $request->il,
            'ilce' => $request->ilce,
            'adres' => $request->address,
            'tcNo' => $request->tcno,
            'vergiNo' => $request->vergiNo,
            'vergiDairesi' => $request->vergiDairesi,
            'created_at' => $request->kayitTarihi,
        ]);

        $customer = Customer::findOrFail($customer->id);
        return response()->json([
            'message' => 'Müşteri bilgileri başarıyla güncellendi.',
            'customer' => $customer
        ]);
    }

    public function CustomerServices($tenant_id,$id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı!',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }

        $customer = Customer::findOrFail($id);
        if(!$customer) {
            $notification = array(
                'message' => 'Müşteri bulunamadı!',
                'alert-type' => 'danger'
            );
            return redirect()->back()->with($notification);
        }

        return view('frontend.secure.customers.customer_services');
    }

    public function DeleteCustomer($tenant_id, $id) {
        $customer = Customer::findOrFail($id);
        if(is_null($customer)) {
            $notification = array(
                'message' => 'Müşteriyi silemezsiniz!',
                'alert-type' => 'danger'
            );
            return redirect()->back()->with($notification);
        }
        else {
            $customer->delete();

            $notification = array(
                'message' => 'Müşteri başarıyla silindi.',
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        }
    }
}
