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
                        <div class="dropdown-menu">
                            <div class="item">
                                <div class="row">
                                    <label class="col-sm-4">Tarih Aralığı:</label>
                                    <div class="col-sm-8">
                                        <input id="modalDaterange" class="tarih-araligi" style="z-index: 9999;">          
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
                <div class="row">
                    <!-- GELİR SEKSİYONU -->
                    <div class="col-sm-6 gelirTablosu">
                        <div class="gelir-gider-header mb-3">
                            <h5 class="text-success mb-0">
                                <i class="mdi mdi-arrow-up-bold"></i> GELİR
                            </h5>
                        </div>
                        
                        <div class="row">
                            <!-- Gelir Listesi -->
                            <div class="col-sm-4">
                                <ul class="gelir-listesi">
                                    <li class="gelir">
                                        <div class="renk" style="background:#34a853"></div>
                                        <div class="adi">Nakit</div>
                                        <div class="para gelirNakit">{{number_format($nakit, 0, ',', '.')}} TL</div>
                                    </li>
                                    <li class="gelir">
                                        <div class="renk" style="background:#e01010"></div>
                                        <div class="adi">EFT/Havale</div>
                                        <div class="para gelirEft">{{number_format($eft, 0, ',', '.')}} TL</div>
                                    </li>
                                    <li class="gelir">
                                        <div class="renk" style="background:#1a73e8"></div>
                                        <div class="adi">Kredi Kartı</div>
                                        <div class="para gelirKart">{{number_format($kart, 0, ',', '.')}} TL</div>
                                    </li>
                                    <li class="gelir toplam-gelir">
                                        <div class="renk" style="background: #000"></div>
                                        <div class="adi">Toplam</div>
                                        <div class="para gelirToplam">{{number_format($gelirler, 0, ',', '.')}} TL</div>
                                    </li>
                                </ul>
                            </div>
                            
                            <!-- Gelir Grafiği -->
                            <div class="col-sm-8">
                                <div class="chart-container">
                                    <canvas id="gelirChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- GİDER SEKSİYONU -->
                    <div class="col-sm-6 giderTablosu">
                        <div class="gelir-gider-header mb-3">
                            <h5 class="text-danger mb-0">
                                <i class="mdi mdi-arrow-down-bold"></i> GİDER
                            </h5>
                        </div>
                        
                        <div class="row">
                            <!-- Gider Listesi -->
                            <div class="col-sm-4">
                                <ul class="gider-listesi">    
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
                                    <li class="gider toplam-gider">
                                        <div class="renk" style="background: #000"></div>
                                        <div class="adi">Toplam</div>
                                        <div class="para">{{number_format($giderlerToplam, 0, ',', '.')}} TL</div>
                                    </li>
                                </ul>
                            </div>
                            
                            <!-- Gider Grafiği -->
                            <div class="col-sm-8">
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
            $(".giderTablosu .gider-listesi").html(response.html);
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
/* Gelir-Gider Başlıkları */
.gelir-gider-header {
    text-align: center;
    padding: 10px 0;
    border-bottom: 2px solid #f0f0f0;
}

.gelir-gider-header h5 {
    font-weight: 600;
    font-size: 16px;
}

/* Chart Container - Larger charts */
.chart-container {
    height: 280px;
    position: relative;
}

/* Gelir Listesi - Compact */
.gelir-listesi {
    list-style: none;
    padding: 0;
    margin: 0;
}

.gelir {
    display: flex;
    align-items: center;
    margin-bottom: 6px;
    padding: 4px 0;
}

.gelir .renk {
    width: 12px;
    height: 12px;
    border-radius: 2px;
    margin-right: 8px;
    flex-shrink: 0;
}

.gelir .adi {
    flex: 1;
    font-size: 11px;
    color: #333;
}

.gelir .para {
    font-weight: bold;
    font-size: 11px;
    color: #333;
    flex-shrink: 0;
}

.toplam-gelir {
    border-top: 1px solid #ddd;
    padding-top: 6px !important;
    margin-top: 6px;
    font-weight: bold;
}

.toplam-gelir .adi,
.toplam-gelir .para {
    font-weight: bold;
    color: #000;
}

/* Gider Listesi - Compact */
.gider-listesi {
    list-style: none;
    padding: 0;
    margin: 0;
}

.gider {
    display: flex;
    align-items: center;
    margin-bottom: 0;
    padding: 4px 0;
}

.gider .renk {
    width: 12px;
    height: 12px;
    border-radius: 2px;
    margin-right: 8px;
    flex-shrink: 0;
}

.gider .adi {
    flex: 1;
    font-size: 11px;
    color: #333;
}

.gider .para {
    font-weight: bold;
    font-size: 11px;
    color: #333;
    flex-shrink: 0;
}

.toplam-gider {
    border-top: 1px solid #ddd;
    padding-top: 6px !important;
    margin-top: 6px;
    font-weight: bold;
}

.toplam-gider .adi,
.toplam-gider .para {
    font-weight: bold;
    color: #000;
}

/* Responsive düzenlemeler */
@media (max-width: 768px) {
    .chart-container {
        height: 220px;
    }
    
    .gelir .adi,
    .gelir .para,
    .gider .adi,
    .gider .para {
        font-size: 10px;
    }
}
</style>

@endsection