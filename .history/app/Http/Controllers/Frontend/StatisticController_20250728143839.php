<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Survey;
use App\Models\ServiceStageAnswer;
use App\Models\ServiceResource;
use App\Models\ServiceStages;
use App\Models\Service;
use App\Models\DeviceBrand;
use App\Models\DeviceType;
use App\Models\ServicePlanning;
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
   public function getTechnicianStatisticsData(Request $request)
    {
        $tenant_id = $request->route('tenant_id'); // Route'dan tenant_id'yi alıyoruz

        $tarih1Str = $request->input('tarih1'); // 'dd/mm/yyyy' formatında gelir
        $tarih2Str = $request->input('tarih2'); // 'dd/mm/yyyy' formatında gelir
        $cihazTurId = $request->input('cihazTur');

        // Tarihleri Carbon nesnesine çevir (YYYY-MM-DD formatına)
        $tarih1 = Carbon::createFromFormat('d/m/Y', $tarih1Str)->startOfDay();
        $tarih2 = Carbon::createFromFormat('d/m/Y', $tarih2Str)->endOfDay();

        // Personel listesini al ( durum=1, grup=244 olan teknisyenler)
        $personeller = User::where('tenant_id', $tenant_id)
                           ->where('status', 1)
                           ->whereHas('roles', function ($query) { // Spatie roles kullanıyorsanız
                               $query->where('name', 'Teknisyen'); // Veya role ID'sini doğrudan kullanın: $query->where('id', 244);
                           })
                           // ->where('grup', 244) // Eğer grup sütunu User modelinizde doğrudan varsa
                           ->orderBy('name', 'ASC')
                           ->get();

        $personelAllIds = $personeller->pluck('user_id')->toArray();

        // **getDatesFromRange fonksiyonu Laravel'de Carbon ile daha kolay yapılır.**
        $dateRange = collect();
        $currentDate = $tarih1->copy();
        while ($currentDate->lte($tarih2)) {
            $dateRange->push($currentDate->format('Y-m-d'));
            $currentDate->addDay();
        }

        // Seçilen tarih aralığındaki servis planlarını ve ilgili servisleri bulma
        $secilenTarihlerdekiPlanidler = ServiceStageAnswer::query()
            ->where('firma_id', $tenant_id)
            ->whereIn('soruid', [296, 326, 369, 306, 337, 341]) // 'Tarih' tipindeki soruların ID'leri
            ->whereBetween(
                \DB::raw("STR_TO_DATE(cevap, '%Y-%m-%d')"), // Veritabanındaki tarih formatına dikkat edin
                [$tarih1->toDateString(), $tarih2->toDateString()]
            )
            ->when($cihazTurId, function ($query, $cihazTurId) {
                // join ile cihazTur bilgisini almalıyız
                $query->join('services', 'service_stage_answers.servisid', '=', 'services.id')
                      ->where('services.cihazTur', $cihazTurId);
            })
            ->pluck('planid')
            ->unique()
            ->toArray();


        if (empty($secilenTarihlerdekiPlanidler)) {
             // Hiç planid bulunamazsa boş bir tablo döndür
            return view('frontend.secure.statistics.technician_statistics_table', [
                'groupAll' => collect(), // Boş koleksiyon
                'personelNotAll' => [],
                'personeller' => $personeller,
                'tarihler' => [$tarih1Str, $tarih2Str] // Boş veri için de tarihler gönderilmeli
            ]);
        }

        // Planidler içerisinden personelin servislerini buluyoruz
        // `cevap` kolonunda personel ID'si bulunan ve `soruid`'i 'Grup' tipinde olan sorular
        $personelServisleri = ServiceStageAnswer::query()
            ->whereIn('cevap', $personelAllIds)
            ->whereHas('serviceStageQuestion', function ($query) {
                $query->where('cevapTuru', 'like', '%Grup%'); // cevapTuru '[Grup-4], [Grup-5]' gibi
            })
            ->whereIn('planid', $secilenTarihlerdekiPlanidler)
            ->select('cevap', 'servisid')
            ->get();

        $groupAll = $personelServisleri->groupBy('cevap')->map(function ($items) {
            return $items->pluck('servisid')->unique()->implode(', ');
        });

        $personelNotAll = $groupAll->keys()->toArray();

        $results = [];

        foreach ($groupAll as $personelId => $servislerStr) {
            $perSec = $personeller->firstWhere('user_id', $personelId);
            if (!$perSec) continue; // Personel bulunamazsa atla

            $servislerArray = explode(', ', $servislerStr);
            $servisSay = count($servislerArray);

            $tamamlanan = Service::whereIn('id', $servislerArray)
                                 ->where('servisDurum', 255)
                                 ->count();

            // Şikayetçi Servis
            $sikayetciSay = ServicePlanning::whereIn('servisid', $servislerArray)
                                           ->where('gidenIslem', 254) // Şikayetçi durumu
                                           ->where('pid', $personelId) // Personelin kendisinin işlemi
                                           ->whereRaw('STR_TO_DATE(tarih, "%Y-%m-%d") >= (SELECT MIN(STR_TO_DATE(tarih, "%Y-%m-%d")) FROM service_planings WHERE servisid = service_planings.servisid AND pid = ?)', [$personelId])
                                           ->groupBy('servisid')
                                           ->get()
                                           ->filter(function ($plan) use ($personelId) {
                                               // İlk servis kaydını bul
                                               $ilkServis = ServicePlanning::where('servisid', $plan->servisid)
                                                                           ->where('pid', $personelId)
                                                                           ->orderBy('tarih')
                                                                           ->first();
                                               // Son şikayet kaydını bul
                                               $sonSikayet = ServicePlanning::where('servisid', $plan->servisid)
                                                                            ->where('gidenIslem', 254)
                                                                            ->orderBy('tarih', 'DESC')
                                                                            ->first();

                                               if ($ilkServis && $sonSikayet) {
                                                   return Carbon::parse($sonSikayet->tarih)->greaterThan(Carbon::parse($ilkServis->tarih));
                                               }
                                               return false;
                                           })->count();


            // İptal Servisler (eski koddaki mantıkla)
            $iptalSay = ServicePlanning::whereIn('servisid', $servislerArray)
                                        ->where('gidenIslem', 244) // İptal durumu
                                        ->where('pid', $personelId) // Personelin kendisinin işlemi
                                        ->whereRaw('STR_TO_DATE(tarih, "%Y-%m-%d") >= (SELECT MIN(STR_TO_DATE(tarih, "%Y-%m-%d")) FROM service_planings WHERE servisid = service_planings.servisid AND pid = ?)', [$personelId])
                                        ->groupBy('servisid')
                                        ->get()
                                        ->filter(function ($plan) use ($personelId) {
                                            $ilkServis = ServicePlanning::where('servisid', $plan->servisid)
                                                                        ->where('pid', $personelId)
                                                                        ->orderBy('tarih')
                                                                        ->first();
                                            $sonIptal = ServicePlanning::where('servisid', $plan->servisid)
                                                                       ->where('gidenIslem', 244)
                                                                       ->orderBy('tarih', 'DESC')
                                                                       ->first();
                                            if ($ilkServis && $sonIptal) {
                                                return Carbon::parse($sonIptal->tarih)->greaterThan(Carbon::parse($ilkServis->tarih));
                                            }
                                            return false;
                                        })->count();

            // Haber Verecek Servisler
            $haberSay = ServicePlanning::whereIn('servisid', $servislerArray)
                                       ->where('gidenIslem', 247) // Haber Verecek durumu
                                       ->where('pid', $personelId)
                                       ->whereRaw('STR_TO_DATE(tarih, "%Y-%m-%d") >= (SELECT MIN(STR_TO_DATE(tarih, "%Y-%m-%d")) FROM service_planings WHERE servisid = service_planings.servisid AND pid = ?)', [$personelId])
                                       ->groupBy('servisid')
                                       ->get()
                                       ->filter(function ($plan) use ($personelId) {
                                           $ilkServis = ServicePlanning::where('servisid', $plan->servisid)
                                                                       ->where('pid', $personelId)
                                                                       ->orderBy('tarih')
                                                                       ->first();
                                           $sonServis = ServicePlanning::where('servisid', $plan->servisid)
                                                                       ->where('gidenIslem', 247)
                                                                       ->orderBy('tarih', 'DESC')
                                                                       ->first();
                                           if ($ilkServis && $sonServis) {
                                               return Carbon::parse($sonServis->tarih)->greaterThanOrEqualTo(Carbon::parse($ilkServis->tarih)->subDay()); // -1 mantığını +0 yapmak için
                                           }
                                           return false;
                                       })->count();

            // Fiyatta Anlaşılamadı Servisler
            $fiyatSay = ServicePlanning::whereIn('servisid', $servislerArray)
                                       ->where('gidenIslem', 241) // Fiyatta Anlaşılamadı durumu
                                       ->where('pid', $personelId)
                                       ->whereRaw('STR_TO_DATE(tarih, "%Y-%m-%d") >= (SELECT MIN(STR_TO_DATE(tarih, "%Y-%m-%d")) FROM service_planings WHERE servisid = service_planings.servisid AND pid = ?)', [$personelId])
                                       ->groupBy('servisid')
                                       ->get()
                                       ->filter(function ($plan) use ($personelId) {
                                           $ilkServis = ServicePlanning::where('servisid', $plan->servisid)
                                                                       ->where('pid', $personelId)
                                                                       ->orderBy('tarih')
                                                                       ->first();
                                           $sonServis = ServicePlanning::where('servisid', $plan->servisid)
                                                                       ->where('gidenIslem', 241)
                                                                       ->orderBy('tarih', 'DESC')
                                                                       ->first();
                                           if ($ilkServis && $sonServis) {
                                               return Carbon::parse($sonServis->tarih)->greaterThanOrEqualTo(Carbon::parse($ilkServis->tarih)->subDay()); // -1 mantığını +0 yapmak için
                                           }
                                           return false;
                                       })->count();


            // Alınan Ücret
            $paraToplam = CashMovement::whereIn('servis', $servislerArray)
                                      ->where('odemeYonu', 1)
                                      ->where('personel', $personelId)
                                      ->whereBetween('islemTarihi', [$tarih1, $tarih2])
                                      ->sum('fiyat');

            // Verilen Teklif
            $teklifToplam = ServiceStageAnswer::whereIn('servisid', $servislerArray)
                                              ->where('soruid', 38) // Eski kodda 350-356 arası idi, sizin tablo örneğinizde 'Teklif' sorusunun id'si 38. Bunu doğru ID'lerle güncelleyin.
                                              ->whereHas('servicePlanning', function ($query) use ($personelId) {
                                                  $query->where('pid', $personelId);
                                              })
                                              ->whereBetween('created_at', [$tarih1, $tarih2]) // Teklifin oluşturulma tarihi filtreye göre alınmalı
                                              ->sum('cevap'); // Cevap kolonunda teklif tutarı var

            $results[] = [
                'personel' => $perSec,
                'servisSay' => $servisSay,
                'tamamlanan' => $tamamlanan,
                'sikayetciSay' => $sikayetciSay,
                'iptalSay' => $iptalSay,
                'haberSay' => $haberSay,
                'fiyatSay' => $fiyatSay,
                'paraToplam' => $paraToplam,
                'teklifToplam' => $teklifToplam,
            ];
        }

        // Servisi olmayan personelleri ekle
        $personelNotAllUsers = $personeller->whereNotIn('user_id', $personelNotAll);

        return view('frontend.secure.statistics.technician_statistics_table', compact(
            'results',
            'personelNotAllUsers',
            'personelAllIds'
        ));
    }


///////////////////////////////////////////////////////Survey Statistics///////////////////////////////////////////////////////////////////
    public function SurveyStatistics($tenant_id)
    {
        return view('frontend.secure.statistics.survey_statistics', compact('tenant_id'));
    }

   
}

