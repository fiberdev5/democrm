<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerApiController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'musteriTipi' => 'required|in:bireysel,kurumsal',
            'adSoyad' => 'required|string|max:255',
            'tel1' => 'required|string|max:20',
            'tel2' => 'nullable|string|max:20',
            'il' => 'nullable|string|max:100',
            'ilce' => 'nullable|string|max:100',
            'adres' => 'nullable|string',
            'tcNo' => 'nullable|string|max:11',
            'vergiNo' => 'nullable|string|max:50',
            'vergiDairesi' => 'nullable|string|max:255',
        ], [
            'musteriTipi.required' => 'Müşteri tipi zorunludur',
            'musteriTipi.in' => 'Müşteri tipi bireysel veya kurumsal olmalıdır',
            'adSoyad.required' => 'Ad Soyad zorunludur',
            'tel1.required' => 'Telefon numarası zorunludur',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasyon hatası',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Middleware'den gelen tenant bilgisi
            $tenant_id = $request->tenant_id;
            $tenant = $request->tenant;

            // API üzerinden eklenen müşteriler için personel_id null olabilir
            // veya sistem kullanıcısı olarak işaretlenebilir
            $customer = Customer::create([
                'firma_id' => $tenant_id,
                'personel_id' => null, // API üzerinden eklendiği için
                'musteriTipi' => $request->musteriTipi,
                'adSoyad' => $request->adSoyad,
                'tel1' => $request->tel1,
                'tel2' => $request->tel2,
                'il' => $request->il,
                'ilce' => $request->ilce,
                'adres' => $request->adres,
                'tcNo' => $request->tcNo,
                'vergiNo' => $request->vergiNo,
                'vergiDairesi' => $request->vergiDairesi,
                'created_at' => Carbon::now(),
            ]);

            // Activity log
            ActivityLogger::logCustomerCreated($customer->id, $request->adSoyad, 'API');

            return response()->json([
                'success' => true,
                'message' => 'Müşteri başarıyla eklendi',
                'data' => [
                    'customer_id' => $customer->id,
                    'adSoyad' => $customer->adSoyad,
                    'tel1' => $customer->tel1,
                    'musteriTipi' => $customer->musteriTipi,
                    'created_at' => $customer->created_at->format('Y-m-d H:i:s')
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Müşteri eklenirken bir hata oluştu',
                'error' => config('app.debug') ? $e->getMessage() : 'Sistem hatası'
            ], 500);
        }
    }

    /**
     * Müşteri listesi
     */
    public function index(Request $request)
    {
        $tenant_id = $request->tenant_id;
        
        $customers = Customer::where('firma_id', $tenant_id)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $customers
        ]);
    }

    /**
     * Müşteri detayı
     */
    public function show(Request $request, $id)
    {
        $tenant_id = $request->tenant_id;
        
        $customer = Customer::where('firma_id', $tenant_id)
            ->where('id', $id)
            ->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Müşteri bulunamadı'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $customer
        ]);
    }
}
