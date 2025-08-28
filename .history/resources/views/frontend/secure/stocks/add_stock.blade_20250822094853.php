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
          <input name="fiyat" type="number" min="0" step="0.01" class="form-control" placeholder="Fiyat" required>
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
                <!-- AJAX ile gelen düzenleme formu buraya yüklenecek -->
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var tenantId = "{{ $firma->id }}";
    var checkTimeout;
    
    // Select2 konfigürasyonu için ortak ayarlar
    var select2CommonConfig = {
        theme: "bootstrap-5",
        width: '100%',
        allowClear: true,
        language: {
            noResults: function() {
                return "Sonuç bulunamadı";
            },
            searching: function() {
                return "Aranıyor...";
            },
            inputTooShort: function() {
                return "En az 1 karakter giriniz";
            },
            loadingMore: function() {
                return "Daha fazla yükleniyor...";
            }
        }
    };

    // Ana stok ekleme modalı açıldığında Select2'yi başlat
    $('#addStockModal').on('shown.bs.modal', function () {
        initializeSelect2ForStockForm();
    });
    
    // Ana stok ekleme modalı kapandığında Select2'yi temizle
    $('#addStockModal').on('hidden.bs.modal', function () {
        destroySelect2ForStockForm();
    });

    // Select2'yi başlatma fonksiyonu
    function initializeSelect2ForStockForm() {
        
        // Marka Select2
        $('#addStock select[name="marka_id"]').select2($.extend({}, select2CommonConfig, {
            dropdownParent: $('#addStockModal'),
            placeholder: 'Marka ara ve seç...',
            minimumInputLength: 1,
            ajax: {
                url: '/' + tenantId + '/search-brands',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: data.length === 20
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            templateResult: function (data) {
                if (data.loading) return data.text;
                return '<div>' + data.text + '</div>';
            },
            templateSelection: function (data) {
                return data.text;
            }
        }));

        // Cihaz Türü Select2
        $('#addStock select[name="cihaz_id"]').select2($.extend({}, select2CommonConfig, {
            dropdownParent: $('#addStockModal'),
            placeholder: 'Cihaz türü ara ve seç...',
            minimumInputLength: 1,
            ajax: {
                url: '/' + tenantId + '/search-devices',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: data.length === 20
                        }
                    };
                },
                cache: true
            }
        }));

        // Kategori Select2
        $('#addStock select[name="urunKategori"]').select2($.extend({}, select2CommonConfig, {
            dropdownParent: $('#addStockModal'),
            placeholder: 'Kategori ara ve seç...',
            minimumInputLength: 1,
            ajax: {
                url: '/' + tenantId + '/search-categories',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: data.length === 20
                        }
                    };
                },
                cache: true
            }
        }));

        // Raf Select2
        $('#addStock select[name="raf_id"]').select2($.extend({}, select2CommonConfig, {
            dropdownParent: $('#addStockModal'),
            placeholder: 'Raf ara ve seç...',
            minimumInputLength: 1,
            ajax: {
                url: '/' + tenantId + '/search-shelves',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: data.length === 20
                        }
                    };
                },
                cache: true
            }
        }));

        // Select2 elementlerinin input-group ile düzgün görünmesi için
        $('.input-group .select2-container').css('flex', '1 1 auto');
    }

    // Select2'yi temizleme fonksiyonu
    function destroySelect2ForStockForm() {
        try {
            $('#addStock select[name="marka_id"]').select2('destroy');
            $('#addStock select[name="cihaz_id"]').select2('destroy');
            $('#addStock select[name="urunKategori"]').select2('destroy');
            $('#addStock select[name="raf_id"]').select2('destroy');
        } catch (e) {
            // Destroy işlemi sırasında hata olursa sessizce devam et
        }
    }

    // Yeni öğe ekleme fonksiyonları - Select2 uyumlu
    
    // Marka ekleme
    $('#addBrandForm').submit(function(e) {
        e.preventDefault(); 
        var form = $(this);
        var submitBtn = form.find('input[type="submit"]');
        
        submitBtn.prop('disabled', true).val('Kaydediliyor...');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                // Select2'ye yeni option ekle
                var newOption = new Option(response.text, response.id, true, true);
                $('#addStock select[name="marka_id"]').append(newOption).trigger('change');
                
                $('#addBrandModal').modal('hide');
                form[0].reset();
                
                // Başarı mesajı
                showSuccessMessage('Marka başarıyla eklendi ve seçildi.');
            },
            error: function(xhr) {
                var errorMsg = 'Bir hata oluştu.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showErrorMessage(errorMsg);
            },
            complete: function() {
                submitBtn.prop('disabled', false).val('Kaydet');
            }
        });
    });

    // Cihaz türü ekleme
    $('#addDeviceTypeForm').submit(function(e) {
        e.preventDefault(); 
        var form = $(this);
        var submitBtn = form.find('input[type="submit"]');
        
        submitBtn.prop('disabled', true).val('Kaydediliyor...');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                var newOption = new Option(response.text, response.id, true, true);
                $('#addStock select[name="cihaz_id"]').append(newOption).trigger('change');
                
                $('#addDeviceTypeModal').modal('hide');
                form[0].reset();
                showSuccessMessage('Cihaz türü başarıyla eklendi ve seçildi.');
            },
            error: function(xhr) {
                var errorMsg = 'Bir hata oluştu.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showErrorMessage(errorMsg);
            },
            complete: function() {
                submitBtn.prop('disabled', false).val('Kaydet');
            }
        });
    });

    // Kategori ekleme
    $('#addCategoryForm').submit(function(e) {
        e.preventDefault(); 
        var form = $(this);
        var submitBtn = form.find('input[type="submit"]');
        
        submitBtn.prop('disabled', true).val('Kaydediliyor...');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                var newOption = new Option(response.text, response.id, true, true);
                $('#addStock select[name="urunKategori"]').append(newOption).trigger('change');
                
                $('#addCategoryModal').modal('hide');
                form[0].reset();
                showSuccessMessage('Kategori başarıyla eklendi ve seçildi.');
            },
            error: function(xhr) {
                var errorMsg = 'Bir hata oluştu.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showErrorMessage(errorMsg);
            },
            complete: function() {
                submitBtn.prop('disabled', false).val('Kaydet');
            }
        });
    });

    // Raf ekleme
    $('#addShelfForm').submit(function(e) {
        e.preventDefault(); 
        var form = $(this);
        var submitBtn = form.find('input[type="submit"]');
        
        submitBtn.prop('disabled', true).val('Kaydediliyor...');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                var newOption = new Option(response.text, response.id, true, true);
                $('#addStock select[name="raf_id"]').append(newOption).trigger('change');
                
                $('#addShelfModal').modal('hide');
                form[0].reset();
                showSuccessMessage('Raf başarıyla eklendi ve seçildi.');
            },
            error: function(xhr) {
                var errorMsg = 'Bir hata oluştu.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showErrorMessage(errorMsg);
            },
            complete: function() {
                submitBtn.prop('disabled', false).val('Kaydet');
            }
        });
    });

    // Yardımcı fonksiyonlar
    function showSuccessMessage(message) {
        // Toast notification veya alert ile başarı mesajı göster
        if (typeof toastr !== 'undefined') {
            toastr.success(message);
        } else {
            alert(message);
        }
    }

    function showErrorMessage(message) {
        // Toast notification veya alert ile hata mesajı göster
        if (typeof toastr !== 'undefined') {
            toastr.error(message);
        } else {
            alert(message);
        }
    }

    // Alt modalların kapanma olayları (scroll düzeltmesi için)
    $('#addBrandModal, #addDeviceTypeModal, #addCategoryModal, #addShelfModal').on('hidden.bs.modal', function (e) {
        if ($('#addStockModal').is(':visible')) {
            $('body').addClass('modal-open');
        }
    });

    // + butonlarına tıklama olayları
    $(document).on('click', '#addNewBrandBtn', function () {
        var brandModal = new bootstrap.Modal(document.getElementById('addBrandModal'));
        brandModal.show();
    });

    $(document).on('click', '#addNewDeviceTypeBtn', function () {
        var deviceTypeModal = new bootstrap.Modal(document.getElementById('addDeviceTypeModal'));
        deviceTypeModal.show();
    });

    $(document).on('click', '#addNewCategoryBtn', function () {
        var categoryModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
        categoryModal.show();
    });

    $(document).on('click', '#addNewShelfBtn', function () {
        var shelfModal = new bootstrap.Modal(document.getElementById('addShelfModal'));
        shelfModal.show();
    });

    // Ürün adı kontrol sistemi
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
                        '<i class="fas fa-exclamation-triangle"></i> Bu ürün adı zaten mevcut. ' +
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

    // Ürün kodu maskesi ve doğrulaması
    $('input[name="urunKodu"]').mask('0000000000000', {
        placeholder: '_____________',
        translation: {
            '0': {pattern: /[0-9]/}
        }
    }).on('input', function() {
        let cleanValue = $(this).cleanVal();
        
        if (cleanValue.length === 13) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
    });
  
    // Ana form gönderimi
    $('#addStock').submit(function(event) {
        var urunKodu = $('input[name="urunKodu"]').cleanVal();

        if (urunKodu.length !== 13) {
            event.preventDefault();
            showErrorMessage('Ürün kodu tam 13 haneli olmalıdır!');
            $('input[name="urunKodu"]').focus();
            return false;
        }

        $('input[name="urunKodu"]').val(urunKodu);
        
        // Form validasyonu
        var isValid = true;
        var firstInvalidField = null;
        
        $(this).find('input, select').each(function() {
            if ($(this).prop('required') && !$(this).val()) {
                isValid = false;
                if (!firstInvalidField) {
                    firstInvalidField = $(this);
                }
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            event.preventDefault();
            showErrorMessage('Lütfen zorunlu alanları doldurun.');
            if (firstInvalidField) {
                firstInvalidField.focus();
            }
            return false;
        }

        // Form gönderilirken butonu devre dışı bırak
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Kaydediliyor...');
        
        // Eğer AJAX ile form göndermek istemiyorsanız bu kısmı kaldırın
        // Şu anda normal form gönderimi yapılacak
        
        // Form gönderildikten sonra butonu eski haline getir (sayfa yenilenirse otomatik olacak)
        setTimeout(function() {
            submitBtn.prop('disabled', false).text('Kaydet');
        }, 1000);
    });

    // Ürün düzenleme modalı açma
    $(document).on('click', '#openEditModalBtn', function() {
        var url = $(this).data('url');

        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function() {
                // Loading göstergesi ekleyebiliriz
                $('#editStockModal .modal-body').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Yükleniyor...</div>');
                $('#editStockModal').modal('show');
            },
            success: function(res) {
                if(res.html) {
                    $('#editStockModal .modal-body').html(res.html);
                } else {
                    $('#editStockModal .modal-body').html('<div class="alert alert-danger">Düzenleme formu yüklenemedi.</div>');
                }
            },
            error: function() {
                $('#editStockModal .modal-body').html('<div class="alert alert-danger">Düzenleme formu yüklenirken hata oluştu.</div>');
            }
        });
    });

    // Fiyat hesaplama (eğer gerekiyorsa)
    function hesaplaFiyat() {
        var adet = parseFloat($('input[name="adet"]').val()) || 0;
        var fiyat = parseFloat($('input[name="fiyat"]').val()) || 0;

        if (adet > 0 && fiyat > 0) {
            $('input[name="fiyatBirim"]').val((fiyat / adet).toFixed(2));
        } else {
            $('input[name="fiyatBirim"]').val('');
        }
    }
    
    // Eğer fiyat hesaplama gerekiyorsa
    $('input[name="adet"], input[name="fiyat"], input[name="fiyatBirim"]').on('input', hesaplaFiyat);

});
</script>










