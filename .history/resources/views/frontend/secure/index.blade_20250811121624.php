@extends('frontend.secure.user_master')
@section('user')

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<div class="page-content">
  <div class="container-fluid">
    {{-- Üstteki kartlar --}}
    <div class="row anasayfa-card mb-3">
      @php
        $cards = [
          ['title'=>'Toplam Servis Sayısı','count'=>1,'icon'=>'ri-mail-open-line'],
          ['title'=>'Müşteri Sayısı','count'=>1,'icon'=>'ri-team-line'],
          ['title'=>'Personel Sayısı','count'=>5,'icon'=>'ri-pencil-line'],
          ['title'=>'Kasa','count'=>12,'icon'=>'ri-message-3-line'],
        ];
      @endphp
      @foreach($cards as $card)
      <div class="col-xl-3 col-md-6 col-sm-6 col-6">
        <div class="card shadow-sm">
          <a href="" class="card-body">
            <div class="d-flex">
              <div class="flex-grow-1">
                <p class="text-truncate font-size-14 text-body mb-2">{{ $card['title'] }}</p>
                <h4 class="mb-0">{{ $card['count'] }}</h4>
              </div>
              <div class="avatar-sm mt-1">
                <span class="avatar-title bg-light text-primary rounded-3">
                  <i class="{{ $card['icon'] }} font-size-24"></i>
                </span>
              </div>
            </div>
          </a>
        </div>
      </div>
      @endforeach
    </div>

    {{-- Grafik Kartı --}}
    <div class="row">
      <div class="col-lg-6 mb-4">
        <div class="card h-100">
          <div class="card-header py-2">
            <i class="fas fa-chart-line"></i> Saatlik Servis Sayıları
          </div>
          <div class="card-body">
            <canvas id="hourlyServiceChart" height="200"></canvas>
          </div>
        </div>
      </div>

      <div class="col-lg-6 mb-4">
        <div class="card h-100">
          <div class="card-header py-2">
            <i class="fas fa-chart-bar"></i> Günlük Servis Sayıları
          </div>
          <div class="card-body">
            <canvas id="dailyServiceChart" height="200"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Örnek veri (Laravel'den dinamik gönderebilirsin)
const hourlyLabels = ['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00'];
const hourlyData = [2, 5, 3, 6, 4, 7, 8, 5];

const dailyLabels = ['Pzt','Sal','Çar','Per','Cum','Cmt','Paz'];
const dailyData = [10, 15, 8, 12, 20, 5, 3];

// Saatlik Grafik
new Chart(document.getElementById('hourlyServiceChart'), {
    type: 'line',
    data: {
        labels: hourlyLabels,
        datasets: [{
            label: 'Servis Sayısı',
            data: hourlyData,
            fill: true,
            borderColor: '#007bff',
            backgroundColor: 'rgba(0,123,255,0.1)',
            tension: 0.3,
            pointRadius: 5
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        }
    }
});

// Günlük Grafik
new Chart(document.getElementById('dailyServiceChart'), {
    type: 'bar',
    data: {
        labels: dailyLabels,
        datasets: [{
            label: 'Servis Sayısı',
            data: dailyData,
            backgroundColor: 'rgba(40,167,69,0.6)',
            borderColor: '#28a745',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

@endsection
