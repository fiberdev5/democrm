{{-- resources/views/frontend/secure/super_admin/support/index.blade.php --}}
@extends('frontend.secure.user_master')
@section('user')
<div class="page-content support-index-page">
    <div class="container-fluid">
        <!-- Başlık -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        <i class="fas fa-user-shield text-warning me-2"></i>
                        Destek Talepleri Yönetimi
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.destek.dashboard') }}" class="text-decoration-none">Super Admin</a></li>
                            <li class="breadcrumb-item active">Destek Talepleri</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtreler -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card filter-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-filter text-primary me-2"></i>
                            <h6 class="mb-0 text-dark">Filtreler</h6>
                        </div>
                        <form method="GET" class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label text-muted small">Durum</label>
                                <select name="status" class="form-select">
                                    <option value="">Tüm Durumlar</option>
                                    <option value="acik" {{ request('status') == 'acik' ? 'selected' : '' }}>Açık</option>
                                    <option value="cevaplandi" {{ request('status') == 'cevaplandi' ? 'selected' : '' }}>Cevaplandı</option>
                                    <option value="kapali" {{ request('status') == 'kapali' ? 'selected' : '' }}>Kapalı</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label text-muted small">Öncelik</label>
                                <select name="priority" class="form-select">
                                    <option value="">Tüm Öncelikler</option>
                                    <option value="acil" {{ request('priority') == 'acil' ? 'selected' : '' }}>Acil</option>
                                    <option value="kritik" {{ request('priority') == 'kritik' ? 'selected' : '' }}>Kritik</option>
                                    <option value="yuksek" {{ request('priority') == 'yuksek' ? 'selected' : '' }}>Yüksek</option>
                                    <option value="orta" {{ request('priority') == 'orta' ? 'selected' : '' }}>Orta</option>
                                    <option value="dusuk" {{ request('priority') == 'dusuk' ? 'selected' : '' }}>Düşük</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label text-muted small">Kategori</label>
                                <select name="category" class="form-select">
                                    <option value="">Tüm Kategoriler</option>
                                    @if(isset($categories))
                                        @foreach($categories as $key => $value)
                                            <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label text-muted small">Firma</label>
                                <select name="tenant_id" class="form-select">
                                    <option value="">Tüm Firmalar</option>
                                    @if(isset($tenants))
                                        @foreach($tenants as $tenant)
                                            <option value="{{ $tenant->id }}" {{ request('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                                {{ $tenant->firma_adi }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label text-muted small">Arama</label>
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Talep no, konu veya kullanıcı ara..." 
                                       value="{{ request('search') }}">
                            </div>

                           <div class="col-md d-flex align-items-end">
    <button type="submit" class="btn d-flex align-items-center justify-content-between" style="background: #868e96; color: white; border: none; border-radius: 6px; padding: 10px 14px; font-weight: 500; box-shadow: 0 2px 4px rgba(134, 142, 150, 0.2); transition: all 0.3s ease; min-width: 110px;" onmouseover="this.style.background='#6f7479'; this.style.boxShadow='0 3px 8px rgba(134, 142, 150, 0.3)'" onmouseout="this.style.background='#868e96'; this.style.boxShadow='0 2px 4px rgba(134, 142, 150, 0.2)'">
        <span>Filtrele</span>
        <i class="fas fa-search ms-2"></i>
    </button>
</div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bildirimler -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
            </div>
        @endif

        <!-- Ana Kart -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
             

                    <div class="card-body p-0">
                        @if(isset($tickets) && $tickets->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0 fw-bold">Talep No</th>
                                            <th class="border-0 fw-bold">Firma</th>
                                            <th class="border-0 fw-bold">Kullanıcı</th>
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
                                                        <div class="avatarsupersupport-xs me-2">
                                                            <div class="avatarsupersupport-title rounded-circle bg-light text-primary">
                                                                <i class="fas fa-hashtag"></i>
                                                            </div>
                                                        </div>
                                                        <span class="fw-bold">{{ $ticket->ticket_number }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatarsupersupport-xs me-2">
                                                            <div class="avatarsupersupport-title rounded-circle bg-soft-primary text-primary">
                                                                <i class="fas fa-building"></i>
                                                            </div>
                                                        </div>
                                                        <span>{{ $ticket->tenant->firma_adi ?? 'Bilinmiyor' }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatarsupersupport-xs me-2">
                                                            <div class="avatarsupersupport-title rounded-circle bg-soft-primary text-primary">
                                                                <i class="fas fa-user"></i>
                                                            </div>
                                                        </div>
                                                        <span>{{ $ticket->user->name ?? 'Bilinmiyor' }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div style="max-width: 200px;">
                                                        <h6 class="mb-0">{{ Str::limit($ticket->subject, 40) }}</h6>
                                                        <small class="text-muted">
                                                            {{ Str::limit(strip_tags($ticket->description ?? ''), 50) }}
                                                        </small>
                                                    </div>
                                                </td>
                                                <td>
                                                    @switch($ticket->category)
                                                        @case('teknik_sorun')
                                                            <span class="badge bg-soft-danger text-danger border border-danger border-opacity-25">
                                                                <i class="fas fa-cogs me-1"></i>Teknik Sorun
                                                            </span>
                                                            @break
                                                        @case('faturalandirma')
                                                            <span class="badge bg-soft-warning text-warning border border-warning border-opacity-25">
                                                                <i class="fas fa-file-invoice me-1"></i>Faturalandırma
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
                                                                <i class="fas fa-user-times me-1"></i>Hesap Sorunu
                                                            </span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-soft-secondary text-secondary">{{ $ticket->category }}</span>
                                                    @endswitch
                                                </td>
                                                <td>
                                                    @php
                                                        $priorityColors = [
                                                            'acil' => 'danger',
                                                            'kritik' => 'warning',
                                                            'yuksek' => 'danger',
                                                            'orta' => 'secondary',
                                                            'dusuk' => 'info'
                                                        ];

                                                        $priorityLabels = [
                                                            'acil' => 'Acil',
                                                            'kritik' => 'Kritik',
                                                            'yuksek' => 'Yüksek',
                                                            'orta' => 'Orta',
                                                            'dusuk' => 'Düşük'
                                                        ];
                                                    @endphp

                                                    @switch($ticket->priority)
                                                        @case('acil')
                                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                                                <i class="fas fa-exclamation-circle me-1"></i>Acil
                                                            </span>
                                                            @break
                                                        @case('kritik')
                                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">
                                                                <i class="fas fa-shield-alt me-1"></i>Kritik
                                                            </span>
                                                            @break
                                                        @case('yuksek')
                                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>Yüksek
                                                            </span>
                                                            @break
                                                        @case('orta')
                                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                                                                <i class="fas fa-clock me-1"></i>Orta
                                                            </span>
                                                            @break
                                                        @case('dusuk')
                                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
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
                                                         <a href="{{ route('super.admin.destek.show', $ticket->id) }}" 
                                                            class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                                                            title="Detay">
                                                                <i class="fas fa-eye"></i>
                                                         </a>
                                                        
                                                        @if($ticket->status == 'kapali')
                                                            <form action="{{ route('super.admin.destek.reopen', $ticket->id) }}" 
                                                                  method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3" 
                                                                        onclick="return confirm('Bu talebi yeniden açmak istediğinizden emin misiniz?')" title="Aç">
                                                                    <i class="fas fa-undo me-1"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('super.admin.destek.close', $ticket->id) }}" 
                                                                  method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3" 
                                                                        onclick="return confirm('Bu talebi kapatmak istediğinizden emin misiniz?')"  title="Kapat">
                                                                    <i class="fas fa-times me-1"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

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
                                        <div class="avatarsupersupport-xl mx-auto">
                                            <div class="avatarsupersupport-title rounded-circle bg-light">
                                                <i class="fas fa-ticket-alt fa-2x text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if(request()->hasAny(['status', 'priority', 'category', 'tenant_id', 'search']))
                                        <h5 class="text-muted mb-3">Filtrelere uygun destek talebi bulunamadı</h5>
                                        <p class="text-muted mb-4">Filtre kriterlerinizi değiştirerek tekrar deneyebilirsiniz.</p>
                                        <div class="mt-3">
                                            <a href="{{ route('super.admin.destek.index') }}" class="btn btn-primary">
                                                <i class="fas fa-refresh me-1"></i> Filtreleri Temizle
                                            </a>
                                        </div>
                                    @else
                                        <h5 class="text-muted mb-3">Henüz sistemde destek talebi bulunmuyor</h5>
                                        <p class="text-muted mb-4">Kullanıcılar destek talebi oluşturdukça burada görünecektir.</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection