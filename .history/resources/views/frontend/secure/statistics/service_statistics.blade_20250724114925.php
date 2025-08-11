@extends('frontend.secure.user_master')

@section('user')
<div class="page-content">
  <div class="container-fluid">
    <div class="row pageDetail">
      <div class="col-12">
        <div class="card">
          <div class="card-header sayfaBaslik">
            İstatistikler
          </div>
          <div class="card-body"> {{-- card-body ekledim, çünkü butonlar genelde card'ın body'si içine konur --}}
            <div class="row">
              <div class="col-12">
                <div class="d-flex flex-wrap justify-content-start gap-2"> {{-- d-flex, flex-wrap ve gap-2 ile yan yana hizalama ve boşluk --}}
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn btn-primary mb-2">
                    <i class="fas fa-tools"></i> Servis İstatistikleri
                  </a>
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn btn-success mb-2">
                    <i class="fas fa-user-cog"></i> Teknisyen İstatistikleri
                  </a>
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn btn-info mb-2">
                    <i class="fas fa-headset"></i> Operatör İstatistikleri
                  </a>
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn btn-danger mb-2">
                    <i class="fas fa-money-bill-wave"></i> Kasa İstatistikleri
                  </a>
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn mb-2" style="background-color:#8e44ad; color: #fff;">
                    <i class="fas fa-clipboard-check"></i> Durum İstatistikleri
                  </a>
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn btn-warning mb-2">
                    <i class="fas fa-stream"></i> Aşama İstatistikleri
                  </a>
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn mb-2" style="background-color:#6e4c1e; color: #fff;">
                    <i class="fas fa-warehouse"></i> Depo İstatistikleri
                  </a>
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn mb-2" style="background-color:#1c3aa9; color: #fff;">
                    <i class="fas fa-map-marked-alt"></i> İlçe İstatistikleri
                  </a>
                  <a href="{{ route('statistics', $tenant_id) }}" class="btn mb-2" style="background-color:#4e6e3f; color: #fff;">
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