@extends('frontend.secure.user_master')
@section('user')

<div class="page-content">
  <div class="container-fluid">

    <!-- Üst İstatistik Kartları -->
    <div class="row g-3">
      @php
        $stats = [
          ['title' => 'Toplam Servis Sayısı', 'value' => 1, 'icon' => 'ri-mail-open-line'],
          ['title' => 'Müşteri Sayısı', 'value' => 1, 'icon' => 'ri-team-line'],
          ['title' => 'Personel Sayısı', 'value' => 5, 'icon' => 'ri-pencil-line'],
          ['title' => 'Kasa', 'value' => 12, 'icon' => 'ri-message-3-line']
        ];
      @endphp

      @foreach($stats as $stat)
        <div class="col-xl-3 col-md-6 col-sm-6 col-6">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                  <p class="text-muted mb-1">{{ $stat['title'] }}</p>
                  <h4 class="mb-0 fw-bold">{{ $stat['value'] }}</h4>
                </div>
                <div class="avatar-sm bg-light rounded-3 d-flex align-items-center justify-content-center">
                  <i class="{{ $stat['icon'] }} fs-4 text-secondary"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <!-- Servis Sayıları -->
    <div class="row mt-4">
      <div class="col-md-12">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white border-bottom py-2 fw-semibold">
            <i class="fas fa-chart-area me-2 text-muted"></i> Servis Sayıları
          </div>
          <div class="card-body">
            <div class="row g-3">
              @for($i=0; $i<3; $i++)
                <div class="col-md-4">
                  <a href="#" class="text-decoration-none">
                    <div class="border rounded p-3 h-100 d-flex flex-column justify-content-center hover-shadow-sm">
                      <div class="small text-muted mb-1"><strong>Bugün</strong></div>
                      <div class="fw-semibold">Alınan Servis Sayısı (0)</div>
                    </div>
                  </a>
                </div>
              @endfor
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<style>
  .hover-shadow-sm:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.2s ease-in-out;
  }
</style>

@endsection
