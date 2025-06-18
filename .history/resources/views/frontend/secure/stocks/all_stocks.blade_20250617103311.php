@extends('frontend.secure.user_master')
@section('user')

<div class="page-content">
  <div class="container-fluid">
    <div class="row pageDetail">
      <div class="col-12">
        <div class="card">
          <div class="card-header sayfaBaslik">
            Stoklar
            <button type="button" class="btn btn-success btn-sm float-end stokEkleBtn"><i class="fas fa-plus"></i> Ürün Ekle</button>
          </div>
          <div class="card-body">
            <div class="mb-3 d-flex justify-content-between align-items-center">
              <div>
                <select id="durum" class="form-select form-select-sm d-inline-block w-auto me-2">
                  <option value="2">Hepsi</option>
                  <option value="1" selected>Stokta</option>
                  <option value="0">Verildi</option>
                </select>

                <select id="raf" class="form-select form-select-sm d-inline-block w-auto me-2">
                  <option value="">Raf Seç</option>
                  @foreach($rafListesi as $raf)
                    <option value="{{ $raf->id }}">{{ $raf->ad }}</option>
                  @endforeach
                </select>

                <select id="marka" class="form-select form-select-sm d-inline-block w-auto me-2">
                  <option value="">Marka Seç</option>
                  @foreach($markalar as $marka)
                    <option value="{{ $marka->id }}">{{ $marka->ad }}</option>
                  @endforeach
                </select>

                <select id="cihaz" class="form-select form-select-sm d-inline-block w-auto">
                  <option value="">Cihaz Seç</option>
                  @foreach($cihazlar as $cihaz)
                    <option value="{{ $cihaz->id }}">{{ $cihaz->ad }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <table id="datatableStok" class="table table-bordered dt-responsive nowrap" style="width:100%">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Tarih</th>
                  <th>Ürün Adı</th>
                  <th>Ürün Kodu</th>
                  <th>Fiyat</th>
                  <th>Adet</th>
                  <th>Raf</th>
                  <th>Marka / Cihaz</th>
                  <th>Düzenle</th>
                  <th>Sil</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Ekle -->
<div id="addStokModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addStokModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="addStokModalLabel">Stok Ekle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
      </div>
      <div class="modal-body">Yükleniyor...</div>
    </div>
  </div>
</div>

<!-- Modal Düzenle -->
<div id="editStokModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editStokModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="editStokModalLabel">Stok Düzenle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
      </div>
      <div class="modal-body">Yükleniyor...</div>
    </div>
  </div>
</div>

@push('scripts')
<script>
$(document).ready(function () {
  var firma_id = {{ $firma->id }};

  var table = $('#datatableStok').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "{{ route('stocks', $firma->id) }}",
      data: function (d) {
        d.durum = $('#durum').val();
        d.raf = $('#raf').val();
        d.marka = $('#marka').val();
        d.cihaz = $('#cihaz').val();
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
      { data: 'edit', orderable: false, searchable: false },
      { data: 'delete', orderable: false, searchable: false }
    ],
    "order": [[0, 'desc']],
    drawCallback: function () {
      // Modal açma - Ekle
      $('.stokEkleBtn').off('click').on('click', function () {
        $.get("/" + firma_id + "/stok/ekle", function (data) {
          $('#addStokModal').modal('show').find('.modal-body').html(data);
        });
      });

      // Modal açma - Düzenle
      $('#datatableStok').off('click', '.editStok').on('click', '.editStok', function () {
        var id = $(this).data('id');
        $.get("/" + firma_id + "/stok/duzenle/" + id, function (data) {
          $('#editStokModal').modal('show').find('.modal-body').html(data);
        });
      });

      // Modal temizleme
      $('#addStokModal, #editStokModal').on('hidden.bs.modal', function () {
        $(this).find('.modal-body').html('Yükleniyor...');
      });
    }
  });

  // Filtre değişikliklerinde tabloyu yenile
  $('#durum, #raf, #marka, #cihaz').on('change', function () {
    table.draw();
  });
});
</script>
@endpush

@endsection
