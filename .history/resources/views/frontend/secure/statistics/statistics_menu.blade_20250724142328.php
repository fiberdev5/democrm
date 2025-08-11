@php
    $currentRoute = \Illuminate\Support\Facades\Route::currentRouteName();
@endphp

<div class="row pageDetail">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-start gap-2 overflow-auto">

          <a href="{{ route('statistics', $tenant_id) }}"
             class="btn btn-primary statistic-btn mb-2 {{ $currentRoute == 'statistics' ? 'active' : '' }}">
            <i class="fas fa-tools"></i> Servis İstatistikleri
          </a>

          <a href="{{ route('technician.statistics', $tenant_id) }}"
             class="btn btn-success statistic-btn mb-2 {{ $currentRoute == 'technician.statistics' ? 'active' : '' }}">
            <i class="fas fa-user-cog"></i> Teknisyen İstatistikleri
          </a>

          <a href="{{ route('operator.statistics', $tenant_id) }}"
             class="btn btn-info statistic-btn mb-2 {{ $currentRoute == 'operator.statistics' ? 'active' : '' }}">
            <i class="fas fa-headset"></i> Operatör İstatistikleri
          </a>

          <a href="{{ route('cash.statistics', $tenant_id) }}"
             class="btn btn-danger statistic-btn mb-2 {{ $currentRoute == 'cash.statistics' ? 'active' : '' }}">
            <i class="fas fa-money-bill"></i> Kasa İstatistikleri
          </a>

          <a href="{{ route('status.statistics', $tenant_id) }}"
             class="btn statistic-btn mb-2 {{ $currentRoute == 'status.statistics' ? 'active' : '' }}"
             style="background-color:#8e44ad; color: #fff;">
            <i class="fas fa-clipboard-check"></i> Durum İstatistikleri
          </a>

          <a href="{{ route('stage.statistics', $tenant_id) }}"
             class="btn btn-warning statistic-btn mb-2 {{ $currentRoute == 'stage.statistics' ? 'active' : '' }}">
            <i class="fas fa-stream"></i> Aşama İstatistikleri
          </a>

          <a href="{{ route('depo.statistics', $tenant_id) }}"
             class="btn statistic-btn mb-2 {{ $currentRoute == 'depo.statistics' ? 'active' : '' }}"
             style="background-color:#6e4c1e; color: #fff;">
            <i class="fas fa-warehouse"></i> Depo İstatistikleri
          </a>

          <a href="{{ route('district.statistics', $tenant_id) }}"
             class="btn statistic-btn mb-2 {{ $currentRoute == 'district.statistics' ? 'active' : '' }}"
             style="background-color:#1c3aa9; color: #fff;">
            <i class="fas fa-map-marked-alt"></i> İlçe İstatistikleri
          </a>

          <a href="{{ route('survey.statistics', $tenant_id) }}"
             class="btn statistic-btn mb-2 {{ $currentRoute == 'survey.statistics' ? 'active' : '' }}"
             style="background-color:#4e6e3f; color: #fff;">
            <i class="fas fa-poll"></i> Anket İstatistikleri
          </a>

        </div>
      </div>
    </div>
  </div>
</div>
