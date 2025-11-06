<link href="{{ asset('frontend/css/super_admin/integrations/add_integrations.css') }}" rel="stylesheet" type="text/css" />

<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

<div class="row" style="margin-top: 10px;">
    <div class="col-12">
        <div class="card" style="box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);">
            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px;">
                <h4 class="card-title mb-0" style="color: white; font-weight: 600;">
                    <i class="fas fa-file-contract me-2"></i>Kullanım Koşulları ve Gizlilik Politikası
                </h4>
            </div>
            <div class="card-body">
                <form method="post" id="updateLegalContent" action="{{ route('update.legal.settings', $firma->id) }}">
                    @csrf
                    
                    <!-- Kullanım Koşulları -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <label class="form-label fw-bold">
                                <i class="fas fa-file-alt text-primary me-2"></i>Kullanım Koşulları
                            </label>
                            <textarea id="termsEditor" name="terms_content" class="form-control summernote">{{ $termsContent->content ?? '' }}</textarea>
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
                            <textarea id="privacyEditor" name="privacy_content" class="form-control summernote">{{ $privacyContent->content ?? '' }}</textarea>
                            <small class="text-muted">Kullanıcılara gösterilecek gizlilik politikasını buraya yazın.</small>
                        </div>
                    </div>
                    
                    <!-- Kaydet Butonu -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-success waves-effect waves-light">
                                <i class="fas fa-save me-2"></i>Kaydet
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<script>
$(document).ready(function() {
    // Summernote editörlerini başlat
    $('.summernote').summernote({
        height: 400,
        minHeight: 300,
        maxHeight: 600,
        focus: false,
        lang: 'tr-TR',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video', 'hr']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        styleTags: ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
        fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Helvetica', 'Impact', 'Tahoma', 'Times New Roman', 'Verdana'],
        fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '22', '24', '26', '28', '36', '48', '72'],
        callbacks: {
            onInit: function() {
                console.log('Summernote başlatıldı');
            }
        }
    });
    
    // Form submit
    $('#updateLegalContent').on('submit', function(e) {
        // Summernote içeriği otomatik olarak textarea'ya kaydedilir, ek işlem gerekmez
        console.log('Form gönderiliyor...');
    });
});
</script>