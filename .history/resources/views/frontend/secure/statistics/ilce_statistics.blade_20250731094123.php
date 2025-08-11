@extends('frontend.secure.user_master')
@section('user')

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<div class="page-content servis-istatistik" id="ilceStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
        <div class="row pageDetail">
            <div class="col-12">
                <div class="table-modern">
                    <div class="card-header">
                        İlçe İstatistikleri
                        <div class="searchWrap float-end">
                            <div class="btn-group mb-2">
                                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Filtrele <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <div class="item">
                                        <div class="row mb-3">
                                            <label class="col-sm-4">İl Seç:</label>
                                            <div class="col-sm-8">
                                                <select class="form-select form-select-sm" id="ilSelect">
                                                    <option value="">Tüm İller</option>
                                                    @foreach($iller as $il)
                                                        <option value="{{ $il->id }}">{{ $il->name }}</option>

                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
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
                        <table id="datatableIlceStats" class="table table-hover mb-0">
                            <thead class="title">
                                <tr>
                                    <th><i class="fas fa-map-marker-alt me-2"></i>İlçe</th>
                                    <th><i class="fas fa-clipboard-list me-2"></i>Toplam Servis</th>
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

    // İl seçimi değiştiğinde tabloyu yenile
    $('#ilSelect').on('change', function() {
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

    var table = $('#datatableIlceStats').DataTable({
        processing: true,
        serverSide: true,
        order: [[1, 'desc']],
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            },
            sEmptyTable: "Tabloda herhangi bir veri mevcut değil",
            sInfo: "İlçe Sayısı: _TOTAL_",
            sInfoEmpty: "Kayıt yok",
            sSearch: "İlçe Ara:",
            sZeroRecords: "Eşleşen kayıt bulunamadı",
            oPaginate: {
                sFirst: "İlk",
                sLast: "Son",
                sNext: '<i class="fas fa-angle-double-right"></i>',
                sPrevious: '<i class="fas fa-angle-double-left"></i>'
            }
        },
        ajax: {
            url: "{{ route('ilce.statistics', $tenant_id) }}",
            data: function(data) {
                // Tarih aralığını ajax'a ekle
                data.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                data.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
                
                // İl filtresini ekle
                data.il = $('#ilSelect').val();
            }
        },
        columns: [
            { 
                data: 'ilceName', 
                render: function(data, type, row) {
                    return `
                        <div class="d-flex align-items-center">
                            <div class="location-icon me-2">
                                <i class="fas fa-map-marker-alt text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-bold">${data}</div>
                                <small class="text-muted">${row.ilName}</small>
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
                data: null,
                render: function(data, type, row) {
                    var from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    var ilceParam = row.il + "-" + row.ilce;
                    var url = "{{ url($tenant_id . '/servisler') }}" + "?ilceArama=" + encodeURIComponent(ilceParam) + "&ilce_istatistik_tarih1=" + from_date + "&ilce_istatistiktarih2=" + to_date;
                    
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

<style>
.location-icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(13, 110, 253, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
}

.tarih-araligi {
    width: 100%;
    padding: 5px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
}

.tarihAraligi .btn {
    margin: 2px;
}

.dropdown-menu {
    padding: 15px;
    min-width: 300px;
}

.dropdown-menu .item {
    margin-bottom: 10px;
}

.badge {
    font-size: 12px;
    padding: 6px 12px;
}

.btn-action {
    background: #28a745;
    border-color: #28a745;
    color: white;
    font-size: 12px;
    padding: 4px 8px;
}

.btn-action:hover {
    background: #218838;
    border-color: #1e7e34;
    color: white;
}
</style>

@endsection