<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\DeviceType;
use App\Models\Il;
use App\Models\Ilce;
use App\Models\ServicePlanStatu;
use App\Models\ServiceResource;
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
            'tomorrow'));
    }

    public function getServiceList(Request $request)
    {
        $kid = auth()->user()->company_id;
        
        $planDate = $request->get('planTarih');
        $city = $request->get('il');
        $districts = $request->get('bolgeler');
        $devices = $request->get('cihazlar');
        $sources = $request->get('kaynaklar');
        $statuses = $request->get('durumlar');
        $persID = $request->get('persID');

        // Handle special status
        if ($statuses == "235-2") {
            $statuses = "235";
        }

        $query = Service::select([
            'servisler.id',
            'servisler.kayitTarihi',
            'servisler.musaitTarih',
            'servisler.servisDurum',
            'servisler.servisKaynak',
            'musteriler.adSoyad',
            'musteriler.ilce',
            'cihaz_markalari.marka',
            'cihaz_markalari.id as markaid',
            'cihaz_turleri.cihaz',
            'cihaz_turleri.id as cihazTur',
            'servisler.cihazAriza'
        ])
        ->join('musteriler', 'servisler.musteriid', '=', 'musteriler.id')
        ->join('cihaz_markalari', 'servisler.cihazMarka', '=', 'cihaz_markalari.id')
        ->join('cihaz_turleri', 'servisler.cihazTur', '=', 'cihaz_turleri.id')
        ->where('servisler.durum', '1')
        ->where('servisler.kid', $kid);

        if (!empty($persID)) {
            // Personnel specific logic
            $selectedDates = DB::table('servis_asama_cevaplari')
                ->where('cevap', Carbon::today()->format('d/m/Y'))
                ->where('soruid', 296)
                ->pluck('planid');

            $serviceAnswers = DB::table('servis_asama_cevaplari as sc')
                ->where('sc.cevap', $persID)
                ->whereIn('sc.planid', $selectedDates)
                ->pluck('servisid');

            $query->whereIn('servisler.id', $serviceAnswers);
        } else {
            // Apply filters
            $query->where('servisDurum', $statuses);

            if ($districts != "0") {
                $districtArray = explode(',', str_replace("'", "", $districts));
                $query->whereIn('ilce', $districtArray);
            } else {
                $query->where('il', $city);
            }

            if ($devices != "0") {
                $deviceArray = explode(',', $devices);
                $query->whereIn('cihazTur', $deviceArray);
            }

            if ($sources != "0") {
                $sourceArray = explode(',', $sources);
                $query->whereIn('servisKaynak', $sourceArray);
            }

            // Date filtering logic based on status
            if (in_array($statuses, ["235", "264"])) {
                $dateArray = explode('-', $planDate);
                $formattedDate = $dateArray[2] . '-' . $dateArray[1] . '-' . $dateArray[0];
                $query->where('musaitTarih', $formattedDate);
            } elseif ($statuses == "261") {
                // Special logic for status 261
                $this->applyStatus261Logic($query, $planDate);
            } else {
                // Check if status has date questions
                $this->applyDateLogicForStatus($query, $statuses, $planDate);
            }
        }

        $services = $query->orderBy('servisler.id', 'desc')->get();
        
        // Get personnel list for assignment
        $personnel = $this->getPersonnelList($kid);

        return view('service.planning.list', compact('services', 'personnel', 'persID'));
    }

    public function getDistricts(Request $request)
    {
        $city = $request->get('ilceSecimId');
        
        $districts = Ilce::where('sehir_id', $city)
            ->orderBy('ilceName')
            ->pluck('ilceName');
        
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
