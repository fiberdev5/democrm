@extends('frontend.secure.user_master')
@section('user')

<div class="page-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card" style="margin-bottom: 0!important;">
          <div class="card-header" style="padding: 5px!important;">
            <h3 class="card-title">
              Ödeme Geçmişi
            </h3>
          </div>

          <div class="card-body">
            <!-- Filtreleme Formu -->
            <div class="row mb-3">
              <div class="col-12">
                <div class="card shadow-sm" style="margin-bottom: 0!important;">           
                  <div class="card-body">
                    <div class="row align-items-end">
                      <!-- Başlangıç -->
                      <div class="col-lg-2 col-md-3 col-sm-6">
                        <div class="form-group mb-3">
                          <label for="date_from" class="form-label fw-bold" style="font-size: 12px;">Başlangıç</label>
                          <input type="date" class="form-control form-control-sm" id="date_from" name="date_from">
                        </div>
                      </div>

                      <!-- Bitiş -->
                      <div class="col-lg-2 col-md-3 col-sm-6">
                        <div class="form-group mb-3">
                          <label for="date_to" class="form-label fw-bold" style="font-size: 12px;">Bitiş</label>
                          <input type="date" class="form-control form-control-sm" id="date_to" name="date_to">
                        </div>
                      </div>

                      <!-- Tür -->
                      <div class="col-lg-2 col-md-3 col-sm-6">
                        <div class="form-group mb-3">
                          <label for="type" class="form-label fw-bold" style="font-size: 12px;">Tür</label>
                          <select class="form-control form-control-sm select-with-arrow" id="type" name="type">
                            <option value="all">Tümü</option>
                            <option value="subscription">Abonelik</option>
                            <option value="storage">Depolama</option>
                          </select>
                        </div>
                      </div>

                      <!-- İşlem Butonları -->
                      <div class="col-auto">
                        <div class="form-group mb-3">
                          <label class="form-label fw-bold text-transparent" style="font-size: 12px;">İşlemler</label>
                          <div class="d-flex gap-1">
                            <button type="button" class="btn btn-outline-secondary btn-sm action-btn" id="clear-filter" title="Temizle">
                              <i class="fas fa-eraser"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm action-btn" id="excel-export" title="Excel İndir">
                              <i class="fas fa-file-excel"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                      <!-- Hızlı Tarih Filtreleri -->
                      <div class="col-12 col-lg-auto">
                        <div class="form-group mb-3">
                          <label class="form-label fw-bold text-transparent" style="font-size: 12px;">Hızlı</label>
                          <div class="btn-group btn-group-sm d-flex flex-nowrap" role="group" style="overflow-x: auto; flex-wrap: nowrap;">
                            <button type="button" class="btn btn-outline-light text-dark quick-filter" data-days="7">7 Gün</button>
                            <button type="button" class="btn btn-outline-light text-dark quick-filter" data-days="30">30 Gün</button>
                            <button type="button" class="btn btn-outline-light text-dark quick-filter" id="this-month">Bu Ay</button>
                            <button type="button" class="btn btn-outline-light text-dark quick-filter" data-days="90">3 Ay</button>
                            <button type="button" class="btn btn-outline-light text-dark quick-filter" data-days="365">1 Yıl</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Ödeme Tablosu -->
            <table id="datatablePayments" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
              <thead class="title">
                <tr>
                  <th style="width: 50px;">ID</th>
                  <th style="width: 150px;">Tür</th>
                  <th>Açıklama</th>
                  <th style="width: 150px;">Tutar</th>
                  <th style="width: 150px;">Durum</th>
                  <th style="width: 150px;">Tarih</th>
                  <th style="width: 150px;">Fatura</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* Temizle ve Excel butonları */
.action-btn {
    padding: 0.375rem 0.75rem !important;
    min-width: 38px;
}

.action-btn:hover {
    background-color: #6c757d !important;
    color: #fff !important;
}

.text-transparent {
    color: transparent !important;
}

/* Hızlı filtre butonları */
.quick-filter, #this-month {
    transition: all 0.2s;
    font-size: 11px;
    padding: 0.25rem 0.5rem;
    white-space: nowrap;
}

.quick-filter:hover, #this-month:hover {
    background-color: transparent !important;
    border: 1px solid #dee2e6 !important;
    color: #495057 !important;
    transform: none !important;
}

.pagination {
    margin: 0;
}

.dataTables_info {
    font-size: 14px;
    color: #495057;
}

/* Tablo link stilleri */
.table > tbody > tr > td > .t-link {
    text-decoration: none !important;
    display: block;
    padding: 15px 5px;
    margin-top: -10px;
    margin-bottom: -10px;
    color: #505d69;
    font-size: 13px;
}

/* Fatura sütununu sola hizala */
#datatablePayments td:nth-child(7),
#datatablePayments th:nth-child(7) {
    text-align: left !important;
}

/* Select ok işareti */
.select-with-arrow {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.5rem center;
    background-size: 12px;
    padding-right: 2rem !important;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
}
</style>

<script>
$(document).ready(function() {
    const today = new Date().toISOString().split('T')[0];
    const lastMonth = new Date();
    lastMonth.setMonth(lastMonth.getMonth() - 1);
    
    // Varsayılan tarihler
    $('#date_from').val(lastMonth.toISOString().split('T')[0]);
    $('#date_to').val(today);
    $('#date_from, #date_to').attr('max', today);
    
    $('#date_from').on('change', function() {
        $('#date_to').attr('min', $(this).val());
    });
    
    $('#date_to').on('change', function() {
        $('#date_from').attr('max', $(this).val());
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
            url: "{{ route('payment-history.index', $tenant->id) }}",
            data: function(data) {
                data.search = $('input[type="search"]').val();
                data.date_from = $('#date_from').val();
                data.date_to = $('#date_to').val();
                data.type = $('#type').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'type_label', name: 'type_label' },
            { data: 'description', name: 'description' },
            { data: 'amount', name: 'amount' },
            { data: 'status_label', name: 'status_label' },
            { data: 'created_at', name: 'created_at' },
            { 
                data: 'invoice_status', 
                name: 'invoice_status',
                orderable: false,
                searchable: false
            }
        ],
        columnDefs: [
            {
                targets: 6, // Fatura sütunu
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html(cellData);
                }
            }
        ],
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        },
        order: [[5, 'desc']],
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
        dom: 'rt<"bottom"i<"float-end"lp>><"clear">',
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Tümü"]]
    });

    // Filtre değişikliklerinde tabloyu yenile
    $('#type, #date_from, #date_to').change(function(){
        table.draw();        
    });

    // Hızlı tarih filtreleri
    $('.quick-filter').on('click', function(e) {
        e.preventDefault();
        const days = parseInt($(this).data('days'));
        if (days) {
            const today = new Date();
            const startDate = new Date();
            startDate.setDate(today.getDate() - days);
            
            $('#date_from').val(startDate.toISOString().split('T')[0]);
            $('#date_to').val(today.toISOString().split('T')[0]);
        }
        table.draw();
    });

    $('#this-month').on('click', function(e) {
        e.preventDefault();
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        
        $('#date_from').val(firstDay.toISOString().split('T')[0]);
        $('#date_to').val(today.toISOString().split('T')[0]);
        
        table.draw();
    });

    $('#clear-filter').on('click', function(e) {
        e.preventDefault();
        $('#type').val('all');
        const today = new Date().toISOString().split('T')[0];
        const lastMonth = new Date();
        lastMonth.setMonth(lastMonth.getMonth() - 1);
        $('#date_from').val(lastMonth.toISOString().split('T')[0]);
        $('#date_to').val(today);
        table.draw();
    });

    $('#excel-export').on('click', function(e) {
        e.preventDefault();
        var dateFrom = $('#date_from').val();
        var dateTo = $('#date_to').val();
        var type = $('#type').val();
        
        var exportUrl = '{{ route("payment-history.export", $tenant->id) }}' + 
            '?date_from=' + dateFrom + 
            '&date_to=' + dateTo +
            '&type=' + type;
        
        window.open(exportUrl, '_blank');
    });
});
</script>
@endsection