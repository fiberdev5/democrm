
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serbis - Teknik Servis Yönetim Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #0066FF;
            --dark-blue: #004BB5;
            --orange: #FF6B35;
            --dark: #1A1A2E;
            --gray: #64748B;
            --light-gray: #F1F5F9;
            --border: #E2E8F0;
            --white: #FFFFFF;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--dark);
            line-height: 1.6;
        }

        /* Navbar */
        .navbar {
            background: var(--white);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-blue);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand i {
            color: var(--orange);
        }

        .nav-link {
            color: var(--dark) !important;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.5rem 1rem !important;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: var(--primary-blue) !important;
        }

        .btn-login {
            color: var(--primary-blue);
            border: 2px solid var(--primary-blue);
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-login:hover {
            background: var(--primary-blue);
            color: white;
        }

        .btn-primary-custom {
            background: var(--orange);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-primary-custom:hover {
            background: #E55A2B;
        }

        /* Hero Section */
        .hero-section {
            background: var(--primary-blue);
            padding: 5rem 0 4rem;
            color: white;
        }

        .hero-title {
            font-size: 3.2rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }

        .hero-title .highlight {
            color: var(--orange);
        }

        .hero-description {
            font-size: 1.2rem;
            opacity: 0.95;
            margin-bottom: 2.5rem;
            line-height: 1.7;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }

        .btn-hero-primary {
            background: white;
            color: var(--primary-blue);
            padding: 1rem 2.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.05rem;
            border: none;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
        }

        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            color: var(--primary-blue);
        }

        .btn-hero-secondary {
            background: transparent;
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.05rem;
            border: 2px solid white;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
        }

        .btn-hero-secondary:hover {
            background: white;
            color: var(--primary-blue);
        }

        .hero-features {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .hero-feature {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .hero-feature i {
            color: var(--orange);
        }

        .hero-image img {
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        /* Stats Section */
        .stats-section {
            background: white;
            padding: 4rem 0;
            border-bottom: 1px solid var(--border);
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--gray);
            font-size: 1rem;
            font-weight: 500;
        }

        /* Section Common */
        .section {
            padding: 5rem 0;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-badge {
            display: inline-block;
            background: var(--light-gray);
            color: var(--primary-blue);
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1rem;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .section-title .accent {
            color: var(--primary-blue);
        }

        .section-subtitle {
            color: var(--gray);
            font-size: 1.1rem;
            max-width: 650px;
            margin: 0 auto;
        }

        /* Cards */
        .card-item {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            border: 1px solid var(--border);
            transition: all 0.2s;
            height: 100%;
        }

        .card-item:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            transform: translateY(-5px);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            background: var(--primary-blue);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .card-icon i {
            font-size: 1.8rem;
            color: white;
        }

        .card-icon.orange {
            background: var(--orange);
        }

        .card-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .card-description {
            color: var(--gray);
            line-height: 1.7;
            font-size: 0.95rem;
        }

        /* Modules Section */
        .modules-section {
            background: var(--light-gray);
        }

        /* Sectors Section */
        .sectors-section {
            background: white;
        }

        .sector-card {
            text-align: center;
            padding: 2rem 1.5rem;
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            transition: all 0.2s;
            height: 100%;
        }

        .sector-card:hover {
            border-color: var(--primary-blue);
            box-shadow: 0 4px 20px rgba(0, 102, 255, 0.1);
        }

        .sector-icon {
            width: 70px;
            height: 70px;
            background: var(--light-gray);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .sector-icon i {
            font-size: 2rem;
            color: var(--primary-blue);
        }

        .sector-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.7rem;
            color: var(--dark);
        }

        .sector-description {
            color: var(--gray);
            font-size: 0.9rem;
        }

        /* Integrations Section */
        .integrations-section {
            background: var(--light-gray);
        }

        /* Pricing Section */
        .pricing-section {
            background: white;
        }

        .pricing-card {
            background: white;
            border: 2px solid var(--border);
            border-radius: 16px;
            padding: 3rem 2.5rem;
            transition: all 0.2s;
            height: 100%;
            position: relative;
        }

        .pricing-card.featured {
            border-color: var(--primary-blue);
            box-shadow: 0 10px 40px rgba(0, 102, 255, 0.15);
        }

        .pricing-badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--orange);
            color: white;
            padding: 0.4rem 1.2rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .pricing-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .pricing-price {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 0.5rem;
        }

        .pricing-period {
            color: var(--gray);
            margin-bottom: 2rem;
        }

        .pricing-features {
            list-style: none;
            padding: 0;
            margin: 2rem 0 2.5rem;
        }

        .pricing-features li {
            padding: 0.8rem 0;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.7rem;
            font-size: 0.95rem;
        }

        .pricing-features i {
            color: #10b981;
            font-size: 1.1rem;
        }

        .btn-pricing {
            width: 100%;
            padding: 1rem;
            border-radius: 10px;
            font-weight: 600;
            border: 2px solid var(--primary-blue);
            background: white;
            color: var(--primary-blue);
            transition: all 0.2s;
        }

        .btn-pricing:hover {
            background: var(--primary-blue);
            color: white;
        }

        .pricing-card.featured .btn-pricing {
            background: var(--primary-blue);
            color: white;
        }

        .pricing-card.featured .btn-pricing:hover {
            background: var(--dark-blue);
        }

        /* CTA Section */
        .cta-section {
            padding: 5rem 0;
            background: var(--primary-blue);
            color: white;
            text-align: center;
        }

        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .cta-description {
            font-size: 1.2rem;
            opacity: 0.95;
            margin-bottom: 2.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-cta {
            background: white;
            color: var(--primary-blue);
            padding: 1.2rem 3rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1.1rem;
            border: none;
            transition: all 0.2s;
        }

        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        /* Contact Section */
        .contact-section {
            background: var(--light-gray);
        }

        .contact-card {
            background: white;
            border-radius: 12px;
            padding: 2.5rem 2rem;
            text-align: center;
            border: 1px solid var(--border);
            transition: all 0.2s;
            height: 100%;
        }

        .contact-card:hover {
            border-color: var(--primary-blue);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .contact-icon {
            width: 70px;
            height: 70px;
            background: var(--light-gray);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .contact-icon i {
            font-size: 2rem;
            color: var(--primary-blue);
        }

        .contact-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.7rem;
            color: var(--dark);
        }

        .contact-info {
            color: var(--gray);
            font-size: 1rem;
            font-weight: 500;
        }

        /* Footer */
        .footer {
            background: var(--dark);
            color: rgba(255, 255, 255, 0.7);
            padding: 4rem 0 2rem;
        }

        .footer-brand {
            font-size: 1.75rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .footer-brand i {
            color: var(--orange);
        }

        .footer-description {
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .footer-title {
            color: white;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 0.7rem;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: white;
        }

        .footer-social {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.2s;
            text-decoration: none;
        }

        .social-icon:hover {
            background: var(--primary-blue);
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 3rem;
            padding-top: 2rem;
            text-align: center;
        }

        .footer-bottom p {
            margin: 0;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.2rem;
            }

            .hero-description {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .stat-number {
                font-size: 2.5rem;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .btn-hero-primary,
            .btn-hero-secondary {
                width: 100%;
                justify-content: center;
            }
        }
    </style>


    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-cogs"></i> Serbis
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="/sektorler">Sektörler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/moduller">Modüller</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/entegrasyonlar">Entegrasyonlar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/fiyatlar">Fiyatlar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#iletisim">İletişim</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a href="/login" class="btn-login">
                            <i class="fas fa-sign-in-alt"></i> Giriş Yap
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                        <button class="btn btn-primary-custom">Ücretsiz Dene</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="hero-title">
                        Teknik Servis İşletmenizi <span class="highlight">Dijitalleştirin</span>
                    </h1>
                    <p class="hero-description">
                        Müşteri, servis, stok ve personel yönetiminizi tek platformdan yönetin. 
                        İşlerinizi hızlandırın, maliyetleri düşürün, müşteri memnuniyetini artırın.
                    </p>
                    <div class="hero-buttons">
                        <a href="#" class="btn-hero-primary">
                            <i class="fas fa-rocket"></i> Hemen Başla
                        </a>
                        <a href="#" class="btn-hero-secondary">
                            <i class="fas fa-play-circle"></i> Demo İzle
                        </a>
                    </div>
                    <div class="hero-features">
                        <div class="hero-feature">
                            <i class="fas fa-check-circle"></i>
                            <span>14 gün ücretsiz</span>
                        </div>
                        <div class="hero-feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Kredi kartı gerektirmez</span>
                        </div>
                        <div class="hero-feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Anında kurulum</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0">
                    <div class="hero-image">
                        <img src="https://via.placeholder.com/600x450/0066FF/ffffff?text=Serbis+Dashboard" alt="Serbis Dashboard">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6 mb-4 mb-md-0">
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Aktif Firma</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4 mb-md-0">
                    <div class="stat-item">
                        <div class="stat-number">50K+</div>
                        <div class="stat-label">Tamamlanan Servis</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number">99.9%</div>
                        <div class="stat-label">Uptime Garantisi</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number">7/24</div>
                        <div class="stat-label">Destek Hizmeti</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modules Section -->
    <section class="modules-section section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">MODÜLLER</span>
                <h2 class="section-title">Güçlü <span class="accent">Modüller</span></h2>
                <p class="section-subtitle">
                    İşletmenizi yönetmek için ihtiyacınız olan tüm modüller tek platformda
                </p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card-item">
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="card-title">Müşteri Yönetimi</h3>
                        <p class="card-description">
                            Müşterilerinizi detaylı kayıt altına alın, geçmiş işlemlerini görüntüleyin ve müşteri memnuniyetini artırın.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card-item">
                        <div class="card-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h3 class="card-title">Servis Takibi</h3>
                        <p class="card-description">
                            Servis süreçlerinizi baştan sona takip edin. Arıza kayıtlarından teslimata kadar her aşamayı yönetin.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card-item">
                        <div class="card-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <h3 class="card-title">Stok Yönetimi</h3>
                        <p class="card-description">
                            Yedek parça stoklarınızı takip edin, kritik stok seviyelerinde otomatik uyarı alın.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card-item">
                        <div class="card-icon orange">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h3 class="card-title">Personel Yönetimi</h3>
                        <p class="card-description">
                            Teknisyenlerinizi yönetin, performanslarını ölçün ve prim hesaplamalarını otomatikleştirin.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card-item">
                        <div class="card-icon orange">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <h3 class="card-title">Fatura & Kasa</h3>
                        <p class="card-description">
                            E-fatura oluşturun, gelir-gider takibi yapın, finansal raporlarınızı anında görüntüleyin.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card-item">
                        <div class="card-icon orange">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3 class="card-title">Mobil Erişim</h3>
                        <p class="card-description">
                            Responsive tasarım sayesinde mobil cihazlardan her yerde işlerinizi yönetin.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sectors Section -->
    <section class="sectors-section section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">SEKTÖRLER</span>
                <h2 class="section-title">Hangi <span class="accent">Sektörlere</span> Hizmet Veriyoruz?</h2>
                <p class="section-subtitle">
                    Farklı sektörlerdeki teknik servis işletmelerinin ihtiyaçlarına özel çözümler
                </p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="sector-card">
                        <div class="sector-icon">
                            <i class="fas fa-tv"></i>
                        </div>
                        <h3 class="sector-title">Beyaz Eşya</h3>
                        <p class="sector-description">
                            Buzdolabı, çamaşır makinesi servisleri
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="sector-card">
                        <div class="sector-icon">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <h3 class="sector-title">Bilgisayar</h3>
                        <p class="sector-description">
                            Bilgisayar, laptop teknik servisleri
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="sector-card">
                        <div class="sector-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3 class="sector-title">Telefon</h3>
                        <p class="sector-description">
                            Cep telefonu, tablet onarım servisleri
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="sector-card">
                        <div class="sector-icon">
                            <i class="fas fa-fan"></i>
                        </div>
                        <h3 class="sector-title">Klima & HVAC</h3>
                        <p class="sector-description">
                            Klima, havalandırma servisleri
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="sector-card">
                        <div class="sector-icon">
                            <i class="fas fa-fire"></i>
                        </div>
                        <h3 class="sector-title">Kombi</h3>
                        <p class="sector-description">
                            Kombi, kalorifer bakım ve onarım
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="sector-card">
                        <div class="sector-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <h3 class="sector-title">Medikal</h3>
                        <p class="sector-description">
                            Tıbbi cihaz bakım servisleri
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="sector-card">
                        <div class="sector-icon">
                            <i class="fas fa-camera"></i>
                        </div>
                        <h3 class="sector-title">Elektronik</h3>
                        <p class="sector-description">
                            TV, ses sistemleri servisleri
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="sector-card">
                        <div class="sector-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h3 class="sector-title">Diğer</h3>
                        <p class="sector-description">
                            Tüm teknik servis işletmeleri
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Integrations Section -->
    <section class="integrations-section section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">ENTEGRASYONLAR</span>
                <h2 class="section-title">Güçlü <span class="accent">Entegrasyonlar</span></h2>
                <p class="section-subtitle">
                    Kullandığınız tüm araçlarla sorunsuz entegre olun
                </p>
            </div>
            
            <!-- Integrations Carousel -->
            <div id="integrationsCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="card-item text-center">
                                    <div class="card-icon" style="margin: 0 auto 1.5rem;">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                    <h3 class="card-title">Paraşüt</h3>
                                    <p class="card-description">
                                        Muhasebe yazılımı ile entegrasyon
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card-item text-center">
                                    <div class="card-icon orange" style="margin: 0 auto 1.5rem;">
                                        <i class="fas fa-phone-volume"></i>
                                    </div>
                                    <h3 class="card-title">Hipcall</h3>
                                    <p class="card-description">
                                        Santral entegrasyonu ile gelen aramalar
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card-item text-center">
                                    <div class="card-icon" style="margin: 0 auto 1.5rem;">
                                        <i class="fas fa-sms"></i>
                                    </div>
                                    <h3 class="card-title">SMS Entegrasyonu</h3>
                                    <p class="card-description">
                                        Netgsm, Verimor ile SMS gönderimi
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="carousel-item">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="card-item text-center">
                                    <div class="card-icon orange" style="margin: 0 auto 1.5rem;">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <h3 class="card-title">Email Sistemi</h3>
                                    <p class="card-description">
                                        SMTP entegrasyonu ile otomatik email
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card-item text-center">
                                    <div class="card-icon" style="margin: 0 auto 1.5rem;">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <h3 class="card-title">Ödeme Sistemleri</h3>
                                    <p class="card-description">
                                        Online ödeme alma entegrasyonları
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card-item text-center">
                                    <div class="card-icon orange" style="margin: 0 auto 1.5rem;">
                                        <i class="fas fa-plug"></i>
                                    </div>
                                    <h3 class="card-title">REST API</h3>
                                    <p class="card-description">
                                        Kendi sistemlerinizle entegrasyon
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Carousel Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#integrationsCarousel" data-bs-slide="prev" style="width: 50px; left: -80px;">
                    <span style="width: 50px; height: 50px; background: var(--primary-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chevron-left" style="color: white;"></i>
                    </span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#integrationsCarousel" data-bs-slide="next" style="width: 50px; right: -80px;">
                    <span style="width: 50px; height: 50px; background: var(--primary-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chevron-right" style="color: white;"></i>
                    </span>
                </button>
                
                <!-- Indicators -->
                <div class="carousel-indicators" style="position: relative; margin-top: 3rem;">
                    <button type="button" data-bs-target="#integrationsCarousel" data-bs-slide-to="0" class="active" style="background: var(--primary-blue); width: 12px; height: 12px; border-radius: 50%;"></button>
                    <button type="button" data-bs-target="#integrationsCarousel" data-bs-slide-to="1" style="background: var(--gray); width: 12px; height: 12px; border-radius: 50%;"></button>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="fiyatlar" class="pricing-section section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">FİYATLAR</span>
                <h2 class="section-title">Size Uygun <span class="accent">Paketi Seçin</span></h2>
                <p class="section-subtitle">
                    Her ölçekteki işletme için esnek ve uygun fiyatlı paketler
                </p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-4">
                    <div class="pricing-card">
                        <h3 class="pricing-name">Başlangıç</h3>
                        <div class="pricing-price">₺599</div>
                        <p class="pricing-period">Aylık / KDV Hariç</p>
                        <ul class="pricing-features">
                            <li><i class="fas fa-check-circle"></i> Max. 3 Kullanıcı</li>
                            <li><i class="fas fa-check-circle"></i> 2 GB Depolama</li>
                            <li><i class="fas fa-check-circle"></i> Sınırsız Servis</li>
                            <li><i class="fas fa-check-circle"></i> Temel Raporlar</li>
                            <li><i class="fas fa-check-circle"></i> Email Destek</li>
                        </ul>
                        <button class="btn btn-pricing">Satın Al</button>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="pricing-card featured">
                        <div class="pricing-badge">En Popüler</div>
                        <h3 class="pricing-name">Profesyonel</h3>
                        <div class="pricing-price">₺999</div>
                        <p class="pricing-period">Aylık / KDV Hariç</p>
                        <ul class="pricing-features">
                            <li><i class="fas fa-check-circle"></i> Max. 5 Kullanıcı</li>
                            <li><i class="fas fa-check-circle"></i> 4 GB Depolama</li>
                            <li><i class="fas fa-check-circle"></i> Sınırsız Servis</li>
                            <li><i class="fas fa-check-circle"></i> Gelişmiş Raporlar</li>
                            <li><i class="fas fa-check-circle"></i> SMS Entegrasyonu</li>
                            <li><i class="fas fa-check-circle"></i> Öncelikli Destek</li>
                        </ul>
                        <button class="btn btn-pricing">Satın Al</button>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="pricing-card">
                        <h3 class="pricing-name">Kurumsal</h3>
                        <div class="pricing-price">₺1,499</div>
                        <p class="pricing-period">Aylık / KDV Hariç</p>
                        <ul class="pricing-features">
                            <li><i class="fas fa-check-circle"></i> Sınırsız Kullanıcı</li>
                            <li><i class="fas fa-check-circle"></i> Sınırsız Depolama</li>
                            <li><i class="fas fa-check-circle"></i> Tüm Özellikler</li>
                            <li><i class="fas fa-check-circle"></i> API Erişimi</li>
                            <li><i class="fas fa-check-circle"></i> Özel Entegrasyonlar</li>
                            <li><i class="fas fa-check-circle"></i> 7/24 Destek</li>
                        </ul>
                        <button class="btn btn-pricing">Satın Al</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="sectors-section section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">MÜŞTERİ YORUMLARI</span>
                <h2 class="section-title">Müşterilerimiz <span class="accent">Ne Diyor?</span></h2>
                <p class="section-subtitle">
                    Binlerce mutlu müşterimizden bazı görüşler
                </p>
            </div>
            
            <!-- Testimonials Carousel -->
            <div id="testimonialsCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="card-item text-center" style="padding: 3rem;">
                                    <div style="margin-bottom: 2rem;">
                                        <i class="fas fa-quote-left" style="font-size: 3rem; color: var(--orange); opacity: 0.3;"></i>
                                    </div>
                                    <p style="font-size: 1.2rem; font-style: italic; margin-bottom: 2.5rem; color: var(--dark); line-height: 1.8;">
                                        "Serbis sayesinde tüm servis süreçlerimizi dijitalleştirdik. Artık her şey çok daha hızlı ve organize. Müşteri memnuniyetimiz %40 arttı. Kesinlikle tavsiye ediyorum."
                                    </p>
                                    <div style="display: flex; align-items: center; gap: 1rem; justify-content: center;">
                                        <div style="width: 60px; height: 60px; background: var(--primary-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.4rem;">
                                            AY
                                        </div>
                                        <div style="text-align: left;">
                                            <div style="font-weight: 700; color: var(--dark); font-size: 1.1rem;">Ahmet Yılmaz</div>
                                            <div style="font-size: 1rem; color: var(--gray);">Beyaz Eşya Servisi</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="carousel-item">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="card-item text-center" style="padding: 3rem;">
                                    <div style="margin-bottom: 2rem;">
                                        <i class="fas fa-quote-left" style="font-size: 3rem; color: var(--orange); opacity: 0.3;"></i>
                                    </div>
                                    <p style="font-size: 1.2rem; font-style: italic; margin-bottom: 2.5rem; color: var(--dark); line-height: 1.8;">
                                        "Müşteri takibi ve stok yönetimi artık çok kolay. Özellikle mobil erişim sahada işimizi inanılmaz kolaylaştırdı. Kağıt formlardan kurtulduk."
                                    </p>
                                    <div style="display: flex; align-items: center; gap: 1rem; justify-content: center;">
                                        <div style="width: 60px; height: 60px; background: var(--orange); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.4rem;">
                                            MK
                                        </div>
                                        <div style="text-align: left;">
                                            <div style="font-weight: 700; color: var(--dark); font-size: 1.1rem;">Mehmet Kara</div>
                                            <div style="font-size: 1rem; color: var(--gray);">Elektronik Servisi</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="carousel-item">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="card-item text-center" style="padding: 3rem;">
                                    <div style="margin-bottom: 2rem;">
                                        <i class="fas fa-quote-left" style="font-size: 3rem; color: var(--orange); opacity: 0.3;"></i>
                                    </div>
                                    <p style="font-size: 1.2rem; font-style: italic; margin-bottom: 2.5rem; color: var(--dark); line-height: 1.8;">
                                        "Destek ekibi harika! Her sorumuzda hızlıca yardımcı oldular. Sistemi kullanmak gerçekten çok basit ve kullanışlı. 3 yıldır memnuniyetle kullanıyoruz."
                                    </p>
                                    <div style="display: flex; align-items: center; gap: 1rem; justify-content: center;">
                                        <div style="width: 60px; height: 60px; background: var(--primary-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.4rem;">
                                            FÖ
                                        </div>
                                        <div style="text-align: left;">
                                            <div style="font-weight: 700; color: var(--dark); font-size: 1.1rem;">Fatma Öztürk</div>
                                            <div style="font-size: 1rem; color: var(--gray);">Klima Servisi</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Carousel Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="prev" style="width: 50px; left: 0;">
                    <span style="width: 50px; height: 50px; background: var(--primary-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chevron-left" style="color: white;"></i>
                    </span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="next" style="width: 50px; right: 0;">
                    <span style="width: 50px; height: 50px; background: var(--primary-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chevron-right" style="color: white;"></i>
                    </span>
                </button>
                
                <!-- Indicators -->
                <div class="carousel-indicators" style="position: relative; margin-top: 2rem;">
                    <button type="button" data-bs-target="#testimonialsCarousel" data-bs-slide-to="0" class="active" style="background: var(--primary-blue); width: 12px; height: 12px; border-radius: 50%;"></button>
                    <button type="button" data-bs-target="#testimonialsCarousel" data-bs-slide-to="1" style="background: var(--gray); width: 12px; height: 12px; border-radius: 50%;"></button>
                    <button type="button" data-bs-target="#testimonialsCarousel" data-bs-slide-to="2" style="background: var(--gray); width: 12px; height: 12px; border-radius: 50%;"></button>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="modules-section section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">SIK SORULAN SORULAR</span>
                <h2 class="section-title">Sıkça Sorulan <span class="accent">Sorular</span></h2>
                <p class="section-subtitle">
                    Merak ettiğiniz soruların cevaplarını burada bulabilirsiniz
                </p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item" style="border: 1px solid var(--border); border-radius: 12px; margin-bottom: 1rem; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" style="background: white; color: var(--dark); font-weight: 600; padding: 1.5rem;">
                                    Serbis'i kullanmak için teknik bilgiye ihtiyacım var mı?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="padding: 1.5rem; color: var(--gray);">
                                    Hayır, Serbis kullanıcı dostu arayüzü ile herkes tarafından kolayca kullanılabilir. Kurulum sonrası eğitim videolarımız ve destek ekibimiz size yardımcı olacaktır.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="border: 1px solid var(--border); border-radius: 12px; margin-bottom: 1rem; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" style="background: white; color: var(--dark); font-weight: 600; padding: 1.5rem;">
                                    Verilerim güvende mi?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="padding: 1.5rem; color: var(--gray);">
                                    Evet, tüm verileriniz SSL şifreleme ile korunur ve düzenli olarak yedeklenir. Türkiye'de bulunan sunucularımızda KVKK uyumlu olarak verilerinizi saklarız.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="border: 1px solid var(--border); border-radius: 12px; margin-bottom: 1rem; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" style="background: white; color: var(--dark); font-weight: 600; padding: 1.5rem;">
                                    Ücretsiz deneme süresi var mı?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="padding: 1.5rem; color: var(--gray);">
                                    Evet, 14 gün boyunca ücretsiz deneyebilirsiniz. Kredi kartı bilgisi gerekmez. Deneme süreniz sonunda istediğiniz paketi seçebilirsiniz.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="border: 1px solid var(--border); border-radius: 12px; margin-bottom: 1rem; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" style="background: white; color: var(--dark); font-weight: 600; padding: 1.5rem;">
                                    Mobil cihazlardan kullanabilir miyim?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="padding: 1.5rem; color: var(--gray);">
                                    Evet, Serbis responsive tasarıma sahiptir. Telefon, tablet ve bilgisayardan sorunsuz kullanabilirsiniz. Ayrıca mobil uygulamamız da yakında yayınlanacak.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="border: 1px solid var(--border); border-radius: 12px; margin-bottom: 1rem; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" style="background: white; color: var(--dark); font-weight: 600; padding: 1.5rem;">
                                    Mevcut verilerimi aktarabilir miyim?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="padding: 1.5rem; color: var(--gray);">
                                    Evet, mevcut müşteri, stok ve servis verilerinizi Excel dosyası ile sisteme aktarabilirsiniz. Destek ekibimiz bu konuda size yardımcı olacaktır.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item" style="border: 1px solid var(--border); border-radius: 12px; margin-bottom: 1rem; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6" style="background: white; color: var(--dark); font-weight: 600; padding: 1.5rem;">
                                    Destek hizmeti nasıl çalışıyor?
                                </button>
                            </h2>
                            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="padding: 1.5rem; color: var(--gray);">
                                    Telefon, email ve canlı destek kanallarımız üzerinden bize ulaşabilirsiniz. Profesyonel pakette öncelikli destek, Kurumsal pakette 7/24 destek sunuyoruz.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="iletisim" class="contact-section section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">İLETİŞİM</span>
                <h2 class="section-title">Bizimle <span class="accent">İletişime Geçin</span></h2>
                <p class="section-subtitle">
                    Sorularınız mı var? Size yardımcı olmaktan memnuniyet duyarız
                </p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h3 class="contact-title">Telefon</h3>
                        <p class="contact-info">0212 909 2861</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3 class="contact-title">E-posta</h3>
                        <p class="contact-info"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="9bf2f5fdf4dbe8fee9f9f2e8b5f8f4f6">[email&#160;protected]</a></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3 class="contact-title">Adres</h3>
                        <p class="contact-info">İstanbul, Türkiye</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">Hemen Başlamaya Hazır mısınız?</h2>
            <p class="cta-description">
                14 gün ücretsiz deneyin. Kredi kartı gerekmez. İstediğiniz zaman iptal edebilirsiniz.
            </p>
            <button class="btn btn-cta">
                <i class="fas fa-rocket me-2"></i> Ücretsiz Denemeyi Başlat
            </button>
        </div>
    </section>

    <!-- Footer -->
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
                        <li><a href="/sektorler">Sektörler</a></li>
                        <li><a href="/moduller">Modüller</a></li>
                        <li><a href="/entegrasyonlar">Entegrasyonlar</a></li>
                        <li><a href="/fiyatlar">Fiyatlar</a></li>
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
                <p>&copy; 2024 Serbis. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>

    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                     target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Auto-play carousels
        var testimonialsCarousel = new bootstrap.Carousel(document.getElementById('testimonialsCarousel'), {
            interval: 5000,
            ride: 'carousel'
        });

        var integrationsCarousel = new bootstrap.Carousel(document.getElementById('integrationsCarousel'), {
            interval: 4000,
            ride: 'carousel'
        });
    </script>

