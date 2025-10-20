@extends('frontend.secure.user_master')
@section('user')

{{-- Daterangepicker için gerekli kütüphaneler --}}
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

@php 
  if ($firma->isOnTrial()) {
    $stockLimit = $firma->stokSayisi ?? null;
  } else {
    $stockLimit = $firma->plan()?->limits['stocks'] ?? null;
  }
$stockAll = App\Models\Stock::where('firma_id', $firma->id)
  ->where('durum','1')
  ->where('urunKategori', '!=', 3)
  ->count();
@endphp

<style>

@media (max-width: 767px) {

.searchWrap{margin-top: 0px !important;}
    .pageDetail .searchWrap{}
    .pageDetail .searchWrap{margin-bottom: 0px !important;}
 div.dataTables_filter input{margin-left: 0 !important;}
 .dataTables_filter{
margin-right: 0px !important;
    }
}
</style>

<div class="page-content">
  <div class="container-fluid">
    <div class="row pageDetail">
      <div class="col-12">
        <div class="card">
          <div class="card-header sayfaBaslik">
            Depo Stoklar
          </div>
          <div class="card-body">
            @if(is_null($stockLimit) || $stockLimit == -1 || $stockAll < $stockLimit)
              <a data-bs-toggle="modal" data-bs-target="#addStockModal" class="btn btn-success btn-sm addStock">
                  <i class="fas fa-plus"></i> <span>Stok Kartı Ekle</span>
              </a>
            @else
              <a class="btn btn-success btn-sm addStock" disabled style="pointer-events: none; opacity: .4; cursor: default;">
                  <i class="fas fa-plus"></i> <span >Stok Kartı Ekle</span>
              </a>
              <!-- Bilgi ikonu ve yanında yazı -->
              <span class="text-muted ms-2" style="position:absolute;left: 330px;top: 47px;">
                  <i class="fas fa-info-circle me-1"></i>Stok limiti doldu (maks: {{ $stockLimit }})
              </span>
            @endif
            <a href="{{ route('consignmentdevice', $firma->id) }}"  data-bs-target="#supplierModal" class="btn btn-info btn-sm supplierBtn">
              <i class="fas fa-industry"></i> Konsinye Cihazlar 
            </a>
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
                            <option value="{{ $personel->user_id }}"
                              {{ request('personel') == $personel->user_id ? 'selected' : '' }}>{{ $personel->name }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>

                   {{-- YENİ: TARİH ARALIĞI FİLTRESİ --}}
  <div class="item">
    <div class="row align-items-center">
      <label class="col-sm-5 mb-0">Tarih Aralığı:</label>
      <div class="col-sm-7">
        <input id="daterangeStock" class="form-control form-control-sm mb-2">
        <div class="tarihAraligi d-flex flex-wrap gap-1">
          <button id="lastYearStock" class="btn btn-sm btn-secondary">Son 1 Yıl</button>
          <button id="lastMonthStock" class="btn btn-sm btn-secondary">Son 1 Ay</button>
          <button id="lastWeekStock" class="btn btn-sm btn-secondary">Son 7 Gün</button>
          <button id="yesterdayStock" class="btn btn-sm btn-secondary">Dün</button>
          <button id="todayStock" class="btn btn-sm btn-secondary">Bugün</button>
        </div>
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
                  <th>Satış Fiyatı</th>
                  <th>Adet</th>
                  <th>Raf</th>
                  <th>Marka/Cihaz</th>
                  <th style="width: 96px;">Düzenle</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>

            <!-- Toplam alanı -->
            <div class="tableToplamaAlani" >
              <div class="row r1" style="display: flex; align-items: center; justify-content: center;">
                <div class="sol"><strong>Toplam Bilgiler</strong></div>
                <div class="sag">
                  <div class="tur t1"><span>Toplam Ürün: </span><span id="toplamAdet">0</span></div>
                  <div class="tur t4"><span>Depo Kazanç: </span><span id="toplamFiyat">0 ₺</span></div>
                </div>
              </div>
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
        <h6 class="modal-title" id="addStockModalLabel">Stok Kartı Ekle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">Yükleniyor...</div>
    </div>
  </div>
</div>


<div id="editStockModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editStockModalLabel" aria-hidden="true">
  {{-- modal-dialog sınıfı JS tarafından eklenecek --}}
  <div class="modal-dialog"> 
    <div class="modal-content">
      {{-- İçerik AJAX ile buraya gelecek --}}
    </div>
  </div>
</div>

<style>
  
</style>

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

  // Ana modal temizleme işlemi
  $("#addStockModal").on("hidden.bs.modal", function(e){
      var modal = $(this);
      
      if (e.target === this) {
        setTimeout(function(){
          if (!$('.modal.show').length) {
            console.log("Modal içeriği temizleniyor");
            modal.find(".modal-body").html("");
    
            // Tüm backdrop'ları zorla kaldır
            $('.modal-backdrop').remove();
            
            // Body'den tüm modal sınıflarını kaldır ve style'ları sıfırla
            $('body').removeClass('modal-open').removeAttr('style');
            $('html').removeClass('modal-open').removeAttr('style');
            
          } else {
            console.log("Başka modal açık, temizleme yapılmıyor");
          }
        }, 100);
      }
  });

  // Edit modal için özel temizlik
  $('#editStockModal').on('hidden.bs.modal', function(e) {
      // Sadece bu modal kapatılıyorsa (event target kontrolü)
      if (e.target === this) {
          setTimeout(function() {
              // Hiç modal açık değilse backdrop temizliği yap
              if (!$('.modal.show').length) {
                  $('.modal-backdrop').remove();
                  $('body').removeClass('modal-open').removeAttr('style');
                  $('html').removeClass('modal-open').removeAttr('style');
              }
          }, 100);
      }
  });

  // Tüm alt modal'lar için ortak temizlik
  $('#addBrandModal, #addDeviceTypeModal, #addCategoryModal, #addShelfModal, #addSupplierModal, #hareketEkleModal').on('hidden.bs.modal', function(e) {
      if (e.target === this) {
          setTimeout(function(){
              // Ana modal hala açıksa body'ye modal-open sınıfını geri ekle
              if ($('#addStockModal').hasClass('show') || $('#editStockModal').hasClass('show')) {
                  $('body').addClass('modal-open');
              } else if (!$('.modal.show').length) {
                  // Hiçbir modal açık değilse tam temizlik
                  $('.modal-backdrop').remove();
                  $('body').removeClass('modal-open').removeAttr('style');
                  $('html').removeClass('modal-open').removeAttr('style');
              }
          }, 50);
      }
  });
  $('#datatableStock').on('click', '.editStock', function(){
    var id = $(this).data('bs-id');
    var modal = $('#editStockModal'); 
   
    modal.find('.modal-dialog').removeClass('modal-xl').addClass('modal-xl'); // Boyutu ayarla (her seferinde)
    modal.find('.modal-content').html('<div class="modal-body text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Yükleniyor...</span></div></div>');
    
    // 2. Modal'ı göster
    modal.modal('show');
    
    // 3. Sunucudan veriyi çek
    $.ajax({
      url: "/" + firma_id + "/stok/duzenle/" + id,
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
      dom: '<"top"f>rt<"bottom"i<"float-end"lp>><"clear">',
      buttons: [
        {
          extend: 'print',
          text: '<i class="fas fa-print"></i> Yazdır',
          className: 'btn btn-warning btn-sm',
          title: '{{$firma->firma_adi }}',
          messageTop: '<div style="font-size:16px; font-weight:900; color:#000; text-align:left;">Tüm Stoklar</div>',
          exportOptions: {
            columns: [0,1,2,3,4,5,6,7]
          },
          customize: function (win) {
            $(win.document.body).find('h1').remove();
          }
        }
      ],
      language: {
        sDecimal: ",",
        sEmptyTable: "Tabloda herhangi bir veri mevcut değil",
        sInfo: "Listelenen Ürün Sayısı: _TOTAL_ ",
        sInfoEmpty: "Kayıt yok",
        sInfoFiltered: "",
        sInfoPostFix: "",
        sInfoThousands:  ".",
        sLengthMenu: "_MENU_ ",
        sLoadingRecords: "Yükleniyor...",
        sProcessing: "İşleniyor...",
        sSearch: "",
        sZeroRecords: "Eşleşen kayıt bulunamadı",
        oPaginate: {
          sFirst: "İlk",
          sLast: "Son",
          sNext: '<i class="fas fa-angle-right"></i>', 
          sPrevious: '<i class="fas fa-angle-left"></i>'
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
      
      lengthMenu: [ [25, 50, 100, -1], [25, 50, 100, "Tümü"] ],
       "initComplete": function(settings, json) {
          // --- DEĞİŞTİRİLEN BÖLÜM BURASI ---
          var searchContainer = $('#datatableStock_filter');
          var searchInput = searchContainer.find('input');
          var filterWrapper = $('.searchWrap');
          var flexContainer = $('<div class="d-flex justify-content-end w-100 mb-2"></div>');

          // Varsayılan "Search:" etiketini kaldır
          searchContainer.find('label').contents().filter(function() {
              return this.nodeType == 3;
          }).remove();

          // Arama kutusunu ve filtreyi sarmalamak için
          searchContainer.addClass('flex-grow-1 me-2');
          searchInput.addClass('w-100');
          searchInput.attr('placeholder', 'Stok Ara...');

          // Ögeleri flex container'a ekle
          flexContainer.append(searchContainer);
          flexContainer.append(filterWrapper);

          // Flex container'ı tablonun üstüne ekle
          $('#datatableStock_wrapper .top').append(flexContainer);

          // Hazır olduğunda görünür yap
          $('.searchWrap').css({ visibility: 'visible', opacity: 1 });
          // --- DEĞİŞTİRİLEN BÖLÜM SONU ---
      }
  });

  // Filtreler değiştiğinde tabloyu yeniden çiz
  $('#raf, #marka, #cihaz, #personel').change(function() {
      table.draw();
  });

  $('#printButton').on('click', function () {
    table.button(0).trigger();
  });
});

</script>


@endsection
