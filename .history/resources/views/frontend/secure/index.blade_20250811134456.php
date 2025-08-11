@extends('frontend.secure.user_master')
@section('user')

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
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
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
        }

        .service-item:hover {
            transform: translateY(-2px);
            border-color: #fff;
            background: #fff;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
        }

        .service-count {
            font-size: 2.2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }

        .service-item.today .service-count { color: #28a745; }
        .service-item.yesterday .service-count { color: #fd7e14; }
        .service-item.previous .service-count { color: #6c757d; }

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
    background-color: #d6bcfa; /* açık mor arka plan */
    color: #495057; /* koyu mor */
    outline: none;
}

/* Aktif buton */
.filter-btn.active {
    background-color: #495057; /* koyu mor arka plan */
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
    </style>

        <h1 class="page-title">Dashboard Ana Sayfa</h1>
        
        <div class="dashboard-container">
            <!-- Üst İstatistik Kartları -->
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card blue">
                        <i class="fas fa-tools stat-icon"></i>
                        <div class="stat-value" id="totalServices">1</div>
                        <div class="stat-label">Toplam Servis Sayısı</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card green">
                        <i class="fas fa-users stat-icon"></i>
                        <div class="stat-value" id="totalCustomers">23</div>
                        <div class="stat-label">Müşteri Sayısı</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card red">
                        <i class="fas fa-user-tie stat-icon"></i>
                        <div class="stat-value" id="totalPersonnel">12</div>
                        <div class="stat-label">Personel Sayısı</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card teal">
                        <i class="fas fa-lira-sign stat-icon"></i>
                        <div class="stat-value" id="totalCash">0,00 TL</div>
                        <div class="stat-label">Kasa</div>
                    </div>
                </div>
            </div>

            <!-- Servis Özeti -->
            <div class="service-summary">
                <h5><i class="fas fa-chart-bar"></i> Servis Sayıları</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="service-item today">
                            <div class="service-count" id="todayServices">1</div>
                            <div>BUGÜN Alınan Servis Sayısı</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="service-item yesterday">
                            <div class="service-count" id="yesterdayServices">0</div>
                            <div>DÜN Alınan Servis Sayısı</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="service-item previous">
                            <div class="service-count" id="previousServices">0</div>
                            <div>ÖNCEKİ GÜN Alınan Servis Sayısı</div>
                        </div>
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

        // Örnek veri - gerçek uygulamada Laravel'den AJAX ile gelecek
        const sampleData = {
            7: {
                daily: {
                    labels: ['10/12', '12/12', '16/12', '17/12', '18/12', '19/12', '24/12'],
                    data: [2, 2, 2, 5, 3, 2, 1]
                },
                hourly: {
                    labels: ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00'],
                    data: [1, 2, 3, 5, 4, 3, 2, 1]
                }
            },
            15: {
                daily: {
                    labels: ['01/12', '03/12', '05/12', '07/12', '09/12', '11/12', '13/12', '15/12', '17/12', '19/12', '21/12', '23/12', '25/12', '27/12', '29/12'],
                    data: [3, 1, 4, 2, 5, 3, 2, 6, 4, 1, 3, 2, 1, 4, 2]
                },
                hourly: {
                    labels: ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'],
                    data: [2, 3, 4, 6, 8, 5, 4, 3, 2, 1]
                }
            },
            30: {
                daily: {
                    labels: ['01/12', '03/12', '05/12', '07/12', '09/12', '11/12', '13/12', '15/12', '17/12', '19/12', '21/12', '23/12', '25/12', '27/12', '29/12', '31/12', '02/01', '04/01', '06/01', '08/01'],
                    data: [4, 2, 6, 3, 7, 4, 3, 8, 5, 2, 4, 3, 2, 5, 3, 6, 4, 2, 3, 1]
                },
                hourly: {
                    labels: ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'],
                    data: [3, 4, 5, 7, 9, 6, 5, 4, 3, 2, 1]
                }
            }
        };

        // Grafik başlatma
        function initCharts() {
            const dailyCtx = document.getElementById('dailyChart').getContext('2d');
            const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');

            // Günlük grafik
            dailyChart = new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: sampleData[7].daily.labels,
                    datasets: [{
                        label: 'Günlük Servis',
                        data: sampleData[7].daily.data,
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
                    labels: sampleData[7].hourly.labels,
                    datasets: [{
                        label: 'Saatlik Servis',
                        data: sampleData[7].hourly.data,
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
        function updateChart(chartType, period) {
            const loadingEl = document.querySelector(`.chart-container:has(#${chartType}Chart) .loading`);
            loadingEl.style.display = 'block';
            
            setTimeout(() => {
                if (chartType === 'daily') {
                    dailyChart.data.labels = sampleData[period].daily.labels;
                    dailyChart.data.datasets[0].data = sampleData[period].daily.data;
                    dailyChart.update('active');
                    currentDailyPeriod = period;
                } else {
                    hourlyChart.data.labels = sampleData[period].hourly.labels;
                    hourlyChart.data.datasets[0].data = sampleData[period].hourly.data;
                    hourlyChart.update('active');
                    currentHourlyPeriod = period;
                }
                
                loadingEl.style.display = 'none';
            }, 500);
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
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
        });

        // Gerçek uygulamada bu fonksiyon Laravel'den veri çekecek
        function loadDataFromServer(period, chartType) {
            // Laravel route example: /api/dashboard/chart-data
            /*
            fetch(`/api/dashboard/chart-data?period=${period}&type=${chartType}`)
                .then(response => response.json())
                .then(data => {
                    updateChart(chartType, period, data);
                });
            */
        }

        // Sayaçları animasyonlu güncelleme
        function animateCounter(elementId, targetValue, duration = 1000) {
            const element = document.getElementById(elementId);
            const start = parseInt(element.textContent) || 0;
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

        // Sayfa yüklendiğinde animasyonları başlat
        setTimeout(() => {
            animateCounter('totalServices', 86);
            animateCounter('totalCustomers', 23);
            animateCounter('totalPersonnel', 12);
            animateCounter('todayServices', 1);
            animateCounter('yesterdayServices', 0);
            animateCounter('previousServices', 0);
        }, 500);
    </script>

@endsection
