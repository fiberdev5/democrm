@extends('frontend.secure.user_master')
@section('user')
  <!-- Moment.js ve DateRangePicker kütüphaneleri -->
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
  }
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

@media (max-width: 767px) {
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
          .pageDetail .searchWrap .dropdown-menu{transform: translate3d(11px, 3px, 0px) !important;min-width: 100% !important;}
.pageDetail .searchWrap .dropdown-menu .item {
    margin-bottom: 0px !important;
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
                        <label class="col-sm-3 custom-p col-3 filtre-i-p">Durum</label>
                        <div class="filtre-i-p custom-p custom-p-m col-9 col-sm-9">
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
                        <label class="col-sm-3 custom-p col-3 filtre-i-p">İl</label>
                        <div class="col-sm-9 custom-p custom-p-m col-9 filtre-i-p">
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
                        <label class="col-sm-3 custom-p col-3 filtre-i-p">İlçe</label>
                        <div class="col-sm-9 custom-p custom-p-m col-9 filtre-i-p">
                            <select name="ilce" id="citySelect" class="form-control form-select" style="width:100%!important;">
                                <option value="" selected disabled>-Seçiniz-</option>                              
                            </select>
                        </div>
                    </div>
                </div>
                <!-- TARİH FİLTRESİ -->
                <div class="item">
                    <div class="row">
                        <label class="col-sm-5 col-5 custom-p">Tarih Aralığı:</label>
                        <div class="col-sm-7 col-7 custom-p">
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
  <div class="modal-dialog custom-modal-width">
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
});
</script>

<script type="text/javascript">
$(document).ready(function(){
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
});
</script>

<script>
$(document).ready(function () {
  // Dashboard'dan gelen URL parametrelerini oku
  const urlParams = new URLSearchParams(window.location.search);
  const dashboardStartDate = urlParams.get('dashboard_istatistik_tarih1');
  const dashboardEndDate = urlParams.get('dashboard_istatistik_tarih2');

  // Dropdown'un daterangepicker ile etkileşimde kapanmasını engelle
  $('#müsteri_filtre .dropdown-menu').on('click', function(e) {
    e.stopPropagation();
  });

  // Dashboard'dan tarih gelirse onu kullan, yoksa son 3 günü kullan
  let initialStartDate = dashboardStartDate ? moment(dashboardStartDate) : moment().subtract(2, 'days').startOf('day');
  let initialEndDate = dashboardEndDate ? moment(dashboardEndDate) : moment().endOf('day');

  // DateRangePicker başlatma
  $('#daterangeCustomer').daterangepicker({
    startDate: initialStartDate,
    endDate: initialEndDate,
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
  }, function (start_date, end_date) {
    $('#daterangeCustomer').val(start_date.format('DD-MM-YYYY') + ' - ' + end_date.format('DD-MM-YYYY'));
    table.draw();
  });

  // Hızlı tarih filtreleme butonları
  $('#lastYearCustomer').on('click', function (e) {
    e.preventDefault();
    $('#daterangeCustomer').data('daterangepicker').setStartDate(moment().subtract(1, 'year'));
    $('#daterangeCustomer').data('daterangepicker').setEndDate(moment());
    table.draw();
  });

  $('#lastMonthCustomer').on('click', function (e) {
    e.preventDefault();
    $('#daterangeCustomer').data('daterangepicker').setStartDate(moment().subtract(1, 'month'));
    $('#daterangeCustomer').data('daterangepicker').setEndDate(moment());
    table.draw();
  });

  $('#lastWeekCustomer').on('click', function (e) {
    e.preventDefault();
    $('#daterangeCustomer').data('daterangepicker').setStartDate(moment().subtract(7, 'days'));
    $('#daterangeCustomer').data('daterangepicker').setEndDate(moment());
    table.draw();
  });

  $('#yesterdayCustomer').on('click', function (e) {
    e.preventDefault();
    $('#daterangeCustomer').data('daterangepicker').setStartDate(moment().subtract(1, 'days'));
    $('#daterangeCustomer').data('daterangepicker').setEndDate(moment().subtract(1, 'days'));
    table.draw();
  });

  $('#todayCustomer').on('click', function (e) {
    e.preventDefault();
    $('#daterangeCustomer').data('daterangepicker').setStartDate(moment());
    $('#daterangeCustomer').data('daterangepicker').setEndDate(moment());
    table.draw();
  });

  var table = $('#datatableCustomer').DataTable({
      processing: false,
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

          // Tarih filtrelerini ekle
          data.from_date_customer = $('#daterangeCustomer').data('daterangepicker').startDate.format('YYYY-MM-DD');
          data.to_date_customer = $('#daterangeCustomer').data('daterangepicker').endDate.format('YYYY-MM-DD');

          // Dashboard'dan gelen tarihleri de ekle
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
          var filterWrapper = $('.searchWrap');
          var flexContainer = $('<div class="d-flex justify-content-end w-100"></div>');

          searchContainer.find('label').contents().filter(function() {
              return this.nodeType == 3;
          }).remove();
          
          searchInput.attr('placeholder', 'Müşteri Ara...');
          searchContainer.addClass('flex-grow-1');
          searchInput.addClass('w-100');

          flexContainer.append(searchContainer);
          flexContainer.append(filterWrapper);
          
          topContainer.html(flexContainer);

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
        citySelect.empty();
        citySelect.append(new Option("Yükleniyor...", ""));
    
        $.get("/get-states/" + countryId, function(data) {
          citySelect.empty();
          citySelect.append(new Option("-Seçiniz-", ""));
          $.each(data, function(index, city) {
            citySelect.append(new Option(city.ilceName, city.id));
          });
        }).fail(function() {
          citySelect.empty();
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