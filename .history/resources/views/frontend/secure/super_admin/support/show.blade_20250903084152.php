{{-- resources/views/frontend/secure/super-admin/support/show.blade.php --}}

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
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.support.index') }}">Destek Talepleri</a></li>
                            <li class="breadcrumb-item active">{{ $ticket->ticket_number }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Talep Bilgileri -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title">{{ $ticket->subject }}</h5>
                                <div class="d-flex flex-wrap gap-2 mb-2">
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
                            <div class="text-end">
                                @if($ticket->status == 'kapali')
                                    <form action="{{ route('super.admin.support.reopen', $ticket->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm" 
                                                onclick="return confirm('Bu talebi yeniden açmak istediğinizden emin misiniz?')">
                                            <i class="fas fa-undo me-1"></i> Yeniden Aç
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('super.admin.support.close', $ticket->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-secondary btn-sm" 
                                                onclick="return confirm('Bu talebi kapatmak istediğinizden emin misiniz?')">
                                            <i class="fas fa-times me-1"></i> Talebi Kapat
                                        </button>
                                    </form>
                                @endif
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
                                                <a href="{{ route('super.admin.support.download', [$ticket->id, $attachment['stored_name']]) }}" 
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
                                                        <a href="{{ route('super.admin.support.download', [$ticket->id, $attachment['stored_name']]) }}" 
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

                        <!-- Admin Yanıt Formu -->
                        @if($ticket->canBeReplied())
                            <hr>
                            <h6 class="mb-3">Destek Ekibi Yanıtı</h6>
                            
                            <form action="{{ route('super.admin.support.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="mb-3">
                                    <textarea name="message" rows="4" 
                                              class="form-control @error('message') is-invalid @enderror" 
                                              placeholder="Müşteriye yanıtınızı yazın..." required>{{ old('message') }}</textarea>
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
                                    <a href="{{ route('super.admin.support.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Geri Dön
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-reply me-1"></i> Yanıt Gönder
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-1"></i>
                                Bu destek talebi kapatılmıştır.
                            </div>
                            
                            <a href="{{ route('super.admin.support.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Geri Dön
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Yan Bilgiler -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Talep Bilgileri</h6>
                        
                        <div class="mb-3">
                            <strong>Talep Numarası:</strong><br>
                            <span class="text-primary">{{ $ticket->ticket_number }}</span>
                        </div>

                        <div class="mb-3">
                            <strong>Firma:</strong><br>
                            {{ $ticket->tenant->firma_adi ?? 'Bilinmiyor' }}
                        </div>

                        <div class="mb-3">
                            <strong>Kullanıcı:</strong><br>
                            {{ $ticket->user->name }}<br>
                            <small class="text-muted">{{ $ticket->user->eposta }}</small>
                        </div>

                        <div class="mb-3">
                            <strong>Oluşturma Tarihi:</strong><br>
                            {{ $ticket->created_at->format('d.m.Y H:i') }}
                        </div>

                        @if($ticket->last_reply_at)
                            <div class="mb-3">
                                <strong>Son Yanıt Tarihi:</strong><br>
                                {{ $ticket->last_reply_at->format('d.m.Y H:i') }}
                            </div>
                        @endif

                        <div class="mb-3">
                            <strong>Toplam Yanıt:</strong><br>
                            {{ $ticket->replies->count() }} yanıt
                        </div>

                        <div class="mb-3">
                            <strong>Admin Yanıtları:</strong><br>
                            {{ $ticket->replies->where('is_admin_reply', true)->count() }} yanıt
                        </div>
                    </div>
                </div>

                <!-- Hızlı İstatistikler -->
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Bu Firmadan Diğer Talepler</h6>
                        
                        @php
                            $otherTickets = \App\Models\SupportTicket::where('tenant_id', $ticket->tenant_id)
                                ->where('id', '!=', $ticket->id)
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp

                        @if($otherTickets->count() > 0)
                            @foreach($otherTickets as $otherTicket)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <a href="{{ route('super.admin.support.show', $otherTicket->id) }}" 
                                           class="text-decoration-none">
                                            {{ $otherTicket->ticket_number }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($otherTicket->subject, 30) }}</small>
                                    </div>
                                    <span class="badge bg-{{ $otherTicket->status_color }}">
                                        {{ $otherTicket->status_text }}
                                    </span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted small">Bu firmadan başka talep bulunmuyor.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection