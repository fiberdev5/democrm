@extends('frontend.secure.user_master')
@section('user')

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<style>
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
        transform: scale(1.01);
        transition: all 0.3s ease;
    }
    .badge.bg-primary {
        background-color: #8e44ad !important;
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
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
    .btn-action {
        background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
        border: none;
        border-radius: 20px;
        padding: 6px 14px;
        color: white !important;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    .btn-action:hover {
        background: linear-gradient(135deg, #7d3c98 0%, #6c3483 100%);
        color: white !important;
    }
.status-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7e3fbf, #a368d4);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 16px;
    margin-right: 12px;
    box-shadow:
        0 4px 8px rgba(142, 68, 173, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.3),
        inset 0 -2px 6px rgba(0, 0, 0, 0.2);
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
    user-select: none;
    transition: box-shadow 0.3s ease, transform 0.3s ease;
    cursor: default;
    position: relative;
    overflow: hidden;
}

/* Hafif parlaklık efekti için animasyonlu parlak çizgi */
.status-avatar::before {
    content: "";
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(60deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.5) 50%, rgba(255,255,255,0.2) 100%);
    transform: rotate(25deg);
    animation: shine 3s infinite;
    pointer-events: none;
    border-radius: 50%;
    filter: blur(8px);
}

/* Parlak çizgi animasyonu */
@keyframes shine {
    0% {
        transform: rotate(25deg) translateX(-100%);
    }
    100% {
        transform: rotate(25deg) translateX(100%);
    }
}

/* Hover efekti: biraz büyü ve gölge artışı */
.status-avatar:hover {
    box-shadow:
        0 6px 14px rgba(142, 68, 173, 0.5),
        inset 0 1px 0 rgba(255, 255, 255, 0.5),
        inset 0 -2px 8px rgba(0, 0, 0, 0.3);
    transform: scale(1.1);
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
            {  data: 'durum',
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
                    let url = `{{ url($tenant_id . '/servisler') }}?state_id=${data}&state_date1=${from}&state_date2=${to}`;
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
