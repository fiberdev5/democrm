<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class PersonelController extends Controller
{
    public function AllStaffs($tenant_id, Request $request) {
        
        // Kullanıcı oturum kontrolü
        if (!Auth::check()) {
            return redirect()->route('giris')->with('error', 'Lütfen giriş yapınız.');
        }
        $user = Auth::user();
        // Kullanıcının tenant bilgisi kontrolü
        if ($tenant_id == null || $user->tenant->id != $tenant_id) {
            return redirect()->route('giris')->with([
                'message' => 'Personellere erişiminiz yoktur.',
                'alert-type' => 'danger',
            ]);
        }
        // Firma bilgisi
        $firma = Tenant::where('id', $tenant_id)->first();
        if (!$firma) {
            return redirect()->route('giris')->with([
                'message' => 'Firma bulunamadı.',
                'alert-type' => 'danger',
            ]);
        }
        // Firma personelleri
        $staffs = User::where('tenant_id', $firma->id)->get();

        $roles = Role::where('name','!=', 'Admin')->get();
        if ($request->ajax()) {       
            
            $data = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'Bayi');
            });    
            // $data = User::query();  //personeller içinde bayileri de listeliyordu
            if ($request->filled('durum')) {
                if ($request->get('durum') == 1) {
                    $data->where('status', 1);
                } elseif ($request->get('durum') == 0) {
                    $data->where('status', 0);
                } elseif ($request->get('durum') == 2) {                
                }
            }
          
            if ($request->get('grup')) {
                $data->whereHas('roles', function ($query) use ($request) {
                    $query->where('id', $request->grup);
                });
            }

            // Sıralama işlemi
            if ($request->has('order')) {
                $order = $request->get('order')[0];
                $columns = $request->get('columns');
                $orderColumn = $columns[$order['column']]['data'];
                $orderDir = $order['dir'];
                // $data->where('tenant_id', $firma->id)->orderBy($orderColumn, $orderDir);
                $data->where('tenant_id', $firma->id)
                ->whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'Bayi');
                })
                ->orderBy($orderColumn, $orderDir);
            } else {
                // $data->where('tenant_id', $firma->id)->orderBy('user_id','desc');
                                $data->where('tenant_id', $firma->id)
                ->whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'Bayi');
                })
                ->orderBy('user_id','desc');
            }
          
            
            $filteredData = $data;
    
            return DataTables::of($filteredData)
                ->addIndexColumn()
                ->addColumn('user_id', function($row){  
                    return '<a class="t-link editPersonel address idWrap" href="javascript:void(0);" data-bs-id="'.$row->user_id.'" data-bs-toggle="modal" data-bs-target="#editPersonelModal">'.$row->user_id.'</a>'; 
                })
                ->addColumn('name', function($row){
                    return '<a class="t-link editPersonel address" href="javascript:void(0);" data-bs-id="'.$row->user_id.'" data-bs-toggle="modal" data-bs-target="#editPersonelModal"><div class="mobileTitle">Personel Adı:</div>'.$row->name.'</a>';     
                })
                ->addColumn('grup', function($row){
                    foreach($row->roles as $role){
                        return '<a class="t-link editPersonel" href="javascript:void(0);" data-bs-id="'.$row->user_id.'" data-bs-toggle="modal" data-bs-target="#editPersonelModal"><div class="mobileTitle">P. Grubu:</div><span class="badge badge-pill bg-danger">'.$role->name.'</span></div></a>';
                    }          
                })
                ->addColumn('tel', function($row){     
                    $telefon = $row->tel;

                    // Eğer telefon numarası başında 0 yoksa ekle
                    if (substr($telefon, 0, 1) !== '0') {
                        $telefon = '0' . $telefon;
                    }
                    return '<a class="t-link editPersonel" href="javascript:void(0);" data-bs-id="'.$row->user_id.'" data-bs-toggle="modal" data-bs-target="#editPersonelModal"><div class="mobileTitle">Telefon:</div>'.$telefon.'</div></a>';
                })
                ->addColumn('address', function($row){  
                    $address = (!empty($row->country->name) && !empty($row->state->ilceName)) 
                    ? $row->country->name . ' - ' . $row->state->ilceName 
                    : '';
              
                    return '<a class="t-link editPersonel address" href="javascript:void(0);" data-bs-id="'.$row->user_id.'" data-bs-toggle="modal" data-bs-target="#editPersonelModal"><div class="mobileTitle">Adres:</div>'.$address.'</div></a>';
                })
                ->addColumn('status', function($row){
                    if($row->status == 1){
                        return '<a class="t-link editPersonel" href="javascript:void(0);" data-bs-id="'.$row->user_id.'" data-bs-toggle="modal" data-bs-target="#editPersonelModal"><div class="mobileTitle">Durum:</div><div style="color: green; display: inline-block;font-weight:700;">Çalışıyor</div></div></a>';
                    }else{
                        return '<a class="t-link editPersonel" href="javascript:void(0);" data-bs-id="'.$row->user_id.'" data-bs-toggle="modal" data-bs-target="#editPersonelModal"><div class="mobileTitle">Durum:</div><div style="color: red; display: inline-block;font-weight:700;">Ayrıldı</div></div></a>';
                    }
                })
                ->addColumn('action', function($row){
                    $deleteUrl = route('delete.personel', [$row->tenant_id,$row->user_id]);
                    $editButton = '';
                    $deleteButton = '';
                    $editButton = '<a href="javascript:void(0);" data-bs-id="'.$row->user_id.'" class="btn btn-warning btn-sm editPersonel mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editPersonelModal" title="Düzenle"><i class="fas fa-edit"></i></a>';
                   
                    $deleteButton = '<a href="'.$deleteUrl.'" class="btn btn-danger btn-sm mobilBtn" id="delete" title="Sil"><i class="fas fa-trash-alt"></i></a>';
                    
                    return $editButton. ' ' .$deleteButton;
                })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function($w) use($request){
                           $search = $request->get('search');
                           $w->where('name', 'LIKE', "%$search%");                        
                        });
                    }
                })
                ->rawColumns(['user_id','name','grup','tel','address','status','action'])
                ->make(true);                      
            }
        return view('frontend.secure.staffs.all_staffs', compact('staffs','firma','roles'));
    }

    public function AddStaff($tenant_id) {
        $roles= Role::where('name','!=', 'Admin')->get();
        $firma = Tenant::where('id', $tenant_id)->first();
        $countries = DB::table('ils')->orderBy('name', 'ASC')->get();
        return view('frontend.secure.staffs.add_staff',compact('roles','firma','countries'));
    }
   

    protected function generateUserEmail($userEmail, $domain)
    {
        $username = explode('@', $userEmail)[0]; // E-postanın kullanıcı adını alır
        return $username . '@' . $domain; // Kullanıcı adı ve firma domainiyle yeni e-posta oluşturur
    }

    public function StoreStaff(Request $request, $tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();

        if (!$firma) {
            return redirect()->route('giris')->with([
                'message' => 'Firma bulunamadı.',
                'alert-type' => 'danger',
            ]);
        }
        $username = Str::slug($request->username, '-');
        $user = new User();
        $user->tenant_id = $firma->id;
        $user->username = $request->username;
        $user->eposta = $this->generateUserEmail($username, $firma->username);
        $user->baslamaTarihi = $request->baslamaTarihi;
        $user->name = $request->name;
        $user->tel = $request->tel;
        $user->il = $request->il;
        $user->ilce = $request->ilce;
        $user->address = $request->address;
        $user->password = Hash::make($request->password);
        $user->status = 1;
        $user->save();

        if($request->roles){
            $role = Role::findById($request->roles); // ID'yi kullanarak rolü al
            $user->assignRole($role->name);
        }

        $notification = array(
            'message' => 'Personel kaydı başarıyla yapıldı.',
            'alert-type' => 'success'
        );

        return redirect()->route('staffs',$tenant_id)->with($notification);
    }

    public function EditStaff($tenant_id,$id) {
        $firma = Tenant::where('id', $tenant_id)->first();

        if (!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı.',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }
        $staff = User::findOrFail($id);
        if(!$staff){
            $notification = array(
                'message' => 'Personel bulunamadı.',
                'alert-type' => 'danger'
            );
            return redirect()->back()->with($notification);
        }
        $roles = Role::where('name','!=', 'Admin')->get();
        $countries = DB::table('ils')->orderBy('name', 'ASC')->get();
        return view('frontend.secure.staffs.edit_staff', compact('staff','roles','firma','countries'));
    }

    public function UpdateStaff(Request $request, $tenant_id,$id){
        $firma = Tenant::where('id', $tenant_id)->first();

        if (!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı.',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }
        $staff = User::findOrFail($id);
        if(!$staff){
            $notification = array(
                'message' => 'Personel bulunamadı.',
                'alert-type' => 'danger'
            );
            return redirect()->back()->with($notification);
        }
        $staff->username = $request->username;
        $staff->name = $request->name;
        $staff->baslamaTarihi = $request->baslamaTarihi;
        $staff->tel = $request->tel;
        $staff->address = $request->address;
        $staff->il = $request->il;
        $staff->ilce = $request->ilce;
        if($request->password){
          $staff->password = Hash::make($request->password);
        }
        $staff->status = $request->status;
        $staff->ayrilmaTarihi = $request->ayrilmaTarihi;
        $staff->save();
    
        $staff->roles()->detach();
        if($request->roles){
            $role = Role::findById($request->roles); // ID'yi kullanarak rolü al
            $staff->assignRole($role->name);
        }
        $notification = array(
          'message' => 'Personel Bilgileri Başarıyla Güncellendi',
          'alert-type' => 'success'
        );
        return response()->json(['success' => $notification]);
    }

    public function DeleteStaff($tenant_id, $id) {
        $staff = User::findOrFail($id);
        $authUser = Auth::user()->user_id;
        if($staff->user_id == $authUser) {
            $notification = array(
                'message' => 'Kendinizi silemezsiniz!',
                'alert-type' => 'danger'
            );
            return redirect()->back()->with($notification);
        }
        if(!is_null($staff)) {
            $staff->delete();
        }

        $notification = array(
            'message' => 'Personel başarıyla silindi.',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

    
    //DEALERS

    public function AllDealers($tenant_id, Request $request) {
    if (!Auth::check()) {
        return redirect()->route('giris')->with('error', 'Lütfen giriş yapınız.');
    }
    $user = Auth::user();
    if ($tenant_id == null || $user->tenant->id != $tenant_id) {
        return redirect()->route('giris')->with([
            'message' => 'Bayilere erişiminiz yoktur.',
            'alert-type' => 'danger',
        ]);
    }

    $firma = Tenant::findOrFail($tenant_id);


    $dealers = User::where('tenant_id', $tenant_id)
        ->whereHas('roles', function ($query) {
            $query->where('name', 'Bayi');
        })
        ->get();

    return view('frontend.secure.dealers.all_dealers', compact('dealers', 'firma'));
}

public function AddDealer($tenant_id) {
    $firma = Tenant::findOrFail($tenant_id);
    $roles= Role::where('name','!=', 'Bayi')->get();
    $countries = DB::table('ils')->orderBy('name', 'ASC')->get();
    return view('frontend.secure.dealers.add_dealer', compact('firma','roles', 'countries'));
}

public function StoreDealer(Request $request, $tenant_id)
{
    $firma = Tenant::findOrFail($tenant_id);

    $username = Str::slug($request->username, '-');

    // Yeni bayi kullanıcısı oluşturuluyor
    $user = new User();
    $user->tenant_id = $firma->id;
    $user->username = $request->username;
    $user->eposta = $this->generateUserEmail($username, $firma->username);
    $user->baslamaTarihi = $request->baslamaTarihi;
    $user->name = $request->name;
    $user->tel = $request->tel;
    $user->il = $request->il;
    $user->ilce = $request->ilce;
    $user->address = $request->address;
    $user->vergiNo = $request->vergiNo; 
    $user->vergiDairesi = $request->vergiDairesi; 
    $user->password = Hash::make($request->password);
    $user->status = 1;
    $user->save();

    // Bayi rolünü ata (ID 259)
    $role = Role::find(259);
    if ($role) {
        $user->assignRole($role->name);
    }

    $notification = [
        'message' => 'Bayi başarıyla kaydedildi.',
        'alert-type' => 'success'
    ];

    return redirect()->route('dealers', $tenant_id)->with($notification);
}
public function EditDealer($tenant_id, $id)
{
    $firma = Tenant::findOrFail($tenant_id);

    $bayi = User::where('tenant_id', $tenant_id)
                ->where('user_id', $id)
                ->whereHas('roles', function ($q) {
                    $q->where('id', 259); // bayi rolü ID
                })
                ->firstOrFail();

    $countries = DB::table('ils')->orderBy('name', 'ASC')->get();
  

    return view('frontend.secure.dealers.edit_dealer', compact('firma', 'bayi', 'countries'));
}

public function UpdateDealer(Request $request, $tenant_id, $id)
{
    $firma = Tenant::findOrFail($tenant_id);
    $bayi = User::where('tenant_id', $tenant_id)
                ->where('user_id', $id)
                ->whereHas('roles', function ($q) {
                    $q->where('id', 259);
                })
                ->firstOrFail();

    $bayi->name = $request->name;
    $bayi->username = $request->username;
    $bayi->tel = $request->tel;
    $bayi->il = $request->il;
    $bayi->ilce = $request->ilce;
    $bayi->address = $request->address;
    $bayi->baslamaTarihi = $request->baslamaTarihi;
    $bayi->status = $request->status;
    $bayi->ayrilmaTarihi = $request->ayrilmaTarihi;

    $bayi->vergiNo = $request->vergiNo;
    $bayi->vergiDairesi = $request->vergiDairesi;

    if ($request->filled('password')) {
        $bayi->password = Hash::make($request->password);
    }

    $bayi->save();

    // Role güncelle (varsa)
    $bayi->roles()->detach();
    $bayi->assignRole('Bayi'); // Eğer rol adı 'Bayi' ise


    $notification = [
        'message' => 'Bayi bilgileri başarıyla güncellendi.',
        'alert-type' => 'success'
    ];

    return redirect()->back()->with($notification);
}


public function DeleteDealer($tenant_id, $id) {
    $dealer = User::findOrFail($id);

    // Giriş yapan kullanıcı kendi hesabını silemez
    if (Auth::user()->user_id == $dealer->user_id) {
        return redirect()->back()->with([
            'message' => 'Kendi hesabınızı silemezsiniz!',
            'alert-type' => 'danger'
        ]);
    }

    // Kullanıcı gerçekten bayi mi kontrolü (rol ID'si ile değil, isimle)
    if ($dealer->hasRole('Bayi')) {
        $dealer->delete();

        return redirect()->back()->with([
            'message' => 'Bayi başarıyla silindi.',
            'alert-type' => 'success'
        ]);
    }

    return redirect()->back()->with([
        'message' => 'Bu kullanıcı bayi değildir.',
        'alert-type' => 'danger'
    ]);
}

public function GetDealersData(Request $request, $tenant_id)
{
    if ($request->ajax()) {
        $query = User::where('tenant_id', $tenant_id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Bayi');
            });

        // Durum filtreleme (0: ayrıldı, 1: çalışıyor, 2: tümü)
        if ($request->filled('durum') && $request->durum !== '2') {
            $query->where('status', $request->durum);
        }

        // Sıralama işlemi
        if ($request->has('order')) {
            $order = $request->get('order')[0];
            $columns = $request->get('columns');
            $orderColumn = $columns[$order['column']]['data'];
            $orderDir = $order['dir'];
            $query->orderBy($orderColumn, $orderDir);
        } else {
            $query->orderBy('user_id', 'desc');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('user_id', function($row){  
                return '<a class="t-link editBayi address idWrap" href="javascript:void(0);" data-bs-id="'.$row->user_id.'" data-bs-toggle="modal" data-bs-target="#editBayiModal">'.$row->user_id.'</a>'; 
            })
            ->addColumn('name', function($row){
                return '<a class="t-link editBayi address" href="javascript:void(0);" data-bs-id="'.$row->user_id.'" data-bs-toggle="modal" data-bs-target="#editBayiModal"><div class="mobileTitle">Bayi Adı:</div>'.$row->name.'</a>';     
            })
            ->addColumn('grup', function($row){
                foreach($row->roles as $role){
                    return '<a class="t-link editBayi" href="javascript:void(0);" data-bs-id="'.$row->user_id.'" data-bs-toggle="modal" data-bs-target="#editBayiModal"><div class="mobileTitle">B. Grubu:</div><span class="badge badge-pill bg-danger">'.$role->name.'</span></div></a>';
                }          
            })
            ->addColumn('tel', function($row){     
                $telefon = $row->tel;
                if (substr($telefon, 0, 1) !== '0') {
                    $telefon = '0' . $telefon;
                }
                return '<a class="t-link editBayi" href="javascript:void(0);" data-bs-id="'.$row->user_id.'" data-bs-toggle="modal" data-bs-target="#editBayiModal"><div class="mobileTitle">Telefon:</div>'.$telefon.'</div></a>';
            })
            ->addColumn('address', function($row){  
                $address = (!empty($row->country->name) && !empty($row->state->ilceName)) 
                    ? $row->country->name . ' - ' . $row->state->ilceName 
                    : '';
                return '<a class="t-link editBayi address" href="javascript:void(0);" data-bs-id="'.$row->user_id.'" data-bs-toggle="modal" data-bs-target="#editBayiModal"><div class="mobileTitle">Adres:</div>'.$address.'</div></a>';
            })
            ->addColumn('status', function($row){
                if($row->status == 1){
                    return '<a class="t-link editBayi" href="javascript:void(0);" data-bs-id="'.$row->user_id.'" data-bs-toggle="modal" data-bs-target="#editBayiModal"><div class="mobileTitle">Durum:</div><div style="color: green; display: inline-block;font-weight:700;">Çalışıyor</div></div></a>';
                }else{
                    return '<a class="t-link editBayi" href="javascript:void(0);" data-bs-id="'.$row->user_id.'" data-bs-toggle="modal" data-bs-target="#editBayiModal"><div class="mobileTitle">Durum:</div><div style="color: red; display: inline-block;font-weight:700;">Ayrıldı</div></div></a>';
                }
            })
            ->addColumn('action', function($row) use ($tenant_id){
                $deleteUrl = route('delete.dealer', [$tenant_id, $row->user_id]);
                $editButton = '<a href="javascript:void(0);" data-bs-id="'.$row->user_id.'" class="btn btn-warning btn-sm editBayi mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editBayiModal" title="Düzenle"><i class="fas fa-edit"></i></a>';
                $deleteButton = '<a href="'.$deleteUrl.'" class="btn btn-danger btn-sm mobilBtn" id="delete" title="Sil"><i class="fas fa-trash-alt"></i></a>';
                return $editButton . ' ' . $deleteButton;
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->where('name', 'LIKE', "%$search%");
                    });
                }
            })
            ->rawColumns(['user_id','name','grup','tel','address','status','action'])
            ->make(true);
    }

    return response()->json(['error' => 'Yetkisiz erişim'], 403);
}




}
