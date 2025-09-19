@extends('frontend.secure.user_master')
@section('user')

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

            <!-- Sayfalama ve Toplam Bilgileri Container -->
            <div class="row mt-3 align-items-center">
              <!-- Sol taraf: Sayfalama bilgileri -->
              <div class="col-md-8">
                <!-- DataTables sayfalama burada otomatik gelecek -->
              </div>
              
              <!-- Sağ taraf: Yeşil toplam kutusu -->
              <div class="col-md-4">
                <div class="card bg-success text-white">
                  <div class="card-body p-3">
                    <div class="row">
                      <div class="col-6">
                        <div class="mb-2">
                          <small class="text-white-50">Toplam Ürün:</small>
                          <div><strong id="toplamAdet">0</strong></div>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="mb-2">
                          <small class="text-white-50">Depo Kazanç:</small>
                          <div><strong id="toplamFiyat">0 ₺</strong></div>
                        </div>
                      </div>
                    </div>
                  </div>
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
        { data: 'toplamTutar', name: 'toplamTutar' },
        { data: 'adet', name: 'adet' },
        { data: 'raf_adi', name: 'raf_adi' },
        { data: 'marka_cihaz', name: 'marka_cihaz' },
        { data: 'action', orderable: false, searchable: false }
      ],
      order: [[0, 'desc']],
      // DOM ayarı - sadece tablo, sayfalama bilgileri ayrı konumda
      dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rt<"row mt-3"<"col-md-8"ilp><"col-md-4">>',
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
      lengthMenu: [ [25, 50, 100, -1], [25, 50, 100, "Tümü"] ]
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
