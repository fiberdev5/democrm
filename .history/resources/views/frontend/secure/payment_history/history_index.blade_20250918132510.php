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
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-filter mr-1"></i>
                                        Filtreleme
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="GET" action="{{ route('payment-history.index',$tenant->id) }}">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="date_from">Başlangıç Tarihi</label>
                                                    <input type="date" 
                                                           class="form-control" 
                                                           id="date_from" 
                                                           name="date_from" 
                                                           value="{{ $dateFrom }}">
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="date_to">Bitiş Tarihi</label>
                                                    <input type="date" 
                                                           class="form-control" 
                                                           id="date_to" 
                                                           name="date_to" 
                                                           value="{{ $dateTo }}">
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="type">Ödeme Türü</label>
                                                    <select class="form-control" id="type" name="type">
                                                        <option value="all" {{ $type === 'all' || !$type ? 'selected' : '' }}>
                                                            Tümü
                                                        </option>
                                                        <option value="subscription" {{ $type === 'subscription' ? 'selected' : '' }}>
                                                            Abonelik
                                                        </option>
                                                        <option value="storage" {{ $type === 'storage' ? 'selected' : '' }}>
                                                            Depolama
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="status">Durum</label>
                                                    <select class="form-control" id="status" name="status">
                                                        <option value="">Tümü</option>
                                                        @foreach($statuses as $value => $label)
                                                            <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="payment_method">Ödeme Yöntemi</label>
                                                    <select class="form-control" id="payment_method" name="payment_method">
                                                        <option value="">Tümü</option>
                                                        @foreach($paymentMethods as $method)
                                                            <option value="{{ $method }}" {{ $paymentMethod === $method ? 'selected' : '' }}>
                                                                {{ ucfirst($method) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-search mr-1"></i>
                                                    Filtrele
                                                </button>
                                                <a href="{{ route('payment-history.index',$tenant->id) }}" class="btn btn-secondary">
                                                    <i class="fas fa-times mr-1"></i>
                                                    Temizle
                                                </a>
                                                <a href="" class="btn btn-success">
                                                    <i class="fas fa-file-excel mr-1"></i>
                                                    Excel'e Aktar
                                                </a>
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
</style>
@endpush

@push('scripts')
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
});
</script>
@endpush