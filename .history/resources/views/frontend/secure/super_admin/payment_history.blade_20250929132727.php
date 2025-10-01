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
<div class="row align-items-end">
  <!-- Firma -->
  <div class="col-lg-2 col-md-3 col-sm-6">
    <div class="form-group mb-3">
      <label for="tenant_id" class="form-label fw-bold" style="font-size: 12px;">Firma</label>
      <select class="form-control form-control-sm" id="tenant_id" name="tenant_id">
        <option value="">Tüm Firmalar</option>
        @foreach($tenants as $tenant)
          <option value="{{ $tenant->id }}" {{ $tenantId == $tenant->id ? 'selected' : '' }}>
            {{ $tenant->firma_adi }}
          </option>
        @endforeach
      </select>
    </div>
  </div>

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
      <select class="form-control form-control-sm" id="type" name="type">
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
        <button type="submit" class="btn btn-primary btn-sm" title="Filtrele">
          <i class="fas fa-search"></i>
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-filter" title="Temizle">
          <i class="fas fa-eraser"></i>
        </button>
        <a href="#" class="btn btn-success btn-sm" id="excel-export" title="Excel İndir">
          <i class="fas fa-file-excel"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- Hızlı Tarih Filtreleri -->
  <div class="col-auto">
    <div class="form-group mb-3">
      <label class="form-label fw-bold text-transparent" style="font-size: 12px;">Hızlı</label>
      <div class="d-flex flex-nowrap gap-1">
        <button type="button" class="btn btn-outline-light text-dark btn-sm quick-filter" data-days="7">Son 7 Gün</button>
        <button type="button" class="btn btn-outline-light text-dark btn-sm quick-filter" data-days="30" >Son 30 Gün</button>
        <button type="button" class="btn btn-outline-light text-dark btn-sm quick-filter" id="this-month" >Bu Ay</button>
        <button type="button" class="btn btn-outline-light text-dark btn-sm quick-filter" data-days="90">
          Son 3 Ay
        </button>
        <button type="button" class="btn btn-outline-light text-dark btn-sm quick-filter" data-days="365">
          Son 1 Yıl
        </button>
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
            @if($pagination->count() > 0)
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead class="thead-dark title">
                    <tr class="text-center">
                      <th style="width: 50px;">#</th>
                      <th style="width: 150px;">Firma</th>
                      <th style="width: 100px;">Tür</th>
                      <th>Açıklama</th>
                      <th style="width: 120px;">Tutar</th>
                      <th style="width: 120px;">Durum</th>
                      <th style="width: 120px;">Tarih</th>
                      <th style="width: 100px;">Fatura</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($pagination as $index => $payment)
                      <tr>
                        <td class="text-center">{{ $pagination->firstItem() + $index }}</td>
                        <td>
                          <strong>{{ $payment['tenant_name'] }}</strong>
                        </td>
                        <td class="text-center">
                          <span class="badge badge-{{ $payment['type'] === 'subscription' ? 'primary' : 'info' }}">
                            {{ $payment['type_label'] }}
                          </span>
                        </td>
                        <td style="padding-left:5px;">
                          <strong>{{ $payment['description'] }}</strong>
                        </td>
                        <td class="text-center">
                          <strong>
                            {{ number_format($payment['amount'], 2) }} 
                            {{ strtoupper($payment['currency']) }}
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
                          <br>
                          <small class="text-muted">
                            {{ $payment['created_at']->format('H:i') }}
                          </small>
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
              </div>

              <!-- Sayfalama -->
              <div class="row mt-3 mb-3">
                <div class="col-12">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="dataTables_info">
                      <i class="fas fa-info-circle mr-1"></i>
                      Ödeme Sayısı: <strong>{{ $pagination->total() }}</strong>
                    </div>
                    <div>
                      {{ $pagination->links() }}
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

            <!-- Toplam Alanı -->
            <div class="tableToplamaAlani">
              {{-- <div class="row r1">
                <div class="sol"><strong>Toplam</strong></div>
                <div class="sag">
                  <div class="tur t1"><span>Tamamlanan:</span> {{ number_format($summaryStats['completed'], 2) }} ₺</div>
                  <div class="tur t2"><span>Bekleyen:</span> {{ number_format($summaryStats['pending'], 2) }} ₺</div>
                  <div class="tur t3"><span>Başarısız:</span> {{ number_format($summaryStats['failed'], 2) }} ₺</div>
                  <div class="tur t4"><span>Toplam:</span> {{ number_format($summaryStats['total'], 2) }} ₺</div>
                </div>
              </div> --}}

              <div class="row r2">
                <div class="sol"><strong>Abonelik Ödemeleri</strong></div>
                <div class="sag">
                  <div class="tur t1"><span>Tamamlanan:</span> {{ number_format($summaryStats['subscription_completed'], 2) }} ₺</div>
                  <div class="tur t2"><span>Bekleyen:</span> {{ number_format($summaryStats['subscription_pending'], 2) }} ₺</div>
                  <div class="tur t3"><span>Başarısız:</span> {{ number_format($summaryStats['subscription_failed'], 2) }} ₺</div>
                  <div class="tur t4"><span>Toplam:</span> {{ number_format($summaryStats['subscription_total'], 2) }} ₺</div>
                </div>
              </div>

              <div class="row r3">
                <div class="sol"><strong>Depolama Ödemeleri</strong></div>
                <div class="sag">
                  <div class="tur t1"><span>Tamamlanan:</span> {{ number_format($summaryStats['storage_completed'], 2) }} ₺</div>
                  <div class="tur t2"><span>Bekleyen:</span> {{ number_format($summaryStats['storage_pending'], 2) }} ₺</div>
                  <div class="tur t3"><span>Başarısız:</span> {{ number_format($summaryStats['storage_failed'], 2) }} ₺</div>
                  <div class="tur t4"><span>Toplam:</span> {{ number_format($summaryStats['storage_total'], 2) }} ₺</div>
                </div>
              </div>

              <div class="row r4">
                <div class="sol"><strong>Genel Toplam</strong></div>
                <div class="sag">
                  <div class="tur t1"><span>Tamamlanan:</span> {{ number_format($summaryStats['completed'], 2) }} ₺</div>
                  <div class="tur t2"><span>Bekleyen:</span> {{ number_format($summaryStats['pending'], 2) }} ₺</div>
                  <div class="tur t3"><span>Başarısız:</span> {{ number_format($summaryStats['failed'], 2) }} ₺</div>
                  <div class="tur t4"><span>Toplam:</span> {{ number_format($summaryStats['total'], 2) }} ₺</div>
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

.quick-filter, #this-month, #last-month {
    transition: all 0.2s;
}

.quick-filter:hover, #this-month:hover, #last-month:hover {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
    color: #fff !important;
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

.tableToplamaAlani .r1 {
    background: #28a745;
}

.tableToplamaAlani .r1 .sol {
    border-right: 1px solid #238c3b;
}

.tableToplamaAlani .r2 {
    background: #dc3545;
}

.tableToplamaAlani .r2 .sol {
    border-right: 1px solid #b92d3b;
}

.tableToplamaAlani .r3 {
    background: #007bff;
}

.tableToplamaAlani .r3 .sol {
    border-right: 1px solid #0063ce;
}

.tableToplamaAlani .r4 {
    background: #343a40;
}

.tableToplamaAlani .r4 .sol {
    padding: 5px 15px;
    border-right: 1px solid #1e2225;
}

/* Pagination Stilleri */
.pagination {
    margin: 0;
}

.dataTables_info {
    font-size: 14px;
    color: #495057;
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

    // Hızlı tarih filtreleri
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