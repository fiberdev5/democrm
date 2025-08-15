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
                    {{-- GELİR TABLOSU --}}
                    <div class="col-sm-6 gelirTablosu">
                        {{-- DEĞİŞİKLİK: align-items-center sınıfı eklendi --}}
                        <div class="row align-items-center">
                            <div class="sol col-sm-6">
                                <ul>    
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
                            <div class="sag col-sm-6">
                                <canvas id="gelirChart"></canvas>
                            </div>
                        </div>
                    </div>
                    {{-- GİDER TABLOSU --}}
                    <div class="col-sm-6 giderTablosu">
                         {{-- DEĞİŞİKLİK: align-items-center sınıfı eklendi --}}
                        <div class="row align-items-center">
                            <div class="sol col-sm-6">
                                <ul>    
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
                            <div class="sag col-sm-6">
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

{{-- SCRIPT'LERDE SADECE BİR FONKSİYON DEĞİŞTİ --}}
<script>
// Chart.js Ayarları
var ctx = document.getElementById("giderArea").getContext('2d');
var myChart = new Chart(ctx, { type: 'pie', data: { labels: {!! $odemeTuruSonuc !!}, datasets: [{ data: [{!! $giderler !!}], backgroundColor: {!! $odemeTuruRenkler !!}, hoverBorderColor: "#fff",}]}, options: { plugins: { labels: { render: 'percentage', fontColor: '#fff',}}, legend: {display: false}, responsive: true, maintainAspectRatio: true }}); // maintainAspectRatio true olabilir.

var ctx2 = document.getElementById("gelirChart").getContext('2d');
var myChart2 = new Chart(ctx2, { type: 'pie', data: { labels: [{!! $odemeSekliAll !!}], datasets: [{ data: [{{$nakit}},{{$eft}}, {{$kart}}], backgroundColor: ["#34a853","#e01010","#1a73e8"], hoverBackgroundColor: ["#34a853","#e01010","#1a73e8"], hoverBorderColor: "#fff"}]}, options: { plugins: { labels: { render: 'percentage', fontColor: '#fff',}}, legend: {display: false}, responsive: true, maintainAspectRatio: true }}); // maintainAspectRatio true olabilir.

// Daterangepicker ve filtreleme buton script'leri
$(document).ready(function () {
    $('#modalDaterange').daterangepicker({ startDate: moment(), endDate: moment(), locale: { format: 'DD-MM-YYYY', separator: ' - ', applyLabel: 'Uygula', cancelLabel: 'İptal', weekLabel: 'H', daysOfWeek: ['Pz', 'Pzt', 'Sal', 'Çrş', 'Prş', 'Cm', 'Cmt'], monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'], firstDay: 1 }});
    var today = moment();
    $('#modalDaterange').data('daterangepicker').setStartDate(today);
    $('#modalDaterange').data('daterangepicker').setEndDate(today);
    filterData(today.format('YYYY-MM-DD'), today.format('YYYY-MM-DD'));
    filterGiderData(today.format('YYYY-MM-DD'), today.format('YYYY-MM-DD'));
    filterGelirGrafik(today.format('YYYY-MM-DD'), today.format('YYYY-MM-DD'));
    filterGiderGrafik(today.format('YYYY-MM-DD'), today.format('YYYY-MM-DD'));
    $('#lastYear, #lastMonth, #lastWeek, #yesterday, #today').on('click', function() {
        var buttonId = $(this).attr('id');
        var startDate, endDate;
        if (buttonId === 'lastYear') { startDate = moment().subtract(1, 'year'); endDate = moment(); } 
        else if (buttonId === 'lastMonth') { startDate = moment().subtract(1, 'month'); endDate = moment(); } 
        else if (buttonId === 'lastWeek') { startDate = moment().subtract(7, 'days'); endDate = moment(); } 
        else if (buttonId === 'yesterday') { startDate = moment().subtract(1, 'days'); endDate = moment().subtract(1, 'days'); } 
        else if (buttonId === 'today') { startDate = moment(); endDate = moment(); }
        $('#modalDaterange').data('daterangepicker').setStartDate(startDate);
        $('#modalDaterange').data('daterangepicker').setEndDate(endDate);
        filterData(startDate.format('YYYY-MM-DD'), endDate.format('YYYY-MM-DD'));
        filterGiderData(startDate.format('YYYY-MM-DD'), endDate.format('YYYY-MM-DD'));
        filterGelirGrafik(startDate.format('YYYY-MM-DD'), endDate.format('YYYY-MM-DD'));
        filterGiderGrafik(startDate.format('YYYY-MM-DD'), endDate.format('YYYY-MM-DD'));
    });
});

// Gelir verisi ve grafiklerini getiren fonksiyonlar
function filterData(startDate, endDate) { $.ajax({ url: '/{{ $tenant_id }}/gelir-tablo/getir', type: 'POST', data: { startDate: startDate, endDate: endDate, _token: "{{ csrf_token() }}" }, success: function(response) { $(".gelirNakit").text(response.nakit + " TL"); $(".gelirEft").text(response.eft + " TL"); $(".gelirKart").text(response.kart + " TL"); $(".gelirToplam").text(response.toplam + " TL"); }, error: function(xhr, status, error) { console.error(error); } }); }
function filterGelirGrafik(startDate, endDate) { $.ajax({ url: '/{{ $tenant_id }}/gelir-grafik/getir', method: 'POST', data: { startDate: startDate, endDate: endDate, _token: "{{ csrf_token() }}" }, success: function(response) { myChart2.data.datasets[0].data = [response.nakit, response.eft, response.kart]; myChart2.update(); }, error: function(xhr, status, error) { console.error(error); } }); }

// DEĞİŞİKLİK: Gider listesi tekrar orijinal `.sol` elementine basılıyor
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

// Gider grafik fonksiyonu
function filterGiderGrafik(startDate, endDate) { $.ajax({ url: '/{{ $tenant_id }}/gider-grafik/getir', method: 'POST', data: { startDate: startDate, endDate: endDate, _token: "{{ csrf_token() }}" }, success: function(response) { if(response.giderler) { myChart.data.datasets[0].data = response.giderler.split(',').map(Number); myChart.update();}}, error: function(xhr, status, error) { console.error(error);}}); }
</script>

{{-- DEĞİŞİKLİK: Sadece temel stil kuralları kaldı --}}
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