<style>
  
@media (min-width: 767px) {
.custom-p-r{
    padding-right: 0px !important;
  }
  .custom-p-l{
    padding-left: 0px !important;
  }

}
</style>
<form method="post" id="addIntegration" action="{{ route('super.admin.integration.store')}}" enctype="multipart/form-data">
                        @csrf   
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Entegrasyon Adı<span style="font-weight: bold; color: red;">*</span></label>
                            <div class="col-sm-9 custom-p-l">
                                <input name="name" class="form-control buyukYaz" type="text" placeholder="Entegrasyon adını giriniz" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Kategori<span style="font-weight: bold; color: red;">*</span></label>
                            <div class="col-sm-9 custom-p-l">
                                <select name="category" class="form-select" required>
                                    <option value="" selected disabled>-Seçiniz-</option>
                                    <option value="invoice">Fatura</option>
                                    <option value="sms">SMS</option>
                                    <option value="accounting">Muhasebe</option>
                                    <option value="other">Diğer</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Fiyat (₺)</label>
                            <div class="col-sm-9 custom-p-l">
                                <input name="price" class="form-control" type="number" step="0.01" min="0" placeholder="0.00">
                                <small class="text-muted">Ücretsiz ise boş bırakabilirsiniz</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Logo</label>
                            <div class="col-sm-9 custom-p-l">
                                <input name="logo" class="form-control" type="file" accept="image/*">
                            </div>
                        </div>
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Kısa Açıklama</label>
                            <div class="col-sm-9 custom-p-l">
                                <textarea  name="description" type="text" class="form-control" rows="3" placeholder="Entegrasyon hakkında kısa açıklama yazınız..."></textarea>
                            </div>
                        </div>
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Detaylı Açıklama</label>
                            <div class="col-sm-9 custom-p-l">
                                <textarea id="elm1" name="explanation" type="text" class="form-control" aria-hidden="true"></textarea>
                            </div>
                        </div>
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Durum</label>
                            <div class="col-sm-9 custom-p-l">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">Entegrasyon Aktif</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">               
                            <div class="col-sm-12 gonderBtn">
                                <input type="submit" class="btn btn-sm btn-info waves-effect waves-light" value="Kaydet">
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

    $(document).ready(function () {
        $('#addIntegration').submit(function (event) {
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