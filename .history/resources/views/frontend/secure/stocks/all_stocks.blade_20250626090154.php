@extends('frontend.secure.user_master')

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

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

            <a data-bs-toggle="modal" data-bs-target="#addStockModal" class="btn btn-success btn-sm addStock">
              <i class="fas fa-plus"></i> <span>Ürün Ekle</span>
            </a>
            <a href="{{ route('consignmentdevice', $firma->id) }}"  data-bs-target="#supplierModal" class="btn btn-info btn-sm supplierBtn">
              <i class="fas fa-industry"></i> Konsinye Cihazlar 
            </a>
            <button class="btn btn-warning btn-sm stockprintButton">
              <i class="fas fa-print"></i> Yazdır
            </button>

            <!-- Filtre dropdown butonu -->
            <div class="searchWrap float-end">
              <div class="btn-group mb-2 ">
                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Filtrele <i class="mdi mdi-chevron-down"></i>
                </button>
                <div class="dropdown-menu p-3" style="min-width: 250px;">
                  
                  <!-- Raf -->
                  <div class="item mb-2">
                    <div class="row align-items-center">
                      <label class="col-sm-5 mb-0">Raf</label>
                      <div class="col-sm-7">
                        <select id="raf" class="form-select form-select-sm">
                          <option value="">Hepsi</option>
                          @foreach($rafListesi as $raf)
                            <option value="{{ $raf->id }}">{{ $raf->raf_adi }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Marka -->
                  <div class="item mb-2">
                    <div class="row align-items-center">
                      <label class="col-sm-5 mb-0">Marka</label>
                      <div class="col-sm-7">
                        <select id="marka" class="form-select form-select-sm">
                          <option value="">Hepsi</option>
                          @foreach($markalar as $marka)
                            <option value="{{ $marka->id }}">{{ $marka->marka }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>

                  <!-- Cihaz -->
                  <div class="item mb-2">
                    <div class="row align-items-center">
                      <label class="col-sm-5 mb-0">Cihaz</label>
                      <div class="col-sm-7">
                        <select id="cihaz" class="form-select form-select-sm">
                          <option value="">Hepsi</option>
                          @foreach($cihazlar as $cihaz)
                            <option value="{{ $cihaz->id }}">{{ $cihaz->cihaz }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>

                  <!-- Personel -->
                  <div class="item">
                    <div class="row align-items-center">
                      <label class="col-sm-5 mb-0">Personel</label>
                      <div class="col-sm-7">
                        <select id="personel" class="form-select form-select-sm">
                          <option value="">Hepsi</option>
                          @foreach($personeller as $personel)
                            <option value="{{ $personel->id }}">{{ $personel->name }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>

                </div>
              </div><!-- /btn-group -->
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
              <tbody></tbody>
            </table>

            <div class="mt-3" style="font-size: 16px;">
              <div><strong>Toplam Adet:</strong> <span id="toplamAdet">0</span></div>
              <div><strong>Toplam Değer:</strong> <span id="toplamFiyat">0 ₺</span></div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modallar -->
<div id="addStockModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addStockModalLabel" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="addStockModalLabel">Ürün Ekle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">Yükleniyor...</div>
    </div>
  </div>
</div>

<div id="editStockModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editStockModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="editStockModalLabel">Stok Düzenle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">Yükleniyor...</div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  var firma_id = {{ $firma->id }};

  $(".addStock").click(function(){
    $.ajax({
      url: "/"+firma_id+"/stok-ekle/"
    }).done(function(data){
      if($.trim(data) === "-1"){
        location.reload(true);
      } else {
        $('#addStockModal').modal('show');
        $('#addStockModal .modal-body').html(data);
      }
    });
  });

  $("#addStockModal").on("hidden.bs.modal", function(){
    $(this).find(".modal-body").html("");
  });

  $('#datatableStock').on('click', '.editStock', function(){
    var id = $(this).data('bs-id');
    $.ajax({
      url: "/"+firma_id+"/stok/duzenle/"+id,
      dataType: 'json'
    }).done(function(data){
      if($.trim(data.html) === "-1"){
        location.reload(true);
      } else {
        $('#editStockModal').modal('show');
        $('#editStockModal .modal-body').html(data.html);
        $('#editStockModalLabel').text(data.urunAdi);
      }
    });
  });

  $("#editStockModal").on("hidden.bs.modal", function(){
    $(this).find(".modal-body").html("");
    $('#editStockModalLabel').text("Stok Düzenle");
  });
  });
</script>

<script>
// DataTable
$(document).ready(function () {
  var table = $('#datatableStock').DataTable({
      processing: true,
      serverSide: true,
    ajax: {
      url: "{{ route('stocks', $firma->id) }}",
      data: function (d) {
        d.raf = $('#raf').val();
        d.marka = $('#marka').val();
        d.cihaz = $('#cihaz').val();
        d.personel = $('#personel').val();
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
         dom: 'Bfrtip', // B = Buttons, f = filter, r = processing, t = table, i = info, p = pagination
      buttons: [
        {
          extend: 'print',
          text: '<i class="fas fa-print"></i> Tablo Yazdır',
          className: 'btn btn-primary btn-sm d-none', // d-none ile gizle, kendi butonumuz tetikleyecek
          title: 'Stok Listesi - {{ $firma->firma_adi ?? "Firma Adı" }}',
          messageTop: function() {
            return '<div style="text-align: center; margin-bottom: 20px;">' +
                   '<h3>{{ $firma->firma_adi ?? "Stok Yönetimi" }}</h3>' +
                   '<p><strong>Yazdırma Tarihi:</strong> ' + new Date().toLocaleDateString('tr-TR') + '</p>' +
                   '</div>';
          },
          messageBottom: function() {
            return '<div style="margin-top: 20px; text-align: right;">' +
                   '<p><strong>Toplam Adet:</strong> ' + $('#toplamAdet').text() + '</p>' +
                   '<p><strong>Toplam Değer:</strong> ' + $('#toplamFiyat').text() + '</p>' +
                   '</div>';
          },
          exportOptions: {
            columns: ':not(.no-print)' // no-print class'ı olan sütunları hariç tut
          },
          customize: function (win) {
            // 6. ADIM: Yazdırma sayfasını özelleştirin
            $(win.document.body).css({
              'font-family': 'Arial, sans-serif',
              'font-size': '12pt'
            });
            
            $(win.document.body).find('table')
              .addClass('compact')
              .css({
                'font-size': '10pt',
                'border-collapse': 'collapse',
                'width': '100%'
              });
              
            $(win.document.body).find('table th, table td')
              .css({
                'border': '1px solid #ddd',
                'padding': '8px',
                'text-align': 'left'
              });
              
            $(win.document.body).find('table th')
              .css({
                'background-color': '#f2f2f2',
                'font-weight': 'bold'
              });
          }
        },
        {
          extend: 'excel',
          text: '<i class="fas fa-file-excel"></i> Excel İndir',
          className: 'btn btn-success btn-sm d-none', // d-none ile gizle
          title: 'Stok_Listesi_' + new Date().toISOString().slice(0,10),
          exportOptions: {
            columns: ':not(.no-print)'
          }
        }
      ],
      
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
      
      dom: '<"top"f>rt<"bottom"ilp><"clear">', // üstte arama, altta length info paginate
      lengthMenu: [ [25, 50, 100, -1], [25, 50, 100, "Tümü"] ]

      
  });
    // Filtreler değiştiğinde tabloyu yeniden çiz
  $(' #raf, #marka, #cihaz, #personel').change(function() {
      table.draw();
  });
   $('.stockprintButton').click(function(e) {
    e.preventDefault();
    table.button('.buttons-print').trigger();
  });
});

</script>

<style>
@media print {
  .no-print {
    display: none !important;
  }
  
  .dt-buttons {
    display: none !important;
  }
  
  .dataTables_filter,
  .dataTables_length,
  .dataTables_info,
  .dataTables_paginate {
    display: none !important;
  }
}

/* DataTables buttons'ların görünümünü iyileştir */
.dt-buttons {
  margin-bottom: 15px;
}

.dt-buttons .btn {
  margin-right: 5px;
}
</style>


@endsection
