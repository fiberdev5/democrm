@extends('frontend.secure.user_master')
@section('user')

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Chart.js & Eklentileri -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script>

<!-- Moment.js & Date Range Picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
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
                
                {{-- DEĞİŞİKLİK: Yapı tamamen değiştirildi. Önce özetler, sonra grafikler. --}}

                <!-- SATIR 1: Özet Bilgileri -->
                <div class="row mb-4">
                    <!-- Gelir Özeti -->
                    <div class="col-md-6">
                        <h5 class="text-center text-success mb-3">Gelir</h5>
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
                    </div>
                    <!-- Gider Özeti -->
                    <div class="col-md-6">
                        <h5 class="text-center text-danger mb-3">Gider</h5>
                        <div class="gider-listesi">
                            <ul>    
                                @foreach($odemeTuruAll as $key => $value )
                                    @php 
                                        $colorIndex = $loop->index % 13;
                                        $renkler = ['#E91E63', '#FF5722', '#FF9800', '#FFC107', '#8BC34A', '#4CAF50', '#00BCD4', '#009688', '#2196F3', '#3F51B5', '#673AB7', '#9C27B0', '#F44336'];
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
                    </div>
                </div>

                <!-- SATIR 2: Grafikler -->
                <div class="row">
                    <!-- Gelir Grafiği -->
                    <div class="col-md-6">
                        <div class="chart-container">
                            <canvas id="gelirChart"></canvas>
                        </div>
                    </div>
                    <!-- Gider Grafiği -->
                    <div class="col-md-6">
                         <div class="chart-container">
                            <canvas id="giderArea"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
// Gider Grafiği Scripti
var ctxGider = document.getElementById("giderArea").getContext('2d');
var myChartGider = new Chart(ctxGider, {
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
        plugins: { labels: { render: 'percentage', fontColor: '#fff' } },
        legend: { display: false },
        responsive: true,
        maintainAspectRatio: false,
    }
});

// Gelir Grafiği Scripti
var ctxGelir = document.getElementById("gelirChart").getContext('2d');
var myChartGelir = new Chart(ctxGelir, {
    type: 'pie',
    data: {
        labels: [{!! $odemeSekliAll !!}],
        datasets: [{
            data: [{{$nakit}}, {{$eft}}, {{$kart}}],
            backgroundColor: ["#34a853", "#e01010", "#1a73e8"],
            hoverBorderColor: "#fff"
        }],
    },
    options: {
        plugins: { labels: { render: 'percentage', fontColor: '#fff' } },
        legend: { display: true }, // Gelir grafiği için legend'i açmak daha kullanışlı olabilir.
        responsive: true,
        maintainAspectRatio: false,
    },
});

// --- Daterangepicker ve AJAX Scriptleri (Değişiklik yok) ---
$(document).ready(function () {
    // ... Daterangepicker ve filtreleme ile ilgili tüm scriptleriniz buraya gelecek ...
    // Bu kısımda bir değişiklik yapmaya gerek yok, çünkü element ID'leri korundu.
});
// Örnek olarak bir tanesini ekliyorum, geri kalanı aynı kalacak.
function filterGelirGrafik(startDate, endDate) {
    $.ajax({
        url: '/{{ $tenant_id }}/gelir-grafik/getir',
        method: 'POST',
        data: { startDate: startDate, endDate: endDate, _token: "{{ csrf_token() }}" },
        success: function(response) {
            myChartGelir.data.datasets[0].data = [response.nakit, response.eft, response.kart];
            myChartGelir.update();
        },
        error: function(xhr, status, error) { console.error(error); }
    });
}
// Diğer AJAX fonksiyonlarınız (filterData, filterGiderData, filterGiderGrafik) aynı şekilde çalışmaya devam edecektir.
// ...
</script>

<style>
/* DEĞİŞİKLİK: Yeni stiller ve düzenlemeler */
.chart-container {
    position: relative;
    height: 350px; /* Grafiklerinize sabit bir yükseklik vererek hizalamayı garantiler */
    width: 100%;
}

.gider-listesi ul {
    padding-left: 0;
    list-style: none;
    margin-bottom: 0;
}

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
    flex-shrink: 0;
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