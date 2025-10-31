@extends('frontend.secure.user_master')
@section('user')

<style>
.purchase-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 30px 15px;
}

.purchase-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
    text-align: center;
}

.purchase-header h1 {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 10px;
    color: white;
}

.purchase-header p {
    font-size: 16px;
    opacity: 0.9;
    margin: 0;
}

.order-summary {
    background: white;
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.order-summary h3 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #212529;
    display: flex;
    align-items: center;
    gap: 10px;
}

.integration-summary {
    display: flex;
    gap: 20px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 20px;
}

.integration-summary-logo {
    width: 80px;
    height: 80px;
    object-fit: contain;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 10px;
    background: white;
}

.integration-summary-info {
    flex: 1;
}

.integration-summary-name {
    font-size: 18px;
    font-weight: 600;
    color: #212529;
    margin-bottom: 8px;
}

.integration-summary-desc {
    font-size: 14px;
    color: #6c757d;
    margin-bottom: 10px;
}

.integration-summary-category {
    display: inline-block;
    padding: 4px 10px;
    background: #667eea;
    color: white;
    font-size: 12px;
    border-radius: 5px;
    font-weight: 500;
}

.price-details {
    border-top: 2px solid #e9ecef;
    padding-top: 20px;
}

.price-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    font-size: 15px;
}

.price-row.total {
    border-top: 2px solid #e9ecef;
    margin-top: 10px;
    padding-top: 15px;
    font-size: 18px;
    font-weight: 700;
    color: #212529;
}

.price-row.total .price-value {
    color: #667eea;
    font-size: 24px;
}

.payment-section {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.payment-section h3 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #212529;
    display: flex;
    align-items: center;
    gap: 10px;
}

.payment-iframe-container {
    min-height: 400px;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    overflow: hidden;
    background: #f8f9fa;
}

.payment-iframe-container iframe {
    width: 100%;
    min-height: 500px;
    border: none;
}

.secure-badge {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    background: #e7f5ff;
    border-left: 4px solid #0066cc;
    border-radius: 8px;
    margin-bottom: 20px;
}

.secure-badge i {
    color: #0066cc;
    font-size: 20px;
}

.secure-badge-text {
    flex: 1;
}

.secure-badge-text strong {
    display: block;
    color: #0066cc;
    margin-bottom: 3px;
}

.secure-badge-text span {
    font-size: 13px;
    color: #495057;
}

.loading-spinner {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    color: #6c757d;
}

.loading-spinner .spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.features-list {
    list-style: none;
    padding: 0;
    margin: 20px 0;
}

.features-list li {
    padding: 8px 0;
    display: flex;
    align-items: center;
    font-size: 14px;
    color: #495057;
}

.features-list li i {
    color: #28a745;
    margin-right: 10px;
    font-size: 16px;
}

@media (max-width: 768px) {
    .integration-summary {
        flex-direction: column;
        text-align: center;
    }
    
    .integration-summary-logo {
        margin: 0 auto;
    }
}
</style>

<div class="page-content">
    <div class="container-fluid">
        <div class="purchase-container">
            <!-- Header -->
            <div class="purchase-header">
                <h1><i class="fas fa-shopping-cart"></i> Entegrasyon Satın Al</h1>
                <p>Güvenli ödeme ile entegrasyonunuzu hemen aktifleştirin</p>
            </div>

            <!-- Sipariş Özeti -->
            <div class="order-summary">
                <h3>
                    <i class="fas fa-receipt"></i>
                    Sipariş Özeti
                </h3>

                <div class="integration-summary">
                    @if($integration->logo)
                    <img src="{{ asset('frontend/' . $integration->logo) }}" alt="{{ $integration->name }}" class="integration-summary-logo">
                    @else
                    <div class="integration-summary-logo d-flex align-items-center justify-content-center">
                        <i class="fas fa-puzzle-piece fa-2x text-muted"></i>
                    </div>
                    @endif

                    <div class="integration-summary-info">
                        <div class="integration-summary-name">{{ $integration->name }}</div>
                        <div class="integration-summary-desc">{{ $integration->description }}</div>
                        <span class="integration-summary-category">
                            @if($integration->category == 'invoice')
                                Fatura Entegrasyonu
                            @elseif($integration->category == 'sms')
                                SMS Entegrasyonu
                            @elseif($integration->category == 'accounting')
                                Muhasebe Entegrasyonu
                            @else
                                Entegrasyon
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Özellikler -->
                <ul class="features-list">
                    <li><i class="fas fa-check-circle"></i> 1 Yıllık Kullanım Hakkı</li>
                    <li><i class="fas fa-check-circle"></i> Sınırsız API Çağrısı</li>
                    <li><i class="fas fa-check-circle"></i> 7/24 Teknik Destek</li>
                    <li><i class="fas fa-check-circle"></i> Otomatik Güncellemeler</li>
                    <li><i class="fas fa-check-circle"></i> Detaylı Dokümantasyon</li>
                </ul>

                <!-- Fiyat Detayları -->
                <div class="price-details">
                    <div class="price-row">
                        <span>Entegrasyon Ücreti</span>
                        <span class="price-value">₺{{ number_format($integration->price, 2) }}</span>
                    </div>
                    <div class="price-row">
                        <span>KDV (%20)</span>
                        <span class="price-value">₺{{ number_format($integration->price * 0.20, 2) }}</span>
                    </div>
                    <div class="price-row total">
                        <span>Toplam Tutar</span>
                        <span class="price-value">₺{{ number_format($integration->price * 1.20, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Ödeme Bölümü -->
            <div class="payment-section">
                <h3>
                    <i class="fas fa-credit-card"></i>
                    Ödeme Bilgileri
                </h3>

                <div class="secure-badge">
                    <i class="fas fa-shield-alt"></i>
                    <div class="secure-badge-text">
                        <strong>Güvenli Ödeme</strong>
                        <span>256-bit SSL şifreleme ile korunan güvenli ödeme altyapısı</span>
                    </div>
                </div>

                <div class="payment-iframe-container" id="paymentIframeContainer">
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                        <p>Ödeme formu yükleniyor...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // PayTR ödeme iframe'ini yükle
    loadPaymentIframe();
});

function loadPaymentIframe() {
    $.ajax({
        url: '{{ route("tenant.integrations.paytr.prepare", [$tenant->id, $integration->id]) }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            integration_id: {{ $integration->id }},
            tenant_id: {{ $tenant->id }}
        },
        success: function(response) {
            if (response.success) {
                $('#paymentIframeContainer').html(response.iframe);
            } else {
                showError(response.message || 'Ödeme formu yüklenirken bir hata oluştu.');
            }
        },
        error: function(xhr) {
            console.error('PayTR Error:', xhr);
            showError('Ödeme formu yüklenirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.');
        }
    });
}

function showError(message) {
    $('#paymentIframeContainer').html(`
        <div class="alert alert-danger" style="margin: 20px;">
            <i class="fas fa-exclamation-triangle"></i> ${message}
            <br><br>
            <button class="btn btn-primary" onclick="loadPaymentIframe()">
                <i class="fas fa-redo"></i> Tekrar Dene
            </button>
        </div>
    `);
}

// PayTR callback fonksiyonu
function payment_success(reference_no) {
    // Ödeme başarılı
    window.location.href = '{{ route("tenant.integrations.paytr.success", [$tenant->id, $integration->id]) }}?reference=' + reference_no;
}

function payment_failed() {
    // Ödeme başarısız
    Swal.fire({
        icon: 'error',
        title: 'Ödeme Başarısız',
        text: 'Ödeme işlemi tamamlanamadı. Lütfen tekrar deneyin.',
        confirmButtonText: 'Tamam'
    }).then(() => {
        window.location.href = '{{ route("tenant.integrations.show", [$tenant->id, $integration->slug]) }}';
    });
}
</script>

@endsection