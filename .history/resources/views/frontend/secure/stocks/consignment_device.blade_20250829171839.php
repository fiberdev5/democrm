@extends('frontend.secure.user_master')
@section('user')

@php 
$stockLimit = $firma->plan()?->limits['konsinye'] ?? null; 
$stockAll = App\Models\Stock::where('firma_id', $firma->id)
  ->where('durum','1')
  ->where('urunKategori',  3)
  ->count();
@endphp

<div class="page-content">
  <div class="container-fluid">
    <div class="row pageDetail">
      <div class="col-12">
        <div class="card">
          <div class="card-header sayfaBaslik">
            Konsinye Cihazlar
          </div>
          <div class="card-body">
            @if(is_null($stockLimit) || $stockLimit == -1 || $stockAll < $stockLimit)
            <a data-bs-toggle="modal" data-bs-target="#addConsignmentModal" class="btn btn-success btn-sm addConsignment">
              <i class="fas fa-plus"></i> <span>Cihaz Ekle</span>
            </a>
            @else
              <a class="btn btn-success btn-sm addStock" disabled style="pointer-events: none; opacity: .4; cursor: default;">
                  <i class="fas fa-plus"></i> <span >Cihaz Ekle</span>
              </a>
              <!-- Bilgi ikonu ve yanında yazı -->
              <span class="text-muted ms-2" style="position:absolute;left: 168px;top: 47px;">
                  <i class="fas fa-info-circle me-1"></i>Stok limiti doldu (maks: {{ $stockLimit }})
              </span>
            @endif
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
  {{-- modal-dialog sınıfı JS tarafından eklenecek --}}
  <div class="modal-dialog"> 
    <div class="modal-content">
      {{-- İçerik AJAX ile buraya gelecek --}}
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
    // Ana modal temizleme işlemi
  $("#addConsignmentModal").on("hidden.bs.modal", function(e){
    var modal = $(this);
    
    if (e.target === this) {
      setTimeout(function(){
        if (!$('.modal.show').length) {
          console.log("Modal içeriği temizleniyor");
          modal.find(".modal-body").html("");
   
          // Tüm backdrop'ları kaldır
          $('.modal-backdrop').remove();
          // Body'den modal sınıflarını kaldır
          $('body').removeClass('modal-open').css('overflow', '').css('padding-right', '');
          // HTML'den de kaldır (bazı durumlarda gerekebilir)
          $('html').removeClass('modal-open').css('overflow', '').css('padding-right', '');
          
        } else {
          console.log("Başka modal açık, temizleme yapılmıyor");
        }
      }, 100);
    }
  });
// Alt modal'ların kapatılması sırasında da backdrop temizliği
  $('#addBrandModal, #addDeviceTypeModal, #addCategoryModal, #addShelfModal').on('hidden.bs.modal', function(){
    setTimeout(function(){
      // Eğer hiçbir modal açık değilse backdrop'u temizle
      if (!$('.modal.show').length) {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('overflow', '').css('padding-right', '');
        $('html').removeClass('modal-open').css('overflow', '').css('padding-right', '');
      }
    }, 50);
  });


    $('#datatableConsignment').on('click', '.editConsignment', function(){
    var id = $(this).data('bs-id');
    var modal = $('#editConsignmentModal'); 
   
    modal.find('.modal-dialog').removeClass('modal-xl').addClass('modal-xl'); // Boyutu ayarla (her seferinde)
    modal.find('.modal-content').html('<div class="modal-body text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Yükleniyor...</span></div></div>');
    
    // 2. Modal'ı göster
    modal.modal('show');
    
    // 3. Sunucudan veriyi çek
    $.ajax({
      url: "/" + firma_id + "/konsinye-cihazlar/duzenle/" + id,
      dataType: 'json',
      success: function(data){
        if($.trim(data.html) === "-1"){
          location.reload(true);
        } else {
          // 4. Gelen tüm HTML'i modal-content'in içine bas
          // Bu sayede gelen HTML'in kendi header, body, footer'ı kullanılır.
          modal.find('.modal-content').html(data.html);
        }
      },
      error: function() {
        // Hata durumunda içeriği temizle ve hata mesajı göster
        modal.find('.modal-content').html('<div class="modal-header"><h5 class="modal-title">Hata</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="alert alert-danger">İçerik yüklenirken bir hata oluştu.</div></div>');
      }
    });
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
      className: 'btn btn-warning btn-sm consignmentDevicePrintButton',
      title: '{{$firma->firma_adi }}',
      messageTop: '<div style="font-size:16px; font-weight:900; color:#000; text-align:left;">Konsinye Cihazlar</div>',
      exportOptions: {
        columns: [0,1,2,3,4,5,6,7]
      },
      customize: function (win) {
      $(win.document.body).find('h1').remove(); // büyük H1 başlığı kaldırılır
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
