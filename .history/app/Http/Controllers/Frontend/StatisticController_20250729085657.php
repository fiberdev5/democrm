<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Survey;
use App\Models\ServiceStageAnswer;
use App\Models\StageQuestion;
use App\Models\ServiceResource;
use App\Models\Service;
use App\Models\DeviceBrand;
use App\Models\DeviceType;
use App\Models\ServicePlanning;
use App\Models\CashTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class StatisticController extends Controller
{
///////////////////////////////////////////////////////Service Statistics///////////////////////////////////////////////////////////////////
    public function ServiceStatistics(Request $request,$tenant_id)
    {
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
        
        $personeller = User::where('tenant_id', $tenant_id)
            ->whereNull('ayrilmaTarihi')
            ->whereIn('user_id', function ($query) {
                $query->select('model_id')
                    ->from('model_has_roles')
                    ->whereIn('role_id', [1, 5, 263]);
            })
            ->get();

        // Tarihleri formatla
        $tarih1 = Carbon::createFromFormat('Y-m-d', $request->tarih1)->format('Y-m-d');
        $tarih2 = Carbon::createFromFormat('Y-m-d', $request->tarih2)->format('Y-m-d');

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

            $personeller = User::where('tenant_id', $tenant_id)
            ->whereNull('ayrilmaTarihi')
            ->whereIn('user_id', function ($query) {
                $query->select('model_id')
                    ->from('model_has_roles')
                    ->whereIn('role_id', [1, 5, 263]);
            })
            ->get();


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
        return Service::join('device_brands', 'services.cihazMarka', '=', 'device_brands.id')
              ->whereIn('services.id', $servisIds) 
              ->select('device_brands.marka', DB::raw('count(*) as sayi'))
              ->groupBy('device_brands.marka')
              ->orderBy('sayi', 'desc')
              ->get();

    }
    private function getDeviceTypeStats($servisIds)
    {
        if (empty($servisIds)) return [];

          return Service::whereIn('services.id', $servisIds) 
                 ->join('device_types', 'services.cihazTur', '=', 'device_types.id')
                 ->select('device_types.cihaz', DB::raw('count(*) as sayi'))
                 ->groupBy('device_types.cihaz')
                 ->orderBy('sayi', 'desc')
                 ->get();
    }
    private function getServiceResourceStats($servisIds)
    {
        if (empty($servisIds)) return [];

           return Service::whereIn('services.id', $servisIds) 
                 ->join('service_resources', 'services.servisKaynak', '=', 'service_resources.id')
                 ->select('service_resources.kaynak', DB::raw('count(*) as sayi'))
                 ->groupBy('service_resources.kaynak')
                 ->orderBy('sayi', 'desc')
                 ->get();
    }
    private function getOperatorStats($servisIds)
    {
        if (empty($servisIds)) return [];

        return Service::whereIn('services.id', $servisIds)
                 ->join('tb_user', 'services.kayitAlan', '=', 'tb_user.user_id')
                 ->select('tb_user.name', DB::raw('count(*) as sayi'))
                 ->groupBy('tb_user.name')
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
                'date' => $start->format('Y-m-d'),
                'count' => $validServices->count()
            ];

            $start->addDay();
        }

        return $data;
    }
    public function getChartDataAjax(Request $request, $tenant_id)
{
    $days = $request->get('days', 7);
    
    // Gün sonunu al
    $endDate = Carbon::now()->endOfDay();
    $startDate = Carbon::now()->subDays($days - 1)->startOfDay();
    
    $chartData = [];
    $currentDate = $startDate->copy();
    
    while ($currentDate <= $endDate) {
        $dayServices = Service::where('firma_id', $tenant_id)
                             ->where('durum', 1)
                             ->whereDate('kayitTarihi', $currentDate->format('Y-m-d'))
                             ->get();
        
        $validServices = $this->filterCancelledServices($dayServices);
        
        $chartData[] = [
            'tarih' => $currentDate->format('Y-m-d'),
            'count' => $validServices->count()
        ];
        
        $currentDate->addDay();
    }
    
    return response()->json($chartData);
}
    private function getHourlyData($startDate, $endDate, $tenant_id)
    {
        //Saatlik istatistikleri çekme
        $hourlyStats = Service::where('firma_id', $tenant_id)
                            ->where('durum', 1)
                            ->whereBetween('created_at', [
                                Carbon::parse($startDate)->startOfDay(), 
                                Carbon::parse($endDate)->endOfDay()     
                            ])
                            ->select(DB::raw('HOUR(created_at) as hour, COUNT(*) as count')) 
                            ->groupBy(DB::raw('HOUR(created_at)')) 
                            ->orderBy('hour')
                            ->get();
        // Kolay erişim için saatleri anahtar olarak kullanan bir koleksiyona dönüştürme
        $hourlyStatsIndexed = $hourlyStats->keyBy('hour');

        $data = [];
        for ($i = 0; $i < 24; $i++) {
            $count = 0;
            if ($hourlyStatsIndexed->has($i)) {
                $count = $hourlyStatsIndexed[$i]->count;
            }
            $data[] = [
                'hour' => str_pad($i, 2, '0', STR_PAD_LEFT) . ':00', // Saati HH:00 formatına getir (örn. "09:00")
                'count' => $count
            ];
        }
        return $data;
    }
    public function getHourlyDataAjax(Request $request, $tenant_id)
    {
        $type = $request->get('type'); 
        $date = $request->get('date'); // Eğer belirli bir tarih seçilmişse
        
        $query = Service::where('firma_id', $tenant_id)->where('durum', 1);
        
        // Eğer kullanıcı tarih seçtiyse, o günün verisini getir
        if ($date) {
            $targetDate = Carbon::parse($date);
            $query->whereDate('created_at', $targetDate->format('Y-m-d'));
        } else {
            // Seçilmemişse type'a göre aralık uygula
            switch ($type) {
                case '7days':
                    $query->whereBetween('created_at', [
                        Carbon::now()->subDays(6)->startOfDay(),
                        Carbon::now()->endOfDay()
                    ]);
                    break;
                case '15days':
                    $query->whereBetween('created_at', [
                        Carbon::now()->subDays(14)->startOfDay(),
                        Carbon::now()->endOfDay()
                    ]);
                    break;
                case '30days':
                    $query->whereBetween('created_at', [
                        Carbon::now()->subDays(29)->startOfDay(),
                        Carbon::now()->endOfDay()
                    ]);
                    break;
            }
        }
        
        $services = $query->get();
        $validServices = $this->filterCancelledServices($services);
        
        // Saatlik dağılımı hesapla
        $hourlyData = [];
        for ($i = 0; $i < 24; $i++) {
            $hourlyData[$i] = 0;
        }
        
        foreach ($validServices as $service) {
            $hour = (int) Carbon::parse($service->created_at)->format('H');
            $hourlyData[$hour]++;
        }
        
        // Formatla ve döndür
        $result = [];
        for ($i = 1; $i <= 24; $i++) {
            $hour = $i === 24 ? 0 : $i; // 24:00'ı 00:00 olarak göster
            $result[] = $hourlyData[$hour];
        }
        return response()->json($result);
    }
///////////////////////////////////////////////////////Technician Statistics///////////////////////////////////////////////////////////////////
    
    public function TechnicianStatistics($tenant_id)
    {
        // Cihaz türlerini al
        $cihazTurleri = DeviceType::where('firma_id', $tenant_id)
                                 ->orderBy('cihaz', 'ASC')
                                 ->get();

        return view('frontend.secure.statistics.technician_statistics', compact(
            'tenant_id', 
            'cihazTurleri'
        ));
    }

    public function getTechnicianStatisticsData(Request $request, $tenant_id)
    {
        // Tarih aralığını parse et
        $dateRange = explode("---", $request->dateRange);
        $tarih1 = Carbon::createFromFormat('d/m/Y', $dateRange[0])->format('Y-m-d');
        $tarih2 = Carbon::createFromFormat('d/m/Y', $dateRange[1])->format('Y-m-d');
        
        // Cihaz türü filtresi
        $deviceTypeFilter = $request->cihazTur;

        // Teknisyenleri al (Teknisyen rolündeki kullanıcılar)
        $technicians = User::whereHas('roles', function($query) {
                                $query->where('name', 'Teknisyen');
                            })
                            ->where('firma_id', $tenant_id)
                            ->where('status', 1)
                            ->orderBy('name', 'ASC')
                            ->get();

        // Tarih aralığındaki tüm tarihleri al
        $dateList = $this->getDatesFromRange($tarih1, $tarih2);
        
        // Tarih sorgusunu oluştur
        $dateQuery = $this->buildDateQuery($dateList);
        
        // Seçilen tarih aralıklarının plan ID'lerini al
        $planIds = $this->getPlanIdsByDateRange($tenant_id, $dateQuery, $deviceTypeFilter);
        
        if (empty($planIds)) {
            return response()->json(['html' => $this->getEmptyTableHtml()]);
        }

        // Teknisyenlerin servis verilerini al
        $technicianServices = $this->getTechnicianServices($planIds, $technicians->pluck('id')->toArray());
        
        // Her teknisyen için istatistikleri hesapla
        $statisticsData = [];
        $techniciansNotInList = [];

        foreach ($technicianServices as $technicianId => $serviceIds) {
            $technician = $technicians->find($technicianId);
            if (!$technician) continue;

            $techniciansNotInList[] = $technicianId;
            $serviceIdList = implode(',', array_unique(explode(',', rtrim($serviceIds, ', '))));
            
            $stats = $this->calculateTechnicianStatistics($technicianId, $serviceIdList, $tarih1, $tarih2);
            $stats['technician'] = $technician;
            $stats['serviceCount'] = count(array_unique(explode(',', rtrim($serviceIds, ', '))));
            
            $statisticsData[] = $stats;
        }

        // Listede olmayan teknisyenleri ekle
        $remainingTechnicians = $technicians->whereNotIn('id', $techniciansNotInList);
        foreach ($remainingTechnicians as $technician) {
            $statisticsData[] = [
                'technician' => $technician,
                'serviceCount' => 0,
                'completed' => 0,
                'complaints' => 0,
                'cancelled' => 0,
                'willNotify' => 0,
                'priceDisagreement' => 0,
                'collectedAmount' => 0,
                'givenQuote' => 0
            ];
        }

        $html = $this->generateStatisticsTableHtml($statisticsData);
        
        return response()->json(['html' => $html]);
    }

    public function getTechnicianDetailStatistics(Request $request, $tenant_id)
    {
        $technicianId = $request->technicianId;
        $tarih1 = Carbon::createFromFormat('d/m/Y', $request->tarih1)->format('Y-m-d');
        $tarih2 = Carbon::createFromFormat('d/m/Y', $request->tarih2)->format('Y-m-d');
        $deviceTypeFilter = $request->cihazTur;

        // Detaylı grafik ve aşama verilerini hazırla
        $detailData = $this->calculateDetailedStatistics($technicianId, $tarih1, $tarih2, $deviceTypeFilter, $tenant_id);
        
        $html = $this->generateDetailStatisticsHtml($detailData, $tarih1, $tarih2);
        
        return response()->json(['html' => $html]);
    }

    private function getDatesFromRange($start, $end)
    {
        $array = [];
        $interval = new \DateInterval('P1D');
        $realEnd = new \DateTime($end);
        $realEnd->add($interval);
        $period = new \DatePeriod(new \DateTime($start), $interval, $realEnd);
        
        foreach($period as $date) {
            $array[] = $date->format('Y-m-d');
        }
        
        return $array;
    }

    private function buildDateQuery($dateList)
    {
        $conditions = [];
        foreach ($dateList as $date) {
            $format = Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y');
            $conditions[] = "sc.cevap = '{$format}'";
        }
        
        return implode(' OR ', $conditions);
    }

    private function getPlanIdsByDateRange($tenant_id, $dateQuery, $deviceTypeFilter = null)
    {
        $query = ServiceStageAnswer::alias('sc')
            ->join('stage_questions as ss', 'ss.id', '=', 'sc.soruid')
            ->join('services', 'services.id', '=', 'sc.servisid')
            ->select('sc.planid')
            ->where('sc.firma_id', $tenant_id)
            ->where('ss.cevap', 'LIKE', '%Tarih%')
            ->whereIn('sc.soruid', [296, 326, 369, 306, 337, 341])
            ->whereRaw("({$dateQuery})");

        if ($deviceTypeFilter) {
            $query->where('services.cihazTur', $deviceTypeFilter);
        }

        return $query->pluck('planid')->toArray();
    }

    private function getTechnicianServices($planIds, $technicianIds)
    {
        if (empty($planIds)) return [];

        $results = ServiceStageAnswer::alias('sc')
            ->join('stage_questions as ss', 'ss.id', '=', 'sc.soruid')
            ->select('sc.cevap', 'sc.servisid')
            ->whereIn('sc.cevap', $technicianIds)
            ->where('ss.cevap', 'LIKE', '%Grup%')
            ->whereIn('sc.planid', $planIds)
            ->get();

        $groupedServices = [];
        foreach ($results as $result) {
            if (!isset($groupedServices[$result->cevap])) {
                $groupedServices[$result->cevap] = '';
            }
            $groupedServices[$result->cevap] .= $result->servisid . ', ';
        }

        // Tekrarlanan servisleri temizle
        foreach ($groupedServices as $techId => $serviceIds) {
            $services = explode(', ', rtrim($serviceIds, ', '));
            $services = array_unique($services);
            $groupedServices[$techId] = implode(', ', $services);
        }

        return $groupedServices;
    }

    private function calculateTechnicianStatistics($technicianId, $serviceIds, $tarih1, $tarih2)
    {
        if (empty($serviceIds)) {
            return [
                'completed' => 0,
                'complaints' => 0,
                'cancelled' => 0,
                'willNotify' => 0,
                'priceDisagreement' => 0,
                'collectedAmount' => 0,
                'givenQuote' => 0
            ];
        }

        // Tamamlanan servisler
        $completed = Service::whereIn('id', explode(',', $serviceIds))
                           ->where('servisDurum', 255)
                           ->count();

        // Şikayetçi servisler
        $complaints = $this->calculateComplaintServices($technicianId, $serviceIds);
        
        // İptal servisler
        $cancelled = $this->calculateCancelledServices($technicianId, $serviceIds);
        
        // Haber verecek
        $willNotify = $this->calculateNotificationServices($technicianId, $serviceIds);
        
        // Fiyatta anlaşılamadı
        $priceDisagreement = $this->calculatePriceDisagreementServices($technicianId, $serviceIds);

        // Alınan ücretler
        $collectedAmount = CashTransaction::whereIn('servis', explode(',', $serviceIds))
                                         ->where('odemeYonu', 1)
                                         ->where('personel', $technicianId)
                                         ->whereBetween('created_at', [
                                             $tarih1 . ' 00:00:00',
                                             $tarih2 . ' 23:59:59'
                                         ])
                                         ->sum('fiyat');

        // Verilen teklifler
        $givenQuote = $this->calculateGivenQuotes($technicianId, $serviceIds, $tarih1, $tarih2);

        return [
            'completed' => $completed,
            'complaints' => $complaints,
            'cancelled' => $cancelled,
            'willNotify' => $willNotify,
            'priceDisagreement' => $priceDisagreement,
            'collectedAmount' => $collectedAmount,
            'givenQuote' => $givenQuote
        ];
    }

    private function calculateComplaintServices($technicianId, $serviceIds)
    {
        $complaints = ServicePlanning::whereIn('servisid', explode(',', $serviceIds))
                                   ->where('gidenIslem', 254)
                                   ->groupBy('servisid')
                                   ->get();

        $count = 0;
        foreach ($complaints as $complaint) {
            $firstService = ServicePlanning::where('servisid', $complaint->servisid)
                                          ->where('pid', $technicianId)
                                          ->orderBy('created_at', 'ASC')
                                          ->first();
            
            $lastComplaint = ServicePlanning::where('servisid', $complaint->servisid)
                                           ->where('gidenIslem', 254)
                                           ->orderBy('created_at', 'DESC')
                                           ->first();

            if ($firstService && $lastComplaint) {
                $firstDate = strtotime($firstService->created_at->format('Y-m-d'));
                $lastDate = strtotime($lastComplaint->created_at->format('Y-m-d'));
                
                if ($lastDate > $firstDate) {
                    $count++;
                }
            }
        }

        return $count;
    }

    private function calculateCancelledServices($technicianId, $serviceIds)
    {
        $cancellations = ServicePlanning::whereIn('servisid', explode(',', $serviceIds))
                                      ->where('gidenIslem', 244)
                                      ->groupBy('servisid')
                                      ->get();

        $count = 0;
        foreach ($cancellations as $cancellation) {
            $firstService = ServicePlanning::where('servisid', $cancellation->servisid)
                                          ->where('pid', $technicianId)
                                          ->orderBy('created_at', 'ASC')
                                          ->first();
            
            $lastCancellation = ServicePlanning::where('servisid', $cancellation->servisid)
                                              ->where('gidenIslem', 244)
                                              ->orderBy('created_at', 'DESC')
                                              ->first();

            if ($firstService && $lastCancellation) {
                $firstDate = strtotime($firstService->created_at->format('Y-m-d'));
                $lastDate = strtotime($lastCancellation->created_at->format('Y-m-d'));
                
                if ($lastDate > $firstDate) {
                    $count++;
                }
            }
        }

        return $count;
    }

    private function calculateNotificationServices($technicianId, $serviceIds)
    {
        $notifications = ServicePlanning::whereIn('servisid', explode(',', $serviceIds))
                                       ->where('gidenIslem', 247)
                                       ->groupBy('servisid')
                                       ->get();

        $count = 0;
        foreach ($notifications as $notification) {
            $firstService = ServicePlanning::where('servisid', $notification->servisid)
                                          ->where('pid', $technicianId)
                                          ->orderBy('created_at', 'ASC')
                                          ->first();
            
            $lastNotification = ServicePlanning::where('servisid', $notification->servisid)
                                              ->where('gidenIslem', 247)
                                              ->orderBy('created_at', 'DESC')
                                              ->first();

            if ($firstService && $lastNotification) {
                $firstDate = strtotime($firstService->created_at->format('Y-m-d'));
                $lastDate = strtotime($lastNotification->created_at->format('Y-m-d'));
                
                if ($lastDate > $firstDate - 1) {
                    $count++;
                }
            }
        }

        return $count;
    }

    private function calculatePriceDisagreementServices($technicianId, $serviceIds)
    {
        $priceIssues = ServicePlanning::whereIn('servisid', explode(',', $serviceIds))
                                     ->where('gidenIslem', 241)
                                     ->groupBy('servisid')
                                     ->get();

        $count = 0;
        foreach ($priceIssues as $priceIssue) {
            $firstService = ServicePlanning::where('servisid', $priceIssue->servisid)
                                          ->where('pid', $technicianId)
                                          ->orderBy('created_at', 'ASC')
                                          ->first();
            
            $lastPriceIssue = ServicePlanning::where('servisid', $priceIssue->servisid)
                                            ->where('gidenIslem', 241)
                                            ->orderBy('created_at', 'DESC')
                                            ->first();

            if ($firstService && $lastPriceIssue) {
                $firstDate = strtotime($firstService->created_at->format('Y-m-d'));
                $lastDate = strtotime($lastPriceIssue->created_at->format('Y-m-d'));
                
                if ($lastDate > $firstDate - 1) {
                    $count++;
                }
            }
        }

        return $count;
    }

    private function calculateGivenQuotes($technicianId, $serviceIds, $tarih1, $tarih2)
    {
        $quotes = ServiceStageAnswer::alias('sc')
            ->join('service_plannings as sp', 'sp.id', '=', 'sc.planid')
            ->whereIn('sc.soruid', [350, 351, 352, 353, 354, 355, 356])
            ->whereIn('sc.servisid', explode(',', $serviceIds))
            ->where('sp.pid', $technicianId)
            ->whereBetween('sc.created_at', [$tarih1, $tarih2])
            ->sum('sc.cevap');

        return $quotes ?: 0;
    }

    private function calculateDetailedStatistics($technicianId, $tarih1, $tarih2, $deviceTypeFilter, $tenant_id)
    {
        // Detaylı istatistikler için gerekli hesaplamalar
        // Bu kısım eski kodunuzdaki detay sayfası mantığını içerir
        
        $dateList = $this->getDatesFromRange($tarih1, $tarih2);
        $dateQuery = $this->buildDateQuery($dateList);
        $planIds = $this->getPlanIdsByDateRange($tenant_id, $dateQuery, $deviceTypeFilter);
        
        // Günlük bazda veriler
        $dailyData = [];
        foreach ($dateList as $date) {
            $dailyData[$date] = $this->calculateDailyStatistics($technicianId, $date, $planIds);
        }
        
        return [
            'dailyData' => $dailyData,
            'dateList' => $dateList,
            'totalStats' => $this->calculateTotalDetailedStatistics($technicianId, $planIds)
        ];
    }

    private function calculateDailyStatistics($technicianId, $date, $planIds)
    {
        // Günlük istatistikleri hesapla
        $dayPlanIds = $this->getDayPlanIds($date, $planIds);
        
        if (empty($dayPlanIds)) {
            return [
                'completed' => 0,
                'cancelled' => 0,
                'revenue' => 0
            ];
        }

        $services = $this->getTechnicianServicesByPlanIds($technicianId, $dayPlanIds);
        
        if (empty($services)) {
            return [
                'completed' => 0,
                'cancelled' => 0,
                'revenue' => 0
            ];
        }

        $completed = Service::whereIn('id', $services)->where('servisDurum', 255)->count();
        $cancelled = ServicePlanning::whereIn('servisid', $services)->where('gidenIslem', 244)->groupBy('servisid')->count();
        $revenue = CashTransaction::whereIn('servis', $services)
                                 ->where('odemeYonu', 1)
                                 ->where('personel', $technicianId)
                                 ->whereDate('created_at', $date)
                                 ->sum('fiyat');

        return [
            'completed' => $completed,
            'cancelled' => $cancelled,
            'revenue' => $revenue
        ];
    }

    private function getDayPlanIds($date, $allPlanIds)
    {
        $formattedDate = Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y');
        
        return ServiceStageAnswer::whereIn('planid', $allPlanIds)
                                ->where('cevap', $formattedDate)
                                ->pluck('planid')
                                ->toArray();
    }

    private function getTechnicianServicesByPlanIds($technicianId, $planIds)
    {
        return ServiceStageAnswer::alias('sc')
            ->join('stage_questions as ss', 'ss.id', '=', 'sc.soruid')
            ->where('sc.cevap', $technicianId)
            ->where('ss.cevap', 'LIKE', '%Grup%')
            ->whereIn('sc.planid', $planIds)
            ->pluck('sc.servisid')
            ->unique()
            ->toArray();
    }

    private function calculateTotalDetailedStatistics($technicianId, $planIds)
    {
        // Tüm detaylı istatistikleri hesapla (aşama bazında)
        $services = $this->getTechnicianServicesByPlanIds($technicianId, $planIds);
        
        if (empty($services)) {
            return $this->getEmptyDetailedStats();
        }

        $serviceIdList = implode(',', $services);
        
        return [
            'assignedServices' => count($services),
            'completedServices' => Service::whereIn('id', $services)->where('servisDurum', 255)->count(),
            'workshopRepair' => $this->calculateStageCount($technicianId, $serviceIdList, 250),
            'takeToWorkshop' => $this->calculateStageCount($technicianId, $serviceIdList, 240),
            'deviceInWorkshop' => $this->calculateStageCount($technicianId, $serviceIdList, 237),
            'cannotRepair' => $this->calculateStageCount($technicianId, $serviceIdList, 246),
            'deviceDelivered' => $this->calculateStageCount($technicianId, $serviceIdList, 253),
            'deviceDeliveredWithPart' => $this->calculateStageCount($technicianId, $serviceIdList, 260),
            'customerBroughtToWorkshop' => $this->calculateStageCount($technicianId, $serviceIdList, 249),
            'cannotReachCustomer' => $this->calculateStageCount($technicianId, $serviceIdList, 243),
            'sendForDelivery' => $this->calculateStageCount($technicianId, $serviceIdList, 262),
            'inDelivery' => $this->calculateStageCount($technicianId, $serviceIdList, 251),
            'partReady' => $this->calculateStageCount($technicianId, $serviceIdList, 261),
            'partOrdered' => $this->calculateStageCount($technicianId, $serviceIdList, 263),
            'partAssignment' => $this->calculateStageCount($technicianId, $serviceIdList, 257),
            'partRequest' => $this->calculateStageCount($technicianId, $serviceIdList, 238),
            'partDelivery' => $this->calculateStageCount($technicianId, $serviceIdList, 259),
            'partTakenToWorkshop' => $this->calculateStageCount($technicianId, $serviceIdList, 245),
            'sendToCollection' => $this->calculateStageCount($technicianId, $serviceIdList, 258),
            'readyForDelivery' => $this->calculateStageCount($technicianId, $serviceIdList, 252),
            'guaranteeExpired' => $this->calculateStageCount($technicianId, $serviceIdList, 242),
            'saleCompleted' => $this->calculateStageCount($technicianId, $serviceIdList, 242),
            'reassignTechnician' => $this->calculateStageCount($technicianId, $serviceIdList, 248),
            'onSiteMaintenance' => $this->calculateStageCount($technicianId, $serviceIdList, 239)
        ];
    }

    private function calculateStageCount($technicianId, $serviceIds, $stageId)
    {
        $stages = ServicePlanning::whereIn('servisid', explode(',', $serviceIds))
                                ->where('gidenIslem', $stageId)
                                ->groupBy('servisid')
                                ->get();

        $count = 0;
        foreach ($stages as $stage) {
            $firstService = ServicePlanning::where('servisid', $stage->servisid)
                                          ->where('pid', $technicianId)
                                          ->orderBy('created_at', 'ASC')
                                          ->first();
            
            $lastStage = ServicePlanning::where('servisid', $stage->servisid)
                                       ->where('gidenIslem', $stageId)
                                       ->orderBy('created_at', 'DESC')
                                       ->first();

            if ($firstService && $lastStage) {
                $firstDate = strtotime($firstService->created_at->format('Y-m-d'));
                $lastDate = strtotime($lastStage->created_at->format('Y-m-d'));
                
                if ($lastDate > $firstDate - 1) {
                    $count++;
                }
            }
        }

        return $count;
    }

    private function getEmptyDetailedStats()
    {
        return [
            'assignedServices' => 0,
            'completedServices' => 0,
            'workshopRepair' => 0,
            'takeToWorkshop' => 0,
            'deviceInWorkshop' => 0,
            'cannotRepair' => 0,
            'deviceDelivered' => 0,
            'deviceDeliveredWithPart' => 0,
            'customerBroughtToWorkshop' => 0,
            'cannotReachCustomer' => 0,
            'sendForDelivery' => 0,
            'inDelivery' => 0,
            'partReady' => 0,
            'partOrdered' => 0,
            'partAssignment' => 0,
            'partRequest' => 0,
            'partDelivery' => 0,
            'partTakenToWorkshop' => 0,
            'sendToCollection' => 0,
            'readyForDelivery' => 0,
            'guaranteeExpired' => 0,
            'saleCompleted' => 0,
            'reassignTechnician' => 0,
            'onSiteMaintenance' => 0
        ];
    }

    private function generateStatisticsTableHtml($statisticsData)
    {
        $html = '<div class="table-responsive">
                    <table class="table table-hover table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Personel</th>
                                <th style="width: 85px">Atanan Servis</th>
                                <th style="width: 85px">Tamamlanan</th>
                                <th style="width: 85px">Şikayetçi</th>
                                <th style="width: 85px">İptal</th>
                                <th style="width: 85px">Haber Verecek</th>
                                <th style="width: 85px">Fiyat Anlaşmazlığı</th>
                                <th style="width: 85px">Alınan Ücret</th>
                                <th style="width: 85px">Verilen Teklif</th>
                            </tr>
                        </thead>
                        <tbody>';

        foreach ($statisticsData as $data) {
            $html .= '<tr data-persid="' . $data['technician']->id . '" class="tdDetayBtn cursor-pointer hover:bg-gray-50">
                        <td><strong>' . $data['technician']->name . '</strong></td>
                        <td><strong>' . $data['serviceCount'] . '</strong></td>
                        <td><strong>' . $data['completed'] . '</strong></td>
                        <td><strong>' . $data['complaints'] . '</strong></td>
                        <td><strong>' . $data['cancelled'] . '</strong></td>
                        <td><strong>' . $data['willNotify'] . '</strong></td>
                        <td><strong>' . $data['priceDisagreement'] . '</strong></td>
                        <td data-sort="' . $data['collectedAmount'] . '"><strong>' . number_format($data['collectedAmount'], 2) . ' TL</strong></td>
                        <td data-sort="' . $data['givenQuote'] . '"><strong>' . number_format($data['givenQuote'], 2) . ' TL</strong></td>
                      </tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
    }

    private function generateDetailStatisticsHtml($detailData, $tarih1, $tarih2)
    {
        // Grafik için etiketler ve veriler
        $labels = [];
        $completedData = [];
        $cancelledData = [];
        $revenueData = [];

        foreach ($detailData['dateList'] as $date) {
            $labels[] = "'" . Carbon::createFromFormat('Y-m-d', $date)->format('d/m') . "'";
            $completedData[] = $detailData['dailyData'][$date]['completed'];
            $cancelledData[] = $detailData['dailyData'][$date]['cancelled'];
            $revenueData[] = $detailData['dailyData'][$date]['revenue'];
        }

        $html = '<div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6>Tamamlanan Servisler</h6>
                                <canvas id="completedChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h6>İptal Servisler</h6>
                                <canvas id="cancelledChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6>Alınan Ücretler</h6>
                                <canvas id="revenueChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>';

        // Aşama istatistikleri
        $stats = $detailData['totalStats'];
        $html .= '<div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Aşama Detayları</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">';

        $stageLabels = [
            'assignedServices' => 'Atanan Servisler',
            'completedServices' => 'Tamamlanan Servisler',
            'workshopRepair' => 'Atölyede Tamir',
            'takeToWorkshop' => 'Atölyeye Aldır',
            'deviceInWorkshop' => 'Cihaz Atölyede',
            'cannotRepair' => 'Tamir Edilemiyor',
            'deviceDelivered' => 'Cihaz Teslim Edildi',
            'deviceDeliveredWithPart' => 'Cihaz Teslim (Parça)',
            'customerBroughtToWorkshop' => 'Müşteri Atölyeye Getirdi',
            'cannotReachCustomer' => 'Müşteriye Ulaşılamadı',
            'sendForDelivery' => 'Nakliye Gönder',
            'inDelivery' => 'Nakliyede',
            'partReady' => 'Parça Hazır',
            'partOrdered' => 'Parça Siparişte',
            'partAssignment' => 'Parça Teknisyen Yönlendir',
            'partRequest' => 'Parça Talep Et',
            'partDelivery' => 'Parça Teslim Et',
            'partTakenToWorkshop' => 'Parça Atölyeye Alındı',
            'sendToCollection' => 'Tahsilata Gönder',
            'readyForDelivery' => 'Teslimata Hazır',
            'guaranteeExpired' => 'Garanti Süresi Doldu',
            'saleCompleted' => 'Satış Yapıldı',
            'reassignTechnician' => 'Yeniden Teknisyen Yönlendir',
            'onSiteMaintenance' => 'Yerinde Bakım'
        ];

        foreach ($stageLabels as $key => $label) {
            $value = $stats[$key] ?? 0;
            $html .= '<div class="col-md-3 col-sm-6 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <p class="card-text">' . $label . '</p>
                                <h4 class="card-title text-primary">' . $value . '</h4>
                            </div>
                        </div>
                      </div>';
        }

        $html .= '          </div>
                            </div>
                        </div>
                    </div>
                </div>';

        // JavaScript için chart verileri
        $html .= '<script>
                    const labels = [' . implode(',', $labels) . '];
                    const completedData = [' . implode(',', $completedData) . '];
                    const cancelledData = [' . implode(',', $cancelledData) . '];
                    const revenueData = [' . implode(',', $revenueData) . '];
                    
                    // Charts will be initialized by the frontend
                    window.chartData = {
                        labels: labels,
                        completed: completedData,
                        cancelled: cancelledData,
                        revenue: revenueData
                    };
                </script>';

        return $html;
    }

    private function getEmptyTableHtml()
    {
        return '<div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i>
                    <p class="mb-0">Seçilen tarih aralığında herhangi bir servis hareketi bulunamadı.</p>
                </div>';
    }


///////////////////////////////////////////////////////Survey Statistics///////////////////////////////////////////////////////////////////
    public function SurveyStatistics($tenant_id)
    {
        return view('frontend.secure.statistics.survey_statistics', compact('tenant_id'));
    }

   
}

