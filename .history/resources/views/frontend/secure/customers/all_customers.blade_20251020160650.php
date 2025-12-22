@extends('frontend.secure.user_master')
@section('user')
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>

@media (min-width: 768px) {
  .custom-modal-width {
    max-width: 330px;
    margin: 1.75rem auto;
  }
  .custom-modal-width-edit{
    max-width: 365px;
    margin: 1.75rem auto;
  }
  .searchWrap .dropdown-menu{
    width: 251px !important;
  }
  .pageDetail .searchWrap .dropdown-menu{transform: translate3d(2px, 1px, 0px) !important;min-width: 100% !important;}
  
}
#datatableCustomer_filter input[type="search"] {
    padding-right: 12px !important; 
}

.searchWrap {
    visibility: hidden;
    opacity: 0;
}
.dataTables_filter{
      margin-bottom: -11px !important;
}
.card-customer{    border: 1px solid rgba(0, 0, 0, .125);}
.card-header-custom{background-color: #f7f7f7;border-bottom: 1px solid rgba(0, 0, 0, .125);margin-bottom: 7px;padding: 4px 7px  !important;}
  .card-customer-body{padding: 3px 7px !important;} 
@media (max-width: 767px) {
      .pageDetail .card-header{padding: 4px 7px !important;}
.btn-secondary {
    color: #fff !important;
    background-color: #5c636a !important;
    border-color: #565e64 !important;
}
.custom-p{
        padding-left: 0px !important;
      }
 div.dataTables_filter input{margin-left: 0 !important;}

 .dataTables_filter{
margin-right: 0px !important;
    }
    .searchWrap{margin-top: 0px !important;}
    .pageDetail .searchWrap{width: 30% !important;}
    .pageDetail .searchWrap{margin-bottom: 0px !important;}
      #datatableCustomer_filter label{width: 100% !important;}
          .pageDetail .searchWrap .dropdown-menu{transform: translate3d(13px, 2px, 0px) !important;min-width: calc(80vw - 20px) !important;}
.pageDetail .searchWrap .dropdown-menu .item {
    margin-bottom: 0px !important;
  }
  #datatableCustomer_wrapper .bottom {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    padding-top: 0.85em !important;
}
.searchWrap .dropdown-menu {
    padding: 5px 1px;
}
.custom-p-r-m-c{
  padding-right: 0px !important;
}
.customer-header-top{margin-top: 30px;}

}
</style>
<div class="page-content">
  <div class="container-fluid customer-header-top">
    <div class="row pageDetail">
      <div class="col-12">
        <div class="card card-customer">
          <div class="card-header card-header-custom sayfaBaslik">
            Müşteriler
          </div>
          <div class="card-body">
            <table id="datatableCustomer" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

<div class="d-flex justify-content-between">
    <!-- Müşteri Ekle Butonu -->
    <a data-bs-toggle="modal" data-bs-target="#addCustomerModal" class="btn btn-success btn-sm addCustomer">
        <i class="fas fa-plus"></i>
        <span class="d-none d-sm-inline">Müşteri Ekle</span>
    </a> 

    <!-- Filtreleme ve Arama Alanı (JavaScript ile taşınacak) -->
    <div class="searchWrap float-end">

        <div class="btn-group" id="müsteri_filtre">
            <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Filtrele <i class="mdi mdi-chevron-down"></i>
            </button>
            <div class="dropdown-menu">
                <div class="item">
                    <div class="row">
                        <label class="col-sm-5 custom-p col-5 filtre-i-p">Durum</label>
                        <div class="filtre-i-p custom-p custom-p-m col-7 col-sm-7">
                            <select name="musteriTipi" id="musteriTipi" class="form-select">
                                <option value="">Hepsi</option>
                                <option value="1" >Bireysel</option>
                                <option value="2">Kurumsal</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="row">
                        <label class="col-sm-5 custom-p col-5 filtre-i-p">İl</label>
                        <div class="col-sm-7 custom-p custom-p-m col-7 filtre-i-p">
                            <select name="il" id="countrySelect" class="form-control form-select" style="width:100%!important;">
                                <option value="" selected disabled>-Seçiniz-</option>
                                @foreach($countries as $item)
                                    <option value="{{ $item->id }}">{{ $item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="row">
                        <label class="col-sm-5 custom-p col-5 filtre-i-p">İlçe</label>
                        <div class="col-sm-7 custom-p custom-p-m col-7 filtre-i-p">
                            <select name="ilce" id="citySelect" class="form-control form-select" style="width:100%!important;">
                                <option value="" selected disabled>-Seçiniz-</option>                              
                            </select>
                        </div>
                    </div>
                </div>
                <!-- YENİ TARİH FİLTRESİ BAŞLANGICI -->
                <div class="item">
                    <div class="row">
                        <label class="col-sm-5 col-5 custom-p">Tarih Aralığı</label>
                        <div class="col-sm-7 custom-p col-7 custom-p-m custom-p-r-m-c">
                            <input id="daterangeCustomer" class="tarih-araligi form-control">
                            <div class="tarihAraligi mt-2 mb-2">
                                <button id="lastYearCustomer" class="btn btn-sm btn-secondary">Son 1 Yıl</button>
                                <button id="lastMonthCustomer" class="btn btn-sm btn-secondary">Son 1 Ay</button>
                                <button id="lastWeekCustomer" class="btn btn-sm btn-secondary">Son 7 Gün</button>
                                <button id="yesterdayCustomer" class="btn btn-sm btn-secondary">Dün</button>
                                <button id="todayCustomer" class="btn btn-sm btn-secondary">Bugün</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- YENİ TARİH FİLTRESİ SONU -->
            </div>
        </div><!-- /btn-group -->
    </div>
</div>

              
              <thead class="title">
                <tr>
                  <th style="width: 10px">ID</th>
                  <th data-priority="2">Ad Soyad</th>
                  <th>Telefon</th>
                  <th>Adres</th>
                  <th data-priority="1" style="width: 96px;">Düzenle</th>
                </tr>
              </thead>
              <tbody>
               
              </tbody>
            </table>
          </div>
        </div>
      </div> <!-- end col -->
    </div> <!-- end row -->
  </div>
</div>

<!-- add modal content -->
<div id="addCustomerModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog custom-modal-width">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="myModalLabel">Müşteri Ekle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- edit modal content -->
<div id="editCustomerModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog custom-modal-width-edit">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="myModalLabel">Müşteri Düzenle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="padding: 5px;">
        Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
$(document).ready(function(){
  // Add Customer Modal
  var firma_id = {{$firma->id}};
  $(".addCustomer").click(function(){
    $.ajax({
      url: "/"+ firma_id + "/musteri-ekle/"
    }).done(function(data) {
      if ($.trim(data) === "-1") {
        window.location.reload(true);
      } else {
        $('#addCustomerModal').modal('show');
        $('#addCustomerModal .modal-body').html(data);
      }
    });
  });
  $("#addCustomerModal").on("hidden.bs.modal", function() {
      $('#addCustomerModal .modal-body').html("");
  });

 // Edit Customer Modal - Buton click event'i
  $('#datatableCustomer').on('click', '.editCustomer', function(e){
      var id = $(this).attr("data-bs-id");
      var firma_id = {{$firma->id}};
      $.ajax({
          url: "/"+ firma_id + "/musteri/duzenle/" + id
      }).done(function(data) {
          if ($.trim(data) === "-1") {
              window.location.reload(true);
          } else {
              $('#editCustomerModal').modal('show');
              $('#editCustomerModal .modal-body').html(data);
          }
      });
  });

  // Mobilde ve masaüstünde satırın boş alanlarına tıklayınca da açılsın
  $('#datatableCustomer tbody').on('click', 'tr', function(e) {
      var $target = $(e.target);
      
      // Düzenle butonuna tıklandıysa, bu tr event'ini çalıştırma (butonun kendi event'i çalışsın)
      if ($target.closest('.editCustomer').length > 0 ||
          $target.closest('.btn').length > 0 || 
          $target.closest('td').index() === 4) {
          return;
      }
      
      var id = $(this).find('.editCustomer').first().attr('data-bs-id');
      
      if (id) {
          // 1. MODAL'I HEMEN AÇ (AJAX beklemeden)
          $('#editCustomerModal').modal('show');
          
          // 2. AYNI ANDA AJAX BAŞLAT
          $.ajax({
              url: "/" + firma_id + "/musteri/duzenle/" + id
          }).done(function(data) {
              if ($.trim(data) === "-1") {
                  window.location.reload(true);
              } else {
                  $('#editCustomerModal .modal-body').html(data);
              }
          });
      }
  });

  $("#editCustomerModal").on("hidden.bs.modal", function() {
    $('#editCustomerModal .modal-body').html("");
  });


  // Ülke seçildiğinde şehirleri getir
  $("#countrySelect").change(function() {
    var selectedCountryId = $(this).val();
    if (selectedCountryId) {
      loadCities(selectedCountryId);
    }
  });

  // Şehirleri yüklemek için kullanılan fonksiyon
  function loadCities(countryId) {
    var citySelect = $("#citySelect");
    citySelect.empty(); // Önceki seçenekleri temizle
    citySelect.append(new Option("Yükleniyor...", "")); // Kullanıcıya yükleniyor bilgisi ver

    // AJAX isteğiyle şehirleri al
    $.get("/get-states/" + countryId, function(data) {
      citySelect.empty(); // Yükleniyor mesajını temizle
      citySelect.append(new Option("-Seçiniz-", "")); // İlk boş seçeneği ekle
      $.each(data, function(index, city) {
        citySelect.append(new Option(city.ilceName, city.id));
      });
    }).fail(function() {
      citySelect.empty(); // Hata durumunda temizle
      citySelect.append(new Option("Unable to load cities", ""));
    });
  }

  // Dashboard'dan gelen URL parametrelerini oku
  const urlParams = new URLSearchParams(window.location.search);
  const dashboardStartDate = urlParams.get('dashboard_istatistik_tarih1');
  const dashboardEndDate = urlParams.get('dashboard_istatistik_tarih2');

  // Müşteri filtreleme dropdown'unun daterangepicker ile etkileşimde kapanmasını engelle
  let preventCustomerDropdownHide = false;
  $('#customerFilterDropdownContainerDesktop, #customerFilterDropdownContainerMobile').on('hide.bs.dropdown', function (e) {
    if (preventCustomerDropdownHide) {
      e.preventDefault();
    }
    preventCustomerDropdownHide = false;
  });
  $(document).on('mousedown', function (e) {
    if ($(e.target).closest('.daterangepicker').length) {
      preventCustomerDropdownHide = true;
    }
  });
  $('#customerFilterDropdownContainerDesktop').find('#daterangeCustomer').on('focus mousedown', function () {
    preventCustomerDropdownHide = true;
  });
  $('#customerFilterDropdownContainerMobile').find('#daterangeCustomer').on('focus mousedown', function () {
    preventCustomerDropdownHide = true;
  });
  $('#customerFilterDropdownContainerDesktop').find('.tarihAraligi button').on('mousedown', function () {
    preventCustomerDropdownHide = true;
  });
  $('#customerFilterDropdownContainerMobile').find('.tarihAraligi button').on('mousedown', function () {
    preventCustomerDropdownHide = true;
  });
  $('#daterangeCustomer').on('apply.daterangepicker cancel.daterangepicker hide.daterangepicker', function () {
    preventCustomerDropdownHide = false;
  });

  // Müşteriler için daterangepicker başlatma (varsayılan son 3 gün)
  // Dashboard'dan gelen tarihler varsa onları, yoksa son 3 günü kullan
  let initialCustomerStartDate = dashboardStartDate ? moment(dashboardStartDate) : moment().subtract(2, 'days').startOf('day');
  let initialCustomerEndDate = dashboardEndDate ? moment(dashboardEndDate) : moment().endOf('day');

  $('#daterangeCustomer').daterangepicker({
    startDate: initialCustomerStartDate,
    endDate: initialCustomerEndDate,
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
    // daterangepicker değiştiğinde DataTable'ı yeniden çiz
    $('#daterangeCustomer').val(start_date.format('DD-MM-YYYY') + ' - ' + end_date.format('DD-MM-YYYY'));
    table.draw();
  });

  // Hızlı tarih filtreleme butonları
  $('#lastYearCustomer').on('click', function () {
    $('#daterangeCustomer').data('daterangepicker').setStartDate(moment().subtract(1, 'year'));
    $('#daterangeCustomer').data('daterangepicker').setEndDate(moment());
    table.draw();
  });

  $('#lastMonthCustomer').on('click', function () {
    $('#daterangeCustomer').data('daterangepicker').setStartDate(moment().subtract(1, 'month'));
    $('#daterangeCustomer').data('daterangepicker').setEndDate(moment());
    table.draw();
  });

  $('#lastWeekCustomer').on('click', function () {
    $('#daterangeCustomer').data('daterangepicker').setStartDate(moment().subtract(7, 'days'));
    $('#daterangeCustomer').data('daterangepicker').setEndDate(moment());
    table.draw();
  });

  $('#yesterdayCustomer').on('click', function () {
    $('#daterangeCustomer').data('daterangepicker').setStartDate(moment().subtract(1, 'days'));
    $('#daterangeCustomer').data('daterangepicker').setEndDate(moment().subtract(1, 'days'));
    table.draw();
  });

  $('#todayCustomer').on('click', function () {
    $('#daterangeCustomer').data('daterangepicker').setStartDate(moment());
    $('#daterangeCustomer').data('daterangepicker').setEndDate(moment());
    table.draw();
  });


  var table = $('#datatableCustomer').DataTable({
      processing: true,
      serverSide: true,
      language: {
        paginate: {
          previous: "<i class='mdi mdi-chevron-left'>",
          next: "<i class='mdi mdi-chevron-right'>"
        }
      },
      ajax: {
        url: "{{ route('customers',$firma->id) }}",
        data: function(data) {
          data.search = $('input[type="search"]').val();
          data.tip = $('#musteriTipi').val();
          data.il = $('#countrySelect').val();
          data.ilce = $('#citySelect').val();

          // Müşteri tarih aralığını ekle
          data.from_date_customer = $('#daterangeCustomer').data('daterangepicker').startDate.format('YYYY-MM-DD');
          data.to_date_customer = $('#daterangeCustomer').data('daterangepicker').endDate.format('YYYY-MM-DD');

          // Eğer URL'den dashboard tarih parametreleri geldiyse, onları da ajax isteğine ekle
          // Ancak müşteri tarih aralığı filtreleri kullanılıyorsa, dashboard tarihleri override edilmeli.
          // Controller tarafında öncelik verilecek.
          if (dashboardStartDate && dashboardEndDate) {
              data.dashboard_istatistik_tarih1 = dashboardStartDate;
              data.dashboard_istatistik_tarih2 = dashboardEndDate;
          }
        }
      },
      'columns': [
        { data: 'id'},
        { data: 'name' },
        { data: 'tel' },
        { data: 'address' },
        { data: 'action'}           
      ],
      drawCallback: function() {
        $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
      },
        "order": [[0, 'desc']],
        "columnDefs": [{
          "targets": 0,
          "className": "gizli"
        }],
       
        "oLanguage": {
            "sDecimal":        ",",
          "sEmptyTable":     "Tabloda herhangi bir veri mevcut değil",
          "sInfo":           "Müşteri Sayısı: _TOTAL_",
          "sInfoEmpty":      "Kayıt yok",
          "sInfoFiltered":   "",
          "sInfoPostFix":    "",
          "sInfoThousands":  ".",
          "sLengthMenu":     "_MENU_",
          "sLoadingRecords": "Yükleniyor...",
          "sProcessing":     "İşleniyor...",
          "sSearch":         "",
          "sZeroRecords":    "Eşleşen kayıt bulunamadı",
          "oPaginate": {
              "sFirst":    "İlk",
              "sLast":     "Son",
              "sNext":     '<i class="fas fa-angle-double-right"></i>',
              "sPrevious": '<i class="fas fa-angle-double-left"></i>'
          },
          "oAria": {
              "sSortAscending":  ": artan sütun sıralamasını aktifleştir",
              "sSortDescending": ": azalan sütun sıralamasını aktifleştir"
          },
          "select": {
              "rows": {
                  "_": "%d kayıt seçildi",
                  "0": "",
                  "1": "1 kayıt seçildi"
              }
          }
          },
      dom: '<"top"f>rt<"bottom"i<"float-end"lp>><"clear">',
      "lengthMenu": [ [25, 50, 100, -1], [25, 50, 100, "Tümü"] ],
     "initComplete": function(settings, json) {
          var topContainer = $('#datatableCustomer_wrapper .top');
          var searchContainer = $('#datatableCustomer_filter');
          var searchInput = searchContainer.find('input');
          
          // Filtre butonunu HTML'den al (artık sadece bir tane var)
          var filterWrapper = $('.searchWrap');

          // Arama kutusu ve filtreyi sarmalayacak yeni bir flex container oluştur
          var flexContainer = $('<div class="d-flex justify-content-end w-100"></div>');

          // DataTables'in varsayılan "Search:" etiketini kaldır ve input'u ayarla
          searchContainer.find('label').contents().filter(function() {
              return this.nodeType == 3;
          }).remove();
          
          searchInput.attr('placeholder', 'Müşteri Ara...');
          searchContainer.addClass('flex-grow-1 me-1'); // Arama kutusunun esnemesini ve sağ boşluk bırakmasını sağla
          searchInput.addClass('w-100');

          // Ögeleri yeni flex container'a ekle
          flexContainer.append(searchContainer);
          flexContainer.append(filterWrapper);
          
          // Her şeyi tablonun üstündeki "top" alanına ekle
          topContainer.html(flexContainer);

          // Görünür yap
          $('.searchWrap').css({ visibility: 'visible', opacity: 1 });
      } 
  });
  $('#musteriTipi').change(function(){
    table.draw();        
  });

  $('#countrySelect').change(function(){
    table.draw();        
  });

  $('#citySelect').change(function(){
    table.draw();        
  });

});
</script>

<script>
    $(document).ready(function() {
      // Ülke seçildiğinde şehirleri getir
      $("#countrySelect").change(function() {
        var selectedCountryId = $(this).val();
        if (selectedCountryId) {
          loadCities(selectedCountryId);
        }
      });
    
      // Şehirleri yüklemek için kullanılan fonksiyon
      function loadCities(countryId) {
        var citySelect = $("#citySelect");
        citySelect.empty(); // Önceki seçenekleri temizle
        citySelect.append(new Option("Yükleniyor...", "")); // Kullanıcıya yükleniyor bilgisi ver
    
        // AJAX isteğiyle şehirleri al
        $.get("/get-states/" + countryId, function(data) {
          citySelect.empty(); // Yükleniyor mesajını temizle
          citySelect.append(new Option("-Seçiniz-", "")); // İlk boş seçeneği ekle
          $.each(data, function(index, city) {
            citySelect.append(new Option(city.ilceName, city.id));
          });
        }).fail(function() {
          citySelect.empty(); // Hata durumunda temizle
          citySelect.append(new Option("Unable to load cities", ""));
        });
      }
    });
    </script>

    <script>
    $(document).ready(function () {
      var dropdownContainer = $('#müsteri_filtre');
      var filterButton = dropdownContainer.find('.filtrele');
      dropdownContainer.on('show.bs.dropdown', function () {
        filterButton.html('Kapat <i class="mdi mdi-chevron-down"></i>');
      });
      dropdownContainer.on('hide.bs.dropdown', function () {
        filterButton.html('Filtrele <i class="mdi mdi-chevron-down"></i>');
      });
    });
  </script>

@endsection