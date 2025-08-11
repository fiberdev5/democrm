@extends('frontend.secure.user_master')
@section('user')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<div class="page-content servis-istatistik" id="ilceStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])

        <div class="row pageDetail">
            <div class="col-12">
                <div class="table-modern">
                    <div class="card-header">
                        Servis İlçe İstatistikleri
                        <div class="searchWrap float-end">
                            <div class="btn-group mb-2">
                                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown">
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
                        <table id="datatableIlceStats" class="table table-hover mb-0">
                            <thead class="title">
                                <tr>
                                    <th><i class="fas fa-map-marker-alt me-2"></i>İlçe</th>
                                    <th><i class="fas fa-list-ol me-2"></i>Toplam Servis</th>
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

    function updateRange(start, end) {
        $('#daterange').data('daterangepicker').setStartDate(start);
        $('#daterange').data('daterangepicker').setEndDate(end);
        table.draw();
    }

    $('#lastYear').click(() => updateRange(moment().subtract(1, 'year'), today));
    $('#lastMonth').click(() => updateRange(moment().subtract(1, 'month'), today));
    $('#lastWeek').click(() => updateRange(moment().subtract(7, 'days'), today));
    $('#yesterday').click(() => updateRange(moment().subtract(1, 'days'), moment().subtract(1, 'days')));
    $('#today').click(() => updateRange(today, today));

    $('#daterange').daterangepicker({
        startDate: start_date,
        endDate: end_date,
        locale: {
            format: 'DD-MM-YYYY',
            separator: ' - ',
            applyLabel: 'Uygula',
            cancelLabel: 'İptal',
            daysOfWeek: ['Pz', 'Pzt', 'Sal', 'Çrş', 'Prş', 'Cm', 'Cmt'],
            monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
            firstDay: 1
        }
    }, function(start, end) {
        table.draw();
    });

    var table = $('#datatableIlceStats').DataTable({
        processing: true,
        serverSide: true,
        order: [[1, 'desc']],
        language: {
            paginate: { previous: "<i class='mdi mdi-chevron-left'>", next: "<i class='mdi mdi-chevron-right'>" },
            sEmptyTable: "Veri yok", sInfo: "İlçe Sayısı: _TOTAL_", sInfoEmpty: "Kayıt yok",
            sSearch: "İlçe Ara:", sZeroRecords: "Eşleşen kayıt bulunamadı"
        },
        ajax: {
            // URL'nin tenant_id'yi doğru şekilde içerdiğinden emin olun
            url: "{{ route('ilce.statistics.data', $tenant_id) }}",
            type: "POST", // POST metodu kullanılıyor
            data: function(data) {
                data._token = "{{ csrf_token() }}";
                data.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                data.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
        },
        columns: [
            // ilce_adi yerine ilce olarak döndürdüğümüz için 'ilce' kullanıyoruz
            { data: 'ilce', name: 'ilce' }, // Controller'da 'i.ilce_adi' olarak arama yapılıyor, 'name' de ilce olarak kalsın
            { data: 'toplam', name: 'toplam', render: d => `<span class="badge bg-info">${d}</span>` },
            {
                data: 'ilce_id',
                orderable: false,
                render: function(data) {
                    var from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    // URL'nin tenant_id'yi doğru şekilde içerdiğinden emin olun
                    var url = "{{ url($tenant_id . '/servisler') }}" + "?ilce=" + data + "&ilce_istatistik_tarih1=" + from_date + "&ilce_istatistik_tarih2=" + to_date;
                    return `<a href="${url}" target="_blank" class="btn btn-action btn-sm"><i class="fas fa-eye me-1"></i>Servisleri Gör</a>`;
                }
            }
        ],
        drawCallback: () => $(".dataTables_paginate > .pagination").addClass("pagination-rounded"),
        dom: '<"top">rt<"bottom"ilp><"clear">',
        lengthMenu: [ [25, 50, 100, -1], [25, 50, 100, "Tümü"] ]
    });
});
</script>
@endsection