@extends('frontend.secure.user_master')
@section('user')
<style>
/* Modern Tabla Temel Stili */
.table-modern {
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border: 1px solid #f0f0f0;
}

/* Tablo Başlığı - Daha sade ve hoş */
.table-modern thead {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%); /* Sade gri tonları */
    color: white;
}

.table-modern thead th {
    font-weight: 600;
    letter-spacing: 0.5px;
    padding: 16px 20px;
    border: none;
    font-size: 14px;
}

/* Tablo Satırlarının Üzerine Gelindiğinde Animasyon - Daha sade */
.table-modern tbody tr {
    border-bottom: 1px solid #f8f9fa;
    transition: all 0.2s ease;
}

.table-modern tbody tr:hover {
    background: #f8f9fa !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.table-modern tbody td {
    padding: 16px 20px;
    vertical-align: middle;
    border: none;
}

/* Aksiyon Butonları - Daha minimal */
.btn-action {
    background: #6c757d;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    color: white;
    transition: all 0.2s ease;
    font-size: 13px;
    font-weight: 500;
}

.btn-action:hover {
    background: #5a6268;
    transform: translateY(-1px);
    box-shadow: 0 3px 12px rgba(108, 117, 125, 0.3);
    color: white;
}

/* Avatar Stili - Daha sade renk */
.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    margin-right: 12px;
    font-size: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Badge stili - Daha hoş renk */
.badge.bg-primary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 13px;
}

/* Kart Başlığı */
.card-header {
    background: #ffffff;
    padding: 20px 24px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    font-weight: 600;
    color: #2c3e50;
    font-size: 18px;
}

/* Kart içeriği */
.card-body {
    padding: 0;
}

/* Filtre butonları */
.btn.btn-dark.btn-sm.dropdown-toggle.filtrele {
    background: #6c757d;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn.btn-dark.btn-sm.dropdown-toggle.filtrele:hover {
    background: #5a6268;
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(108, 117, 125, 0.2);
}

/* Tarih aralığı butonları */
.tarihAraligi .btn {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    color: #6c757d;
    border-radius: 6px;
    padding: 6px 12px;
    margin-right: 6px;
    font-size: 12px;
    transition: all 0.2s ease;
}

.tarihAraligi .btn:hover {
    background: #6c757d;
    color: white;
    border-color: #6c757d;
    transform: translateY(-1px);
}

/* Datatable genel stil ayarları */
#datatableOperatorStats {
    background: none !important;
    color: #2c3e50 !important;
}

#datatableOperatorStats thead.title {
    color: white !important;
}

/* DataTable sayfalama */

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #f8f9fa !important;
    border-color: #6c757d !important;
    color: #495057 !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #6c757d !important;
    border-color: #6c757d !important;
    color: white !important;
}

/* DataTable bilgi metni */
.dataTables_info {
    color: #6c757d;
    font-size: 14px;
    font-weight: 500;
}

/* Arama kutusu */
.dataTables_filter input {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 8px 12px;
    background: #f8f9fa;
    transition: all 0.2s ease;
}

.dataTables_filter input:focus {
    border-color: #6c757d;
    box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.15);
    background: white;
}
</style>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<div class="page-content servis-istatistik" id="operatorStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
        <div class="row pageDetail">
            <div class="col-12">
                <div class="table-modern">
                    <div class="card-header">
                        Operatör İstatistikleri
                        <div class="searchWrap float-end">
                            <div class="btn-group mb-2">
                                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Filtrele <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <div class="item">
                                        <div class="row">
                                            <label class="col-sm-4">Tarih Aralığı:</label>
                                            <div class="col-sm-8">
                                                <input id="daterange" class="tarih-araligi" />
                                                <div class="tarihAraligi mt-2 mb-2">
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
                    </div>
                    <div class="card-body">
                        <table id="datatableOperatorStats" class="table table-hover mb-0">
                            <thead class="title">
                                <tr>
                                    <th><i class="fas fa-user me-2"></i>Operatör Adı</th>
                                    <th><i class="fas fa-clipboard-list me-2"></i>Toplam Servis Kaydı</th>
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
    // Tarih aralığı seçenekleri
    var lastYear = moment().subtract(1, 'year');
    var lastMonth = moment().subtract(1, 'month');
    var lastWeek = moment().subtract(7, 'days');
    var yesterday = moment().subtract(1, 'days');
    var today = moment();

    // Butonlara tıklanınca tarih aralığını değiştir ve tabloyu yenile
    $('#lastYear').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(lastYear);
        $('#daterange').data('daterangepicker').setEndDate(today);
        table.draw();
    });

    $('#lastMonth').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(lastMonth);
        $('#daterange').data('daterangepicker').setEndDate(today);
        table.draw();
    });

    $('#lastWeek').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(lastWeek);
        $('#daterange').data('daterangepicker').setEndDate(today);
        table.draw();
    });

    $('#yesterday').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(yesterday);
        $('#daterange').data('daterangepicker').setEndDate(yesterday);
        table.draw();
    });

    $('#today').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(today);
        $('#daterange').data('daterangepicker').setEndDate(today);
        table.draw();
    });

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
        table.draw();
    });


    var table = $('#datatableOperatorStats').DataTable({
        processing: true,
        serverSide: true,
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
        ajax: {
            url: "{{ route('operator.statistics', $tenant_id) }}",
            data: function(data) {
                // Tarih aralığını ajax'a ekle
                data.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                data.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
        },
columns: [
    { 
        data: 'name', 
        render: function(data, type, row) {
            return `
                <div class="d-flex align-items-center">
                    <div class="avatar">${data.charAt(0)}</div>
                    <div>
                        <div class="fw-bold">${data}</div>
                        <small class="text-muted">Operatör #${row.id}</small>
                    </div>
                </div>`;
        }
    },
    { 
        data: 'toplam', 
        render: function(data) {
            return `<div class="badge bg-primary">${data}</div>`;
        }
    },
    {
        data: 'id',
        render: function(data, type, row) {
            var from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
            var to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
            var url = "{{ url($tenant_id . '/servisler') }}" + "?operator_id=" + data + "&opeator_istatistik_tarih1=" + from_date + "&opeator_istatistik_tarih2=" + to_date;
            
            return `<a href="${url}" target="_blank" class="btn btn-action btn-sm">
                        <i class="fas fa-eye me-1"></i>Servisleri Gör
                    </a>`;
        }
    }
],
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        },
        dom: '<"top">rt<"bottom"ilp><"clear">',
        lengthMenu: [ [25, 50, 100, -1], [25, 50, 100, "Tümü"] ]
    });
});
</script>
@endsection