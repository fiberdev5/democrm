@extends('frontend.secure.user_master')
@section('user')
<div class="page-content servis-istatistik">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
      <div class="container">
    <h4 class="mb-4">İstatistik Filtreleme</h4>
    
    <form id="filtreForm" class="row g-3 mb-4">
        @csrf
        <div class="col-md-4">
            <label for="baslangicTarihi" class="form-label">Başlangıç Tarihi</label>
            <input type="text" class="form-control" id="baslangicTarihi" name="baslangic" autocomplete="off">
        </div>
        <div class="col-md-4">
            <label for="bitisTarihi" class="form-label">Bitiş Tarihi</label>
            <input type="text" class="form-control" id="bitisTarihi" name="bitis" autocomplete="off">
        </div>
        <div class="col-md-4 align-self-end">
            <button type="submit" class="btn btn-primary w-100">Filtrele</button>
        </div>
    </form>

    <div id="sonucAlani"></div>
</div>
    </div>
</div>
@endsection