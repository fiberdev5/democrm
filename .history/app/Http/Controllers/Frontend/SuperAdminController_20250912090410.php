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
        // Temel istatistikler
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('status', 1)->count(),
            'trial_tenants' => Tenant::where('subscription_status', 'trial')->count(),
            'subscribed_tenants' => Tenant::where('subscription_status', 'active')->count(),
            'total_users' => User::count(),
            'active_users' => User::where('status', 1)->count(),
        ];

        // Yüzdelik hesaplamalar
        $stats['active_tenant_percentage'] = round(($stats['active_tenants'] / max($stats['total_tenants'], 1)) * 100);
        $stats['active_user_percentage'] = round(($stats['active_users'] / max($stats['total_users'], 1)) * 100);
        $stats['trial_percentage'] = round(($stats['trial_tenants'] / max($stats['total_tenants'], 1)) * 100);

        // Destek talepleri istatistikleri
        $supportStats = [
            'urgent_tickets' => \App\Models\SupportTicket::where('priority', 'acil')
                              ->where('status', '!=', 'kapali')->count(),
            'new_tickets' => \App\Models\SupportTicket::where('status', 'acik')->count(),
            'total_tickets' => \App\Models\SupportTicket::count(),
        ];

        // Son 7 günlük grafik verileri
        $chartData = $this->getChartData();

        return view('frontend.secure.super_admin.dashboard', compact('stats', 'supportStats', 'chartData'));
    }

    // Grafik verilerini hazırlayan yardımcı method
    private function getChartData()
    {
        $labels = [];
        $newRegistrations = [];
        $activeUsers = [];

        // Son 7 günlük verileri hesapla
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('D'); // Gün kısaltması (Mon, Tue, etc.)
            
            // O gün kayıt olan kullanıcı sayısı
            $dailyRegistrations = User::whereDate('created_at', $date->format('Y-m-d'))->count();
            $newRegistrations[] = $dailyRegistrations;
            
            // O gün aktif olan kullanıcı sayısı
            $dailyActiveUsers = User::where('status', 1)
                                   ->whereDate('updated_at', '<=', $date->format('Y-m-d'))
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
            if (!empty($request->get('search')['value'])) {
                $search = $request->get('search')['value'];
                $data->where(function($w) use($search) {
                   $w->where('firma_adi', 'LIKE', "%$search%")
                     ->orWhere('tel1', 'LIKE', "%$search%")
                     ->orWhere('adres', 'LIKE', "%$search%");
                });
            }

            // Genel status filtering (aktif/pasif)
            if ($request->filled('status')) {
                $data->where('status', $request->get('status'));
            }

            // Abonelik durumu filtering
            if ($request->filled('subscription_status')) {
                $data->where('subscription_status', $request->get('subscription_status'));
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
                $orderColumnName = $columns[$orderColumnIndex]['name'];
                $orderDir = $order['dir'];
                
                // Kolon isim mapping
                if ($orderColumnName == 'name') $orderColumnName = 'firma_adi';
                if ($orderColumnName == 'tel') $orderColumnName = 'tel1';
                if ($orderColumnName == 'address') $orderColumnName = 'adres';
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
                    if ($telefon && substr($telefon, 0, 1) !== '0') {
                        $telefon = '0' . $telefon;
                    }
                    return '<a class="t-link editTenant" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editTenantModal"><div class="mobileTitle">Telefon:</div>'.$telefon.'</a>';
                })
                ->addColumn('address', function($row) {
                    $fullAddress = $row->adres ?? '';
                    return '<a class="t-link editTenant address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editTenantModal">
                                <div class="mobileTitle">Adres:</div>' . $fullAddress . '
                            </a>';
                })
                ->addColumn('durum', function($row) {
                    // Genel durum (aktif/pasif)
                    $statusBadge = $row->status == 1
                        ? '<span class="badge bg-success"><i class="mdi mdi-check-circle"></i> Aktif</span>'
                        : '<span class="badge bg-danger"><i class="mdi mdi-close-circle"></i> Pasif</span>';
                    
                    return '<a class="t-link editTenant" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editTenantModal"><div class="mobileTitle">Durum:</div>'.$statusBadge.'</a>';
                })
                ->addColumn('subscription', function($row) {
                    // Abonelik durumu
                    $subscriptionBadge = '';
                    switch($row->subscription_status) {
                        case 'trial':
                            $subscriptionBadge = '<span class="badge bg-warning"><i class="mdi mdi-timer"></i> Deneme</span>';
                            break;
                        case 'active':
                            $subscriptionBadge = '<span class="badge bg-primary"><i class="mdi mdi-crown"></i> Aboneli</span>';
                            break;
                        case 'expired':
                            $subscriptionBadge = '<span class="badge bg-secondary"><i class="mdi mdi-clock-alert"></i> Süresi Doldu</span>';
                            break;
                        case 'canceled':
                            $subscriptionBadge = '<span class="badge bg-dark"><i class="mdi mdi-cancel"></i> İptal Edildi</span>';
                            break;
                        default:
                            $subscriptionBadge = '<span class="badge bg-light text-dark">Belirsiz</span>';
                    }
                    
                    return '<div class="mobileTitle">Abonelik:</div>'.$subscriptionBadge;
                })
                ->addColumn('action', function($row) {
                    $editButton = '<a href="javascript:void(0);" data-bs-id="'.$row->id.'" 
                    class="btn btn-sm btn-outline-primary editTenant mobilBtn me-1" 
                    data-bs-toggle="modal" data-bs-target="#editTenantModal" 
                    title="Detay"><i class="fas fa-eye"></i></a>';
                    
                    $usersButton = '<button class="btn btn-sm btn-outline-danger 
                        mobilBtn view-tenant-users me-1" data-tenant-id="'.$row->id.'" 
                        title="Kullanıcıları Görüntüle">
                        <i class="fas fa-users"></i></button>';
                    
                    $impersonateButton = '';
                    // Hem genel durumu aktif hem de abonelik durumu aktif/trial olanlar için giriş butonu
                    if ($row->status == 1 && in_array($row->subscription_status, ['active', 'trial'])) {
                        $tenantOwner = User::where('tenant_id', $row->id)
                                          ->whereHas('roles', function($query) {
                                              $query->whereIn('name', ['Patron', 'Müdür']);
                                          })
                                          ->first();
                        
                        if ($tenantOwner) {
                            $impersonateButton = '<button class="btn btn-sm btn-outline-success mobilBtn impersonate-tenant-owner me-1" 
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
                ->rawColumns(['id','name','tel','address','durum','subscription','action'])
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
            'activeSubscription.plansubs',
            'currentSubscription.plansubs'
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
            ->with('plansubs')
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
            'subscription_status' => 'sometimes|in:trial,active,expired,canceled',
        ]);

        $updateData = $request->only([
            'firma_adi', 'eposta', 'tel1', 'adres', 'il', 'ilce', 'status'
        ]);

        // Abonelik durumu güncellemesi varsa ekle
        if($request->filled('subscription_status')) {
            $updateData['subscription_status'] = $request->subscription_status;
        }

        $tenant->update($updateData);

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

        $notification = array(
            'message' => $message,
            'alert-type' => 'success'
        );

        // Firma aktif edildiğinde ve abonelik durumu aktif/trial ise mail gönder
        if ($tenant->status == 1 && in_array($tenant->subscription_status, ['active', 'trial'])) {
            $customUsername = $tenant->firma_slug . '@' . $tenant->username;
            
            $trialDaysRemaining = 0;
            $isTrialActive = false;
            
            if ($tenant->subscription_status === 'trial' && $tenant->trial_ends_at) {
                $trialDaysRemaining = $tenant->trial_ends_at->diffInDays(now());
                $isTrialActive = $tenant->trial_ends_at->isFuture();
            }
            
            $mailData = [
                'username' => $customUsername,
                'tenant' => $tenant,
                'trialDaysRemaining' => $trialDaysRemaining,
                'isTrialActive' => $isTrialActive
            ];
            
            Mail::to($tenant->eposta)->queue(new UserRegisteredMail($mailData));
        }
        
        return redirect()->back()->with($notification);
    }

    // Abonelik durumunu değiştiren yeni method
    public function changeSubscriptionStatus($id, Request $request)
    {
        $tenant = Tenant::findOrFail($id);
        
        $request->validate([
            'subscription_status' => 'required|in:trial,active,expired,canceled'
        ]);

        $tenant->subscription_status = $request->subscription_status;
        
        // Duruma göre tarihleri güncelle
        switch($request->subscription_status) {
            case 'trial':
                if (!$tenant->trial_ends_at) {
                    $tenant->trial_ends_at = now()->addDays(30); // 30 günlük deneme
                }
                break;
            case 'active':
                if (!$tenant->subscription_ends_at) {
                    $tenant->subscription_ends_at = now()->addYear(); // 1 yıllık abonelik
                }
                break;
            case 'expired':
            case 'canceled':
                // Bu durumlar için özel işlem gerekebilir
                break;
        }
        
        $tenant->save();

        $statusMessages = [
            'trial' => 'deneme',
            'active' => 'aktif',
            'expired' => 'süresi dolmuş',
            'canceled' => 'iptal edilmiş'
        ];

        $notification = array(
            'message' => 'Abonelik durumu ' . $statusMessages[$request->subscription_status] . ' olarak güncellendi.',
            'alert-type' => 'success'
        );
        
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