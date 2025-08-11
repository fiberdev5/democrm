@extends('frontend.secure.user_master')
@section('user')

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    body {
        background-color: #f0f2f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .page-content {
        padding: 30px 0;
    }

    .filter-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 25px;
        color: white;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }
    
    .filter-section .form-control.tarih-araligi {
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        padding: 10px 15px;
        border-radius: 10px;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
    }

    .filter-section .form-control.tarih-araligi::placeholder {
        color: rgba(255,255,255,0.7);
    }
    
    .date-buttons .btn {
        margin: 3px;
        border-radius: 20px;
        padding: 8px 16px;
        transition: all 0.3s ease;
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        font-weight: 500;
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

    .table-modern th {
        padding: 15px 20px;
        font-weight: 600;
        font-size: 1rem;
    }
    
    .table-modern tbody tr:hover {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        transform: scale(1.005);
        transition: all 0.3s ease;
    }
    
    .btn-action {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border: none;
        border-radius: 20px;
        padding: 8px 16px;
        color: white;
        transition: all 0.3s ease;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 5px;
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
        text-align: center;
        min-width: 60px;
    }
    
    .badge-high { background: linear-gradient(135deg, #28a745, #218838); color: white; }
    .badge-medium { background: linear-gradient(135deg, #ffc107, #e0a800); color: white; }
    .badge-low { background: linear-gradient(135deg, #dc3545, #c82333); color: white; }
    
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
        flex-shrink: 0;
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
        transition: opacity 0.3s ease;
        opacity: 0;
        pointer-events: none;
    }

    .loading-overlay.active {
        opacity: 1;
        pointer-events: all;
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

    div.dataTables_wrapper div.dataTables_filter {
        text-align: right;
        margin-bottom: 15px;
    }

    div.dataTables_wrapper div.dataTables_length {
        text-align: left;
        margin-bottom: 15px;
    }

    div.dataTables_wrapper div.dataTables_info {
        padding-top: 0.85em;
        white-space: nowrap;
        margin-top: 15px;
    }

    div.dataTables_wrapper div.dataTables_paginate {
        margin: 0;
        white-space: nowrap;
        text-align: right;
        margin-top: 15px;
    }
    
    .dataTables_filter input {
        border: 1px solid #ced4da;
        border-radius: 0.5rem;
        padding: 0.375rem 0.75rem;
        width: 250px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .dataTables_filter input:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .pagination .page-item .page-link {
        border-radius: 50px !important;
        margin: 0 3px;
        min-width: 32px;
        text-align: center;
        color: #6c757d;
        background-color: #fff;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: white;
        box-shadow: 0 2px 5px rgba(102, 126, 234, 0.4);
    }

    .pagination .page-item .page-link:hover {
        background-color: #e9ecef;
        border-color: #ced4da;
        color: #495057;
    }
</style>

<div class="page-content servis-istatistik" id="operatorStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5><i class="fas fa-filter me-2"></i>Tarih Aralığı Seçin</h5>
                    <input type="text" id="daterange" class="form-control tarih-araligi" readonly>
                </div>
                <div class="col-md-6">
                    <div class="date-buttons text-end mt-3 mt-md-0">
                        <button class="btn" id="today">Bugün</button>
                        <button class="btn" id="yesterday">Dün</button>
                        <button class="btn" id="lastWeek">Son 7 Gün</button>
                        <button class="btn" id="lastMonth">Son 1 Ay</button>
                        <button class="btn" id="lastYear">Son 1 Yıl</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row pageDetail">
            <div class="col-12">
                <div class="card table-modern">
                    <div class="card-header sayfaBaslik" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-top-left-radius: 15px; border-top-right-radius: 15px; padding: 15px 20px; font-size: 1.2rem; font-weight: 600;">
                        Operatör Performans Detayları
                    </div>
                    <div class="card-body position-relative">
                        <div id="table-loading-overlay" class="loading-overlay">
                            <div class="spinner"></div>
                        </div>
                        <table id="datatableOperatorStats" class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th data-priority="1">Operatör Adı</th>
                                    <th data-priority="2">Toplam Servis Kaydı</th>
                                    <th data-priority="3">Performans (%)</th>
                                    <th style="width: 130px;">İşlemler</th>
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
$(document).ready(function () {
    const tenantId = "{{ $tenant_id }}";

    // Tarih aralığı seçenekleri
    var lastYear = moment().subtract(1, 'year');
    var lastMonth = moment().subtract(1, 'month');
    var lastWeek = moment().subtract(7, 'days');
    var yesterday = moment().subtract(1, 'days');
    var today = moment();

    // Tarih aralığı başlangıç değerleri (Varsayılan olarak bu ayı seçer)
    var start_date_initial = moment().startOf('month');
    var end_date_initial = moment();

    // Date Range Picker başlat
    $('#daterange').daterangepicker({
        startDate: start_date_initial,
        endDate: end_date_initial,
        opens: 'left',
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
        $('#daterange').val(start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY'));
        table.draw(); // Tarih değiştiğinde tabloyu yeniden çiz
    });

    // Başlangıçta daterange inputunu ayarla
    $('#daterange').val(start_date_initial.format('DD-MM-YYYY') + ' - ' + end_date_initial.format('DD-MM-YYYY'));

    // Butonlara tıklanınca tarih aralığını değiştir ve tabloyu yenile
    $('#lastYear').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(lastYear);
        $('#daterange').data('daterangepicker').setEndDate(today);
        $('#daterange').val(lastYear.format('DD-MM-YYYY') + ' - ' + today.format('DD-MM-YYYY'));
        table.draw();
    });

    $('#lastMonth').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(lastMonth);
        $('#daterange').data('daterangepicker').setEndDate(today);
        $('#daterange').val(lastMonth.format('DD-MM-YYYY') + ' - ' + today.format('DD-MM-YYYY'));
        table.draw();
    });

    $('#lastWeek').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(lastWeek);
        $('#daterange').data('daterangepicker').setEndDate(today);
        $('#daterange').val(lastWeek.format('DD-MM-YYYY') + ' - ' + today.format('DD-MM-YYYY'));
        table.draw();
    });

    $('#yesterday').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(yesterday);
        $('#daterange').data('daterangepicker').setEndDate(yesterday);
        $('#daterange').val(yesterday.format('DD-MM-YYYY') + ' - ' + yesterday.format('DD-MM-YYYY'));
        table.draw();
    });

    $('#today').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(today);
        $('#daterange').data('daterangepicker').setEndDate(today);
        $('#daterange').val(today.format('DD-MM-YYYY') + ' - ' + today.format('DD-MM-YYYY'));
        table.draw();
    });

    // DataTables başlatma
    var table = $('#datatableOperatorStats').DataTable({
        processing: true,
        serverSide: true,
        order: [[1, 'desc']], // Toplam servis kaydına göre azalan sırala
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            },
            sEmptyTable: "Tabloda herhangi bir veri mevcut değil",
            sInfo: "Toplam _TOTAL_ operatörden _START_ - _END_ arası gösteriliyor",
            sInfoEmpty: "Kayıt yok",
            sSearch: "Operatör Ara:",
            sZeroRecords: "Eşleşen kayıt bulunamadı",
            oPaginate: {
                sFirst: "İlk",
                sLast: "Son",
                sNext: '<i class="fas fa-angle-double-right"></i>',
                sPrevious: '<i class="fas fa-angle-double-left"></i>'
            },
            sLengthMenu: "Sayfada _MENU_ kayıt göster",
        },
        ajax: {
            url: "{{ route('operator.statistics', $tenant_id) }}",
            data: function(d) {
                d.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                d.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
            },
            beforeSend: function() {
                $('#table-loading-overlay').addClass('active');
            },
            complete: function() {
                $('#table-loading-overlay').removeClass('active');
            }
        },
        columns: [
            { 
                data: 'name', 
                render: function(data, type, row) {
                    return `
                        <div class="d-flex align-items-center">
                            <div class="avatar">${data.charAt(0).toUpperCase()}</div>
                            <div>
                                <div class="fw-bold operator-name">${data}</div>
                                <small class="text-muted">Operatör #${row.id}</small>
                            </div>
                        </div>`;
                }
            },
            { 
                data: 'total_services', // Backend'den gelen veri alanı
                className: 'text-center',
                render: function(data) {
                    return `<div class="service-count">${data}</div>`;
                }
            },
            {
                data: 'performance_percentage', // Backend'den gelen veri alanı
                className: 'text-center',
                render: function(data) {
                    // Verinin null veya tanımsız olma durumunu kontrol edin
                    if (data === null || typeof data === 'undefined') {
                        return '<span class="metric-badge badge-low">N/A</span>'; // Veri yoksa 'N/A' göster
                    }
                    const badgeClass = data >= 85 ? 'badge-high' : 
                                       data >= 70 ? 'badge-medium' : 'badge-low';
                    return `<span class="metric-badge ${badgeClass}">%${data.toFixed(0)}</span>`;
                }
            },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    var from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    var url = `{{ url('${tenantId}/servisler') }}?operator_id=${data}&operator_istatistik_tarih1=${from_date}&operator_istatistik_tarih2=${to_date}`;
                    
                    return `<a href="${url}" target="_blank" class="btn btn-action btn-sm">
                                <i class="fas fa-eye me-1"></i>Servisleri Gör
                            </a>`;
                }
            }
        ],
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        },
        dom: '<"top"lf>rt<"bottom"ip><"clear">',
        lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "Tümü"] ]
    });

    // Sayfa yüklendiğinde ilk verileri çek
    table.draw();
});
</script>

@endsection