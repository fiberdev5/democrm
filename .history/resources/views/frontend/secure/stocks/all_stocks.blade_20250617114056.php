@extends('frontend.secure.user_master')
@section('user')

<div class="page-content">
  <div class="container-fluid">
    <div class="row pageDetail">
      <div class="col-12">
        <div class="card">
          <div class="card-header sayfaBaslik">
            Stoklar
          </div>
          <div class="card-body">
            <table id="datatableStock" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
              <div class="d-flex justify-content-between mb-3">
                <div class="btn-group">
                  <a data-bs-toggle="modal" data-bs-target="#addStockModal" class="btn btn-success btn-sm addStock">
                    <i class="fas fa-plus"></i> <span>Ürün Ekle</span>
                  </a>
                  <a data-bs-toggle="modal" data-bs-target="#supplierModal" class="btn btn-info btn-sm supplierBtn">
                    <i class="fas fa-industry"></i> Tedarikçiler
                  </a>
                  <a data-bs-toggle="modal" data-bs-target="#stockPrintModal" class="btn btn-warning btn-sm stockprintButton">
                    <i class="fas fa-print"></i> Yazdır
                  </a>
                </div>
                <div class="searchWrap float-end mb-2">
  <div class="btn-group">
    <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
      Filtrele <i class="mdi mdi-chevron-down"></i>
    </button>
    <div class="dropdown-menu px-3 py-2" style="min-width: 300px;">
      <div class="mb-2">
        <label class="form-label">Personel</label>
        <select class="form-select" id="personel">
          <option value="">Hepsi</option>
          @foreach($personeller as $p)
            @if($p->grup == 244 && $p->durum == 1)
              <option value="{{ $p->id }}">{{ $p->adsoyad }}</option>
            @endif
          @endforeach
        </select>
      </div>
      <div class="mb-2">
        <label class="form-label">Raf</label>
        <select class="form-select" id="raf">
          <option value="">Hepsi</option>
          @foreach($depolar as $depo)
            <option value="{{ $depo->id }}">{{ $depo->kategori }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-2">
        <label class="form-label">Marka</label>
        <select class="form-select" id="marka">
          <option value="">Hepsi</option>
          @foreach($markalar as $marka)
            <option value="{{ $marka->id }}">{{ $marka->marka }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-2">
        <label class="form-label">Cihaz</label>
        <select class="form-select" id="cihaz">
          <option value="">Hepsi</option>
          @foreach($cihazlar as $cihaz)
            <option value="{{ $cihaz->id }}">{{ $cihaz->cihaz }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Stok Durumu</label>
        <select class="form-select" id="durum">
          <option value="">Hepsi</option>
          <option value="1">Stokta Var</option>
          <option value="2">Stokta Yok</option>
        </select>
      </div>
    </div>
  </div>
</div>
         
              </div>
              <thead class="title">
                  <tr>
                  <th>ID</th>
                  <th>Tarih</th>
                  <th>Ürün Adı</th>
                  <th>Ürün Kodu</th>
                  <th>Fiyat</th>
                  <th>Adet</th>
                  <th>Raf</th>
                  <th>Marka / Cihaz</th>
                  <th data-priority="1" style="width: 96px;">Düzenle</th>
                  <th data-priority="1" style="width: 96px;">Sil</th>
                  </tr>
                </thead>
              <tbody>
               
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Alanı -->
<div id="stokModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Stok</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">Yükleniyor...</div>
    </div>
  </div>
</div>

@push('scripts')
<script>
$(document).ready(function () {
  let firma_id = {{ $firma->id }};
  
  const table = $('#datatableStock').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "{{ route('stocks', $firma->id) }}",
      data: function(d) {
        d.personel = $('#personel').val();
        d.raf = $('#raf').val();
        d.marka = $('#marka').val();
        d.cihaz = $('#cihaz').val();
        d.durum = $('#durum').val();
      }
    },
    columns: [
      { data: 'id' },
      { data: 'tarih' },
      { data: 'urun_adi' },
      { data: 'urun_kodu' },
      { data: 'fiyat' },
      { data: 'adet' },
      { data: 'raf' },
      { data: 'marka_cihaz' },
    ],
    drawCallback: function() {
      $(".stokEkleBtn, .tedarikcilerBtn").off('click').on('click', function(){
        let url = $(this).hasClass("stokEkleBtn") ? `/{{ $firma->id }}/stok/ekle` : `/{{ $firma->id }}/tedarikciler`;
        $.get(url, function(data){
          $('#stokModal').modal('show').find('.modal-body').html(data);
        });
      });
    }
  });

  $('#personel, #raf, #marka, #cihaz, #durum').change(function() {
    table.draw();
  });
});
</script>
@endpush

@endsection
