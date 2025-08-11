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
          <div class="row">
    <div class="col-md-3">
        <a href="{{ route('service.statistics', $tenant_id) }}" class="btn btn-primary btn-block mb-2">
            <i class="fas fa-tools"></i> Servis İstatistikleri
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('technician.statistics', $tenant_id) }}" class="btn btn-success btn-block mb-2">
            <i class="fas fa-user-cog"></i> Teknisyen İstatistikleri
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('operator.statistics', $tenant_id) }}" class="btn btn-info btn-block mb-2">
            <i class="fas fa-headset"></i> Operatör İstatistikleri
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('kasa.statistics', $tenant_id) }}" class="btn btn-danger btn-block mb-2">
            <i class="fas fa-money-bill-wave"></i> Kasa İstatistikleri
        </a>
    </div>

    <div class="col-md-3">
        <a href="{{ route('durum.statistics', $tenant_id) }}" class="btn btn-purple btn-block mb-2" style="background-color:#8e44ad; color: #fff;">
            <i class="fas fa-clipboard-check"></i> Durum İstatistikleri
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('asama.statistics', $tenant_id) }}" class="btn btn-warning btn-block mb-2">
            <i class="fas fa-stream"></i> Aşama İstatistikleri
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('depo.statistics', $tenant_id) }}" class="btn btn-brown btn-block mb-2" style="background-color:#6e4c1e; color: #fff;">
            <i class="fas fa-warehouse"></i> Depo İstatistikleri
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('ilce.statistics', $tenant_id) }}" class="btn btn-primary btn-block mb-2" style="background-color:#1c3aa9;">
            <i class="fas fa-map-marked-alt"></i> İlçe İstatistikleri
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('anket.statistics', $tenant_id) }}" class="btn btn-success btn-block mb-2" style="background-color:#4e6e3f;">
            <i class="fas fa-poll"></i> Anket İstatistikleri
        </a>
    </div>
</div>

      
          </div>
          </div>
          </div>
          </div>
          </div>

@endsection