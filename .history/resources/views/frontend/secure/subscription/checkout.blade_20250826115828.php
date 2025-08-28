@extends('frontend.secure.user_master')

@section('user')
<div class="page-content">
    <div class="container-fluid">
        <h4>Ödeme Sayfası</h4>

        <form action="{{ route('subscription.process', [$tenant->id, $plan->id]) }}" method="POST">
            @csrf

            <div class="row">
                <!-- Sol taraf (Paket Bilgileri) -->
                <div class="col-md-6">
                    <h5>Paket Bilgileri</h5>
                    <div class="card">
                        <div class="card-body">
                            <p><strong>Paket Adı:</strong> {{ $plan->name }}</p>
                            <p><strong>Fiyat:</strong> {{ $plan->getFormattedPrice() }} / {{ $plan->getBillingCycleText() }}</p>
                            <p><strong>Açıklama:</strong> {!! $plan->description !!}</p>
                        </div>
                    </div>
                </div>

                <!-- Sağ taraf (Fatura Bilgileri) -->
                <div class="col-md-6">
                    <h5>Fatura Bilgileri</h5>

                    <!-- Bireysel / Kurumsal Seçimi -->
                    <div class="btn-group mb-3" role="group">
                        <input type="radio" class="btn-check" name="billing_type" id="bireysel" value="bireysel">
                        <label class="btn btn-outline-primary" for="bireysel">Bireysel</label>

                        <input type="radio" class="btn-check" name="billing_type" id="kurumsal" value="kurumsal">
                        <label class="btn btn-outline-primary" for="kurumsal">Kurumsal</label>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Adınız</label>
                            <input type="text" name="first_name" class="form-control" 
                                value="{{ old('first_name', $tenant->first_name ?? '') }}" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Soyadınız</label>
                            <input type="text" name="last_name" class="form-control" 
                                value="{{ old('last_name', $tenant->last_name ?? '') }}" required>
                        </div>
                    </div>

                    <!-- Bireysel Alanları -->
                    <div id="bireysel-fields">
                        <div class="mb-3">
                            <label class="form-label">TC Kimlik Numaranız</label>
                            <input type="text" name="identity_number" class="form-control" 
                                value="{{ old('identity_number', $tenant->identity_number ?? '') }}">
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="foreign" id="foreign"
                                    {{ (isset($tenant->foreign) && $tenant->foreign) ? 'checked' : '' }}>
                                <label class="form-check-label" for="foreign">TC Uyruklu Değilim</label>
                            </div>
                        </div>
                    </div>

                    <!-- Kurumsal Alanları -->
                    <div id="kurumsal-fields" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label">Vergi Dairesi</label>
                            <input type="text" name="tax_office" class="form-control" 
                                value="{{ old('tax_office', $tenant->tax_office ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vergi Numarası</label>
                            <input type="text" name="tax_number" class="form-control" 
                                value="{{ old('tax_number', $tenant->tax_number ?? '') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">E-Posta Adresiniz</label>
                        <input type="email" name="email" class="form-control" 
                            value="{{ old('email', $tenant->email ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telefon Numaranız</label>
                        <input type="text" name="phone" class="form-control" 
                            value="{{ old('phone', $tenant->phone ?? '') }}" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">İl</label>
                            <select name="city" class="form-select" required>
                                <option value="">İl seç</option>
                                <option value="İstanbul" {{ (old('city', $tenant->city ?? '') == 'İstanbul') ? 'selected' : '' }}>İstanbul</option>
                                <option value="Ankara" {{ (old('city', $tenant->city ?? '') == 'Ankara') ? 'selected' : '' }}>Ankara</option>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">İlçe</label>
                            <input type="text" name="district" class="form-control" 
                                value="{{ old('district', $tenant->district ?? '') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mahalle</label>
                        <input type="text" name="neighborhood" class="form-control" 
                            value="{{ old('neighborhood', $tenant->neighborhood ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adres</label>
                        <textarea name="address" class="form-control" rows="3">{{ old('address', $tenant->address ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">Sonraki Adım</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const bireyselRadio = document.getElementById('bireysel');
    const kurumsalRadio = document.getElementById('kurumsal');
    const bireyselFields = document.getElementById('bireysel-fields');
    const kurumsalFields = document.getElementById('kurumsal-fields');

    function toggleFields() {
        if (bireyselRadio.checked) {
            bireyselFields.style.display = 'block';
            kurumsalFields.style.display = 'none';
        } else {
            bireyselFields.style.display = 'none';
            kurumsalFields.style.display = 'block';
        }
    }

    // Tenant verisine göre radio seçili gelsin
    @if(isset($tenant->tax_number) || isset($tenant->tax_office))
        kurumsalRadio.checked = true;
    @else
        bireyselRadio.checked = true;
    @endif

    toggleFields();

    bireyselRadio.addEventListener('change', toggleFields);
    kurumsalRadio.addEventListener('change', toggleFields);
});
</script>
@endsection
