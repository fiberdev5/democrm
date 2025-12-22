<div class="row" style="margin-top: 10px;">
    <div class="col-12">
        <div class="card" style="box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);">
            <div class="card-body">
                <form method="post" id="updateLegalContent" action="{{ route('update.legal.settings', $firma->id) }}">
                    @csrf
                    <!-- Kullanım Koşulları -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <label class="form-label fw-bold">
                                <i class="fas fa-file-alt text-primary me-2"></i>Kullanım Koşulları
                            </label>
                            <textarea id="termsEditor" name="terms_content" class="form-control">{{ $termsContent->content ?? '' }}</textarea>
                            <small class="text-muted">Kullanıcılara gösterilecek kullanım koşullarını buraya yazın.</small>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Gizlilik Politikası -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <label class="form-label fw-bold">
                                <i class="fas fa-shield-alt text-success me-2"></i>Gizlilik Politikası
                            </label>
                            <textarea id="privacyEditor" name="privacy_content" class="form-control">{{ $privacyContent->content ?? '' }}</textarea>
                            <small class="text-muted">Kullanıcılara gösterilecek gizlilik politikasını buraya yazın.</small>
                        </div>
                    </div>
                    
                    <!-- Kaydet Butonu -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-info waves-effect waves-light">Kaydet</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Mevcut instance'ları temizle
    tinymce.remove();
    
    // Basit TinyMCE konfigürasyonu
    tinymce.init({
        selector: '#termsEditor, #privacyEditor',
        height: 500,
        menubar: false,
        plugins: 'lists link image code table',
        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
        language: 'tr_TR',
        setup: function(editor) {
            editor.on('init', function() {
                console.log('Editor yüklendi: ' + editor.id);
            });
        }
    });
    
    // Form submit
    $('#updateLegalContent').on('submit', function(e) {
        tinymce.triggerSave();
    });
});
</script>