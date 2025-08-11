<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Survey;
use App\Models\ServiceStageAnswer;
use App\Models\ServiceResource;
use App\Models\Service;
use App\Models\DeviceBrand;
use App\Models\DeviceType;
use App\Models\ServicePlanning;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;



class StatisticController extends Controller
{

     public function ServiceStatistics(Request $request)
    {
        $tenant_id = session('tenant_id');
        
        // Personeller (sadece aktif personeller)
        $personeller = User::where('tenant_id', $tenant_id)
                          ->where('status', 1)
                          ->orderBy('name', 'ASC')
                          ->get();
        
        // Servis Kaynakları
        $servisKaynaklari = ServiceResource::where('firma_id', $tenant_id)
                                          ->orderBy('id', 'DESC')
                                          ->get();

        if ($request->has('servisSayListele')) {
            return $this->getFilteredStatistics($request, $tenant_id, $personeller, $servisKaynaklari);
        }

        return $this->getDefaultStatistics($tenant_id, $personeller, $servisKaynaklari);
    }

    private function getFilteredStatistics($request, $tenant_id, $personeller, $servisKaynaklari)
    {
        // Tarihleri formatla
        $tarih1 = Carbon::createFromFormat('d/m/Y', $request->tarih1)->format('Y-m-d');
        $tarih2 = Carbon::createFromFormat('d/m/Y', $request->tarih2)->format('Y-m-d');

        // Temel sorgu
        $query = Service::where('firma_id', $tenant_id)
                       ->where('durum', 1)
                       ->whereBetween('kayitTarihi', [$tarih1 . ' 00:00:00', $tarih2 . ' 23:59:59']);

        // Personel filtresi
        if ($request->personeller != '0') {
            $query->where('kayitAlan', $request->personeller);
        }

        // Servis kaynağı filtresi
        if ($request->servisKaynak != '0') {
            $query->where('servisKaynak', $request->servisKaynak);
        }

        $servisler = $query->get();

        // İptal edilmemiş servisleri filtrele
        $validServisler = $this->filterCancelledServices($servisler);
        $validServisIds = $validServisler->pluck('id')->toArray();

        $statistics = [
            'toplam' => count($validServisIds),
            'markalar' => $this->getDeviceBrandStats($validServisIds),
            'turler' => $this->getDeviceTypeStats($validServisIds),
            'kaynaklar' => $this->getServiceResourceStats($validServisIds),
            'operatorler' => $this->getOperatorStats($validServisIds),
            'chartData' => $this->getChartData($tarih1, $tarih2, $tenant_id),
            'hourlyData' => $this->getHourlyData($tarih1, $tarih2, $tenant_id)
        ];

        return view('frontend.secure.statistics.service_statistics', compact(
            'tenant_id', 'personeller', 'servisKaynaklari', 'statistics', 'request'
        ));
    }

    private function getDefaultStatistics($tenant_id, $personeller, $servisKaynaklari)
    {
        $today = Carbon::today();
        
        $periods = [
            'bugun' => [
                'start' => $today->copy(),
                'end' => $today->copy(),
                'label' => 'Bugün'
            ],
            'son2gun' => [
                'start' => $today->copy()->subDay(),
                'end' => $today->copy(),
                'label' => 'Son İki Gün'
            ],
            'son3gun' => [
                'start' => $today->copy()->subDays(2),
                'end' => $today->copy(),
                'label' => 'Son Üç Gün'
            ],
            'son5gun' => [
                'start' => $today->copy()->subDays(4),
                'end' => $today->copy(),
                'label' => 'Son Beş Gün'
            ],
            'son7gun' => [
                'start' => $today->copy()->subDays(6),
                'end' => $today->copy(),
                'label' => 'Son Yedi Gün'
            ],
            'ayinBasi' => [
                'start' => $today->copy()->startOfMonth(),
                'end' => $today->copy(),
                'label' => 'Ayın Başından İtibaren'
            ]
        ];

        $periodStats = [];
        foreach ($periods as $key => $period) {
            $servisler = Service::where('firma_id', $tenant_id)
                              ->where('durum', 1)
                              ->whereBetween('kayitTarihi', [
                                  $period['start']->format('Y-m-d') . ' 00:00:00',
                                  $period['end']->format('Y-m-d') . ' 23:59:59'
                              ])->get();

            $validServisler = $this->filterCancelledServices($servisler);
            $validServisIds = $validServisler->pluck('id')->toArray();

            $periodStats[$key] = [
                'label' => $period['label'],
                'toplam' => count($validServisIds),
                'markalar' => $this->getDeviceBrandStats($validServisIds),
                'turler' => $this->getDeviceTypeStats($validServisIds),
                'kaynaklar' => $this->getServiceResourceStats($validServisIds),
                'operatorler' => $this->getOperatorStats($validServisIds)
            ];
        }

        $chartData = $this->getChartData($today->copy()->subDays(30)->format('Y-m-d'), $today->format('Y-m-d'), $tenant_id);
        $hourlyData = $this->getHourlyData($today->format('Y-m-d'), $today->format('Y-m-d'), $tenant_id);

        return view('frontend.secure.statistics.service_statistics', compact(
            'tenant_id', 'personeller', 'servisKaynaklari', 'periodStats', 'chartData', 'hourlyData'
        ));
    }

    private function filterCancelledServices($servisler)
    {
        return $servisler->filter(function ($servis) {
            return !ServicePlanning::where('servisid', $servis->id)
                                 ->where('gidenIslem', 244)
                                 ->exists();
        });
    }

    private function getDeviceBrandStats($servisIds)
    {
        if (empty($servisIds)) return [];

        return Service::whereIn('id', $servisIds)
                     ->join('device_brands', 'services.cihazMarka', '=', 'device_brands.id')
                     ->select('device_brands.marka', DB::raw('count(*) as sayi'))
                     ->groupBy('device_brands.marka')
                     ->orderBy('sayi', 'desc')
                     ->get();
    }

    private function getDeviceTypeStats($servisIds)
    {
        if (empty($servisIds)) return [];

        return Service::whereIn('id', $servisIds)
                     ->join('device_types', 'services.cihazTur', '=', 'device_types.id')
                     ->select('device_types.cihaz', DB::raw('count(*) as sayi'))
                     ->groupBy('device_types.cihaz')
                     ->orderBy('sayi', 'desc')
                     ->get();
    }

    private function getServiceResourceStats($servisIds)
    {
        if (empty($servisIds)) return [];

        return Service::whereIn('id', $servisIds)
                     ->join('service_resources', 'services.servisKaynak', '=', 'service_resources.id')
                     ->select('service_resources.kaynak', DB::raw('count(*) as sayi'))
                     ->groupBy('service_resources.kaynak')
                     ->orderBy('sayi', 'desc')
                     ->get();
    }

    private function getOperatorStats($servisIds)
    {
        if (empty($servisIds)) return [];

        return Service::whereIn('id', $servisIds)
                     ->join('users', 'services.kayitAlan', '=', 'users.user_id')
                     ->select('users.name', DB::raw('count(*) as sayi'))
                     ->groupBy('users.name')
                     ->orderBy('sayi', 'desc')
                     ->get();
    }

    private function getChartData($startDate, $endDate, $tenant_id)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $data = [];

        while ($start <= $end) {
            $dayServices = Service::where('firma_id', $tenant_id)
                                 ->where('durum', 1)
                                 ->whereDate('kayitTarihi', $start->format('Y-m-d'))
                                 ->get();

            $validServices = $this->filterCancelledServices($dayServices);

            $data[] = [
                'date' => $start->format('d/m'),
                'count' => $validServices->count()
            ];

            $start->addDay();
        }

        return $data;
    }

    private function getHourlyData($startDate, $endDate, $tenant_id)
    {
        $hourlyStats = Service::where('firma_id', $tenant_id)
                             ->where('durum', 1)
                             ->whereBetween('kayitTarihi', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                             ->select(DB::raw('HOUR(kayitTarihi) as hour, COUNT(*) as count'))
                             ->groupBy(DB::raw('HOUR(kayitTarihi)'))
                             ->orderBy('hour')
                             ->get();

        $data = [];
        for ($i = 0; $i < 24; $i++) {
            $hourStat = $hourlyStats->firstWhere('hour', $i);
            $data[] = [
                'hour' => str_pad($i, 2, '0', STR_PAD_LEFT) . ':00',
                'count' => $hourStat ? $hourStat->count : 0
            ];
        }

        return $data;
    }

    public function SurveyStatistics($tenant_id)
    {
        return view('frontend.secure.statistics.survey_statistics', compact('tenant_id'));
    }
   
        



   
}
