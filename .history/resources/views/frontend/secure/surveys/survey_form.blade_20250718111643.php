<form id="surveyForm">
    @csrf
    <input type="hidden" name="servisid" value="{{ $servis->id }}">

    @for ($i = 1; $i <= 5; $i++)
    <div class="mb-3">
        <label class="form-label">Soru {{ $i }}</label>
        <select name="soru{{ $i }}" class="form-select" required>
            <option value="">Seçiniz</option>
            <option value="1" @selected(optional($anket)->{"soru$i"} == 1)>Evet</option>
            <option value="2" @selected(optional($anket)->{"soru$i"} == 2)>Hayır</option>
            <option value="3" @selected(optional($anket)->{"soru$i"} == 3)>Belli Değil</option>
        </select>
        <input type="text" name="soru{{ $i }}Text" class="form-control mt-2" placeholder="Açıklama (isteğe bağlı)"
            value="{{ old("soru{$i}Text", optional($anket)->{"soru{$i}Text"}) }}">
    </div>
    @endfor

    <button type="submit" class="btn btn-success">Kaydet</button>
</form>
