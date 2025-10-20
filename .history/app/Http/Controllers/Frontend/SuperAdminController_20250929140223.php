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
        
        // Tüm ödemeleri getir
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
        
        // Raw değerler ile formatlanmış değerleri ayrı ayrı gönder
        $summaryResponse = [];
        foreach ($summary as $key => $value) {
            // Raw değer
            $summaryResponse[$key] = $value;
            // Formatlanmış değer - ayrı key ile
            $summaryResponse[$key . '_formatted'] = number_format($value, 2, '.', ',');
        }
        
        // Ödemeleri tarih sırasına göre sırala
        $sortedPayments = $allPayments->sortByDesc('created_at')->values();
        
        return response()->json([
            'success' => true,
            'summary' => $summaryResponse,  // Bu şekilde gönder
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
    $breakdown = [
        'support_attachments' => $this->getSupportAttachmentsBreakdown($tenant),
        'dealer_documents' => $this->getDealerDocumentsBreakdown($tenant),
        'invoice_documents' => $this->getInvoiceDocumentsBreakdown($tenant)
    ];
    
    $totalSize = array_sum(array_column($breakdown, 'size'));
    $totalCount = array_sum(array_column($breakdown, 'count'));
    
    return [
        'total_count' => $totalCount,
        'total_size' => $totalSize,
        'total_size_formatted' => $this->formatBytes($totalSize),
        'breakdown' => $breakdown
    ];
}

private function getSupportAttachmentsBreakdown($tenant)
{
    $count = 0;
    $totalSize = 0;

    // Önce tenant'ın user ID'lerini al
    $userIds = DB::table('tb_user')
                 ->where('tenant_id', $tenant->id)
                 ->pluck('user_id');

    $supportReplies = DB::table('support_ticket_replies')
                        ->whereIn('user_id', $userIds)
                        ->whereNotNull('attachments')
                        ->where('attachments', '!=', '')
                        ->get();

    foreach ($supportReplies as $reply) {
        $attachments = json_decode($reply->attachments, true);
        if (is_array($attachments)) {
            foreach ($attachments as $attachment) {
                if (isset($attachment['path'])) {
                    $filePath = storage_path('app/public/' . $attachment['path']);
                    if (file_exists($filePath)) {
                        $size = filesize($filePath);
                        $totalSize += $size;
                        $count++;
                    }
                }
            }
        }
    }

    return [
        'count' => $count,
        'size' => $totalSize,
        'size_formatted' => $this->formatBytes($totalSize)
    ];
}

private function getDealerDocumentsBreakdown($tenant)
{
    $dealerDocsPath = storage_path("app/public/dealers-documents/firma_{$tenant->firma_slug}");
    
    if (!is_dir($dealerDocsPath)) {
        return ['count' => 0, 'size' => 0, 'size_formatted' => '0 B'];
    }
    
    $count = $this->countFilesInDirectory($dealerDocsPath);
    $size = $this->calculateDirectorySize($dealerDocsPath);
    
    return [
        'count' => $count,
        'size' => $size,
        'size_formatted' => $this->formatBytes($size)
    ];
}

private function getInvoiceDocumentsBreakdown($tenant)
{
    $count = 0;
    $totalSize = 0;
    
    $invoices = DB::table('invoices')
                   ->where('firma_id', $tenant->id)
                   ->whereNotNull('faturaPdf')
                   ->where('faturaPdf', '!=', '')
                   ->get();
    
    foreach ($invoices as $invoice) {
        $filePath = public_path($invoice->faturaPdf);
        if (file_exists($filePath)) {
            $size = filesize($filePath);
            $totalSize += $size;
            $count++;
        }
    }
    
    return [
        'count' => $count,
        'size' => $totalSize,
        'size_formatted' => $this->formatBytes($totalSize)
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

/**
 * Tüm firmaların ödeme geçmişini listele
 */
public function allPaymentHistory(Request $request)
{
    $superAdminTenant = Tenant::where('firma_adi', 'Super Admin Panel')
                             ->orWhere('name', 'Super Admin')
                             ->first();
    
    $superAdminTenantId = $superAdminTenant ? $superAdminTenant->id : null;

    if ($request->ajax()) {
        // Filtreleme parametreleri
        $dateFrom = $request->get('date_from', now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $type = $request->get('type', 'all');
        $tenantId = $request->get('tenant_id');
        $search = $request->get('search')['value'] ?? '';

        $tenants = Tenant::when($superAdminTenantId, function($query) use ($superAdminTenantId) {
            return $query->where('id', '!=', $superAdminTenantId);
        })->orderBy('firma_adi')->get();

        $allPayments = collect();

        $tenantsToProcess = $tenantId 
            ? Tenant::where('id', $tenantId)->get() 
            : $tenants;

        foreach ($tenantsToProcess as $tenant) {
            // Abonelik ödemeleri
            if (in_array($type, ['all', 'subscription'])) {
                if (method_exists($tenant, 'subscriptionPayments')) {
                    $subscriptionPayments = $tenant->subscriptionPayments()
                        ->when($dateFrom, function($query) use ($dateFrom) {
                            return $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
                        })
                        ->when($dateTo, function($query) use ($dateTo) {
                            return $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
                        })
                        ->orderBy('created_at', 'desc')
                        ->get()
                        ->map(function($payment) use ($tenant) {
                            return [
                                'id' => $payment->id,
                                'tenant_id' => $tenant->id,
                                'tenant_name' => $tenant->firma_adi,
                                'type' => 'subscription',
                                'type_label' => 'Abonelik',
                                'description' => $this->getSubscriptionPaymentDescriptionForAdmin($payment),
                                'amount' => number_format($payment->amount ?? 0, 2) . ' ' . strtoupper($payment->currency ?? 'TL'),
                                'status' => $payment->status,
                                'status_label' => $this->getStatusLabel($payment->status),
                                'invoice_status' => !empty($payment->invoice_path) ? '<span class=""><i class="fas fa-check mr-1"></i>Mevcut</span>' : '<span class=""><i class="fas fa-clock mr-1"></i>Bekleniyor</span>',
                                'created_at' => '<strong>' . $payment->created_at->format('d.m.Y') . '</strong><br><small class="text-muted">' . $payment->created_at->format('H:i') . '</small>',
                                'created_at_timestamp' => $payment->created_at->timestamp
                            ];
                        });

                    $allPayments = $allPayments->concat($subscriptionPayments);
                }
            }

            // Depolama ödemeleri
            if (in_array($type, ['all', 'storage'])) {
                if (method_exists($tenant, 'storagePurchases')) {
                    $storagePurchases = $tenant->storagePurchases()
                        ->when($dateFrom, function($query) use ($dateFrom) {
                            return $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
                        })
                        ->when($dateTo, function($query) use ($dateTo) {
                            return $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
                        })
                        ->orderBy('created_at', 'desc')
                        ->get()
                        ->map(function($purchase) use ($tenant) {
                            $paymentResponse = is_string($purchase->payment_response) 
                                ? json_decode($purchase->payment_response, true) 
                                : $purchase->payment_response;
                                
                            return [
                                'id' => $purchase->id,
                                'tenant_id' => $tenant->id,
                                'tenant_name' => $tenant->firma_adi,
                                'type' => 'storage',
                                'type_label' => 'Depolama',
                                'description' => $this->getStorageDescriptionForAdmin($purchase),
                                'amount' => number_format($purchase->amount, 2) . ' ' . strtoupper($paymentResponse['currency'] ?? 'TL'),
                                'status' => $purchase->status,
                                'status_label' => $this->getStatusLabel($purchase->status),
                                'invoice_status' => !empty($purchase->invoice_path) ? '<span class="badge badge-success"><i class="fas fa-check mr-1"></i>Mevcut</span>' : '<span class=""><i class="fas fa-clock mr-1"></i>Bekleniyor</span>',
                                'created_at' => '<strong>' . $purchase->created_at->format('d.m.Y') . '</strong><br><small class="text-muted">' . $purchase->created_at->format('H:i') . '</small>',
                                'created_at_timestamp' => $purchase->created_at->timestamp
                            ];
                        });

                    $allPayments = $allPayments->concat($storagePurchases);
                }
            }
        }

        // Arama filtresi
        if (!empty($search)) {
            $allPayments = $allPayments->filter(function($payment) use ($search) {
                return stripos($payment['tenant_name'], $search) !== false ||
                       stripos($payment['description'], $search) !== false ||
                       stripos($payment['type_label'], $search) !== false;
            });
        }

        // Sıralama
        $allPayments = $allPayments->sortByDesc('created_at_timestamp')->values();

        return DataTables::of($allPayments)
            ->addIndexColumn()
            ->editColumn('status_label', function($row) {
                $statusClass = match($row['status']) {
                    'active', 'completed' => 'success',
                    'pending' => 'warning',
                    'cancelled' => 'danger',
                    'expired' => 'secondary',
                    default => 'dark'
                };
                return '<span class="' . $statusClass . '">' . $row['status_label'] . '</span>';
            })
            ->rawColumns(['status_label', 'invoice_status', 'created_at'])
            ->make(true);
    }

    // Normal sayfa yüklemesi için
    $tenants = Tenant::when($superAdminTenantId, function($query) use ($superAdminTenantId) {
        return $query->where('id', '!=', $superAdminTenantId);
    })->orderBy('firma_adi')->get();

    $dateFrom = $request->get('date_from', now()->subMonth()->format('Y-m-d'));
    $dateTo = $request->get('date_to', now()->format('Y-m-d'));
    $type = $request->get('type', 'all');
    $tenantId = $request->get('tenant_id');

    return view('frontend.secure.super_admin.payment_history', compact(
        'tenants',
        'dateFrom',
        'dateTo',
        'type',
        'tenantId'
    ));
}
/**
 * Yardımcı methodlar
 */
private function getSubscriptionPaymentDescriptionForAdmin($payment)
{
    $description = 'Abonelik Ödemesi';
    
    if (!empty($payment->subscription_id)) {
        $description .= " (ID: {$payment->subscription_id})";
    }
    
    return $description;
}

private function getStorageDescriptionForAdmin($purchase)
{
    return "Ek Depolama - " . ($purchase->storage_gb ?? 0) . " GB";
}

private function extractPaymentMethodUnifiedForAdmin($purchase)
{
    if (!empty($purchase->payment_method)) {
        return $this->formatPaymentType($purchase->payment_method);
    }
    
    if (!empty($purchase->payment_response)) {
        $paymentResponse = is_string($purchase->payment_response) 
            ? json_decode($purchase->payment_response, true) 
            : $purchase->payment_response;
        
        if (is_array($paymentResponse) && isset($paymentResponse['payment_type'])) {
            return $this->formatPaymentType($paymentResponse['payment_type']);
        }
    }
    
    return 'Belirtilmemiş';
}

private function getAllPaymentMethods($tenants)
{
    $methods = collect();

    foreach ($tenants as $tenant) {
        if (method_exists($tenant, 'subscriptionPayments')) {
            $subscriptionMethods = $tenant->subscriptionPayments()
                ->whereNotNull('payment_method')
                ->pluck('payment_method')
                ->unique();
            $methods = $methods->concat($subscriptionMethods);
        }
    }

    return $methods->unique()->sort()->values();
}

private function formatPaymentType($paymentType)
{
    $types = [
        'card' => 'Kredi Kartı',
        'credit_card' => 'Kredi Kartı',
        'bank_transfer' => 'Banka Havalesi',
        'eft' => 'EFT',
        'cash' => 'Nakit',
        'paytr' => 'PayTR',
        'iyzico' => 'Iyzico'
    ];

    return $types[$paymentType] ?? ucfirst(str_replace('_', ' ', $paymentType));
}

private function getStatusLabel($status)
{
    $labels = [
        'active' => 'Aktif',
        'completed' => 'Tamamlandı',
        'pending' => 'Beklemede',
        'cancelled' => 'İptal Edildi',
        'expired' => 'Süresi Doldu',
        'failed' => 'Başarısız',
        'paid' => 'Ödendi'
    ];

    return $labels[$status] ?? ucfirst($status);
}
/**
 * Tüm firmaların ödeme geçmişini Excel'e aktar
 */
public function exportAllPayments(Request $request)
{
    // Super Admin panelini hariç tut
    $superAdminTenant = Tenant::where('firma_adi', 'Super Admin Panel')
                             ->orWhere('name', 'Super Admin')
                             ->first();
    
    $superAdminTenantId = $superAdminTenant ? $superAdminTenant->id : null;

    // Filtreleme parametreleri
    $dateFrom = $request->get('date_from', now()->subMonth()->format('Y-m-d'));
    $dateTo = $request->get('date_to', now()->format('Y-m-d'));
    $paymentMethod = $request->get('payment_method');
    $status = $request->get('status');
    $type = $request->get('type', 'all');
    $tenantId = $request->get('tenant_id');

    // Tüm firmaları getir
    $tenants = Tenant::when($superAdminTenantId, function($query) use ($superAdminTenantId) {
        return $query->where('id', '!=', $superAdminTenantId);
    })->orderBy('firma_adi')->get();

    $allPayments = collect();

    // Seçili firma varsa sadece o firmayı, yoksa tüm firmaları işle
    $tenantsToProcess = $tenantId 
        ? Tenant::where('id', $tenantId)->get() 
        : $tenants;

    foreach ($tenantsToProcess as $tenant) {
        try {
            // Abonelik ödemeleri
            $subscriptionPayments = collect();
            if (method_exists($tenant, 'subscriptionPayments')) {
                $subscriptionPayments = $tenant->subscriptionPayments()
                    ->when($dateFrom, function($query) use ($dateFrom) {
                        return $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
                    })
                    ->when($dateTo, function($query) use ($dateTo) {
                        return $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
                    })
                    ->when($paymentMethod, function($query) use ($paymentMethod) {
                        return $query->where('payment_method', $paymentMethod);
                    })
                    ->when($status, function($query) use ($status) {
                        return $query->where('status', $status);
                    })
                    ->when($type && $type !== 'all', function($query) use ($type) {
                        if ($type === 'storage') {
                            return $query->whereRaw('1=0');
                        }
                        return $query;
                    })
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($payment) use ($tenant) {
                        return [
                            'id' => $payment->id,
                            'tenant_name' => $tenant->firma_adi,
                            'type_label' => 'Abonelik',
                            'description' => $this->getSubscriptionPaymentDescriptionForAdmin($payment),
                            'amount' => number_format($payment->amount ?? 0, 2, ',', '.'),
                            'currency' => $payment->currency ?? 'TL',
                            'payment_method' => $payment->payment_method ?: 'Belirtilmemiş',
                            'status_label' => $this->getStatusLabel($payment->status),
                            'created_at' => $payment->created_at->format('d.m.Y H:i'),
                            'paid_at' => $payment->paid_at ? $payment->paid_at->format('d.m.Y H:i') : '-',
                            'transaction_id' => $payment->transaction_id ?: '-',
                            'gateway' => $payment->gateway ?: '-',
                            'has_invoice' => !empty($payment->invoice_path) ? 'Mevcut' : 'Bekleniyor'
                        ];
                    });
            }

            // Depolama ödemeleri
            $storagePurchases = collect();
            if (method_exists($tenant, 'storagePurchases')) {
                $storagePurchases = $tenant->storagePurchases()
                    ->when($dateFrom, function($query) use ($dateFrom) {
                        return $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
                    })
                    ->when($dateTo, function($query) use ($dateTo) {
                        return $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
                    })
                    ->when($paymentMethod, function($query) use ($paymentMethod) {
                        return $query->where(function($q) use ($paymentMethod) {
                            $q->whereJsonContains('payment_response->payment_type', 'card')
                              ->orWhere('payment_method', $paymentMethod);
                        });
                    })
                    ->when($status, function($query) use ($status) {
                        return $query->where('status', $status)
                                     ->orWhereJsonContains('payment_response->status', $status);
                    })
                    ->when($type && $type !== 'all', function($query) use ($type) {
                        if ($type === 'subscription') {
                            return $query->whereRaw('1=0');
                        }
                        return $query;
                    })
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($purchase) use ($tenant) {
                        $paymentResponse = is_string($purchase->payment_response) 
                            ? json_decode($purchase->payment_response, true) 
                            : ($purchase->payment_response ?? []);
                            
                        return [
                            'id' => $purchase->id,
                            'tenant_name' => $tenant->firma_adi,
                            'type_label' => 'Depolama',
                            'description' => $this->getStorageDescriptionForAdmin($purchase),
                            'amount' => number_format($purchase->amount ?? 0, 2, ',', '.'),
                            'currency' => $paymentResponse['currency'] ?? 'TL',
                            'payment_method' => $this->formatPaymentType($paymentResponse['payment_type'] ?? 'Belirtilmemiş'),
                            'status_label' => $this->getStatusLabel($purchase->status),
                            'created_at' => $purchase->created_at->format('d.m.Y H:i'),
                            'paid_at' => isset($purchase->purchased_at) ? $purchase->purchased_at->format('d.m.Y H:i') : '-',
                            'transaction_id' => $paymentResponse['merchant_oid'] ?? ($purchase->payment_token ?? '-'),
                            'gateway' => isset($paymentResponse['payment_type']) ? 'PayTR' : 'Depolama Sistemi',
                            'has_invoice' => !empty($purchase->invoice_path) ? 'Mevcut' : 'Bekleniyor'
                        ];
                    });
            }

            $allPayments = $allPayments->concat($subscriptionPayments)->concat($storagePurchases);

        } catch (\Exception $e) {
            \Log::error('Export error for tenant: ' . $tenant->id, [
                'error' => $e->getMessage()
            ]);
            continue;
        }
    }

    // Tarihe göre sırala
    $payments = $allPayments->sortByDesc('created_at')->values();

    // CSV dosya adı
    $filename = 'tum-firmalar-odeme-gecmisi-' . now()->format('Y-m-d') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        'Cache-Control' => 'no-cache, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0'
    ];

    $callback = function() use ($payments) {
        $file = fopen('php://output', 'w');
        
        // BOM for UTF-8 Excel compatibility
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Header
        fputcsv($file, [
            'ID',
            'Firma',
            'Tür', 
            'Açıklama',
            'Tutar',
            'Para Birimi',
            'Ödeme Yöntemi',
            'Durum',
            'Oluşturma Tarihi',
            'Ödeme Tarihi',
            'İşlem ID',
            'Gateway',
            'Fatura Durumu'
        ], ';');

        // Data
        foreach ($payments as $payment) {
            try {
                fputcsv($file, [
                    $payment['id'] ?? '',
                    $payment['tenant_name'] ?? '',
                    $payment['type_label'] ?? '',
                    $payment['description'] ?? '',
                    $payment['amount'] ?? '0,00',
                    $payment['currency'] ?? 'TL',
                    $payment['payment_method'] ?? 'Belirtilmemiş',
                    $payment['status_label'] ?? '',
                    $payment['created_at'] ?? '',
                    $payment['paid_at'] ?? '-',
                    $payment['transaction_id'] ?? '-',
                    $payment['gateway'] ?? '-',
                    $payment['has_invoice'] ?? 'Bekleniyor'
                ], ';');
            } catch (\Exception $e) {
                \Log::error('CSV row error: ' . $e->getMessage());
                continue;
            }
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

public function getPaymentTotals(Request $request)
{
    $superAdminTenant = Tenant::where('firma_adi', 'Super Admin Panel')
                             ->orWhere('name', 'Super Admin')
                             ->first();
    
    $superAdminTenantId = $superAdminTenant ? $superAdminTenant->id : null;

    $dateFrom = $request->get('date_from', now()->subMonth()->format('Y-m-d'));
    $dateTo = $request->get('date_to', now()->format('Y-m-d'));
    $type = $request->get('type', 'all');
    $tenantId = $request->get('tenant_id');

    $tenants = Tenant::when($superAdminTenantId, function($query) use ($superAdminTenantId) {
        return $query->where('id', '!=', $superAdminTenantId);
    })->orderBy('firma_adi')->get();

    $allPayments = collect();

    $tenantsToProcess = $tenantId 
        ? Tenant::where('id', $tenantId)->get() 
        : $tenants;

    foreach ($tenantsToProcess as $tenant) {
        // Abonelik ödemeleri
        if (in_array($type, ['all', 'subscription'])) {
            if (method_exists($tenant, 'subscriptionPayments')) {
                $subscriptionPayments = $tenant->subscriptionPayments()
                    ->when($dateFrom, function($query) use ($dateFrom) {
                        return $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
                    })
                    ->when($dateTo, function($query) use ($dateTo) {
                        return $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
                    })
                    ->get()
                    ->map(function($payment) use ($tenant) {
                        return [
                            'type' => 'subscription',
                            'status' => $payment->status,
                            'amount' => $payment->amount ?? 0
                        ];
                    });

                $allPayments = $allPayments->concat($subscriptionPayments);
            }
        }

        // Depolama ödemeleri
        if (in_array($type, ['all', 'storage'])) {
            if (method_exists($tenant, 'storagePurchases')) {
                $storagePurchases = $tenant->storagePurchases()
                    ->when($dateFrom, function($query) use ($dateFrom) {
                        return $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
                    })
                    ->when($dateTo, function($query) use ($dateTo) {
                        return $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
                    })
                    ->get()
                    ->map(function($purchase) use ($tenant) {
                        return [
                            'type' => 'storage',
                            'status' => $purchase->status,
                            'amount' => $purchase->amount ?? 0
                        ];
                    });

                $allPayments = $allPayments->concat($storagePurchases);
            }
        }
    }

    // Toplamları hesapla
    $summaryStats = [
        'completed' => number_format($allPayments->where('status', 'completed')->sum('amount'), 2, ',', '.') . ' ₺',
        'pending' => number_format($allPayments->where('status', 'pending')->sum('amount'), 2, ',', '.') . ' ₺',
        'failed' => number_format($allPayments->where('status', 'failed')->sum('amount'), 2, ',', '.') . ' ₺',
        'total' => number_format($allPayments->sum('amount'), 2, ',', '.') . ' ₺',
        
        'subscription_completed' => number_format($allPayments->where('type', 'subscription')->where('status', 'completed')->sum('amount'), 2, ',', '.') . ' ₺',
        'subscription_pending' => number_format($allPayments->where('type', 'subscription')->where('status', 'pending')->sum('amount'), 2, ',', '.') . ' ₺',
        'subscription_failed' => number_format($allPayments->where('type', 'subscription')->where('status', 'failed')->sum('amount'), 2, ',', '.') . ' ₺',
        'subscription_total' => number_format($allPayments->where('type', 'subscription')->sum('amount'), 2, ',', '.') . ' ₺',
        
        'storage_completed' => number_format($allPayments->where('type', 'storage')->where('status', 'completed')->sum('amount'), 2, ',', '.') . ' ₺',
        'storage_pending' => number_format($allPayments->where('type', 'storage')->where('status', 'pending')->sum('amount'), 2, ',', '.') . ' ₺',
        'storage_failed' => number_format($allPayments->where('type', 'storage')->where('status', 'failed')->sum('amount'), 2, ',', '.') . ' ₺',
        'storage_total' => number_format($allPayments->where('type', 'storage')->sum('amount'), 2, ',', '.') . ' ₺',
    ];

    return response()->json($summaryStats);
}
}