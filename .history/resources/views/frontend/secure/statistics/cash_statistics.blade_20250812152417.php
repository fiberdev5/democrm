@extends('frontend.secure.user_master')
@section('user')

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Gerekli Kütüphaneler -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> 

<div class="page-content">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
        <div class="card mb-3 pageDetail istatistikSonuclarPage kasaSonuclari">
            <div class="card-header sayfaBaslik">Kasa İstatistikleri</div>
            <div class="card-body">

                <div class="card">
                    <div class="card-header">
                        <span>Gelir-Gider Tablosu</span>
                        <div class="dropdown float-end">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">FİLTRELE</button>
                            <div class="dropdown-menu p-3" style="width: 300px;">
                                <div class="row form-group mb-2">
                                    <label class="col-lg-4 col-form-label">Tarih Aralığı</label>
                                    <div class="col-lg-8">
                                        <input id="modalDaterange" class="form-control" style="z-index: 9999;">
                                    </div>
                                </div>
                                <div class="tarihAraliklari text-center mb-2">
                                    <button type="button" class="btn btn-sm btn-secondary tarihDegistirBtn" data-range="month">Son 1 Ay</button>
                                    <button type="button" class="btn btn-sm btn-secondary tarihDegistirBtn" data-range="week">Son 7 Gün</button>
                                    <button type="button" class="btn btn-sm btn-secondary tarihDegistirBtn" data-range="yesterday">Dün</button>
                                    <button type="button" class="btn btn-sm btn-secondary tarihDegistirBtn" data-range="today">Bugün</button>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm w-100" id="filterButton">ARA</button>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Gelir Tablosu -->
                            <div class="col-lg-6 gelirTablosu">
                                <div class="row">
                                    <div class="sol col-sm-6">
                                        <ul class="summary-list">
                                            <li class="summary-item">
                                                <div class="renk" style="background:#34a853"></div>
                                                <div class="adi">Nakit</div>
                                                <div class="para gelirNakit">{{ number_format($nakit, 2, ',', '.') }} TL</div>
                                            </li>
                                            <li class="summary-item">
                                                <div class="renk" style="background:#e01010"></div>
                                                <div class="adi">EFT/Havale</div>
                                                <div class="para gelirEft">{{ number_format($eft, 2, ',', '.') }} TL</div>
                                            </li>
                                            <li class="summary-item">
                                                <div class="renk" style="background:#1a73e8"></div>
                                                <div class="adi">Kredi Kartı</div>
                                                <div class="para gelirKart">{{ number_format($kart, 2, ',', '.') }} TL</div>
                                            </li>
                                            <li class="summary-item">
                                                <div class="renk" style="background:#000"></div>
                                                <div class="adi fw-bold">Toplam</div>
                                                <div class="para fw-bold gelirToplam">{{ number_format($gelirler, 2, ',', '.') }} TL</div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="sag col-sm-6">
                                        <canvas id="gelirChart" height="300"></canvas>
                                    </div>
                                </div>
                            </div>
                            <!-- Gider Tablosu -->
                            <div class="col-lg-6 giderTablosu">
                                <div class="row">
                                    <div class="sol col-sm-6" id="giderListesiWrapper">
                                         <ul class="summary-list">    
                                            @php
                                            $renkler = ['#E91E63', '#FF5722', '#FF9800', '#FFC107', '#8BC34A', '#4CAF50', '#00BCD4', '#009688', '#2196F3', '#3F51B5', '#673AB7', '#9C27B0', '#F44336'];
                                            @endphp
                                            @foreach($odemeTuruAll as $key => $value)
                                                <li class="summary-item">
                                                    <div class="renk" style="background:{{ $renkler[$loop->index % count($renkler)] }}"></div>
                                                    <div class="adi">{{$key}}</div>
                                                    <div class="para">{{number_format($value, 2, ',', '.')}} TL</div>
                                                </li>
                                            @endforeach
                                            <li class="summary-item">
                                                <div class="renk" style="background: #000"></div>
                                                <div class="adi fw-bold">Toplam</div>
                                                <div class="para fw-bold">{{number_format($giderlerToplam, 2, ',', '.')}} TL</div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="sag col-sm-6">
                                        <canvas id="giderArea" height="300"></canvas>
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

<style>
.summary-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.summary-item {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
    padding: 5px 0;
    font-size: 14px;
}
.summary-item .renk {
    width: 15px;
    height: 15px;
    border-radius: 3px;
    margin-right: 10px;
    flex-shrink: 0;
}
.summary-item .adi {
    flex-grow: 1;
}
.summary-item .para {
    font-weight: bold;
    white-space: nowrap;
    padding-left: 5px;
}
.dropdown-menu {
    --bs-dropdown-min-width: 300px;
}
</style>

<script>
// Chart.js Global Ayarları (Tüm grafikler için ortak)
Chart.defaults.global.plugins.labels.render = 'percentage';
Chart.defaults.global.plugins.labels.fontColor = '#fff';
Chart.defaults.global.legend.display = false;
Chart.defaults.global.responsive = true;
Chart.defaults.global.maintainAspectRatio = false;

// --- CHART OLUŞTURMA ---
// Gider Grafiği
var ctxGider = document.getElementById("giderArea").getContext('2d');
var giderChart = new Chart(ctxGider, {
    type: 'pie',
    data: {
        labels: {!! $odemeTuruSonuc !!},
        datasets: [{
            data: [{!! $giderler !!}],
            backgroundColor: {!! $odemeTuruRenkler !!},
            borderWidth: 0,
        }],
    }
});

// Gelir Grafiği
var ctxGelir = document.getElementById("gelirChart").getContext('2d');
var gelirChart = new Chart(ctxGelir, {
    type: 'pie',
    data: {
        labels: [{!! $odemeSekliAll !!}],
        datasets: [{
            data: [{{$nakit}}, {{$eft}}, {{$kart}}],
            backgroundColor: ["#34a853","#e01010","#1a73e8"],
            borderWidth: 0,
        }],
    }
});


// --- SAYFA YÜKLENİNCE VE FİLTRELEME İŞLEMLERİ ---
$(document).ready(function () {
    var daterangeInput = $('#modalDaterange');

    // Daterangepicker Ayarları
    daterangeInput.daterangepicker({
        startDate: moment(),
        endDate: moment(),
        locale: {
            format: 'DD/MM/YYYY',
            separator: ' - ',
            applyLabel: 'Uygula',
            cancelLabel: 'İptal',
            weekLabel: 'H',
            daysOfWeek: ['Pz', 'Pzt', 'Sal', 'Çrş', 'Prş', 'Cm', 'Cmt'],
            monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
            firstDay: 1
        }
    });

    // Hızlı Tarih Değiştirme Butonları (AJAX çağırmaz, sadece tarihi ayarlar)
    $('.tarihDegistirBtn').on('click', function() {
        var range = $(this).data('range');
        var startDate, endDate;

        if (range === 'today') {
            startDate = moment();
            endDate = moment();
        } else if (range === 'yesterday') {
            startDate = moment().subtract(1, 'days');
            endDate = moment().subtract(1, 'days');
        } else if (range === 'week') {
            startDate = moment().subtract(6, 'days');
            endDate = moment();
        } else if (range === 'month') {
            startDate = moment().subtract(1, 'month');
            endDate = moment();
        }
        
        daterangeInput.data('daterangepicker').setStartDate(startDate);
        daterangeInput.data('daterangepicker').setEndDate(endDate);
    });

    // "ARA" Butonuna Tıklandığında AJAX Çağrılarını Yap
    $("#filterButton").on('click', function() {
        var dates = daterangeInput.data('daterangepicker');
        var startDate = dates.startDate.format('YYYY-MM-DD');
        var endDate = dates.endDate.format('YYYY-MM-DD');
        
        // Tüm verileri bu fonksiyonla getir
        fetchKasaVerileri(startDate, endDate);

        // Bootstrap 5+ için dropdown menüyü manuel kapat
        $(this).closest('.dropdown').find('.dropdown-toggle').dropdown('hide');
    });

    // Dropdown menüsünün içindeki elementlere tıklanınca kapanmasını engelle
    $('.dropdown-menu').on('click', function (e) {
        e.stopPropagation();
    });

    // Sayfa ilk yüklendiğinde bugünün verilerini getir
    var todayStart = moment().format('YYYY-MM-DD');
    var todayEnd = moment().format('YYYY-MM-DD');
    fetchKasaVerileri(todayStart, todayEnd);
});

// --- AJAX FONKSİYONLARI ---
function fetchKasaVerileri(startDate, endDate) {
    const tenant_id = '{{ $tenant_id }}';
    const token = "{{ csrf_token() }}";

    // 1. Gelir Tablosu Listesini Güncelle
    $.ajax({
        url: `/${tenant_id}/gelir-tablo/getir`,
        type: 'POST',
        data: { startDate: startDate, endDate: endDate, _token: token },
        success: function(response) {
            $(".gelirNakit").text(response.nakit + " TL");
            $(".gelirEft").text(response.eft + " TL");
            $(".gelirKart").text(response.kart + " TL");
            $(".gelirToplam").text(response.toplam + " TL");
        }
    });

    // 2. Gider Tablosu Listesini (HTML olarak) Güncelle
    $.ajax({
        url: `/${tenant_id}/gider-tablo/getir`,
        method: 'POST',
        data: { startDate: startDate, endDate: endDate, _token: token },
        success: function(response) {
            $("#giderListesiWrapper").html(response.html);
        }
    });

    // 3. Gelir Grafiğini Güncelle
    $.ajax({
        url: `/${tenant_id}/gelir-grafik/getir`,
        method: 'POST',
        data: { startDate: startDate, endDate: endDate, _token: token },
        success: function(response) {
            gelirChart.data.datasets[0].data = [response.nakit, response.eft, response.kart];
            gelirChart.update();
        }
    });

    // 4. Gider Grafiğini Güncelle
    $.ajax({
        url: `/${tenant_id}/gider-grafik/getir`,
        method: 'POST',
        data: { startDate: startDate, endDate: endDate, _token: token },
        success: function(response) {
            if(response.giderler) {
                giderChart.data.datasets[0].data = response.giderler.split(',').map(Number);
                giderChart.update();
            }
        }
    });
}
</script>

@endsection