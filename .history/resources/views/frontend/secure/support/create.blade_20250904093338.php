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
                <div class="card shadow-sm">
                    <div class="card-header text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-plus-circle"></i>
                            Yeni Destek Talebi Oluştur
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('support.store', Auth::user()->tenant_id) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Kategori ve Öncelik Seçimi -->
                            <div class="mb-4">
                                <!-- Kategori - Kart Tabanlı Seçim (Tek Satır) -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                                    <div class="row g-3 flex-nowrap overflow-auto pb-2">
                                        @php
                                        $categoryIcons = [
                                            'teknik_sorun' => ['icon' => 'fas fa-cog', 'color' => 'warning', 'desc' => 'Sistem hataları ve teknik problemler'],
                                            'faturalandirma' => ['icon' => 'fas fa-credit-card', 'color' => 'warning', 'desc' => 'Ödeme ve fatura sorunları'],
                                            'ozellik_talebi' => ['icon' => 'fas fa-lightbulb', 'color' => 'warning', 'desc' => 'Yeni özellik önerileri'],
                                            'genel_destek' => ['icon' => 'fas fa-question-circle', 'color' => 'warning', 'desc' => 'Genel sorular ve yardım'],
                                            'hesap_sorunu' => ['icon' => 'fas fa-user-cog', 'color' => 'warning', 'desc' => 'Hesap erişimi ve profil sorunları']
                                        ];
                                        @endphp
                                        
                                        @foreach($categories as $key => $value)
                                            @php $iconData = $categoryIcons[$key] ?? ['icon' => 'fas fa-folder', 'color' => 'secondary', 'desc' => '']; @endphp
                                            <div class="col-auto" style="min-width: 250px;">
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

                                <!-- Öncelik - Kart Tabanlı Seçim (Tek Satır) -->
                                <div>
                                    <label class="form-label fw-bold">Öncelik <span class="text-danger">*</span></label>
                                    <div class="row g-3 flex-nowrap overflow-auto pb-2">
                                        @php
                                        $priorityOptions = [
                                            'acil' => ['icon' => 'fas fa-exclamation-circle', 'color' => 'warning', 'text' => 'Acil', 'desc' => 'En kısa sürede yanıt (1 saat içinde)'],
                                            'kritik' => ['icon' => 'fas fa-shield-alt', 'color' => 'warning', 'text' => 'Kritik', 'desc' => 'Hızlı yanıt (4 saat içinde)'],
                                            'yuksek' => ['icon' => 'fas fa-exclamation-triangle', 'color' => 'warning', 'text' => 'Yüksek', 'desc' => '24 saat içinde yanıt'],
                                            'orta' => ['icon' => 'fas fa-clock', 'color' => 'warning', 'text' => 'Orta', 'desc' => '48 saat içinde yanıt'],
                                            'dusuk' => ['icon' => 'fas fa-chevron-down', 'color' => 'warning', 'text' => 'Düşük', 'desc' => '72 saat içinde yanıt']
                                        ];
                                        @endphp

                                        @foreach($priorityOptions as $key => $data)
                                            <div class="col-auto" style="min-width: 250px;">
                                                <input type="radio" name="priority" id="priority_{{ $key }}" value="{{ $key }}"
                                                       class="btn-check" {{ old('priority') == $key ? 'checked' : '' }} {{ $key == 'orta' && !old('priority') ? 'checked' : '' }} required>
                                                <label for="priority_{{ $key }}" class="btn btn-outline-{{ $data['color'] }} w-100 h-100 p-2 priority-card">
                                                    <div class="d-flex flex-column align-items-center text-center">
                                                        <i class="{{ $data['icon'] }} fa-2x mb-2"></i>
                                                        <strong class="mb-1">{{ $data['text'] }}</strong>
                                                        <small class="text-muted">{{ $data['desc'] }}</small>
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
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
                                
                                <div class="upload-area border-2 border-dashed border-primary rounded p-4 text-center bg-light" id="uploadArea">
                                    <div class="mb-3">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-2"></i>
                                        <h6>Dosyalarınızı buraya sürükleyin veya seçin</h6>
                                        <p class="text-muted mb-0">Maksimum 3 dosya yükleyebilirsiniz.<br>İzin verilen formatlar: JPG, PNG, PDF, DOC, DOCX</p>
                                    </div>
                                    
                                    <input type="file" name="attachments[]" id="attachments" 
                                           class="form-control d-none @error('attachments.*') is-invalid @enderror" 
                                           multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                    
                                    <button type="button" class="btn btn-outline-primary" id="selectFilesBtn">
                                        <i class="fas fa-folder-open me-2"></i>Dosya Seç
                                    </button>
                                </div>
                                
                                @error('attachments.*')
                                    <div class="text-danger mt-2"><small>{{ $message }}</small></div>
                                @enderror
                            </div>

                            <!-- Dosya Önizleme Alanı -->
                            <div id="file-preview" class="mb-4" style="display: none;">
                                <div class="card">
                                    <div class="card-header py-2">
                                        <small><i class="fas fa-check-circle me-1"></i> Seçilen Dosyalar <span id="fileCount" class="badge bg-primary ms-1"></span></small>
                                    </div>
                                    <div class="card-body p-3">
                                        <div id="file-list" class="d-flex flex-wrap gap-2"></div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="clearFilesBtn">
                                                <i class="fas fa-times me-1"></i> Tüm Dosyaları Temizle
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Butonlar -->
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <a href="{{ route('support.index', Auth::user()->tenant_id) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i> Geri Dön
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
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

.card .text-muted {
    color: #000 !important;
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

.upload-area.dragover {
    background-color: #f0f9ff !important;
    border-color: #3b82f6 !important;
    transform: scale(1.02);
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

.file-item {
    position: relative;
    max-width: 200px;
}

.file-remove-btn {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: none;
    background: #dc3545;
    color: white;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
}

.file-remove-btn:hover {
    background: #c82333;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('attachments');
    const fileList = document.getElementById('file-list');
    const filePreview = document.getElementById('file-preview');
    const selectFilesBtn = document.getElementById('selectFilesBtn');
    const clearFilesBtn = document.getElementById('clearFilesBtn');
    const uploadArea = document.getElementById('uploadArea');
    const fileCount = document.getElementById('fileCount');
    
    let selectedFiles = [];
    const maxFiles = 3;
    const maxSize = 10 * 1024 * 1024; // 10MB

    // Dosya seç butonu click handler
    selectFilesBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        fileInput.click();
    });

    // Upload area click handler
    uploadArea.addEventListener('click', function(e) {
        if (e.target === selectFilesBtn || e.target.closest('#selectFilesBtn')) {
            return; // Button click'ini engelleme
        }
        fileInput.click();
    });

    // Drag and drop handlers
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = Array.from(e.dataTransfer.files);
        handleFiles(files);
    });

    // File input change handler
    fileInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        handleFiles(files);
    });

    // Clear files button
    clearFilesBtn.addEventListener('click', function() {
        selectedFiles = [];
        updateFileList();
        updateFileInput();
    });

    function handleFiles(newFiles) {
        // Dosya sayısı kontrolü
        if (selectedFiles.length + newFiles.length > maxFiles) {
            alert(`Maksimum ${maxFiles} dosya yükleyebilirsiniz.`);
            return;
        }

        // Her dosyayı kontrol et ve ekle
        newFiles.forEach(file => {
            // Dosya boyutu kontrolü
            if (file.size > maxSize) {
                alert(`"${file.name}" dosyası çok büyük. Maksimum dosya boyutu 10MB'dir.`);
                return;
            }

            // Dosya tipi kontrolü
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf', 
                                'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!allowedTypes.includes(file.type)) {
                alert(`"${file.name}" dosya formatı desteklenmiyor.`);
                return;
            }

            // Aynı isimde dosya var mı kontrol et
            const existingFile = selectedFiles.find(f => f.name === file.name);
            if (existingFile) {
                alert(`"${file.name}" dosyası zaten seçili.`);
                return;
            }

            selectedFiles.push(file);
        });

        updateFileList();
        updateFileInput();
    }

    function updateFileList() {
        fileList.innerHTML = '';
        fileCount.textContent = selectedFiles.length;
        
        if (selectedFiles.length > 0) {
            filePreview.style.display = 'block';
            
            selectedFiles.forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'card border-light shadow-sm file-item';
                fileItem.innerHTML = `
                    <div class="card-body p-2 d-flex align-items-center">
                        <div class="me-2">
                            ${getFileIcon(file.name)}
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small text-truncate" style="max-width: 120px;" title="${file.name}">${file.name}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
                        </div>
                        <button type="button" class="file-remove-btn" onclick="removeFile(${index})" title="Dosyayı kaldır">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                fileList.appendChild(fileItem);
            });
        } else {
            filePreview.style.display = 'none';
        }
    }

    function updateFileInput() {
        // Create new FileList
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }

    // Global function for removing files
    window.removeFile = function(index) {
        selectedFiles.splice(index, 1);
        updateFileList();
        updateFileInput();
    };

    function getFileIcon(fileName) {
        const extension = fileName.split('.').pop().toLowerCase();
        const iconMap = {
            'pdf': '<i class="fas fa-file-pdf text-danger fa-lg"></i>',
            'doc': '<i class="fas fa-file-word text-primary fa-lg"></i>',
            'docx': '<i class="fas fa-file-word text-primary fa-lg"></i>',
            'jpg': '<i class="fas fa-file-image text-success fa-lg"></i>',
            'jpeg': '<i class="fas fa-file-image text-success fa-lg"></i>',
            'png': '<i class="fas fa-file-image text-success fa-lg"></i>'
        };
        return iconMap[extension] || '<i class="fas fa-file text-muted fa-lg"></i>';
    }
});
</script>

@endsection