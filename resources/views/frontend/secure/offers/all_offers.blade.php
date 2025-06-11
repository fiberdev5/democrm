@extends('frontend.secure.user_master')
@section('user')

<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{asset('backend/assets/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
<script src="{{asset('backend/assets/libs/jquery/jquery.min.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />



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
      <div class="modal-body">
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
    $(".modal-body").html("");
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
      $(".modal-body").html("");
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
    var start_date = '01-01-2023';
    var end_date = moment().add(1, 'day');

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
          "sSearch":         "Teklif Ara:",
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
        dom: '<"top"f>rt<"bottom"ilp><"clear">',
        "lengthMenu": [ [25, 50, 100, -1], [25, 50, 100, "Tümü"] ],
  });

  $('#teklifDurumu').change(function(){
    table.draw();        
  });

});
</script>
@endsection
