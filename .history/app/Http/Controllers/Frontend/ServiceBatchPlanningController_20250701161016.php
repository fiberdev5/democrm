<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\DeviceType;
use App\Models\Il;
use App\Models\Ilce;
use App\Models\Service;
use App\Models\ServicePlanStatu;
use App\Models\ServiceResource;
use App\Models\ServiceStageAnswer;
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

    public function getServiceList(Request $request,$tenant_id)
    {
        // Gelen filtre verileri
        $kid = auth()->user()->user_id;
        $date = $request->input('planTarih');  // format: d-m-Y
        $il = $request->input('il');
        $districts = $request->input('bolgeler', []);
        $deviceTypes = $request->input('cihazlar', []);
        $sources = $request->input('kaynaklar', []);
        $statuses = $request->input('durumlar');

        // Sorgu başlangıcı
        $query = Service::query()->with(['musteri', 'markaCihaz', 'turCihaz'])
            ->where('firma_id', $tenant_id)
            ->where('durum', '1');

        // Duruma göre filtreleme
        if ($statuses == "235-2") {
            $statuses = "235";
        }

        if ($statuses) {
            $query->where('servisDurum', $statuses);
        }

        // İlçe filtreleme
        if (!empty($districts) && !in_array('0', $districts)) {
            $query->whereHas('musteri', function($q) use ($districts) {
                $q->whereIn('ilce', $districts);
            });
        } else {
            $query->whereHas('musteri', function($q) use ($il) {
                $q->where('il', $il);
            });
        }

        // Cihaz türü filtreleme
        if (!empty($deviceTypes) && !in_array('0', $deviceTypes)) {
            $query->whereIn('cihazTur', $deviceTypes);
        }

        // Kaynak filtreleme
        if (!empty($sources) && !in_array('0', $sources)) {
            $query->whereIn('servisKaynak', $sources);
        }

        // Tarih filtreleme (musaitTarih)
        if (in_array($statuses, ['235', '264'])) {
            if ($date) {
                $dateFormatted = Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
                $query->whereDate('musaitTarih', $dateFormatted);
            }
        }

        $services = $query->orderByDesc('id')->get();
        $firma = Tenant::where('id', $tenant_id)->first();

        //Personel e göre ilgili personelin servislerini göstermekte
        $personelQuery = User::where('tenant_id', $tenant_id)
            ->where('status', '1');


        $personeller = $personelQuery->orderBy('name')->get();

        // Bugünün tarihi (d/m/Y formatında)
        $bugun = Carbon::now()->format('Y-m-d');

        // Atanmış planid'leri çekiyoruz (soruid=296 olan ve cevap bugünün tarihi)
        $planIdler = ServiceStageAnswer::where('soruid', 296)
            ->where('cevap', $bugun)
            ->pluck('planid')
            ->toArray();

        // Her personelin bugün atandığı servis sayısı (cevap = personel id, planid in $planIdler)
        $personelAtamaSayilari = ServiceStageAnswer::whereIn('cevap', $personeller->pluck('user_id'))
            ->whereIn('planid', $planIdler)
            ->select('cevap', DB::raw('count(*) as toplam'))
            ->groupBy('cevap')
            ->pluck('toplam', 'cevap'); // ['personel_id' => sayı]
        return view('frontend.secure.all_services.service_batch_planning.list', compact('services','firma','personeller', 'personelAtamaSayilari'));
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
