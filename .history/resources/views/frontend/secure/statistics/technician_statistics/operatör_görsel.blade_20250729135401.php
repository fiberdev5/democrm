@extends('frontend.secure.user_master')
@section('user')

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
    }
    
    .stats-card .icon {
        font-size: 3rem;
        opacity: 0.8;
        margin-bottom: 15px;
    }
    
    .stats-card h3 {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .stats-card p {
        opacity: 0.9;
        margin-bottom: 0;
    }
    
    .chart-container {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        margin-bottom: 25px;
    }
    
    .chart-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 20px;
        text-align: center;
    }
    
    .performance-indicator {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px;
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        border-radius: 10px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }
    
    .performance-indicator:hover {
        background: linear-gradient(45deg, #e9ecef, #dee2e6);
        transform: translateX(5px);
    }
    
    .operator-name {
        font-weight: 600;
        color: #2c3e50;
    }
    
    .service-count {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: 600;
    }
    
    .progress-bar-custom {
        height: 8px;
        border-radius: 10px;
        background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1);
        position: relative;
        overflow: hidden;
    }
    
    .progress-bar-custom::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    
    .filter-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 25px;
        color: white;
    }
    
    .date-buttons .btn {
        margin: 3px;
        border-radius: 20px;
        padding: 8px 16px;
        transition: all 0.3s ease;
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
    }
    
    .date-buttons .btn:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        color: white;
    }
    
    .table-modern {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }
    
    .table-modern thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .table-modern tbody tr:hover {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        transform: scale(1.01);
        transition: all 0.3s ease;
    }
    
    .btn-action {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border: none;
        border-radius: 20px;
        padding: 8px 16px;
        color: white;
        transition: all 0.3s ease;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
        color: white;
    }
    
    .metric-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
        margin: 2px;
    }
    
    .badge-high { background: linear-gradient(135deg, #ff6b6b, #ee5a24); color: white; }
    .badge-medium { background: linear-gradient(135deg, #feca57, #ff9ff3); color: white; }
    .badge-low { background: linear-gradient(135deg, #48dbfb, #0abde3); color: white; }
    
    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        margin-right: 15px;
    }
    
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        border-radius: 15px;
    }
    
    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<div class="page-content servis-istatistik" id="operatorStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
        <!-- Özet İstatistikler -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 id="totalOperators">0</h3>
                    <p>Toplam Operatör</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <div class="icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h3 id="totalServices">0</h3>
                    <p>Toplam Servis</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);">
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 id="avgServices">0</h3>
                    <p>Ortalama Servis/Operatör</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                    <div class="icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3 id="topOperator">-</h3>
                    <p>En Aktif Operatör</p>
                </div>
            </div>
        </div>

        <!-- Filtre Bölümü -->
        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5><i class="fas fa-filter me-2"></i>Tarih Aralığı Seçin</h5>
                    <input type="text" id="daterange" class="form-control tarih-araligi" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white;">
                </div>
                <div class="col-md-6">
                    <div class="date-buttons text-end">
                        <button class="btn" id="today">Bugün</button>
                        <button class="btn" id="yesterday">Dün</button>
                        <button class="btn" id="lastWeek">Son 7 Gün</button>
                        <button class="btn" id="lastMonth">Son 1 Ay</button>
                        <button class="btn" id="lastYear">Son 1 Yıl</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafikler -->
        <div class="row">
            <div class="col-md-8">
                <div class="chart-container" style="position: relative;">
                    <div id="chartLoading" class="loading-overlay" style="display: none;">
                        <div class="spinner"></div>
                    </div>
                    <div class="chart-title">
                        <i class="fas fa-chart-bar me-2"></i>Operatör Performans Karşılaştırması
                    </div>
                    <canvas id="operatorChart" height="100"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-container">
                    <div class="chart-title">
                        <i class="fas fa-chart-pie me-2"></i>Performans Dağılımı
                    </div>
                    <canvas id="pieChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Performans İndikatörleri -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="chart-container">
                    <div class="chart-title">
                        <i class="fas fa-tachometer-alt me-2"></i>Top 5 Operatör Performansı
                    </div>
                    <div id="performanceList">
                        <!-- Dinamik olarak doldurulacak -->
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row pageDetail">
            <div class="col-12">
                <div class="table-modern">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px;">
                        <h4 class="mb-0 sayfaBaslik"><i class="fas fa-table me-2"></i>Detaylı Operatör İstatistikleri</h4>
                    </div>
                    <div class="card-body p-0">
                        <table id="datatableOperatorStats" class="table table-hover mb-0" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead class="title">
                                <tr>
                                    <th data-priority="1"><i class="fas fa-user me-2"></i>Operatör Adı</th>
                                    <th data-priority="2"><i class="fas fa-clipboard-list me-2"></i>Toplam Servis Kaydı</th>
                                    <th><i class="fas fa-percentage me-2"></i>Performans</th>
                                    <th style="width: 130px;"><i class="fas fa-cogs me-2"></i>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Örnek veri - gerçek projede backend'den gelecek
const demoOperatorData = [
    {id: 1, name: "Ahmet Yılmaz", toplam: 185, performance: 95},
    {id: 2, name: "Mehmet Kaya", toplam: 167, performance: 87},
    {id: 3, name: "Fatma Demir", toplam: 154, performance: 82},
    {id: 4, name: "Ali Çelik", toplam: 142, performance: 78},
    {id: 5, name: "Ayşe Şahin", toplam: 138, performance: 75},
    {id: 6, name: "Mustafa Öz", toplam: 125, performance: 71},
    {id: 7, name: "Zeynep Acar", toplam: 118, performance: 68},
    {id: 8, name: "Hasan Doğan", toplam: 112, performance: 65},
    {id: 9, name: "Elif Yurt", toplam: 98, performance: 62},
    {id: 10, name: "Ömer Bal", toplam: 85, performance: 58}
];

let operatorChart, pieChart;

$(document).ready(function () {
    // Tarih aralığı seçenekleri
    var lastYear = moment().subtract(1, 'year');
    var lastMonth = moment().subtract(1, 'month');
    var lastWeek = moment().subtract(7, 'days');
    var yesterday = moment().subtract(1, 'days');
    var today = moment();

    // Tarih aralığı başlangıç değerleri
    var start_date = moment().startOf('month').format('DD-MM-YYYY');
    var end_date = moment().format('DD-MM-YYYY');

    // Date Range Picker başlat
    $('#daterange').daterangepicker({
        startDate: start_date,
        endDate: end_date,
        locale: {
            format: 'DD-MM-YYYY',
            separator: ' - ',
            applyLabel: 'Uygula',
            cancelLabel: 'İptal',
            weekLabel: 'H',
            daysOfWeek: ['Pz', 'Pzt', 'Sal', 'Çrş', 'Prş', 'Cm', 'Cmt'],
            monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
            firstDay: 1
        }
    }, function(start, end) {
        $('#daterange').html(start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY'));
        updateChartsAndStats();
        table.draw();
    });

    // Butonlara tıklanınca tarih aralığını değiştir
    $('#lastYear').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(lastYear);
        $('#daterange').data('daterangepicker').setEndDate(today);
        updateChartsAndStats();
        table.draw();
    });

    $('#lastMonth').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(lastMonth);
        $('#daterange').data('daterangepicker').setEndDate(today);
        updateChartsAndStats();
        table.draw();
    });

    $('#lastWeek').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(lastWeek);
        $('#daterange').data('daterangepicker').setEndDate(today);
        updateChartsAndStats();
        table.draw();
    });

    $('#yesterday').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(yesterday);
        $('#daterange').data('daterangepicker').setEndDate(yesterday);
        updateChartsAndStats();
        table.draw();
    });

    $('#today').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(today);
        $('#daterange').data('daterangepicker').setEndDate(today);
        updateChartsAndStats();
        table.draw();
    });

    var table = $('#datatableOperatorStats').DataTable({
        processing: true,
        serverSide: false, // Demo için false
        order: [[1, 'desc']],
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            },
            sEmptyTable: "Tabloda herhangi bir veri mevcut değil",
            sInfo: "Operatör Sayısı: _TOTAL_",
            sInfoEmpty: "Kayıt yok",
            sSearch: "Operatör Ara:",
            sZeroRecords: "Eşleşen kayıt bulunamadı",
            oPaginate: {
                sFirst: "İlk",
                sLast: "Son",
                sNext: '<i class="fas fa-angle-double-right"></i>',
                sPrevious: '<i class="fas fa-angle-double-left"></i>'
            }
        },
        // Demo veri için
        data: demoOperatorData,
        columns: [
            { 
                data: 'name', 
                orderable: true,
                render: function(data, type, row, meta) {
                    return `
                        <div class="d-flex align-items-center">
                            <div class="avatar">${data.charAt(0)}</div>
                            <div>
                                <div class="fw-bold">${data}</div>
                                <small class="text-muted">Operatör #${row.id}</small>
                            </div>
                        </div>
                    `;
                }
            },
            { 
                data: 'toplam', 
                orderable: true,
                render: function(data, type, row, meta) {
                    return `<div class="service-count d-inline-block">${data}</div>`;
                }
            },
            {
                data: 'performance',
                orderable: true,
                render: function(data, type, row, meta) {
                    const badgeClass = data >= 85 ? 'badge-high' : 
                                      data >= 70 ? 'badge-medium' : 'badge-low';
                    return `<span class="metric-badge ${badgeClass}">%${data}</span>`;
                }
            },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                    var from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    var url = "{{ url($tenant_id . '/servisler') }}" + "?operator_id=" + data + "&opeator_istatistik_tarih1=" + from_date + "&opeator_istatistik_tarih2=" + to_date;
                    
                    return `<a href="${url}" target="_blank" class="btn btn-action btn-sm">
                                <i class="fas fa-eye me-1"></i>Servisleri Göster
                            </a>`;
                }
            }
        ],
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        },
        dom: '<"top">rt<"bottom"ilp><"clear">',
        lengthMenu: [ [25, 50, 100, -1], [25, 50, 100, "Tümü"] ]
        
        /* Gerçek veri için:
        ajax: {
            url: "{{ route('operator.statistics', $tenant_id) }}",
            data: function(data) {
                data.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                data.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
        },
        */
    });

    // İlk yükleme
    initializeCharts();
    updateChartsAndStats();
    populatePerformanceList();
});

function initializeCharts() {
    // Bar Chart
    const ctx = document.getElementById('operatorChart').getContext('2d');
    operatorChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Servis Sayısı',
                data: [],
                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                borderColor: 'rgba(102, 126, 234, 1)',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    pieChart = new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Yüksek Performans (85%+)', 'Orta Performans (70-84%)', 'Düşük Performans (<70%)'],
            datasets: [{
                data: [0, 0, 0],
                backgroundColor: [
                    'rgba(255, 107, 107, 0.8)',
                    'rgba(254, 202, 87, 0.8)',
                    'rgba(72, 219, 251, 0.8)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function updateChartsAndStats() {
    // Loading göster
    $('#chartLoading').show();
    
    setTimeout(() => {
        // Demo veri ile grafikleri güncelle
        const topOperators = demoOperatorData.slice(0, 8);
        
        // Bar chart güncelle
        operatorChart.data.labels = topOperators.map(op => op.name.split(' ')[0]);
        operatorChart.data.datasets[0].data = topOperators.map(op => op.toplam);
        operatorChart.update();
        
        // Performans dağılımını hesapla
        const highPerf = demoOperatorData.filter(op => op.performance >= 85).length;
        const mediumPerf = demoOperatorData.filter(op => op.performance >= 70 && op.performance < 85).length;
        const lowPerf = demoOperatorData.filter(op => op.performance < 70).length;
        
        // Pie chart güncelle
        pieChart.data.datasets[0].data = [highPerf, mediumPerf, lowPerf];
        pieChart.update();
        
        // İstatistikleri güncelle
        const totalOperators = demoOperatorData.length;
        const totalServices = demoOperatorData.reduce((sum, op) => sum + op.toplam, 0);
        const avgServices = (totalServices / totalOperators).toFixed(1);
        const topOperator = demoOperatorData.reduce((prev, current) => 
            (prev.toplam > current.toplam) ? prev : current
        );
        
        // Animasyonlu sayı güncellemesi
        animateNumber('totalOperators', totalOperators);
        animateNumber('totalServices', totalServices);
        animateNumber('avgServices', parseFloat(avgServices), true);
        
        document.getElementById('topOperator').textContent = topOperator.name.split(' ')[0] + ' ' + 
            topOperator.name.split(' ')[1].charAt(0) + '.';
        
        // Loading gizle
        $('#chartLoading').hide();
    }, 500);
}

function populatePerformanceList() {
    const performanceList = document.getElementById('performanceList');
    const sortedData = [...demoOperatorData].sort((a, b) => b.toplam - a.toplam).slice(0, 5);
    
    performanceList.innerHTML = '';
    
    sortedData.forEach((operator, index) => {
        const progressWidth = (operator.toplam / sortedData[0].toplam) * 100;
        const badgeClass = operator.performance >= 85 ? 'badge-high' : 
                          operator.performance >= 70 ? 'badge-medium' : 'badge-low';
        
        const item = document.createElement('div');
        item.className = 'performance-indicator';
        item.innerHTML = `
            <div>
                <div class="operator-name">${index + 1}. ${operator.name}</div>
                <div class="progress-bar-custom mt-2" style="width: ${progressWidth}%"></div>
            </div>
            <div class="d-flex align-items-center">
                <span class="metric-badge ${badgeClass}">%${operator.performance}</span>
                <div class="service-count ms-2">${operator.toplam}</div>
            </div>
        `;
        performanceList.appendChild(item);
    });
}

function animateNumber(elementId, targetValue, isDecimal = false) {
    const element = document.getElementById(elementId);
    let currentValue = 0;
    const increment = targetValue / 50;
    
    const timer = setInterval(() => {
        currentValue += increment;
        if (currentValue >= targetValue) {
            currentValue = targetValue;
            clearInterval(timer);
        }
        
        if (isDecimal) {
            element.textContent = currentValue.toFixed(1);
        } else {
            element.textContent = Math.floor(currentValue).toLocaleString();
        }
    }, 30);
}
</script>

@endsection