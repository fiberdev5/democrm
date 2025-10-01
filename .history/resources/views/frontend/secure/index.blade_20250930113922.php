@extends('frontend.secure.user_master')

@section('user')


{{-- CSRF Token Meta Tag --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<style>
    body {
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .dashboard-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 25px;
        margin: 15px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        overflow: visible !important;
    }

.stat-card {
    background: linear-gradient(135deg, var(--card-bg-1), var(--card-bg-2));
    border-radius: 16px;
    padding: 15px 20px;
    color: white;
    position: relative;
    overflow: hidden;
    transition: none; /* Tüm animasyonları kaldır */
    border: none;
    margin-bottom: 16px;
    cursor: pointer;
    text-decoration: none;
    display: block;
}

.stat-card:hover {
    color: white; /* Renk değişimini engelle */
    text-decoration: none;
}

.stat-card:hover .stat-label {
    text-decoration: underline; /* Sadece başlığın altını çiz */
}

    

    .stat-card.blue { --card-bg-1: #495057; --card-bg-2: #6c757d; }
    .stat-card.green { --card-bg-1: #495057; --card-bg-2: #6c757d; }
    .stat-card.red { --card-bg-1: #495057; --card-bg-2: #6c757d; }
    .stat-card.teal { --card-bg-1: #495057; --card-bg-2: #6c757d; }

    .stat-icon { font-size: 1.8rem; opacity: 0.8; float: right; margin-top: -5px; }
    .stat-value { font-size: 2rem; font-weight: bold; margin-bottom: 2px; position: relative; z-index: 2; }
    .stat-label { font-size: 0.85rem; opacity: 0.9; position: relative; z-index: 2; }

    /* Service Summary - More Compact */
    .service-summary {
        background: white;
        border-radius: 16px;
        padding: 15px; /* Reduced padding */
        margin: 0 0 16px 0; /* Adjusted margin */
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }

    .service-summary h5 {
        color: #333;
        margin-bottom: 15px; /* Reduced margin */
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.1rem;
    }

    .service-item {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 15px 12px; /* Reduced padding */
        text-align: center;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    /* .service-item:hover {
        transform: translateY(-2px);
        border-color: #fff;
        background: #fff;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
        text-decoration: none;
        color: inherit;
    } */

    .service-item:hover {
    color: inherit;  /* link rengini sabit tut */
    text-decoration: none; /* linkin kendisinin altı çizilmesin */
    }

    .service-item:hover div:not(.service-count) {
        text-decoration: underline; /* sadece açıklama yazısının altını çiz */
    }
    
    .service-count {
        font-size: 1.8rem; /* Slightly smaller font size */
        font-weight: bold;
        color: #333;
        margin-bottom: 4px;
    }

    .service-item.today .service-count { color: #007bff; }
    .service-item.yesterday .service-count { color: #dc3545; }
    .service-item.in-process .service-count { color: #ffc107; } /* Added for consistency */


/* Chart Containers - Responsive */
.chart-container {
    background: white;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    position: relative;
    height: 370px;
    display: flex;
    flex-direction: column;
}

.chart-container-cash {
    height: 280px;
    position: relative;
    background: white;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
}

.chart-canvas {
    position: relative;
    flex-grow: 1;
    min-height: 0;
    width: 100%;
    overflow: hidden;
}

.chart-canvas canvas {
    width: 100% !important;
    height: 100% !important;
    max-width: 100%;
    max-height: 100%;
}

/* Responsive breakpoints */
@media (max-width: 1200px) {
    .chart-container {
        height: 350px;
        padding: 18px;
    }
    
    .chart-container-cash {
        height: 260px;
        padding: 18px;
    }
}

@media (max-width: 992px) {
    .chart-container {
        height: 320px;
        padding: 16px;
    }
    
    .chart-container-cash {
        height: 240px;
        padding: 16px;
    }
    
    .chart-header h5 {
        font-size: 1rem;
    }
}

@media (max-width: 768px) {
    .dashboard-container { 
        margin: 8px;
        padding: 12px;
    }
    
    .stat-card { 
        margin-bottom: 8px;
        padding: 14px 18px;
    }
    
    .stat-value { font-size: 1.8rem; }
    .stat-icon { font-size: 1.5rem; }
    
    .row { 
        margin-left: -6px;
        margin-right: -6px; 
    }
    
    .row > [class*="col-"] { 
        padding-left: 6px;
        padding-right: 6px; 
    }
}

/* 576px breakpoint'i 768px'den SONRA gelmeli */
@media (max-width: 576px) {
    .stat-card { 
        margin-bottom: 6px !important;   /* -9px yerine 6px ve !important */
        padding: 12px 16px !important;   /* 6px 13px çok küçük, biraz arttırdım */
    }
    
    .stat-value { font-size: 1.6rem !important; }
    .stat-icon { font-size: 1.3rem !important; }
    
    .dashboard-container {
        margin: 5px !important;
        padding: 10px !important;
    }
}
@media (max-width: 768px) {
    .chart-container {
        height: 280px;
        padding: 15px;
        margin: 0 0 12px 0;
    }
    
    .chart-container-cash {
        height: 220px;
        padding: 15px;
        margin: 0 0 12px 0;
    }
    
    .chart-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 10px;
        padding-bottom: 10px;
    }
    
    .chart-header h5 {
        font-size: 0.95rem;
        margin-bottom: 0;
    }
    
    .time-filter {
        align-self: stretch;
        justify-content: center;
    }
    
    .filter-btn {
        font-size: 0.75rem;
        padding: 5px 12px;
    }
    
    /* Chart text ve font boyutları için */
    .chart-canvas {
        font-size: 12px;
    }
    
    /* Kasa sayfası özel elementler */
    .gelir .adi,
    .gelir .para,
    .gider .adi,
    .gider .para {
        font-size: 10px;
    }
}

@media (max-width: 576px) {
    .chart-container {
        height: 250px;
        padding: 12px;
    }
    
    .chart-container-cash {
        height: 200px;
        padding: 12px;
    }
    
    .chart-header {
        margin-bottom: 8px;
        padding-bottom: 8px;
    }
    
    .chart-header h5 {
        font-size: 0.9rem;
    }
    
    .time-filter {
        gap: 4px;
        padding: 2px 4px;
    }
    
    .filter-btn {
        font-size: 0.7rem;
        padding: 4px 8px;
    }
    
    /* Chart canvas için minimum boyutlar */
    .chart-canvas {
        min-height: 150px;
    }
}

/* Chart.js için özel responsive ayarları */
@media (max-width: 768px) {
    .chart-container .chart-canvas canvas,
    .chart-container-cash .chart-canvas canvas {
        font-size: 11px !important;
    }
}

/* Flexbox düzenlemeleri */
.row {
    margin-left: -8px;
    margin-right: -8px;
}

.row > [class*="col-"] {
    padding-left: 8px;
    padding-right: 8px;
}

/* Dashboard container responsive */
@media (max-width: 768px) {
    .dashboard-container {
        margin: 10px;
        padding: 15px;
    }
}

@media (max-width: 576px) {
    .dashboard-container {
        margin: 5px;
        padding: 10px;
    }
}

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f1f1f1;
        flex-shrink: 0; /* Header should not shrink */
    }

    .chart-header h5 {
        color: #333;
        margin: 0;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.1rem;
    }

    .time-filter {
        display: flex;
        gap: 8px;
        background: white;
        border-radius: 30px;
        padding: 4px 8px;
        box-shadow: 0 2px 6px rgba(102, 126, 234, 0.15);
    }

    .filter-btn {
        all: unset;
        padding: 6px 16px;
        border-radius: 30px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 600;
        transition: background-color 0.3s ease, color 0.3s ease;
        user-select: none;
    }

    /* .filter-btn:hover,
    .filter-btn:focus { background-color: #6c757d; color: #495057; outline: none; } */
    .filter-btn.active { background-color: #495057; color: white; box-shadow: 0 4px 8px #495057; }

    .chart-canvas {
        position: relative;
        flex-grow: 1; /* Canvas wrapper will fill remaining space */
        min-height: 0; /* Fix for flexbox overflow in some browsers */
    }

    .loading {
        display: none;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #667eea;
    }

    .page-title { color: white; text-align: center; margin-bottom: 20px; font-size: 2.5rem; font-weight: 300; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
    .error-message { background: #f8d7da; color: #721c24; padding: 10px 15px; border-radius: 8px; margin: 10px 0; border: 1px solid #f5c6cb; display: none; }

    /* Dashboard Card - Alt kartlar için iyileştirilmiş */
    .dashboard-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        color: #212529;
        height: 370px; /* Grafik kartları ile aynı yükseklik */
        display: flex;
        flex-direction: column;
    }

    .dashboard-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e9ecef;
        flex-shrink: 0;
    }

    .dashboard-card-header h5 { margin: 0; font-weight: 600; display: flex; align-items: center; gap: 10px; color: #333; font-size: 1.1rem; }
    .view-all-btn { color: #6c757d; text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: color 0.3s ease; }
    .view-all-btn:hover { color: #343a40; text-decoration: none; }

    .dashboard-card-body {
        flex: 1;
        overflow-y: auto;
    }
    
    .stock-item, .service-request-item { display: flex; align-items: center; gap: 12px; padding-bottom: 12px; border-bottom: 1px solid #e9ecef; }
    .stock-item:last-child, .service-request-item:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
    .stock-item { margin-bottom: 12px; }
    .service-request-item { padding: 10px 0; }

    .stock-icon-wrapper { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 1.1rem; flex-shrink: 0; }
    .stock-icon-wrapper.critical { background-color: rgba(220, 53, 69, 0.2); color: #dc3545; }
    .stock-icon-wrapper.low { background-color: rgba(255, 193, 7, 0.2); color: #ffc107; }

    .stock-details, .service-customer-info { flex-grow: 1; min-width: 0; }
    .stock-details h6, .service-customer-info h6 { margin: 0; font-size: 0.9rem; color: #212529; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .stock-details p, .service-customer-info p { margin: 0; font-size: 0.75rem; color: #6c757d; line-height: 1.2; }

    .stock-level { text-align: right; flex-shrink: 0; }
    .stock-level .level-text { font-size: 1rem; font-weight: bold; }
    .stock-level .level-label { font-size: 0.7rem; color: #6c757d; }
    .stock-level.critical .level-text { color: #dc3545; }
    .stock-level.low .level-text { color: #ffc107; }
    
    .stock-alert-footer { margin-top: 12px; padding-top: 12px; border-top: 1px solid #e9ecef; color: #dc3545; font-weight: 500; display: flex; align-items: center; gap: 6px; font-size: 0.8rem; }
    .service-status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; color: #fff; flex-shrink: 0; }

    /* Status Colors */
    .status-high { background-color: #dc3545; }
    .status-medium { background-color: #0ca4d3; }
    .status-completed { background-color: #28a79c; }
    .status-pending { background-color:#f8560b;  }
    .status-cancelled { background-color: #495057; }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-container { margin: 10px; padding: 15px; }
        .stat-card { margin-bottom: 12px; padding: 16px 20px; }
        .stat-value { font-size: 2rem; }
        .stat-icon { font-size: 1.6rem; }
        .chart-container, .dashboard-card { height: auto; min-height: 320px; }
        .time-filter { flex-wrap: wrap; }
        .filter-btn { font-size: 0.75rem; padding: 5px 12px; }
        .service-summary { padding: 16px; }
        .service-item { padding: 14px 12px; }
        .service-count { font-size: 1.6rem; }
    }

    .row { margin-left: -8px; margin-right: -8px; }
    .row > [class*="col-"] { padding-left: 8px; padding-right: 8px; }
    .mb-4 { margin-bottom: 12px !important; }
</style>

<div class="page-title" style="height:30px;"></div>
<div class="dashboard-container">
    <!-- Hata mesajı -->
    <div id="errorMessage" class="error-message"></div>
    
    <!-- Üst İstatistik Kartları -->
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="#" class="stat-card blue" id="totalServicesCard">
                <i class="fas fa-tools stat-icon"></i>
                <div class="stat-value" id="totalServices">-</div>
                <div class="stat-label">Aylık Servis Sayısı</div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="#" class="stat-card green" id="totalCustomersCard">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-value" id="totalCustomers">-</div>
                <div class="stat-label">Aylık Müşteri Sayısı</div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="{{ route('staffs', ['tenant_id' => request()->route('tenant_id')]) }}" class="stat-card red" id="totalPersonnelCard">
                <i class="fas fa-user-tie stat-icon"></i>
                <div class="stat-value" id="totalPersonnel">-</div>
                <div class="stat-label">Aktif Personel Sayısı</div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="#" onclick="window.location.href = buildCashUrl('total')" class="stat-card teal" id="totalCashCard">
                <i class="fas fa-lira-sign stat-icon"></i>
                <div class="stat-value" id="totalCash">0,00 TL</div>
                <div class="stat-label">Aylık Kasa</div>
            </a>
        </div>
    </div>
     
    <!-- Servis Özeti -->
    <div class="service-summary">
        <h5><i class="fas fa-chart-bar"></i> Servis Sayıları</h5>
        <div class="row">
            <div class="col-md-4">
                <a href="#" class="service-item today" id="todayServicesCard">
                    <div class="service-count" id="todayServices">-</div>
                    <div>BUGÜN Alınan Servis Sayısı</div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="#" class="service-item yesterday" id="todayCancelledCard">
                    <div class="service-count" id="todayCancelledServices">-</div>
                    <div>BUGÜN İptal Edilen Servisler</div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="#" class="service-item in-process" id="todayInProcessCard">
                    <div class="service-count" id="todayInProcessServices">-</div>
                    <div>BUGÜN İşlemde Olan Servisler</div>
                </a>
            </div>
        </div>
    </div>
   
    <!-- Grafikler -->
    <div class="row">
        <div class="col-lg-6">
            <div class="chart-container">
                <div class="chart-header">
                    <h5><i class="fas fa-chart-line"></i> Günlük Servis Trendi</h5>
                    <div class="time-filter">
                        <button class="filter-btn active" data-period="7" data-chart="daily">7 Gün</button>
                        <button class="filter-btn" data-period="15" data-chart="daily">15 Gün</button>
                        <button class="filter-btn" data-period="30" data-chart="daily">30 Gün</button>
                    </div>
                </div>
                <div class="loading"><i class="fas fa-spinner fa-spin"></i> Yükleniyor...</div>
                <div class="chart-canvas">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="chart-container">
                <div class="chart-header">
                    <h5><i class="fas fa-clock"></i> Saatlik Servis Dağılımı</h5>
                    <div class="time-filter">
                        <button class="filter-btn active" data-period="7" data-chart="hourly">7 Gün</button>
                        <button class="filter-btn" data-period="15" data-chart="hourly">15 Gün</button>
                        <button class="filter-btn" data-period="30" data-chart="hourly">30 Gün</button>
                    </div>
                </div>
                <div class="loading"><i class="fas fa-spinner fa-spin"></i> Yükleniyor...</div>
                <div class="chart-canvas">
                    <canvas id="hourlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Alt Kartlar - Operatör için düzenlenmiş -->
<div class="row">
    <!-- Son Servis Talepleri -->
    <div class="@unlessrole('Operatör') col-lg-6 @else col-lg-12 @endunlessrole">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5><i class="fas fa-clipboard-list"></i> Son Servis Talepleri</h5>
                <a href="{{ route('all.services', ['tenant_id' => request()->route('tenant_id')]) }}" class="view-all-btn">
                    Tümünü Gör <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="dashboard-card-body">
                @forelse ($last_services as $service)
                <div class="service-request-item">
                    <div class="service-customer-info">
                        <h6>{{ $service->customer_name }} <small class="text-muted">#{{ $service->service_id }}</small></h6>
                        <p>{{ $service->service_description }}</p>
                        <p style="font-size: 0.7rem; color: #8892b0;">
                            <i class="fas fa-user-cog"></i> {{ $service->technician_name ?? 'Atanmadı' }} | 
                            <i class="fas fa-calendar-alt"></i> {{ $service->estimated_date ? \Carbon\Carbon::parse($service->estimated_date)->format('d.m.Y') : 'Belirsiz' }}
                        </p>
                    </div>
                    <span class="service-status-badge {{ $service->status_info['class'] }}">{{ $service->status_info['name'] }}</span>
                </div>
                @empty
                <p class="text-center text-muted">Gösterilecek servis talebi bulunamadı.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Kritik Stok Uyarıları - Sadece Operatör olmayan kullanıcılar görebilir -->
    @unlessrole('Operatör')
    <div class="col-lg-6">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5><i class="fas fa-exclamation-triangle"></i> Kritik Stok Uyarıları</h5>
                <a href="{{ route('stocks', ['tenant_id' => request()->route('tenant_id')]) }}" class="view-all-btn">
                    Tümünü Gör <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="dashboard-card-body">
                @php $criticalCount = count($stock_alerts['critical']); @endphp
                
                {{-- Kritik Seviyedeki Ürünler --}}
                @foreach ($stock_alerts['critical'] as $item)
                <div class="stock-item">
                    <div class="stock-icon-wrapper critical"><i class="fas fa-box-open"></i></div>
                    <div class="stock-details">
                        <h6>{{ $item->urunAdi }}</h6>
                        <p>Kategori ID: {{ $item->urunKategori }}</p>
                    </div>
                    <div class="stock-level critical">
                        <div class="level-text">{{ $item->current_stock }} / {{ $item->threshold }}</div>
                        <div class="level-label">Kritik Seviye</div>
                    </div>
                </div>
                @endforeach

                {{-- Düşük Stoktaki Ürünler --}}
                @foreach ($stock_alerts['low'] as $item)
                <div class="stock-item">
                    <div class="stock-icon-wrapper low"><i class="fas fa-box-open"></i></div>
                    <div class="stock-details">
                        <h6>{{ $item->urunAdi }}</h6>
                        <p>Kategori ID: {{ $item->urunKategori }}</p>
                    </div>
                    <div class="stock-level low">
                        <div class="level-text">{{ $item->current_stock }} / {{ $item->threshold }}</div>
                        <div class="level-label">Düşük Stok</div>
                    </div>
                </div>
                @endforeach
                
                {{-- Eğer kritik ürün varsa alt uyarı mesajı --}}
                @if ($criticalCount > 0)
                <div class="stock-alert-footer">
                    <i class="fas fa-info-circle"></i> {{ $criticalCount }} ürün kritik stok seviyesinde! Acilen tedarik yapılması gerekiyor.
                </div>
                @endif

                {{-- Eğer hiç uyarı yoksa --}}
                @if(empty($stock_alerts['critical']) && empty($stock_alerts['low']))
                <p class="text-center text-muted">Kritik seviyede ürün bulunmamaktadır.</p>
                @endif
            </div>
        </div>
    </div>
    @endunlessrole
</div>

<script>
    // Global değişkenler
    let dailyChart, hourlyChart;
    let currentDailyPeriod = 7;
    let currentHourlyPeriod = 7;

    // CSRF token al
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Hata gösterme fonksiyonu
    function showError(message) {
        const errorDiv = document.getElementById('errorMessage');
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
        setTimeout(() => {
            errorDiv.style.display = 'none';
        }, 5000);
    }

    // Tarih formatı fonksiyonu
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // URL oluşturma fonksiyonu
    function buildServiceUrl(type) {
        const tenant_id = {{ $user->tenant->id }};
        const baseUrl = `/${tenant_id}/servisler`;
        const today = new Date();
        let startDate, endDate;

        let extraParams = '';

        switch(type) {
            case 'today':
                startDate = formatDate(today);
                endDate = formatDate(today);
                break;
            case 'today-cancelled':
                startDate = formatDate(today);
                endDate = formatDate(today);
                extraParams = '&status_group=cancelled';
                break;
            case 'today-in-process':
                startDate = formatDate(today);
                endDate = formatDate(today);
                extraParams = '&status_group=in_process';
                break;
            case 'total':
                const lastMonth = new Date(today);
                lastMonth.setMonth(today.getMonth() - 1);
                startDate = formatDate(lastMonth);
                endDate = formatDate(today);
                break;
            default:
                startDate = formatDate(today);
                endDate = formatDate(today);
        }

        return `${baseUrl}?dashboard_filter=1&dashboard_istatistik_tarih1=${startDate}&dashboard_istatistik_tarih2=${endDate}${extraParams}`;
    }

    function buildCashUrl(type) {
        const tenant_id = {{ $user->tenant->id }};
        const baseUrl = `/${tenant_id}/kasa-filtrele`;
        const today = new Date();
        let startDate, endDate;

        switch(type) {
            case 'today':
                startDate = formatDate(today);
                endDate = formatDate(today);
                break;
            case 'yesterday':
                const yesterday = new Date(today);
                yesterday.setDate(today.getDate() - 1);
                startDate = formatDate(yesterday);
                endDate = formatDate(yesterday);
                break;
            case 'previous':
                const previousDay = new Date(today);
                previousDay.setDate(today.getDate() - 2);
                startDate = formatDate(previousDay);
                endDate = formatDate(previousDay);
                break;
            case 'total':
                const lastMonth = new Date(today);
                lastMonth.setMonth(today.getMonth() - 1);
                startDate = formatDate(lastMonth);
                endDate = formatDate(today);
                break;
            default:
                startDate = formatDate(today);
                endDate = formatDate(today);
        }

        return `${baseUrl}?dashboard_filter=1&dashboard_istatistik_tarih1=${startDate}&dashboard_istatistik_tarih2=${endDate}`;
    }
    function buildCustomerUrl() {
        const tenant_id = {{ $user->tenant->id }};
        const baseUrl = `/${tenant_id}/musteriler`; // Müşteri listesi route'u
        const today = new Date();
        
        // Son 1 ay için tarihleri hesapla
        const lastMonth = new Date(today);
        lastMonth.setMonth(today.getMonth() - 1);
        const startDate = formatDate(lastMonth);
        const endDate = formatDate(today);

        // Filtre parametreleriyle tam URL'yi oluştur
        return `${baseUrl}?dashboard_filter=1&dashboard_istatistik_tarih1=${startDate}&dashboard_istatistik_tarih2=${endDate}`;
    }


    // İstatistikleri yükle
    async function loadStats() {
        try {
            const tenant_id = {{ $user->tenant->id }};
            const url = `/${tenant_id}/dashboard/stats`;
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();
            if (result.success) {
                const data = result.data;
                updateCounter('totalServices', data.total_services);
                updateCounter('totalCustomers', data.total_customers);
                updateCounter('totalPersonnel', data.total_personnel);
                updateCounter('todayServices', data.today_services);
                updateCounter('todayCancelledServices', data.today_cancelled_services);
                updateCounter('todayInProcessServices', data.today_in_process_services);
                document.getElementById('totalCash').textContent = 
                    new Intl.NumberFormat('tr-TR', {
                        style: 'currency',
                        currency: 'TRY',
                        minimumFractionDigits: 2
                    }).format(data.monthly_cash.net);
            } else {
                showError('İstatistikler yüklenirken hata oluştu: ' + result.message);
            }
        } catch (error) {
            console.error('AJAX Stats hatası:', error);
            showError('Sunucuya bağlanırken hata oluştu. Lütfen daha sonra tekrar deneyin.');
        }
    }

    // Grafik verilerini yükle 
    async function loadChartData(period, chartType) {
        try {
            const tenant_id = {{ $user->tenant->id }};
            const url = `/${tenant_id}/dashboard/chart-data?period=${period}&type=${chartType}`;
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();
            if (result.success) {
                return result.data;
            } else {
                showError('Grafik verisi yüklenirken hata oluştu: ' + result.message);
                return null;
            }
        } catch (error) {
            console.error('AJAX Chart hatası:', error);
            showError('Grafik verisi yüklenirken hata oluştu.');
            return null;
        }
    }

    // Grafik başlatma
    async function initCharts() {
        const dailyCtx = document.getElementById('dailyChart').getContext('2d');
        const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
        const dailyData = await loadChartData(7, 'daily');
        const hourlyData = await loadChartData(7, 'hourly');

        // Daily Chart
        dailyChart = new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: dailyData?.labels || [],
                datasets: [{
                    label: 'Günlük Servis',
                    data: dailyData?.data || [],
                    backgroundColor: 'rgba(79, 172, 254, 0.8)',
                    borderColor: '#4facfe',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f1f1' }, ticks: { color: '#6c757d' } },
                    x: { grid: { display: false }, ticks: { color: '#6c757d' }, categoryPercentage: 0.8, barPercentage: 0.6 }
                }
            }
        });

        // Hourly Chart
        hourlyChart = new Chart(hourlyCtx, {
            type: 'line',
            data: {
                labels: hourlyData?.labels || [],
                datasets: [{
                    label: 'Saatlik Servis',
                    data: hourlyData?.data || [],
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255,193,7,0.2)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ffc107',
                    pointBorderColor: '#ffc107',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f1f1' }, ticks: { color: '#6c757d' } },
                    x: { grid: { display: false }, ticks: { color: '#6c757d' } }
                }
            }
        });
    }

    // Grafik güncelleme fonksiyonu
    async function updateChart(chartType, period) {
        const loadingEl = document.querySelector(`.chart-container:has(#${chartType}Chart) .loading`);
        loadingEl.style.display = 'block';
        try {
            const data = await loadChartData(period, chartType);
            if (data) {
                const chart = (chartType === 'daily') ? dailyChart : hourlyChart;
                chart.data.labels = data.labels;
                chart.data.datasets[0].data = data.data;
                chart.update('active');
                if (chartType === 'daily') currentDailyPeriod = period;
                else currentHourlyPeriod = period;
            }
        } catch (error) {
            showError('Grafik güncellenirken hata oluştu.');
        } finally {
            loadingEl.style.display = 'none';
        }
    }

    function updateCounter(elementId, targetValue) {
        const element = document.getElementById(elementId);
        if (element) element.textContent = targetValue;
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadStats();
        initCharts();
        
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const period = parseInt(this.dataset.period);
                const chartType = this.dataset.chart;
                this.parentElement.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                updateChart(chartType, period);
            });
        });

        document.getElementById('totalServicesCard').addEventListener('click', (e) => { e.preventDefault(); window.location.href = buildServiceUrl('total'); });
        document.getElementById('todayServicesCard').addEventListener('click', (e) => { e.preventDefault(); window.location.href = buildServiceUrl('today'); });
        document.getElementById('todayCancelledCard').addEventListener('click', (e) => { e.preventDefault(); window.location.href = buildServiceUrl('today-cancelled'); });
        document.getElementById('todayInProcessCard').addEventListener('click', (e) => { e.preventDefault(); window.location.href = buildServiceUrl('today-in-process'); });
        document.getElementById('totalCustomersCard').addEventListener('click', (e) => { e.preventDefault(); window.location.href = buildCustomerUrl(); });
    });

    setInterval(loadStats, 5 * 60 * 1000); // 5 minutes
</script>
@endsection