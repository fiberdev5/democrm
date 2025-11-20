<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HipcallCallLog;
use App\Models\IntegrationPurchase;
use App\Services\HipcallService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HipcallWebhookController extends Controller
{
    public function handle(Request $request, $token)
    {
        try {
            // Token ile entegrasyonu bul
            $integration = IntegrationPurchase::where('webhook_token', $token)
                ->where('is_active', true)
                ->where('status', 'completed')
                ->first();

            if (!$integration) {
                Log::warning('Hipcall: Geçersiz webhook token', [
                    'token' => $token,
                    'ip' => $request->ip()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid webhook token'
                ], 401);
            }

            $tenant = $integration->tenant;
            
            if (!$tenant) {
                Log::error('Hipcall: Tenant bulunamadı', [
                    'integration_id' => $integration->id
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found'
                ], 404);
            }

            // Webhook verisini logla
            Log::info('Hipcall Webhook - ' . $tenant->firma_adi, [
                'tenant_id' => $tenant->id,
                'event' => $request->input('event'),
                'data' => $request->input('data')
            ]);

            $event = $request->input('event');
            $data = $request->input('data');

            // Event işle
            $this->processWebhook($tenant, $event, $data);

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Hipcall Webhook Error:', [
                'token' => $token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    private function processWebhook($tenant, $event, $data)
    {
        switch ($event) {
            case 'call_init':
                $this->handleCallInit($tenant, $data);
                break;
                
            case 'call_hangup':
                $this->handleCallHangup($tenant, $data);
                break;
                
            case 'call_bridged':
                $this->handleCallBridged($tenant, $data);
                break;
        }
    }

    private function handleCallInit($tenant, $data)
    {
        $callerNumber = $data['caller_number'] ?? null;
        $calleeNumber = $data['callee_number'] ?? null;
        $direction = $data['direction'] ?? 'inbound';
        
        Log::info("Arama başladı - {$tenant->firma_adi}", [
            'tenant_id' => $tenant->id,
            'arayan' => $callerNumber,
            'yön' => $direction,
            'uuid' => $data['uuid']
        ]);
        
        $customerId = null;
        
        // Gelen arama ise ve arayan numara varsa müşteri kontrol et
        if ($direction == 'inbound' && $callerNumber) {
            $customer = $this->findOrCreateCustomer($tenant, $callerNumber, $data);
            $customerId = $customer ? $customer->id : null;
        }
        
        // Çağrı kaydı oluştur
        HipcallCallLog::create([
            'tenant_id' => $tenant->id,
            'uuid' => $data['uuid'],
            'event_type' => 'call_init',
            'caller_number' => $callerNumber,
            'callee_number' => $calleeNumber,
            'direction' => $direction,
            'started_at' => $data['started_at'] ?? now(),
            'customer_id' => $customerId,
            'raw_data' => $data
        ]);
    }

    private function handleCallHangup($tenant, $data)
    {
        $uuid = $data['uuid'];
        $duration = $data['call_duration'] ?? 0;
        $recordUrl = $data['record_url'] ?? null;
        $callerNumber = $data['caller_number'] ?? null;
        $direction = $data['direction'] ?? 'inbound';
        
        Log::info("Arama bitti - {$tenant->firma_adi}", [
            'tenant_id' => $tenant->id,
            'uuid' => $uuid,
            'süre' => $duration
        ]);
        
        $customerId = null;
        
        // Gelen arama ise müşteri bul/oluştur
        if ($direction == 'inbound' && $callerNumber) {
            $customer = $this->findOrCreateCustomer($tenant, $callerNumber, $data);
            $customerId = $customer ? $customer->id : null;
        }
        
        // Mevcut kaydı güncelle veya yeni oluştur
        HipcallCallLog::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'uuid' => $uuid
            ],
            [
                'event_type' => 'call_hangup',
                'caller_number' => $data['caller_number'] ?? null,
                'callee_number' => $data['callee_number'] ?? null,
                'call_duration' => $duration,
                'record_url' => $recordUrl,
                'direction' => $direction,
                'started_at' => $data['started_at'] ?? now(),
                'ended_at' => $data['ended_at'] ?? now(),
                'customer_id' => $customerId,
                'raw_data' => $data
            ]
        );
    }

    private function handleCallBridged($tenant, $data)
{
    $callerNumber = $data['caller_number'] ?? null;
    $direction = $data['direction'] ?? 'inbound';
    $callId = $data['uuid']; 
    
    // ✅ DÜZELT: $tenant->name → $tenant->firma_adi
    Log::info("Çağrı bağlandı - {$tenant->firma_adi}", [
        'tenant_id' => $tenant->id,
        'call_id' => $callId,
        'caller' => $callerNumber,
        'full_data' => $data
    ]);
    
    $customerId = null;
    $customer = null;
    
    // Gelen arama ise müşteri bul
    if ($direction == 'inbound' && $callerNumber) {
        $customer = $this->findOrCreateCustomer($tenant, $callerNumber, $data);
        $customerId = $customer ? $customer->id : null;
        
        // 🎯 KART GÖNDERİMİ BURADA!
        if ($customer && $callId) {
            Log::info("Müşteri kartı gönderilecek", [
                'customer_id' => $customer->id,
                'customer_name' => $customer->adSoyad,
                'call_id' => $callId
            ]);
            
            $this->sendCustomerCardToHipcall($tenant, $customer, $callId, $data);
        }
    }
    
    HipcallCallLog::updateOrCreate(
        [
            'tenant_id' => $tenant->id,
            'uuid' => $data['uuid']
        ],
        [
            'event_type' => 'call_bridged',
            'caller_number' => $callerNumber,
            'callee_number' => $data['callee_number'] ?? null,
            'direction' => $direction,
            'started_at' => $data['started_at'] ?? now(),
            'customer_id' => $customerId,
            'raw_data' => $data
        ]
    );
}

    /**
     * Müşteriyi bul veya otomatik oluştur
     */
    private function findOrCreateCustomer($tenant, $phone, $callData)
    {
        if (empty($phone)) {
            return null;
        }
        
        // Telefon numarasını temizle (sadece rakamlar)
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        // Müşteriyi ara
        $customer = DB::table('customers')
            ->where('firma_id', $tenant->id)
            ->where(function($q) use ($phone, $cleanPhone) {
                $q->where('tel1', 'LIKE', "%{$cleanPhone}%")
                  ->orWhere('tel1', $phone)
                  ->orWhere('tel2', 'LIKE', "%{$cleanPhone}%");
            })
            ->first();
        
        if ($customer) {
            Log::info("Müşteri bulundu", [
                'tenant_id' => $tenant->id,
                'customer_id' => $customer->id,
                'name' => $customer->adSoyad,
                'phone' => $phone
            ]);
            return $customer;
        }
        
        // Müşteri yoksa yeni oluştur
        try {
            $newCustomerId = DB::table('customers')->insertGetId([
                'firma_id' => $tenant->id,
                'tel1' => $phone,
                'adSoyad' => 'Hipcall #' . substr($cleanPhone, -4), // Son 4 rakam
                'not' => 'Hipcall üzerinden otomatik oluşturuldu - ' . now()->format('d.m.Y H:i:s'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $customer = DB::table('customers')->where('id', $newCustomerId)->first();
            
            Log::info("Yeni müşteri otomatik oluşturuldu", [
                'tenant_id' => $tenant->id,
                'customer_id' => $newCustomerId,
                'phone' => $phone,
                'name' => $customer->adSoyad
            ]);
            
            return $customer;
            
        } catch (\Exception $e) {
            Log::error("Müşteri oluşturulamadı", [
                'tenant_id' => $tenant->id,
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    private function sendCustomerCardToHipcall($tenant, $customer, $callId, $callData)
{
    try {
        $hipcallService = new HipcallService($tenant->id);
        
        // Site URL'ini al
        $baseUrl = rtrim(config('app.url'), '/');
        
        // Kart verisini hazırla
        $cardData = $hipcallService->prepareCustomerCard($customer, $tenant->id, $baseUrl);
        
        Log::info("Hipcall'a kart gönderiliyor", [
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
            'customer_name' => $customer->adSoyad,
            'call_id' => $callId,
            'card_data' => $cardData
        ]);
        
        // Hipcall'a gönder
        $result = $hipcallService->sendCard($callId, $cardData);
        
        if ($result['success']) {
            Log::info("✅ Müşteri kartı başarıyla gönderildi", [
                'tenant_id' => $tenant->id,
                'customer_id' => $customer->id,
                'call_id' => $callId
            ]);
        } else {
            Log::error("❌ Müşteri kartı gönderilemedi", [
                'tenant_id' => $tenant->id,
                'customer_id' => $customer->id,
                'call_id' => $callId,
                'error' => $result['message'] ?? 'Bilinmeyen hata',
                'status' => $result['status'] ?? null,
                'response' => $result
            ]);
        }
        
    } catch (\Exception $e) {
        Log::error('Hipcall kart gönderme exception', [
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
            'call_id' => $callId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}
}
