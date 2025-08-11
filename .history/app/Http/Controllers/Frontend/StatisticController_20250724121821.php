<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Personel için User modelini kullanıyoruz
use App\Models\Service;
use App\Models\ServiceResource;
use App\Models\DeviceBrand;
use App\Models\DeviceType;
use App\Models\ServicePlanning; // Servis iptal kontrolü için
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    public function ServiceStatistics(Request $request, $tenant_id)
    {
        $kid = $tenant_id;

        // Tarih aralığı ve filtreleme değişkenleri
        $tarih1 = $request->input('tarih1', Carbon::today()->format('d/m/Y'));
        $tarih2 = $request->input('tarih2', Carbon::today()->format('d/m/Y'));
        $selectedPersonel = $request->input('personeller', '0');
        $selectedServiceSource = $request->input('servisKaynak', '0');

        // Tarih formatını veritabanına uygun hale getiriyoruz
        $dbTarih1 = Carbon::createFromFormat('d/m/Y', $tarih1)->startOfDay();
        $dbTarih2 = Carbon::createFromFormat('d/m/Y', $tarih2)->endOfDay();

        // Personeller (Users) ve Servis Kaynakları dropdown'ları için veriler
      $personeller = User::where('tenant_id', $kid)
            ->where('status', '1') // 'durum' yerine 'status' kullanıldı
            ->orderBy('name', 'ASC') // 'adsoyad' yerine 'name' kullanıldı
            ->get();
        $servisKaynak = ServiceResource::where('kid', $kid)->orderBy('id', 'DESC')->get();

        // Servisleri filtrelemek için temel sorgu
        $baseQuery = Service::where('kid', $kid)
            ->where('durum', '1')
            ->whereBetween('kayitTarihi', [$dbTarih1, $dbTarih2]);

        if ($selectedPersonel != '0') {
            $baseQuery->where('kayitAlan', $selectedPersonel);
        }

        if ($selectedServiceSource != '0') {
            $baseQuery->where('servisKaynak', $selectedServiceSource);
        }

        $allServices = $baseQuery->get();

        // İptalleri düşerek servis ID'lerini toplama
        $filteredServiceIds = [];
        foreach ($allServices as $service) {
            $isCancelled = ServicePlanning::where('servisid', $service->id)
                                          ->where('gidenIslem', '244')
                                          ->exists();
            if (!$isCancelled) {
                $filteredServiceIds[] = $service->id;
            }
        }
        $toplamAlinanServislerSay = count($filteredServiceIds);
        $toplamAlinanServislerAll = $filteredServiceIds;

        // İstatistikleri hesaplama (Marka, Tür, Kaynak, Operatör)
        // Eğer filtrelenmiş servis ID'leri varsa sorguları çalıştır
        $markalar = collect();
        if (!empty($toplamAlinanServislerAll)) {
            $markalar = Service::select('cihazMarka', DB::raw('COUNT(*) as sayi'))
                               ->whereIn('id', $toplamAlinanServislerAll)
                               ->groupBy('cihazMarka')
                               ->with('markaCihaz') // Modeldeki ilişki adını kullanıyoruz
                               ->get()
                               ->map(function ($item) {
                                   return ['marka' => $item->markaCihaz->marka ?? 'Bilinmeyen Marka', 'sayi' => $item->sayi];
                               });
        }
        
        $turler = collect();
        if (!empty($toplamAlinanServislerAll)) {
            $turler = Service::select('cihazTur', DB::raw('COUNT(*) as sayi'))
                             ->whereIn('id', $toplamAlinanServislerAll)
                             ->groupBy('cihazTur')
                             ->with('turCihaz') // Modeldeki ilişki adını kullanıyoruz
                             ->get()
                             ->map(function ($item) {
                                return ['cihaz' => $item->turCihaz->cihaz ?? 'Bilinmeyen Tür', 'sayi' => $item->sayi];
                             });
        }

        $kaynaklar = collect();
        if (!empty($toplamAlinanServislerAll)) {
            $kaynaklar = Service::select('servisKaynak', DB::raw('COUNT(*) as sayi'))
                                ->whereIn('id', $toplamAlinanServislerAll)
                                ->groupBy('servisKaynak')
                                ->with('skaynak') // Modeldeki ilişki adını kullanıyoruz
                                ->get()
                                ->map(function ($item) {
                                    return ['kaynak' => $item->skaynak->kaynak ?? 'Bilinmeyen Kaynak', 'sayi' => $item->sayi];
                                });
        }

        $operatorler = collect();
        if (!empty($toplamAlinanServislerAll)) {
            $operatorler = Service::select('kayitAlan', DB::raw('COUNT(*) as sayi'))
                                 ->whereIn('id', $toplamAlinanServislerAll)
                                 ->groupBy('kayitAlan')
                                 ->with('users') // Modeldeki ilişki adını kullanıyoruz
                                 ->get()
                                 ->map(function ($item) {
                                     return ['adsoyad' => $item->users->adsoyad ?? 'Bilinmeyen Operatör', 'sayi' => $item->sayi];
                                 });
        }

        // Genel istatistikler (Bugün, Son İki Gün, vb.)
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $twoDaysAgo = Carbon::today()->subDays(2);
        $fourDaysAgo = Carbon::today()->subDays(4);
        $sixDaysAgo = Carbon::today()->subDays(6);
        $firstDayOfMonth = Carbon::today()->startOfMonth();
        
        $stats = [
            'today' => $this->getServiceStatsForPeriod($kid, $today, $today),
            'lastTwoDays' => $this->getServiceStatsForPeriod($kid, $yesterday, $today),
            'lastThreeDays' => $this->getServiceStatsForPeriod($kid, $twoDaysAgo, $today),
            'lastFiveDays' => $this->getServiceStatsForPeriod($kid, $fourDaysAgo, $today),
            'lastSevenDays' => $this->getServiceStatsForPeriod($kid, $sixDaysAgo, $today),
            'thisMonth' => $this->getServiceStatsForPeriod($kid, $firstDayOfMonth, $today),
        ];

        return view('frontend.secure.statistics.service_statistics', [
            'tenant_id' => $tenant_id,
            'personeller' => $personeller,
            'servisKaynak' => $servisKaynak,
            'tarih1' => $tarih1,
            'tarih2' => $tarih2,
            'selectedPersonel' => $selectedPersonel,
            'selectedServiceSource' => $selectedServiceSource,
            'toplamAlinanServislerSay' => $toplamAlinanServislerSay,
            'markalar' => $markalar,
            'turler' => $turler,
            'kaynaklar' => $kaynaklar,
            'operatorler' => $operatorler,
            'stats' => $stats,
            'isFiltered' => $request->has('servisSayListele'), // Filtreleme yapılıp yapılmadığını kontrol et
        ]);
    }

    /**
     * Belirli bir tarih aralığı için servis istatistiklerini hesaplar.
     */
    private function getServiceStatsForPeriod($kid, $startDate, $endDate)
    {
        $servicesInPeriod = Service::where('kid', $kid)
            ->where('durum', '1')
            ->whereBetween('kayitTarihi', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->get();

        $filteredServiceIds = [];
        foreach ($servicesInPeriod as $service) {
            $isCancelled = ServicePlanning::where('servisid', $service->id)
                                          ->where('gidenIslem', '244')
                                          ->exists();
            if (!$isCancelled) {
                $filteredServiceIds[] = $service->id;
            }
        }

        $count = count($filteredServiceIds);
        $serviceIds = $filteredServiceIds;

        // İstatistik detayları
        $markalar = collect();
        if (!empty($serviceIds)) {
            $markalar = Service::select('cihazMarka', DB::raw('COUNT(*) as sayi'))
                               ->whereIn('id', $serviceIds)
                               ->groupBy('cihazMarka')
                               ->with('markaCihaz')
                               ->get()
                               ->map(function ($item) {
                                   return ['marka' => $item->markaCihaz->marka ?? 'Bilinmeyen Marka', 'sayi' => $item->sayi];
                               });
        }
        
        $turler = collect();
        if (!empty($serviceIds)) {
            $turler = Service::select('cihazTur', DB::raw('COUNT(*) as sayi'))
                             ->whereIn('id', $serviceIds)
                             ->groupBy('cihazTur')
                             ->with('turCihaz')
                             ->get()
                             ->map(function ($item) {
                                return ['cihaz' => $item->turCihaz->cihaz ?? 'Bilinmeyen Tür', 'sayi' => $item->sayi];
                             });
        }

        $kaynaklar = collect();
        if (!empty($serviceIds)) {
            $kaynaklar = Service::select('servisKaynak', DB::raw('COUNT(*) as sayi'))
                                ->whereIn('id', $serviceIds)
                                ->groupBy('servisKaynak')
                                ->with('skaynak')
                                ->get()
                                ->map(function ($item) {
                                    return ['kaynak' => $item->skaynak->kaynak ?? 'Bilinmeyen Kaynak', 'sayi' => $item->sayi];
                                });
        }

        $operatorler = collect();
        if (!empty($serviceIds)) {
            $operatorler = Service::select('kayitAlan', DB::raw('COUNT(*) as sayi'))
                                 ->whereIn('id', $serviceIds)
                                 ->groupBy('kayitAlan')
                                 ->with('users')
                                 ->get()
                                 ->map(function ($item) {
                                     return ['adsoyad' => $item->users->adsoyad ?? 'Bilinmeyen Operatör', 'sayi' => $item->sayi];
                                 });
        }

        return [
            'count' => $count,
            'markalar' => $markalar,
            'turler' => $turler,
            'kaynaklar' => $kaynaklar,
            'operatorler' => $operatorler,
        ];
    }
}