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

<style>
    .sayfaBaslik {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 12px !important;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    .sayfaBaslik span {
        font-weight: 600;
        font-size: 18px;
    }
    .searchWrap {
        display: flex;
        align-items: center;
    }
    .tarihAraligi button {
        margin: 2px 0;
        font-size: 12px;
        padding: 3px 6px;
    }
    /* Gider listesi */
    .gider {
        display: flex;
        align-items: center;
        margin-bottom: 6px;
        padding: 4px 0;
        border-bottom: 1px dashed #e9ecef;
    }
    .gider:last-child {
        border-bottom: none;
    }
    .gider .renk {
        width: 14px;
        height: 14px;
        border-radius: 3px;
        margin-right: 8px;
    }
    .gider .adi {
        flex: 1;
        font-size: 13px;
        color: #495057;
    }
    .gider .para {
        font-weight: bold;
        font-size: 13px;
        white-space: nowrap;
    }
    /* Grafik yüksekliği */
    #gelirChart, #giderArea {
        max-height: 220px;
    }
    /* Responsive düzen */
    @media (max-width: 768px) {
        .gelirTablosu, .giderTablosu {
            margin-bottom: 20px;
        }
    }
</style>

<div class="page-content servis-istatistik">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
        <div class="card kasaSonuclari mb-3">
            <div class="card-header sayfaBaslik">
                <span>Gelir-Gider Tablosu</span>
                <div class="searchWrap">
                    <div class="btn-group">
                        <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown">
                            Filtrele <i class="mdi mdi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu p-3" style="min-width: 250px;">
                            <div class="mb-2">
                                <label class="form-label mb-1">Tarih Aralığı:</label>
                                <input id="modalDaterange" class="form-control form-control-sm mb-2">
                            </div>
                            <div class="tarihAraligi d-flex flex-wrap gap-1">
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
            <div class="card-body">
                <div class="row g-4">
                    <!-- Gelir Tablosu -->
                    <div class="col-lg-6 gelirTablosu">
                        <div class="row text-center mb-3">
                            <div class="col-3">
                                <h6 class="mb-0 gelirNakit">{{number_format($nakit, 0, ',', '.')}} TL</h6>
                                <small class="text-muted">Nakit</small>
                            </div>
                            <div class="col-3">
                                <h6 class="mb-0 gelirEft">{{number_format($eft, 0, ',', '.')}} TL</h6>
                                <small class="text-muted">EFT/Havale</small>
                            </div>
                            <div class="col-3">
                                <h6 class="mb-0 gelirKart">{{number_format($kart, 0, ',', '.')}} TL</h6>
                                <small class="text-muted">Kredi Kartı</small>
                            </div>
                            <div class="col-3">
                                <h6 class="mb-0 gelirToplam">{{number_format($gelirler, 0, ',', '.')}} TL</h6>
                                <small class="text-muted">Toplam</small>
                            </div>
                        </div>
                        <canvas id="gelirChart"></canvas>
                    </div>

                    <!-- Gider Tablosu -->
                    <div class="col-lg-6 giderTablosu">
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled m-0">
                                    @foreach($odemeTuruAll as $key => $value )
                                        @php 
                                            $colorIndex = $loop->index % 13;
                                            $renkler = ['#E91E63','#FF5722','#FF9800','#FFC107','#8BC34A','#4CAF50','#00BCD4','#009688','#2196F3','#3F51B5','#673AB7','#9C27B0','#F44336'];
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
                            <div class="col-md-6">
                                <canvas id="giderArea"></canvas>
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



@endsection