@extends('frontend.main_master')
@section('title', $sector['title'] . ' - Serbis')
@section('main')

<!-- Hero Section -->
<!-- Process Section -->
<section class="sector-process-section section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">İŞ AKIŞI</span>
            <h2 class="section-title">Sistem Nasıl <span class="accent">İşler?</span></h2>
            <p class="section-subtitle">
                Servis operasyonlarınızı dijitalleştirerek 4 adımda mükemmel yönetim
            </p>
        </div>

        <div class="row g-4">
            <!-- Adım 1 -->
            <div class="col-lg-3 col-md-6">
                <div class="process-card">
                    <div class="process-number">01</div>
                    <div class="process-icon">
                        <i class="fas fa-keyboard"></i>
                    </div>
                    <h3 class="process-title">Hızlı Kayıt</h3>
                    <p class="process-description">Müşteri ve cihaz bilgilerini saniyeler içinde sisteme girin, barkod etiketini basın.</p>
                </div>
            </div>
            
            <!-- Adım 2 -->
            <div class="col-lg-3 col-md-6">
                <div class="process-card">
                    <div class="process-number">02</div>
                    <div class="process-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3 class="process-title">Teknik İşlem</h3>
                    <p class="process-description">Yapılan işlemleri, kullanılan yedek parçaları ve işçiliği servis formuna ekleyin.</p>
                </div>
            </div>
            
            <!-- Adım 3 -->
            <div class="col-lg-3 col-md-6">
                <div class="process-card">
                    <div class="process-number">03</div>
                    <div class="process-icon">
                        <i class="fas fa-sms"></i>
                    </div>
                    <h3 class="process-title">Bilgilendirme</h3>
                    <p class="process-description">Durum değişikliklerinde (Hazır, Fiyat Onayı vb.) müşterinize otomatik SMS/Mail gönderin.</p>
                </div>
            </div>
            
            <!-- Adım 4 -->
            <div class="col-lg-3 col-md-6">
                <div class="process-card">
                    <div class="process-number">04</div>
                    <div class="process-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <h3 class="process-title">Teslimat & Fatura</h3>
                    <p class="process-description">Servis formunu yazdırın, ödemeyi tahsil edin ve garantili olarak cihazı teslim edin.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Stats Section -->
<section class="sector-stats-section">
    <div class="container">
        <div class="row">
            @foreach($sector['stats'] as $stat)
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

<!-- Features Section -->
<section class="sector-features-section section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">ÖZELLİKLER</span>
            <h2 class="section-title">Neden <span class="accent">Bizi Seçmelisiniz?</span></h2>
        </div>
        <div class="row g-4">
            @foreach($sector['features'] as $feature)
            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="{{ $feature['icon'] }}"></i>
                    </div>
                    <h3 class="feature-title">{{ $feature['title'] }}</h3>
                    <p class="feature-description">{{ $feature['description'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="hizmetler" class="sector-services-section section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="section-header text-start">
                    <span class="section-badge">HİZMETLERİMİZ</span>
                    <h2 class="section-title">Sunduğumuz <span class="accent">Hizmetler</span></h2>
                    <p class="section-subtitle text-start">
                        {{ $sector['title'] }} alanında geniş hizmet yelpazesi ile yanınızdayız.
                    </p>
                </div>
                <ul class="services-list">
                    @foreach($sector['services'] as $service)
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>{{ $service }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-lg-6">
                <div class="section-header text-start">
                    <span class="section-badge">AVANTAJLAR</span>
                    <h2 class="section-title">Bizimle Çalışmanın <span class="accent">Avantajları</span></h2>
                    <p class="section-subtitle text-start">
                        Müşteri memnuniyeti odaklı hizmet anlayışımızla fark yaratıyoruz.
                    </p>
                </div>
                <ul class="benefits-list">
                    @foreach($sector['benefits'] as $benefit)
                    <li>
                        <i class="fas fa-star"></i>
                        <span>{{ $benefit }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Process Section -->
<section class="sector-process-section section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">SÜREÇ</span>
            <h2 class="section-title">Nasıl <span class="accent">Çalışıyoruz?</span></h2>
            <p class="section-subtitle">
                Profesyonel hizmet sürecimiz ile sorunsuz deneyim
            </p>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="process-card">
                    <div class="process-number">01</div>
                    <div class="process-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h3 class="process-title">İletişim</h3>
                    <p class="process-description">Bize telefon, email veya online form ile ulaşın</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="process-card">
                    <div class="process-number">02</div>
                    <div class="process-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3 class="process-title">Değerlendirme</h3>
                    <p class="process-description">Uzman ekibimiz arızayı tespit eder ve fiyat verir</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="process-card">
                    <div class="process-number">03</div>
                    <div class="process-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3 class="process-title">Onarım</h3>
                    <p class="process-description">Onayınız sonrası hızlı ve kaliteli onarım yapılır</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="process-card">
                    <div class="process-number">04</div>
                    <div class="process-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3 class="process-title">Teslimat</h3>
                    <p class="process-description">Cihazınız test edilerek garanti ile teslim edilir</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
{{-- <section id="iletisim" class="cta-section">
    <div class="container">
        <h2 class="cta-title">{{ $sector['title'] }} İçin Hemen Teklif Alın</h2>
        <p class="cta-description">
            Profesyonel ekibimiz size en iyi hizmeti sunmak için hazır. Ücretsiz ön değerlendirme için iletişime geçin.
        </p>
        <div class="cta-buttons">
            <button class="btn btn-cta" onclick="window.location.href='tel:02129092861'">
                <i class="fas fa-phone me-2"></i> 0212 909 2861
            </button>
            <button class="btn btn-cta" style="background: var(--orange); margin-left: 1rem;" onclick="window.open('{{ url('/kullanici-girisi')}}', '_blank')">
                <i class="fas fa-envelope me-2"></i> Online Talep Oluştur
            </button>
        </div>
    </div>
</section> --}}

<!-- Other Sectors -->
<section class="other-sectors-section section">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">DİĞER SEKTÖRLER</span>
            <h2 class="section-title">Diğer <span class="accent">Hizmetlerimiz</span></h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <a href="{{ route('sector.detail', 'elektrik-elektronik') }}" class="other-sector-card">
                    <i class="fas fa-plug"></i>
                    <h4>Elektrik-Elektronik</h4>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a href="{{ route('sector.detail', 'beyaz-esya') }}" class="other-sector-card">
                    <i class="fas fa-tv"></i>
                    <h4>Beyaz Eşya</h4>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a href="{{ route('sector.detail', 'klima-sogutma') }}" class="other-sector-card">
                    <i class="fas fa-fan"></i>
                    <h4>Klima-Soğutma</h4>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a href="{{ route('sector.detail', 'bilgisayar-teknoloji') }}" class="other-sector-card">
                    <i class="fas fa-laptop"></i>
                    <h4>Bilgisayar-Teknoloji</h4>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection