<!-- 1. EKLEME FORMU DÜZELTİLMİŞ HALİ -->
<form method="post" id="addStock" action="{{ route('store.stock', $firma->id) }}" enctype="multipart/form-data">
  @csrf
  <div class="row mb-2">
    <label class="col-sm-4">Markalar</label>
    <div class="col-sm-8">
      <select name="marka_id" class="form-select">
        <option value="" selected disabled>- Seçiniz -</option>
        @foreach($markalar as $marka)
          <option value="{{ $marka->id }}">{{ $marka->marka }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Cihaz Türleri</label>
    <div class="col-sm-8">
      <select name="cihaz_id" class="form-select">
        <option value="" selected disabled>- Seçiniz -</option>
        @foreach($cihazlar as $cihaz)
          <option value="{{ $cihaz->id }}">{{ $cihaz->cihaz }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Raf Seç</label>
    <div class="col-sm-8">
      <select name="raf_id" class="form-select">
        <option value="" selected disabled>- Seçiniz -</option>
        @foreach($rafListesi as $raf)
          <option value="{{ $raf->id }}">{{ $raf->raf_adi }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Ürün Kodu<span style="color:red;">*</span></label>
    <div class="col-sm-8">
      <input name="urunKodu" type="text" class="form-control" required>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Ürün Adı<span style="color:red;">*</span></label>
    <div class="col-sm-8">
      <input name="urunAdi" type="text" class="form-control" required>
    </div>
  </div>

  <!-- Adet -->
  <div class="row mb-2">
    <label class="col-sm-4">Adet <span style="color:red;">*</span></label>
    <div class="col-sm-8">
      <input name="adet" type="number" min="1" class="form-control" required>
    </div>
  </div>

  <!-- DÜZELTİLEN KISIM: Fiyat alanları -->
  <div class="row mb-2">
    <label class="col-sm-4">Alış Fiyatı (Toplam)</label>
    <div class="col-sm-8">
      <input name="fiyat" type="number" min="0" step="0.01" class="form-control" placeholder="Toplam alış fiyatı">
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Birim Fiyat</label>
    <div class="col-sm-8">
      <input name="fiyatBirim" type="number" min="0" step="0.01" class="form-control" placeholder="Adet başına fiyat">
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Açıklama</label>
    <div class="col-sm-8">
      <textarea name="aciklama" rows="3" class="form-control"></textarea>
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
  // Otomatik hesaplama fonksiyonu
  function hesaplaFiyat() {
    var adet = parseFloat($('input[name="adet"]').val()) || 0;
    var fiyat = parseFloat($('input[name="fiyat"]').val()) || 0;
    var fiyatBirim = parseFloat($('input[name="fiyatBirim"]').val()) || 0;

    // Eğer adet ve toplam fiyat girilmişse, birim fiyatı hesapla
    if (adet > 0 && fiyat > 0 && fiyatBirim == 0) {
      $('input[name="fiyatBirim"]').val((fiyat / adet).toFixed(2));
    }
    // Eğer adet ve birim fiyat girilmişse, toplam fiyatı hesapla
    else if (adet > 0 && fiyatBirim > 0 && fiyat == 0) {
      $('input[name="fiyat"]').val((adet * fiyatBirim).toFixed(2));
    }
  }

  // Input değişikliklerinde hesapla
  $('input[name="adet"], input[name="fiyat"], input[name="fiyatBirim"]').on('input', hesaplaFiyat);

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
