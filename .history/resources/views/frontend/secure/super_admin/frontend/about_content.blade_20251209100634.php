@extends('frontend.secure.user_master')
@section('user')
<div class="page-content">
    <div class="container-fluid">
        
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Hakkımızda İçerik Yönetimi</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Hakkımızda</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('super.admin.frontend.about-content.update') }}" method="POST" id="aboutContentForm">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label">Hakkımızda İçeriği (JSON Format)</label>
                                <div class="d-flex gap-2 mb-2">
                                    <button type="button" class="btn btn-sm btn-success" id="validateAboutJson">
                                        <i class="fas fa-check me-1"></i> JSON Doğrula
                                    </button>
                                    <button type="button" class="btn btn-sm btn-info" id="formatAboutJson">
                                        <i class="fas fa-indent me-1"></i> Düzenle
                                    </button>
                                </div>
                                
                                <div id="aboutValidationMessage"></div>
                                
                                <textarea 
                                    class="form-control" 
                                    id="about_content_json" 
                                    name="content" 
                                    rows="30" 
                                    style="font-family: 'Courier New', monospace; font-size: 13px; line-height: 1.6;">{{ json_encode($about->content ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</textarea>
                            </div>

                            <div class="alert alert-info">
                                <strong><i class="fas fa-info-circle me-1"></i> JSON Yapısı:</strong>
                                <ul class="mb-0 mt-2">
                                    <li><code>hero</code>: Ana banner bölümü (badge, title, description, image, stats)</li>
                                    <li><code>mission</code>: Misyon bölümü (icon, title, text)</li>
                                    <li><code>vision</code>: Vizyon bölümü (icon, title, text)</li>
                                    <li><code>story</code>: Hikaye bölümü (image, timeline array)</li>
                                    <li><code>values</code>: Değerler bölümü (items array)</li>
                                    <li><code>stats</code>: İstatistikler array</li>
                                    <li><code>team</code>: Ekip bölümü (intro, tags array)</li>
                                </ul>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-1"></i> Kaydet
                                </button>
                                <a href="{{ url('/hakkimizda') }}" target="_blank" class="btn btn-info">
                                    <i class="fas fa-external-link-alt me-1"></i> Sayfayı Görüntüle
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    // JSON Doğrulama
    $('#validateAboutJson').on('click', function() {
        try {
            const json = JSON.parse($('#about_content_json').val());
            $('#aboutValidationMessage').html('<div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>JSON formatı geçerli!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
        } catch(e) {
            $('#aboutValidationMessage').html('<div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-times-circle me-2"></i>Hata: ' + e.message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
        }
    });

    // JSON Düzenleme
    $('#formatAboutJson').on('click', function() {
        try {
            const json = JSON.parse($('#about_content_json').val());
            $('#about_content_json').val(JSON.stringify(json, null, 2));
            toastr.success('JSON düzenlendi');
        } catch(e) {
            toastr.error('Geçersiz JSON formatı!');
        }
    });

    // Form Submit
    $('#aboutContentForm').on('submit', function(e) {
        try {
            const json = JSON.parse($('#about_content_json').val());
            // JSON geçerli, form gönder
        } catch(e) {
            e.preventDefault();
            toastr.error('Geçersiz JSON formatı! Lütfen düzeltin.');
            return false;
        }
    });
});
</script>
@endsection