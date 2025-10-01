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
                              <button type="button" class="btn btn-outline-light text-dark btn-sm quick-filter" data-days="7">
                                Son 7 Gün
                              </button>
                              <button type="button" class="btn btn-outline-light text-dark btn-sm quick-filter" data-days="30">
                                Son 30 Gün
                              </button>
                              <button type="button" class="btn btn-outline-light text-dark btn-sm quick-filter" data-days="90">
                                Son 3 Ay
                              </button>
                              <button type="button" class="btn btn-outline-light text-dark btn-sm quick-filter" data-days="365">
                                Son 1 Yıl
                              </button>
                              <button type="button" class="btn btn-outline-light text-dark btn-sm" id="this-month">
                                Bu Ay
                              </button>
                              <button type="button" class="btn btn-outline-light text-dark btn-sm" id="last-month">
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
                          <strong>
                            @php
                              $description = $payment['description'];
                              // "(Abonelik ID: X)" kısmını kaldır
                              $description = preg_replace('/\(Abonelik ID:\s*\d+\)/i', '', $description);
                              // "via Paytr" kısmını kaldır
                              $description = preg_replace('/via\s+paytr/i', '', $description);
                              // "via PayPal" kısmını kaldır  
                              $description = preg_replace('/via\s+paypal/i', '', $description);
                              // Fazla boşlukları temizle
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

/* Hızlı filtre butonları için özel stiller */
.quick-filter, #this-month, #last-month {
    background-color: transparent !important;
    border: 1px solid #dee2e6 !important;
    color: #495057 !important;
    transition: none !important;
}

.quick-filter:hover, #this-month:hover, #last-month:hover {
    background-color: transparent !important;
    border: 1px solid #dee2e6 !important;
    color: #495057 !important;
    transform: none !important;
    box-shadow: none !important;
}

.quick-filter:focus, #this-month:focus, #last-month:focus {
    background-color: transparent !important;
    border: 1px solid #dee2e6 !important;
    color: #495057 !important;
    box-shadow: none !important;
    outline: none !important;
}

.quick-filter.active, #this-month.active, #last-month.active {
    background-color: transparent !important;
    border: 2px solid #c8d6e5 !important;
    color: #495057 !important;
    box-shadow: none !important;
}

.quick-filter.active:hover, #this-month.active:hover, #last-month.active:hover {
    background-color: transparent !important;
    border: 2px solid #c8d6e5 !important;
    color: #495057 !important;
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

    // Hızlı tarih filtreleri - Düzeltilmiş versiyon
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
        
        // Aktif butonu vurgula - sadece active class ekle/çıkar
        $('.quick-filter').removeClass('active');
        $('#this-month, #last-month').removeClass('active');
        $(this).addClass('active');
        
        // Form değerlerini doğrula
        console.log('Form values after quick filter:');
        console.log('date_from:', $('#date_from').val());
        console.log('date_to:', $('#date_to').val());
        
        // Formu submit et
        submitForm();
        
        return false;
    });

    // Bu ay filtresi - Düzeltilmiş versiyon
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
        
        // Aktif butonu vurgula - sadece active class ekle/çıkar
        $('.quick-filter').removeClass('active');
        $('#last-month').removeClass('active');
        $(this).addClass('active');
        
        // Formu submit et
        submitForm();
        
        return false;
    });

    // Geçen ay filtresi - Düzeltilmiş versiyon
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
        
        // Aktif butonu vurgula - sadece active class ekle/çıkar
        $('.quick-filter').removeClass('active');
        $('#this-month').removeClass('active');
        $(this).addClass('active');
        
        // Formu submit et
        submitForm();
        
        return false;
    });

    // Temizle butonu
    $('#clear-filter').on('click', function(e) {
        e.preventDefault();
        
        // Form alanlarını temizle
        $('#date_from').val('');
        $('#date_to').val('');
        $('#type').val('all');
        
        // Aktif buton stillerini sıfırla
        $('.quick-filter').removeClass('active');
        $('#this-month, #last-month').removeClass('active');
        
        // Sayfayı yenile (filtresiz)
        window.location.href = '{{ route("payment-history.index", $tenant->id) }}';
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
    console.log('date_from:', $('#date_from').val());@extends('frontend.secure.user_master')
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