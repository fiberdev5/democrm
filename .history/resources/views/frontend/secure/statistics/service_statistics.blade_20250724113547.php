@extends('frontend.secure.user_master')

@section('content')
<div id="wrapper">
  @include('frontend.secure.includes.menu')

  <div id="content-wrapper">
    <div class="container-fluid">

      <div class="girisEkrani istatistiklerGirisEkrani">
        @include('frontend.secure.includes.istatistikler-menu')
      </div>

      <div class="card mb-3 pageDetail istatistikSonuclarPage servisSonuclari">
        <div class="card-header sayfaBaslik d-flex justify-content-between align-items-center">
          <span>Servis İstatistikleri</span>
          <div class="dropdown">
            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
              FİLTRELE <span class="caret"></span>
            </button>
            <ul class="dropdown-menu p-3" style="min-width:300px">
              <form action="{{ route('frontend.servis.istatistik', $tenant_id) }}" method="GET">
                <div class="form-group">
                  <label for="tarih1">Başlangıç Tarihi</label>
                  <input type="date" name="tarih1" class="form-control">
                </div>
                <div class="form-group">
                  <label for="tarih2">Bitiş Tarihi</label>
                  <input type="date" name="tarih2" class="form-control">
                </div>
                <button type="submit" class="btn btn-success btn-block mt-2">Filtrele</button>
              </form>
            </ul>
          </div>
        </div>

        <div class="card-body">
          {{-- Buraya servis istatistik sonuçlarını tablo veya kutular halinde ekle --}}
          <div class="row">
            <div class="col-md-4">
              <div class="card bg-light p-3">
                <strong>Toplam Servis:</strong> 123
              </div>
            </div>
            <div class="col-md-4">
              <div class="card bg-light p-3">
                <strong>Başarılı Servis:</strong> 97
              </div>
            </div>
            <div class="col-md-4">
              <div class="card bg-light p-3">
                <strong>İptal Edilen:</strong> 26
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
