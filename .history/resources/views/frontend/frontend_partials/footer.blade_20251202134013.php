<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="footer-brand">
                    <i class="fas fa-cogs"></i> Serbis
                </div>
                <p class="footer-description">
                    Teknik servis işletmeleri için yeni nesil, bulut tabanlı yönetim sistemi.
                </p>
                <div class="footer-social">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-6 mb-4 mb-lg-0">
                <h5 class="footer-title">Ürün</h5>
                <ul class="footer-links">
                    <li><a href="{{ url('/sektorler') }}">Sektörler</a></li>
                    <li><a href="{{ url('/moduller') }}">Modüller</a></li>
                    <li><a href="{{ url('/entegrasyonlar') }}">Entegrasyonlar</a></li>
                    <li><a href="{{ url('/fiyatlar') }}">Fiyatlar</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-6 mb-4 mb-lg-0">
                <h5 class="footer-title">Şirket</h5>
                <ul class="footer-links">
                    <li><a href="#">Hakkımızda</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Kariyer</a></li>
                    <li><a href="#iletisim">İletişim</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-6 mb-4 mb-lg-0">
                <h5 class="footer-title">Destek</h5>
                <ul class="footer-links">
                    <li><a href="#">Yardım Merkezi</a></li>
                    <li><a href="#">Dokümantasyon</a></li>
                    <li><a href="#">API</a></li>
                    <li><a href="#">Durum</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-6">
                <h5 class="footer-title">Yasal</h5>
                <ul class="footer-links">
                    <li><a href="#">Gizlilik</a></li>
                    <li><a href="#">Kullanım Şartları</a></li>
                    <li><a href="#">KVKK</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Serbis. Tüm hakları saklıdır.</p>
        </div>
    </div>
</footer>