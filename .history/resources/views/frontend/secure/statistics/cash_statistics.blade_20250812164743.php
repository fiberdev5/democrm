@extends('frontend.secure.user_master')
@section('user')

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Gerekli Kütüphaneler -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> 
<script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script>

<div class="page-content servis-istatistik">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
        <div class="card kasaSonuclari">
            <div class="card-header sayfaBaslik" style="padding: .75rem 1.25rem; font-weight: 500; font-size: 18px; background-color: transparent; border-bottom: 1px solid rgba(0,0,0,.125);">
                <div class="d-flex justify-content-between align-items-center">
                    <span>Gelir-Gider Tablosu</span>
                    <div class="searchWrap">
                        <div class="btn-group">
                            <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Filtrele <i class="mdi mdi-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-2" style="width: 280px;">
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Tarih:</label>
                                    <div class="col-sm-8">
                                        <input id="modalDaterange" class="form-control form-control-sm">          
                                        <div class="tarihAraligi mt-2">
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
                    <!-- GELİR BÖLÜMÜ -->
                    <div class="col-md-6 border-end">
                        <div class="row align-items-center h-100">
                            <!-- Liste için Sütun -->
                            <div class="col-md-5">
                                <ul class="list-unstyled mb-0" id="gelirListesi">
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
                            <!-- Grafik için Sütun -->
                            <div class="col-md-7">
                                <canvas id="gelirChart" style="min-height: 250px; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- GİDER BÖLÜMÜ -->
                    <div class="col-md-6">
                        <div class="row align-items-center h-100">
                            <!-- Liste için Sütun -->
                            <div class="col-md-5">
                                <ul class="list-unstyled mb-0" id="giderListesi">    
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
                            <!-- Grafik için Sütun -->
                            <div class="col-md-7">
                                <canvas id="giderArea" style="min-height: 250px; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Gider Grafiği
var ctxGider = document.getElementById("giderArea").getContext('2d');
var giderChart = new Chart(ctxGider, {
    type: 'pie',
    data: {
        labels: {!! $odemeTuruSonuc !!},
        datasets: [{ data: [{!! $giderler !!}], backgroundColor: {!! $odemeTuruRenkler !!}, hoverBorderColor: "#fff" }]
    },
    options: {
        plugins: { labels: { render: 'percentage', fontColor: '#fff', fontSize: 14, fontStyle: 'bold' } },
        legend: { display: false },
        responsive: true,
        maintainAspectRatio: false,
    }
});

// Gelir Grafiği
var ctxGelir = document.getElementById("gelirChart").getContext('2d');
var gelirChart = new Chart(ctxGelir, {
    type: 'pie',
    data: {
        labels: [{!! $odemeSekliAll !!}],
        datasets: [{ data: [{{$nakit}},{{$eft}},{{$kart}}], backgroundColor: ["#34a853","#e01010","#1a73e8"], hoverBorderColor: "#fff" }]
    },
    options: {
        plugins: { labels: { render: 'percentage', fontColor: '#fff', fontSize: 14, fontStyle: 'bold' } },
        legend: { display: false },
        responsive: true,
        maintainAspectRatio: false,
    },
});

// Daterangepicker ve Filtreleme Scriptleri
$(document).ready(function () {
    var start_date = moment();
    var end_date = moment();

    $('#modalDaterange').daterangepicker({
        startDate : start_date,
        endDate : end_date,
        locale: {
            format: 'DD-MM-YYYY', separator: ' - ', applyLabel: 'Uygula', cancelLabel: 'İptal',
            weekLabel: 'H', daysOfWeek: ['Pz', 'Pzt', 'Sal', 'Çrş', 'Prş', 'Cm', 'Cmt'],
            monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
            firstDay: 1
        }
    });
    
    // Tarih aralığı değiştiğinde filtrele
    $('#modalDaterange').on('apply.daterangepicker', function(ev, picker) {
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        runFilters(startDate, endDate);
    });

    // Hazır butonlara tıklandığında filtrele
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
        
        runFilters(startDate.format('YYYY-MM-DD'), endDate.format('YYYY-MM-DD'));
    });
    
    // Tüm filtreleri çalıştıran ana fonksiyon
    function runFilters(startDate, endDate) {
        filterGelirData(startDate, endDate);
        filterGiderData(startDate, endDate);
        filterGelirGrafik(startDate, endDate);
        filterGiderGrafik(startDate, endDate);
    }
    
    // Sayfa ilk yüklendiğinde bugünün verilerini getir
    $('#today').trigger('click');
});

// AJAX Fonksiyonları
function filterGelirData(startDate, endDate) {
    $.ajax({
        url: '/{{ $tenant_id }}/gelir-tablo/getir',
        type: 'POST',
        data: { startDate: startDate, endDate: endDate, _token: "{{ csrf_token() }}" },
        success: function(response) {
            // NOT: Eğer bu endpoint HTML döndürürse, aşağıdaki gibi güncellenmeli:
            // $('#gelirListesi').html(response.html);
            $(".gelirNakit").text(response.nakit + " TL");
            $(".gelirEft").text(response.eft + " TL");
            $(".gelirKart").text(response.kart + " TL");
            $(".gelirToplam").text(response.toplam + " TL");
        },
        error: function(xhr, status, error) { console.error("Gelir Tablosu Hatası:", error); }
    });
}

function filterGiderData(startDate, endDate) {
    $.ajax({
        url: '/{{ $tenant_id }}/gider-tablo/getir',
        method: 'POST',
        data: { startDate: startDate, endDate: endDate, _token: "{{ csrf_token() }}" },
        success: function(response) {
            $("#giderListesi").html(response.html);
        },
        error: function(xhr, status, error) { console.error("Gider Tablosu Hatası:", error); }
    });
}

function filterGelirGrafik(startDate, endDate) {
    $.ajax({
        url: '/{{ $tenant_id }}/gelir-grafik/getir',
        method: 'POST',
        data: { startDate: startDate, endDate: endDate, _token: "{{ csrf_token() }}" },
        success: function(response) {
            gelirChart.data.datasets[0].data = [response.nakit, response.eft, response.kart];
            gelirChart.update();
        },
        error: function(xhr, status, error) { console.error("Gelir Grafik Hatası:", error); }
    });
}

function filterGiderGrafik(startDate, endDate) {
    $.ajax({
        url: '/{{ $tenant_id }}/gider-grafik/getir',
        method: 'POST',
        data: { startDate: startDate, endDate: endDate, _token: "{{ csrf_token() }}" },
        success: function(response) {
            if(response.giderler) {
                giderChart.data.datasets[0].data = response.giderler.split(',').map(Number);
                giderChart.data.labels = response.labels; // Etiketleri de güncellemek iyi olabilir
                giderChart.update();
            }
        },
        error: function(xhr, status, error) { console.error("Gider Grafik Hatası:", error); }
    });
}
</script>

<style>
.gider {
    display: flex;
    align-items: center;
    margin-bottom: 2px;
    padding: 4px 0;
}
.gider .renk {
    width: 12px;
    height: 12px;
    border-radius: 3px;
    margin-right: 10px;
    flex-shrink: 0;
}
.gider .adi {
    flex: 1;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #495057;
}
.gider .para {
    font-weight: 500;
    font-size: 14px;
    padding-left: 10px;
    color: #212529;
}
.border-end {
    border-right: 1px solid #dee2e6!important;
}
</style>

@endsection