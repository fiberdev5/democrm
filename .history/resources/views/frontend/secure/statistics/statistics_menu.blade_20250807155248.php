<div class="row pageDetail">
  <div class="col-12">
    <div class="row">
      <div class="col-12">
        <div class="d-flex flex-wrap gap-2 mb-2">
          <a href="{{ route('statistics', $tenant_id) }}" class="btn btn-dark statistic-btn">
            <i class="fas fa-tools"></i> Servis İstatistikleri
          </a>
          <a href="{{ route('technician.statistics', $tenant_id) }}" class="btn btn-success statistic-btn">
            <i class="fas fa-user-cog"></i> Teknisyen İstatistikleri
          </a>
          <a href="{{ route('operator.statistics', $tenant_id) }}" class="btn btn-info statistic-btn">
            <i class="fas fa-headset"></i> Operatör İstatistikleri
          </a>
          <a href="{{ route('cash.statistics', $tenant_id) }}" class="btn btn-danger statistic-btn">
            <i class="fas fa-money-bill"></i> Kasa İstatistikleri
          </a>
          <a href="{{ route('state.statistics', $tenant_id) }}" class="btn statistic-btn" style="background-color:#8e44ad; color: #fff;">
            <i class="fas fa-clipboard-check"></i> Durum İstatistikleri
          </a>
          <a href="{{ route('stage.statistics', $tenant_id) }}" class="btn btn-warning statistic-btn">
            <i class="fas fa-stream"></i> Aşama İstatistikleri
          </a>
          <a href="{{ route('stock.statistics', $tenant_id) }}" class="btn statistic-btn" style="background-color:#6e4c1e; color: #fff;">
            <i class="fas fa-warehouse"></i> Depo İstatistikleri
          </a>
          <a href="{{ route('ilce.statistics', $tenant_id) }}" class="btn statistic-btn" style="background-color:#1c3aa9; color: #fff;">
            <i class="fas fa-map-marked-alt"></i> İlçe İstatistikleri
          </a>
          <a href="{{ route('survey.statistics', $tenant_id) }}" class="btn statistic-btn" style="background-color:#4e6e3f; color: #fff;">
            <i class="fas fa-poll"></i> Anket İstatistikleri
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
