<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Il;
use App\Models\Service;
use App\Models\ServicePlanning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user() || !Auth::user()->isSuperAdmin()) {
                abort(403, 'Super Admin yetkisi gereklidir.');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('status', 1)->count(),
            'total_users' => User::count(),
            'active_users' => User::where('status', 1)->count(),
        ];

        return view('frontend.secure.super_admin.dashboard', compact('stats'));
    }

    public function allTenants(Request $request)
    {
        $countries = Il::orderBy('name', 'ASC')->get();

        if ($request->ajax()) {
            $data = Tenant::with('ils', 'ilces');

            // Filtreleme işlemleri
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

            // Sıralama
            if ($request->has('order')) {
                $order = $request->get('order')[0];
                $columns = $request->get('columns');
                $orderColumn = $columns[$order['column']]['data'];
                $orderDir = $order['dir'];
                $data->orderBy($orderColumn, $orderDir);
            } else {
                $data->orderBy('id', 'desc');
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('id', function($row) {
                    return '<a class="t-link editTenant address idWrap" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editTenantModal">'.$row->id.'</a>';
                })
                ->addColumn('name', function($row) {
                    return '<a class="t-link editTenant address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editTenantModal"><div class="mobileTitle">Firma Adı:</div>'.$row->firma_adi.'</a>';
                })
                ->addColumn('tel', function($row) {
                    $telefon = $row->tel1;
                    if (substr($telefon, 0, 1) !== '0') {
                        $telefon = '0' . $telefon;
                    }
                    return '<a class="t-link editTenant" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editTenantModal"><div class="mobileTitle">Telefon:</div>'.$telefon.'</a>';
                })
                ->addColumn('address', function($row) {
                    $address = (!empty($row->ils->name) && !empty($row->ilces->ilceName))
                        ? $row->addresss . ' ' . $row->ils->name . ' / ' . $row->ilces->ilceName
                        : '';
                    return '<a class="t-link editTenant address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editTenantModal"><div class="mobileTitle">Adres:</div>'.$address.'</a>';
                })
                ->addColumn('durum', function($row) {
                    $statusBadge = $row->status == 1
                        ? '<span class="badge bg-success"><i class="mdi mdi-check-circle"></i> Aktif</span>'
                        : '<span class="badge bg-danger"><i class="mdi mdi-close-circle"></i> Aktif Değil</span>';
                    return '<a class="t-link editTenant" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editTenantModal"><div class="mobileTitle">Durum:</div>'.$statusBadge.'</a>';
                })
                ->addColumn('action', function($row) {
                    $editButton = '<a href="javascript:void(0);" data-bs-id="'.$row->id.'" class="btn btn-warning btn-sm editTenant mobilBtn me-1" data-bs-toggle="modal" data-bs-target="#editTenantModal" title="Düzenle"><i class="fas fa-edit"></i></a>';
                    
                    $usersButton = '<button class="btn btn-primary btn-sm mobilBtn view-tenant-users me-1" data-tenant-id="'.$row->id.'" title="Kullanıcıları Görüntüle"><i class="fas fa-users"></i></button>';
                    
                    $impersonateButton = '';
                    if ($row->status == 1) {
                        $tenantOwner = User::where('tenant_id', $row->id)
                                          ->whereHas('roles', function($query) {
                                              $query->whereIn('name', ['Patron', 'Müdür']);
                                          })
                                          ->first();
                        
                        if ($tenantOwner) {
                            $impersonateButton = '<button class="btn btn-success btn-sm mobilBtn impersonate-tenant-owner me-1" 
                                                        data-tenant-id="'.$row->id.'" 
                                                        data-owner-id="'.$tenantOwner->user_id.'"
                                                        data-owner-name="'.$tenantOwner->name.'"
                                                        data-company-name="'.$row->firma_adi.'"
                                                        title="Firma Yetkilisi Olarak Giriş Yap">
                                                    <i class="fas fa-user-secret"></i>
                                                </button>';
                        }
                    }
                    
                    return '<div class="d-flex gap-1">' . $editButton . $usersButton . $impersonateButton . '</div>';
                })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function($w) use($request) {
                           $search = $request->get('search');
                           $w->where('firma_adi', 'LIKE', "%$search%");
                        });
                    }
                })
                ->rawColumns(['id','name','tel','address','durum','action'])
                ->make(true);
        }

        return view('frontend.secure.super_admin.all_tenants', compact('countries'));
    }

    public function editTenant($id)
    {
        $tenant = Tenant::findOrFail($id);
        if(!$tenant) {
            $notification = array(
                'message' => 'Firma bulunamadı!',
                'alert-type' => 'danger'
            );
            return redirect()->back()->with($notification);
        }

        $countries = Il::orderBy('name','asc')->get();
        $today = Carbon::today();
        
        $periods = [
            'bugun' => [
                'start' => $today->copy(),       // Başlangıç: Bugün
                'end' => $today->copy(),         // Bitiş: Bugün
                'label' => 'Bugün'
            ],
            'dun' => [
                'start' => $today->copy()->subDay(), // Başlangıç: Dün
                'end' => $today->copy()->subDay(),   // Bitiş: Dün
                'label' => 'Dün'
            ],
            'onceki_gun' => [
                'start' => $today->copy()->subDays(2), // Başlangıç: Önceki gün
                'end' => $today->copy()->subDays(2),   // Bitiş: Önceki gün
                'label' => 'Önceki Gün'
            ],
            'ayinBasi' => [
                'start' => $today->copy()->startOfMonth(),
                'end' => $today->copy(),
                'label' => 'Ayın Başından İtibaren'
            ]
        ];

        $periodStats = [];
        foreach ($periods as $key => $period) {
            $servisler = Service::where('firma_id', $id) 
            ->where('durum', 1)
            ->whereBetween('kayitTarihi', [
                $period['start']->format('Y-m-d') . ' 00:00:00',
                $period['end']->format('Y-m-d') . ' 23:59:59'
            ])->get();
            
            $personeller = User::where('tenant_id', $id)
            ->whereNull('ayrilmaTarihi')
            ->whereIn('user_id', function ($query) {
                $query->select('model_id')
                    ->from('model_has_roles')
                    ->whereIn('role_id', [1, 5, 263]);
            })
            ->get();

            $validServisler = $this->filterCancelledServices($servisler);
            $validServisIds = $validServisler->pluck('id')->toArray();

            $periodStats[$key] = [
                'label' => $period['label'],
                'toplam' => count($validServisIds),
                'markalar' => $this->getDeviceBrandStats($validServisIds),
                'turler' => $this->getDeviceTypeStats($validServisIds),
                'kaynaklar' => $this->getServiceResourceStats($validServisIds),
                'operatorler' => $this->getOperatorStats($validServisIds)
            ];
        }
        
        $topServisSayisi = Service::where('firma_id', $id) 
            ->where('durum', 1)
            ->count();
            
        return view('frontend.secure.super_admin.edit_tenants', compact('tenant','countries', 'periodStats','topServisSayisi'));
    }

    public function updateTenant(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);
        
        $request->validate([
            'firma_adi' => 'required|string|max:255',
            'eposta' => 'required|email|max:255',
            'tel1' => 'required|string|max:20',
        ]);

        $tenant->update($request->only([
            'firma_adi', 'eposta', 'tel1', 'adres', 'il', 'ilce', 'status'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Firma başarıyla güncellendi.'
        ]);
    }
    public function deleteTenant($id)
    {
        $tenant = Tenant::findOrFail($id);

        $tenant->status = 0;
        $tenant->save();

        $notification = array(
            'message' => 'Firma başarıyla pasif hale getirildi.',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }


    public function changeTenantStatus($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->status = $tenant->status == 1 ? 0 : 1;
        $tenant->save();

        $message = $tenant->status == 1 
            ? 'Firma başarıyla aktif edildi!' 
            : 'Firma başarıyla pasif hale getirildi.';

        $notification = [
            'message' => $message,
            'alert-type' => 'success'
        ];

        return redirect()->back()->with($notification);
    }

    // Helper metodları - TenantController'dan alındı
    private function filterCancelledServices($servisler)
    {
        return $servisler->filter(function ($servis) {
            return !ServicePlanning::where('servisid', $servis->id)
                                 ->where('gidenIslem', 244)
                                 ->exists();
        });
    }

    private function getDeviceBrandStats($servisIds)
    {
        if (empty($servisIds)) return [];
        return Service::join('device_brands', 'services.cihazMarka', '=', 'device_brands.id')
              ->whereIn('services.id', $servisIds) 
              ->select('device_brands.marka', DB::raw('count(*) as sayi'))
              ->groupBy('device_brands.marka')
              ->orderBy('sayi', 'desc')
              ->get();
    }

    private function getDeviceTypeStats($servisIds)
    {
        if (empty($servisIds)) return [];

          return Service::whereIn('services.id', $servisIds) 
                 ->join('device_types', 'services.cihazTur', '=', 'device_types.id')
                 ->select('device_types.cihaz', DB::raw('count(*) as sayi'))
                 ->groupBy('device_types.cihaz')
                 ->orderBy('sayi', 'desc')
                 ->get();
    }
    
    private function getServiceResourceStats($servisIds)
    {
        if (empty($servisIds)) return [];

           return Service::whereIn('services.id', $servisIds) 
                 ->join('service_resources', 'services.servisKaynak', '=', 'service_resources.id')
                 ->select('service_resources.kaynak', DB::raw('count(*) as sayi'))
                 ->groupBy('service_resources.kaynak')
                 ->orderBy('sayi', 'desc')
                 ->get();
    }
    
    private function getOperatorStats($servisIds)
    {
        if (empty($servisIds)) return [];

        return Service::whereIn('services.id', $servisIds)
                 ->join('tb_user', 'services.kayitAlan', '=', 'tb_user.user_id')
                 ->select('tb_user.name', DB::raw('count(*) as sayi'))
                 ->groupBy('tb_user.name')
                 ->orderBy('sayi', 'desc')
                 ->get();
    }
}