<div class="row detayGrafikler">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 tamamlananGrafik"><span>Tamamlanan Servisler</span><canvas id="tamamlananArea" width="100%" height="30"></canvas></div>
            <div class="col-md-4 iptalGrafik"><span>İptal Servisler</span><canvas id="iptalArea" width="100%" height="30"></canvas></div>
            <div class="col-md-4 gelirGrafik"><span>Alinan Ücretler</span><canvas id="gelirArea" width="100%" height="30"></canvas></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        // Tamamlanan Servisler Grafiği
        var ctxTamamlanan = document.getElementById("tamamlananArea");
        new Chart(ctxTamamlanan, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    lineTension: 0.3,
                    backgroundColor: "rgba(2,117,216,0.2)",
                    borderColor: "rgba(2,117,216,1)",
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(2,117,216,1)",
                    pointBorderColor: "rgba(255,255,255,0.8)",
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(2,117,216,1)",
                    pointHitRadius: 50,
                    pointBorderWidth: 2,
                    data: @json($tamamlananServisler),
                }],
            },
            options: {
                legend: {
                    display: false
                }
            }
        });

        // İptal Servisler Grafiği
        var ctxIptal = document.getElementById("iptalArea");
        new Chart(ctxIptal, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    lineTension: 0.3,
                    backgroundColor: "rgba(255,0,0,0.2)",
                    borderColor: "rgba(255,0,0,0.7)",
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(255,0,0,1)",
                    pointBorderColor: "rgba(255,255,255,0.8)",
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(255,0,0,1)",
                    pointHitRadius: 50,
                    pointBorderWidth: 2,
                    data: @json($iptalServisler),
                }],
            },
            options: {
                legend: {
                    display: false
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            suggestedMin: 0,
                            suggestedMax: {{ $maxTop + 1 }}
                        }
                    }]
                }
            }
        });

        // Alınan Ücretler Grafiği
        var ctxUcret = document.getElementById("gelirArea");
        new Chart(ctxUcret, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    lineTension: 0.3,
                    backgroundColor: "rgba(84,177,47,0.2)",
                    borderColor: "rgba(84,177,47,0.7)",
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(84,177,47,1)",
                    pointBorderColor: "rgba(255,255,255,0.8)",
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(84,177,47,1)",
                    pointHitRadius: 50,
                    pointBorderWidth: 2,
                    data: @json($alinanUcretler),
                }],
            },
            options: {
                legend: {
                    display: false
                }
            }
        });
    });
</script>

<div class="row detayAsamalar mt-4">
    <div class="col-md-2 cols">
        <div class="capt text-center">
            <p>Atanan Servisler</p>
            <h2>{{ $counts['atananServislerSay'] ?? 0 }}</h2>
        </div>
    </div>
    <div class="col-md-2 cols">
        <div class="capt text-center">
            <p>Tamamlanan Servisler</p>
            <h2>{{ $counts['tamamlananServislerSay'] ?? 0 }}</h2>
        </div>
    </div>
    <div class="col-md-2 cols">
        <div class="capt text-center">
            <p>Şikayetçi Servisler</p>
            <h2>{{ $counts['sikayetciServislerSay'] ?? 0 }}</h2>
        </div>
    </div>
    <div class="col-md-2 cols">
        <div class="capt text-center">
            <p>İptal Servisler</p>
            <h2>{{ $counts['iptalServislerSay'] ?? 0 }}</h2>
        </div>
    </div>
    <div class="col-md-2 cols">
        <div class="capt text-center">
            <p>Haber Verecek</p>
            <h2>0</h2> {{-- Bu veri Controller'dan gelmiyor, manuel olarak 0 atanmıştır. --}}
        </div>
    </div>
    {{-- Diğer kartlar için Controller'dan gelen verilerle devam edilebilir. --}}
</div>