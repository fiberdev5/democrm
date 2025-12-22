@extends('frontend.secure.user_master')
@section('user')

<div class="integration-detail">
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <div class="breadcrumb-custom">
            <a href="{{ route('tenant.integrations.marketplace', $tenant->id) }}">
                <i class="fas fa-puzzle-piece"></i> Entegrasyonlar
            </a>
            <span class="separator">›</span>
            <span>{{ $integration->name }}</span>
        </div>

        <div class="detail-container">
            <!-- Sol Taraf - Detaylar -->
            <div class="detail-left">
                <div class="integration-header">
                    @if($integration->logo)
                    <img src="{{ asset($integration->logo) }}" alt="{{ $integration->name }}" class="integration-logo-large">
                    @else
                    <div class="integration-logo-large d-flex align-items-center justify-content-center">
                        <i class="fas fa-puzzle-piece fa-3x text-muted"></i>
                    </div>
                    @endif

                    <div class="integration-title-section">
                        <h1>{{ $integration->name }}</h1>
                        <p style="color: #757575; font-size: 16px;">{{ $integration->description }}</p>
                        
                        <div class="integration-meta">
                            <div class="meta-item">
                                <span class="meta-label">Kategori</span>
                                <span class="meta-value">
                                    @if($integration->category == 'invoice')
                                        Fatura
                                    @elseif($integration->category == 'sms')
                                        SMS
                                    @elseif($integration->category == 'accounting')
                                        Muhasebe
                                    @else
                                        Diğer
                                    @endif
                                </span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Son Güncelleme</span>
                                <span class="meta-value">{{ $integration->updated_at->format('d M Y') }}</span>
                            </div>
                        </div>

                        <div class="integration-tags">
                            <span class="tag">{{ $integration->category }}</span>
                            @if($integration->price == 0)
                            <span class="tag">ücretsiz</span>
                            @endif
                            <span class="tag">entegrasyon</span>
                        </div>
                    </div>
                </div>

                <!-- Banner Görseli (eğer varsa) -->
                {{-- @if($integration->logo)
                <img src="{{ asset($integration->logo) }}" alt="{{ $integration->name }} Banner" class="integration-banner">
                @endif --}}

                <!-- Detaylı Açıklama -->
                <div class="integration-description">
                    <h2>{{ $integration->name }} Hakkında</h2>
                    {!! $integration->explanation ?: '<p>Bu entegrasyon için detaylı açıklama henüz eklenmemiştir.</p>' !!}
                </div>
            </div>

            <!-- Sağ Taraf - Fiyatlandırma ve Aksiyon -->
            <div class="detail-right">
                <div class="pricing-box">
                   

                    <div class="price-display">
                        @if($integration->price > 0)
                        <div class="price-amount">₺{{ number_format($integration->price, 2) }}</div>
                        <div class="price-period">aylık</div>
                        @else
                        <div class="price-free">
                            <i class="fas fa-gift"></i> Ücretsiz
                        </div>
                        @endif
                    </div>

                    
                </div>

                <div class="info-box">
                    <h3>Destek</h3>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <span class="info-item-text">7/24 Uzman Desteği</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-book"></i>
                        <span class="info-item-text">Dokümantasyon Mevcut</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-shield-alt"></i>
                        <span class="info-item-text">Güvenli Entegrasyon</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-sync-alt"></i>
                        <span class="info-item-text">Otomatik Güncelleme</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fiyatlandırma seçenekleri
document.querySelectorAll('.pricing-option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('.pricing-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        this.classList.add('selected');
        this.querySelector('input[type="radio"]').checked = true;
    });
});
</script>

@endsection