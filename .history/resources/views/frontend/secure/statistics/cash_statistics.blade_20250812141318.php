@extends('frontend.secure.user_master')
@section('user')

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>

<!-- Moment.js (daterangepicker için gerekli) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<!-- Date Range Picker CSS ve JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> 

<div class="page-content servis-istatistik">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
        <!-- Ana Başlık -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title text-center">
                        <i class="mdi mdi-cash-multiple me-2"></i>
                        Kasa İstatistikleri
                    </h4>
                </div>
            </div>
        </div>
        
        <div class="card kasaSonuclari">
            <div class="card-header sayfaBaslik">
                <div class="row align-items-center">
                    <div class="col-md-6 col-12 mb-2 mb-md-0">
                        <span class="fw-bold fs-5">
                            <i class="mdi mdi-chart-line me-2"></i>
                            Gelir-Gider Tablosu
                        </span>
                    </div>
                    <div class="col-md-6 col-12 text-md-end">
                        <div class="btn-group w-100 w-md-auto">
                            <button class="btn btn-dark btn-sm dropdown-toggle filtrele w-100 w-md-auto" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-filter-variant me-1"></i>
                                Filtrele 
                                <i class="mdi mdi-chevron-down ms-1"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 320px;">
                                <div class="row">
                                    <label class="col-12 col-sm-4 form-label fw-bold mb-2">Tarih Aralığı:</label>
                                    <div class="col-12 col-sm-8">
                                        <input id="modalDaterange" class="form-control tarih-araligi mb-3" style="z-index: 9999;">          
                                        <div class="tarihAraligi d-flex flex-wrap gap-2">
                                            <button id="lastYear" class="btn btn-sm btn-outline-secondary flex-fill">Son 1 Yıl</button>
                                            <button id="lastMonth" class="btn btn-sm btn-outline-secondary flex-fill">Son 1 Ay</button>
                                            <button id="lastWeek" class="btn btn-sm btn-outline-secondary flex-fill">Son 7 Gün</button>
                                            <button id="yesterday" class="btn btn-sm btn-outline-secondary flex-fill">Dün</button>
                                            <button id="today" class="btn btn-sm btn-primary flex-fill">Bugün</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-2 p-md-3">
                <div class="row g-3">
                    <!-- GELİR BÖLÜMÜ -->
                    <div class="col-12 col-lg-6">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white py-2">
                                <h6 class="card-title mb-0 text-center">
                                    <i class="mdi mdi-trending-up me-2"></i>
                                    GELİRLER
                                </h6>
                            </div>
                            <div class="card-body p-2 p-md-3">
                                <!-- Gelir Özet Kartları -->
                                <div class="row text-center mb-3">
                                    <div class="col-6 col-md-3 mb-2">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body p-2">
                                                <h6 class="mb-1 gelirNakit text-success fw-bold" style="font-size: 0.9rem;">{{number_format($nakit, 0, ',', '.')}} TL</h6>
                                                <p class="text-muted mb-0" style="font-size: 0.8rem;">Nakit</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3 mb-2">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body p-2">
                                                <h6 class="mb-1 gelirEft text-danger fw-bold" style="font-size: 0.9rem;">{{number_format($eft, 0, ',', '.')}} TL</h6>
                                                <p class="text-muted mb-0" style="font-size: 0.8rem;">EFT/Havale</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3 mb-2">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body p-2">
                                                <h6 class="mb-1 gelirKart text-primary fw-bold" style="font-size: 0.9rem;">{{number_format($kart, 0, ',', '.')}} TL</h6>
                                                <p class="text-muted mb-0" style="font-size: 0.8rem;">Kredi Kartı</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3 mb-2">
                                        <div class="card border-success bg-success text-white">
                                            <div class="card-body p-2">
                                                <h6 class="mb-1 gelirToplam fw-bold" style="font-size: 0.9rem;">{{number_format($gelirler, 0, ',', '.')}} TL</h6>
                                                <p class="mb-0" style="font-size: 0.8rem;">Toplam</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Gelir Grafiği -->
                                <div class="chart-container" style="height: 200px; position: relative;">
                                    <canvas id="gelirChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- GİDER BÖLÜMÜ -->
                    <div class="col-12 col-lg-6">
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white py-2">
                                <h6 class="card-title mb-0 text-center">
                                    <i class="mdi mdi-trending-down me-2"></i>
                                    GİDERLER
                                </h6>
                            </div>
                            <div class="card-body p-2 p-md-3">
                                <div class="row">
                                    <!-- Gider Listesi -->
                                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                                        <div class="gider-list-container" style="max-height: 250px; overflow-y: auto;">
                                            <ul class="list-unstyled gider-listesi">    
                                                @foreach($odemeTuruAll as $key => $value )
                                                    @php 
                                                        $colorIndex = $loop->index % 13;
                                                        $renkler = [
                                                        '#E91E63', '#FF5722', '#FF9800', '#FFC107', '#8BC34A', 
                                                        '#4CAF50', '#00BCD4', '#009688', '#2196F3', '#3F51B5', 
                                                        '#673AB7', '#9C27B0', '#F44336'
                                                    ];
                                                        $renk = $renkler[$colorIndex];
                                                    @endphp
                                                    <li class="gider-item">
                                                        <div class="gider-color" style="background:{{$renk}}"></div>
                                                        <div class="gider-name">{{$key}}</div>
                                                        <div class="gider-amount">{{number_format($value, 0, ',', '.')}} TL</div>
                                                    </li>
                                                @endforeach
                                                <li class="gider-item border-top pt-2 mt-2">
                                                    <div class="gider-color" style="background: #000"></div>
                                                    <div class="gider-name fw-bold">Toplam</div>
                                                    <div class="gider-amount fw-bold text-danger">{{number_format($giderlerToplam, 0, ',', '.')}} TL</div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- Gider Grafiği -->
                                    <div class="col-12 col-md-6">
                                        <div class="chart-container" style="height: 200px; position: relative;">
                                            <canvas id="giderArea"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script>

<script>
var ctx = document.getElementById("giderArea").getContext('2d');
var myChart;
myChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: {!! $odemeTuruSonuc !!},
        datasets: [{
            data: [{!! $giderler !!}],
            backgroundColor: {!! $odemeTuruRenkler !!},
            hoverBorderColor: "#fff",
        }],
    },
    options: {
        plugins: {
            labels: {
                render: 'percentage',
                fontColor: '#fff',
                fontSize: 10,
            }
        },
        legend: {
            display: false
        },
        responsive: true,
        maintainAspectRatio: false,
    }
});
</script>

<script>
var ctx2 = document.getElementById("gelirChart").getContext('2d');
var myChart2;
myChart2 = new Chart(ctx2, {
    type: 'pie',
    data: {
        labels: [{!! $odemeSekliAll !!}],
        datasets: [{
            data: [{{$nakit}},{{$eft}}, {{$kart}}],
            backgroundColor: ["#34a853","#e01010","#1a73e8"],
            hoverBackgroundColor: ["#34a853","#e01010","#1a73e8"],
            hoverBorderColor: "#fff"
        }],
    },
    options: {
        plugins: {
            labels: {
                render: 'percentage',
                fontColor: '#fff',
                fontSize: 10,
            }
        },
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            display: false
        },
    },
});
</script>

<script>
$(document).ready(function () {
    var start_date = moment();
    var end_date = moment();

    $('#modalDaterange').daterangepicker({
        startDate : start_date,
        endDate : end_date,
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
});
</script>

<script>
$(document).ready(function () {
    
    // Sayfa yüklendiğinde tarih aralığını bugüne ayarla
    var today = moment();
    $('#modalDaterange').data('daterangepicker').setStartDate(today);
    $('#modalDaterange').data('daterangepicker').setEndDate(today);

    // Bugünün tarihine göre filtreleme fonksiyonlarını çağır
    filterData(today.format('YYYY-MM-DD'), today.format('YYYY-MM-DD'));
    filterGiderData(today.format('YYYY-MM-DD'), today.format('YYYY-MM-DD'));
    filterGelirGrafik(today.format('YYYY-MM-DD'), today.format('YYYY-MM-DD'));
    filterGiderGrafik(today.format('YYYY-MM-DD'), today.format('YYYY-MM-DD'));
   
   $('#lastYear, #lastMonth, #lastWeek, #yesterday, #today').on('click', function() {
    var buttonId = $(this).attr('id');
    var startDate, endDate;

    // Tüm butonlardan active class'ını kaldır
    $('#lastYear, #lastMonth, #lastWeek, #yesterday, #today').removeClass('btn-primary').addClass('btn-outline-secondary');
    // Tıklanan butona active class ekle
    $(this).removeClass('btn-outline-secondary').addClass('btn-primary');

    if (buttonId === 'lastYear') {
        startDate = moment().subtract(1, 'year');
        endDate = moment();
    } else if (buttonId === 'lastMonth') {
        startDate = moment().subtract(1, 'month');
        endDate = moment();
    } else if (buttonId === 'lastWeek') {
        startDate = moment().subtract(7, 'days');
        endDate = moment();
    } else if (buttonId === 'yesterday') {
        startDate = moment().subtract(1, 'days');
        endDate = moment().subtract(1, 'days');
    } else if (buttonId === 'today') {
        startDate = moment();
        endDate = moment();
    }

    // Tarih aralığını daterangepicker inputuna programlı olarak set et
    $('#modalDaterange').data('daterangepicker').setStartDate(startDate);
    $('#modalDaterange').data('daterangepicker').setEndDate(endDate);

    // Filtreleme fonksiyonlarını çağır
    filterData(startDate.format('YYYY-MM-DD'), endDate.format('YYYY-MM-DD'));
    filterGiderData(startDate.format('YYYY-MM-DD'), endDate.format('YYYY-MM-DD'));
    filterGelirGrafik(startDate.format('YYYY-MM-DD'), endDate.format('YYYY-MM-DD'));
    filterGiderGrafik(startDate.format('YYYY-MM-DD'), endDate.format('YYYY-MM-DD'));
});

});
</script>

<script>
function filterData(startDate, endDate) {
    $.ajax({
        url: '/{{ $tenant_id }}/gelir-tablo/getir',
        type: 'POST',
        data: {
            startDate: startDate,
            endDate: endDate,
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            $(".gelirNakit").text(response.nakit + " TL");
            $(".gelirEft").text(response.eft + " TL");
            $(".gelirKart").text(response.kart + " TL");
            $(".gelirToplam").text(response.toplam + " TL");
        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
}

function filterGiderData(startDate, endDate) {
    $.ajax({
        url: '/{{ $tenant_id }}/gider-tablo/getir',
        method: 'POST',
        data: {
            startDate: startDate,
            endDate: endDate,
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            $(".gider-listesi").html(response.html);
        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
}

function filterGelirGrafik(startDate, endDate) {
    $.ajax({
        url: '/{{ $tenant_id }}/gelir-grafik/getir',
        method: 'POST',
        data: {
            startDate: startDate,
            endDate: endDate,
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            myChart2.data.datasets[0].data = [response.nakit, response.eft, response.kart];
            myChart2.update();
        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
}

function filterGiderGrafik(startDate, endDate) {
    $.ajax({
        url: '/{{ $tenant_id }}/gider-grafik/getir',
        method: 'POST',
        data: {
            startDate: startDate,
            endDate: endDate,
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            if(response.giderler) {
                myChart.data.datasets[0].data = response.giderler.split(',').map(Number);
                myChart.update();
            }
        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
}
</script>

<style>
/* Ana başlık stili */
.page-title-box {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.page-title {
    color: white !important;
    margin: 0;
    font-weight: 600;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

/* Kart başlık stili */
.sayfaBaslik {
    padding: 15px 20px !important;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-bottom: 2px solid #dee2e6;
}

/* Dropdown menu responsive */
@media (max-width: 768px) {
    .dropdown-menu {
        width: 100% !important;
        left: 0 !important;
        transform: none !important;
    }
    
    .tarihAraligi {
        flex-direction: column !important;
    }
    
    .tarihAraligi .btn {
        width: 100% !important;
        margin-bottom: 5px;
    }
}

/* Gider item stili */
.gider-item {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    padding: 8px 12px;
    background: #f8f9fa;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.gider-item:hover {
    background: #e9ecef;
    transform: translateX(3px);
}

.gider-color {
    width: 18px;
    height: 18px;
    border-radius: 4px;
    margin-right: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.gider-name {
    flex: 1;
    font-size: 13px;
    font-weight: 500;
}

.gider-amount {
    font-weight: 600;
    font-size: 13px;
    color: #495057;
}

/* Chart container */
.chart-container {
    position: relative;
}

/* Responsive chart ayarları */
@media (max-width: 768px) {
    .chart-container {
        height: 180px !important;
    }
    
    .gider-list-container {
        max-height: 200px !important;
        margin-bottom: 20px;
    }
    
    .card-body {
        padding: 15px !important;
    }
}

/* Gelir kartları responsive */
@media (max-width: 576px) {
    .col-6.col-md-3 {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

/* Scrollbar styling */
.gider-list-container::-webkit-scrollbar {
    width: 6px;
}

.gider-list-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.gider-list-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.gider-list-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Card border animasyonu */
.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

/* Buton hover efektleri */
.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>

@endsection