@extends('frontend.main_master')

@section('title', 'Sektörler - Serbis')

@section('main')

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="page-title">Hizmet Verdiğimiz <span class="accent">Sektörler</span></h1>
                <p class="page-subtitle">
                    Farklı sektörlerdeki teknik servis işletmelerinin ihtiyaçlarına özel çözümler sunuyoruz
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Sectors Grid -->
<section class="sectors-grid-section section">
    <div class="container">
        <div class="row g-4">
            @foreach($sectors as $sector)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="sector-card-detailed">
                    <div class="sector-image">
                        <img src="{{ asset($sector['image']) }}" alt="{{ $sector['title'] }}" onerror="this.src='https://via.placeholder.com/400x300/49657B/ffffff?text={{ $sector['title'] }}'">
                        <div class="sector-overlay">
                            <i class="{{ $sector['icon'] }}"></i>
                        </div>
                    </div>
                    <div class="sector-content">
                        <h3 class="sector-card-title">{{ $sector['title'] }}</h3>
                        <p class="sector-card-description">{{ $sector['short_description'] }}</p>
                        <ul class="sector-features">
                            @foreach($sector['features'] as $feature)
                            <li><i class="fas fa-check"></i> {{ $feature }}</li>
                            @endforeach
                        </ul>
                        <a href="#" class="btn-sector-detail">Detaylı Bilgi <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2 class="cta-title">Sektörünüze Özel Çözüm mü Arıyorsunuz?</h2>
        <p class="cta-description">
            Size en uygun paketi birlikte belirleyelim. Uzman ekibimiz size yardımcı olmak için hazır.
        </p>
        <button class="btn btn-cta" onclick="window.open('{{ url('/kullanici-girisi')}}', '_blank')">
            <i class="fas fa-phone me-2"></i> Bizimle İletişime Geçin
        </button>
    </div>
</section>

@endsection