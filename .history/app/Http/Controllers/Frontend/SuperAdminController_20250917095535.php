<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\UserRegisteredMail;
use App\Models\Tenant;
use App\Models\User;
use App\Models\TenantSubscription;
use App\Models\SubscriptionPlan;
use App\Models\Il;
use App\Models\Service;
use App\Models\ServicePlanning;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

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
    // Super Admin panelini hariç tutmak için
    $superAdminTenant = Tenant::where('firma_adi', 'Super Admin Panel')
                             ->orWhere('name', 'Super Admin')
                             ->first();
    
    $superAdminTenantId = $superAdminTenant ? $superAdminTenant->id : null;

    // Temel istatistikler - Super Admin hariç
    $stats = [
        'total_tenants' => Tenant::when($superAdminTenantId, function($query) use ($superAdminTenantId) {
            return $query->where('id', '!=', $superAdminTenantId);
        })->count(),
        
        'active_tenants' => Tenant::where('status', 1)
                                  ->when($superAdminTenantId, function($query) use ($superAdminTenantId) {
                                      return $query->where('id', '!=', $superAdminTenantId);
                                  })->count(),
        
        'total_users' => User::when($superAdminTenantId, function($query) use ($superAdminTenantId) {
            return $query->where('tenant_id', '!=', $superAdminTenantId);
        })->count(),
        
        'active_users' => User::where('status', 1)
                             ->when($superAdminTenantId, function($query) use ($superAdminTenantId) {
                                 return $query->where('tenant_id', '!=', $superAdminTenantId);
                             })->count(),
    ];

    // Yüzdelik hesaplamalar
    $stats['active_tenant_percentage'] = $stats['total_tenants'] > 0 
        ? round(($stats['active_tenants'] / $stats['total_tenants']) * 100)
        : 0;
    
    $stats['active_user_percentage'] = $stats['total_users'] > 0 
        ? round(($stats['active_users'] / $stats['total_users']) * 100)
        : 0;

    // Destek talepleri istatistikleri
    $supportStats = [
        'urgent_tickets' => \App\Models\SupportTicket::where('priority', 'acil')
                          ->where('status', '!=', 'kapali')->count(),
        'new_tickets' => \App\Models\SupportTicket::where('status', 'acik')->count(),
        'total_tickets' => \App\Models\SupportTicket::count(),
    ];

    // Son 7 günlük grafik verileri - Super Admin hariç
    $chartData = $this->getChartData($superAdminTenantId);

    return view('frontend.secure.super_admin.dashboard', compact('stats', 'supportStats', 'chartData'));
}

// Grafik verilerini hazırlayan yardımcı method - Super Admin hariç
private function getChartData($superAdminTenantId = null)
{
    $labels = [];
    $newRegistrations = [];
    $activeUsers = [];

    // Son 7 günlük verileri hesapla
    for ($i = 6; $i >= 0; $i--) {
        $date = Carbon::now()->subDays($i);
        $labels[] = $date->format('D'); // Gün kısaltması (Mon, Tue, etc.)
        
        // O gün kayıt olan kullanıcı sayısı - Super Admin hariç
        $dailyRegistrations = User::whereDate('created_at', $date->format('Y-m-d'))
                                 ->when($superAdminTenantId, function($query) use ($superAdminTenantId) {
                                     return $query->where('tenant_id', '!=', $superAdminTenantId);
                                 })
                                 ->count();
        $newRegistrations[] = $dailyRegistrations;
        
        // O gün aktif olan kullanıcı sayısı - Super Admin hariç
        $dailyActiveUsers = User::where('status', 1)
                               ->whereDate('updated_at', '<=', $date->format('Y-m-d'))
                               ->when($superAdminTenantId, function($query) use ($superAdminTenantId) {
                                   return $query->where('tenant_id', '!=', $superAdminTenantId);
                               })
                               ->count();
        $activeUsers[] = min($dailyActiveUsers, 100); // Grafik için makul bir üst limit
    }

    return [
        'labels' => $labels,
        'new_registrations' => $newRegistrations,
        'active_users' => $activeUsers
    ];
}
    public function allTenants(Request $request)
    {
        $countries = Il::orderBy('name', 'ASC')->get();
        if ($request->ajax()) {
            $data = Tenant::with(['ils', 'ilces']);
            if (!empty($request->get('search')['value'])) { // Accessing search value correctly
                $search = $request->get('search')['value'];
                $data->where(function($w) use($search) {
                   $w->where('firma_adi', 'LIKE', "%$search%")
                     ->orWhere('tel1', 'LIKE', "%$search%")
                     ->orWhere('adres', 'LIKE', "%$search%"); // Also search in the 'adres' column
                });
            }

            // Status filtering  - aktif/pasif durumu
            if ($request->filled('status')) {
                $data->where('status', $request->get('status'));
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
                $orderColumnIndex = $order['column'];
                $orderColumnName = $columns[$orderColumnIndex]['name']; // Use 'name' for direct column mapping
                $orderDir = $order['dir'];
                
                // Special handling for related columns if needed, otherwise use direct column name
                if ($orderColumnName == 'name') $orderColumnName = 'firma_adi';
                if ($orderColumnName == 'tel') $orderColumnName = 'tel1';
                if ($orderColumnName == 'address') $orderColumnName = 'adres'; // Sort by 'adres' field
                if ($orderColumnName == 'durum') $orderColumnName = 'status';

                $data->orderBy($orderColumnName, $orderDir);
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
                    if ($telefon && substr($telefon, 0, 1) !== '0') { // Added check for $telefon existence
                        $telefon = '0' . $telefon;
                    }
                    return '<a class="t-link editTenant" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editTenantModal"><div class="mobileTitle">Telefon:</div>'.$telefon.'</a>';
                })
                ->addColumn('address', function($row) {
                        // Sadece adres sütununu al
                        $fullAddress = $row->adres ?? ''; // Boşsa boş string döner

                        return '<a class="t-link editTenant address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editTenantModal">
                                    <div class="mobileTitle">Adres:</div>' . $fullAddress . '
                                </a>';
                    })

                ->addColumn('durum', function($row) {
                    $statusBadge = $row->status == 1
                        ? '<span class="badge bg-success"><i class="mdi mdi-check-circle"></i> Aktif</span>'
                        : '<span class="badge bg-danger"><i class="mdi mdi-close-circle"></i> Pasif</span>'; // Changed text to Pasif
                    return '<a class="t-link editTenant" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editTenantModal"><div class="mobileTitle">Durum:</div>'.$statusBadge.'</a>';
                })
                ->addColumn('action', function($row) {
                    $editButton = '<a href="javascript:void(0);" data-bs-id="'.$row->id.'" 
                    class="btn btn-sm btn-outline-primary  editTenant mobilBtn me-1" 
                    data-bs-toggle="modal" data-bs-target="#editTenantModal" 
                    title="Detay"><i class="fas fa-eye"></i></a>';
                    
                    $usersButton = '<button class="btn btn-sm btn-outline-danger 
                        mobilBtn view-tenant-users me-1" data-tenant-id="'.$row->id.'" 
                        title="Kullanıcıları Görüntüle">
                        <i class="fas fa-users"></i></button>';
                    
                    $impersonateButton = '';
                    if ($row->status == 1) {
                        $tenantOwner = User::where('tenant_id', $row->id)
                                          ->whereHas('roles', function($query) {
                                              $query->whereIn('name', ['Patron', 'Müdür']);
                                          })
                                          ->first();
                        
                        if ($tenantOwner) {
                            $impersonateButton = '<button class="btn btn-sm btn-outline-success  mobilBtn impersonate-tenant-owner me-1" 
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
                ->rawColumns(['id','name','tel','address','durum','action'])
                ->make(true);
        }

        return view('frontend.secure.super_admin.all_tenants', compact('countries'));
    }

    public function editTenant($id)
    {
        // İlişkileri dahil ederek tenant'ı getir
        $tenant = Tenant::with([
            'ils', 
            'ilces', 
            'activeSubscription.plansubs', // Aktif abonelik ve plan bilgisi
            'currentSubscription.plansubs' // Güncel abonelik ve plan bilgisi
        ])->findOrFail($id);
        
        if(!$tenant) {
            $notification = array(
                'message' => 'Firma bulunamadı!',
                'alert-type' => 'danger'
            );
            return redirect()->back()->with($notification);
        }

        $countries = Il::orderBy('name','asc')->get();
        $today = Carbon::today();
        
        // Mevcut period kodları...
        $periods = [
            'bugun' => [
                'start' => $today->copy(),
                'end' => $today->copy(),
                'label' => 'Bugün'
            ],
            'dun' => [
                'start' => $today->copy()->subDay(),
                'end' => $today->copy()->subDay(),
                'label' => 'Dün'
            ],
            'onceki_gun' => [
                'start' => $today->copy()->subDays(2),
                'end' => $today->copy()->subDays(2),
                'label' => 'Önceki Gün'
            ],
            'ayinBasi' => [
                'start' => $today->copy()->startOfMonth(),
                'end' => $today->copy(),
                'label' => 'Ayın Başından İtibaren'
            ]
        ];

        // Mevcut period stats kodları...
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

        // Abonelik geçmişini getir
        $subscriptionHistory = $tenant->subscriptions()
            ->with('plansubs') // plansubs kullanıyoruz
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('frontend.secure.super_admin.edit_tenants', compact(
            'tenant',
            'countries', 
            'periodStats',
            'topServisSayisi',
            'subscriptionHistory'
        ));
    }

    public function updateTenant(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);
        
        $request->validate([
            'firma_adi' => 'required|string|max:255',
            'eposta' => 'required|email|max:255',
            'tel1' => 'required|string|max:20',
            'status' => 'required|boolean',
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
        // İlgili firmayı bul, bulamazsan hata ver
        $tenant = Tenant::findOrFail($id);

        $tenant->status = $tenant->status == 1 ? 0 : 1;
        $tenant->save();

        $message = $tenant->status == 1 
            ? 'Firma başarıyla aktif edildi!' 
            : 'Firma başarıyla pasif hale getirildi.';

        $notification = array(
            'message' => $message,
            'alert-type' => 'success'
        );
         // Firma aktif edildiğinde mail gönder
        if ($tenant->status == 1) {
            $customUsername = $tenant->firma_slug . '@' . $tenant->username;
            
            // Tenant bilgilerini mail'e gönder
            $mailData = [
                'username' => $customUsername,
                'tenant' => $tenant,
                'trialDaysRemaining' => $tenant->trial_ends_at ? $tenant->trial_ends_at->diffInDays(now()) : 0,
                'isTrialActive' => $tenant->subscription_status === 'trial' && $tenant->trial_ends_at && $tenant->trial_ends_at->isFuture()
            ];
            
            Mail::to($tenant->eposta)->queue(new UserRegisteredMail($mailData));
        }
        return redirect()->back()->with($notification);
    }



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