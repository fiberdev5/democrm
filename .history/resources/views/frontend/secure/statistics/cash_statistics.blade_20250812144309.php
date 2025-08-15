@extends('frontend.secure.user_master')
@section('user')

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Chart.js ve gerekli kütüphaneler -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<div class="page-content servis-istatistik">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])

        <div class="card mb-3 pageDetail istatistikSonuclarPage kasaSonuclari">
            <div class="card-header sayfaBaslik">
                <span>Gelir-Gider Tablosu</span>
                <div class="dropdown float-end">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        FİLTRELE
                    </button>
                    <div class="dropdown-menu p-3">
                        <div class="row">
                            <label class="col-lg-4">Tarih Aralığı</label>
                            <div class="col-lg-8">
                                <input type="text" id="modalDaterange" class="form-control" style="background:#fff;margin-bottom: 3px;">
                                <div class="tarihAraliklari mt-2">
                                    <button type="button" id="lastMonth" class="btn btn-sm btn-secondary">Son 1 Ay</button>
                                    <button type="button" id="lastWeek" class="btn btn-sm btn-secondary">Son 7 Gün</button>
                                    <button type="button" id="yesterday" class="btn btn-sm btn-secondary">Dün</button>
                                    <button type="button" id="today" class="btn btn-sm btn-secondary">Bugün</button>
                                </div>
                            </div>
                        </div>
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
                                <ul>
                                    <li class="gelirNakit"><div class="renk"></div><div class="adi">Nakit</div><div class="para">{{ number_format($nakit, 2, ',', '.') }} TL</div></li>
                                    <li class="gelirEft"><div class="renk"></div><div class="adi">EFT/Havale</div><div class="para">{{ number_format($eft, 2, ',', '.') }} TL</div></li>
                                    <li class="gelirKart"><div class="renk"></div><div class="adi">Kredi Kartı</div><div class="para">{{ number_format($kart, 2, ',', '.') }} TL</div></li>
                                </ul>
                            </div>
                            <div class="sag col-sm-6">
                                <canvas id="gelirArea" width="100%" height="220"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Gider Tablosu -->
                    <div class="col-lg-6 giderTablosu">
                        <div class="row">
                            <div class="sol col-sm-6">
                                <ul>
                                    @foreach($odemeTuruAll as $key => $value)
                                    <li class="gider-{{ \Str::slug($key) }}"><div class="renk"></div><div class="adi">{{ $key }}</div><div class="para">{{ number_format($value, 2, ',', '.') }} TL</div></li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="sag col-sm-6">
                                <canvas id="giderArea" width="100%" height="220"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ### SCRIPT VE STİL BÖLÜMÜ ### -->

<script>
$(document).ready(function () {
    // --- GİDER GRAFİĞİ (ESKİ KOD İLE AYNI RENKLER) ---
    var ctxGider = document.getElementById("giderArea").getContext('2d');
    var giderChart = new Chart(ctxGider, {
        type: 'pie',
        data: {
            labels: {!! json_encode(array_keys($odemeTuruAll)) !!},
            datasets: [{
                backgroundColor: ["#607d8b","#e09100","#00e2d8","#212529","#00bfff","#ace400","#ff7600","#801a1a","#bf00ff","#e01010","#34a853","#1a73e8"],
                borderWidth: 0,
                data: {!! json_encode(array_values($odemeTuruAll)) !!}
            }]
        },
        options: {
            plugins: {
                labels: { render: 'percentage', fontColor: '#fff' }
            },
            legend: { display: false },
            responsive: true,
            maintainAspectRatio: false,
        }
    });

    // --- GELİR GRAFİĞİ ---
    var ctxGelir = document.getElementById("gelirArea").getContext('2d');
    var gelirChart = new Chart(ctxGelir, {
        type: 'pie',
        data: {
            labels: ['Nakit', 'EFT/Havale', 'Kredi Kartı'],
            datasets: [{
                backgroundColor: ["#34a853","#e01010","#1a73e8"],
                borderWidth: 0,
                data: [{{ $nakit }}, {{ $eft }}, {{ $kart }}]
            }]
        },
        options: {
            plugins: {
                labels: { render: 'percentage', fontColor: '#fff' }
            },
            legend: { display: false },
            responsive: true,
            maintainAspectRatio: false,
        }
    });

    // --- TARİH FİLTRESİ AYARLARI ---
    $('#modalDaterange').daterangepicker({
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
    
    // Tarih aralığı değiştiğinde AJAX ile verileri güncelle
    $('#modalDaterange').on('apply.daterangepicker', function(ev, picker) {
        var startDate = picker.startDate.format('YYYY-MM-DD');
        var endDate = picker.endDate.format('YYYY-MM-DD');
        updateAllData(startDate, endDate);
    });

    // Hızlı tarih butonları
    $('#lastMonth, #lastWeek, #yesterday, #today').on('click', function() {
        var buttonId = $(this).attr('id');
        var startDate, endDate;

        if (buttonId === 'lastMonth') {
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

        $('#modalDaterange').data('daterangepicker').setStartDate(startDate);
        $('#modalDaterange').data('daterangepicker').setEndDate(endDate);
        updateAllData(startDate.format('YYYY-MM-DD'), endDate.format('YYYY-MM-DD'));
    });
    
    // --- AJAX GÜNCELLEME FONKSİYONLARI ---
    function updateAllData(startDate, endDate) {
        // Gelir Tablosu ve Grafik
        $.ajax({
            url: '/{{ $tenant_id }}/gelir-getir', // Route'unuzu buraya yazın
            method: 'POST',
            data: { startDate: startDate, endDate: endDate, _token: "{{ csrf_token() }}" },
            success: function (data) {
                // Tablo
                $('.gelirNakit .para').text(new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(data.nakit));
                $('.gelirEft .para').text(new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(data.eft));
                $('.gelirKart .para').text(new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(data.kart));
                // Grafik
                gelirChart.data.datasets[0].data = [data.nakit, data.eft, data.kart];
                gelirChart.update();
            }
        });

        // Gider Tablosu ve Grafik
        $.ajax({
            url: '/{{ $tenant_id }}/gider-getir', // Route'unuzu buraya yazın
            method: 'POST',
            data: { startDate: startDate, endDate: endDate, _token: "{{ csrf_token() }}" },
            success: function (data) {
                // Tablo
                var giderListesi = $(".giderTablosu .sol ul");
                giderListesi.empty();
                if(data.giderler && Object.keys(data.giderler).length > 0){
                    Object.entries(data.giderler).forEach(([key, value]) => {
                        let slug = key.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                        giderListesi.append(`<li class="gider-${slug}"><div class="renk"></div><div class="adi">${key}</div><div class="para">${new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(value)}</div></li>`);
                    });
                }
                
                // Grafik
                giderChart.data.labels = Object.keys(data.giderler);
                giderChart.data.datasets[0].data = Object.values(data.giderler);
                giderChart.update();
            }
        });
    }

});
</script>

<style>
/* Eski kodunuzdaki stilleri temel alan CSS */
.kasaSonuclari .card-header .dropdown { margin-top: -5px; }
.gelirTablosu ul, .giderTablosu ul {
    list-style: none;
    padding-left: 0;
    margin-top: 15px;
}
.gelirTablosu ul li, .giderTablosu ul li {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    font-size: 14px;
}
.gelirTablosu ul li .renk, .giderTablosu ul li .renk {
    width: 15px;
    height: 15px;
    border-radius: 3px;
    margin-right: 10px;
}
.gelirTablosu ul li .adi, .giderTablosu ul li .adi {
    flex: 1;
}
.gelirTablosu ul li .para, .giderTablosu ul li .para {
    font-weight: bold;
}

/* Renkler */
.gelirNakit .renk { background-color: #34a853; }
.gelirEft .renk { background-color: #e01010; }
.gelirKart .renk { background-color: #1a73e8; }

/* Gider renklerini dinamik olarak atamak için JS kullanılıyor, 
   ancak statik bir listeleme gerekirse buraya eklenebilir. */
.giderTablosu .sol ul li .renk {
    background-color: #607d8b; /* Varsayılan Renk */
}

/* Dinamik Renk Ataması İçin Örnek Sınıflar */
@php
    $renkler = ["#607d8b","#e09100","#00e2d8","#212529","#00bfff","#ace400","#ff7600","#801a1a","#bf00ff","#e01010","#34a853","#1a73e8"];
    $i = 0;
@endphp
@foreach($odemeTuruAll as $key => $value)
.gider-{{ \Str::slug($key) }} .renk {
    background-color: {{ $renkler[$i % count($renkler)] }};
}
@php $i++; @endphp
@endforeach

</style>

@endsection