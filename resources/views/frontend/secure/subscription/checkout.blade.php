@extends('frontend.secure.user_master')

@section('user')
<div class="page-content">
    <div class="container-fluid">
        <h4>Fatura Bilgileri</h4>

        <form action="{{ route('subscription.process', [$tenant_id, $planid]) }}" method="POST">
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
                                value="{{ old('first_name', $tenant->name ?? '') }}" required>
                        </div>
                    </div>

                    <!-- Bireysel Alanları -->
                    <div id="bireysel-fields">
                        <div class="mb-3">
                            <label class="form-label">TC Kimlik Numaranız</label>
                            <input type="text" name="identity_number" class="form-control" 
                                value="{{ old('identity_number', $tenant->tcNo ?? '') }}">
                        </div>
                    </div>

                    <!-- Kurumsal Alanları -->
                    <div id="kurumsal-fields" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label">Vergi Dairesi</label>
                            <input type="text" name="tax_office" class="form-control" 
                                value="{{ old('tax_office', $tenant->vergiDairesi ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vergi Numarası</label>
                            <input type="text" name="tax_number" class="form-control" 
                                value="{{ old('tax_number', $tenant->vergiNo ?? '') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">E-Posta Adresiniz</label>
                        <input type="email" name="email" class="form-control" 
                            value="{{ old('email', $tenant->eposta ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telefon Numaranız</label>
                        <input type="text" name="phone" class="form-control" 
                            value="{{ old('phone', $tenant->tel1 ?? '') }}" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">İl</label>
                            <select name="il" id="sehirSelect"  class="form-select" required>
                                <option value="">İl seç</option>
                                @foreach($countries as $item)
                                    <option value="{{ $item->id }}" {{ $tenant->il == $item->id ? 'selected' : ''}}>{{ $item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label">İlçe</label>
                            <select name="ilce" id="ilceSelect" class="form-control form-select" style="width:100%!important;">
                                <option value="" selected >-Seçiniz-</option>                              
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adres</label>
                        <textarea name="address" class="form-control" rows="3">{{ old('address', $tenant->adres ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-end">
                <a href="{{ route('subscription.plans', $tenant->id) }}" class="btn btn-secondary me-2">Geri</a>
                <button type="submit" class="btn btn-primary">Ödeme Sayfasına Geç</button>
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

<script>
$(document).ready(function() {
    var selectedCountryId = {{ $tenant->il == '' ? '0' : $tenant->il }};
    if(selectedCountryId){
        $.get("/get-states/" + selectedCountryId, function(data) {
            $.each(data, function(index, city) {
                $('#ilceSelect').append(new Option(city.ilceName, city.id));
                if(city.id == {{ $tenant->ilce == '' ? '0' : $tenant->ilce}}){
                    $("#ilceSelect").val(city.id).change();
                } 
            });
        });
    }
    
    // Ülke seçildiğinde
    $("#sehirSelect").change(function() {
        var selectedCountryId = $(this).val();
        // Şehirleri getir ve ikinci select'i güncelle
        $.get("/get-states/" + selectedCountryId, function(data) {
            var citySelect = $("#ilceSelect");
            citySelect.empty(); // Önceki seçenekleri temizle
            $.each(data, function(index, city) {
                citySelect.append(new Option(city.ilceName, city.id));
            });
        });
    });
});
</script>
@endsection