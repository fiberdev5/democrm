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
        try {
            // Debug: Request parametrelerini kontrol et
            \Log::info('Request parametreleri:', [
                'tarihAraligi' => $request->tarihAraligi,
                'cihazTur' => $request->cihazTur,
                'tenant_id' => $tenant_id
            ]);

            // Teknisyenleri al
            $teknisyenler = User::where('tenant_id', $tenant_id)
                                ->where('status', 1)
                                ->whereNull('ayrilmaTarihi')
                                ->whereHas('roles', function($query) {
                                    $query->where('name', 'Teknisyen');
                                })
                                ->orderBy('name', 'ASC')
                                ->get();

            \Log::info('Bulunan teknisyen sayısı: ' . $teknisyenler->count());

            // Tarih aralığını parse et
            $tarihler = explode('---', $request->tarihAraligi);
            $tarih1 = Carbon::createFromFormat('d/m/Y', $tarihler[0])->format('Y-m-d');
            $tarih2 = Carbon::createFromFormat('d/m/Y', $tarihler[1])->format('Y-m-d');

            \Log::info('Tarih aralığı:', ['tarih1' => $tarih1, 'tarih2' => $tarih2]);

            // Tarih aralığındaki tüm tarihleri al
            $tarihListesi = $this->getDatesFromRange($tarih1, $tarih2);
            \Log::info('Tarih listesi:', $tarihListesi);

            // Seçilen tarihlerdeki servis planlarını al
            $servisPlanlar = $this->getServicePlansForDates($tarihListesi, $tenant_id, $request->cihazTur);
            \Log::info('Bulunan servis planları (ID\'ler):', $servisPlanlar);

            // Her teknisyen için istatistikleri hesapla
            $teknisyenIstatistikleri = [];

            foreach ($teknisyenler as $teknisyen) {
                $teknisyenServisleri = $this->getTechnicianServices($teknisyen->user_id, $servisPlanlar);
                \Log::info('Teknisyen: ' . $teknisyen->name . ' - Servis sayısı: ' . count($teknisyenServisleri));

                if (!empty($teknisyenServisleri)) {
                    $istatistik = $this->calculateTechnicianStats($teknisyen, $teknisyenServisleri, $tarih1, $tarih2);
                    $teknisyenIstatistikleri[] = $istatistik;
                }
            }

            // Hiç servisi olmayan teknisyenleri de ekle
            $mevcutTeknisyenIds = collect($teknisyenIstatistikleri)->pluck('id')->toArray();
            $digerTeknisyenler = $teknisyenler->whereNotIn('user_id', $mevcutTeknisyenIds);

            foreach ($digerTeknisyenler as $teknisyen) {
                $teknisyenIstatistikleri[] = [
                    'id' => $teknisyen->user_id,
                    'name' => $teknisyen->name,
                    'atanan_servis' => 0,
                    'tamamlanan_servis' => 0,
                    'sikayetci_servis' => 0,
                    'iptal_servis' => 0,
                    'haber_verecek' => 0,
                    'fiyat_anlasma' => 0,
                    'alinan_ucret' => 0,
                    'verilen_teklif' => 0
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $teknisyenIstatistikleri
            ]);

        } catch (\Exception $e) {
            \Log::error('Teknisyen istatistikleri hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage()
            ]);
        }
    }

    private function getDatesFromRange($start, $end)
    {
        $dates = [];
        $current = Carbon::parse($start);
        $endDate = Carbon::parse($end);

        while ($current <= $endDate) {
            $dates[] = $current->format('d/m/Y');
            $current->addDay();
        }

        return $dates;
    }

    private function getServicePlansForDates($tarihListesi, $tenant_id, $cihazTur = null)
    {
        $query = ServiceStageAnswer::query()
                    ->join('stage_questions as ss', 'ss.id', '=', 'service_stage_answers.soruid')
                    ->join('services', 'services.id', '=', 'service_stage_answers.servisid')
                    ->where('ss.cevapTuru', 'LIKE', '%Tarih%')
                    ->where('services.firma_id', $tenant_id);

        // Tarih formatını kontrol et - hem d/m/Y hem de Y-m-d formatlarını dene
        $tarihListesiYmd = array_map(function($tarih) {
            return Carbon::createFromFormat('d/m/Y', $tarih)->format('Y-m-d');
        }, $tarihListesi);

        $query->where(function($q) use ($tarihListesi, $tarihListesiYmd) {
            $q->whereIn('service_stage_answers.cevap', $tarihListesi)
              ->orWhereIn('service_stage_answers.cevap', $tarihListesiYmd);
        });

        if ($cihazTur) {
            $query->where('services.cihazTur', $cihazTur);
        }

        $seciliTarihler = $query->whereIn('service_stage_answers.soruid', [296, 326, 369, 306, 337, 341])
                                ->pluck('service_stage_answers.planid')
                                ->unique() // Tekrar eden plan ID'lerini önle
                                ->toArray();

        \Log::info('Servis plan sorgusu sonucu:', [
            'tarih_listesi' => $tarihListesi,
            'tarih_listesi_ymd' => $tarihListesiYmd,
            'bulunan_planlar' => count($seciliTarihler),
            'plan_ids' => $seciliTarihler
        ]);

        return $seciliTarihler;
    }


    private function getTechnicianServices($teknisyenId, $servisPlanlar)
    {
        if (empty($servisPlanlar)) {
            return [];
        }

        // ServiceStageAnswer modelini kullanarak teknisyen atamasını kontrol et
        $servisler1 = ServiceStageAnswer::where('cevap', $teknisyenId)
            ->whereHas('question', function ($query) { // İlişki varsayımı: ServiceStageAnswer modelinde 'question' ilişkisi StageQuestion'a
                $query->where('cevapTuru', 'LIKE', '%Grup%');
            })
            ->whereIn('planid', $servisPlanlar)
            ->pluck('servisid')
            ->unique()
            ->toArray();

        // ServicePlanning modelini kullanarak kontrol et
        $servisler2 = ServicePlanning::where('pid', $teknisyenId)
            ->whereIn('id', $servisPlanlar)
            ->pluck('servisid')
            ->unique()
            ->toArray();

        // İki sonucu birleştir
        $servisler = array_unique(array_merge($servisler1, $servisler2));

        \Log::info('Teknisyen servisleri:', [
            'teknisyen_id' => $teknisyenId,
            'stage_answers_servisleri' => count($servisler1),
            'plannings_servisleri' => count($servisler2),
            'toplam_servisler' => count($servisler)
        ]);

        return $servisler;
    }


    private function calculateTechnicianStats($teknisyen, $servisler, $tarih1, $tarih2)
    {
        // Tamamlanan servisler
        $tamamlanan = Service::whereIn('id', $servisler)
                              ->where('servisDurum', 255) // 255 = Tamamlandı
                              ->count();

        // Şikayetçi servisler
        $sikayetciSay = $this->calculateComplaintServices($teknisyen->user_id, $servisler);

        // İptal servisler
        $iptalSay = $this->calculateCancelledServices($teknisyen->user_id, $servisler);

        // Haber verecek servisler
        $haberSay = $this->calculateNotificationServices($teknisyen->user_id, $servisler);

        // Fiyat anlaşma servisler
        $fiyatSay = $this->calculatePriceDisagreementServices($teknisyen->user_id, $servisler);

        // Alınan ücret (kasa hareketlerinden)
        $alinanUcret = $this->calculateCollectedAmount($teknisyen->user_id, $servisler, $tarih1, $tarih2);

        // Verilen teklifler
        $verilenTeklif = $this->calculateOfferedAmount($teknisyen->user_id, $servisler, $tarih1, $tarih2);

        return [
            'id' => $teknisyen->user_id,
            'name' => $teknisyen->name,
            'atanan_servis' => count($servisler),
            'tamamlanan_servis' => $tamamlanan,
            'sikayetci_servis' => $sikayetciSay,
            'iptal_servis' => $iptalSay,
            'haber_verecek' => $haberSay,
            'fiyat_anlasma' => $fiyatSay,
            'alinan_ucret' => $alinanUcret,
            'verilen_teklif' => $verilenTeklif
        ];
    }

    private function calculateComplaintServices($teknisyenId, $servisler)
    {
        $sikayetciSay = 0;

        $sikayetciler = ServicePlanning::whereIn('servisid', $servisler)
                                       ->where('gidenIslem', 254) // 254 = Şikayetçi
                                       ->groupBy('servisid')
                                       ->get();

        foreach ($sikayetciler as $servis) {
            $ilkServis = ServicePlanning::where('servisid', $servis->servisid)
                                        ->where('pid', $teknisyenId) // pid = personel id
                                        ->orderBy('tarih', 'ASC')
                                        ->first();

            $sonSikayet = ServicePlanning::where('servisid', $servis->servisid)
                                         ->where('gidenIslem', 254)
                                         ->orderBy('tarih', 'DESC')
                                         ->first();

            if ($ilkServis && $sonSikayet) {
                $ilkTarih = Carbon::parse(explode(" ", $ilkServis->tarih)[0]);
                $sikayetTarih = Carbon::parse(explode(" ", $sonSikayet->tarih)[0]);

                if ($sikayetTarih->greaterThan($ilkTarih)) {
                    $sikayetciSay++;
                }
            }
        }

        return $sikayetciSay;
    }

    private function calculateCancelledServices($teknisyenId, $servisler)
    {
        $iptalSay = 0;

        $iptaller = ServicePlanning::whereIn('servisid', $servisler)
                                   ->where('gidenIslem', 244) // 244 = İptal
                                   ->groupBy('servisid')
                                   ->get();

        foreach ($iptaller as $servis) {
            $ilkServis = ServicePlanning::where('servisid', $servis->servisid)
                                        ->where('pid', $teknisyenId)
                                        ->orderBy('tarih', 'ASC')
                                        ->first();

            $sonIptal = ServicePlanning::where('servisid', $servis->servisid)
                                       ->where('gidenIslem', 244)
                                       ->orderBy('tarih', 'DESC')
                                       ->first();

            if ($ilkServis && $sonIptal) {
                $ilkTarih = Carbon::parse(explode(" ", $ilkServis->tarih)[0]);
                $iptalTarih = Carbon::parse(explode(" ", $sonIptal->tarih)[0]);

                if ($iptalTarih->greaterThan($ilkTarih)) {
                    $iptalSay++;
                }
            }
        }

        return $iptalSay;
    }

    private function calculateNotificationServices($teknisyenId, $servisler)
    {
        $haberSay = 0;

        $haberciler = ServicePlanning::whereIn('servisid', $servisler)
                                     ->where('gidenIslem', 247) // 247 = Haber verecek
                                     ->groupBy('servisid')
                                     ->get();

        foreach ($haberciler as $haber) {
            $ilkServis = ServicePlanning::where('servisid', $haber->servisid)
                                        ->where('pid', $teknisyenId)
                                        ->orderBy('tarih', 'ASC')
                                        ->first();

            $sonHaber = ServicePlanning::where('servisid', $haber->servisid)
                                       ->where('gidenIslem', 247)
                                       ->orderBy('tarih', 'DESC')
                                       ->first();

            if ($ilkServis && $sonHaber) {
                $ilkTarih = Carbon::parse(explode(" ", $ilkServis->tarih)[0]);
                $haberTarih = Carbon::parse(explode(" ", $sonHaber->tarih)[0]);

                // Buradaki mantıkta bir değişiklik yapılmış gibi duruyor, 'greaterThanOrEqualTo' kullanıldı.
                if ($haberTarih->greaterThanOrEqualTo($ilkTarih)) {
                    $haberSay++;
                }
            }
        }

        return $haberSay;
    }

    private function calculatePriceDisagreementServices($teknisyenId, $servisler)
    {
        $fiyatSay = 0;

        $fiyatlar = ServicePlanning::whereIn('servisid', $servisler)
                                   ->where('gidenIslem', 241) // 241 = Fiyatta anlaşılamadı
                                   ->groupBy('servisid')
                                   ->get();

        foreach ($fiyatlar as $fiyat) {
            $ilkServis = ServicePlanning::where('servisid', $fiyat->servisid)
                                        ->where('pid', $teknisyenId)
                                        ->orderBy('tarih', 'ASC')
                                        ->first();

            $sonFiyat = ServicePlanning::where('servisid', $fiyat->servisid)
                                       ->where('gidenIslem', 241)
                                       ->orderBy('tarih', 'DESC')
                                       ->first();

            if ($ilkServis && $sonFiyat) {
                $ilkTarih = Carbon::parse(explode(" ", $ilkServis->tarih)[0]);
                $fiyatTarih = Carbon::parse(explode(" ", $sonFiyat->tarih)[0]);

                // Buradaki mantıkta bir değişiklik yapılmış gibi duruyor, 'greaterThanOrEqualTo' kullanıldı.
                if ($fiyatTarih->greaterThanOrEqualTo($ilkTarih)) {
                    $fiyatSay++;
                }
            }
        }

        return $fiyatSay;
    }

    private function calculateCollectedAmount($teknisyenId, $servisler, $tarih1, $tarih2)
    {
        if (empty($servisler)) {
            return 0;
        }

        // Doğrudan CashTransaction modelini kullanın
        $paraToplam = CashTransaction::whereIn('servis', $servisler)
                                     ->where('odemeYonu', '1')
                                     ->where('personel', $teknisyenId)
                                     ->whereBetween('created_at', [$tarih1 . ' 00:00:00', $tarih2 . ' 23:59:59'])
                                     ->sum('fiyat');

        \Log::info('Kasa hareketleri bulundu:', [
            'tablo' => 'cash_transactions', // Model adı
            'teknisyen' => $teknisyenId,
            'toplam' => $paraToplam
        ]);

        return $paraToplam ?: 0;
    }

    private function calculateOfferedAmount($teknisyenId, $servisler, $tarih1, $tarih2)
    {
        if (empty($servisler)) {
            return 0;
        }

        $teklifToplam = ServiceStageAnswer::join('service_plannings as sp', 'sp.id', '=', 'service_stage_answers.planid')
            ->whereIn('service_stage_answers.soruid', [350, 351, 352, 353, 354, 355, 356])
            ->whereIn('service_stage_answers.servisid', $servisler)
            ->where('sp.pid', $teknisyenId)
            ->whereBetween('sp.created_at', [$tarih1 . ' 00:00:00', $tarih2 . ' 23:59:59'])
            ->where('service_stage_answers.cevap', '!=', '')
            ->where('service_stage_answers.cevap', '!=', '0')
            ->sum(DB::raw('CAST(service_stage_answers.cevap AS DECIMAL(10,2))'));

        \Log::info('Teklif tutarları bulundu:', [
            'teknisyen' => $teknisyenId,
            'toplam' => $teklifToplam
        ]);

        return $teklifToplam ?: 0;
    }

    public function debugTableStructure()
    {
        $tablolar = [
            'service_stage_answers',
            'stage_questions',
            'services',
            'service_plannings',
            'cash_transactions'
        ];

        foreach ($tablolar as $tablo) {
            if (Schema::hasTable($tablo)) {
                $columns = Schema::getColumnListing($tablo);
                \Log::info("Tablo: $tablo", ['columns' => $columns]);
            } else {
                \Log::info("Tablo bulunamadı: $tablo");
            }
        }
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
public function DepotStatistics($tenant_id)
{
    return view('frontend.secure.statistics.stock_statistics', compact('tenant_id'));
}
 public function getPersonelDepoData(Request $request, $tenant_id)
    {
        if ($request->ajax()) {
            $from_date = Carbon::parse($request->input('from_date'))->startOfDay();
            $to_date = Carbon::parse($request->input('to_date'))->endOfDay();

            // Sadece 'Teknisyen' ve 'Teknisyen Yardımcısı' rollerine sahip kullanıcıları veya belirli yetki gruplarını filtrele
            // Spatie Roles kullanıyorsanız:
            $usersWithRoles = User::role(['Teknisyen', 'Teknisyen Yardımcısı'])
                                   ->where('tenant_id', $tenant_id)
                                   ->withCount(['personelStocks' => function ($query) use ($from_date, $to_date) {
                                       $query->where('adet', '!=', 0)
                                             ->whereBetween('tarih', [$from_date, $to_date]);
                                   }])
                                   ->having('personel_stocks_count', '>', 0) // Sadece stoğu olan personelleri göster
                                   ->get();

            // Eğer Spatie Roles kullanmıyorsanız ve kullanıcının rolünü/yetkisini belirten bir sütun varsa:
            /*
            $usersWithRoles = User::where('tenant_id', $tenant_id)
                                   ->whereIn('role_id', [2, 3]) // Varsayımsal olarak 'Teknisyen' ve 'Teknisyen Yardımcısı' rol ID'leri
                                   ->withCount(['personelStocks' => function ($query) use ($from_date, $to_date) {
                                       $query->where('adet', '!=', 0)
                                             ->whereBetween('tarih', [$from_date, $to_date]);
                                   }])
                                   ->having('personel_stocks_count', '>', 0)
                                   ->get();
            */

            return Datatables::of($usersWithRoles)
                ->addIndexColumn()
                ->addColumn('personel_name', function($row){
                    return '<strong>' . $row->name . '</strong>';
                })
                ->addColumn('total_stock', function($row){
                    return '<strong>' . $row->personel_stocks_count . '</strong>';
                })
                ->addColumn('action', function($row) use ($tenant_id){
                    // Laravel route ile dinamik link oluşturma
                    $url = url($tenant_id . '/stoklar?depoArama=' . $row->user_id);
                    return '<a href="' . $url . '" target="_blank" class="btn btn-primary btn-sm btn-block" style="font-size:13px;padding:1px">Parçaları Göster</a>';
                })
                ->rawColumns(['personel_name', 'total_stock', 'action']) // HTML içeriğini render etmek için
                ->make(true);
        }
    }

public function getDepotStatisticsData(Request $request, $tenant_id)
{
    $from_date = $request->input('from_date');
    $to_date = $request->input('to_date');

    $query = DB::table('personel_stocks')
        ->join('tb_user', 'personel_stocks.pid', '=', 'tb_user.user_id')
        ->select('tb_user.name', DB::raw('SUM(personel_stocks.adet) as toplam'))
        ->where('personel_stocks.adet', '!=', 0)
        ->where('tb_user.tenant_id', $tenant_id)
        ->whereBetween('personel_stocks.tarih', [$from_date, $to_date])
        ->whereIn('tb_user.role', [2, 3]) // Teknisyen ve Teknisyen Yardımcısı
        ->groupBy('personel_stocks.pid')
        ->get();

    $data = [];
    foreach ($query as $row) {
        $data[] = [
            'personel' => $row->name,
            'toplam' => $row->toplam,
            'action' => '<a href="#" class="btn btn-primary btn-sm">Detay</a>'
        ];
    }

    return response()->json(['data' => $data]);
}
///////////////////////////////////////////////////////Survey Statistics///////////////////////////////////////////////////////////////////
public function SurveyStatistics($tenant_id)
    {
        return view('frontend.secure.statistics.survey_statistics', compact('tenant_id'));
    }

   
}

