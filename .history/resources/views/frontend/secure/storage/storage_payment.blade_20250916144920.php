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
                                <img src="https://www.paytr.com/images/visa.png" alt="Visa" height="30">
                                <img src="https://www.paytr.com/images/mastercard.png" alt="Mastercard" height="30">
                                <img src="https://www.paytr.com/images/troy.png" alt="Troy" height="30">
                            </div>
                        </div>

                        <!-- PayTR İframe -->
                        <div class="payment-iframe-container" style="min-height: 600px;">
                            <form method="POST" action="https://www.paytr.com/odeme/guvenli/{{ $paytrData['merchant_id'] }}" id="paytr-form">
                                @foreach($paytrData as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                            </form>
                            
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Ödeme sayfası yükleniyor...</span>
                                </div>
                                <p class="mt-3 text-muted">Ödeme sayfası yükleniyor...</p>
                            </div>
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

.payment-iframe-container {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 20px;
    background-color: #f8f9fa;
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
    // PayTR iframe'ini yükle
    loadPaytrIframe();
});

function loadPaytrIframe() {
    const formData = new FormData(document.getElementById('paytr-form'));
    
    fetch('https://www.paytr.com/odeme/api/get-token', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Iframe'i oluştur ve yükle
            const iframe = document.createElement('iframe');
            iframe.id = 'paytr_iframe';
            iframe.src = 'https://www.paytr.com/odeme/' + data.token;
            
            // Loading indicator'ı kaldır ve iframe'i ekle
            const container = document.querySelector('.payment-iframe-container');
            container.innerHTML = '';
            container.appendChild(iframe);
            
            // Ödeme durumunu dinle
            window.addEventListener('message', function(event) {
                if (event.origin !== 'https://www.paytr.com') return;
                
                if (event.data === 'payment_success') {
                    window.location.href = '{{ route("storage.packages", $firma->id) }}?payment_check=1';
                } else if (event.data === 'payment_failed') {
                    window.location.href = '{{ route("storage.packages", $firma->id) }}';
                }
            });
            
        } else {
            // Hata durumu
            document.querySelector('.payment-iframe-container').innerHTML = `
                <div class="alert alert-danger text-center">
                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                    <h5>Ödeme Sayfası Yüklenemedi</h5>
                    <p>Ödeme sayfası yüklenirken bir hata oluştu. Lütfen tekrar deneyin.</p>
                    <a href="{{ route('storage.packages', $firma->id) }}" class="btn btn-primary">
                        Geri Dön
                    </a>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('PayTR yükleme hatası:', error);
        document.querySelector('.payment-iframe-container').innerHTML = `
            <div class="alert alert-danger text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <h5>Bağlantı Hatası</h5>
                <p>Ödeme sayfasına bağlanılamadı. İnternet bağlantınızı kontrol edin.</p>
                <button onclick="loadPaytrIframe()" class="btn btn-primary me-2">
                    <i class="fas fa-redo me-1"></i>Tekrar Dene
                </button>
                <a href="{{ route('storage.packages', $firma->id) }}" class="btn btn-outline-secondary">
                    Geri Dön
                </a>
            </div>
        `;
    });
}
</script>

@endsection