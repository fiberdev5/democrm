@extends('frontend.secure.user_master')
@section('user')

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script>

<!-- Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<!-- Date Range Picker -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> 

<div class="page-content servis-istatistik">
    <div class="container-fluid">

        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])

        <div class="card kasaSonuclari mb-1">
            <div class="card-header sayfaBaslik" style="padding:5px!important;font-weight:500;font-size:18px;">
                <span>Gelir-Gider Tablosu</span>
                <div class="searchWrap float-end">
                    <div class="btn-group">
                        <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown">
                            Filtrele <i class="mdi mdi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu p-2">
                            <label class="fw-bold">Tarih Aralığı:</label>
                            <input id="modalDaterange" class="form-control form-control-sm mb-2">

                            <div class="tarihAraligi">
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
                <div class="row">
                    {{-- GELİR TABLOSU --}}
                    <div class="col-sm-6 gelirTablosu">
                        <div class="row">
                            <div class="sol col-sm-6">
                                <ul class="gelir-gider-list">
                                    <li>
                                        <div class="renk" style="background:#4CAF50"></div>
                                        <div class="adi">Nakit</div>
                                        <div class="para gelirNakit">{{number_format($nakit, 0, ',', '.')}} TL</div>
                                    </li>
                                    <li>
                                        <div class="renk" style="background:#1a73e8"></div>
                                        <div class="adi">EFT/Havale</div>
                                        <div class="para gelirEft">{{number_format($eft, 0, ',', '.')}} TL</div>
                                    </li>
                                    <li>
                                        <div class="renk" style="background:#e01010"></div>
                                        <div class="adi">Kredi Kartı</div>
                                        <div class="para gelirKart">{{number_format($kart, 0, ',', '.')}} TL</div>
                                    </li>
                                    <li>
                                        <div class="renk" style="background:#000"></div>
                                        <div class="adi">Toplam</div>
                                        <div class="para gelirToplam">{{number_format($gelirler, 0, ',', '.')}} TL</div>
                                    </li>
                                </ul>
                            </div>
                            <div class="sag col-sm-6">
                                <canvas id="gelirChart" height="150"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- GİDER TABLOSU --}}
                    <div class="col-sm-6 giderTablosu">
                        <div class="row">
                            <div class="sol col-sm-6">
                                <ul class="gelir-gider-list">
                                    @foreach($odemeTuruAll as $key => $value)
                                        @php 
                                            $colorIndex = $loop->index % 13;
                                            $renkler = [
                                                '#E91E63','#FF5722','#FF9800','#FFC107','#8BC34A','#4CAF50',
                                                '#00BCD4','#009688','#2196F3','#3F51B5','#673AB7','#9C27B0','#F44336'
                                            ];
                                        @endphp
                                        <li>
                                            <div class="renk" style="background:{{$renkler[$colorIndex]}}"></div>
                                            <div class="adi">{{$key}}</div>
                                            <div class="para">{{number_format($value, 0, ',', '.')}} TL</div>
                                        </li>
                                    @endforeach
                                    <li>
                                        <div class="renk" style="background:#000"></div>
                                        <div class="adi">Toplam</div>
                                        <div class="para">{{number_format($giderlerToplam, 0, ',', '.')}} TL</div>
                                    </li>
                                </ul>
                            </div>
                            <div class="sag col-sm-6">
                                <canvas id="giderArea" height="150"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
.gelir-gider-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.gelir-gider-list li {
    display: flex;
    align-items: center;
    margin-bottom: 6px;
    padding: 3px 0;
}
.gelir-gider-list .renk {
    width: 14px;
    height: 14px;
    border-radius: 3px;
    margin-right: 8px;
}
.gelir-gider-list .adi {
    flex: 1;
    font-size: 14px;
}
.gelir-gider-list .para {
    font-weight: bold;
    font-size: 14px;
}
</style>

<script>
var giderChart = new Chart(document.getElementById("giderArea"), {
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

var gelirChart = new Chart(document.getElementById("gelirChart"), {
    type: 'pie',
    data: {
        labels: ["Nakit", "EFT/Havale", "Kredi Kartı"],
        datasets: [{
            data: [{{$nakit}}, {{$eft}}, {{$kart}}],
            backgroundColor: ["#4CAF50", "#1a73e8", "#e01010"],
            hoverBorderColor: "#fff"
        }],
    },
    options: {
        plugins: { labels: { render: 'percentage', fontColor: '#fff' } },
        legend: { display: false },
        responsive: true,
    }
});
</script>
@endsection
