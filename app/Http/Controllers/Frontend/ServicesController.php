<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Customer;
use App\Models\DeviceBrand;
use App\Models\DeviceType;
use App\Models\Il;
use App\Models\Ilce;
use App\Models\Service;
use App\Models\ServiceResource;
use App\Models\ServiceStage;
use App\Models\StageQuestion;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WarrantyPeriod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ServicesController extends Controller
{
    public function AllServices($tenant_id, Request $request) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if(!$firma) {
            $notification = array(
                'message' => 'Firma bulunamadı',
                'alert-type' => 'danger'
            );
            return redirect()->route('giris')->with($notification);
        }
        $services = Service::where('firma_id', $firma->id)->get();
        $device_brands = DeviceBrand::where('firma_id', $firma->id)->orderBy('marka', 'asc')->get();
        $device_types = DeviceType::where('firma_id', $firma->id)->orderBy('cihaz', 'asc')->get();
        $service_stages = ServiceStage::where(function ($query) use ($tenant_id) {
            $query->whereNull('firma_id')->orWhere('firma_id', $tenant_id);
        })->orderBy('asama', 'asc')->get();
    
        if ($request->ajax()) {           
            $data = Service::where('firma_id', $firma->id)->where('durum', 1);
    
            // if ($request->get('resources')) {
            //     $data->where('kaynak', $request->get('resources'));
            // }
    
            // if ($request->get('categories')) {
            //     $data->where('kategori', $request->get('categories'));
            // }
    
            // if ($request->get('stages')) {
            //     $data->where('asama_id', $request->get('stages'));
            // }
    
            // if ($request->filled('aksiyon')) {
            //     if ($request->get('aksiyon') == 1) {
            //         $data->where('aksiyon', 1);
            //     } elseif ($request->get('aksiyon') == 0) {
            //         $data->where('aksiyon', 0);
            //     }
            // }
    
            // Sıralama işlemi
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
                ->addColumn('id', function($row){
                    return '<a class="t-link serBilgiDuzenle address idWrap" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editServiceDescModal">'.$row->id.'</a>';
                })
                ->addColumn('created_at', function($row){
                    $sontarih = Carbon::parse($row->created_at)->format('d/m/Y');
                    return '<a class="t-link serBilgiDuzenle address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editServiceDescModal"><span class="mobileTitle">Tarih:</span>'.$sontarih.'</a>';
                })
                ->addColumn('m_adi', function($row){ 
                    return '<a class="t-link serBilgiDuzenle address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editServiceDescModal"><span class="mobileTitle">Müşteri:</span><strong>'.$row->musteri->adSoyad.'</strong><br><div style="font-size:12px;">'.$row->musteri->tel1.' - '.$row->musteri->tel2.'</div><div style="font-size:12px;">'.$row->musteri->adres.'</div></a>';
                    
                })
                ->addColumn('cihaz', function($row){
                    return '<a class="t-link serBilgiDuzenle" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editServiceDescModal"><span class="mobileTitle">Cihaz:</span><strong>'.$row->markaCihaz->marka.' - '.$row->turCihaz->cihaz.'</strong></a>';
                })
                ->addColumn('asama_id', function($row){                   
                    return '<a class="t-link serBilgiDuzenle address" href="javascript:void(0);" data-bs-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#editServiceDescModal"><span class="mobileTitle">S. Durumu:</span><strong>'.$row->asamalar?->asama.'</strong><br><div style="font-size:12px;">('.$row->cihazAriza.')</div></a>';    
                })
                ->addColumn('action', function($row){
                    $deleteUrl = route('delete.customer', [$row->firma_id,$row->id]);
                    $editButton = '';
                    $deleteButton = '';
                        $editButton = '<a href="javascript:void(0);" data-bs-id="'.$row->id.'" class="btn btn-warning btn-sm serBilgiDuzenle mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editServiceDescModal" title="Düzenle" ><i class="fas fa-edit"></i><span> Düzenle</span></a>';
                        $deleteButton = '<a href="'.$deleteUrl.'" class="btn btn-danger btn-sm mobilBtn" id="delete" title="Sil" ><i class="fas fa-trash-alt"></i> <span> Sil</span></a>';
                    return $editButton. '  '.$deleteButton;
                })
                ->rawColumns(['id', 'created_at', 'm_adi', 'cihaz', 'asama_id', 'action'])
                ->make(true);                      
        }
    
        return view('frontend.secure.all_services.services', compact('services', 'device_brands', 'device_types', 'service_stages','firma'));
    }

    public function searchCustomer(Request $request, $firma_id)
    {
        try {
            $searchTerm = $request->input('musteriGetir');
            
            // Minimum karakter kontrolü
            if (strlen($searchTerm) < 2) {
                return response()->json([]);
            }
            
            // Müşteri arama - firma_id'ye göre filtreleme
            $customers = Customer::where('firma_id', $firma_id)
                ->where(function($query) use ($searchTerm) {
                    $query->where('adSoyad', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('tel1', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhereRaw('REPLACE(tel1, "-", "") LIKE ?', ['%' . str_replace(['-', '(', ')', ' '], '', $searchTerm) . '%'])
                          ->orWhere('tcNo', 'LIKE', '%' . $searchTerm . '%')
                          ->orWhere('vergiNo', 'LIKE', '%' . $searchTerm . '%');
                })
                ->select([
                    'id',
                    'adSoyad', 
                    'tel1', 
                    'tel2', 
                    'adres', 
                    'il', 
                    'ilce', 
                    'musteriTipi',
                    'tcNo',
                    'vergiNo',
                    'vergiDairesi'
                ])
                ->orderBy('adSoyad')
                ->limit(10) // Maksimum 10 sonuç
                ->get();
            
            // İl ve ilçe isimlerini çek (eğer ID olarak saklanıyorsa)
            $results = $customers->map(function($customer) {
                // Eğer il ve ilçe ID olarak saklanıyorsa, isimlerini çek
                $il = DB::table('ils')->where('id', $customer->il)->value('name') ?? $customer->il;
                $ilce = DB::table('ilces')->where('id', $customer->ilce)->value('ilceName') ?? $customer->ilce;
                
                return [
                    'id' => $customer->id,
                    'adSoyad' => $customer->adSoyad,
                    'tel1' => $customer->tel1,
                    'tel2' => $customer->tel2,
                    'adres' => $customer->adres,
                    'il' => $il,
                    'ilce' => $ilce,
                    'musteriTipi' => $customer->musteriTipi,
                    'tcNo' => $customer->tcNo,
                    'vergiNo' => $customer->vergiNo,
                    'vergiDairesi' => $customer->vergiDairesi
                ];
            });
            
            return response()->json($results);
            
        } catch (\Exception $e) {
            \Log::error('Customer search error: ' . $e->getMessage());
            return response()->json(['error' => 'Arama sırasında hata oluştu'], 500);
        }
    }

    public function AddService($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $service_resources = ServiceResource::where('firma_id', $firma->id)->orderBy('kaynak', 'asc')->get();
        $iller = DB::table('ils')->orderBy('name', 'ASC')->get();
        $device_brands = DeviceBrand::where('firma_id', $firma->id)->orderBy('marka', 'asc')->get();
        $device_types = DeviceType::where('firma_id', $firma->id)->orderBy('cihaz', 'asc')->get();
        $warranty_periods = WarrantyPeriod::where('firma_id', $firma->id)->orderBy('garanti', 'asc')->get();

        return view('frontend.secure.all_services.add_service', compact('firma','service_resources','iller','device_brands','device_types','warranty_periods'));
    }

    public function StoreService($tenant_id, Request $request) {        
        $firma = Tenant::where('id', $tenant_id)->first();
        
        if (!$firma) {
            return redirect()->route('giris');
        }
        
        $raw1 = preg_replace('/\D/', '', $request->tel1); // Sadece rakamlar
        $tel1 = preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1 $2 $3', $raw1);

        $raw2 = preg_replace('/\D/', '', $request->tel2); // Sadece rakamlar
        $tel2 = preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1 $2 $3', $raw2);

        $musteriData = [
            'firma_id' => $firma->id,
            'personel_id' => auth()->id(),
            'musteriTipi' => $request->musteriTipi,
            'adSoyad' => $request->adSoyad,
            'tel1' => $tel1,
            'tel2' => $tel2,
            'il' => $request->il,
            'ilce' => $request->ilce,
            'adres' => $request->adres,
            'tcNo' => $request->tcNo,
            'vergiNo' => $request->vergiNo,
            'vergiDairesi' => $request->vergiDairesi,
            'created_at' => now(),
        ];
        $eskiMusteriId = $request->eskiMusteriId;

        if (empty($eskiMusteriId)) {
            // Yeni müşteri - önce aynı bilgilerle müşteri var mı kontrol et
            $musteriKontrol = Customer::where('firma_id', $firma->id)->where([
                'musteriTipi' => $request->musteriTipi,
                'adSoyad' => $request->adSoyad,
                'tel1' => $request->tel1,
                'tel2' => $request->tel2,
                'il' => $request->il,
                'ilce' => $request->ilce,
                'adres' => $request->adres,
                'tcNo' => $request->tcNo,
                'vergiNo' => $request->vergiNo,
                'vergiDairesi' => $request->vergiDairesi,
            ])->first();

            if ($musteriKontrol) {
                $musteriId = $musteriKontrol->id;
            } else {
                $musteri = Customer::create($musteriData);
                $musteriId = $musteri->id;
            }
        } else {
            // Eski müşteri seçilmiş - sadece güncelle, yeni müşteri oluşturma
            $mevcutMusteri = Customer::find($eskiMusteriId);
            if ($mevcutMusteri) {
                $mevcutMusteri->update($musteriData);
                $musteriId = $eskiMusteriId;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Seçilen müşteri bulunamadı.'
                ], 404);
            }
        }

        if ($musteriId) {

            // İlk servis durumunu al
            $servisDurum = ServiceStage::where('ilkServis', '1')->first();

            $servisData = [
                'firma_id' => $firma->id,
                'kid' => auth()->id(),
                'bid' => '0',
                'pid' => '0',
                'musteri_id' => $musteriId,
                'kayitTarihi' => now(),
                'servisKaynak' => $request->input('servisReso'),
                'musaitTarih' => $request->kayitTarihi,
                'musaitSaat1' => $request->input('musaitSaat1'),
                'musaitSaat2' => $request->input('musaitSaat2'),
                'cihazMarka' => $request->input('cihazMarka'),
                'cihazTur' => $request->input('cihazTur'),
                'cihazModel' => $request->input('cihazModel'),
                'cihazSeriNo' => $request->input('cihazSeriNo'),
                'cihazAriza' => $request->cihazAriza,
                'operatorNotu' => $request->input('opNot'),
                'garantiSuresi' => $request->input('cihazGaranti'),
                'servisDurum' => $servisDurum->id ?? null,
                'kayitAlan' => auth()->id(),
                'planDurum' => '0',
                'pbDurum' => 0,
                'durum' => 1,
                'acil' => 0,
            ];

            // Aynı servis kontrolü
            $servisKontrol = Service::orderBy('id', 'desc')->first();
            
            $ayniServis = false;
            if ($servisKontrol && 
                $servisKontrol->musteriid == $musteriId && 
                $servisKontrol->kayitTarihi->format('Y-m-d H:i:s') == now()->format('Y-m-d H:i:s') && 
                $servisKontrol->pid == auth()->id()) {
                $ayniServis = true;
            }

            if (!$ayniServis) {
                $servis = Service::create($servisData);
                $servisId = $servis->id;

                // Acil servis kontrolü
                // if ($request->input('acil') == "1") {
                //     $acilData = [
                //         'pid' => auth()->id(),
                //         'servisid' => $servisId,
                //     ];

                //     $acilServis = EmergencyService::create($acilData);
                    
                //     Service::where('id', $servisId)->update([
                //         'acil' => $acilServis->id
                //     ]);
                // }

                if ($servisId) {
                    
                    // SMS gönderimi için kod buraya eklenebilir
                    // ...

                    $notification = array(
                        'message' => 'Servis Başarıyla Eklendi',
                        'alert-type' => 'success'
                    );

                    return redirect()->back()->with($notification);
                } else {
                    $notification = array(
                        'message' => 'Servis Kayıt Edilemedi',
                        'alert-type' => 'warning'
                    );
                }
            } else {
                $notification = array(
                'message' => 'Aynı Servis Zaten Mevcut',
                'alert-type' => 'warning'
            );
            }
        } else {
            $notification = array(
                'message' => 'Servis Kayıt Edilemedi',
                'alert-type' => 'warning'
            );
        }
    }

    public function EditService($tenant_id, $id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $service_id = Service::where('firma_id', $firma->id)->findOrFail($id);
        
        return view('frontend.secure.all_services.edit_service', compact('firma', 'service_id'));
    }

    public function TumServiceDesc($tenant_id, $id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $service_id = Service::where('firma_id', $firma->id)->findOrFail($id);
        $service_resources = ServiceResource::where('firma_id', $firma->id)->orderBy('kaynak', 'asc')->get();
        $iller = DB::table('ils')->orderBy('name', 'ASC')->get();
        $device_brands = DeviceBrand::where('firma_id', $firma->id)->orderBy('marka', 'asc')->get();
        $device_types = DeviceType::where('firma_id', $firma->id)->orderBy('cihaz', 'asc')->get();
        $warranty_periods = WarrantyPeriod::where('firma_id', $firma->id)->orderBy('garanti', 'asc')->get();
        
        $altAsamaIDs = [];
        $altAsamalar = collect(); // boş koleksiyon

        if (!empty($service_id->asamalar->altAsamalar)) {
            // Virgül ile ayrılmış ID listesini array'e dönüştür
            $altAsamaIDs = explode(',', $service_id->asamalar->altAsamalar);
            $altAsamalar = ServiceStage::whereIn('id', $altAsamaIDs)->orderBy('asama')->get();
        }
        return view('frontend.secure.all_services.service_information', compact('firma', 'service_id', 'service_resources', 'iller', 'device_brands', 'device_types', 'warranty_periods','altAsamalar'));
    }

    //Servis Bilgileri düzenleme modalında yapılacak işlemler selectini seçince çıkan formun olduğu sayfayı gösteren fonksiyon
    public function ServiceStageQuestionShow($tenant_id ,$asamaid, $serviceid) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $stage_id = ServiceStage::findOrFail($asamaid);
        $service_id = Service::where('firma_id', $firma->id)->findOrFail($serviceid);
        $stage_questions = StageQuestion::where('asama', $asamaid)->orderBy('sira', 'asc')->get();

         // İşlem türünü belirle (request'ten gelen islem parametresi)
        $islem = $stage_id;
        
        // Servis bilgilerini kontrol et
        $servisSec = Service::where('id', $serviceid)->first();
        
        // Eğer bid != 0 ise (bayi servisi)
        if($servisSec->bid != 0) {
            return view('frontend.secure.all_services.service_stage_questions_bayi', 
                    compact('stage_questions', 'stage_id', 'service_id', 'firma', 'islem'));
        }
        
        // Normal servis işlemleri
        if($islem == "238") {
            // Parça talep işlemi
            return view('frontend.secure.all_services.service_stage_questions_parca', 
                    compact('stage_questions', 'stage_id', 'service_id', 'firma', 'islem'));
        } else {
            
                            
            // $stoklar = DB::table('personel_stoklar')
            //             ->where('firma_id', $firma->id)
            //             ->where('pid', auth()->user()->id)
            //             ->orderBy('id', 'asc')
            //             ->get();
                        
            // Personel listesi al (grup kontrolü için)
            $personeller = User::where('tenant_id', $firma->id)
                            ->where('status', '1')
                            ->orderBy('name', 'asc')
                            ->get();
                            
            // Araç listesi al
            $araclar = Car::where('firma_id', $firma->id)
                        ->where('durum', '1')
                        ->orderBy('id', 'asc')
                        ->get();
                        
            // Bayi listesi al
            // $bayiler = DB::table('personeller')
            //             ->where('grup', '258')
            //             ->where('firma_id', $firma->id)
            //             ->where('durum', '1')
            //             ->orderBy('adsoyad', 'asc')
            //             ->get();
            
            return view('frontend.secure.all_services.service_stage_questions_show', 
                    compact('stage_questions', 'stage_id', 'service_id', 'firma', 'islem', 'personeller', 'araclar'));
        }
    }

    public function EditServiceCustomer($tenant_id, $id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $customer = Customer::where('firma_id', $firma->id)->find($id);
        $countries = Il::orderBy('name','asc')->get();
        return view('frontend.secure.all_services.edit_service_customer', compact('firma', 'customer', 'countries'));
    }

    public function UpdateService($tenant_id, Request $request) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $resource_id = $request->id;
        Service::findOrFail($resource_id)->update([
            
        ]);
        $updatedResource = Service::find($resource_id);
        return response()->json($updatedResource);
    }

    public function DeleteService($tenant_id, $id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        if (!$firma) {
            return redirect()->back()->with([
                'message' => 'Firma bulunamadı!',
                'alert-type' => 'danger',
            ]);
        }
        $service_resources = Service::find($id);
        if($service_resources) {
            $service_resources->delete();
            return response()->json(['success' => true]);
        }
        else{
            return response()->json(['success' => false, 'message' => 'Stok kategorisi başarıyla silindi.']);
        }
    }
}
