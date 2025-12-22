<form method="post" id="editIntegration" action="{{ route('super.admin.integration.update', $integration->id)}}" enctype="multipart/form-data">
                        @csrf   
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Entegrasyon Adı<span style="font-weight: bold; color: red;">*</span></label>
                            <div class="col-sm-9 custom-p-l">
                                <input name="name" class="form-control buyukYaz" type="text" placeholder="Entegrasyon adını giriniz" value="{{ $integration->name }}" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Kategori<span style="font-weight: bold; color: red;">*</span></label>
                            <div class="col-sm-9 custom-p-l">
                                <select name="category" class="form-select" required>
                                    <option value="" disabled>-Seçiniz-</option>
                                    <option value="invoice" {{ $integration->category == 'invoice' ? 'selected' : '' }}>Fatura</option>
                                    <option value="sms" {{ $integration->category == 'sms' ? 'selected' : '' }}>SMS</option>
                                    <option value="accounting" {{ $integration->category == 'accounting' ? 'selected' : '' }}>Muhasebe</option>
                                    <option value="other" {{ $integration->category == 'other' ? 'selected' : '' }}>Diğer</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Fiyat (₺)</label>
                            <div class="col-sm-9 custom-p-l">
                                <input name="price" class="form-control" type="number" step="0.01" min="0" placeholder="0.00" value="{{ $integration->price }}">
                                <small class="text-muted">Ücretsiz ise boş bırakabilirsiniz</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Logo</label>
                            <div class="col-sm-9 custom-p-l">
                                <input name="logo" class="form-control" type="file" accept="image/*" id="logoInput">
                                @if($integration->logo)
                                    <div class="mt-2">
                                        <img src="{{ asset($integration->logo) }}" alt="Mevcut Logo" style="max-width: 150px; max-height: 150px; border: 1px solid #ddd; padding: 5px; border-radius: 5px;" id="logoPreview">
                                        <p class="text-muted mt-1">Mevcut Logo - Yeni logo seçerseniz güncellenecektir</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Kısa Açıklama</label>
                            <div class="col-sm-9 custom-p-l">
                                <textarea name="description" type="text" class="form-control" rows="3" placeholder="Entegrasyon hakkında kısa açıklama yazınız...">{{ $integration->description }}</textarea>
                            </div>
                        </div>
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Detaylı Açıklama</label>
                            <div class="col-sm-9 custom-p-l">
                                <textarea id="elm1" name="explanation" type="text" class="form-control" aria-hidden="true">{{ $integration->explanation }}</textarea>
                            </div>
                        </div>
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Durum</label>
                            <div class="col-sm-9 custom-p-l">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $integration->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Entegrasyon Aktif</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">               
                            <div class="col-sm-12 gonderBtn">
                                <input type="submit" class="btn btn-sm btn-info waves-effect waves-light" value="Güncelle">
                                <a href="{{route('super.admin.integrations')}}" class="btn btn-sm btn-light waves-effect">İptal</a>
                            </div>
                        </div>
                    </form>

<script>
    tinymce.init({
        selector: '#elm1',
        height: 300,
        language: 'tr',
        plugins: [
            'advlist', 'autolink', 'link', 'image', 'lists', 'charmap', 'preview', 'anchor', 'pagebreak',
            'searchreplace', 'wordcount', 'visualblocks', 'code', 'fullscreen', 'insertdatetime', 'media', 
            'table', 'emoticons', 'help'
        ],
        toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | link image | print preview media fullscreen | ' +
            'forecolor backcolor emoticons | help',
        menu: {
            favs: {title: 'Favoriler', items: 'code visualaid | searchreplace | emoticons'}
        },
        menubar: 'favs file edit view insert format tools table help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
    });
    
    $('.buyukYaz').keyup(function(){
        this.value = this.value.toUpperCase();
    });

    // Logo önizleme
    $('#logoInput').change(function(){
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#logoPreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(file);
        }
    });

    $(document).ready(function () {
        $('#editIntegration').submit(function (event) {
            var formIsValid = true;
            $(this).find('input, select').each(function () {
                var isRequired = $(this).prop('required');
                var isEmpty = !$(this).val();
                if (isRequired && isEmpty) {
                    formIsValid = false;
                    return false;
                }
            });
        
            if (!formIsValid) {
                event.preventDefault();
                alert('Lütfen zorunlu alanları doldurun.');
                return false;
            }
        });
    });
</script>