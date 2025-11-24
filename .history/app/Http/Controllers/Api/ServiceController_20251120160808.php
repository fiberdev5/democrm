<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Service;
use App\Models\ServiceOptNote;
use App\Models\ServicePlanning;
use App\Models\ServiceStage;
use App\Models\ServiceStageAnswer;
use App\Models\StageQuestion;
use App\Models\Stock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    // Kendime atanan servisleri listele
    public function myAssignedServices(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Firma bulunamadı'
            ], 404);
        }

        // Yetkili servis ID'lerini al
        $servisIDleri = $this->getYetkiliServisIDleri($user->user_id, $tenant->id);

        if (empty($servisIDleri)) {
            return response()->json([
                'success' => true,
                'message' => 'Size atanmış servis bulunmamaktadır',
                'data' => []
            ], 200);
        }

        // Servisleri getir
        $services = Service::with([
                'musteri:id,adSoyad,tel1,tel2,adres,il,ilce',
                'markaCihaz:id,marka',
                'turCihaz:id,cihaz',
                'asamalar:id,asama,asama_renk',
            ])
            ->whereIn('id', $servisIDleri)
            ->where('firma_id', $tenant->id)
            ->where('durum', 1)
            ->orderBy('id', 'desc')
            ->get();

        // Response formatla
        $data = $services->map(function ($service) use ($tenant) {
            return [
                'id' => $service->id,
                'musteri' => [
                    'id' => $service->musteri?->id,
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
                'asama' => [
                    'id' => $service->asamalar?->id,
                    'asama' => $service->asamalar?->asama,
                    'renk' => $service->asamalar?->asama_renk,
                ],
                'acil' => $service->acil != 0,
                'kayit_tarihi' => $service->created_at->format('Y-m-d H:i'),
                'renk' => $this->hesaplaServisRenk($service->id, $tenant->id),
                'asama_detay' => $this->getAsamaDetaylari($service->planDurum, $tenant->id),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $data->count()
        ], 200);
    }

    // Servis detayı
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

        // Yetkili mi kontrol et
        $servisIDleri = $this->getYetkiliServisIDleri($user->user_id, $tenant->id);

        if (!in_array($id, $servisIDleri)) {
            return response()->json([
                'success' => false,
                'message' => 'Bu servise erişim yetkiniz yok'
            ], 403);
        }

        // Servisi getir
        $servis = Service::with([
            'asamalar',
            'musteri',
            'markaCihaz',
            'turCihaz',
            'warranty',
        ])->where('firma_id', $tenant->id)->find($id);

        if (!$servis) {
            return response()->json([
                'success' => false,
                'message' => 'Servis bulunamadı'
            ], 404);
        }

        // Alt aşamalar
        $altAsamalar = [];
        if ($servis->asamalar && $servis->asamalar->altAsamalar) {
            $altAsamaIds = explode(',', $servis->asamalar->altAsamalar);
            $altAsamalar = ServiceStage::whereIn('id', $altAsamaIds)
                ->orderBy('asama')
                ->get()
                ->map(fn($asama) => [
                    'id' => $asama->id,
                    'asama' => $asama->asama,
                    'asama_renk' => $asama->asama_renk,
                ]);
        }

        // Eski işlemler
        $eskiIslemler = ServicePlanning::where('servisid', $id)
            ->with('user:user_id,name')
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn($planning) => [
                'id' => $planning->id,
                'tarih' => $planning->tarih,
                'saat' => $planning->saat,
                'aciklama' => $planning->aciklama,
                'durum' => $planning->durum,
                'user' => $planning->user?->name,
                'created_at' => $planning->created_at->format('Y-m-d H:i'),
            ]);

        // Garanti bilgisi
        $garantiInfo = null;
        if ($servis->warranty && $servis->warranty->garanti) {
            $garantiBitis = Carbon::parse($servis->created_at)->addMonths($servis->warranty->garanti);
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
            ->map(fn($note) => [
                'id' => $note->id,
                'not' => $note->not,
                'user' => $note->user?->name,
                'created_at' => $note->created_at->format('Y-m-d H:i'),
            ]);

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
                    'acil' => $servis->acil != 0,
                    'musait_tarih' => $servis->musaitTarih,
                    'created_at' => $servis->created_at->format('Y-m-d H:i'),
                ],
                'alt_asamalar' => $altAsamalar,
                'eski_islemler' => $eskiIslemler,
                'garanti' => $garantiInfo,
                'notlar' => $servisNotlari,
                'asama_detay' => $this->getAsamaDetaylari($servis->planDurum, $tenant->id),
            ]
        ], 200);
    }

    // Yetkili servis ID'lerini getir (PHP mantığıyla)
   private function getYetkiliServisIDleri($userId, $tenantId)
{
    $servisIzinler = [];
    $bugunStr = now();
    $bugunTimestamp = strtotime($bugunStr);

    // DEBUG: İlk sorguyu kontrol et
    $maxPlanIds = ServiceStageAnswer::select([
            'servisid',

        ])
        ->where('firma_id', $tenantId)
        ->where('cevapText', 'LIKE', '%[Grup-%')
        ->where(function ($q) use ($userId) {
            $q->where('cevap', 'LIKE', '%' . $userId . '%')
              ->orWhere('cevap', 'LIKE', '%[' . $userId . ']%')
              ->orWhere('cevap', 'LIKE', '%,' . $userId . ',%')
              ->orWhere('cevap', 'LIKE', '[' . $userId . ']%')
              ->orWhere('cevap', 'LIKE', '%[' . $userId . ']');
        })
        ->get();

    // DEBUG LOG
    Log::info('User ID: ' . $userId);
    Log::info('Tenant ID: ' . $tenantId);
    Log::info('Max Plan IDs:', $maxPlanIds->toArray());

    foreach ($maxPlanIds as $item) {
        $servisid = $item->servisid;
        $maxPlanid = $item->max_planid;

        Log::info("İşleniyor - Servis: $servisid, Plan: $maxPlanid");

        // Bu servis için cevapları getir
        $cevap = ServiceStageAnswer::where('firma_id', $tenantId)
            ->where('servisid', $servisid)
            ->where('planid', $maxPlanid)
            ->where('cevapText', 'LIKE', '%[Grup-%')
            ->where(function ($q) use ($userId) {
                $q->where('cevap', 'LIKE', '%' . $userId . '%')
                  ->orWhere('cevap', 'LIKE', '%[' . $userId . ']%')
                  ->orWhere('cevap', 'LIKE', '%,' . $userId . ',%')
                  ->orWhere('cevap', 'LIKE', '[' . $userId . ']%')
                  ->orWhere('cevap', 'LIKE', '%[' . $userId . ']');
            })
            ->first();

        if (!$cevap) {
            Log::info("Cevap bulunamadı");
            continue;
        }

        Log::info("Cevap bulundu:", $cevap->toArray());

        // Soruda Grup var mı kontrol et
        $perIDKontrol = StageQuestion::find($cevap->soruid);
        
        if (!$perIDKontrol) {
            Log::info("Soru bulunamadı: " . $cevap->soruid);
            continue;
        }

        Log::info("Soru cevap: " . $perIDKontrol->cevap);

        if (!str_contains($perIDKontrol->cevap, 'Grup')) {
            Log::info("Soruda Grup yok");
            continue;
        }

        // Plan bilgisini al
        $plan = ServicePlanning::find($maxPlanid);
        
        if (!$plan) {
            Log::info("Plan bulunamadı: " . $maxPlanid);
            continue;
        }

        Log::info("Plan tarihDurum: " . ($plan->tarihDurum ?? 'null'));

        // Tarih durumu kontrolü
        $tarihDurum = $plan->tarihDurum ?? 0;

        // DURUM 1: Tarih VAR (tarihDurum = 1)
        if ($tarihDurum == 1) {
            Log::info("Tarih durumu 1 - Tarih kontrolü yapılıyor");
            
            // Tarih cevabını bul
            $tarihCevap = ServiceStageAnswer::where('planid', $maxPlanid)
                ->where('cevapText', '[Tarih]')
                ->first();

            if ($tarihCevap) {
                Log::info("Tarih cevap: " . $tarihCevap->cevap);
                
                // Tarihleri karşılaştır
                $servisTarih = str_replace('/', '.', $tarihCevap->cevap);
                $tarihTimestamp = strtotime($servisTarih);

                Log::info("Bugün: $bugunStr ($bugunTimestamp) - Servis tarihi: $servisTarih ($tarihTimestamp)");

                if ($bugunTimestamp == $tarihTimestamp) {
                    Log::info("Tarihler eşleşti");
                    
                    // Bugün eşleşti, saat kontrolü yap
                    $simdikiSaat = strtotime(date('H.i'));
                    $baslangicSaati = strtotime('08:00');
                    
                    Log::info("Şimdiki saat: " . date('H:i') . " ($simdikiSaat) - Başlangıç: 08:00 ($baslangicSaati)");
                    
                    if ($simdikiSaat >= $baslangicSaati) {
                        Log::info("✓ Servis eklendi: $servisid");
                        $servisIzinler[] = $servisid;
                    } else {
                        Log::info("Henüz erken (08:00'dan önce)");
                    }
                } else {
                    Log::info("Tarihler eşleşmedi");
                }
            } else {
                Log::info("Tarih cevabı bulunamadı");
            }
        }
        // DURUM 2: Tarih YOK (tarihDurum = 0)
        else if ($tarihDurum == 0) {
            Log::info("Tarih durumu 0 - Plan kontrolü yapılıyor");
            
            // Servisin son planid'si ile eşleşiyor mu?
            $servis = Service::find($servisid);
            
            if ($servis) {
                Log::info("Servis planDurum: " . $servis->planDurum . " - Max planid: $maxPlanid");
                
                if ($servis->planDurum == $maxPlanid) {
                    Log::info("✓ Servis eklendi (planDurum eşleşti): $servisid");
                    $servisIzinler[] = $servisid;
                }

                // VEYA bugün bu kullanıcı tarafından planlama yapılmış mı?
                $planSec = ServicePlanning::find($servis->planDurum);
                
                if ($planSec && $planSec->kid == $userId) {
                    $planTarihBugun = date('Y-m-d');
                    $planTarih = Carbon::parse($planSec->created_at)->format('Y-m-d');
                    
                    Log::info("Plan tarihi: $planTarih - Bugün: $planTarihBugun");
                    
                    if ($planTarih == $planTarihBugun) {
                        Log::info("✓ Servis eklendi (bugün planlama): $servisid");
                        $servisIzinler[] = $servisid;
                    }
                }
            } else {
                Log::info("Servis bulunamadı: $servisid");
            }
        }
    }

    Log::info("Toplam yetkili servisler:", $servisIzinler);

    return array_unique($servisIzinler);
}

    // Servis renk hesaplama (şikayet durumuna göre)
    private function hesaplaServisRenk($servisId, $tenantId)
    {
        $renk = '';

        // Şikayet kontrolü (gidenIslem = 257)
        $sikayetVar = ServicePlanning::where('servisid', $servisId)
            ->where('gidenIslem', 257)
            ->exists();

        if ($sikayetVar) {
            return '62daff'; // Mavi
        }

        // Tekrar gelen sayısı (gidenIslem = 254)
        $tekrarGelenSayisi = ServicePlanning::where('servisid', $servisId)
            ->where('gidenIslem', 254)
            ->count();

        if ($tekrarGelenSayisi == 1) {
            $renk = 'ffdf40'; // Sarı
        } else if ($tekrarGelenSayisi == 2) {
            $renk = 'ff8c00'; // Turuncu
        } else if ($tekrarGelenSayisi == 3) {
            $renk = 'ff0000'; // Kırmızı
        } else if ($tekrarGelenSayisi > 3) {
            $renk = 'cf0000'; // Koyu kırmızı
        }

        return $renk;
    }

    // Aşama detaylarını getir
    private function getAsamaDetaylari($planId, $tenantId)
    {
        if (!$planId) {
            return [];
        }

        $aciklamalar = ServiceStageAnswer::where('planid', $planId)
            ->orderBy('id', 'asc')
            ->get();

        if ($aciklamalar->isEmpty()) {
            return [];
        }

        $detaylar = [];

        foreach ($aciklamalar as $aciklama) {
            if (empty($aciklama->cevap)) {
                continue;
            }

            $soru = StageQuestion::find($aciklama->soruid);
            
            if (!$soru) {
                continue;
            }

            // Grup bilgisi
            if (str_contains($soru->cevap, 'Grup')) {
                $personel = User::where('user_id', $aciklama->cevap)->first();
                $detaylar[$soru->soru] = $personel?->name ?? '';
            }
            // Araç bilgisi
            else if ($soru->cevap == '[Arac]') {
                $arac = Car::find($aciklama->cevap);
                $detaylar[$soru->soru] = $arac?->arac ?? '';
            }
            // Parça bilgisi
            else if ($soru->cevap == '[Parca]') {
                $parcaCevaplar = '';
                $parcalar = explode(', ', $aciklama->cevap);
                
                foreach ($parcalar as $parca) {
                    $parcaParts = explode('---', $parca);
                    if (count($parcaParts) >= 2) {
                        $parcaId = $parcaParts[0];
                        $adet = $parcaParts[1];
                        $stok = Stock::find($parcaId);
                        
                        if ($stok) {
                            $parcaCevaplar .= $stok->urunAdi . ' (' . $adet . '), ';
                        }
                    }
                }
                
                $detaylar[$soru->soru] = rtrim($parcaCevaplar, ', ');
            }
            // Diğer cevaplar
            else {
                $detaylar[$soru->soru] = $aciklama->cevap;
            }
        }

        return $detaylar;
    }
}
