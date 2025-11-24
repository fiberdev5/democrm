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

        // Kendisine atanan ve bugün henüz işlem yapmadığı servisleri getir
        $atananServisIDleri = $this->getAtananServisIDleri($user->user_id, $tenant->id);

        if (empty($atananServisIDleri)) {
            return response()->json([
                'success' => true,
                'message' => 'Size atanmış servis bulunmamaktadır',
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

        // Servis kendisine atanmış mı kontrol et
        $atananServisIDleri = $this->getAtananServisIDleri($user->user_id, $tenant->id);

        if (!in_array($id, $atananServisIDleri)) {
            return response()->json([
                'success' => false,
                'message' => 'Bu servis size atanmamış veya üzerinde işlem yapmışsınız'
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

    // Kendisine atanan servis ID'lerini getir
    private function getAtananServisIDleri($userId, $tenantId)
    {
        // Bugünün başlangıcı
        $bugun = Carbon::today();

        // 1. Grup cevaplarından kendisine atanan servisleri bul
        $atananServisler = ServiceStageAnswer::where('firma_id', $tenantId)
            ->where('cevapText', 'LIKE', '%[Grup-%')
            ->where(function ($q) use ($userId) {
                $q->where('cevap', 'LIKE', '%' . $userId . '%')
                  ->orWhere('cevap', 'LIKE', '%[' . $userId . ']%')
                  ->orWhere('cevap', 'LIKE', '%,' . $userId . ',%')
                  ->orWhere('cevap', 'LIKE', '[' . $userId . ']%')
                  ->orWhere('cevap', 'LIKE', '%[' . $userId . ']');
            })
            ->pluck('servisid')
            ->unique()
            ->toArray();

        if (empty($atananServisler)) {
            return [];
        }

        // 2. Bu servislerden bugün içinde bu kullanıcının işlem yaptığı servisleri çıkar
        $bugunIslemYapilanServisler = ServicePlanning::whereIn('servisid', $atananServisler)
            ->where('kid', $userId)
            ->whereDate('created_at', '>=', $bugun)
            ->pluck('servisid')
            ->unique()
            ->toArray();

        // Atanan servislerden bugün işlem yapılanları çıkar
        $henuzIslemYapilmamisServisler = array_diff($atananServisler, $bugunIslemYapilanServisler);

        return array_values($henuzIslemYapilmamisServisler);
    }
}
