@extends('frontend.secure.user_master')
@section('user')

{{-- Daterangepicker için gerekli kütüphaneler --}}
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

@php 
if ($firma->isOnTrial()) {
    $konsinyeLimit = $firma->konsinyeSayisi ?? null;
  } else {
    $konsinyeLimit = $firma->plan()?->limits['konsinye'] ?? null;
  }
$stockAll = App\Models\Stock::where('firma_id', $firma->id)
  ->where('durum','1')
  ->where('urunKategori',  3)
  ->count();
@endphp

<style>
   .servisDrop {
      transition: none !important;
      animation: none !important;
      transform: translate3d(1px, 2px, 0px) !important;
    }

    .card-consigment {
      border: 1px solid rgba(0, 0, 0, .125) !important;
    }

    .card-consigment-header {
      background-color: #f7f7f7 !important;
      border-bottom: 1px solid rgba(0, 0, 0, .125) !important;
      margin-bottom: 7px !important;
      padding: 4px 7px !important;
    }

    .card-consigment-body {
      padding: 3px 7px !important;
    }
    /* Genel Stiller */
    .searchWrap {
      visibility: hidden; /* JS ile görünür yapılacak */
      opacity: 0;
    }
    @media (min-width: 768px) {
  .custom-modal-width {
    max-width: 424px;
    margin: 1.75rem auto;
  }
  .searchWrap .dropdown-menu{
    width: 250px !important;
  }
}

    /* Mobil Cihazlar İçin Özel Stiller */
    @media (max-width: 767px) {

          .pageDetail .searchWrap {
        width: 30% !important;
      }
      
      div.dataTables_filter input {
        margin-left: 0 !important;
      }
      
      .dataTables_filter {
        margin-right: 0 !important;
      }
      
      .pageDetail .searchWrap {
        margin-bottom: 0px !important;
      }

      .searchWrap {
        margin-top: 0px !important;
      }
      .pageDetail .searchWrap .dropdown-menu{
        transform: translate3d(9px, 9px, 0px) !important;
        min-width: calc(78vw - 20px) !important;
      }
      .searchWrap .dropdown-menu {
    padding: 0px;
}
    .pageDetail .searchWrap .dropdown-menu .item{
      margin-bottom: 0px !important;
    }

        #datatableConsignment_wrapper .dataTables_info{
          width: auto !important;
        }
            #datatableConsignment_wrapper .bottom {
        flex-direction: row !important;
    }
        li.paginate_button.next, li.paginate_button.previous {
        font-size: 15px;
    }

    .consigment-header-top{margin-top: 30px;}
.btn-secondary {
    color: #fff !important;
    background-color: #5c636a !important;
    border-color: #565e64 !important;
}

    }
</style>

<div class="page-content">
  <div class="container-fluid consigment-header-top">
    <div class="row pageDetail">
      <div class="col-12">
        <div class="card card-consigment">
          <div class="card-header card-consigment-header sayfaBaslik">
            Konsinye Cihazlar
          </div>
          <div class="card-body card-consigment-body">
          <div class="consignment-buttons-container">
  @if(is_null($konsinyeLimit) || $konsinyeLimit == -1 || $stockAll < $konsinyeLimit)
  <a data-bs-toggle="modal" data-bs-target="#addConsignmentModal" class="btn btn-success btn-sm addConsignment">
    <i class="fas fa-plus"></i> <span>Cihaz Ekle</span>
  </a>
  @else
    <a class="btn btn-success btn-sm addStock" disabled style="pointer-events: none; opacity: .4; cursor: default;">
        <i class="fas fa-plus"></i> <span >Cihaz Ekle</span>
    </a>
  @endif

  <a href="javascript:void(0);" class="btn btn-warning btn-sm printConsignment">
    <i class="fas fa-print"></i><span> Yazdır</span>
  </a>
</div>
@if(!is_null($konsinyeLimit) && $konsinyeLimit != -1 && $stockAll >= $konsinyeLimit)
  <span class="text-muted consignment-limit-warning">
      <i class="fas fa-info-circle me-1"></i>Aboneliğinizin stok limiti doldu (maks: {{ $konsinyeLimit }})
  </span>
@endif

            <!-- Filtre dropdown butonu -->
            <div class="searchWrap float-end">
              <div class="btn-group">
                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Filtrele <i class="mdi mdi-chevron-down"></i>
                </button>
                <div class="dropdown-menu servisDrop" style="min-width: 250px;">
                  
                  <!-- Raf -->
                  <div class="item mb-1">
                    <div class="row align-items-center">
                      <label class="col-sm-5 col-5 custom-p-r-m mb-0 custom-p-m-k">Raf</label>
                      <div class="col-sm-7 col-7 custom-p-m custom-p-r-m-k">
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
                  <div class="item mb-1">
                    <div class="row align-items-center">
                      <label class="col-sm-5 col-5 custom-p-r-m mb-0 custom-p-m-k ">Marka</label>
                      <div class="col-sm-7 col-7 custom-p-m custom-p-r-m-k">
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
                  <div class="item mb-1">
                    <div class="row align-items-center">
                      <label class="col-sm-5 col-5 custom-p-r-m mb-0 custom-p-m-k">Cihaz</label>
                      <div class="col-sm-7 col-7 custom-p-m custom-p-r-m-k">
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
                  <div class="item mb-2">
                    <div class="row align-items-center">
                      <label class="col-sm-5 col-5 custom-p-r-m mb-0 custom-p-m-k">Personel</label>
                      <div class="col-sm-7 col-7 custom-p-m custom-p-r-m-k">
                        <select id="personel" class="form-select form-select-sm">
                          <option value="">Hepsi</option>
                          @foreach($personeller as $personel)
                            <option value="{{ $personel->id }}">{{ $personel->name }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>

                  {{-- TARİH ARALIĞI FİLTRESİ - DROPDOWN İÇİNDE --}}
                  <div class="item">
                    <div class="row ">
                      <label class="col-sm-5 col-5 custom-p-r-m mb-0 custom-p-m-k">Tarih Aralığı:</label>
                      <div class="col-sm-7 col-7 custom-p-m custom-p-r-m-k">
                        <input id="daterangeConsignment" class="form-control form-control-sm mb-2">
                        <div class="tarihAraligi d-flex flex-wrap gap-1">
                          <button id="lastYearConsignment" class="btn btn-sm btn-secondary">Son 1 Yıl</button>
                          <button id="lastMonthConsignment" class="btn btn-sm btn-secondary">Son 1 Ay</button>
                          <button id="lastWeekConsignment" class="btn btn-sm btn-secondary">Son 7 Gün</button>
                          <button id="yesterdayConsignment" class="btn btn-sm btn-secondary">Dün</button>
                          <button id="todayConsignment" class="btn btn-sm btn-secondary">Bugün</button>
                        </div>
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
<div id="addConsignmentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addConsignmentModalLabel" aria-hidden="true">
  <div class="modal-dialog custom-modal-width">
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
  $('#editConsignmentModal').on('hidden.bs.modal', function(e) {
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
              if ($('#addConsignmentModal').hasClass('show') || $('#editConsignmentModal').hasClass('show')) {
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

   // Edit Consignment Modal - Buton click event'i
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

  // Mobilde ve masaüstünde satırın boş alanlarına tıklayınca da açılsın
  $('#datatableConsignment tbody').on('click', 'tr', function(e) {
    var $target = $(e.target);
    
    // Düzenle butonuna tıklandıysa, bu tr event'ini çalıştırma (butonun kendi event'i çalışsın)
    if ($target.closest('.editConsignment').length > 0 ||
        $target.closest('.btn').length > 0 || 
        $target.closest('td').index() === 8) {
      return;
    }
    
    var id = $(this).find('.editConsignment').first().data('bs-id');
    
    if (id) {
      var modal = $('#editConsignmentModal');
      
      // 1. Modal boyutunu ayarla ve loading göster
      modal.find('.modal-dialog').removeClass('modal-xl').addClass('modal-xl');
      modal.find('.modal-content').html('<div class="modal-body text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Yükleniyor...</span></div></div>');
      
      // 2. Modal'ı hemen göster
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
            modal.find('.modal-content').html(data.html);
          }
        },
        error: function() {
          // Hata durumunda içeriği temizle ve hata mesajı göster
          modal.find('.modal-content').html('<div class="modal-header"><h5 class="modal-title">Hata</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="alert alert-danger">İçerik yüklenirken bir hata oluştu.</div></div>');
        }
      });
    }
  });
});
</script>

<script>
// DataTable
$(document).ready(function () {
  // Dashboard'dan gelen URL parametrelerini oku
  const urlParams = new URLSearchParams(window.location.search);
  const dashboardStartDate = urlParams.get('dashboard_istatistik_tarih1');
  const dashboardEndDate = urlParams.get('dashboard_istatistik_tarih2');

  // Daterangepicker başlatma (varsayılan son 3 gün)
  let initialConsignmentStartDate = dashboardStartDate ? moment(dashboardStartDate) : moment().subtract(2, 'days').startOf('day');
  let initialConsignmentEndDate = dashboardEndDate ? moment(dashboardEndDate) : moment().endOf('day');

  $('#daterangeConsignment').daterangepicker({
    startDate: initialConsignmentStartDate,
    endDate: initialConsignmentEndDate,
    locale: {
      format: 'DD-MM-YYYY',
      separator: ' - ',
      applyLabel: 'Uygula',
      cancelLabel: 'İptal',
      weekLabel: 'H',
      daysOfWeek: ['Pz', 'Pzt', 'Sal', 'Çrş', 'Prş', 'Cm', 'Cmt'],
      monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
      firstDay: 1
    }
  },
  function (start_date, end_date) {
    $('#daterangeConsignment').val(start_date.format('DD-MM-YYYY') + ' - ' + end_date.format('DD-MM-YYYY'));
    table.draw();
  });

  // Hızlı tarih filtreleme butonları
  $('#lastYearConsignment').on('click', function () {
    $('#daterangeConsignment').data('daterangepicker').setStartDate(moment().subtract(1, 'year'));
    $('#daterangeConsignment').data('daterangepicker').setEndDate(moment());
    table.draw();
  });

  $('#lastMonthConsignment').on('click', function () {
    $('#daterangeConsignment').data('daterangepicker').setStartDate(moment().subtract(1, 'month'));
    $('#daterangeConsignment').data('daterangepicker').setEndDate(moment());
    table.draw();
  });

  $('#lastWeekConsignment').on('click', function () {
    $('#daterangeConsignment').data('daterangepicker').setStartDate(moment().subtract(7, 'days'));
    $('#daterangeConsignment').data('daterangepicker').setEndDate(moment());
    table.draw();
  });

  $('#yesterdayConsignment').on('click', function () {
    $('#daterangeConsignment').data('daterangepicker').setStartDate(moment().subtract(1, 'days'));
    $('#daterangeConsignment').data('daterangepicker').setEndDate(moment().subtract(1, 'days'));
    table.draw();
  });

  $('#todayConsignment').on('click', function () {
    $('#daterangeConsignment').data('daterangepicker').setStartDate(moment());
    $('#daterangeConsignment').data('daterangepicker').setEndDate(moment());
    table.draw();
  });

  var table = $('#datatableConsignment').DataTable({
      processing:true,
      serverSide: true,
      ajax: {
        url: "{{ route('consignmentdevice.data', $firma->id) }}",
        data: function (d) {
          d.raf = $('#raf').val();
          d.marka = $('#marka').val();
          d.cihaz = $('#cihaz').val();
          d.personel = $('#personel').val();
          
          // Konsinye tarih aralığını ekle
          d.from_date_consignment = $('#daterangeConsignment').data('daterangepicker').startDate.format('YYYY-MM-DD');
          d.to_date_consignment = $('#daterangeConsignment').data('daterangepicker').endDate.format('YYYY-MM-DD');
          
          // Dashboard tarih parametreleri varsa ekle
          if (dashboardStartDate && dashboardEndDate) {
              d.dashboard_istatistik_tarih1 = dashboardStartDate;
              d.dashboard_istatistik_tarih2 = dashboardEndDate;
          }
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
          messageTop: '<div style="font-size:16px; font-weight:900; color:#000; text-align:left;">Konsinye Cihazlar</div>',
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
      initComplete: function(settings, json) {
          var searchContainer = $('#datatableConsignment_filter');
          var searchInput = searchContainer.find('input');
          var filterWrapper = $('.searchWrap');
          var flexContainer = $('<div class="d-flex justify-content-end w-100"></div>');

          searchContainer.find('label').contents().filter(function() {
              return this.nodeType == 3;
          }).remove();

          searchContainer.addClass('flex-grow-1 me-2');
          searchInput.addClass('w-100');
          searchInput.attr('placeholder', 'Konsinye Cihaz Ara...');

          flexContainer.append(searchContainer);
          flexContainer.append(filterWrapper);

          $('#datatableConsignment_wrapper .top').append(flexContainer);

          $('.searchWrap').css({ visibility: 'visible', opacity: 1 });
           $('.tableToplamaAlani').insertBefore('#datatableConsignment_wrapper .bottom');
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