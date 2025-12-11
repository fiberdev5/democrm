@extends('frontend.main_master')

@section('title', 'Hakkımızda - Serbis')

@section('main')

<!-- Hero Section -->
<section class="about-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="about-hero-content">
                    <div class="about-badge">
                        <i class="fas fa-heart"></i>
                        Türkiye'nin Teknik Servis Yazılımı
                    </div>
                    <h1 class="about-hero-title">
                        Teknik Servislerin <span>Dijital Dönüşüm</span> Ortağı
                    </h1>
                    <p class="about-hero-description">
                        Serbis olarak, teknik servis sektörünün dijitalleşme ihtiyacını yakından tanıyoruz. 
                        Yılların deneyimini modern teknolojiyle birleştirerek, işinizi büyütmenize yardımcı oluyoruz.
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-hero-image">
                    <img src="{{ asset('frontend/img/about/hakkimizda1.jpeg') }}" alt="Serbis Ekibi" onerror="this.src='https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=600&h=400&fit=crop'">
                    
                    <div class="floating-stat stat-1">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <div class="stat-number">500+</div>
                            <div class="stat-label">Aktif Firma</div>
                        </div>
                    </div>
                    
                    <div class="floating-stat stat-2">
                        <div class="stat-icon orange">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div>
                            <div class="stat-number">1M+</div>
                            <div class="stat-label">Servis Kaydı</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<section class="mission-vision-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="mv-card">
                    <div class="mv-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3 class="mv-title">Misyonumuz</h3>
                    <p class="mv-text">
                        Teknik servis firmalarının iş süreçlerini dijitalleştirerek, zamandan tasarruf etmelerini 
                        ve müşteri memnuniyetini artırmalarını sağlamak. Her ölçekteki teknik servise, kurumsal 
                        düzeyde yazılım çözümleri sunarak sektörün gelişimine katkıda bulunmak.
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mv-card">
                    <div class="mv-icon orange">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3 class="mv-title">Vizyonumuz</h3>
                    <p class="mv-text">
                        Türkiye'nin lider teknik servis yönetim platformu olmak ve teknik servis sektörünün 
                        dijital dönüşümüne öncülük etmek. Global standartlarda bir yazılım geliştirerek, 
                        sektörün referans noktası haline gelmek.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Story Section -->
<section class="story-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2">
                <div class="story-image-container">
                    <img src="{{ asset('frontend/img/about/hakkimizda2.jpeg') }}" alt="Hikayemiz" onerror="this.src='https://images.unsplash.com/photo-1553877522-43269d4ea984?w=600&h=450&fit=crop'">
                    <div class="story-highlight-box">
                        <div class="highlight-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div class="highlight-text">
                            Sektörün ihtiyaçlarını bilen bir ekip tarafından geliştirildi
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 order-lg-1">
                <div class="section-header text-start">
                    <h2 class="section-title">Hikayemiz</h2>
                </div>
                <div class="story-timeline">
                    <div class="story-item">
                        <div class="story-year">Başlangıç</div>
                        <h4 class="story-title">Sektörden Gelen Deneyim</h4>
                        <p class="story-text">
                            Teknik servis sektöründe yıllarca çalışmış bir ekip olarak, sektörün yaşadığı 
                            zorlukları ve ihtiyaçları yakından gözlemledik.
                        </p>
                    </div>
                    <div class="story-item">
                        <div class="story-year">Geliştirme</div>
                        <h4 class="story-title">Çözüm Odaklı Yaklaşım</h4>
                        <p class="story-text">
                            Excel tablolarından, dağınık notlardan ve kaotik iş süreçlerinden kurtuluş 
                            için kapsamlı bir platform geliştirdik.
                        </p>
                    </div>
                    <div class="story-item">
                        <div class="story-year">Bugün</div>
                        <h4 class="story-title">Sürekli Gelişim</h4>
                        <p class="story-text">
                            Kullanıcı geri bildirimleriyle sürekli geliştirilen Serbis, yüzlerce teknik 
                            servisin güvendiği bir platform haline geldi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="values-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Değerlerimiz</h2>
            <p class="section-description">
                Her kararımızda bizi yönlendiren temel ilkeler
            </p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h4 class="value-title">Güvenilirlik</h4>
                    <p class="value-text">
                        Verileriniz bizim için değerli. Güvenlik ve gizlilik en öncelikli konularımız.
                    </p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="value-card">
                    <div class="value-icon orange">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h4 class="value-title">Yenilikçilik</h4>
                    <p class="value-text">
                        Teknolojiyi yakından takip eder, en güncel çözümleri platformumuza entegre ederiz.
                    </p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4 class="value-title">Müşteri Odaklılık</h4>
                    <p class="value-text">
                        Kullanıcı deneyimi her şeyin merkezinde. Geri bildirimleriniz bizi şekillendirir.
                    </p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="value-card">
                    <div class="value-icon orange">
                        <i class="fas fa-gem"></i>
                    </div>
                    <h4 class="value-title">Kalite</h4>
                    <p class="value-text">
                        Detaylara önem verir, her özelliği titizlikle geliştiririz. Kaliteden ödün vermeyiz.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-6 col-md-3">
                <div class="stat-box">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Aktif Firma</div>
                </div>
            </div>
            <div class="col-auto d-none d-md-block">
                <div class="stat-divider"></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-box">
                    <div class="stat-number">50K+</div>
                    <div class="stat-label">Müşteri Kaydı</div>
                </div>
            </div>
            <div class="col-auto d-none d-md-block">
                <div class="stat-divider"></div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-box">
                    <div class="stat-number">1M+</div>
                    <div class="stat-label">Servis Kaydı</div>
                </div>
            </div>
            <div class="col-auto d-none d-md-block">
                <div class="stat-divider"></div>
            </div>
            <div class="col-6 col-md-2">
                <div class="stat-box">
                    <div class="stat-number">10+</div>
                    <div class="stat-label">Sektör</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="team-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Ekibimiz</h2>
            <p class="section-description">
                Serbis'in arkasındaki tutkulu ekip
            </p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="team-intro">
                    <div class="team-intro-icon">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <h3>Deneyimli & Tutkulu Bir Ekip</h3>
                    <p>
                        Serbis ekibi, teknik servis sektörünü yakından tanıyan yazılım geliştiricileri, 
                        tasarımcılar ve müşteri destek uzmanlarından oluşmaktadır. Her birimiz, 
                        teknik servislerin günlük yaşadığı zorlukları çözmek için tutkuyla çalışıyoruz.
                    </p>
                    <div class="team-tags">
                        <span class="team-tag"><i class="fas fa-code"></i> Yazılım Geliştirme</span>
                        <span class="team-tag"><i class="fas fa-paint-brush"></i> UI/UX Tasarım</span>
                        <span class="team-tag"><i class="fas fa-headset"></i> Müşteri Desteği</span>
                        <span class="team-tag"><i class="fas fa-chart-line"></i> İş Analizi</span>
                        <span class="team-tag"><i class="fas fa-shield-alt"></i> Güvenlik</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
{{-- <section class="about-cta-section">
    <div class="container">
        <div class="cta-content">
            <h2 class="cta-title">Serbis Ailesine Katılın</h2>
            <p class="cta-text">
                Teknik servisinizi dijitalleştirmek ve işinizi büyütmek için hemen ücretsiz deneme hesabı oluşturun.
            </p>
            <div class="cta-buttons">
                <a href="{{ url('/kullanici-girisi') }}" class="btn-cta-primary" target="_blank">
                    <i class="fas fa-rocket"></i> Ücretsiz Deneyin
                </a>
                <a href="{{ url('/#iletisim') }}" class="btn-cta-secondary">
                    <i class="fas fa-envelope"></i> Bize Ulaşın
                </a>
            </div>
        </div>
    </div>
</section> --}}

@endsection