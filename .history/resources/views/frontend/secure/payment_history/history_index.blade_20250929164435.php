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
                    <form method="GET" action="{{ route('payment-history.index',$tenant->id) }}" id="filter-form">
                      <div class="row align-items-end">
                        <!-- Başlangıç -->
                        <div class="col-lg-2 col-md-3 col-sm-6">
                          <div class="form-group mb-3">
                            <label for="date_from" class="form-label fw-bold" style="font-size: 12px;">Başlangıç</label>
                            <input type="date" class="form-control form-control-sm" id="date_from" name="date_from" value="{{ $dateFrom }}">
                          </div>
                        </div>

                        <!-- Bitiş -->
                        <div class="col-lg-2 col-md-3 col-sm-6">
                          <div class="form-group mb-3">
                            <label for="date_to" class="form-label fw-bold" style="font-size: 12px;">Bitiş</label>
                            <input type="date" class="form-control form-control-sm" id="date_to" name="date_to" value="{{ $dateTo }}">
                          </div>
                        </div>

                        <!-- Tür -->
                        <div class="col-lg-2 col-md-3 col-sm-6">
                          <div class="form-group mb-3">
                            <label for="type" class="form-label fw-bold" style="font-size: 12px;">Tür</label>
                            <select class="form-control form-control-sm select-with-arrow" id="type" name="type">
                              <option value="all" {{ $type === 'all' || !$type ? 'selected' : '' }}>Tümü</option>
                              <option value="subscription" {{ $type === 'subscription' ? 'selected' : '' }}>Abonelik</option>
                              <option value="storage" {{ $type === 'storage' ? 'selected' : '' }}>Depolama</option>
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
                    </form>
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
                @foreach($pagination as $index => $payment)
                  <tr>
                    <td class="text-center">{{ $pagination->firstItem() + $index }}</td>
                    <td class="text-center">
                      <strong>{{ $payment['type_label'] }}</strong>
                    </td>
                    <td style="padding-left:5px;">
                      <strong>
                        @php
                          $description = $payment['description'];
                          $description = preg_replace('/\(Abonelik ID:\s*\d+\)/i', '', $description);
                          $description = preg_replace('/via\s+paytr/i', '', $description);
                          $description = preg_replace('/via\s+paypal/i', '', $description);
                          $description = preg_replace('/\s+/', ' ', trim($description));
                        @endphp
                        {{ $description }}
                      </strong>
                    </td>
                    <td class="text-center">
                      <strong>
                        {{ number_format($payment['amount'], 2) }} 
                        {{ isset($payment['currency']) ? strtoupper($payment['currency']) : '₺' }}
                      </strong>
                    </td>
                    <td class="text-center">
                      @php
                        $statusClass = match($payment['status']) {
                          'active', 'completed' => 'success',
                          'pending' => 'warning',
                          'cancelled' => 'danger',
                          'expired' => 'secondary',
                          default => 'dark'
                        };
                      @endphp
                      <span class="badge badge-{{ $statusClass }}">
                        {{ $payment['status_label'] }}
                      </span>
                    </td>
                    <td class="text-center">
                      <strong>{{ $payment['created_at']->format('d.m.Y') }}</strong>
                    </td>
                    <td class="text-center">
                      @if($payment['invoice_path'])
                        <span class="badge badge-success">
                          <i class="fas fa-check mr-1"></i>
                          Mevcut
                        </span>
                      @else
                        <span class="badge badge-warning">
                          <i class="fas fa-clock mr-1"></i>
                          Bekleniyor
                        </span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>

            <!-- Sayfalama -->
            <div class="row mt-3">
              <div class="col-sm-12 col-md-5">
                <div class="dataTables_info">
                  Ödeme Sayısı: {{ $pagination->total() }}
                </div>
              </div>
              <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate float-end">
                  {{ $pagination->appends(request()->query())->links() }}
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
    padding-top: 0.5rem;
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

.table th {
    border-top: none;
    font-weight: 600;
}
</style>

<script>
$(document).ready(function() {
    const today = new Date().toISOString().split('T')[0];
    const lastMonth = new Date();
    lastMonth.setMonth(lastMonth.getMonth() - 1);
    
    // Tarihleri bugünden fazla olmaması için kontrol et
    $('#date_from, #date_to').attr('max', today);
    
    $('#date_from').on('change', function() {
        $('#date_to').attr('min', $(this).val());
    });
    
    $('#date_to').on('change', function() {
        $('#date_from').attr('max', $(this).val());
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
        $('#filter-form').submit();
    });

    $('#this-month').on('click', function(e) {
        e.preventDefault();
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        
        $('#date_from').val(firstDay.toISOString().split('T')[0]);
        $('#date_to').val(today.toISOString().split('T')[0]);
        
        $('#filter-form').submit();
    });

    $('#clear-filter').on('click', function(e) {
        e.preventDefault();
        $('#type').val('all');
        const today = new Date().toISOString().split('T')[0];
        const lastMonth = new Date();
        lastMonth.setMonth(lastMonth.getMonth() - 1);
        $('#date_from').val(lastMonth.toISOString().split('T')[0]);
        $('#date_to').val(today);
        $('#filter-form').submit();
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

    // Filtre değişikliklerinde formu submit et
    $('#type').change(function(){
        $('#filter-form').submit();
    });
});
</script>
@endsection