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
  .searchWrap .dropdown-menu{
    width: 251px !important;
    padding: 15px !important;
  }
}

#datatableCustomer_filter input[type="search"] {
    padding-right: 12px !important;
}

.searchWrap {
    visibility: hidden;
    opacity: 0;
}

.searchWrap .dropdown-menu {
    padding: 15px;
    min-width: 250px;
}

.searchWrap .dropdown-menu .item {
    margin-bottom: 15px;
}

.searchWrap .dropdown-menu .item:last-child {
    margin-bottom: 0;
}

.searchWrap .dropdown-menu .tarihAraligi {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.searchWrap .dropdown-menu .tarihAraligi button {
    flex: 1 1 auto;
    font-size: 11px;
    padding: 4px 8px;
    white-space: nowrap;
}

.searchWrap .dropdown-menu .tarih-araligi {
    width: 100%;
    font-size: 13px;
}

.dataTables_filter{
      margin-bottom: -11px !important;
}

@media (max-width: 767px) {
  .custom-p{
    padding-left: 0px !important;
  }
  div.dataTables_filter input{
    margin-left: 0 !important;
  }
  .dataTables_filter{
    margin-right: 0px !important;
  }
  .searchWrap{
    margin-top: 0px !important;
  }
  .pageDetail .searchWrap{
    width: 30% !important;
  }
  .pageDetail .searchWrap{
    margin-bottom: 0px !important;
  }
  #datatableCustomer_filter label{
    width: 100% !important;
  }
  .pageDetail .searchWrap .dropdown-menu{
    transform: translate3d(11px, 3px, 0px) !important;
    min-width: 100% !important;
    padding: 10px !important;
  }
  .pageDetail .searchWrap .dropdown-menu .item {
    margin-bottom: 10px !important;
  }
  .pageDetail .searchWrap .dropdown-menu .item:last-child {
    margin-bottom: 0 !important;
  }
  .pageDetail .searchWrap .dropdown-menu .tarihAraligi {
    gap: 3px !important;
  }
  .pageDetail .searchWrap .dropdown-menu .tarihAraligi button {
    font-size: 10px !important;
    padding: 3px 6px !important;
  }
  #datatableCustomer_wrapper .bottom {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    padding-top: 0.85em !important;
  }
}
</style>
<div class="page-content">
  <div class="container-fluid">
    <div class="row pageDetail">
      <div class="col-12">
        <div class="card">
          <div class="card-header sayfaBaslik">
            Müşteriler
          </div>
          <div class="card-body">
            <table id="datatableCustomer" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

<div class="d-none d-lg-block">
    <a data-bs-toggle="modal" data-bs-target="#addCustomerModal" class="btn btn-success btn-sm addCustomer"><i class="fas fa-plus"></i><span>Müşteri Ekle</span></a> 
    <div class="searchWrap float-end">
        <div class="btn-group mb-2 " id="customerFilterDropdownContainerDesktop">
            <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Filtrele <i class="mdi mdi-chevron-down"></i>
            </button>
            <div class="dropdown-menu">
                <div class="item">
                    <div class="row">
                        <label class="col-sm-5">Durum</label>
                        <div class="col-sm-7">
                            <select name="musteriTipi" id="musteriTipiDesktop" class="form-select musteriTipi">
                                <option value="">Hepsi</option>
                                <option value="1" >Bireysel</option>
                                <option value="2">Kurumsal</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="row">
                        <label class="col-sm-5">İl</label>
                        <div class="col-sm-7">
                            <select name="il" id="countrySelectDesktop" class="form-control form-select countrySelect" style="width:100%!important;">
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
                        <label class="col-sm-5">İlçe</label>
                        <div class="col-sm-7">
                            <select name="ilce" id="citySelectDesktop" class="form-control form-select citySelect" style="width:100%!important;">
                                <option value="" selected disabled>-Seçiniz-</option>                              
                            </select>
                        </div>
                    </div>
                </div>
                <!-- YENİ TARİH FİLTRESİ BAŞLANGICI -->
                <div class="item">
                    <div class="row">
                        <label class="col-sm-5">Tarih Aralığı:</label>
                        <div class="col-sm-7">
                            <input id="daterangeCustomerDesktop" class="tarih-araligi daterangeCustomer form-control">
                            <div class="tarihAraligi mt-2 mb-2">
                                <button class="btn btn-sm btn-secondary lastYearCustomer">Son 1 Yıl</button>
                                <button class="btn btn-sm btn-secondary lastMonthCustomer">Son 1 Ay</button>
                                <button class="btn btn-sm btn-secondary lastWeekCustomer">Son 7 Gün</button>
                                <button class="btn btn-sm btn-secondary yesterdayCustomer">Dün</button>
                                <button class="btn btn-sm btn-secondary todayCustomer">Bugün</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- YENİ TARİH FİLTRESİ SONU -->
            </div>
        </div><!-- /btn-group -->
    </div>
</div>

<div class="d-lg-none">
    <div class="d-flex gap-1 justify-content-between align-items-center mb-2">
        <a data-bs-toggle="modal" data-bs-target="#addCustomerModal" class="btn btn-success btn-sm addCustomer">
            <i class="fas fa-plus"></i><span>Müşteri Ekle</span>
        </a> 
        <div style="margin-top: 0px !important;width: 44% !important;" class="searchWrap">
            <div class="btn-group" id="customerFilterDropdownContainerMobile">
                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Filtrele <i class="mdi mdi-chevron-down"></i>
                </button>
                <div  class="dropdown-menu">
                    <div class="item">
                        <div class="row">
                            <label class="col-sm-5">Durum</label>
                            <div class="col-sm-7">
                                <select name="musteriTipi" id="musteriTipiMobile" class="form-select musteriTipi">
                                    <option value="">Hepsi</option>
                                    <option value="1" >Bireysel</option>
                                    <option value="2">Kurumsal</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="row">
                            <label class="col-sm-5">İl</label>
                            <div class="col-sm-7">
                                <select name="il" id="countrySelectMobile" class="form-control form-select countrySelect" style="width:100%!important;">
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
                            <label class="col-sm-5">İlçe</label>
                            <div class="col-sm-7">
                                <select name="ilce" id="citySelectMobile" class="form-control form-select citySelect" style="width:100%!important;">
                                    <option value="" selected disabled>-Seçiniz-</option>                              
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- YENİ TARİH FİLTRESİ BAŞLANGICI -->
                    <div class="item">
                        <div class="row">
                            <label class="col-sm-5">Tarih Aralığı:</label>
                            <div class="col-sm-7">
                                <input id="daterangeCustomerMobile" class="tarih-araligi daterangeCustomer form-control">
                                <div class="tarihAraligi mt-2 mb-2">
                                    <button class="btn btn-sm btn-secondary lastYearCustomer">Son 1 Yıl</button>
                                    <button class="btn btn-sm btn-secondary lastMonthCustomer">Son 1 Ay</button>
                                    <button class="btn btn-sm btn-secondary lastWeekCustomer">Son 7 Gün</button>
                                    <button class="btn btn-sm btn-secondary yesterdayCustomer">Dün</button>
                                    <button class="btn btn-sm btn-secondary todayCustomer">Bugün</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- YENİ TARİH FİLTRESİ SONU -->
                </div>
            </div><!-- /btn-group -->
        </div>
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
  <div class="modal-dialog ">
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
  <div class="modal-dialog">
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

  // Edit Customer Modal
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
  $("#editCustomerModal").on("hidden.bs.modal", function() {
    $('#editCustomerModal .modal-body').html("");
  });

  // Ülke seçildiğinde şehirleri getir
  $(".countrySelect").change(function() {
    var selectedCountryId = $(this).val();
    var containerType = $(this).attr('id').includes('Desktop') ? 'Desktop' : 'Mobile';
    if (selectedCountryId) {
      loadCities(selectedCountryId, containerType);
    }
  });

  // Şehirleri yüklemek için kullanılan fonksiyon
  function loadCities(countryId, containerType) {
    var citySelect = $("#citySelect" + containerType);
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
  $('.daterangeCustomer').on('focus mousedown', function () {
    preventCustomerDropdownHide = true;
  });
  $('.tarihAraligi button').on('mousedown', function () {
    preventCustomerDropdownHide = true;
  });
  $('.daterangeCustomer').on('apply.daterangepicker cancel.daterangepicker hide.daterangepicker', function () {
    preventCustomerDropdownHide = false;
  });

  // Müşteriler için daterangepicker başlatma (varsayılan son 3 gün)
  // Dashboard'dan gelen tarihler varsa onları, yoksa son 3 günü kullan
  let initialCustomerStartDate = dashboardStartDate ? moment(dashboardStartDate) : moment().subtract(2, 'days').startOf('day');
  let initialCustomerEndDate = dashboardEndDate ? moment(dashboardEndDate) : moment().endOf('day');

  $('.daterangeCustomer').daterangepicker({
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
    $(this.element).val(start_date.format('DD-MM-YYYY') + ' - ' + end_date.format('DD-MM-YYYY'));
    table.draw();
  });

  // Hızlı tarih filtreleme butonları
  $('.lastYearCustomer').on('click', function () {
    var $dateInput = $(this).closest('.item').find('.daterangeCustomer');
    $dateInput.data('daterangepicker').setStartDate(moment().subtract(1, 'year'));
    $dateInput.data('daterangepicker').setEndDate(moment());
    table.draw();
  });

  $('.lastMonthCustomer').on('click', function () {
    var $dateInput = $(this).closest('.item').find('.daterangeCustomer');
    $dateInput.data('daterangepicker').setStartDate(moment().subtract(1, 'month'));
    $dateInput.data('daterangepicker').setEndDate(moment());
    table.draw();
  });

  $('.lastWeekCustomer').on('click', function () {
    var $dateInput = $(this).closest('.item').find('.daterangeCustomer');
    $dateInput.data('daterangepicker').setStartDate(moment().subtract(7, 'days'));
    $dateInput.data('daterangepicker').setEndDate(moment());
    table.draw();
  });

  $('.yesterdayCustomer').on('click', function () {
    var $dateInput = $(this).closest('.item').find('.daterangeCustomer');
    $dateInput.data('daterangepicker').setStartDate(moment().subtract(1, 'days'));
    $dateInput.data('daterangepicker').setEndDate(moment().subtract(1, 'days'));
    table.draw();
  });

  $('.todayCustomer').on('click', function () {
    var $dateInput = $(this).closest('.item').find('.daterangeCustomer');
    $dateInput.data('daterangepicker').setStartDate(moment());
    $dateInput.data('daterangepicker').setEndDate(moment());
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
          data.tip = $('.musteriTipi').filter(function() { return $(this).val() !== ''; }).val() || '';
          data.il = $('.countrySelect').filter(function() { return $(this).val() !== ''; }).val() || '';
          data.ilce = $('.citySelect').filter(function() { return $(this).val() !== ''; }).val() || '';

          // Müşteri tarih aralığını ekle (görünen olan)
          var visibleDateRangePicker = $('.daterangeCustomer:visible').first();
          if (visibleDateRangePicker.length && visibleDateRangePicker.data('daterangepicker')) {
            data.from_date_customer = visibleDateRangePicker.data('daterangepicker').startDate.format('YYYY-MM-DD');
            data.to_date_customer = visibleDateRangePicker.data('daterangepicker').endDate.format('YYYY-MM-DD');
          }

          // Eğer URL'den dashboard tarih parametreleri geldiyse, onları da ajax isteğine ekle
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
    var searchContainer = $('#datatableCustomer_filter');
    var searchInput = searchContainer.find('input');
    searchInput.attr('placeholder', 'Müşteri Ara...');
    if (window.matchMedia("(max-width: 991.98px)").matches) {
        var mobileFilterWrapper = $('.d-lg-none .searchWrap');

        searchContainer.append(mobileFilterWrapper);
        searchContainer.addClass('input-group');
        mobileFilterWrapper.find('.btn').css({
            'border-top-left-radius': '0',
            'border-bottom-left-radius': '0'
        });
    }
    $('.searchWrap').css({ visibility: 'visible', opacity: 1 });
}
  });

  $('.musteriTipi').change(function(){
    table.draw();        
  });

  $('.countrySelect').change(function(){
    table.draw();        
  });

  $('.citySelect').change(function(){
    table.draw();        
  });

});
</script>

<script>
    $(document).ready(function() {
      // Ülke seçildiğinde şehirleri getir - bu fonksiyon yukarıda zaten tanımlandı
      // Tekrar tanımlamaya gerek yok, bu script bloğunu kaldırabilirsiniz
    });
</script>

@endsection