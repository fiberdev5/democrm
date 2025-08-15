@extends('frontend.secure.user_master')
@section('user')

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Gerekli Kütüphaneler -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script>
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
                        <div class="dropdown-menu p-2">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Tarih:</label>
                                <div class="col-sm-8">
                                    <input id="modalDaterange" class="form-control form-control-sm">          
                                    <div class="tarihAraligi mt-2">
                                        <button id="today" class="btn btn-sm btn-secondary">Bugün</button>
                                        <button id="yesterday" class="btn btn-sm btn-secondary">Dün</button>
                                        <button id="lastWeek" class="btn btn-sm btn-secondary">Son 7 Gün</button>
                                        <button id="lastMonth" class="btn btn-sm btn-secondary">Son 1 Ay</button>
                                        <button id="lastYear" class="btn btn-sm btn-secondary">Son 1 Yıl</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="card-body">
                {{-- DEĞİŞİKLİK: Ana yapı iki dikey sütuna bölündü --}}
                <div class="row">
                    
                    <!-- 1. SÜTUN: GELİR BÖLÜMÜ -->
                    <div class="col-lg-6 border-end">
                        <h5 class="text-center text-success mb-4">Gelir</h5>
                        {{-- Gelir bölümü için iç içe geçmiş satır --}}
                        <div class="row align-items-center">
                            <!-- Gelir Bilgileri (Sol) -->
                            <div class="col-md-6">
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <h6 class="mb-0 gelirNakit">{{number_format($nakit, 0, ',', '.')}} TL</h6>
                                        <p class="text-muted text-truncate mb-0">Nakit</p>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <h6 class="mb-0 gelirEft">{{number_format($eft, 0, ',', '.')}} TL</h6>
                                        <p class="text-muted text-truncate mb-0">EFT/Havale</p>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <h6 class="mb-0 gelirKart">{{number_format($kart, 0, ',', '.')}} TL</h6>
                                        <p class="text-muted text-truncate mb-0">Kredi Kartı</p>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <h6 class="mb-0 gelirToplam">{{number_format($gelirler, 0, ',', '.')}} TL</h6>
                                        <p class="text-muted text-truncate mb-0">Toplam</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Gelir Grafiği (Sağ) -->
                            <div class="col-md-6">
                                <div class="chart-container">
                                    <canvas id="gelirChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. SÜTUN: GİDER BÖLÜMÜ -->
                    <div class="col-lg-6">
                        <h5 class="text-center text-danger mb-4">Gider</h5>
                         {{-- Gider bölümü için iç içe geçmiş satır --}}
                        <div class="row align-items-center">
                             <!-- Gider Bilgileri (Sol) -->
                            <div class="col-md-6">
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
                             <!-- Gider Grafiği (Sağ) -->
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
    </div>
</div>

<script>
// Chart.js Scriptleri
var myChartGider = new Chart(document.getElementById("giderArea").getContext('2d'), {
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
        plugins: { labels: { render: 'percentage', fontColor: '#fff', precision: 0 } },
        legend: { display: false },
        responsive: true,
        maintainAspectRatio: false,
    }
});

var myChartGelir = new Chart(document.getElementById("gelirChart").getContext('2d'), {
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
        plugins: { labels: { render: 'percentage', fontColor: '#fff', precision: 0 } },
        legend: { display: false }, // Legend'ı kapattım, bilgiler yanda zaten var
        responsive: true,
        maintainAspectRatio: false,
    },
});

// Daterangepicker ve AJAX Scriptleri (Bu kısımda değişiklik yok, aynen kalabilir)
// ...
</script>

<style>
.chart-container {
    position: relative;
    /* Yüksekliği 250px olarak ayarladım, tasarımınıza göre değiştirebilirsiniz */
    height: 250px; 
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
    padding: 2px 0; /* Boşluğu azalttım */
}
.gider .renk {
    width: 13px;
    height: 13px;
    border-radius: 3px;
    margin-right: 8px;
    flex-shrink: 0;
}
.gider .adi {
    flex: 1;
    font-size: 13px; /* Yazıyı biraz küçülttüm */
}
.gider .para {
    font-weight: 500; /* Kalınlığı azalttım */
    font-size: 13px;
}
.border-end{
    border-right: 1px solid #dee2e6!important;
}

/* Küçük ekranlar için düzenleme */
@media (max-width: 991.98px) {
    .border-end {
        border-right: none !important; /* Dikey çizgiyi kaldır */
        border-bottom: 1px solid #dee2e6!important; /* Altına yatay çizgi ekle */
        margin-bottom: 1.5rem; /* İki bölüm arasına boşluk koy */
        padding-bottom: 1.5rem; /* İçerikle çizgi arasına boşluk koy */
    }
}
</style>

@endsection