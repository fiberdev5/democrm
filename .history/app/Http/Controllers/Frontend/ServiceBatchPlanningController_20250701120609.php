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
        $kid = auth()->user()->kid;
        $date = $request->input('planTarih');  // format: d-m-Y
        $il = $request->input('il', 'İSTANBUL');
        $districts = $request->input('bolgeler', []);
        $deviceTypes = $request->input('cihazlar', []);
        $sources = $request->input('kaynaklar', []);
        $statuses = $request->input('durumlar');

        // Sorgu başlangıcı
        $query = Service::query()->with(['customer', 'deviceBrand', 'deviceType'])
            ->where('durum', '1')
            ->where('kid', $kid);

        // Duruma göre filtreleme
        if ($statuses == "235-2") {
            $statuses = "235";
        }

        if ($statuses) {
            $query->where('servisDurum', $statuses);
        }

        // İlçe filtreleme
        if (!empty($districts) && !in_array('0', $districts)) {
            $query->whereIn('ilce', $districts);
        } else {
            $query->where('il', $il);
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
                $dateFormatted = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
                $query->whereDate('musaitTarih', $dateFormatted);
            }
        }

        $services = $query->orderByDesc('id')->get();

        return view('frontend.secure.all_services.service_batch_planning.batch_plannings', compact('services'));
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
        $serviceIds = $request->get('servisidler');
        $personnel = $request->get('personel');
        $status = $request->get('gidenDurum');
        
        // Assignment logic here
        $serviceArray = explode(', ', $serviceIds);
        
        foreach ($serviceArray as $serviceId) {
            // Update service status and assignment
            Service::where('id', $serviceId)->update([
                'servisDurum' => $status,
                'assigned_personnel' => $personnel,
                'updated_at' => now()
            ]);
            
            // Log the assignment
            DB::table('servis_planlama')->insert([
                'servisid' => $serviceId,
                'personel_id' => $personnel,
                'gidenIslem' => $status,
                'created_at' => now()
            ]);
        }
        
        return response()->json(['success' => true]);
    }
}
