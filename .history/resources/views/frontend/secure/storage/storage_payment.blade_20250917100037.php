{{-- resources/views/frontend/secure/storage/payment.blade.php --}}
@extends('frontend.secure.user_master')
@section('user')

<div class="page-content">
    <div class="container-fluid">
        <!-- Başlık -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        <i class="fas fa-credit-card text-primary me-2"></i>
                        Ödeme Sayfası
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('secure.home', $firma->id) }}">Ana Sayfa</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('storage.packages', $firma->id) }}">Storage Paketleri</a></li>
                            <li class="breadcrumb-item active">Ödeme</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Sipariş Özeti -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Sipariş Özeti</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-md me-3">
                                        <div class="avatar-title bg-primary-subtle rounded text-primary">
                                            <i class="fas fa-database fa-2x"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $package->name }}</h6>
                                        <p class="text-muted mb-0">{{ $package->description }}</p>
                                        <small class="text-success">
                                            <i class="fas fa-check-circle me-1"></i>
                                            +{{ $package->storage_gb }} GB Kalıcı Depolama
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <h4 class="text-primary mb-0">{{ number_format($package->price, 2) }} ₺</h4>
                                <small class="text-muted">Tek seferlik ödeme</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PayTR Ödeme Formu -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Güvenli Ödeme</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <p class="text-muted mb-2">Ödeme işlemi güvenli PayTR sistemi üzerinden gerçekleştirilmektedir.</p>
                            <div class="d-flex justify-content-center align-items-center gap-3">
                                <img src="{{asset('frontend/img/visa.jpg')}}" alt="Visa" class="h-8" width="30">
            <img src="{{asset('frontend/img/masterpass.jpg')}}" alt="Mastercard" class="h-10" width="30">
            <img src="{{asset('frontend/img/troy.jpg')}}" alt="Troy" class="h-8" width="30">
                            </div>
                        </div>

                        <!-- PayTR İframe Container -->
                        <div id="paytr-iframe-container" style="min-height: 600px; text-align: center;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Ödeme sayfası yükleniyor...</span>
                            </div>
                            <p class="mt-3 text-muted">Ödeme sayfası yükleniyor...</p>
                        </div>

                        <!-- İptal Butonu -->
                        <div class="text-center mt-4">
                            <a href="{{ route('storage.packages', $firma->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Geri Dön
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-md {
    width: 3rem;
    height: 3rem;
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

#paytr_iframe {
    width: 100%;
    height: 600px;
    border: none;
    border-radius: 0.375rem;
    background: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // PaytrService'den gelen iframe URL'ini direkt kullan
    loadPaytrIframe();
});

function loadPaytrIframe() {
    const paytrResponse = @json($paytrResponse);
    
    if (paytrResponse.success) {
        // İframe'i oluştur
        const iframe = document.createElement('iframe');
        iframe.id = 'paytr_iframe';
        iframe.src = paytrResponse.iframe_url;
        iframe.style.width = '100%';
        iframe.style.height = '600px';
        iframe.style.border = 'none';
        iframe.style.borderRadius = '0.375rem';
        
        // Container'ı güncelle
        const container = document.getElementById('paytr-iframe-container');
        container.innerHTML = '';
        container.appendChild(iframe);
        
        // Ödeme mesajlarını dinle
        window.addEventListener('message', function(event) {
            if (event.origin !== 'https://www.paytr.com') return;
            
            console.log('PayTR Message:', event.data);
            
            if (event.data === 'payment_success') {
                alert('Ödeme başarılı! Yönlendiriliyorsunuz...');
                window.location.href = '{{ route("storage.packages", $firma->id) }}?payment_check=1';
            } else if (event.data === 'payment_failed') {
                alert('Ödeme işlemi başarısız.');
                window.location.href = '{{ route("storage.packages", $firma->id) }}';
            }
        });
        
    } else {
        showError('PayTR Hatası', paytrResponse.error);
    }
}

function showError(title, message) {
    document.getElementById('paytr-iframe-container').innerHTML = `
        <div class="alert alert-danger text-center">
            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
            <h5>${title}</h5>
            <p>${message}</p>
            <a href="{{ route('storage.packages', $firma->id) }}" class="btn btn-primary">
                Geri Dön
            </a>
        </div>
    `;
}
</script>

@endsection