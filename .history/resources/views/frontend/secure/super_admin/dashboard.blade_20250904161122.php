@extends('frontend.secure.user_master')
@section('user')

<div class="dashboard">
        <!-- Header -->
        <header class="header">
            <div class="container">
                <div class="header-content">
                    <div class="header-left">
                        <h1 class="page-title">Super Admin Dashboard</h1>
                        <nav class="breadcrumb">
                            <span class="breadcrumb-item active">Dashboard</span>
                        </nav>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <!-- İstatistik Kartları -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-info">
                                <p class="stat-label">Toplam Firma</p>
                                <h3 class="stat-value">24</h3>
                            </div>
                            <div class="stat-icon blue">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-info">
                                <p class="stat-label">Aktif Firma</p>
                                <h3 class="stat-value">21</h3>
                                <div class="stat-trend positive">
                                    <span class="trend-arrow">↗</span>
                                    88%
                                </div>
                            </div>
                            <div class="stat-icon green">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-info">
                                <p class="stat-label">Toplam Kullanıcı</p>
                                <h3 class="stat-value">156</h3>
                            </div>
                            <div class="stat-icon purple">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-content">
                            <div class="stat-info">
                                <p class="stat-label">Aktif Kullanıcı</p>
                                <h3 class="stat-value">142</h3>
                                <div class="stat-trend positive">
                                    <span class="trend-arrow">↗</span>
                                    91%
                                </div>
                            </div>
                            <div class="stat-icon orange">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hızlı Erişim ve Sistem Durumu -->
                <div class="cards-grid">
                    <div class="quick-access-card">
                        <div class="card-header">
                            <h4 class="card-title">Hızlı Erişim</h4>
                        </div>
                        <div class="card-body">
                            <div class="quick-access-list">
                                <a href="#" class="quick-access-item">
                                    <div class="item-content">
                                        <div class="item-icon blue">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div class="item-info">
                                            <h5 class="item-title">Tüm Firmaları Yönet</h5>
                                            <p class="item-description">Sistemdeki tüm firmaları görüntüleyip yönetebilirsiniz.</p>
                                        </div>
                                    </div>
                                    <div class="item-meta">
                                        <span class="item-count">24</span>
                                        <i class="fas fa-arrow-right item-arrow"></i>
                                    </div>
                                </a>

                                <a href="#" class="quick-access-item">
                                    <div class="item-content">
                                        <div class="item-icon green">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div class="item-info">
                                            <h5 class="item-title">Kullanıcı Yönetimi</h5>
                                            <p class="item-description">Sistem kullanıcılarını görüntüleyin ve düzenleyin.</p>
                                        </div>
                                    </div>
                                    <div class="item-meta">
                                        <span class="item-count">156</span>
                                        <i class="fas fa-arrow-right item-arrow"></i>
                                    </div>
                                </a>

                                <a href="#" class="quick-access-item">
                                    <div class="item-content">
                                        <div class="item-icon purple">
                                            <i class="fas fa-chart-bar"></i>
                                        </div>
                                        <div class="item-info">
                                            <h5 class="item-title">Sistem Raporları</h5>
                                            <p class="item-description">Detaylı sistem analitiklerini inceleyin.</p>
                                        </div>
                                    </div>
                                    <div class="item-meta">
                                        <span class="item-count">12</span>
                                        <i class="fas fa-arrow-right item-arrow"></i>
                                    </div>
                                </a>

                                <a href="#" class="quick-access-item">
                                    <div class="item-content">
                                        <div class="item-icon gray">
                                            <i class="fas fa-cog"></i>
                                        </div>
                                        <div class="item-info">
                                            <h5 class="item-title">Sistem Ayarları</h5>
                                            <p class="item-description">Genel sistem konfigürasyonlarını yönetin.</p>
                                        </div>
                                    </div>
                                    <div class="item-meta">
                                        <i class="fas fa-arrow-right item-arrow"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="system-status-card">
                        <div class="card-header">
                            <div class="header-with-icon">
                                <i class="fas fa-activity"></i>
                                <h4 class="card-title">Sistem Durumu</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="status-metrics">
                                <div class="metric">
                                    <div class="metric-header">
                                        <span class="metric-label">Aktif Firma Oranı</span>
                                        <span class="metric-value green">88%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill green" style="width: 88%"></div>
                                    </div>
                                </div>

                                <div class="metric">
                                    <div class="metric-header">
                                        <span class="metric-label">Aktif Kullanıcı Oranı</span>
                                        <span class="metric-value blue">91%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill blue" style="width: 91%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="status-summary">
                                <div class="summary-icon">
                                    <i class="fas fa-trending-up"></i>
                                </div>
                                <div class="summary-content">
                                    <h6 class="summary-title">Sistem Performansı</h6>
                                    <p class="summary-text">Sistem optimal düzeyde çalışıyor. Tüm servisler aktif ve kullanıcı deneyimi sorunsuz.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Yetkiler Kartı -->
                <div class="privileges-card">
                    <div class="card-header">
                        <div class="header-with-icon">
                            <i class="fas fa-shield-alt"></i>
                            <h4 class="card-title">Super Admin Yetkileri</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="admin-badge">
                            <i class="fas fa-shield-alt"></i>
                            Super Admin Olarak Giriş Yaptınız
                        </div>
                        <p class="privileges-intro">Super Admin olarak sahip olduğunuz yetkiler:</p>
                        <div class="privileges-list">
                            <div class="privilege-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Tüm firmaları görüntüleme ve yönetme</span>
                            </div>
                            <div class="privilege-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Herhangi bir firmanın herhangi bir kullanıcısı olarak giriş yapma</span>
                            </div>
                            <div class="privilege-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Sistem genelinde istatistikler görme</span>
                            </div>
                            <div class="privilege-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Firma durumlarını aktif/pasif yapma</span>
                            </div>
                            <div class="privilege-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Tüm impersonation işlemlerini gerçekleştirme</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

@endsection