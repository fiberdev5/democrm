{{-- resources/views/frontend/secure/super_admin/support/show.blade.php --}}

@extends('frontend.secure.user_master')

@section('user')

<div class="page-content">
    <div class="container-fluid">
        <!-- Başlık -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Destek Talebi: {{ $ticket->ticket_number }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.dashboard') }}">Super Admin</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.destek.index') }}">Destek Talepleri</a></li>
                            <li class="breadcrumb-item active">{{ $ticket->ticket_number }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
            </div>
        @endif

        <!-- Talep Bilgileri -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="card-title">{{ $ticket->subject }}</h5>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="badge bg-{{ $ticket->status_color }}">{{ $ticket->status_text }}</span>
                                    <@php
    $priorityColors = [
        'acil' => 'danger',
        'kritik' => 'warning',
        'yuksek' => 'danger',
        'orta' => 'secondary',
        'dusuk' => 'info'
    ];
@endphp

<span class="badge bg-{{ $priorityColors[$ticket->priority] ?? 'secondary' }}">
    {{ $ticket->priority_text }}
</span>

                                        {{ $ticket->priority_text }}
                                    </span>
                                    @switch($ticket->category)
                                        @case('teknik_sorun')
                                            <span class="badge bg-danger">Teknik Sorun</span>
                                            @break
                                        @case('faturalandirma')
                                            <span class="badge bg-warning">Faturalandırma</span>
                                            @break
                                        @case('ozellik_talebi')
                                            <span class="badge bg-info">Özellik Talebi</span>
                                            @break
                                        @case('genel_destek')
                                            <span class="badge bg-primary">Genel Destek</span>
                                            @break
                                        @case('hesap_sorunu')
                                            <span class="badge bg-warning">Hesap Sorunu</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $ticket->category }}</span>
                                    @endswitch
                                </div>
                                
                                <!-- Firma ve Kullanıcı Bilgisi -->
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <strong>Firma:</strong> {{ $ticket->tenant->firma_adi ?? 'Bilinmiyor' }}
                                        </div>
                                        <div class="col-sm-6">
                                            <strong>Kullanıcı:</strong> {{ $ticket->user->name }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <small class="text-muted">
                                    <strong>Oluşturma:</strong> {{ $ticket->created_at->format('d.m.Y H:i') }}<br>
                                    @if($ticket->last_reply_at)
                                        <strong>Son Yanıt:</strong> {{ $ticket->last_reply_at->format('d.m.Y H:i') }}
                                    @endif
                                </small>
                                
                                <!-- Hızlı İşlemler -->
                                <div class="mt-3">
                                    @if($ticket->status == 'kapali')
                                        <form action="{{ route('super.admin.destek.reopen', $ticket->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm" 
                                                    onclick="return confirm('Bu talebi yeniden açmak istediğinizden emin misiniz?')">
                                                <i class="fas fa-undo me-1"></i> Yeniden Aç
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('super.admin.destek.close', $ticket->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-secondary btn-sm" 
                                                    onclick="return confirm('Bu talebi kapatmak istediğinizden emin misiniz?')">
                                                <i class="fas fa-times me-1"></i> Kapat
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- İlk Mesaj -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar-sm me-3">
                                    <div class="avatar-title bg-primary rounded-circle">
                                        {{ substr($ticket->user->name, 0, 1) }}
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $ticket->user->name }}</h6>
                                    <small class="text-muted">{{ $ticket->created_at->format('d.m.Y H:i') }}</small>
                                </div>
                            </div>
                            <div class="ps-5">
                                <div class="p-3 bg-light rounded">
                                    <p class="mb-2">{{ $ticket->description }}</p>
                                    
                                    @if($ticket->attachments)
                                        <div class="mt-2">
                                            <strong>Ekli Dosyalar:</strong>
                                            <div class="d-flex flex-wrap gap-2 mt-1">
                                                @foreach($ticket->attachments as $attachment)
                                                    <a href="{{ route('super.admin.destek.download', [$ticket->id, $attachment['stored_name']]) }}" 
                                                       class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-download me-1"></i>
                                                        {{ $attachment['original_name'] }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Yanıtlar -->
                        @if($ticket->replies->count() > 0)
                            <hr>
                            <h6 class="mb-3">Yanıtlar ({{ $ticket->replies->count() }})</h6>
                            
                            @foreach($ticket->replies as $reply)
                                <div class="mb-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar-sm me-3">
                                            <div class="avatar-title bg-{{ $reply->is_admin_reply ? 'success' : 'primary' }} rounded-circle">
                                                {{ substr($reply->user->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">
                                                {{ $reply->user->name }}
                                                @if($reply->is_admin_reply)
                                                    <span class="badge bg-success ms-2">Destek Ekibi</span>
                                                @endif
                                            </h6>
                                            <small class="text-muted">{{ $reply->created_at->format('d.m.Y H:i') }}</small>
                                        </div>
                                    </div>
                                    <div class="ps-5">
                                        <div class="p-3 bg-{{ $reply->is_admin_reply ? 'success' : 'light' }} bg-opacity-10 rounded">
                                            <p class="mb-2">{{ $reply->message }}</p>
                                            
                                            @if($reply->attachments)
                                                <div class="mt-2">
                                                    <strong>Ekli Dosyalar:</strong>
                                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                                        @foreach($reply->attachments as $attachment)
                                                            <a href="{{ route('super.admin.destek.download', [$ticket->id, $attachment['stored_name']]) }}" 
                                                               class="btn btn-sm btn-outline-secondary">
                                                                <i class="fas fa-download me-1"></i>
                                                                {{ $attachment['original_name'] }}
                                                            </a>
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
                            <hr>
                            <h6 class="mb-3">Admin Yanıtı</h6>
                            
                            <form action="{{ route('super.admin.destek.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="message" class="form-label">Yanıt Mesajı</label>
                                    <textarea name="message" id="message" rows="4" 
                                              class="form-control @error('message') is-invalid @enderror" 
                                              placeholder="Müşteriye yanıtınızı yazın..." required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="attachments" class="form-label">Dosya Ekle (Opsiyonel)</label>
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

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('super.admin.destek.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Geri Dön
                                    </a>
                                    <div>
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="fas fa-reply me-1"></i> Yanıt Gönder
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-1"></i>
                                Bu destek talebi kapatılmıştır. Yeni yanıt ekleyemezsiniz.
                            </div>
                            
                            <a href="{{ route('super.admin.destek.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Geri Dön
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection