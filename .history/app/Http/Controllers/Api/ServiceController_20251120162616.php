<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceOptNote;
use App\Models\ServicePlanning;
use App\Models\ServiceStage;
use App\Models\ServiceStageAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        // Bugün kendisine atanan ve henüz işlem yapmadığı servisleri getir
        $atananServisIDleri = $this->getYetkiliServisIDleri($user->user_id, $tenant->id);

        if (empty($atananServisIDleri)) {
            return response()->json([
                'success' => true,
                'message' => 'Bugün size atanmış servis bulunmamaktadır',
                'data' => []
            ], 200);
        }

        // Servisleri getir
        $services = Service::with([
                'musteri:id,adSoyad,tel1,tel2,adres',
                'markaCihaz:id,marka',
                'turCihaz:id,cihaz',
                'asamalar:id,asama,asama_renk',
            ])
            ->whereIn('id', $atananServisIDleri)
            ->where('firma_id', $tenant->id)
            ->where('durum', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        // Response formatla
        $data = $services->map(function ($service) {
            return [
                'id' => $service->id,
                'musteri' => [
                    'id' => $service->musteri?->id,
                    'ad_soyad' => $service->musteri?->adSoyad,
                    'tel1' => $service->musteri?->tel1,
                    'tel2' => $service->musteri?->tel2,
                ],
                'cihaz' => [
                    'marka' => $service->markaCihaz?->marka,
                    'tur' => $service->turCihaz?->cihaz,
                    'ariza' => $service->cihazAriza,
                ],
                'asama' => [
                    'id' => $service->asamalar?->id,
                    'asama' => $service->asamalar?->asama,
                    'renk' => $service->asamalar?->asama_renk,
                ],
                'acil' => $service->acil != 0 ? true : false,
                'created_at' => $service->created_at->format('Y-m-d H:i'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $data->count()
        ], 200);
    }

    // Atanan servis detayı
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
        $atananServisIDleri = $this->getYetkiliServisIDleri($user->user_id, $tenant->id);

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

    // BUGÜN kendisine atanan servis ID'lerini getir
    private function getYetkiliServisIDleri($userId, $tenantId)
{
    $servisIzinler = [];
    $bugunStr = date('d.m.Y');
    $bugunTimestamp = strtotime($bugunStr);

    // Kullanıcıya atanan servisleri getir (Grup cevaplarından, MAX planid ile)
    $servislerTable = ServiceStageAnswer::where('firma_id', $tenantId)
        ->where('cevap', $userId)
        ->selectRaw('servisid, MAX(planid) as max_planid')
        ->groupBy('servisid')
        ->get();

    foreach ($servislerTable as $servisRow) {
        $servisid = $servisRow->servisid;
        $maxPlanid = $servisRow->max_planid;

        // Plan bilgisini al
        $plan = ServicePlanning::find($maxPlanid);
        
        if (!$plan) {
            continue;
        }

        $tarihDurum = $plan->tarihDurum ?? 0;

        // DURUM 1: Tarih VAR (tarihDurum = 1)
        if ($tarihDurum == 1) {
            // Tarih cevabını bul
            $tarihCevap = ServiceStageAnswer::where('planid', $maxPlanid)
                ->where('cevapText', '[Tarih]')
                ->first();

            if ($tarihCevap) {
                // Tarihleri karşılaştır
                $servisTarih = str_replace('/', '.', $tarihCevap->cevap);
                $tarihTimestamp = strtotime($servisTarih);

                if ($bugunTimestamp == $tarihTimestamp) {
                    // Bugün eşleşti, saat kontrolü
                    $simdikiSaat = strtotime(date('H:i'));
                    $baslangicSaati = strtotime('08:00');
                    
                    if ($simdikiSaat >= $baslangicSaati) {
                        $servisIzinler[] = $servisid;
                    }
                }
            }
        }
        // DURUM 2: Tarih YOK (tarihDurum = 0)
        else if ($tarihDurum == 0) {
            $servis = Service::find($servisid);
            
            if (!$servis) {
                continue;
            }

            // Servisin son planDurum ile eşleşiyor mu?
            if ($servis->planDurum == $maxPlanid) {
                $servisIzinler[] = $servisid;
            }

            // VEYA bugün bu kullanıcı tarafından planlama yapılmış mı?
            $planSec = ServicePlanning::find($servis->planDurum);
            
            if ($planSec && $planSec->kid == $userId) {
                $planTarihBugun = date('Y-m-d');
                $planTarih = Carbon::parse($planSec->created_at)->format('Y-m-d');
                
                if ($planTarih == $planTarihBugun) {
                    $servisIzinler[] = $servisid;
                }
            }
        }
    }

    return array_unique($servisIzinler);
}
}
