{{-- resources/views/support/create.blade.php --}}

@extends('frontend.secure.user_master')

@section('user')
<div class="page-content">
    <div class="container-fluid">
        <!-- Başlık -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Yeni Destek Talebi</h4>
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

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Yeni Destek Talebi Oluştur</h5>

                        <form action="{{ route('support.store', Auth::user()->tenant_id) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- Kategori -->
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                                        <option value="">Kategori Seçin</option>
                                        @foreach($categories as $key => $value)
                                            <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Öncelik -->
                                <div class="col-md-6 mb-3">
                                    <label for="priority" class="form-label">Öncelik <span class="text-danger">*</span></label>
                                    <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                        <option value="">Öncelik Seçin</option>
                                        <option value="orta" {{ old('priority') == 'orta' ? 'selected' : '' }}>Orta</option>
                                        <option value="yuksek" {{ old('priority') == 'yuksek' ? 'selected' : '' }}>Yüksek</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Konu -->
                            <div class="mb-3">
                                <label for="subject" class="form-label">Konu <span class="text-danger">*</span></label>
                                <input type="text" name="subject" id="subject" 
                                       class="form-control @error('subject') is-invalid @enderror" 
                                       value="{{ old('subject') }}" 
                                       placeholder="Destek talebinizin konusunu kısaca özetleyin" required>
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Detaylı Açıklama -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Detaylı Açıklama <span class="text-danger">*</span></label>
                                <textarea name="description" id="description" rows="6" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          placeholder="Sorununuzu veya talebinizi detaylı olarak açıklayın..." required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Dosya Ekleme -->
                            <div class="mb-4">
                                <label for="attachments" class="form-label">Belge/Fotoğraf Ekle</label>
                                <input type="file" name="attachments[]" id="attachments" 
                                       class="form-control @error('attachments.*') is-invalid @enderror" 
                                       multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Maksimum dosya boyutu: 10MB. İzin verilen formatlar: JPG, PNG, PDF, DOC, DOCX
                                </div>
                                @error('attachments.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Dosya Önizleme Alanı -->
                            <div id="file-preview" class="mb-3" style="display: none;">
                                <label class="form-label">Seçilen Dosyalar:</label>
                                <div id="file-list" class="d-flex flex-wrap gap-2"></div>
                            </div>

                            <!-- Butonlar -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('support.index', Auth::user()->tenant_id) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Geri Dön
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-1"></i> Destek Talebi Gönder
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


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
            fileItem.className = 'border rounded p-2 d-flex align-items-center';
            fileItem.innerHTML = `
                <i class="fas fa-file me-2"></i>
                <span class="small">${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
            `;
            fileList.appendChild(fileItem);
        });
    } else {
        filePreview.style.display = 'none';
    }
});
</script>

@endsection