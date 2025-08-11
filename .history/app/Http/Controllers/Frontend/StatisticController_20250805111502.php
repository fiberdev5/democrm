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
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

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

    $startDate = $request->startDate ?: Carbon::yesterday()->format('Y-m-d');
    $endDate = $request->endDate ?: Carbon::yesterday()->format('Y-m-d');
    $deviceTypeId = $request->cihazTur;

    // Tarih aralığındaki tüm günleri al
    $dateRange = CarbonPeriod::create($startDate, $endDate)->toArray();
    
    // Teknisyenleri getir (tb_user tablosundan, role: Teknisyen)
    $technicians = User::where('tenant_id', $tenant_id)
        ->whereHas('roles', function($query) {
            $query->where('name', 'Teknisyen');
        })
        ->where('status', 1)
        ->orderBy('name', 'ASC')
        ->get();

    // Seçilen tarih aralığındaki planları bul
    $servicePlans = $this->getServicePlansForDateRange($dateRange, $tenant_id, $deviceTypeId);
    
    // Teknisyenlerin servislerini grupla
    $technicianServices = $this->groupTechnicianServices($servicePlans, $technicians->pluck('id')->toArray());

    $results = [];
    $processedTechnicians = [];

    foreach ($technicianServices as $technicianId => $services) {
        $technician = $technicians->where('id', $technicianId)->first();
        if (!$technician) continue;

        $processedTechnicians[] = $technicianId;
        $serviceIds = implode(',', array_unique($services));
        
        $stats = $this->calculateTechnicianStats($technicianId, $serviceIds, $startDate, $endDate);
        
        $results[] = [
            'id' => $technicianId,
            'name' => $technician->name,
            'assigned_services' => count(array_unique($services)),
            'completed_services' => $stats['completed'],
            'complaint_services' => $stats['complaints'],
            'cancelled_services' => $stats['cancelled'],
            'callback_services' => $stats['callbacks'],
            'price_disagreement' => $stats['price_disagreement'],
            'collected_amount' => $stats['collected_amount'],
            'given_offers' => $stats['given_offers']
        ];
    }

    // Hiç servis almayan teknisyenleri ekle
    $remainingTechnicians = $technicians->whereNotIn('id', $processedTechnicians);
    foreach ($remainingTechnicians as $technician) {
        $results[] = [
            'id' => $technician->id,
            'name' => $technician->name,
            'assigned_services' => 0,
            'completed_services' => 0,
            'complaint_services' => 0,
            'cancelled_services' => 0,
            'callback_services' => 0,
            'price_disagreement' => 0,
            'collected_amount' => 0,
            'given_offers' => 0
        ];
    }

    return view('frontend.secure.statistics.technician_statistics_table', compact('results'))->render();
}

public function getTechnicianStatisticsDetail(Request $request, $tenant_id)
{
    $technicianId = $request->personelTabloDetayGetir;
    $startDate = Carbon::createFromFormat('d/m/Y', $request->tarih1)->format('Y-m-d');
    $endDate = Carbon::createFromFormat('d/m/Y', $request->tarih2)->format('Y-m-d');
    $deviceTypeId = $request->cihazTur;

    // Teknisyenin detay bilgilerini getir
    $detailData = $this->getTechnicianDetailData($technicianId, $startDate, $endDate, $deviceTypeId);

    return view('frontend.secure.statistics.technician_statistics_detail', compact('detailData'))->render();
}
/**
 * Tarih aralığındaki servis planlarını getir
 */
private function getServicePlansForDateRange($dateRange, $tenant_id, $deviceTypeId = null)
{
    $dateConditions = [];
    foreach ($dateRange as $date) {
        $dateConditions[] = $date->format('d/m/Y');
    }

    $query = ServiceStageAnswer::select([
            'service_stage_answers.id',
            'service_stage_answers.servisid',
            'service_stage_answers.planid',
            'service_stage_answers.soruid',
            'service_stage_answers.cevap',
            'stage_questions.cevapTuru as question_answer',
            'services.cihazTur'
        ])
        ->leftJoin('stage_questions', 'stage_questions.id', '=', 'service_stage_answers.soruid')
        ->leftJoin('services', 'services.id', '=', 'service_stage_answers.servisid')
        ->where('stage_questions.cevapTuru', 'LIKE', '%Tarih%')
        ->where('services.tenant_id', $tenant_id)
        ->whereIn('service_stage_answers.cevap', $dateConditions);

    if ($deviceTypeId) {
        $query->where('services.cihazTur', $deviceTypeId);
    }

    $results = $query->get();

    // Sadece belirli soru ID'lerini filtrele (eski sistemdeki soru ID'leri yerine yeni sistem mantığı)
    $validQuestionIds = [48, 51, 27, 35, 41]; // Bu ID'leri yeni sisteme göre güncellemelisiniz
    
    return $results->where('soruid', 'in', $validQuestionIds)->pluck('planid')->toArray();
}

/**
 * Teknisyenlerin servislerini grupla
 */
private function groupTechnicianServices($servicePlans, $technicianIds)
{
    if (empty($servicePlans)) {
        return [];
    }

    $results = ServiceStageAnswer::select([
            'service_stage_answers.servisid',
            'service_stage_answers.cevap as technician_id'
        ])
        ->leftJoin('stage_questions', 'stage_questions.id', '=', 'service_stage_answers.soruid')
        ->whereIn('service_stage_answers.cevap', $technicianIds)
        ->where('stage_questions.cevapTuru', 'LIKE', '%Grup%')
        ->whereIn('service_stage_answers.planid', $servicePlans)
        ->get();

    $grouped = [];
    foreach ($results as $result) {
        if (!isset($grouped[$result->technician_id])) {
            $grouped[$result->technician_id] = [];
        }
        $grouped[$result->technician_id][] = $result->servisid;
    }

    // Her teknisyen için benzersiz servis ID'lerini al
    foreach ($grouped as $techId => $services) {
        $grouped[$techId] = array_unique($services);
    }

    return $grouped;
}

/**
 * Teknisyen istatistiklerini hesapla
 */
private function calculateTechnicianStats($technicianId, $serviceIds, $startDate, $endDate)
{
    if (empty($serviceIds)) {
        return [
            'completed' => 0,
            'complaints' => 0,
            'cancelled' => 0,
            'callbacks' => 0,
            'price_disagreement' => 0,
            'collected_amount' => 0,
            'given_offers' => 0
        ];
    }

    // Tamamlanan servisler
    $completed = Service::whereIn('id', explode(',', $serviceIds))
        ->where('servisDurum', '255') // status alanını kendi sisteminize göre güncelleyin
        ->count();

    // Şikayet servisleri
    $complaints = $this->calculateComplaintServices($technicianId, $serviceIds);

    // İptal servisleri
    $cancelled = $this->calculateCancelledServices($technicianId, $serviceIds);

    // Haber verecek servisleri
    $callbacks = $this->calculateCallbackServices($technicianId, $serviceIds);

    // Fiyatta anlaşılamayan servisleri
    $priceDisagreement = $this->calculatePriceDisagreementServices($technicianId, $serviceIds);

    // Alınan ücret
    $collectedAmount = CashTransaction::whereIn('servis', explode(',', $serviceIds))
        ->where('odemeYonu', 1) // Gelen ödeme
        ->where('personel', $technicianId)
        ->whereBetween('created-at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
        ->sum('fiyat');

    // Verilen teklifler
    $givenOffers = $this->calculateGivenOffers($technicianId, $serviceIds, $startDate, $endDate);

    return [
        'completed' => $completed,
        'complaints' => $complaints,
        'cancelled' => $cancelled,
        'callbacks' => $callbacks,
        'price_disagreement' => $priceDisagreement,
        'collected_amount' => $collectedAmount ?: 0,
        'given_offers' => $givenOffers
    ];
}
//Şikayet servis
private function calculateComplaintServices($technicianId, $serviceIds)
{
    $complaintServices = ServicePlanning::whereIn('servisid', explode(',', $serviceIds))
        ->where('gidenIslem', '254') // Şikayet işlem türü
        ->groupBy('servisid')
        ->get();

    $count = 0;
    foreach ($complaintServices as $service) {
        $firstService = ServicePlanning::where('servisid', $service->servisid)
            ->where('pid', $technicianId)
            ->orderBy('created_at', 'ASC')
            ->first();

        $lastComplaint = ServicePlanning::where('servisid', $service->servisid)
            ->where('gidenIslem', '254')
            ->orderBy('created_at', 'DESC')
            ->first();

        if ($firstService && $lastComplaint) {
            $firstDate = Carbon::parse($firstService->created_at)->format('Y-m-d');
            $lastDate = Carbon::parse($lastComplaint->created_at)->format('Y-m-d');
            
            if ($lastDate > $firstDate) {
                $count++;
            }
        }
    }

    return $count;
}
/**
 * İptal servislerini hesapla
 */
private function calculateCancelledServices($technicianId, $serviceIds)
{
    $cancelledServices = ServicePlanning::whereIn('servisid', explode(',', $serviceIds))
        ->where('gidenIslem', '244') // İptal işlem türü
        ->groupBy('servisid')
        ->get();

    $count = 0;
    foreach ($cancelledServices as $service) {
        $firstService = ServicePlanning::where('servisid', $service->servisid)
            ->where('pid', $technicianId)
            ->orderBy('created_at', 'ASC')
            ->first();

        $lastCancelled = ServicePlanning::where('servisid', $service->servisid)
            ->where('gidenIslem', '244')
            ->orderBy('created_at', 'DESC')
            ->first();

        if ($firstService && $lastCancelled) {
            $firstDate = Carbon::parse($firstService->created_at)->format('Y-m-d');
            $lastDate = Carbon::parse($lastCancelled->created_at)->format('Y-m-d');
            
            if ($lastDate > $firstDate) {
                $count++;
            }
        }
    }

    return $count;
}

/**
 * Haber verecek servislerini hesapla
 */
private function calculateCallbackServices($technicianId, $serviceIds)
{
    $callbackServices = ServicePlanning::whereIn('servisid', explode(',', $serviceIds))
        ->where('gidenIslem', '247') // Haber verecek işlem türü
        ->groupBy('servisid')
        ->get();

    $count = 0;
    foreach ($callbackServices as $service) {
        $firstService = ServicePlanning::where('servisid', $service->servisid)
            ->where('pid', $technicianId)
            ->orderBy('created_at', 'ASC')
            ->first();

        $lastCallback = ServicePlanning::where('servisid', $service->servisid)
            ->where('gidenIslem', '247')
            ->orderBy('created_at', 'DESC')
            ->first();

        if ($firstService && $lastCallback) {
            $firstDate = Carbon::parse($firstService->created_at)->format('Y-m-d');
            $lastDate = Carbon::parse($lastCallback->created_at)->format('Y-m-d');
            
            if ($lastDate >= $firstDate - 1) {
                $count++;
            }
        }
    }

    return $count;
}
/**
 * Fiyatta anlaşılamayan servislerini hesapla
 */
private function calculatePriceDisagreementServices($technicianId, $serviceIds)
{
    $priceServices = ServicePlanning::whereIn('servisid', explode(',', $serviceIds))
        ->where('gidenIslem', '241') // Fiyat anlaşmazlığı işlem türü
        ->groupBy('servisid')
        ->get();

    $count = 0;
    foreach ($priceServices as $service) {
        $firstService = ServicePlanning::where('servisid', $service->servisid)
            ->where('pid', $technicianId)
            ->orderBy('created_at', 'ASC')
            ->first();

        $lastPrice = ServicePlanning::where('servisid', $service->servisid)
            ->where('gidenIslem', '241')
            ->orderBy('created_at', 'DESC')
            ->first();

        if ($firstService && $lastPrice) {
            $firstDate = Carbon::parse($firstService->created_at)->format('Y-m-d');
            $lastDate = Carbon::parse($lastPrice->created_at)->format('Y-m-d');
            
            if ($lastDate >= $firstDate - 1) {
                $count++;
            }
        }
    }

    return $count;
}
/**
 * Verilen teklifleri hesapla
 */
private function calculateGivenOffers($technicianId, $serviceIds, $startDate, $endDate)
{
    // Teklif soru ID'leri (350-356 arası, sisteminize göre güncelleyin)
    $offerQuestionIds = [350, 351, 352, 353, 354, 355, 356];
    
    $offers = ServiceStageAnswer::select(['service_stage_answers.cevap', 'service_plannings.created_at'])
        ->leftJoin('service_plannings', 'service_plannings.id', '=', 'service_stage_answers.planid')
        ->whereIn('service_stage_answers.soruid', $offerQuestionIds)
        ->whereIn('service_stage_answers.servisid', explode(',', $serviceIds))
        ->where('service_plannings.pid', $technicianId)
        ->get();

    $total = 0;
    foreach ($offers as $offer) {
        $offerDate = Carbon::parse($offer->date)->format('Y-m-d');
        if ($offerDate >= $startDate && $offerDate <= $endDate) {
            $total += floatval($offer->answer);
        }
    }

    return $total;
}
private function getTechnicianDetailData($technicianId, $startDate, $endDate, $deviceTypeId)
{
    // Bu metod teknisyen detay sayfası için gerekli verileri döndürür
    // Implementasyon ihtiyaçlarınıza göre genişletilebilir
    
    return [
        'technician_id' => $technicianId,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'device_type_id' => $deviceTypeId,
        // Detay verileri buraya eklenecek
    ];
}



    
///////////////////////////////////////////////////////Operatör Statistics//////////////////////////////////////////////////////////////////
  public function OperatorStatistics(Request $request, $tenant_id)
{
    if ($request->ajax()) {
        // AJAX request için DataTable verisi döndür
       $query = DB::table('services as s')
      ->join('tb_user as u', 's.kayitAlan', '=', 'u.user_id')
      ->select('u.user_id as id', 'u.name', DB::raw('COUNT(s.id) as toplam'))
      ->where('s.durum', 1); 


        // Tarih filtreleme
        if ($request->has('from_date') && $request->has('to_date')) {
            $from_date = Carbon::createFromFormat('Y-m-d', $request->from_date)->startOfDay();
            $to_date = Carbon::createFromFormat('Y-m-d', $request->to_date)->endOfDay();
            
            $query->whereBetween('s.kayitTarihi', [$from_date, $to_date]);
        }

        // Arama filtreleme
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where('u.name', 'LIKE', '%' . $searchValue . '%');
        }

        $query->groupBy('u.user_id', 'u.name');

        // Sıralama
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];
            
            $columns = ['name', 'toplam'];
            
            if (isset($columns[$orderColumn])) {
                if ($columns[$orderColumn] == 'name') {
                    $query->orderBy('u.name', $orderDirection);
                } else {
                    $query->orderBy('toplam', $orderDirection);
                }
            }
        } else {
            $query->orderByDesc('toplam');
        }

        // Sayfalama için toplam kayıt sayısı
        $totalRecords = DB::table('services as s')
            ->join('tb_user as u', 's.kayitAlan', '=', 'u.user_id')
            ->select(DB::raw('COUNT(DISTINCT u.user_id) as total'))
            ->first()
            ->total;

        // Filtrelenmiş kayıt sayısı
        $filteredRecords = $query->get()->count();

        // Sayfalama
        if ($request->has('start') && $request->has('length')) {
            $query->offset($request->start)->limit($request->length);
        }

        $data = $query->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    // Normal sayfa yüklemesi
    return view('frontend.secure.statistics.operator_statistics', compact('tenant_id'));
}
///////////////////////////////////////////////////////State Statistics//////////////////////////////////////////////////////////////////
public function StateStatistics(Request $request, $tenant_id)
{
    if ($request->ajax()) {
        $query = DB::table('services as s')
            ->join('service_stages as ss', 's.servisDurum', '=', 'ss.id')
            ->select('ss.id as durum_id','ss.asama as durum', DB::raw('COUNT(s.id) as toplam'))
            ->where('s.firma_id', $tenant_id)
            ->where('s.durum', 1); 

        // Tarih filtresi
        if ($request->has('from_date') && $request->has('to_date')) {
            $from_date = Carbon::createFromFormat('Y-m-d', $request->from_date)->startOfDay();
            $to_date = Carbon::createFromFormat('Y-m-d', $request->to_date)->endOfDay();

            $query->whereBetween('s.kayitTarihi', [$from_date, $to_date]);
        }

        // Arama filtresi (durum adı)
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where('ss.asama', 'LIKE', "%$searchValue%");
        }

       $query->groupBy('ss.id', 'ss.asama');
        // Sıralama
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];
            $columns = ['durum', 'toplam'];

            if (isset($columns[$orderColumn])) {
                $query->orderBy($columns[$orderColumn], $orderDirection);
            }
        } else {
            $query->orderByDesc('toplam');
        }

        $filteredRecords = $query->get()->count();

        if ($request->has('start') && $request->has('length')) {
            $query->offset($request->start)->limit($request->length);
        }

        $data = $query->get();

        $totalRecords = DB::table('services')
            ->where('firma_id', $tenant_id)
            ->distinct('servisDurum')
            ->count('servisDurum');

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    return view('frontend.secure.statistics.state_statistics', compact('tenant_id'));
}
///////////////////////////////////////////////////////Stage Statistics//////////////////////////////////////////////////////////////////
public function StageStatistics(Request $request, $tenant_id) 
{
    if ($request->ajax()) {
        // Tarih aralığını al
        $from_date = $request->from_date ? Carbon::createFromFormat('Y-m-d', $request->from_date)->startOfDay() : Carbon::now()->subMonth()->startOfDay();
        $to_date = $request->to_date ? Carbon::createFromFormat('Y-m-d', $request->to_date)->endOfDay() : Carbon::now()->endOfDay();
        
        // service_plannings tablosundan verileri al - sadece aktif servislere ait olanlar
        $plannings = DB::table('service_plannings as sp')
            ->join('services as s', 'sp.servisid', '=', 's.id')
            ->where('s.firma_id', $tenant_id)
            ->where('s.durum', 1) // Sadece aktif servisler
            ->whereBetween('sp.created_at', [$from_date, $to_date])
            ->select('sp.servisid', 'sp.gidenIslem')
            ->orderBy('sp.servisid', 'ASC')
            ->get();
        
        // Servis ID'ye göre grupla ve işlemleri birleştir
        $groupConcat = [];
        foreach ($plannings as $row) {
            if (!isset($groupConcat[$row->servisid])) {
                $groupConcat[$row->servisid] = $row->gidenIslem . ", ";
            } else {
                $groupConcat[$row->servisid] .= $row->gidenIslem . ", ";
            }
        }
        
        // Her servis için benzersiz aşamaları çıkar
        $arrayUnique = [];
        foreach ($groupConcat as $key => $value) {
            $newVal = trim($value);
            $newVal = substr($newVal, 0, -1);
            $newVal = explode(", ", $newVal);
            $newVal = array_unique($newVal);
            $newVal = implode(", ", $newVal);
            $arrayUnique[$key] = $newVal;
        }
        
        // Tüm aşamaları topla
        $asamalar = [];
        foreach ($arrayUnique as $key => $value) {
            $newVal = explode(", ", $value);
            $newVal = array_unique($newVal);
            foreach ($newVal as $val) {
                if (!empty($val)) {
                    $asamalar[] = $val;
                }
            }
        }
        
        sort($asamalar);
        
        // Her aşamadan kaç tane olduğunu say
        $sayilar = [];
        foreach ($asamalar as $asama) {
            if (!isset($sayilar[$asama])) {
                $sayilar[$asama] = 0;
            }
            $sayilar[$asama]++;
        }
        
        // Aşama isimlerini veritabanından çek
        $data = [];
        foreach ($sayilar as $asama_id => $count) {
            $asamaInfo = DB::table('service_stages')->where('id', $asama_id)->first();
            if ($asamaInfo) {
                $data[] = [
                    'asama_id' => $asama_id,
                    'asama' => $asamaInfo->asama,
                    'toplam' => $count
                ];
            }
        }
        
        // Sıralama
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];
            
            if ($orderColumn == 1) { // toplam sütunu
                usort($data, function($a, $b) use ($orderDirection) {
                    return $orderDirection === 'asc' ? $a['toplam'] <=> $b['toplam'] : $b['toplam'] <=> $a['toplam'];
                });
            }
        } else {
            // Varsayılan olarak toplama göre azalan sırala
            usort($data, function($a, $b) {
                return $b['toplam'] <=> $a['toplam'];
            });
        }
        
        // Arama filtresi
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = strtolower($request->search['value']);
            $data = array_filter($data, function($item) use ($searchValue) {
                return strpos(strtolower($item['asama']), $searchValue) !== false;
            });
        }
        
        // Toplam kayıt sayısını hesapla (aktif servislerin aşama sayısı)
        $totalPlannings = DB::table('service_plannings as sp')
            ->join('services as s', 'sp.servisid', '=', 's.id')
            ->where('s.firma_id', $tenant_id)
            ->where('s.durum', 1)
            ->distinct('sp.gidenIslem')
            ->count('sp.gidenIslem');
            
        $totalRecords = $totalPlannings;
        $filteredRecords = count($data);
        
        // Sayfalama
        if ($request->has('start') && $request->has('length')) {
            $data = array_slice($data, $request->start, $request->length);
        }
        
        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => array_values($data)
        ]);
    }
    
    return view('frontend.secure.statistics.stage_statistics', compact('tenant_id'));
}
///////////////////////////////////////////////////////Stocks Statistics///////////////////////////////////////////////////////////////////
public function StockStatistics($tenant_id)
{
    return view('frontend.secure.statistics.stock_statistics', compact('tenant_id'));
}
 public function getPersonelDepoData(Request $request, $tenant_id)
{
    if ($request->ajax()) {

        // Tarih filtresi olmayan hali:
        $usersWithRoles = User::role(['Teknisyen', 'Teknisyen Yardımcısı'])
            ->where('tenant_id', $tenant_id)
            ->withSum(['personelStocks as toplam_adet' => function ($query) {
                $query->where('adet', '!=', 0);
                // whereBetween tarih filtresi kaldırıldı
            }], 'adet')
            ->having('toplam_adet', '>', 0)
            ->get();

        return Datatables::of($usersWithRoles)
            ->addIndexColumn()
            ->addColumn('personel_name', function($row){
                return '<strong>' . $row->name . '</strong>';
            })
            ->addColumn('toplam_adet', function($row){
                return '<strong>' . $row->toplam_adet . '</strong>';
            })
            ->addColumn('action', function($row) use ($tenant_id){
                return '';
            })
            ->rawColumns(['personel_name', 'toplam_adet', 'action']) // HTML içeriğini render etmek için
            ->make(true);
    }
}

///////////////////////////////////////////////////////İlçe Statistics///////////////////////////////////////////////////////////////////
public function IlceStatistics(Request $request, $tenant_id)
{
    if ($request->ajax()) {
        $query = DB::table('services as s')
            ->join('customers as c', 's.musteri_id', '=', 'c.id')
            ->join('ilces as i', 'c.ilce', '=', 'i.id') // ilces tablosuna join
            ->join('ils as il', 'c.il', '=', 'il.id')
            ->select('il.name as ilName', 'i.ilceName', DB::raw('COUNT(s.id) as toplam'))
            ->where('s.durum', 1);

        // Tarih aralığı filtresi
        if ($request->has('from_date') && $request->has('to_date')) {
            $from_date = Carbon::createFromFormat('Y-m-d', $request->from_date)->startOfDay();
            $to_date = Carbon::createFromFormat('Y-m-d', $request->to_date)->endOfDay();
            $query->whereBetween('s.kayitTarihi', [$from_date, $to_date]);
        }

        // İl filtresi
        if ($request->has('il') && !empty($request->il)) {
            $query->where('il.id', $request->il);
        }

        // Arama filtresi
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                 $q->where('il.name', 'LIKE', '%' . $searchValue . '%')
                  ->orWhere('i.ilceName', 'LIKE', '%' . $searchValue . '%');
            });
        }

        // Gruplama
        $query->groupBy('il.name', 'i.ilceName');


        // Sıralama
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];

            $columns = ['ilceName', 'toplam'];

            if (isset($columns[$orderColumn])) {
                if ($columns[$orderColumn] == 'ilceName') {
                    $query->orderBy('i.ilceName', $orderDirection);
                } else {
                    $query->orderBy('toplam', $orderDirection);
                }
            }
        } else {
            $query->orderByDesc('toplam');
        }

        // Toplam kayıt sayısı
        $totalQuery = DB::table('services as s')
            ->join('customers as c', 's.musteri_id', '=', 'c.id')
            ->join('ilces as i', 'c.ilce', '=', 'i.id')
            ->where('s.durum', 1);

        if ($request->has('from_date') && $request->has('to_date')) {
            $from_date = Carbon::createFromFormat('Y-m-d', $request->from_date)->startOfDay();
            $to_date = Carbon::createFromFormat('Y-m-d', $request->to_date)->endOfDay();
            $totalQuery->whereBetween('s.kayitTarihi', [$from_date, $to_date]);
        }

        if ($request->has('il') && !empty($request->il)) {
            $totalQuery->where('c.il', $request->il);
        }

        $totalRecords = $totalQuery->select(DB::raw('COUNT(DISTINCT CONCAT(c.il, "-", i.ilceName)) as total'))
            ->first()->total;

        $filteredRecords = $query->get()->count();

        // Sayfalama
        if ($request->has('start') && $request->has('length')) {
            $query->offset($request->start)->limit($request->length);
        }

        $data = $query->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    // Normal sayfa yüklemesi için il listesini al
    $iller = DB::table('ils')->select('id', 'name')->orderBy('name')->get();
    return view('frontend.secure.statistics.ilce_statistics', compact('tenant_id', 'iller'));
}

///////////////////////////////////////////////////////Survey Statistics///////////////////////////////////////////////////////////////////
public function SurveyStatistics($tenant_id)
{
        return view('frontend.secure.statistics.survey_statistics', compact('tenant_id'));
}
public function getSurveyStatisticsData(Request $request, $tenant_id)
{
    $fromDate = $request->from_date ?: Carbon::yesterday()->format('Y-m-d');
    $toDate = $request->to_date ?: Carbon::yesterday()->format('Y-m-d');
    $deviceTypeId = $request->device_type_id;

    // Tarih aralığı için başlangıç ve bitiş zamanları
    $startDateTime = $fromDate . ' 00:00:00';
    $endDateTime = $toDate . ' 23:59:59';

    // 1. Önce tamamlanan servisleri personel bazında getir (service_plannings tablosundan)
    $completedServicesQuery = ServicePlanning::select(
        'service_plannings.kid as personel_id',
        'tb_user.name as adsoyad',
        'service_plannings.servisid'
    )
    ->leftJoin('tb_user', 'service_plannings.kid', '=', 'tb_user.user_id')
    ->where('service_plannings.gidenIslem', 255) // Tamamlanan servisler
    ->whereBetween('service_plannings.created_at', [$startDateTime, $endDateTime])
    ->where('service_plannings.firma_id', $tenant_id);

    // Cihaz türü filtresi varsa ekle
    if ($deviceTypeId) {
        $completedServicesQuery->leftJoin('services', 'service_plannings.servisid', '=', 'services.id')
                              ->where('services.cihazTur', $deviceTypeId);
    }

    $completedServices = $completedServicesQuery->get();

    // 2. Yapılan anketleri personel bazında getir
    $surveysQuery = Survey::select(
        'surveys.ekleyen as personel_id', // surveys tablosunda ekleyen personel alanı kullanılıyor
        'surveys.servisid'
    )
    ->whereBetween('surveys.created_at', [$startDateTime, $endDateTime])
    ->where('surveys.firma_id', $tenant_id);

    // Cihaz türü filtresi varsa ekle
    if ($deviceTypeId) {
        $surveysQuery->leftJoin('services', 'surveys.servisid', '=', 'services.id')
                    ->where('services.cihazTur', $deviceTypeId);
    }

    $surveys = $surveysQuery->get();

    // 3. Personel bazında grupla
    $groupedStats = [];

    // Önce tamamlanan servisleri grupla
    foreach ($completedServices as $service) {
        if (!isset($groupedStats[$service->personel_id])) {
            $groupedStats[$service->personel_id] = [
                'personel_id' => $service->personel_id,
                'adsoyad' => $service->adsoyad,
                'tamamlanan_servisler' => [],
                'anket_yapilan_servisler' => []
            ];
        }
        // Aynı servisi birden fazla kez eklememek için kontrol et
        if (!in_array($service->servisid, $groupedStats[$service->personel_id]['tamamlanan_servisler'])) {
            $groupedStats[$service->personel_id]['tamamlanan_servisler'][] = $service->servisid;
        }
    }

    // Sonra anket yapılan servisleri ekle
    foreach ($surveys as $survey) {
        // Eğer bu personel daha önce eklenmemişse (tamamlanan servisi yoksa), ekle
        if (!isset($groupedStats[$survey->personel_id])) {
            // Personel adını almak için ayrı sorgu
            $user = User::find($survey->personel_id);
            $groupedStats[$survey->personel_id] = [
                'personel_id' => $survey->personel_id,
                'adsoyad' => $user ? $user->name : 'Bilinmeyen Personel',
                'tamamlanan_servisler' => [],
                'anket_yapilan_servisler' => []
            ];
        }
        
        // Aynı servisi birden fazla kez eklememek için kontrol et
        if (!in_array($survey->servisid, $groupedStats[$survey->personel_id]['anket_yapilan_servisler'])) {
            $groupedStats[$survey->personel_id]['anket_yapilan_servisler'][] = $survey->servisid;
        }
    }

    // 4. Sayıları hesapla ve final formatı oluştur
    $finalStats = [];
    foreach ($groupedStats as $personelId => $stat) {
        $finalStats[$personelId] = [
            'personel_id' => $stat['personel_id'],
            'adsoyad' => $stat['adsoyad'],
            'tamamlanan_servis_sayisi' => count($stat['tamamlanan_servisler']),
            'anket_yapilan_servis_sayisi' => count($stat['anket_yapilan_servisler']),
            'servisler' => $stat['tamamlanan_servisler'] // Detay butonu için kullanılabilir
        ];
    }

    // 5. Toplam sayıları hesapla
    $totalCompletedServices = 0;
    $totalSurveyedServices = 0;
    
    foreach ($finalStats as $stat) {
        $totalCompletedServices += $stat['tamamlanan_servis_sayisi'];
        $totalSurveyedServices += $stat['anket_yapilan_servis_sayisi'];
    }

    // Cihaz türleri listesi
    $deviceTypes = DeviceType::where('firma_id', $tenant_id)
                            ->orderBy('cihaz', 'ASC')
                            ->get();

    return response()->json([
        'personnelStats' => $finalStats,
        'totalCompletedServices' => $totalCompletedServices,
        'totalSurveyedServices' => $totalSurveyedServices,
        'deviceTypes' => $deviceTypes
    ]);
}

public function getSurveyResults(Request $request, $tenant_id) 
{
    $fromDate = $request->from_date ?: Carbon::yesterday()->format('Y-m-d');
    $toDate = $request->to_date ?: Carbon::yesterday()->format('Y-m-d');
    $deviceTypeId = $request->device_type_id;
    $bayiId = $request->bayi_id;

    // Tarih aralığı için başlangıç ve bitiş zamanları
    $startDateTime = $fromDate . ' 00:00:00';
    $endDateTime = $toDate . ' 23:59:59';

    // Anket sonuçlarını getir
    $surveysQuery = Survey::select(
        'surveys.*',
        'tb_user.name as personel_adi'
    )
    ->leftJoin('tb_user', 'surveys.personel', '=', 'tb_user.user_id')
    ->whereBetween('surveys.created_at', [$startDateTime, $endDateTime])
    ->where('surveys.firma_id', $tenant_id);

    // Bayi filtresi
    if ($bayiId) {
        $surveysQuery->where('surveys.bayi', $bayiId);
    }

    // Cihaz türü filtresi
    if ($deviceTypeId) {
        $surveysQuery->leftJoin('services', 'surveys.servisid', '=', 'services.id')
                    ->where('services.cihazTur', $deviceTypeId);
    }

    $surveys = $surveysQuery->get();

    // Soru istatistiklerini hesapla
    $questionStats = [
        'soru1' => ['evet' => 0, 'hayir' => 0, 'belli_degil' => 0],
        'soru2' => ['evet' => 0, 'hayir' => 0, 'belli_degil' => 0],
        'soru3' => ['evet' => 0, 'hayir' => 0, 'belli_degil' => 0],
        'soru5' => ['evet' => 0, 'hayir' => 0, 'belli_degil' => 0]
    ];

    foreach ($surveys as $survey) {
        // Soru 1 istatistikleri
        if ($survey->soru1 == 1) $questionStats['soru1']['evet']++;
        elseif ($survey->soru1 == 2) $questionStats['soru1']['hayir']++;
        elseif ($survey->soru1 == 0) $questionStats['soru1']['belli_degil']++;

        // Soru 2 istatistikleri
        if ($survey->soru2 == 1) $questionStats['soru2']['evet']++;
        elseif ($survey->soru2 == 2) $questionStats['soru2']['hayir']++;
        elseif ($survey->soru2 == 0) $questionStats['soru2']['belli_degil']++;

        // Soru 3 istatistikleri
        if ($survey->soru3 == 1) $questionStats['soru3']['evet']++;
        elseif ($survey->soru3 == 2) $questionStats['soru3']['hayir']++;
        elseif ($survey->soru3 == 0) $questionStats['soru3']['belli_degil']++;

        // Soru 5 istatistikleri
        if ($survey->soru5 == 1) $questionStats['soru5']['evet']++;
        elseif ($survey->soru5 == 2) $questionStats['soru5']['hayir']++;
        elseif ($survey->soru5 == 0) $questionStats['soru5']['belli_degil']++;
    }
    // Toplam anket sayısı
    $totalSurveys = $surveys->count();
    // Yüzdelik hesaplamaları
    $questionPercentages = [];
    foreach ($questionStats as $questionKey => $stats) {
        $total = array_sum($stats); // Tüm cevapların toplamı (evet + hayir + belli_degil)

        $questionPercentages[$questionKey] = [
            'evet_percentage'       => $total > 0 ? round(($stats['evet'] / $total) * 100, 1) : 0,
            'hayir_percentage'      => $total > 0 ? round(($stats['hayir'] / $total) * 100, 1) : 0,
            'belli_degil_percentage' => $total > 0 ? round(($stats['belli_degil'] / $total) * 100, 1) : 0,
        ];
    }
    // Bayiler listesi
    $bayiRole = Role::where('name', 'Bayi')->first();
    $bayiRoleId = $bayiRole ? $bayiRole->id : null;
    $bayiler = User::where('tenant_id', $tenant_id)
        ->whereHas('roles', function ($query) use ($bayiRoleId) {
            $query->where('id', $bayiRoleId);
        })
        ->get();

    // Cihaz türleri
    $deviceTypes = DeviceType::where('firma_id', $tenant_id)
                            ->orderBy('cihaz', 'ASC')
                            ->get();

    return response()->json([
        'questionStats' => $questionStats,
        'questionPercentages' => $questionPercentages,
        'totalSurveys' => $totalSurveys,
        'bayiler' => $bayiler,
        'deviceTypes' => $deviceTypes,
        'surveys' => $surveys->toArray()
    ]);
}
}

