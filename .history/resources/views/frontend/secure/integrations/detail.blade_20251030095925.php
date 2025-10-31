@extends('frontend.secure.user_master')
@section('user')

<style>
.integration-detail {
    background: #f6f7f7;
    min-height: 100vh;
}

.breadcrumb-custom {
    background: white;
    padding: 15px 20px;
    margin-bottom: 20px;
    border-radius: 4px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.breadcrumb-custom a {
    color: #2271b1;
    text-decoration: none;
}

.breadcrumb-custom a:hover {
    text-decoration: underline;
}

.breadcrumb-custom .separator {
    margin: 0 8px;
    color: #999;
}

.detail-container {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 30px;
    align-items: start;
}

.detail-left {
    background: white;
    border-radius: 4px;
    padding: 30px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.integration-header {
    display: flex;
    gap: 20px;
    margin-bottom: 25px;
    padding-bottom: 25px;
    border-bottom: 1px solid #dcdcde;
}

.integration-logo-large {
    width: 100px;
    height: 100px;
    object-fit: contain;
    border: 1px solid #dcdcde;
    border-radius: 4px;
    padding: 15px;
    background: white;
}

.integration-title-section h1 {
    font-size: 28px;
    font-weight: 600;
    color: #1e1e1e;
    margin-bottom: 10px;
}

.integration-meta {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    flex-direction: column;
}

.meta-label {
    font-size: 12px;
    color: #757575;
    margin-bottom: 3px;
}

.meta-value {
    font-size: 14px;
    color: #1e1e1e;
    font-weight: 500;
}

.integration-tags {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 15px;
}

.tag {
    background: #f0f0f1;
    padding: 5px 12px;
    border-radius: 3px;
    font-size: 13px;
    color: #50575e;
}

.integration-banner {
    width: 100%;
    border-radius: 4px;
    margin: 25px 0;
    border: 1px solid #dcdcde;
}

.integration-description {
    font-size: 16px;
    line-height: 1.7;
    color: #1e1e1e;
}

.integration-description h2 {
    font-size: 24px;
    font-weight: 600;
    margin: 30px 0 15px;
    color: #1e1e1e;
}

.integration-description p {
    margin-bottom: 15px;
}

.integration-description ul {
    margin: 15px 0;
    padding-left: 25px;
}

.integration-description li {
    margin-bottom: 8px;
}

.detail-right {
    position: sticky;
    top: 20px;
}

.pricing-box {
    background: white;
    border: 1px solid #dcdcde;
    border-radius: 4px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.price-display {
    text-align: center;
    margin-bottom: 20px;
}

.price-amount {
    font-size: 36px;
    font-weight: 600;
    color: #2271b1;
}

.price-period {
    color: #757575;
    font-size: 14px;
    margin-top: 5px;
}

.price-free {
    font-size: 32px;
    font-weight: 600;
    color: #008a20;
}

.pricing-options {
    margin-bottom: 20px;
}

.pricing-option {
    display: flex;
    align-items: center;
    padding: 12px;
    border: 2px solid #dcdcde;
    border-radius: 4px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.2s;
}

.pricing-option:hover {
    border-color: #2271b1;
}

.pricing-option.selected {
    border-color: #2271b1;
    background: #f0f6fc;
}

.pricing-option input[type="radio"] {
    margin-right: 10px;
}

.pricing-option-label {
    flex: 1;
    font-weight: 500;
}

.pricing-option-price {
    font-weight: 600;
    color: #2271b1;
}

.action-button-primary {
    width: 100%;
    padding: 15px;
    background: #2271b1;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
    display: block;
    text-decoration: none;
}

.action-button-primary:hover {
    background: #135e96;
    color: white;
}

.action-button-danger {
    width: 100%;
    padding: 15px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
    display: block;
    text-decoration: none;
    margin-bottom: 10px;
}

.action-button-danger:hover {
    background: #c82333;
    color: white;
}

.action-button-secondary {
    width: 100%;
    padding: 15px;
    background: white;
    color: #2271b1;
    border: 2px solid #2271b1;
    border-radius: 4px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
    display: block;
    text-decoration: none;
}

.action-button-secondary:hover {
    background: #2271b1;
    color: white;
}

.info-box {
    background: white;
    border: 1px solid #dcdcde;
    border-radius: 4px;
    padding: 20px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.info-box h3 {
    font-size: 16px;
    font-weight: 600;
    color: #1e1e1e;
    margin-bottom: 15px;
}

.info-item {
    display: flex;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f1;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item i {
    width: 30px;
    color: #2271b1;
}

.info-item-text {
    flex: 1;
    font-size: 14px;
    color: #50575e;
}

.active-badge-large {
    display: inline-block;
    background: #008a20;
    color: white;
    padding: 8px 16px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 15px;
}

@media (max-width: 991px) {
    .detail-container {
        grid-template-columns: 1fr;
    }
    
    .detail-right {
        position: static;
    }
}
</style>

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
                @if($integration->logo)
                <img src="{{ asset($integration->logo) }}" alt="{{ $integration->name }} Banner" class="integration-banner">
                @endif

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

                    {{-- @if($integration->price > 0)
                    <div class="pricing-options">
                        <label class="pricing-option selected">
                            <input type="radio" name="pricing" value="monthly" checked>
                            <span class="pricing-option-label">Aylık</span>
                            <span class="pricing-option-price">₺{{ number_format($integration->price, 2) }}</span>
                        </label>
                        <label class="pricing-option">
                            <input type="radio" name="pricing" value="yearly">
                            <span class="pricing-option-label">Yıllık <small class="text-success">(2 ay ücretsiz)</small></span>
                            <span class="pricing-option-price">₺{{ number_format($integration->price * 10, 2) }}</span>
                        </label>
                    </div>
                    @endif --}}

                    
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