{{-- resources/views/support/create.blade.php --}}

@extends('frontend.secure.user_master')

@section('user')
<div class="page-content">
    <div class="container-fluid">
        <!-- Başlık -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        <i class="fas fa-headset text-primary me-2"></i>
                        Yeni Destek Talebi
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('secure.home', Auth::user()->tenant_id) }}">Ana Sayfa</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('support.index', Auth::user()->tenant_id) }}">Destek Taleplerim</a></li>
                            <li class="breadcrumb-item active">Yeni Talep</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bilgilendirme Kartı -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm" style="background: linear-gradient(45deg, #e3f2fd, #f1f8e9);">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle fa-2x text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="alert-heading mb-1">Destek Talep Rehberi</h6>
                            <p class="mb-0 small">Sorununuzu en hızlı şekilde çözebilmemiz için lütfen kategori ve önceliği doğru seçin, detaylı açıklama yazın.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm ">
                    <div class="card-header text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-plus-circle "></i>
                            Yeni Destek Talebi Oluştur
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('support.store', Auth::user()->tenant_id) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Kategori ve Öncelik Seçimi -->
                            <div class="row mb-4">
                                <div class="col-8">
                                    <h6 class="text-muted mb-3">
                                        <i class="fas fa-tags me-2"></i>Kategori ve Öncelik Seçimi
                                    </h6>
                                </div>
                                
                                <!-- Kategori - Kart Tabanlı Seçim -->
                                <div class="col-12 mb-4">
                                    <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                                    <div class="row g-3">
                                        @php
                                        $categoryIcons = [
                                            'teknik_sorun' => ['icon' => 'fas fa-cog', 'color' => 'danger', 'desc' => 'Sistem hataları ve teknik problemler'],
                                            'faturalandirma' => ['icon' => 'fas fa-credit-card', 'color' => 'warning', 'desc' => 'Ödeme ve fatura sorunları'],
                                            'ozellik_talebi' => ['icon' => 'fas fa-lightbulb', 'color' => 'info', 'desc' => 'Yeni özellik önerileri'],
                                            'genel_destek' => ['icon' => 'fas fa-question-circle', 'color' => 'primary', 'desc' => 'Genel sorular ve yardım'],
                                            'hesap_sorunu' => ['icon' => 'fas fa-user-cog', 'color' => 'secondary', 'desc' => 'Hesap erişimi ve profil sorunları']
                                        ];
                                        @endphp
                                        
                                        @foreach($categories as $key => $value)
                                            @php $iconData = $categoryIcons[$key] ?? ['icon' => 'fas fa-folder', 'color' => 'secondary', 'desc' => '']; @endphp
                                            <div class="col-md-4 col-lg-3">
                                                <input type="radio" name="category" id="category_{{ $key }}" value="{{ $key }}" 
                                                       class="btn-check" {{ old('category') == $key ? 'checked' : '' }} required>
                                                <label for="category_{{ $key }}" class="btn btn-outline-{{ $iconData['color'] }} w-100 h-100 p-1 category-card">
                                                    <div class="d-flex flex-column align-items-center text-center">
                                                        <i class="{{ $iconData['icon'] }} fa-2x mb-2"></i>
                                                        <strong class="mb-1">{{ $value }}</strong>
                                                        <small class="text-muted">{{ $iconData['desc'] }}</small>
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('category')
                                        <div class="text-danger mt-2"><small>{{ $message }}</small></div>
                                    @enderror
                                </div>

                                <!-- Öncelik - Kart Tabanlı Seçim -->
                                <div class="col-12">
                                    <label class="form-label fw-bold">Öncelik <span class="text-danger">*</span></label>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <input type="radio" name="priority" id="priority_orta" value="orta" 
                                                   class="btn-check" {{ old('priority') == 'orta' ? 'checked' : '' }} required>
                                            <label for="priority_orta" class="btn btn-outline-warning w-100 p-1 priority-card">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-clock me-2 fa-lg"></i>
                                                    <div class="text-start">
                                                        <strong>Orta Öncelik</strong>
                                                        <small class="d-block text-muted">48 saat içinde yanıt</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="radio" name="priority" id="priority_yuksek" value="yuksek" 
                                                   class="btn-check" {{ old('priority') == 'yuksek' ? 'checked' : '' }}>
                                            <label for="priority_yuksek" class="btn btn-outline-danger w-100 p-1 priority-card">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-exclamation-triangle me-2 fa-lg"></i>
                                                    <div class="text-start">
                                                        <strong>Yüksek Öncelik</strong>
                                                        <small class="d-block text-muted">24 saat içinde yanıt</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    @error('priority')
                                        <div class="text-danger mt-2"><small>{{ $message }}</small></div>
                                    @enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Talep Detayları -->
                            <div class="mb-4">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-edit me-2"></i>Talep Detayları
                                </h6>
                                
                                <!-- Konu -->
                                <div class="mb-3">
                                    <label for="subject" class="form-label fw-bold">Konu <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-heading text-muted"></i>
                                        </span>
                                        <input type="text" name="subject" id="subject" 
                                               class="form-control form-control-lg @error('subject') is-invalid @enderror" 
                                               value="{{ old('subject') }}" 
                                               placeholder="Destek talebinizin konusunu kısaca özetleyin" required>
                                    </div>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Detaylı Açıklama -->
                                <div class="mb-3">
                                    <label for="description" class="form-label fw-bold">Detaylı Açıklama <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <textarea name="description" id="description" rows="6" 
                                                  class="form-control @error('description') is-invalid @enderror" 
                                                  placeholder="Sorununuzu veya talebinizi detaylı olarak açıklayın...&#10;&#10;• Ne oldu?&#10;• Ne bekliyordunuz?&#10;• Hangi adımları izlediniz?&#10;• Hata mesajı aldınız mı?" 
                                                  required>{{ old('description') }}</textarea>
                                        <div class="position-absolute top-0 end-0 p-2">
                                            <i class="fas fa-comment-dots text-muted"></i>
                                        </div>
                                    </div>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Dosya Ekleme -->
                            <div class="mb-4">
                                <h6 class="text-muted mb-3">
                                    <i class="fas fa-paperclip me-2"></i>Belge/Fotoğraf Ekle (İsteğe Bağlı)
                                </h6>
                                
                                <div class="upload-area border-2 border-dashed border-primary rounded p-4 text-center bg-light">
                                    <div class="mb-3">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-2"></i>
                                        <h6>Dosyalarınızı buraya sürükleyin veya seçin</h6>
                                        <p class="text-muted mb-0">Maksimum dosya boyutu: 10MB. İzin verilen formatlar: JPG, PNG, PDF, DOC, DOCX</p>
                                    </div>
                                    
                                    <input type="file" name="attachments[]" id="attachments" 
                                           class="form-control d-none @error('attachments.*') is-invalid @enderror" 
                                           multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                    
                                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('attachments').click()">
                                        <i class="fas fa-folder-open me-2"></i>Dosya Seç
                                    </button>
                                </div>
                                
                                @error('attachments.*')
                                    <div class="text-danger mt-2"><small>{{ $message }}</small></div>
                                @enderror
                            </div>

                            <!-- Dosya Önizleme Alanı -->
                            <div id="file-preview" class="mb-4" style="display: none;">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white py-2">
                                        <small><i class="fas fa-check-circle me-1"></i> Seçilen Dosyalar</small>
                                    </div>
                                    <div class="card-body p-3">
                                        <div id="file-list" class="d-flex flex-wrap gap-2"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Butonlar -->
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <a href="{{ route('support.index', Auth::user()->tenant_id) }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i> Geri Dön
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-paper-plane me-2"></i> Destek Talebi Gönder
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.category-card, .priority-card {
    transition: all 0.3s ease;
    min-height: 120px;
    border: 2px solid transparent !important;
}

.category-card:hover, .priority-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.btn-check:checked + .category-card,
.btn-check:checked + .priority-card {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.upload-area {
    transition: all 0.3s ease;
    cursor: pointer;
}

.upload-area:hover {
    background-color: #f8f9ff !important;
    border-color: #4f46e5 !important;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.form-control:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
}

.form-control-lg {
    font-size: 1.1rem;
    padding: 0.75rem 1rem;
}

.input-group-text {
    border-color: #e2e8f0;
}

.card {
    border-radius: 15px;
    overflow: hidden;
}

.btn-lg {
    padding: 0.75rem 2rem;
    font-size: 1.1rem;
}
</style>

<script>
document.getElementById('attachments').addEventListener('change', function(e) {
    const fileList = document.getElementById('file-list');
    const filePreview = document.getElementById('file-preview');
    const files = e.target.files;
    
    fileList.innerHTML = '';
    
    if (files.length > 0) {
        filePreview.style.display = 'block';
        
        Array.from(files).forEach(function(file, index) {
            const fileItem = document.createElement('div');
            fileItem.className = 'card border-light shadow-sm';
            fileItem.innerHTML = `
                <div class="card-body p-2 d-flex align-items-center">
                    <div class="me-2">
                        ${getFileIcon(file.name)}
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold small">${file.name}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            `;
            fileList.appendChild(fileItem);
        });
    } else {
        filePreview.style.display = 'none';
    }
});

function getFileIcon(fileName) {
    const extension = fileName.split('.').pop().toLowerCase();
    const iconMap = {
        'pdf': '<i class="fas fa-file-pdf text-danger"></i>',
        'doc': '<i class="fas fa-file-word text-primary"></i>',
        'docx': '<i class="fas fa-file-word text-primary"></i>',
        'jpg': '<i class="fas fa-file-image text-success"></i>',
        'jpeg': '<i class="fas fa-file-image text-success"></i>',
        'png': '<i class="fas fa-file-image text-success"></i>'
    };
    return iconMap[extension] || '<i class="fas fa-file text-muted"></i>';
}

// Upload area click handler
document.querySelector('.upload-area').addEventListener('click', function() {
    document.getElementById('attachments').click();
});
</script>

@endsection