<form method="post" id="addStock" action="{{ route('store.stock', $firma->id) }}" enctype="multipart/form-data">
  @csrf
  <div class="row mb-1 align-items-center">
    <label class="col-sm-3">Markalar</label>
    <div class="col-sm-9">
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

  <div class="row mb-1">
    <label class="col-sm-3">Cihaz Türü</label>
    <div class="col-sm-9">
      <div class="input-group">
        <select name="cihaz_id" class="form-select">
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
        <select name="urunKategori" class="form-select">
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
    <label class="col-sm-3">Raf Seç</label>
    <div class="col-sm-9">
      <div class="input-group">
        <select name="raf_id" class="form-select">
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
    <label class="col-sm-3">Ürün Kodu <span class="text-danger">*</span></label>
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
    <label class="col-sm-3">Ürün Adı </label>
    <div class="col-sm-9">
      <input name="urunAdi" type="text" class="form-control" value="{{ old('urunAdi') }}" required>
      <div id="urunAdiUyari"></div>
    </div>
  </div>



 
<div class="row mb-0">
    <label class="col-sm-3">Satış Fiyatı</label>
    <div class="col-sm-9">
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
$(document).ready(function () {

    // --- 1. MODALLARI SAYFA YÜKLENİRKEN BİR KERE TANIMLAYIN ---
    // Bu sayede her seferinde yeni nesne oluşturma karmaşasından kurtuluruz.
    var addBrandModal = new bootstrap.Modal(document.getElementById('addBrandModal'));
    var addDeviceTypeModal = new bootstrap.Modal(document.getElementById('addDeviceTypeModal'));
    var addCategoryModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
    var addShelfModal = new bootstrap.Modal(document.getElementById('addShelfModal'));
    var editStockModal = new bootstrap.Modal(document.getElementById('editStockModal'));

    var tenantId = "{{ $firma->id }}";

    // --- 2. ÜRÜN KODU MASKESİ VE FORM KONTROLLERİ ---
    function initializeMask(selector) {
        $(selector).mask('0000000000000', {
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
    }

    // Ana formdaki ürün kodu için maskeyi başlat
    initializeMask('form#addStock input[name="urunKodu"]');

    $('#addStock').submit(function(event) {
        var urunKoduInput = $('input[name="urunKodu"]');
        var urunKodu = urunKoduInput.cleanVal();

        if (urunKodu.length !== 13) {
            event.preventDefault();
            alert('Ürün kodu tam 13 haneli olmalıdır!');
            urunKoduInput.focus();
            return false;
        }
        urunKoduInput.val(urunKodu);
    });

    // --- 3. ÜRÜN ADI KONTROLÜ (AJAX) ---
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
                            '<button type="button" id="openEditModalBtn" data-url="' + res.edit_url + '" class="btn btn-sm btn-primary ms-2">Ürünü Düzenle</button>' +
                            '</div>';
                        $('#urunAdiUyari').html(warningHtml);
                    }
                }
            });
        }, 600);
    });

    // --- 4. YENİ KAYIT EKLEME MODALLARI (MARKA, CİHAZ, KATEGORİ, RAF) ---

    // Generic AJAX Form Submission Function
    function handleAjaxFormSubmit(form, modalInstance, selectName, successMessage) {
        form.submit(function(e) {
            e.preventDefault();
            var formData = $(this);
            $.ajax({
                url: formData.attr('action'),
                type: 'POST',
                data: formData.serialize(),
                success: function(response) {
                    var newOption = new Option(response.text, response.id, true, true);
                    $('#addStock select[name="' + selectName + '"]').append(newOption).trigger('change');
                    modalInstance.hide(); // Tanımlanan modal nesnesini kullanarak kapat
                    formData[0].reset();
                    alert(successMessage);
                },
                error: function(xhr) {
                    alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                    console.log(xhr.responseText);
                }
            });
        });
    }

    // Formları ve modalları işle
    handleAjaxFormSubmit($('#addBrandForm'), addBrandModal, 'marka_id', 'Marka başarıyla eklendi.');
    handleAjaxFormSubmit($('#addDeviceTypeForm'), addDeviceTypeModal, 'cihaz_id', 'Cihaz Türü başarıyla eklendi.');
    handleAjaxFormSubmit($('#addCategoryForm'), addCategoryModal, 'urunKategori', 'Kategori başarıyla eklendi.');
    handleAjaxFormSubmit($('#addShelfForm'), addShelfModal, 'raf_id', 'Raf başarıyla eklendi.');

    // Modal açma butonları
    $('#addNewBrandBtn').click(function() { addBrandModal.show(); });
    $('#addNewDeviceTypeBtn').click(function() { addDeviceTypeModal.show(); });
    $('#addNewCategoryBtn').click(function() { addCategoryModal.show(); });
    $('#addNewShelfBtn').click(function() { addShelfModal.show(); });

    // --- 5. ÜRÜN DÜZENLEME MODALI ---
    
    // Düzenle butonuna tıklandığında (dinamik olarak eklendiği için .on() kullanıyoruz)
    $(document).on('click', '#openEditModalBtn', function() {
        var url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            success: function(res) {
                if(res.html) {
                    $('#editStockModal .modal-body').html(res.html);
                    editStockModal.show(); // Tanımlanan modal nesnesiyle aç
                    
                    // !!! ÖNEMLİ !!!
                    // Modal içine yeni bir form yüklendiği için,
                    // bu formdaki ürün kodu alanına maskeyi tekrar uygulamalıyız.
                    initializeMask('#editStockModal input[name="urunKodu"]');
                } else {
                    alert('Düzenleme formu yüklenemedi.');
                }
            },
            error: function() {
                alert('Düzenleme formu yüklenirken hata oluştu.');
            }
        });
    });

    // Düzenleme modalı açıldığında/kapandığında ana formu gizle/göster
    $('#editStockModal').on('show.bs.modal', function () {
        $('#addStock').css('visibility', 'hidden');
    }).on('hidden.bs.modal', function () {
        $('#addStock').css('visibility', 'visible');
    });

});
</script>








