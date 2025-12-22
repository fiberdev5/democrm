{{-- @extends('frontend.main_master')
@section('main')

  @include('frontend.home_all.slider')

  @include('frontend.home_all.home_about')

  @include('frontend.home_all.home_products')

  @include('frontend.home_all.home_section')

  @include('frontend.home_all.home_references')
@endsection --}}
@extends('frontend.main_master')

@section('title', 'Serbis - Teknik Servis Yönetim Sistemi')

@section('main')

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="hero-title">
                    Teknik Servis İşletmenizi <span class="highlight">Dijitalleştirin</span>
                </h1>
                <p class="hero-description">
                    Müşteri, servis, stok ve personel yönetiminizi tek platformdan yönetin. 
                    İşlerinizi hızlandırın, maliyetleri düşürün, müşteri memnuniyetini artırın.
                </p>
                <div class="hero-buttons">
                    <a href="{{ url('/kullanici-girisi') }}" class="btn-hero-primary" target="_blank">
                        <i class="fas fa-rocket"></i> Hemen Başla
                    </a>
                    <a href="#" class="btn-hero-secondary" data-bs-toggle="modal" data-bs-target="#videoModal">
                        <i class="fas fa-play-circle"></i> Demo İzle
                    </a>
                </div>
                <div class="hero-features">
                    <div class="hero-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>14 gün ücretsiz</span>
                    </div>
                    <div class="hero-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Kredi kartı gerektirmez</span>
                    </div>
                    <div class="hero-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>Anında kurulum</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0">
                <div class="hero-image">
                    <img src="{{ asset('frontend/img/1782735853554914.jpg') }}" alt="Serbis Dashboard" onerror="this.src='https://via.placeholder.com/600x450/0066FF/ffffff?text=Serbis+Dashboard'">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row">
            @foreach($stats as $stat)
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-item">
                    <div class="stat-number">{{ $stat['number'] }}</div>
                    <div class="stat-label">{{ $stat['label'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Modules Section -->
<section class="modules-section section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">MODÜLLER</span>
            <h2 class="section-title">Güçlü <span class="accent">Modüller</span></h2>
            <p class="section-subtitle">
                İşletmenizi yönetmek için ihtiyacınız olan tüm modüller tek platformda
            </p>
        </div>
        <div class="row g-4">
            @foreach($modules as $module)
            <div class="col-md-6 col-lg-4">
                <div class="card-item">
                    <div class="card-icon {{ $module['color'] == 'orange' ? 'orange' : '' }}">
                        <i class="{{ $module['icon'] }}"></i>
                    </div>
                    <h3 class="card-title">{{ $module['title'] }}</h3>
                    <p class="card-description">
                        {{ $module['description'] }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Sectors Section -->
<section class="sectors-section section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">SEKTÖRLER</span>
            <h2 class="section-title">Hangi <span class="accent">Sektörlere</span> Hizmet Veriyoruz?</h2>
            <p class="section-subtitle">
                Farklı sektörlerdeki teknik servis işletmelerinin ihtiyaçlarına özel çözümler
            </p>
        </div>
        <div class="row g-4">
            @foreach($sectors as $sector)
            <div class="col-md-6 col-lg-3">
                <div class="sector-card">
                    <div class="sector-icon">
                        <i class="{{ $sector['icon'] }}"></i>
                    </div>
                    <h3 class="sector-title">{{ $sector['title'] }}</h3>
                    <p class="sector-description">
                        {{ $sector['description'] }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Integrations Carousel -->
<section class="integrations-section section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">ENTEGRASYONLAR</span>
            <h2 class="section-title">Güçlü <span class="accent">Entegrasyonlar</span></h2>
            <p class="section-subtitle">
                Kullandığınız tüm araçlarla sorunsuz entegre olun
            </p>
        </div>
        
        <div id="integrationsCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @php $chunks = collect($integrations)->chunk(3); @endphp
                @foreach($chunks as $chunkIndex => $chunk)
                <div class="carousel-item {{ $chunkIndex == 0 ? 'active' : '' }}">
                    <div class="row g-4">
                        @foreach($chunk as $integration)
                        <div class="col-md-4">
                            <div class="card-item text-center">
                                <div class="card-icon {{ $integration['color'] == 'orange' ? 'orange' : '' }}" style="margin: 0 auto 1.5rem;">
                                    <i class="{{ $integration['icon'] }}"></i>
                                </div>
                                <h3 class="card-title">{{ $integration['title'] }}</h3>
                                <p class="card-description">{{ $integration['description'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            
            <button class="carousel-control-prev" type="button" data-bs-target="#integrationsCarousel" data-bs-slide="prev" style="width: 50px; left: -80px;">
                <span style="width: 50px; height: 50px; background: var(--primary-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-chevron-left" style="color: white;"></i>
                </span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#integrationsCarousel" data-bs-slide="next" style="width: 50px; right: -80px;">
                <span style="width: 50px; height: 50px; background: var(--primary-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-chevron-right" style="color: white;"></i>
                </span>
            </button>
            
            <div class="carousel-indicators" style="position: relative; margin-top: 3rem;">
                @foreach($chunks as $index => $chunk)
                <button type="button" data-bs-target="#integrationsCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}" style="background: {{ $index == 0 ? 'var(--primary-blue)' : 'var(--gray)' }}; width: 12px; height: 12px; border-radius: 50%;"></button>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Carousel -->
<section class="sectors-section section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">MÜŞTERİ YORUMLARI</span>
            <h2 class="section-title">Müşterilerimiz <span class="accent">Ne Diyor?</span></h2>
            <p class="section-subtitle">Binlerce mutlu müşterimizden bazı görüşler</p>
        </div>
        
        <div id="testimonialsCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach($testimonials as $index => $testimonial)
                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="card-item text-center" style="padding: 3rem;">
                                <div style="margin-bottom: 2rem;">
                                    <i class="fas fa-quote-left" style="font-size: 3rem; color: var(--orange); opacity: 0.3;"></i>
                                </div>
                                <p style="font-size: 1.2rem; font-style: italic; margin-bottom: 2.5rem; color: var(--dark); line-height: 1.8;">
                                    "{{ $testimonial['quote'] }}"
                                </p>
                                <div style="display: flex; align-items: center; gap: 1rem; justify-content: center;">
                                    <div style="width: 60px; height: 60px; background: {{ $testimonial['color'] == 'blue' ? 'var(--primary-blue)' : 'var(--orange)' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.4rem;">
                                        {{ $testimonial['initials'] }}
                                    </div>
                                    <div style="text-align: left;">
                                        <div style="font-weight: 700; color: var(--dark); font-size: 1.1rem;">{{ $testimonial['name'] }}</div>
                                        <div style="font-size: 1rem; color: var(--gray);">{{ $testimonial['position'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="prev" style="width: 50px; left: 0;">
                <span style="width: 50px; height: 50px; background: var(--primary-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-chevron-left" style="color: white;"></i>
                </span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="next" style="width: 50px; right: 0;">
                <span style="width: 50px; height: 50px; background: var(--primary-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-chevron-right" style="color: white;"></i>
                </span>
            </button>
            
            <div class="carousel-indicators" style="position: relative; margin-top: 2rem;">
                @foreach($testimonials as $index => $testimonial)
                <button type="button" data-bs-target="#testimonialsCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}" style="background: {{ $index == 0 ? 'var(--primary-blue)' : 'var(--gray)' }}; width: 12px; height: 12px; border-radius: 50%;"></button>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="modules-section section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">SIK SORULAN SORULAR</span>
            <h2 class="section-title">Sıkça Sorulan <span class="accent">Sorular</span></h2>
            <p class="section-subtitle">Merak ettiğiniz soruların cevaplarını burada bulabilirsiniz</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    @foreach($faqs as $index => $faq)
                    <div class="accordion-item" style="border: 1px solid var(--border); border-radius: 12px; margin-bottom: 1rem; overflow: hidden;">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $index != 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index }}" style="background: white; color: var(--dark); font-weight: 600; padding: 1.5rem;">
                                {{ $faq['question'] }}
                            </button>
                        </h2>
                        <div id="faq{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="padding: 1.5rem; color: var(--gray);">
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

<!-- Contact Section -->
<section id="iletisim" class="contact-section section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">İLETİŞİM</span>
            <h2 class="section-title">Bizimle <span class="accent">İletişime Geçin</span></h2>
            <p class="section-subtitle">Sorularınız mı var? Size yardımcı olmaktan memnuniyet duyarız</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h3 class="contact-title">Telefon</h3>
                    <p class="contact-info">0212 909 2861</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3 class="contact-title">E-posta</h3>
                    <p class="contact-info">info@serbis.com</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3 class="contact-title">Adres</h3>
                    <p class="contact-info">İstanbul, Türkiye</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2 class="cta-title">Hemen Başlamaya Hazır mısınız?</h2>
        <p class="cta-description">
            14 gün ücretsiz deneyin. Kredi kartı gerekmez. İstediğiniz zaman iptal edebilirsiniz.
        </p>
        <button class="btn btn-cta" onclick="window.open('{{ url('/kullanici-girisi')}}', '_blank')">
            <i class="fas fa-rocket me-2"></i> Ücretsiz Denemeyi Başlat
        </button>
    </div>
</section>

<!-- Video Modal -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <div class="ratio ratio-16x9">
                    <iframe id="videoIframe" src="" title="Serbis Demo Video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-play carousels
        var testimonialsCarousel = new bootstrap.Carousel(document.getElementById('testimonialsCarousel'), {
            interval: 5000,
            ride: 'carousel'
        });

        var integrationsCarousel = new bootstrap.Carousel(document.getElementById('integrationsCarousel'), {
            interval: 4000,
            ride: 'carousel'
        });

        // Video Modal
        var videoModal = document.getElementById('videoModal');
        var videoIframe = document.getElementById('videoIframe');
        var videoUrl = 'https://www.youtube.com/embed/Caa1CJUFFIs?autoplay=1&rel=0';

        videoModal.addEventListener('show.bs.modal', function () {
            videoIframe.src = videoUrl;
        });

        videoModal.addEventListener('hide.bs.modal', function () {
            videoIframe.src = '';
        });
    });
</script>
