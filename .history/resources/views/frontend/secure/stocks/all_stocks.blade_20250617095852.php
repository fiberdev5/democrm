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
            <div class="mb-2 d-flex justify-content-between">
              <div>
                  <button type="button" class="btn btn-success btn-sm stokEkleBtn" @if($kalanGun < 0) disabled @endif><i class="fas fa-plus"></i> Ürün Ekle</button>
                  <button type="button" class="btn btn-info btn-sm tedarikcilerBtn" @if($kalanGun < 0) disabled @endif><i class="fas fa-industry"></i> Tedarikçiler</button>
                  <button type="button" class="btn btn-warning btn-sm stokYazdirBtn"><i class="fas fa-print"></i> Yazdır</button>
              </div>

              <div class="btn-group">
                <button class="btn btn-dark btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Filtrele <i class="mdi mdi-chevron-down"></i>
                </button>
                <div class="dropdown-menu p-2">
                  <div class="mb-2">
                    <label>Personel</label>
                    <select id="personel" class="form-select">
                      <option value="">Hepsi</option>
                      @foreach($personeller as $personel)
                        <option value="{{ $personel->id }}">{{ $personel->adsoyad }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="mb-2">
                    <label>Raf</label>
                    <select id="raf" class="form-select">
                      <option value="">Hepsi</option>
                      @foreach($rafListesi as $raf)
                        <option value="{{ $raf->id }}">{{ $raf->ad }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="mb-2">
                    <label>Marka</label>
                    <select id="marka" class="form-select">
                      <option value="">Hepsi</option>
                      @foreach($markalar as $marka)
                        <option value="{{ $marka->id }}">{{ $marka->ad }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="mb-2">
                    <label>Cihaz</label>
                    <select id="cihaz" class="form-select">
                      <option value="">Hepsi</option>
                      @foreach($cihazlar as $cihaz)
                        <option value="{{ $cihaz->id }}">{{ $cihaz->ad }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="mb-2">
                    <label>Durum</label>
                    <select id="durum" class="form-select">
                      <option value="2">Hepsi</option>
                      <option value="1" selected>Stokta</option>
                      <option value="0">Verildi</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <table id="datatableStok" class="table table-bordered dt-responsive nowrap" style="width:100%">
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
                  @if($kalanGun >= 0)
                    <th>Düzenle</th>
                    <th>Sil</th>
                  @endif
                </tr>
              </thead>
              <tbody></tbody>
            </table>

            <div class="toplamWrap mt-3 text-end">
              <strong>Toplam Adet:</strong> <span id="toplamAdet">0</span>
              &nbsp;&nbsp;|&nbsp;&nbsp;
              <strong>Toplam Tutar:</strong> <span id="toplamFiyat">0 ₺</span>
            </div>

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
  
  const table = $('#datatableStok').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "{{ route('stoklar.data', $firma->id) }}",
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
      @if($kalanGun >= 0)
        { data: 'edit', orderable: false, searchable: false },
        { data: 'delete', orderable: false, searchable: false },
      @endif
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
