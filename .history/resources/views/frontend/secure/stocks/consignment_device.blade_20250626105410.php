@extends('frontend.secure.user_master')
@section('user')

<div class="page-content">
  <div class="container-fluid">
    <div class="row pageDetail">
      <div class="col-12">
        <div class="card">
          <div class="card-header sayfaBaslik">
            Konsinye Cihazlar
          </div>
          <div class="card-body">

            <a data-bs-toggle="modal" data-bs-target="#addConsignmentModal" class="btn btn-success btn-sm addConsignment">
              <i class="fas fa-plus"></i> <span>Cihaz Ekle</span>
            </a>
            {{-- <a data-bs-toggle="modal" data-bs-target="#consignmentPrintModal" class="btn btn-warning btn-sm consignmentPrintButton">
              <i class="fas fa-print"></i> Yazdır
            </a> --}}

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

            <table id="datatableConsignment" class="table table-bordered dt-responsive nowrap" style="width: 100%;">
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
<div id="addConsignmentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addConsignmentModalLabel" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="addConsignmentModalLabel">Konsinye Cihaz Ekle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">Yükleniyor...</div>
    </div>
  </div>
</div>

<div id="editConsignmentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editConsignmentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="editConsignmentModalLabel">Cihaz Düzenle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">Yükleniyor...</div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  var firma_id = {{ $firma->id }};

  $(".addConsignment").click(function(){
    $.ajax({
      url: "/"+firma_id+"/konsinye-cihaz-ekle/"
    }).done(function(data){
      if($.trim(data) === "-1"){
        location.reload(true);
      } else {
        $('#addConsignmentModal').modal('show');
        $('#addConsignmentModal .modal-body').html(data);
      }
    });
  });

  $("#addConsignmentModal").on("hidden.bs.modal", function(){
    $(this).find(".modal-body").html("");
  });

  $('#datatableConsignment').on('click', '.editConsignment', function(){
    var id = $(this).data('bs-id');

    $.ajax({
      url: "/"+firma_id+"/konsinye-cihazlar/duzenle/"+id,
      dataType: 'json'
    }).done(function(data){
      if($.trim(data.html) === "-1"){
        location.reload(true);
      } else {
        $('#editConsignmentModal').modal('show');
        $('#editConsignmentModal .modal-body').html(data.html);
        $('#editConsignmentModalLabel').text(data.urunAdi);
      }
    });
  });

  $("#editConsignmentModal").on("hidden.bs.modal", function(){
    $(this).find(".modal-body").html("");
    $('#editConsignmentModalLabel').text("Cihaz Düzenle");
  });
});
</script>

<script>
$(document).ready(function () {
  var table = $('#datatableConsignment').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ route('consignmentdevice.data', $firma->id) }}",
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
        { data: 'toplamTutar', name: 'toplamTutar' , orderable: false },
        { data: 'adet', name: 'adet' , orderable: false },
        { data: 'raf_adi', name: 'raf_adi' },
        { data: 'marka_cihaz', name: 'marka_cihaz' },
        { data: 'action', orderable: false, searchable: false }
      ],
      order: [[0, 'desc']],
  dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rt<"d-flex justify-content-between align-items-center mt-2"ilp>',
  buttons: [
    {
      extend: 'print',
      text: '<i class="fas fa-print"></i> Yazdır',
      className: 'btn btn-warning btn-sm',
      title: 'Stok Listesi',
      exportOptions: {
        columns: [0,1,2,3,4,5,6,7]
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

      // dom: '<"top"f>rt<"bottom"ilp><"clear">',
      lengthMenu: [ [25, 50, 100, -1], [25, 50, 100, "Tümü"] ]
  });

  $('#raf, #marka, #cihaz, #personel').change(function() {
      table.draw();
  });
  $('#printButton').on('click', function () {
  table.button(0).trigger(); // DataTables içindeki yazdır butonunu çalıştırır
});
});
</script>

@endsection
