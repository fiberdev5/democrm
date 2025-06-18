<form method="post" id="addStock" action="{{ route('store.stock', $firma->id) }}" enctype="multipart/form-data">
  @csrf
    <div class="row">
    <label class="col-sm-4">Markalar<span style="color:red;">*</span></label>
    <div class="col-sm-8">
      <select name="marka_id" class="form-select" required>
        <option value="" selected disabled>- Seçiniz -</option>
        @foreach($markalar as $marka)
          <option value="{{ $marka->id }}">{{ $marka->marka }}</option>
        @endforeach
      </select>
    </div>
  </div>

 <div class="row">
    <label class="col-sm-4 ">Cihaz Türleri<span style="color:red;">*</span></label>
    <div class="col-sm-8">
      <select name="cihaz_id" class="form-select" required>
        <option value="" selected disabled>- Seçiniz -</option>
        @foreach($cihazlar as $cihaz)
          <option value="{{ $cihaz->id }}">{{ $cihaz->cihaz }}</option>
        @endforeach
      </select>
    </div>
  </div>

    <div class="row">
    <label class="col-sm-4">Raf Seç<span style="color:red;">*</span></label>
    <div class="col-sm-8">
      <select name="raf_id" class="form-select" required>
        <option value="" selected disabled>- Seçiniz -</option>
        @foreach($rafListesi as $raf)
          <option value="{{ $raf->id }}">{{ $raf->raf_adi }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="row">
    <label class="col-sm-4">Ürün Kodu<span style="color:red;">*</span></label>
    <div class="col-sm-8">
      <input name="urunKodu" type="text" class="form-control" required>
    </div>
  </div>

  <div class="row">
    <label class="col-sm-4">Ürün Adı<span style="color:red;">*</span></label>
    <div class="col-sm-8">
      <input name="urunAdi" type="text" class="form-control" required>
    </div>
  </div>


  <!-- Adet -->
  <div class="row">
    <label class="col-sm-4">Adet <span style="color:red;">*</span></label>
    <div class="col-sm-8">
      <input name="adet" type="number" min="1" class="form-control" required>
    </div>
  </div>

<!-- Fiyat ve Birim -->
<div class="row mb-2">
  <label class="col-sm-4">Satış Fiyatı <span style="color:red;">*</span></label>
  <div class="col-sm-4">
    <input name="fiyat" type="number" min="0" step="0.01" class="form-control" required>
  </div>
  <div class="col-sm-4">
    <select name="fiyat_birim" class="form-select" required>
      <option value="TL">TL</option>
      <option value="USD">USD</option>
      <option value="EUR">EUR</option>
    </select>
  </div>
</div>


  <div class="row">
    <label class="col-sm-4">Açıklama</label>
    <div class="col-sm-8">
      <textarea name="description" rows="3" class="form-control"></textarea>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12 d-flex justify-content-end">
      <button type="submit" class="btn btn-primary">Kaydet</button>
    </div>
  </div>
</form>

<script>
  $(document).ready(function () {
    $('#addStock').submit(function(event) {
      var isValid = true;
      $(this).find('input, select').each(function() {
        if ($(this).prop('required') && !$(this).val()) {
          isValid = false;
          return false;
        }
      });

      if (!isValid) {
        event.preventDefault();
        alert('Lütfen zorunlu alanları doldurun.');
      }
    });
  });
</script>
