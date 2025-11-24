<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceOptNote;
use App\Models\ServicePlanning;
use App\Models\ServiceStage;
use App\Models\ServiceStageAnswer;
use App\Models\ServiceTime;
use App\Models\StageQuestion;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // Sabit Grup ID'leri (Eski koddan alındı, gerekirse config'den çekilebilir)
    const GROUP_DEPOCU = 249;
    const GROUP_BAYI = 258; // Tahmini, eski kodda geçiyor
    const ISLEM_SIKAYET = 254;
    const ISLEM_PARCA_BEKLIYOR = 257;
    const ISLEM_TAMAMLANDI = 263;

    public function myAssignedServices(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'Firma bulunamadı'], 404);
        }

        // 1. Filtreleme Mantığı (Eski kodun ana kalbi)
        $atananServisIDleri = $this->getYetkiliServisIDleri($user, $tenant->id);

        if (empty($atananServisIDleri)) {
            return response()->json([
                'success' => true,
                'message' => 'Bugün size atanmış servis bulunmamaktadır',
                'data' => []
            ], 200);
        }

        // 2. Servisleri Veritabanından Çek
        $services = Service::with([
            'musteri:id,adSoyad,tel1,tel2,adres,il,ilce',
            'markaCihaz:id,marka',
            'turCihaz:id,cihaz',
            'asamalar:id,asama',
            // İlişkili son planlamayı ve cevapları çekmek performans için iyi olabilir
            // ancak karmaşık filtreler olduğu için döngü içinde işleyeceğiz.
        ])
            ->whereIn('id', $atananServisIDleri)
            ->where('firma_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. Veriyi Formatla ve Renk/Detay Mantığını İşle
        $data = $services->map(function ($service) use ($tenant) {
            
            // Renk Mantığı (Legacy koddan)
            $renk = "";
            
            // Giden işlem 257 ise Mavi
            $maviKontrol = ServicePlanning::where('servisid', $service->id)
                ->where('gidenIslem', self::ISLEM_PARCA_BEKLIYOR)
                ->exists();

            if ($maviKontrol) {
                $renk = "62daff"; // Mavi
            }

            // Şikayet Sayısına Göre Renkler (gidenIslem = 254)
            $sikayetSayisi = ServicePlanning::where('servisid', $service->id)
                ->where('gidenIslem', self::ISLEM_SIKAYET)
                ->count();

            if ($sikayetSayisi == 1) $renk = "ffdf40";      // Sarı
            else if ($sikayetSayisi == 2) $renk = "ff8c00"; // Turuncu
            else if ($sikayetSayisi == 3) $renk = "ff0000"; // Kırmızı
            else if ($sikayetSayisi > 3) $renk = "cf0000";  // Koyu Kırmızı

            // Aşama Detaylarını Parse Et ([Parca], [Arac] vs.)
            $asamaDetay = $this->getAsamaDetaylari($service->planDurum, $tenant->id);

            return [
                'id' => $service->id,
                'renk' => $renk,
                'musteri' => [
                    'ad_soyad' => $service->musteri?->adSoyad,
                    'tel1' => $service->musteri?->tel1,
                    'tel2' => $service->musteri?->tel2,
                    'adres' => $service->musteri?->adres,
                    'il' => $service->musteri?->il,
                    'ilce' => $service->musteri?->ilce,
                ],
                'cihaz' => [
                    'marka' => $service->markaCihaz?->marka,
                    'tur' => $service->turCihaz?->cihaz,
                    'model' => $service->cihazModel,
                    'ariza' => $service->cihazAriza,
                ],
                'asama' => $service->asamalar?->asama,
                'asama_detay' => $asamaDetay, // Örn: "Değişen Parçalar: Ekran (1)"
                'acil' => $service->acil != 0,
                'created_at' => $service->created_at->format('Y-m-d H:i'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $data->count()
        ], 200);
    }

    /**
     * Legacy kodun "servis_izinler" dizisini oluşturduğu mantık.
     */
  private function getYetkiliServisIDleri($userId, $tenantId)
    {
        $servisIzinler = [];
        $bugun = Carbon::today(); // 2025-11-20 00:00:00
        
        // Mesai Saati Kontrolü (Örn: 1630)
        $simdikiSaat = (int) date('Hi'); 
        $zamanAyar = ServiceTime::where('firma_id', $tenantId)->first();
        $mesaiBaslangicStr = $zamanAyar ? $zamanAyar->zaman : "08:00";
        $mesaiBaslangic = (int) str_replace([':', '.'], '', $mesaiBaslangicStr);

        // 1. Adım: Kullanıcının atandığı (cevap = user_id olan) servisleri ve planları bul
        $servisCevaplari = ServiceStageAnswer::where('firma_id', $tenantId)
            ->where('cevap', $userId)
            ->selectRaw('servisid, MAX(planid) as max_planid')
            ->groupBy('servisid')
            ->get();

        foreach ($servisCevaplari as $row) {
            $servisId = $row->servisid;
            $planId = $row->max_planid;

            $plan = ServicePlanning::find($planId);
            if (!$plan) continue;

            // 2. Adım: Bu plana ait "Tarih" cevabını bul
            // Eski koddaki JOIN mantığını buraya uyguluyoruz:
            // Sorular tablosuna bak, cevabı '[Tarih]' olan sorunun, Cevaplar tablosundaki karşılığını getir.
            $tarihCevap = ServiceStageAnswer::join('servis_asama_sorulari', 'servis_asama_cevaplari.soruid', '=', 'servis_asama_sorulari.id')
                ->where('servis_asama_cevaplari.planid', $planId)
                ->where('servis_asama_sorulari.cevap', '[Tarih]') // Soru tipi Tarih olanı ara
                ->select('servis_asama_cevaplari.*')
                ->first();

            $isTarihliIs = false;
            $tarihUygun = false;

            // Eğer tarih sorusu varsa ve cevap boş değilse
            if ($tarihCevap && !empty($tarihCevap->cevap)) {
                $isTarihliIs = true; // Bu bir tarihli iş
                try {
                    // Tarih formatı düzeltme (20.11.2025 veya 2025-11-20)
                    $rawDate = str_replace('/', '.', $tarihCevap->cevap);
                    $servisTarihi = Carbon::parse($rawDate)->startOfDay();

                    // Tarih BUGÜN ise
                    if ($servisTarihi->equalTo($bugun)) {
                        // Saat kontrolü (Mesai saati geldi mi?)
                        if ($simdikiSaat >= $mesaiBaslangic) {
                            $tarihUygun = true;
                        }
                    }
                } catch (\Exception $e) {
                    $tarihUygun = false;
                }
            }

            // --- KURAL: TARİHLİ İŞLER ---
            // Plan tablosunda tarihDurum=1 ise VEYA sistem bir tarih sorusu bulduysa
            if (($plan->tarihDurum ?? 0) == 1 || $isTarihliIs) {
                if ($tarihUygun) {
                    $servisIzinler[] = $servisId;
                }
                // Tarihli iş ama tarih bugüne uymuyorsa listeye eklemiyoruz.
            }
            // --- KURAL: TARİHSİZ İŞLER ---
            // Tarih durumu yok, o zaman servisin aktif durumu bu plan mı diye bakılır
            else {
                $servis = Service::select('id', 'planDurum')->find($servisId);
                
                if ($servis && $servis->planDurum == $planId) {
                    $servisIzinler[] = $servisId;
                }
                // Ekstra: Planı bugün oluşturan kişi bu kullanıcı ise (Depocu/Teknisyen fark etmeksizin anlık takip)
                else if ($plan->pid == $userId) {
                     $planTarih = Carbon::parse($plan->created_at)->startOfDay();
                     if ($planTarih->equalTo($bugun)) {
                         $servisIzinler[] = $servisId;
                     }
                }
            }
        }

        return array_unique($servisIzinler);
    }

    /**
     * Legacy koddaki `[Parca]`, `[Arac]` gibi cevapları parse eder.
     */
    private function getAsamaDetaylari($planId, $tenantId)
    {
        $detaylar = [];
        
        // İlgili plana ait tüm cevapları çek
        $cevaplar = ServiceStageAnswer::where('planid', $planId)
            ->where('firma_id', $tenantId)
            ->get();

        foreach ($cevaplar as $cevap) {
            if (empty($cevap->cevap)) continue;

            // Soruyu bul (Label için)
            $soru = StageQuestion::find($cevap->soruid);
            if (!$soru) continue;

            // 1. [Grup] -> Personel Adı
            if (str_contains($soru->cevap, 'Grup') || str_contains($soru->cevap, 'Bayi')) {
                // Burada User modelinden isim çekilmeli
                // $personel = User::find($cevap->cevap);
                // $detaylar[$soru->soru] = $personel ? $personel->name : '-';
                
                // Örnek: Veri direkt varsa
                $detaylar[$soru->soru] = "Personel #" . $cevap->cevap; 
            }
            // 2. [Arac] -> Araç Plakası/Adı
            else if ($soru->cevap == '[Arac]') {
                // $arac = Vehicle::find($cevap->cevap);
                // $detaylar[$soru->soru] = $arac->plaka;
                $detaylar[$soru->soru] = $cevap->cevap;
            }
            // 3. [Parca] -> Stok Adı ve Adeti (Parsing Logic)
            else if ($soru->cevap == '[Parca]') {
                // Format: "ID---ADET, ID2---ADET2"
                $parcaString = "";
                $parcalar = explode(", ", $cevap->cevap);
                
                foreach ($parcalar as $parcaItem) {
                    $parts = explode("---", $parcaItem);
                    if (count($parts) < 2) continue;
                    
                    $stokId = $parts[0];
                    $adet = $parts[1];

                    $stok = Stock::find($stokId); // Stok Modeli
                    if ($stok) {
                        $parcaString .= $stok->urunAdi . " (" . $adet . "), ";
                    }
                }
                $detaylar[$soru->soru] = rtrim($parcaString, ", ");
            }
            // 4. Standart Metin
            else {
                $detaylar[$soru->soru] = $cevap->cevap;
            }
        }

        return empty($detaylar) ? null : $detaylar;
    }

    // Detay methodu da benzer mantıkla güncellenmelidir...
    public function myAssignedServiceDetail(Request $request, $id)
    {
        // Mevcut detay kodunuz burada kalabilir, 
        // ancak erişim kontrolünü yine getYetkiliServisIDleri ile yapmalısınız.
        return parent::myAssignedServiceDetail($request, $id);
    }
}
