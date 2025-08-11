@extends('frontend.secure.user_master')
@section('user')

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<div class="page-content servis-istatistik" id="personelDepoStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        <div class="row pageDetail">
            <div class="col-12">
                <div class="table-modern">
                    <div class="card-header">
                        Personel Depo İstatistikleri
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
                        <table id="datatablePersonelDepoStats" class="table table-hover mb-0">
                            <thead class="title">
                                <tr>
                                    <th><i class="fas fa-user me-2"></i>Personel</th>
                                    <th><i class="fas fa-warehouse me-2"></i>Toplam Stok Adedi</th>
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
        table.draw(); // DataTables'ı yeniden çiz
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
        table.draw(); // Tarih değiştiğinde DataTables'ı yeniden çiz
    });

    var table = $('#datatablePersonelDepoStats').DataTable({
        processing: true,
        serverSide: true,
        order: [[1, 'desc']],
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            },
            sEmptyTable: "Herhangi bir personel depo hareketi bulunamadı.",
            sInfo: "Personel Sayısı: _TOTAL_",
            sInfoEmpty: "Kayıt yok",
            sSearch: "Personel Ara:",
            sZeroRecords: "Eşleşen kayıt bulunamadı"
        },
        ajax: {
            url: "{{ route('stock.statistics.data', $tenant_id) }}", // Controller'dan gelecek veri
            type: "POST",
            data: function(d) {
                d._token = "{{ csrf_token() }}";
                d.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                d.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
        },
        columns: [
            {
                data: 'personel_name', // Controller'da 'personel_name' olarak tanımlandı
                name: 'name', // Veritabanı sütun adı
                render: function(data, type, row) {
<div class="d-flex align-items-center">
                    <div class="avatar">${data.charAt(0)}</div>
                    <div>
                        <div class="fw-bold">${data}</div>
                        <small class="text-muted">Operatör #${row.id}</small>
                    </div>
                </div>`;
                }
            },
            {
                data: 'toplam_adet', 
                render: function(data) {
                    return `<span class="badge bg-info">${data}</span>`;
                }
            },
            {
                data: 'action', 
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        },
        dom: '<"top">rt<"bottom"ilp><"clear">', // Arama çubuğunu gizledik, sadece filtre dropdown'ı kullanılıyor
        lengthMenu: [ [25, 50, 100, -1], [25, 50, 100, "Tümü"] ]
    });

    // Filtre dropdown'u tıklandığında tablonun yeniden çizilmesini engelleyelim
    // Dropdown açılıp kapandığında DataTables'ın yeniden çizilmesini istemiyorsak bu kalabilir.
    // Eğer 'ARA' butonuna basıldığında yeniden çizilmesini istiyorsak, bu kısım gereksiz.
    // Ancak mevcut kodunuzda 'ARA' butonu yok, doğrudan tarih seçimi ve kısayol butonları ile 'table.draw()' çağrılıyor.
    // Bu nedenle aşağıdaki kod bloğu gereksiz olabilir.
    // $(".filtrele").on("hide.bs.dropdown", function () {
    //     // dropdown kapanırken bir işlem yapmak istersen
    // });
});
</script>
@endsection