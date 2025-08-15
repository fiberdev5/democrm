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
                <span>Gelir-Gider Tablosu</span>
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
                    <div class="col-sm-6 gelirTablosu">
                        <div class="row">        
                            <div class="sag">
                                <div class="row text-center">
                                    <div class="col-3">
                                        <h6 class="mb-0 gelirNakit">{{number_format($nakit, 0, ',', '.')}} TL</h6>
                                        <p class="text-muted text-truncate">Nakit</p>
                                    </div>
                                    <div class="col-3">
                                        <h6 class="mb-0 gelirEft">{{number_format($eft, 0, ',', '.')}} TL</h6>
                                        <p class="text-muted text-truncate">EFT/Havale</p>
                                    </div>
                                    <div class="col-3">
                                        <h6 class="mb-0 gelirKart">{{number_format($kart, 0, ',', '.')}} TL</h6>
                                        <p class="text-muted text-truncate">Kredi Kartı</p>
                                    </div>
                                    <div class="col-3">
                                        <h6 class="mb-0 gelirToplam">{{number_format($gelirler, 0, ',', '.')}} TL</h6>
                                        <p class="text-muted text-truncate">Toplam</p>
                                    </div>
                                </div>
                                <canvas id="gelirChart" height="150"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 giderTablosu">
                        <div class="row">
                            <div class="sol col-sm-6">
                                <ul>    
                                    @foreach($odemeTuruAll as $key => $value )
                                        @php 
                                            $colorIndex = $loop->index % 8;
                                            $renkler = [
    '#FF6384', // kırmızı-pembe
    '#36A2EB', // mavi
    '#FBC02D', // sarı
    '#4BC0C0', // turkuaz
    '#9966FF', // mor
    '#FF9F40', // turuncu
    '#8E44AD', // koyu mor
    '#2ECC71', // yeşil
    '#E67E22', // koyu turuncu
    '#3498DB', // açık mavi
    '#1ABC9C', // açık turkuaz
    '#C0392B', // koyu kırmızı
    '#27AE60'  // koyu yeşil
];

                                            $renk = $renkler[$colorIndex];
                                        @endphp
                                        <li class="gider">
                                            <div class="renk" style="background:{{$renk}}"></div>
                                            <div class="adi">{{$key}}</div>
                                            <div class="para">{{number_format($value, 0, ',', '.')}} TL</div>
                                        </li>
                                    @endforeach
                                    <li class="gider">
                                        <div class="renk" style="background: #000"></div>
                                        <div class="adi">Toplam</div>
                                        <div class="para">{{number_format($giderlerToplam, 0, ',', '.')}} TL</div>
                                    </li>
                                </ul>
                            </div>
                            <div class="sag col-sm-6">
                                <canvas id="giderArea" width="100%" height="120"></canvas>
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
        responsive: true,
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
    $('#modalDaterange').on('apply.daterangepicker', function(ev, picker) {
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        filterData(startDate, endDate);
        filterGiderData(startDate, endDate);
        filterGelirGrafik(startDate, endDate);
        filterGiderGrafik(startDate, endDate);
    });

    $('#lastYear, #lastMonth, #lastWeek, #yesterday, #today').on('click', function() {
        var buttonId = $(this).attr('id');
        var startDate, endDate;

        if (buttonId === 'lastYear') {
            startDate = moment().subtract(1, 'year').format('YYYY-MM-DD');
            endDate = moment().format('YYYY-MM-DD');
        } else if (buttonId === 'lastMonth') {
            startDate = moment().subtract(1, 'month').format('YYYY-MM-DD');
            endDate = moment().format('YYYY-MM-DD');
        } else if (buttonId === 'lastWeek') {
            startDate = moment().subtract(7, 'days').format('YYYY-MM-DD');
            endDate = moment().format('YYYY-MM-DD');
        } else if (buttonId === 'yesterday') {
            startDate = moment().subtract(1, 'days').format('YYYY-MM-DD');
            endDate = moment().format('YYYY-MM-DD');
        } else if (buttonId === 'today') {
            startDate = moment().format('YYYY-MM-DD');
            endDate = moment().format('YYYY-MM-DD');
        }

        filterData(startDate, endDate);
        filterGiderData(startDate, endDate);
        filterGelirGrafik(startDate, endDate);
        filterGiderGrafik(startDate, endDate);
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
            $(".giderTablosu .sol").html(response.html);
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
.gider {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
    padding: 5px 0;
}
.gider .renk {
    width: 15px;
    height: 15px;
    border-radius: 3px;
    margin-right: 10px;
}
.gider .adi {
    flex: 1;
    font-size: 14px;
}
.gider .para {
    font-weight: bold;
    font-size: 14px;
}
</style>

@endsection