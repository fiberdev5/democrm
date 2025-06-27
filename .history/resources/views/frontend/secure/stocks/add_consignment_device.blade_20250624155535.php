<form method="POST" id="addConsignmentDevice" action="{{ route('store.consignment.device', $tenant_id) }}" enctype="multipart/form-data">
  @csrf

  <div class="row mb-2">
    <label class="col-sm-4 col-form-label">Markalar</label>
    <div class="col-sm-8">
      <select name="marka_id" class="form-select">
        <option value="" selected disabled>- Seçiniz -</option>
        @foreach($markalar as $marka)
          <option value="{{ $marka->id }}" {{ old('marka_id') == $marka->id ? 'selected' : '' }}>{{ $marka->marka }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4 col-form-label">Cihaz Türleri</label>
    <div class="col-sm-8">
      <select name="cihaz_id" class="form-select">
        <option value="" selected disabled>- Seçiniz -</option>
        @foreach($cihazlar as $cihaz)
          <option value="{{ $cihaz->id }}" {{ old('cihaz_id') == $cihaz->id ? 'selected' : '' }}>{{ $cihaz->cihaz }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4 col-form-label">Kategori <span class="text-danger">*</span></label>
    <div class="col-sm-8">
      <select name="urunKategori" class="form-select" required>
        <option value="" disabled selected>- Seçiniz -</option>
        @foreach($kategoriler as $kategori)
          <option value="{{ $kategori->id }}" {{ old('urunKategori') == $kategori->id ? 'selected' : '' }}>{{ $kategori->kategori }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4 col-form-label">Raf Seç</label>
    <div class="col-sm-8">
      <select name="raf_id" class="form-select">
        <option value="" selected disabled>- Seçiniz -</option>
        @foreach($rafListesi as $raf)
          <option value="{{ $raf->id }}" {{ old('raf_id') == $raf->id ? 'selected' : '' }}>{{ $raf->raf_adi }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4 col-form-label">Ürün Kodu <span class="text-danger">*</span></label>
    <div class="col-sm-8">
      <input name="urunKodu" type="text" class="form-control" value="{{ old('urunKodu') }}" required>
      <small class="text-danger">Ürün kodu tam 13 haneli olmalıdır.</small>
      @error('urunKodu') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4 col-form-label">Ürün Adı <span class="text-danger">*</span></label>
    <div class="col-sm-8">
      <input name="urunAdi" type="text" class="form-control" value="{{ old('urunAdi') }}" required>
      <div id="urunAdiUyari"></div>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4 col-form-label">Adet <span class="text-danger">*</span></label>
    <div class="col-sm-8">
      <input name="adet" type="number" min="1" class="form-control" value="{{ old('adet') ?? 1 }}" required>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4 col-form-label">Satış Fiyatı</label>
    <div class="col-sm-8">
      <div class="row g-2">
        <div class="col-8">
          <input name="fiyat" type="number" min="0" step="0.01" class="form-control" value="{{ old('fiyat') }}">
        </div>
        <div class="col-4">
          <select name="fiyatBirim" class="form-select">
            <option value="" disabled selected>Birim</option>
            <option value="1" {{ old('fiyatBirim') == 1 ? 'selected' : '' }}>TL</option>
            <option value="2" {{ old('fiyatBirim') == 2 ? 'selected' : '' }}>USD</option>
            <option value="3" {{ old('fiyatBirim') == 3 ? 'selected' : '' }}>EUR</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4 col-form-label">Açıklama</label>
    <div class="col-sm-8">
      <textarea name="aciklama" rows="3" class="form-control">{{ old('aciklama') }}</textarea>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-12 d-flex justify-content-end gap-2">
      <button type="submit" class="btn btn-primary">Kaydet</button>
      <a href="{{ route('consignmentdevice', $tenant_id) }}" class="btn btn-secondary">Geri</a>
    </div>
  </div>
</form>


<script>
$(document).ready(function () {
  $('#addConsignmentDevice').submit(function(event) {
    var urunKodu = $('input[name="urunKodu"]').val().trim();

    if (urunKodu.length !== 13) {
      event.preventDefault();
      alert('Ürün kodu tam 13 haneli olmalıdır!');
      $('input[name="urunKodu"]').focus();
      return false;
    }

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

  var tenantId = "{{ $tenant_id }}";
  var checkTimeout;

  $('input[name="urunAdi"]').on('input', function () {
    clearTimeout(checkTimeout);
    var urunAdi = $(this).val().trim();
    $('#urunAdiUyari').html(''); 

    if (urunAdi.length < 3) return;

    checkTimeout = setTimeout(function () {
      $.ajax({
        url: "/" + tenantId + "/stok/urun-adi-kontrol",
        method: "POST",
        data: {
          urunAdi: urunAdi,
          _token: "{{ csrf_token() }}"
        },
        success: function (res) {
          if (res.exists) {
            var warningHtml = '<div class="alert alert-warning mt-2">' +
                              'Bu ürün adı zaten mevcut. ' +
                              '<button id="openEditModalBtn" data-url="' + res.edit_url + '" class="btn btn-sm btn-primary ms-2">Ürünü Düzenle</button>' +
                              '</div>';
            $('#urunAdiUyari').html(warningHtml);
          } else {
            $('#urunAdiUyari').html('');
          }
        }
      });
    }, 600);
  });

  $(document).on('click', '#openEditModalBtn', function() {
    var url = $(this).data('url');

    $.ajax({
      url: url,
      type: 'GET',
      success: function(res) {
        if(res.html) {
          $('#editStockModal .modal-body').html(res.html);
          $('#editStockModal').modal('show');
        } else {
          alert('Düzenleme formu yüklenemedi.');
        }
      },
      error: function() {
        alert('Düzenleme formu yüklenirken hata oluştu.');
      }
    });
  });

});
</script>
