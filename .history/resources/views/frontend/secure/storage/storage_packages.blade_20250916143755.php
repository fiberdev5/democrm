{{-- resources/views/frontend/secure/storage/packages.blade.php --}}
@extends('frontend.secure.user_master')
@section('user')

<div class="page-content">
    <div class="container-fluid">
        <!-- Başlık -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        <i class="fas fa-hdd text-primary me-2"></i>
                        Ek Storage Paketleri
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('secure.home', $firma->id) }}">Ana Sayfa</a></li>
                            <li class="breadcrumb-item active">Storage Paketleri</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mevcut Storage Durumu -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Mevcut Storage Durumunuz</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Kullanım: {{ $storageInfo['current_usage_formatted'] }} / {{ $storageInfo['limit_formatted'] }}</span>
                                    <span class="text-{{ $storageInfo['danger_threshold'] ? 'danger' : ($storageInfo['warning_threshold'] ? 'warning' : 'success') }}">
                                        %{{ $storageInfo['usage_percentage'] }}
                                    </span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-{{ $storageInfo['danger_threshold'] ? 'danger' : ($storageInfo['warning_threshold'] ? 'warning' : 'success') }}" 
                                         style="width: {{ $storageInfo['usage_percentage'] }}%"></div>
                                </div>
                                @if($storageInfo['has_extra_storage'])
                                    <small class="text-success mt-1 d-block">
                                        <i class="fas fa-plus-circle me-1"></i>
                                        Ek Storage: {{ $storageInfo['extra_storage_gb'] }} GB aktif
                                    </small>
                                @endif
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="text-muted">Kalan Alan</div>
                                <h4 class="mb-0">{{ $storageInfo['remaining_formatted'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Storage Paketleri -->
        <div class="row">
            @foreach($packages as $package)
            <div class="col-lg-4 col-md-6">
                <div class="card storage-package-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <div class="avatar-lg mx-auto">
                                <div class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                    <i class="fas fa-database fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="card-title">{{ $package->name }}</h5>
                        <p class="text-muted mb-3">{{ $package->description }}</p>
                        
                        <div class="mb-3">
                            <h2 class="text-primary mb-0">{{ number_format($package->price, 2) }} ₺</h2>
                            <small class="text-muted">Tek seferlik ödeme</small>
                        </div>
                        
                        <div class="mb-4">
                            <div class="d-flex justify-content-center align-items-center">
                                <span class="badge bg-success-subtle text-success fs-6 px-3 py-2">
                                    +{{ $package->storage_gb }} GB
                                </span>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <ul class="list-unstyled text-start">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Kalıcı ek depolama alanı
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Tüm dosya türleri desteklenir
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Anında aktifleşir
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    7/24 destek
                                </li>
                            </ul>
                        </div>
                        
                        <form action="{{ route('storage.purchase', $firma->id) }}" method="POST" class="w-100">
                            @csrf
                            <input type="hidden" name="package_id" value="{{ $package->id }}">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Satın Al
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Bilgilendirme -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6><i class="fas fa-info-circle text-primary me-2"></i>Önemli Bilgiler</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Ek storage kalıcıdır, süre sınırı yoktur</li>
                                    <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Satın aldığınız alan mevcut limitinize eklenir</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Güvenli PayTR ödeme sistemi kullanılır</li>
                                    <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Ödeme sonrası anında aktif olur</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.storage-package-card {
    transition: transform 0.2s;
    border: 2px solid transparent;
}

.storage-package-card:hover {
    transform: translateY(-5px);
    border-color: #0d6efd;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.avatar-lg {
    width: 4rem;
    height: 4rem;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-primary-subtle {
    background-color: rgba(13, 110, 253, 0.1);
}

.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1);
}

.text-success {
    color: #198754 !important;
}
</style>

<script>
@if(request('payment_check'))
    // Ödeme kontrolü JavaScript'i buraya
    $(document).ready(function() {
        checkStoragePaymentStatus();
    });
@endif
</script>

@endsection