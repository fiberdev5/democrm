@extends('frontend.secure.user_master')
@section('user')

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    /* İkinci kodunuzdaki tabloya özel stil kurallarını buraya kopyaladım */
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
    
</style>

<div class="page-content servis-istatistik" id="operatorStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
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
        $('#daterange').val(start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY'));
        table.draw(); // Tarih değiştiğinde tabloyu yenile
    });

    // Butonlara tıklanınca tarih aralığını değiştir
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
        serverSide: false, // Demo için false olarak ayarlandı. Canlıda true olmalı.
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
        dom: '<"top"f<"clear">>rt<"bottom"ilp><"clear">', // Filtreleme kutusunu top'a taşıdım
        lengthMenu: [ [25, 50, 100, -1], [25, 50, 100, "Tümü"] ]
        
       
        ajax: {
            url: "{{ route('operator.statistics', $tenant_id) }}",
            data: function(d) {
                d.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                d.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
        },
        
    });
});
</script>
@endsection