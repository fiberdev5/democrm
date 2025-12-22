@extends('frontend.secure.user_master')
@section('user')

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<style>
@media (max-width: 767px) {

.searchWrap{margin-top: 0px !important;}
    .pageDetail .searchWrap{width: 39% !important;}
    .pageDetail .searchWrap{margin-bottom: 0px !important;}
 div.dataTables_filter input{margin-left: 0 !important;}
}
</style>

<div class="page-content servis-istatistik" id="operatorStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
        <div class="row pageDetail">
            <div class="col-12">
                
                <div class="table-modern">
                    <div class="card-header">
                        Operatör İstatistikleri
                        <div class="searchWrap float-end d-none d-lg-block" style="visibility: hidden; opacity: 0; transition: opacity 0.3s;">
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
                        <!-- Mobil için filtre butonu -->
                        <div class="searchWrap d-lg-none" style="visibility: hidden; opacity: 0; transition: opacity 0.3s;">
                            <div class="btn-group mb-2">
                                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Filtrele <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <div class="item">
                                        <div class="row">
                                            <label class="col-sm-4">Tarih Aralığı:</label>
                                            <div class="col-sm-8">
                                                <input id="daterange-mobile" class="tarih-araligi" />
                                                <div class="tarihAraligi mt-2 mb-2">
                                                    <button class="btn btn-sm btn-secondary lastYear-mobile">Son 1 Yıl</button>
                                                    <button class="btn btn-sm btn-secondary lastMonth-mobile">Son 1 Ay</button>
                                                    <button class="btn btn-sm btn-secondary lastWeek-mobile">Son 7 Gün</button>
                                                    <button class="btn btn-sm btn-secondary yesterday-mobile">Dün</button>
                                                    <button class="btn btn-sm btn-secondary today-mobile">Bugün</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table id="datatableOperatorStats" class="table table-hover mb-0">
                            <thead class="title">
                                <tr>
                                    <th>
                                        <i class="fas fa-user me-2 d-none d-lg-inline"></i>
                                        <span class="d-none d-lg-inline">Operatör Adı</span>
                                        <span class="d-lg-none">Operatör</span>
                                    </th>
                                    <th>
                                        <i class="fas fa-clipboard-list me-2 d-none d-lg-inline"></i>
                                        <span class="d-none d-lg-inline">Toplam Servis Kaydı</span>
                                        <span class="d-lg-none">Toplam</span>
                                    </th>
                                    <th style="width: 130px;">
                                        <span class="d-none d-lg-inline">İşlemler</span>
                                        <span class="d-lg-none">İşlem</span>
                                    </th>
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

    // Desktop tarih aralığı butonları
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

    // Mobil tarih aralığı butonları
    $('.lastYear-mobile').on('click', function() {
        $('#daterange-mobile').data('daterangepicker').setStartDate(lastYear);
        $('#daterange-mobile').data('daterangepicker').setEndDate(today);
        table.draw();
    });

    $('.lastMonth-mobile').on('click', function() {
        $('#daterange-mobile').data('daterangepicker').setStartDate(lastMonth);
        $('#daterange-mobile').data('daterangepicker').setEndDate(today);
        table.draw();
    });

    $('.lastWeek-mobile').on('click', function() {
        $('#daterange-mobile').data('daterangepicker').setStartDate(lastWeek);
        $('#daterange-mobile').data('daterangepicker').setEndDate(today);
        table.draw();
    });

    $('.yesterday-mobile').on('click', function() {
        $('#daterange-mobile').data('daterangepicker').setStartDate(yesterday);
        $('#daterange-mobile').data('daterangepicker').setEndDate(yesterday);
        table.draw();
    });

    $('.today-mobile').on('click', function() {
        $('#daterange-mobile').data('daterangepicker').setStartDate(today);
        $('#daterange-mobile').data('daterangepicker').setEndDate(today);
        table.draw();
    });

    // Tarih aralığı başlangıç değerleri
    var start_date = moment().subtract(1, 'months').format('DD-MM-YYYY');       
    var end_date = moment().format('DD-MM-YYYY');

    // Desktop Date Range Picker
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

    // Mobil Date Range Picker
    $('#daterange-mobile').daterangepicker({
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
        $('#daterange-mobile').html(start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY'));
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
            sSearch: "",
            sZeroRecords: "Eşleşen kayıt bulunamadı",
            sLengthMenu: "_MENU_",
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
                // Tarih aralığını ajax'a ekle - hem desktop hem mobil için kontrol
                if ($('#daterange').length && $('#daterange').data('daterangepicker')) {
                    data.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    data.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
                } else if ($('#daterange-mobile').length && $('#daterange-mobile').data('daterangepicker')) {
                    data.from_date = $('#daterange-mobile').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    data.to_date = $('#daterange-mobile').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
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
                    var from_date, to_date;
                    
                    // Hem desktop hem mobil için tarih al
                    if ($('#daterange').length && $('#daterange').data('daterangepicker')) {
                        from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    } else if ($('#daterange-mobile').length && $('#daterange-mobile').data('daterangepicker')) {
                        from_date = $('#daterange-mobile').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        to_date = $('#daterange-mobile').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    }
                    
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
        dom: '<"top"f>rt<"bottom"i<"float-end"lp>><"clear">',
        lengthMenu: [ [25, 50, 100, -1], [25, 50, 100, "Tümü"] ],
        
    });
});
</script>
@endsection