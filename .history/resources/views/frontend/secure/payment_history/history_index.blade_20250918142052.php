@extends('frontend.secure.user_master')
@section('user')

<div class="page-content" >
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-credit-card mr-2"></i>
                        Ödeme Geçmişi
                    </h3>
                </div>

                <div class="card-body">
                    <!-- Filtreleme Formu -->
                    <div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary">
                <h5 class="card-title mb-0 text-white">
                    <i class="fas fa-filter mr-2"></i>
                    Ödeme Geçmişi Filtreleme
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('payment-history.index',$tenant->id) }}" id="filter-form">
                    <div class="row align-items-end">
                        <!-- Başlangıç Tarihi -->
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="form-group mb-3">
                                <label for="date_from" class="form-label fw-bold">
                                    <i class="fas fa-calendar-alt text-primary mr-1"></i>
                                    Başlangıç
                                </label>
                                <input type="date" 
                                       class="form-control form-control-lg" 
                                       id="date_from" 
                                       name="date_from" 
                                       value="{{ $dateFrom }}"
                                       title="Başlangıç tarihi seçin">
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Varsayılan: Son 30 gün
                                </small>
                            </div>
                        </div>

                        <!-- Bitiş Tarihi -->
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="form-group mb-3">
                                <label for="date_to" class="form-label fw-bold">
                                    <i class="fas fa-calendar-check text-success mr-1"></i>
                                    Bitiş
                                </label>
                                <input type="date" 
                                       class="form-control form-control-lg" 
                                       id="date_to" 
                                       name="date_to" 
                                       value="{{ $dateTo }}"
                                       title="Bitiş tarihi seçin">
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Varsayılan: Bugün
                                </small>
                            </div>
                        </div>

                        <!-- Ödeme Türü -->
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="form-group mb-3">
                                <label for="type" class="form-label fw-bold">
                                    <i class="fas fa-tags text-warning mr-1"></i>
                                    Ödeme Türü
                                </label>
                                <select class="form-control form-control-lg" id="type" name="type">
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

                        <!-- Durum Filtresi -->

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
                                    <a href="{{ route('payment-history.index',$tenant->id) }}?clear=1" 
                                       class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-eraser mr-1"></i>
                                        Temizle
                                    </a>
                                    
                                    <!-- Excel Export Butonu -->
                                    <a href="#" class="btn btn-success btn-sm" id="excel-export">
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
                                <button type="button" class="btn btn-outline-info btn-sm quick-filter" data-days="7">
                                    Son 7 Gün
                                </button>
                                <button type="button" class="btn btn-outline-info btn-sm quick-filter" data-days="30">
                                    Son 30 Gün
                                </button>
                                <button type="button" class="btn btn-outline-info btn-sm quick-filter" data-days="90">
                                    Son 3 Ay
                                </button>
                                <button type="button" class="btn btn-outline-info btn-sm quick-filter" data-days="365">
                                    Son 1 Yıl
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-sm" id="this-month">
                                    Bu Ay
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-sm" id="last-month">
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
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Tür</th>
                                        <th>Açıklama</th>
                                        <th>Tutar</th>
                                        <th>Ödeme Yöntemi</th>
                                        <th>Durum</th>
                                        <th>Tarih</th>
                                        <th>Fatura</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pagination as $index => $payment)
                                        <tr>
                                            <td>{{ $pagination->firstItem() + $index }}</td>
                                            <td>
                                                <span class="badge badge-{{ $payment['type'] === 'subscription' ? 'primary' : 'success' }}">
                                                    <i class="fas fa-{{ $payment['type'] === 'subscription' ? 'calendar-check' : 'hdd' }} mr-1"></i>
                                                    {{ $payment['type_label'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ $payment['description'] }}</strong>
                                                @if($payment['type'] === 'subscription')
                                                    @if(isset($payment['transaction_id']))
                                                        <br>
                                                        <small class="text-muted">
                                                            İşlem ID: {{ $payment['transaction_id'] }}
                                                        </small>
                                                    @endif
                                                    @if(isset($payment['gateway']))
                                                        <br>
                                                        <small class="text-info">
                                                            {{ ucfirst($payment['gateway']) }} üzerinden
                                                        </small>
                                                    @endif
                                                    @if(isset($payment['paid_at']))
                                                        <br>
                                                        <small class="text-success">
                                                            Ödeme: {{ \Carbon\Carbon::parse($payment['paid_at'])->format('d.m.Y H:i') }}
                                                        </small>
                                                    @endif
                                                @endif
                                                @if($payment['type'] === 'storage' && isset($payment['expires_at']))
                                                    <br>
                                                    <small class="text-muted">
                                                        Bitiş: {{ \Carbon\Carbon::parse($payment['expires_at'])->format('d.m.Y H:i') }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="text-success">
                                                    {{ number_format($payment['amount'], 2) }} 
                                                    {{ isset($payment['currency']) ? strtoupper($payment['currency']) : '₺' }}
                                                </strong>
                                            </td>
                                            <td>
                                                @if($payment['payment_method'])
                                                    <span class="badge badge-outline-dark">
                                                        {{ ucfirst($payment['payment_method']) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
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
                                            <td>
                                                <strong>{{ $payment['created_at']->format('d.m.Y') }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $payment['created_at']->format('H:i') }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                @if($payment['invoice_path'] && file_exists(storage_path('app/' . $payment['invoice_path'])))
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
                                                @if($payment['invoice_path'] && file_exists(storage_path('app/' . $payment['invoice_path'])))
                                                    <a href="{{ route('tenant.payment-history.invoice', ['type' => $payment['type'], 'id' => $payment['id']]) }}" 
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
                        <div class="row mt-4">
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
                            <div class="alert alert-info">
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
@endsection

@push('styles')
<style>
.badge-outline-dark {
    color: #343a40;
    border: 1px solid #343a40;
    background: transparent;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.table th {
    border-top: none;
    font-weight: 600;
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

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-success {
    background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
    border: none;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(86, 171, 47, 0.4);
}

.quick-filter {
    border-radius: 20px;
    font-size: 0.85rem;
    padding: 5px 15px;
    transition: all 0.3s ease;
}

.quick-filter:hover {
    transform: translateY(-1px);
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
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Tarih filtrelerini bugünden fazla olmaması için kontrol et
    $('#date_from, #date_to').attr('max', new Date().toISOString().split('T')[0]);
    
    // Eğer tarih değerleri boşsa, varsayılan olarak son 1 ay aralığını set et
    if (!$('#date_from').val()) {
        var lastMonth = new Date();
        lastMonth.setMonth(lastMonth.getMonth() - 1);
        $('#date_from').val(lastMonth.toISOString().split('T')[0]);
    }
    
    if (!$('#date_to').val()) {
        var today = new Date();
        $('#date_to').val(today.toISOString().split('T')[0]);
    }
    
    // Başlangıç tarihini değiştirdiğinde bitiş tarihinin minimum değerini ayarla
    $('#date_from').on('change', function() {
        $('#date_to').attr('min', $(this).val());
    });
    
    // Bitiş tarihini değiştirdiğinde başlangıç tarihinin maksimum değerini ayarla  
    $('#date_to').on('change', function() {
        $('#date_from').attr('max', $(this).val());
    });
    
    // Temizle butonuna özel davranış ekle
    $('.btn-secondary').on('click', function(e) {
        e.preventDefault();
        
        // Form alanlarını temizle ve varsayılan değerleri set et
        var today = new Date();
        var lastMonth = new Date();
        lastMonth.setMonth(lastMonth.getMonth() - 1);
        
        $('#date_from').val(lastMonth.toISOString().split('T')[0]);
        $('#date_to').val(today.toISOString().split('T')[0]);
        $('#type').val('all');
        $('#payment_method').val('');
        $('#status').val('');
        
        // Formu submit et
        $('form').submit();
    });
});
</script>
<script>
$(document).ready(function() {
    // Tarih filtrelerini bugünden fazla olmaması için kontrol et
    $('#date_from, #date_to').attr('max', new Date().toISOString().split('T')[0]);
    
    // Başlangıç tarihini değiştirdiğinde bitiş tarihinin minimum değerini ayarla
    $('#date_from').on('change', function() {
        $('#date_to').attr('min', $(this).val());
    });
    
    // Bitiş tarihini değiştirdiğinde başlangıç tarihinin maksimum değerini ayarla
    $('#date_to').on('change', function() {
        $('#date_from').attr('max', $(this).val());
    });

    // Hızlı tarih filtreleri
    $('.quick-filter').on('click', function() {
        const days = $(this).data('days');
        const today = new Date();
        const startDate = new Date();
        startDate.setDate(today.getDate() - days);
        
        $('#date_from').val(startDate.toISOString().split('T')[0]);
        $('#date_to').val(today.toISOString().split('T')[0]);
        
        // Aktif butonu vurgula
        $('.quick-filter').removeClass('active');
        $(this).addClass('active');
    });

    // Bu ay filtresi
    $('#this-month').on('click', function() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        
        $('#date_from').val(firstDay.toISOString().split('T')[0]);
        $('#date_to').val(today.toISOString().split('T')[0]);
        
        $('.quick-filter, #last-month').removeClass('active');
        $(this).addClass('active');
    });

    // Geçen ay filtresi
    $('#last-month').on('click', function() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth(), 0);
        
        $('#date_from').val(firstDay.toISOString().split('T')[0]);
        $('#date_to').val(lastDay.toISOString().split('T')[0]);
        
        $('.quick-filter, #this-month').removeClass('active');
        $(this).addClass('active');
    });

    // Excel export
    $('#excel-export').on('click', function(e) {
        e.preventDefault();
        const formData = $('#filter-form').serialize();
        window.location.href = '' + formData;
    });

    // Form submit animasyonu
    $('#filter-form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Filtreleniyor...');
        submitBtn.prop('disabled', true);
    });

    // Tooltip'ler
    $('[title]').tooltip();
});
</script>
@endpush