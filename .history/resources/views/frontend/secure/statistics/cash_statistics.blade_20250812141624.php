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
        
        <div class="card kasaSonuclari" style="margin-bottom: 5px;">
            <div class="card-header sayfaBaslik" style="padding:5px!important;font-weight:500;font-size:18px;">
                <span>Kasa İstatistikleri</span>
                <div class="searchWrap float-end">
                    <div class="btn-group">
                        <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Filtrele <i class="mdi mdi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="item p-3">
                                <div class="row">
                                    <label class="col-12 col-sm-4 mb-2">Tarih Aralığı:</label>
                                    <div class="col-12 col-sm-8">
                                        <input id="modalDaterange" class="tarih-araligi form-control mb-2">          
                                        <div class="tarihAraligi">
                                            <div class="row g-1">
                                                <div class="col-6 col-md-12 mb-1">
                                                    <button id="lastYear" class="btn btn-sm btn-secondary w-100">Son 1 Yıl</button>
                                                </div>
                                                <div class="col-6 col-md-12 mb-1">
                                                    <button id="lastMonth" class="btn btn-sm btn-secondary w-100">Son 1 Ay</button>
                                                </div>
                                                <div class="col-6 col-md-12 mb-1">
                                                    <button id="lastWeek" class="btn btn-sm btn-secondary w-100">Son 7 Gün</button>
                                                </div>
                                                <div class="col-6 col-md-12 mb-1">
                                                    <button id="yesterday" class="btn btn-sm btn-secondary w-100">Dün</button>
                                                </div>
                                                <div class="col-12 col-md-12">
                                                    <button id="today" class="btn btn-sm btn-secondary w-100">Bugün</button>
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
            <div class="card-body">
                <div class="row">
                    <!-- Gelir Tablosu -->
                    <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                        <div class="gelirTablosu">
                            <h5 class="text-center mb-3 text-success">Gelir</h5>
                            <div class="row text-center mb-3">
                                <div class="col-3">
                                    <h6 class="mb-0 gelirNakit fs-6">{{number_format($nakit, 0, ',', '.')}} TL</h6>
                                    <p class="text-muted text-truncate small">Nakit</p>
                                </div>
                                <div class="col-3">
                                    <h6 class="mb-0 gelirEft fs-6">{{number_format($eft, 0, ',', '.')}} TL</h6>
                                    <p class="text-muted text-truncate small">EFT/Havale</p>
                                </div>
                                <div class="col-3">
                                    <h6 class="mb-0 gelirKart fs-6">{{number_format($kart, 0, ',', '.')}} TL</h6>
                                    <p class="text-muted text-truncate small">Kredi Kartı</p>
                                </div>
                                <div class="col-3">
                                    <h6 class="mb-0 gelirToplam fs-6">{{number_format($gelirler, 0, ',', '.')}} TL</h6>
                                    <p class="text-muted text-truncate small">Toplam</p>
                                </div>
                            </div>
                            <div class="chart-container">
                                <canvas id="gelirChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Gider Tablosu -->
                    <div class="col-12 col-lg-6">
                        <div class="giderTablosu">
                            <h5 class="text-center mb-3 text-danger">Gider</h5>
                            <div class="row">
                                <div class="col-12 col-md-6 order-2 order-md-1">
                                    <div class="gider-list">
                                        <ul class="list-unstyled">    
                                            @foreach($odemeTuruAll as $key => $value )
                                                @php 
                                                    $colorIndex = $loop->index % 13;
                                                    $renkler = [
                                                    '#E91E63', // Canlı Pembe
                                                    '#FF5722', // Domates Kırmızısı
                                                    '#FF9800', // Parlak Turuncu
                                                    '#FFC107', // Amber Sarısı
                                                    '#8BC34A', // Canlı Yeşil
                                                    '#4CAF50', // Klasik Yeşil
                                                    '#00BCD4', // Turkuaz (Cyan)
                                                    '#009688', // Deniz Yeşili (Teal)
                                                    '#2196F3', // Gökyüzü Mavisi
                                                    '#3F51B5', // Lacivert (Indigo)
                                                    '#673AB7', // Derin Mor
                                                    '#9C27B0', // Fuşya
                                                    '#F44336'  // Klasik Kırmızı
                                                ];

                                                    $renk = $renkler[$colorIndex];
                                                @endphp
                                                <li class="gider">
                                                    <div class="renk" style="background:{{$renk}}"></div>
                                                    <div class="adi">{{$key}}</div>
                                                    <div class="para">{{number_format($value, 0, ',', '.')}} TL</div>
                                                </li>
                                            @endforeach
                                            <li class="gider border-top pt-2 mt-2">
                                                <div class="renk" style="background: #000"></div>
                                                <div class="adi"><strong>Toplam</strong></div>
                                                <div class="para"><strong>{{number_format($giderlerToplam, 0, ',', '.')}} TL</strong></div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 order-1 order-md-2 mb-3 mb-md-0">
                                    <div class="chart-container">
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
                fontSize: 12,
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
                fontSize: 12,
            }
        },
        legend: {
            display: false
        },
        responsive: true,
        maintainAspectRatio: false,
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
            $(".gider-list").html(response.html);
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
.chart-container {
    position: relative;
    height: 250px;
    margin-bottom: 15px;
}

.gider {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
    padding: 5px 0;
    font-size: 13px;
}

.gider .renk {
    width: 15px;
    height: 15px;
    border-radius: 3px;
    margin-right: 10px;
    flex-shrink: 0;
}

.gider .adi {
    flex: 1;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.gider .para {
    font-weight: bold;
    flex-shrink: 0;
    margin-left: 10px;
}

.gider-list {
    max-height: 250px;
    overflow-y: auto;
}

.dropdown-menu {
    min-width: 280px;
}

/* Responsive düzenlemeler */
@media (max-width: 768px) {
    .chart-container {
        height: 200px;
    }
    
    .gider {
        font-size: 12px;
        padding: 3px 0;
    }
    
    .gider .renk {
        width: 12px;
        height: 12px;
        margin-right: 8px;
    }
    
    .dropdown-menu {
        min-width: 250px;
    }
    
    .dropdown-menu-end {
        right: 0 !important;
        left: auto !important;
    }
}

@media (max-width: 576px) {
    .chart-container {
        height: 180px;
    }
    
    .fs-6 {
        font-size: 0.9rem !important;
    }
    
    .small {
        font-size: 0.8rem !important;
    }
    
    .dropdown-menu {
        min-width: 220px;
    }
}

@media (min-width: 992px) {
    .chart-container {
        height: 280px;
    }
}
</style>

@endsection