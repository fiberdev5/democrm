@extends('frontend.secure.user_master')
@section('user')

<div class="page-content" >
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
            <div class="row mb-1">
              <div class="col-12">
                <div class="card shadow-sm" style="margin-bottom: 0!important;">           
                  <div class="card-body">
                    <form method="GET" action="{{ route('payment-history.index',$tenant->id) }}" id="filter-form">
                      <div class="row">
                        <!-- Başlangıç Tarihi -->
                        <div class="col-lg-3 col-md-4 col-sm-6">
                          <div class="form-group mb-3">
                            <label for="date_from" class="form-label fw-bold">
                              Başlangıç Tarihi
                            </label>
                            <input type="date" 
                              class="form-control datepicker" 
                              id="date_from" 
                              name="date_from" 
                              value="{{ $dateFrom }}"
                              title="Başlangıç tarihi seçin" style="width: 100%!important;">
                          </div>
                        </div>

                        <!-- Bitiş Tarihi -->
                        <div class="col-lg-3 col-md-4 col-sm-6">
                          <div class="form-group mb-3">
                            <label for="date_to" class="form-label fw-bold">
                              Bitiş Tarihi
                            </label>
                            <input type="date" 
                              class="form-control datepicker" 
                              id="date_to" 
                              name="date_to" 
                              value="{{ $dateTo }}"
                              title="Bitiş tarihi seçin" style="width: 100%!important;">                        
                          </div>
                        </div>

                        <!-- Ödeme Türü -->
                        <div class="col-lg-3 col-md-4 col-sm-6">
                          <div class="form-group mb-3">
                            <label for="type" class="form-label fw-bold">
                              Ödeme Türü
                            </label>
                            <select class="form-control " id="type" name="type">
                              <option value="all" {{ $type === 'all' || !$type ? 'selected' : '' }}>
                                <i class="fas fa-list"></i> Tüm Ödemeler
                              </option>
                              <option value="subscription" {{ $type === 'subscription' ? 'selected' : '' }}>
                                <i class="fas fa-calendar-check"></i> Abonelik Ödemeleri
                              </option>
                              <option value="storage" {{ $type === 'storage' ? 'selected' : '' }}>
                                <i class="fas fa-hdd"></i> Depolama Ödemeleri
                              </option>
                            </select>
                          </div>
                        </div>

                        <!-- İşlem Butonları -->
                        <div class="col-lg-3 col-md-12">
                          <div class="form-group mb-3">
                            <label class="form-label fw-bold text-transparent">İşlemler</label>
                              <div class="d-flex gap-2" role="group">
                                <!-- Filtrele Butonu -->
                                <button type="submit" class="btn  btn-primary btn-sm">
                                  <i class="fas fa-search mr-2"></i>
                                  Filtrele
                                </button>
                                    
                                <!-- Temizle Butonu -->
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-filter">
                                  <i class="fas fa-eraser mr-1"></i>
                                  Temizle
                                </button>
                                    
                                <!-- Excel Export Butonu -->
                                <a href="{{route('payment-history.export', $tenant->id)}}" class="btn btn-success btn-sm" id="excel-export">
                                  <i class="fas fa-file-excel mr-1"></i>
                                  Excel İndir
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- Hızlı Tarih Filtreleri -->
                        <div class="row mt-3">
                          <div class="col-12">
                            <div class="d-flex flex-wrap gap-2">
                              <span class="text-muted fw-bold mr-3">
                                <i class="fas fa-clock mr-1"></i>
                                Hızlı Filtreler:
                              </span>
                              <button type="button" class="btn  btn-sm quick-filter" data-days="7" onclick="console.log('Button clicked directly')">
                                Son 7 Gün
                              </button>
                              <button type="button" class="btn  btn-sm quick-filter" data-days="30" onclick="console.log('Button clicked directly')">
                                Son 30 Gün
                              </button>
                              <button type="button" class="btn  btn-sm quick-filter" data-days="90">
                                Son 3 Ay
                              </button>
                              <button type="button" class="btn  btn-sm quick-filter" data-days="365">
                                Son 1 Yıl
                              </button>
                              <button type="button" class="btn  btn-sm" id="this-month">
                                Bu Ay
                              </button>
                              <button type="button" class="btn  btn-sm" id="last-month">
                                Geçen Ay
                              </button>
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
                      <th>Tür</th>
                      <th>Açıklama</th>
                      <th>Tutar</th>
                      <th>Durum</th>
                      <th>Tarih</th>
                      <th>Fatura</th>
                      <th>İşlemler</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($pagination as $index => $payment)
                      <tr>
                        <td class="text-center">{{ $pagination->firstItem() + $index }}</td>
                        <td class="text-center">
                          <strong class="">{{ $payment['type_label'] }}</strong>
                        </td>
                        <td style="padding-left:5px;">
                          <strong>{{ $payment['description'] }}</strong>
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
                          <br>
                          {{-- <small class="text-muted">
                            {{ $payment['created_at']->format('H:i') }}
                          </small> --}}
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
                      <td class="text-center">
                        @if($payment['invoice_path'])
                          <a href="{{ asset($payment['invoice_path']) }}" 
                            class="btn btn-sm btn-outline-primary"
                            target="_blank">
                            <i class="fas fa-file-pdf mr-1"></i>
                            Faturayı İndir
                          </a>
                        @else
                          <span class="text-muted">
                            <i class="fas fa-times"></i>
                            Fatura Yok
                          </span>
                        @endif
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>

              <!-- Sayfalama -->
              <div class="row mt-2">
                <div class="col-12">
                  {{ $pagination->links() }}
                </div>
              </div>

            @else
              <div class="alert alert-warning text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <h5>Ödeme kaydı bulunamadı</h5>
                <p class="mb-0">Belirtilen kriterlere uygun herhangi bir ödeme kaydı bulunmamaktadır.</p>
              </div>
            @endif

            <!-- Sonuç Sayısı -->
            <div class="row mb-3">
              <div class="col-12">
                <div class="">
                  <i class="fas fa-info-circle mr-1"></i>
                  Toplam <strong>{{ $pagination->total() }}</strong> ödeme kaydı bulundu.
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
.badge-outline-dark {
    color: #343a40;
    border: 1px solid #343a40;
    background: transparent;
}



.table th {
    border-top: none;
    font-weight: 600;
}
.table-striped>tbody>tr td {
    text-align: center!important;
}
.alert-info {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border: none;
    color: #1565c0;
}

.btn-outline-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,123,255,0.3);
}

.table-responsive {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.form-control-lg {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-control-lg:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-group-vertical .btn {
    margin-bottom: 5px;
}


.form-label {
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.gap-2 {
    gap: 0.5rem !important;
}

.text-transparent {
    color: transparent !important;
}

@media (max-width: 768px) {
    .btn-group-vertical {
        display: block !important;
    }
    
    .btn-group-vertical .btn {
        width: 100%;
        margin-bottom: 8px;
    }
    
    .d-flex.gap-2 {
        justify-content: center;
    }
}
</style>



<script>
$(document).ready(function() {
    console.log('Payment history page loaded');
    
    // Excel export butonunu düzelt - href'i kaldır
    $('#excel-export').removeAttr('href');
    
    // Tarih filtrelerini bugünden fazla olmaması için kontrol et
    const today = new Date().toISOString().split('T')[0];
    $('#date_from, #date_to').attr('max', today);
    
    // Başlangıç tarihini değiştirdiğinde bitiş tarihinin minimum değerini ayarla
    $('#date_from').on('change', function() {
        $('#date_to').attr('min', $(this).val());
        console.log('Date from changed:', $(this).val());
    });
    
    // Bitiş tarihini değiştirdiğinde başlangıç tarihinin maksimum değerini ayarla
    $('#date_to').on('change', function() {
        $('#date_from').attr('max', $(this).val());
        console.log('Date to changed:', $(this).val());
    });

    // Hızlı tarih filtreleri - Çalışan versiyon
    $('.quick-filter').off('click').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const days = parseInt($(this).data('days'));
        console.log('Quick filter clicked for days:', days);
        
        if (isNaN(days)) {
            console.error('Invalid days value:', $(this).data('days'));
            return false;
        }
        
        const today = new Date();
        const startDate = new Date();
        startDate.setDate(today.getDate() - days);
        
        // Tarihleri formatla
        const startDateStr = startDate.toISOString().split('T')[0];
        const todayStr = today.toISOString().split('T')[0];
        
        console.log('Setting dates:', startDateStr, 'to', todayStr);
        
        // Tarihleri set et
        $('#date_from').val(startDateStr);
        $('#date_to').val(todayStr);
        
        // Aktif butonu vurgula
        $('.quick-filter').removeClass('btn-info active').addClass('btn-outline-info');
        $('#this-month, #last-month').removeClass('btn-warning active').addClass('btn-outline-warning');
        $(this).removeClass('btn-outline-info').addClass('btn-info active');
        
        // Form değerlerini doğrula
        console.log('Form values after quick filter:');
        console.log('date_from:', $('#date_from').val());
        console.log('date_to:', $('#date_to').val());
        
        // Formu submit et
        submitForm();
        
        return false;
    });

    // Bu ay filtresi - Çalışan versiyon
    $('#this-month').off('click').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('This month clicked');
        
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        
        const firstDayStr = firstDay.toISOString().split('T')[0];
        const todayStr = today.toISOString().split('T')[0];
        
        console.log('Setting this month dates:', firstDayStr, 'to', todayStr);
        
        $('#date_from').val(firstDayStr);
        $('#date_to').val(todayStr);
        
        // Aktif butonu vurgula
        $('.quick-filter').removeClass('btn-info active').addClass('btn-outline-info');
        $('#last-month').removeClass('btn-warning active').addClass('btn-outline-warning');
        $(this).removeClass('btn-outline-warning').addClass('btn-warning active');
        
        // Formu submit et
        submitForm();
        
        return false;
    });

    // Geçen ay filtresi - Çalışan versiyon
    $('#last-month').off('click').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Last month clicked');
        
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth(), 0);
        
        const firstDayStr = firstDay.toISOString().split('T')[0];
        const lastDayStr = lastDay.toISOString().split('T')[0];
        
        console.log('Setting last month dates:', firstDayStr, 'to', lastDayStr);
        
        $('#date_from').val(firstDayStr);
        $('#date_to').val(lastDayStr);
        
        // Aktif butonu vurgula
        $('.quick-filter').removeClass('btn-info active').addClass('btn-outline-info');
        $('#this-month').removeClass('btn-warning active').addClass('btn-outline-warning');
        $(this).removeClass('btn-outline-warning').addClass('btn-warning active');
        
        // Formu submit et
        submitForm();
        
        return false;
    });

    // Form submit fonksiyonu
    function submitForm() {
        console.log('Submitting form with data:', $('#filter-form').serialize());
        
        const form = $('#filter-form')[0];
        if (form) {
            // Loading göster
            const submitBtn = $('#filter-form button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Yükleniyor...');
            submitBtn.prop('disabled', true);
            
            // Form submit et
            form.submit();
        } else {
            console.error('Form bulunamadı!');
        }
    }

    // Excel export - Düzeltilmiş
    $('#excel-export').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const formData = $('#filter-form').serialize();
        const tenantId = '{{ $tenant->id }}';
        
        // Export URL'yi oluştur
        let exportUrl = '{{ route("payment-history.export", $tenant->id) }}';
        
        if (formData) {
            exportUrl += '?' + formData;
        }
        
        console.log('Excel export URL:', exportUrl);
        
        // Yeni sekmede aç
        window.open(exportUrl, '_blank');
        
        return false;
    });

    // Manuel filtrele butonu
    $('#filter-form').on('submit', function(e) {
        console.log('Form submitted manually');
        console.log('Form data:', $(this).serialize());
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        //submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Filtreleniyor...');
        submitBtn.prop('disabled', true);
    });

    // Debug: Form elemanlarının değerlerini logla
    console.log('Form initial values:');
    console.log('date_from:', $('#date_from').val());
    console.log('date_to:', $('#date_to').val());
    console.log('type:', $('#type').val());
    
    // Debug: Butonları kontrol et
    console.log('Quick filter buttons found:', $('.quick-filter').length);
    console.log('This month button found:', $('#this-month').length);
    console.log('Last month button found:', $('#last-month').length);
    console.log('Excel export button found:', $('#excel-export').length);
});
</script>
@endsection