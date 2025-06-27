@extends('frontend.secure.user_master')
@section('user')

<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="{{asset('backend/assets/libs/jquery/jquery.min.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<div class="page-content" id="customerTable">
  <div class="container-fluid">
    <div class="row pageDetail">
      <div class="col-12">
        <div class="card">
          <div class="card-header sayfaBaslik">
            Servisler
          </div>
          <div class="card-body">
            <table id="datatableService" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                <a class="btn btn-success btn-sm addService" data-bs-toggle="modal" data-bs-target="#addServiceModal"><i class="fas fa-plus"></i><span>Servis Ekle</span></a> 
          
              <div class="searchWrap float-end">
              <div class="btn-group mb-2 ">
                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Filtrele <i class="mdi mdi-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                  <div class="item">
                    <div class="row">
                      <label class="col-sm-5">Cihaz Marka:</label>
                      <div class="col-sm-7">
                        <select name="device_brands" id="device_brands" class="form-select">
                          <option value="">Hepsi</option>
                          @foreach($device_brands as $brand)
                            <option value="{{$brand->id}}">{{$brand->marka}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="item">
                    <div class="row">
                      <label class="col-sm-5">Cihaz Türü:</label>
                      <div class="col-sm-7">
                        <select name="device_types" id="device_types" class="form-select">
                          <option value="">Hepsi</option>
                          @foreach($device_types as $type)
                            <option value="{{$type->id}}">{{$type->cihaz}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="item">
                    <div class="row">
                      <label class="col-sm-5">Servis Durumu:</label>
                      <div class="col-sm-7">
                        <select name="stages" id="stages" class="form-select">
                          <option value="">Hepsi</option>
                          @foreach($service_stages as $stage)
                            <option value="{{$stage->id}}">{{$stage->asama}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="item">
                    <div class="row">
                      <label class="col-sm-5">Servis Kaynağı:</label>
                      <div class="col-sm-7">
                        <select name="stages" id="stages" class="form-select">
                          <option value="">Hepsi</option>
                          @foreach($service_resources as $resource)
                            <option value="{{$resource->id}}">{{$resource->kaynak}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="item">
                    <div class="row">
                      <label class="col-sm-5">İl:</label>
                      <div class="col-sm-7">
                        <select name="il" id="country2" class="form-control form-select" style="width:100%!important;">
                          <option value="" selected>-Seçiniz-</option>
                          @foreach($states as $item)
                            <option value="{{ $item->id }}">{{ $item->name}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="item">
                    <div class="row">
                      <label class="col-sm-5">İlçe:</label>
                      <div class="col-sm-7">
                        <select name="ilce" id="city2" class="form-control form-select" style="width:100%!important;">
                          <option value="" selected disabled>-Seçiniz-</option>                              
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
                  <th style="width: 10px">Tarih</th>
                  <th style="width: 250px">Müşteri</th>
                  <th style="">Cihaz</th>
                  <th>Servis Durumu</th>
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
<div id="addServiceModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addCustomerLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="width: 930px;">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="addCustomerLabel">Servis Ekle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- edit modal content -->
<div id="editServiceDescModal" class="modal fade" data-bs-backdrop="static" tabindex='-1'>  {{--data-bs-backdrop="static" data-bs-keyboard="false"  modalın hemen kapanmaması için bunu eklemiştim. Eğer eklenmesi gerekirse aria-hidden in yanına ekleyebilirsin--}}
  <div class="modal-dialog modal-lg" style="width: 980px;">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="editCustomerLabel">Servis Bilgileri Düzenle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- edit modal content -->
<div id="editServiceCustomerModal" class="modal fade"  style="padding-top: 50px;background: rgba(0, 0, 0, 0.50);">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="editCustomerLabel">Servis Müşteri Düzenle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="editServiceNotModal" class="modal fade" style="padding-top: 50px;background: rgba(0, 0, 0, 0.50);">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="editCustomerLabel">Müşteri Notu Düzenle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="editServicePlanModal" class="modal fade" style="padding-top: 50px;background: rgba(0, 0, 0, 0.50);">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" >Servis Plan Düzenle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script type="text/javascript">
$(document).ready(function(){
  $(".addService").click(function(){
    var firma_id = {{$firma->id}};
    $.ajax({
      url: "/"+ firma_id + "/servis/ekle/"
    }).done(function(data) {
      if ($.trim(data) === "-1") {
        window.location.reload(true);
      } else {
        $('#addServiceModal').modal('show');
        $('#addServiceModal .modal-body').html(data);
      }
    });
  });
  $("#addServiceModal").on("hidden.bs.modal", function() {
      $(".modal-body").html("");
    });
});
</script>

<script type="text/javascript">
$(document).ready(function(){
    $('#datatableService').on('click', '.serBilgiDuzenle', function(e){
        var id = $(this).attr("data-bs-id");
        var firma_id = {{$firma->id}};
        $.ajax({
            url: "/" + firma_id + "/servis/duzenle/" + id
        }).done(function(data) {
            if ($.trim(data) === "-1") {
                window.location.reload(true);
            } else {
              $('#editServiceDescModal .modal-body').html(data);
                $('#editServiceDescModal').modal('show');
                
                
            }
        });
    });
    $("#editServiceDescModal").on("hidden.bs.modal", function() {
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
        $('#datatableService').DataTable().draw();
    }
});
</script>

<script>
  $(document).ready(function () {
    var start_date = '01-01-2024';
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

    var firma_id = {{$firma->id}};
    var table = $('#datatableService').DataTable({
      processing: true,
      serverSide: true,
      ordering: true,
      language: {
        paginate: {
          previous: "<i class='mdi mdi-chevron-left'>",
          next: "<i class='mdi mdi-chevron-right'>"
        }
      },

      ajax: {
        url: "{{ route('all.services', $firma->id) }}",
        data: function(data) {
          data.search = $('input[type="search"]').val();
          data.device_brands = $('#device_brands').val();
          data.device_types = $('#device_types').val();
          data.stages = $('#stages').val();
        }
      },
      'columns': [
        { data: 'id',name: 'id', orderable: true },
        { data: 'created_at',name:'created_at', orderable: true},
        { data: 'm_adi',name:'m_adi', orderable: true },
        { data: 'cihaz',name: 'cihaz', orderable:true },
        { data: 'asama_id',name:'durum', orderable: true },
        { data: 'action', name:'action', orderable: false, searchable: false}           
      ],
      drawCallback: function() {
        $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        
        // Her satırı döngü ile kontrol et ve duruma göre arka plan rengini ayarla
        $('#datatableService tbody tr').each(function() {
          var asama_id = $(this).find('td:eq(4) strong').text().trim(); // Durum sütunu
          if (asama_id === 'Şikayetçi') {
            $(this).css('background-color', 'rgb(233 100 100)'); // Örneğin, kırmızı rgb(205 28 28)
          } else if (asama_id === 'Yeni Servisler') {
            $(this).css('background-color', 'rgb(135, 255, 135)'); // Örneğin, yeşil
          }else if (asama_id === 'Tekrar Aranacak') {
            $(this).css('background-color', 'rgb(242, 255, 42)')
          }else if (asama_id === 'Parça Takmak İçin Teknisyen Yönlendir') {
            $(this).css('background-color', 'rgb(98, 218, 255)')
          }     
        });
      },
      order: [[0, 'desc']],
      "columnDefs": [{
        "targets": 0,
        "className": "gizli"
      }],
      "oLanguage": {
        "sDecimal":        ",",
        "sEmptyTable":     "Tabloda herhangi bir veri mevcut değil",
        "sInfo":           "Servis Sayısı: _TOTAL_",
        "sInfoEmpty":      "Kayıt yok",
        "sInfoFiltered":   "",
        "sInfoPostFix":    "",
        "sInfoThousands":  ".",
        "sLengthMenu":     "_MENU_",
        "sLoadingRecords": "Yükleniyor...",
        "sProcessing":     "İşleniyor...",
        "sSearch":         "Servis Ara:",
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

    

    $('#device_brands').change(function(){
      table.draw();        
    }); 

    $('#device_types').change(function(){
      table.draw();        
    }); 

    $('#stages').change(function(){
      table.draw();        
    }); 
 
  });
</script>
<script>
 $(document).ready(function () {
    // Ülke seçildiğinde şehirleri getir
    $("#country2").change(function() {
      var selectedCountryId = $(this).val();
      if (selectedCountryId) {
        loadCities(selectedCountryId);
      }
    });
    // Şehirleri yüklemek için kullanılan fonksiyon
    function loadCities(countryId) {
      var citySelect = $("#city2");
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

