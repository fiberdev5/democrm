<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CashTransaction;
use App\Models\Customer;
use App\Models\DeviceBrand;
use App\Models\DeviceType;
use App\Models\EmergencyService;
use App\Models\Il;
use App\Models\Ilce;
use App\Models\PaymentMethod;
use App\Models\PaymentType;
use App\Models\Service;
use App\Models\ServiceFormSetting;
use App\Models\ServiceMoneyAction;
use App\Models\ServiceOptNote;
use App\Models\ServicePhoto;
use App\Models\ServicePlanning;
use App\Models\ServiceResource;
use App\Models\ServiceStage;
use App\Models\ServiceStageAnswer;
use App\Models\StageQuestion;
use App\Models\Stock;
use App\Models\StockAction;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WarrantyPeriod;
use App\Models\ServiceReceiptNote;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
            
            return response()->json(['error' => 'Arama sırasında hata oluştu'], 500);
        }
    }

    public function AddService($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $service_resources = ServiceResource::where('firma_id', $firma->id)->orderBy('kaynak', 'asc')->get();
        $iller = DB::table('ils')->orderBy('name', 'ASC')->get();
        $device_brands = DeviceBrand::where('firma_id', $firma->id)->orderBy('marka', 'asc')->get();
        $device_types = DeviceType::where('firma_id', $firma->id)->orderBy('cihaz', 'asc')->get();
        $warranty_periods = WarrantyPeriod::orderBy('garanti', 'asc')->get();

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
        $warranty_periods = WarrantyPeriod::orderBy('garanti', 'asc')->get();
        
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
        
        
        // Normal servis işlemleri
       
            
                            
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

    //Servis Alt Aşamalarını veritabanına kaydederken yapılan işlemleri içeren fonksiyonlar
    public function SaveServicePlan(Request $request, $tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();

        try {
            
            $servisId = $request->input('servis');
            $gelenIslem = json_decode($request->input('gelenIslem'), true);
            $gidenIslem = $request->input('gidenIslem');

            // Servis durumu kontrolü
            $servisDurum = Service::where('firma_id',$firma->id)->where('id', $servisId)->first();
            if (!$servisDurum || $servisDurum->firma_id != $tenant_id) {
                return response()->json(['status' => 'error', 'message' => '-1']);
            }

            // Stok kontrolü
            $stokHatasiVar = $this->stokKontrolEt($request, $gelenIslem);
            if ($stokHatasiVar) {
                return response()->json(['status' => 'error', 'message' => $stokHatasiVar]);
            }

            
            $kid = Auth()->user()->user_id;
            // Servis planlama kaydı
            $planData = [
                'firma_id' => $tenant_id,
                'kid' => $kid,
                'pid' => $kid,
                'servisid' => $servisId,
                'gelenIslem' => $gelenIslem['id'],
                'gidenIslem' => $gidenIslem,
                'tarihDurum' => 0,
                'tarihKontrol' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ];

            $planId = ServicePlanning::insertGetId($planData);

            if ($planId) {
                // Log kaydı
                

                // Servis durumunu güncelle
                Service::where('id', $servisId)
                    ->update([
                        'servisDurum' => $gidenIslem,
                        'planDurum' => $planId,
                        'updated_at' => now()
                    ]);
                $servis = Service::find($servisId);

                // Soru cevaplarını işle
                $this->soruCevaplariniIsle($request, $servisId, $planId, $tenant_id, $gelenIslem);

                // Özel durumları işle
                $this->ozelDurumlariIsle($request, $servisId, $planId, $tenant_id, $gidenIslem, $servisDurum);

                // Tarih durumu kontrolü
                $this->tarihDurumuKontrolEt($tenant_id);

        
                $currentStage = $servis->servisDurum; // veya hangi field'dan alıyorsanız
        
                // Bu aşamaya ait alt aşamaları getir. Servis planı eklendikten sonra altAsamalar kısmını güncellemek için bunu yaptım.
                $altAsamaIDs = explode(',', $servis->asamalar->altAsamalar);
                $altAsamalar = ServiceStage::whereIn('id', $altAsamaIDs)->orderBy('asama')->get();

              
                $guncellenmisAsamaBilgisi = $servis->asamalar->asama;
                return response()->json([
                    'asama' => $guncellenmisAsamaBilgisi,
                    'altAsamalar' => $altAsamalar,
                ]);

            } else {
               
                
                return response()->json(['status' => 'error', 'message' => 'HATA! Servis aşama eklenemedi.']);
            }

        } catch (\Exception $e) {
            
            return response()->json(['status' => 'error', 'message' => 'Bir hata oluştu: ' . $e->getMessage()]);
        }
    }

     private function stokKontrolEt(Request $request, $gelenIslem)
    {
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'soru') !== false && $value == "Parca") {
                foreach ($request->all() as $stokKey => $stokValue) {
                    if (strpos($stokKey, 'stokCheck') !== false) {
                        $stokId = (int) filter_var($stokKey, FILTER_SANITIZE_NUMBER_INT);
                        $adet = abs($request->input("stokAdet{$stokId}", 0));

                        // Stok durumunu kontrol et
                        $stokHareketleri = StockAction::where('stokId', $stokId)
                            ->get();

                        if ($stokHareketleri->count() > 0) {
                            $toplam = 0;
                            foreach ($stokHareketleri as $hareket) {
                                if ($hareket->islem == "1") {
                                    $toplam += $hareket->adet;
                                } elseif ($hareket->islem == "2") {
                                    if ($hareket->plan_id == 0) {
                                        $toplam -= $hareket->adet;
                                    }
                                } elseif ($hareket->islem == "3") {
                                    $toplam -= $hareket->adet;
                                }
                            }

                            if ($toplam <= 0 || $adet > $toplam) {
                                $stok = Stock::where('id', $stokId)->first();
                                return "STOKHATA: " . mb_convert_case($stok->urun_adi, MB_CASE_TITLE, "UTF-8") . " Stok Adeti Yetersizdir.";
                            }
                        } else {
                            $stok = Stock::where('id', $stokId)->first();
                            return "STOKHATA: " . mb_convert_case($stok->urun_adi, MB_CASE_TITLE, "UTF-8") . " Stok Adeti Yetersizdir.";
                        }
                    }
                }

                // Parça teslim et işleminde stok seçimi zorunlu
                if ($gelenIslem == "238") {
                    $stokSecildi = false;
                    foreach ($request->all() as $key => $value) {
                        if (strpos($key, 'stokCheck') !== false) {
                            $stokSecildi = true;
                            break;
                        }
                    }

                    if (!$stokSecildi) {
                        return "STOKHATA: Parça Teslim Ederken Stok Seçmeni Zorunludur.";
                    }
                }
            }
        }

        return null;
    }

    private function soruCevaplariniIsle(Request $request, $servisId, $planId, $tenantId, $gelenIslem)
    {
        if ($request->has('soru')) {
            foreach ($request->input('soru') as $soruId => $cevap) {
                if ($cevap == "Parca") {
                    $this->parcaIslemleriniYap($request, $servisId, $planId, $tenantId, $soruId, $gelenIslem);
                } else {
                    $kid = Auth()->user()->user_id;
                    if (is_array($cevap)) {
                        // Çoklu cevap (checkbox)
                        foreach ($cevap as $cevapItem) {
                            ServiceStageAnswer::create([
                                'firma_id' => $tenantId,
                                'kid' => $kid,
                                'servisid' => $servisId,
                                'planid' => $planId,
                                'soruid' => $soruId,
                                'cevap' => $cevapItem,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    } else {
                        // Tekli cevap
                        ServiceStageAnswer::create([
                            'firma_id' => $tenantId,
                            'kid' => $kid,
                            'servisid' => $servisId,
                            'planid' => $planId,
                            'soruid' => $soruId,
                            'cevap' => $cevap,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
        }
    }

    private function parcaIslemleriniYap(Request $request, $servisId, $planId, $tenantId, $soruId, $gelenIslem)
    {
        $stokCevap = "";

        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'stokCheck') !== false) {
                $stokId = (int) filter_var($key, FILTER_SANITIZE_NUMBER_INT);
                $adet = abs($request->input("stokAdet{$stokId}", 1));

                if (empty($stokCevap)) {
                    $stokCevap = "{$stokId}---{$adet}";
                } else {
                    $stokCevap .= ", {$stokId}---{$adet}";
                }

                if ($gelenIslem == "238") {
                    $this->parcaTeslimEt($stokId, $adet, $servisId, $planId, $tenantId);
                } else {
                    $this->parcaKullan($stokId, $adet, $servisId, $planId, $tenantId);
                }
            }
        }
        $stokCevap = is_array($stokCevap) ? implode(', ', $stokCevap) : $stokCevap;
        if (!empty($stokCevap)) {
            ServiceStageAnswer::create([
                'firma_id' => $tenantId,
                'servisid' => $servisId,
                'planid' => $planId,
                'soruid' => $soruId,
                'cevap' => $stokCevap,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    private function parcaTeslimEt($stokId, $adet, $servisId, $planId, $tenantId)
    {
        // Önceki planı bul
        $sonPlan = ServicePlanning::where('servisid', $servisId)
            ->orderBy('id', 'desc')
            ->skip(1)
            ->first();

        // Personel stok ekle/güncelle
        $perStok = DB::table('personel_stoklar')
            ->where('user_id', $sonPlan->user_id)
            ->where('stok_id', $stokId)
            ->first();

        // if ($perStok) {
        //     DB::table('personel_stoklar')
        //         ->where('id', $perStok->id)
        //         ->update([
        //             'adet' => $perStok->adet + $adet,
        //             'updated_at' => now()
        //         ]);
        //     $perStokId = $perStok->id;
        // } else {
        //     $perStokId = DB::table('personel_stoklar')->insertGetId([
        //         'tenant_id' => $tenantId,
        //         'user_id' => $sonPlan->user_id,
        //         'stok_id' => $stokId,
        //         'adet' => $adet,
        //         'created_at' => now(),
        //         'updated_at' => now()
        //     ]);
        // }

        // Stok hareketi kaydet
        StockAction::create([
            'firma_id' => $tenantId,
            'stokId' => $stokId,
            'islem' => 3,
            'adet' => $adet,
            'servisid' => $servisId,
            'fiyat' => 0,
            'fiyat_birim' => 1,
            'planId' => $planId,
            //'personel_stok_id' => $perStokId,
            'personel' => $sonPlan->user_id,
            'kid' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        
    }

    private function parcaKullan($stokId, $adet, $servisId, $planId, $tenantId)
    {
        $stok = Stock::where('id', $stokId)->first();
        $fiyat = $adet * $stok->fiyat;

        // Stok hareketi kaydet
        $stokHareketId = StockAction::insertGetId([
            'firma_id' => $tenantId,
            'kid' => auth()->id(),
            'stokId' => $stokId,
            'islem' => 2,
            'servisid' => $servisId,
            'depo' => 1,
            'adet' => $adet,
            'fiyat' => $fiyat,
            'fiyat_birim' => 1,
            'planId' => $planId,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Personel stoğundan düş
        // $perStok = DB::table('personel_stoklar')
        //     ->where('user_id', auth()->id())
        //     ->where('stok_id', $stokId)
        //     ->first();

        // if ($perStok) {
        //     DB::table('personel_stoklar')
        //         ->where('id', $perStok->id)
        //         ->update([
        //             'adet' => $perStok->adet - $adet,
        //             'updated_at' => now()
        //         ]);
        // }

        // Servis durumu bilgilerini al
        $servisDurum = Service::where('id', $servisId)->first();

        // Kasa hareketi ekle
        $stokIslem = PaymentType::where('parca', '1')->first();

        // DB::table('kasa_hareketleri')->insert([
        //     'tenant_id' => $tenantId,
        //     'user_id' => auth()->id(),
        //     'personel_id' => auth()->id(),
        //     'islem_tarihi' => now(),
        //     'odeme_yonu' => 2,
        //     'odeme_sekli' => 178,
        //     'odeme_turu' => $stokIslem->id,
        //     'odeme_durum' => 1,
        //     'fiyat' => $fiyat,
        //     'fiyat_birim' => 1,
        //     'aciklama' => "Stok ID: {$stokId} ({$stok->urun_adi})",
        //     'marka' => $servisDurum->cihaz_marka,
        //     'cihaz' => $servisDurum->cihaz_tur,
        //     'servis_id' => $servisDurum->id,
        //     'stok_islem' => $stokHareketId,
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // Servis para hareketi ekle
        // DB::table('servis_para_hareketleri')->insert([
        //     'tenant_id' => $tenantId,
        //     'servis_id' => $servisId,
        //     'tarih' => now(),
        //     'odeme_sekli' => 178,
        //     'odeme_durum' => 1,
        //     'fiyat' => $fiyat,
        //     'aciklama' => "Stok ID: {$stokId} ({$stok->urun_adi})",
        //     'odeme_yonu' => 2,
        //     'stok_islem' => $stokHareketId,
        //     'user_id' => auth()->id(),
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);
    }

    private function ozelDurumlariIsle(Request $request, $servisId, $planId, $tenantId, $gidenIslem, $servisDurum)
    {
        // Parça Teslim Et (259) özel durumu
        if ($gidenIslem == "259") {
            $this->parcaTeslimEtOzelDurum($servisId, $planId, $tenantId);
        }

        // Diğer özel durumlar (254, 267, 268)
        if ($gidenIslem == "254") {
            $planlama = ServicePlanning::where('servisid', $servisId)
                ->orderBy('id', 'desc')
                ->skip(1)
                ->first();

            if ($planlama && $planlama->gidenIslem == "255") {
                ServicePlanning::where('id', $planlama->id)->delete();
            }
        }

        if ($gidenIslem == "267") {
            $this->musteriIadeEdildiIslem($request, $servisId, $planId, $tenantId, $servisDurum);
        }

        if ($gidenIslem == "268") {
            $this->fiyatYukseltildiIslem($request, $servisId, $planId, $tenantId, $servisDurum);
        }
    }

    private function parcaTeslimEtOzelDurum($servisId, $planId, $tenantId)
    {
        $planlama = ServicePlanning::where('servisid', $servisId)
            ->orderBy('id', 'desc')
            ->skip(1)
            ->first();

        // Yeni plan oluştur
        $yeniPlanId = ServicePlanning::insertGetId([
            'firma_id' => $tenantId,
            'servisid' => $servisId,
            'gelenIslem' => 259,
            'gidenIslem' => $planlama->gelen_islem,
            'kid' => auth()->id(),
            'pid' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Servis durumunu güncelle
        Service::where('id', $servisId)
            ->update([
                'servisDurum' => $planlama->gelen_islem,
                'planDurum' => $yeniPlanId,
                'updated_at' => now()
            ]);

        // Önceki cevapları kopyala
        $planlama2 = ServicePlanning::where('servisid', $servisId)
            ->orderBy('id', 'desc')
            ->skip(2)
            ->first();

        $cevaplar = ServiceStageAnswer::where('planid', $planlama2->id)
            ->orderBy('id', 'asc')
            ->get();

        foreach ($cevaplar as $cevap) {
            $soru = StageQuestion::where('id', $cevap->soru_id)->first();
            $cevapText = ($soru->cevap == "[Tarih]") ? now()->format('d/m/Y') : $cevap->cevap;
            
            ServiceStageAnswer::insert([
                'firma_id' => $tenantId,
                'servisid' => $servisId,
                'planid' => $yeniPlanId,
                'soruid' => $cevap->soru_id,
                'cevap' => $cevapText,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    private function musteriIadeEdildiIslem(Request $request, $servisId, $planId, $tenantId, $servisDurum)
    {
        $fiyat = $request->input('soru378');
        $aciklama = $request->input('soru376');

        // Servis para hareketi
        // $paraHareketId = DB::table('servis_para_hareketleri')->insertGetId([
        //     'tenant_id' => $tenantId,
        //     'user_id' => auth()->id(),
        //     'servis_id' => $servisId,
        //     'tarih' => now(),
        //     'odeme_yonu' => 2,
        //     'odeme_sekli' => 178,
        //     'odeme_durum' => 1,
        //     'fiyat' => $fiyat,
        //     'aciklama' => $aciklama,
        //     'plan_islem' => $planId,
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // Kasa hareketi
        // DB::table('kasa_hareketleri')->insert([
        //     'tenant_id' => $tenantId,
        //     'user_id' => auth()->id(),
        //     'islem_tarihi' => now(),
        //     'odeme_yonu' => 2,
        //     'odeme_sekli' => 178,
        //     'odeme_turu' => 214,
        //     'odeme_durum' => 1,
        //     'fiyat' => $fiyat,
        //     'fiyat_birim' => 1,
        //     'aciklama' => $aciklama,
        //     'servis_id' => $servisId,
        //     'marka' => $servisDurum->cihaz_marka,
        //     'cihaz' => $servisDurum->cihaz_tur,
        //     'servis_islem' => $paraHareketId,
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);
    }

    private function fiyatYukseltildiIslem(Request $request, $servisId, $planId, $tenantId, $servisDurum)
    {
        $fiyat = $request->input('soru380');
        $aciklama = $request->input('soru379');

        // Servis para hareketi
        // $paraHareketId = DB::table('servis_para_hareketleri')->insertGetId([
        //     'tenant_id' => $tenantId,
        //     'user_id' => auth()->id(),
        //     'servis_id' => $servisId,
        //     'tarih' => now(),
        //     'odeme_yonu' => 1,
        //     'odeme_sekli' => 178,
        //     'odeme_durum' => 2,
        //     'fiyat' => $fiyat,
        //     'aciklama' => $aciklama,
        //     'plan_islem' => $planId,
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // Kasa hareketi
        // DB::table('kasa_hareketleri')->insert([
        //     'tenant_id' => $tenantId,
        //     'user_id' => auth()->id(),
        //     'islem_tarihi' => now(),
        //     'odeme_yonu' => 1,
        //     'odeme_sekli' => 178,
        //     'odeme_turu' => 202,
        //     'odeme_durum' => 2,
        //     'fiyat' => $fiyat,
        //     'fiyat_birim' => 2,
        //     'aciklama' => $aciklama,
        //     'servis_id' => $servisId,
        //     'marka' => $servisDurum->cihaz_marka,
        //     'cihaz' => $servisDurum->cihaz_tur,
        //     'servis_islem' => $paraHareketId,
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);
    }

    private function tarihDurumuKontrolEt($tenant_id)
    {
        // Tarih durumu kontrolü - performans optimizasyonu
        $servisPlanlar = ServicePlanning::where('firma_id', $tenant_id)->where('tarihKontrol', '0')
            ->get();

        foreach ($servisPlanlar as $servisRow) {
            $tarihDurum = "0";
            $cevaplar = ServiceStageAnswer::where('firma_id', $tenant_id)->where('planid', $servisRow->id)
                ->get();

            foreach ($cevaplar as $cevapRow) {
                $soru = StageQuestion::where('id', $cevapRow->soruid)
                    ->first();

                if ($soru && $soru->cevapTuru == "[Tarih]") {
                    $tarihDurum = "1";
                    break;
                }
            }

            ServicePlanning::where('firma_id', $tenant_id)->where('id', $servisRow->id)
                ->update([
                    'tarihDurum' => $tarihDurum,
                    'tarihKontrol' => "1",
                    'updated_at' => now()
                ]);
        }

        // Cevap text güncelleme
        $cevaplar = ServiceStageAnswer::where('firma_id', $tenant_id)->where('cevapText', null)
            ->get();

        foreach ($cevaplar as $cevapRow) {
            $soru = StageQuestion::where('id', $cevapRow->soruid)
                ->first();

            if ($soru) {
                ServiceStageAnswer::where('firma_id', $tenant_id)->where('id', $cevapRow->id)
                    ->update([
                        'cevapText' => $soru->cevapTuru,
                        'updated_at' => now()
                    ]);
            }
        }
    }
    //Servis Alt Aşamalarının veritabanına kaydını yapan fonksiyonların SONU

    //Servis Alt Aşamalarını silme fonksiyonu
    public function DeleteServicePlan($tenant_id, $planid) {
        $servisPlanID = $planid;

        $plan = ServicePlanning::where('firma_id', $tenant_id)->findOrFail($servisPlanID);
        $servis = Service::where('firma_id', $tenant_id)->findOrFail($plan->servisid);
        $cevaplar = ServiceStageAnswer::where('planid', $servisPlanID)->get();

        $kullanici = auth()->user();

        try {
            // alt bayi işlemi silme (gidenIslem == 264)
            if ($plan->gidenIslem == 264) {
                // bayi ve ilgili tüm veriler silinir
                // aynı mantıkla çalıştırılır
            }

            // stok silme işlemi (gidenIslem == 259)
            if ($plan->gidenIslem == 259) {
                $stok_cevap = ServiceStageAnswer::where('firma_id', $tenant_id)->where('planid', $plan->id)->first();
                $stoklar = explode(', ', $stok_cevap->cevap);

                foreach ($stoklar as $stokCevap) {
                    [$stokID, $adet] = explode('---', $stokCevap);
                    $stok = StockAction::where('stokId', $stokID)->where('planId', $plan->id)->first();
                    // $perStok = PersonelStok::find($stok->perStokID);
                    // $perStok->update(['adet' => $perStok->adet - $adet]);
                    // $stok->delete();
                }
            }

            // ödeme silme işlemleri
            if (in_array($plan->gidenIslem, [267, 268])) {
                $servisPara = ServiceMoneyAction::where('planIslem', $servisPlanID)->first();
                if ($servisPara) {
                    // KasaHareket::where('servisIslem', $servisPara->id)->delete();
                    // $servisPara->delete();
                }
            }

            // stokları geri al
            $stokHareketleri = StockAction::where('planId', $servisPlanID)->get();
            foreach ($stokHareketleri as $stok) {
                // PersonelStok::where([
                //     'pid' => $plan->pid,
                //     'stokid' => $stok->stokid
                // ])->increment('adet', $stok->adet);

                //KasaHareket::where('stokIslem', $stok->id)->delete();
                ServiceMoneyAction::where('stokIslem', $stok->id)->delete();

                $stok->delete();
            }

            // cevapları sil
            ServiceStageAnswer::where('planid', $servisPlanID)->delete();

            $plan->delete();

            // son plan mıydı? servisDurum güncelle
            if ($servis->servisDurum == $plan->gidenIslem) {
                $sonPlan = ServicePlanning::where('servisid', $plan->servisid)->latest()->first();
                if ($sonPlan) {
                    $servis->update([
                        'servisDurum' => $sonPlan->gidenIslem,
                        'planDurum' => $sonPlan->id,
                    ]);
                } else {
                    $ilkAsama = ServiceStage::where('ilkServis', 1)->first();
                    $servis->update([
                        'servisDurum' => $ilkAsama->id,
                        'planDurum' => 0,
                    ]);
                }
            }

            // Bu aşamaya ait alt aşamaları getir. Servis planı eklendikten sonra altAsamalar kısmını güncellemek için bunu yaptım.
                $altAsamaIDs = explode(',', $servis->asamalar->altAsamalar);
                $altAsamalar = ServiceStage::whereIn('id', $altAsamaIDs)->orderBy('asama')->get();

              
                $guncellenmisAsamaBilgisi = $servis->asamalar->asama;
                return response()->json([
                    'asama' => $guncellenmisAsamaBilgisi,
                    'altAsamalar' => $altAsamalar,
                ]);

            $guncellenmisAsamaBilgisi = $servis->asamalar->asama;
            return response()->json([
                'asama' => $guncellenmisAsamaBilgisi // örn: $servis->asama->asama
            ]);

        } catch (\Exception $e) {
            return response("HATA! Servis Plan Silinemedi.", 500);
        }
    }

    //Servis planı düzenleme viewını açan fonksiyon
    public function EditServicePlan($tenant_id, $planid) {
        $firma = Tenant::where('id', $tenant_id)->first();
        
        if (!$firma) {
            return response()->json(['error' => 'Firma bulunamadı'], 404);
        }

        // Servis planı bilgilerini al
        $servisPlan = ServicePlanning::where('id', $planid)
            ->where('firma_id', $tenant_id)
            ->first();

        if (!$servisPlan) {
            return response()->json(['error' => 'Plan bulunamadı'], 404);
        }

        // Plan cevaplarını al
        $planCevaplar = ServiceStageAnswer::where('planid', $planid)
            ->orderBy('id', 'ASC')
            ->get();

        // Servis bilgilerini al
        $servis = Service::find($servisPlan->servisid);

        // Personelleri al
        $personellerAll = User::where('tenant_id', $tenant_id)
            ->where('status', '1')
            ->orderBy('name', 'ASC')
            ->get();

        // Stokları al (eğer işlem parça teslim değilse)
        $stoklar = collect();
        $personel_id = auth()->user()->user_id;
        if ($servisPlan->gidenIslem != "259") {
            // $stoklar = Stock::whereHas('personelStoklar', function($query) use ($tenant_id, $personel_id) {
            //     $query->where('kid', $tenant_id)
            //         ->where('pid', $personel_id);
            // })->orderBy('id', 'ASC')->get();
        }

        // Kullanıcı bilgilerini al
        $kullanici = auth()->user();

        return view('frontend.secure.all_services.edit_service_plan', compact(
            'servisPlan',
            'planCevaplar', 
            'servis',
            'personellerAll',
            'stoklar',
            'kullanici',
            'tenant_id'
        ));
    }
    //servis planı düzenleme viewını açma fonksiyonu SONU

    //Servis plan aşama düzenleme güncelleme fonksiyonu
    public function UpdateServicePlan(Request $request, $tenant_id)
    {
        $planid = $request->input('planid');

        try {
            // Servis planını güncelle
            $servisPlan = ServicePlanning::where('id', $planid)
                ->where('firma_id', $tenant_id)
                ->first();

            if (!$servisPlan) {
                return response()->json(['error' => 'Plan bulunamadı'], 404);
            }

            // İşlemi yapan personeli güncelle
            if ($request->has('planIslemiYapan')) {
                $servisPlan->pid = $request->input('planIslemiYapan');
                $servisPlan->save();
            }

            // Plan cevaplarını güncelle
            $planCevaplar = ServiceStageAnswer::where('firma_id', $tenant_id)->where('planid', $planid)->get();
            
            foreach ($planCevaplar as $cevap) {
                $soruKey = 'soru' . $cevap->id;
                
                if ($request->has($soruKey)) {
                    $yeniCevap = $request->input($soruKey);
                    
                    // Eğer parça seçimi varsa, checkbox'ları işle
                    if ($yeniCevap == 'Parca') {
                        $parcaCevap = $this->processParcaSelection($request, $tenant_id);
                        $cevap->cevap = $parcaCevap;
                    } else {
                        $cevap->cevap = $yeniCevap;
                    }
                    
                    $cevap->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Plan başarıyla güncellendi',
                'servis_id' => $servisPlan->servisid
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Güncelleme sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    //servis plan aşaması güncelleme fonksiyonu SONU

    //Servis Aşamalarının servis-information blade'inde görüntülenmesini sağlayan ajaxı çalıştıran fonksionlar
    public function getServiceStageHistory($tenant_id, $servisId)
    {
        $servis = Service::where('firma_id', $tenant_id)->findOrFail($servisId);
    
        $data = [
            'acilIslem' => null,
            'notlar' => [],
            'eskiIslemler' => [],
            'paraHareketleri' => []
        ];
        
        // Acil durum kontrolü - with kullan
        if ($servis->bid == 0 && $servis->acil != 0) {
            $acilIslem = EmergencyService::with('user:user_id,name')
                ->where('firma_id', $tenant_id)
                ->where('servisid', $servisId)
                ->first();
                
            if ($acilIslem) {
                $data['acilIslem'] = [
                    'tarih' => $acilIslem->created_at->format('d/m/Y'),
                    'personel' => $acilIslem->user->name ?? ''
                ];
            }
        }
        
        // Operatör notları - with kullan
        $notlar = ServiceOptNote::with('user:id,name')
            ->where('firma_id', $tenant_id)
            ->where('servisid', $servisId)
            ->orderBy('id', 'desc')
            ->get();
            
        foreach ($notlar as $not) {
            $data['notlar'][] = [
                'tarih' => $not->created_at->format('d/m/Y H:i'),
                'personel' => $not->user->name ?? '',
                'aciklama' => $not->aciklama
            ];
        }
        
        // Eski işlemler - nested with kullan
        $eskiIslemler = ServicePlanning::with([
            'user:user_id,name',
            'serviceStage:id,asama',
            'answers.question:id,soru,cevapTuru'
        ])->where('servisid', $servisId)
        ->orderBy('created_at', 'desc')
        ->get();
        
        $eklenenPara = [];
        
        foreach ($eskiIslemler as $eskiIslem) {
            $aciklamalar = [];
            foreach ($eskiIslem->answers as $cevap) {
                if (!empty($cevap->cevap)) {
                    $aciklamalar[] = $this->formatCevap($cevap->question, $cevap->cevap);
                }
            }
            
            $islemData = [
                'id' => $eskiIslem->id,
                'tarih' => $eskiIslem->created_at->format('d/m/Y H:i'),
                'personel' => $eskiIslem->user->name ?? '',
                'asama' => $eskiIslem->serviceStage->asama ?? '',
                'aciklamalar' => $aciklamalar,
                'pid' => $eskiIslem->pid,
            ];
            
            $data['eskiIslemler'][] = $islemData;
            
            // Para hareketleri için tarih
            $tarih = $eskiIslem->created_at->format('Y-m-d');
            $paraHareketleri = ServiceMoneyAction::with([
                'user:user_id,name',
                'paymentMethod:id,sekli'
            ])->where('firma_id', $tenant_id)
            ->where('servisid', $servisId)
            ->where('odemeYonu', 1)
            ->whereDate('created_at', $tarih)
            ->get();
                
            foreach ($paraHareketleri as $paraIslem) {
                if (!in_array($paraIslem->id, $eklenenPara)) {
                    $eklenenPara[] = $paraIslem->id;
                    $data['eskiIslemler'][] = $this->formatParaHareketi($paraIslem);
                }
            }
        }
        
        // Kalan para hareketleri
        $kalanParaHareketleri = ServiceMoneyAction::with([
            'personel:user_id,name',
            'odemeSekliRelation:id,odemeSekli'
        ])->where('firma_id', $tenant_id)
        ->where('servisid', $servisId)
        ->where('odemeYonu', 1)
        ->whereNotIn('id', $eklenenPara)
        ->orderBy('id', 'desc')
        ->get();
            
        foreach ($kalanParaHareketleri as $paraIslem) {
            $data['paraHareketleri'][] = $this->formatParaHareketi($paraIslem);
        }
        
        return response()->json($data);
    }
    
    private function formatCevap($soru, $cevap)
    {
        if (!$soru) return '';
        
        $result = '<strong>' . $soru->soru . '</strong>: ';
        
        if (strpos($soru->cevapTuru, 'Grup') !== false) {
            $personel = User::find($cevap);
            $result .= $personel->name ?? '';
        } elseif ($soru->cevapTuru == '[Arac]') {
            $arac = Car::find($cevap);
            $result .= $arac->arac ?? '';
        } elseif ($soru->cevapTuru == '[Parca]') {
            $parcalar = explode(', ', $cevap);
            $parcaMetinler = [];
            foreach ($parcalar as $parca) {
                $parcaData = explode('---', $parca);
                if (count($parcaData) >= 2) {
                    $parcaId = $parcaData[0];
                    $adet = $parcaData[1];
                    $stok = Stock::find($parcaId);
                    if ($stok) {
                        $parcaMetinler[] = $stok->urunAdi . ' (' . $adet . ')';
                    }
                }
            }
            $result .= implode(', ', $parcaMetinler);
        } elseif ($soru->cevapTuru == '[Bayi]') {
            $bayi = User::find($cevap);
            $result .= $bayi->name ?? '';
        } else {
            $result .= $cevap;
        }
        
        return $result;
    }
    
    private function formatParaHareketi($paraIslem)
    {
        $personel = User::find($paraIslem->pid);
        $odemeSekli = PaymentMethod::find($paraIslem->odemeSekli);
        
        $odemeDurum = '';
        if ($paraIslem->odemeDurum == 2) {
            $odemeDurum = '<span style="color:red">Beklemede</span>';
        } elseif ($paraIslem->odemeDurum == 1) {
            $odemeDurum = '<span style="color:green">Tamamlandı</span>';
        }
        
        $odemeYonu = '';
        if ($paraIslem->odemeYonu == 2) {
            $odemeYonu = '<i style="color: red;">Gider - ' . ($odemeSekli->odemeSekli ?? '') . '</i>';
        } elseif ($paraIslem->odemeYonu == 1) {
            $odemeYonu = '<i style="color: green;">Gelir - ' . ($odemeSekli->odemeSekli ?? '') . '</i>';
        }
        
        $fiyat = number_format($paraIslem->fiyat, 2, ',', '.') . ' TL';
        
        return [
            'type' => 'para',
            'tarih' => Carbon::parse($paraIslem->created_at)->format('d/m/Y H:i'),
            'personel' => $personel->name ?? '',
            'islem' => 'Para Hareketi: ' . $odemeDurum,
            'aciklama' => $fiyat . ' (' . $odemeYonu . ' ) <br>' . ucfirst($paraIslem->aciklama)
        ];
    }
    

    //Servis Aşamalarının servis-bilgileri blade'inde görüntülenmesini sağlayan fonk SONU

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

    //Servis yazdırma fonksiyonu
    public function ServicetoPdf($tenant_id, $id) {
        $servis = Service::findOrFail($id); 
        $kid = Auth()->user()->user_id;
        // Tarih ve saat bilgilerini ayır
        $data = $this->getServisDetay($tenant_id, $id);
        
        if (!$data) {
            return abort(404, 'Servis bulunamadı');
        }
        
        

        $pdf = Pdf::loadView('frontend.secure.all_services.service_to_pdf',$data)->setPaper('A4', 'portrait')
        ->setOption('isPhpEnabled', true)
        ->setOption('isHtml5ParserEnabled', true);
        return $pdf->stream();
    }

    private function getServisDetay($tenant_id, $servisId)
    {
        // Servis bilgisini al
        $servis = Service::where('firma_id', $tenant_id)->where('id', $servisId)->first();
        
        if (!$servis) {
            return null;
        }
        
        // Tarih ve saat bilgilerini ayır
        $tarihSaat = explode(' ', $servis->created_at);
        $tarih = explode('-', $tarihSaat[0]);
        $saat = explode(':', $tarihSaat[1]);
        
        // İlgili tabloların bilgilerini al
        $musteri = Customer::where('firma_id', $tenant_id)->where('id', $servis->musteri_id)->first();
        $cihazMarka = DeviceBrand::where('firma_id', $tenant_id)->where('id', $servis->cihazMarka)->first();
        $cihazTur = DeviceType::where('firma_id', $tenant_id)->where('id', $servis->cihazTur)->first();
        $servisDurum = ServiceStage::where(function ($query) use ($tenant_id) {
                $query->whereNull('firma_id')->orWhere('firma_id', $tenant_id);
            })->where('id', $servis->servisDurum)->first();
        
        // Son 5 işlemi al
        $eskiIslemler = ServicePlanning::where('firma_id', $tenant_id)->where('servisid', $servis->id)
                         ->orderBy('id', 'DESC')
                         ->limit(5)
                         ->get();
        
        // Para hareketlerini al
        $paraIslemler = ServiceMoneyAction::where('firma_id', $tenant_id)->where('servisid', $servis->id)
                         ->orderBy('id', 'DESC')
                         ->get();
        
        // Mesaj ayarlarını al
        $mesajObj = ServiceFormSetting::where('firma_id', $tenant_id)->first();
        $mesaj = $mesajObj ? $mesajObj->mesaj : '';
        
        // Garanti kontrolü
        $garantiBitis = null;
        if ($servis->garantiSuresi != "0") {
            $garanti = WarrantyPeriod::where('id', $servis->garantiSuresi)->first();
            
            if ($garanti) {
                $garantiBitisTarihi = Carbon::parse($tarihSaat[0])->addMonths($garanti->garanti);
                $garantiBitis = [
                    $garantiBitisTarihi->day,
                    $garantiBitisTarihi->month,
                    $garantiBitisTarihi->year
                ];
            }
        }
        
        // Servis planlama bilgilerini al
        $servisPlanlama = ServiceStageAnswer::where('firma_id', $tenant_id)
                           ->where('servisid', $servis->id)
                           ->orderBy('id', 'DESC')
                           ->get();
        
        // Bayi personel bilgilerini kontrol et
        $getUye = null;
        $logoPath = null;
        $webSitesi = " ";
        
        foreach ($servisPlanlama as $asama) {
            if ($asama && $asama->cevapText == '[Bayi]') {
                $bayiPersonelId = $asama->cevap;
                $getUye = User::where('tenant_id', $tenant_id)
                              ->where('status', '1')
                              ->whereHas('roles', function($query) {
                                  $query->whereIn('id', ['259']);
                              })->where('user_id', $bayiPersonelId)->first();
                
                if ($getUye) {
                    $logoPath = $getUye->image;
                    $webSitesi = " ";
                    $mesaj = str_replace("[TEL]", $getUye->tel, $mesaj);
                }
                break;
            }
        }
        
        if (!$getUye) {
            $getUye = Tenant::where('id', $tenant_id)->first();
            if ($getUye) {
                $logoPath = $getUye->logo;
                $webSitesi = $getUye->webSitesi ?? " ";
                $mesaj = str_replace("[TEL]", $getUye->tel1, $mesaj);
            }
        }
        
        // İşlem detaylarını hazırla
        $islemDetaylari = [];
        foreach ($eskiIslemler as $eskiIslem) {
            $tarihSaat = explode(" ", $eskiIslem->created_at);
            $tarihArray = explode("-", $tarihSaat[0]);
            $saatArray = explode(":", $tarihSaat[1]);
            
            $asama = ServiceStage::where('id', $eskiIslem->gidenIslem)->first();
            $aciklamalar = ServiceStageAnswer::where('firma_id', $tenant_id)
                            ->where('planid', $eskiIslem->id)
                            ->orderBy('id', 'ASC')
                            ->get();
            
            $aciklamaMetni = '';
            foreach ($aciklamalar as $aciklama) {
                if (!empty($aciklama->cevap)) {
                    $soru = StageQuestion::where('id', $aciklama->soruid)->first();
                    
                    if (strpos($soru->cevapTuru, "[Grup") !== false) {
                        $personel = User::where('tenant_id', $tenant_id)->where('user_id', $aciklama->cevap)->first();
                        $aciklamaMetni .= '<strong>' . $soru->soru . '</strong>: ' . ($personel->name ?? '') . "<br>";
                    } else if ($soru->cevapTuru == "[Arac]") {
                        $arac = Car::where('firma_id', $tenant_id)->where('id', $aciklama->cevap)->first();
                        $aciklamaMetni .= '<strong>' . $soru->soru . '</strong>: ' . ($arac->arac ?? '') . "<br>";
                    } else if ($soru->cevapTuru == "[Parca]") {
                        $aciklamaMetni .= '<strong>' . $soru->soru . '</strong>: ';
                        $parcalar = explode(", ", $aciklama->cevap);
                        foreach ($parcalar as $parca) {
                            $parcaArray = explode("---", $parca);
                            $parcaId = $parcaArray[0];
                            $adet = $parcaArray[1] ?? 1;
                            $stok = Stock::where('firma_id', $tenant_id)->where('id', $parcaId)->first();
                            $aciklamaMetni .= ($stok->urunAdi ?? '') . " (" . $adet . "), ";
                        }
                        $aciklamaMetni .= "<br>";
                    } else if ($soru->cevapTuru == "[Bayi]") {
                        $bayi = User::where('tenant_id', $tenant_id)
                              ->where('status', '1')
                              ->whereHas('roles', function($query) {
                                  $query->whereIn('id', ['259']);
                              })->where('id', $aciklama->cevap)->first();
                        $aciklamaMetni .= '<strong>' . $soru->soru . '</strong>: ' . ($bayi->name ?? '') . "<br>";
                    } else {               
                        $aciklamaMetni .= '<strong>' . $soru->soru . '</strong>: ' . $aciklama->cevap . "<br>"; 
                    }
                    
                }
            }
            
            $islemDetaylari[] = [
                'tarih' => $tarihArray[2] . "/" . $tarihArray[1] . "/" . $tarihArray[0] . ' - ' . $saatArray[0] . ":" . $saatArray[1],
                'asama' => $asama->asama ?? '',
                'aciklama' => $aciklamaMetni
            ];
        }
        
        // Para işlem detaylarını hazırla
        $paraDetaylari = [];
        foreach ($paraIslemler as $paraIslem) {
            $tarihSaat = explode(" ", $paraIslem->created_at);
            $tarihArray = explode("-", $tarihSaat[0]);
            
            $personel = User::where('tenant_id', $tenant_id)->where('user_id', $paraIslem->pid)->first();
            $odemeSekli = PaymentMethod::where('firma_id', $tenant_id)->where('id', $paraIslem->odemeSekli)->first();
            
            $odemeDurum = "";
            if ($paraIslem->odemeDurum == "2") {
                $odemeDurum = 'Beklemede';
            } else if ($paraIslem->odemeDurum == "1") {
                $odemeDurum = 'Tamamlandı';
            }
            
            $paraDetaylari[] = [
                'tarih' => $tarihArray[2] . "/" . $tarihArray[1] . "/" . $tarihArray[0],
                'personel' => $personel->name ?? '',
                'odemeSekli' => $odemeSekli->odemeSekli ?? '',
                'odemeDurum' => $odemeDurum,
                'fiyat' => number_format($paraIslem->fiyat, 2, ',', '.') . ' TL'
            ];
        }
        
        return [
            'servis' => $servis,
            'tarih' => $tarih,
            'saat' => $saat,
            'musteri' => $musteri,
            'cihazMarka' => $cihazMarka,
            'cihazTur' => $cihazTur,
            'servisDurum' => $servisDurum,
            'garantiBitis' => $garantiBitis,
            'getUye' => $getUye,
            'logoPath' => $logoPath,
            'webSitesi' => $webSitesi,
            'mesaj' => $mesaj,
            'islemDetaylari' => $islemDetaylari,
            'paraDetaylari' => $paraDetaylari
        ];
    }
    //Servis yazdırma fonksiyonu SONU 

    //Servisler modalında servis para hareketleri 
    public function ServiceMoneyActions($tenant_id, $service_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $servis = Service::where('firma_id', $tenant_id)->where('id', $service_id)->first();
        // Servis para hareketlerini personel bilgileri ile beraber al
        $servisParaHareketleri = ServiceMoneyAction::where('firma_id', $firma->id)
            ->where('servisid', $servis->id)
            ->with(['personel:user_id,name', 'odemeSekliRelation:id,odemeSekli'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Toplam hesaplama
        $toplamSonuc = 0;
        foreach ($servisParaHareketleri as $hareket) {
            if ($hareket->odemeYonu == 2) { // Gider
                $toplamSonuc -= $hareket->fiyat;
            } elseif ($hareket->odemeYonu == 1) { // Gelir
                $toplamSonuc += $hareket->fiyat;
            }
        }

        return view('frontend.secure.all_services.service_money_actions.service_money_actions', compact('firma', 'servis','servisParaHareketleri', 'toplamSonuc'));
    }

    public function AddServiceIncome($tenant_id, $service_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $servis = Service::where('firma_id', $tenant_id)->where('id', $service_id)->first();
        $personeller = User::where('tenant_id', $tenant_id)->where('status', '1')->get();
        $odemeSekilleri = PaymentMethod::get();
        return view('frontend.secure.all_services.service_money_actions.add_service_income', compact('firma', 'servis', 'personeller', 'odemeSekilleri'));
    }

    public function StoreServiceIncome($tenant_id, Request $request) {
        $rules = [
            'servisid' => 'required|numeric',
            'odemeSekli' => 'required|numeric',
            'odemeDurum' => 'required|in:1,2',
            'fiyat' => 'required|numeric|min:0',
            'aciklama' => 'nullable|string|max:255',
        ];

        // Patron ise ek validasyon kuralları
        if (auth()->user()->hasRole('Patron')) {
            $rules['tarih'] = 'required|date';
            $rules['personeller'] = 'required|numeric|exists:tb_user,user_id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validasyon hatası', 
                'messages' => $validator->errors()
            ], 422);
        }

         // Temel verileri al
        $servisid = $request->input('servisid');
        $fiyat = str_replace(",", ".", trim($request->input('fiyat')));
        
        // Tarih değişkenini doğru yerde tanımla
        $tarih = Carbon::now(); // Varsayılan olarak şu anki tarih
        
        // Eğer kullanıcı Patron ise ve tarih gönderilmişse, o tarihi kullan
        if (auth()->user()->hasRole('Patron') && $request->input('tarih')) {
           $tarih = Carbon::parse($request->input('tarih') . ' ' . now()->format('H:i:s'));
        }

        // Ana tabloya eklenecek veri
        $data = [
            'firma_id' => $tenant_id,
            'kid'          => auth()->user()->user_id,
            'servisid'     => $servisid,
            'created_at'   => $tarih,
            'odemeSekli'   => $request->input('odemeSekli'),
            'odemeDurum'   => $request->input('odemeDurum'),
            'fiyat'        => $fiyat,
            'aciklama'     => $request->input('aciklama'),
            'odemeYonu'    => 1,
        ];

        // Personel ID'sini belirle
        if (auth()->user()->hasRole('Patron') && $request->input('personeller')) {
            $data['pid'] = $request->input('personeller');
        } else {
            $data['pid'] = auth()->user()->user_id;
        }

        // servis_para_hareketleri tablosuna veri ekle
        $sonuc = ServiceMoneyAction::where('firma_id', $tenant_id)->create($data);
    
        if ($sonuc) {
            // kasa_hareketleri için veri hazırlığı
            $kasaData = [
                'firma_id' => $tenant_id,
                'kid'          => auth()->user()->user_id,
                'created_at'   => $tarih, // Aynı tarih değişkenini kullan
                'odemeYonu'    => 1,
                'odemeSekli'   => $request->input('odemeSekli'),
                'odemeDurum'   => $request->input('odemeDurum'),
                'fiyat'        => $fiyat,
                'fiyatBirim'   => 1,
                'aciklama'     => $request->input('aciklama'),
                'marka'        => $request->input('markaid'),
                'cihaz'        => $request->input('cihazid'),
                'servis'       => $servisid,
                'servisIslem'  => $sonuc->id, // ID'yi al
            ];

            // Personel bilgilerini ekle
            if (auth()->user()->hasRole('Patron') && $request->input('personeller')) {
                $kasaData['pid'] = $request->input('personeller');
                $kasaData['personel'] = $request->input('personeller');
            } else {
                $kasaData['pid'] = auth()->user()->user_id;
                $kasaData['personel'] = auth()->user()->user_id;
            }

            // Ödeme türünü belirle
            $odemeTuru = PaymentType::where('servis', 1)->first();
            if ($odemeTuru) {
                $kasaData['odemeTuru'] = $odemeTuru->id;
            }

            // kasa_hareketleri tablosuna ekle
            $kasaID = CashTransaction::create($kasaData);

            return response()->json(['success' => 'Ödeme eklendi.']);
        } else {
            return response()->json(['error' => 'HATA! Ödeme eklenemedi.'], 500);
        }

    }

    public function AddServiceExpense($tenant_id, $service_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $servis = Service::where('firma_id', $tenant_id)->where('id', $service_id)->first();
        $personeller = User::where('tenant_id', $tenant_id)->where('status', '1')->get();
        $odemeSekilleri = PaymentMethod::get();
        return view('frontend.secure.all_services.service_money_actions.add_service_expense', compact('firma', 'servis','personeller','odemeSekilleri'));
    }

    public function StoreServiceExpense($tenant_id, Request $request) {
         $rules = [
            'servisid' => 'required|numeric',
            'odemeSekli' => 'required|numeric',
            'odemeDurum' => 'required|in:1,2',
            'fiyat' => 'required|numeric|min:0',
            'aciklama' => 'nullable|string|max:255',
        ];

        // Patron ise ek validasyon kuralları
        if (auth()->user()->hasRole('Patron')) {
            $rules['tarih'] = 'required|date';
            $rules['personeller'] = 'required|numeric|exists:tb_user,user_id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validasyon hatası', 
                'messages' => $validator->errors()
            ], 422);
        }

         // Temel verileri al
        $servisid = $request->input('servisid');
        $fiyat = str_replace(",", ".", trim($request->input('fiyat')));
        
        // Tarih değişkenini doğru yerde tanımla
        $tarih = Carbon::now(); // Varsayılan olarak şu anki tarih
        
        // Eğer kullanıcı Patron ise ve tarih gönderilmişse, o tarihi kullan
        if (auth()->user()->hasRole('Patron') && $request->input('tarih')) {
            $tarih = Carbon::parse($request->input('tarih') . ' ' . now()->format('H:i:s'));
        }

        // Ana tabloya eklenecek veri
        $data = [
            'firma_id' => $tenant_id,
            'kid'          => auth()->user()->user_id,
            'servisid'     => $servisid,
            'created_at'   => $tarih,
            'odemeSekli'   => $request->input('odemeSekli'),
            'odemeDurum'   => $request->input('odemeDurum'),
            'fiyat'        => $fiyat,
            'aciklama'     => $request->input('aciklama'),
            'odemeYonu'    => 2,
        ];

        // Personel ID'sini belirle
        if (auth()->user()->hasRole('Patron') && $request->input('personeller')) {
            $data['pid'] = $request->input('personeller');
        } else {
            $data['pid'] = auth()->user()->user_id;
        }

        // servis_para_hareketleri tablosuna veri ekle
        $sonuc = ServiceMoneyAction::where('firma_id', $tenant_id)->create($data);
    
        if ($sonuc) {
            // kasa_hareketleri için veri hazırlığı
            $kasaData = [
                'firma_id' => $tenant_id,
                'kid'          => auth()->user()->user_id,
                'created_at'   => $tarih,
                'odemeYonu'    => 2,
                'odemeSekli'   => $request->input('odemeSekli'),
                'odemeDurum'   => $request->input('odemeDurum'),
                'fiyat'        => $fiyat,
                'fiyatBirim'   => 1,
                'aciklama'     => $request->input('aciklama'),
                'marka'        => $request->input('markaid'),
                'cihaz'        => $request->input('cihazid'),
                'servis'       => $servisid,
                'servisIslem'  => $sonuc->id, // ID'yi al
            ];

            // Personel bilgilerini ekle
            if (auth()->user()->hasRole('Patron') && $request->input('personeller')) {
                $kasaData['pid'] = $request->input('personeller');
                $kasaData['personel'] = $request->input('personeller');
            } else {
                $kasaData['pid'] = auth()->user()->user_id;
                $kasaData['personel'] = auth()->user()->user_id;
            }

            // Ödeme türünü belirle
            $odemeTuru = PaymentType::where('servis', 1)->first();
            if ($odemeTuru) {
                $kasaData['odemeTuru'] = $odemeTuru->id;
            }

            // kasa_hareketleri tablosuna ekle
            $kasaID = CashTransaction::create($kasaData);

            return response()->json(['success' => 'Ödeme eklendi.']);
        } else {
            return response()->json(['error' => 'HATA! Ödeme eklenemedi.'], 500);
        }
    }

    public function EditServiceMoneyAction($tenant_id, $payment_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $servisPara = ServiceMoneyAction::where('firma_id', $tenant_id)
            ->where('id', $payment_id)
            ->with(['personel', 'odemeSekliRelation'])
            ->first();
        
        if (!$servisPara) {
            abort(404, 'Ödeme kaydı bulunamadı.');
        }
        
        $personeller = User::where('tenant_id', $tenant_id)->where('status', '1')->get();
        $odemeSekli = PaymentMethod::get();
        
        return view('frontend.secure.all_services.service_money_actions.edit_service_money_action', 
            compact('firma', 'servisPara', 'personeller', 'odemeSekli'));
    }

    public function UpdateServiceMoneyAction(Request $request, $tenant_id)
    {
        try {
            // Validation
            $request->validate([
                'odemeSekli' => 'required|integer',
                'odemeDurum' => 'required|integer',
                'fiyat' => 'required|numeric|min:0',
                'odemeYonu' => 'required|integer|in:1,2',
                'aciklama' => 'nullable|string|max:255',
            ]);
            
            $user = Auth::user();
            $kid = $user->user_id;
            $id = $request->payment_id;
            
            // Mevcut kaydı getir
            $asamaSec = ServiceMoneyAction::where('firma_id', $tenant_id)
                ->where('id', $id)
                ->where('kid', $kid)
                ->first();
            
            if (!$asamaSec) {
                return response()->json(['error' => 'Kayıt bulunamadı'], 404);
            }
            
            // Tarih işlemi
            $tarih = null;
            if ($request->has('tarih') && !empty($request->tarih)) {
                $tarihArray = explode("/", $request->tarih);
                if (count($tarihArray) == 3) {
                    $tarih = $tarihArray[2] . "-" . $tarihArray[1] . "-" . $tarihArray[0] . " " . now()->format("H:i:s");
                }
            }
            
            // Fiyat formatı düzeltme
            $fiyat = str_replace(",", ".", $request->fiyat);
            
            // Güncelleme verilerini hazırla
            $updateData = [
                'kid' => $kid,
                'odemeSekli' => $request->odemeSekli,
                'odemeDurum' => $request->odemeDurum,
                'fiyat' => $fiyat,
                'aciklama' => $request->aciklama,
                'odemeYonu' => $request->odemeYonu,
                'updated_at' => now(),
            ];
            
            // Personel bilgisi (sadece yetkili kullanıcılar için)
            if (auth()->user()->hasRole('Patron')) {
                $updateData['pid'] = $request->personeller;
                if ($tarih) {
                    $updateData['created_at'] = $tarih;
                }
            }
            
            if (!$tarih) {
                $updateData['created_at'] = now();
            }
                    
            // Servis para hareketini güncelle
            $servisGuncellendi = ServiceMoneyAction::where('firma_id', $tenant_id)
                ->where('id', $id)
                ->update($updateData);
            
            if ($servisGuncellendi) {                
                // Kasa hareketini güncelle
                $kasaSec = CashTransaction::where('firma_id', $tenant_id)
                    ->where('servisIslem', $id)
                    ->first();
                
                if ($kasaSec) {
                    $kasaUpdateData = [
                        'kid' => $kid,
                        'odemeYonu' => $request->odemeYonu,
                        'odemeSekli' => $request->odemeSekli,
                        'odemeDurum' => $request->odemeDurum,
                        'fiyat' => $fiyat,
                        'fiyatBirim' => "1",
                        'aciklama' => $request->aciklama,
                        'servis' => $asamaSec->servisid,
                        'updated_at' => now(),
                    ];
                    
                    if (auth()->user()->hasRole('Patron')) {
                        $kasaUpdateData['pid'] = $request->personeller;
                        $kasaUpdateData['personel'] = $request->personeller;
                        if ($tarih) {
                            $kasaUpdateData['created_at'] = $tarih;
                        }
                    }
                    
                    if (!$tarih) {
                        $kasaUpdateData['created_at'] = now();
                    }
                    
                    // Ödeme türünü getir
                    $servisIslem = PaymentType::where('firma_id', $tenant_id)
                        ->where('servis', '1')
                        ->first();
                    
                    if ($servisIslem) {
                        $kasaUpdateData['odemeTuru'] = $servisIslem->id;
                    }
                    
                    CashTransaction::where('firma_id', $tenant_id)
                        ->where('id', $kasaSec->id)
                        ->update($kasaUpdateData);
                    
                }
                
                
                return response()->json([
                    'success' => true,
                    'message' => 'Ödeme güncellendi.'
                ]);
                
            } else {
                
                return response()->json([
                    'success' => false,
                    'message' => 'HATA! Ödeme güncellenemedi.'
                ]);
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasyon hatası',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function DeleteServiceMoneyAction($tenant_id, $payment_id) {
        $paymentId = $payment_id;
    
        try {
            $payment = ServiceMoneyAction::where('firma_id', $tenant_id)
                ->where('id', $paymentId)
                ->first();
            
            if (!$payment) {
                return response()->json(['success' => false, 'message' => 'Ödeme kaydı bulunamadı.'], 404);
            }
            
            // İlgili kasa hareketini de sil
            CashTransaction::where('servisIslem', $payment->id)->delete();
            
            $payment->delete();
            
            return response()->json(['success' => true, 'message' => 'Ödeme kaydı başarıyla silindi.']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Bir hata oluştu: ' . $e->getMessage()], 500);
        }
    }
    //Servisler modalında servis para hareketleri SONU

    //Servisler modalında servis fotoğrafları kısmı başlangıcı
    public function ServicePhotos($tenant_id, $service_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $servis = Service::where('firma_id', $tenant_id)->where('id', $service_id)->first();
        $photos = ServicePhoto::where('firma_id', $firma->id)->where('servisid', $servis->id)->orderBy('created_at', 'desc')->get();
        return view('frontend.secure.all_services.service_photos.all_service_photos', compact('firma', 'servis', 'photos'));
    }

    public function StoreServicePhoto($tenant_id, Request $request) {
        try {
            // Validasyon kuralları
            $validator = Validator::make($request->all(), [
                'belge' => 'required|file|mimes:jpg,jpeg,png|max:5120', // 5MB = 5120KB
            ], [
                'belge.required' => 'Lütfen bir dosya seçiniz.',
                'belge.mimes' => 'Sadece JPG, JPEG ve PNG dosyaları yükleyebilirsiniz.',
                'belge.max' => 'Dosya boyutu 5MB\'dan büyük olamaz.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Dosya işlemleri
            $file = $request->file('belge');
            $fileName = time().'.'.$file->getClientOriginalExtension();  
            $save_url = $file->move('upload/servis_fotolari', $fileName);
            
            // Veritabanına kaydet
            $photo = ServicePhoto::create([
                'firma_id' => $tenant_id,
                'kid' => auth()->user()->user_id ?? null,
                'servisid' => $request->servisid,
                'resimyol' => $save_url,
                'created_at' => Carbon::now(),
            ]);

            // Başarılı response
            return response()->json([
                'success' => true,
                'message' => 'Fotoğraf başarıyla yüklendi.',
                'photo' => [
                    'id' => $photo->id,
                    'url' => asset($photo->resimyol),
                    'created_at' => $photo->created_at->format('d/m/Y')
                ]
            ]);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Dosya yüklenirken bir hata oluştu. Lütfen tekrar deneyiniz.'
            ], 500);
        }
    }

    public function DeleteServicePhoto($tenant_id, $photo_id)
    {
        try {
            $photo = ServicePhoto::where('firma_id', $tenant_id)
                                ->where('id', $photo_id)
                                ->firstOrFail();

            // Dosyayı disk'ten sil
            // resimyol: upload/servis_fotolari/1750842597.png şeklinde kaydedildiği için
            $filePath = public_path($photo->resimyol);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            // Veritabanından sil
            $photo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Fotoğraf başarıyla silindi.'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fotoğraf bulunamadı.'
            ], 404);

        } catch (\Exception $e) {            
            return response()->json([
                'success' => false,
                'message' => 'Fotoğraf silinirken bir hata oluştu.'
            ], 500);
        }
    }
    //Servisler modalında servis fotoğrafları kısmı SONU

    //Servisler modalında fiş notu kısmı başlangıcı
    public function ServiceReceiptNotes($tenant_id, $service_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $servis  = Service::where('id', $service_id)->first();
        $servis_fis_notlari = ServiceReceiptNote::where('firma_id',$firma->id)->where('servisid', $servis->id)->get();
        return view('frontend.secure.all_services.service_receipt_notes.receipt_notes', compact('firma', 'servis','servis_fis_notlari'));
    }

    public function AddServiceReceiptNote($tenant_id, $service_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $servis  = Service::where('id', $service_id)->first();
        return view('frontend.secure.all_services.service_receipt_notes.add_receipt_note', compact('firma', 'servis'));
    }

    public function StoreReceiptNote($tenant_id, Request $request) {
        $kid = Auth::user()->user_id;
        $receiptNotes = ServiceReceiptNote::create([
            'firma_id' => $tenant_id,
            'kid' => $kid,
            'servisid' => $request->servisid,
            'aciklama' => $request->aciklama,
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
                'success' => true,
                'message' => 'Fotoğraf başarıyla yüklendi.',
                'note' => $receiptNotes,
            ]);
    }

    public function DeleteReceiptNote($tenant_id, $note_id) {

        try {
            $service_receipt_note = ServiceReceiptNote::where('firma_id', $tenant_id)
                                ->where('id', $note_id)
                                ->firstOrFail();

            
            // Veritabanından sil
            $service_receipt_note->delete();

            return response()->json([
                'success' => true,
                'message' => 'Servis fiş notu başarıyla silindi.'
            ]);

        } catch (\Exception $e) {            
            return response()->json([
                'success' => false,
                'message' => 'Fiş notu silinirken bir hata oluştu.'
            ], 500);
        }
    }
}
