<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Il;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TenantsController extends Controller
{
    public function AllTenants($tenant_id, Request $request) {
        // Firma bilgisi
        $firma = Tenant::where('id', $tenant_id)->first();
        if (!$firma) {
            return redirect()->route('giris')->with([
                'message' => 'Firma bulunamadı.',
                'alert-type' => 'danger',
            ]);
        }
        
        $countries = Il::orderBy('name', 'ASC')->get();

        if ($request->ajax()) {           
            $data = Tenant::query();
    
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
                $data->where('id', $firma->id)->orderBy($orderColumn, $orderDir);
            } else {
                $data->where('id', $firma->id)->orderBy('id','desc');
            }
          
            
            $filteredData = $data;
    
            return DataTables::of($filteredData)
                ->addIndexColumn()
                ->addColumn('id', function($row){  
                    return '<a class="t-link editCustomer address idWrap" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCustomerModal">'.$row->id.'</a>'; 
                })
                ->addColumn('name', function($row){
                    return '<a class="t-link editCustomer address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCustomerModal"><div class="mobileTitle">Ad Soyad:</div>'.$row->firma_adi.'</a>';     
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
                    $deleteUrl = route('delete.tenant', [$row->id,$row->id]);
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
                           $w->where('firma_adi', 'LIKE', "%$search%");                        
                        });
                    }
                })
                ->rawColumns(['id','name','tel','address','action'])
                ->make(true);                      
            }
        return view('frontend.secure.tenants.all_tenants',compact('firma','countries'));
    }
}
