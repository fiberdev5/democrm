@extends('frontend.secure.user_master')

@section('user')
<div class="page-content">
    <div class="container-fluid">
        <h4>Ödeme Sayfası</h4>

        <form action="{{ route('subscription.process', [$tenant->id, $plan->id]) }}" method="POST">
            @csrf

            <div class="row">
                <div class="card">
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
                        <input type="radio" class="btn-check" name="billing_type" id="bireysel" value="bireysel" checked>
                        <label class="btn btn-outline-primary" for="bireysel">Bireysel</label>

                        <input type="radio" class="btn-check" name="billing_type" id="kurumsal" value="kurumsal">
                        <label class="btn btn-outline-primary" for="kurumsal">Kurumsal</label>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Adınız</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Soyadınız</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                    </div>

                    <!-- Bireysel Alanları -->
                    <div id="bireysel-fields">
                        <div class="mb-3">
                            <label class="form-label">TC Kimlik Numaranız</label>
                            <input type="text" name="identity_number" class="form-control">
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="foreign" id="foreign">
                                <label class="form-check-label" for="foreign">TC Uyruklu Değilim</label>
                            </div>
                        </div>
                    </div>

                    <!-- Kurumsal Alanları -->
                    <div id="kurumsal-fields" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label">Vergi Dairesi</label>
                            <input type="text" name="tax_office" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vergi Numarası</label>
                            <input type="text" name="tax_number" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">E-Posta Adresiniz</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telefon Numaranız</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">İl</label>
                            <select name="city" class="form-select" required>
                                <option value="">İl seç</option>
                                <option value="İstanbul">İstanbul</option>
                                <option value="Ankara">Ankara</option>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">İlçe</label>
                            <input type="text" name="district" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mahalle</label>
                        <input type="text" name="neighborhood" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adres</label>
                        <textarea name="address" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">Sonraki Adım</button>
            </div>
        </form>
    </div>
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

    toggleFields();

    bireyselRadio.addEventListener('change', toggleFields);
    kurumsalRadio.addEventListener('change', toggleFields);
});
</script>
@endsection
