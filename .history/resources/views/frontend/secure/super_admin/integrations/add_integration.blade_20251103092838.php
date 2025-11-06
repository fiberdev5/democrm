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
                        <input type="hidden" name="form_token" id="formTokenIntegration" value="">
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
$('.buyukYaz').keyup(function(){
    this.value = this.value.toUpperCase();
});

$(document).ready(function() {
    let integrationFormSubmitting = false;
    
    // Benzersiz token oluştur
    function generateToken() {
        return Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    // Sayfa yüklendiğinde ilk token'ı oluştur
    $('#formTokenIntegration').val(generateToken());
    
    // Form submit
    $('#addIntegration').on('submit', function(event) {
        // TinyMCE içeriğini kaydet
        if (tinymce.get('elm1')) {
            tinymce.get('elm1').save();
        }
        
        // Token kontrolü
        if (integrationFormSubmitting) {
            event.preventDefault();
            alert('Form gönderiliyor, lütfen bekleyin...');
            return false;
        }
        
        // Validasyon kontrolü
        var formIsValid = true;
        $(this).find('input, select').each(function() {
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
        
        // Token işaretle ve butonu disable et
        integrationFormSubmitting = true;
        $(this).find('input[type="submit"]').prop('disabled', true);
        
        // Form gönderilecek (normal submit devam eder)
        return true;
    });
});
</script>