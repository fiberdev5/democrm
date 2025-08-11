@extends('frontend.secure.user_master')
@section('user')

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="page-content servis-istatistik" id="technicianStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
        <div class="row pageDetail">
            <div class="col-12">
                <div class="table-modern">
                    <div class="card-header">
                        Teknisyen İstatistikleri
                        <div class="searchWrap float-end">
                            <div class="btn-group mb-2">
                                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Filtrele <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu p-3" style="min-width: 200px;">
                                    <div class="item">
                                        <div class="row mb-3">
                                            <label class="col-sm-4">Cihaz Türü:</label>
                                            <div class="col-sm-8">
                                                <select class="form-select" id="deviceTypeFilter">
                                                    <option value="">Hepsi</option>
                                                    @foreach ($deviceTypes as $device)
                                                        <option value="{{ $device->id }}">{{ $device->cihaz }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4">Tarih Aralığı:</label>
                                            <div class="col-sm-8">
                                                <input id="daterange" class="form-control tarih-araligi" />
                                                <div class="tarihAraligi mt-2 mb-2">
                                                    <button id="lastMonth" class="btn btn-sm btn-secondary">Son 1 Ay</button>
                                                    <button id="lastWeek" class="btn btn-sm btn-secondary">Son 15 Gün</button>
                                                    <button id="last7Days" class="btn btn-sm btn-secondary">Son 7 Gün</button>
                                                    <button id="yesterday" class="btn btn-sm btn-secondary">Dün</button>
                                                    <button id="today" class="btn btn-sm btn-secondary">Bugün</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="button" class="btn btn-primary btn-sm w-100" id="filterButton">
                                                    <i class="fas fa-search me-1"></i>Filtrele
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatableTechnicianStats" class="table table-hover mb-0">
                            <thead class="title">
                                <tr>
                                    <th><i class="fas fa-user me-2"></i>Teknisyen</th>
                                    <th style="width: 85px;"><span class="d-none d-md-inline">Atanan Servis</span><span class="d-md-none">A</span></th>
                                    <th style="width: 85px;"><span class="d-none d-md-inline">Tamamlanan Servis</span><span class="d-md-none">T</span></th>
                                    <th style="width: 85px;"><span class="d-none d-md-inline">Şikayetçi Servis</span><span class="d-md-none">Ş</span></th>
                                    <th style="width: 85px;"><span class="d-none d-md-inline">İptal Servis</span><span class="d-md-none">İ</span></th>
                                    <th style="width: 85px;"><span class="d-none d-md-inline">Haber Verecek</span><span class="d-md-none">H</span></th>
                                    <th style="width: 85px;"><span class="d-none d-md-inline">Fiyatta Anlaşılamadı</span><span class="d-md-none">F</span></th>
                                    <th style="width: 85px;"><span class="d-none d-md-inline">Alınan Ücret</span><span class="d-md-none">Ü</span></th>
                                    <th style="width: 85px;"><span class="d-none d-md-inline">Verilen Teklif</span><span class="d-md-none">T</span></th>
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

<!-- Detay Modal -->
<div class="modal fade" id="technicianDetailModal" tabindex="-1" aria-labelledby="technicianDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="technicianDetailModalLabel">Teknisyen Detay İstatistikleri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detayGrafikler" class="row mb-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>Tamamlanan Servisler</h6>
                                <canvas id="tamamlananChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>İptal Servisler</h6>
                                <canvas id="iptalChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h6>Alınan Ücretler</h6>
                                <canvas id="gelirChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="detayAsamalar" class="row">
                    <!-- Detay istatistikler buraya yüklenecek -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.detayAsamalar .cols {
    width: 20%;
    padding: 10px;
    float: left;
}

.detayAsamalar .capt {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 15px;
    text-align: center;
}

.detayAsamalar .capt p {
    margin: 0 0 10px 0;
    font-size: 12px;
    color: #6c757d;
}

.detayAsamalar .capt h2 {
    margin: 0;
    font-size: 24px;
    color: #495057;
}

.clicked {
    background-color: #e3f2fd !important;
}

@media (max-width: 768px) {
    .detayAsamalar .cols {
        width: 50%;
    }
}
</style>

<script>
$(document).ready(function () {
    // Tarih aralığı seçenekleri
    var lastMonth = moment().subtract(1, 'month');
    var lastWeek = moment().subtract(15, 'days');
    var last7Days = moment().subtract(7, 'days');
    var yesterday = moment().subtract(1, 'days');
    var today = moment();

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
    });

    // Butonlara tıklanınca tarih aralığını değiştir
    $('#lastMonth').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(lastMonth);
        $('#daterange').data('daterangepicker').setEndDate(today);
    });

    $('#lastWeek').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(lastWeek);
        $('#daterange').data('daterangepicker').setEndDate(today);
    });

    $('#last7Days').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(last7Days);
        $('#daterange').data('daterangepicker').setEndDate(today);
    });

    $('#yesterday').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(yesterday);
        $('#daterange').data('daterangepicker').setEndDate(yesterday);
    });

    $('#today').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(today);
        $('#daterange').data('daterangepicker').setEndDate(today);
    });

    var table = $('#datatableTechnicianStats').DataTable({
        processing: true,
        serverSide: true,
        order: [[7, 'desc']], // Alınan ücrete göre sırala
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            },
            sEmptyTable: "Herhangi bir servis hareketi bulunamadı.",
            sInfo: "Teknisyen Sayısı: _TOTAL_",
            sInfoEmpty: "-",
            sSearch: "Teknisyen Ara:",
            sZeroRecords: "Eşleşen kayıt bulunamadı",
            oPaginate: {
                sFirst: "İlk",
                sLast: "Son",
                sNext: '<i class="fas fa-angle-double-right"></i>',
                sPrevious: '<i class="fas fa-angle-double-left"></i>'
            }
        },
        ajax: {
            url: "{{ route('technician.statistics.data', $tenant_id) }}",
            data: function(data) {
                data.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                data.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
                data.device_type = $('#deviceTypeFilter').val();
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
                                <small class="text-muted">Teknisyen #${row.user_id}</small>
                            </div>
                        </div>`;
                }
            },
            { 
                data: 'atanan_servis',
                render: function(data) {
                    return `<div class="badge bg-primary">${data}</div>`;
                }
            },
            { 
                data: 'tamamlanan_servis',
                render: function(data) {
                    return `<div class="badge bg-success">${data}</div>`;
                }
            },
            { 
                data: 'sikayetci_servis',
                render: function(data) {
                    return `<div class="badge bg-warning">${data}</div>`;
                }
            },
            { 
                data: 'iptal_servis',
                render: function(data) {
                    return `<div class="badge bg-danger">${data}</div>`;
                }
            },
            { 
                data: 'haber_verecek',
                render: function(data) {
                    return `<div class="badge bg-info">${data}</div>`;
                }
            },
            { 
                data: 'fiyat_anlasilamadi',
                render: function(data) {
                    return `<div class="badge bg-secondary">${data}</div>`;
                }
            },
            { 
                data: 'alinan_ucret',
                render: function(data) {
                    return `<strong>${parseFloat(data).toLocaleString('tr-TR')} TL</strong>`;
                }
            },
            { 
                data: 'verilen_teklif',
                render: function(data) {
                    return `<strong>${parseFloat(data).toLocaleString('tr-TR')} TL</strong>`;
                }
            }
        ],
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
            
            // Satırlara tıklama özelliği ekle
            $('#datatableTechnicianStats tbody tr').off('click').on('click', function() {
                var data = table.row(this).data();
                if (data && parseInt(data.atanan_servis) > 0) {
                    showTechnicianDetail(data.user_id, data.name);
                    $(this).addClass('clicked').siblings().removeClass('clicked');
                }
            });
        },
        dom: '<"top">rt<"bottom"ilp><"clear">',
        lengthMenu: [ [25, 50, 100, -1], [25, 50, 100, "Tümü"] ],
        paging: false
    });

    // Filtrele butonu
    $('#filterButton').on('click', function() {
        table.draw();
        $('.dropdown-toggle').dropdown('hide');
    });

    // Teknisyen detay fonksiyonu
    function showTechnicianDetail(userId, userName) {
        $('#technicianDetailModalLabel').text(userName + ' - Detay İstatistikleri');
        $('#detayGrafikler, #detayAsamalar').html('<div class="text-center"><div class="spinner-border" role="status"></div></div>');
        
        var fromDate = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var toDate = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
        var deviceType = $('#deviceTypeFilter').val();
        
        $.ajax({
            url: "{{ route('technician.statistics.detail', $tenant_id) }}",
            method: "POST",
            data: {
                user_id: userId,
                from_date: fromDate,
                to_date: toDate,
                device_type: deviceType,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.charts) {
                    createCharts(response.charts);
                }
                if (response.details) {
                    $('#detayAsamalar').html(response.details);
                }
                $('#technicianDetailModal').modal('show');
            },
            error: function() {
                alert('Detay bilgileri yüklenirken hata oluştu.');
            }
        });
    }

    // Grafikleri oluştur
    function createCharts(chartData) {
        // Tamamlanan servisler grafiği
        var ctx1 = document.getElementById('tamamlananChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Tamamlanan',
                    data: chartData.tamamlanan,
                    borderColor: 'rgba(2,117,216,1)',
                    backgroundColor: 'rgba(2,117,216,0.2)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // İptal servisler grafiği
        var ctx2 = document.getElementById('iptalChart').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'İptal',
                    data: chartData.iptal,
                    borderColor: 'rgba(255,0,0,0.7)',
                    backgroundColor: 'rgba(255,0,0,0.2)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Gelir grafiği
        var ctx3 = document.getElementById('gelirChart').getContext('2d');
        new Chart(ctx3, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Gelir',
                    data: chartData.gelir,
                    borderColor: 'rgba(84,177,47,0.7)',
                    backgroundColor: 'rgba(84,177,47,0.2)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
});
</script>

@endsection