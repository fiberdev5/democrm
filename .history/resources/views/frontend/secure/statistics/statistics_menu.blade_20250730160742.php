<div class="row pageDetail">
      <div class="col-12">
        {{-- <div class="card">
          <div class="card-body"> --}}
            <div class="row">
              <div class="col-12">
                <div class="d-flex justify-content-start gap-2 overflow-auto">
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn btn-dark statistic-btn mb-2">
                    <i class="fas fa-tools"></i> Servis İstatistikleri
                  </a>
                  <a href="{{ route('technician.statistics', $tenant_id) }}" class="btn btn-success statistic-btn mb-2">
                    <i class="fas fa-user-cog"></i> Teknisyen İstatistikleri
                  </a>
                  <a href="{{ route('operator.statistics', $tenant_id) }}" class="btn btn-info statistic-btn mb-2">
                    <i class="fas fa-headset"></i> Operatör İstatistikleri
                  </a>
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn btn-danger statistic-btn mb-2">
                    <i class="fas fa-money-bill"></i> Kasa İstatistikleri
                  </a>
                  <a href="{{ route('state.statistics', $tenant_id) }}" class="btn statistic-btn mb-2" style="background-color:#8e44ad; color: #fff;">
                    <i class="fas fa-clipboard-check"></i> Durum İstatistikleri
                  </a>
                  <a href="{{ route('stage.statistics', $tenant_id) }}" class="btn btn-warning statistic-btn mb-2">
                    <i class="fas fa-stream"></i> Aşama İstatistikleri
                  </a>
                  <a href="{{ route('stock.statistics', $tenant_id) }}" class="btn statistic-btn mb-2" style="background-color:#6e4c1e; color: #fff;">
                    <i class="fas fa-warehouse"></i> Depo İstatistikleri
                  </a>
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn statistic-btn mb-2" style="background-color:#1c3aa9; color: #fff;">
                    <i class="fas fa-map-marked-alt"></i> İlçe<br>İstatistikleri
                  </a>
                  <a href="{{ route('survey.statistics', $tenant_id) }}" class="btn statistic-btn mb-2" style="background-color:#4e6e3f; color: #fff;">
                    <i class="fas fa-poll"></i> Anket İstatistikleri
                  </a>
                </div>
              </div>
            </div>
          {{-- </div>
        </div> --}}
      </div>
    </div>


    <style>
      .statistic-btn {
    min-width: 120px;
    height: 70px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 5px 10px;
    font-size: 0.85rem;
    white-space: normal !important;
    transition: transform 0.2s ease-in-out, background-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out; /* Animasyon geçişi için */
}

/* .statistic-btn i {
    margin-bottom: 5px;
    font-size: 1.5rem;
} */
.statistic-btn:hover {
    text-decoration: underline !important; 
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Hafif bir gölge */
    transform: scale(1.03); /* %3 oranında büyütme */
}
    </style>