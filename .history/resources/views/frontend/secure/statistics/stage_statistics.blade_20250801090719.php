{{-- resources/views/frontend/secure/statistics/stage_statistics.blade.php --}}
@extends('frontend.secure.user_master')
@section('user')

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<div class="page-content servis-istatistik" id="stageStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])

        <div class="row pageDetail">
            <div class="col-12">
                <div class="table-modern">
                    <div class="card-header">
                        Servis Aşama İstatistikleri
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
                        <table id="datatableStageStats" class="table table-hover mb-0">
                            <thead class="title">
                                <tr>
                                    <th><i class="fas fa-tasks me-2"></i>Aşama</th>
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
    let start_date = moment().subtract(1, 'months');
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

    var table = $('#datatableStageStats').DataTable({
        processing: true,
        serverSide: true,
        order: [[1, 'desc']],
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            },
            sEmptyTable: "Herhangi bir servis hareketi bulunamadı.",
            sInfo: "Aşama Sayısı: _TOTAL_",
            sInfoEmpty: "Kayıt yok",
            sSearch: "Aşama Ara:",
            sZeroRecords: "Eşleşen kayıt bulunamadı"
        },
        ajax: {
            url: "{{ route('stage.statistics', $tenant_id) }}",
            data: function(data) {
                data.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                data.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
        },
        columns: [
            {   
                data: 'asama',
                name: 'asama',
                render: function(data, type, row) {
                    let firstChar = data.charAt(0).toUpperCase();
                    return `<div style="display:flex; align-items:center;">
                                <div class="status-avatar">${firstChar}</div>
                                <span><strong>${data}</strong></span>
                            </div>`;
                } 
            },
            { 
                data: 'toplam', 
                name: 'toplam',
                render: function(data) {
                    return `<span class="badge bg-primary"><strong>${data}</strong></span>`;
                }
            },
            {
                data: 'asama_id',
                orderable: false,
                render: function(data, type, row) {
                    var from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    var url = "{{ url($tenant_id . '/servisler') }}" + "?stage_id=" + data + "&stage_istatistik_tarih1=" + from_date + "&stage_istatistik_tarih2=" + to_date;
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

<style>
.status-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    margin-right: 10px;
    font-size: 14px;
}
</style>

@endsection