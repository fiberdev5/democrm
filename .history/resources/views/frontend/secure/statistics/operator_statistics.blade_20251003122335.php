@extends('frontend.secure.user_master')
@section('user')

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<style>
@media (max-width: 767px) {
    .searchWrap {
        margin-top: 0px !important;
        margin-bottom: 0px !important;
        width: auto !important;
        flex: 0 0 auto !important;
    }
    
    .pageDetail .searchWrap {
        width: auto !important;
        margin-bottom: 0px !important;
    }
    
    div.dataTables_filter {
        width: 100% !important;
        display: flex !important;
        align-items: center !important;
    }
    
    div.dataTables_filter label {
        width: 100% !important;
        display: flex !important;
        margin-bottom: 0 !important;
    }
    
    div.dataTables_filter input {
        margin-left: 0 !important;
        flex: 1 !important;
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }
    
    /* Mobilde tablo başlıkları */
    #datatableOperatorStats thead {
        display: none;
    }
    
    #datatableOperatorStats tbody tr {
        display: block;
        margin-bottom: 10px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 8px;
    }
    
    #datatableOperatorStats tbody td {
        display: block;
        text-align: left !important;
        padding: 6px 3px !important;
        border: none !important;
    }
    
    #datatableOperatorStats tbody td:before {
        content: attr(data-label);
        font-weight: bold;
        display: block;
        margin-bottom: 3px;
        font-size: 11px;
        color: #6c757d;
    }
    
    #datatableOperatorStats tbody td:nth-child(1):before {
        content: "Operatör:";
    }
    
    #datatableOperatorStats tbody td:nth-child(2):before {
        content: "Toplam:";
    }
    
    #datatableOperatorStats tbody td:nth-child(3):before {
        content: "İşlem:";
    }
    
    /* Avatar boyutu mobilde küçült */
    .avatar {
        width: 32px !important;
        height: 32px !important;
        font-size: 13px !important;
        margin-right: 8px !important;
    }
    
    /* Buton boyutu küçült */
    .btn-action {
        font-size: 11px !important;
        padding: 4px 8px !important;
    }
    
    /* Arama ve filtre yan yana */
    #datatableOperatorStats_filter.input-group {
        display: flex !important;
        flex-wrap: nowrap !important;
    }
    
    #datatableOperatorStats_filter.input-group .searchWrap {
        margin-left: 0 !important;
    }
    
    /* Badge boyutu */
    .badge {
        font-size: 12px !important;
        padding: 4px 8px !important;
    }
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
                        
                        <!-- Mobil için filtre butonu -->
                        <div class="searchWrap float-end d-lg-none" style="visibility: hidden; opacity: 0;">
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
                        
                        <!-- Desktop için filtre butonu -->
                        <div class="searchWrap float-end d-none d-lg-block">
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
    var start_date = moment().subtract(1, 'months').format('DD-MM-YYYY');       
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
        dom: '<"top"f>rt<"bottom"i<"float-end"lp>><"clear">',
        lengthMenu: [ [25, 50, 100, -1], [25, 50, 100, "Tümü"] ],
        initComplete: function(settings, json) {
            var searchContainer = $('#datatableOperatorStats_filter');
            var searchInput = searchContainer.find('input');
            searchInput.attr('placeholder', 'Operatör Ara...');
            
            // Mobilde arama ve filtreleme yan yana
            if (window.matchMedia("(max-width: 991.98px)").matches) {
                var mobileFilterWrapper = $('.d-lg-none .searchWrap');
                searchContainer.append(mobileFilterWrapper);
                searchContainer.addClass('input-group');
                mobileFilterWrapper.find('.btn').css({
                    'border-top-left-radius': '0',
                    'border-bottom-left-radius': '0'
                });
            }
            
            $('.searchWrap').css({ visibility: 'visible', opacity: 1 });
        }
    });
});
</script>
@endsection