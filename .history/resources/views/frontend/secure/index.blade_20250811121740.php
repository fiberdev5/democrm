@extends('frontend.secure.user_master')
@section('user')

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<style>
.stat-card {
    border-radius: 15px;
    color: #fff;
    padding: 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.stat-icon {
    font-size: 32px;
    opacity: 0.9;
}
.bg-gradient-blue {
    background: linear-gradient(45deg, #2196f3, #21cbf3);
}
.bg-gradient-green {
    background: linear-gradient(45deg, #28a745, #85d682);
}
.bg-gradient-orange {
    background: linear-gradient(45deg, #ff9800, #ffc107);
}
.bg-gradient-red {
    background: linear-gradient(45deg, #dc3545, #ff6f61);
}
</style>

<div class="page-content">
  <div class="container-fluid">

    {{-- Üst İstatistik Kartları --}}
    <div class="row mb-4">
      <div class="col-xl-3 col-md-6 col-sm-6 col-6">
        <div class="stat-card bg-gradient-blue shadow">
          <div>
            <p class="mb-1">Toplam Servis</p>
            <h4 class="mb-0">1</h4>
          </div>
          <i class="ri-mail-open-line stat-icon"></i>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 col-sm-6 col-6">
        <div class="stat-card bg-gradient-green shadow">
          <div>
            <p class="mb-1">Müşteri</p>
            <h4 class="mb-0">1</h4>
          </div>
          <i class="ri-team-line stat-icon"></i>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 col-sm-6 col-6">
        <div class="stat-card bg-gradient-orange shadow">
          <div>
            <p class="mb-1">Personel</p>
            <h4 class="mb-0">5</h4>
          </div>
          <i class="ri-pencil-line stat-icon"></i>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 col-sm-6 col-6">
        <div class="stat-card bg-gradient-red shadow">
          <div>
            <p class="mb-1">Kasa</p>
            <h4 class="mb-0">12</h4>
          </div>
          <i class="ri-money-dollar-circle-line stat-icon"></i>
        </div>
      </div>
    </div>

    {{-- Bugün / Hafta / Ay Servis Kartları --}}
    <div class="row mb-4">
      <div class="col-md-4">
        <a class="card text-white bg-info shadow-sm" href="">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <strong>Bugün</strong> Alınan Servis
              </div>
              <i class="fas fa-calendar-day"></i>
            </div>
            <h4 class="mt-2 mb-0">0</h4>
          </div>
        </a>
      </div>
      <div class="col-md-4">
        <a class="card text-white bg-primary shadow-sm" href="">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <strong>Bu Hafta</strong> Alınan Servis
              </div>
              <i class="fas fa-calendar-week"></i>
            </div>
            <h4 class="mt-2 mb-0">0</h4>
          </div>
        </a>
      </div>
      <div class="col-md-4">
        <a class="card text-white bg-success shadow-sm" href="">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <strong>Bu Ay</strong> Alınan Servis
              </div>
              <i class="fas fa-calendar-alt"></i>
            </div>
            <h4 class="mt-2 mb-0">0</h4>
          </div>
        </a>
      </div>
    </div>

    {{-- Grafikler --}}
    <div class="row">
      <div class="col-lg-6 mb-4">
        <div class="card h-100 shadow-sm">
          <div class="card-header py-2">
            <i class="fas fa-chart-line"></i> Saatlik Servis Sayıları
          </div>
          <div class="card-body">
            <canvas id="hourlyServiceChart" height="200"></canvas>
          </div>
        </div>
      </div>

      <div class="col-lg-6 mb-4">
        <div class="card h-100 shadow-sm">
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
// Örnek veri
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
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#007bff'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
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
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>

@endsection
