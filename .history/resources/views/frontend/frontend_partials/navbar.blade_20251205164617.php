<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('frontend/img/logo-new.png') }}" alt="Serbis Logo" style="height: 70px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                 <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Anasayfa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/hakkimizda') }}">Hakkımızda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('sectors') }}">Sektörler</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="featuresDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Özellikler
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="featuresDropdown">
                        <li><a class="dropdown-item" href="{{ route('feature.detail', 'musteri-yonetimi') }}">Müşteri Yönetimi</a></li>
                        <li><a class="dropdown-item" href="{{ route('feature.detail', 'is-talep-yonetimi') }}">İş Talep Yönetimi</a></li>
                        <li><a class="dropdown-item" href="{{ route('feature.detail', 'mobil-saha-yonetimi') }}">Mobil Saha Yönetimi</a></li>
                        <li><a class="dropdown-item" href="{{ route('feature.detail', 'stok-parca') }}">Stok Yönetimi</a></li>
                        <li><a class="dropdown-item" href="{{ route('feature.detail', 'fatura-yonetimi') }}">Fatura Yönetimi</a></li>
                        <li><a class="dropdown-item" href="{{ route('feature.detail', 'destek-yardim') }}">Destek ve Yardım</a></li>
                        <li><a class="dropdown-item" href="{{ route('feature.detail', 'teklif-yonetimi') }}">Teklif Yönetimi</a></li>
                        <li><a class="dropdown-item" href="{{ route('feature.detail', 'entegrasyonlar') }}">Entegrasyonlar</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ url('/ozellikler') }}">
                            <strong>Tüm Özellikleri Görüntüle →</strong>
                        </a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/entegrasyonlar') }}">Entegrasyonlar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/fiyatlar') }}">Fiyatlar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('contact_frontend') }}">İletişim</a>
                </li>

                <li class="nav-item ms-2">
                    <a href="{{ url('/kullanici-girisi') }}" class="btn-login" target="_blank">
                        <i class="fas fa-sign-in-alt"></i> Giriş Yap
                    </a>
                </li>
                    <li class="nav-item ms-2">
                        <button class="btn btn-primary-custom" onclick="window.open('{{ url('/kullanici-girisi')}}', '_blank')">Ücretsiz Dene</button>
                    </li>
            </ul>
        </div>
    </div>
</nav>