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
    var mainFormSelector = '#addStock'; // Ana formun ID'si
    var mainModalSelector = '#addStockModal'; // Ana form bir modal içinde ise (bu örnekte değil, ancak kalsın)
    
    // Select2'yi başlatmak için yardımcı fonksiyon
    function initializeSelect2(selector, placeholder, url) {
        var parentModal = $(selector).closest('.modal');
        if (parentModal.length === 0) {
            parentModal = $('.modal:visible').last(); // Aktif modalı hedef al
        }
        $(selector).select2({
            theme: "bootstrap-5",
            placeholder: placeholder,
            allowClear: true,
            dropdownParent: parentModal.length ? parentModal : $('body'),
            ajax: {
                url: url,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });
    }

    // Select2 başlatmaları
    initializeSelect2(mainFormSelector + ' select[name="marka_id"]', 'Marka ara...', '/' + tenantId + '/search-brands');
    initializeSelect2(mainFormSelector + ' select[name="cihaz_id"]', 'Cihaz türü ara...', '/' + tenantId + '/search-devices');
    initializeSelect2(mainFormSelector + ' select[name="urunKategori"]', 'Kategori ara...', '/' + tenantId + '/search-categories');
    initializeSelect2(mainFormSelector + ' select[name="raf_id"]', 'Raf ara...', '/' + tenantId + '/search-shelves');

    // Otomatik hesaplama fonksiyonu (Örn: Birim Fiyatı Hesaplama)
    // Not: HTML'de 'fiyatBirim' bir <select> elementi. Eğer birim fiyat hesaplayıp
    // başka bir inputa yazmak istiyorsanız, o inputu HTML'e eklemelisiniz.
    // Bu kısım şu an HTML'deki "fiyatBirim" select'ine yazmaya çalışmıyor.
    function hesaplaFiyat() {
        // var adet = parseFloat($(mainFormSelector + ' input[name="adet"]').val()) || 0;
        // var fiyat = parseFloat($(mainFormSelector + ' input[name="fiyat"]').val()) || 0;
        // if (adet > 0 && fiyat > 0) {
        //     // $(mainFormSelector + ' input[name="birimFiyatGoster"]').val((fiyat / adet).toFixed(2));
        // } else {
        //     // $(mainFormSelector + ' input[name="birimFiyatGoster"]').val('');
        // }
    }
    // $(mainFormSelector + ' input[name="adet"], ' + mainFormSelector + ' input[name="fiyat"]').on('input', hesaplaFiyat);


    // Ürün kodu maskeleme ve anlık görsel geri bildirim
   // Ürün kodu maskesi
    $(mainFormSelector + ' input[name="urunKodu"]').mask('0000000000000', {
        placeholder: '_____________',
        translation: { '0': { pattern: /[0-9]/ } }
    }).on('input', function() {
        let cleanValue = $(this).cleanVal();
        $(this).removeClass('is-invalid is-valid').addClass(cleanValue.length === 13 ? 'is-valid' : 'is-invalid');
    });

     // Ürün adı kontrolü
    var checkTimeout;
    $(mainFormSelector + ' input[name="urunAdi"]').on('input', function () {
        clearTimeout(checkTimeout);
        var urunAdi = $(this).val().trim();
        $('#urunAdiUyari').html('');
        if (urunAdi.length < 3) return;
        checkTimeout = setTimeout(function () {
            $.ajax({
                url: "/" + tenantId + "/stok/urun-adi-kontrol",
                method: "POST",
                data: { urunAdi: urunAdi, _token: "{{ csrf_token() }}" },
                success: function (res) {
                    if (res.exists) {
                        var warningHtml = '<div class="alert alert-warning mt-2">' +
                            'Bu ürün adı zaten mevcut. ' +
                            '<button id="openEditModalBtn" data-url="' + res.edit_url + '" class="btn btn-sm btn-primary ms-2">Ürünü Düzenle</button>' +
                            '</div>';
                        $('#urunAdiUyari').html(warningHtml);
                    }
                }
            });
        }, 600);
    });



    // Ana Form Gönderim İşlemi ve Doğrulama
    $(mainFormSelector).submit(function(event) {
        // Ürün kodu doğrulaması
        var urunKoduInput = $(mainFormSelector + ' input[name="urunKodu"]');
        var cleanUrunKodu = urunKoduInput.cleanVal(); // Sadece rakamları al
        
        if (cleanUrunKodu.length !== 13) {
            event.preventDefault(); // Form gönderimini engelle
            alert('Ürün kodu tam 13 haneli olmalıdır!');
            urunKoduInput.focus();
            urunKoduInput.addClass('is-invalid'); // Hatalı inputu vurgula
            return false;
        }
        // Form gönderilmeden önce temiz değeri input'a ata (isteğe bağlı, Laravel maskeyi de işleyebilir)
        urunKoduInput.val(cleanUrunKodu);

        // Ürün adı tekrar kontrolü (eğer önceki AJAX sonucu tekrar varsa)
        if (isProductNameDuplicate) {
            event.preventDefault(); // Form gönderimini engelle
            alert('Bu ürün adı zaten mevcut. Lütfen farklı bir ad girin veya mevcut ürünü düzenleyin.');
            $(mainFormSelector + ' input[name="urunAdi"]').focus().addClass('is-invalid');
            return false;
        }

        // Genel zorunlu alan kontrolü
        var isValid = true;
        $(this).find('input[required], select[required], textarea[required]').each(function() {
            // Select2 alanları için özel kontrol (eğer boş değerleri varsa)
            if ($(this).hasClass('select2-hidden-accessible')) {
                if (!$(this).val() || $(this).val().length === 0) { // Select2, çoklu seçimde array dönebilir
                    isValid = false;
                    $(this).next('.select2-container').find('.select2-selection').addClass('is-invalid'); // Select2 kapsayıcısını vurgula
                    return false;
                } else {
                    $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
                }
            } else if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid'); // Normal inputları vurgula
                return false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            event.preventDefault();
            alert('Lütfen tüm zorunlu alanları doldurun.');
            return false;
        }

        // Tüm kontroller geçti, form gönderilebilir.
        // Bu noktada ek bir işlem yapılmaz, form doğal yollarla Laravel'e gönderilir.
    });


    // Alt modal (Marka, Cihaz, Kategori, Raf Ekleme) işlevselliği
    function setupSubModal(buttonSelector, modalId, formId, selectName, successMessage) {
        $(document).on('click', buttonSelector, function () {
            // Ana modal açıksa, gizleme yerine görünürlüğünü kapat
            if ($(mainModalSelector).length && $(mainModalSelector).is(':visible')) {
                $(mainModalSelector).css('visibility', 'hidden');
            }
            var subModal = new bootstrap.Modal(document.getElementById(modalId.substring(1)));
            subModal.show();
        });

        $(modalId).on('hidden.bs.modal', function (e) {
            // Alt modal kapandığında, ana modalın görünürlüğünü geri getir
            if ($(mainModalSelector).length && $(mainModalSelector).is(':visible')) {
                $(mainModalSelector).css('visibility', 'visible');
                $('body').addClass('modal-open'); // Scroll sorununu gidermek için
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
                    $(mainFormSelector + ' select[name="' + selectName + '"]').append(newOption).trigger('change');
                    $(modalId).modal('hide');
                    form[0].reset();
                    alert(successMessage);
                },
                error: function(xhr) {
                    var errorMessage = 'Bir hata oluştu. Lütfen tekrar deneyin.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
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
            error: function(xhr) {
                var errorMessage = 'Düzenleme formu yüklenirken hata oluştu.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
                console.error("Düzenleme formu yüklenirken hata oluştu:", xhr.responseText);
            }
        });
    });

    // Düzenleme modalı açıldığında/kapandığında ana formu gizle/göster
    $('#editStockModal').on('show.bs.modal', function () {
        // Eğer ana stok ekleme formu bir modal içindeyse onu gizle (görünürlüğünü kapat)
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









