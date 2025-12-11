<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="fas fa-cogs"></i> Serbis
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/sektorler') }}">Sektörler</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/moduller') }}">Modüller</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/entegrasyonlar') }}">Entegrasyonlar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/fiyatlar') }}">Fiyatlar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#iletisim">İletişim</a>
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