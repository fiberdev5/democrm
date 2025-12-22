@extends('frontend.main_master')

@section('title', 'Özellikler - Serbis')

@section('main')

<!-- Page Header -->
<section class="features-page-header">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="features-main-title">Serbis Özellikleri</h1>
                <p class="features-main-subtitle">
                    Teknik servis işletmenizi büyütmek için ihtiyacınız olan tüm özellikleri keşfedin
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Features Categories -->
<section class="features-categories-section">
    <div class="container">
        
        <!-- Müşteri Yönetimi -->
        <div class="feature-category-block">
            <div class="row align-items-start">
                <div class="col-lg-5">
                    <div class="feature-category-header">
                        <div class="feature-category-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h2 class="feature-category-title">Müşteri Yönetimi</h2>
                        <p class="feature-category-description">
                            Tüm müşteri bilgilerinizi organize edin ve ilişkilerinizi güçlendirin
                        </p>
                        <a href="{{ route('feature.detail', 'musteri-yonetimi') }}" class="btn-feature-detail">
                            Detaylı İncele <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="feature-items-grid">
                        <div class="feature-item-card">
                            <i class="fas fa-address-card"></i>
                            <h4>Detaylı Profiller</h4>
                            <p>Müşterilerinizin tüm bilgilerini tek yerde toplayın</p>
                        </div>
                        <div class="feature-item-card">
                            <i class="fas fa-history"></i>
                            <h4>Geçmiş Takibi</h4>
                            <p>Tüm işlem geçmişini kronolojik olarak görün</p>
                        </div>
                        <div class="feature-item-card">
                            <i class="fas fa-search"></i>
                            <h4>Hızlı Arama</h4>
                            <p>İsim, telefon veya email ile anında bulun</p>
                        </div>
                        <div class="feature-item-card">
                            <i class="fas fa-sms"></i>
                            <h4>Otomatik SMS</h4>
                            <p>Özel günlerde otomatik mesaj gönderin</p>
                        </div>
                        <div class="feature-item-card">
                            <i class="fas fa-tags"></i>
                            <h4>Etiketleme</h4>
                            <p>Müşteri segmentasyonu yapın</p>
                        </div>
                        <div class="feature-item-card">
                            <i class="fas fa-file-excel"></i>
                            <h4>Excel İçe/Dışa Aktar</h4>
                            <p>Toplu veri transferi yapın</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- İş Talep Yönetimi -->
        <div class="feature-category-block">
            <div class="row align-items-start">
                <div class="col-lg-5">
                    <div class="feature-category-header">
                        <div class="feature-category-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h2 class="feature-category-title">İş Talep Yönetimi</h2>
                        <p class="feature-category-description">
                            Servis taleplerini kaydedin, teknisyen atayın ve süreçleri takip edin
                        </p>
                        <a href="{{ route('feature.detail', 'is-talep-yonetimi') }}" class="btn-feature-detail">
                            Detaylı İncele <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="feature-items-grid">
                        <div class="feature-item-card">
                            <i class="fas fa-barcode"></i>
                            <h4>Barkod Desteği</h4>
                            <p>Seri no ile hızlı kayıt oluşturun</p>
                        </div>
                        <div class="feature-item-card">
                            <i class="fas fa-user-cog"></i>
                            <h4>Teknisyen Atama</h4>
                            <p>İş yükünü dengeli dağıtın</p>
                        </div>
                        <div class="feature-item-card">
                            <i class="fas fa-bell"></i>
                            <h4>Otomatik Bildirim</h4>
                            <p>Her aşamada müşteri bilgilendir</p>
                        </div>
                        <div class="feature-item-card">
                            <i class="fas fa-camera"></i>
                            <h4>Fotoğraf Ekleme</h4>
                            <p>Arızalı cihazın fotoğrafını kaydedin</p>
                        </div>
                        <div class="feature-item-card">
                            <i class="fas fa-star"></i>
                            <h4>Öncelik Sistemi</h4>
                            <p>VIP ve acil işleri ayırın</p>
                        </div>
                        <div class="feature-item-card">
                            <i class="fas fa-shield-alt"></i>
                            <h4>Garanti Takibi</h4>
                            <p>Garanti durumunu otomatik kontrol edin</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobil Saha Yönetimi -->
        <div class="feature-category-block">
            <div class="row align-items-start">
                <div class="col-lg-5">
                    <div class="feature-category-header">
                        <div class="feature-category-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h2 class="feature-category-title">Mobil Saha Yönetimi</h2>
                        <p class="feature-category-description">
                            Teknisyenleriniz sahadan mobil cihazlarla işlem yapabilir
                        </p>
                        <a href="{{ route('feature.detail', 'mobil-saha-yonetimi') }}" class="btn-feature-detail">
                            Detaylı İncele <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="feature-items-grid">
                        <div class="feature-item-card">
                            <i class="fas fa-mobile-alt"></i>
                            <h4>Responsive Tasarım</h4>
                            <p>Her cihazdan sorunsuz erişim</p>
                        </div>
                        <div class="feature-item-card">
                            <i class="fas fa-map-marker-alt"></i>
                            <h4>GPS Navigasyon</h4>
                            <p>En kısa yolu bulun</p>
                        </div>
                        <div class="feature-item-card">
                            <i class="fas fa-camera"></i>
                            <h4>Fotoğraf Yükleme</h4>
                            <p>Sahadan anlık fotoğraf paylaşın</p>
                        </div>
                        <div class="feature-item-card">
                            <i class="fas fa-pen-nib"></i>
                            <h4>Dijital İmza</h4>
                            <p>Ekranda müşteri imzası alın</p>
                        </div>
                        <div class="feature-item-card">
                            <i class="fas fa-cubes"></i>
                            <h4>Araç Stoğu</h4>
                            <p>Teknisyen aracındaki stoku yönetin</p>
                        </div>
                        <div class="feature-item-card">
                            <i class="fas fa-qrcode"></i>
                            <h4>QR Kod</h4>
                            <p>Cihazları hızlıca sorgulayın</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stok Yönetimi -->
        <div class="feature-category-block">
            <div class="row align-items-start">
                <div class="col-lg-5">
                    <div class="feature-category-header">
                        <div class="feature-category-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <h2 class="feature-category-title">Stok Yönetimi</h2>
                        <p class="feature-category-description">
                            Parça stoklarınızı takip edin, kritik seviyelerde uyarı alın
                        </p>
                        <a href="{{ route('feature.detail', 'stok-parca') }}" class="btn-feature-detail">
                            Detaylı İncele <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="feature-items-grid">
                        <div class="feature-item-card">
                            <i class="fas fa-box-open"></i>
                            <h4>Ürün Kartları</h4>
                            <p>Detaylı stok kartları oluşturun</p>
                        </div>
                        <div class="feature-item-card">
                            <i class="fas fa-exclamation-circle"></i>
                            <h4>Kritik Stok Uyarısı</h4>
                            <p>Azalan ürünler için bildirim alın</p>
                        </div>
                        <div class="feature-item-card">
                            <i class="fas fa-exchange-alt"></i>
                            <h4>Hareket Geçmişi</h4>
                            <p>Tüm giriş-çıkışları izleyin</p>
                        </div>
                        <div class="feature-item-card">
                            <i class="fas fa-warehouse"></i>
                            <h4>Çoklu Depo</h4>
                            <p>Birden fazla depo yönetin</p>
                        </div>
                        <div class="feature-item-card">
                            <i class="fas fa-clipboard-list"></i>
                            <h4>Sayım Modülü</h4>
                            <p>Dönemsel envanter sayımı yapın</p>
                        </div>
                        <div class="feature-item-card">
                            <i class="fas fa-file-excel"></i>
                            <h4>Toplu İçe Aktarım</h4>
                            <p>Excel ile toplu ürün yükleyin</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2 class="cta-title">Tüm Özellikleri Ücretsiz Deneyin!</h2>
        <p class="cta-description">
            14 gün boyunca kredi kartı gerektirmeden tüm özelliklere erişin
        </p>
        <button class="btn btn-cta" onclick="window.open('{{ url('/kullanici-girisi')}}', '_blank')">
            Hemen Başlayın
        </button>
    </div>
</section>

@endsection