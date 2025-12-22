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

    // 1. Filtreleme Mantığı
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
    ])
        ->whereIn('id', $atananServisIDleri)
        ->where('firma_id', $tenant->id)
        ->orderBy('created_at', 'desc')
        ->get();

    // 3. Veriyi Formatla
    $data = $services->map(function ($service) use ($tenant) {
        
        // Renk Mantığı
        $renk = "";
        
        $maviKontrol = ServicePlanning::where('servisid', $service->id)
            ->where('gidenIslem', self::ISLEM_PARCA_BEKLIYOR)
            ->exists();

        if ($maviKontrol) {
            $renk = "62daff";
        }

        $sikayetSayisi = ServicePlanning::where('servisid', $service->id)
            ->where('gidenIslem', self::ISLEM_SIKAYET)
            ->count();

        if ($sikayetSayisi == 1) $renk = "ffdf40";
        else if ($sikayetSayisi == 2) $renk = "ff8c00";
        else if ($sikayetSayisi == 3) $renk = "ff0000";
        else if ($sikayetSayisi > 3) $renk = "cf0000";

        // Aşama Detayları
        $asamaDetay = $this->getAsamaDetaylari($service->planDurum, $tenant->id);

        return [
            'id' => $service->id,
            'plan_id' => $service->planDurum, // SON PLAN ID
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
            'asama_detay' => $asamaDetay,
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
    private function getYetkiliServisIDleri($user, $tenantId)
{
    $servisIzinler = [];
    $bugunYmd = date('Y-m-d');

    // Mesai başlangıç saati kontrolü
    $zamanAyar = ServiceTime::where('firma_id', $tenantId)->first(); 
    $mesaiBaslangic = $zamanAyar ? $zamanAyar->zaman : "08:00";

    $simdikiSaat = strtotime(date("H:i"));
    $baslangicSaati = strtotime($mesaiBaslangic);

    // Kullanıcıya atanan servisleri bul
    $servisCevaplari = ServiceStageAnswer::where('firma_id', $tenantId)
        ->where('cevap', $user->user_id)
        ->selectRaw('servisid, MAX(planid) as planid')
        ->groupBy('servisid')
        ->get();

    foreach ($servisCevaplari as $row) {
        $servisId = $row->servisid;
        $planId = $row->planid;

        // "Gidiş Tarihi" cevabını bul
        $tarihCevap = ServiceStageAnswer::where('planid', $planId)
            ->where('cevapText', '[Tarih]')
            ->first();

        // Eğer "Gidiş Tarihi" yoksa bu servisi atla
        if (!$tarihCevap) {
            continue;
        }

        // Tarih parse et
        $gidisTarihi = $tarihCevap->cevap;
        
        // Formatı normalize et (2025-11-20 formatına çevir)
        if (strpos($gidisTarihi, '.') !== false) {
            // "20.11.2025" -> "2025-11-20"
            $parts = explode('.', $gidisTarihi);
            if (count($parts) == 3) {
                $gidisTarihi = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
            }
        } else if (strpos($gidisTarihi, '/') !== false) {
            // "20/11/2025" -> "2025-11-20"
            $parts = explode('/', $gidisTarihi);
            if (count($parts) == 3) {
                $gidisTarihi = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
            }
        }

        // ÖNEMLİ: Sadece "Gidiş Tarihi" BUGÜN olanları işle
        if ($bugunYmd != $gidisTarihi) {
            continue; // Bugün değilse atla
        }

        // Saat Kontrolü
        if ($simdikiSaat < $baslangicSaati) {
            continue; // Henüz mesai başlamamışsa atla
        }

        // Bugün bu kullanıcı bu serviste işlem yaptı mı?
        $bugunIslemYapti = ServicePlanning::where('servisid', $servisId)
            ->where('kid', $user->user_id)
            ->whereDate('created_at', $bugunYmd)
            ->exists();

        // İşlem yapmamışsa ekle
        if (!$bugunIslemYapti) {
            $servisIzinler[] = $servisId;
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
        $user = $request->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Firma bulunamadı'
            ], 404);
        }

        // Servis bugün kendisine atanmış mı kontrol et
        $atananServisIDleri = $this->getBugunAtananServisIDleri($user->user_id, $tenant->id);

        if (!in_array($id, $atananServisIDleri)) {
            return response()->json([
                'success' => false,
                'message' => 'Bu servis bugün size atanmamış veya üzerinde işlem yapmışsınız'
            ], 403);
        }

        // Servisi getir
        $servis = Service::with([
            'asamalar',
            'musteri',
            'markaCihaz',
            'turCihaz',
            'warranty',
        ])
        ->where('firma_id', $tenant->id)
        ->find($id);

        if (!$servis) {
            return response()->json([
                'success' => false,
                'message' => 'Servis bulunamadı'
            ], 404);
        }

        // Alt aşamaları getir
        $altAsamalar = [];
        if ($servis->asamalar && $servis->asamalar->altAsamalar) {
            $altAsamaIds = explode(',', $servis->asamalar->altAsamalar);
            $altAsamalar = ServiceStage::whereIn('id', $altAsamaIds)
                ->orderBy('asama')
                ->get()
                ->map(function ($asama) {
                    return [
                        'id' => $asama->id,
                        'asama' => $asama->asama,
                        'asama_renk' => $asama->asama_renk,
                    ];
                });
        }

        // Eski işlemler
        $eskiIslemler = ServicePlanning::where('servisid', $id)
            ->with('user:user_id,name')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($planning) {
                return [
                    'id' => $planning->id,
                    'tarih' => $planning->tarih,
                    'saat' => $planning->saat,
                    'aciklama' => $planning->aciklama,
                    'durum' => $planning->durum,
                    'user' => $planning->user ? $planning->user->name : null,
                    'created_at' => $planning->created_at->format('Y-m-d H:i'),
                ];
            });

        // Garanti hesaplama
        $garantiInfo = null;
        if ($servis->warranty && $servis->warranty->garanti) {
            $garantiBitis = Carbon::parse($servis->created_at)
                ->addMonths($servis->warranty->garanti);
            $kalanGun = Carbon::now()->diffInDays($garantiBitis, false);

            $garantiInfo = [
                'garanti_suresi' => $servis->warranty->garanti . ' ay',
                'garanti_bitis' => $garantiBitis->format('Y-m-d'),
                'kalan_gun' => $kalanGun,
                'garanti_gecerli' => $kalanGun >= 0,
            ];
        }

        // Servis notları
        $servisNotlari = ServiceOptNote::where('servisid', $id)
            ->with('user:user_id,name')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($note) {
                return [
                    'id' => $note->id,
                    'not' => $note->not,
                    'user' => $note->user ? $note->user->name : null,
                    'created_at' => $note->created_at->format('Y-m-d H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'servis' => [
                    'id' => $servis->id,
                    'musteri' => [
                        'id' => $servis->musteri?->id,
                        'ad_soyad' => $servis->musteri?->adSoyad,
                        'tel1' => $servis->musteri?->tel1,
                        'tel2' => $servis->musteri?->tel2,
                        'adres' => $servis->musteri?->adres,
                    ],
                    'cihaz' => [
                        'marka' => $servis->markaCihaz?->marka,
                        'tur' => $servis->turCihaz?->cihaz,
                        'model' => $servis->cihazModel,
                        'seri_no' => $servis->cihazSeriNo,
                        'ariza' => $servis->cihazAriza,
                        'cihaz_sifresi' => $servis->cihazSifresi,
                        'cihaz_deseni' => $servis->cihazDeseni,
                    ],
                    'asama' => [
                        'id' => $servis->asamalar?->id,
                        'asama' => $servis->asamalar?->asama,
                        'renk' => $servis->asamalar?->asama_renk,
                    ],
                    'acil' => $servis->acil != 0 ? true : false,
                    'musait_tarih' => $servis->musaitTarih,
                    'created_at' => $servis->created_at->format('Y-m-d H:i'),
                ],
                'alt_asamalar' => $altAsamalar,
                'eski_islemler' => $eskiIslemler,
                'garanti' => $garantiInfo,
                'notlar' => $servisNotlari,
            ]
        ], 200);
    }
}
