<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceApiController extends Controller
{
    public function checkStatus(Request $request, $id)
    {
        $tenant_id = $request->tenant_id;

        // Servisi bul
        $service = Service::where('id', $id)
            ->where('firma_id', $tenant_id)
            ->first();

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Servis bulunamadı'
            ], 404);
        }

        // Servis tamamlandı mı?
        $isCompleted = $service->isCompleted();

        return response()->json([
            'success' => true,
            'data' => [
                'service_id' => $service->id,
                'is_completed' => $isCompleted,
                'status_code' => $service->servisDurum,
                'status_text' => $service->getStatusText(),
                'musteri_id' => $service->musteri_id,
                'kayit_tarihi' => $service->kayitTarihi,
                'cihaz_marka' => $service->cihazMarka,
                'cihaz_model' => $service->cihazModel,
            ]
        ]);
    }

    /**
     * Sadece tamamlanma durumunu kontrol et (basit)
     * GET /api/services/{id}/is-completed
     */
    public function isCompleted(Request $request, $id)
    {
        $tenant_id = $request->tenant_id;

        $service = Service::where('id', $id)
            ->where('firma_id', $tenant_id)
            ->first();

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Servis bulunamadı'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'service_id' => (int) $service->id,
            'is_completed' => $service->servisDurum == Service::STATUS_COMPLETED,
            'status_code' => (int) $service->servisDurum,
        ]);
    }

    /**
     * Servis listesi
     * GET /api/services
     */
    public function index(Request $request)
    {
        $tenant_id = $request->tenant_id;

        // Query builder
        $query = Service::where('firma_id', $tenant_id)
            ->orderBy('created_at', 'desc');

        // Durum filtreleme
        if ($request->has('status')) {
            $query->where('servisDurum', $request->status);
        }

        // Tamamlananlar
        if ($request->has('completed') && $request->completed == 'true') {
            $query->where('servisDurum', Service::STATUS_COMPLETED);
        }

        // Tamamlanmayanlar
        if ($request->has('active') && $request->active == 'true') {
            $query->where('servisDurum', '!=', Service::STATUS_COMPLETED);
        }

        $services = $query->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * Servis detayı
     * GET /api/services/{id}
     */
    public function show(Request $request, $id)
    {
        $tenant_id = $request->tenant_id;

        $service = Service::where('id', $id)
            ->where('firma_id', $tenant_id)
            ->with('customer') // Müşteri bilgisi ile birlikte
            ->first();

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Servis bulunamadı'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $service
        ]);
    }

    /**
     * Servis durumu güncelle
     * PUT /api/services/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        $tenant_id = $request->tenant_id;

        $request->validate([
            'status' => 'required|integer',
        ]);

        $service = Service::where('id', $id)
            ->where('firma_id', $tenant_id)
            ->first();

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Servis bulunamadı'
            ], 404);
        }

        $service->servisDurum = $request->status;
        $service->save();

        return response()->json([
            'success' => true,
            'message' => 'Servis durumu güncellendi',
            'data' => [
                'service_id' => $service->id,
                'status_code' => $service->servisDurum,
                'status_text' => $service->getStatusText(),
                'is_completed' => $service->isCompleted(),
            ]
        ]);
    }
}
