@extends('frontend.secure.user_master')
@section('user')

{{-- CSRF Token Meta Tag --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>



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
                     <a href="#" onclick="window.location.href = buildCashUrl('total')" 
                       class="stat-card teal" id="totalCashCard">
                        <i class="fas fa-lira-sign stat-icon"></i>
                        <div class="stat-value" id="totalCash">0,00 TL</div>
                        <div class="stat-label">Kasa</div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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
                    
                    // Sayaçları güncelle
                    animateCounter('totalServices', data.total_services);
                    animateCounter('totalCustomers', data.total_customers);
                    animateCounter('totalPersonnel', data.total_personnel);
                    animateCounter('todayServices', data.today_services);
                    animateCounter('yesterdayServices', data.yesterday_services);
                    animateCounter('previousServices', data.previous_services);
                    
                    // Kasa bilgisini güncelle
                    document.getElementById('totalCash').textContent = 
                        new Intl.NumberFormat('tr-TR', {
                            style: 'currency',
                            currency: 'TRY',
                            minimumFractionDigits: 2
                        }).format(data.total_cash.net);
                        
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

            // Günlük grafik
            dailyChart = new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: dailyData?.labels || [],
                    datasets: [{
                        label: 'Günlük Servis',
                        data: dailyData?.data || [],
                        borderColor: 'rgba(255,165,0,0.7)',
                        backgroundColor: 'rgba(255,165,0,0.2)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(255,165,0,1)',
                        pointBorderColor: 'rgba(255,165,0,1)',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
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
                    },
                    elements: {
                        point: {
                            hoverBackgroundColor: 'rgba(255,165,0,1)'
                        }
                    }
                }
            });

            // Saatlik grafik
            hourlyChart = new Chart(hourlyCtx, {
                type: 'bar',
                data: {
                    labels: hourlyData?.labels || [],
                    datasets: [{
                        label: 'Saatlik Servis',
                        data: hourlyData?.data || [],
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

        // Sayaçları animasyonlu güncelleme
        function animateCounter(elementId, targetValue, duration = 1000) {
            const element = document.getElementById(elementId);
            if (!element) return;
            
            const start = parseInt(element.textContent.replace(/[^\d]/g, '')) || 0;
            const increment = (targetValue - start) / (duration / 16);
            let current = start;

            const timer = setInterval(() => {
                current += increment;
                if ((increment > 0 && current >= targetValue) || (increment < 0 && current <= targetValue)) {
                    current = targetValue;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current);
            }, 16);
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