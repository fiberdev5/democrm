@extends('frontend.secure.user_master')

@section('user')
<div class="page-content">
  <div class="container-fluid">
    <div class="row pageDetail">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <div class="d-flex justify-content-start gap-2 overflow-auto">
                  {{-- Aşağıdaki CSS'i projeye özgü bir yere (örneğin ana CSS dosyanıza) taşımanız daha iyi olacaktır. --}}
                  <style>
                    .statistic-btn {
                      min-width: 120px; /* Minimum genişlik, ihtiyaca göre ayarlayabilirsiniz */
                      height: 70px; /* Sabit yükseklik, ihtiyaca göre ayarlayabilirsiniz */
                      display: flex; /* İçeriği ortalamak için flexbox kullan */
                      flex-direction: column; /* İçeriği dikey sırala (ikon ve yazı) */
                      justify-content: center; /* Dikeyde ortala */
                      align-items: center; /* Yatayda ortala */
                      text-align: center; /* Yazıyı ortala */
                      padding: 5px 10px; /* İç boşluğu ayarla */
                      white-space: normal; /* Metin sarmasına izin ver, ancak min-width ile kontrol edeceğiz */
                      font-size: 0.85rem; /* Yazı boyutunu küçült */
                    }

                    .statistic-btn i {
                      margin-bottom: 5px; /* İkon ve yazı arasına boşluk */
                      font-size: 1.5rem; /* İkon boyutunu büyüt */
                    }
                  </style>

                  <a href="{{ route('statistics', $tenant_id) }}" class="btn btn-primary statistic-btn mb-2">
                    <i class="fas fa-tools"></i> Servis İstatistikleri
                  </a>
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn btn-success statistic-btn mb-2">
                    <i class="fas fa-user-cog"></i> Teknisyen İstatistikleri
                  </a>
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn btn-info statistic-btn mb-2">
                    <i class="fas fa-headset"></i> Operatör İstatistikleri
                  </a>
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn btn-danger statistic-btn mb-2">
                    <i class="fas fa-money-bill-wave"></i> Kasa İstatistikleri
                  </a>
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn statistic-btn mb-2" style="background-color:#8e44ad; color: #fff;">
                    <i class="fas fa-clipboard-check"></i> Durum İstatistikleri
                  </a>
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn btn-warning statistic-btn mb-2">
                    <i class="fas fa-stream"></i> Aşama İstatistikleri
                  </a>
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn statistic-btn mb-2" style="background-color:#6e4c1e; color: #fff;">
                    <i class="fas fa-warehouse"></i> Depo İstatistikleri
                  </a>
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn statistic-btn mb-2" style="background-color:#1c3aa9; color: #fff;">
                    <i class="fas fa-map-marked-alt"></i> İlçe İstatistikleri
                  </a>
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn statistic-btn mb-2" style="background-color:#4e6e3f; color: #fff;">
                    <i class="fas fa-poll"></i> Anket İstatistikleri
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection