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
        // Super Admin panelini hariç tut ve plan bilgileri ile birlikte getir
        $data = Tenant::with([
            'ils', 
            'ilces', 
            'currentSubscription.plansubs',
            'activeSubscription.plansubs'
        ])
        ->where('firma_adi', '!=', 'Super Admin Panel')
        ->where('name', '!=', 'Super Admin');
        
        if (!empty($request->get('search')['value'])) {
            $search = $request->get('search')['value'];
            $data->where(function($w) use($search) {
               $w->where('firma_adi', 'LIKE', "%$search%")
                 ->orWhere('adres', 'LIKE', "%$search%");
            });
        }

        // Status filtering - aktif/pasif durumu
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
            $orderColumnName = $columns[$orderColumnIndex]['name'];
            $orderDir = $order['dir'];
            
            // Special handling for related columns
            if ($orderColumnName == 'name') $orderColumnName = 'firma_adi';
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
            ->addColumn('plan', function($row) {
                $planInfo = 'Tanımsız';
                
                // Önce trial durumunu kontrol et (tenant tablosundan)
                if ($row->subscription_status === 'trial' && $row->trial_ends_at && $row->trial_ends_at->isFuture()) {
                    $planInfo = 'Deneme';
                }
                // Sonra active subscription kontrol et
                elseif ($row->activeSubscription && $row->activeSubscription->plansubs) {
                    $planName = $row->activeSubscription->plansubs->name ?? $row->activeSubscription->plansubs->plan_name ?? 'Plan';
                    
                    if ($row->activeSubscription->status === 'active') {
                        $planInfo = $planName;
                    } elseif ($row->activeSubscription->status === 'trial') {
                        $planInfo = $planName . ' (Deneme)';
                    } elseif ($row->activeSubscription->status === 'expired') {
                        $planInfo = $planName . ' (Süresi Dolmuş)';
                    } elseif ($row->activeSubscription->status === 'suspended') {
                        $planInfo = $planName . ' (Askıya Alınmış)';
                    } else {
                        $planInfo = $planName;
                    }
                }
                // Son olarak current subscription kontrol et
                elseif ($row->currentSubscription && $row->currentSubscription->plansubs) {
                    $planName = $row->currentSubscription->plansubs->name ?? $row->currentSubscription->plansubs->plan_name ?? 'Plan';
                    
                    if ($row->currentSubscription->status === 'active') {
                        $planInfo = $planName;
                    } elseif ($row->currentSubscription->status === 'trial') {
                        $planInfo = $planName . ' (Deneme)';
                    } elseif ($row->currentSubscription->status === 'expired') {
                        $planInfo = $planName . ' (Süresi Dolmuş)';
                    } elseif ($row->currentSubscription->status === 'suspended') {
                        $planInfo = $planName . ' (Askıya Alınmış)';
                    } else {
                        $planInfo = $planName;
                    }
                }
                
                return '<a class="t-link editTenant" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editTenantModal">
                           <div class="mobileTitle">Plan:</div>
                           '.$planInfo.'
                        </a>';
            })
            ->addColumn('plan_end_date', function($row) {
                $endDate = 'Belirsiz';
                
                // Önce trial durumunu kontrol et (tenant tablosundan)
                if ($row->subscription_status === 'trial' && $row->trial_ends_at) {
                    $trialEnd = \Carbon\Carbon::parse($row->trial_ends_at);
                    if ($trialEnd->isFuture()) {
                        $endDate = $trialEnd->format('d.m.Y');
                        $daysLeft = $trialEnd->diffInDays(now());
                        if ($daysLeft <= 15) {
                            $endDate .= " ($daysLeft gün kaldı)";
                        }
                    } else {
                        $endDate = 'Süresi Dolmuş';
                    }
                }
                // Active subscription kontrol et
                elseif ($row->activeSubscription && $row->activeSubscription->ends_at) {
                    $subscriptionEnd = \Carbon\Carbon::parse($row->activeSubscription->ends_at);
                    if ($subscriptionEnd->isFuture()) {
                        $endDate = $subscriptionEnd->format('d.m.Y');
                        $daysLeft = $subscriptionEnd->diffInDays(now());
                        if ($daysLeft <= 15) {
                            $endDate .= " ($daysLeft gün kaldı)";
                        }
                    } else {
                        $endDate = 'Süresi Dolmuş';
                    }
                }
                // Current subscription kontrol et
                elseif ($row->currentSubscription && $row->currentSubscription->ends_at) {
                    $subscriptionEnd = \Carbon\Carbon::parse($row->currentSubscription->ends_at);
                    if ($subscriptionEnd->isFuture()) {
                        $endDate = $subscriptionEnd->format('d.m.Y');
                        $daysLeft = $subscriptionEnd->diffInDays(now());
                        if ($daysLeft <= 15) {
                            $endDate .= " ($daysLeft gün kaldı)";
                        }
                    } else {
                        $endDate = 'Süresi Dolmuş';
                    }
                }
                // Tenant tablosundaki subscription_ends_at kontrol et
                elseif ($row->subscription_ends_at) {
                    $subscriptionEnd = \Carbon\Carbon::parse($row->subscription_ends_at);
                    if ($subscriptionEnd->isFuture()) {
                        $endDate = $subscriptionEnd->format('d.m.Y');
                        $daysLeft = $subscriptionEnd->diffInDays(now());
                        if ($daysLeft <= 15) {
                            $endDate .= " ($daysLeft gün kaldı)";
                        }
                    } else {
                        $endDate = 'Süresi Dolmuş';
                    }
                }
                
                return '<a class="t-link editTenant" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editTenantModal">
                           <div class="mobileTitle">Plan Bitiş:</div>
                           '.$endDate.'
                        </a>';
            })
            ->addColumn('address', function($row) {
                $fullAddress = $row->adres ?? '';
                return '<a class="t-link editTenant address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editTenantModal">
                            <div class="mobileTitle">Adres:</div>' . $fullAddress . '
                        </a>';
            })
           ->addColumn('durum', function($row) {
                $statusBadge = $row->status == 1
                    ? '<span class="badge" style="background-color: #28a745; color: white; padding: 4px 8px; font-size: 11px;"><i class="mdi mdi-check-circle"></i> Aktif</span>'
                    : '<span class="badge bg-danger" style="padding: 4px 8px; font-size: 11px;"><i class="mdi mdi-close-circle"></i> Pasif</span>';
                return '<a class="t-link editTenant" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editTenantModal"><div class="mobileTitle">Durum:</div>'.$statusBadge.'</a>';
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
                if ($row->status == 1) {
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
            ->rawColumns(['id','name','plan','plan_end_date','address','durum','action'])
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

        // Depolama bilgilerini al
        $storageInfo = $tenant->getStorageInfo();
    
            
        return view('frontend.secure.super_admin.edit_tenants', compact(
            'tenant',
            'countries', 
            'periodStats',
            'topServisSayisi',
            'subscriptionHistory',
            'storageInfo'
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

public function getTenantPayments($id)
{
    try {
        $tenant = Tenant::findOrFail($id);
        
        // Tüm ödemeleri getir (Tenant modelindeki getAllPayments metodunu kullan)
        $allPayments = $tenant->getAllPayments();
        
        // Ödeme özetini hesapla
        $summary = [
            'completed' => 0,
            'pending' => 0,
            'failed' => 0,
            'refunded' => 0,
            'canceled' => 0,
            'total_amount' => 0
        ];
        
        foreach ($allPayments as $payment) {
            $amount = floatval($payment['amount'] ?? 0);
            $status = $payment['status'];
            
            // Toplam tutarı hesapla (sadece completed ödemeler)
            if ($status === 'completed') {
                $summary['total_amount'] += $amount;
            }
            
            // Durum bazlı toplamları hesapla
            if (isset($summary[$status])) {
                $summary[$status] += $amount;
            }
        }
        
        // Para birimini formatla
        foreach ($summary as $key => $value) {
            if ($key !== 'total_amount') {
                $summary[$key] = number_format($value, 2, ',', '.');
            }
        }
        $summary['total_amount'] = number_format($summary['total_amount'], 2, ',', '.');
        
        // Ödemeleri tarih sırasına göre sırala (en yeni önce)
        $sortedPayments = $allPayments->sortByDesc('created_at')->values();
        
        return response()->json([
            'success' => true,
            'summary' => $summary,
            'payments' => $sortedPayments,
            'tenant_info' => [
                'id' => $tenant->id,
                'name' => $tenant->firma_adi,
                'email' => $tenant->eposta
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Tenant ödeme bilgileri getirme hatası: ' . $e->getMessage(), [
            'tenant_id' => $id,
            'error' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Ödeme bilgileri yüklenirken bir hata oluştu.',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function getPaymentDetail($tenantId, $paymentType, $paymentId)
{
    try {
        $tenant = Tenant::findOrFail($tenantId);
        
        $paymentDetail = null;
        
        if ($paymentType === 'subscription') {
            $paymentDetail = \App\Models\SubscriptionPayment::where('tenant_id', $tenantId)
                                                           ->where('id', $paymentId)
                                                           ->with(['subscription.plansubs'])
                                                           ->first();
            
            if ($paymentDetail) {
                $paymentDetail->type = 'subscription';
                $paymentDetail->type_label = 'Abonelik Ödemesi';
                $paymentDetail->plan_name = $paymentDetail->subscription->plansubs->name ?? 'Bilinmeyen Plan';
            }
            
        } elseif ($paymentType === 'storage') {
            $paymentDetail = \App\Models\StoragePurchase::where('tenant_id', $tenantId)
                                                       ->where('id', $paymentId)
                                                       ->first();
            
            if ($paymentDetail) {
                $paymentDetail->type = 'storage';
                $paymentDetail->type_label = 'Depolama Paketi';
                $paymentDetail->plan_name = $paymentDetail->storage_gb . ' GB Ek Depolama';
            }
        }
        
        if (!$paymentDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Ödeme kaydı bulunamadı.'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'payment' => $paymentDetail,
            'tenant_info' => [
                'id' => $tenant->id,
                'name' => $tenant->firma_adi
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Ödeme detayı getirme hatası: ' . $e->getMessage(), [
            'tenant_id' => $tenantId,
            'payment_type' => $paymentType,
            'payment_id' => $paymentId
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Ödeme detayı yüklenirken bir hata oluştu.'
        ], 500);
    }
}
public function getPaymentStatistics($tenantId)
{
    try {
        $tenant = Tenant::findOrFail($tenantId);
        
        // Abonelik ödemeleri istatistikleri
        $subscriptionStats = DB::table('subscription_payments')
            ->where('tenant_id', $tenantId)
            ->select([
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN amount ELSE 0 END) as total_completed'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN amount ELSE 0 END) as total_pending'),
                DB::raw('SUM(CASE WHEN status = "failed" THEN amount ELSE 0 END) as total_failed'),
                DB::raw('COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_count'),
                DB::raw('COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_count'),
                DB::raw('COUNT(CASE WHEN status = "failed" THEN 1 END) as failed_count')
            ])
            ->first();
        
        // Depolama ödemeleri istatistikleri
        $storageStats = DB::table('storage_purchases')
            ->where('tenant_id', $tenantId)
            ->select([
                DB::raw('COUNT(*) as total_count'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN amount ELSE 0 END) as total_completed'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN amount ELSE 0 END) as total_pending'),
                DB::raw('SUM(CASE WHEN status = "failed" THEN amount ELSE 0 END) as total_failed'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN storage_gb ELSE 0 END) as total_storage_gb'),
                DB::raw('COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_count'),
                DB::raw('COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_count'),
                DB::raw('COUNT(CASE WHEN status = "failed" THEN 1 END) as failed_count')
            ])
            ->first();
        
        // Son 12 aylık ödeme trendi
        $monthlyTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            
            $monthlyAmount = DB::table('subscription_payments')
                ->where('tenant_id', $tenantId)
                ->where('status', 'completed')
                ->whereRaw('DATE_FORMAT(paid_at, "%Y-%m") = ?', [$monthKey])
                ->sum('amount');
            
            $monthlyStorageAmount = DB::table('storage_purchases')
                ->where('tenant_id', $tenantId)
                ->where('status', 'completed')
                ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$monthKey])
                ->sum('amount');
            
            $monthlyTrend[] = [
                'month' => $date->format('M Y'),
                'subscription_amount' => floatval($monthlyAmount),
                'storage_amount' => floatval($monthlyStorageAmount),
                'total_amount' => floatval($monthlyAmount) + floatval($monthlyStorageAmount)
            ];
        }
        
        return response()->json([
            'success' => true,
            'subscription_stats' => $subscriptionStats,
            'storage_stats' => $storageStats,
            'monthly_trend' => $monthlyTrend,
            'tenant_info' => [
                'id' => $tenant->id,
                'name' => $tenant->firma_adi
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Ödeme istatistikleri getirme hatası: ' . $e->getMessage(), [
            'tenant_id' => $tenantId
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'İstatistikler yüklenirken bir hata oluştu.'
        ], 500);
    }
}
public function getStorageDetails($tenant_id)
{
    try {
        $tenant = Tenant::findOrFail($tenant_id);
        
        $storageInfo = $tenant->getStorageInfo();
        
        $details = [
            'service_photos' => $this->getServicePhotosBreakdown($tenant),
            'stock_photos' => $this->getStockPhotosBreakdown($tenant),
            'other_files' => $this->getOtherFilesBreakdown($tenant)
        ];
        
        return response()->json([
            'success' => true,
            'storage_info' => $storageInfo,
            'details' => $details
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Storage details error', [
            'tenant_id' => $tenant_id,
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Storage detayları alınırken hata oluştu.'
        ], 500);
    }
}
/**
 * Servis fotoğrafları breakdown'ı
 */
private function getServicePhotosBreakdown($tenant)
{
    $photos = \App\Models\ServicePhoto::where('firma_id', $tenant->id)
                         ->selectRaw('COUNT(*) as count, SUM(file_size) as total_size, AVG(file_size) as avg_size')
                         ->first();
    
    return [
        'count' => $photos->count ?? 0,
        'total_size' => $photos->total_size ?? 0,
        'total_size_formatted' => $this->formatBytes($photos->total_size ?? 0),
        'average_size' => $photos->avg_size ?? 0,
        'average_size_formatted' => $this->formatBytes($photos->avg_size ?? 0),
    ];
}

/**
 * Stok fotoğrafları breakdown'ı
 */
private function getStockPhotosBreakdown($tenant)
{
    $stockPhotos = \App\Models\stock_photos::where('kid', $tenant->id)
                              ->selectRaw('COUNT(*) as count, COALESCE(SUM(file_size), 0) as total_size, AVG(file_size) as avg_size')
                              ->first();
    
    return [
        'count' => $stockPhotos->count ?? 0,
        'total_size' => $stockPhotos->total_size ?? 0,
        'total_size_formatted' => $this->formatBytes($stockPhotos->total_size ?? 0),
        'average_size' => $stockPhotos->avg_size ?? 0,
        'average_size_formatted' => $this->formatBytes($stockPhotos->avg_size ?? 0),
    ];
}

/**
 * Diğer dosyalar breakdown'ı
 */
private function getOtherFilesBreakdown($tenant)
{
    // Basit bir hesaplama - daha detaylı yapabilirsiniz
    $count = 0;
    $totalSize = 0;
    
    return [
        'total_count' => $count,
        'total_size' => $totalSize,
        'total_size_formatted' => $this->formatBytes($totalSize),
    ];
}

private function formatBytes($bytes, $precision = 2)
{
    if ($bytes === null || $bytes < 0) {
        return '0 B';
    }
    
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
}