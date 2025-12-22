@extends('frontend.main_master')

@section('title', 'Entegrasyonlar - Serbis')

@section('main')

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="page-title">Güçlü <span class="accent">Entegrasyonlar</span></h1>
                <p class="page-subtitle">
                    Kullandığınız sistemlerle kolayca entegre olun, iş süreçlerinizi otomatikleştirin
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Integrations -->
<section class="featured-integrations-section section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">ÖN MUHASEBE</span>
            <h2 class="section-title">Ön Muhasebe <span class="accent">Entegrasyonları</span></h2>
            <p class="section-subtitle">
                Ön muhasebe entegrasyonları ile üretimden, satışa kadarki yolculuğu kontrol edin!
            </p>
        </div>
        <div class="row g-4">
            @foreach($integrations['Fatura'] as $integration)
            <div class="col-lg-4 col-md-6">
                <div class="integration-card featured">
                    <div class="integration-badge">Ücretli</div>
                    <div class="integration-logo">
                        <img src="{{ asset($integration['logo']) }}" alt="{{ $integration['name'] }}" onerror="this.src='https://via.placeholder.com/120x60/49657B/ffffff?text={{ $integration['name'] }}'">
                    </div>
                    <h3 class="integration-name">{{ $integration['name'] }}</h3>
                    <p class="integration-description">{{ $integration['description'] }}</p>
                    <a href="#" class="btn-integration-detail">Detaylı Bilgi</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- SMS Integrations -->
<section class="integrations-category-section section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">SMS</span>
            <h2 class="section-title">SMS <span class="accent">Entegrasyonları</span></h2>
            <p class="section-subtitle">
                SMS entegrasyonları ile müşterilerinize anında bildirim gönderin
            </p>
        </div>
        <div class="row g-4">
            @foreach($integrations['SMS'] as $integration)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="integration-card">
                    <div class="integration-badge {{ $integration['featured'] ? 'featured' : '' }}">
                        {{ $integration['featured'] ? 'Popüler' : 'Ücretli' }}
                    </div>
                    <div class="integration-logo">
                        <img src="{{ asset($integration['logo']) }}" alt="{{ $integration['name'] }}" onerror="this.src='https://via.placeholder.com/120x60/49657B/ffffff?text={{ $integration['name'] }}'">
                    </div>
                    <h3 class="integration-name">{{ $integration['name'] }}</h3>
                    <p class="integration-description">{{ $integration['description'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Other Integrations -->
<section class="integrations-category-section section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">DİĞER</span>
            <h2 class="section-title">Diğer <span class="accent">Entegrasyonlar</span></h2>
        </div>
        <div class="row g-4">
            @foreach($integrations['Diğer'] as $integration)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="integration-card">
                    <div class="integration-badge">{{ $integration['featured'] ? 'Popüler' : 'Ücretli' }}</div>
                    <div class="integration-logo">
                        <img src="{{ asset($integration['logo']) }}" alt="{{ $integration['name'] }}" onerror="this.src='https://via.placeholder.com/120x60/49657B/ffffff?text={{ $integration['name'] }}'">
                    </div>
                    <h3 class="integration-name">{{ $integration['name'] }}</h3>
                    <p class="integration-description">{{ $integration['description'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Santral Integrations -->
<section class="integrations-category-section section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">SANTRAL</span>
            <h2 class="section-title">Santral <span class="accent">Entegrasyonları</span></h2>
        </div>
        <div class="row g-4">
            @foreach($integrations['Santral'] as $integration)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="integration-card">
                    <div class="integration-badge">Ücretli</div>
                    <div class="integration-logo">
                        <img src="{{ asset($integration['logo']) }}" alt="{{ $integration['name'] }}" onerror="this.src='https://via.placeholder.com/120x60/49657B/ffffff?text={{ $integration['name'] }}'">
                    </div>
                    <h3 class="integration-name">{{ $integration['name'] }}</h3>
                    <p class="integration-description">{{ $integration['description'] }}</p>
                </div>
            </div>
            @endforeach
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