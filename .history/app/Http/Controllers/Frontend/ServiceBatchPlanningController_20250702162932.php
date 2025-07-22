<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\DeviceType;
use App\Models\Il;
use App\Models\Ilce;
use App\Models\Service;
use App\Models\ServicePlanStatu;
use App\Models\ServiceResource;
use App\Models\ServiceStageAnswer;
use App\Models\StageQuestion;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceBatchPlanningController extends Controller
{
    public function ServiceBatchPlanning($tenant_id) {
        $firma = Tenant::where('id', $tenant_id)->first();
        $teknisyen = User::role(['Teknisyen'])->where('tenant_id', $tenant_id)->get();
        $kid = auth()->user()->user_id;
        
        // Get planning statuses
        $planningStatuses = ServicePlanStatu::where('kid', $kid)->first();
        
        // Get districts for Istanbul
        $districts = Ilce::where('sehir_id', '34')->orderBy('ilceName')->get();
        $iller = Il::orderBy('name', 'asc')->get();
        // Get device types
        $deviceTypes = DeviceType::where('firma_id', $tenant_id)->orderBy('cihaz')->get();
        
        // Get service sources
        $serviceSources = ServiceResource::where('firma_id', $tenant_id)->orderBy('id')->get();
        
        // Tomorrow's date as default
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        return view('frontend.secure.all_services.service_batch_planning.batch_plannings', compact('firma','teknisyen','planningStatuses',
            'districts', 
            'deviceTypes',
            'serviceSources',
            'tomorrow','iller'));
    }

    public function getServiceList(Request $request, $tenant_id)
    {
        // Gelen filtre verileri
        $kid = auth()->user()->user_id;
        $date = $request->input('planTarih');  // format: Y-m-d veya d-m-Y
        $il = $request->input('il');
        $districts = $request->input('bolgeler', []);
        $deviceTypes = $request->input('cihazlar', []);
        $sources = $request->input('kaynaklar', []);
        $statuses = $request->input('durumlar');
        $persID = $request->get('persID');

        // Sorgu başlangıcı
        $query = Service::query()->with(['musteri', 'markaCihaz', 'turCihaz'])
            ->where('firma_id', $tenant_id)
            ->where('durum', '1');

        if (!empty($persID)) {
            // Personnel specific logic - bugün için atanan servisleri getir
            $today = Carbon::today()->format('Y-m-d');
            
            $selectedDates = ServiceStageAnswer::where('cevap', $today)
                ->where('firma_id', $tenant_id)
                ->where('soruid', 48)
                ->pluck('planid');

            $serviceAnswers = ServiceStageAnswer::where('cevap', $persID)
                ->whereIn('planid', $selectedDates)
                ->where('soruid', 45) // Personel atama sorusu ID'si
                ->pluck('servisid');
            if ($serviceAnswers->isEmpty()) {
                        $query->whereRaw('1 = 0'); // Hiçbir sonuç döndürmemek için
                    } else {
                        $query->whereIn('id', $serviceAnswers);
                    }
            
        } else {
            // Normal filtreleme işlemleri
            
            // Duruma göre filtreleme
            if ($statuses == "235-2") {
                $statuses = "235";
            }

            if (!empty($statuses) && $statuses !== '0') {
                $query->where('servisDurum', $statuses);
            }

            // İl ve İlçe filtreleme - Düzeltildi
            if (!empty($districts) && !in_array('0', $districts)) {
                $query->whereHas('musteri', function($q) use ($districts) {
                    $q->whereIn('ilce', $districts);
                });
            } elseif (!empty($il) && $il !== '0') {
                // İlçe seçilmemişse ama il seçilmişse
                $query->whereHas('musteri', function($q) use ($il) {
                    $q->where('il', $il);
                });
            }

            // Cihaz türü filtreleme - Düzeltildi
            if (!empty($deviceTypes) && !in_array('0', $deviceTypes)) {
                $query->whereIn('cihazTur', $deviceTypes);
            }

            // Kaynak filtreleme - Düzeltildi
            if (!empty($sources) && !in_array('0', $sources)) {
                $query->whereIn('servisKaynak', $sources);
            }

            // Tarih filtreleme - Düzeltildi
            if (!empty($date)) {
                try {
                    // Gelen tarih formatını kontrol et ve dönüştür
                    if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $date)) {
                        // d-m-Y formatında geliyorsa
                        $dateFormatted = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
                    } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                        // Y-m-d formatında geliyorsa
                        $dateFormatted = $date;
                    } else {
                        throw new \Exception('Invalid date format');
                    }

                    // Belirli durumlar için musaitTarih, diğerleri için genel tarih filtreleme
                    if (in_array($statuses, ['235', '264'])) {
                        $query->whereDate('musaitTarih', $dateFormatted);
                    } else {
                        // Diğer durumlar için created_at veya başka bir tarih alanı kullanılabilir
                        $query->whereDate('created_at', $dateFormatted);
                    }
                } catch (\Exception $e) {
                    // Hatalı tarih formatı durumunda log kaydı
                }
            }
        }

        // Sonuçları getir
        $services = $query->orderByDesc('id')->get();

        /* Planlama ayarları (grup filtresi) */
        $defaultRoles = [
            'Teknisyen',
            'Teknisyen Yardımcısı',
            'Atölye Çırak',
            'Atölye Ustası',
        ];

        $personeller = User::query()
            ->role($defaultRoles)
            ->where('tenant_id', $tenant_id)
            ->where('status', 1)
            ->orderBy('name')
            ->get(['user_id', 'name']);

        /* Bugün atama yapılan planId'leri */
        $today = Carbon::now()->format('Y-m-d');
        $todayPlans = ServiceStageAnswer::where('soruid', 48)
            ->where('cevap', $today)
            ->where('firma_id', $tenant_id) // Firma filtresi eklendi
            ->pluck('planid');

        /* Kişi başına "bugün atanan servis" sayısı */
        $rawCounts = ServiceStageAnswer::select('cevap as personel_id', DB::raw('COUNT(*) as toplam'))
            ->whereIn('cevap', $personeller->pluck('user_id'))
            ->whereIn('planid', $todayPlans)
            ->where('soruid', 45) // Personel atama sorusu ID'si
            ->groupBy('cevap')
            ->pluck('toplam', 'personel_id')
            ->toArray();

        // Eksik olanlara 0 ver
        $personelAtamaSayilari = [];
        foreach ($personeller as $p) {
            $personelAtamaSayilari[$p->user_id] = $rawCounts[$p->user_id] ?? 0;
        }
        
        $firma = Tenant::where('id', $tenant_id)->first();
        
        return view('frontend.secure.all_services.service_batch_planning.list', 
            compact('services', 'firma', 'personeller', 'personelAtamaSayilari'));
    }

     public function getServicePlanForm(Request $request, $tenant_id)
    {
        $servisIds = $request->input('servisidler'); // 'servisidler' query string'den geliyor
        $gelenDurum = $request->input('gelenDurum');
        $gidenDurum = $request->input('gidenDurum');

        $idList = explode(', ', $servisIds);

        // Kiracı kontrolü (PHP'deki $servisDurum["kid"] != $kid kontrolü)
        // Burada her servisin tenant_id'sini kontrol edebilirsiniz
        foreach ($idList as $serviceId) {
            $service = Service::where('id', $serviceId)->where('firma_id', $tenant_id)->first();
            
        }

        // PHP kodundaki $gelenDurum == "235-2" kontrolü
        if ($gelenDurum == "235-2") {
            $gelenDurum = "235";
        }

        // Atanacak aşamaya ait soruları çek (PHP'deki $gelenAltAsamalar)
        $questions = StageQuestion::where('asama', $gidenDurum)
                                        ->orderBy('sira', 'ASC')
                                        ->get();

        // Formda kullanılacak diğer verileri hazırlayalım
        $personnel = User::role(['Teknisyen', 'Teknisyen Yardımcısı', 'Atölye Çırak', 'Atölye Ustası']) // Rol bazlı personel çekimi
                         ->where('tenant_id', $tenant_id)
                         ->where('status', 1)
                         ->orderBy('name')
                         ->get(['user_id', 'name']); // Grup bilgisi de gerekli olabilir

        $vehicles = Car::where('firma_id', $tenant_id)
                                  ->where('durum', 1)
                                  ->orderBy('id')
                                  ->get();

        // Bayi personellerini çek (PHP'deki grup=258)
        $dealers = User::role('Bayi') // Varsayılan olarak bayi rolü var ise
                        ->where('tenant_id', $tenant_id)
                        ->where('status', 1)
                        ->orderBy('name')
                        ->get(['user_id', 'name']);


        // Tarih alanı için varsayılan değer
        $defaultDate = Carbon::now();
        
        $bugun = date('w'); // 0: Pazar, 6: Cumartesi
         $date = ($bugun == 6)
                ? date('Y-m-d', strtotime('+2 days'))
                : date('Y-m-d', strtotime('+1 day'));
         $defaultDateFormatted = $date;     


        // Blade view'a verileri gönder
        return view('frontend.secure.all_services.service_batch_planning.assignment_form', compact(
            'questions',
            'personnel',
            'vehicles',
            'dealers',
            'servisIds', // Hidden input için servisidler'i de gönder
            'gelenDurum', // Hidden input için
            'gidenDurum', // Hidden input için
            'defaultDateFormatted', // Tarih inputu için
            'tenant_id' // URL'den gelen tenant_id'yi de view'a gönder
        ));
    }

    public function getDistricts(Request $request, $tenant_id)
    {
        $city = $request->city_id;
        
        $districts = Ilce::where('sehir_id', $city)
            ->orderBy('ilceName')
            ->get(['id', 'ilceName']);
        
        return response()->json($districts);
    }

    public function assignService(Request $request)
    {
        // Gelen servis id'leri ve personel ataması
        $serviceIds = $request->input('servisidler', []);
        $personnelId = $request->input('personel');
        $newStatus = $request->input('gidenDurum');

        // İşlem yap
        if (!empty($serviceIds) && $personnelId && $newStatus) {
            foreach ($serviceIds as $serviceId) {
                $service = Service::find($serviceId);
                if ($service) {
                    // Servis durumunu güncelle
                    $service->servisDurum = $newStatus;
                    $service->assigned_personnel_id = $personnelId;
                    $service->save();

                    // İstersen burada servis aşama cevabı tablosuna da kayıt ekleyebilirsin
                }
            }
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'Geçersiz veri']);
    }
}
