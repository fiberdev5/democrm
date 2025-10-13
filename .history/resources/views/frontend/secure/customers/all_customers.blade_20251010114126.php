@extends('frontend.secure.user_master')
@section('user')
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<style>
#datatableCustomer_filter input[type="search"] {
    padding-right: 12px !important; 
}
.searchWrap {
    visibility: hidden;
    opacity: 0;
}

@media (max-width: 767px) {
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
            Müşteriler
          </div>
          <div class="card-body">
            <table id="datatableCustomer" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

<div class="d-none d-lg-block">
    <a data-bs-toggle="modal" data-bs-target="#addCustomerModal" class="btn btn-success btn-sm addCustomer"><i class="fas fa-plus"></i><span>Müşteri Ekle</span></a> 
    <div class="searchWrap float-end">
        <div class="btn-group mb-2 ">
            <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Filtrele <i class="mdi mdi-chevron-down"></i>
            </button>
            <div class="dropdown-menu">
                <div class="item">
                    <div class="row">
                        <label class="col-sm-5">Durum</label>
                        <div class="col-sm-7">
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
                        <label class="col-sm-5">İl</label>
                        <div class="col-sm-7">
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
                        <label class="col-sm-5">İlçe</label>
                        <div class="col-sm-7">
                            <select name="ilce" id="citySelect" class="form-control form-select" style="width:100%!important;">
                                <option value="" selected disabled>-Seçiniz-</option>                              
                            </select>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="row">
                        <label class="col-sm-5">Tarih Aralığı:</label>
                        <div class="col-sm-7">
                            <input id="daterange" class="tarih-araligi">
                            <div class="tarihAraligi mt-2 mb-2">
                                <button id="lastYear" class="btn btn-sm btn-secondary">Son 1 Yıl</button>
                                <button id="lastMonth" class="btn btn-sm btn-secondary">Son 1 Ay</button>
                                <button id="lastWeek" class="btn btn-sm btn-secondary">Son 7 Gün</button>
                                <button id="yesterday" class="btn btn-sm btn-secondary">Dün</button>
                                <button id="today" class="btn btn-sm btn-secondary">Bugün</button>
                            </div>
                        </div>
                    </div>
                </div>
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
            <div class="btn-group">
                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Filtrele <i class="mdi mdi-chevron-down"></i>
                </button>
                <div  class="dropdown-menu">
                    <div class="item">
                        <div class="row">
                            <label class="col-sm-5">Durum</label>
                            <div class="col-sm-7">
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
                            <label class="col-sm-5">İl</label>
                            <div class="col-sm-7">
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
                            <label class="col-sm-5">İlçe</label>
                            <div class="col-sm-7">
                                <select name="ilce" id="citySelect" class="form-control form-select" style="width:100%!important;">
                                    <option value="" selected disabled>-Seçiniz-</option>                              
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="row">
                            <label class="col-sm-5">Tarih Aralığı:</label>
                            <div class="col-sm-7">
                                <input id="daterange" class="tarih-araligi">
                                <div class="tarihAraligi mt-2 mb-2">
                                    <button id="lastYear" class="btn btn-sm btn-secondary">Son 1 Yıl</button>
                                    <button id="lastMonth" class="btn btn-sm btn-secondary">Son 1 Ay</button>
                                    <button id="lastWeek" class="btn btn-sm btn-secondary">Son 7 Gün</button>
                                    <button id="yesterday" class="btn btn-sm btn-secondary">Dün</button>
                                    <button id="today" class="btn btn-sm btn-secondary">Bugün</button>
                                </div>
                            </div>
                        </div>
                    </div>
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
    // Dropdown kapanma kontrolü
    let preventDropdownHide = false;
    $('.btn-group').on('hide.bs.dropdown', function (e) {
        if (preventDropdownHide) {
            e.preventDefault();
        }
        preventDropdownHide = false;
    });

    $(document).on('mousedown', function (e) {
        if ($(e.target).closest('.daterangepicker').length) {
            preventDropdownHide = true;
        }
    });

    $('#daterange').on('focus mousedown', function () {
        preventDropdownHide = true;
    });

    $('.tarihAraligi button').on('mousedown', function () {
        preventDropdownHide = true;
    });

    $('#daterange').on('apply.daterangepicker cancel.daterangepicker hide.daterangepicker', function () {
        preventDropdownHide = false;
    });

    // Tarih değişkenleri
    var lastYear = moment().subtract(1, 'year');
    var lastMonth = moment().subtract(1, 'month');
    var lastWeek = moment().subtract(7, 'days');
    var yesterday = moment().subtract(1, 'days');
    var today = moment();

    // DateRangePicker başlat
    $('#daterange').daterangepicker({
        autoUpdateInput: false,
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
    });

    $('#daterange').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
        filterData();
    });

    $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        filterData();
    });

    // Dashboard'dan gelen URL parametrelerini oku
    const urlParams = new URLSearchParams(window.location.search);
    const startDate = urlParams.get('dashboard_istatistik_tarih1');
    const endDate = urlParams.get('dashboard_istatistik_tarih2');

    if (startDate && endDate) {
        $('#daterange').data('daterangepicker').setStartDate(moment(startDate));
        $('#daterange').data('daterangepicker').setEndDate(moment(endDate));
        $('#daterange').val(moment(startDate).format('DD-MM-YYYY') + ' - ' + moment(endDate).format('DD-MM-YYYY'));
    }

    // Hızlı tarih butonları
    $('#lastYear').on('click', function () {
        $('#daterange').data('daterangepicker').setStartDate(lastYear);
        $('#daterange').data('daterangepicker').setEndDate(today);
        $('#daterange').val(lastYear.format('DD-MM-YYYY') + ' - ' + today.format('DD-MM-YYYY'));
        filterData();
    });

    $('#lastMonth').on('click', function () {
        $('#daterange').data('daterangepicker').setStartDate(lastMonth);
        $('#daterange').data('daterangepicker').setEndDate(today);
        $('#daterange').val(lastMonth.format('DD-MM-YYYY') + ' - ' + today.format('DD-MM-YYYY'));
        filterData();
    });

    $('#lastWeek').on('click', function () {
        $('#daterange').data('daterangepicker').setStartDate(lastWeek);
        $('#daterange').data('daterangepicker').setEndDate(today);
        $('#daterange').val(lastWeek.format('DD-MM-YYYY') + ' - ' + today.format('DD-MM-YYYY'));
        filterData();
    });

    $('#yesterday').on('click', function () {
        $('#daterange').data('daterangepicker').setStartDate(yesterday);
        $('#daterange').data('daterangepicker').setEndDate(yesterday);
        $('#daterange').val(yesterday.format('DD-MM-YYYY') + ' - ' + yesterday.format('DD-MM-YYYY'));
        filterData();
    });

    $('#today').on('click', function () {
        $('#daterange').data('daterangepicker').setStartDate(today);
        $('#daterange').data('daterangepicker').setEndDate(today);
        $('#daterange').val(today.format('DD-MM-YYYY') + ' - ' + today.format('DD-MM-YYYY'));
        filterData();
    });

    // Filtreleme fonksiyonu
    function filterData() {
        $('#datatableCustomer').DataTable().draw();
    }

   // DataTable
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

          // Tarih filtresi
          var dateRange = $('#daterange').val();
          if (dateRange) {
              var dates = dateRange.split(' - ');
              data.from_date = moment(dates[0], 'DD-MM-YYYY').format('YYYY-MM-DD');
              data.to_date = moment(dates[1], 'DD-MM-YYYY').format('YYYY-MM-DD');
          }

          // Eğer URL'den tarih parametreleri geldiyse, onları da ajax isteğine ekle
          if (startDate && endDate) {
              data.dashboard_istatistik_tarih1 = startDate;
              data.dashboard_istatistik_tarih2 = endDate;
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

@endsection