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
        background: linear-gradient(135deg, #4CAF50 0%, #2196F3 100%);
        color: white;
    }
    .table-modern tbody tr:hover {
        background: linear-gradient(135deg, rgba(33,150,243,0.1), rgba(76,175,80,0.1));
        transform: scale(1.01);
        transition: all 0.3s ease;
    }
    .badge.bg-primary {
        background-color: #009688 !important;
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
</style>

<div class="page-content servis-istatistik" id="stateStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])

        <div class="row pageDetail">
            <div class="col-12">
                <div class="table-modern">
                    <div class="card-header">
                        Servis Aşama İstatistikleri
                        <div class="searchWrap float-end">
                            <input id="daterange" class="tarih-araligi form-control form-control-sm" style="width: 200px;" />
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatableStateStats" class="table table-hover mb-0">
                            <thead class="title">
                                <tr>
                                    <th><i class="fas fa-flag me-2"></i>Durum</th>
                                    <th><i class="fas fa-list-ol me-2"></i>Toplam Servis Sayısı</th>
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
    let start_date = moment().startOf('month').format('DD-MM-YYYY');
    let end_date = today.format('DD-MM-YYYY');

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
            { data: 'durum', name: 'durum' },
            { 
                data: 'toplam', 
                name: 'toplam',
                render: function(data) {
                    return `<span class="badge bg-primary">${data}</span>`;
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
