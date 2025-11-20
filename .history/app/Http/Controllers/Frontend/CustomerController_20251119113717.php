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
use Illuminate\Support\Facades\Cache;
use App\Services\VerimorSantralService;
use Illuminate\Support\Facades\Log;



class CustomerController extends Controller
{
    public function AllCustomer($tenant_id, Request $request) {
        // Firma bilgisi
        $firma = Tenant::where('id', $tenant_id)->first();
        if (!$firma) {
            return redirect()->route('giris')->with([
                'message' => 'Firma bulunamadı.',
                'alert-type' => 'danger',
            ]);
        }
        
        $countries = Il::orderBy('name', 'ASC')->get();
        
        // $customers = Customer::where('firma_id', $firma->id)->get(); // Bu satır artık gerekli değil, DataTable AJAX ile çekiyor

        if ($request->ajax()) {           
            $data = Customer::query()->where('firma_id', $firma->id); // Firma ID'sine göre başlangıç filtresi

            // Tarih filtreleme mantığı (Servisler Controller'ından uyarlandı)
            $hasUserSelectedCustomerDate = $request->filled('from_date_customer') && $request->filled('to_date_customer') && !$this->isDefaultCustomerDateRange($request);
            $hasDashboardDate    = $request->filled('dashboard_istatistik_tarih1') && $request->filled('dashboard_istatistik_tarih2');
            $hasSearchOrOtherFilters = !empty(trim($request->get('search', ''))) || 
                                        $request->filled('tip') || 
                                        $request->filled('il') || 
                                        $request->filled('ilce');

            if ($hasUserSelectedCustomerDate) {
                // Müşteri sayfasındaki tarih filtresi en yüksek önceliğe sahiptir
                $this->applyMainCustomerDateRange($data, $request);
            } elseif ($hasDashboardDate && !$hasSearchOrOtherFilters) {
                // Müşteri tarih filtresi yoksa ve dashboard tarihi varsa, onu uygula (ancak başka arama/filtre yoksa)
                $startDate = Carbon::parse($request->get('dashboard_istatistik_tarih1'))->startOfDay();
                $endDate = Carbon::parse($request->get('dashboard_istatistik_tarih2'))->endOfDay();
                $data->whereBetween('created_at', [$startDate, $endDate]);
            } elseif (!$hasUserSelectedCustomerDate && !$hasDashboardDate && !$hasSearchOrOtherFilters) {
                // Hiçbir tarih veya arama/filtre yoksa, varsayılan son 3 günü uygula
                $from = Carbon::today()->subDays(2)->startOfDay();
                $to   = Carbon::today()->endOfDay();
                $data->whereBetween('created_at', [$from, $to]);
            }
            // Eğer $hasSearchOrOtherFilters true ise ancak tarih filtresi seçilmemişse,
            // herhangi bir tarih kısıtlaması uygulanmaz, bu da tüm kayıtlarda arama yapılmasını sağlar.

            // Mevcut filtreler
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

            // Sıralama işlemi
            if ($request->has('order')) {
                $order = $request->get('order')[0];
                $columns = $request->get('columns');
                $orderColumn = $columns[$order['column']]['data'];
                $orderDir = $order['dir'];
                $data->orderBy($orderColumn, $orderDir);
            } else {
                $data->orderBy('id','desc');
            }
          
            // DataTables filter callback, genel arama çubuğu için
            return DataTables::of($data) // Artık $filteredData değil, doğrudan $data kullanıyoruz
                ->addIndexColumn()
                ->addColumn('id', function($row){  
                    return '<a class="t-link editCustomer address idWrap" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCustomerModal">'.$row->id.'</a>'; 
                })
                ->addColumn('name', function($row){
                    return '<a class="t-link editCustomer address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCustomerModal"><div class="mobileTitle">Ad Soyad:</div>'.$row->adSoyad.'</a>';     
                })
                ->addColumn('tel', function($row){     
                    $telefon = $row->tel1;

                    // Eğer telefon numarası başında 0 yoksa ekle
                    if (substr($telefon, 0, 1) !== '0') {
                        $telefon = '0' . $telefon;
                    }
                    return '<a class="t-link editCustomer" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCustomerModal"><div class="mobileTitle">Telefon:</div>'.$telefon.'</div></a>';
                })
                ->addColumn('address', function($row){  
                    $address = (!empty($row->country->name) && !empty($row->state->ilceName)) 
                    ? $row->adres . '  ' .$row->country->name . ' / ' . $row->state->ilceName 
                    : '';
              
                    return '<a class="t-link editCustomer address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editCustomerModal"><div class="mobileTitle">Adres:</div>'.$address.'</div></a>';
                })
                ->addColumn('action', function($row){
                    $deleteUrl = route('delete.customer', [$row->firma_id,$row->id]);
                    $editButton = '';
                    $viewButton = '';
                    $deleteButton = '';
                    $editButton = '<a href="javascript:void(0);" data-bs-id="'.$row->id.'" class="btn btn-outline-warning btn-sm editCustomer mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editCustomerModal" title="Düzenle"><i class="fas fa-edit"></i></a>';
                    $viewButton = '<a href="javascript:void(0);" data-bs-id="'.$row->id.'" class="btn btn-outline-primary btn-sm editCustomer mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editCustomerModal" title="Düzenle"><i class="fas fa-eye"></i></a>';
                   
                    $deleteButton = '<a href="'.$deleteUrl.'" class="btn btn-outline-danger btn-sm mobilBtn" id="delete" title="Sil"><i class="fas fa-trash-alt"></i></a>';
                    
                    return $viewButton . ' ' . $editButton. ' ' .$deleteButton;
                })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('search'))) {
                        $instance->where(function($w) use($request){
                           $search = $request->get('search');
                           $w->where('adSoyad', 'LIKE', "%$search%")                        
                            ->orWhere('tel1', 'LIKE', "%$search%"); // Telefon numarasında da arama yap
                        });
                    }
                })
                ->rawColumns(['id','name','tel','address','action'])
                ->make(true);                      
            }
        return view('frontend.secure.customers.all_customers',compact('firma','countries'));
    }

 // Helper Methods
    private function isDefaultCustomerDateRange(Request $request): bool
    {
        if (!$request->filled('from_date_customer') || !$request->filled('to_date_customer')) {
            return false;
        }
        
        try {
            $from = Carbon::createFromFormat('Y-m-d', $request->from_date_customer)->startOfDay();
            $to = Carbon::createFromFormat('Y-m-d', $request->to_date_customer)->endOfDay();
            $defaultFrom = Carbon::today()->subDays(2)->startOfDay();
            $defaultTo = Carbon::today()->endOfDay();
            
            return $from->equalTo($defaultFrom) && $to->equalTo($defaultTo);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function applyMainCustomerDateRange($query, Request $request): void
    {
        if ($request->filled('from_date_customer') && $request->filled('to_date_customer')) {
            $from = Carbon::createFromFormat('Y-m-d', $request->from_date_customer)->startOfDay();
            $to   = Carbon::createFromFormat('Y-m-d', $request->to_date_customer)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }
    }

    public function AddCustomer($tenant_id) {
        $countries = Il::orderBy('name', 'ASC')->get();
        $firma = Tenant::where('id', $tenant_id)->first();
        return view('frontend.secure.customers.add_customer', compact('countries','firma'));
    }

    public function StoreCustomer($tenant_id, Request $request) {

        $token = $request->input('form_token');
        // Token boş mu kontrol et
        if (empty($token)) {
            $notification = array(
                'message' => 'Geçersiz form token! Lütfen sayfayı yenileyin.',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }
        
        // Bu token daha önce kullanıldı mı kontrol et
        $cacheKey = 'customer_form_token_' . $token;
        
        if (Cache::has($cacheKey)) {
            $notification = array(
                'message' => 'Bu form zaten gönderildi! Lütfen bekleyin veya sayfayı yenileyin.',
                'alert-type' => 'warning'
            );
            return redirect()->back()->with($notification);
        }
        
        // Token'ı 10 dakika boyunca sakla
        Cache::put($cacheKey, true, now()->addMinutes(10));


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
