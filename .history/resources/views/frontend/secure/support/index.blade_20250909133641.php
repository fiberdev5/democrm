{{-- resources/views/frontend/secure/support/index.blade.php --}}

@extends('frontend.secure.user_master')
@section('user')
<div class="page-content  usersupport-index-page">
    <div class="container-fluid">
        <!-- Başlık -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        <i class="fas fa-headset text-primary me-2"></i>
                        Destek Taleplerim
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('secure.home', Auth::user()->tenant_id) }}">Ana Sayfa</a></li>
                            <li class="breadcrumb-item active">Destek Taleplerim</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

       <!-- Özet Kartları -->
        @if($totalTickets > 0)
        <div class="row summary-ticket-cards">
            <div class="col-xl-4 col-md-6">
                <div class="card bg-ticket-total text-white border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-white bg-opacity-25 p-2">
                                    <i class="fas fa-ticket-alt fa-lg text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0 text-white">{{ $totalTickets }}</h5>
                                <p class="mb-0">Toplam Talep</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4 col-md-6">
                <div class="card bg-ticket-active text-white border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-white bg-opacity-25 p-2">
                                    <i class="fas fa-clock fa-lg text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0 text-white">{{ $statusCounts['acik'] + $statusCounts['cevaplandi'] }}</h5>
                                <p class="mb-0">Aktif Talep</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4 col-md-6">
                <div class="card bg-ticket-solved text-white border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded-circle bg-white bg-opacity-25 p-2">
                                    <i class="fas fa-check-circle fa-lg text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0 text-white">{{ $statusCounts['kapali'] }}</h5>
                                <p class="mb-0">Çözülen Talep</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Ana Kart -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 d-flex align-items-center">
                                Destek Taleplerim
                                @if($tickets->count() > 0)
                                    <span class="badge bg-light text-dark ms-2">{{ $tickets->total() ?? $tickets->count() }}</span>
                                @endif
                            </h5>
                            <a href="{{ route('support.create', Auth::user()->tenant_id) }}" class="btn btn-secondary  shadow-sm">
                                <i class="fas fa-plus me-1"></i> Yeni Destek Talebi
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show m-3 border-0 shadow-sm" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle me-2"></i>
                                    {{ session('success') }}
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
                            </div>
                        @endif

                        @if($tickets->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0 fw-bold">Talep No</th>
                                            <th class="border-0 fw-bold">Konu</th>
                                            <th class="border-0 fw-bold">Kategori</th>
                                            <th class="border-0 fw-bold">Öncelik</th>
                                            <th class="border-0 fw-bold">Durum</th>
                                            <th class="border-0 fw-bold">Oluşturma</th>
                                            <th class="border-0 fw-bold">Son Yanıt</th>
                                            <th class="border-0 fw-bold text-center">İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tickets as $ticket)
                                            <tr class="align-middle">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-2">
                                                            <div class="avatar-title rounded-circle bg-light text-primary">
                                                                <i class="fas fa-hashtag"></i>
                                                            </div>
                                                        </div>
                                                        <span class="fw-bold">{{ $ticket->ticket_number }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <h6 class="mb-0">{{ Str::limit($ticket->subject, 40) }}</h6>
                                                        <small class="text-muted">
                                                            {{ Str::limit(strip_tags($ticket->description), 50) }}
                                                        </small>
                                                    </div>
                                                </td>
                                                <td>
                                                    @switch($ticket->category)
                                                        @case('teknik_sorun')
                                                            <span class="badge bg-soft-danger text-danger border border-danger border-opacity-25">
                                                                <i class="fas fa-cog me-1"></i>Teknik Sorun
                                                            </span>
                                                            @break
                                                        @case('faturalandirma')
                                                            <span class="badge bg-soft-warning text-warning border border-warning border-opacity-25">
                                                                <i class="fas fa-credit-card me-1"></i>Faturalandırma
                                                            </span>
                                                            @break
                                                        @case('ozellik_talebi')
                                                            <span class="badge bg-soft-info text-info border border-info border-opacity-25">
                                                                <i class="fas fa-lightbulb me-1"></i>Özellik Talebi
                                                            </span>
                                                            @break
                                                        @case('genel_destek')
                                                            <span class="badge bg-soft-primary text-primary border border-primary border-opacity-25">
                                                                <i class="fas fa-question-circle me-1"></i>Genel Destek
                                                            </span>
                                                            @break
                                                        @case('hesap_sorunu')
                                                            <span class="badge bg-soft-secondary text-secondary border border-secondary border-opacity-25">
                                                                <i class="fas fa-user-cog me-1"></i>Hesap Sorunu
                                                            </span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-soft-secondary text-secondary">
                                                                {{ $ticket->category }}
                                                            </span>
                                                    @endswitch
                                                </td>
                                                <td>
                                                    @switch($ticket->priority)
                                                        @case('acil')
                                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                                                <i class="fas fa-exclamation-circle me-1"></i>Acil
                                                            </span>
                                                            @break
                                                        @case('kritik')
                                                            <span class="badge bg-dark bg-opacity-10 text-dark border border-dark border-opacity-25">
                                                                <i class="fas fa-shield-alt me-1"></i>Kritik
                                                            </span>
                                                            @break
                                                        @case('yuksek')
                                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>Yüksek
                                                            </span>
                                                            @break
                                                        @case('orta')
                                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                                                                <i class="fas fa-clock me-1"></i>Orta
                                                            </span>
                                                            @break
                                                        @case('dusuk')
                                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                                                                <i class="fas fa-chevron-down me-1"></i>Düşük
                                                            </span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                                {{ ucfirst($ticket->priority) }}
                                                            </span>
                                                    @endswitch
                                                </td>

                                                <td>
                                                    @php
                                                        $statusConfig = [
                                                            'acik' => ['color' => 'primary', 'icon' => 'fas fa-spinner', 'text' => 'Açık'],
                                                            'cevaplandi' => ['color' => 'warning', 'icon' => 'fas fa-check-circle', 'text' => 'Cevaplandı'],
                                                            'kapali' => ['color' => 'danger', 'icon' => 'fas fa-times-circle', 'text' => 'Kapatıldı']
                                                        ];
                                                        $currentStatus = $statusConfig[$ticket->status] ?? ['color' => 'secondary', 'icon' => 'fas fa-question', 'text' => $ticket->status];
                                                    @endphp
                                                    
                                                    <span class="badge bg-{{ $currentStatus['color'] }} bg-opacity-10 text-{{ $currentStatus['color'] }} border border-{{ $currentStatus['color'] }} border-opacity-25">
                                                        <i class="{{ $currentStatus['icon'] }} me-1"></i>{{ $currentStatus['text'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="text-nowrap">
                                                        <div class="fw-medium">{{ $ticket->created_at->format('d.m.Y') }}</div>
                                                        <small class="text-muted">{{ $ticket->created_at->format('H:i') }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-nowrap">
                                                        @if($ticket->last_reply_at)
                                                            <div class="fw-medium">{{ $ticket->last_reply_at->format('d.m.Y') }}</div>
                                                            <small class="text-muted">{{ $ticket->last_reply_at->format('H:i') }}</small>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <a href="{{ route('support.show', [$ticket->tenant_id, $ticket->id]) }}" 
                                                           class="btn btn-sm btn-outline-primary rounded-pill px-3"  title="Detay">
                                                            <i class="fas fa-eye me-1"></i>
                                                        </a>
                                                        
                                                        @if(in_array($ticket->status, ['waiting_customer', 'open']))
                                                            <a href="{{ route('support.show', [$ticket->tenant_id, $ticket->id]) }}#reply" 
                                                               class="btn btn-sm btn-outline-success rounded-pill px-3">
                                                                <i class="fas fa-reply me-1"></i> Yanıtla
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Sayfalama -->
                            @if ($tickets instanceof \Illuminate\Pagination\LengthAwarePaginator && $tickets->hasPages())
                                <div class="card-footer bg-white border-0 py-3">
                                    <div class="d-flex justify-content-center">
                                        {{ $tickets->links('vendor.pagination.bootstrap-5') }}
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5 mx-3">
                                <div class="empty-state">
                                    <div class="empty-state-icon mb-4">
                                        <div class="avatar-xl mx-auto">
                                            <div class="avatar-title rounded-circle bg-light">
                                                <i class="fas fa-ticket-alt fa-2x text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <h5 class="text-muted mb-3">Henüz bir destek talebiniz bulunmuyor</h5>
                                    <p class="text-muted mb-4">İlk destek talebinizi oluşturmak için yukarıdaki butona tıklayın.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Yardım Kartı -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 bg-gradient-light">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title rounded-circle bg-primary bg-opacity-10 text-primary">
                                                <i class="fas fa-info-circle"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Hızlı yardıma mı ihtiyacınız var?</h6>
                                        <p class="mb-0 text-muted">Sık sorulan sorularımızı inceleyerek hızlı çözüm bulabilirsiniz.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                                 <a href="{{ url('/#faq-section') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-question-circle me-1"></i> SSS Sayfası
                                </a>
                                <a href="#" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-book me-1"></i> Kılavuz
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection