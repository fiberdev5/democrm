<div class="row detayGrafikler" style="padding: 15px;">
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
        // Grafik verileri PHP'den gelecek şekilde ayarlanmalı
        // Örneğin: `labels: [@json($labels)], data: [@json($tamamlananServisler)]`
        var ctxTamamlanan = document.getElementById("tamamlananArea").getContext('2d');
        var myLineChartTamamlanan = new Chart(ctxTamamlanan, {
            type: 'line',
            data: {
                labels: [{{ $labels ?? '' }}], // PHP tarafından doldurulacak
                datasets: [{
                    label: "Tamamlanan",
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
                    data: [{{ $tamamlananServisler ?? '' }}], // PHP tarafından doldurulacak
                }],
            },
            options: {
                scales: {
                    xAxes: [{
                        time: { unit: 'date' },
                        gridLines: { display: false },
                        ticks: { maxTicksLimit: 7 }
                    }],
                    yAxes: [{
                        ticks: {
                            min: 0,
                            max: {{ $maxTop ?? 10 }}, // Dinamik maksimum değer
                            maxTicksLimit: 5
                        },
                        gridLines: { display: true }
                    }],
                },
                legend: { display: false }
            }
        });

        var ctxIptal = document.getElementById("iptalArea").getContext('2d');
        var myLineChartIptal = new Chart(ctxIptal, {
            type: 'line',
            data: {
                labels: [{{ $labels ?? '' }}], // PHP tarafından doldurulacak
                datasets: [{
                    label: "İptal",
                    lineTension: 0.3,
                    backgroundColor: "rgba(255,99,132,0.2)",
                    borderColor: "rgba(255,99,132,1)",
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(255,99,132,1)",
                    pointBorderColor: "rgba(255,255,255,0.8)",
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(255,99,132,1)",
                    pointHitRadius: 50,
                    pointBorderWidth: 2,
                    data: [{{ $iptalServisler ?? '' }}], // PHP tarafından doldurulacak
                }],
            },
            options: {
                scales: {
                    xAxes: [{
                        time: { unit: 'date' },
                        gridLines: { display: false },
                        ticks: { maxTicksLimit: 7 }
                    }],
                    yAxes: [{
                        ticks: {
                            min: 0,
                            max: {{ $maxTop ?? 10 }}, // Dinamik maksimum değer
                            maxTicksLimit: 5
                        },
                        gridLines: { display: true }
                    }],
                },
                legend: { display: false }
            }
        });

        var ctxGelir = document.getElementById("gelirArea").getContext('2d');
        var myLineChartGelir = new Chart(ctxGelir, {
            type: 'line',
            data: {
                labels: [{{ $labels ?? '' }}], // PHP tarafından doldurulacak
                datasets: [{
                    label: "Gelir",
                    lineTension: 0.3,
                    backgroundColor: "rgba(40,167,69,0.2)",
                    borderColor: "rgba(40,167,69,1)",
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(40,167,69,1)",
                    pointBorderColor: "rgba(255,255,255,0.8)",
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(40,167,69,1)",
                    pointHitRadius: 50,
                    pointBorderWidth: 2,
                    data: [{{ $alinanUcretler ?? '' }}], // PHP tarafından doldurulacak
                }],
            },
            options: {
                scales: {
                    xAxes: [{
                        time: { unit: 'date' },
                        gridLines: { display: false },
                        ticks: { maxTicksLimit: 7 }
                    }],
                    yAxes: [{
                        ticks: {
                            min: 0,
                            max: {{ $maxTopGelir ?? 1000 }}, // Dinamik maksimum değer (gelir için farklı olabilir)
                            maxTicksLimit: 5
                        },
                        gridLines: { display: true }
                    }],
                },
                legend: { display: false }
            }
        });
    });
</script>