{{-- RENDER TESTİ --}}
<div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: red; color: white; padding: 20px; z-index: 99999; font-size: 20px;">
    COMPONENT RENDER EDİLDİ!
</div>

@php
    // Verimor Santral aktif mi kontrol et
    $verimorActive = false;
    
    if (Auth::check() && Auth::user()->firma_id) {
        try {
            // BASİTLEŞTİRİLMİŞ SORGU
            $verimorActive = \App\Models\IntegrationPurchase::where('tenant_id', Auth::user()->firma_id)
                ->where('integration_id', 10) // Verimor Santral ID
                ->where('status', 'completed')
                ->where('is_active', 1)
                ->exists();
        } catch (\Exception $e) {
            \Log::error('Verimor widget kontrol hatası: ' . $e->getMessage());
        }
    }
@endphp

{{-- DEBUG --}}
<div style="position: fixed; top: 10px; left: 10px; background: yellow; padding: 10px; z-index: 99999;">
    Auth Check: {{ Auth::check() ? 'YES' : 'NO' }}<br>
    Firma ID: {{ Auth::user()->firma_id ?? 'NULL' }}<br>
    Verimor Active: {{ $verimorActive ? 'YES' : 'NO' }}
</div>