@extends('frontend.secure.user_master')
@section('user')

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<style>
/* Modern Tabla Temel Stili - Birleştirilmiş */
.table-modern {
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border: 1px solid #f0f0f0;
}

/* Tablo Başlığı - Sade gri tonları */
.table-modern thead {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
}

.table-modern thead th {
    font-weight: 600;
    letter-spacing: 0.5px;
    padding: 16px 20px;
    border: none;
    font-size: 14px;
}

/* Tablo Satırlarının Üzerine Gelindiğinde Animasyon */
.table-modern tbody tr {
    border-bottom: 1px solid #f8f9fa;
    transition: all 0.2s ease;
}

.table-modern tbody tr:hover {
    background: #f8f9fa !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.table-modern tbody td {
    padding: 16px 20px;
    vertical-align: middle;
    border: none;
}

/* Aksiyon Butonları - Sade gri renk */
.btn-action {
    background: #6c757d;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    color: white !important;
    transition: all 0.2s ease;
    font-size: 13px;
    font-weight: 500;
    letter-spacing: 0.3px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

.btn-action:hover {
    background: #5a6268;
    color: white !important;
    transform: translateY(-1px);
    box-shadow: 0 3px 12px rgba(108, 117, 125, 0.3);
}

/* Avatar Stili (Operatör sayfası için) */
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
    transition: all 0.3s ease;
    border: 2px solid rgba(255,255,255,0.9);
}

.avatar:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
}

/* Status Avatar (Durum sayfası için) */
.status-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 16px;
    margin-right: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    flex-shrink: 0;
    user-select: none;
    transition: all 0.3s ease;
    border: 2px solid rgba(255,255,255,0.9);
}

.status-avatar:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
}

/* Badge stili - Sade gri renk */
.badge.bg-primary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 13px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.badge.bg-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(108, 117, 125, 0.2);
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
#datatableOperatorStats, #datatableStateStats {
    background: none !important;
    color: #2c3e50 !important;
}

#datatableOperatorStats thead.title, #datatableStateStats thead.title {
    color: white !important;
}

/* DataTable sayfalama */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 8px 12px;
    margin-left: 2px;
    border-radius: 6px;
    border: 1px solid #dee2e6;
    color: #6c757d !important;
    background: white;
    transition: all 0.2s ease;
}

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

/* Özel flex düzeni */
div[style*="display:flex"] {
    display: flex;
    align-items: center;
}

/* Operatör adı için özel stil */
.fw-bold {
    font-weight: 600 !important;
    color: #2c3e50;
}

.text-muted {
    color: #6c757d !important;
    font-size: 12px;
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
                    var from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    var url = "{{ url($tenant_id . '/servisler') }}" + "?state_id=" + data + "&state_istatistik_tarih1=" + from_date + "&state_istatistik_tarih2=" + to_date;
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