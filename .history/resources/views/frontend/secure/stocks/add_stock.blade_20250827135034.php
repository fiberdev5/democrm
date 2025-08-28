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
    // Select2'yi ortak bir fonksiyonla başlatmak için bir yardımcı fonksiyon
    function initializeSelect2(selector, placeholder, url) {
    // Select2 zaten başlatılmışsa önce yok et
    if ($(selector).data('select2')) {
        $(selector).select2('destroy');
    }

    var parentModal = $(selector).closest('.modal');
    // Eğer select2 bir modal içinde değilse (ana sayfadaki filtreler gibi), body'yi hedefle.
    // Eğer bir modal içindeyse, o modalı parent olarak ata.
    // Burada önemli olan, alt modal açıldığında üstteki modalın da açık olabilmesidir.
    // Bu yüzden "visible" olan son modalı bulmaya çalışıyoruz.
    var dropdownParentSelector = parentModal.length ? parentModal : $('body');
    
    // Eğer ana modal kapalıyken alt modal açılıyorsa ve ana select2'yi tetikliyorsa,
    // o zaman dropdownParent olarak en üstteki body'yi kullanmak daha güvenli olabilir.
    // Ancak genellikle select2'ler açıldıkları modalın içinde kalmalı.
    // Eğer sorun devam ederse 'body' kullanmayı düşünebilirsiniz: dropdownParent: $('body')
    
    $(selector).select2({
        theme: "bootstrap-5",
        placeholder: placeholder,
        allowClear: true,
        dropdownParent: dropdownParentSelector, // Dinamik olarak doğru parent'ı ayarla
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
            data: form.serialize(), // Formdaki tüm verileri al
            success: function(response) {
            // Select2'nin beklediği formatta yeni bir "Option" nesnesi oluştur
            var newOption = new Option(response.text, response.id, true, true);
            // Bu yeni seçeneği Select2'ye ekle ve seçili hale getir
            $('#addStock select[name="marka_id"]').append(newOption).trigger('change');
            
            $('#addBrandModal').modal('hide');
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
    //Marka Ekleme modalı kapandığında, arkadaki modalın scroll problemini düzelt
    $('#addBrandModal').on('hidden.bs.modal', function (e) {
        if ($('#addStockModal').is(':visible')) {
            $('body').addClass('modal-open');
        }
    });
    // Marka Ekle (+) butonuna tıklandığında modalı manuel olarak aç
    $(document).on('click', '#addNewBrandBtn', function () {
        // Bootstrap'in JavaScript API'sini kullanarak yeni bir modal örneği oluştur
        var brandModal = new bootstrap.Modal(document.getElementById('addBrandModal'));
        // Modalı göster
        brandModal.show();
    });

    // Yeni Cihaz Türü Ekleme Formu Gönderimi
    $('#addDeviceTypeForm').submit(function(e) {
        e.preventDefault(); 
        var form = $(this);  
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(), // Formdaki tüm verileri al
            success: function(response) {
                var newOption = new Option(response.text, response.id, true, true);
                //Ana stok ekleme formundaki marka dropdown'ına bu yeni option'ı ekle ve seçili yap
                $('#addStock select[name="cihaz_id"]').append(newOption).trigger('change');
                //"Yeni Marka Ekle" modalını kapat
                $('#addDeviceTypeModal').modal('hide');
                //Modal içindeki formu temizle ki bir sonraki açılışta boş gelsin
                form[0].reset();
                alert('Cihaz Türü başarıyla eklendi.');
            },
            error: function(xhr) {
                // Bir hata olursa kullanıcıyı bilgilendir
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                console.log(xhr.responseText);
            }
        });
    });
    //Cihaz Türü Ekleme modalı kapandığında, arkadaki modalın scroll problemini düzelt
    $('#addDeviceTypeModal').on('hidden.bs.modal', function (e) {
        if ($('#addStockModal').is(':visible')) {
            $('body').addClass('modal-open');
        }
    });
    //Cihaz Türü Ekle (+) butonuna tıklandığında modalı manuel olarak aç
    $(document).on('click', '#addNewDeviceTypeBtn', function () {
        // Bootstrap'in JavaScript API'sini kullanarak yeni bir modal örneği oluştur
        var deviceTypeModal = new bootstrap.Modal(document.getElementById('addDeviceTypeModal'));
        // Modalı göster
        deviceTypeModal.show();
    });

    // Yeni Kategori Ekleme Formu Gönderimi
    $('#addCategoryForm').submit(function(e) {
        e.preventDefault(); 
        var form = $(this);  
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(), // Formdaki tüm verileri al
            success: function(response) {
                var newOption = new Option(response.text, response.id, true, true);
                //Ana stok ekleme formundaki marka dropdown'ına bu yeni option'ı ekle ve seçili yap
                $('#addStock select[name="urunKategori"]').append(newOption).trigger('change');
                //"Yeni Marka Ekle" modalını kapat
                $('#addCategoryModal').modal('hide');
                //Modal içindeki formu temizle ki bir sonraki açılışta boş gelsin
                form[0].reset();
                alert('Cihaz Kategori başarıyla eklendi.');
            },
            error: function(xhr) {
                // Bir hata olursa kullanıcıyı bilgilendir
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                console.log(xhr.responseText);
            }
        });
    });
    //Kategori Ekleme modalı kapandığında, arkadaki modalın scroll problemini düzelt
    $('#addCategoryModal').on('hidden.bs.modal', function (e) {
        if ($('#addStockModal').is(':visible')) {
            $('body').addClass('modal-open');
        }
    });
    //Kategori Ekle (+) butonuna tıklandığında modalı manuel olarak aç
    $(document).on('click', '#addNewCategoryBtn', function () {
        // Bootstrap'in JavaScript API'sini kullanarak yeni bir modal örneği oluştur
        var CategoryModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
        // Modalı göster
        CategoryModal.show();
    });

    // Yeni Raf Ekleme Formu Gönderimi
    $('#addShelfForm').submit(function(e) {
        e.preventDefault(); 
        var form = $(this);  
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(), // Formdaki tüm verileri al
            success: function(response) {
                var newOption = new Option(response.text, response.id, true, true);
                //Ana stok ekleme formundaki marka dropdown'ına bu yeni option'ı ekle ve seçili yap
                $('#addStock select[name="raf_id"]').append(newOption).trigger('change');
                //"Yeni Marka Ekle" modalını kapat
                $('#addShelfModal').modal('hide');
                //Modal içindeki formu temizle ki bir sonraki açılışta boş gelsin
                form[0].reset();
                alert('Raf başarıyla eklendi.');
            },
            error: function(xhr) {
                // Bir hata olursa kullanıcıyı bilgilendir
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                console.log(xhr.responseText);
            }
        });
    });
    //Raf Ekleme modalı kapandığında, arkadaki modalın scroll problemini düzelt
    $('#addShelfModal').on('hidden.bs.modal', function (e) {
        if ($('#addStockModal').is(':visible')) {
            $('body').addClass('modal-open');
        }
    });
    //Raf Ekle (+) butonuna tıklandığında modalı manuel olarak aç
    $(document).on('click', '#addNewShelfBtn', function () {
        // Bootstrap'in JavaScript API'sini kullanarak yeni bir modal örneği oluştur
        var ShelfModal = new bootstrap.Modal(document.getElementById('addShelfModal'));
        // Modalı göster
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










