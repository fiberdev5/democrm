<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Integration;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IntegrationMarketplaceController extends Controller
{
    // Entegrasyonlar pazaryeri - Firmalar buradan süper admin'in eklediği entegrasyonları görür
    public function index(Request $request, $tenant_id) {
        $user = Auth::user();
        $tenant = Tenant::where('id', $tenant_id)->first();
        
        if(!$tenant) {
            abort(404, 'Firma bulunamadı.');
        }

        // Kategoriler
        $categories = [
            'all' => 'Tümü',
            'invoice' => 'Fatura',
            'sms' => 'SMS',
            'accounting' => 'Muhasebe',
            'other' => 'Diğer'
        ];

        // Süper admin tarafından eklenen AKTİF entegrasyonları getir
        $query = Integration::where('is_active', 1);
        
        // Kategori filtresi
        if($request->filled('category') && $request->category != 'all') {
            $query->where('category', $request->category);
        }

        // Arama filtresi
        if($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $integrations = $query->orderBy('name', 'ASC')->get();

        // Bu firmanın aktif ettiği entegrasyonlar
        // $activeIntegrations = TenantIntegration::where('tenant_id', $tenant->id)
        //     ->where('is_enabled', 1)
        //     ->pluck('integration_id')
        //     ->toArray();

        return view('frontend.secure.integrations.marketplace', compact('integrations', 'categories', 'tenant'));
    }

    // Entegrasyon detayı
    public function show($tenant_id, $slug) {
        $user = Auth::user();
        $tenant = Tenant::where('id', $tenant_id)->first();
        $integration = Integration::where('slug', $slug)->where('is_active', 1)->firstOrFail();
        
        $purchase = $tenant->getIntegrationPurchase($integration->id);
        $isPurchased = $purchase !== null;
        $isActive = $isPurchased && $purchase->isActive();
        return view('frontend.secure.integrations.detail', compact('integration', 'tenant', 'isPurchased', 'isActive', 'purchase'));
    }

     public function purchase($tenant_id, $integration_id)
    {
        $user = Auth::user();
        $tenant = Tenant::findOrFail($tenant_id);
        $integration = Integration::where('is_active', 1)->findOrFail($integration_id);

        // Zaten satın alınmış mı kontrol et
        if ($tenant->hasIntegration($integration_id)) {
            $notification = array(
                'message' => 'Bu entegrasyonu zaten satın aldınız.',
                'alert-type' => 'warning'
            );
            return redirect()->route('tenant.integrations.show', [$tenant_id, $integration->slug])->with($notification);
        }

        return view('frontend.secure.integrations.purchase', compact('tenant', 'integration'));
    }

    // Satın alma işlemini tamamla
    public function processPurchase(Request $request, $tenant_id, $integration_id)
    {
        $request->validate([
            'payment_method' => 'required|in:credit_card,bank_transfer',
        ]);

        $tenant = Tenant::findOrFail($tenant_id);
        $integration = Integration::where('is_active', 1)->findOrFail($integration_id);

        // Zaten satın alınmış mı kontrol et
        if ($tenant->hasIntegration($integration_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Bu entegrasyonu zaten satın aldınız.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Entegrasyon satın alımı oluştur
            $purchase = IntegrationPurchase::create([
                'tenant_id' => $tenant->id,
                'integration_id' => $integration->id,
                'amount' => $integration->price,
                'currency' => 'TRY',
                'status' => 'pending',
                'payment_method' => $request->payment_method,
            ]);

            // Ödeme işlemi simülasyonu (gerçek ödeme gateway'i entegre edilecek)
            // Şimdilik otomatik onaylı kabul ediyoruz
            if ($integration->price == 0) {
                // Ücretsiz entegrasyonlar direkt aktif
                $purchase->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                    'is_active' => true,
                    'activated_at' => now(),
                    'transaction_id' => 'FREE-' . uniqid(),
                ]);
            } else {
                // Ücretli entegrasyonlar için ödeme gateway'ine yönlendirme yapılacak
                // Şimdilik test amaçlı completed yapıyoruz
                $purchase->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                    'is_active' => true,
                    'activated_at' => now(),
                    'transaction_id' => 'TEST-' . uniqid(),
                    'expires_at' => now()->addYear(), // 1 yıllık
                ]);
            }

            DB::commit();

            $notification = array(
                'message' => 'Entegrasyon başarıyla satın alındı!',
                'alert-type' => 'success'
            );

            return redirect()->route('tenant.integrations.settings', [$tenant_id, $integration_id])->with($notification);

        } catch (\Exception $e) {
            DB::rollBack();
            
            $notification = array(
                'message' => 'Satın alma işlemi sırasında bir hata oluştu: ' . $e->getMessage(),
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);
        }
    }
}
