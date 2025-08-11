@extends('frontend.secure.user_master')
@section('user')

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    /* Genel sayfa içeriği ve container stilleri */
    .page-content {
        padding: 30px;
        background-color: #f4f6f9; /* Hafif bir arka plan rengi */
    }

    .container-fluid {
        padding: 0;
    }

    /* Kart ve Başlık Stilleri */
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden; /* Köşelerin yuvarlak görünmesi için */
        margin-bottom: 30px;
    }

    .card-header.sayfaBaslik {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /* Mor-mavi degrade */
        color: white;
        padding: 20px 25px;
        font-size: 1.5rem;
        font-weight: 600;
        border-bottom: none; /* Varsayılan border'ı kaldır */
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-body {
        padding: 25px;
        background-color: #fff;
    }

    /* Filtreleme ve Tarih Seçimi */
    .searchWrap {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-bottom: 20px;
        gap: 10px; /* Butonlar arasına boşluk */
    }

    .btn-group .filtrele {
        background-color: #4CAF50; /* Yeşil buton */
        border-color: #4CAF50;
        color: white;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-group .filtrele:hover {
        background-color: #45a049;
        border-color: #45a049;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .dropdown-menu {
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        padding: 15px;
        min-width: 300px;
    }

    .dropdown-menu .item label {
        font-weight: 600;
        color: #555;
        margin-bottom: 5px;
    }

    .tarih-araligi {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 8px 12px;
        width: 100%;
        margin-bottom: 10px;
        font-size: 0.95rem;
        color: #333;
    }

    .tarihAraligi .btn {
        margin: 4px;
        border-radius: 20px;
        padding: 6px 12px;
        font-size: 0.85rem;
        background-color: #6c757d; /* Gri butonlar */
        border-color: #6c757d;
        color: white;
        transition: all 0.2s ease;
    }

    .tarihAraligi .btn:hover {
        background-color: #5a6268;
        border-color: #545b62;
        transform: translateY(-1px);
    }

    /* DataTables Özelleştirmeleri */
    .table-bordered {
        border: none !important; /* DataTables'ın kendi border'ını kaldır */
    }

    .table {
        margin-bottom: 0; /* Tablo altında boşluk olmaması için */
    }

    .table-modern thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /* Başlık degrade */
        color: white;
    }

    .table-modern thead th {
        padding: 15px 20px;
        border-bottom: none;
        font-weight: 600;
        font-size: 0.95rem;
        text-align: left;
    }

    .table-modern tbody tr {
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .table-modern tbody tr:nth-child(even) {
        background-color: #f9f9f9; /* Zebra efekti */
    }

    .table-modern tbody tr:hover {
        background-color: #e9f5ff; /* Hover rengi */
        transform: scale(1.005);
    }

    .table-modern tbody td {
        padding: 12px 20px;
        vertical-align: middle;
        border-top: 1px solid #eee;
        color: #333;
    }

    /* İşlemler butonu */
    .btn-primary.btn-sm {
        background: linear-gradient(45deg, #4facfe 0%, #00f2fe 100%); /* Degrade mavi */
        border: none;
        border-radius: 20px;
        padding: 7px 15px;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        color: white;
    }

    .btn-primary.btn-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 242, 254, 0.3);
        color: white;
    }

    /* DataTables Pagination ve Bilgi */
    .dataTables_wrapper .dataTables_paginate .pagination-rounded .page-item.active .page-link {
        background-color: #667eea;
        border-color: #667eea;
    }

    .dataTables_wrapper .dataTables_paginate .pagination-rounded .page-item .page-link {
        border-radius: 50% !important;
        margin: 0 3px;
    }

    .dataTables_info {
        font-size: 0.9rem;
        color: #666;
        padding-top: 10px;
    }

    .dataTables_filter label {
        color: #333;
        font-weight: 600;
    }

    .dataTables_filter input {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 7px 12px;
        margin-left: 8px;
        width: 200px;
        transition: all 0.3s ease;
    }

    .dataTables_filter input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* Loading overlay (Datatables yüklenirken) */
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
        
        <div class="row pageDetail">
            <div class="col-12">
                <div class="card">
                    <div class="card-header sayfaBaslik">
                        <i class="fas fa-users-cog"></i> Operatör İstatistikleri
                    </div>
                    <div class="card-body">
                        <div class="searchWrap">
                            <div class="btn-group mb-2">
                                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Filtrele <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <div class="item">
                                        <div class="row g-2 align-items-center">
                                            <label class="col-sm-4">Tarih Aralığı:</label>
                                            <div class="col-sm-8">
                                                <input id="daterange" class="tarih-araligi form-control" />
                                                <div class="tarihAraligi mt-2">
                                                    <button id="lastYear" class="btn btn-sm btn-secondary">Son 1 Yıl</button>
                                                    <button id="lastMonth" class="btn btn-sm btn-secondary">Son 1 Ay</button>
                                                    <button id="lastWeek" class="btn btn-sm btn-secondary">Son 7 Gün</button>
                                                    <button id="yesterday" class="btn btn-sm btn-secondary">Dün</button>
                                                    <button id="today" class="btn btn-sm btn-secondary">Bugün</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-modern" style="position: relative;">
                            <div id="tableLoading" class="loading-overlay" style="display: none;">
                                <div class="spinner"></div>
                            </div>
                            <table id="datatableOperatorStats" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead class="title">
                                    <tr>
                                        <th data-priority="1"><i class="fas fa-user me-2"></i>Operatör Adı</th>
                                        <th data-priority="2"><i class="fas fa-clipboard-list me-2"></i>Toplam Servis Kaydı</th>
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
    var start_date_initial = moment().startOf('month').format('DD-MM-YYYY');
    var end_date_initial = moment().format('DD-MM-YYYY');

    // Date Range Picker başlat
    $('#daterange').daterangepicker({
        startDate: start_date_initial,
        endDate: end_date_initial,
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
        // Tarih seçildiğinde DataTables'ı yeniden yükle
        table.draw();
    });

    // Butonlara tıklanınca tarih aralığını değiştir ve tabloyu yenile
    function setDateRangeAndDrawTable(start, end) {
        $('#daterange').data('daterangepicker').setStartDate(start);
        $('#daterange').data('daterangepicker').setEndDate(end);
        table.draw();
    }

    $('#lastYear').on('click', function() { setDateRangeAndDrawTable(lastYear, today); });
    $('#lastMonth').on('click', function() { setDateRangeAndDrawTable(lastMonth, today); });
    $('#lastWeek').on('click', function() { setDateRangeAndDrawTable(lastWeek, today); });
    $('#yesterday').on('click', function() { setDateRangeAndDrawTable(yesterday, yesterday); });
    $('#today').on('click', function() { setDateRangeAndDrawTable(today, today); });

    var table = $('#datatableOperatorStats').DataTable({
        processing: true,
        serverSide: true,
        order: [[1, 'desc']], // Toplam Servis Kaydı sütununa göre azalan sırada sırala
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
                sNext: '<i class="fas fa-angle-right"></i>', // Font Awesome ikonu
                sPrevious: '<i class="fas fa-angle-left"></i>' // Font Awesome ikonu
            }
        },
        ajax: {
            url: "{{ route('operator.statistics', $tenant_id) }}",
            data: function(d) {
                // Tarih aralığını ajax'a ekle
                d.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                d.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
            },
            beforeSend: function() {
                $('#tableLoading').show(); // Yükleniyor spinner'ını göster
            },
            complete: function() {
                $('#tableLoading').hide(); // Yükleniyor spinner'ını gizle
            }
        },
        columns: [
            { 
                data: 'name', 
                orderable: true,
                render: function(data, type, row) {
                    // Avatar ve operatör adını birleştirme
                    var initials = data.split(' ').map(n => n[0]).join('').toUpperCase();
                    return '<div style="display: flex; align-items: center;"><div class="avatar">' + initials + '</div><span>' + data + '</span></div>';
                }
            },
            { data: 'toplam', orderable: true },
            {
                data: 'id', // operatörün ID'si 
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                    // Tarih aralığını al
                    var from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');

                    //servisler sayfasına yönlendirme
                    var url = "{{ url($tenant_id . '/servisler') }}" + "?operator_id=" + data + "&opeator_istatistik_tarih1=" + from_date + "&opeator_istatistik_tarih2=" + to_date;
                    
                    return '<a href="' + url + '" target="_blank" class="btn btn-primary btn-sm">Servisleri Göster <i class="fas fa-arrow-right ms-1"></i></a>';
                }
            }
        ],
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        },
        dom: '<"top"lf<"clear">>rt<"bottom"ip><"clear">', // Arama kutusu ve uzunluk seçeneğini üste taşı
        lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "Tümü"] ] // Daha okunabilir uzunluk menüsü
    });
});
</script>
@endsection