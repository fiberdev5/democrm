<!-- resources/views/backend/surveys/survey_form.blade.php -->
<form id="surveyForm" action="{{ route('store.survey') }}" method="POST">
    @csrf
    <input type="hidden" name="servisid" value="{{ $service->id }}">
    
    <!-- Servis Bilgileri -->
    <div class="row mb-3">
        <div class="col-md-6">
            <strong>Servis No:</strong> {{ $service->id }}
        </div>
        <div class="col-md-6">
            <strong>Müşteri:</strong> {{ $service->musteri_adi ?? 'Belirtilmemiş' }}
        </div>
    </div>

    <!-- Bayi/Teknisyen Seçimi -->
    <div class="row form-group">
        <div class="col-md-6">
            <label>Bayi Seçin:</label>
            <select class="form-control" name="bayi" id="bayiSelect">
                <option value="0">Merkez Personeli</option>
                @foreach($dealers as $dealer)
                    <option value="{{ $dealer->id }}" {{ (isset($existingSurvey) && $existingSurvey->bayi == $dealer->id) ? 'selected' : '' }}>
                        {{ $dealer->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6" id="teknisyenDiv" style="{{ (isset($existingSurvey) && $existingSurvey->bayi > 0) ? 'display:none' : '' }}">
            <label>Teknisyen:</label>
            <select class="form-control" name="teknisyen">
                <option value="">Seçin</option>
                @foreach($personels as $personel)
                    <option value="{{ $personel->id }}" {{ (isset($existingSurvey) && $existingSurvey->personel == $personel->id) ? 'selected' : '' }}>
                        {{ $personel->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Soru 1 -->
    <div class="row form-group">
        <div class="col-12">
            <label>Teknisyen dediği saatte geldi mi?</label>
        </div>
        <div class="col-md-3">
            <select class="form-control" name="soru1" required>
                <option value="0" {{ (isset($existingSurvey) && $existingSurvey->soru1 == 0) ? 'selected' : '' }}>Belli Değil</option>
                <option value="1" {{ (isset($existingSurvey) && $existingSurvey->soru1 == 1) ? 'selected' : '' }}>Evet</option>
                <option value="2" {{ (isset($existingSurvey) && $existingSurvey->soru1 == 2) ? 'selected' : '' }}>Hayır</option>
            </select>
        </div>
        <div class="col-md-9">
            <input type="text" class="form-control" name="soru1Text" placeholder="Açıklama" value="{{ $existingSurvey->soru1Text ?? '' }}">
        </div>
    </div>

    <!-- Soru 2 -->
    <div class="row form-group">
        <div class="col-12">
            <label>Teknisyen davranışlarından, kılık ve kıyafetlerinden memnun musunuz?</label>
        </div>
        <div class="col-md-3">
            <select class="form-control" name="soru2" required>
                <option value="0" {{ (isset($existingSurvey) && $existingSurvey->soru2 == 0) ? 'selected' : '' }}>Belli Değil</option>
                <option value="1" {{ (isset($existingSurvey) && $existingSurvey->soru2 == 1) ? 'selected' : '' }}>Evet</option>
                <option value="2" {{ (isset($existingSurvey) && $existingSurvey->soru2 == 2) ? 'selected' : '' }}>Hayır</option>
            </select>
        </div>
        <div class="col-md-9">
            <input type="text" class="form-control" name="soru2Text" placeholder="Açıklama" value="{{ $existingSurvey->soru2Text ?? '' }}">
        </div>
    </div>

    <!-- Soru 3 -->
    <div class="row form-group">
        <div class="col-12">
            <label>Teknisyen cihazınızla yeterince ilgilendi mi?</label>
        </div>
        <div class="col-md-3">
            <select class="form-control" name="soru3" required>
                <option value="0" {{ (isset($existingSurvey) && $existingSurvey->soru3 == 0) ? 'selected' : '' }}>Belli Değil</option>
                <option value="1" {{ (isset($existingSurvey) && $existingSurvey->soru3 == 1) ? 'selected' : '' }}>Evet</option>
                <option value="2" {{ (isset($existingSurvey) && $existingSurvey->soru3 == 2) ? 'selected' : '' }}>Hayır</option>
            </select>
        </div>
        <div class="col-md-9">
            <input type="text" class="form-control" name="soru3Text" placeholder="Açıklama" value="{{ $existingSurvey->soru3Text ?? '' }}">
        </div>
    </div>

    <!-- Soru 4 - Ücret -->
    <div class="row form-group">
        <div class="col-12">
            <label>Sizden Talep Edilen Ücret</label>
        </div>
        <div class="col-12">
            <input type="text" class="form-control" name="soru4Text" placeholder="0.00" value="{{ $existingSurvey->soru4Text ?? '' }}" onkeyup="sayiKontrol(this)">
        </div>
    </div>

    <!-- Soru 5 -->
    <div class="row form-group">
        <div class="col-12">
            <label>Genel olarak servis hizmetimizden memnun musunuz?</label>
        </div>
        <div class="col-md-3">
            <select class="form-control" name="soru5" required>
                <option value="0" {{ (isset($existingSurvey) && $existingSurvey->soru5 == 0) ? 'selected' : '' }}>Belli Değil</option>
                <option value="1" {{ (isset($existingSurvey) && $existingSurvey->soru5 == 1) ? 'selected' : '' }}>Evet</option>
                <option value="2" {{ (isset($existingSurvey) && $existingSurvey->soru5 == 2) ? 'selected' : '' }}>Hayır</option>
            </select>
        </div>
        <div class="col-md-9">
            <input type="text" class="form-control" name="soru5Text" placeholder="Açıklama" value="{{ $existingSurvey->soru5Text ?? '' }}">
        </div>
    </div>

    <!-- Form Butonları -->
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
        <button type="submit" class="btn btn-primary">{{ isset($existingSurvey) ? 'Güncelle' : 'Kaydet' }}</button>
    </div>
</form>

<script>
// Bayi seçim kontrolü
$('#bayiSelect').on('change', function() {
    if ($(this).val() == '0') {
        $('#teknisyenDiv').show();
    } else {
        $('#teknisyenDiv').hide();
    }
});

// Sayı kontrol fonksiyonu
function sayiKontrol(input) {
    let value = input.value;
    value = value.replace(/[^0-9.,]/g, '');
    input.value = value;
}
</script>