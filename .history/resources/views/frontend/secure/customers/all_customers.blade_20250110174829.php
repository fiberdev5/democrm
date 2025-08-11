@extends('frontend.secure.user_master')
@section('user')

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

                  </div>
                </div><!-- /btn-group -->
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
      $(".modal-body").html("");
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
      $(".modal-body").html("");
    });
});
</script>

<script>
$(document).ready(function () {
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
          "sSearch":         "Müşteri Ara:",
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
