@extends('frontend.secure.user_master')
@section('user')

<div class="page-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card" style="margin-bottom: 0!important;">
          <div class="card-header" style="padding: 5px!important;">
            <h3 class="card-title">
              Tüm Firmaların Ödeme Geçmişi
            </h3>
          </div>

          <div class="card-body">
            <!-- Filtreleme Formu -->
            <div class="row mb-3">
              <div class="col-12">
                <div class="card shadow-sm" style="margin-bottom: 0!important;">           
                  <div class="card-body">
                    <form method="GET" action="{{ route('super.admin.payment.history.index') }}" id="filter-form">
                      <div class="row">
                        <!-- Firma Seçimi -->
                        <div class="col-lg-2 col-md-4 col-sm-6">
                          <div class="form-group mb-3">
                            <label for="tenant_id" class="form-label fw-bold">Firma</label>
                            <select class="form-control" id="tenant_id" name="tenant_id">
                              <option value="">Tüm Firmalar</option>
                              @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}" {{ $tenantId == $tenant->id ? 'selected' : '' }}>
                                  {{ $tenant->firma_adi }}
                                </option>
                              @endforeach
                            </select>
                          </div>
                        </div>

                        <!-- Başlangıç Tarihi -->
                        <div class="col-lg-2 col-md-4 col-sm-6">
                          <div class="form-group mb-3">
                            <label for="date_from" class="form-label fw-bold">Başlangıç Tarihi</label>
                            <input type="date" 
                              class="form-control" 
                              id="date_from" 
                              name="date_from" 
                              value="{{ $dateFrom }}"
                              style="width: 100%!important;">
                          </div>
                        </div>

                        <!-- Bitiş Tarihi -->
                        <div class="col-lg-2 col-md-4 col-sm-6">
                          <div class="form-group mb-3">
                            <label for="date_to" class="form-label fw-bold">Bitiş Tarihi</label>
                            <input type="date" 
                              class="form-control" 
                              id="date_to" 
                              name="date_to" 
                              value="{{ $dateTo }}"
                              style="width: 100%!important;">                        
                          </div>
                        </div>

                        <!-- Ödeme Türü -->
                        <div class="col-lg-2 col-md-4 col-sm-6">
                          <div class="form-group mb-3">
                            <label for="type" class="form-label fw-bold">Ödeme Türü</label>
                            <select class="form-control" id="type" name="type">
                              <option value="all" {{ $type === 'all' || !$type ? 'selected' : '' }}>Tüm Ödemeler</option>
                              <option value="subscription" {{ $type === 'subscription' ? 'selected' : '' }}>Abonelik</option>
                              <option value="storage" {{ $type === 'storage' ? 'selected' : '' }}>Depolama</option>
                            </select>
                          </div>
                        </div>

                        <!-- Durum Filtresi -->
                        <div class="col-lg-2 col-md-4 col-sm-6">
                          <div class="form-group mb-3">
                            <label for="status" class="form-label fw-bold">Durum</label>
                            <select class="form-control" id="status" name="status">
                              <option value="">Tümü</option>
                              @foreach($statuses as $statusKey => $statusLabel)
                                <option value="{{ $statusKey }}" {{ $status === $statusKey ? 'selected' : '' }}>
                                  {{ $statusLabel }}
                                </option>
                              @endforeach
                            </select>
                          </div>
                        </div>

                        <!-- İşlem Butonları -->
                        <div class="col-lg-2 col-md-12">
                          <div class="form-group mb-3">
                            <label class="form-label fw-bold text-transparent">İşlemler</label>
                            <div class="d-flex gap-1">
                              <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i>
                              </button>
                              <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-filter">
                                <i class="fas fa-eraser"></i>
                              </button>
                              <a href="#" class="btn btn-success btn-sm" id="excel-export">
                                <i class="fas fa-file-excel"></i>
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Hızlı Tarih Filtreleri -->
                      <div class="row">
                        <div class="col-12">
                          <div class="d-flex flex-wrap gap-2">
                            <span class="text-muted fw-bold mr-2">
                              <i class="fas fa-clock"></i> Hızlı Filtreler:
                            </span>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-filter" data-days="7">Son 7 Gün</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-filter" data-days="30">Son 30 Gün</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-filter" data-days="90">Son 3 Ay</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-filter" data-days="365">Son 1 Yıl</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="this-month">Bu Ay</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="last-month">Geçen Ay</button>
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>

            <!-- Ödeme Tablosu -->
            @if($pagination->count() > 0)
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead class="thead-dark title">
                    <tr class="text-center">
                      <th>#</th>
                      <th>Firma</th>
                      <th>Tür</th>
                      <th>Açıklama</th>
                      <th>Tutar</th>
                      <th>Durum</th>
                      <th>Tarih</th>
                      <th>Fatura</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($pagination as $index => $payment)
                      <tr>
                        <td class="text-center">{{ $pagination->firstItem() + $index }}</td>
                        <td><strong>{{ $payment['tenant_name'] }}</strong></td>
                        <td class="text-center">
                          <span class="badge badge-{{ $payment['type'] === 'subscription' ? 'primary' : 'info' }}">
                            {{ $payment['type_label'] }}
                          </span>
                        </td>
                        <td style="padding-left:5px;"><strong>{{ $payment['description'] }}</strong></td>
                        <td class="text-center">
                          <strong>{{ number_format($payment['amount'], 2) }} {{ strtoupper($payment['currency']) }}</strong>
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
                          <span class="badge badge-{{ $statusClass }}">{{ $payment['status_label'] }}</span>
                        </td>
                        <td class="text-center">
                          <strong>{{ $payment['created_at']->format('d.m.Y') }}</strong><br>
                          <small class="text-muted">{{ $payment['created_at']->format('H:i') }}</small>
                        </td>
                        <td class="text-center">
                          @if($payment['invoice_path'])
                            <span class="badge badge-success"><i class="fas fa-check"></i> Mevcut</span>
                          @else
                            <span class="badge badge-warning"><i class="fas fa-clock"></i> Bekleniyor</span>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>

              <!-- Sayfalama -->
              <div class="row mt-2">
                <div class="col-sm-12 col-md-5">
                  <div class="dataTables_info">
                    Ödeme Sayısı: <strong>{{ $pagination->total() }}</strong>
                  </div>
                </div>
                <div class="col-sm-12 col-md-7">
                  <div class="dataTables_paginate paging_simple_numbers float-end">
                    {{ $pagination->links() }}
                  </div>
                </div>
              </div>

              <!-- Genel Toplam Alanı - Sağda -->
              @php
                $completedPayments = collect($pagination->items())->where('status', 'completed');
                $subscriptionTotal = $completedPayments->where('type', 'subscription')->sum('amount');
                $storageTotal = $completedPayments->where('type', 'storage')->sum('amount');
                $generalTotal = $completedPayments->sum('amount');
                
                $pendingTotal = collect($pagination->items())->where('status', 'pending')->sum('amount');
                $failedTotal = collect($pagination->items())->where('status', 'failed')->sum('amount');
              @endphp

              <div class="row mt-3">
                <div class="col-md-8"></div>
                <div class="col-md-4">
                  <!-- Tamamlanan Ödemeler -->
                  <div class="payment-summary-box bg-success text-white mb-2">
                    <div class="summary-title">Tamamlanan</div>
                    <div class="summary-item">
                      <span>Abonelik:</span>
                      <span>{{ number_format($subscriptionTotal, 2) }} TL</span>
                    </div>
                    <div class="summary-item">
                      <span>Depolama:</span>
                      <span>{{ number_format($storageTotal, 2) }} TL</span>
                    </div>
                    <div class="summary-item border-top">
                      <span><strong>Toplam:</strong></span>
                      <span><strong>{{ number_format($generalTotal, 2) }} TL</strong></span>
                    </div>
                  </div>

                  <!-- Bekleyen Ödemeler -->
                  <div class="payment-summary-box bg-warning text-white mb-2">
                    <div class="summary-title">Bekleyen</div>
                    <div class="summary-item">
                      <span>Adet:</span>
                      <span>{{ collect($pagination->items())->where('status', 'pending')->count() }}</span>
                    </div>
                    <div class="summary-item border-top">
                      <span><strong>Toplam:</strong></span>
                      <span><strong>{{ number_format($pendingTotal, 2) }} TL</strong></span>
                    </div>
                  </div>

                  <!-- Genel Toplam -->
                  <div class="payment-summary-box bg-dark text-white">
                    <div class="summary-title">Genel Toplam</div>
                    <div class="summary-item">
                      <span>Adet:</span>
                      <span>{{ $pagination->total() }}</span>
                    </div>
                    <div class="summary-item border-top">
                      <span><strong>Toplam:</strong></span>
                      <span><strong>{{ number_format($generalTotal, 2) }} TL</strong></span>
                    </div>
                  </div>
                </div>
              </div>

            @else
              <div class="alert alert-warning text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <h5>Ödeme kaydı bulunamadı</h5>
                <p class="mb-0">Belirtilen kriterlere uygun herhangi bir ödeme kaydı bulunmamaktadır.</p>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.quick-filter, #this-month, #last-month {
    background-color: transparent !important;
    border: 1px solid #6c757d !important;
    color: #6c757d !important;
}

.quick-filter:hover, #this-month:hover, #last-month:hover {
    background-color: #6c757d !important;
    color: white !important;
}

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

/* Ödeme Özet Kutuları */
.payment-summary-box {
    padding: 15px;
    border-radius: 5px;
}

.payment-summary-box .summary-title {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 2px solid rgba(255,255,255,0.3);
}

.payment-summary-box .summary-item {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    font-size: 14px;
}

.payment-summary-box .summary-item.border-top {
    border-top: 1px solid rgba(255,255,255,0.3);
    margin-top: 5px;
    padding-top: 8px;
}

.dataTables_info {
    padding-top: 0.85em;
}

.dataTables_paginate {
    padding-top: 0.25em;
}
</style>

<script>
$(document).ready(function() {
    const today = new Date().toISOString().split('T')[0];
    $('#date_from, #date_to').attr('max', today);
    
    $('#date_from').on('change', function() {
        $('#date_to').attr('min', $(this).val());
    });
    
    $('#date_to').on('change', function() {
        $('#date_from').attr('max', $(this).val());
    });

    $('.quick-filter').on('click', function(e) {
        e.preventDefault();
        const days = parseInt($(this).data('days'));
        const today = new Date();
        const startDate = new Date();
        startDate.setDate(today.getDate() - days);
        
        $('#date_from').val(startDate.toISOString().split('T')[0]);
        $('#date_to').val(today.toISOString().split('T')[0]);
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

    $('#last-month').on('click', function(e) {
        e.preventDefault();
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth(), 0);
        $('#date_from').val(firstDay.toISOString().split('T')[0]);
        $('#date_to').val(lastDay.toISOString().split('T')[0]);
        $('#filter-form').submit();
    });

    $('#clear-filter').on('click', function(e) {
        e.preventDefault();
        window.location.href = '{{ route("super.admin.payment.history.index") }}';
    });

    $('#excel-export').on('click', function(e) {
        e.preventDefault();
        const formData = $('#filter-form').serialize();
        let exportUrl = '{{ route("super.admin.payment.history.export") }}';
        if (formData) {
            exportUrl += '?' + formData;
        }
        window.open(exportUrl, '_blank');
    });
});
</script>
@endsection