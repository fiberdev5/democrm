@extends('frontend.secure.user_master')
@section('user')

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<style>
.dashboard-card {
    background: #fff;
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.2s ease;
}
.dashboard-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 14px rgba(0,0,0,0.1);
}
.dashboard-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #fff;
}
.icon-blue { background: #007bff; }
.icon-green { background: #28a745; }
.icon-orange { background: #fd7e14; }
.icon-red { background: #dc3545; }

.stat-label {
    font-size: 14px;
    color: #666;
    margin-bottom: 3px;
}
.stat-value {
    font-size: 22px;
    font-weight: 600;
    margin: 0;
}

.card-section {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.card-section .card-header {
    background: none;
    border-bottom: 1px solid #eee;
    font-weight: 600;
}
</style>

<div class="page-content">
  <div class="container-fluid">

    {{-- Üst İstatistik Kartları --}}
    <div class="row mb-4">
      <div class="col-xl-3 col-md-6 mb-3">
        <div class="dashboard-card">
          <div>
            <div class="stat-label">Toplam Servis</div>
            <div class="stat-value">1</div>
          </div>
          <div class="dashboard-icon icon-blue">
            <i class="ri-mail-open-line"></i>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 mb-3">
        <div class="dashboard-card">
          <div>
            <div class="stat-label">Müşteri</div>
            <div class="stat-value">1</div>
          </div>
          <div class="dashboard-icon icon-green">
            <i class="ri-team-line"></i>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 mb-3">
        <div class="dashboard-card">
          <div>
            <div class="stat-label">Personel</div>
            <div class="stat-value">5</div>
          </div>
          <div class="dashboard-icon icon-orange">
            <i class="ri-user-line"></i>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 mb-3">
        <div class="dashboard-card">
          <div>
            <div class="stat-label">Kasa</div>
            <div class="stat-value">12</div>
          </div>
          <div class="dashboard-icon icon-red">
            <i class="ri-money-dollar-circle-line"></i>
          </div>
        </div>
      </div>
    </div>

    {{-- Bugün / Hafta / Ay Servis Kartları --}}
    <div class="row mb-4">
      <div class="col-md-4 mb-3">
        <div class="dashboard-card">
          <div>
            <div class="stat-label">Bugün Alınan Servis</div>
            <div class="stat-value">0</div>
          </div>
          <div class="dashboard-icon icon-blue">
            <i class="fas fa-calendar-day"></i>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="dashboard-card">
          <div>
            <div class="stat-label">Bu Hafta Alınan Servis</div>
            <div class="stat-value">0</div>
          </div>
          <div class="dashboard-icon icon-green">
            <i class="fas fa-calendar-week"></i>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="dashboard-card">
          <div>
            <div class="stat-label">Bu Ay Alınan Servis</div>
            <div class="stat-value">0</div>
          </div>
          <div class="dashboard-icon icon-orange">
            <i class="fas fa-calendar-alt"></i>
          </div>
        </div>
      </div>
    </div>

    {{-- Grafikler --}}
    <div class="row">
      <div class="col-lg-6 mb-4">
        <div class="card-section">
          <div class="card-header">
            <i class="fas fa-chart-line"></i> Saatlik Servis Sayıları
          </div>
          <div class="card-body">
            <canvas id="hourlyServiceChart" height="200"></canvas>
          </div>
        </div>
      </div>

      <div class="col-lg-6 mb-4">
        <div class="card-section">
          <div class="card-header">
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

// Saatlik Grafik (Mavi)
new Chart(document.getElementById('hourlyServiceChart'), {
    type: 'line',
    data: {
        labels: hourlyLabels,
        datasets: [{
            label: 'Saatlik Servis Sayısı',
            data: hourlyData,
            fill: true,
            borderColor: 'rgba(255,165,0,0.7)',
            backgroundColor: 'rgba(255,165,0,0.2)',
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

// Günlük Grafik (Yeşil)
new Chart(document.getElementById('dailyServiceChart'), {
    type: 'line',
    data: {
        labels: dailyLabels,
        datasets: [{
            label: 'Günlük Servis Sayısı',
            data: dailyData,
            fill: true,
            borderColor: '#28a745',
            backgroundColor: 'rgba(40,167,69,0.1)',
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#28a745'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});

</script>

@endsection
