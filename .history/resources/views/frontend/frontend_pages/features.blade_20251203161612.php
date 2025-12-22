@extends('frontend.main_master')

@section('title', 'Özellikler - Serbis')

@section('main')

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="page-title">Güçlü <span class="accent">Özellikler</span></h1>
                <p class="page-subtitle">
                    Teknik servis işletmenizi yönetmek için ihtiyacınız olan tüm özellikler bir arada
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Features Grid -->
<section class="features-grid-section section">
    <div class="container">
        <div class="row g-4">
            {{-- @foreach($features as $feature)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="{{ route('feature.detail', $feature['slug']) }}" style="text-decoration: none;">
                    <div class="feature-module-card">
                        <div class="feature-module-icon {{ $feature['color'] }}">
                            <i class="{{ $feature['icon'] }}"></i>
                        </div>
                        <h3 class="feature-module-title">{{ $feature['title'] }}</h3>
                        <p class="feature-module-description">{{ $feature['short_description'] }}</p>
                        <div class="feature-module-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach --}}
            @foreach($features as $index => $feature)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <a href="{{ route('feature.detail', $feature['slug']) }}" style="text-decoration: none;">
                    <div class="feature-module-card">
                        <div class="feature-module-number {{ $feature['color'] }}">
                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                        </div>
                        <h3 class="feature-module-title">{{ $feature['title'] }}</h3>
                        <p class="feature-module-description">{{ $feature['short_description'] }}</p>
                        <div class="feature-module-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Why Serbis -->
<section class="why-serbis-section section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">NEDEN SERBİS?</span>
            <h2 class="section-title">Serbis ile <span class="accent">Fark Yaratın</span></h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="why-card">
                    <div class="why-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Hızlı ve Kolay</h3>
                    <p>Kullanıcı dostu arayüz ile dakikalar içinde işlemlerinizi tamamlayın</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="why-card">
                    <div class="why-icon">
                        <i class="fas fa-cloud"></i>
                    </div>
                    <h3>Bulut Tabanlı</h3>
                    <p>Her yerden, her cihazdan güvenli erişim imkanı</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="why-card">
                    <div class="why-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Detaylı Raporlar</h3>
                    <p>İşletmenizi analiz edin, doğru kararlar alın</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2 class="cta-title">14 Gün Ücretsiz Deneyin!</h2>
        <p class="cta-description">
            Kredi kartı gerektirmez. Anında başlayın, tüm özellikleri keşfedin.
        </p>
        <button class="btn btn-cta" onclick="window.open('{{ url('/kullanici-girisi')}}', '_blank')">
            <i class="fas fa-rocket me-2"></i> Hemen Ücretsiz Başla
        </button>
    </div>
</section>

@endsection