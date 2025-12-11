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
        <div class="feature-category-section">
            <div class="text-center mb-5">
                <h2 class="feature-section-title">Müşteri Yönetimi</h2>
                <p class="feature-section-subtitle">Tüm müşteri bilgilerinizi organize edin ve ilişkilerinizi güçlendirin</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon blue">
                            <i class="fas fa-address-card"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Detaylı Müşteri Profilleri</h4>
                            <p>Müşterilerinizin tüm bilgilerini, notlarını, toplantılarını, görevlerini, dosyalarını ve daha fazlasını içeren kapsamlı bir müşteri görünümü edinin.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon green">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Özel Alanlar</h4>
                            <p>Müşteri kayıtlarına özel alanlarla ek bilgiler saklayın.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon orange">
                            <i class="fas fa-list"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Müşteri Listeleri</h4>
                            <p>Daha iyi segmentasyon ve hedefleme için statik veya dinamik müşteri listeleri oluşturun.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon purple">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Kontrol Paneli</h4>
                            <p>Aktivite zaman çizelgesi, yaklaşan etkinlikler, aktif müşteriler ve daha fazlasını içeren kapsamlı bir kontrol paneli.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon red">
                            <i class="fas fa-upload"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>İçe Aktarma Şablonu</h4>
                            <p>Özel alanlar da dahil olmak üzere müşterilerinizi içe aktarmak için düzgün biçimlendirilmiş CSV elektronik tablosunu kullanın.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon teal">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Mobil CRM</h4>
                            <p>Her yerden, her zaman erişim. Tam duyarlı mobil web uygulaması herhangi bir cihaz için otomatik olarak uyarlanır.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- İş Talep Yönetimi -->
        <div class="feature-category-section">
            <div class="text-center mb-5">
                <h2 class="feature-section-title">İş Talep Yönetimi</h2>
                <p class="feature-section-subtitle">Servis taleplerini kaydedin, teknisyen atayın ve süreçleri takip edin</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon blue">
                            <i class="fas fa-barcode"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Hızlı Servis Kaydı</h4>
                            <p>Müşteri seçimi, cihaz bilgisi, arıza açıklaması işlemlerini tek ekrandan hızlıca tamamlayın. Barkod okuma ile hatasız kayıt oluşturun.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon green">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Teknisyen Atama</h4>
                            <p>Servisleri uygun teknisyenlere atayın, iş yüklerini dengeleyin. Kimin elinde kaç iş var anlık olarak görün.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon orange">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Otomatik Bildirimler</h4>
                            <p>Cihazın durumunu adım adım izleyin. Her aşamada müşteriye otomatik SMS ve email bildirimleri gönderin.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon purple">
                            <i class="fas fa-camera"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Fotoğraflı Arıza Kaydı</h4>
                            <p>Cihazın arızalı durumunu fotoğraf ve video ile kaydedin. Müşteri ile anlaşmazlık durumlarında kanıt oluşturun.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon red">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Garanti Takibi</h4>
                            <p>Garanti kapsamındaki cihazları otomatik tespit edin. Garanti süresi dolmadan müşterilerinizi bilgilendirin.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon teal">
                            <i class="fas fa-print"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Servis Formu Yazdırma</h4>
                            <p>Profesyonel servis formları ve etiketler yazdırın. QR kod ile hızlı sorgulama imkanı.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobil Saha Yönetimi -->
        <div class="feature-category-section">
            <div class="text-center mb-5">
                <h2 class="feature-section-title">Mobil Saha Yönetimi</h2>
                <p class="feature-section-subtitle">Teknisyenleriniz sahadan mobil cihazlarla tüm işlemleri yapabilir</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon blue">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Responsive Mobil Arayüz</h4>
                            <p>Uygulama yüklemeden telefon veya tabletten sisteme girin. Her cihaza uyumlu responsive tasarım.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon green">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>GPS ve Navigasyon</h4>
                            <p>Teknisyenler kendilerine atanan işleri görür, adres tarifi alarak müşteriye en kısa yoldan ulaşır.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon orange">
                            <i class="fas fa-pen-nib"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Dijital İmza</h4>
                            <p>İş bitiminde müşteri imzasını tablet ekranından dijital olarak alın. Kağıtsız süreç yönetimi.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon purple">
                            <i class="fas fa-camera"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Sahadan Fotoğraf</h4>
                            <p>Onarım öncesi ve sonrası fotoğrafları anında sisteme yükleyin. Müşteri memnuniyetini artırın.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon red">
                            <i class="fas fa-cubes"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Araç Stoğu</h4>
                            <p>Teknisyen sahada ihtiyaç duyduğu parçayı talep edebilir veya aracındaki stoktan düşebilir.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon teal">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Mobil Tahsilat</h4>
                            <p>Sahadan fatura kesimi ve tahsilat işlemlerini gerçekleştirin. Kasaya gitmeden iş tamamlayın.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stok Yönetimi -->
        <div class="feature-category-section">
            <div class="text-center mb-5">
                <h2 class="feature-section-title">Stok ve Yedek Parça</h2>
                <p class="feature-section-subtitle">Parça stoklarınızı takip edin, kritik seviyelerde otomatik uyarı alın</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon blue">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Detaylı Stok Kartları</h4>
                            <p>Her parça için alış/satış fiyatı, KDV oranı, raf yeri ve uyumlu cihaz modellerini kaydedin.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon green">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Kritik Stok Uyarıları</h4>
                            <p>Belirlediğiniz adedin altına düşen ürünler için otomatik uyarı alın. Parça bitmeden sipariş verin.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon orange">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Hareket Geçmişi</h4>
                            <p>Hangi parça hangi serviste kullanıldı, ne zaman alındı? Tüm envanter hareketlerini şeffafça izleyin.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon purple">
                            <i class="fas fa-warehouse"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Çoklu Depo Yönetimi</h4>
                            <p>Birden fazla depo ve raf sistemi ile stoklarınızı organize edin. Depo transferleri yapın.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon red">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Stok Sayım</h4>
                            <p>Dönemsel stok sayımları yapın, fireleri kaydedin. Fiziksel ve sistem stoğunu eşitleyin.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="feature-box">
                        <div class="feature-box-icon teal">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div class="feature-box-content">
                            <h4>Karlılık Analizi</h4>
                            <p>En çok giden parçaları analiz edin. Alış-satış raporları ile karlılığınızı kontrol edin.</p>
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