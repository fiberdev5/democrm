@extends('frontend.secure.user_master')
@section('user')

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<style>
/* Mevcut stiller korunmuştur */
.table-modern {
    background: #ffffff;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}
.table-modern thead {
    background: linear-gradient(135deg, #8e44ad 0%, #9b59b6 100%);
    color: white;
}
.table-modern tbody tr:hover {
    background: linear-gradient(135deg, rgba(142, 68, 173, 0.1), rgba(155, 89, 182, 0.1));
    transform: translateY(-2px); /* Hafif yukarı kayma efekti */
    box-shadow: 0 8px 25px rgba(0,0,0,0.1); /* Hover'da daha belirgin gölge */
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); /* Yumuşak geçiş */
}
.badge.bg-primary {
    background-color: #8e44ad !important;
    color: white;
    padding: 6px 12px; /* Daha dolgun görünüm */
    border-radius: 20px; /* Daha yuvarlak */
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.3s ease; /* Hover için geçiş */
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.badge.bg-primary:hover {
    transform: translateY(-1px); /* Hafif yukarı kayma */
    box-shadow: 0 4px 10px rgba(0,0,0,0.15); /* Gölge artışı */
}
.card-header {
    background: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
    font-weight: bold;
    color: #343a40;
}

/* YENİ BUTON STİLİ */
.btn-action {
    background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); /* Mor tonlarında geçiş */
    border: none;
    border-radius: 25px; /* Daha yuvarlak butonlar */
    padding: 8px 18px; /* Daha geniş ve dolgun */
    color: white !important;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); /* Yumuşak geçiş */
    font-weight: 500;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15); /* Hafif gölge */
    letter-spacing: 0.5px; /* Hafif harf aralığı */
}
.btn-action:hover {
    background: linear-gradient(135deg, #7d3c98 0%, #6c3483 100%); /* Hover'da daha koyu geçiş */
    color: white !important;
    transform: translateY(-2px); /* Hafif yukarı kayma efekti */
    box-shadow: 0 6px 15px rgba(0,0,0,0.25); /* Gölge artışı */
}

/* YENİ AVATAR STİLLERİ */
.status-avatar {
    width: 36px; /* Daha büyük avatar */
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #a864d4, #7f34a1); /* Daha canlı mor geçiş */
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700; /* Daha kalın font */
    font-size: 16px; /* Daha büyük harf */
    margin-right: 12px;
    box-shadow: 0 4px 12px rgba(158, 73, 171, 0.4); /* Daha belirgin ve yumuşak gölge */
    flex-shrink: 0;
    user-select: none;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); /* Yumuşak geçiş */
    border: 2px solid rgba(255,255,255,0.8); /* Hafif beyaz çerçeve */
}

.status-avatar:hover {
    transform: scale(1.1); /* Hover'da hafif büyüme */
    box-shadow: 0 6px 16px rgba(158, 73, 171, 0.6); /* Gölge artışı */
}
</style>


<div class="page-content servis-istatistik" id="stateStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])

        <div class="row pageDetail">
            <div class="col-12">
                <div class="table-modern">
                    <div class="card-header">
                        Servis Durum İstatistikleri
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
                        <table id="datatableStateStats" class="table table-hover mb-0">
                            <thead class="title">
                                <tr>
                                    <th><i class="fas fa-flag me-2"></i>Durum</th>
                                    <th><i class="fas fa-list-ol me-2"></i>Toplam Servis Sayısı</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    let today = moment();
    let start_date = moment().startOf('month');
    let end_date = today;

    // Kısayol butonları
    $('#lastYear').click(function() {
        updateRange(moment().subtract(1, 'year'), today);
    });
    $('#lastMonth').click(function() {
        updateRange(moment().subtract(1, 'month'), today);
    });
    $('#lastWeek').click(function() {
        updateRange(moment().subtract(7, 'days'), today);
    });
    $('#yesterday').click(function() {
        updateRange(moment().subtract(1, 'days'), moment().subtract(1, 'days'));
    });
    $('#today').click(function() {
        updateRange(today, today);
    });

    function updateRange(start, end) {
        $('#daterange').data('daterangepicker').setStartDate(start);
        $('#daterange').data('daterangepicker').setEndDate(end);
        table.draw();
    }

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
        table.draw();
    });

    var table = $('#datatableStateStats').DataTable({
        processing: true,
        serverSide: true,
        order: [[1, 'desc']],
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            },
            sEmptyTable: "Veri yok",
            sInfo: "Durum Sayısı: _TOTAL_",
            sInfoEmpty: "Kayıt yok",
            sSearch: "Durum Ara:",
            sZeroRecords: "Eşleşen kayıt bulunamadı"
        },
        ajax: {
            url: "{{ route('state.statistics', $tenant_id) }}",
            data: function(data) {
                data.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                data.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
        },
        columns: [
            {   data: 'durum',
                name: 'durum',
                render: function(data, type, row) {
                    let firstChar = data.charAt(0).toUpperCase();

                    // İstersen avatar içeriği ikon olabilir, şimdilik harf kullandım
                    return `<div style="display:flex; align-items:center;">
                                <div class="status-avatar">${firstChar}</div>
                                <span>${data}</span>
                            </div>`;
                } },
            { 
                data: 'toplam', 
                name: 'toplam',
                render: function(data) {
                    return `<span class="badge bg-primary">${data}</span>`;
                }
            },
            {
                data: 'durum_id',
                orderable: false,
                render: function(data, type, row) {
                    let from = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    let to = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    let url =  "{{ url($tenant_id . '/servisler') }}" + "?state_id=" + data + "&opeator_istatistik_tarih1=" + from_date + "&opeator_istatistik_tarih2=" + to_date;
                    return `<a href="${url}" target="_blank" class="btn btn-action btn-sm"><i class="fas fa-eye me-1"></i>Servisleri Gör</a>`;
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