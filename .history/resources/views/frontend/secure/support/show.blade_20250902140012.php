{{-- resources/views/support/show.blade.php --}}

@extends('layouts.app')

@section('title', 'Destek Talebi: ' . $ticket->ticket_number)

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Başlık -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Destek Talebi: {{ $ticket->ticket_number }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('secure.home', Auth::user()->tenant_id) }}">Ana Sayfa</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('support.index', Auth::user()->tenant_id) }}">Destek Taleplerim</a></li>
                            <li class="breadcrumb-item active">{{ $ticket->ticket_number }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

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
                                    <span class="badge bg-{{ $ticket->priority == 'yuksek' ? 'danger' : 'secondary' }}">
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
                                        @default
                                            <span class="badge bg-secondary">{{ $ticket->category }}</span>
                                    @endswitch
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <small class="text-muted">
                                    <strong>Oluşturma:</strong> {{ $ticket->created_at->format('d.m.Y H:i') }}<br>
                                    @if($ticket->last_reply_at)
                                        <strong>Son Yanıt:</strong> {{ $ticket->last_reply_at->format('d.m.Y H:i') }}
                                    @endif
                                </small>
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
                                <p class="mb-2">{{ $ticket->description }}</p>
                                
                                @if($ticket->attachments)
                                    <div class="mt-2">
                                        <strong>Ekli Dosyalar:</strong>
                                        <div class="d-flex flex-wrap gap-2 mt-1">
                                            @foreach($ticket->attachments as $attachment)
                                                <a href="{{ route('support.download', [$ticket->tenant_id, $ticket->id, $attachment['stored_name']]) }}" 
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
                                        <p class="mb-2">{{ $reply->message }}</p>
                                        
                                        @if($reply->attachments)
                                            <div class="mt-2">
                                                <strong>Ekli Dosyalar:</strong>
                                                <div class="d-flex flex-wrap gap-2 mt-1">
                                                    @foreach($reply->attachments as $attachment)
                                                        <a href="{{ route('support.download', [$ticket->tenant_id, $ticket->id, $attachment['stored_name']]) }}" 
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
                            @endforeach
                        @endif

                        <!-- Yanıt Formu -->
                        @if($ticket->canBeReplied())
                            <hr>
                            <h6 class="mb-3">Yanıt Ekle</h6>
                            
                            <form action="{{ route('support.reply', [$ticket->tenant_id, $ticket->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="mb-3">
                                    <textarea name="message" rows="4" 
                                              class="form-control @error('message') is-invalid @enderror" 
                                              placeholder="Yanıtınızı yazın..." required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <input type="file" name="attachments[]" 
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
                                    <a href="{{ route('support.index', Auth::user()->tenant_id) }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Geri Dön
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-reply me-1"></i> Yanıt Gönder
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-1"></i>
                                Bu destek talebi kapatılmıştır. Yeni yanıt ekleyemezsiniz.
                            </div>
                            
                            <a href="{{ route('support.index', Auth::user()->tenant_id) }}" class="btn btn-secondary">
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