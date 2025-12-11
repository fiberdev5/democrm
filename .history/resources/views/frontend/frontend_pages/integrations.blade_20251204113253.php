@extends('frontend.main_master')

@section('title', 'Entegrasyonlar - Serbis')

@section('main')

<!-- Hero Section -->
<section class="integrations-hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="hero-main-title">Serbis Entegrasyonları ile<br>Tüm Süreçlerinizi Entegre Edin</h1>
                <p class="hero-description">
                    Serbis uygulama mağazasındaki uygulama ve entegrasyonlar ile teknik servis sitenizi çok yönlü hale getirin.
                </p>
                <button class="btn btn-hero-cta" onclick="window.open('{{ url('/kullanici-girisi')}}', '_blank')">
                    Deneme Hesabı Oluştur
                </button>
            </div>
        </div>
        
<!-- Featured Logos Slider -->
<div class="marquee-wrapper">
    <div class="marquee-content">
        <!-- 1. GRUP LOGOLAR -->
        <div class="logo-item">
            <img src="{{ asset('frontend/img/integrations/logo.png') }}" alt="Logo" onerror="this.src='https://via.placeholder.com/80x40/49657B/ffffff?text=Logo'">
            <span>Logo</span>
            <small>Muhasebe</small>
        </div>
        <div class="logo-item">
            <img src="{{ asset('frontend/img/integrations/ups.png') }}" alt="UPS" onerror="this.src='https://via.placeholder.com/80x40/49657B/ffffff?text=UPS'">
            <span>UPS Kargo</span>
            <small>Kargo</small>
        </div>
        <div class="logo-item">
            <img src="{{ asset('frontend/img/integrations/mikro.png') }}" alt="Mikro" onerror="this.src='https://via.placeholder.com/80x40/49657B/ffffff?text=Mikro'">
            <span>Mikro</span>
            <small>Muhasebe</small>
        </div>
        <div class="logo-item">
            <img src="{{ asset('frontend/img/integrations/jivochat.png') }}" alt="JivoChat" onerror="this.src='https://via.placeholder.com/80x40/49657B/ffffff?text=Jivo'">
            <span>JivoChat</span>
            <small>Pazarlama</small>
        </div>
        <div class="logo-item">
            <img src="{{ asset('frontend/img/integrations/ikasmai.png') }}" alt="İkasMai" onerror="this.src='https://via.placeholder.com/80x40/49657B/ffffff?text=Mail'">
            <span>Email</span>
            <small>Pazarlama</small>
        </div>

        <!-- 2. GRUP LOGOLAR (Sonsuz döngü için tekrar) -->
        <div class="logo-item">
            <img src="{{ asset('frontend/img/integrations/logo.png') }}" alt="Logo" onerror="this.src='https://via.placeholder.com/80x40/49657B/ffffff?text=Logo'">
            <span>Logo</span>
            <small>Muhasebe</small>
        </div>
        <div class="logo-item">
            <img src="{{ asset('frontend/img/integrations/ups.png') }}" alt="UPS" onerror="this.src='https://via.placeholder.com/80x40/49657B/ffffff?text=UPS'">
            <span>UPS Kargo</span>
            <small>Kargo</small>
        </div>
        <div class="logo-item">
            <img src="{{ asset('frontend/img/integrations/mikro.png') }}" alt="Mikro" onerror="this.src='https://via.placeholder.com/80x40/49657B/ffffff?text=Mikro'">
            <span>Mikro</span>
            <small>Muhasebe</small>
        </div>
        <div class="logo-item">
            <img src="{{ asset('frontend/img/integrations/jivochat.png') }}" alt="JivoChat" onerror="this.src='https://via.placeholder.com/80x40/49657B/ffffff?text=Jivo'">
            <span>JivoChat</span>
            <small>Pazarlama</small>
        </div>
        <div class="logo-item">
            <img src="{{ asset('frontend/img/integrations/ikasmai.png') }}" alt="İkasMai" onerror="this.src='https://via.placeholder.com/80x40/49657B/ffffff?text=Mail'">
            <span>Email</span>
            <small>Pazarlama</small>
        </div>
    </div>
</div>
</section>

<!-- En Popüler -->
<section class="integration-category-section">
    <div class="container">
        <div class="category-header-simple">
            <h2 class="category-title">En Popüler</h2>
            <p class="category-description">Serbis kullanıcılarının en çok kullandığı uygulamalar</p>
        </div>
        
        <div class="row g-4">
            @foreach($integrations['SMS'] as $index => $integration)
            @if($index < 3)
            <div class="col-lg-4 col-md-6">
                <div class="integration-card-soft">
                    <div class="integration-logo-soft">
                        <img src="{{ asset($integration['logo']) }}" alt="{{ $integration['name'] }}" onerror="this.src='https://via.placeholder.com/100x60/49657B/ffffff?text={{ substr($integration['name'], 0, 1) }}'">
                    </div>
                    <h3 class="integration-name-soft">{{ $integration['name'] }}</h3>
                    <span class="integration-category-tag">{{ $integration['category'] }}</span>
                    <p class="integration-description-soft">{{ $integration['description'] }}</p>
                    <span class="integration-price-tag">{{ $integration['featured'] ? 'Ücretli' : 'Ücretsiz' }}</span>
                </div>
            </div>
            @endif
            @endforeach
            
            @foreach($integrations['Diğer'] as $integration)
            <div class="col-lg-4 col-md-6">
                <div class="integration-card-soft">
                    <div class="integration-logo-soft">
                        <img src="{{ asset($integration['logo']) }}" alt="{{ $integration['name'] }}" onerror="this.src='https://via.placeholder.com/100x60/49657B/ffffff?text={{ substr($integration['name'], 0, 1) }}'">
                    </div>
                    <h3 class="integration-name-soft">{{ $integration['name'] }}</h3>
                    <span class="integration-category-tag">{{ $integration['category'] }}</span>
                    <p class="integration-description-soft">{{ $integration['description'] }}</p>
                    <span class="integration-price-tag">{{ $integration['featured'] ? 'Ücretli' : 'Ücretsiz' }}</span>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-5">
            <button class="btn-load-more">Tümünü Gör ↓</button>
        </div>
    </div>
</section>

<!-- Ön Muhasebe -->
<section class="integration-category-section gray-bg">
    <div class="container">
        <div class="category-header-with-link">
            <div>
                <h2 class="category-title">Ön Muhasebe Entegrasyonları</h2>
                <p class="category-description">Ön muhasebe entegrasyonları ile üretimden, satışa kadarki yolculuğu kontrol edin!</p>
            </div>
            <a href="#" class="view-all-link">Tümünü Görüntüle →</a>
        </div>
        
        <div class="row g-4">
            @foreach($integrations['Fatura'] as $integration)
            <div class="col-lg-4 col-md-6">
                <div class="integration-card-soft">
                    <div class="integration-logo-soft">
                        <img src="{{ asset($integration['logo']) }}" alt="{{ $integration['name'] }}" onerror="this.src='https://via.placeholder.com/100x60/49657B/ffffff?text={{ substr($integration['name'], 0, 1) }}'">
                    </div>
                    <h3 class="integration-name-soft">{{ $integration['name'] }}</h3>
                    <span class="integration-category-tag">{{ $integration['category'] }}</span>
                    <p class="integration-description-soft">{{ $integration['description'] }}</p>
                    <span class="integration-price-tag">Ücretli</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- SMS Entegrasyonları -->
<section class="integration-category-section">
    <div class="container">
        <div class="category-header-with-link">
            <div>
                <h2 class="category-title">SMS Entegrasyonları</h2>
                <p class="category-description">SMS entegrasyonları ile müşterilerinize anında bildirim gönderin</p>
            </div>
            <a href="#" class="view-all-link">Tümünü Görüntüle →</a>
        </div>
        
        <div class="row g-4">
            @foreach($integrations['SMS'] as $integration)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="integration-card-soft">
                    <div class="integration-logo-soft">
                        <img src="{{ asset($integration['logo']) }}" alt="{{ $integration['name'] }}" onerror="this.src='https://via.placeholder.com/100x60/49657B/ffffff?text={{ substr($integration['name'], 0, 1) }}'">
                    </div>
                    <h3 class="integration-name-soft">{{ $integration['name'] }}</h3>
                    <span class="integration-category-tag">SMS</span>
                    <p class="integration-description-soft">{{ $integration['description'] }}</p>
                    <span class="integration-price-tag">{{ $integration['featured'] ? 'Ücretli' : 'Ücretsiz' }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection