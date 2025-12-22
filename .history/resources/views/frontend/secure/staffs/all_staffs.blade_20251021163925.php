<style>
  .servisDrop {
    transition: none !important;
    animation: none !important;
    transform: translate3d(1px, 2px, 0px) !important;
  }
.card-staff{border: 1px solid rgba(0, 0, 0, .125) !important;}
.card-staff-header{background-color: #f7f7f7 !important;border-bottom: 1px solid rgba(0, 0, 0, .125) !important;margin-bottom: 7px !important; padding: 4px 7px !important;}
  .card-staff-body{padding: 3px 7px !important;}

  @media (min-width: 767px) {
    .custom-modal-width {
      max-width: 360px !important;
      margin: 1.75rem auto;
    }

    .searchWrap .dropdown-menu {
      width: 271px !important;
    }
  }

  @media (max-width: 767px) {
        
    .pageDetail .searchWrap .dropdown-menu .item{
      margin-bottom: 0px !important;
    }
    .custom-p {
      padding-left: 0px !important;
    }

    .pageDetail .searchWrap .dropdown-menu .item {
      margin-bottom: 0px !important;
    }

    #datatablePersonel_wrapper .bottom {
      display: flex !important;
      justify-content: space-between !important;
      align-items: center !important;
      padding-top: 0.85em !important;
    }
    .staff-header-top{margin-top: 30px;}
  }

  #editPers {
    margin-block-end: 0em !important;
  }
</style>

@extends('frontend.secure.user_master')
@section('user')

  @php 
    if ($firma->isOnTrial()) {
      // Deneme süresinde => firmalar tablosundaki personelSayisi alanı kullanılacak
      $staffLimit = $firma->personelSayisi ?? null;
    } else {
      // Normal abonelik planındaki limit
      $staffLimit = $firma->plan()?->limits['users'] ?? null;
    }
    $staffAll = App\Models\User::where('tenant_id', $firma->id)
      ->where('status', '1')
      ->whereHas('roles', function ($query) {
        $query->where('name', '!=', 'Bayi');
      })->count();
  @endphp


  <style>
    @media (max-width: 767px) {
.pageDetail .card-header {
        padding: 4px 8px !important;
    }
      .searchWrap {
        margin-top: 0px !important;
      }

      .pageDetail .searchWrap {
        width: 30% !important;
      }

      .pageDetail .searchWrap {
        margin-bottom: 0px !important;
      }

      div.dataTables_filter input {
        margin-left: 0 !important;
      }

      .dataTables_filter {
        margin-right: 0px !important;
      }

      .pageDetail .searchWrap .dropdown-menu {
        transform: translate3d(12px, 1px, 0px) !important;
        width: 100% !important;
        min-width: calc(79vw - 20px) !important;
        padding: 0px !important;
      }

      #datatablePersonel_filter label {
        width: 100% !important;
      }

      #datatablePersonel_wrapper .dataTables_info {
        width: auto !important;
      }

      li.paginate_button.next,
      li.paginate_button.previous {
        display: inline-block;
        font-size: 15px;
      }
    }
  </style>
  <div class="page-content">
    <div class="container-fluid staff-header-top">
      <div class="row pageDetail">
        <div class="col-12">
          <div class="card card-staff">
            <div class="card-header card-staff-header sayfaBaslik">
              Personeller
            </div>
            <div class="card-body card-staff-body">
              <table id="datatablePersonel" class="table table-bordered dt-responsive nowrap"
                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                <div class="staff-buttons-container d-flex d-sm-block">
  @if(is_null($staffLimit) || $staffLimit == -1 || $staffAll < $staffLimit)
    <a data-bs-toggle="modal" data-bs-target="#addPersonelModal" class="btn btn-success btn-sm addPersonel">
      <i class="fas fa-plus"></i><span>Personel Ekle</span>
    </a>
  @else
    <a class="btn btn-success btn-sm addPersonel" disabled
      style="pointer-events: none; opacity: .4; cursor: default;">
      <i class="fas fa-plus"></i><span>Personel Ekle</span>
    </a>
    <span class="text-muted ms-2" style="position:absolute;left: 104px;top: 47px;">
      <i class="fas fa-info-circle me-1"></i>Aboneliğinize göre personel limiti doldu (maks: {{ $staffLimit }})
    </span>
  @endif
  
  <button id="printStaffs" class="btn btn-warning btn-sm printStaffs">
    <i class="fas fa-print"></i>
    <span>Yazdır</span>
  </button>
</div>
                <div class="searchWrap float-end">
                  <div class="btn-group " id="personelfiltre">
                    <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown"
                      aria-expanded="false">
                      Filtrele <i class="mdi mdi-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu servisDrop">
                      <div class="item">
                        <div class="row">
                          <label class="col-sm-5 custom-p col-5 ">Durum</label>
                          <div class="col-sm-7 custom-p custom-p-m col-7">
                            <select name="durum" id="durum" class="form-select">
                              <option value="2">Hepsi</option>
                              <option value="1" selected>Çalışıyor</option>
                              <option value="0">Ayrıldı</option>
                            </select>
                          </div>
                        </div>
                      </div>

                      <div class="item">
                        <div class="row">
                          <label class="col-sm-5 custom-p col-5">Personel Grubu</label>
                          <div class="col-sm-7 custom-p custom-p-m col-7">
                            <select name="rolePers" id="rolePers" class="form-select">
                              <option value="">Hepsi</option>
                              @foreach($roles as $role)
                                <option value="{{$role->id}}">{{$role->name}}</option>
                              @endforeach
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
                    <th data-priority="2">Personel Adı</th>
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
  <div id="addPersonelModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog custom-modal-width">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title" id="myModalLabel">Personel Ekle</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Yükleniyor...
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->


  <!-- edit modal content -->
  <div id="editPersonelModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog custom-modal-width">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title" id="myModalLabel">Personel Düzenle</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Yükleniyor...
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

  <script type="text/javascript">
    $(document).ready(function () {
      var firma_id = {{$firma->id}};
      $(".addPersonel").click(function () {

        $.ajax({
          url: "/" + firma_id + "/personel-ekle/"
        }).done(function (data) {
          if ($.trim(data) === "-1") {
            window.location.reload(true);
          } else {
            $('#addPersonelModal').modal('show');
            $('#addPersonelModal .modal-body').html(data);
          }
        });
      });
      $("#addPersonelModal").on("hidden.bs.modal", function () {
        $('#addPersonelModal .modal-body').html("");
      });
    });
  </script>


<script type="text/javascript">
$(document).ready(function(){
    // Edit Personel Modal - Buton click event'i 
    $('#datatablePersonel').on('click', '.editPersonel', function(e){

        var id = $(this).attr("data-bs-id");
        var firma_id = {{$firma->id}};
        $.ajax({
          url: "/" + firma_id + "/personel/duzenle/" + id
        }).done(function (data) {
          if ($.trim(data) === "-1") {
            window.location.reload(true);
          } else {
            $('#editPersonelModal').modal('show');
            $('#editPersonelModal .modal-body').html(data);
          }
        });
      });
      $("#editPersonalModal").on("hidden.bs.modal", function () {
        $('#editPersonelModal .modal-body').html("");
      });
    });


    // Mobilde ve masaüstünde satırın boş alanlarına tıklayınca da açılsın
    $('#datatablePersonel tbody').on('click', 'tr', function(e) {
        var $target = $(e.target);
        
        // Düzenle butonuna tıklandıysa, bu tr event'ini çalıştırma (butonun kendi event'i çalışsın)
        if ($target.closest('.editPersonel').length > 0 ||
            $target.closest('.btn').length > 0 || 
            $target.closest('td').index() === 6) {  // Düzenle kolonu 6. index
            return;
        }
        
        var id = $(this).find('.editPersonel').first().attr('data-bs-id');
        
        if (id) {
            // 1. MODAL'I HEMEN AÇ (AJAX beklemeden)
            $('#editPersonelModal').modal('show');
            
            // 2. AYNI ANDA AJAX BAŞLAT
            var firma_id = {{$firma->id}};
            $.ajax({
                url: "/" + firma_id + "/personel/duzenle/" + id
            }).done(function(data) {
                if ($.trim(data) === "-1") {
                    window.location.reload(true);
                } else {
                    $('#editPersonelModal .modal-body').html(data);
                }
            });
        }
    });

    $("#editPersonelModal").on("hidden.bs.modal", function() {
        $('#editPersonelModal .modal-body').html("");
    });

</script>

<script>
$(document).ready(function () {
  var table = $('#datatablePersonel').DataTable({
      processing: true,
      serverSide: true,
      language: {
        paginate: {
          previous: "<i class='mdi mdi-chevron-left'>",
          next: "<i class='mdi mdi-chevron-right'>"
        }
      },
      ajax: {
        url: "{{ route('staffs',$firma->id) }}",
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
          "sDecimal": ",",
          "sEmptyTable": "Tabloda herhangi bir veri mevcut değil",
          "sInfo": "Personel Sayısı: _TOTAL_",
          "sInfoEmpty": "Kayıt yok",
          "sInfoFiltered": "",
          "sInfoPostFix": "",
          "sInfoThousands": ".",
          "sLengthMenu": "_MENU_",
          "sLoadingRecords": "Yükleniyor...",
          "sProcessing": "İşleniyor...",
          "sSearch": "",
          "sZeroRecords": "Eşleşen kayıt bulunamadı",
          "oPaginate": {
            "sFirst": "İlk",
            "sLast": "Son",
            "sNext": '<i class="fas fa-angle-double-right"></i>',
            "sPrevious": '<i class="fas fa-angle-double-left"></i>'
          },
          "oAria": {
            "sSortAscending": ": artan sütun sıralamasını aktifleştir",
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
        "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "Tümü"]],
        "initComplete": function (settings, json) {
          // --- DEĞİŞTİRİLEN BÖLÜM BURASI ---
          var topContainer = $('#datatablePersonel_wrapper .top'); // .top div'ini seçiyoruz
          var searchContainer = $('#datatablePersonel_filter');
          var searchInput = searchContainer.find('input');
          var filterWrapper = $('.searchWrap');
          var flexContainer = $('<div class="d-flex justify-content-end w-100"></div>');

          searchContainer.find('label').contents().filter(function () {
            return this.nodeType == 3;
          }).remove();

          searchContainer.addClass('flex-grow-1');
          searchInput.addClass('w-100');
          searchInput.attr('placeholder', 'Personel Ara...');

          flexContainer.append(searchContainer);
          flexContainer.append(filterWrapper);

          // .append() yerine .html() kullanarak mevcut içeriği değiştiriyoruz
          topContainer.html(flexContainer);

          $('.searchWrap').css({ visibility: 'visible', opacity: 1 });
          // --- DEĞİŞTİRİLEN BÖLÜM SONU ---
        }
      });

      $('#rolePers').change(function () {
        table.draw();
      });

      $('#durum').change(function () {
        table.draw();
      });

    });
  </script>

  <script>
    $(document).ready(function () {
      var dropdownContainer = $('#personelfiltre');
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