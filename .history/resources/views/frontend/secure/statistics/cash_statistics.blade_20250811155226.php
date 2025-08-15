@extends('frontend.secure.user_master')

@section('user')
<div class="page-content servis-istatistik">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="card kasaSonuclari" style="margin-bottom: 5px;">
            <div class="card-header sayfaBaslik" style="padding:5px!important;font-weight:500;font-size:18px;">
                <span>Gelir-Gider Tablosu</span>
                <div class="searchWrap float-end">
                    <div class="btn-group ">
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
                    </div><!-- /btn-group -->
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
                                            $odemeTurSec = App\Models\PaymentType::where('tenant_id', $tenant_id)->where('tur', $key)->first();                        
                                        @endphp
                                        @if($odemeTurSec)
                                            <li class="gider">
                                                <div class="renk" style="background:{{$odemeTurSec->renk}}"></div>
                                                <div class="adi">{{$key}}</div>
                                                <div class="para">{{number_format($value, 0, ',', '.')}} TL</div>
                                            </li>
                                        @endif
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

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Chart.js global ayarları
    Chart.defaults.global.tooltips.enabled = true;
    Chart.defaults.global.legend.display = false;

    // Gider Chart'ı başlangıçta çiz
    var ctxGider = document.getElementById("giderArea").getContext('2d');
    var giderChart = new Chart(ctxGider, {
        type: 'pie',
        data: {
            labels: {!! $odemeTuruSonuc !!},
            datasets: [{
                data: [{{ $giderler }}],
                backgroundColor: {!! $odemeTuruRenkler !!},
                hoverBorderColor: "#fff",
            }],
        },
        options: {
            responsive: true,
            plugins: {
                labels: {
                    render: 'percentage',
                    fontColor: '#fff',
                }
            }
        },
    });

    // Gelir Chart'ı başlangıçta çiz
    var ctxGelir = document.getElementById("gelirChart").getContext('2d');
    var gelirChart = new Chart(ctxGelir, {
        type: 'doughnut',
        data: {
            labels: ["Nakit", "EFT/Havale", "Kredi Kartı"],
            datasets: [{
                data: [{{ $nakit }}, {{ $eft }}, {{ $kart }}],
                backgroundColor: ["#36a2eb", "#ff6384", "#ffce56"],
                hoverBorderColor: "#fff",
            }],
        },
        options: {
            responsive: true,
            plugins: {
                labels: {
                    render: 'percentage',
                    fontColor: '#fff',
                }
            }
        },
    });

    // Tarih aralığı butonları
    $('#today').click(function() {
        var today = new Date().toISOString().slice(0, 10);
        updateData(today, today);
    });

    $('#yesterday').click(function() {
        var yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        yesterday = yesterday.toISOString().slice(0, 10);
        updateData(yesterday, yesterday);
    });

    $('#lastWeek').click(function() {
        var today = new Date();
        var lastWeek = new Date();
        lastWeek.setDate(lastWeek.getDate() - 7);
        updateData(lastWeek.toISOString().slice(0, 10), today.toISOString().slice(0, 10));
    });

    $('#lastMonth').click(function() {
        var today = new Date();
        var lastMonth = new Date();
        lastMonth.setMonth(lastMonth.getMonth() - 1);
        updateData(lastMonth.toISOString().slice(0, 10), today.toISOString().slice(0, 10));
    });

    $('#lastYear').click(function() {
        var today = new Date();
        var lastYear = new Date();
        lastYear.setFullYear(lastYear.getFullYear() - 1);
        updateData(lastYear.toISOString().slice(0, 10), today.toISOString().slice(0, 10));
    });

    // AJAX ile verileri güncelleyen ana fonksiyon
    function updateData(startDate, endDate) {
        var tenant_id = "{{ $tenant_id }}";

        // Gelir verilerini güncelle
        $.ajax({
            url: "/kasa-gelir-grafik/" + tenant_id,
            type: "GET",
            data: { startDate: startDate, endDate: endDate },
            success: function(response) {
                $('.gelirNakit').text(response.nakit + ' TL');
                $('.gelirEft').text(response.eft + ' TL');
                $('.gelirKart').text(response.kart + ' TL');
                $('.gelirToplam').text(response.toplam + ' TL');
                
                // Grafik verilerini güncelle
                gelirChart.data.datasets[0].data = [response.nakit, response.eft, response.kart];
                gelirChart.update();
            }
        });

        // Gider verilerini güncelle
        $.ajax({
            url: "/kasa-gider-tablo/" + tenant_id,
            type: "GET",
            data: { startDate: startDate, endDate: endDate },
            success: function(response) {
                $('.giderTablosu .sol').html(response);
            }
        });
        
        // Gider grafik verilerini güncelle
        $.ajax({
            url: "/kasa-gider-grafik/" + tenant_id,
            type: "GET",
            data: { startDate: startDate, endDate: endDate },
            success: function(response) {
                // Burada response'dan gelen verilerle grafiği güncellemelisiniz.
                // Örnek olarak:
                // giderChart.data.datasets[0].data = response.giderler.split(',').map(Number);
                // giderChart.update();
            }
        });
    }

});
</script>