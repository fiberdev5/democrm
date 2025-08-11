@extends('frontend.secure.user_master')
@section('user')

<div class="page-content" style="background-color: #f5f6fa; min-height: 100vh;">
  <div class="container-fluid">

    <!-- Üst İstatistik Kartları -->
    <div class="row g-3">
      <div class="col-xl-3 col-md-6 col-sm-6 col-6">
        <div class="card stat-card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <p class="mb-1 text-secondary">Toplam Servis Sayısı</p>
                <h4 class="mb-0 fw-bold text-dark">1</h4>
              </div>
              <div class="avatar-sm bg-light rounded-3 d-flex align-items-center justify-content-center">
                <i class="ri-mail-open-line fs-4 text-secondary"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6 col-sm-6 col-6">
        <div class="card stat-card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <p class="mb-1 text-secondary">Müşteri Sayısı</p>
                <h4 class="mb-0 fw-bold text-dark">1</h4>
              </div>
              <div class="avatar-sm bg-light rounded-3 d-flex align-items-center justify-content-center">
                <i class="ri-team-line fs-4 text-secondary"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6 col-sm-6 col-6">
        <div class="card stat-card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <p class="mb-1 text-secondary">Personel Sayısı</p>
                <h4 class="mb-0 fw-bold text-dark">5</h4>
              </div>
              <div class="avatar-sm bg-light rounded-3 d-flex align-items-center justify-content-center">
                <i class="ri-pencil-line fs-4 text-secondary"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6 col-sm-6 col-6">
        <div class="card stat-card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <p class="mb-1 text-secondary">Kasa</p>
                <h4 class="mb-0 fw-bold text-dark">12</h4>
              </div>
              <div class="avatar-sm bg-light rounded-3 d-flex align-items-center justify-content-center">
                <i class="ri-message-3-line fs-4 text-secondary"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Servis Sayıları -->
    <div class="mt-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-2 fw-semibold">
          <i class="fas fa-chart-area text-muted me-2"></i> Servis Sayıları
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <div class="info-box">
                <strong>Bugün</strong> Alınan Servis Sayısı (0)
              </div>
            </div>
            <div class="col-md-4">
              <div class="info-box">
                <strong>Dün</strong> Alınan Servis Sayısı (0)
              </div>
            </div>
            <div class="col-md-4">
              <div class="info-box">
                <strong>Bu Hafta</strong> Alınan Servis Sayısı (0)
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<style>
  .stat-card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: transform 0.2s ease;
  }
  .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  }
  .info-box {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 12px;
    font-size: 14px;
    color: #444;
    transition: background 0.2s ease;
  }
  .info-box:hover {
    background: #f1f3f5;
  }
</style>

@endsection
