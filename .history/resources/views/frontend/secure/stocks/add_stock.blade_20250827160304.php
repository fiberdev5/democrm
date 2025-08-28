<form method="post" id="addStock" action="{{ route('store.stock', $firma->id) }}" enctype="multipart/form-data">
  @csrf
  <div class="row mb-1 align-items-center">
    <label class="col-sm-3">Markalar<span style="color:red;">*</span></label>
    <div class="col-sm-9">
      <div class="input-group">
        <select name="marka_id" class="form-select" required>
          <option value="" selected disabled>- Seçiniz -</option>
          @foreach($markalar as $marka)
            <option value="{{ $marka->id }}">{{ $marka->marka }}</option>
          @endforeach
        </select>
        <button class="btn btn-success" type="button"  id="addNewBrandBtn">+</button>
      </div>
    </div>
  </div>

  <div class="row mb-1">
    <label class="col-sm-3">Cihaz Türü<span style="color:red;">*</span></label>
    <div class="col-sm-9">
      <div class="input-group">
        <select name="cihaz_id" class="form-select" required>
          <option value="" selected disabled>- Seçiniz -</option>
          @foreach($cihazlar as $cihaz)
            <option value="{{ $cihaz->id }}">{{ $cihaz->cihaz }}</option>
          @endforeach
        </select>
        <button class="btn btn-success" type="button"  id="addNewDeviceTypeBtn">+</button>
      </div>
    </div>
  </div>

  <div class="row mb-1">
    <label class="col-sm-3">Kategori<span style="color:red;">*</span></label>
    <div class="col-sm-9">
      <div class="input-group">
        <select name="urunKategori" class="form-select" required>
          <option value="" selected disabled>- Seçiniz -</option>
           @foreach($kategoriler as $kategori)
             <option value="{{ $kategori->id }}">{{ $kategori->kategori }}</option>
           @endforeach
        </select>
        <button class="btn btn-success" type="button"  id="addNewCategoryBtn">+</button>
      </div>
    </div>
  </div>
  
<div class="row mb-1">
    <label class="col-sm-3">Raf Seç<span style="color:red;">*</span></label>
    <div class="col-sm-9">
      <div class="input-group">
        <select name="raf_id" class="form-select" required>
          <option value="" selected disabled>- Seçiniz -</option>
           @foreach($rafListesi as $raf)
            <option value="{{ $raf->id }}">{{ $raf->raf_adi }}</option>
           @endforeach
        </select>
        <button class="btn btn-success" type="button"  id="addNewShelfBtn">+</button>
      </div>
    </div>
</div>


<div class="row mb-0">
    <label class="col-sm-3">Ürün Kodu<span class="text-danger">*</span></label>
    <div class="col-sm-9">
        <input name="urunKodu" type="text" class="form-control" 
               value="{{ old('urunKodu') }}" 
               placeholder="0000000000000" 
               data-mask="000-0000000000" required>
        <small class="text-danger">Ürün kodu 13 haneli olmalıdır.</small>
        @error('urunKodu') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row mb-0">
    <label class="col-sm-3">Ürün Adı<span class="text-danger">*</span></label>
    <div class="col-sm-9">
      <input name="urunAdi" type="text" class="form-control" value="{{ old('urunAdi') }}" required>
      <div id="urunAdiUyari"></div>
    </div>
</div>

<div class="row mb-0">
    <label class="col-sm-3">Satış Fiyatı<span class="text-danger">*</span></label>
    <div class="col-sm-9">
      <div class="row g-2">
        <div class="col-8">
          <input name="fiyat" type="number" min="0" step="0.01" class="form-control" required>
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


  <div class="row mb-0">
    <label class="col-sm-3">Açıklama</label>
    <div class="col-sm-9">
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
<div class="modal fade" id="addBrandModal" tabindex="-1" aria-hidden="true" style="background: rgba(0, 0, 0, 0.7); padding-top:100px;">
    <div class="modal-dialog modal-sm ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Marka Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addBrandForm" action="{{ route('store.brand.ajax', $firma->id) }}">
                    @csrf
                    <div class="row mb-3"><label class="col-sm-4">Marka:<span class="text-danger">*</span></label>
                        <div class="col-sm-8"><input name="marka" class="form-control" type="text" required>
                        </div>
                    </div>
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
<!-- Yeni Cihaz Türü Ekle Modal -->
<div class="modal fade" id="addDeviceTypeModal" tabindex="-1" aria-hidden="true" style="background: rgba(0, 0, 0, 0.7); padding-top:100px;">
    <div class="modal-dialog modal-sm ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cihaz Türü Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addDeviceTypeForm" action="{{ route('store.device.type.ajax', $firma->id) }}">
                    @csrf
                    <div class="row mb-3"><label class="col-sm-4">Cihaz:<span class="text-danger">*</span></label><div class="col-sm-8"><input name="cihaz" class="form-control" type="text" required></div></div>
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

<!-- Yeni Kategori Ekle Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true" style="background: rgba(0, 0, 0, 0.7); padding-top:100px;">
    <div class="modal-dialog modal-sm ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kategori Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addCategoryForm" action="{{ route('store.category.ajax', $firma->id) }}">
                    @csrf
                    <div class="row mb-3"><label class="col-sm-4">Kategori:<span class="text-danger">*</span></label><div class="col-sm-8"><input name="kategori" class="form-control" type="text" required></div></div>
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

<!-- Yeni Raf Ekle Modal -->
<div class="modal fade" id="addShelfModal" tabindex="-1" aria-hidden="true" style="background: rgba(0, 0, 0, 0.7); padding-top:100px;">
    <div class="modal-dialog modal-sm ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Raf Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addShelfForm" action="{{ route('store.shelf.ajax', $firma->id) }}">
                    @csrf
                    <div class="row mb-3"><label class="col-sm-4">Raf:<span class="text-danger">*</span></label><div class="col-sm-8"><input name="raf_adi" class="form-control" type="text" required></div></div>
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
<!-- Ürün Düzenle Modal -->
<div class="modal fade" id="editStockModal" tabindex="-1" aria-labelledby="editStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function () {
    var tenantId = "{{ $firma->id }}";
    var mainFormId = '#addStock'; // Ana formun ID'si
    // Stok ekleme formunuz bir modal içinde açılıyorsa, o modalın ID'si buraya gelecek.
    // Eğer form doğrudan bir sayfanın parçasıysa, bu satırı silebilirsiniz.
    var mainModalSelector = '#addStockModal'; // Varsayımsal ana modal ID'si. Lütfen kontrol edin/güncelleyin!

    // Select2'yi belirli bir selector için başlatmak üzere yardımcı fonksiyon
    function initializeSelect2(selector, placeholder, url) {
        var parentModal = $(selector).closest('.modal'); // Kendisi bir modalın içinde mi?
        // Eğer değilse, şu anda açık olan en son modalı bul
        if (parentModal.length === 0) {
            parentModal = $('.modal:visible').last();
        }
        $(selector).select2({
            theme: "bootstrap-5",
            placeholder: placeholder,
            allowClear: true,
            // dropdownParent: Eğer bir parentModal bulunduysa onu kullan, yoksa body'yi kullan.
            dropdownParent: parentModal.length ? parentModal : $('body'),
            ajax: {
                url: url,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return { results: data };
                },
                cache: true
            }
        });
    }

    // Select2 başlatmaları
    initializeSelect2(mainFormId + ' select[name="marka_id"]', 'Marka ara...', '/' + tenantId + '/search-brands');
    initializeSelect2(mainFormId + ' select[name="cihaz_id"]', 'Cihaz türü ara...', '/' + tenantId + '/search-devices');
    initializeSelect2(mainFormId + ' select[name="urunKategori"]', 'Kategori ara...', '/' + tenantId + '/search-categories');
    initializeSelect2(mainFormId + ' select[name="raf_id"]', 'Raf ara...', '/' + tenantId + '/search-shelves');

    // Otomatik hesaplama fonksiyonu (eğer kullanılıyorsa, burada 'adet' inputu yok)
    // Bu kısım eğer stok ekleme formunuzda 'adet' ve 'fiyat' çarpılıp 'fiyatBirim' alanına yazılıyorsa gerekli.
    // Mevcut formunuzda 'adet' inputu olmadığı için bu kısmı kontrol edin.
    // function hesaplaFiyat() {
    //   var adet = parseFloat($(mainFormId + ' input[name="adet"]').val()) || 0;
    //   var fiyat = parseFloat($(mainFormId + ' input[name="fiyat"]').val()) || 0;
    //   if (adet > 0 && fiyat > 0) {
    //     $(mainFormId + ' input[name="fiyatBirim"]').val((fiyat / adet).toFixed(2));
    //   } else {
    //     $(mainFormId + ' input[name="fiyatBirim"]').val('');
    //   }
    // }
    // $(mainFormId + ' input[name="adet"], ' + mainFormId + ' input[name="fiyat"]').on('input', hesaplaFiyat);

    // Ürün kodu maskesi
    $(mainFormId + ' input[name="urunKodu"]').mask('000-0000000000', { // Maskeyi kontrol edin, orijinalde '0000000000000' idi.
        placeholder: '___-__________', // Placeholder'ı maskeye uygun hale getirin
        translation: { '0': { pattern: /[0-9]/ } }
    }).on('input', function() {
        let cleanValue = $(this).cleanVal();
        // Visual feedback
        if (cleanValue.length === 13) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
    });

    // Ana form gönderim kontrolü
    $(mainFormId).submit(function(event) {
        var urunKoduInput = $(this).find('input[name="urunKodu"]');
        var urunKodu = urunKoduInput.cleanVal();

        if (urunKodu.length !== 13) {
            event.preventDefault();
            alert('Ürün kodu tam 13 haneli olmalıdır!');
            urunKoduInput.focus();
            return false;
        }
        // Form gönderilmeden önce temiz değeri input'a ata
        urunKoduInput.val(urunKodu);

        var isValid = true;
        $(this).find('input[required], select[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                return false; // Döngüyü durdur
            }
        });

        if (!isValid) {
            event.preventDefault();
            alert('Lütfen zorunlu alanları doldurun.');
        }
    });

    // Ürün Adı Kontrolü
    var checkTimeout;
    $(mainFormId + ' input[name="urunAdi"]').on('input', function () {
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
                        '<button type="button" id="openEditModalBtn" data-url="' + res.edit_url + '" class="btn btn-sm btn-primary ms-2">Ürünü Düzenle</button>' +
                        '</div>';
                        $('#urunAdiUyari').html(warningHtml);
                    } else {
                        $('#urunAdiUyari').html('');
                    }
                }
            });
        }, 600);
    });

    // Ortak Modal Açma/Kapama İşlevselliği
    function setupSubModal(buttonSelector, modalId, formId, selectName, successMessage) {
        $(document).on('click', buttonSelector, function () {
            var subModal = new bootstrap.Modal(document.getElementById(modalId.substring(1))); // # işaretini kaldır
            subModal.show();
        });

        $(modalId).on('hidden.bs.modal', function (e) {
            // Eğer ana modal açıksa, body'ye modal-open sınıfını geri ekle
            if ($(mainModalSelector).is(':visible')) {
                $('body').addClass('modal-open');
            }
        });

        $(formId).submit(function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    var newOption = new Option(response.text, response.id, true, true);
                    $(mainFormId + ' select[name="' + selectName + '"]').append(newOption).trigger('change');
                    $(modalId).modal('hide');
                    form[0].reset();
                    alert(successMessage);
                },
                error: function(xhr) {
                    alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                    console.log(xhr.responseText);
                }
            });
        });
    }

    setupSubModal('#addNewBrandBtn', '#addBrandModal', '#addBrandForm', 'marka_id', 'Marka başarıyla eklendi.');
    setupSubModal('#addNewDeviceTypeBtn', '#addDeviceTypeModal', '#addDeviceTypeForm', 'cihaz_id', 'Cihaz Türü başarıyla eklendi.');
    setupSubModal('#addNewCategoryBtn', '#addCategoryModal', '#addCategoryForm', 'urunKategori', 'Cihaz Kategori başarıyla eklendi.');
    setupSubModal('#addNewShelfBtn', '#addShelfModal', '#addShelfForm', 'raf_id', 'Raf başarıyla eklendi.');


    // Ürün Düzenle Modalını açma
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

    // Düzenleme modalı açıldığında ana formu gizle/göster
    $('#editStockModal').on('show.bs.modal', function () {
        // Eğer ana stok ekleme formu bir modal içindeyse onu gizle
        if ($(mainModalSelector).length && $(mainModalSelector).is(':visible')) {
            $(mainModalSelector).css('visibility', 'hidden');
        }
    }).on('hidden.bs.modal', function () {
        // Eğer ana stok ekleme formu bir modal içindeyse görünürlüğünü geri getir
        if ($(mainModalSelector).length && $(mainModalSelector).is(':visible')) {
            $(mainModalSelector).css('visibility', 'visible');
            $('body').addClass('modal-open'); // Scroll sorununu gidermek için
        }
    });

});
</script>










