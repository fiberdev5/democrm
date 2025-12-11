@extends('frontend.main_master')

@section('title', 'Entegrasyonlar - Serbis')

@section('main')

<!-- Page Header -->
<section class="integrations-header">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="integrations-main-title">Güçlü <span class="accent">Entegrasyonlar</span></h1>
                <p class="integrations-main-subtitle">
                    Kullandığınız sistemlerle kolayca entegre olun, iş süreçlerinizi otomatikleştirin
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Ön Muhasebe Entegrasyonları -->
<section class="integration-category-block">
    <div class="container">
        <div class="category-header">
            <div>
                <h2 class="category-title">Ön Muhasebe Entegrasyonları</h2>
                <p class="category-description">Ön muhasebe entegrasyonları ile üretimden, satışa kadarki yolculuğu kontrol edin!</p>
            </div>
            <a href="#" class="view-all-link">Tümünü Görüntüle →</a>
        </div>
        
        <div class="row g-4">
            @foreach($integrations['Fatura'] as $integration)
            <div class="col-lg-4 col-md-6">
                <div class="integration-item">
                    <div class="integration-icon">
                        <img src="{{ asset($integration['logo']) }}" alt="{{ $integration['name'] }}" onerror="this.src='https://via.placeholder.com/80x80/49657B/ffffff?text={{ substr($integration['name'], 0, 1) }}'">
                    </div>
                    <div class="integration-info">
                        <h3 class="integration-title">{{ $integration['name'] }}</h3>
                        <p class="integration-desc">{{ $integration['description'] }}</p>
                    </div>
                    <span class="integration-price-badge">Ücretli</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- E-Fatura Entegrasyonları -->
<section class="integration-category-block">
    <div class="container">
        <div class="category-header">
            <div>
                <h2 class="category-title">E-Fatura Entegrasyonları</h2>
                <p class="category-description">E-fatura entegrasyonları ile satış sonrası süreçlerinizi otomatik hale getirebilirsiniz.</p>
            </div>
            <a href="#" class="view-all-link">Tümünü Görüntüle →</a>
        </div>
        
        <div class="row g-4">
            @foreach($integrations['Fatura'] as $integration)
            <div class="col-lg-4 col-md-6">
                <div class="integration-item">
                    <div class="integration-icon">
                        <img src="{{ asset($integration['logo']) }}" alt="{{ $integration['name'] }}" onerror="this.src='https://via.placeholder.com/80x80/49657B/ffffff?text={{ substr($integration['name'], 0, 1) }}'">
                    </div>
                    <div class="integration-info">
                        <h3 class="integration-title">{{ $integration['name'] }}</h3>
                        <p class="integration-desc">{{ $integration['description'] }}</p>
                    </div>
                    <span class="integration-price-badge">Ücretsiz</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- En Popüler -->
<section class="integration-category-block popular-section">
    <div class="container">
        <div class="category-header">
            <div>
                <h2 class="category-title">En Popüler</h2>
                <p class="category-description">İkas kullanıcılarının en çok kullandığı uygulamalar</p>
            </div>
        </div>
        
        <div class="row g-4">
            @foreach($integrations['SMS'] as $index => $integration)
            @if($index < 3)
            <div class="col-lg-4 col-md-6">
                <div class="integration-item">
                    <div class="integration-icon">
                        <img src="{{ asset($integration['logo']) }}" alt="{{ $integration['name'] }}" onerror="this.src='https://via.placeholder.com/80x80/49657B/ffffff?text={{ substr($integration['name'], 0, 1) }}'">
                    </div>
                    <div class="integration-info">
                        <h3 class="integration-title">{{ $integration['name'] }}</h3>
                        <p class="integration-desc">{{ $integration['description'] }}</p>
                    </div>
                    <span class="integration-price-badge">{{ $integration['featured'] ? 'Ücretli' : 'Ücretsiz' }}</span>
                </div>
            </div>
            @endif
            @endforeach
            
            @foreach($integrations['Diğer'] as $integration)
            <div class="col-lg-4 col-md-6">
                <div class="integration-item">
                    <div class="integration-icon">
                        <img src="{{ asset($integration['logo']) }}" alt="{{ $integration['name'] }}" onerror="this.src='https://via.placeholder.com/80x80/49657B/ffffff?text={{ substr($integration['name'], 0, 1) }}'">
                    </div>
                    <div class="integration-info">
                        <h3 class="integration-title">{{ $integration['name'] }}</h3>
                        <p class="integration-desc">{{ $integration['description'] }}</p>
                    </div>
                    <span class="integration-price-badge">{{ $integration['featured'] ? 'Ücretli' : 'Ücretsiz' }}</span>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-5">
            <button class="btn-show-all">Tümünü Gör ↓</button>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2 class="cta-title">Entegrasyonları Hemen Deneyin!</h2>
        <p class="cta-description">
            Tüm entegrasyonlara 14 gün ücretsiz erişim. Kredi kartı gerektirmez.
        </p>
        <button class="btn btn-cta" onclick="window.open('{{ url('/kullanici-girisi')}}', '_blank')">
            Hemen Ücretsiz Başlayın
        </button>
    </div>
</section>

@endsection