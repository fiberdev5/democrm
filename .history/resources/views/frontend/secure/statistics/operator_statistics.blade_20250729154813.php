@extends('frontend.secure.user_master')
@section('user')
<style>
/* Modern Tabla Temel Stili */
.table-modern {
    background: #ffffff; /* Beyaz arka plan */
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

/* Tablo Başlığı */
.table-modern thead {
    /* Mavi-yeşil tonlarında gradyan */
    background: linear-gradient(135deg, #4CAF50 0%, #2196F3 100%); /* Canlı yeşil ile parlak mavi geçiş */
    color: white;
}

/* Tablo Satırlarının Üzerine Gelindiğinde Animasyon */
.table-modern tbody tr:hover {
    /* Hafif mavi-yeşil tonunda gradyan */
    background: linear-gradient(135deg, rgba(33,150,243,0.1) 0%, rgba(76,175,80,0.1) 100%) !important; /* Mavi-yeşil geçişli şeffaf ton */
    transform: scale(1.01);
    transition: all 0.3s ease;
}

/* Aksiyon Butonları */
.btn-action {
    /* Mavi-turkuaz gradyan */
    background: linear-gradient(135deg, #00BCD4 0%, #2196F3 100%); /* Turkuaz ile parlak mavi geçiş */
    border: none;
    border-radius: 20px;
    padding: 6px 14px;
    color: white;
    transition: all 0.3s ease;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(33,150,243,0.4); /* Mavi tonlarında gölge */
    color: white;
}

/* Avatar Stili */
.avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    /* Mavi-yeşil tonlu gradyan */
    background: linear-gradient(135deg, #4CAF50 0%, #2196F3 100%); /* Tablo başlığı ile uyumlu gradyan */
    display: flex;
    align-items: center;
    justify-content: center;
    color: white; /* Yazı rengini beyaz tutuyoruz */
    font-weight: bold;
    margin-right: 10px;
}

/* "Toplam Servis Kaydı" için badge stili */
.badge.bg-primary {
    /* Mavi-yeşil temasına uygun bir renk */
    background-color: #009688 !important; /* Çam yeşili */
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
}

/* Kart Başlığı ve Filtreleme Alanı */
.card-header {
    background: #f8f9fa; /* Hafif gri arka plan */
    padding: 15px 20px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
    font-weight: bold;
    color: #343a40; /* Koyu gri yazı rengi */
}

/* Filtreleme Buton Grubu */
.searchWrap .btn-group .btn-dark {
    background-color: #6c757d; /* Koyu gri */
    border-color: #6c757d;
}

.searchWrap .btn-group .btn-dark:hover {
    background-color: #5a6268;
    border-color: #545b62;
}

/* Tarih Aralığı Butonları */
.tarihAraligi .btn-secondary {
    background-color: #6c757d; /* Gri */
    border-color: #6c757d;
    margin-right: 5px;
}

.tarihAraligi .btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}

/* Datatable genel stil ayarları */
#datatableOperatorStats {
    background: none !important; /* Mevcut inline stilin üzerine yazmak için */
    color: #343a40 !important; /* Yazı rengini koyu gri yapıyoruz */
}

#datatableOperatorStats thead.title {
    color: white; /* Başlık metin rengini beyaz yapıyoruz */
}

#datatableOperatorStats tbody tr {
    border-bottom: 1px solid #dee2e6; /* Satırlar arasına ince çizgi */
}

#datatableOperatorStats tbody tr:last-child {
    border-bottom: none; /* Son satırda çizgi olmasın */
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
                                <button class="filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
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