@extends('frontend.secure.user_master')
@section('user')

<div class="page-content">
  <div class="container-fluid">

    <!-- Üst İstatistik Kartları -->
    <div class="row">
      <div class="col-xl-3 col-md-6 col-sm-6 col-6">
        <div class="card border">
          <a href="" class="card-body text-dark text-decoration-none">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <p class="mb-1 text-muted">Toplam Servis Sayısı</p>
                <h4 class="mb-0 fw-bold">1</h4>
              </div>
              <div class="avatar-sm">
                <span class="avatar-title bg-light rounded-3">
                  <i class="ri-mail-open-line fs-4 text-muted"></i>
                </span>
              </div>
            </div>
          </a>
        </div>
      </div>

      <div class="col-xl-3 col-md-6 col-sm-6 col-6">
        <div class="card border">
          <a href="" class="card-body text-dark text-decoration-none">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <p class="mb-1 text-muted">Müşteri Sayısı</p>
                <h4 class="mb-0 fw-bold">1</h4>
              </div>
              <div class="avatar-sm">
                <span class="avatar-title bg-light rounded-3">
                  <i class="ri-team-line fs-4 text-muted"></i>
                </span>
              </div>
            </div>
          </a>
        </div>
      </div>

      <div class="col-xl-3 col-md-6 col-sm-6 col-6">
        <div class="card border">
          <a href="" class="card-body text-dark text-decoration-none">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <p class="mb-1 text-muted">Personel Sayısı</p>
                <h4 class="mb-0 fw-bold">5</h4>
              </div>
              <div class="avatar-sm">
                <span class="avatar-title bg-light rounded-3">
                  <i class="ri-pencil-line fs-4 text-muted"></i>
                </span>
              </div>
            </div>
          </a>
        </div>
      </div>

      <div class="col-xl-3 col-md-6 col-sm-6 col-6">
        <div class="card border">
          <a href="" class="card-body text-dark text-decoration-none">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <p class="mb-1 text-muted">Kasa</p>
                <h4 class="mb-0 fw-bold">12</h4>
              </div>
              <div class="avatar-sm">
                <span class="avatar-title bg-light rounded-3">
                  <i class="ri-message-3-line fs-4 text-muted"></i>
                </span>
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>

    <!-- Servis Sayıları -->
    <div class="mt-4">
      <div class="card border">
        <div class="card-header bg-light py-2">
          <i class="fas fa-chart-area text-muted me-2"></i> Servis Sayıları
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4 mb-3">
              <a href="" class="d-block border rounded p-3 text-dark text-decoration-none">
                <strong>Bugün</strong> Alınan Servis Sayısı (0)
              </a>
            </div>
            <div class="col-md-4 mb-3">
              <a href="" class="d-block border rounded p-3 text-dark text-decoration-none">
                <strong>Dün</strong> Alınan Servis Sayısı (0)
              </a>
            </div>
            <div class="col-md-4 mb-3">
              <a href="" class="d-block border rounded p-3 text-dark text-decoration-none">
                <strong>Bu Hafta</strong> Alınan Servis Sayısı (0)
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

@endsection
