@extends('frontend.secure.user_master')
@section('user')

<div class="page-content">
  <div class="container-fluid">
    <div class="row pageDetail">
      <div class="col-12">
        <div class="card">
          <div class="card-header sayfaBaslik">Stoklar</div>
          <div class="card-body">

            <!-- Butonlar -->
            <a data-bs-toggle="modal" data-bs-target="#addStockModal" class="btn btn-success btn-sm addStock">
              <i class="fas fa-plus"></i> <span>Ürün Ekle</span>
            </a>
            <a data-bs-toggle="modal" data-bs-target="#supplierModal" class="btn btn-info btn-sm supplierBtn">
              <i class="fas fa-industry"></i> Konsinye Cihazlar 
            </a>
            <a data-bs-toggle="modal" data-bs-target="#stockPrintModal" class="btn btn-warning btn-sm stockprintButton">
              <i class="fas fa-print"></i> Yazdır
            </a>

            <!-- Filtre -->
            <div class="searchWrap float-end">
              <div class="btn-group mb-2">
                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown">
                  Filtrele <i class="mdi mdi-chevron-down"></i>
                </button>
                <div class="dropdown-menu p-3" style="min-width: 250px;">

                  @foreach ([
                      ['Raf', 'raf', $rafListesi, 'raf_adi'],
                      ['Marka', 'marka', $markalar, 'marka'],
                      ['Cihaz', 'cihaz', $cihazlar, 'cihaz'],
                      ['Personel', 'personel', $personeller, 'name']
                  ] as [$label, $id, $list, $field])
                  <div class="item mb-2">
                    <div class="row align-items-center">
                      <label class="col-sm-5 mb-0">{{ $label }}</label>
                      <div class="col-sm-7">
                        <select id="{{ $id }}" class="form-select form-select-sm">
                          <option value="">Hepsi</option>
                          @foreach($list as $item)
                            <option value="{{ $item->id }}">{{ $item->$field }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>
                  @endforeach

                </div>
              </div>
            </div>

            <!-- Tablo -->
            <table id="datatableStock" class="table table-bordered dt-responsive nowrap" style="width: 100%;">
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
                  <th>Düzenle</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>

            <!-- Toplam -->
            <div class="mt-3" style="font-size: 16px;">
              <div><strong>Toplam Adet:</strong> <span id="toplamAdet">0</span></div>
              <div><strong>Toplam Değer:</strong> <span id="toplamFiyat">0 TL</span></div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@include('frontend.secure.stocks.partials.modals')

<script>
$(document).ready(function(){
  const firma_id = {{ $firma->id }};

  // Ürün ekle modal
  $('.addStock').click(function(){
    $.ajax({
      url: "/" + firma_id + "/stok-ekle/"
    }).done(function(data){
      if(data.trim() === "-1"){ location.reload(); }
      else {
        $('#addStockModal').modal('show').find('.modal-body').html(data);
      }
    });
  });

  // Ürün düzenle modal
  $('#datatableStock').on('click', '.editStock', function(){
    const id = $(this).data('bs-id');
    $.ajax({
      url: "/" + firma_id + "/stok/duzenle/" + id,
      dataType: 'json'
    }).done(function(data){
      if(data.html.trim() === "-1"){ location.reload(); }
      else {
        $('#editStockModal').modal('show');
        $('#editStockModal .modal-body').html(data.html);
        $('#editStockModalLabel').text(data.urunAdi);
      }
    });
  });

  // Modal temizliği
  $('#addStockModal, #editStockModal').on('hidden.bs.modal', function(){
    $(this).find('.modal-body').html('');
    $('#editStockModalLabel').text("Stok Düzenle");
  });

  // DataTable
  const table = $('#datatableStock').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "{{ route('stocks', $firma->id) }}",
      data: function(d){
        d.search = $('input[type="search"]').val();
        d.raf = $('#raf').val();
        d.marka = $('#marka').val();
        d.cihaz = $('#cihaz').val();
        d.personel = $('#personel').val();
      }
    },
    columns: [
      { data: 'id' },
      { data: 'created_at' },
      { data: 'urunAdi' },
      { data: 'urunKodu' },
      { data: 'toplamTutar' },
      { data: 'adet' },
      { data: 'raf_adi' },
      { data: 'marka_cihaz' },
      { data: 'action', orderable: false, searchable: false }
    ],
    order: [[0, 'desc']],
    drawCallback: function(settings){
      let json = this.api().ajax.json();
      $('#toplamAdet').text(json?.toplamAdet ?? 0);
      $('#toplamFiyat').text(json?.toplamFiyat ?? '0 TL');
      $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
    },
    dom: '<"top"f>rt<"bottom"ilp><"clear">',
    lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Tümü"]],
    language: {
      sSearch: "Ürün Ara:",
      sEmptyTable: "Kayıt yok",
      sZeroRecords: "Sonuç bulunamadı",
      sInfo: "Toplam: _TOTAL_ ürün",
      sLengthMenu: "_MENU_ kayıt göster",
      oPaginate: {
        sPrevious: "<i class='mdi mdi-chevron-left'></i>",
        sNext: "<i class='mdi mdi-chevron-right'></i>"
      }
    }
  });

  $('#raf, #marka, #cihaz, #personel').change(function(){
    table.draw();
  });
});
</script>
@endsection
