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
  /**
     * Teknisyen İstatistikleri Ana Sayfa
     */
    public function TechnicianStatistics($tenant_id)
    {
        // Cihaz türlerini al
        $cihazTurleri = DB::table('device_types')
            ->where('firma_id', $tenant_id)
            ->orderBy('cihaz', 'ASC')
            ->get();

        return view('frontend.secure.statistics.technician_statistics', compact(
            'tenant_id',
            'cihazTurleri'
        ));
    }

    /**
     * Teknisyen İstatistikleri Veri Tablosu
     */
    public function TechnicianStatisticsData(Request $request, $tenant_id)
    {
        $tarihler = explode("---", $request->personelTabloGetir);

// Eğer geçerli değilse, hata döndür
if (count($tarihler) < 2) {
    return response()->json([
        'html' => '<div class="alert alert-danger">Geçersiz tarih aralığı girildi.</div>'
    ]);
}

        $tarih1 = explode("/", $tarihler[0]);
        $tarih2 = explode("/", $tarihler[1]);
        
        $tarih1 = $tarih1[2] . "-" . $tarih1[1] . "-" . $tarih1[0];
        $tarih2 = $tarih2[2] . "-" . $tarih2[1] . "-" . $tarih2[0];

        // Teknisyenleri al (Teknisyen rolüne sahip aktif kullanıcılar)
        $personeller = DB::table('tb_user')
            ->join('model_has_roles', 'tb_user.user_id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('tb_user.tenant_id', $tenant_id)
            ->where('roles.name', 'Teknisyen')
            ->where('tb_user.status', 1)
            ->select('tb_user.user_id as id', 'tb_user.name as adsoyad')
            ->orderBy('tb_user.name', 'ASC')
            ->get();

        $personelAll = $personeller->pluck('id')->toArray();

        // Tarih aralığını oluştur
        $where = "AND sc.cevap='" . $tarihler[1] . "'";
        $where2 = [$tarih2];
        $tarih2_temp = date('Y-m-d', strtotime($tarih2 . ' -1 days'));

        $tarihListe = $this->getDatesFromRange($tarih1, $tarih2_temp);
        foreach ($tarihListe as $tarih) {
            $format = explode("-", $tarih);
            $where .= " OR sc.cevap='" . $format[2] . "/" . $format[1] . "/" . $format[0] . "' ";
            $where2[] = $format[2] . "-" . $format[1] . "-" . $format[0];
        }

        // Seçilen tarih aralıklarının planidlerini al
        $secTarihlerQuery = "
            SELECT sc.id, sc.servisid, sc.planid, sc.soruid, sc.cevap, ss.cevap as cevap2, s.cihazTur
            FROM service_stage_answers as sc 
            LEFT JOIN stage_questions as ss ON ss.id = sc.soruid 
            LEFT JOIN services as s ON s.id = sc.servisid 
            WHERE ss.cevap LIKE '%Tarih%' AND sc.firma_id = ? " . $where;

        $secTarihler = DB::select($secTarihlerQuery, [$tenant_id]);

        // Plan ID'leri topla
        $servis_planlar = [];
        $allowedSoruIds = [296, 326, 369, 306, 337, 341]; // Eski soruid'ler - güncellemelisiniz
        
        foreach ($secTarihler as $secTarih) {
            if (in_array($secTarih->soruid, $allowedSoruIds)) {
                if (!empty($request->cihazTur)) {
                    if ($request->cihazTur == $secTarih->cihazTur) {
                        $servis_planlar[] = $secTarih->planid;
                    }
                } else {
                    $servis_planlar[] = $secTarih->planid;
                }
            }
        }

        if (empty($servis_planlar)) {
            return response()->json(['html' => '<div class="alert alert-info">Bu tarih aralığında veri bulunamadı.</div>']);
        }

        // Personellerin servislerini bul
        $getTableQuery = "
            SELECT sc.id, sc.servisid, sc.planid, sc.soruid, sc.cevap, ss.cevap as cevap2
            FROM service_stage_answers as sc 
            LEFT JOIN stage_questions as ss ON ss.id = sc.soruid 
            WHERE sc.cevap IN (" . implode(", ", $personelAll) . ") 
            AND ss.cevap LIKE '%Grup%' 
            AND sc.planid IN (" . implode(", ", $servis_planlar) . ")
            AND sc.firma_id = ?";

        $getTable = DB::select($getTableQuery, [$tenant_id]);

        // Personel servislerini grupla
        $groupConcat = [];
        foreach ($getTable as $row) {
            if (!isset($groupConcat[$row->cevap])) {
                $groupConcat[$row->cevap] = [];
            }
            $groupConcat[$row->cevap][] = $row->servisid;
        }

        $groupAll = [];
        foreach ($groupConcat as $key => $servisler) {
            $uniqueServisler = array_unique($servisler);
            $groupAll[$key] = implode(", ", $uniqueServisler);
        }

        $html = $this->generateTechnicianTable($groupAll, $personeller, $tenant_id, $tarih1, $tarih2, $request->cihazTur);
        
        return response()->json(['html' => $html]);
    }

    /**
     * Teknisyen Detay İstatistikleri
     */
    public function TechnicianStatisticsDetail(Request $request, $tenant_id)
    {
        $persid = $request->personelTabloDetayGetir;
        $gelenTarih1 = explode("/", $request->tarih1);
        $gelenTarih2 = explode("/", $request->tarih2);
        $gelenTarih3 = $gelenTarih1[2] . "-" . $gelenTarih1[1] . "-" . $gelenTarih1[0];
        $gelenTarih4 = $gelenTarih2[2] . "-" . $gelenTarih2[1] . "-" . $gelenTarih2[0];

        // Detay istatistik verilerini hazırla
        $detailData = $this->generateTechnicianDetailData($persid, $gelenTarih3, $gelenTarih4, $tenant_id, $request->cihazTur);
        
        return response()->json(['html' => $detailData]);
    }

    /**
     * Tarih aralığındaki tarihleri döndür
     */
    private function getDatesFromRange($start, $end, $format = 'Y-m-d')
    {
        $array = [];
        $interval = new DateInterval('P1D');
        $realEnd = new DateTime($end);
        $realEnd->add($interval);
        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        foreach ($period as $date) {
            $array[] = $date->format($format);
        }

        return $array;
    }

    /**
     * Teknisyen tablosu HTML'ini oluştur
     */
    private function generateTechnicianTable($groupAll, $personeller, $tenant_id, $tarih1, $tarih2, $cihazTur = null)
    {
        $html = '<div class="table-responsive">
            <table class="table table-hover table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="title">
                    <tr>
                        <th><span class="desktop">Personel</span><span class="mobile">Personel</span></th>
                        <th style="width: 85px"><span class="desktop">Atanan Servis</span><span class="mobile">A</span></th>
                        <th style="width: 85px"><span class="desktop">Tamamlanan Servis</span><span class="mobile">T</span></th>
                        <th style="width: 85px"><span class="desktop">Şikayetçi Servis</span><span class="mobile">Ş</span></th>
                        <th style="width: 85px"><span class="desktop">İptal Servis</span><span class="mobile">İ</span></th>
                        <th style="width: 85px"><span class="desktop">Haber Verecek</span><span class="mobile">H</span></th>
                        <th style="width: 85px"><span class="desktop">Fiyatta Anlaşılamadı</span><span class="mobile">F</span></th>
                        <th style="width: 85px"><span class="desktop">Alınan Ücret</span><span class="mobile">Ü</span></th>
                        <th style="width: 85px"><span class="desktop">Verilen Teklif</span><span class="mobile">T</span></th>
                    </tr>
                </thead>
                <tbody>';

        $personelNotAll = [];
        
        foreach ($groupAll as $key => $row) {
            $personelNotAll[] = $key;
            $perSec = $personeller->firstWhere('id', $key);
            if (!$perSec) continue;

            $servisSay = count(explode(", ", rtrim($row, ', ')));
            $servisler = rtrim($row, ', ');

            $html .= '<tr data-persid="' . $key . '" class="tdDetayBtn">';
            $html .= '<td><strong>' . $perSec->adsoyad . '</strong></td>';
            $html .= '<td><strong>' . $servisSay . '</strong></td>';

            // Tamamlanan servisler
            $tamamlanan = DB::table('services')
                ->whereRaw("id IN($servisler)")
                ->where('servisDurum', 255)
                ->count();

            // Şikayetçi servisler hesaplama
            $sikayetciSay = $this->calculateComplaintServices($servisler, $key, $tenant_id);

            // İptal servisler hesaplama
            $iptalSay = $this->calculateCancelledServices($servisler, $key, $tenant_id);

            // Haber verecek servisler
            $haberSay = $this->calculateCallbackServices($servisler, $key, $tenant_id);

            // Fiyatta anlaşılamadı servisler
            $fiyatSay = $this->calculatePriceDisagreementServices($servisler, $key, $tenant_id);

            // Alınan ücretler
            $paraToplam = $this->calculateCollectedAmount($servisler, $key, $tenant_id, $tarih1, $tarih2);

            // Verilen teklifler
            $teklifToplam = $this->calculateGivenOffers($servisler, $key, $tenant_id, $tarih1, $tarih2);

            $html .= '<td><strong>' . $tamamlanan . '</strong></td>';
            $html .= '<td><strong>' . $sikayetciSay . '</strong></td>';
            $html .= '<td><strong>' . $iptalSay . '</strong></td>';
            $html .= '<td><strong>' . $haberSay . '</strong></td>';
            $html .= '<td><strong>' . $fiyatSay . '</strong></td>';
            $html .= '<td data-sort="' . $paraToplam . '"><strong>' . $paraToplam . ' TL</strong></td>';
            $html .= '<td data-sort="' . $teklifToplam . '"><strong>' . $teklifToplam . ' TL</strong></td>';
            $html .= '</tr>';
        }

        // Servis atanmamış personeller
        $remainingPersonels = $personeller->whereNotIn('id', $personelNotAll);
        foreach ($remainingPersonels as $perSec) {
            $html .= '<tr>';
            $html .= '<td><strong>' . $perSec->adsoyad . '</strong></td>';
            $html .= '<td><strong>0</strong></td>';
            $html .= '<td><strong>0</strong></td>';
            $html .= '<td><strong>0</strong></td>';
            $html .= '<td><strong>0</strong></td>';
            $html .= '<td><strong>0</strong></td>';
            $html .= '<td><strong>0</strong></td>';
            $html .= '<td><strong>0 TL</strong></td>';
            $html .= '<td><strong>0 TL</strong></td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
    }

    // Yardımcı hesaplama metodları
    private function calculateComplaintServices($servisler, $personelId, $tenant_id)
    {
        $sikayetciSay = 0;
        $sikayetciler = DB::table('service_plannings')
            ->where('firma_id', $tenant_id)
            ->whereRaw("servisid IN($servisler)")
            ->where('gidenIslem', 254)
            ->groupBy('servisid')
            ->get();

        foreach ($sikayetciler as $servis) {
            $ilkServis = DB::table('service_plannings')
                ->where('servisid', $servis->servisid)
                ->where('pid', $personelId)
                ->orderBy('created_at', 'ASC')
                ->first();
                
            $sonSikayet = DB::table('service_plannings')
                ->where('servisid', $servis->servisid)
                ->where('gidenIslem', 254)
                ->orderBy('created_at', 'DESC')
                ->first();

            if ($ilkServis && $sonSikayet) {
                $ilkServisTime = strtotime($ilkServis->created_at);
                $sonSikayetTime = strtotime($sonSikayet->created_at);
                if ($sonSikayetTime > $ilkServisTime) {
                    $sikayetciSay++;
                }
            }
        }

        return $sikayetciSay;
    }

    private function calculateCancelledServices($servisler, $personelId, $tenant_id)
    {
        $iptalSay = 0;
        $iptaller = DB::table('service_plannings')
            ->where('firma_id', $tenant_id)
            ->whereRaw("servisid IN($servisler)")
            ->where('gidenIslem', 244)
            ->groupBy('servisid')
            ->get();

        foreach ($iptaller as $servis) {
            $ilkServis = DB::table('service_plannings')
                ->where('servisid', $servis->servisid)
                ->where('pid', $personelId)
                ->orderBy('created_at', 'ASC')
                ->first();
                
            $sonIptal = DB::table('service_plannings')
                ->where('servisid', $servis->servisid)
                ->where('gidenIslem', 244)
                ->orderBy('created_at', 'DESC')
                ->first();

            if ($ilkServis && $sonIptal) {
                $ilkServisTime = strtotime($ilkServis->created_at);
                $sonIptalTime = strtotime($sonIptal->created_at);
                if ($sonIptalTime > $ilkServisTime) {
                    $iptalSay++;
                }
            }
        }

        return $iptalSay;
    }

    private function calculateCallbackServices($servisler, $personelId, $tenant_id)
    {
        $haberSay = 0;
        $haberciler = DB::table('service_plannings')
            ->where('firma_id', $tenant_id)
            ->whereRaw("servisid IN($servisler)")
            ->where('gidenIslem', 247)
            ->groupBy('servisid')
            ->get();

        foreach ($haberciler as $haber) {
            $ilkServis = DB::table('service_plannings')
                ->where('servisid', $haber->servisid)
                ->where('pid', $personelId)
                ->orderBy('created_at', 'ASC')
                ->first();
                
            $sonServis = DB::table('service_plannings')
                ->where('servisid', $haber->servisid)
                ->where('gidenIslem', 247)
                ->orderBy('created_at', 'DESC')
                ->first();

            if ($ilkServis && $sonServis) {
                $ilkServisTime = strtotime($ilkServis->created_at);
                $sonServisTime = strtotime($sonServis->created_at);
                if ($sonServisTime > $ilkServisTime - 1) {
                    $haberSay++;
                }
            }
        }

        return $haberSay;
    }

    private function calculatePriceDisagreementServices($servisler, $personelId, $tenant_id)
    {
        $fiyatSay = 0;
        $fiyatlar = DB::table('service_plannings')
            ->where('firma_id', $tenant_id)
            ->whereRaw("servisid IN($servisler)")
            ->where('gidenIslem', 241)
            ->groupBy('servisid')
            ->get();

        foreach ($fiyatlar as $fiyat) {
            $ilkServis = DB::table('service_plannings')
                ->where('servisid', $fiyat->servisid)
                ->where('pid', $personelId)
                ->orderBy('created_at', 'ASC')
                ->first();
                
            $sonServis = DB::table('service_plannings')
                ->where('servisid', $fiyat->servisid)
                ->where('gidenIslem', 241)
                ->orderBy('created_at', 'DESC')
                ->first();

            if ($ilkServis && $sonServis) {
                $ilkServisTime = strtotime($ilkServis->created_at);
                $sonServisTime = strtotime($sonServis->created_at);
                if ($sonServisTime > $ilkServisTime - 1) {
                    $fiyatSay++;
                }
            }
        }

        return $fiyatSay;
    }

    private function calculateCollectedAmount($servisler, $personelId, $tenant_id, $tarih1, $tarih2)
    {
        $paraToplam = DB::table('cash_transactions')
            ->where('firma_id', $tenant_id)
            ->whereRaw("servis IN($servisler)")
            ->where('odemeYonu', 1)
            ->where('personel', $personelId)
            ->whereBetween('created_at', [$tarih1 . ' 00:00:00', $tarih2 . ' 23:59:59'])
            ->sum('fiyat');

        return $paraToplam ?? 0;
    }

    private function calculateGivenOffers($servisler, $personelId, $tenant_id, $tarih1, $tarih2)
    {
        // Teklif soruid'lerini güncelleyin (350-356 yerine yeni ID'ler)
        $teklifSoruIds = [350, 351, 352, 353, 354, 355, 356];
        
        $teklifToplam = DB::table('service_stage_answers as sc')
            ->join('service_plannings as sp', 'sp.id', '=', 'sc.planid')
            ->where('sc.firma_id', $tenant_id)
            ->whereIn('sc.soruid', $teklifSoruIds)
            ->whereRaw("sc.servisid IN($servisler)")
            ->where('sp.pid', $personelId)
            ->whereBetween('sc.created_at', [$tarih1 . ' 00:00:00', $tarih2 . ' 23:59:59'])
            ->sum('sc.cevap');

        return $teklifToplam ?? 0;
    }

    private function generateTechnicianDetailData($persid, $gelenTarih3, $gelenTarih4, $tenant_id, $cihazTur)
    {
        // Grafiklerdeki tarih alanları için
        $tarihSon = $this->getDatesFromRange($gelenTarih3, $gelenTarih4);
        $labels = [];
        foreach ($tarihSon as $tarih) {
            $format = explode("-", $tarih);
            $labels[] = "'" . $format[2] . "/" . $format[1] . "'";
        }
        $labelsString = implode(", ", $labels);

        // Tarih aralığında bu personele ait servisler
        $where = "AND sc.cevap='" . date('d/m/Y', strtotime($gelenTarih4)) . "'";
        $tarih1 = date('Y-m-d', strtotime($gelenTarih4 . ' -1 days'));
        $tarihListe = $this->getDatesFromRange($gelenTarih3, $tarih1);
        
        foreach ($tarihListe as $tarih) {
            $format = explode("-", $tarih);
            $where .= " OR sc.cevap='" . $format[2] . "/" . $format[1] . "/" . $format[0] . "' ";
        }

        // Personelin servis verilerini al
        $personelServisler = $this->getPersonelServisler($persid, $where, $tenant_id, $cihazTur);
        
        if (empty($personelServisler)) {
            return '<div class="alert alert-info">Bu personel için veri bulunamadı.</div>';
        }

        $servisler = implode(", ", $personelServisler);

        // İstatistik verilerini hesapla
        $stats = $this->calculatePersonelStats($servisler, $persid, $tenant_id);

        // Günlük grafik verileri
        $gunlukVeriler = $this->calculateDailyData($persid, $gelenTarih3, $gelenTarih4, $tenant_id, $cihazTur);

        $html = '
        <div class="row detayGrafikler">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 tamamlananGrafik">
                        <span>Tamamlanan Servisler</span>
                        <canvas id="tamamlananArea" width="100%" height="30"></canvas>
                    </div>
                    <div class="col-md-4 iptalGrafik">
                        <span>İptal Servisler</span>
                        <canvas id="iptalArea" width="100%" height="30"></canvas>
                    </div>
                    <div class="col-md-4 gelirGrafik">
                        <span>Alınan Ücretler</span>
                        <canvas id="gelirArea" width="100%" height="30"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row detayAsamalar">
            <div class="cols">
                <div class="capt text-center">
                    <p>Atanan Servisler</p>
                    <h2>' . count($personelServisler) . '</h2>
                </div>
            </div>
            <div class="cols">
                <div class="capt text-center">
                    <p>Tamamlanan Servisler</p>
                    <h2>' . $stats['tamamlanan'] . '</h2>
                </div>
            </div>
            <div class="cols">
                <div class="capt text-center">
                    <p>Şikayetçi Servisler</p>
                    <h2>' . $stats['sikayetci'] . '</h2>
                </div>
            </div>
            <div class="cols">
                <div class="capt text-center">
                    <p>İptal Servisler</p>
                    <h2>' . $stats['iptal'] . '</h2>
                </div>
            </div>
            <div class="cols">
                <div class="capt text-center">
                    <p>Haber Verecek</p>
                    <h2>' . $stats['haber'] . '</h2>
                </div>
            </div>
            <div class="cols">
                <div class="capt text-center">
                    <p>Atölyede Tamir Ediliyor</p>
                    <h2>' . $stats['atolyedeTamir'] . '</h2>
                </div>
            </div>
            <div class="cols">
                <div class="capt text-center">
                    <p>Atölyeye Aldır (Nakliye Gönder)</p>
                    <h2>' . $stats['atolyeyeAldir'] . '</h2>
                </div>
            </div>
            <div class="cols">
                <div class="capt text-center">
                    <p>Cihaz Atölyeye Alındı</p>
                    <h2>' . $stats['cihazAtolyeye'] . '</h2>
                </div>
            </div>
            <div class="cols">
                <div class="capt text-center">
                    <p>Cihaz Tamir Edilemiyor</p>
                    <h2>' . $stats['tamirEdilemiyor'] . '</h2>
                </div>
            </div>
            <div class="cols">
                <div class="capt text-center">
                    <p>Cihaz Teslim Edildi</p>
                    <h2>' . $stats['teslimEdildi'] . '</h2>
                </div>
            </div>
            <div class="cols">
                <div class="capt text-center">
                    <p>Fiyatta Anlaşılamadı</p>
                    <h2>' . $stats['fiyatAnlasilamadi'] . '</h2>
                </div>
            </div>
            <div class="cols">
                <div class="capt text-center">
                    <p>Yerinde Bakım Yapıldı</p>
                    <h2>' . $stats['yerindeBakim'] . '</h2>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        $(document).ready(function(){
            // Tamamlanan Servisler Grafiği
            var ctxTamamlanan = document.getElementById("tamamlananArea");
            new Chart(ctxTamamlanan, {
                type: "line",
                data: {   
                    labels: [' . $labelsString . '],
                    datasets: [{
                        lineTension: 0.3,
                        backgroundColor: "rgba(2,117,216,0.2)",
                        borderColor: "rgba(2,117,216,1)",
                        pointRadius: 5,
                        pointBackgroundColor: "rgba(2,117,216,1)",
                        pointBorderColor: "rgba(255,255,255,0.8)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(2,117,216,1)",
                        pointHitRadius: 50,
                        pointBorderWidth: 2,
                        data: [' . $gunlukVeriler['tamamlanan'] . '],
                    }],
                },
                options: {
                    plugins: { legend: { display: false } },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // İptal Servisler Grafiği
            var ctxIptal = document.getElementById("iptalArea");
            new Chart(ctxIptal, {
                type: "line",
                data: {   
                    labels: [' . $labelsString . '],
                    datasets: [{
                        lineTension: 0.3,
                        backgroundColor: "rgba(255,0,0,0.2)",
                        borderColor: "rgba(255,0,0,0.7)",
                        pointRadius: 5,
                        pointBackgroundColor: "rgba(255,0,0,1)",
                        pointBorderColor: "rgba(255,255,255,0.8)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(255,0,0,1)",
                        pointHitRadius: 50,
                        pointBorderWidth: 2,
                        data: [' . $gunlukVeriler['iptal'] . '],
                    }],
                },
                options: {
                    plugins: { legend: { display: false } },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Gelir Grafiği
            var ctxUcret = document.getElementById("gelirArea");
            new Chart(ctxUcret, {
                type: "line",
                data: {   
                    labels: [' . $labelsString . '],
                    datasets: [{
                        lineTension: 0.3,
                        backgroundColor: "rgba(84,177,47,0.2)",
                        borderColor: "rgba(84,177,47,0.7)",
                        pointRadius: 5,
                        pointBackgroundColor: "rgba(84,177,47,1)",
                        pointBorderColor: "rgba(255,255,255,0.8)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(84,177,47,1)",
                        pointHitRadius: 50,
                        pointBorderWidth: 2,
                        data: [' . $gunlukVeriler['gelir'] . '],
                    }],
                },
                options: {
                    plugins: { legend: { display: false } },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
        </script>';

        return $html;
    }

    private function getPersonelServisler($persid, $where, $tenant_id, $cihazTur)
    {
        // Bu methodun içeriğini PHP kodundaki mantığa göre doldurmanız gerekiyor
        // Şimdilik basit bir örnek
        return [];
    }

    private function calculatePersonelStats($servisler, $persid, $tenant_id)
    {
        return [
            'tamamlanan' => 0,
            'sikayetci' => 0,
            'iptal' => 0,
            'haber' => 0,
            'atolyedeTamir' => 0,
            'atolyeyeAldir' => 0,
            'cihazAtolyeye' => 0,
            'tamirEdilemiyor' => 0,
            'teslimEdildi' => 0,
            'fiyatAnlasilamadi' => 0,
            'yerindeBakim' => 0
        ];
    }

    private function calculateDailyData($persid, $startDate, $endDate, $tenant_id, $cihazTur)
    {
        return [
            'tamamlanan' => '0,0,0',
            'iptal' => '0,0,0',
            'gelir' => '0,0,0'
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

