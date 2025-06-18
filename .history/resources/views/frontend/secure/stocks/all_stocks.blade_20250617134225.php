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
              </div>
              <table id="datatableStock" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
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

<!-- add modal content -->
<div id="addStockModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="myModalLabel">Ürün Ekle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<!-- edit modal content -->
<div id="editStockModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="myModalLabel">Stok Düzenle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script type="text/javascript">
$(document).ready(function(){
  var firma_id = {{$firma->id}};
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
});
</script>


<script>
$(document).ready(function () {
  let firma_id = {{ $firma->id }};

  const table = $('#datatableStock').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "{{ route('stocks', $tenant->id) }}",
      data: function (d) {
        d.personel = $('#personel').val();
        d.raf = $('#raf').val();
        d.marka = $('#marka').val();
        d.cihaz = $('#cihaz').val();
        d.durum = $('#durum').val();
      }
    },
    columns: [
      {
        data: 'id',
        render: (data, type, row) => `<a href="javascript:void(0);" class="t-link editStock" data-bs-id="${row.id}" data-bs-toggle="modal" data-bs-target="#stokModal">${data}</a>`
      },
      {
        data: 'tarih',
        render: (data, type, row) => `<a href="javascript:void(0);" class="t-link editStock" data-bs-id="${row.id}" data-bs-toggle="modal" data-bs-target="#stokModal">${data}</a>`
      },
      {
        data: 'urun_adi',
        render: (data, type, row) => `<a href="javascript:void(0);" class="t-link editStock" data-bs-id="${row.id}" data-bs-toggle="modal" data-bs-target="#stokModal">${data}</a>`
      },
      {
        data: 'urun_kodu',
        render: (data, type, row) => `<a href="javascript:void(0);" class="t-link editStock" data-bs-id="${row.id}" data-bs-toggle="modal" data-bs-target="#stokModal">${data}</a>`
      },
      { data: 'fiyat' },
      { data: 'adet' },
      { data: 'raf' },
      { data: 'marka_cihaz' },
      {
        data: 'id',
        orderable: false,
        searchable: false,
        render: (data) => `
          <a href="javascript:void(0);" class="btn btn-warning btn-sm editStock" data-bs-id="${data}" data-bs-toggle="modal" data-bs-target="#stokModal">
            <i class="fas fa-edit"></i>
          </a>`
      },
      {
        data: 'id',
        orderable: false,
        searchable: false,
        render: (data) => `
          <a href="/${firma_id}/stok/sil/${data}" class="btn btn-danger btn-sm" id="delete" title="Sil">
            <i class="fas fa-trash-alt"></i>
          </a>`
      }
    ],
    order: [[0, 'desc']],
    drawCallback: function () {
      $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
    },
    language: {
      decimal: ",",
      emptyTable: "Tabloda herhangi bir veri mevcut değil",
      info: "Toplam Kayıt: _TOTAL_",
      infoEmpty: "Kayıt yok",
      infoFiltered: "",
      lengthMenu: "_MENU_",
      loadingRecords: "Yükleniyor...",
      processing: "İşleniyor...",
      search: "Stok Ara:",
      zeroRecords: "Eşleşen kayıt bulunamadı",
      paginate: {
        first: "İlk",
        last: "Son",
        next: "<i class='fas fa-angle-double-right'></i>",
        previous: "<i class='fas fa-angle-double-left'></i>"
      },
      aria: {
        sortAscending: ": artan sütun sıralamasını aktifleştir",
        sortDescending: ": azalan sütun sıralamasını aktifleştir"
      },
      select: {
        rows: {
          _: "%d kayıt seçildi",
          0: "",
          1: "1 kayıt seçildi"
        }
      }
    },
    dom: '<"top"f>rt<"bottom"ilp><"clear">',
    lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Tümü"]]
  });

  $('#personel, #raf, #marka, #cihaz, #durum').change(function () {
    table.draw();
  });
});
</script>


@endsection
