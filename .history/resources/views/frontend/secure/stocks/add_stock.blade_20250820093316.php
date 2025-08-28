
<form method="post" id="addStock" action="{{ route('store.stock', $firma->id) }}" enctype="multipart/form-data">
  @csrf
  <div class="row mb-2">
    <label class="col-sm-4">Markalar</label>
    <div class="col-sm-8">
      <div class="input-group">
        <select name="marka_id" class="form-select">
          <option value="" selected disabled>- Seçiniz -</option>
          @foreach($markalar as $marka)
            <option value="{{ $marka->id }}">{{ $marka->marka }}</option>
          @endforeach
        </select>
        <button class="btn btn-success" type="button"  id="addNewBrandBtn">+</button>
      </div>
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
  <label class="col-sm-4">Kategori<span style="color:red;">*</span></label>
  <div class="col-sm-8">
    <select name="urunKategori" class="form-select"  required>
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
    <label class="col-sm-4 ">Ürün Kodu <span class="text-danger">*</span></label>
    <div class="col-sm-8">
        <input name="urunKodu" type="text" class="form-control" 
               value="{{ old('urunKodu') }}" 
               placeholder="0000000000000" 
               data-mask="000-0000000000" required>
        <small class="text-danger">Ürün kodu 13 haneli olmalıdır.</small>
        @error('urunKodu') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
</div>


  <div class="row mb-2">
    <label class="col-sm-4 ">Ürün Adı <span class="text-danger">*</span></label>
    <div class="col-sm-8">
      <input name="urunAdi" type="text" class="form-control" value="{{ old('urunAdi') }}" required>
      <div id="urunAdiUyari"></div>
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


<!-- Yeni Marka Ekle Modal -->
<div class="modal fade" id="addBrandModal" tabindex="-1" aria-hidden="true" style="background: rgba(0, 0, 0, 0.7);">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Marka Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Verdiğiniz form buraya uyarlandı -->
                <form id="addBrandForm" action="{{ route('store.brand.ajax', $firma->id) }}">
                    @csrf
                    <div class="row mb-3"><label class="col-sm-4">Marka:<span class="text-danger">*</span></label><div class="col-sm-8"><input name="marka" class="form-control" type="text" required></div></div>
                    <div class="row">
                        <div class="col-sm-12 text-end">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">İptal</button>
                            <input type="submit" class="btn btn-info btn-sm" value="Kaydet">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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

  // Ürün kodu mask
  $('input[name="urunKodu"]').mask('0000000000000', {
        placeholder: '_____________',
        translation: {
            '0': {pattern: /[0-9]/}
        }
    }).on('input', function() {
        let cleanValue = $(this).cleanVal();
        
        // Visual feedback
        if (cleanValue.length === 13) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
  });
  
  $('#addStock').submit(function(event) {
    // cleanVal() kullanarak sadece rakamları al
    var urunKodu = $('input[name="urunKodu"]').cleanVal();

    if (urunKodu.length !== 13) {
      event.preventDefault();
      alert('Ürün kodu tam 13 haneli olmalıdır!');
      $('input[name="urunKodu"]').focus();
      return false;
    }

   // Form gönderilmeden önce temiz değeri input'a ata
    $('input[name="urunKodu"]').val(urunKodu);
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

<script>
$(document).ready(function () {
    var tenantId = "{{ $firma->id }}";
    var checkTimeout;

    $('input[name="urunAdi"]').on('input', function () {
        clearTimeout(checkTimeout);
        var urunAdi = $(this).val().trim();
        $('#urunAdiUyari').html(''); // Uyarıyı temizle

        if (urunAdi.length < 3) return; // 3 karakterden kısa ise kontrol yapma

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

    // Yeni Marka Ekleme Formu Gönderimi
    $('#addBrandForm').submit(function(e) {
        e.preventDefault(); // Formun normal şekilde gönderilmesini engelle

        var form = $(this);
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(), // Formdaki tüm verileri al
            success: function(response) {
                // Backend'den başarılı bir cevap gelirse:

                // 1. Yeni bir <option> elementi oluştur
                // response.id -> Controller'dan gelen ID
                // response.text -> Controller'dan gelen marka adı
                var newOption = new Option(response.text, response.id, true, true);
                
                // 2. Ana stok ekleme formundaki marka dropdown'ına bu yeni option'ı ekle ve seçili yap
                $('#addStock select[name="marka_id"]').append(newOption).trigger('change');
                
                // 3. "Yeni Marka Ekle" modalını kapat
                $('#addBrandModal').modal('hide');
                
                // 4. Modal içindeki formu temizle ki bir sonraki açılışta boş gelsin
                form[0].reset();

                alert('Marka başarıyla eklendi.');
            },
            error: function(xhr) {
                // Bir hata olursa kullanıcıyı bilgilendir
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                console.log(xhr.responseText);
            }
        });
    });
    //Tedarikçi Ekleme modalı kapandığında, arkadaki modalın scroll problemini düzelt
    $('#addBrandModal').on('hidden.bs.modal', function (e) {
        if ($('#addStockModal').is(':visible')) {
            $('body').addClass('modal-open');
        }
    });
    // Tedarikçi Ekle (+) butonuna tıklandığında modalı manuel olarak aç
    $(document).on('click', '#addNewBrandBtn', function () {
        // Bootstrap'in JavaScript API'sini kullanarak yeni bir modal örneği oluştur
        var brandModal = new bootstrap.Modal(document.getElementById('addBrandModal'));
        // Modalı göster
        brandModal.show();
    });

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
</script>

<script>
$(document).ready(function() {

  
// Modal açılırken arka plan formu gizle
$('#editStockModal').on('show.bs.modal', function () {
    $('#addStock').css('visibility', 'hidden');
});

$('#editStockModal').on('hidden.bs.modal', function () {
    $('#addStock').css('visibility', 'visible');
});

});
</script>








