<form method="post" id="addStock" action="{{ route('store.stock', $firma->id) }}" enctype="multipart/form-data">
  @csrf
  
  <!-- İki sütunlu grid yapısı -->
  <div class="row g-2">
    <!-- Sol sütun -->
    <div class="col-md-6">
      <!-- Markalar -->
      <div class="mb-2">
        <label class="form-label small">Markalar</label>
        <div class="input-group input-group-sm">
          <select name="marka_id" class="form-select form-select-sm">
            <option value="" selected disabled>- Seçiniz -</option>
            @foreach($markalar as $marka)
              <option value="{{ $marka->id }}">{{ $marka->marka }}</option>
            @endforeach
          </select>
          <button class="btn btn-success btn-sm px-2" type="button" id="addNewBrandBtn">+</button>
        </div>
      </div>

      <!-- Kategori -->
      <div class="mb-2">
        <label class="form-label small">Kategori<span class="text-danger">*</span></label>
        <div class="input-group input-group-sm">
          <select name="urunKategori" class="form-select form-select-sm">
            <option value="" selected disabled>- Seçiniz -</option>
             @foreach($kategoriler as $kategori)
               <option value="{{ $kategori->id }}">{{ $kategori->kategori }}</option>
             @endforeach
          </select>
          <button class="btn btn-success btn-sm px-2" type="button" id="addNewCategoryBtn">+</button>
        </div>
      </div>

      <!-- Ürün Kodu -->
      <div class="mb-2">
        <label class="form-label small">Ürün Kodu <span class="text-danger">*</span></label>
        <input name="urunKodu" type="text" class="form-control form-control-sm" 
               value="{{ old('urunKodu') }}" 
               placeholder="0000000000000" 
               data-mask="000-0000000000" required>
        <small class="text-muted">13 haneli olmalı</small>
        @error('urunKodu') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>

      <!-- Satış Fiyatı -->
      <div class="mb-2">
        <label class="form-label small">Satış Fiyatı</label>
        <div class="row g-1">
          <div class="col-8">
            <input name="fiyat" type="number" min="0" step="0.01" class="form-control form-control-sm" placeholder="Fiyat">
          </div>
          <div class="col-4">
            <select name="fiyatBirim" class="form-select form-select-sm">
              <option value="" disabled selected>Birim</option>
              <option value="1">TL</option>
              <option value="2">USD</option>
              <option value="3">EUR</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Sağ sütun -->
    <div class="col-md-6">
      <!-- Cihaz Türleri -->
      <div class="mb-2">
        <label class="form-label small">Cihaz Türleri</label>
        <div class="input-group input-group-sm">
          <select name="cihaz_id" class="form-select form-select-sm">
            <option value="" selected disabled>- Seçiniz -</option>
            @foreach($cihazlar as $cihaz)
              <option value="{{ $cihaz->id }}">{{ $cihaz->cihaz }}</option>
            @endforeach
          </select>
          <button class="btn btn-success btn-sm px-2" type="button" id="addNewDeviceTypeBtn">+</button>
        </div>
      </div>

      <!-- Raf Seç -->
      <div class="mb-2">
        <label class="form-label small">Raf Seç</label>
        <div class="input-group input-group-sm">
          <select name="raf_id" class="form-select form-select-sm">
            <option value="" selected disabled>- Seçiniz -</option>
             @foreach($rafListesi as $raf)
              <option value="{{ $raf->id }}">{{ $raf->raf_adi }}</option>
             @endforeach
          </select>
          <button class="btn btn-success btn-sm px-2" type="button" id="addNewShelfBtn">+</button>
        </div>
      </div>

      <!-- Ürün Adı -->
      <div class="mb-2">
        <label class="form-label small">Ürün Adı <span class="text-danger">*</span></label>
        <input name="urunAdi" type="text" class="form-control form-control-sm" value="{{ old('urunAdi') }}" required>
        <div id="urunAdiUyari"></div>
      </div>
    </div>
  </div>

  <!-- Açıklama - Full width -->
  <div class="mb-3">
    <label class="form-label small">Açıklama</label>
    <textarea name="aciklama" rows="2" class="form-control form-control-sm"></textarea>
  </div>

  <!-- Kaydet butonu -->
  <div class="d-flex justify-content-end">
    <button type="submit" class="btn btn-primary btn-sm px-4">Kaydet</button>
  </div>
</form>

<!-- Yeni Marka Ekle Modal -->
<div class="modal fade" id="addBrandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title">Marka Ekle</h6>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2">
                <form id="addBrandForm" action="{{ route('store.brand.ajax', $firma->id) }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small">Marka:<span class="text-danger">*</span></label>
                        <input name="marka" class="form-control form-control-sm" type="text" required>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary btn-sm me-1" data-bs-dismiss="modal">İptal</button>
                        <input type="submit" class="btn btn-info btn-sm" value="Kaydet">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Yeni Cihaz Türü Ekle Modal -->
<div class="modal fade" id="addDeviceTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title">Cihaz Türü Ekle</h6>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2">
                <form id="addDeviceTypeForm" action="{{ route('store.device.type.ajax', $firma->id) }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small">Cihaz:<span class="text-danger">*</span></label>
                        <input name="cihaz" class="form-control form-control-sm" type="text" required>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary btn-sm me-1" data-bs-dismiss="modal">İptal</button>
                        <input type="submit" class="btn btn-info btn-sm" value="Kaydet">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Yeni Kategori Ekle Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title">Kategori Ekle</h6>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2">
                <form id="addCategoryForm" action="{{ route('store.category.ajax', $firma->id) }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small">Kategori:<span class="text-danger">*</span></label>
                        <input name="kategori" class="form-control form-control-sm" type="text" required>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary btn-sm me-1" data-bs-dismiss="modal">İptal</button>
                        <input type="submit" class="btn btn-info btn-sm" value="Kaydet">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Yeni Raf Ekle Modal -->
<div class="modal fade" id="addShelfModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title">Raf Ekle</h6>
                <button type="button" "btn-close btn-close-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2">
                <form id="addShelfForm" action="{{ route('store.shelf.ajax', $firma->id) }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small">Raf:<span class="text-danger">*</span></label>
                        <input name="raf_adi" class="form-control form-control-sm" type="text" required>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary btn-sm me-1" data-bs-dismiss="modal">İptal</button>
                        <input type="submit" class="btn btn-info btn-sm" value="Kaydet">
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
                        var warningHtml = '<div class="alert alert-warning alert-sm mt-1 py-1">' +
    'Bu ürün adı zaten mevcut. ' +
    '<button id="openEditModalBtn" data-url="' + res.edit_url + '" class="btn btn-sm btn-primary btn-xs ms-1">Ürünü Düzenle</button>' +
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
        e.preventDefault(); 
        var form = $(this);  
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                var newOption = new Option(response.text, response.id, true, true);
                $('#addStock select[name="marka_id"]').append(newOption).trigger('change');
                $('#addBrandModal').modal('hide');
                form[0].reset();
                alert('Marka başarıyla eklendi.');
            },
            error: function(xhr) {
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                console.log(xhr.responseText);
            }
        });
    });

    $('#addBrandModal').on('hidden.bs.modal', function (e) {
        if ($('#addStockModal').is(':visible')) {
            $('body').addClass('modal-open');
        }
    });

    $(document).on('click', '#addNewBrandBtn', function () {
        var brandModal = new bootstrap.Modal(document.getElementById('addBrandModal'));
        brandModal.show();
    });

    // Yeni Cihaz Türü Ekleme Formu Gönderimi
    $('#addDeviceTypeForm').submit(function(e) {
        e.preventDefault(); 
        var form = $(this);  
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                var newOption = new Option(response.text, response.id, true, true);
                $('#addStock select[name="cihaz_id"]').append(newOption).trigger('change');
                $('#addDeviceTypeModal').modal('hide');
                form[0].reset();
                alert('Cihaz Türü başarıyla eklendi.');
            },
            error: function(xhr) {
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                console.log(xhr.responseText);
            }
        });
    });

    $('#addDeviceTypeModal').on('hidden.bs.modal', function (e) {
        if ($('#addStockModal').is(':visible')) {
            $('body').addClass('modal-open');
        }
    });

    $(document).on('click', '#addNewDeviceTypeBtn', function () {
        var deviceTypeModal = new bootstrap.Modal(document.getElementById('addDeviceTypeModal'));
        deviceTypeModal.show();
    });

    // Yeni Kategori Ekleme Formu Gönderimi
    $('#addCategoryForm').submit(function(e) {
        e.preventDefault(); 
        var form = $(this);  
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                var newOption = new Option(response.text, response.id, true, true);
                $('#addStock select[name="urunKategori"]').append(newOption).trigger('change');
                $('#addCategoryModal').modal('hide');
                form[0].reset();
                alert('Kategori başarıyla eklendi.');
            },
            error: function(xhr) {
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                console.log(xhr.responseText);
            }
        });
    });

    $('#addCategoryModal').on('hidden.bs.modal', function (e) {
        if ($('#addStockModal').is(':visible')) {
            $('body').addClass('modal-open');
        }
    });

    $(document).on('click', '#addNewCategoryBtn', function () {
        var CategoryModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
        CategoryModal.show();
    });

    // Yeni Raf Ekleme Formu Gönderimi
    $('#addShelfForm').submit(function(e) {
        e.preventDefault(); 
        var form = $(this);  
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                var newOption = new Option(response.text, response.id, true, true);
                $('#addStock select[name="raf_id"]').append(newOption).trigger('change');
                $('#addShelfModal').modal('hide');
                form[0].reset();
                alert('Raf başarıyla eklendi.');
            },
            error: function(xhr) {
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                console.log(xhr.responseText);
            }
        });
    });

    $('#addShelfModal').on('hidden.bs.modal', function (e) {
        if ($('#addStockModal').is(':visible')) {
            $('body').addClass('modal-open');
        }
    });

    $(document).on('click', '#addNewShelfBtn', function () {
        var ShelfModal = new bootstrap.Modal(document.getElementById('addShelfModal'));
        ShelfModal.show();
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