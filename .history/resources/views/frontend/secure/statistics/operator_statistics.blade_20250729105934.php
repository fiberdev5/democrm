@extends('frontend.secure.user_master')
@section('user')

<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{asset('backend/assets/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
<script src="{{asset('backend/assets/libs/jquery/jquery.min.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<div class="page-content servis-istatistik" id="operatorStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
        <div class="row pageDetail">
            <div class="col-12">
                <div class="card">
                    <div class="card-header sayfaBaslik">
                        Operatör Bazlı Servis Giriş Sayısı
                    </div>
                    <div class="card-body">
                        <table id="datatableOperatorStats" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            
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
                                                    <input id="daterange" class="tarih-araligi">
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
                            
                            <thead class="title">
                                <tr>
                                    <th data-priority="1">Operatör Adı</th>
                                    <th data-priority="2">Toplam Servis Kaydı</th>
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

    // Butonları oluştur ve tarih aralığını güncelle
    $('#lastYear').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(lastYear);
        $('#daterange').data('daterangepicker').setEndDate(today);
        filterData();
    });

    $('#lastMonth').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(lastMonth);
        $('#daterange').data('daterangepicker').setEndDate(today);
        filterData();
    });

    $('#lastWeek').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(lastWeek);
        $('#daterange').data('daterangepicker').setEndDate(today);
        filterData();
    });

    $('#yesterday').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(yesterday);
        $('#daterange').data('daterangepicker').setEndDate(yesterday);
        filterData();
    });

    $('#today').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(today);
        $('#daterange').data('daterangepicker').setEndDate(today);
        filterData();
    });
    
    // Filtreleme fonksiyonu
    function filterData() {
        $('#datatableOperatorStats').DataTable().draw();
    }
});
</script>

<script>
$(document).ready(function () {
    var start_date = moment().startOf('month').format('DD-MM-YYYY');
    var end_date = moment().format('DD-MM-YYYY');

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
    },
    function(start_date, end_date){
        $('#daterange').html(start_date.format('DD-MM-YYYY') + '-' + end_date.format('DD-MM-YYYY'));
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
            }
        },
        ajax: {
            url: "{{ route('operator.statistics', $tenant_id) }}",
            data: function(data) {
                data.search = $('input[type="search"]').val();
                data.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                data.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
        },
        'columns': [
            { data: 'name', orderable: true},
            { data: 'toplam', orderable: true}
        ],
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        },
        "order": [[1, 'desc']],
        "oLanguage": {
            "sDecimal": ",",
            "sEmptyTable": "Tabloda herhangi bir veri mevcut değil",
            "sInfo": "Operatör Sayısı: _TOTAL_",
            "sInfoEmpty": "Kayıt yok",
            "sInfoFiltered": "",
            "sInfoPostFix": "",
            "sInfoThousands": ".",
            "sLengthMenu": "_MENU_",
            "sLoadingRecords": "Yükleniyor...",
            "sProcessing": "İşleniyor...",
            "sSearch": "Operatör Ara:",
            "sZeroRecords": "Eşleşen kayıt bulunamadı",
            "oPaginate": {
                "sFirst": "İlk",
                "sLast": "Son",
                "sNext": '<i class="fas fa-angle-double-right"></i>',
                "sPrevious": '<i class="fas fa-angle-double-left"></i>'
            },
            "oAria": {
                "sSortAscending": ": artan sütun sıralamasını aktifleştir",
                "sSortDescending": ": azalan sütun sıralamasını aktifleştir"
            },
            "select": {
                "rows": {
                    "_": "%d kayıt seçildi",
                    "0": "",
                    "1": "1 kayıt seçildi"
                }
            }
        },
        dom: '<"top"f>rt<"bottom"ilp><"clear">',
        "lengthMenu": [ [25, 50, 100, -1], [25, 50, 100, "Tümü"] ]
    });
});
</script>

@endsection