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

                    {{-- <div class="mb-3">
                        <label class="form-label">Paket Süresi</label>
                        <select name="duration" class="form-select" required>
                            <option value="1">1 Aylık</option>
                            <option value="12">12 Aylık</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kupon Kodu</label>
                        <div class="input-group">
                            <input type="text" name="coupon" class="form-control">
                            <button class="btn btn-outline-primary" type="button">Kupon Kodu Uygula</button>
                        </div>
                        <small class="text-muted">Kupon kodunu uyguladığınızda sayfa yenilenecektir.</small>
                    </div> --}}
                </div>

                <!-- Sağ taraf (Fatura Bilgileri) -->
                <div class="col-md-6">
                    <h5>Fatura Bilgileri</h5>

                    <!-- Bireysel / Kurumsal Seçimi zaten mevcut -->
<div class="btn-group mb-3" role="group">
    <input type="radio" class="btn-check" name="billing_type" id="bireysel" value="bireysel" checked>
    <label class="btn btn-outline-primary" for="bireysel">Bireysel</label>

    <input type="radio" class="btn-check" name="billing_type" id="kurumsal" value="kurumsal">
    <label class="btn btn-outline-primary" for="kurumsal">Kurumsal</label>
</div>

<!-- Bireysel alanları -->
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

<!-- Kurumsal alanları -->
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




            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">Sonraki Adım</button>
            </div>
        </form>
    </div>
</div>

<!-- JS kısmı -->
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

    // İlk durum
    toggleFields();

    // Radio değişiminde alanları güncelle
    bireyselRadio.addEventListener('change', toggleFields);
    kurumsalRadio.addEventListener('change', toggleFields);
});
</script>
@endsection
