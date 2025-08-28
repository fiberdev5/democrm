<form method="POST" id="addConsignmentDevice" action="{{ route('store.consignment.device', $tenant_id) }}" enctype="multipart/form-data">
    @csrf
    <div class="row mb-2 align-items-center">
        <label class="col-sm-3">Markalar<span class="text-danger">*</span></label>
        <div class="col-sm-9">
            <div class="input-group">
                <select name="marka_id" class="form-select" required>
                    <option value="" selected disabled>- Marka arayın veya seçin -</option>
                    @if(old('marka_id'))
                        @php
                            $oldMarka = \App\Models\Marka::find(old('marka_id'));
                        @endphp
                        @if($oldMarka)
                            <option value="{{ $oldMarka->id }}" selected>{{ $oldMarka->marka }}</option>
                        @endif
                    @endif
                </select>
                <button class="btn btn-success" type="button" id="addNewBrandBtn">+</button>
            </div>
        </div>
    </div>

    <div class="row mb-2 align-items-center">
        <label class="col-sm-3">Cihaz Türleri<span class="text-danger">*</span></label>
        <div class="col-sm-9">
            <div class="input-group">
                <select name="cihaz_id" class="form-select" required>
                    <option value="" selected disabled>- Cihaz türü arayın veya seçin -</option>
                     @if(old('cihaz_id'))
                        @php
                            $oldCihaz = \App\Models\Cihaz::find(old('cihaz_id'));
                        @endphp
                        @if($oldCihaz)
                            <option value="{{ $oldCihaz->id }}" selected>{{ $oldCihaz->cihaz }}</option>
                        @endif
                    @endif
                </select>
                <button class="btn btn-success" type="button" id="addNewDeviceTypeBtn">+</button>
            </div>
        </div>
    </div>

    <div class="row mb-2 align-items-center">
        <label class="col-sm-3">Raf Seç<span class="text-danger">*</span></label>
        <div class="col-sm-9">
            <div class="input-group">
                <select name="raf_id" class="form-select" required>
                    <option value="" selected disabled>- Raf arayın veya seçin -</option>
                     @if(old('raf_id'))
                        @php
                            $oldRaf = \App\Models\Raf::find(old('raf_id'));
                        @endphp
                        @if($oldRaf)
                            <option value="{{ $oldRaf->id }}" selected>{{ $oldRaf->raf_adi }}</option>
                        @endif
                    @endif
                </select>
                <button class="btn btn-success" type="button" id="addNewShelfBtn">+</button>
            </div>
        </div>
    </div>

    <div class="row mb-2 align-items-center">
        <label class="col-sm-3">Ürün Kodu <span class="text-danger">*</span></label>
        <div class="col-sm-9">
            <input name="urunKodu" type="text" class="form-control"
                   value="{{ old('urunKodu') }}"
                   placeholder="0000000000000"
                   data-mask="0000000000000" required>
            <small class="text-danger">Ürün kodu 13 haneli olmalıdır.</small>
            @error('urunKodu') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="row mb-2 align-items-center">
        <label class="col-sm-3">Ürün Adı <span class="text-danger">*</span></label>
        <div class="col-sm-9">
            <input name="urunAdi" type="text" class="form-control" value="{{ old('urunAdi') }}" required>
            <div id="urunAdiUyari"></div>
        </div>
    </div>

    <div class="row mb-2 align-items-center">
        <label class="col-sm-3">Adet <span class="text-danger">*</span></label>
        <div class="col-sm-9">
            <input name="adet" type="number" min="1" class="form-control" value="{{ old('adet') ?? 1 }}" required>
        </div>
    </div>

    <div class="row mb-2 align-items-center">
        <label class="col-sm-3">Satış Fiyatı</label>
        <div class="col-sm-9">
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

    <div class="row mb-2 align-items-center">
        <label class="col-sm-3">Açıklama</label>
        <div class="col-sm-9">
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

<!-- Yeni Marka Ekle Modal -->
<div class="modal fade" id="addBrandModal" tabindex="-1" aria-hidden="true" style="background: rgba(0, 0, 0, 0.7); padding-top:100px;">
    <div class="modal-dialog modal-sm ">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Marka Ekle</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form id="addBrandForm" action="{{ route('store.brand.ajax', $tenant_id) }}">
                    @csrf
                    <div class="row mb-3"><label class="col-sm-4">Marka:<span class="text-danger">*</span></label><div class="col-sm-8"><input name="marka" class="form-control" type="text" required></div></div>
                    <div class="row"><div class="col-sm-12 text-end"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">İptal</button><input type="submit" class="btn btn-info btn-sm" value="Kaydet"></div></div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Yeni Cihaz Türü Ekle Modal -->
<div class="modal fade" id="addDeviceTypeModal" tabindex="-1" aria-hidden="true" style="background: rgba(0, 0, 0, 0.7); padding-top:100px;">
    <div class="modal-dialog modal-sm ">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Cihaz Türü Ekle</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <form id="addDeviceTypeForm" action="{{ route('store.device.type.ajax', $tenant_id) }}"> @csrf 
      <div class="row mb-3"><label class="col-sm-4">Cihaz:<span class="text-danger">*</span></label><div class="col-sm-8">
        <input name="cihaz" class="form-control" type="text" required>
      </div>
    </div>
    <div class="row"><div class="col-sm-12 text-end">
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
    <div class="modal-dialog modal-sm "><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Raf Ekle</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form id="addShelfForm" action="{{ route('store.shelf.ajax', $tenant_id) }}"> @csrf <div class="row mb-3"><label class="col-sm-4">Raf:<span class="text-danger">*</span></label><div class="col-sm-8"><input name="raf_adi" class="form-control" type="text" required></div></div><div class="row"><div class="col-sm-12 text-end"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">İptal</button><input type="submit" class="btn btn-info btn-sm" value="Kaydet"></div></div></form></div></div></div>
</div>

<!-- Ürün Düzenle Modal -->
<div class="modal fade" id="editConsignmentModal" tabindex="-1" aria-labelledby="editConsignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <!-- Düzenleme formu AJAX ile buraya yüklenecek -->
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function () {
    var tenantId = "{{ $tenant_id }}";
    var checkTimeout;

    var mainModalSelector = '#addConsignmentDevice'; 

    function initializeSelect2(selector, placeholder, url) {
        // Select2'nin, içinde bulunduğu modal'ı bulmasını sağlıyoruz
        var parentModal = $(selector).closest('.modal');

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
    $(mainModalSelector).on('show.bs.modal', function () {
        // Her açılışta tekrar tekrar başlatmamak için bir kontrol ekliyoruz.
        if (!$(this).data('select2-initialized')) {
            console.log('Select2 ilk defa başlatılıyor...');
            initializeSelect2('select[name="marka_id"]', 'Marka ara...', "/" + tenantId + "/search-brands");
            initializeSelect2('select[name="cihaz_id"]', 'Cihaz türü ara...', "/" + tenantId + "/search-devices");
            initializeSelect2('select[name="raf_id"]', 'Raf ara...', "/" + tenantId + "/search-shelves");
            
            // Başlatıldığını işaretliyoruz.
            $(this).data('select2-initialized', true);
        }
    });


    // Ürün adı kontrolü
    $('input[name="urunAdi"]').on('input', function () {
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
        translation: { '0': { pattern: /[0-9]/ } }
    }).on('input', function() {
        let cleanValue = $(this).cleanVal();
        if (cleanValue.length === 13) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
    });

    // Form submit kontrolü
    $('#addConsignmentDevice').submit(function(event) {
        var urunKodu = $('input[name="urunKodu"]').cleanVal();
        if (urunKodu.length !== 13) {
            event.preventDefault();
            alert('Ürün kodu tam 13 haneli olmalıdır!');
            $('input[name="urunKodu"]').focus();
            return false;
        }
        $('input[name="urunKodu"]').val(urunKodu);

        var isValid = true;
        $(this).find('input[required], select[required]').each(function () {
            if (!$(this).val()) {
                isValid = false;
                return false; // Döngüyü sonlandır
            }
        });

        if (!isValid) {
            event.preventDefault();
            alert('Lütfen yıldızlı zorunlu alanları doldurun.');
        }
    });

    // AJAX ile yeni kayıt ekleme fonksiyonu
    function handleAjaxFormSubmit(formSelector, modalSelector, selectName) {
        $(formSelector).submit(function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    var newOption = new Option(response.text, response.id, true, true);
                    $('#addConsignmentDevice select[name="' + selectName + '"]').append(newOption).trigger('change');
                    $(modalSelector).modal('hide');
                    form[0].reset();
                    alert(response.text + ' başarıyla eklendi.');
                },
                error: function() {
                    alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                }
            });
        });
    }

    handleAjaxFormSubmit('#addBrandForm', '#addBrandModal', 'marka_id');
    handleAjaxFormSubmit('#addDeviceTypeForm', '#addDeviceTypeModal', 'cihaz_id');
    handleAjaxFormSubmit('#addShelfForm', '#addShelfModal', 'raf_id');

    // (+) Butonları için modal açma
    $('#addNewBrandBtn').click(function() { new bootstrap.Modal(document.getElementById('addBrandModal')).show(); });
    $('#addNewDeviceTypeBtn').click(function() { new bootstrap.Modal(document.getElementById('addDeviceTypeModal')).show(); });
    $('#addNewShelfBtn').click(function() { new bootstrap.Modal(document.getElementById('addShelfModal')).show(); });

    // Üst üste açılan modallarda scroll sorununu çözmek için
    $('.modal').on('hidden.bs.modal', function (e) {
        if ($('.modal:visible').length) {
            $('body').addClass('modal-open');
        }
    });

    // "Ürünü Düzenle" butonu ile modal açma
    $(document).on('click', '#openEditModalBtn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            success: function (res) {
                if (res.html) {
                    $('#editConsignmentModal .modal-body').html(res.html);
                    $('#editConsignmentModal').modal('show');
                } else {
                    alert('Düzenleme formu yüklenemedi.');
                }
            },
            error: function () {
                alert('Düzenleme formu yüklenirken hata oluştu.');
            }
        });
    });

    // Düzenleme modalı açıldığında ana formu gizle, kapandığında göster
    $('#editConsignmentModal').on('show.bs.modal', function () {
        $('#addConsignmentDevice').css('visibility', 'hidden');
    });
    $('#editConsignmentModal').on('hidden.bs.modal', function () {
        $('#addConsignmentDevice').css('visibility', 'visible');
    });
});
</script>