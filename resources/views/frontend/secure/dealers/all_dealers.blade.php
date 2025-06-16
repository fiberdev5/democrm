@extends('frontend.secure.user_master')
@section('user')

@php 
$bayiSayisi = $firma->bayiSayisi;
$dealerAll = App\Models\User::where('tenant_id', $firma->id)->where('status','1')
    ->whereHas('roles', function ($query) {
        $query->where('name', 'Bayi');
    })->count();
@endphp


<div class="page-content">
  <div class="container-fluid">
    <div class="row pageDetail">
      <div class="col-12">
        <div class="card">
          <div class="card-header sayfaBaslik">
            Bayiler
          </div>
          <div class="card-body">
            <table id="datatableBayi" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                @if($dealerAll<$bayiSayisi)
                <a data-bs-toggle="modal" data-bs-target="#addBayiModal" class="btn btn-success btn-sm addBayi"><i class="fas fa-plus"></i><span>Bayi Ekle</span></a> 
                @else
                <a data-bs-toggle="modal" data-bs-target="#addBayiModal" class="btn btn-success btn-sm addBayi" disabled="disabled" style="pointer-events: none;opacity: .4;cursor: default;"><i class="fas fa-plus"></i><span>Bayi Ekle</span></a> 
                @endif
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
                            <select name="durum" id="durum" class="form-select">
                              <option value="2">Hepsi</option>
                              <option value="1" selected>Çalışıyor</option>
                              <option value="0">Ayrıldı</option>
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
                    <th data-priority="2">Bayi Adı</th>
                    <th>Personel Grubu</th>
                    <th>Telefon</th>
                    <th>Adres</th>
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
<div id="addBayiModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="myModalLabel">Bayi Ekle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- edit modal content -->
<div id="editBayiModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="myModalLabel">Bayi Düzenle</h6>
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
  var firma_id = {{$firma->id}};
  $(".addBayi").click(function(){
    
    $.ajax({
      url: "/"+ firma_id + "/bayi-ekle/"
    }).done(function(data) {
      if ($.trim(data) === "-1") {
        window.location.reload(true);
      } else {
        $('#addBayiModal').modal('show');
        $('#addBayiModal .modal-body').html(data);
      }
    });
  });
  $("#addBayiModal").on("hidden.bs.modal", function() {
      $(".modal-body").html("");
    });
});
</script>

<script type="text/javascript">
$(document).ready(function(){
    $('#datatableBayi').on('click', '.editBayi', function(e){
        var id = $(this).attr("data-bs-id");
        var firma_id = {{$firma->id}};
        $.ajax({
            url: "/"+ firma_id + "/bayi/duzenle/" + id
        }).done(function(data) {
            if ($.trim(data) === "-1") {
                window.location.reload(true);
            } else {
                $('#editBayiModal').modal('show');
                $('#editBayiModal .modal-body').html(data);
            }
        });
    });
    $("#editBayiModal").on("hidden.bs.modal", function() {
      $(".modal-body").html("");
    });
});
</script>

<script>
$(document).ready(function () {
  var table = $('#datatableBayi').DataTable({
      processing: true,
      serverSide: true,
      language: {
        paginate: {
          previous: "<i class='mdi mdi-chevron-left'>",
          next: "<i class='mdi mdi-chevron-right'>"
        }
      },
      ajax: {
        url: "{{ route('dealers.data', ['tenant_id' => $firma->id]) }}",
        data: function(data) {
          data.search = $('input[type="search"]').val();
          data.durum = $('#durum').val();
          data.grup = $('#rolePers').val();
        }
      },
      'columns': [
        { data: 'user_id'},
        { data: 'name' },
        { data: 'grup', orderable: false },
        { data: 'tel' },
        { data: 'address' },
        { data: 'status' },
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
          "sInfo":           "Bayi Sayısı: _TOTAL_",
          "sInfoEmpty":      "Kayıt yok",
          "sInfoFiltered":   "",
          "sInfoPostFix":    "",
          "sInfoThousands":  ".",
          "sLengthMenu":     "_MENU_",
          "sLoadingRecords": "Yükleniyor...",
          "sProcessing":     "İşleniyor...",
          "sSearch":         "Bayi Ara:",
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

  $('#rolePers').change(function(){
    table.draw();        
  });

  $('#durum').change(function(){
    table.draw();        
  });

});
</script>

@endsection
