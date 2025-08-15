@extends('frontend.secure.user_master')

@section('user')

{{-- CSRF Token Meta Tag --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

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
            padding: 30px;
            margin: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: visible !important;
            
        }

        .stat-card {
            background: linear-gradient(135deg, var(--card-bg-1), var(--card-bg-2));
            border-radius: 16px;
            padding: 25px;
            color: white;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            border: none;
            margin-bottom: 20px;
            cursor: pointer;
            text-decoration: none;
            display: block;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            color: white;
            text-decoration: none;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }

        .stat-card.blue {
      --card-bg-1: #495057; 
      --card-bg-2: #6c757d; 
        }

        .stat-card.green {
            --card-bg-1: #495057; 
            --card-bg-2: #6c757d; 
        }

        .stat-card.red {
            --card-bg-1: #495057; 
            --card-bg-2: #6c757d; 
        }

        .stat-card.teal {
            --card-bg-1: #495057;
            --card-bg-2: #6c757d; 
            
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
            float: right;
            margin-top: -10px;
        }

        .stat-value {
            font-size: 2.8rem;
            font-weight: bold;
            margin-bottom: 5px;
            position: relative;
            z-index: 2;
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        .service-summary {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin: 20px 0;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        .service-summary h5 {
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
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
        }

        .service-item:hover {
            transform: translateY(-2px);
            border-color: #fff;
            background: #fff;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
            text-decoration: none;
            color: inherit;
        }

        .service-count {
            font-size: 2.2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        .service-item.today .service-count {
            color: #007bff; /* parlak mavi - bootstrap primary */
        }

        .service-item.yesterday .service-count {
            color: #dc3545; /* canlı kırmızı - bootstrap danger */
        }

        .service-item.previous .service-count {
            color: #6c757d; /* orta koyulukta gri */
        }

        .chart-container {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin: 20px 0;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            position: relative;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f1f1;
        }

        .chart-header h5 {
            color: #333;
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

       /* Nav menü kapsayıcısı */
        .time-filter {
            display: flex;
            gap: 10px;
            background: white;
            border-radius: 30px;
            padding: 5px 10px;
            box-shadow: 0 2px 6px rgba(102, 126, 234, 0.15);
        }

        /* Nav item olarak butonlar */
        .filter-btn {
            all: unset; /* buton varsayılanlarını sıfırlar */
            padding: 8px 20px;
            border-radius: 30px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            transition: background-color 0.3s ease, color 0.3s ease;
            user-select: none;
        }

        /* Hover ve odak durumları */
        .filter-btn:hover,
        .filter-btn:focus {
            background-color: #6c757d; 
            color: #495057;
            outline: none;
        }

        /* Aktif buton */
        .filter-btn.active {
            background-color: #495057; 
            color: white;
            box-shadow: 0 4px 8px #495057;
        }

        /* Responsive küçülünce biraz küçült */
        @media (max-width: 768px) {
            .filter-btn {
                padding: 6px 14px;
                font-size: 0.85rem;
            }
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
        }

        .page-title {
            color: white;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5rem;
            font-weight: 300;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px 15px;
            border-radius: 8px;
            margin: 10px 0;
            border: 1px solid #f5c6cb;
            display: none;
        }

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
        }
        .stat-card {
            padding: 15px 20px !important;
        }

        .stat-icon {
            font-size: 1.8rem !important;
            margin-top: -5px !important;
        }

        .stat-value {
            font-size: 1.8rem !important;
        }

        .stat-label {
            font-size: 0.85rem !important;
        }
        .service-summary {
            padding: 15px 20px;  /* Daha az iç boşluk */
            margin: 10px 0;      /* Daha az üst-alt boşluk */
        }

        .service-summary h5 {
            font-size: 1.1rem;   /* Başlık biraz küçüldü */
            margin-bottom: 15px;
        }

        .service-item {
            padding: 15px 10px;  /* Daha küçük kutu iç boşluğu */
            border-radius: 10px;
            font-size: 0.9rem;   /* İçerik genel font küçüldü */
        }

        .service-count {
            font-size: 1.8rem;   /* Sayı boyutu küçüldü */
            font-weight: 700;
            margin-bottom: 6px;
        }

        .service-item div:not(.service-count) {
            font-size: 0.85rem;  /* Alt açıklama metni daha küçük */
        }
    </style>

        <div class="page-title" style="height:40px;"></div>
        <div class="dashboard-container">
            <!-- Hata mesajı -->
            <div id="errorMessage" class="error-message"></div>
            
            <!-- Üst İstatistik Kartları -->
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <a href="#" class="stat-card blue" id="totalServicesCard">
                        <i class="fas fa-tools stat-icon"></i>
                        <div class="stat-value" id="totalServices">-</div>
                        <div class="stat-label">Toplam Servis Sayısı</div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <a href="{{ route('customers', ['tenant_id' => request()->route('tenant_id')]) }}" class="stat-card green" id="totalCustomersCard">
                        <i class="fas fa-users stat-icon"></i>
                        <div class="stat-value" id="totalCustomers">-</div>
                        <div class="stat-label">Müşteri Sayısı</div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <a href="{{ route('staffs', ['tenant_id' => request()->route('tenant_id')]) }}" class="stat-card red" id="totalPersonnelCard">
                        <i class="fas fa-user-tie stat-icon"></i>
                        <div class="stat-value" id="totalPersonnel">-</div>
                        <div class="stat-label">Personel Sayısı</div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                     <a href="#" onclick="window.location.href = buildCashUrl('today')" 
                       class="stat-card teal" id="totalCashCard">
                        <i class="fas fa-lira-sign stat-icon"></i>
                        <div class="stat-value" id="totalCash">0,00 TL</div>
                        <div class="stat-label">Günlük Kasa</div>
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
                        <a href="#" class="service-item yesterday" id="yesterdayServicesCard">
                            <div class="service-count" id="yesterdayServices">-</div>
                            <div>DÜN Alınan Servis Sayısı</div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="#" class="service-item previous" id="previousServicesCard">
                            <div class="service-count" id="previousServices">-</div>
                            <div>ÖNCEKİ GÜN Alınan Servis Sayısı</div>
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
                case 'total':
                    // Toplam servisler için başlangıç tarihi çok eskiye ayarla
                    startDate = '2025-01-01';
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
            const response = await fetch('/api/dashboard/stats', {
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
                
                // Sayaçları direkt güncelle (animasyon yok)
                updateCounter('totalServices', data.total_services);
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
                    }).format(data.daily_cash.net);
                    
            } else {
                console.error('Stats yüklenirken hata:', result.message);
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
                const response = await fetch(`/api/dashboard/chart-data?period=${period}&type=${chartType}`, {
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

    // Günlük grafik - MAVİ SÜTUN GRAFİK
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
                borderSkipped: false,
                barThickness: 25, // Sabit kalınlık - orta boyut
                maxBarThickness: 25, // Maksimum kalınlık sınırı
                categoryPercentage: 0.8, // Kategori genişliği (sütun + boşluk)
                barPercentage: 0.6 // Sütunun kategori içindeki oranı
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
                        color: '#f1f1f1'
                    },
                    ticks: {
                        color: '#6c757d'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6c757d'
                    }
                }
            }
        }
    });

    // Saatlik grafik - SARI NOKTA GRAFİK (küçük noktalar, dolu)
    hourlyChart = new Chart(hourlyCtx, {
        type: 'line',
        data: {
            labels: hourlyData?.labels || [],
            datasets: [{
                label: 'Saatlik Servis',
                data: hourlyData?.data || [],
                borderColor: '#ffc107', // Sarı çizgi
                backgroundColor: 'rgba(255,193,7,0.2)', // Hafif sarı arkaplan
                borderWidth: 3,
                fill: true, // İçi dolu olsun
                tension: 0.4,
                pointBackgroundColor: '#ffc107', // Sarı noktalar
                pointBorderColor: '#ffc107',
                pointBorderWidth: 2,
                pointRadius: 5, // Küçük noktalar
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
                        color: '#f1f1f1'
                    },
                    ticks: {
                        color: '#6c757d'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6c757d'
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

// Sayaçları  güncelleme
function updateCounter(elementId, targetValue) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    // Animasyon kaldırıldı, direkt değer atanıyor
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
                window.location.href = buildServiceUrl('total');
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

        // Periyodik güncelleme (opsiyonel - her 5 dakikada bir)
        setInterval(() => {
            loadStats();
        }, 5 * 60 * 1000); // 5 dakika
    </script>
@endsection