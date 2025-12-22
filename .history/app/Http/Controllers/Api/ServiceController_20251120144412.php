<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceOptNote;
use App\Models\ServicePlanning;
use App\Models\ServiceStage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // Servisleri listele
    public function index(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Firma bulunamadı'
            ], 404);
        }

        // Servis sorgusu
        $query = Service::query()
            ->with([
                'musteri:id,adSoyad,tel1,tel2,adres',
                'markaCihaz:id,marka',
                'turCihaz:id,cihaz',
                'asamalar:id,asama,asama_renk',
                'users:user_id,name',
            ])
            ->where('firma_id', $tenant->id)
            ->where('durum', 1);

        // Yetki kontrolü - Sadece kendi servislerini görebilir mi?
        if ($user->can('Kendi Servislerini Görebilir')) {
            $servisIDleri = $this->getYetkiliServisIDleri($user, $tenant->id);
            $query->whereIn('id', $servisIDleri);
        }

        // Filtreleme parametreleri
        $this->applyFilters($query, $request);

        // Sıralama
        $orderBy = $request->input('order_by', 'created_at');
        $orderDirection = $request->input('order_direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);

        // Sayfalama
        $perPage = $request->input('per_page', 20);
        $services = $query->paginate($perPage);

        // Response formatla
        $data = $services->map(function ($service) {
            return $this->formatService($service);
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'current_page' => $services->currentPage(),
                'last_page' => $services->lastPage(),
                'per_page' => $services->perPage(),
                'total' => $services->total(),
                'from' => $services->firstItem(),
                'to' => $services->lastItem(),
            ]
        ], 200);
    }

    // Tek servis detayı
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Firma bulunamadı'
            ], 404);
        }

        // Servisi getir
        $servis = Service::with([
            'asamalar',
            'musteri',
            'markaCihaz',
            'turCihaz',
            'warranty',
            'cevaplar.question'
        ])
        ->where('firma_id', $tenant->id)
        ->find($id);

        if (!$servis) {
            return response()->json([
                'success' => false,
                'message' => 'Servis bulunamadı'
            ], 404);
        }

        // Yetki kontrolü
        if ($user->can('Kendi Servislerini Görebilir')) {
            $servisIDleri = $this->getYetkiliServisIDleri($user, $tenant->id);
            if (!in_array($id, $servisIDleri)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu servise erişim yetkiniz yok'
                ], 403);
            }
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

        // Eski işlemler (servis planlamaları)
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
        $garantiInfo = $this->calculateGaranti($servis);

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
                'servis' => $this->formatServiceDetail($servis),
                'alt_asamalar' => $altAsamalar,
                'eski_islemler' => $eskiIslemler,
                'garanti' => $garantiInfo,
                'notlar' => $servisNotlari,
                'acil_durum' => $servis->acil != 0 ? true : false,
            ]
        ], 200);
    }

    // Yetkili servis ID'lerini getir (web controller'dan)
    private function getYetkiliServisIDleri($user, $tenant_id)
    {
        // Bu methodu web controller'ınızdan kopyalayın
        // Örnek implementasyon:
        $servisIDleri = [];

        // Teknisyen ise kendisine atanan servisler
        if ($user->hasRole('Teknisyen')) {
            $servisIDleri = Service::where('firma_id', $tenant_id)
                ->where('teknisyen', $user->user_id)
                ->where('durum', 1)
                ->pluck('id')
                ->toArray();
        }

        // Bayi ise kendi bölgesindeki servisler
        if ($user->hasRole('Bayi')) {
            $servisIDleri = Service::where('firma_id', $tenant_id)
                ->where('bayi_id', $user->user_id)
                ->where('durum', 1)
                ->pluck('id')
                ->toArray();
        }

        // Operatör ise kendisinin kaydettiği servisler
        if ($user->hasRole('Operatör')) {
            $servisIDleri = Service::where('firma_id', $tenant_id)
                ->where('kayitAlan', $user->user_id)
                ->where('durum', 1)
                ->pluck('id')
                ->toArray();
        }

        return $servisIDleri;
    }

    // Filtreleme uygula
    private function applyFilters($query, $request)
    {
        // Tarih filtresi
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Aşama filtresi
        if ($request->filled('stage_id')) {
            $query->where('servisDurum', $request->stage_id);
        }

        // Cihaz markası filtresi
        if ($request->filled('brand_id')) {
            $query->where('cihazMarka', $request->brand_id);
        }

        // Cihaz türü filtresi
        if ($request->filled('device_type_id')) {
            $query->where('cihazTur', $request->device_type_id);
        }

        // Acil servis filtresi
        if ($request->filled('acil')) {
            $query->where('acil', $request->acil);
        }

        // Arama (müşteri adı, telefon, servis id)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%$search%")
                    ->orWhereHas('musteri', function ($q) use ($search) {
                        $q->where('adSoyad', 'LIKE', "%$search%")
                            ->orWhere('tel1', 'LIKE', "%$search%")
                            ->orWhere('tel2', 'LIKE', "%$search%");
                    });
            });
        }
    }

    // Servis formatla (liste için)
    private function formatService($service)
    {
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
            'kayit_alan' => $service->users?->name,
            'acil' => $service->acil != 0 ? true : false,
            'created_at' => $service->created_at->format('Y-m-d H:i'),
        ];
    }

    // Servis detay formatla
    private function formatServiceDetail($service)
    {
        return [
            'id' => $service->id,
            'musteri' => [
                'id' => $service->musteri?->id,
                'ad_soyad' => $service->musteri?->adSoyad,
                'tel1' => $service->musteri?->tel1,
                'tel2' => $service->musteri?->tel2,
                'adres' => $service->musteri?->adres,
            ],
            'cihaz' => [
                'marka' => $service->markaCihaz?->marka,
                'marka_id' => $service->cihazMarka,
                'tur' => $service->turCihaz?->cihaz,
                'tur_id' => $service->cihazTur,
                'model' => $service->cihazModel,
                'seri_no' => $service->cihazSeriNo,
                'ariza' => $service->cihazAriza,
                'cihaz_sifresi' => $service->cihazSifresi,
                'cihaz_deseni' => $service->cihazDeseni,
            ],
            'asama' => [
                'id' => $service->asamalar?->id,
                'asama' => $service->asamalar?->asama,
                'renk' => $service->asamalar?->asama_renk,
            ],
            'kayit_alan' => $service->users?->name,
            'acil' => $service->acil != 0 ? true : false,
            'musait_tarih' => $service->musaitTarih,
            'created_at' => $service->created_at->format('Y-m-d H:i'),
            'updated_at' => $service->updated_at->format('Y-m-d H:i'),
        ];
    }

    // Garanti hesapla
    private function calculateGaranti($servis)
    {
        if (!$servis->warranty || !$servis->warranty->garanti) {
            return [
                'garanti_var' => false,
                'garanti_bitis' => null,
                'kalan_gun' => -1,
                'garanti_gecerli' => false,
            ];
        }

        $garantiBitis = Carbon::parse($servis->created_at)
            ->addMonths($servis->warranty->garanti);

        $kalanGun = Carbon::now()->diffInDays($garantiBitis, false);

        return [
            'garanti_var' => true,
            'garanti_suresi' => $servis->warranty->garanti . ' ay',
            'garanti_bitis' => $garantiBitis->format('Y-m-d'),
            'kalan_gun' => $kalanGun,
            'garanti_gecerli' => $kalanGun >= 0,
        ];
    }
}
