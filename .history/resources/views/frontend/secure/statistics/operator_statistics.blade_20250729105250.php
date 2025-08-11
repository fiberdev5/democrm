@extends('frontend.secure.user_master')
@section('user')
<div class="page-content" id="operator_statistics">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
    </div>
    <div class="container">
        <h4 class="mb-3">Operatör Bazlı Servis Giriş Sayısı</h4>

        <div class="searchWrap float-end">
            <div class="btn-group mb-2 ">
                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Filtrele <i class="mdi mdi-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                    {{-- Durum filtresi bu tabloda yok, kaldırıldı --}}
                    
                    <div class="item">
                        <div class="row">
                            <label class="col-sm-4">Tarih Aralığı:</label>
                            <div class="col-sm-8">
                                <input id="daterange" class="tarih-araligi" readonly>
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
            </div></div>

        <table id="datatableOperatorStatistics" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead class="title">
                <tr>
                    <th>Operatör Adı</th>
                    <th>Toplam Servis Kaydı</th>
                </tr>
            </thead>
            <tbody>
                {{-- DataTables verileri burada gösterilecek --}}
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        var start_date_initial = moment("{{ $tarih1->format('Y-m-d') }}");
        var end_date_initial = moment("{{ $tarih2->format('Y-m-d') }}");

        // DaterangePicker başlatma
        $('#daterange').daterangepicker({
            startDate: start_date_initial,
            endDate: end_date_initial,
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
            $('#daterange').val(start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY'));
            table.draw(); // Tarih değiştiğinde DataTables'ı yeniden çiz
        });

        // DataTables başlatma
        var table = $('#datatableOperatorStatistics').DataTable({
            processing: true,
            serverSide: true,
            order: [[1, 'desc']], // Toplam servis kaydına göre azalan sıralama
            language: {
                paginate: {
                    previous: "<i class='mdi mdi-chevron-left'>",
                    next: "<i class='mdi mdi-chevron-right'>"
                },
                sDecimal: ",",
                sEmptyTable: "Tabloda herhangi bir veri mevcut değil",
                sInfo: "Operatör Sayısı: _TOTAL_",
                sInfoEmpty: "Kayıt yok",
                sInfoFiltered: "",
                sInfoPostFix: "",
                sInfoThousands: ".",
                sLengthMenu: "_MENU_",
                sLoadingRecords: "Yükleniyor...",
                sProcessing: "İşleniyor...",
                sSearch: "Operatör Ara:",
                sZeroRecords: "Eşleşen kayıt bulunamadı",
                oPaginate: {
                    sFirst: "İlk",
                    sLast: "Son",
                    sNext: '<i class="fas fa-angle-double-right"></i>',
                    sPrevious: '<i class="fas fa-angle-double-left"></i>'
                },
                oAria: {
                    sSortAscending: ": artan sütun sıralamasını aktifleştir",
                    sSortDescending: ": azalan sütun sıralamasını aktifleştir"
                },
            },
            ajax: {
                url: "{{ route('operator.statistics', $tenant_id) }}", // Controller'daki route adı
                data: function(d) {
                    d.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    d.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
            },
            columns: [
                { data: 'name', name: 'u.name' },
                { data: 'toplam', name: 'toplam' },
            ],
            drawCallback: function() {
                $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
            },
            dom: '<"top"f>rt<"bottom"ilp><"clear">',
            "lengthMenu": [ [25, 50, 100, -1], [25, 50, 100, "Tümü"] ],
        });

        // Tarih butonu tıklamaları için olay dinleyicileri
        function setAndFilterDate(startDate, endDate) {
            $('#daterange').data('daterangepicker').setStartDate(startDate);
            $('#daterange').data('daterangepicker').setEndDate(endDate);
            $('#daterange').val(startDate.format('DD-MM-YYYY') + ' - ' + endDate.format('DD-MM-YYYY'));
            table.draw();
        }

        $('#lastYear').on('click', function() {
            setAndFilterDate(moment().subtract(1, 'year').startOfDay(), moment().endOfDay());
        });

        $('#lastMonth').on('click', function() {
            setAndFilterDate(moment().subtract(1, 'month').startOfDay(), moment().endOfDay());
        });

        $('#lastWeek').on('click', function() {
            setAndFilterDate(moment().subtract(7, 'days').startOfDay(), moment().endOfDay());
        });

        $('#yesterday').on('click', function() {
            setAndFilterDate(moment().subtract(1, 'days').startOfDay(), moment().subtract(1, 'days').endOfDay());
        });

        $('#today').on('click', function() {
            setAndFilterDate(moment().startOfDay(), moment().endOfDay());
        });
    });
</script>
@section('scripts')
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endsection
@endsection