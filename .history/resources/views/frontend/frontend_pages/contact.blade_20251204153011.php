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
                <div class="contact-card">
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
                <div class="contact-card">
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
                <div class="contact-card">
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
        <div class="row align-items-start">
            <!-- Left Side -->
            <div class="col-lg-5">
                <div class="contact-info-side">
                    <h2 class="contact-info-title">
                        Serbis CRM'i denemeye hazır mısınız? <span>Lütfen yandaki formu doldurun</span>
                    </h2>
                    <p class="contact-info-desc">
                        Teknik servis süreçlerinizi dijitalleştirmek için formu doldurun, uzman ekibimiz size en kısa sürede dönüş yapsın.
                    </p>
                    
                    <!-- Features -->
                    <div class="contact-features">
                        <div class="contact-feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-gift"></i>
                            </div>
                            <div class="feature-content">
                                <h4>14 Gün Ücretsiz Deneme</h4>
                                <p>Kredi kartı gerektirmeden başlayın</p>
                            </div>
                        </div>
                        <div class="contact-feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div class="feature-content">
                                <h4>7/24 Teknik Destek</h4>
                                <p>Her zaman yanınızdayız</p>
                            </div>
                        </div>
                        <div class="contact-feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="feature-content">
                                <h4>Ücretsiz Eğitim</h4>
                                <p>Kurulum ve kullanım desteği</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- App Download -->
                    <div class="app-download-box">
                        <p class="app-download-label">Uygulamayı Şimdi İndirin</p>
                        <div class="app-buttons">
                            <a href="#" class="app-button">
                                <i class="fab fa-google-play"></i>
                                <div class="text">
                                    <small>GET IT ON</small>
                                    <span>Google Play</span>
                                </div>
                            </a>
                            <a href="#" class="app-button">
                                <i class="fab fa-apple"></i>
                                <div class="text">
                                    <small>Download on the</small>
                                    <span>App Store</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - Form -->
            <div class="col-lg-7">
                <div class="contact-form-wrapper">
                    <div class="form-header">
                        <h3 class="form-title">Bize Ulaşın</h3>
                        <p class="form-subtitle">Tüm alanları doldurarak bize mesaj gönderin</p>
                    </div>
                    
                    <form action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Ad-Soyad *</label>
                                <input type="text" class="form-control" name="name" placeholder="Adınız Soyadınız" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">E-posta *</label>
                                <input type="email" class="form-control" name="email" placeholder="ornek@email.com" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Telefon Numarası</label>
                            <input type="tel" class="form-control" name="phone" placeholder="0500 000 00 00">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Mesaj *</label>
                            <textarea class="form-control" name="message" placeholder="Mesajınızı buraya yazın..." required></textarea>
                        </div>
                        
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane"></i>
                            Gönder
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="contact-map-section">
    <div class="container">
        <div class="map-wrapper">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d192697.79327595045!2d28.847329949999997!3d41.00527225!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14caa7040068086b%3A0xe1ccfe98bc01b0d0!2zxLBzdGFuYnVs!5e0!3m2!1str!2str!4v1701234567890"
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
            <div class="map-info-card">
                <div class="map-info-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="map-info-content">
                    <h4>Merkez Ofis</h4>
                    <p>İstanbul, Türkiye</p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection