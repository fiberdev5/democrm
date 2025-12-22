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


        
 
    </div>
</div>

<script>


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
        
       
        ajax: {
            url: "{{ route('operator.statistics', $tenant_id) }}",
            data: function(data) {
                data.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                data.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
        },
        
    });

});

 





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