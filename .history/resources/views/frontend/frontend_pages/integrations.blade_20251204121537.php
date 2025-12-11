@extends('frontend.main_master')

@section('title', 'Entegrasyonlar - Serbis')

@section('main')

<!-- Hero Section (Burada değişiklik yok, aynen kalabilir) -->
<section class="integrations-hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="hero-main-title">Serbis Entegrasyonları ile<br>Tüm Süreçlerinizi Entegre Edin</h1>
                <p class="hero-description">
                    Serbis uygulama mağazasındaki uygulama ve entegrasyonlar ile teknik servis sitenizi çok yönlü hale getirin.
                </p>
                <!-- Buton vs. aynen kalabilir -->
            </div>
        </div>
        <!-- Marquee (Kayan yazı) kısmı buraya gelecek (önceki cevaptaki kod) -->
         <div class="marquee-wrapper">
            <!-- ... Marquee kodlarınız ... -->
         </div>
    </div>
</section>

<!-- TEK BÖLÜM: Tüm Entegrasyonlar -->
<section class="integration-category-section">
    <div class="container">
        
        <!-- EN POPÜLER (SMS ve Diğer kategorisi birleşik) -->
        <div class="category-header-simple">
            <h2 class="category-title">En Popüler Uygulamalar</h2>
            <p class="category-description">Müşterilerimizin en çok tercih ettiği entegrasyonlar</p>
        </div>
        
        <div class="row g-4 mb-5">
            {{-- SMS KATEGORİSİNDEKİLER --}}
            @foreach($integrations['SMS'] as $integration)
            <div class="col-lg-4 col-md-6">
                <!-- Flip Kart Yapısı Başlangıcı -->
                <div class="integration-flip-card">
                    <div class="flip-card-inner">
                        
                        <!-- ÖN YÜZ (Logo ve İsim) -->
                        <div class="flip-card-front">
                            <div class="integration-logo-soft">
                                <img src="{{ asset($integration['logo']) }}" alt="{{ $integration['name'] }}" onerror="this.src='https://via.placeholder.com/100x60/49657B/ffffff?text={{ substr($integration['name'], 0, 1) }}'">
                            </div>
                            <h3 class="integration-name-soft">{{ $integration['name'] }}</h3>
                            <span class="integration-category-tag">{{ $integration['category'] }}</span>
                            <span class="integration-price-tag-front">Detay İçin Üzerine Gel</span>
                        </div>

                        <!-- ARKA YÜZ (Açıklama) -->
                        <div class="flip-card-back">
                            <h3 class="text-white mb-3">{{ $integration['name'] }}</h3>
                            <p class="integration-description-back">{{ $integration['description'] }}</p>
                            <!-- Featured durumuna göre buton veya yazı -->
                            <button class="btn btn-light btn-sm mt-3">Hemen Başla</button>
                        </div>

                    </div>
                </div>
                <!-- Flip Kart Bitiş -->
            </div>
            @endforeach
            
            {{-- DİĞER KATEGORİSİNDEKİLER --}}
            @foreach($integrations['Diğer'] as $integration)
            <div class="col-lg-4 col-md-6">
                <div class="integration-flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <div class="integration-logo-soft">
                                <img src="{{ asset($integration['logo']) }}" alt="{{ $integration['name'] }}" onerror="this.src='https://via.placeholder.com/100x60/49657B/ffffff?text={{ substr($integration['name'], 0, 1) }}'">
                            </div>
                            <h3 class="integration-name-soft">{{ $integration['name'] }}</h3>
                            <span class="integration-category-tag">{{ $integration['category'] }}</span>
                        </div>
                        <div class="flip-card-back">
                            <h3 class="text-white mb-3">{{ $integration['name'] }}</h3>
                            <p class="integration-description-back">{{ $integration['description'] }}</p>
                            <button class="btn btn-light btn-sm mt-3">İncele</button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- ÖN MUHASEBE (Link kaldırıldı) -->
        <div class="category-header-simple mt-5">
            <h2 class="category-title">Ön Muhasebe Entegrasyonları</h2>
            <p class="category-description">Faturalarınızı otomatikleştirin</p>
        </div>
        
        <div class="row g-4 mb-5">
            @foreach($integrations['Fatura'] as $integration)
            <div class="col-lg-4 col-md-6">
                <div class="integration-flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <div class="integration-logo-soft">
                                <img src="{{ asset($integration['logo']) }}" alt="{{ $integration['name'] }}" onerror="this.src='https://via.placeholder.com/100x60/49657B/ffffff?text={{ substr($integration['name'], 0, 1) }}'">
                            </div>
                            <h3 class="integration-name-soft">{{ $integration['name'] }}</h3>
                            <span class="integration-category-tag">{{ $integration['category'] }}</span>
                        </div>
                        <div class="flip-card-back">
                            <h3 class="text-white mb-3">{{ $integration['name'] }}</h3>
                            <p class="integration-description-back">{{ $integration['description'] }}</p>
                            <button class="btn btn-light btn-sm mt-3">Entegre Et</button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>

@endsection