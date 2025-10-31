@extends('frontend.secure.user_master')
@section('user')


<div class="page-content" id="passwords">
    <div class="container-fluid">
        <div class="row pageDetail">
            <div class="col-12">
                <div class="integration-detail">
                    <!-- Breadcrumb -->
                    <div class="breadcrumb-custom">
                        <a href="{{ route('tenant.integrations.marketplace', $tenant->id) }}">
                            <i class="fas fa-arrow-left"></i> Entegrasyonlar
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
                                    <div class="price-amount">₺{{ number_format($integration->price) }}</div>
                                    <div class="price-period">/ tek seferlik</div>
                                    
                                    @else
                                    <div class="price-free">
                                        <i class="fas fa-gift"></i> Ücretsiz
                                    </div>
                                    @endif
                                </div>

                                @if($isPurchased)
                                    {{-- Zaten satın alınmış --}}
                                    @if($isActive)
                                        <div class="alert alert-success mb-3" style="padding: 12px; border-radius: 8px;">
                                            <i class="fas fa-check-circle"></i> Bu entegrasyon aktif
                                        </div>
                                    @endif
                                @else
                                @if($integration->price > 0)
                                    <a href="{{ route('tenant.integrations.purchase', [$tenant->id, $integration->id]) }}" class="action-button-primary">
                                        <i class="fas fa-shopping-cart"></i> Şimdi Satın Al
                                    </a>
                              
                                @endif
                                @endif
                            </div>

                            <!-- Tab Yapısı -->
                            <div class="info-tabs mt-3">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="features-tab" data-bs-toggle="tab" data-bs-target="#features" type="button" role="tab">
                                            <i class="fas fa-star"></i> Özellikler
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="setup-tab" data-bs-toggle="tab" data-bs-target="#setup" type="button" role="tab">
                                            <i class="fas fa-cogs"></i> Kurulum
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="support-tab" data-bs-toggle="tab" data-bs-target="#support" type="button" role="tab">
                                            <i class="fas fa-life-ring"></i> Destek
                                        </button>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <!-- Özellikler Tab -->
                                    <div class="tab-pane fade show active" id="features" role="tabpanel">
                                        <ul class="features-list">
                                            <li>
                                                <i class="fas fa-check-circle"></i>
                                                <span>Kolay Kurulum ve Kullanım</span>
                                            </li>
                                            <li>
                                                <i class="fas fa-check-circle"></i>
                                                <span>Otomatik Veri Senkronizasyonu</span>
                                            </li>
                                            <li>
                                                <i class="fas fa-check-circle"></i>
                                                <span>Gerçek Zamanlı Bildirimler</span>
                                            </li>
                                            <li>
                                                <i class="fas fa-check-circle"></i>
                                                <span>Kapsamlı Raporlama</span>
                                            </li>
                                            <li>
                                                <i class="fas fa-check-circle"></i>
                                                <span>API Desteği</span>
                                            </li>
                                            <li>
                                                <i class="fas fa-check-circle"></i>
                                                <span>Güvenli Veri İletimi</span>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Kurulum Tab -->
                                    <div class="tab-pane fade" id="setup" role="tabpanel">
                                        <div class="setup-step">
                                            <h4>
                                                <span class="setup-step-number">1</span>
                                                Entegrasyonu Aktifleştirin
                                            </h4>
                                            <p>Yukarıdaki "Satın Al" butonuna tıklayarak entegrasyonu aktifleştirin.</p>
                                        </div>

                                        <div class="setup-step">
                                            <h4>
                                                <span class="setup-step-number">2</span>
                                                API Bilgilerinizi Girin
                                            </h4>
                                            <p>Entegrasyon ayarları sayfasından gerekli API anahtarlarını ve kimlik bilgilerini girin.</p>
                                        </div>

                                        <div class="setup-step">
                                            <h4>
                                                <span class="setup-step-number">3</span>
                                                Bağlantıyı Test Edin
                                            </h4>
                                            <p>Ayarlar sayfasındaki "Bağlantıyı Test Et" butonuna tıklayarak kurulumun doğru yapıldığından emin olun.</p>
                                        </div>

                                        <div class="setup-step">
                                            <h4>
                                                <span class="setup-step-number">4</span>
                                                Kullanmaya Başlayın
                                            </h4>
                                            <p>Entegrasyon aktif ve hazır! Artık sisteminizdeki veriler otomatik olarak senkronize edilecek.</p>
                                        </div>
                                    </div>

                                    <!-- Destek Tab -->
                                    <div class="tab-pane fade" id="support" role="tabpanel">
                                        <div class="info-item">
                                            <i class="fas fa-clock"></i>
                                            <span class="info-item-text">7/24 Canlı Destek Hizmeti - Her zaman yanınızdayız</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-book"></i>
                                            <span class="info-item-text">Detaylı Dokümantasyon - Adım adım kurulum rehberi</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-video"></i>
                                            <span class="info-item-text">Video Eğitimler - Görsel anlatım ile öğrenin</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-shield-alt"></i>
                                            <span class="info-item-text">Güvenli Entegrasyon - SSL şifrelemeli veri aktarımı</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-credit-card"></i>
                                            <span class="info-item-text">Güvenli Ödeme - 3D Secure ile korumalı ödeme</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-sync-alt"></i>
                                            <span class="info-item-text">Otomatik Güncellemeler - Her zaman en son sürüm</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Bootstrap Tab Aktivasyonu (eğer Bootstrap 5 kullanıyorsanız)
document.addEventListener('DOMContentLoaded', function () {
    var triggerTabList = [].slice.call(document.querySelectorAll('.nav-tabs button'));
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl);
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });
});
</script>
@endsection