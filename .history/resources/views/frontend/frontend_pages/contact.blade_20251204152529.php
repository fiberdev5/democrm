@extends('frontend.main_master')

@section('title', 'İletişim - Serbis')

@section('main')

<!-- Hero Section -->
<section class="contact-hero">
    <div class="container">
        <div class="contact-hero-content">
            <div class="contact-badge">
                <i class="fas fa-headset"></i>
                7/24 Destek
            </div>
            <h1 class="contact-hero-title">Bizimle <span>İletişime</span> Geçin</h1>
            <p class="contact-hero-description">
                Sorularınız mı var? Size yardımcı olmaktan mutluluk duyarız. Formu doldurun veya doğrudan bize ulaşın.
            </p>
        </div>
    </div>
</section>

<!-- Contact Info Cards -->
<section class="contact-info-section">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-4 col-md-6">
                <div class="contact-info-card">
                    <div class="contact-info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3 class="contact-info-title">E-posta</h3>
                    <p class="contact-info-text">
                        <a href="mailto:info@fibermedya.com.tr">info@fibermedya.com.tr</a>
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="contact-info-card">
                    <div class="contact-info-icon blue">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h3 class="contact-info-title">Telefon Numarası</h3>
                    <p class="contact-info-text">
                        <a href="tel:+902129092861">0212 909 2861</a>
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="contact-info-card">
                    <div class="contact-info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="contact-info-title">Çalışma Saatleri</h3>
                    <p class="contact-info-text">
                        Pazartesi - Cuma: 09:00 - 18:00
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Contact Section -->
<section class="contact-main-section" id="iletisim">
    <div class="container">
        <div class="row align-items-stretch">
            <!-- Left Side -->
            <div class="col-lg-5 mb-4 mb-lg-0">
                <div class="contact-left-content">
                    <h2 class="contact-cta-title">
                        Serbis'i denemeye <span>hazır mısınız?</span>
                    </h2>
                    <p class="contact-cta-text">
                        Teknik servis süreçlerinizi dijitalleştirmek için hemen formu doldurun. Size en kısa sürede dönüş yapacağız.
                    </p>
                    
                    <!-- Why Contact List -->
                    <ul class="why-contact-list">
                        <li>
                            <div class="icon"><i class="fas fa-check"></i></div>
                            <span>14 gün ücretsiz deneme hakkı</span>
                        </li>
                        <li>
                            <div class="icon"><i class="fas fa-check"></i></div>
                            <span>Kurulum ve eğitim desteği</span>
                        </li>
                        <li>
                            <div class="icon"><i class="fas fa-check"></i></div>
                            <span>Kişiselleştirilmiş demo sunumu</span>
                        </li>
                        <li>
                            <div class="icon"><i class="fas fa-check"></i></div>
                            <span>Teknik destek hizmeti</span>
                        </li>
                    </ul>
                    
                    <!-- App Download -->
                    <div class="app-download-section">
                        <h4 class="app-download-title">Uygulamayı Şimdi İndirin</h4>
                        <div class="app-download-buttons">
                            <a href="#" class="app-btn">
                                <i class="fab fa-google-play"></i>
                                <div class="btn-text">
                                    <small>GET IT ON</small>
                                    <span>Google Play</span>
                                </div>
                            </a>
                            <a href="#" class="app-btn">
                                <i class="fab fa-apple"></i>
                                <div class="btn-text">
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
                <div class="contact-form-card">
                    <h3 class="contact-form-title">Bize Ulaşın</h3>
                    <p class="contact-form-subtitle">Formu doldurun, size en kısa sürede dönelim.</p>
                    
                    <form action="#" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Ad Soyad</label>
                                <input type="text" class="form-control" name="name" placeholder="Adınız Soyadınız" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">E-posta</label>
                                <input type="email" class="form-control" name="email" placeholder="ornek@email.com" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Telefon Numarası</label>
                            <input type="tel" class="form-control" name="phone" placeholder="0500 000 00 00">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Firma Adı</label>
                            <input type="text" class="form-control" name="company" placeholder="Firma adınız (opsiyonel)">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Mesajınız</label>
                            <textarea class="form-control" name="message" placeholder="Mesajınızı buraya yazın..." rows="4" required></textarea>
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
<section class="map-section">
    <div class="map-container">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3008.123456789!2d28.9783589!3d41.0082376!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDHCsDAwJzI5LjciTiAyOMKwNTgnNDIuMSJF!5e0!3m2!1str!2str!4v1234567890"
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
        <div class="map-overlay">
            <div class="map-overlay-icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="map-overlay-text">
                İstanbul, Türkiye<br>
                <small style="color: var(--gray);">Merkez Ofis</small>
            </div>
        </div>
    </div>
</section>

<!-- Social Section -->
<section class="social-section">
    <div class="container">
        <h2 class="social-title">Sosyal Medyada Bizi Takip Edin</h2>
        <p class="social-text">Güncel haberler ve özel kampanyalar için sosyal medya hesaplarımızı takip edin</p>
        <div class="social-links">
            <a href="#" class="social-link" title="Facebook">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="#" class="social-link" title="Twitter">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="#" class="social-link" title="Instagram">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="#" class="social-link" title="LinkedIn">
                <i class="fab fa-linkedin-in"></i>
            </a>
            <a href="#" class="social-link" title="YouTube">
                <i class="fab fa-youtube"></i>
            </a>
        </div>
    </div>
</section>

@endsection