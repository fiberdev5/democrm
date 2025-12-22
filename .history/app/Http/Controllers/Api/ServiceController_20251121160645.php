<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\PersonelStock;
use App\Models\Service;
use App\Models\ServiceOptNote;
use App\Models\ServicePlanning;
use App\Models\ServiceReceiptNote;
use App\Models\ServiceStage;
use App\Models\ServiceStageAnswer;
use App\Models\ServiceTime;
use App\Models\StageQuestion;
use App\Models\Stock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Soruyu bul
        $soru = StageQuestion::find($cevap->soruid);
        if (!$soru) continue;

        // 1. [Grup] -> Personel Adı
        if (str_contains($soru->cevap, 'Grup')) {
            $personel = \App\Models\User::where('user_id', $cevap->cevap)->first();
            $detaylar[$soru->soru] = $personel ? $personel->name : 'Personel #' . $cevap->cevap;
        }
        // 2. [Arac] -> Araç Plakası
        else if ($soru->cevap == '[Arac]') {
            $arac = \App\Models\ServiceVehicle::find($cevap->cevap);
            $detaylar[$soru->soru] = $arac ? $arac->arac : $cevap->cevap;
        }
        // 3. [Parca] -> Stok Adı ve Adeti
        else if ($soru->cevap == '[Parca]') {
            $parcaString = "";
            $parcalar = explode(", ", $cevap->cevap);
            
            foreach ($parcalar as $parcaItem) {
                $parts = explode("---", $parcaItem);
                if (count($parts) < 2) continue;
                
                $stokId = $parts[0];
                $adet = $parts[1];

                $stok = Stock::find($stokId);
                if ($stok) {
                    $parcaString .= $stok->urunAdi . " (" . $adet . "), ";
                }
            }
            $detaylar[$soru->soru] = rtrim($parcaString, ", ");
        }
        // 4. [Tarih] - Formatı düzenle
        else if ($soru->cevap == '[Tarih]' || $cevap->cevapText == '[Tarih]') {
            // Tarih formatını düzenle (örn: 2025-11-20 -> 20/11/2025)
            $tarih = $cevap->cevap;
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tarih)) {
                // Y-m-d formatındaysa d/m/Y'ye çevir
                $detaylar[$soru->soru] = Carbon::parse($tarih)->format('d/m/Y');
            } else {
                $detaylar[$soru->soru] = $tarih;
            }
        }
        // 5. Standart Metin
        else {
            $detaylar[$soru->soru] = $cevap->cevap;
        }
    }

    return $detaylar;
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
        // $atananServisIDleri = $this->getYetkiliServisIDleri($user, $tenant->id);

        // if (!in_array($id, $atananServisIDleri)) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Bu servis bugün size atanmamış veya üzerinde işlem yapmışsınız'
        //     ], 403);
        // }

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
        ->orderBy('id', 'desc')
        ->get()
        ->map(function ($planning) use ($tenant) {
            // İşlem yapan kişiyi bul
            $islemYapan = null;
            if ($planning->pid) {
                $user = \App\Models\User::where('user_id', $planning->pid)->first();
                $islemYapan = $user ? $user->name : null;
            }

            // Aşama adını bul (gidenIslem)
            $asamaTitle = null;
            $asamaId = null;
            if ($planning->gidenIslem) {
                $asama = ServiceStage::find($planning->gidenIslem);
                $asamaTitle = $asama ? $asama->asama : null;
                $asamaId = $planning->gidenIslem;
            }

            // Aşama detaylarını al (planid'ye göre cevaplar)
            $aciklama = $this->getAsamaDetaylari($planning->id, $tenant->id);

            return [
                'id' => (string) $planning->id,
                'pid' => (string) $planning->pid,
                'tarih' => $planning->created_at->format('d/m/Y H:i'),
                'islem_yapan' => $islemYapan,
                'title' => $asamaTitle,
                'asama_id' => (string) $asamaId,
                'aciklama' => $aciklama ?: new \stdClass(), // Boşsa {} döndür
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

    // Personele atanan depo ürünleri
    public function myStocks(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Firma bulunamadı'
            ], 404);
        }

        // Personele atanan stokları getir
        $staffStocks = PersonelStock::with(['stok'])
            ->where('firma_id', $tenant->id)
            ->where('pid', $user->user_id)
            ->get();

        if ($staffStocks->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Size atanmış stok bulunmamaktadır',
                'data' => []
            ], 200);
        }

        // Stokları formatla
        $data = $staffStocks->map(function ($item) {
            return [
                'id' => $item->stok?->id,
                'urun_adi' => $item->stok?->urunAdi,
                'fiyat' => $item->stok?->fiyat,
                'adet' => $item->adet,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $data->count()
        ], 200);
    }

    // Aşama sorularını detaylı getir (seçeneklerle birlikte)
    public function getStageQuestions(Request $request, $asama_id)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Firma bulunamadı'
            ], 404);
        }

        // Aşamaya ait soruları getir
        $sorular = StageQuestion::where('asama', $asama_id)
            ->where(function($q) use ($tenant) {
                $q->whereNull('firma_id')
                ->orWhere('firma_id', $tenant->id);
            })
            ->orderBy('sira', 'asc')
            ->get();

        if ($sorular->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Bu aşamaya ait soru bulunmamaktadır',
                'altAsamalar' => []
            ], 200);
        }

        $altAsamalarArray = [];

        foreach ($sorular as $soru) {
            $inArray = [
                'id' => (string) $soru->id,
                'asama' => (string) $soru->asama,
                'soru' => $soru->soru,
                'cevap' => $soru->cevapTuru, // BURADA DEĞİŞİKLİK: cevap -> cevapTuru
            ];

            // [Aciklama] - Normal text input
            if ($soru->cevapTuru == '[Aciklama]') {
                $inArray['type'] = 'input';
            }
            // [Grup] - Personel seçimi
            else if (str_contains($soru->cevapTuru, 'Grup')) {
                $inArray['type'] = 'select';
                $inArray['aciklama'] = $this->getPersonelListByGroup($soru->cevapTuru, $tenant->id);
            }
            // [Tarih] - Tarih seçici
            else if ($soru->cevapTuru == '[Tarih]') {
                $inArray['type'] = 'datepicker';
                $inArray['aciklama'] = $this->getDefaultDate();
            }
            // [Saat] - Saat aralığı seçimi
            else if ($soru->cevapTuru == '[Saat]') {
                $inArray['type'] = 'timepicker';
                $inArray['aciklama'] = "08:00-10:00,09:00-11:00,10:00-12:00,11:00-13:00,12:00-14:00,13:00-15:00,14:00-16:00,15:00-17:00,16:00-18:00,17:00-19:00,18:00-20:00,19:00-21:00,20:00-22:00,21:00-23:00";
            }
            // [Arac] - Araç seçimi
            else if ($soru->cevapTuru == '[Arac]') {
                $inArray['type'] = 'select';
                $inArray['aciklama'] = $this->getAracList($tenant->id);
            }
            // [Fiyat] - Para input
            else if ($soru->cevapTuru == '[Fiyat]') {
                $inArray['type'] = 'money';
            }
            // [Teklif] - Teklif input
            else if ($soru->cevapTuru == '[Teklif]') {
                $inArray['type'] = 'money';
            }
            // [Parca] - Stok listesi
            else if ($soru->cevapTuru == '[Parca]') {
                $inArray['type'] = 'checkbox';
                $inArray['aciklama'] = $this->getPersonelStokList($user->user_id, $tenant->id);
            }
            // [Konsinye Cihaz] - Konsinye cihaz listesi
            else if ($soru->cevapTuru == '[Konsinye Cihaz]') {
                $inArray['type'] = 'checkbox';
                $inArray['aciklama'] = $this->getKonsinyeCihazList($user->user_id, $tenant->id);
            }
            // [Bayi] - Bayi seçimi
            else if ($soru->cevapTuru == '[Bayi]') {
                $inArray['type'] = 'select';
                $inArray['aciklama'] = $this->getBayiList($tenant->id);
            }

            $altAsamalarArray[] = $inArray;
        }

        return response()->json([
            'altAsamalar' => $altAsamalarArray
        ], 200);
    }

    // Grup numarasına göre personel listesi (Spatie role ile)
    private function getPersonelListByGroup($cevap, $tenantId)
    {
        // Örnek: "[Grup-244], [Grup-245]" -> [244, 245]
        preg_match_all('/\[Grup-(\d+)\]/', $cevap, $matches);
        $roleIds = $matches[1] ?? [];

        if (empty($roleIds)) {
            return [];
        }

        // Bu role ID'lerine sahip kullanıcıları getir
        $personeller = User::where('tenant_id', $tenantId)
            ->where('status', '1')
            ->whereHas('roles', function($query) use ($roleIds) {
                $query->whereIn('roles.id', $roleIds);
            })
            ->orderBy('name', 'asc')
            ->get()
            ->map(function($personel) {
                return [
                    'id' => (string) $personel->user_id,
                    'adsoyad' => $personel->name,
                ];
            })
            ->toArray();

        return $personeller;
    }

    // Varsayılan tarih (yarın veya cumartesiyse pazartesi)
    private function getDefaultDate()
    {
        $bugun = date('w');
        $date = ($bugun == 6)
            ? date('Y-m-d', strtotime('+2 days'))
            : date('Y-m-d', strtotime('+1 day'));
        
        // d/m/Y formatında döndür
        return date('d/m/Y', strtotime($date));
    }

    // Araç listesi
    private function getAracList($tenantId)
    {
        $araclar = Car::where('firma_id', $tenantId)
            ->where('durum', '1')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function($arac) {
                return [
                    'id' => (string) $arac->id,
                    'arac' => $arac->arac,
                ];
            })
            ->toArray();

        return $araclar;
    }

    // Personel stok listesi
    private function getPersonelStokList($userId, $tenantId)
    {
        $stoklar = DB::table('personel_stocks as ps')
            ->join('stocks as s', 's.id', '=', 'ps.stokid')
            ->where('ps.pid', $userId)
            ->where('ps.firma_id', $tenantId)
            ->where('ps.adet', '>', 0)
            ->select([
                'ps.id',
                'ps.stokid as stokid',
                'ps.adet',
                's.urunAdi',
                's.urunKodu'
            ])
            ->orderBy('ps.created_at', 'desc')
            ->get()
            ->map(function($stok) {
                return [
                    'id' => (string) $stok->id,
                    'stokid' => (string) $stok->stokid,
                    'adet' => (string) $stok->adet,
                    'urunAdi' => $stok->urunAdi,
                    'urunKodu' => $stok->urunKodu,
                ];
            })
            ->toArray();

        return $stoklar;
    }

    // Konsinye cihaz listesi
    private function getKonsinyeCihazList($userId, $tenantId)
    {
        $cihazlar = DB::table('stocks as s')
            ->join('stock_categories as kategori', 'kategori.id', '=', 's.urunKategori')
            ->where('s.firma_id', $tenantId)
            ->where('kategori.id', 3) // Konsinye cihaz kategorisi
            ->where('s.stokAdedi', '>', 0) // Stokta olan cihazlar
            ->select([
                's.id',
                's.urunAdi',
                's.urunKodu',
                's.stokAdedi as adet'
            ])
            ->orderBy('s.urunAdi', 'asc')
            ->get()
            ->map(function($cihaz) {
                return [
                    'id' => (string) $cihaz->id,
                    'adet' => (string) $cihaz->adet,
                    'urunAdi' => $cihaz->urunAdi,
                    'urunKodu' => $cihaz->urunKodu,
                ];
            })
            ->toArray();

        return $cihazlar;
    }

    // Bayi listesi
    private function getBayiList($tenantId)
    {
        $bayiler = User::where('tenant_id', $tenantId)
            ->where('status', '1')
            ->whereHas('roles', function($query) {
                $query->where('name', 'Bayi');
            })
            ->orderBy('name', 'asc')
            ->get()
            ->map(function($bayi) {
                return [
                    'id' => (string) $bayi->user_id,
                    'adsoyad' => $bayi->name,
                ];
            })
            ->toArray();

        return $bayiler;
    }

    // ServiceController.php içine ekleyin

    public function getServiceNotes(Request $request, $servis_id)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Firma bulunamadı'
            ], 404);
        }

        // Servisin bu firmaya ait olduğunu kontrol et
        $servis = Service::where('id', $servis_id)
            ->where('firma_id', $tenant->id)
            ->first();

        if (!$servis) {
            return response()->json([
                'success' => false,
                'message' => 'Servis bulunamadı veya bu servise erişim yetkiniz yok'
            ], 404);
        }

        // Servis fiş notlarını getir
        $notlar = ServiceReceiptNote::where('servisid', $servis_id)
            ->where('firma_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($not) {
                return [
                    'id' =>  $not->id,
                    'kid' => $not->kid,
                    'servisid' => $not->servisid,
                    'aciklama' => $not->aciklama,
                    'kayitTarihi' => Carbon::parse($not->created_at)->format('Y-m-d H:i:s'),
                ];
            })
            ->toArray();

        return response()->json([
            'success' => true,
            'notlar' => $notlar,
        ], 200);
    }
}
