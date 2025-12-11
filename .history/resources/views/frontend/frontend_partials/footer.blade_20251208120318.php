<footer class="footer">
    <div class="container">
        <div class="row">
            <!-- Açıklama -->
            <div class="col-lg-4 mb-4 mb-lg-0">
                <!-- Başlık (diğer menülerle hizalı) -->
                <h5 class="footer-title">Hakkımızda</h5>
                
                <p class="footer-description" style="font-size: 1rem; line-height: 1.6; margin-bottom: 1.5rem;">
                    Teknik servis işletmeleri için yeni nesil, bulut tabanlı yönetim sistemi.
                </p>
                
                <!-- Mobil Uygulama Linkleri -->
                <div class="mb-3">
                    <p class="mb-2" style="font-weight: 600; color: white; font-size: 0.95rem;">Mobil Uygulamayı İndirin</p>
                    <div class="d-flex gap-2">
                        <a href="#" class="app-store-badge">
                            <img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg" alt="App Store" style="height: 40px;">
                        </a>
                        <a href="#" class="google-play-badge">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Google Play" style="height: 40px;">
                        </a>
                    </div>
                </div>
                
                <!-- Sosyal Medya -->
                <div class="footer-social">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            
            <!-- Ürün -->
            <div class="col-lg-2 col-6 mb-4 mb-lg-0">
                <h5 class="footer-title">Ürün</h5>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">Anasayfa</a></li>
                    <li><a href="{{ url('/hakkimizda') }}">Hakkımızda</a></li>
                    <li><a href="{{ route('sectors') }}">Sektörler</a></li>
                    <li><a href="{{ url('/ozellikler') }}">Özellikler</a></li>
                    <li><a href="{{ url('/entegrasyonlar') }}">Entegrasyonlar</a></li>
                    <li><a href="{{ url('/fiyatlar') }}">Fiyatlar</a></li>
                </ul>
            </div>
            
            <!-- Özellikler -->
            <div class="col-lg-3 col-6 mb-4 mb-lg-0">
                <h5 class="footer-title">Özellikler</h5>
                <ul class="footer-links">
                    <li><a href="{{ route('feature.detail', 'musteri-yonetimi') }}">Müşteri Yönetimi</a></li>
                    <li><a href="{{ route('feature.detail', 'is-talep-yonetimi') }}">İş Talep Yönetimi</a></li>
                    <li><a href="{{ route('feature.detail', 'mobil-saha-yonetimi') }}">Mobil Saha Yönetimi</a></li>
                    <li><a href="{{ route('feature.detail', 'stok-parca') }}">Stok Yönetimi</a></li>
                    <li><a href="{{ route('feature.detail', 'fatura-yonetimi') }}">Fatura Yönetimi</a></li>
                    <li><a href="{{ route('feature.detail', 'teklif-yonetimi') }}">Teklif Yönetimi</a></li>
                </ul>
            </div>
            
            <!-- İletişim -->
            <div class="col-lg-3 col-6">
                <h5 class="footer-title">İletişim</h5>
                <ul class="footer-links">
                    <li>
                        <i class="fas fa-phone me-2" style="color: var(--primary-blue);"></i>
                        <a href="tel:02129092861">{{ $contact['items'][0]['info'] ?? '0212 909 2861' }}</a>
                    </li>
                    <li>
                        <i class="fas fa-envelope me-2" style="color: var(--primary-blue);"></i>
                        <a href="mailto:{{ $contact['items'][1]['info'] ?? 'info@serbis.com' }}">{{ $contact['items'][1]['info'] ?? 'info@serbis.com' }}</a>
                    </li>
                    <li>
                        <i class="fas fa-map-marker-alt me-2" style="color: var(--primary-blue);"></i>
                        <span>{{ $contact['items'][2]['info'] ?? 'İstanbul, Türkiye' }}</span>
                    </li>
                    <li class="mt-3">
                        <a href="{{ route('contact_frontend') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-paper-plane me-1"></i> İletişim Formu
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <hr style="border-color: rgba(255,255,255,0.1); margin: 2.5rem 0 1.5rem 0;">
        
                <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                    <p class="mb-0">&copy; {{ date('Y') }} Serbis. Tüm hakları saklıdır.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="{{ url('/gizlilik') }}" class="footer-legal-link">Gizlilik Politikası</a>
                    <span class="mx-2">|</span>
                    <a href="{{ url('/kullanim-sartlari') }}" class="footer-legal-link">Kullanım Şartları</a>
                    <span class="mx-2">|</span>
                    <a href="{{ url('/kvkk') }}" class="footer-legal-link">KVKK</a>
                </div>
            </div>
        </div>
    </div>
</footer>