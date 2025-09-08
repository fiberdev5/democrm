@extends('frontend.secure.user_master')

@section('user')
<div class="page-content">
    <div class="container-fluid">
        <h4>Ödeme</h4>

        <!-- Debug bilgileri (geliştirme aşamasında) -->
        {{-- @if(config('app.debug'))
        <div class="alert alert-info">
            <strong>Debug:</strong> Tenant ID: {{ $tenant_id }}, Plan ID: {{ $planid }}
        </div>
        @endif --}}

        <div class="row">
            <!-- Sol taraf - Sipariş Özeti -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Sipariş Özeti</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Paket Bilgileri</h6>
                                <p><strong>{{ $planData['name'] }}</strong></p>
                                <p>{{ $planData['price'] }} TL / {{ $planData['billing_cycle'] == 'monthly' ? 'Aylık' : 'Yıllık' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Fatura Bilgileri</h6>
                                <p><strong>{{ $billingData['first_name'] }}</strong></p>
                                <p>{{ $billingData['email'] }}</p>
                                <p>{{ $billingData['phone'] }}</p>
                                @if($billingData['billing_type'] == 'bireysel')
                                    <p>Bireysel Fatura</p>
                                    @if(isset($billingData['identity_number']))
                                        <p>TC: {{ $billingData['identity_number'] }}</p>
                                    @endif
                                @else
                                    <p>Kurumsal Fatura</p>
                                    @if(isset($billingData['tax_office']))
                                        <p>Vergi Dairesi: {{ $billingData['tax_office'] }}</p>
                                    @endif
                                    @if(isset($billingData['tax_number']))
                                        <p>Vergi No: {{ $billingData['tax_number'] }}</p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ödeme Formu -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Ödeme Bilgileri</h5>
                    </div>
                    <div class="card-body">
                        <!-- Paytr Ödeme Yöntemi -->
                        <div class="mb-4">
                            <div class="alert alert-info">
                                <i class="fas fa-credit-card me-2"></i>
                                <strong>Güvenli Ödeme</strong><br>
                                Ödemeniz Paytr güvenli ödeme sistemi ile 256-bit SSL şifrelemesi altında işlenir.
                                <br><small>Kredi Kartı, Banka Kartı ve diğer ödeme seçenekleri kullanabilirsiniz.</small>
                            </div>
                        </div>

                        <!-- Ödeme Kartları -->
                        <div class="mb-4 text-center">
                            <img src="{{asset('frontend/img/visa.jpg')}}" alt="Visa" height="30" class="me-2">
                            <img src="{{asset('frontend/img/masterpass.jpg')}}" alt="Mastercard" height="50" class="me-2">
                            <img src="{{asset('frontend/img/troy.jpg')}}" alt="Troy" height="30" class="me-2">
                        </div>

                        <!-- Sözleşme Onayı -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    <a href="#" target="_blank">Kullanım Koşulları</a>'nı ve 
                                    <a href="#" target="_blank">Gizlilik Politikası</a>'nı okudum, kabul ediyorum.
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('subscription.subscribe', [$tenant_id, $planid]) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Geri
                            </a>
                            <button type="button" id="payButton" class="btn btn-primary btn-lg">
                                <i class="fas fa-lock me-2"></i>Güvenli Ödemeye Geç
                            </button>
                        </div>
                        
                        <!-- Debug bilgileri -->
                        @if(config('app.debug'))
                        <div class="mt-3">
                            <small class="text-muted">
                                Paytr Route: {{ route('subscription.payment.initiate', [$tenant_id, $planid]) }}
                            </small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sağ taraf - Fiyat Özeti -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Ödeme Özeti</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Paket Ücreti:</span>
                            <span>{{ number_format($planData['price'], 2) }} TL</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>KDV (%20):</span>
                            <span>{{ number_format($planData['price'] * 0.20, 2) }} TL</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between h5">
                            <strong>Toplam:</strong>
                            <strong>{{ number_format($planData['price'] * 1.20, 2) }} TL</strong>
                        </div>
                        
                        <div class="mt-3 p-3 bg-light rounded">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Ödemeniz 256-bit SSL şifrelemesi ile güvence altındadır.
                            </small>
                        </div>

                        <div class="mt-2 p-3 bg-success text-white rounded text-center">
                            <small>
                                <i class="fas fa-check-circle me-1"></i>
                                Paytr Güvenli Ödeme Sistemi
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Payment page loaded'); // Debug için
    
    const payButton = document.getElementById('payButton');
    const termsCheckbox = document.getElementById('terms');
    
    if (!payButton) {
        console.error('Pay button not found!');
        return;
    }
    
    if (!termsCheckbox) {
        console.error('Terms checkbox not found!');
        return;
    }
    
    payButton.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('Pay button clicked'); // Debug için
        
        if (!termsCheckbox.checked) {
            alert('Lütfen kullanım koşullarını ve gizlilik politikasını kabul ediniz.');
            return;
        }
        
        // Loading göster
        const originalText = payButton.innerHTML;
        payButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Ödeme sayfasına yönlendiriliyor...';
        payButton.disabled = true;
        
        // URL oluştur
        const paymentUrl = '{{ route("subscription.payment.initiate", [$tenant_id, $planid]) }}';
        console.log('Redirecting to:', paymentUrl); // Debug için
        
        // Yönlendirme yap
        setTimeout(function() {
            window.location.href = paymentUrl;
        }, 1000);
        
        // Hata durumunda butonu eski haline getir (10 saniye sonra)
        setTimeout(function() {
            if (window.location.href.indexOf('payment/initiate') === -1) {
                payButton.innerHTML = originalText;
                payButton.disabled = false;
                console.log('Button reset - redirect may have failed');
            }
        }, 10000);
    });
});
</script>

@endsection