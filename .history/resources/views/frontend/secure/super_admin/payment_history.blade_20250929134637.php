@extends('frontend.secure.user_master')
@section('user')

<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<div class="page-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card" style="margin-bottom: 0!important;">
          <div class="card-header" style="padding: 5px!important;">
            <h3 class="card-title">
              Tüm Müşterilerin Ödeme Geçmişi
            </h3>
          </div>

          <div class="card-body">
            <!-- Ödeme Tablosu -->
            <table id="datatablePayments" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
              <div class="searchWrap float-end">
                <div class="btn-group mb-2">
                  <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Filtrele <i class="mdi mdi-chevron-down"></i>
                  </button>
                  <div class="dropdown-menu">
                    <!-- Firma -->
                    <div class="item">
                      <div class="row form-group">
                        <label class="col-sm-4 form-label fw-bold" style="font-size: 12px;">Müşteri</label>
                        <div class="col-md-8">
                          <select class="form-control form-control-sm" id="tenant_id" name="tenant_id">
                            <option value="">Tüm Müşteriler</option>
                            @foreach($tenants as $tenant)
                              <option value="{{ $tenant->id }}">{{ $tenant->firma_adi }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>

                    <!-- Tür -->
                    <div class="item">
                      <div class="row form-group">
                        <label class="col-sm-4 form-label fw-bold" style="font-size: 12px;">Tür</label>
                        <div class="col-md-8">
                          <select class="form-control form-control-sm" id="type" name="type">
                            <option value="all">Tümü</option>
                            <option value="subscription">Abonelik</option>
                            <option value="storage">Depolama</option>
                          </select>
                        </div>
                      </div>
                    </div>

                    <!-- Tarih Aralığı -->
                    <div class="item">
                      <div class="row">
                        <label class="col-sm-4">Tarih Aralığı:</label>
                        <div class="col-sm-8">
                          <input id="daterange" class="tarih-araligi form-control form-control-sm">
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

                    <!-- Butonlar -->
                    <div class="item text-end">
                      <a href="#" class="btn btn-success btn-sm" id="excel-export" title="Excel İndir">
                        <i class="fas fa-file-excel"></i> Excel
                      </a>
                    </div>
                  </div>
                </div>
              </div>

              <thead class="thead-dark title">
                <tr class="text-center">
                  <th style="width: 50px;">#</th>
                  <th style="width: 150px;">Müşteri</th>
                  <th style="width: 100px;">Tür</th>
                  <th>Açıklama</th>
                  <th style="width: 120px;">Tutar</th>
                  <th style="width: 100px;">Durum</th>
                  <th style="width: 120px;">Tarih</th>
                  <th style="width: 100px;">Fatura</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>

            <!-- Toplam Alanı -->
            <div class="tableToplamaAlani">
              <div class="row r2">
                <div class="sol"><strong>Abonelik Ödemeleri</strong></div>
                <div class="sag">
                  <div class="tur t1 subscription_completed"><span>Tamamlanan:</span></div>
                  <div class="tur t2 subscription_pending"><span>Bekleyen:</span></div>
                  <div class="tur t3 subscription_failed"><span>Başarısız:</span></div>
                  <div class="tur t4 subscription_total"><span>Toplam:</span></div>
                </div>
              </div>

              <div class="row r3">
                <div class="sol"><strong>Depolama Ödemeleri</strong></div>
                <div class="sag">
                  <div class="tur t1 storage_completed"><span>Tamamlanan:</span></div>
                  <div class="tur t2 storage_pending"><span>Bekleyen:</span></div>
                  <div class="tur t3 storage_failed"><span>Başarısız:</span></div>
                  <div class="tur t4 storage_total"><span>Toplam:</span></div>
                </div>
              </div>

              <div class="row r4">
                <div class="sol"><strong>Genel Toplam</strong></div>
                <div class="sag">
                  <div class="tur t1 general_completed"><span>Tamamlanan:</span></div>
                  <div class="tur t2 general_pending"><span>Bekleyen:</span></div>
                  <div class="tur t3 general_failed"><span>Başarısız:</span></div>
                  <div class="tur t4 general_total"><span>Toplam:</span></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.text-transparent {
    color: transparent !important;
}

.table th {
    border-top: none;
    font-weight: 600;
}

.table-striped>tbody>tr td {
    text-align: center!important;
}

.searchWrap {
    float: right;
}

/* Toplama Alanı Stilleri */
.tableToplamaAlani {
    margin-bottom: 0;
    clear: both;
    font-size: 13px;
}

.tableToplamaAlani .row {
    margin: 1px;
    color: #fff;
    font-weight: 700;
}

.tableToplamaAlani .row .sol {
    text-align: right;
    padding: 36px 15px;
    border: 1px solid #e0e0e0;
    float: left;
    background: #eff2f7;
    color: #000;
    width: calc(100% - 230px);
}

.tableToplamaAlani .row .sag {
    padding: 5px 15px;
    float: right;
    width: 230px;
    text-align: left;
}

.tableToplamaAlani .row .sag .tur {
    margin: 2px 0;
}

.tableToplamaAlani .row .sag .tur span {
    width: 97px;
    display: inline-block;
    text-align: left;
}

.tableToplamaAlani .row .sag .t4 {
    font-weight: bolder;
}

.tableToplamaAlani .r2 {
    background: #238c3b;
}

.tableToplamaAlani .r2 .sol {
    border-right: 1px solid #238c3b;
}

.tableToplamaAlani .r3 {
    background: #b92d3b;
}

.tableToplamaAlani .r3 .sol {
    border-right: 1px solid #b92d3b;
}

.tableToplamaAlani .r4 {
    background: #343a40;
}

.tableToplamaAlani .r4 .sol {
    padding: 5px 15px;
    border-right: 1px solid #1e2225;
}

.pagination {
    margin: 0;
}

.dataTables_info {
    font-size: 14px;
    color: #495057;
}
</style>

<script>
$(document).ready(function () {
    var start_date = moment().subtract(1, 'month');
    var end_date = moment();

    // Date Range Picker
    $('#daterange').daterangepicker({
        startDate: start_date,
        endDate: end_date,
        opens: 'left',
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
    }, function(start_date, end_date){
        table.draw();
    });

    // DataTable
    var table = $('#datatablePayments').DataTable({
        processing: true,
        serverSide: true,
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            }
        },
        ajax: {
            url: "{{ route('super.admin.payment.history.index') }}",
            data: function(data) {
                data.search = $('input[type="search"]').val();
                data.date_from = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                data.date_to = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
                data.tenant_id = $('#tenant_id').val();
                data.type = $('#type').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'tenant_name', name: 'tenant_name' },
            { data: 'type_label', name: 'type_label' },
            { data: 'description', name: 'description' },
            { data: 'amount', name: 'amount' },
            { data: 'status_label', name: 'status_label' },
            { data: 'created_at', name: 'created_at' },
            { data: 'invoice_status', name: 'invoice_status' }
        ],
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        },
        order: [[6, 'desc']],
        columnDefs: [
            {
                targets: 0,
                className: "gizli"
            }
        ],
        oLanguage: {
            sDecimal: ",",
            sEmptyTable: "Tabloda herhangi bir veri mevcut değil",
            sInfo: "Ödeme Sayısı: _TOTAL_",
            sInfoEmpty: "Kayıt yok",
            sInfoFiltered: "",
            sInfoPostFix: "",
            sInfoThousands: ".",
            sLengthMenu: "_MENU_",
            sLoadingRecords: "Yükleniyor...",
            sProcessing: "İşleniyor...",
            sSearch: "Ara:",
            sZeroRecords: "Eşleşen kayıt bulunamadı",
            oPaginate: {
                sFirst: "İlk",
                sLast: "Son",
                sNext: '<i class="fas fa-angle-double-right"></i>',
                sPrevious: '<i class="fas fa-angle-double-left"></i>'
            },
            oAria: {
                sSortAscending: ": artan sütun sıralamasını aktifleştir",
                sSortDescending: ": azalan sütun sıralamasını aktifleştir"
            },
            select: {
                rows: {
                    "_": "%d kayıt seçildi",
                    "0": "",
                    "1": "1 kayıt seçildi"
                }
            }
        },
        dom: '<"top"f>rt<"bottom"i<"float-end"lp>><"clear">',
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Tümü"]]
    });

    // Filtre değişikliklerinde tabloyu yenile
    $('#tenant_id, #type').change(function(){
        table.draw();        
    });

    // Hızlı tarih filtreleri
    $('#lastYear').on('click', function() {
        var lastYear = moment().subtract(1, 'year');
        var today = moment();
        $('#daterange').data('daterangepicker').setStartDate(lastYear);
        $('#daterange').data('daterangepicker').setEndDate(today);
        table.draw();
    });

    $('#lastMonth').on('click', function() {
        var lastMonth = moment().subtract(1, 'month');
        var today = moment();
        $('#daterange').data('daterangepicker').setStartDate(lastMonth);
        $('#daterange').data('daterangepicker').setEndDate(today);
        table.draw();
    });

    $('#lastWeek').on('click', function() {
        var lastWeek = moment().subtract(7, 'days');
        var today = moment();
        $('#daterange').data('daterangepicker').setStartDate(lastWeek);
        $('#daterange').data('daterangepicker').setEndDate(today);
        table.draw();
    });

    $('#yesterday').on('click', function() {
        var yesterday = moment().subtract(1, 'days');
        $('#daterange').data('daterangepicker').setStartDate(yesterday);
        $('#daterange').data('daterangepicker').setEndDate(yesterday);
        table.draw();
    });

    $('#today').on('click', function() {
        var today = moment();
        $('#daterange').data('daterangepicker').setStartDate(today);
        $('#daterange').data('daterangepicker').setEndDate(today);
        table.draw();
    });

    // Excel export
    $('#excel-export').on('click', function(e) {
        e.preventDefault();
        var startDate = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var endDate = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
        var tenantId = $('#tenant_id').val();
        var type = $('#type').val();
        
        var exportUrl = '{{ route("super.admin.payment.history.export") }}' + 
            '?date_from=' + startDate + 
            '&date_to=' + endDate +
            '&tenant_id=' + tenantId +
            '&type=' + type;
        
        window.open(exportUrl, '_blank');
    });

    // Toplam değerleri güncelleme fonksiyonu
    var updateTotals = function() {
        var startDate = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var endDate = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
        var tenantId = $('#tenant_id').val();
        var type = $('#type').val();

        $.ajax({
            url: '{{ route("super.admin.payment.history.totals") }}',
            method: 'GET',
            data: {
                date_from: startDate,
                date_to: endDate,
                tenant_id: tenantId,
                type: type
            },
            success: function(response) {
                $('.subscription_completed').html('<span>Tamamlanan:</span> ' + response.subscription_completed);
                $('.subscription_pending').html('<span>Bekleyen:</span> ' + response.subscription_pending);
                $('.subscription_failed').html('<span>Başarısız:</span> ' + response.subscription_failed);
                $('.subscription_total').html('<span>Toplam:</span> ' + response.subscription_total);
                
                $('.storage_completed').html('<span>Tamamlanan:</span> ' + response.storage_completed);
                $('.storage_pending').html('<span>Bekleyen:</span> ' + response.storage_pending);
                $('.storage_failed').html('<span>Başarısız:</span> ' + response.storage_failed);
                $('.storage_total').html('<span>Toplam:</span> ' + response.storage_total);
                
                $('.general_completed').html('<span>Tamamlanan:</span> ' + response.completed);
                $('.general_pending').html('<span>Bekleyen:</span> ' + response.pending);
                $('.general_failed').html('<span>Başarısız:</span> ' + response.failed);
                $('.general_total').html('<span>Toplam:</span> ' + response.total);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    };

    // Sayfa yüklendiğinde ve tablo her çizildiğinde toplamları güncelle
    updateTotals();
    table.on('draw.dt', function () {
        updateTotals();
    });

    // Date range değiştiğinde toplamları güncelle
    $('#daterange').on('apply.daterangepicker', function(ev, picker) {
        updateTotals();
    });
});
</script>
@endsection