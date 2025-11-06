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
                            <!-- Loading indicator -->
                            <div id="termsLoading" class="text-center py-5" style="border: 1px solid #dee2e6; border-radius: 4px; background-color: #f8f9fa;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Yükleniyor...</span>
                                </div>
                                <p class="text-muted mt-2 mb-0">Editör yükleniyor...</p>
                            </div>
                            <textarea id="termsEditor" name="terms_content" class="form-control" style="display: none;">{{ $termsContent->content ?? '' }}</textarea>
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
                            <!-- Loading indicator -->
                            <div id="privacyLoading" class="text-center py-5" style="border: 1px solid #dee2e6; border-radius: 4px; background-color: #f8f9fa;">
                                <div class="spinner-border text-success" role="status">
                                    <span class="visually-hidden">Yükleniyor...</span>
                                </div>
                                <p class="text-muted mt-2 mb-0">Editör yükleniyor...</p>
                            </div>
                            <textarea id="privacyEditor" name="privacy_content" class="form-control" style="display: none;">{{ $privacyContent->content ?? '' }}</textarea>
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
    // Önce mevcut TinyMCE instance'larını temizle
    if (tinymce.get('termsEditor')) {
        tinymce.get('termsEditor').remove();
    }
    if (tinymce.get('privacyEditor')) {
        tinymce.get('privacyEditor').remove();
    }
    
    // TinyMCE ortak konfigürasyonu
    const tinymceConfig = {
        height: 400,
        menubar: true,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }',
        promotion: false, // TinyMCE promosyon mesajını kapat
        branding: false   // "Powered by TinyMCE" yazısını kapat
    };
    
    // Kullanım Koşulları editörü
    tinymce.init({
        ...tinymceConfig,
        selector: '#termsEditor',
        setup: function(editor) {
            editor.on('init', function() {
                // Loading indicator'ı gizle
                $('#termsLoading').fadeOut(300, function() {
                    $(this).remove();
                });
                // Editörü göster
                $('#termsEditor').show();
            });
        }
    });
    
    // Gizlilik Politikası editörü
    tinymce.init({
        ...tinymceConfig,
        selector: '#privacyEditor',
        setup: function(editor) {
            editor.on('init', function() {
                // Loading indicator'ı gizle
                $('#privacyLoading').fadeOut(300, function() {
                    $(this).remove();
                });
                // Editörü göster
                $('#privacyEditor').show();
            });
        }
    });
    
    // Form submit
    $('#updateLegalContent').on('submit', function(e) {
        // TinyMCE içeriğini kaydet
        if (tinymce.get('termsEditor')) {
            tinymce.get('termsEditor').save();
        }
        if (tinymce.get('privacyEditor')) {
            tinymce.get('privacyEditor').save();
        }
    });
});
</script>