<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Yajra\DataTables\DataTables;
use App\Models\Il; // İl modelini ekledik

class SuperAdminController extends Controller
{
       public function index(Request $request)
    {
        $countries = Il::orderBy('name', 'ASC')->get(); // Tüm illeri al
        
        if ($request->ajax()) {
            // Sadece super admin'e tüm firmaları gösteriyoruz, bu yüzden tenant_id filtresi yok.
            $data = Tenant::with('ils', 'ilces');

            if ($request->filled('tip')) {
                if ($request->get('tip') == 1) {
                    $data->where('musteriTipi', 1);
                } elseif ($request->get('tip') == 2) {
                    $data->where('musteriTipi', 2);
                }
            }
          
            if ($request->get('il')) {
                $data->where('il', $request->get('il'));
            }

            if ($request->get('ilce')) {
                $data->where('ilce', $request->get('ilce'));
            }

            if ($request->has('order')) {
                $order = $request->get('order')[0];
                $columns = $request->get('columns');
                $orderColumn = $columns[$order['column']]['data'];
                $orderDir = $order['dir'];
                $data->orderBy($orderColumn, $orderDir);
            } else {
                $data->orderBy('id','desc');
            }
          
            return DataTables::of($data) // $filteredData yerine $data kullanıldı
                ->addIndexColumn()
                ->addColumn('id', function($row){  
                    return '<a class="t-link editTenant address idWrap" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editTenantModal">'.$row->id.'</a>'; 
                })
                ->addColumn('name', function($row){
                    return '<a class="t-link editTenant address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editTenantModal"><div class="mobileTitle">Ad Soyad:</div>'.$row->firma_adi.'</a>';     
                })
                ->addColumn('tel', function($row){     
                    $telefon = $row->tel1;

                    if (substr($telefon, 0, 1) !== '0') {
                        $telefon = '0' . $telefon;
                    }
                    return '<a class="t-link editTenant" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editTenantModal"><div class="mobileTitle">Telefon:</div>'.$telefon.'</div></a>';
                })
                ->addColumn('address', function($row){  
                    $address = (!empty($row->ils->name) && !empty($row->ilces->ilceName)) 
                    ? $row->adres . '  ' .$row->ils->name . ' / ' . $row->ilces->ilceName 
                    : '';
              
                    return '<a class="t-link editTenant address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editTenantModal"><div class="mobileTitle">Adres:</div>'.$address.'</div></a>';
                })
                ->addColumn('durum', function($row) {
                    $statusBadge = $row->status == 1
                        ? '<span class="badge bg-success" style="background-color: rgb(59 131 77) !important;"><i class="mdi mdi-check-circle"></i> Aktif</span>'
                        : '<span class="badge bg-danger"><i class="mdi mdi-close-circle"></i> Pasif</span>';

                    return '<a class="t-link editTenant" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editTenantModal"><div class="mobileTitle">Durum:</div>'.$statusBadge.'</a>';
                })
               ->addColumn('action', function($row){
                    $deleteUrl = route('delete.tenant', [$row->id, $row->id]); // Bu rota Super Admin için güncellenmeli
                    
                    $editButton = '<a href="javascript:void(0);" data-bs-id="'.$row->id.'" class="btn btn-warning btn-sm editTenant mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editTenantModal" title="Düzenle"><i class="fas fa-edit"></i></a>';
                    
                    $deleteButton = '<a href="'.$deleteUrl.'" class="btn btn-danger btn-sm mobilBtn" id="delete" title="Pasif Yap"><i class="fas fa-toggle-off"></i></a>'; // Pasif yapma olarak değiştirildi
                    
                    $usersButton = '<button class="btn btn-primary btn-sm mobilBtn view-tenant-users me-1" data-tenant-id="'.$row->id.'" title="Kullanıcıları Görüntüle"><i class="fas fa-users"></i></button>';
                    
                    // Impersonation butonu - herhangi bir kullanıcıya giriş için
                    $impersonateButton = '';
                    if ($row->status == 1) { // Sadece aktif firmalar için impersonate
                        $impersonateButton = '<button class="btn btn-success btn-sm mobilBtn impersonate-any-user me-1" 
                                                    data-tenant-id="'.$row->id.'" 
                                                    data-company-name="'.$row->firma_adi.'"
                                                    title="Firma Kullanıcısı Olarak Giriş Yap">
                                                <i class="fas fa-user-secret"></i>
                                            </button>';
                    }
                    
                    return '<div class="d-flex gap-1">' . $editButton . $usersButton . $impersonateButton . $deleteButton . '</div>';
                })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search')['value'])) { // Arama kutusu değeri
                        $search = $request->get('search')['value'];
                        $instance->where('firma_adi', 'LIKE', "%$search%");                        
                    }
                })
                ->rawColumns(['id','name','tel','address','durum','action'])
                ->make(true);                      
        }

        return view('frontend.secure.tenants.all_tenants', compact('countries')); // Yeni view dosyası
    }

    // Diğer tenant yönetimi metodları buraya eklenebilir (create, store, edit, update, destroy)
    // Şimdilik sadece index metodunu ve dataTable entegrasyonunu yaptık.
}


