<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Il;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class SuperAdminController extends Controller
{
    public function allTenants(Request $request)
    {
        // Super Admin kontrolü - sadece Admin rolü görebilir
        if (!Auth::user()->hasRole('Admin')) {
            $notification = [
                'message' => 'Bu sayfaya erişim yetkiniz yok.',
                'alert-type' => 'danger'
            ];
            return redirect()->route('dashboard')->with($notification);
        }

        $countries = Il::orderBy('name', 'ASC')->get();

        if ($request->ajax()) {
            $data = Tenant::with('ils', 'ilces');

            // Filtreleme
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

            if ($request->get('status') !== '') {
                $data->where('status', $request->get('status'));
            }

            // Arama
            if ($request->get('search')) {
                $search = $request->get('search');
                $data->where(function($query) use ($search) {
                    $query->where('firma_adi', 'LIKE', "%{$search}%")
                          ->orWhere('name', 'LIKE', "%{$search}%")
                          ->orWhere('eposta', 'LIKE', "%{$search}%")
                          ->orWhere('tel1', 'LIKE', "%{$search}%");
                });
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
                ->addColumn('firma_info', function($row) {
                    $userCount = User::where('tenant_id', $row->id)->count();
                    return '<div>
                        <strong>' . $row->firma_adi . '</strong><br>
                        <small class="text-muted">' . $row->name . '</small><br>
                        <span class="badge bg-info">' . $userCount . ' kullanıcı</span>
                    </div>';
                })
                ->addColumn('contact', function($row) {
                    $telefon = $row->tel1;
                    if (substr($telefon, 0, 1) !== '0') {
                        $telefon = '0' . $telefon;
                    }
                    return '<div>
                        <i class="mdi mdi-phone me-1"></i>' . $telefon . '<br>
                        <i class="mdi mdi-email me-1"></i>' . $row->eposta . '
                    </div>';
                })
                ->addColumn('location', function($row) {
                    $location = '';
                    if ($row->ils && $row->ilces) {
                        $location = $row->ils->name . ' / ' . $row->ilces->ilceName;
                    }
                    return '<small class="text-muted">' . $location . '</small>';
                })
                ->addColumn('subscription', function($row) {
                    $trialBadge = '';
                    if ($row->isOnTrial()) {
                        $remainingDays = $row->getRemainingTrialDays();
                        $trialBadge = '<span class="badge bg-warning text-dark">Trial (' . $remainingDays . ' gün)</span>';
                    } elseif ($row->hasActiveSubscription()) {
                        $trialBadge = '<span class="badge bg-success">Aktif Abonelik</span>';
                    } else {
                        $trialBadge = '<span class="badge bg-danger">Süre Dolmuş</span>';
                    }
                    
                    $statusBadge = $row->status == 1
                        ? '<span class="badge bg-success">Aktif</span>'
                        : '<span class="badge bg-secondary">Pasif</span>';
                    
                    return $trialBadge . '<br>' . $statusBadge;
                })
                ->addColumn('actions', function($row) {
                    $actions = '';
                    
                    // Firma Detayları Butonu
                    $actions .= '<button class="btn btn-info btn-sm me-1 view-tenant" 
                                        data-tenant-id="' . $row->id . '" 
                                        title="Firma Detayları">
                                    <i class="fas fa-eye"></i>
                                </button>';
                    
                    // Firma Kullanıcıları Butonu
                    $actions .= '<button class="btn btn-primary btn-sm me-1 view-users" 
                                        data-tenant-id="' . $row->id . '" 
                                        title="Kullanıcıları Görüntüle">
                                    <i class="fas fa-users"></i>
                                </button>';
                    
                    // Impersonation Butonu
                    $actions .= '<button class="btn btn-warning btn-sm me-1 impersonate-tenant" 
                                        data-tenant-id="' . $row->id . '" 
                                        title="Firma Yetkilisi Olarak Giriş Yap">
                                    <i class="fas fa-user-secret"></i>
                                </button>';
                    
                    // Durum Değiştir Butonu
                    $statusBtn = $row->status == 1 
                        ? '<button class="btn btn-secondary btn-sm me-1 change-status" data-tenant-id="' . $row->id . '" title="Pasif Et"><i class="fas fa-pause"></i></button>'
                        : '<button class="btn btn-success btn-sm me-1 change-status" data-tenant-id="' . $row->id . '" title="Aktif Et"><i class="fas fa-play"></i></button>';
                    
                    $actions .= $statusBtn;
                    
                    return '<div class="d-flex">' . $actions . '</div>';
                })
                ->rawColumns(['firma_info', 'contact', 'location', 'subscription', 'actions'])
                ->make(true);
        }

        return view('backend.super_admin.all_tenants', compact('countries'));
    }

    public function getTenantUsers($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $currentUser = Auth::user();

        if (!$currentUser->hasRole('Admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Yetkiniz yok.'
            ], 403);
        }

        $users = User::where('tenant_id', $tenantId)
                    ->with('roles')
                    ->orderBy('name')
                    ->get()
                    ->map(function($user) {
                        return [
                            'user_id' => $user->user_id,
                            'name' => $user->name,
                            'username' => $user->username,
                            'eposta' => $user->eposta,
                            'roles' => $user->roles->pluck('name'),
                            'status' => $user->status,
                            'can_be_impersonated' => $user->canBeImpersonated(),
                            'last_login' => $user->last_login_at ?? null,
                            'ayrilma_tarihi' => $user->ayrilmaTarihi
                        ];
                    });

        return response()->json([
            'success' => true,
            'tenant' => [
                'id' => $tenant->id,
                'firma_adi' => $tenant->firma_adi,
                'name' => $tenant->name
            ],
            'users' => $users
        ]);
    }

    public function impersonateTenantOwner($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $currentUser = Auth::user();

        if (!$currentUser->hasRole('Admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Yetkiniz yok.'
            ], 403);
        }

        // Firma sahibini (Patron rolündeki kullanıcıyı) bul
        $tenantOwner = User::where('tenant_id', $tenantId)
                          ->whereHas('roles', function($query) {
                              $query->where('name', 'Patron');
                          })
                          ->first();

        if (!$tenantOwner) {
            return response()->json([
                'success' => false,
                'message' => 'Bu firma için Patron rolünde kullanıcı bulunamadı.'
            ], 404);
        }

        // Super admin özel izni - Patron'u da impersonate edebilir
        return $this->startImpersonation($currentUser, $tenantOwner, 'Super Admin tarafından firma yönetimi için');
    }

    private function startImpersonation($impersonator, $target, $reason = null)
    {
        // Aktif impersonation varsa sonlandır
        $activeImpersonation = $impersonator->getActiveImpersonation();
        if ($activeImpersonation) {
            $activeImpersonation->update(['ended_at' => now()]);
        }

        // Yeni impersonation kaydı
        $impersonation = \App\Models\UserImpersonation::create([
            'impersonator_id' => $impersonator->user_id,
            'impersonated_id' => $target->user_id,
            'tenant_id' => $target->tenant_id,
            'started_at' => now(),
            'ip_address' => request()->ip(),
            'reason' => $reason ?? 'Super Admin impersonation'
        ]);

        // Session ayarları
        session([
            'impersonator_id' => $impersonator->user_id,
            'impersonated_user_id' => $target->user_id,
            'impersonation_id' => $impersonation->id,
            'impersonation_started_at' => now()
        ]);

        Auth::setUser($target);

        return response()->json([
            'success' => true,
            'message' => "{$target->name} ({$target->tenant->firma_adi}) olarak giriş yapıldı.",
            'redirect_url' => route('dashboard')
        ]);
    }

    public function getTenantStats($tenantId)
    {
        $tenant = Tenant::with(['users' => function($query) {
            $query->with('roles');
        }])->findOrFail($tenantId);

        $stats = [
            'user_count' => $tenant->users->count(),
            'active_users' => $tenant->users->where('status', 1)->count(),
            'roles_distribution' => $tenant->users->groupBy(function($user) {
                return $user->roles->first()->name ?? 'Rolsüz';
            })->map->count(),
            'recent_users' => $tenant->users()->where('created_at', '>=', now()->subDays(7))->count()
        ];

        return response()->json([
            'success' => true,
            'tenant' => $tenant->only(['id', 'firma_adi', 'name', 'status']),
            'stats' => $stats
        ]);
    }
}