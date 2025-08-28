<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TeknikPro - Modern Dashboard</title>
    <meta name="csrf-token" content="mock-csrf-token">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #2d3748;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
            color: white;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .page-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Stat Cards Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: rotate(45deg);
            transition: all 0.6s;
            opacity: 0;
        }

        .stat-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 30px 60px rgba(0,0,0,0.15);
        }

        .stat-card:hover::before {
            animation: shimmer 0.8s ease-in-out;
            opacity: 1;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .stat-card.critical {
            border-left: 5px solid #e53e3e;
            background: linear-gradient(135deg, rgba(229, 62, 62, 0.05), rgba(255,255,255,0.95));
            animation: pulse-critical 2s infinite;
        }

        @keyframes pulse-critical {
            0%, 100% { box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
            50% { box-shadow: 0 20px 40px rgba(229, 62, 62, 0.2); }
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .stat-icon.blue { background: linear-gradient(135deg, #4299e1, #3182ce); }
        .stat-icon.green { background: linear-gradient(135deg, #48bb78, #38a169); }
        .stat-icon.purple { background: linear-gradient(135deg, #9f7aea, #805ad5); }
        .stat-icon.orange { background: linear-gradient(135deg, #ed8936, #dd6b20); }
        .stat-icon.red { background: linear-gradient(135deg, #f56565, #e53e3e); }
        .stat-icon.teal { background: linear-gradient(135deg, #38b2ac, #319795); }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: #2d3748;
            margin-bottom: 8px;
            display: flex;
            align-items: baseline;
            gap: 5px;
        }

        .stat-label {
            font-size: 0.95rem;
            color: #718096;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .trend-up { color: #38a169; }
        .trend-down { color: #e53e3e; }
        .trend-neutral { color: #718096; }

        /* Service Summary Cards */
        .service-summary {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .service-summary h3 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .service-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .service-item {
            background: #f7fafc;
            border-radius: 15px;
            padding: 25px 20px;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .service-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
            transition: left 0.5s;
        }

        .service-item:hover {
            transform: translateY(-5px);
            background: white;
            border-color: #e2e8f0;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .service-item:hover::before {
            left: 100%;
        }

        .service-count {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .service-count.today { color: #3182ce; }
        .service-count.yesterday { color: #e53e3e; }
        .service-count.previous { color: #805ad5; }

        .service-label {
            font-size: 0.9rem;
            color: #718096;
            font-weight: 500;
        }

        /* Charts Section */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
        }

        .chart-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .chart-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .time-filter {
            display: flex;
            background: #f7fafc;
            border-radius: 25px;
            padding: 5px;
            gap: 5px;
        }

        .filter-btn {
            padding: 8px 16px;
            border-radius: 20px;
            border: none;
            background: transparent;
            color: #718096;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: white;
            color: #3182ce;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .chart-canvas {
            position: relative;
            height: 300px;
        }

        /* Critical Stock Alert */
        .critical-alert {
            background: linear-gradient(135deg, #fed7d7, #feb2b2);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid #e53e3e;
            animation: pulse-alert 2s infinite;
        }

        @keyframes pulse-alert {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }

        .alert-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            color: #742a2a;
            font-weight: 700;
        }

        .critical-items {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .critical-item {
            background: rgba(255,255,255,0.8);
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #742a2a;
            font-weight: 600;
        }

        /* Loading States */
        .loading {
            display: none;
            text-align: center;
            color: #718096;
            padding: 20px;
        }

        .loading i {
            font-size: 1.5rem;
            margin-bottom: 10px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container { padding: 15px; }
            .stats-grid { grid-template-columns: 1fr; gap: 20px; }
            .charts-grid { grid-template-columns: 1fr; }
            .chart-header { flex-direction: column; align-items: flex-start; }
            .service-grid { grid-template-columns: 1fr; }
            .stat-card { padding: 20px; }
            .stat-value { font-size: 2rem; }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1>Dashboard</h1>
            <p>Teknik servis operasyonlarınızın genel görünümü</p>
        </div>

        <!-- Critical Stock Alert -->
        <div id="criticalAlert" class="critical-alert" style="display: none;">
            <div class="alert-header">
                <i class="fas fa-exclamation-triangle"></i>
                <span>KRİTİK STOK UYARISI!</span>
            </div>
            <div class="critical-items" id="criticalItems">
                <!-- Critical items will be populated here -->
            </div>
        </div>

        <!-- Main Stats Grid -->
        <div class="stats-grid">
            <!-- Monthly Services -->
            <div class="stat-card" id="monthlyServicesCard">
                <div class="stat-header">
                    <div>
                        <div class="stat-value" id="monthlyServices">-</div>
                        <div class="stat-label">Aylık Servis Sayısı</div>
                        <div class="stat-trend trend-up" id="monthlyTrend">
                            <i class="fas fa-arrow-up"></i>
                            <span>+12% son aydan</span>
                        </div>
                    </div>
                    <div class="stat-icon blue">
                        <i class="fas fa-tools"></i>
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="stat-card" id="customersCard">
                <div class="stat-header">
                    <div>
                        <div class="stat-value" id="totalCustomers">-</div>
                        <div class="stat-label">Aktif Müşteriler</div>
                        <div class="stat-trend trend-up" id="customerTrend">
                            <i class="fas fa-arrow-up"></i>
                            <span>+3 yeni müşteri</span>
                        </div>
                    </div>
                    <div class="stat-icon green">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>

            <!-- Total Staff -->
            <div class="stat-card" id="staffCard">
                <div class="stat-header">
                    <div>
                        <div class="stat-value" id="totalStaff">-</div>
                        <div class="stat-label">Aktif Personel</div>
                        <div class="stat-trend trend-neutral" id="staffTrend">
                            <i class="fas fa-minus"></i>
                            <span>Değişim yok</span>
                        </div>
                    </div>
                    <div class="stat-icon purple">
                        <i class="fas fa-user-tie"></i>
                    </div>
                </div>
            </div>

            <!-- Daily Cash -->
            <div class="stat-card" id="cashCard">
                <div class="stat-header">
                    <div>
                        <div class="stat-value" id="dailyCash">₺0,00</div>
                        <div class="stat-label">Günlük Kasa</div>
                        <div class="stat-trend trend-up" id="cashTrend">
                            <i class="fas fa-arrow-up"></i>
                            <span>+15% dünden</span>
                        </div>
                    </div>
                    <div class="stat-icon teal">
                        <i class="fas fa-lira-sign"></i>
                    </div>
                </div>
            </div>

            <!-- Critical Stock -->
            <div class="stat-card critical" id="criticalStockCard">
                <div class="stat-header">
                    <div>
                        <div class="stat-value" id="criticalStock">-</div>
                        <div class="stat-label">Kritik Stok Ürünleri</div>
                        <div class="stat-trend trend-down" id="criticalTrend">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Acil eylem gerekli!</span>
                        </div>
                    </div>
                    <div class="stat-icon red">
                        <i class="fas fa-box-open"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Services -->
            <div class="stat-card" id="pendingCard">
                <div class="stat-header">
                    <div>
                        <div class="stat-value" id="pendingServices">-</div>
                        <div class="stat-label">Bekleyen İşlemler</div>
                        <div class="stat-trend trend-down" id="pendingTrend">
                            <i class="fas fa-clock"></i>
                            <span>-8% düne göre</span>
                        </div>
                    </div>
                    <div class="stat-icon orange">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Summary -->
        <div class="service-summary">
            <h3><i class="fas fa-chart-bar"></i> Son 3 Günün Servis Detayları</h3>
            <div class="service-grid">
                <div class="service-item" id="todayCard">
                    <div class="service-count today" id="todayCount">-</div>
                    <div class="service-label">BUGÜN Alınan</div>
                </div>
                <div class="service-item" id="yesterdayCard">
                    <div class="service-count yesterday" id="yesterdayCount">-</div>
                    <div class="service-label">DÜN Alınan</div>
                </div>
                <div class="service-item" id="previousCard">
                    <div class="service-count previous" id="previousCount">-</div>
                    <div class="service-label">ÖNCEKİ GÜN</div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-grid">
            <!-- Daily Trend Chart -->
            <div class="chart-container">
                <div class="chart-header">
                    <div class="chart-title">
                        <i class="fas fa-chart-line"></i>
                        Aylık Servis Trendi
                    </div>
                    <div class="time-filter">
                        <button class="filter-btn active" data-period="7" data-chart="daily">7 Gün</button>
                        <button class="filter-btn" data-period="15" data-chart="daily">15 Gün</button>
                        <button class="filter-btn" data-period="30" data-chart="daily">30 Gün</button>
                    </div>
                </div>
                <div class="loading" id="dailyLoading">
                    <i class="fas fa-spinner"></i>
                    <div>Veriler yükleniyor...</div>
                </div>
                <div class="chart-canvas">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>

            <!-- Hourly Distribution Chart -->
            <div class="chart-container">
                <div class="chart-header">
                    <div class="chart-title">
                        <i class="fas fa-clock"></i>
                        Saatlik Dağılım
                    </div>
                    <div class="time-filter">
                        <button class="filter-btn active" data-period="7" data-chart="hourly">7 Gün</button>
                        <button class="filter-btn" data-period="15" data-chart="hourly">15 Gün</button>
                        <button class="filter-btn" data-period="30" data-chart="hourly">30 Gün</button>
                    </div>
                </div>
                <div class="loading" id="hourlyLoading">
                    <i class="fas fa-spinner"></i>
                    <div>Veriler yükleniyor...</div>
                </div>
                <div class="chart-canvas">
                    <canvas id="hourlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let dailyChart, hourlyChart;
        let currentTenant = 1; // This should come from your backend

        // Mock data for demonstration - replace with your API calls
        const mockData = {
            monthly_services: 247,
            total_customers: 89,
            total_staff: 12,
            daily_cash: 124750,
            critical_stock_count: 5,
            pending_services: 23,
            today_services: 8,
            yesterday_services: 12,
            previous_services: 5,
            critical_items: [
                { name: 'LCD Ekran', stock: 2 },
                { name: 'Anakart', stock: 1 },
                { name: 'Batarya', stock: 3 },
                { name: 'Kamera Modülü', stock: 1 },
                { name: 'Şarj Soketi', stock: 2 }
            ]
        };

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
            initializeCharts();
            setupEventListeners();
        });

        // Load dashboard data
        function loadDashboardData() {
            // Update stats - in real app, replace with API calls
            updateStatCard('monthlyServices', mockData.monthly_services);
            updateStatCard('totalCustomers', mockData.total_customers);
            updateStatCard('totalStaff', mockData.total_staff);
            updateStatCard('dailyCash', formatCurrency(mockData.daily_cash));
            updateStatCard('criticalStock', mockData.critical_stock_count);
            updateStatCard('pendingServices', mockData.pending_services);
            
            // Update service counts
            updateStatCard('todayCount', mockData.today_services);
            updateStatCard('yesterdayCount', mockData.yesterday_services);
            updateStatCard('previousCount', mockData.previous_services);
            
            // Show critical stock alert if needed
            if (mockData.critical_stock_count > 0) {
                showCriticalStockAlert(mockData.critical_items);
            }
        }

        // Update stat card with animation
        function updateStatCard(elementId, value) {
            const element = document.getElementById(elementId);
            if (!element) return;
            
            // Add loading animation
            element.style.opacity = '0.5';
            
            setTimeout(() => {
                element.textContent = value;
                element.style.opacity = '1';
                element.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    element.style.transform = 'scale(1)';
                }, 200);
            }, 300);
        }

        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('tr-TR', {
                style: 'currency',
                currency: 'TRY',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Show critical stock alert
        function showCriticalStockAlert(items) {
            const alertDiv = document.getElementById('criticalAlert');
            const itemsDiv = document.getElementById('criticalItems');
            
            itemsDiv.innerHTML = items.map(item => 
                `<div class="critical-item">${item.name} (${item.stock} adet)</div>`
            ).join('');
            
            alertDiv.style.display = 'block';
        }

        // Initialize charts
        function initializeCharts() {
            const dailyCtx = document.getElementById('dailyChart').getContext('2d');
            const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');

            // Daily chart with modern gradient
            dailyChart = new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: generateDateLabels(30),
                    datasets: [{
                        label: 'Günlük Servis',
                        data: generateMockChartData(30, 5, 25),
                        borderColor: '#4299e1',
                        backgroundColor: 'rgba(66, 153, 225, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#4299e1',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#4299e1',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { color: '#718096' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#718096' }
                        }
                    },
                    elements: {
                        point: {
                            hoverBackgroundColor: '#3182ce'
                        }
                    }
                }
            });

            // Hourly chart with vibrant colors
            hourlyChart = new Chart(hourlyCtx, {
                type: 'bar',
                data: {
                    labels: generateHourLabels(),
                    datasets: [{
                        label: 'Saatlik Dağılım',
                        data: generateMockChartData(24, 0, 15),
                        backgroundColor: 'rgba(156, 163, 175, 0.8)',
                        borderColor: '#9ca3af',
                        borderWidth: 1,
                        borderRadius: 6,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { color: '#718096' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#718096' }
                        }
                    }
                }
            });
        }

        // Generate mock data
        function generateDateLabels(days) {
            const labels = [];
            for (let i = days - 1; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                labels.push(date.toLocaleDateString('tr-TR', { month: 'short', day: 'numeric' }));
            }
            return labels;
        }

        function generateHourLabels() {
            return Array.from({ length: 24 }, (_, i) => `${i.toString().padStart(2, '0')}:00`);
        }

        function generateMockChartData(count, min, max) {
            return Array.from({ length: count }, () => 
                Math.floor(Math.random() * (max - min + 1)) + min
            );
        }

        // Setup event listeners
        function setupEventListeners() {
            // Filter buttons
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const period = this.dataset.period;
                    const chartType = this.dataset.chart;
                    
                    // Update active button
                    this.parentElement.querySelectorAll('.filter-btn').forEach(b => 
                        b.classList.remove('active')
                    );
                    this.classList.add('active');
                    
                    // Update chart
                    updateChart(chartType, period);
                });
            });

            // Card click handlers
            document.getElementById('monthlyServicesCard').addEventListener('click', () => {
                // Navigate to services page
                console.log('Navigate to monthly services');
            });

            document.getElementById('criticalStockCard').addEventListener('click', () => {
                // Navigate to stock management
                console.log('Navigate to stock management');
            });

            // Auto refresh every 5 minutes
            setInterval(loadDashboardData, 5 * 60 * 1000);
        }

        // Update chart with new data
        function updateChart(chartType, period) {
            const loadingId = chartType + 'Loading';
            const loadingEl = document.getElementById(loadingId);
            
            if (loadingEl) {
                loadingEl.style.display = 'block';
            }

            // Simulate API call delay
            setTimeout(() => {
                const chart = chartType === 'daily' ? dailyChart : hourlyChart;
                const newData = chartType === 'daily' ? 
                    generateMockChartData(parseInt(period), 5, 25) :
                    generateMockChartData(24, 0, 15);
                
                const newLabels = chartType === 'daily' ? 
                    generateDateLabels(parseInt(period)) :
                    generateHourLabels();

                // Update chart data with animation
                chart.data.labels = newLabels;
                chart.data.datasets[0].data = newData;
                chart.update('active');

                if (loadingEl) {
                    loadingEl.style.display = 'none';
                }
            }, 500);
        }

        // Real API integration functions (replace these with your actual endpoints)
        async function loadStats() {
            try {
                const response = await fetch(`/${currentTenant}/dashboard/stats`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        const data = result.data;
                        
                        // Update all stats
                        updateStatCard('monthlyServices', data.monthly_services || 0);
                        updateStatCard('totalCustomers', data.total_customers || 0);
                        updateStatCard('totalStaff', data.total_personnel || 0);
                        updateStatCard('dailyCash', formatCurrency(data.daily_cash?.net || 0));
                        updateStatCard('criticalStock', data.critical_stock_count || 0);
                        updateStatCard('pendingServices', data.pending_services || 0);
                        updateStatCard('todayCount', data.today_services || 0);
                        updateStatCard('yesterdayCount', data.yesterday_services || 0);
                        updateStatCard('previousCount', data.previous_services || 0);
                        
                        // Handle critical stock alert
                        if (data.critical_items && data.critical_items.length > 0) {
                            showCriticalStockAlert(data.critical_items);
                        }
                    }
                }
            } catch (error) {
                console.error('Error loading stats:', error);
                // Fallback to mock data
                loadDashboardData();
            }
        }

        async function loadChartData(period, chartType) {
            try {
                const response = await fetch(`/${currentTenant}/dashboard/chart-data?period=${period}&type=${chartType}`, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        return result.data;
                    }
                }
                
                // Fallback to mock data
                return {
                    labels: chartType === 'daily' ? generateDateLabels(period) : generateHourLabels(),
                    data: chartType === 'daily' ? generateMockChartData(period, 5, 25) : generateMockChartData(24, 0, 15)
                };
            } catch (error) {
                console.error('Error loading chart data:', error);
                return {
                    labels: chartType === 'daily' ? generateDateLabels(period) : generateHourLabels(),
                    data: chartType === 'daily' ? generateMockChartData(period, 5, 25) : generateMockChartData(24, 0, 15)
                };
            }
        }

        // Navigation helper functions
        function buildServiceUrl(type) {
            const baseUrl = `/${currentTenant}/servisler`;
            const today = new Date();
            let startDate, endDate;

            const formatDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };

            switch(type) {
                case 'today':
                    startDate = endDate = formatDate(today);
                    break;
                case 'yesterday':
                    const yesterday = new Date(today);
                    yesterday.setDate(today.getDate() - 1);
                    startDate = endDate = formatDate(yesterday);
                    break;
                case 'previous':
                    const previousDay = new Date(today);
                    previousDay.setDate(today.getDate() - 2);
                    startDate = endDate = formatDate(previousDay);
                    break;
                case 'monthly':
                    const lastMonth = new Date(today);
                    lastMonth.setMonth(today.getMonth() - 1);
                    startDate = formatDate(lastMonth);
                    endDate = formatDate(today);
                    break;
                default:
                    startDate = endDate = formatDate(today);
            }

            return `${baseUrl}?dashboard_filter=1&dashboard_istatistik_tarih1=${startDate}&dashboard_istatistik_tarih2=${endDate}`;
        }

        function buildCashUrl(type) {
            const baseUrl = `/${currentTenant}/kasa-filtrele`;
            const today = new Date();
            const formatDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };

            let startDate, endDate;
            
            switch(type) {
                case 'today':
                    startDate = endDate = formatDate(today);
                    break;
                case 'monthly':
                    startDate = '2025-01-01';
                    endDate = formatDate(today);
                    break;
                default:
                    startDate = endDate = formatDate(today);
            }

            return `${baseUrl}?dashboard_filter=1&dashboard_istatistik_tarih1=${startDate}&dashboard_istatistik_tarih2=${endDate}`;
        }

        // Enhanced card interactions
        function setupCardClickHandlers() {
            // Monthly services card
            document.getElementById('monthlyServicesCard').addEventListener('click', () => {
                window.location.href = buildServiceUrl('monthly');
            });

            // Customers card
            document.getElementById('customersCard').addEventListener('click', () => {
                window.location.href = `/${currentTenant}/musteriler`;
            });

            // Staff card
            document.getElementById('staffCard').addEventListener('click', () => {
                window.location.href = `/${currentTenant}/personel`;
            });

            // Cash card
            document.getElementById('cashCard').addEventListener('click', () => {
                window.location.href = buildCashUrl('today');
            });

            // Critical stock card
            document.getElementById('criticalStockCard').addEventListener('click', () => {
                window.location.href = `/${currentTenant}/stok-yonetimi?kritik=1`;
            });

            // Pending services card
            document.getElementById('pendingCard').addEventListener('click', () => {
                window.location.href = buildServiceUrl('today') + '&durum=bekliyor';
            });

            // Service summary cards
            document.getElementById('todayCard').addEventListener('click', () => {
                window.location.href = buildServiceUrl('today');
            });

            document.getElementById('yesterdayCard').addEventListener('click', () => {
                window.location.href = buildServiceUrl('yesterday');
            });

            document.getElementById('previousCard').addEventListener('click', () => {
                window.location.href = buildServiceUrl('previous');
            });
        }

        // Call setup functions when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Try to load real data first, fallback to mock
            if (typeof loadStats === 'function') {
                loadStats();
            } else {
                loadDashboardData();
            }
            
            initializeCharts();
            setupEventListeners();
            setupCardClickHandlers();
        });

        // Add some visual effects
        function addVisualEffects() {
            // Stagger animation for stat cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('animate-fade-in');
            });

            // Add intersection observer for animations
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-slide-in');
                    }
                });
            });

            document.querySelectorAll('.chart-container, .service-summary').forEach(el => {
                observer.observe(el);
            });
        }

        // Add CSS animations
        const animationCSS = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }

            @keyframes slideIn {
                from { opacity: 0; transform: translateX(-30px); }
                to { opacity: 1; transform: translateX(0); }
            }

            .animate-fade-in {
                animation: fadeIn 0.6s ease-out forwards;
            }

            .animate-slide-in {
                animation: slideIn 0.8s ease-out forwards;
            }
        `;

        // Inject animation CSS
        const styleSheet = document.createElement('style');
        styleSheet.textContent = animationCSS;
        document.head.appendChild(styleSheet);

        // Initialize visual effects
        setTimeout(addVisualEffects, 100);
    </script>
</body>
</html>