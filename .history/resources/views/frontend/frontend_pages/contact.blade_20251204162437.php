@extends('frontend.main_master')

@section('title', 'İletişim - Serbis')

@section('main')

<!-- Hero Section -->
<section class="contact-hero">
    <div class="container">
        <div class="contact-hero-content">
            <h1 class="contact-hero-title">İletişim</h1>
            <p class="contact-hero-subtitle">
                Sorularınız için bize ulaşın, size yardımcı olmaktan mutluluk duyarız.
            </p>
            <div class="contact-breadcrumb">
                <a href="{{ route('home') }}">Ana Sayfa</a>
                <span>/</span>
                <span class="current">İletişim</span>
            </div>
        </div>
    </div>
</section>

<!-- Contact Cards -->
<section class="contact-cards-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <!-- Sınıf ismi değiştirildi: contact-card -> contact-card-page -->
                <div class="contact-card-page">
                    <div class="contact-card-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3 class="contact-card-title">E-posta</h3>
                    <p class="contact-card-text">
                        <a href="mailto:info@fibermedya.com.tr">info@fibermedya.com.tr</a>
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <!-- Sınıf ismi değiştirildi -->
                <div class="contact-card-page">
                    <div class="contact-card-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h3 class="contact-card-title">Telefon Numarası</h3>
                    <p class="contact-card-text">
                        <a href="tel:+902129092861">0212 909 2861</a>
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <!-- Sınıf ismi değiştirildi -->
                <div class="contact-card-page">
                    <div class="contact-card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="contact-card-title">Çalışma Saatleri</h3>
                    <p class="contact-card-text">
                        Pzt - Cuma: 09:00 - 18:00
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Contact Section -->
<section class="contact-main-section" id="iletisim">
    <div class="container">
        <!-- Unified Contact Wrapper -->
        <div class="contact-split-wrapper">
            <div class="row g-0">
                <!-- Left Side: Dark Info Panel -->
                <div class="col-lg-5">
                    <div class="contact-left-panel">
                        <div class="panel-content">
                            <h2 class="panel-title">
                                Serbis CRM ile <br><span>İşinizi Büyütün</span>
                            </h2>
                            <p class="panel-desc">
                                Teknik servis süreçlerinizi dijitalleştirmek için formu doldurun. Uzman ekibimiz size özel çözüm önerileriyle en kısa sürede dönüş yapsın.
                            </p>
                            
                            <!-- Modern Feature Grid -->
                            <div class="panel-features">
                                <div class="p-feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>14 Gün Ücretsiz Deneme</span>
                                </div>
                                <div class="p-feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Kredi Kartı Gerekmez</span>
                                </div>
                                <div class="p-feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>7/24 Teknik Destek</span>
                                </div>
                                <div class="p-feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Hızlı Kurulum</span>
                                </div>
                            </div>

                            <!-- App Download Minimal -->
                            <div class="panel-apps">
                                <p class="apps-label">Mobil Uygulamamızı İndirin:</p>
                                <div class="apps-buttons">
                                    <a href="#" class="app-btn-light">
                                        <i class="fab fa-google-play"></i>
                                    </a>
                                    <a href="#" class="app-btn-light">
                                        <i class="fab fa-apple"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Background Pattern Decoration -->
                        <div class="panel-bg-pattern"></div>
                    </div>
                </div>
                
                <!-- Right Side: Clean Form -->
                <div class="col-lg-7">
                    <div class="contact-right-panel">
                        <div class="form-header-clean">
                            <h3>Bize Ulaşın</h3>
                            <p>Aşağıdaki formu doldurarak bize mesaj gönderin.</p>
                        </div>
                        
                        <form action="{{ route('contact.submit') }}" method="POST" class="modern-form">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="input-group-modern">
                                        <label>Ad-Soyad</label>
                                        <input type="text" name="name" required placeholder="Adınız Soyadınız">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group-modern">
                                        <label>E-posta</label>
                                        <input type="email" name="email" required placeholder="ornek@email.com">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-group-modern">
                                        <label>Telefon</label>
                                        <input type="tel" name="phone" placeholder="0555 555 55 55">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-group-modern">
                                        <label>Mesajınız</label>
                                        <textarea name="message" required placeholder="Size nasıl yardımcı olabiliriz?"></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="submit-btn-modern">
                                        <span>Mesajı Gönder</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection