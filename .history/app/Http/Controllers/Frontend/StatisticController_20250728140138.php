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
        \Log::info('Bulunan servis planları:', $servisPlanlar);

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


private function getDatesFromRange($start, $end, $format = 'Y-m-d') // <- Varsayılan formatı Y-m-d olarak değiştirin
{
    $dates = [];
    $current = Carbon::parse($start);
    $endDate = Carbon::parse($end);

    while ($current <= $endDate) {
        $dates[] = $current->format($format);
        $current->addDay();
    }

    return $dates;
}
private function getServicePlansForDates($tarihListesi, $tenant_id, $cihazTur = null)
{
    $query = DB::table('service_stage_answers as sc')
        ->leftJoin('stage_questions as ss', 'ss.id', '=', 'sc.soruid')
        ->leftJoin('services', 'services.id', '=', 'sc.servisid')
        ->where('ss.cevapTuru', 'LIKE', '%Tarih%')
        ->where('services.firma_id', $tenant_id);

    

    // Eğer sc.cevap sütunu Y-m-d formatında ise, direkt $tarihListesi'ni kullanın:
    $query->whereIn('sc.cevap', $tarihListesi); // Artık bu liste Y-m-d formatında

    if ($cihazTur) {
        $query->where('services.cihazTur', $cihazTur);
    }

    $seciliTarihler = $query->whereIn('sc.soruid', [296, 326, 369, 306, 337, 341])
                            ->pluck('sc.planid')
                            ->toArray();

    \Log::info('Servis plan sorgusu sonucu:', [
        'tarih_listesi_query' => $tarihListesi, // Artık bu direkt Y-m-d formatı
        'bulunan_planlar' => count($seciliTarihler),
        'plan_ids' => $seciliTarihler
    ]);

    return $seciliTarihler;
}


private function getTechnicianServices($teknisyenId, $servisPlanlar)
{
     \Log::info('Teknisyen ID:', [$teknisyenId]);
    if (empty($servisPlanlar)) {
        return [];
    }

    // Önce service_stage_answers tablosundaki teknisyen atamasını kontrol et
    $servisler1 = DB::table('service_stage_answers as sc')
        ->leftJoin('stage_questions as ss', 'ss.id', '=', 'sc.soruid')
        ->where('sc.cevap', $teknisyenId)
        ->where('ss.cevapTuru', 'LIKE', '%Grup%')
        ->whereIn('sc.planid', $servisPlanlar)
        ->pluck('sc.servisid')
        ->unique()
        ->toArray();

    \Log::info('servisler1 count:', [count($servisler1)]);

    // Alternatif olarak service_plannings tablosundan da kontrol et
    $servisler2 = DB::table('service_plannings')
        ->where('pid', $teknisyenId)
        ->whereIn('id', $servisPlanlar)
        ->pluck('servisid')
        ->unique()
        ->toArray();
\Log::info('servisler2 count:', [count($servisler2)]);
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
    // Tamamlanan servisler - DÜZELTME: Doğru durum kodu
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
     if (empty($servisler)) {
        return 0;
    }
    $sikayetciSay = 0;
    
  
    $sikayetciler = DB::table('service_plannings')
                      ->whereIn('servisid', $servisler)
                      ->where('gidenIslem', 254) // 254 = Şikayetçi
                      ->groupBy('servisid')
                      ->get();

    foreach ($sikayetciler as $servis) {
        $ilkServis = DB::table('service_plannings')
                       ->where('servisid', $servis->servisid)
                       ->where('pid', $teknisyenId) // pid = personel id
                       ->orderBy('tarih', 'ASC')
                       ->first();
                                  
        $sonSikayet = DB::table('service_plannings')
                        ->where('servisid', $servis->servisid)
                        ->where('gidenIslem', 254)
                        ->orderBy('tarih', 'DESC')
                        ->first();

        if ($ilkServis && $sonSikayet) {
            $ilkTarih = strtotime(explode(" ", $ilkServis->tarih)[0]);
            $sikayetTarih = strtotime(explode(" ", $sonSikayet->tarih)[0]);
            
            if ($sikayetTarih > $ilkTarih) {
                $sikayetciSay++;
            }
        }
    }

    return $sikayetciSay;
}

private function calculateCancelledServices($teknisyenId, $servisler)
{
    $iptalSay = 0;
    
    $iptaller = DB::table('service_plannings') // Doğru tablo adı
                  ->whereIn('servisid', $servisler)
                  ->where('gidenIslem', 244) // 244 = İptal
                  ->groupBy('servisid')
                  ->get();

    foreach ($iptaller as $servis) {
        $ilkServis = DB::table('service_plannings')
                       ->where('servisid', $servis->servisid)
                       ->where('pid', $teknisyenId)
                       ->orderBy('tarih', 'ASC')
                       ->first();
                                  
        $sonIptal = DB::table('service_plannings')
                      ->where('servisid', $servis->servisid)
                      ->where('gidenIslem', 244)
                      ->orderBy('tarih', 'DESC')
                      ->first();

        if ($ilkServis && $sonIptal) {
            $ilkTarih = strtotime(explode(" ", $ilkServis->tarih)[0]);
            $iptalTarih = strtotime(explode(" ", $sonIptal->tarih)[0]);
            
            if ($iptalTarih > $ilkTarih) {
                $iptalSay++;
            }
        }
    }

    return $iptalSay;
}

private function calculateNotificationServices($teknisyenId, $servisler)
{
    $haberSay = 0;
    
    $haberciler = DB::table('service_plannings')
                    ->whereIn('servisid', $servisler)
                    ->where('gidenIslem', 247) // 247 = Haber verecek
                    ->groupBy('servisid')
                    ->get();

    foreach ($haberciler as $haber) {
        $ilkServis = DB::table('service_plannings')
                       ->where('servisid', $haber->servisid)
                       ->where('pid', $teknisyenId)
                       ->orderBy('tarih', 'ASC')
                       ->first();
                                  
        $sonHaber = DB::table('service_plannings')
                      ->where('servisid', $haber->servisid)
                      ->where('gidenIslem', 247)
                      ->orderBy('tarih', 'DESC')
                      ->first();

        if ($ilkServis && $sonHaber) {
            $ilkTarih = strtotime(explode(" ", $ilkServis->tarih)[0]);
            $haberTarih = strtotime(explode(" ", $sonHaber->tarih)[0]);
            
            if ($haberTarih > ($ilkTarih - 1)) {
                $haberSay++;
            }
        }
    }

    return $haberSay;
}
private function calculatePriceDisagreementServices($teknisyenId, $servisler)
{
    $fiyatSay = 0;
    
    $fiyatlar = DB::table('service_plannings')
                  ->whereIn('servisid', $servisler)
                  ->where('gidenIslem', 241) // 241 = Fiyatta anlaşılamadı
                  ->groupBy('servisid')
                  ->get();

    foreach ($fiyatlar as $fiyat) {
        $ilkServis = DB::table('service_plannings')
                       ->where('servisid', $fiyat->servisid)
                       ->where('pid', $teknisyenId)
                       ->orderBy('tarih', 'ASC')
                       ->first();
                                  
        $sonFiyat = DB::table('service_plannings')
                      ->where('servisid', $fiyat->servisid)
                      ->where('gidenIslem', 241)
                      ->orderBy('tarih', 'DESC')
                      ->first();

        if ($ilkServis && $sonFiyat) {
            $ilkTarih = strtotime(explode(" ", $ilkServis->tarih)[0]);
            $fiyatTarih = strtotime(explode(" ", $sonFiyat->tarih)[0]);
            
            if ($fiyatTarih > ($ilkTarih - 1)) {
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

    // Farklı tablo adlarını dene
    $tabloAdlari = ['kasa_hareketleri', 'cash_movements', 'kasa_movement', 'treasury_movements'];
    $paraToplam = 0;

    foreach ($tabloAdlari as $tablo) {
        try {
            if (Schema::hasTable($tablo)) {
                $paraToplam = DB::table($tablo)
                    ->whereIn('servis', $servisler)
                    ->where('odemeYonu', '1')
                    ->where('personel', $teknisyenId)
                    ->whereBetween('islemTarihi', [$tarih1 . ' 00:00:00', $tarih2 . ' 23:59:59'])
                    ->sum('fiyat');
                
                \Log::info('Kasa hareketleri bulundu:', [
                    'tablo' => $tablo,
                    'teknisyen' => $teknisyenId,
                    'toplam' => $paraToplam
                ]);
                break;
            }
        } catch (\Exception $e) {
            continue;
        }
    }

    return $paraToplam ?: 0;
}

private function calculateOfferedAmount($teknisyenId, $servisler, $tarih1, $tarih2)
{
    if (empty($servisler)) {
        return 0;
    }

    $tabloAdlari = ['service_plannings', 'service_planning'];
    $teklifToplam = 0;

    foreach ($tabloAdlari as $tablo) {
        try {
            if (Schema::hasTable($tablo)) {
                $teklifToplam = DB::table('service_stage_answers as sc')
                    ->leftJoin($tablo . ' as sp', 'sp.id', '=', 'sc.planid')
                    ->whereIn('sc.soruid', [350, 351, 352, 353, 354, 355, 356])
                    ->whereIn('sc.servisid', $servisler)
                    ->where('sp.pid', $teknisyenId)
                    ->whereBetween('sp.tarih', [$tarih1 . ' 00:00:00', $tarih2 . ' 23:59:59']) // Saat bilgisi ekle
                    ->where('sc.cevap', '!=', '')
                    ->where('sc.cevap', '!=', '0')
                    ->sum(DB::raw('CAST(sc.cevap AS DECIMAL(10,2))'));
                
                \Log::info('Teklif tutarları bulundu:', [
                    'tablo' => $tablo,
                    'teknisyen' => $teknisyenId,
                    'toplam' => $teklifToplam
                ]);
                break;
            }
        } catch (\Exception $e) {
            \Log::error('Teklif tutarı hesaplama hatası (' . $tablo . '): ' . $e->getMessage());
            continue;
        }
    }

    return $teklifToplam ?: 0;
}
public function debugTableStructure()
{
    $tablolar = [
        'service_stage_answers',
        'stage_questions', 
        'services',
        'service_plannings',
        'service_planning',
        'kasa_hareketleri',
        'cash_movements'
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
///////////////////////////////////////////////////////Survey Statistics///////////////////////////////////////////////////////////////////
    public function SurveyStatistics($tenant_id)
    {
        return view('frontend.secure.statistics.survey_statistics', compact('tenant_id'));
    }

   
}

