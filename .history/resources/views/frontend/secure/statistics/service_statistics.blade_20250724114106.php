@extends('frontend.secure.user_master')

@section('user')
<div id="wrapper">
  <div id="content-wrapper">
    <div class="container-fluid">
      <div class="girisEkrani istatistiklerGirisEkrani">
      </div>
      <div class="card mb-3 pageDetail istatistikSonuclarPage servisSonuclari">
        <div class="card-header sayfaBaslik d-flex justify-content-between align-items-center">
          <span>Servis İstatistikleri</span>
          <div class="dropdown">
          </div>
        </div>
        <div class="card-body">
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