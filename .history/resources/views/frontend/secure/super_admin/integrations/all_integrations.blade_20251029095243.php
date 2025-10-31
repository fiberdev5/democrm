@extends('frontend.secure.user_master')
@section('user')

  <!--<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>-->
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <style>
    .searchWrap {
      visibility: hidden;
      opacity: 0;
    }

    .card-invocies {
      border: 1px solid rgba(0, 0, 0, .125) !important;
    }

    .card-invocies-header {
      background-color: #f7f7f7 !important;
      border-bottom: 1px solid rgba(0, 0, 0, .125) !important;
      margin-bottom: 7px !important;
      padding: 4px 7px !important;
    }

    .card-invocies-body {
      padding: 3px 7px !important;
    }

    @media (max-width: 767px) {
      .pageDetail .searchWrap {}

      .btn-secondary {
        color: #fff !important;
        background-color: #5c636a !important;
        border-color: #565e64 !important;
      }

      .pageDetail .searchWrap .dropdown-menu .item {
        margin-bottom: 10px !important;
      }

      .invoices-filtre {
        width: auto !important;
      }

      .pageDetail .searchWrap .dropdown-menu {
        min-width: calc(90vw - 20px) !important;
        transform: translate3d(9.274994px, 2px, 0px) !important;
      }

      .searchWrap .tarih-araligi {
        padding: 5px 3px;
      }

      .custom-p-m-m {
        padding-left: 0px !important;
      }

      #datatableIntegration_filter {
        margin-right: 0px !important;
      }

      .dataTables_filter label {}

      .searchWrap {
        margin-top: 0px !important;
      }

      div.dataTables_filter input {
        margin-left: 0px !important;
      }

      .top {
        margin-top: 19px;
      }

      div.dataTables_wrapper div.dataTables_filter input {
        width: 227px !important;
      }

      .servisDrop {
        inset: 0px 0px -412px -213px !important;
      }

      .pageDetail .searchWrap {
        margin-bottom: 0px !important;
      }

    }
  </style>
  <div class="page-content" id="passwords">
    <div class="container-fluid">
      <div class="row pageDetail">
        <div class="col-12">
          <div class="card card-invocies">
            <div class="card-header card-invocies-header sayfaBaslik">
              Entegrasyonlar
            </div>
            <div class="card-body card-invocies-body">
              <table id="datatableIntegration" class="table table-bordered dt-responsive nowrap"
                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                <a class="btn btn-success btn-sm addIntegration" data-bs-toggle="modal" data-bs-target="#addIntegrationModal"><i
                    class="fas fa-plus"></i><span>Entegrasyon Ekle</span></a>
                <div class="searchWrap float-end">
                  <div class="btn-group">
                    <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown"
                      aria-expanded="false">
                      Filtrele <i class="mdi mdi-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu">
                      <div class="item">
                        <div class="row form-group">
                          <label class="col-sm-4 col-4 custom-p-m-m">Kategoriler<span
                              style="font-weight: bold; color: red;">*</span></label>
                          <div class="col-md-8 col-8 custom-p-m-m">
                            <select name="category" class="form-select" required>
                              <option value="" selected disabled>-Seçiniz-</option>
                              <option value="invoice">Fatura</option>
                              <option value="sms">SMS</option>
                              <option value="accounting">Muhasebe</option>
                              <option value="other">Diğer</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="item">
                        <div class="row">
                          <div class="col-md-4 rw1"><label>Durum</label></div>
                          <div class="col-md-8 rw2">
                            <select class="form-select" name="entegreDurumu">
                              <option value="">Hepsi</option>
                              <option value="1">Aktif</option>
                              <option value="0">Pasif</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="item">
                        <div class="row">
                          <label class="col-sm-4 col-4 custom-p-m-m">Tarih Aralığı:</label>
                          <div class="col-sm-8 col-8 custom-p-m-m">
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
                    <th>Entegrasyon Adı</th>
                    <th style="width: 250px">Kategori</th>
                    <th>Fiyat</th>
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
  <div id="addIntegrationModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 830px;">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title" id="myModalLabel">Entegrasyon Ekle</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Yükleniyor...
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

  <!-- edit modal content -->
  <div id="editIntegrationModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 830px;">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title" id="myModalLabel">Entegrasyon Düzenle</h6>
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
        $(".addIntegration").click(function () {
          $.ajax({
            url: "{{ route('super.admin.integration.add') }}"
          }).done(function (data) {
            if ($.trim(data) === "-1") {
              window.location.reload(true);
            } else {
              $('#addIntegrationModal').modal('show');
              $('#addIntegrationModal .modal-body').html(data);
            }
          });
        });
        $("#addIntegrationModal").on("hidden.bs.modal", function () {
          $('#addIntegrationModal .modal-body').html("");
        });
      });
    </script>

    <script type="text/javascript">
      $(document).ready(function () {
        $('#datatableIntegration').on('click', '.editIntegration', function (e) {
          var id = $(this).attr("data-bs-id");
          $.ajax({
            url: "{{ route('super.admin.integration.edit', '') }}/" + id
          }).done(function (data) {
            if ($.trim(data) === "-1") {
              window.location.reload(true);
            } else {
              $('#editIntegrationModal').modal('show');
              $('#editIntegrationModal .modal-body').html(data);
            }
          });
        });
        $("#editIntegrationModal").on("hidden.bs.modal", function () {
          $('#editIntegrationModal .modal-body').html("");
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
        $('#lastYear').on('click', function () {
          $('#daterange').data('daterangepicker').setStartDate(lastYear);
          $('#daterange').data('daterangepicker').setEndDate(today);
          filterData();
        });

        $('#lastMonth').on('click', function () {
          $('#daterange').data('daterangepicker').setStartDate(lastMonth);
          $('#daterange').data('daterangepicker').setEndDate(today);
          filterData();
        });

        $('#lastWeek').on('click', function () {
          $('#daterange').data('daterangepicker').setStartDate(lastWeek);
          $('#daterange').data('daterangepicker').setEndDate(today);
          filterData();
        });

        $('#yesterday').on('click', function () {
          $('#daterange').data('daterangepicker').setStartDate(yesterday);
          $('#daterange').data('daterangepicker').setEndDate(yesterday);
          filterData();
        });

        $('#today').on('click', function () {
          $('#daterange').data('daterangepicker').setStartDate(today);
          $('#daterange').data('daterangepicker').setEndDate(today);
          filterData();
        });

        // Filtreleme fonksiyonu
        function filterData() {
          $('#datatableIntegration').DataTable().draw();
        }
      });
    </script>

    <script>
      $(document).ready(function () {

        var start_date = '01-01-2025';
        var end_date = moment().add(1, 'day');

        $('#daterange').daterangepicker({
          startDate: start_date,
          endDate: end_date,
          opens: 'right',
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
            $('#daterange').html(start_date.format('DD-MM-YYYY') + '-' + end_date.format('DD-MM-YYYY'));
            table.draw();
          });

        var table = $('#datatableIntegration').DataTable({
          processing: true,
          serverSide: true,
          language: {
            paginate: {
              previous: "<i class='mdi mdi-chevron-left'>",
              next: "<i class='mdi mdi-chevron-right'>"
            }
          },
          ajax: {
            url: "{{ route('super.admin.integrations') }}",
            data: function (data) {
              data.search = $('input[type="search"]').val();
              data.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
              data.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
              data.firma = $('#firma').val();
              //data.durum = $('#durum').val();
            }
          },
          'columns': [
            { data: 'id' },
            { data: 'created_at' },
            { data: 'name' },
            { data: 'category' },
            { data: 'price' },
            { data: 'actions' }
          ],

          drawCallback: function () {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
          },
          "order": [[1, 'desc']],
          "columnDefs": [
            {
              "targets": 0,
              "className": "gizli"
            }
          ],
          "oLanguage": {
            "sDecimal": ",",
            "sEmptyTable": "Tabloda herhangi bir veri mevcut değil",
            "sInfo": "Entegrasyon Sayısı: _TOTAL_",
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
          dom: '<"top"f>rt<"bottom"i<"float-end invoices-filtre"lp>><"clear">',
          "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "Tümü"]],
          "initComplete": function (settings, json) {
            var searchContainer = $('#datatableIntegration_filter');
            var searchInput = searchContainer.find('input');
            var filterWrapper = $('.searchWrap');
            var flexContainer = $('<div class="d-flex justify-content-end w-100"></div>');
            searchContainer.find('label').contents().filter(function () {
              return this.nodeType == 3;
            }).remove();
            searchContainer.addClass('flex-grow-1 me-2');
            searchInput.addClass('w-100');
            searchInput.attr('placeholder', 'Entegrasyon Ara...');
            flexContainer.append(searchContainer);
            flexContainer.append(filterWrapper);
            $('#datatableIntegration_wrapper .top').append(flexContainer);
            $('.searchWrap').css({ visibility: 'visible', opacity: 1 });

          }


        });

        $('#firma').change(function () {
          table.draw();
        });

        $('#durum').change(function () {
          table.draw();
        });

        table.on('draw.dt', function () {
          updateValues();
        });

        var updateValues = function () {
          var startDate = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
          var endDate = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
          var firma = $('#firma').val();
          var durum = $('#durum').val();

          $.ajax({
            url: '{{ route("super.admin.invoices.get") }}',
            method: 'GET',
            data: {
              from_date: startDate,
              to_date: endDate,
              firma: firma,
              durum: durum,
            },

            success: function (response) {

              $('.toplamNakitTL').html('<span>Nakit:</span> ' + response.toplamNakitTL);
              $('.toplamHavaleTL').html('<span>EFT/Havale:</span> ' + response.toplamHavaleTL);
              $('.toplamKartTL ').html('<span>Kredi Kartı:</span> ' + response.toplamKartTL);
              $('.toplamTutarTL1 ').html('<span>Toplam:</span> ' + response.toplamTutarTL1);
              $('.kdvNakitTL ').html('<span>Nakit:</span> ' + response.kdvNakitTL);
              $('.kdvHavaleTL').html('<span>EFT/Havale:</span> ' + response.kdvHavaleTL);
              $('.kdvKartTL ').html('<span>Kredi Kartı:</span> ' + response.kdvKartTL);
              $('.toplamTutarTL2 ').html('<span>Toplam:</span> ' + response.toplamTutarTL2);
              $('.genelNakitTL ').html('<span>Nakit:</span> ' + response.genelNakitTL);
              $('.genelHavaleTL ').html('<span>EFT/Havale:</span> ' + response.genelHavaleTL);
              $('.genelKartTL').html('<span>Kredi Kartı:</span> ' + response.genelKartTL);
              $('.toplamTutarTL3 ').html('<span>Toplam:</span> ' + response.toplamTutarTL3);

            },
            error: function (xhr, status, error) {
              console.error(error);
            }
          });
        };
        // Sayfa yüklendiğinde updateValues fonksiyonunu çağır
        updateValues();

        // DataTables çizeceği zaman updateValues fonksiyonunu çağır
        table.on('draw.dt', function () {
          updateValues();
        });

        // Date range picker değiştiğinde updateValues fonksiyonunu çağır
        $('#daterange').on('apply.daterangepicker', function (ev, picker) {
          updateValues();
        });
      });
    </script>
@endsection