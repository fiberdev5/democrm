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
        // ÖNCELİKLE: Kullanıcı yetkilerini BİR KEZ kontrol et
        $user = auth()->user();
        $canEdit = $user->can('Müşterileri Düzenleyebilir');
        $canView = $user->can('Müşterileri Görebilir');
        $canDelete = $user->can('Müşterileri Silebilir');
        
        // Sadece gerekli kolonları seç (performans için)
        $data = Customer::select([
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
        ])->where('firma_id', $firma->id);

        // Dashboard'dan gelen tarih filtresini uygula
        if ($request->filled('dashboard_istatistik_tarih1') && $request->filled('dashboard_istatistik_tarih2')) {
            $startDate = Carbon::parse($request->get('dashboard_istatistik_tarih1'))->startOfDay();
            $endDate = Carbon::parse($request->get('dashboard_istatistik_tarih2'))->endOfDay();
            $data->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Müşteri tipi filtresi
        if ($request->filled('tip')) {
            $data->where('musteriTipi', $request->get('tip'));
        }
      
        // İl filtresi
        if ($request->get('il')) {
            $data->where('il', $request->get('il'));
        }

        // İlçe filtresi
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
            ->addColumn('id', fn($row) => $this->colId($row))
            ->addColumn('name', fn($row) => $this->colName($row))
            ->addColumn('tel', fn($row) => $this->colTel($row))
            ->addColumn('address', fn($row) => $this->colAddress($row))
            // Yetkileri closure'a geçir
            ->addColumn('action', function($row) use ($canView, $canEdit, $canDelete) {
                return $this->colActions($row, $canView, $canEdit, $canDelete);
            })
            ->filter(function ($instance) use ($request) {
                if (!empty($request->get('search'))) {
                    $search = $request->get('search');
                    $instance->where('adSoyad', 'LIKE', "%$search%");
                }
            })
            ->rawColumns(['id', 'name', 'tel', 'address', 'action'])
            ->make(true);
    }
    
    return view('frontend.secure.customers.all_customers', compact('firma', 'customers', 'countries'));
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
