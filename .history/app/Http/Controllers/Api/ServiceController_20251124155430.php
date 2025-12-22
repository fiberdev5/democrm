<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CashTransaction;
use App\Models\DeviceBrand;
use App\Models\DeviceType;
use App\Models\PaymentMethod;
use App\Models\PersonelStock;
use App\Models\Service;
use App\Models\ServiceMoneyAction;
use App\Models\ServiceOptNote;
use App\Models\ServicePhoto;
use App\Models\ServicePlanning;
use App\Models\ServiceReceiptNote;
use App\Models\ServiceStage;
use App\Models\ServiceStageAnswer;
use App\Models\ServiceTime;
use App\Models\StageQuestion;
use App\Models\Stock;
use App\Models\StockAction;
use App\Models\User;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

    public function saveServicePlan(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Firma bulunamadı'
            ], 404);
        }

        // Validasyon
        $validator = Validator::make($request->all(), [
            'servis_id' => 'required|integer',
            'gelen_islem' => 'required|integer',
            'giden_islem' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz veri',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $servisId = $request->input('servis_id');
            $gelenIslem = $request->input('gelen_islem');
            $gidenIslem = $request->input('giden_islem');

            // Servis durumu kontrolü
            $servis = Service::where('firma_id', $tenant->id)
                ->where('id', $servisId)
                ->first();

            if (!$servis) {
                return response()->json([
                    'success' => false,
                    'message' => 'Servis bulunamadı veya bu servise erişim yetkiniz yok'
                ], 404);
            }

            // Stok kontrolü yap
            $stokHatasi = $this->mobilStokKontrol($request, $gelenIslem, $user->user_id, $tenant->id);
            if ($stokHatasi) {
                return response()->json([
                    'success' => false,
                    'message' => $stokHatasi
                ], 400);
            }

            DB::beginTransaction();

            // Servis planlama kaydı oluştur
            $planData = [
                'firma_id' => $tenant->id,
                'kid' => $user->user_id,
                'pid' => $user->user_id,
                'servisid' => $servisId,
                'gelenIslem' => $gelenIslem,
                'gidenIslem' => $gidenIslem,
                'tarihDurum' => 0,
                'tarihKontrol' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ];

            $planId = ServicePlanning::insertGetId($planData);

            if (!$planId) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Servis planı oluşturulamadı'
                ], 500);
            }

            // Servis durumunu güncelle
            Service::where('id', $servisId)->update([
                'servisDurum' => $gidenIslem,
                'planDurum' => $planId,
                'updated_at' => now()
            ]);

            // Soru cevaplarını kaydet
            $this->mobilSoruCevapKaydet($request, $servisId, $planId, $tenant->id, $user->user_id, $gelenIslem);

            // Özel durumları işle
            $this->mobilOzelDurumIsle($request, $servisId, $planId, $tenant->id, $gidenIslem, $servis);

            // Tarih durumu kontrolü
            $this->tarihDurumuKontrolEt($tenant->id);

            DB::commit();

            // Log kaydı
            $stageName = ServiceStage::find($gidenIslem)->asama ?? 'Bilinmeyen Aşama';
            ActivityLogger::logServicePlanAdded($servisId, $planId, $stageName);

            return response()->json([
                'success' => true,
                'message' => 'Servis planı başarıyla kaydedildi',
                'data' => [
                    'plan_id' => $planId,
                    'asama' => $stageName
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Mobil servis plan kayıt hatası: ' . $e->getMessage(), [
                'user_id' => $user->user_id,
                'servis_id' => $servisId ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mobil için stok kontrolü
     */
    private function mobilStokKontrol($request, $gelenIslem, $userId, $tenantId)
    {
        // Parça kontrolü
        if ($request->has('parca')) {
            foreach ($request->input('parca') as $soruId => $parcalar) {
                foreach ($parcalar as $parca) {
                    $stokId = $parca['stok_id'] ?? null;
                    $adet = abs($parca['adet'] ?? 0);

                    if ($stokId && $adet > 0) {
                        $personelStok = PersonelStock::where('pid', $userId)
                            ->where('stokid', $stokId)
                            ->where('firma_id', $tenantId)
                            ->first();

                        if (!$personelStok || $personelStok->adet < $adet) {
                            $stok = Stock::find($stokId);
                            $mevcutAdet = $personelStok ? $personelStok->adet : 0;
                            $urunAdi = $stok ? $stok->urunAdi : "Bilinmeyen Ürün";
                            
                            return "'{$urunAdi}' için personel stoğunuz yetersiz. Mevcut: {$mevcutAdet}, İstenen: {$adet}";
                        }
                    }
                }
            }

            // Parça teslim et kontrolü
            if ($gelenIslem == "238" && empty($request->input('parca'))) {
                return "Parça Teslim Ederken Stok Seçimi Zorunludur";
            }
        }

        // Konsinye cihaz kontrolü
        if ($request->has('konsinye_cihaz')) {
            foreach ($request->input('konsinye_cihaz') as $soruId => $cihazlar) {
                foreach ($cihazlar as $cihaz) {
                    $cihazId = $cihaz['cihaz_id'] ?? null;
                    $adet = abs($cihaz['adet'] ?? 0);

                    if ($cihazId && $adet > 0) {
                        $girisAdet = StockAction::where('stokId', $cihazId)
                            ->whereIn('islem', [1, 4])
                            ->sum('adet');
                        
                        $cikisAdet = StockAction::where('stokId', $cihazId)
                            ->where('islem', 2)
                            ->sum('adet');

                        $mevcutAdet = $girisAdet - $cikisAdet;

                        if ($adet > $mevcutAdet) {
                            $stok = Stock::find($cihazId);
                            $urunAdi = $stok ? $stok->urunAdi : "Bilinmeyen Cihaz";
                            return "'{$urunAdi}' Konsinye Cihaz Stok Adedi Yetersiz";
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Mobil için soru cevap kaydetme
     */
    private function mobilSoruCevapKaydet($request, $servisId, $planId, $tenantId, $userId, $gelenIslem)
    {
        // Normal soru cevapları (soru_302, soru_332 formatında gelenler)
        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'soru_') === 0) {
                $soruId = str_replace('soru_', '', $key);
                
                ServiceStageAnswer::create([
                    'firma_id' => $tenantId,
                    'kid' => $userId,
                    'servisid' => $servisId,
                    'planid' => $planId,
                    'soruid' => $soruId,
                    'cevap' => $value,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // Parça işlemleri
        if ($request->has('parca')) {
            foreach ($request->input('parca') as $soruId => $parcalar) {
                foreach ($parcalar as $parca) {
                    $stokId = $parca['stok_id'] ?? null;
                    $adet = abs($parca['adet'] ?? 0);

                    if ($stokId && $adet > 0) {
                        $this->mobilParcaIsle($stokId, $adet, $servisId, $planId, $tenantId, $userId, $soruId, $gelenIslem);
                    }
                }
            }
        }

        // Konsinye cihaz işlemleri
        if ($request->has('konsinye_cihaz')) {
            foreach ($request->input('konsinye_cihaz') as $soruId => $cihazlar) {
                foreach ($cihazlar as $cihaz) {
                    $cihazId = $cihaz['cihaz_id'] ?? null;
                    $adet = abs($cihaz['adet'] ?? 0);

                    if ($cihazId && $adet > 0) {
                        $this->mobilKonsinyeIsle($cihazId, $adet, $servisId, $planId, $tenantId, $userId, $soruId);
                    }
                }
            }
        }
    }

    /**
     * Mobil için parça işleme
     */
    private function mobilParcaIsle($stokId, $adet, $servisId, $planId, $tenantId, $userId, $soruId, $gelenIslem)
    {
        // Personel stoğundan düş
        $personelStok = PersonelStock::where('pid', $userId)
            ->where('stokid', $stokId)
            ->where('firma_id', $tenantId)
            ->first();

        if ($personelStok) {
            $personelStok->adet -= $adet;
            $personelStok->save();
        }

        // Stok hareketi kaydet
        StockAction::create([
            'firma_id' => $tenantId,
            'kid' => $userId,
            'pid' => $userId,
            'stokId' => $stokId,
            'islem' => 2, // Serviste kullanım
            'servisid' => $servisId,
            'adet' => $adet,
            'planId' => $planId,
            'depo' => 2,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Cevap olarak kaydet
        $cevapText = $stokId . "---" . $adet;
        
        ServiceStageAnswer::create([
            'firma_id' => $tenantId,
            'kid' => $userId,
            'servisid' => $servisId,
            'planid' => $planId,
            'soruid' => $soruId,
            'cevap' => $cevapText,
            'cevapText' => '[Parca]',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Mobil için konsinye cihaz işleme
     */
    private function mobilKonsinyeIsle($cihazId, $adet, $servisId, $planId, $tenantId, $userId, $soruId)
    {
        // Stok hareketi kaydet
        StockAction::create([
            'firma_id' => $tenantId,
            'kid' => $userId,
            'pid' => $userId,
            'stokId' => $cihazId,
            'islem' => 2, // Serviste kullanım
            'servisid' => $servisId,
            'adet' => $adet,
            'planId' => $planId,
            'depo' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Cevap olarak kaydet
        $cevapText = $cihazId . "---" . $adet;
        
        ServiceStageAnswer::create([
            'firma_id' => $tenantId,
            'kid' => $userId,
            'servisid' => $servisId,
            'planid' => $planId,
            'soruid' => $soruId,
            'cevap' => $cevapText,
            'cevapText' => '[Konsinye Cihaz]',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Mobil için özel durum işleme
     */
    private function mobilOzelDurumIsle($request, $servisId, $planId, $tenantId, $gidenIslem, $servis)
    {
        // Konsinye cihaz geri alındı (272)
        if ($gidenIslem == 272) {
            $konsinyeCihazlar = StockAction::where('servisid', $servisId)
                ->where('planId', '<>', $planId)
                ->where('islem', 2)
                ->where('firma_id', $tenantId)
                ->get();

            foreach ($konsinyeCihazlar as $cihaz) {
                $stok = Stock::find($cihaz->stokId);
                if ($stok && $stok->urunKategori == 3) {
                    $this->geriAlConsignmentDevice($cihaz->stokId, $cihaz->adet, $servisId, $planId, $tenantId);
                }
            }
        }

        // Parça teslim et (259)
        if ($gidenIslem == "259") {
            $this->parcaTeslimEtOzelDurum($servisId, $planId, $tenantId);
        }

        // Diğer özel durumlar (254)
        if ($gidenIslem == "254") {
            $planlama = ServicePlanning::where('servisid', $servisId)
                ->orderBy('id', 'desc')
                ->skip(1)
                ->first();

            if ($planlama && $planlama->gidenIslem == "255") {
                ServicePlanning::where('id', $planlama->id)->delete();
            }
        }
    }
    
    private function tarihDurumuKontrolEt($tenantId)
    {
        // Tarih durumu kontrolü - performans optimizasyonu
        $servisPlanlar = ServicePlanning::where('firma_id', $tenantId)
            ->where('tarihKontrol', '0')
            ->get();

        foreach ($servisPlanlar as $servisRow) {
            $tarihDurum = "0";
            $cevaplar = ServiceStageAnswer::where('firma_id', $tenantId)
                ->where('planid', $servisRow->id)
                ->get();

            foreach ($cevaplar as $cevapRow) {
                $soru = StageQuestion::where('id', $cevapRow->soruid)->first();

                if ($soru && $soru->cevapTuru == "[Tarih]") {
                    $tarihDurum = "1";
                    break;
                }
            }

            ServicePlanning::where('firma_id', $tenantId)
                ->where('id', $servisRow->id)
                ->update([
                    'tarihDurum' => $tarihDurum,
                    'tarihKontrol' => "1",
                    'updated_at' => now()
                ]);
        }

        // Cevap text güncelleme
        $cevaplar = ServiceStageAnswer::where('firma_id', $tenantId)
            ->whereNull('cevapText')
            ->get();

        foreach ($cevaplar as $cevapRow) {
            $soru = StageQuestion::where('id', $cevapRow->soruid)->first();

            if ($soru) {
                ServiceStageAnswer::where('firma_id', $tenantId)
                    ->where('id', $cevapRow->id)
                    ->update([
                        'cevapText' => $soru->cevapTuru,
                        'updated_at' => now()
                    ]);
            }
        }
    }

    private function geriAlConsignmentDevice($stokId, $adet, $servisId, $planId, $tenantId, $soruId = null)
    {
        // Yeni giriş işlemi
        StockAction::create([
            'firma_id' => $tenantId,
            'kid' => auth()->user()->user_id,
            'pid' => auth()->user()->user_id,
            'stokId' => $stokId,
            'islem' => 4, // Geri alma
            'servisid' => $servisId,
            'adet' => $adet,
            'planId' => $planId,
            'depo' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Cevap olarak kaydet
        if ($soruId) {
            $cevapText = $stokId . "---" . $adet;
            
            ServiceStageAnswer::create([
                'firma_id' => $tenantId,
                'servisid' => $servisId,
                'planid' => $planId,
                'soruid' => $soruId,
                'cevap' => $cevapText,
                'cevapText' => '[Konsinye Cihaz]',
                'kid' => auth()->user()->user_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    private function parcaTeslimEtOzelDurum($servisId, $planId, $tenantId)
    {
        // Serviste kullanılan parçaları bul
        $kullanılanParcalar = StockAction::where('servisid', $servisId)
            ->where('islem', 2) // Serviste kullanım
            ->where('firma_id', $tenantId)
            ->get();

        foreach ($kullanılanParcalar as $parca) {
            // Ana stoktan düş
            $stok = Stock::find($parca->stokId);
            if ($stok) {
                $stok->stokAdedi -= $parca->adet;
                $stok->save();
            }

            // Stok hareketini güncelle
            StockAction::where('id', $parca->id)->update([
                'depo' => 0, // Ana depodan çıkış
                'updated_at' => now()
            ]);
        }
    }

    public function deleteServicePlan(Request $request, $plan_id)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Firma bulunamadı'
            ], 404);
        }

        try {
            // Planı bul
            $plan = ServicePlanning::where('firma_id', $tenant->id)
                ->where('id', $plan_id)
                ->first();

            if (!$plan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Servis planı bulunamadı'
                ], 404);
            }

            // Servisi bul
            $servis = Service::where('firma_id', $tenant->id)
                ->where('id', $plan->servisid)
                ->first();

            if (!$servis) {
                return response()->json([
                    'success' => false,
                    'message' => 'Servis bulunamadı'
                ], 404);
            }

            // KONTROL 1: Bu plan kullanıcıya ait mi?
            if ($plan->pid != $user->user_id && $plan->kid != $user->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu aşamayı silemezsiniz. Bu işlemi siz yapmadınız.'
                ], 403);
            }

            // KONTROL 2: Bu plan servisin en son aşaması mı?
            if ($servis->servisDurum != $plan->gidenIslem || $servis->planDurum != $plan_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Serviste yapılan son işlem size ait olmadığı için bu aşamayı silemezsiniz'
                ], 403);
            }

            DB::beginTransaction();

            // Stok silme işlemi (gidenIslem == 259)
            if ($plan->gidenIslem == 259) {
                $stok_cevap = ServiceStageAnswer::where('firma_id', $tenant->id)
                    ->where('planid', $plan->id)
                    ->first();

                if ($stok_cevap && $stok_cevap->cevap) {
                    $stoklar = explode(', ', $stok_cevap->cevap);

                    foreach ($stoklar as $stokCevap) {
                        if (strpos($stokCevap, '---') !== false) {
                            [$stokID, $adet] = explode('---', $stokCevap);
                            
                            // Ana stoktan geri ekle
                            $stok = Stock::find($stokID);
                            if ($stok) {
                                $stok->stokAdedi += $adet;
                                $stok->save();
                            }
                        }
                    }
                }
            }

            // Ödeme silme işlemleri (gidenIslem == 267 veya 268)
            if (in_array($plan->gidenIslem, [267, 268])) {
                $servisPara = ServiceMoneyAction::where('planIslem', $plan_id)->first();
                if ($servisPara) {
                    CashTransaction::where('servisIslem', $servisPara->id)->delete();
                    $servisPara->delete();
                }
            }

            // Stokları geri al (Personel stoğuna iade)
            $stokHareketleri = StockAction::where('planId', $plan_id)->get();

            foreach ($stokHareketleri as $stok) {
                // Personel stoğunu bul ve artır
                $personelStok = PersonelStock::where('pid', $plan->pid)
                    ->where('stokid', $stok->stokId)
                    ->where('firma_id', $tenant->id)
                    ->first();

                if ($personelStok) {
                    $personelStok->increment('adet', $stok->adet);
                } else {
                    // Eğer personel stoğunda yoksa, yeni kayıt oluştur
                    PersonelStock::create([
                        'firma_id' => $tenant->id,
                        'kid' => $plan->kid ?? $user->user_id,
                        'pid' => $plan->pid,
                        'stokid' => $stok->stokId,
                        'adet' => $stok->adet,
                        'tarih' => now(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                $stok->delete();
            }

            // Log kaydı
            $stageName = ServiceStage::find($plan->gidenIslem)->asama ?? 'Bilinmeyen Aşama';
            ActivityLogger::logServicePlanDeleted($plan->servisid, $plan_id, $stageName);

            // Cevapları sil
            ServiceStageAnswer::where('planid', $plan_id)->delete();

            // Planı sil
            $plan->delete();

            // Son plan mıydı? Servis durumunu güncelle
            if ($servis->servisDurum == $plan->gidenIslem || $servis->planDurum == $plan_id) {
                $sonPlan = ServicePlanning::where('servisid', $plan->servisid)
                    ->where('firma_id', $tenant->id)
                    ->orderBy('id', 'desc')
                    ->first();

                if ($sonPlan) {
                    $servis->update([
                        'servisDurum' => $sonPlan->gidenIslem,
                        'planDurum' => $sonPlan->id,
                        'updated_at' => now()
                    ]);
                } else {
                    // İlk aşamaya geri dön
                    $ilkAsama = ServiceStage::where('ilkServis', 1)->first();
                    $servis->update([
                        'servisDurum' => $ilkAsama ? $ilkAsama->id : null,
                        'planDurum' => 0,
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            // Güncel servis bilgisini al
            $servis->refresh();
            
            // Alt aşamaları getir
            $altAsamalar = [];
            if ($servis->asamalar && $servis->asamalar->altAsamalar) {
                $altAsamaIDs = explode(',', $servis->asamalar->altAsamalar);
                $altAsamalar = ServiceStage::whereIn('id', $altAsamaIDs)
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

            return response()->json([
                'success' => true,
                'message' => 'Servis planı başarıyla silindi',
                'data' => [
                    'asama' => $servis->asamalar->asama ?? null,
                    'altAsamalar' => $altAsamalar
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Mobil servis plan silme hatası: ' . $e->getMessage(), [
                'user_id' => $user->user_id,
                'plan_id' => $plan_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Servis planı silinemedi: ' . $e->getMessage()
            ], 500);
        }
    }
   
    public function getServicePlanUpdateForm(Request $request, $plan_id)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Firma bulunamadı'
            ], 404);
        }

        try {
            // Servis planını bul
            $servisPlan = ServicePlanning::where('id', $plan_id)
                ->where('firma_id', $tenant->id)
                ->first();

            if (!$servisPlan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Servis planı bulunamadı'
                ], 404);
            }

            // Aşamayı bul
            $asama = ServiceStage::find($servisPlan->gidenIslem);

            if (!$asama) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aşama bulunamadı'
                ], 404);
            }

            // Aşamaya ait soruları getir
            $sorular = StageQuestion::where('asama', $asama->id)
                ->where(function($q) use ($tenant) {
                    $q->whereNull('firma_id')
                    ->orWhere('firma_id', $tenant->id);
                })
                ->orderBy('sira', 'asc')
                ->get();

            $altAsamalarArray = [];

            foreach ($sorular as $soru) {
                // Mevcut cevabı bul
                $mevcutCevap = ServiceStageAnswer::where('planid', $plan_id)
                    ->where('soruid', $soru->id)
                    ->where('firma_id', $tenant->id)
                    ->first();

                $inArray = [
                    'id' => (string) $soru->id,
                    'asama' => (string) $soru->asama,
                    'soru' => $soru->soru,
                    'cevap' => $soru->cevapTuru,
                    'servisid' => (string) $servisPlan->servisid,
                    'planid' => (string) $plan_id,
                    'cevapid' => $mevcutCevap ? (string) $mevcutCevap->id : null,
                ];

                // Soru tipine göre açıklama ve mevcut değer ekle
                if ($soru->cevapTuru == '[Aciklama]') {
                    $inArray['type'] = 'input';
                    $inArray['cevapText'] = $mevcutCevap ? $mevcutCevap->cevap : '';
                }
                // [Grup] - Personel seçimi
                else if (str_contains($soru->cevapTuru, 'Grup')) {
                    $inArray['type'] = 'select';
                    $inArray['aciklama'] = $this->getPersonelListByGroup($soru->cevapTuru, $tenant->id);
                    $inArray['cevapText'] = $mevcutCevap ? $mevcutCevap->cevap : '';
                }
                // [Tarih] - Tarih seçici
                else if ($soru->cevapTuru == '[Tarih]') {
                    $inArray['type'] = 'datepicker';
                    $inArray['aciklama'] = $this->getDefaultDate();
                    $inArray['cevapText'] = $mevcutCevap ? $mevcutCevap->cevap : '';
                }
                // [Saat] - Saat aralığı seçimi
                else if ($soru->cevapTuru == '[Saat]') {
                    $inArray['type'] = 'timepicker';
                    $inArray['aciklama'] = "08:00-10:00,09:00-11:00,10:00-12:00,11:00-13:00,12:00-14:00,13:00-15:00,14:00-16:00,15:00-17:00,16:00-18:00,17:00-19:00,18:00-20:00,19:00-21:00,20:00-22:00,21:00-23:00";
                    $inArray['cevapText'] = $mevcutCevap ? $mevcutCevap->cevap : '';
                }
                // [Arac] - Araç seçimi
                else if ($soru->cevapTuru == '[Arac]') {
                    $inArray['type'] = 'select';
                    $inArray['aciklama'] = $this->getAracList($tenant->id);
                    $inArray['cevapText'] = $mevcutCevap ? $mevcutCevap->cevap : '';
                }
                // [Fiyat] - Para input
                else if ($soru->cevapTuru == '[Fiyat]') {
                    $inArray['type'] = 'money';
                    $inArray['cevapText'] = $mevcutCevap ? $mevcutCevap->cevap : '';
                }
                // [Teklif] - Teklif input
                else if ($soru->cevapTuru == '[Teklif]') {
                    $inArray['type'] = 'money';
                    $inArray['cevapText'] = $mevcutCevap ? $mevcutCevap->cevap : '';
                }
                // [Parca] - Stok listesi
                else if ($soru->cevapTuru == '[Parca]') {
                    $inArray['type'] = 'checkbox';
                    $inArray['aciklama'] = $this->getPersonelStokList($user->user_id, $tenant->id);
                    
                    // Mevcut seçili parçaları parse et
                    if ($mevcutCevap && $mevcutCevap->cevap) {
                        $parcaArray = [];
                        $inParcalar = explode(", ", $mevcutCevap->cevap);
                        
                        foreach ($inParcalar as $parca) {
                            if (strpos($parca, '---') !== false) {
                                $parcaSec = explode("---", $parca);
                                $parcaArray[] = [
                                    'id' => (string) $parcaSec[0],
                                    'adet' => (string) $parcaSec[1]
                                ];
                            }
                        }
                        $inArray['cevapText'] = $parcaArray;
                    } else {
                        $inArray['cevapText'] = [];
                    }
                }
                // [Konsinye Cihaz] - Konsinye cihaz listesi
                else if ($soru->cevapTuru == '[Konsinye Cihaz]') {
                    $inArray['type'] = 'checkbox';
                    $inArray['aciklama'] = $this->getKonsinyeCihazList($user->user_id, $tenant->id);
                    
                    // Mevcut seçili konsinye cihazları parse et
                    if ($mevcutCevap && $mevcutCevap->cevap) {
                        $cihazArray = [];
                        $inCihazlar = explode(", ", $mevcutCevap->cevap);
                        
                        foreach ($inCihazlar as $cihaz) {
                            if (strpos($cihaz, '---') !== false) {
                                $cihazSec = explode("---", $cihaz);
                                $cihazArray[] = [
                                    'id' => (string) $cihazSec[0],
                                    'adet' => (string) $cihazSec[1]
                                ];
                            }
                        }
                        $inArray['cevapText'] = $cihazArray;
                    } else {
                        $inArray['cevapText'] = [];
                    }
                }
                // [Bayi] - Bayi seçimi
                else if ($soru->cevapTuru == '[Bayi]') {
                    $inArray['type'] = 'select';
                    $inArray['aciklama'] = $this->getBayiList($tenant->id);
                    $inArray['cevapText'] = $mevcutCevap ? $mevcutCevap->cevap : '';
                }

                $altAsamalarArray[] = $inArray;
            }

            return response()->json([
                'success' => true,
                'altAsamalar' => $altAsamalarArray
            ], 200);

        } catch (\Exception $e) {
            Log::error('Servis plan güncelleme formu hatası: ' . $e->getMessage(), [
                'user_id' => $user->user_id,
                'plan_id' => $plan_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function updateServicePlan(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Firma bulunamadı'
            ], 404);
        }

        // Validasyon
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz veri',
                'errors' => $validator->errors()
            ], 422);
        }

        $planId = $request->input('plan_id');

        try {
            // Servis planını bul
            $servisPlan = ServicePlanning::where('id', $planId)
                ->where('firma_id', $tenant->id)
                ->first();

            if (!$servisPlan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plan bulunamadı'
                ], 404);
            }

            DB::beginTransaction();

            // Plan işlemi yapan kişiyi güncelle (opsiyonel)
            if ($request->has('plan_islemi_yapan')) {
                $yeniPid = $request->input('plan_islemi_yapan');
                
                // Yeni personelin bu firmaya ait olduğunu kontrol et
                $yeniPersonel = User::where('user_id', $yeniPid)
                    ->where('tenant_id', $tenant->id)
                    ->where('status', '1')
                    ->first();

                if ($yeniPersonel) {
                    $servisPlan->pid = $yeniPid;
                    $servisPlan->updated_at = now();
                    $servisPlan->save();
                }
            }

            // Plan cevaplarını getir
            $planCevaplar = ServiceStageAnswer::where('firma_id', $tenant->id)
                ->where('planid', $planId)
                ->get();

            $guncellenenSayisi = 0;

            foreach ($planCevaplar as $cevap) {
                // Mobil formatı: cevap_{id} veya soru_{id}
                $cevapKey = 'cevap_' . $cevap->id;
                $soruKey = 'soru_' . $cevap->id;

                $yeniCevap = null;

                // İki formatı da destekle
                if ($request->has($cevapKey)) {
                    $yeniCevap = $request->input($cevapKey);
                } else if ($request->has($soruKey)) {
                    $yeniCevap = $request->input($soruKey);
                }

                if ($yeniCevap !== null) {
                    // PARÇA VE KONSİNYE İÇİN MEVCUT CEVABI KORU
                    if ($yeniCevap == 'Parca' || $yeniCevap == 'Konsinye Cihaz') {
                        // Cevap değişmez, mevcut parça/konsinye seçimi korunur
                        continue;
                    } else {
                        // Diğer cevaplar normal şekilde güncellenir
                        $cevap->cevap = $yeniCevap;
                        $cevap->updated_at = now();
                        $cevap->save();
                        $guncellenenSayisi++;
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Plan başarıyla güncellendi',
                'data' => [
                    'servis_id' => $servisPlan->servisid,
                    'plan_id' => $planId,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Mobil servis plan güncelleme hatası: ' . $e->getMessage(), [
                'user_id' => $user->user_id,
                'plan_id' => $planId ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Güncelleme sırasında hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

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

    public function addServiceNote(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Firma bulunamadı'
            ], 404);
        }

        // Validasyon
        $validator = Validator::make($request->all(), [
            'servis_id' => 'required|integer',
            'aciklama' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz veri',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $servisId = $request->input('servis_id');
            $aciklama = $request->input('aciklama');

            // Servisin bu firmaya ait olduğunu kontrol et
            $servis = Service::where('id', $servisId)
                ->where('firma_id', $tenant->id)
                ->first();

            if (!$servis) {
                return response()->json([
                    'success' => false,
                    'message' => 'Servis bulunamadı veya bu servise erişim yetkiniz yok'
                ], 404);
            }

            // Servis fiş notu oluştur
            $receiptNote = ServiceReceiptNote::create([
                'firma_id' => $tenant->id,
                'kid' => $user->user_id,
                'servisid' => $servisId,
                'aciklama' => $aciklama,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Log kaydı
            ActivityLogger::logServiceNoteAdded($servisId, 'receipt', $receiptNote->id);

            // Not bilgisini formatla
            $noteData = [
                'id' => $receiptNote->id,
                'not' => $receiptNote->aciklama,
                'user' => $user->name,
                'created_at' => $receiptNote->created_at->format('Y-m-d H:i'),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Servis fiş notu başarıyla eklendi',
                'data' => $noteData
            ], 201);

        } catch (\Exception $e) {
            Log::error('Mobil servis fiş notu ekleme hatası: ' . $e->getMessage(), [
                'user_id' => $user->user_id,
                'servis_id' => $request->input('servis_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Not eklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteServiceNote(Request $request, $note_id)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Firma bulunamadı'
            ], 404);
        }

        try {
            // Servis fiş notunu bul
            $serviceNote = ServiceReceiptNote::where('firma_id', $tenant->id)
                ->where('id', $note_id)
                ->first();

            if (!$serviceNote) {
                return response()->json([
                    'success' => false,
                    'message' => 'Servis fiş notu bulunamadı'
                ], 404);
            }

            // GÜVENLİK KONTROLÜ: Kullanıcı bu notu silebilir mi?
            // Sadece kendi eklediği notları silebilir (veya admin yetkisi varsa)
            if ($serviceNote->kid != $user->user_id) {
                // Eğer admin/süpervizör kontrolü eklemek isterseniz:
                // if ($serviceNote->kid != $user->user_id && !$user->hasRole(['Admin', 'Supervisor'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu notu silme yetkiniz yok'
                ], 403);
            }

            // Log için bilgileri sakla
            $servisId = $serviceNote->servisid;
            $noteContent = $serviceNote->aciklama;

            // Notu sil
            $serviceNote->delete();

            // Log kaydı
            ActivityLogger::log(
                $tenant->id,
                $user->user_id,
                'service_note_deleted',
                "Servis fiş notu silindi",
                [
                    'servis_id' => $servisId,
                    'note_id' => $note_id,
                    'deleted_content' => $noteContent
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Servis fiş notu başarıyla silindi'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Mobil servis fiş notu silme hatası: ' . $e->getMessage(), [
                'user_id' => $user->user_id,
                'note_id' => $note_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Fiş notu silinirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getServicePhotos(Request $request, $servis_id)
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

        // Servis fotoğraflarını getir
        $fotolar = ServicePhoto::where('servisid', $servis_id)
            ->where('firma_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($foto) {
                // Base URL'i config'den al veya sabit tanımla
                $baseUrl = config('app.url'); // veya env('APP_URL')
                
                // Tam URL oluştur
                $fullUrl = $baseUrl . '/storage/' . $foto->resimyol;
                
                return [
                    'id' => (string) $foto->id,
                    'servisid' => (string) $foto->servisid,
                    'resimyol' => $fullUrl,
                ];
            })
            ->toArray();

        return response()->json([
            'success' => true,
            'resimler' => $fotolar,
        ], 200);
    }

    public function getPaymentMethods(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Firma bulunamadı'
            ], 404);
        }

        // Firmaya özel veya genel ödeme şekillerini getir
        $odemeSekilleri = PaymentMethod::where(function($query) use ($tenant) {
                $query->whereNull('firma_id') // Genel ödeme şekilleri
                    ->orWhere('firma_id', $tenant->id); // Firmaya özel
            })
            ->orderBy('odemeSekli', 'asc')
            ->get()
            ->map(function($odeme) {
                return [
                    'id' => (string) $odeme->id,
                    'odemeSekli' => $odeme->odemeSekli,
                ];
            })
            ->toArray();

        return response()->json([
            'success' => true,
            'odemeSekilleri' => $odemeSekilleri,
        ], 200);
    }

    public function getServicePayments(Request $request, $servis_id)
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

        // Para hareketlerini getir
        $paraHareketleri = DB::table('service_money_actions as so')
            ->leftJoin('payment_methods as os', 'os.id', '=', 'so.odemeSekli')
            ->leftJoin('tb_user as u', 'u.user_id', '=', 'so.pid')
            ->where('so.servisid', $servis_id)
            ->where('so.firma_id', $tenant->id)
            ->select([
                'so.id',
                'so.servisid',
                'so.odemeSekli',
                'os.odemeSekli as sekli',
                'so.odemeDurum',
                'so.fiyat',
                'so.aciklama',
                'so.odemeYonu',
                'so.created_at',
                'u.name as adsoyad'
            ])
            ->orderBy('so.created_at', 'desc')
            ->get()
            ->map(function($odeme) {
                return [
                    'id' => (string) $odeme->id,
                    'servisid' => (string) $odeme->servisid,
                    'tarih' => Carbon::parse($odeme->created_at)->format('d/m/Y H:i'),
                    'odemeSekli' => (string) $odeme->odemeSekli,
                    'sekli' => $odeme->sekli ?? 'Belirtilmemiş',
                    'odemeDurum' => $odeme->odemeDurum ?? '',
                    'fiyat' => number_format($odeme->fiyat, 2, '.', ''),
                    'aciklama' => $odeme->aciklama ?? '',
                    'odemeYonu' => (string) $odeme->odemeYonu,
                    'adsoyad' => $odeme->adsoyad ?? 'Bilinmiyor',
                ];
            })
            ->toArray();

        // Toplam tutarı hesapla
        $toplam = ServiceMoneyAction::where('servisid', $servis_id)
            ->where('firma_id', $tenant->id)
            ->sum('fiyat');

        return response()->json([
            'success' => true,
            'toplam' => number_format($toplam, 2, '.', ''),
            'para_hareketleri' => $paraHareketleri,
        ], 200);
    }

    // Cihaz Markaları
    public function getDeviceBrands(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Firma bulunamadı'
            ], 404);
        }

        // Cihaz markalarını getir
        $markalar = DeviceBrand::where(function($query) use ($tenant) {
                $query->whereNull('firma_id') 
                    ->orWhere('firma_id', $tenant->id); 
            })
            ->select([
                'id',
                'firma_id as kid',
                'marka',
                'aciklama',
                'servisUcreti as ucret',
                'operatorPrim as optPrim',
                'atolyePrim as atyPrim'
            ])
            ->orderBy('marka', 'asc')
            ->get()
            ->map(function($marka) {
                return [
                    'id' => (string) $marka->id,
                    'kid' => (string) ($marka->kid ?? ''),
                    'marka' => $marka->marka,
                    'aciklama' => $marka->aciklama ?? '',
                    'ucret' => $marka->ucret,
                    'optPrim' => number_format($marka->optPrim, 2, '.', ''),
                    'atyPrim' => number_format($marka->atyPrim, 2, '.', ''),
                ];
            })
            ->toArray();

        return response()->json([
            'success' => true,
            'markalar' => $markalar,
        ], 200);
    }

    //  Cihaz Türleri
    public function getDeviceTypes(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Firma bulunamadı'
            ], 404);
        }

        // Cihaz türlerini getir
        $turler = DeviceType::where(function($query) use ($tenant) {
                $query->whereNull('firma_id') // Genel türler
                    ->orWhere('firma_id', $tenant->id); // Firmaya özel
            })
            ->select([
                'id',
                'firma_id as kid',
                'cihaz',
                'operatorPrim as optPrim',
                'atolyePrim as atyPrim'
            ])
            ->orderBy('cihaz', 'asc')
            ->get()
            ->map(function($tur) {
                return [
                    'id' => (string) $tur->id,
                    'kid' => (string) ($tur->kid ?? ''),
                    'cihaz' => $tur->cihaz,
                    'optPrim' => number_format($tur->optPrim, 2, '.', ''),
                    'atyPrim' => number_format($tur->atyPrim, 2, '.', ''),
                ];
            })
            ->toArray();

        return response()->json([
            'success' => true,
            'turler' => $turler,
        ], 200);
    }


}
