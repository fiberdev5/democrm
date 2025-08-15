@extends('frontend.secure.user_master')
@section('user')

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Gerekli JS Kütüphaneleri -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> 
<script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script>

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
                        <div class="dropdown-menu p-2">
                            <div class="item">
                                <label class="form-label">Tarih Aralığı:</label>
                                <input id="modalDaterange" class="form-control tarih-araligi" style="z-index: 9999;">          
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
            <div class="card-body">
                {{-- DEĞİŞİKLİK: TÜM İÇERİK TEK BİR SATIRA ALINDI VE DİKEY ORTALANDI --}}
                <div class="row align-items-center">

                    <!-- SÜTUN 1: GELİR LİSTESİ -->
                    <div class="col-lg-2 col-md-6">
                        <ul class="list-unstyled mb-0">    
                            <li class="gider">
                                <div class="renk" style="background:#34a853"></div>
                                <div class="adi">Nakit</div>
                                <div class="para gelirNakit">{{number_format($nakit, 0, ',', '.')}} TL</div>
                            </li>
                             <li class="gider">
                                <div class="renk" style="background:#e01010"></div>
                                <div class="adi">EFT/Havale</div>
                                <div class="para gelirEft">{{number_format($eft, 0, ',', '.')}} TL</div>
                            </li>
                             <li class="gider">
                                <div class="renk" style="background:#1a73e8"></div>
                                <div class="adi">Kredi Kartı</div>
                                <div class="para gelirKart">{{number_format($kart, 0, ',', '.')}} TL</div>
                            </li>
                            <li class="gider">
                                <div class="renk" style="background: #000"></div>
                                <div class="adi">Toplam</div>
                                <div class="para gelirToplam">{{number_format($gelirler, 0, ',', '.')}} TL</div>
                            </li>
                        </ul>
                    </div>

                    <!-- SÜTUN 2: GELİR GRAFİĞİ -->
                    <div class="col-lg-4 col-md-6">
                        <canvas id="gelirChart"></canvas>
                    </div>

                    <!-- SÜTUN 3: GİDER LİSTESİ (AJAX İÇİN ID EKLENDİ) -->
                    <div class="col-lg-2 col-md-6" id="gider-list-container">
                        <ul class="list-unstyled mb-0">    
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

                    <!-- SÜTUN 4: GİDER GRAFİĞİ -->
                    <div class="col-lg-4 col-md-6">
                        <canvas id="giderArea"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Chart.js Kurulumları
var ctxGider = document.getElementById("giderArea").getContext('2d');
var chartGider = new Chart(ctxGider, { type: 'pie', data: { labels: {!! $odemeTuruSonuc !!}, datasets: [{ data: [{!! $giderler !!}], backgroundColor: {!! $odemeTuruRenkler !!}, hoverBorderColor: "#fff" }]}, options: { plugins: { labels: { render: 'percentage', fontColor: '#fff' }}, legend: { display: false }, responsive: true, maintainAspectRatio: true }});

var ctxGelir = document.getElementById("gelirChart").getContext('2d');
var chartGelir = new Chart(ctxGelir, { type: 'pie', data: { labels: [{!! $odemeSekliAll !!}], datasets: [{ data: [{{$nakit}}, {{$eft}}, {{$kart}}], backgroundColor: ["#34a853", "#e01010", "#1a73e8"], hoverBorderColor: "#fff" }]}, options: { plugins: { labels: { render: 'percentage', fontColor: '#fff' }}, legend: { display: false }, responsive: true, maintainAspectRatio: true }});

// Filtreleme Fonksiyonları
$(document).ready(function () {
    // Daterangepicker Kurulumu
    $('#modalDaterange').daterangepicker({ startDate: moment(), endDate: moment(), locale: { format: 'DD-MM-YYYY', separator: ' - ', applyLabel: 'Uygula', cancelLabel: 'İptal', weekLabel: 'H', daysOfWeek: ['Pz', 'Pzt', 'Sal', 'Çrş', 'Prş', 'Cm', 'Cmt'], monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'], firstDay: 1 }});

    function applyFilter(startDate, endDate) {
        filterData(startDate.format('YYYY-MM-DD'), endDate.format('YYYY-MM-DD'));
        filterGiderData(startDate.format('YYYY-MM-DD'), endDate.format('YYYY-MM-DD'));
        filterGelirGrafik(startDate.format('YYYY-MM-DD'), endDate.format('YYYY-MM-DD'));
        filterGiderGrafik(startDate.format('YYYY-MM-DD'), endDate.format('YYYY-MM-DD'));
    }

    // Tarih aralığı butonları
    $('#lastYear, #lastMonth, #lastWeek, #yesterday, #today').on('click', function() {
        var buttonId = $(this).attr('id');
        var startDate, endDate = moment();
        if (buttonId === 'lastYear') { startDate = moment().subtract(1, 'year'); } 
        else if (buttonId === 'lastMonth') { startDate = moment().subtract(1, 'month'); } 
        else if (buttonId === 'lastWeek') { startDate = moment().subtract(7, 'days'); } 
        else if (buttonId === 'yesterday') { startDate = moment().subtract(1, 'days'); endDate = moment().subtract(1, 'days'); } 
        else if (buttonId === 'today') { startDate = moment(); }
        $('#modalDaterange').data('daterangepicker').setStartDate(startDate);
        $('#modalDaterange').data('daterangepicker').setEndDate(endDate);
        applyFilter(startDate, endDate);
    });
    
    // Daterangepicker'dan seçim yapıldığında
    $('#modalDaterange').on('apply.daterangepicker', function(ev, picker) {
        applyFilter(picker.startDate, picker.endDate);
    });

    // Sayfa ilk yüklendiğinde bugünün verilerini getir
    applyFilter(moment(), moment());
});

// AJAX Çağrıları
function filterData(startDate, endDate) {
    $.ajax({
        url: '/{{ $tenant_id }}/gelir-tablo/getir',
        type: 'POST',
        data: { startDate: startDate, endDate: endDate, _token: "{{ csrf_token() }}" },
        success: function(response) {
            $(".gelirNakit").text(response.nakit + " TL");
            $(".gelirEft").text(response.eft + " TL");
            $(".gelirKart").text(response.kart + " TL");
            $(".gelirToplam").text(response.toplam + " TL");
        },
        error: function(xhr) { console.error("Gelir Tablosu Getirilemedi:", xhr); }
    });
}

function filterGiderData(startDate, endDate) {
    $.ajax({
        url: '/{{ $tenant_id }}/gider-tablo/getir',
        method: 'POST',
        data: { startDate: startDate, endDate: endDate, _token: "{{ csrf_token() }}" },
        success: function(response) {
            // DEĞİŞİKLİK: Gider listesi artık #gider-list-container içine yükleniyor.
            $("#gider-list-container").html(response.html);
        },
        error: function(xhr) { console.error("Gider Tablosu Getirilemedi:", xhr); }
    });
}

function filterGelirGrafik(startDate, endDate) {
    $.ajax({
        url: '/{{ $tenant_id }}/gelir-grafik/getir',
        method: 'POST',
        data: { startDate: startDate, endDate: endDate, _token: "{{ csrf_token() }}" },
        success: function(response) {
            chartGelir.data.datasets[0].data = [response.nakit, response.eft, response.kart];
            chartGelir.update();
        },
        error: function(xhr) { console.error("Gelir Grafiği Getirilemedi:", xhr); }
    });
}

function filterGiderGrafik(startDate, endDate) {
    $.ajax({
        url: '/{{ $tenant_id }}/gider-grafik/getir',
        method: 'POST',
        data: { startDate: startDate, endDate: endDate, _token: "{{ csrf_token() }}" },
        success: function(response) {
            if(response.giderler) {
                chartGider.data.datasets[0].data = response.giderler.split(',').map(Number);
                chartGider.update();
            }
        },
        error: function(xhr) { console.error("Gider Grafiği Getirilemedi:", xhr); }
    });
}
</script>

<style>
/* Stil kodları basitleştirildi */
.gider {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
    padding: 5px 0;
    border-bottom: 1px dotted #eee;
}
.gider:last-child {
    border-bottom: none;
}
.gider .renk {
    width: 12px;
    height: 12px;
    border-radius: 3px;
    margin-right: 10px;
    flex-shrink: 0;
}
.gider .adi {
    flex-grow: 1;
    font-size: 10px;
}
.gider .para {
    font-weight: bold;
    font-size: 14px;
    white-space: nowrap;
    padding-left: 10px;
}
.list-unstyled {
    padding-left: 0;
    list-style: none;
}
</style>

@endsection