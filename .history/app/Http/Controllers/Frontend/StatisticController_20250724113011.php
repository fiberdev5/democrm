<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
       public function ServiceStatistics(Request $request, $tenant_id)
    {
        // Yetkilendirme kontrolü (middleware tarafından zaten yapılıyor olabilir)
        // Buraya ek yetkilendirme kontrolleri eklenebilir.

        // Personel ve Servis Kaynakları verilerini çekme
        $personeller = Personel::where('kid', $tenant_id)
                               ->where('yetki', '0')
                               ->where('durum', '1')
                               ->whereIn('grup', [243, 256])
                               ->orderBy('adsoyad', 'ASC')
                               ->get();

        $servisKaynaklar = ServisKaynak::where('kid', $tenant_id)
                                       ->orderBy('id', 'DESC')
                                       ->get();

        $toplamAlinanServislerSay = 0;
        $toplamAlinanServislerAll = [];

        // Filtreleme yapıldı mı kontrol et
        if ($request->has('servisSayListele')) {
            $tarih1_str = htmlspecialchars(trim($request->input('tarih1')));
            $tarih2_str = htmlspecialchars(trim($request->input('tarih2')));

            // Tarihleri Carbon objelerine çevir
            $tarih1 = Carbon::createFromFormat('d/m/Y', $tarih1_str)->startOfDay();
            $tarih2 = Carbon::createFromFormat('d/m/Y', $tarih2_str)->endOfDay();

            $query = Servis::where('kid', $tenant_id)
                           ->where('durum', '1')
                           ->whereBetween('kayitTarihi', [$tarih1, $tarih2]);

            if ($request->input('personeller') != '0') {
                $query->where('kayitAlan', $request->input('personeller'));
            }

            if ($request->input('servisKaynak') != '0') {
                $query->where('servisKaynak', $request->input('servisKaynak'));
            }

            $toplamAlinanServisler = $query->get();

            foreach ($toplamAlinanServisler as $row) {
                $iptalVarMi = ServisPlanlama::where('servisid', $row->id)
                                             ->where('gidenIslem', '244')
                                             ->first();
                if (!$iptalVarMi) {
                    $toplamAlinanServislerSay++;
                    $toplamAlinanServislerAll[] = $row->id;
                }
            }
            
            // Buradaki getServisCihazMarkaSay2, getServisCihazTurSay2, getServisKaynakSay2, getServisOptSay2
            // gibi metodları kendi Eloquent sorgularınızla değiştirmelisiniz.
            // Örnek olarak birini göstereyim:
            
            $servisMarkalar = Servis::selectRaw('cihazMarka, COUNT(*) as sayi')
                                    ->whereIn('id', $toplamAlinanServislerAll)
                                    ->groupBy('cihazMarka')
                                    ->get();

            $servisTurleri = Servis::selectRaw('cihazTur, COUNT(*) as sayi')
                                    ->whereIn('id', $toplamAlinanServislerAll)
                                    ->groupBy('cihazTur')
                                    ->get();
                                    
            $servisKaynaklariFiltered = Servis::selectRaw('servisKaynak, COUNT(*) as sayi')
                                    ->whereIn('id', $toplamAlinanServislerAll)
                                    ->groupBy('servisKaynak')
                                    ->get();

            $servisOperatorleri = Servis::selectRaw('kayitAlan, COUNT(*) as sayi')
                                    ->whereIn('id', $toplamAlinanServislerAll)
                                    ->groupBy('kayitAlan')
                                    ->get();

            return view('statistics.service-statistics', compact(
                'personeller',
                'servisKaynaklar',
                'toplamAlinanServislerSay',
                'servisMarkalar',
                'servisTurleri',
                'servisKaynaklariFiltered',
                'servisOperatorleri'
            ))->withInput($request->all()); // Form değerlerini korumak için
        } else {
            // Varsayılan istatistikler (Bugün, Son İki Gün vb.)
            $today = Carbon::today();
            $yesterday = Carbon::yesterday();
            $twoDaysAgo = Carbon::today()->subDays(1); // yesterday
            $threeDaysAgo = Carbon::today()->subDays(2);
            $fiveDaysAgo = Carbon::today()->subDays(4);
            $sevenDaysAgo = Carbon::today()->subDays(6);
            $firstDayOfMonth = Carbon::now()->startOfMonth();

            $bugunAlinanServisler = $this->getFilteredServices($tenant_id, $today, $today);
            $bugunAlinanServislerSay = count($bugunAlinanServisler);
            $bugunAlinanServislerAll = array_column($bugunAlinanServisler, 'id');

            $son2AlinanServisler = $this->getFilteredServices($tenant_id, $twoDaysAgo, $today);
            $son2AlinanServislerSay = count($son2AlinanServisler);
            $son2AlinanServislerAll = array_column($son2AlinanServisler, 'id');

            $son3AlinanServisler = $this->getFilteredServices($tenant_id, $threeDaysAgo, $today);
            $son3AlinanServislerSay = count($son3AlinanServisler);
            $son3AlinanServislerAll = array_column($son3AlinanServisler, 'id');

            $son5AlinanServisler = $this->getFilteredServices($tenant_id, $fiveDaysAgo, $today);
            $son5AlinanServislerSay = count($son5AlinanServisler);
            $son5AlinanServislerAll = array_column($son5AlinanServisler, 'id');

            $son7AlinanServisler = $this->getFilteredServices($tenant_id, $sevenDaysAgo, $today);
            $son7AlinanServislerSay = count($son7AlinanServisler);
            $son7AlinanServislerAll = array_column($son7AlinanServisler, 'id');

            $ay1AlinanServisler = $this->getFilteredServices($tenant_id, $firstDayOfMonth, $today);
            $ay1AlinanServislerSay = count($ay1AlinanServisler);
            $ay1AlinanServislerAll = array_column($ay1AlinanServisler, 'id');

            // Varsayılan istatistikler için detayları çekin
            $bugunMarkalar = Servis::selectRaw('cihazMarka, COUNT(*) as sayi')
                                    ->whereIn('id', $bugunAlinanServislerAll)
                                    ->groupBy('cihazMarka')
                                    ->get();
            $bugunTurler = Servis::selectRaw('cihazTur, COUNT(*) as sayi')
                                    ->whereIn('id', $bugunAlinanServislerAll)
                                    ->groupBy('cihazTur')
                                    ->get();
            $bugunKaynaklar = Servis::selectRaw('servisKaynak, COUNT(*) as sayi')
                                    ->whereIn('id', $bugunAlinanServislerAll)
                                    ->groupBy('servisKaynak')
                                    ->get();
            $bugunOperatorler = Servis::selectRaw('kayitAlan, COUNT(*) as sayi')
                                    ->whereIn('id', $bugunAlinanServislerAll)
                                    ->groupBy('kayitAlan')
                                    ->get();

            $son2Markalar = Servis::selectRaw('cihazMarka, COUNT(*) as sayi')
                                    ->whereIn('id', $son2AlinanServislerAll)
                                    ->groupBy('cihazMarka')
                                    ->get();
            $son2Turler = Servis::selectRaw('cihazTur, COUNT(*) as sayi')
                                    ->whereIn('id', $son2AlinanServislerAll)
                                    ->groupBy('cihazTur')
                                    ->get();
            $son2Kaynaklar = Servis::selectRaw('servisKaynak, COUNT(*) as sayi')
                                    ->whereIn('id', $son2AlinanServislerAll)
                                    ->groupBy('servisKaynak')
                                    ->get();
            $son2Operatorler = Servis::selectRaw('kayitAlan, COUNT(*) as sayi')
                                    ->whereIn('id', $son2AlinanServislerAll)
                                    ->groupBy('kayitAlan')
                                    ->get();
            
            $son3Markalar = Servis::selectRaw('cihazMarka, COUNT(*) as sayi')
                                    ->whereIn('id', $son3AlinanServislerAll)
                                    ->groupBy('cihazMarka')
                                    ->get();
            $son3Turler = Servis::selectRaw('cihazTur, COUNT(*) as sayi')
                                    ->whereIn('id', $son3AlinanServislerAll)
                                    ->groupBy('cihazTur')
                                    ->get();
            $son3Kaynaklar = Servis::selectRaw('servisKaynak, COUNT(*) as sayi')
                                    ->whereIn('id', $son3AlinanServislerAll)
                                    ->groupBy('servisKaynak')
                                    ->get();
            $son3Operatorler = Servis::selectRaw('kayitAlan, COUNT(*) as sayi')
                                    ->whereIn('id', $son3AlinanServislerAll)
                                    ->groupBy('kayitAlan')
                                    ->get();
            // Diğerleri için de benzer şekilde devam edebiliriz.


            return view('statistics.service-statistics', compact(
                'personeller',
                'servisKaynaklar',
                'bugunAlinanServislerSay',
                'son2AlinanServislerSay',
                'son3AlinanServislerSay',
                'son5AlinanServislerSay',
                'son7AlinanServislerSay',
                'ay1AlinanServislerSay',
                'bugunMarkalar',
                'bugunTurler',
                'bugunKaynaklar',
                'bugunOperatorler',
                'son2Markalar',
                'son2Turler',
                'son2Kaynaklar',
                'son2Operatorler',
                'son3Markalar',
                'son3Turler',
                'son3Kaynaklar',
                'son3Operatorler'
            ));
        }
    }

    // Helper metot: Servisleri filtreleyip iptalleri düşmek için
    private function getFilteredServices($kid, $startDate, $endDate)
    {
        $services = Servis::where('kid', $kid)
                          ->where('durum', '1')
                          ->whereBetween('kayitTarihi', [$startDate->startOfDay(), $endDate->endOfDay()])
                          ->get();

        $filteredServices = [];
        foreach ($services as $service) {
            $iptalVarMi = ServisPlanlama::where('servisid', $service->id)
                                         ->where('gidenIslem', '244')
                                         ->first();
            if (!$iptalVarMi) {
                $filteredServices[] = $service;
            }
        }
        return $filteredServices;
    }
}
