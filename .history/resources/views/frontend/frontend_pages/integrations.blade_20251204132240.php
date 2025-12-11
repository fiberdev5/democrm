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
                    <img src="{{ asset('frontend/img/integrations/hipcall.jpg') }}" alt="Hipcall">
                    <span>Hipcall</span>
                    <small>Santral</small>
                </div>
                <div class="logo-item">
                    <img src="{{ asset('frontend/img/integrations/netgsm.png') }}" alt="NetGSM">
                    <span>NetGSM</span>
                    <small>SMS</small>
                </div>
                <div class="logo-item">
                    <img src="{{ asset('frontend/img/integrations/parasut.png') }}" alt="Paraşüt">
                    <span>Paraşüt</span>
                    <small>Muhasebe</small>
                </div>
                <div class="logo-item">
                    <img src="{{ asset('frontend/img/integrations/solveline.png') }}" alt="Solveline">
                    <span>Solveline</span>
                    <small>Santral</small>
                </div>
                <div class="logo-item">
                    <img src="{{ asset('frontend/img/integrations/tescom.png') }}" alt="Tescom">
                    <span>Tescom</span>
                    <small>SMS</small>
                </div>
                <div class="logo-item">
                    <img src="{{ asset('frontend/img/integrations/verimor.jpeg') }}" alt="Verimor">
                    <span>Verimor</span>
                    <small>SMS</small>
                </div>

                <!-- 2. GRUP LOGOLAR (Sonsuz döngü için tekrar) -->
                <div class="logo-item">
                    <img src="{{ asset('frontend/img/integrations/hipcall.jpg') }}" alt="Hipcall">
                    <span>Hipcall</span>
                    <small>Santral</small>
                </div>
                <div class="logo-item">
                    <img src="{{ asset('frontend/img/integrations/netgsm.png') }}" alt="NetGSM">
                    <span>NetGSM</span>
                    <small>SMS</small>
                </div>
                <div class="logo-item">
                    <img src="{{ asset('frontend/img/integrations/parasut.png') }}" alt="Paraşüt">
                    <span>Paraşüt</span>
                    <small>Muhasebe</small>
                </div>
                <div class="logo-item">
                    <img src="{{ asset('frontend/img/integrations/solveline.png') }}" alt="Solveline">
                    <span>Solveline</span>
                    <small>Santral</small>
                </div>
                <div class="logo-item">
                    <img src="{{ asset('frontend/img/integrations/tescom.png') }}" alt="Tescom">
                    <span>Tescom</span>
                    <small>SMS</small>
                </div>
                <div class="logo-item">
                    <img src="{{ asset('frontend/img/integrations/verimor.jpeg') }}" alt="Verimor">
                    <span>Verimor</span>
                    <small>SMS</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SMS Entegrasyonları -->
<section class="integration-category-section">
    <div class="container">
        <div class="category-header-simple">
            <h2 class="category-title">SMS Entegrasyonları</h2>
            <p class="category-description">SMS entegrasyonları ile müşterilerinize anında bildirim gönderin</p>
        </div>
        
        <div class="row g-4">
            @foreach($integrations['SMS'] as $integration)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="integration-card-soft">
                    <div class="integration-logo-soft">
                        <img src="{{ asset($integration['logo']) }}" alt="{{ $integration['name'] }}">
                    </div>
                    <h3 class="integration-name-soft">{{ $integration['name'] }}</h3>
                    <span class="integration-category-tag">SMS</span>
                    
                    <!-- Hover Overlay -->
                    <div class="card-overlay">
                        <h4 class="overlay-title">{{ $integration['name'] }}</h4>
                        <p class="overlay-description">{{ $integration['detail'] ?? $integration['description'] }}</p>
                        <ul class="overlay-features">
                            @if(isset($integration['features']))
                                @foreach($integration['features'] as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            @else
                                <li>Toplu SMS gönderimi</li>
                                <li>Otomatik bildirimler</li>
                                <li>Raporlama</li>
                            @endif
                        </ul>
                        <span class="overlay-badge">Aktif Entegrasyon</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Ön Muhasebe -->
<section class="integration-category-section gray-bg">
    <div class="container">
        <div class="category-header-simple">
            <h2 class="category-title">Ön Muhasebe Entegrasyonları</h2>
            <p class="category-description">Ön muhasebe entegrasyonları ile üretimden, satışa kadarki yolculuğu kontrol edin!</p>
        </div>
        
        <div class="row g-4 justify-content-center">
            @foreach($integrations['Fatura'] as $integration)
            <div class="col-lg-4 col-md-6">
                <div class="integration-card-soft">
                    <div class="integration-logo-soft">
                        <img src="{{ asset($integration['logo']) }}" alt="{{ $integration['name'] }}">
                    </div>
                    <h3 class="integration-name-soft">{{ $integration['name'] }}</h3>
                    <span class="integration-category-tag">{{ $integration['category'] }}</span>
                    <p class="integration-description-soft">{{ $integration['description'] }}</p>
                    
                    <!-- Hover Overlay -->
                    <div class="card-overlay">
                        <h4 class="overlay-title">{{ $integration['name'] }}</h4>
                        <p class="overlay-description">{{ $integration['detail'] ?? 'Muhasebe ve fatura işlemlerinizi otomatikleştirin.' }}</p>
                        <ul class="overlay-features">
                            @if(isset($integration['features']))
                                @foreach($integration['features'] as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            @else
                                <li>E-Fatura gönderimi</li>
                                <li>Otomatik muhasebe kaydı</li>
                                <li>Cari hesap takibi</li>
                                <li>Gelir-gider raporları</li>
                            @endif
                        </ul>
                        <span class="overlay-badge">Ücretli</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Santral Entegrasyonları -->
<section class="integration-category-section">
    <div class="container">
        <div class="category-header-simple">
            <h2 class="category-title">Santral Entegrasyonları</h2>
            <p class="category-description">Gelen aramaları otomatik kaydedin ve müşteri bilgilerini anında görüntüleyin</p>
        </div>
        
        <div class="row g-4 justify-content-center">
            @foreach($integrations['Diğer'] as $integration)
                @if($integration['name'] == 'HIPCALL')
                <div class="col-lg-4 col-md-6">
                    <div class="integration-card-soft">
                        <div class="integration-logo-soft">
                            <img src="{{ asset($integration['logo']) }}" alt="{{ $integration['name'] }}">
                        </div>
                        <h3 class="integration-name-soft">{{ $integration['name'] }}</h3>
                        <span class="integration-category-tag">Santral</span>
                        <p class="integration-description-soft">{{ $integration['description'] }}</p>
                        
                        <!-- Hover Overlay -->
                        <div class="card-overlay">
                            <h4 class="overlay-title">{{ $integration['name'] }}</h4>
                            <p class="overlay-description">{{ $integration['detail'] ?? 'Bulut tabanlı santral çözümü ile müşteri iletişiminizi güçlendirin.' }}</p>
                            <ul class="overlay-features">
                                @if(isset($integration['features']))
                                    @foreach($integration['features'] as $feature)
                                        <li>{{ $feature }}</li>
                                    @endforeach
                                @else
                                    <li>Arayan numara tanıma</li>
                                    <li>Müşteri kartı popup</li>
                                    <li>Arama geçmişi kaydı</li>
                                    <li>Webhook entegrasyonu</li>
                                @endif
                            </ul>
                            <span class="overlay-badge">Aktif Entegrasyon</span>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
            
            @foreach($integrations['Santral'] as $integration)
            <div class="col-lg-4 col-md-6">
                <div class="integration-card-soft">
                    <div class="integration-logo-soft">
                        <img src="{{ asset($integration['logo']) }}" alt="{{ $integration['name'] }}">
                    </div>
                    <h3 class="integration-name-soft">{{ $integration['name'] }}</h3>
                    <span class="integration-category-tag">Santral</span>
                    <p class="integration-description-soft">{{ $integration['description'] }}</p>
                    
                    <!-- Hover Overlay -->
                    <div class="card-overlay">
                        <h4 class="overlay-title">{{ $integration['name'] }}</h4>
                        <p class="overlay-description">{{ $integration['detail'] ?? 'Verimor santral sistemi ile iş iletişiminizi yönetin.' }}</p>
                        <ul class="overlay-features">
                            @if(isset($integration['features']))
                                @foreach($integration['features'] as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            @else
                                <li>Bulut santral</li>
                                <li>Sesli yanıt sistemi</li>
                                <li>Çağrı yönlendirme</li>
                            @endif
                        </ul>
                        <span class="overlay-badge">Aktif Entegrasyon</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Diğer Entegrasyonlar -->
<section class="integration-category-section gray-bg">
    <div class="container">
        <div class="category-header-simple">
            <h2 class="category-title">Diğer Entegrasyonlar</h2>
            <p class="category-description">İş süreçlerinizi kolaylaştıran ek araçlar</p>
        </div>
        
        <div class="row g-4 justify-content-center">
            @foreach($integrations['Diğer'] as $integration)
                @if($integration['name'] != 'HIPCALL')
                <div class="col-lg-4 col-md-6">
                    <div class="integration-card-soft">
                        <div class="integration-logo-soft">
                            <img src="{{ asset($integration['logo']) }}" alt="{{ $integration['name'] }}">
                        </div>
                        <h3 class="integration-name-soft">{{ $integration['name'] }}</h3>
                        <span class="integration-category-tag">{{ $integration['category'] }}</span>
                        <p class="integration-description-soft">{{ $integration['description'] }}</p>
                        
                        <!-- Hover Overlay -->
                        <div class="card-overlay">
                            <h4 class="overlay-title">{{ $integration['name'] }}</h4>
                            <p class="overlay-description">{{ $integration['detail'] ?? $integration['description'] }}</p>
                            <ul class="overlay-features">
                                @if(isset($integration['features']))
                                    @foreach($integration['features'] as $feature)
                                        <li>{{ $feature }}</li>
                                    @endforeach
                                @else
                                    <li>Hızlı erişim</li>
                                    <li>Kapsamlı veritabanı</li>
                                    <li>Otomatik güncelleme</li>
                                @endif
                            </ul>
                            <span class="overlay-badge">Ücretsiz</span>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</section>

@endsection