@extends('frontend.secure.user_master')

@section('user')

{{-- CSRF Token Meta Tag --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<style>
    body {
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .dashboard-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 30px;
        margin: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        overflow: visible !important;
    }

    .page-header {
        text-align: center;
        margin-bottom: 40px;
        color: white;
    }

    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 300;
        margin-bottom: 10px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .page-header p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0;
    }

    /* Modern Stat Cards */
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        border: none;
        margin-bottom: 20px;
        cursor: pointer;
        text-decoration: none;
        display: block;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border-left: 4px solid var(--accent-color);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        text-decoration: none;
    }

    .stat-card.blue {
        --accent-color: #4facfe;
    }

    .stat-card.green {
        --accent-color: #43e97b;
    }

    .stat-card.red {
        --accent-color: #fa709a;
    }

    .stat-card.purple {
        --accent-color: #a855f7;
    }

    .stat-card.orange {
        --accent-color: #ff9500;
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        background: var(--accent-color);
    }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 2.2rem;
        font-weight: bold;
        color: #2d3748;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #718096;
        margin-bottom: 10px;
    }

    .stat-change {
        font-size: 0.8rem;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 20px;
        display: inline-block;
    }

    .stat-change.positive {
        background: #f0fff4;
        color: #38a169;
    }

    .stat-change.negative {
        background: #fff5f5;
        color: #e53e3e;
    }

    .stat-change.neutral {
        background: #f7fafc;
        color: #718096;
    }

    /* Critical Stock Alert */
    .critical-stock-alert {
        background: linear-gradient(135deg, #ff6b6b, #ee5a52);
        color: white;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3); }
        50% { box-shadow: 0 8px 35px rgba(255, 107, 107, 0.5); }
        100% { box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3); }
    }

    .alert-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    .alert-icon {
        font-size: 1.5rem;
        animation: shake 1s infinite;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-2px); }
        75% { transform: translateX(2px); }
    }

    .critical-items {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .critical-item {
        background: rgba(255, 255, 255, 0.1);
        padding: 12px;
        border-radius: 8px;
        backdrop-filter: blur(10px);
    }

    /* Service Summary Cards */
    .service-summary {
        background: white;
        border-radius: 16px;
        padding: 25px;
        margin: 20px 0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .service-summary h5 {
        color: #2d3748;
        margin-bottom: 20px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.2rem;
    }

    .service-item {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        display: block;
        position: relative;
        overflow: hidden;
    }

    .service-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--service-color);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .service-item:hover::before {
        transform: scaleX(1);
    }

    .service-item:hover {
        transform: translateY(-2px);
        border-color: var(--service-color);
        background: white;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        text-decoration: none;
        color: inherit;
    }

    .service-item.today {
        --service-color: #4facfe;
    }

    .service-item.yesterday {
        --service-color: #fa709a;
    }

    .service-item.previous {
        --service-color: #a855f7;
    }

    .service-count {
        font-size: 2.2rem;
        font-weight: bold;
        color: var(--service-color);
        margin-bottom: 8px;
    }

    .service-label {
        font-size: 0.9rem;
        color: #718096;
        font-weight: 500;
    }

    /* Chart Containers */
    .chart-container {
        background: white;
        border-radius: 16px;
        padding: 25px;
        margin: 20px 0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        position: relative;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f1f3f4;
    }

    .chart-header h5 {
        color: #2d3748;
        margin: 0;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.2rem;
    }

    .time-filter {
        display: flex;
        gap: 5px;
        background: #f8f9fa;
        border-radius: 25px;
        padding: 4px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
    }

    .filter-btn {
        all: unset;
        padding: 8px 16px;
        border-radius: 20px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.3s ease;
        user-select: none;
        color: #718096;
    }

    .filter-btn:hover {
        background-color: #e2e8f0;
        color: #4a5568;
    }

    .filter-btn.active {
        background-color: #667eea;
        color: white;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }

    .chart-canvas {
        position: relative;
        height: 300px;
    }

    .loading {
        display: none;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #667eea;
        font-size: 1.1rem;
    }

    .error-message {
        background: #fed7d7;
        color: #c53030;
        padding: 12px 16px;
        border-radius: 8px;
        margin: 10px 0;
        border: 1px solid #feb2b2;
        display: none;
        font-weight: 500;
    }

    /* Recent Activities */
    .recent-activities {
        background: white;
        border-radius: 16px;
        padding: 25px;
        margin: 20px 0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .activity-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid #f1f3f4;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: white;
    }

    .activity-icon.high { background: #ff6b6b; }
    .activity-icon.medium { background: #ffa726; }
    .activity-icon.low { background: #66bb6a; }
    .activity-icon.completed { background: #42a5f5; }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 4px;
    }

    .activity-subtitle {
        font-size: 0.85rem;
        color: #718096;
    }

    .activity-time {
        font-size: 0.8rem;
        color: #a0aec0;
        font-weight: 500;
    }

    /* Quick Actions */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin: 20px 0;
    }

    .quick-action {
        background: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        border: 2px solid transparent;
    }

    .quick-action:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border-color: var(--action-color);
        text-decoration: none;
        color: inherit;
    }

    .quick-action-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        color: white;
        background: var(--action-color);
        margin: 0 auto 10px;
    }

    .quick-action.new-service { --action-color: #4facfe; }
    .quick-action.customers { --action-color: #43e97b; }
    .quick-action.inventory { --action-color: #a855f7; }
    .quick-action.reports { --action-color: #ff9500; }

    @media (max-width: 768px) {
        .dashboard-container {
            margin: 10px;
            padding: 20px;
        }
        
        .stat-card {
            margin-bottom: 15px;
        }
        
        .time-filter {
            flex-wrap: wrap;
        }
        
        .filter-btn {
            font-size: 0.8rem;
            padding: 6px 12px;
        }

        .page-header h1 {
            font-size: 2rem;
        }

        .critical-items {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-header">

    
</div>

<div class="dashboard-container">
    <!-- Hata mesajı -->
    <div id="errorMessage" class="error-message"></div>
    
    <!-- Kritik Stok Uyarısı -->
    <div id="criticalStockAlert" class="critical-stock-alert" style="display: none;">
        <div class="alert-header">
            <i class="fas fa-exclamation-triangle alert-icon"></i>
            <h4 style="margin: 0;">Kritik Stok Uyarısı!</h4>
        </div>
        <p style="margin: 0 0 10px 0;">Aşağıdaki ürünlerin stok miktarı kritik seviyede:</p>
        <div id="criticalItems" class="critical-items"></div>
    </div>

    <!-- Üst İstatistik Kartları -->
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="#" class="stat-card blue" id="totalServicesCard">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-value" id="totalServices">-</div>
                        <div class="stat-label">Aylık Servis Sayısı</div>
                        <span class="stat-change positive" id="servicesChange">+12% son aydan</span>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="{{ route('customers', ['tenant_id' => request()->route('tenant_id')]) }}" class="stat-card green" id="totalCustomersCard">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-value" id="totalCustomers">-</div>
                        <div class="stat-label">Müşteri Sayısı</div>
                        <span class="stat-change positive" id="customersChange">+2 son aydan</span>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="{{ route('staffs', ['tenant_id' => request()->route('tenant_id')]) }}" class="stat-card red" id="totalPersonnelCard">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-value" id="totalPersonnel">-</div>
                        <div class="stat-label">Personel Sayısı</div>
                        <span class="stat-change neutral" id="personnelChange">Değişim yok</span>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="#" onclick="window.location.href = buildCashUrl('today')" class="stat-card purple" id="totalCashCard">
                <div class="stat-header">
                    <div class="stat-content">
                        <div class="stat-value" id="totalCash">0,00 TL</div>
                        <div class="stat-label">Günlük Kasa</div>
                        <span class="stat-change positive" id="cashChange">+15% son günden</span>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-lira-sign"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Hızlı İşlemler -->
    <div class="quick-actions">
        <a href="#" class="quick-action new-service">
            <div class="quick-action-icon">
                <i class="fas fa-plus"></i>
            </div>
            <div>Yeni Servis</div>
        </a>
        <a href="#" class="quick-action customers">
            <div class="quick-action-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div>Müşteri Ekle</div>
        </a>
        <a href="#" class="quick-action inventory">
            <div class="quick-action-icon">
                <i class="fas fa-boxes"></i>
            </div>
            <div>Stok Girişi</div>
        </a>
        <a href="#" class="quick-action reports">
            <div class="quick-action-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div>Raporlar</div>
        </a>
    </div>

    <!-- Servis Özeti -->
    <div class="service-summary">
        <h5><i class="fas fa-chart-bar"></i> Günlük Servis Sayıları</h5>
        <div class="row">
            <div class="col-md-4">
                <a href="#" class="service-item today" id="todayServicesCard">
                    <div class="service-count" id="todayServices">-</div>
                    <div class="service-label">BUGÜN Alınan Servis</div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="#" class="service-item yesterday" id="yesterdayServicesCard">
                    <div class="service-count" id="yesterdayServices">-</div>
                    <div class="service-label">DÜN Alınan Servis</div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="#" class="service-item previous" id="previousServicesCard">
                    <div class="service-count" id="previousServices">-</div>
                    <div class="service-label">ÖNCEKİ GÜN Alınan Servis</div>
                </a>
            </div>
        </div>
    </div>

    <!-- Son Servis Talepleri -->
    <div class="recent-activities">
        <h5><i class="fas fa-clock"></i> Son Servis Talepleri</h5>
        <div id="recentServices">
            <!-- Dinamik olarak yüklenecek -->
        </div>
    </div>

    <!-- Grafikler -->
    <div class="row">
        <div class="col-lg-6">
            <div class="chart-container">
                <div class="chart-header">
                    <h5><i class="fas fa-chart-line"></i> Aylık Servis Trendi</h5>
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
            case 'monthly':
                // Son 30 gün
                const lastMonth = new Date(today);
                lastMonth.setDate(today.getDate() - 30);
                startDate = formatDate(lastMonth);
                endDate = formatDate(today);
                break;
            default:
                startDate = formatDate(today);
                endDate = formatDate(today);
        }

        return `${baseUrl}?dashboard_filter=1&dashboard_istatistik_tarih1=${startDate}&dashboard_istatistik_tarih2=${endDate}`;
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
                startDate = '2025-01-01';
                endDate = formatDate(today);
                break;
            default:
                startDate = formatDate(today);
                endDate = formatDate(today);
        }

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
                
                // Sayaçları güncelle
                updateCounter('totalServices', data.monthly_services || data.total_services);
                updateCounter('totalCustomers', data.total_customers);
                updateCounter('totalPersonnel', data.total_personnel);
                updateCounter('todayServices', data.today_services);
                updateCounter('yesterdayServices', data.yesterday_services);
                updateCounter('previousServices', data.previous_services);
                
                // Günlük kasa bilgisini güncelle
                document.getElementById('totalCash').textContent = 
                    new Intl.NumberFormat('tr-TR', {
                        style: 'currency',
                        currency: 'TRY',
                        minimumFractionDigits: 2
                    }).format(data.daily_cash?.net || 0);

                // Kritik stok kontrolü
                if (data.critical_stock && data.critical_stock.length > 0) {
                    showCriticalStock(data.critical_stock);
                }

                // Son servisleri göster
                if (data.recent_services) {
                    showRecentServices(data.recent_services);
                }
                        
            } else {
                console.error('Stats yüklenirken hata:', result.message);
                showError('İstatistikler yüklenirken hata oluştu: ' + result.message);
            }
        } catch (error) {
            console.error('AJAX Stats hatası:', error);
            showError('Sunucuya bağlanırken hata oluştu. Lütfen daha sonra tekrar deneyin.');
        }
    }

    // Kritik stok uyarısını göster
    function showCriticalStock(criticalItems) {
        const alertDiv = document.getElementById('criticalStockAlert');
        const itemsDiv = document.getElementById('criticalItems');
        
        itemsDiv.innerHTML = '';
        
        criticalItems.forEach(item => {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'critical-item';
            itemDiv.innerHTML = `
                <strong>${item.name}</strong><br>
                <small>Kalan: ${item.quantity} ${item.unit || 'adet'}</small>
            `;
            itemsDiv.appendChild(itemDiv);
        });
        
        alertDiv.style.display = 'block';
    }

    // Son servisleri göster
    function showRecentServices(services) {
        const container = document.getElementById('recentServices');
        container.innerHTML = '';
        
        if (services.length === 0) {
            container.innerHTML = '<p style="text-align: center; color: #718096; padding: 20px;">Henüz servis talebi bulunmuyor.</p>';
            return;
        }
        
        services.forEach(service => {
            const serviceDiv = document.createElement('div');
            serviceDiv.className = 'activity-item';
            
            let iconClass = 'activity-icon ';
            switch(service.priority?.toLowerCase()) {
                case 'high': iconClass += 'high'; break;
                case 'medium': iconClass += 'medium'; break;
                case 'low': iconClass += 'low'; break;
                default: iconClass += 'completed';
            }
            
            serviceDiv.innerHTML = `
                <div class="${iconClass}">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title">${service.customer_name}</div>
                    <div class="activity-subtitle">${service.description || 'Servis talebi'}</div>
                </div>
                <div class="activity-time">${service.created_at}</div>
            `;
            
            container.appendChild(serviceDiv);
        });
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
                console.error('Chart data yüklenirken hata:', result.message);
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

        // İlk veriyi yükle
        const dailyData = await loadChartData(7, 'daily');
        const hourlyData = await loadChartData(7, 'hourly');

        // Günlük grafik - Modern gradient bars
        dailyChart = new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: dailyData?.labels || [],
                datasets: [{
                    label: 'Günlük Servis',
                    data: dailyData?.data || [],
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderColor: '#667eea',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f3f4'
                        },
                        ticks: {
                            color: '#718096'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#718096'
                        },
                        categoryPercentage: 0.8,
                        barPercentage: 0.6
                    }
                }
            }
        });

        // Saatlik grafik - Smooth line chart
        hourlyChart = new Chart(hourlyCtx, {
            type: 'line',
            data: {
                labels: hourlyData?.labels || [],
                datasets: [{
                    label: 'Saatlik Servis',
                    data: hourlyData?.data || [],
                    borderColor: '#a855f7',
                    backgroundColor: 'rgba(168, 85, 247, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#a855f7',
                    pointBorderColor: '#a855f7',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f3f4'
                        },
                        ticks: {
                            color: '#718096'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#718096'
                        }
                    }
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
                if (chartType === 'daily') {
                    dailyChart.data.labels = data.labels;
                    dailyChart.data.datasets[0].data = data.data;
                    dailyChart.update('active');
                    currentDailyPeriod = period;
                } else {
                    hourlyChart.data.labels = data.labels;
                    hourlyChart.data.datasets[0].data = data.data;
                    hourlyChart.update('active');
                    currentHourlyPeriod = period;
                }
            }
        } catch (error) {
            console.error('Grafik güncellenirken hata:', error);
            showError('Grafik güncellenirken hata oluştu.');
        } finally {
            loadingEl.style.display = 'none';
        }
    }

    // Sayaçları güncelleme
    function updateCounter(elementId, targetValue) {
        const element = document.getElementById(elementId);
        if (!element) return;
        
        element.textContent = targetValue;
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // İstatistikleri yükle
        loadStats();
        
        // Grafikleri başlat
        initCharts();
        
        // Filter button event listeners
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const period = parseInt(this.dataset.period);
                const chartType = this.dataset.chart;
                
                // Active button güncelle
                this.parentElement.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Grafiği güncelle
                updateChart(chartType, period);
            });
        });

        // İstatistik kartları için click event listeners
        document.getElementById('totalServicesCard').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = buildServiceUrl('monthly');
        });

        document.getElementById('todayServicesCard').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = buildServiceUrl('today');
        });

        document.getElementById('yesterdayServicesCard').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = buildServiceUrl('yesterday');
        });

        document.getElementById('previousServicesCard').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = buildServiceUrl('previous');
        });
    });

    // Periyodik güncelleme (her 5 dakikada bir)
    setInterval(() => {
        loadStats();
    }, 5 * 60 * 1000);
</script>
@endsection