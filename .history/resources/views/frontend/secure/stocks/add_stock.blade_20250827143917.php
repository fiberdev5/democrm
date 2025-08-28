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
<form method="POST" id="addConsignmentDevice" action="{{ route('store.consignment.device', $tenant_id) }}" enctype="multipart/form-data">
    @csrf
    <div class="row mb-1 align-items-center">
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

    <div class="row mb-1 align-items-center">
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

    <div class="row mb-1 align-items-center">
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

    <div class="row mb-1 align-items-center">
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

    <div class="row mb-1 align-items-center">
        <label class="col-sm-3">Ürün Adı <span class="text-danger">*</span></label>
        <div class="col-sm-9">
            <input name="urunAdi" type="text" class="form-control" value="{{ old('urunAdi') }}" required>
            <div id="urunAdiUyari"></div>
        </div>
    </div>

    <div class="row mb-1 align-items-center">
        <label class="col-sm-3">Adet <span class="text-danger">*</span></label>
        <div class="col-sm-9">
            <input name="adet" type="number" min="1" class="form-control" value="{{ old('adet') ?? 1 }}" required>
        </div>
    </div>

    <div class="row mb-1 align-items-center">
        <label class="col-sm-3">Satış Fiyatı<span class="text-danger">*</span></label>
        <div class="col-sm-9">
            <div class="row g-2">
                <div class="col-8">
                    <input name="fiyat" type="number" min="0" step="0.01" class="form-control" value="{{ old('fiyat') }}" required>
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

    <div class="row mb-1 align-items-center">
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
                    <div class="row mb-3"><label class="col-sm-4">Marka:<span class="text-danger">*</span>
                    </label>
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
    <div class="modal-dialog modal-sm ">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Raf Ekle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body"><form id="addShelfForm" action="{{ route('store.shelf.ajax', $tenant_id) }}">
           @csrf 
           <div class="row mb-3">
            <label class="col-sm-4">Raf:<span class="text-danger">*</span></label>
            <div class="col-sm-8">
              <input name="raf_adi" class="form-control" type="text" required>
            </div>
          </div>
          <div class="row">
                <div class="col-sm-12 text-end">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">İptal</button>
                    <input type="submit" class="btn btn-info btn-sm" value="Kaydet">
                </div>
              </div>
        </div>
      </form>
    </div>
  </div>
</div>
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
    var mainFormId = '#addConsignmentDevice';
    
    var mainModalSelector = '#addConsignmentDeviceModal'; 
    function initializeSelect2(selector, placeholder, url) {
        var parentModal = $(selector).closest('.modal');
        if (parentModal.length === 0) {
            parentModal = $('.modal:visible').last();
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
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data }; },
                cache: true
            }
        });
    }

    // Select2'leri sayfa başında başlatıyoruz 
    initializeSelect2(mainFormId + ' select[name="marka_id"]', 'Marka ara...', "/" + tenantId + "/search-brands");
    initializeSelect2(mainFormId + ' select[name="cihaz_id"]', 'Cihaz türü ara...', "/" + tenantId + "/search-devices");
    initializeSelect2(mainFormId + ' select[name="raf_id"]', 'Raf ara...', "/" + tenantId + "/search-shelves");



    // --- MARKA MODALI ---
    $('#addBrandForm').submit(function(e) {
        e.preventDefault(); 
        var form = $(this);  
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                var newOption = new Option(response.text, response.id, true, true);
                $(mainFormId + ' select[name="marka_id"]').append(newOption).trigger('change');
                $('#addBrandModal').modal('hide');
                form[0].reset();
                alert('Marka başarıyla eklendi.');
            },
            error: function() { alert('Bir hata oluştu.'); }
        });
    });
    $('#addBrandModal').on('hidden.bs.modal', function (e) {
        if ($(mainModalSelector).is(':visible')) {
            $('body').addClass('modal-open');
        }
    });
    $(document).on('click', '#addNewBrandBtn', function () {
        var brandModal = new bootstrap.Modal(document.getElementById('addBrandModal'));
        brandModal.show();
    });

    // --- CİHAZ TÜRÜ MODALI ---
    $('#addDeviceTypeForm').submit(function(e) {
        e.preventDefault(); 
        var form = $(this);  
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                var newOption = new Option(response.text, response.id, true, true);
                $(mainFormId + ' select[name="cihaz_id"]').append(newOption).trigger('change');
                $('#addDeviceTypeModal').modal('hide');
                form[0].reset();
                alert('Cihaz Türü başarıyla eklendi.');
            },
            error: function() { alert('Bir hata oluştu.'); }
        });
    });
    $('#addDeviceTypeModal').on('hidden.bs.modal', function (e) {
        if ($(mainModalSelector).is(':visible')) {
            $('body').addClass('modal-open');
        }
    });
    $(document).on('click', '#addNewDeviceTypeBtn', function () {
        var deviceTypeModal = new bootstrap.Modal(document.getElementById('addDeviceTypeModal'));
        deviceTypeModal.show();
    });

    // --- RAF MODALI ---
    $('#addShelfForm').submit(function(e) {
        e.preventDefault(); 
        var form = $(this);  
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                var newOption = new Option(response.text, response.id, true, true);
                $(mainFormId + ' select[name="raf_id"]').append(newOption).trigger('change');
                $('#addShelfModal').modal('hide');
                form[0].reset();
                alert('Raf başarıyla eklendi.');
            },
            error: function() { alert('Bir hata oluştu.'); }
        });
    });
    $('#addShelfModal').on('hidden.bs.modal', function (e) {
        if ($(mainModalSelector).is(':visible')) {
            $('body').addClass('modal-open');
        }
    });
    $(document).on('click', '#addNewShelfBtn', function () {
        var ShelfModal = new bootstrap.Modal(document.getElementById('addShelfModal'));
        ShelfModal.show();
    });


    
    // Ürün kodu maskesi
    $(mainFormId + ' input[name="urunKodu"]').mask('0000000000000', {
        placeholder: '_____________',
        translation: { '0': { pattern: /[0-9]/ } }
    }).on('input', function() {
        let cleanValue = $(this).cleanVal();
        $(this).removeClass('is-invalid is-valid').addClass(cleanValue.length === 13 ? 'is-valid' : 'is-invalid');
    });
    
    // Ürün adı kontrolü
    var checkTimeout;
    $(mainFormId + ' input[name="urunAdi"]').on('input', function () {
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

    // Form gönderim kontrolü
    $(mainFormId).submit(function(event) {
        var urunKoduInput = $(this).find('input[name="urunKodu"]');
        var urunKodu = urunKoduInput.cleanVal();
        if (urunKodu.length !== 13) {
            event.preventDefault();
            alert('Ürün kodu tam 13 haneli olmalıdır!');
            urunKoduInput.focus();
            return false;
        }
        urunKoduInput.val(urunKodu);

        var isValid = true;
        $(this).find('input[required], select[required]').each(function () {
            if (!$(this).val()) {
                isValid = false; return false;
            }
        });
        if (!isValid) {
            event.preventDefault();
            alert('Lütfen yıldızlı zorunlu alanları doldurun.');
        }
    });

    // Ürün Düzenle Modalını açma
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
            error: function () { alert('Düzenleme formu yüklenirken hata oluştu.'); }
        });
    });

    // Düzenleme modalı açıldığında ana formu gizle/göster (Ana modal ID'sine göre düzeltildi)
    $('#editConsignmentModal').on('show.bs.modal', function () {
        $(mainModalSelector).css('visibility', 'hidden');
    }).on('hidden.bs.modal', function () {
        $(mainModalSelector).css('visibility', 'visible');
        // Üst üste modal sorununu çözmek için bu da eklenmeli
        if ($(mainModalSelector).is(':visible')) {
            $('body').addClass('modal-open');
        }
    });

});
</script>










