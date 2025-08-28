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
        <button class="btn btn-success" type="button" id="addNewBrandBtn">+</button>
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
        <button class="btn btn-success" type="button" id="addNewDeviceTypeBtn">+</button>
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
        <button class="btn btn-success" type="button" id="addNewCategoryBtn">+</button>
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
        <button class="btn btn-success" type="button" id="addNewShelfBtn">+</button>
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

<!-- Modallar - Değişiklik yok, aynen kalacak -->
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

<style>
/* Select2 ile Bootstrap 5 uyumu için */
.select2-container--bootstrap-5 .select2-selection {
    min-height: calc(1.5em + 0.75rem + 2px);
}

.select2-container--bootstrap-5 .select2-selection--single {
    padding: 0.375rem 0.75rem;
}

.select2-container--bootstrap-5.select2-container--focus .select2-selection {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>

<script>
// Stok ekleme formundaki select'ler için Select2 yapılandırması
$(document).ready(function() {
    
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
        var tenantId = "{{ $firma->id }}";
        
        // Marka Select2
        $('#addStock select[name="marka_id"]').select2({
            theme: "bootstrap-5",
            dropdownParent: $('#addStockModal'),
            placeholder: 'Marka ara...',
            allowClear: true,
            ajax: {
                url: '/' + tenantId + '/search-brands',
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

        // Cihaz Türü Select2
        $('#addStock select[name="cihaz_id"]').select2({
            theme: "bootstrap-5",
            dropdownParent: $('#addStockModal'),
            placeholder: 'Cihaz türü ara...',
            allowClear: true,
            ajax: {
                url: '/' + tenantId + '/search-devices',
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

        // Kategori Select2
        $('#addStock select[name="urunKategori"]').select2({
            theme: "bootstrap-5",
            dropdownParent: $('#addStockModal'),
            placeholder: 'Kategori ara...',
            allowClear: true,
            ajax: {
                url: '/' + tenantId + '/search-categories',
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

        // Raf Select2
        $('#addStock select[name="raf_id"]').select2({
            theme: "bootstrap-5",
            dropdownParent: $('#addStockModal'),
            placeholder: 'Raf ara...',
            allowClear: true,
            ajax: {
                url: '/' + tenantId + '/search-shelves',
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

    // Select2'yi temizleme fonksiyonu
    function destroySelect2ForStockForm() {
        $('#addStock select[name="marka_id"]').select2('destroy');
        $('#addStock select[name="cihaz_id"]').select2('destroy');
        $('#addStock select[name="urunKategori"]').select2('destroy');
        $('#addStock select[name="raf_id"]').select2('destroy');
    }

    // Yeni öğe ekleme modalları için güncellenmiş fonksiyonlar
    
    // Marka ekleme
    $('#addBrandForm').submit(function(e) {
        e.preventDefault(); 
        var form = $(this);  
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                // Select2 ile uyumlu olarak yeni option ekleme
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

    // Cihaz türü ekleme
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

    // Kategori ekleme
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

    // Raf ekleme
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

    // Mevcut kodlarınızın geri kalanı...
    var tenantId = "{{ $firma->id }}";
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

    // Ürün kodu maskesi
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
  
    // Form gönderimi
    $('#addStock').submit(function(event) {
        var urunKodu = $('input[name="urunKodu"]').cleanVal();

        if (urunKodu.length !== 13) {
            event.preventDefault();
            alert('Ürün kodu tam 13 haneli olmalıdır!');
            $('input[name="urunKodu"]').focus();
            return false;
        }

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

    // Ürün düzenleme modalı açma
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