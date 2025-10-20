@extends('frontend.secure.user_master')
@section('user')

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<style>
    /* Mobil görünüm için stil ayarlamaları */
    @media (max-width: 767px) {
        .searchWrap {
            margin-top: 0px !important;
        }
        .pageDetail .searchWrap {
            width: 39% !important; /* Arama kutusunun yanında durması için genişlik ayarı */
            margin-bottom: 0px !important;
        }
        div.dataTables_filter input {
            margin-left: 0 !important;
        }

        .dataTables_filter label {
            width: 61% !important;
        }
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
                        <div class="searchWrap float-end d-none d-lg-block"> <!-- Masaüstü için görünür -->
                            <div class="btn-group mb-2">
                                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Filtrele <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <div class="item">
                                        <div class="row">
                                            <label class="col-sm-4">Tarih Aralığı:</label>
                                            <div class="col-sm-8">
                                                <input id="daterange-desktop" class="tarih-araligi" />
                                                <div class="tarihAraligi mt-2 mb-2">
                                                    <button class="btn btn-sm btn-secondary lastYear">Son 1 Yıl</button>
                                                    <button class="btn btn-sm btn-secondary lastMonth">Son 1 Ay</button>
                                                    <button class="btn btn-sm btn-secondary lastWeek">Son 7 Gün</button>
                                                    <button class="btn btn-sm btn-secondary yesterday">Dün</button>
                                                    <button class="btn btn-sm btn-secondary today">Bugün</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="searchWrap float-end d-lg-none"> <!-- Mobil için görünür, initComplete'te taşınacak -->
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
                                                    <button class="btn btn-sm btn-secondary lastYear">Son 1 Yıl</button>
                                                    <button class="btn btn-sm btn-secondary lastMonth">Son 1 Ay</button>
                                                    <button class="btn btn-sm btn-secondary lastWeek">Son 7 Gün</button>
                                                    <button class="btn btn-sm btn-secondary yesterday">Dün</button>
                                                    <button class="btn btn-sm btn-secondary today">Bugün</button>
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
                                    <th>
                                        <i class="fas fa-flag me-2 d-none d-lg-inline"></i>
                                        <span class="d-none d-lg-inline">Durum</span>
                                        <span class="d-lg-none">Durum</span>
                                    </th>
                                    <th>
                                        <i class="fas fa-list-ol me-2 d-none d-lg-inline"></i>
                                        <span class="d-none d-lg-inline">Toplam Servis Sayısı</span>
                                        <span class="d-lg-none">Toplam</span>
                                    </th>
                                    <th style="width: 130px;">
                                        <span class="d-none d-lg-inline">İşlemler</span>
                                        <span class="d-lg-none">İşlem</span>
                                    </th>
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
    let start_date = moment().subtract(1, 'months');
    let end_date = today;
    
    // ÖNCE daterangepicker'ı başlat
    $('.tarih-araligi').daterangepicker({
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
        // Her iki input'u da senkronize et
        $('.tarih-araligi').each(function() {
            $(this).data('daterangepicker').setStartDate(start);
            $(this).data('daterangepicker').setEndDate(end);
        });
        if (typeof table !== 'undefined') {
            table.draw();
        }
    });
    
    // SONRA DataTable'ı başlat
    var table = $('#datatableStateStats').DataTable({
        processing: true,
        serverSide: true,
        order: [[1, 'desc']],
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            },
            sLengthMenu: "_MENU_",
            sEmptyTable: "Tabloda herhangi bir veri mevcut değil",
            sInfo: "Durum Sayısı: _TOTAL_",
            sInfoEmpty: "Kayıt yok",
            sSearch: "",
            sZeroRecords: "Eşleşen kayıt bulunamadı"
        },
        ajax: {
            url: "{{ route('state.statistics', $tenant_id) }}",
            data: function(data) {
                // Görünür olan input'tan tarih al
                var activeDateRange = $('.tarih-araligi:visible').first();
                if (activeDateRange.length && activeDateRange.data('daterangepicker')) {
                    data.from_date = activeDateRange.data('daterangepicker').startDate.format('YYYY-MM-DD');
                    data.to_date = activeDateRange.data('daterangepicker').endDate.format('YYYY-MM-DD');
                } else {
                    // Fallback: eğer daterangepicker hazır değilse varsayılan değerleri kullan
                    data.from_date = moment().subtract(1, 'months').format('YYYY-MM-DD');
                    data.to_date = moment().format('YYYY-MM-DD');
                }
            }
        },
        columns: [
            {   
                data: 'durum',
                name: 'durum',
                render: function(data, type, row) {
                    let firstChar = data.charAt(0).toUpperCase();
                    return `<div style="display:flex; align-items:center;">
                                // <div class="status-avatar">${firstChar}</div>
                                <span>${data}</span>
                            </div>`;
                } 
            },
            { 
                data: 'toplam', 
                name: 'toplam',
                render: function(data) {
                    return `<div class="badge bg-primary">${data}</div>`;
                }
            },
            {
                data: 'durum_id',
                orderable: false,
                render: function(data, type, row) {
                    var activeDateRange = $('.tarih-araligi:visible').first();
                    var from_date, to_date;
                    
                    if (activeDateRange.length && activeDateRange.data('daterangepicker')) {
                        from_date = activeDateRange.data('daterangepicker').startDate.format('YYYY-MM-DD');
                        to_date = activeDateRange.data('daterangepicker').endDate.format('YYYY-MM-DD');
                    } else {
                        from_date = moment().subtract(1, 'months').format('YYYY-MM-DD');
                        to_date = moment().format('YYYY-MM-DD');
                    }
                    
                    var url = "{{ url($tenant_id . '/servisler') }}" + "?state_id=" + data + "&state_istatistik_tarih1=" + from_date + "&state_istatistik_tarih2=" + to_date;
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
            var searchContainer = $('#datatableStateStats_filter');
            var searchInput = searchContainer.find('input');
            searchInput.attr('placeholder', 'Durum Ara...');

            if (window.matchMedia("(max-width: 991.98px)").matches) {
                // Mobil cihazlarda filtre butonunu arama kutusunun yanına taşı
                var mobileFilterWrapper = $('.searchWrap.d-lg-none');
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
    
    // Kısayol butonları - DataTable başlatıldıktan SONRA
    $('.lastYear').click(function() {
        updateRange(moment().subtract(1, 'year'), today);
    });
    $('.lastMonth').click(function() {
        updateRange(moment().subtract(1, 'month'), today);
    });
    $('.lastWeek').click(function() {
        updateRange(moment().subtract(7, 'days'), today);
    });
    $('.yesterday').click(function() {
        updateRange(moment().subtract(1, 'days'), moment().subtract(1, 'days'));
    });
    $('.today').click(function() {
        updateRange(today, today);
    });

    function updateRange(start, end) {
        // Her iki input'u da güncelle
        $('.tarih-araligi').each(function() {
            if ($(this).data('daterangepicker')) {
                $(this).data('daterangepicker').setStartDate(start);
                $(this).data('daterangepicker').setEndDate(end);
            }
        });
        table.draw();
    }
});
</script>
@endsection