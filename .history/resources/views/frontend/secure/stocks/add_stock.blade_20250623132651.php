
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
  <label class="col-sm-4">Kategori<span class="text-danger"></span></label>
  <div class="col-sm-8">
    <select name="urunKategori" class="form-select">
      <option value="" disabled selected>- Seçiniz -</option>
      @foreach($kategoriler as $kategori)
        <option value="{{ $kategori->id }}">{{ $kategori->kategori }}</option>
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

  <div class="row mb-2">
    <label class="col-sm-4">Adet <span style="color:red;">*</span></label>
    <div class="col-sm-8">
      <input name="adet" type="number" min="1" class="form-control" required>
    </div>
  </div>

 
<div class="row mb-2">
    <label class="col-sm-4">Satış Fiyatı</label>
    <div class="col-sm-8">
      <div class="row g-2">
        <div class="col-8">
          <input name="fiyat" type="number" min="0" step="0.01" class="form-control" placeholder="Fiyat">
        </div>
        <div class="col-4">
          <select name="fiyatBirim" class="form-select">
            <option value="" disabled selected>Birim</option>
            <option value="1">TL</option>
            <option value="2">USD</option>
            <option value="3">EUR</option>
          </select>
        </div>
      </div>
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

  if (adet > 0 && fiyat > 0) {

    $('input[name="fiyatBirim"]').val((fiyat / adet).toFixed(2));
  } else {

    $('input[name="fiyatBirim"]').val('');
  }
}
  $('input[name="adet"], input[name="fiyat"], input[name="fiyatBirim"]').on('input', hesaplaFiyat);

</script>
<script>
 $('#addStock').submit(function(e) {
    e.preventDefault();

    var form = $(this);
    var formData = new FormData(this);

    // Zorunlu alan kontrolü (basit)
    var isValid = true;
    form.find('input[required], select[required]').each(function() {
      if (!$(this).val()) {
        isValid = false;
        return false;
      }
    });
    if (!isValid) {
      alert("Lütfen zorunlu alanları doldurun.");
      return;
    }

    $.ajax({
      url: form.attr('action'),
      method:'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(res) {
        if (res.status === 'exists' || res.status === 'exists_code') {
          toastr.warning(res.message);

          $.ajax({
            url: '/' + {{ $firma->id }} + '/stok/duzenle/' + res.stock_id,
            method: 'GET',
            success: function(editRes) {
              $('#editStockModal .modal-body').html(editRes.html);
              $('#editStockModal').modal('show');
            },
            error: function() {
              alert('Düzenleme formu yüklenirken hata oluştu.');
            }
          });

        } else if (res.status === 'success') {
          toastr.success(res.message);
          setTimeout(function() {
            window.location.href = "/{{ $firma->id }}/stoklar";
          }, 1000);
        }
      },
      error: function() {
        alert('Bir hata oluştu, lütfen tekrar deneyiniz.');
      }
    });
  });
});
</script>
