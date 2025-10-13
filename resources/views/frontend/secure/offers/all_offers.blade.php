@extends('frontend.secure.user_master')
@section('user')

<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{asset('backend/assets/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
<script src="{{asset('backend/assets/libs/jquery/jquery.min.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<style>
.searchWrap {
    visibility: hidden;
    opacity: 0;
}
.p-b{
  padding-bottom: 5px !important;
}
@media (max-width: 767px) {
.custom-p{
        padding-left: 0px !important;
      }
.pageDetail .searchWrap .dropdown-menu .item {
        margin-bottom: 0px !important;
    }
.searchWrap{margin-top: 0px !important;}
    .pageDetail .searchWrap{width: 30% !important;}
    .pageDetail .searchWrap{margin-bottom: 0px !important;}
 div.dataTables_filter input{margin-left: 0 !important;}
     #datatableOffer_filter label {
        width: 100% !important;
    }
    li.paginate_button.next, li.paginate_button.previous {
        font-size: 15px;
    }
        #datatableOffer_wrapper .dataTables_info {
        text-align: left !important;
    }
    .searchWrap .tarih-araligi {
    padding: 5px 0px;
}
        .pageDetail .searchWrap .dropdown-menu {
        min-width: calc(78vw - 20px) !important;
                transform: translate3d(12px, 4px, 0px) !important;
    }
        .pageDetail .searchWrap .dropdown-menu .item {
        margin-bottom: 0px !important;
        padding: 0px !important;
    }
    .searchWrap .dropdown-menu {
    padding: 0px !important;
}
.pageDetail .searchWrap .tarihAraligi .btn {
        color: #fff !important;
    background-color: #5c636a !important;
    border-color: #565e64 !important;
      }
}
</style>

<div class="page-content" id="passwords">
  <div class="container-fluid">
    <div class="row pageDetail">
      <div class="col-12">
        <div class="card">
          <div class="card-header sayfaBaslik">
            Teklifler
          </div>
          <div class="card-body">
            <table id="datatableOffer" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                {{-- MASAÜSTÜ GÖRÜNÜMÜ (Orijinal Kodunuz - Hiçbir Değişiklik Yapılmadı) --}}
{{-- Bu bölüm sadece geniş ekranlarda (lg ve üstü) görünecektir. --}}
<div class="d-none d-lg-block">
    <a class="btn btn-success btn-sm addOffer" data-bs-toggle="modal" data-bs-target="#addOfferModal"><i class="fas fa-plus"></i><span>Teklif Ekle</span></a> 
    <div class="searchWrap float-end">
        <div class="btn-group mb-2 ">
            <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Filtrele <i class="mdi mdi-chevron-down"></i>
            </button>
            <div class="dropdown-menu">
                <div class="item">
                    <div class="row">
                        <label class="col-sm-4">Durum</label>
                        <div class="col-sm-8">
                            <select name="teklifDurumu" id="teklifDurumu" class="form-select">
                                <option value="">Hepsi</option>
                                <option value="0">Beklemede</option>
                                <option value="1">Onaylandı</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="item">
                    <div class="row">
                        <label class="col-sm-4">Tarih Aralığı:</label>
                        <div class="col-sm-8">
                            <input id="daterange" class="tarih-araligi">
                            <div class="tarihAraligi mt-2 mb-2">
                              <button id="today" class="btn btn-sm btn-secondary">Bugün</button>
                              <button id="yesterday" class="btn btn-sm btn-secondary">Dün</button>
                              <button id="lastWeek" class="btn btn-sm btn-secondary">Son 7 Gün</button>
                              <button id="lastMonth" class="btn btn-sm btn-secondary">Son 1 Ay</button>
                                <button id="lastYear" class="btn btn-sm btn-secondary">Son 1 Yıl</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /btn-group -->
    </div>
</div>


<div class="d-lg-none">
    <div  class="d-flex gap-1 justify-content-between align-items-center mb-2">
        <a class="btn btn-success btn-sm addOffer" data-bs-toggle="modal" data-bs-target="#addOfferModal">
            <i class="fas fa-plus"></i><span>Teklif Ekle</span>
        </a>
        <div style="margin-top: 0px !important;" class="searchWrap">
            <div class="btn-group" id="teklif_filtre">
                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Filtrele <i class="mdi mdi-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                    <div class="item">
                        <div class="row">
                            <label class="col-sm-4 custom-p  col-4">Durum</label>
                            <div class="col-sm-8 custom-p col-8">
                                <select name="teklifDurumu" id="teklifDurumu" class="form-select">
                                    <option value="">Hepsi</option>
                                    <option value="0">Beklemede</option>
                                    <option value="1">Onaylandı</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="row">
                            <label class="col-sm-4 custom-p  col-4">Tarih Aralığı:</label>
                            <div class="col-sm-8 custom-p col-8">
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
                  <th data-priority="2">Tarih</th>
                  <th>Müşteri Adı</th>
                  <th>G. Toplam</th>
                  <th>Durum</th>
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
<div id="addOfferModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="myModalLabel">Teklif Ekle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-b">
      Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- edit modal content -->
<div id="editOfferModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="myModalLabel">Teklif Düzenle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
  var getUrlParameter = function getUrlParameter(sParam) {
      var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;
      for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
      }
    };

    var mid = getUrlParameter('did');
    var firma_id = {{$firma->id}};
    if(mid){
      $.ajax({
        url: "/"+ firma_id + "/teklif-duzenle/"+ mid
      }).done(function(data) { 
        if($.trim(data)==="-1"){
          window.location.reload(true);
        }else{
          $('#editOfferModal').modal('show');
          $('#editOfferModal .modal-body').html(data);
        }
      });
    }
</script>

<script type="text/javascript">
$(document).ready(function(){
  $(".addOffer").click(function(){
    var firma_id = {{$firma->id}};
    $.ajax({
      url: "/"+ firma_id + "/teklif-ekle/"
    }).done(function(data) {
      if ($.trim(data) === "-1") {
        window.location.reload(true);
      } else {
        $('#addOfferModal').modal('show');
        $('#addOfferModal .modal-body').html(data);
      }
    });
  });
  $("#addOfferModal").on("hidden.bs.modal", function() {
    $('#addOfferModal .modal-body').html("");
  });
});
</script>

<script type="text/javascript">
$(document).ready(function(){
    $('#datatableOffer').on('click', '.editOffer', function(e){
        var id = $(this).attr("data-bs-id");
        var firma_id = {{$firma->id}};
        $.ajax({
            url: "/"+ firma_id + "/teklif-duzenle/" + id
        }).done(function(data) {
            if ($.trim(data) === "-1") {
                window.location.reload(true);
            } else {
                $('#editOfferModal').modal('show');
                $('#editOfferModal .modal-body').html(data);
            }
        });
    });
    $("#editOfferModal").on("hidden.bs.modal", function() {
      $('#editOfferModal .modal-body').html("");
    });
});
</script>

<script>
  $(document).ready(function () {
    // Tarih aralığı seçenekleri
    var lastYear = moment().subtract(1, 'year');
    var lastMonth = moment().subtract(1, 'month');
    var lastWeek = moment().subtract(7, 'days');
    var yesterday = moment().subtract(1, 'days');
    var today = moment();

    // Butonları oluştur ve tarih aralığını güncelle
    $('#lastYear').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(lastYear);
        $('#daterange').data('daterangepicker').setEndDate(today);
        // Filtreleme fonksiyonunu çağır
        filterData();
    });

    $('#lastMonth').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(lastMonth);
        $('#daterange').data('daterangepicker').setEndDate(today);
        // Filtreleme fonksiyonunu çağır
        filterData();
    });

    $('#lastWeek').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(lastWeek);
        $('#daterange').data('daterangepicker').setEndDate(today);
        // Filtreleme fonksiyonunu çağır
        filterData();
    });

    $('#yesterday').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(yesterday);
        $('#daterange').data('daterangepicker').setEndDate(yesterday);
        // Filtreleme fonksiyonunu çağır
        filterData();
    });

    $('#today').on('click', function() {
        $('#daterange').data('daterangepicker').setStartDate(today);
        $('#daterange').data('daterangepicker').setEndDate(today);
        // Filtreleme fonksiyonunu çağır
        filterData();
    });
    
    // Filtreleme fonksiyonu
    function filterData() {
        $('#datatableOffer').DataTable().draw();
    }
});
</script>

<script>
$(document).ready(function () {
    // Varsayılan tarih aralığı: Son 3 gün (bugünden 2 gün öncesi - bugün)
    var start_date = moment().subtract(2, 'days');
    var end_date = moment(); 

    $('#daterange').daterangepicker({
      startDate : start_date,
      endDate : end_date,
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

    function(start_date, end_date){
      $('#daterange').html(start_date.format('DD-MM-YYYY') + '-' + end_date.format('DD-MM-YYYY'));
      table.draw();
    });

  var table = $('#datatableOffer').DataTable({
      processing: true,
      serverSide: true,
      order: [[0, 'desc']],
      language: {
        paginate: {
          previous: "<i class='mdi mdi-chevron-left'>",
          next: "<i class='mdi mdi-chevron-right'>"
        }
      },
      ajax: {
        url: "{{ route('offers', $firma->id) }}",
        data: function(data) {
          data.search = $('input[type="search"]').val();
          data.teklifDurumu = $('#teklifDurumu').val();
          data.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
          data.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
        }
      },
      'columns': [
        { data: 'id', orderable: true},
        { data: 'created_at', orderable: true},
        { data: 'mid', orderable: true },
        { data: 'genelToplam', orderable: true },
        { data: 'teklifDurumu' , orderable: true},
        { data: 'action'}           
      ],
      drawCallback: function() {
        $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
      },
        "order": [[1, 'desc']],
        "columnDefs": [{
          "targets": 0,
          "className": "gizli"
        }],
        "oLanguage": {
            "sDecimal":        ",",
          "sEmptyTable":     "Tabloda herhangi bir veri mevcut değil",
          "sInfo":           "Teklif Sayısı: _TOTAL_",
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
    var searchContainer = $('#datatableOffer_filter');
    var searchInput = searchContainer.find('input');

    // Mobil ve masaüstünde tutarlı bir yapı için mobil görünüme ait filtre butonunu kullanacağız.
    var filterWrapper = $('.d-lg-none .searchWrap');

    // Arama kutusu ve filtre butonu için yeni bir flex container oluşturuyoruz.
    var flexContainer = $('<div class="d-flex justify-content-end w-100"></div>');

    // DataTables'ın varsayılan "Search:" etiketini kaldırıyoruz.
    searchContainer.find('label').contents().filter(function() {
        return this.nodeType == 3;
    }).remove();

    // Arama kutusunu ve kapsayıcısını stillendiriyoruz.
    searchContainer.addClass('flex-grow-1');
    searchInput.addClass('w-100');
    searchInput.attr('placeholder', 'Teklif Ara...');

    // Arama kutusunu ve filtre butonunu yeni flex container'a ekliyoruz.
    flexContainer.append(searchContainer);
    flexContainer.append(filterWrapper);

    // Artık ihtiyaç kalmadığı için masaüstü için olan orijinal filtre sarmalayıcısını kaldırıyoruz.
    $('.d-none.d-lg-block .searchWrap').remove();

    // Oluşturduğumuz yeni ve birleşik arama/filtre çubuğunu tablonun üst kısmına ekliyoruz.
    $('#datatableOffer_wrapper .top').append(flexContainer);

    // Taşıma işlemi bittikten sonra filtre butonunu görünür yapıyoruz.
    $('.searchWrap').css({ visibility: 'visible', opacity: 1 });
}
  });

  $('#teklifDurumu').change(function(){
    table.draw();        
  });

});


</script>
<script>
    $(document).ready(function () {
      // Olay dinleyiciyi, silinmeyen bir üst element olan `.searchWrap` üzerine kuruyoruz.
      // Bu sayede içindeki elementler taşınsa bile olay dinleyici çalışmaya devam eder.
      $('.searchWrap').on('show.bs.dropdown', '.btn-group', function () {
        // Tıklanan `.btn-group` içindeki `.filtrele` butonunu bulup metnini değiştiriyoruz.
        $(this).find('.filtrele').html('Kapat <i class="mdi mdi-chevron-down"></i>');
      });

      $('.searchWrap').on('hide.bs.dropdown', '.btn-group', function () {
        // Menü kapandığında metni tekrar eski haline getiriyoruz.
        $(this).find('.filtrele').html('Filtrele <i class="mdi mdi-chevron-down"></i>');
      });
    });
</script>
@endsection
