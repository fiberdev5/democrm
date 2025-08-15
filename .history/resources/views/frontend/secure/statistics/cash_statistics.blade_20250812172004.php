@extends('frontend.secure.user_master')
@section('user')

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Chart.js ve Eklentisi -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script>

<!-- Moment.js ve Date Range Picker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> 

<div class="page-content servis-istatistik">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
        <div class="card kasaSonuclari">
            <div class="card-header sayfaBaslik" style="padding:5px!important;font-weight:500;font-size:18px;">
                <span>Kasa İstatistikleri</span>
                <div class="searchWrap float-end">
                    <div class="btn-group">
                        <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            FİLTRELE <i class="mdi mdi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu p-2" style="width: 280px;">
                            <div class="item">
                                <label class="col-form-label">Tarih Aralığı:</label>
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
            <div class="card-body">
                <div class="title-area" style="display: flex; justify-content: space-around; margin-bottom: 1rem;">
                    <h5 style="flex: 1; text-align: center;">Gelir-Gider Tablosu</h5>
                </div>
                <div class="gelir-gider-container">
                    
                    <!-- Gelir Listesi -->
                    <div class="list-container gelir-listesi">
                        <ul>
                            <li class="kalem">
                                <div class="renk" style="background:#34a853"></div>
                                <div class="adi">Nakit</div>
                                <div class="para" id="gelirNakitPara">{{number_format($nakit, 2, ',', '.')}} TL</div>
                            </li>
                            <li class="kalem">
                                <div class="renk" style="background:#e01010"></div>
                                <div class="adi">EFT/Havale</div>
                                <div class="para" id="gelirEftPara">{{number_format($eft, 2, ',', '.')}} TL</div>
                            </li>
                            <li class="kalem">
                                <div class="renk" style="background:#1a73e8"></div>
                                <div class="adi">Kredi Kartı</div>
                                <div class="para" id="gelirKartPara">{{number_format($kart, 2, ',', '.')}} TL</div>
                            </li>
                        </ul>
                    </div>

                    <!-- Gelir Grafiği -->
                    <div class="chart-container">
                        <canvas id="gelirChart"></canvas>
                    </div>

                    <!-- Gider Listesi -->
                    <div class="list-container gider-listesi">
                        <ul>    
                            @foreach($odemeTuruAll as $key => $value )
                                @php 
                                    $colorIndex = $loop->index % 13;
                                    $renkler = ['#8BC34A', '#F44336', '#FF9800', '#E91E63', '#2196F3', '#673AB7', '#009688', '#FFC107', '#FF5722', '#4CAF50', '#00BCD4', '#3F51B5', '#9C27B0'];
                                    $renk = $renkler[$colorIndex];
                                @endphp
                                <li class="kalem">
                                    <div class="renk" style="background:{{$renk}}"></div>
                                    <div class="adi">{{$key}}</div>
                                    <div class="para">{{number_format($value, 2, ',', '.')}} TL</div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    
                    <!-- Gider Grafiği -->
                    <div class="chart-container">
                        <canvas id="giderChart"></canvas>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
.gelir-gider-container {
    display: flex;
    justify-content: space-around;
    align-items: flex-start; /* Üstten hizala */
    width: 100%;
    gap: 20px; /* Sütunlar arası boşluk */
}
.list-container {
    width: 25%;
    padding: 0 10px;
}
.chart-container {
    width: 25%;
    height: 200px; /* Grafik yüksekliğini ayarla */
}
.list-container ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.kalem {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    padding: 5px 0;
    font-size: 14px;
}
.kalem .renk {
    width: 15px;
    height: 15px;
    margin-right: 10px;
    flex-shrink: 0; /* Küçülmesini engelle */
}
.kalem .adi {
    flex-grow: 1; /* Esnek büyüme */
}
.kalem .para {
    font-weight: 500;
    white-space: nowrap; /* Değerlerin alt satıra kaymasını engelle */
}
</style>

<script>
// Chart Nesnelerini Global Kapsamda Tanımla
var gelirChart, giderChart;

// Sayfa Yüklendiğinde Grafikleri Oluştur
document.addEventListener("DOMContentLoaded", function() {
    // GELİR GRAFİĞİ
    var ctxGelir = document.getElementById("gelirChart").getContext('2d');
    gelirChart = new Chart(ctxGelir, {
        type: 'pie',
        data: {
            labels: ['Nakit', 'EFT/Havale', 'Kredi Kartı'],
            datasets: [{
                data: [{{$nakit}}, {{$eft}}, {{$kart}}],
                backgroundColor: ["#34a853", "#e01010", "#1a73e8"],
                hoverBorderColor: "#fff"
            }],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            legend: { display: false },
            plugins: { labels: { render: 'percentage', fontColor: '#fff', fontSize: 14, fontStyle: 'bold' } },
        },
    });

    // GİDER GRAFİĞİ
    var ctxGider = document.getElementById("giderChart").getContext('2d');
    giderChart = new Chart(ctxGider, {
        type: 'pie',
        data: {
            labels: {!! $odemeTuruSonuc !!},
            datasets: [{
                data: [{!! $giderler !!}],
                backgroundColor: ['#8BC34A', '#F44336', '#FF9800', '#E91E63', '#2196F3', '#673AB7', '#009688', '#FFC107', '#FF5722', '#4CAF50', '#00BCD4', '#3F51B5', '#9C27B0'],
                hoverBorderColor: "#fff",
            }],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            legend: { display: false },
            plugins: { labels: { render: 'percentage', fontColor: '#fff', fontSize: 14, fontStyle: 'bold' } },
        }
    });
});
</script>

<script>
$(document).ready(function () {
    // Daterangepicker Kurulumu
    $('#modalDaterange').daterangepicker({
        locale: {
            format: 'DD-MM-YYYY', separator: ' - ', applyLabel: 'Uygula', cancelLabel: 'İptal',
            weekLabel: 'H', daysOfWeek: ['Pz', 'Pzt', 'Sal', 'Çrş', 'Prş', 'Cm', 'Cmt'],
            monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
            firstDay: 1
        }
    });

    function applyAllFilters(startDate, endDate) {
        var formattedStart = startDate.format('YYYY-MM-DD');
        var formattedEnd = endDate.format('YYYY-MM-DD');
        
        filterGelirData(formattedStart, formattedEnd);
        filterGiderData(formattedStart, formattedEnd);
        filterGelirGrafik(formattedStart, formattedEnd);
        filterGiderGrafik(formattedStart, formattedEnd);
    }
    
    // Hazır Butonlar İçin Olay Dinleyici
    $('#lastYear, #lastMonth, #lastWeek, #yesterday, #today').on('click', function() {
        var buttonId = $(this).attr('id');
        var startDate, endDate = moment();

        if (buttonId === 'lastYear') startDate = moment().subtract(1, 'year');
        else if (buttonId === 'lastMonth') startDate = moment().subtract(1, 'month');
        else if (buttonId === 'lastWeek') startDate = moment().subtract(7, 'days');
        else if (buttonId === 'yesterday') {
            startDate = moment().subtract(1, 'days');
            endDate = moment().subtract(1, 'days');
        } 
        else if (buttonId === 'today') startDate = moment();

        $('#modalDaterange').data('daterangepicker').setStartDate(startDate);
        $('#modalDaterange').data('daterangepicker').setEndDate(endDate);
        applyAllFilters(startDate, endDate);
    });

    // Daterangepicker'dan tarih seçildiğinde filtrele
    $('#modalDaterange').on('apply.daterangepicker', function(ev, picker) {
        applyAllFilters(picker.startDate, picker.endDate);
    });
    
    // Sayfa ilk yüklendiğinde bugünü ayarla ve filtrele
    var today = moment();
    $('#modalDaterange').data('daterangepicker').setStartDate(today);
    $('#modalDaterange').data('daterangepicker').setEndDate(today);
    applyAllFilters(today, today);
});

//--- AJAX FONKSİYONLARI (DEĞİŞTİRİLMEDİ, SADECE SEÇİCİLER GÜNCELLENDİ) ---

function filterGelirData(startDate, endDate) {
    $.ajax({
        url: '/{{ $tenant_id }}/gelir-tablo/getir',
        type: 'POST',
        data: { startDate: startDate, endDate: endDate, _token: "{{ csrf_token() }}" },
        success: function(response) {
            // Controller'dan gelen formatlı metni doğrudan yazdır
            $("#gelirNakitPara").text(response.nakit + " TL");
            $("#gelirEftPara").text(response.eft + " TL");
            $("#gelirKartPara").text(response.kart + " TL");
            // Toplam satırı görselde olmadığı için kaldırıldı
        },
        error: function(xhr, status, error) { console.error(error); }
    });
}

function filterGiderData(startDate, endDate) {
    $.ajax({
        url: '/{{ $tenant_id }}/gider-tablo/getir',
        method: 'POST',
        data: { startDate: startDate, endDate: endDate, _token: "{{ csrf_token() }}" },
        success: function(response) {
            // Controller'dan gelen hazır HTML'i, gider listesinin <ul> etiketinin içine yerleştir
            $(".gider-listesi ul").html(response.html);
        },
        error: function(xhr, status, error) { console.error(error); }
    });
}

function filterGelirGrafik(startDate, endDate) {
    $.ajax({
        url: '/{{ $tenant_id }}/gelir-grafik/getir',
        method: 'POST',
        data: { startDate: startDate, endDate: endDate, _token: "{{ csrf_token() }}" },
        success: function(response) {
            // Controller'dan gelen sayısal verilerle grafiği güncelle
            gelirChart.data.datasets[0].data = [response.nakit, response.eft, response.kart];
            gelirChart.update();
        },
        error: function(xhr, status, error) { console.error(error); }
    });
}

function filterGiderGrafik(startDate, endDate) {
    $.ajax({
        url: '/{{ $tenant_id }}/gider-grafik/getir',
        method: 'POST',
        data: { startDate: startDate, endDate: endDate, _token: "{{ csrf_token() }}" },
        success: function(response) {
            // Controller'dan gelen virgüllü metni diziye çevirerek grafiği güncelle
            if(response.giderler) {
                giderChart.data.datasets[0].data = response.giderler.split(',').map(Number);
                giderChart.update();
            }
        },
        error: function(xhr, status, error) { console.error(error); }
    });
}
</script>

@endsection