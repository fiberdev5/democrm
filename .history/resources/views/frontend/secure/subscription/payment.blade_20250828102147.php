@extends('frontend.secure.user_master')

@section('user')
<div class="page-content">
    <div class="container-fluid">
        <h4>Ödeme</h4>

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
                        <form method="POST" id="processPay" action="https://vpostest.qnb.com.tr/Gateway/Default.aspx" target="_parent">                            @csrf
                            @php
    $rnd = microtime();
    $fiyat = $planData['price'];
    $MbrId = "5";
    $MerchantID = "085300000009597";
    $OrderId = "346";
    $OkUrl = "https://www.serbiscrm.com";
    $FailUrl = "https://www.serbiscrm.com";
    $TxnType = "Auth";
    $InstallmentCount = "0";
    $UserPass = "12345678";

    $hashstr = $MbrId . $MerchantID . $OrderId . $fiyat . $OkUrl . $FailUrl . $TxnType . $InstallmentCount . $rnd . $UserPass;
    $hash = base64_encode(pack('H*', sha1($hashstr)));
@endphp

                            <input type="hidden" name="MbrId" value="5">
                            <input type="hidden" name="MerchantID" value="{{$MerchantID}}">
                            <input type="hidden" name="UserCode" value="QNB_ISYERI_KULLANICI">
                            <input type="hidden" name="UserPass" value="9ZPar">
                            <input type="hidden" name="SecureType" value="3DPay">
                            <input type="hidden" name="TxnType" value="Auth">
                            <input type="hidden" name="InstallmentCount" value="0">
                            <input type="hidden" name="Currency" value="949">   {{--Kur bilgisi. (TL:949, USD:840, EUR:978, GBP:826, JPY:392, RUB:643) --}}
                            <input type="hidden" name="OkUrl" value="{{route('subscription.success', $tenant->id)}}">
                            <input type="hidden" name="FailUrl" value="{{route('subscription.fail', $tenant->id)}}">
                            <input type="hidden" name="OrderId" value="{{$OrderId}}">
                            <input type="hidden" name="PurchAmount" value="1">
                            <input type="hidden" name="Lang" value="TR">
                            <input type="hidden" name="Rnd" value="{{ $rnd }}">
                            <input type="hidden" name="Hash" value="{{ $hash }}">

                            <!-- Ödeme Yöntemi Seçimi -->
                            <div class="mb-4">
                                <label class="form-label">Ödeme Yöntemi</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="payment_method" id="credit_card" value="credit_card" checked>
                                    <label class="btn btn-outline-primary" for="credit_card">
                                        <i class="fas fa-credit-card me-2"></i>Kredi Kartı
                                    </label>

                                    <input type="radio" class="btn-check" name="payment_method" id="bank_transfer" value="bank_transfer">
                                    <label class="btn btn-outline-primary" for="bank_transfer">
                                        <i class="fas fa-university me-2"></i>Havale/EFT
                                    </label>
                                </div>
                            </div>

                            <!-- Kredi Kartı Alanları -->
                            <div id="credit-card-fields">
                                <div class="mb-3">
                                    <label class="form-label">Kart Üzerindeki İsim</label>
                                    <input type="text" name="card_holder" class="form-control" 
                                        value="{{ old('card_holder', $billingData['first_name']) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Kart Numarası</label>
                                    <input type="text" name="card_number" class="form-control" 
                                        placeholder="1234 5678 9012 3456" maxlength="19">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Son Kullanma Tarihi</label>
                                            <input type="text" name="card_expiry" class="form-control" 
                                                placeholder="MM/YY" maxlength="5">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">CVC</label>
                                            <input type="text" name="card_cvc" class="form-control" 
                                                placeholder="123" maxlength="4">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Havale Bilgileri -->
                            <div id="bank-transfer-info" style="display: none;">
                                <div class="alert alert-info">
                                    <h6>Havale/EFT Bilgileri</h6>
                                    <p><strong>Banka:</strong> Örnek Banka</p>
                                    <p><strong>IBAN:</strong> TR00 0000 0000 0000 0000 0000 00</p>
                                    <p><strong>Hesap Sahibi:</strong> Şirket Adı</p>
                                    <p class="mb-0"><small>Havale/EFT sonrası aboneliğiniz 1-2 iş günü içinde aktifleştirilecektir.</small></p>
                                </div>
                            </div>

                            <!-- Sözleşme Onayı -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input type="checkbox" name="terms_accepted" class="form-check-input" id="terms" required>
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
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-lock me-2"></i>Ödemeyi Tamamla
                                </button>
                            </div>
                        </form>
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
                                <i class="fas fa-info-circle me-1"></i>
                                Ödeme güvenli SSL şifrelemesi ile korunmaktadır.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const creditCardRadio = document.getElementById('credit_card');
    const bankTransferRadio = document.getElementById('bank_transfer');
    const creditCardFields = document.getElementById('credit-card-fields');
    const bankTransferInfo = document.getElementById('bank-transfer-info');

    function togglePaymentMethod() {
        if (creditCardRadio.checked) {
            creditCardFields.style.display = 'block';
            bankTransferInfo.style.display = 'none';
            // Kredi kartı alanlarını zorunlu yap
            document.querySelectorAll('#credit-card-fields input').forEach(input => {
                input.required = true;
            });
        } else {
            creditCardFields.style.display = 'none';
            bankTransferInfo.style.display = 'block';
            // Kredi kartı alanlarını zorunlu yapma
            document.querySelectorAll('#credit-card-fields input').forEach(input => {
                input.required = false;
            });
        }
    }

    creditCardRadio.addEventListener('change', togglePaymentMethod);
    bankTransferRadio.addEventListener('change', togglePaymentMethod);

    // Kart numarası formatı
    const cardNumberInput = document.querySelector('input[name="card_number"]');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });
    }

    // Son kullanma tarihi formatı
    const cardExpiryInput = document.querySelector('input[name="card_expiry"]');
    if (cardExpiryInput) {
        cardExpiryInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
    }

    // CVC sadece sayı
    const cardCvcInput = document.querySelector('input[name="card_cvc"]');
    if (cardCvcInput) {
        cardCvcInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
    }
});
</script>
@endsection