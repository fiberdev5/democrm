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
    <style>
        /* Reset ve Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background-color: #f8fafc;
    color: #334155;
    line-height: 1.6;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* Header */
.header {
    background: white;
    border-bottom: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 0;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.5rem;
}

.breadcrumb {
    display: flex;
    margin-top: 0.5rem;
}

.breadcrumb-item {
    font-size: 0.875rem;
    font-weight: 500;
    color: #3b82f6;
}

/* Main Content */
.main-content {
    padding: 2rem 0;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
    border: 1px solid #f1f5f9;
    padding: 1.5rem;
    transition: all 0.3s ease;
}

.stat-card:hover {
    box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.stat-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.stat-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #64748b;
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.stat-trend {
    display: flex;
    align-items: center;
    font-size: 0.75rem;
    font-weight: 600;
}

.stat-trend.positive {
    color: #059669;
}

.trend-arrow {
    margin-right: 0.25rem;
}

.stat-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.stat-icon.blue {
    background-color: #dbeafe;
    color: #2563eb;
}

.stat-icon.green {
    background-color: #d1fae5;
    color: #059669;
}

.stat-icon.purple {
    background-color: #e9d5ff;
    color: #7c3aed;
}

.stat-icon.orange {
    background-color: #fed7aa;
    color: #ea580c;
}

/* Cards Grid */
.cards-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

/* Quick Access Card */
.quick-access-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
    border: 1px solid #f1f5f9;
    overflow: hidden;
}

.card-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    background-color: #fafbfc;
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
}

.header-with-icon {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.header-with-icon i {
    color: #64748b;
}

.card-body {
    padding: 0;
}

.quick-access-list {
    display: flex;
    flex-direction: column;
}

.quick-access-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    text-decoration: none;
    color: inherit;
    border-bottom: 1px solid #f1f5f9;
    transition: background-color 0.2s ease;
}

.quick-access-item:last-child {
    border-bottom: none;
}

.quick-access-item:hover {
    background-color: #f8fafc;
}

.item-content {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    flex: 1;
}

.item-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.item-icon.blue {
    background-color: #dbeafe;
    color: #2563eb;
}

.item-icon.green {
    background-color: #d1fae5;
    color: #059669;
}

.item-icon.purple {
    background-color: #e9d5ff;
    color: #7c3aed;
}

.item-icon.gray {
    background-color: #f1f5f9;
    color: #64748b;
}

.item-info {
    flex: 1;
    min-width: 0;
}

.item-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.25rem;
    transition: color 0.2s ease;
}

.quick-access-item:hover .item-title {
    color: #3b82f6;
}

.item-description {
    font-size: 0.75rem;
    color: #64748b;
    line-height: 1.5;
}

.item-meta {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.item-count {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.625rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    background-color: #f1f5f9;
    color: #475569;
}

.item-arrow {
    color: #94a3b8;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.quick-access-item:hover .item-arrow {
    color: #3b82f6;
    transform: translateX(2px);
}

/* System Status Card */
.system-status-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
    border: 1px solid #f1f5f9;
}

.status-metrics {
    margin-bottom: 1.5rem;
}

.metric {
    margin-bottom: 1.5rem;
}

.metric:last-child {
    margin-bottom: 0;
}

.metric-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.metric-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #64748b;
}

.metric-value {
    font-size: 0.875rem;
    font-weight: 600;
}

.metric-value.green {
    color: #059669;
}

.metric-value.blue {
    color: #2563eb;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background-color: #f1f5f9;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.5s ease-out;
}

.progress-fill.green {
    background-color: #10b981;
}

.progress-fill.blue {
    background-color: #3b82f6;
}

.status-summary {
    padding: 1rem;
    background: linear-gradient(135deg, #f0f9ff 0%, #ecfdf5 100%);
    border-radius: 8px;
    border: 1px solid #bfdbfe;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.summary-icon {
    color: #2563eb;
    font-size: 1rem;
    margin-top: 0.125rem;
}

.summary-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.summary-text {
    font-size: 0.75rem;
    color: #64748b;
    line-height: 1.5;
}

/* Privileges Card */
.privileges-card {
    background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 100%);
    border-radius: 12px;
    border: 1px solid #cbd5e1;
    overflow: hidden;
}

.privileges-card .card-header {
    background: rgba(255, 255, 255, 0.8);
    border-bottom: 1px solid #cbd5e1;
}

.admin-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    background-color: #dbeafe;
    color: #1d4ed8;
    margin-bottom: 1rem;
}

.privileges-intro {
    font-size: 0.875rem;
    color: #475569;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.privileges-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.privilege-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    transition: color 0.2s ease;
}

.privilege-item:hover {
    color: #1e293b;
}

.privilege-item i {
    color: #10b981;
    font-size: 1rem;
    margin-top: 0.125rem;
    flex-shrink: 0;
}

.privilege-item span {
    font-size: 0.875rem;
    line-height: 1.5;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .cards-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
}

@media (max-width: 768px) {
    .container {
        padding: 0 1rem;
    }
    
    .main-content {
        padding: 1.5rem 0;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .cards-grid {
        gap: 1.5rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .stat-card {
        padding: 1.25rem;
    }
    
    .stat-value {
        font-size: 1.75rem;
    }
}
    </style>

@endsection