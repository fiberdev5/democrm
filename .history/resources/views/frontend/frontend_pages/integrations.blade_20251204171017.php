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
                        {{-- <h4 class="overlay-title">{{ $integration['name'] }}</h4> --}}
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
                            
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="modules-section section" style="padding: 60px 0; background-color: #fff;">
    <div class="container">
        <div class="section-header text-center mb-5">
            <span class="section-badge" style="color: var(--primary); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 0.85rem;">MERAK EDİLENLER</span>
            <h2 class="section-title mt-2" style="font-size: 2.5rem; font-weight: 700;">Sıkça Sorulan <span class="accent" style="color: var(--primary);">Sorular</span></h2>
            <p class="section-subtitle mt-3" style="color: #6c757d; max-width: 600px; margin-left: auto; margin-right: auto;">
                Entegrasyon süreçleri, kurulum ve teknik detaylar hakkında merak ettiğiniz soruların cevapları.
            </p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    @foreach($faqs as $index => $faq)
                    <div class="accordion-item" style="border: 1px solid #e9ecef; border-radius: 12px; margin-bottom: 1rem; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $index != 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index }}" 
                                style="background: white; color: #2d3436; font-weight: 600; padding: 1.5rem; border: none; box-shadow: none;">
                                {{ $faq['question'] }}
                            </button>
                        </h2>
                        <div id="faq{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="padding: 0 1.5rem 1.5rem 1.5rem; color: #636e72; line-height: 1.6;">
                                {{ $faq['answer'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>


@endsection