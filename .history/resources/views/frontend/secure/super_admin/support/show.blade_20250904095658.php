{{-- resources/views/frontend/secure/super_admin/support/show.blade.php --}}

@extends('frontend.secure.user_master')

@section('user')

<style>
.ticket-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid #dee2e6;
}

.message-bubble {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 15px;
    margin-bottom: 15px;
    border-left: 4px solid #007bff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.admin-bubble {
    border-left-color: #28a745;
    background: #f0f9f4;
}

.user-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
    font-size: 16px;
}

.attachment-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 10px;
    transition: all 0.3s ease;
}

.attachment-card:hover {
    background: #e9ecef;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.file-upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.file-upload-area:hover {
    border-color: #007bff;
    background: #f0f8ff;
}

.file-preview {
    background: #e9ecef;
    border-radius: 8px;
    padding: 10px;
    margin: 5px;
    display: inline-block;
    position: relative;
}

.file-remove {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    cursor: pointer;
}

.admin-actions {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
}

.company-info {
    background: #e7f3ff;
    border: 1px solid #b8daff;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
}

.status-badge-large {
    font-size: 0.9rem;
    padding: 8px 15px;
}

.message-container {
    position: relative;
}

.alert-modern {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
</style>

<div class="page-content">
    <div class="container-fluid">
        <!-- Başlık -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 text-dark">
                        <i class="fas fa-user-shield me-2 text-warning"></i>
                        Destek Talebi: {{ $ticket->ticket_number }}
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.dashboard') }}" class="text-decoration-none">Super Admin</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.destek.index') }}" class="text-decoration-none">Destek Talepleri</a></li>
                            <li class="breadcrumb-item active">{{ $ticket->ticket_number }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bildirimler -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show alert-modern" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show alert-modern" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
            </div>
        @endif

        <!-- Admin Hızlı İşlemler -->
        <div class="row">
            <div class="col-12">
                <div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if($ticket->status == 'kapali')
                                <form action="{{ route('super.admin.destek.reopen', $ticket->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success" 
                                            onclick="return confirm('Bu talebi yeniden açmak istediğinizden emin misiniz?')">
                                        <i class="fas fa-undo me-1"></i> Yeniden Aç
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('super.admin.destek.close', $ticket->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-secondary" 
                                            onclick="return confirm('Bu talebi kapatmak istediğinizden emin misiniz?')">
                                        <i class="fas fa-times me-1"></i> Talebi Kapat
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Talep Bilgileri Header -->
        <div class="row">
            <div class="col-12">
                <div class="ticket-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-3 text-dark">{{ $ticket->subject }}</h3>
                            
                            <!-- Firma ve Kullanıcı Bilgisi -->
                            <div class="company-info mb-3">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-building text-primary me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Firma</small>
                                                <strong>{{ $ticket->tenant->firma_adi ?? 'Bilinmiyor' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user text-info me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Kullanıcı</small>
                                                <strong>{{ $ticket->user->name }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge status-badge-large bg-{{ $ticket->status_color }}">
                                    <i class="fas fa-circle me-1"></i>{{ $ticket->status_text }}
                                </span>
                                
                                @php
                                    $priorityColors = [
                                        'acil' => 'danger',
                                        'kritik' => 'warning',
                                        'yuksek' => 'danger',
                                        'orta' => 'secondary',
                                        'dusuk' => 'info'
                                    ];
                                @endphp

                                <span class="badge status-badge-large bg-{{ $priorityColors[$ticket->priority] ?? 'secondary' }}">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    {{ $ticket->priority_text }}
                                </span>

                                @switch($ticket->category)
                                    @case('teknik_sorun')
                                        <span class="badge status-badge-large bg-danger">
                                            <i class="fas fa-cogs me-1"></i>Teknik Sorun
                                        </span>
                                        @break
                                    @case('faturalandirma')
                                        <span class="badge status-badge-large bg-warning">
                                            <i class="fas fa-file-invoice me-1"></i>Faturalandırma
                                        </span>
                                        @break
                                    @case('ozellik_talebi')
                                        <span class="badge status-badge-large bg-info">
                                            <i class="fas fa-lightbulb me-1"></i>Özellik Talebi
                                        </span>
                                        @break
                                    @case('genel_destek')
                                        <span class="badge status-badge-large bg-primary">
                                            <i class="fas fa-question-circle me-1"></i>Genel Destek
                                        </span>
                                        @break
                                    @case('hesap_sorunu')
                                        <span class="badge status-badge-large bg-warning">
                                            <i class="fas fa-user-times me-1"></i>Hesap Sorunu
                                        </span>
                                        @break
                                    @default
                                        <span class="badge status-badge-large bg-secondary">{{ $ticket->category }}</span>
                                @endswitch
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="card border-0 bg-white">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-calendar-plus text-success me-2"></i>
                                        <small class="text-muted">
                                            <strong>Oluşturma:</strong> {{ $ticket->created_at->format('d.m.Y H:i') }}
                                        </small>
                                    </div>
                                    @if($ticket->last_reply_at)
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clock text-info me-2"></i>
                                            <small class="text-muted">
                                                <strong>Son Yanıt:</strong> {{ $ticket->last_reply_at->format('d.m.Y H:i') }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mesajlar -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="message-container">
                            <!-- İlk Mesaj -->
                            <div class="d-flex mb-4">
                                <div class="user-avatar bg-primary me-3">
                                    {{ substr($ticket->user->name, 0, 1) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="message-bubble">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1 text-dark">{{ $ticket->user->name }}</h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $ticket->created_at->format('d.m.Y H:i') }}
                                                </small>
                                            </div>
                                            <span class="badge bg-primary">İlk Mesaj</span>
                                        </div>
                                        <p class="mb-2 text-dark">{{ $ticket->description }}</p>
                                        
                                        @if($ticket->attachments)
                                            <div class="mt-3">
                                                <h6 class="text-muted mb-2">
                                                    <i class="fas fa-paperclip me-1"></i>Ekli Dosyalar:
                                                </h6>
                                                <div class="row g-2">
                                                    @foreach($ticket->attachments as $attachment)
                                                        <div class="col-md-4">
                                                            <a href="{{ route('super.admin.destek.download', [$ticket->id, $attachment['stored_name']]) }}" 
                                                               class="attachment-card d-block text-decoration-none">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-file text-primary me-2"></i>
                                                                    <div class="flex-grow-1">
                                                                        <small class="text-dark d-block">{{ $attachment['original_name'] }}</small>
                                                                        <small class="text-muted">
                                                                            <i class="fas fa-download me-1"></i>İndir
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Yanıtlar -->
                            @if($ticket->replies->count() > 0)
                                <div class="mb-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <hr class="flex-grow-1">
                                        <span class="px-3 text-muted">
                                            <i class="fas fa-comments me-1"></i>
                                            {{ $ticket->replies->count() }} Yanıt
                                        </span>
                                        <hr class="flex-grow-1">
                                    </div>
                                </div>
                                
                                @foreach($ticket->replies as $reply)
                                    <div class="d-flex mb-4">
                                        <div class="user-avatar bg-{{ $reply->is_admin_reply ? 'success' : 'primary' }} me-3">
                                            {{ substr($reply->user->name, 0, 1) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="message-bubble {{ $reply->is_admin_reply ? 'admin-bubble' : '' }}">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h6 class="mb-1 text-dark">
                                                            {{ $reply->user->name }}
                                                            @if($reply->is_admin_reply)
                                                                <span class="badge bg-success ms-2">
                                                                    <i class="fas fa-shield-alt me-1"></i>Destek Ekibi
                                                                </span>
                                                            @endif
                                                        </h6>
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock me-1"></i>
                                                            {{ $reply->created_at->format('d.m.Y H:i') }}
                                                        </small>
                                                    </div>
                                                </div>
                                                <p class="mb-2 text-dark">{{ $reply->message }}</p>
                                                
                                                @if($reply->attachments)
                                                    <div class="mt-3">
                                                        <h6 class="text-muted mb-2">
                                                            <i class="fas fa-paperclip me-1"></i>Ekli Dosyalar:
                                                        </h6>
                                                        <div class="row g-2">
                                                            @foreach($reply->attachments as $attachment)
                                                                <div class="col-md-4">
                                                                    <a href="{{ route('super.admin.destek.download', [$ticket->id, $attachment['stored_name']]) }}" 
                                                                       class="attachment-card d-block text-decoration-none">
                                                                        <div class="d-flex align-items-center">
                                                                            <i class="fas fa-file text-primary me-2"></i>
                                                                            <div class="flex-grow-1">
                                                                                <small class="text-dark d-block">{{ $attachment['original_name'] }}</small>
                                                                                <small class="text-muted">
                                                                                    <i class="fas fa-download me-1"></i>İndir
                                                                                </small>
                                                                            </div>
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <!-- Admin Yanıt Formu -->
                            @if($ticket->canBeReplied())
                                <div class="mt-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <hr class="flex-grow-1">
                                        <span class="px-3 text-muted">
                                            <i class="fas fa-user-shield me-1"></i>
                                            Admin Yanıtı
                                        </span>
                                        <hr class="flex-grow-1">
                                    </div>
                                    
                                    <form action="{{ route('super.admin.destek.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        
                                        <div class="mb-3">
                                            <label for="message" class="form-label text-dark fw-semibold">
                                                <i class="fas fa-comment me-1"></i>Yanıt Mesajı
                                            </label>
                                            <textarea name="message" id="message" rows="4" 
                                                      class="form-control @error('message') is-invalid @enderror" 
                                                      placeholder="Müşteriye yanıtınızı buraya yazın..." required>{{ old('message') }}</textarea>
                                            @error('message')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="attachments" class="form-label text-dark fw-semibold">
                                                <i class="fas fa-paperclip me-1"></i>Dosya Ekle (Maksimum 3 adet)
                                            </label>
                                            <div class="file-upload-area">
                                                <input type="file" name="attachments[]" id="fileInput"
                                                       class="form-control @error('attachments.*') is-invalid @enderror" 
                                                       multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" style="display: none;">
                                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('fileInput').click();">
                                                    <i class="fas fa-cloud-upload-alt me-2"></i>
                                                    Dosya Seç
                                                </button>
                                                <div class="mt-2 text-muted small">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Maksimum 3 dosya, her biri en fazla 10MB. Desteklenen formatlar: JPG, PNG, PDF, DOC, DOCX
                                                </div>
                                            </div>
                                            
                                            <div id="filePreview" class="mt-3" style="display: none;">
                                                <h6 class="text-muted">Seçilen Dosyalar:</h6>
                                                <div id="fileList"></div>
                                            </div>
                                            
                                            @error('attachments.*')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('super.admin.destek.index') }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-arrow-left me-1"></i> Destek Listesi
                                            </a>
                                            <button type="submit" class="btn btn-primary px-4">
                                                <i class="fas fa-paper-plane me-1"></i> Yanıt Gönder
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <div class="alert alert-info border-0 shadow-sm">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle text-info me-3 fa-2x"></i>
                                        <div>
                                            <h6 class="alert-heading mb-1">Destek Talebi Kapatıldı</h6>
                                            <p class="mb-0">Bu destek talebi kapatılmıştır. Yeni yanıt ekleyemezsiniz.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <a href="{{ route('super.admin.destek.index') }}" class="btn btn-outline-secondary px-4">
                                        <i class="fas fa-arrow-left me-1"></i> Destek Listesine Dön
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('fileInput').addEventListener('change', function() {
    const files = this.files;
    const maxFiles = 3;
    const filePreview = document.getElementById('filePreview');
    const fileList = document.getElementById('fileList');
    
    if (files.length > maxFiles) {
        alert('Maksimum ' + maxFiles + ' dosya seçebilirsiniz.');
        this.value = '';
        filePreview.style.display = 'none';
        return;
    }
    
    if (files.length > 0) {
        filePreview.style.display = 'block';
        fileList.innerHTML = '';
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const fileDiv = document.createElement('div');
            fileDiv.className = 'file-preview';
            fileDiv.innerHTML = `
                <i class="fas fa-file me-2"></i>
                <span>${file.name}</span>
                <button type="button" class="file-remove" onclick="removeFile(${i})">×</button>
            `;
            fileList.appendChild(fileDiv);
        }
    } else {
        filePreview.style.display = 'none';
    }
});

function removeFile(index) {
    const fileInput = document.getElementById('fileInput');
    const dt = new DataTransfer();
    const files = fileInput.files;
    
    for (let i = 0; i < files.length; i++) {
        if (i !== index) {
            dt.items.add(files[i]);
        }
    }
    
    fileInput.files = dt.files;
    fileInput.dispatchEvent(new Event('change'));
}
</script>

@endsection