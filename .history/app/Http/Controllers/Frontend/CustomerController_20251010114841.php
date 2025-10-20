<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Il;
use App\Models\Service;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use App\Services\ActivityLogger;


class CustomerController extends Controller
{
       public function AllCustomer($tenant_id, Request $request) {
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
            // SADECE GEREKLİ KOLONLAR
            $data = Customer::query()
                ->select([
                    'id',
                    'firma_id',
                    'adSoyad',
                    'tel1',
                    'tel2',
                    'adres',
                    'il',
                    'ilce',
                    'musteriTipi',
                    'created_at'
                ])
                ->with(['country:id,name', 'state:id,ilceName'])
                ->where('firma_id', $firma->id);

            // FİLTRELERİ UYGULA (Helper metodları kullan)
            $this->applyDateFilters($data, $request);
            $this->applyOtherFilters($data, $request);
            $this->applySearch($data, $request);
            $this->applyOrdering($data, $request);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('id', fn($row) => $this->colId($row))
                ->addColumn('name', fn($row) => $this->colName($row))
                ->addColumn('tel', fn($row) => $this->colTel($row))
                ->addColumn('address', fn($row) => $this->colAddress($row))
                ->addColumn('action', fn($row) => $this->colAction($row))
                ->rawColumns(['id', 'name', 'tel', 'address', 'action'])
                ->make(true);
        }

        return view('frontend.secure.customers.all_customers', compact('firma', 'customers', 'countries'));
    }

    /* ===========================
       ===== Helper Methods ======
       =========================== */

    // Tarih filtrelerini uygula
    private function applyDateFilters($query, Request $request): void
    {
        // 1. Ana daterangepicker'dan gelen tarih var mı?
        $hasMainDateRange = $request->filled('from_date') && $request->filled('to_date');

        // 2. Dashboard'dan gelen tarih var mı?
        $hasDashboardFilter = $request->filled('dashboard_istatistik_tarih1') && 
                            $request->filled('dashboard_istatistik_tarih2');

        // 3. Kullanıcı arama veya filtre yapıyor mu?
        $hasSearch = !empty(trim($request->get('search', '')));
        $hasFilters = $request->filled('tip') || 
                      $request->filled('il') || 
                      $request->filled('ilce');

        // KARAR MANTIGI:
        // - Eğer tarih filtresi varsa, onu kullan
        // - Eğer arama/filtre var ama tarih yoksa, TÜM kayıtlarda ara (tarih kısıtlaması yok)
        // - Eğer hiçbir şey yoksa, tüm müşterileri göster (varsayılan)
        
        if ($hasMainDateRange) {
            // Ana tarih filtresi
            $from = Carbon::createFromFormat('Y-m-d', $request->from_date)->startOfDay();
            $to   = Carbon::createFromFormat('Y-m-d', $request->to_date)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        } 
        elseif ($hasDashboardFilter) {
            // Dashboard'dan gelen tarih
            $startDate = Carbon::parse($request->dashboard_istatistik_tarih1)->startOfDay();
            $endDate = Carbon::parse($request->dashboard_istatistik_tarih2)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        // Eğer arama/filtre varsa ama tarih yoksa, tarih kısıtlaması uygulanmaz (TÜM kayıtlar)
    }

    // Diğer filtreleri uygula
    private function applyOtherFilters($query, Request $request): void
    {
        // Müşteri tipi filtresi (Bireysel/Kurumsal)
        if ($request->filled('tip')) {
            if (in_array($request->tip, [1, 2])) {
                $query->where('musteriTipi', $request->tip);
            }
        }

        // İl filtresi
        if ($request->filled('il')) {
            $query->where('il', $request->il);
        }

        // İlçe filtresi
        if ($request->filled('ilce')) {
            $query->where('ilce', $request->ilce);
        }
    }

    // Arama işlemi
    private function applySearch($query, Request $request): void
    {
        $search = trim($request->get('search', ''));
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('adSoyad', 'LIKE', "%$search%")
                  ->orWhere('tel1', 'LIKE', "%$search%")
                  ->orWhere('tel2', 'LIKE', "%$search%")
                  ->orWhere('tcNo', 'LIKE', "%$search%")
                  ->orWhere('vergiNo', 'LIKE', "%$search%");
            });
        }
    }

    // Sıralama
    private function applyOrdering($query, Request $request): void
    {
        if ($request->has('order')) {
            $order = $request->get('order')[0];
            $columns = $request->get('columns');
            $orderColumn = $columns[$order['column']]['data'];
            $orderDir = $order['dir'];
            $query->orderBy($orderColumn, $orderDir);
        } else {
            $query->orderBy('id', 'desc');
        }
    }

    /* ====== DataTables sütun render yardımcıları ====== */

    private function colId($row): string
    {
        return '<a class="t-link editCustomer address idWrap" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCustomerModal">'.$row->id.'</a>';
    }

    private function colName($row): string
    {
        return '<a class="t-link editCustomer address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCustomerModal"><div class="mobileTitle">Ad Soyad:</div>'.e($row->adSoyad).'</a>';
    }

    private function colTel($row): string
    {
        $telefon = $row->tel1;
        
        if (!empty($telefon) && substr($telefon, 0, 1) !== '0') {
            $telefon = '0' . $telefon;
        }
        
        return '<a class="t-link editCustomer" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCustomerModal"><div class="mobileTitle">Telefon:</div>'.e($telefon).'</a>';
    }

    private function colAddress($row): string
    {
        $address = '';
        
        if (!empty($row->adres)) {
            $address = e($row->adres);
            
            if (!empty($row->country->name) && !empty($row->state->ilceName)) {
                $address .= ' ' . e($row->country->name) . ' / ' . e($row->state->ilceName);
            }
        }
        
        return '<a class="t-link editCustomer address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCustomerModal"><div class="mobileTitle">Adres:</div>'.$address.'</a>';
    }

    private function colAction($row): string
    {
        $deleteUrl = route('delete.customer', [$row->firma_id, $row->id]);
        
        $viewButton = '<a href="javascript:void(0);" data-bs-id="'.$row->id.'" class="btn btn-outline-primary btn-sm editCustomer mobilBtn mbuton2" data-bs-toggle="modal" data-bs-target="#editCustomerModal" title="Görüntüle"><i class="fas fa-eye"></i><span> Görüntüle</span></a>';
        
        $editButton = '<a href="javascript:void(0);" data-bs-id="'.$row->id.'" class="btn btn-outline-warning btn-sm editCustomer mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editCustomerModal" title="Düzenle"><i class="fas fa-edit"></i><span> Düzenle</span></a>';
        
        $deleteButton = '<a href="'.$deleteUrl.'" class="btn btn-outline-danger btn-sm mobilBtn mbuton3" id="delete" title="Sil"><i class="fas fa-trash-alt"></i><span> Sil</span></a>';
        
        return $viewButton . ' ' . $editButton . ' ' . $deleteButton;
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
        $customer = Customer::create([
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

         // Müşteri oluşturma log kaydı
        ActivityLogger::logCustomerCreated($customer->id, $request->name);

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

         // Müşteri güncelleme log kaydı
        ActivityLogger::logCustomerUpdated($customer->id, $request->name);

        $customer = Customer::with(['country','state'])->findOrFail($customer->id);
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

        $customer_services = Service::where('firma_id', $tenant_id)->where('musteri_id',$id)->get();

        return view('frontend.secure.customers.customer_services', compact('customer_services','firma'));
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
            // Müşteri silme log kaydı (silmeden önce bilgileri al)
            $customerName = $customer->adSoyad;
            $customerId = $customer->id;
            
            $customer->delete();

            // Log kaydı
            ActivityLogger::logCustomerDeleted($customerId, $customerName);

            $notification = array(
                'message' => 'Müşteri başarıyla silindi.',
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        }
    }
}
