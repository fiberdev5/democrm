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
    public function show($slug) {
        $user = Auth::user();
        $tenant = Tenant::where('id', $user->firma_id)->first();
        $integration = Integration::where('slug', $slug)->where('is_active', 1)->firstOrFail();
        
        // Bu firmanın bu entegrasyonu aktif mi?
        // $tenantIntegration = TenantIntegration::where('tenant_id', $tenant->id)
        //     ->where('integration_id', $integration->id)
        //     ->first();

        // $isActive = $tenantIntegration && $tenantIntegration->is_enabled;

        return view('frontend.secure.integrations.detail', compact('integration', 'tenant'));
    }
}
