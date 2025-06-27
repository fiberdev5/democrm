@extends('frontend.secure.user_master')
@section('user')

<div class="page-content">
  <div class="container-fluid">

    <div class="row pageDetail">
      <div class="col-12">

        <div class="card">
          <div class="card-header sayfaBaslik d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Stoklar</h5>
            <div>
              <a data-bs-toggle="modal" data-bs-target="#addStockModal" class="btn btn-success btn-sm me-1 addStock">
                <i class="fas fa-plus"></i> Ürün Ekle
              </a>
              <a data-bs-toggle="modal" data-bs-target="#supplierModal" class="btn btn-info btn-sm me-1 supplierBtn">
                <i class="fas fa-industry"></i> Konsinye Cihazlar
              </a>
              <a data-bs-toggle="modal" data-bs-target="#stockPrintModal" class="btn btn-warning btn-sm stockprintButton">
                <i class="fas fa-print"></i> Yazdır
              </a>
            </div>
          </div>

          <div class="card-body">

            <div class="d-flex justify-content-end mb-3">
              <div class="btn-group">
                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Filtrele <i class="mdi mdi-chevron-down"></i>
                </button>
                <div class="dropdown-menu p-3" style="min-width: 250px;">
                  <div class="item mb-2">
                    <div class="row align-items-center">
                      <label class="col-sm-5 mb-0">Durum</label>
                      <div class="col-sm-7">
                        <select name="durum" id="durum" class="form-select form-select-sm">
                          <option value="2">Hepsi</option>
                          <option value="1" selected>Çalışıyor</option>
                          <option value="0">Ayrıldı</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <!-- Buraya diğer filtreler de eklenebilir -->

                </div>
              </div>
            </div>

            <table id="datatableStock" class="table table-bordered dt-responsive nowrap" style="width: 100%;">
              <thead class="title">
                <tr>
                  <th style="width: 10px">ID</th>
                  <th>Tarih</th>
                  <th>Ürün Adı</th>
                  <th>Ürün Kodu</th>
                  <th>Fiyat</th>
                  <th>Adet</th>
                  <th>Raf</th>
                  <th>Marka / Cihaz</th>
                  <th style="width: 96px;">Düzenle</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>

            <!-- Toplam bilgileri gösterecek alan -->
            <div class="mt-3" style="font-size: 16px;">
              <div><strong>Toplam Adet:</strong> <span id="toplamAdet">0</span></div>
              <div><strong>Toplam Değer:</strong> <span id="toplamFiyat">0 ₺</span></div>
            </div>

          </div>
        </div>

      </div> <!-- end col -->
    </div> <!-- end row -->

  </div> <!-- container-fluid -->
</div> <!-- page-content -->

<!-- Modallar (Add & Edit) aynı kalabilir -->

<!-- Add Stock Modal -->
<div id="addStockModal" class="modal fade" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Ürün Ekle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">Yükleniyor...</div>
    </div>
  </div>
</div>

<!-- Edit Stock Modal -->
<div id="editStockModal" class="modal fade" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
       <h6 class="modal-title" id="editStockModalTitle">Stok Düzenle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="padding: 5px;">Yükleniyor...</div>
    </div>
  </div>
</div>


<script>
$(document).ready(function(){
  var firma_id = {{$firma->id}};

  // Ürün Ekle butonu
  $(".addStock").click(function(){
    $.ajax({
      url: "/"+ firma_id + "/stok-ekle/"
    }).done(function(data) {
      if ($.trim(data) === "-1") {
        window.location.reload(true);
      } else {
        $('#addStockModal').modal('show');
        $('#addStockModal .modal-body').html(data);
      }
    });
  });

  $("#addStockModal").on("hidden.bs.modal", function() {
    $(".modal-body").html("");
  });

  // Ürün Düzenle butonu
  $('#datatableStock').on('click', '.editStock', function () {
    var id = $(this).attr("data-bs-id");

    $.ajax({
      url: '/' + firma_id + '/stok/duzenle/' + id,
      dataType: 'json'
    }).done(function (data) {
      if ($.trim(data.html) === "-1") {
        window.location.reload(true);
      } else {
        $('#editStockModal').modal('show');
        $('#editStockModal .modal-body').html(data.html);
        $('#editStockModalTitle').text(data.urunAdi);
      }
    });
  });

  $("#editStockModal").on("hidden.bs.modal", function () {
    $(".modal-body").html("");
    $("#editStockModalTitle").text("Stok Düzenle");
  });

  // DataTable ayarları
  var table = $('#datatableStock').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "{{ route('stocks', $firma->id) }}",
      data: function (d) {
        d.durum = $('#durum').val();
      }
    },
    columns: [
      { data: 'id', name: 'id' },
      { data: 'created_at', name: 'created_at' },
      { data: 'urunAdi', name: 'urunAdi' },
      { data: 'urunKodu', name: 'urunKodu' },
      { data: 'toplamTutar', name: 'toplamTutar' },
      { data: 'adet', name: 'adet' },
      { data: 'raf_adi', name: 'raf_adi' },
      { data: 'marka_cihaz', name: 'marka_cihaz' },
      { data: 'action', orderable: false, searchable: false }
    ],
    order: [[0, 'desc']],
    language: {
      sDecimal: ",",
      sEmptyTable: "Tabloda herhangi bir veri mevcut değil",
      sInfo: "Ürün Sayısı: _TOTAL_ ",
      sInfoEmpty: "Kayıt yok",
      sInfoFiltered: "",
      sInfoPostFix: "",
      sInfoThousands:  ".",
      sLengthMenu: "_MENU_ ",
      sLoadingRecords: "Yükleniyor...",
      sProcessing: "İşleniyor...",
      sSearch: "Ürün Ara:",
      sZeroRecords: "Eşleşen kayıt bulunamadı",
      oPaginate: {
        sFirst: "İlk",
        sLast: "Son",
        sNext: '<i class="fas fa-angle-double-right"></i>',
        sPrevious: '<i class="fas fa-angle-double-left"></i>'
      },
      oAria: {
        sSortAscending: ": artan sütun sıralamasını aktifleştir",
        sSortDescending: ": azalan sütun sıralamasını aktifleştir"
      },
      select: {
        rows: {
          _: "%d kayıt seçildi",
          0: "",
          1: "1 kayıt seçildi"
        }
      }
    },
    drawCallback: function(settings) {
      var api = this.api();
      var json = api.ajax.json();

      if (json && json.toplamAdet) {
          $('#toplamAdet').text(json.toplamAdet);
          $('#toplamFiyat').text(json.toplamFiyat);
      }

      $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
    },
    dom: '<"top"f>rt<"bottom"ilp><"clear">',
    lengthMenu: [ [25, 50, 100, -1], [25, 50, 100, "Tümü"] ]
  });

  // Filtre değişirse tabloyu yenile
  $('#durum').change(function(){
    table.draw();
  });
});
</script>

@endsection
