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


