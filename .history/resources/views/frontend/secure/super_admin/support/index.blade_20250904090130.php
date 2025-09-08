{{-- resources/views/frontend/secure/super_admin/support/index.blade.php --}}

@extends('frontend.secure.user_master')

@section('user')
<div class="page-content">
    <div class="container-fluid">
        <!-- Başlık -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Destek Talepleri</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.destek.dashboard') }}">Super Admin</a></li>
                            <li class="breadcrumb-item active">Destek Talepleri</li>
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

        <!-- Filtreler -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">Tüm Durumlar</option>
                                    <option value="acik" {{ request('status') == 'acik' ? 'selected' : '' }}>Açık</option>
                                    <option value="cevaplandi" {{ request('status') == 'cevaplandi' ? 'selected' : '' }}>Cevaplandı</option>
                                    <option value="kapali" {{ request('status') == 'kapali' ? 'selected' : '' }}>Kapalı</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <select name="priority" class="form-select">
                                    <option value="">Tüm Öncelikler</option>
                                    <option value="yuksek" {{ request('priority') == 'yuksek' ? 'selected' : '' }}>Yüksek</option>
                                    <option value="orta" {{ request('priority') == 'orta' ? 'selected' : '' }}>Orta</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="category" class="form-select">
                                    <option value="">Tüm Kategoriler</option>
                                    @foreach($categories as $key => $value)
                                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="tenant_id" class="form-select">
                                    <option value="">Tüm Firmalar</option>
                                    @foreach($tenants as $tenant)
                                        <option value="{{ $tenant->id }}" {{ request('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                            {{ $tenant->firma_adi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Talep no, konu veya kullanıcı ara..." 
                                       value="{{ request('search') }}">
                            </div>

                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary">Filtrele</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Talepler Tablosu -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">
                                Tüm Destek Talepleri 
                                @if(isset($tickets))
                                    ({{ $tickets->total() }})
                                @endif
                            </h5>
                            <div>
                                <a href="{{ route('super.admin.destek.dashboard') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-chart-bar me-1"></i> Destek Talepleri
                                </a>
                            </div>
                        </div>

                        @if(isset($tickets) && $tickets->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Talep No</th>
                                            <th>Firma</th>
                                            <th>Kullanıcı</th>
                                            <th>Konu</th>
                                            <th>Kategori</th>
                                            <th>Öncelik</th>
                                            <th>Durum</th>
                                            <th>Oluşturma</th>
                                            <th>Son Yanıt</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tickets as $ticket)
                                            <tr>
                                                <td>
                                                    <strong>{{ $ticket->ticket_number }}</strong>
                                                </td>
                                                <td>
                                                    {{ $ticket->tenant->firma_adi ?? 'Bilinmiyor' }}
                                                </td>
                                                <td>{{ $ticket->user->name ?? 'Bilinmiyor' }}</td>
                                                <td>
                                                    <div style="max-width: 200px;">
                                                        {{ Str::limit($ticket->subject, 50) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @switch($ticket->category)
                                                        @case('teknik_sorun')
                                                            <span class="badge bg-danger">Teknik</span>
                                                            @break
                                                        @case('faturalandirma')
                                                            <span class="badge bg-warning">Fatura</span>
                                                            @break
                                                        @case('ozellik_talebi')
                                                            <span class="badge bg-info">Özellik</span>
                                                            @break
                                                        @case('genel_destek')
                                                            <span class="badge bg-primary">Genel</span>
                                                            @break
                                                        @case('hesap_sorunu')
                                                            <span class="badge bg-warning">Hesap</span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-secondary">Diğer</span>
                                                    @endswitch
                                                </td>
                                                @php
                                                    $priorityColors = [
                                                        'acil' => 'danger',
                                                        'kritik' => 'warning',
                                                        'yuksek' => 'danger',
                                                        'orta' => 'secondary',
                                                        'dusuk' => 'secondary',
                                                    ];

                                                    $priorityLabels = [
                                                        'acil' => 'Acil',
                                                        'kritik' => 'Kritik',
                                                        'yuksek' => 'Yüksek',
                                                        'orta' => 'Orta',
                                                        'dusuk' => 'Düşük',
                                                    ];
                                                @endphp

                                                <td>
                                                    <span class="badge bg-{{ $priorityColors[$ticket->priority] ?? 'secondary' }}">
                                                        {{ $priorityLabels[$ticket->priority] ?? ucfirst($ticket->priority) }}
                                                    </span>
                                                </td>

                                                <td>
                                                    <span class="badge bg-{{ $ticket->status == 'acik' ? 'warning' : ($ticket->status == 'cevaplandi' ? 'info' : 'danger') }}">
                                                        {{ $ticket->status == 'acik' ? 'Açık' : ($ticket->status == 'cevaplandi' ? 'Cevaplandı' : 'Kapalı') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small>{{ $ticket->created_at->format('d.m.Y') }}<br>{{ $ticket->created_at->format('H:i') }}</small>
                                                </td>
                                                <td>
                                                    @if($ticket->last_reply_at)
                                                        <small>{{ $ticket->last_reply_at->format('d.m.Y') }}<br>{{ $ticket->last_reply_at->format('H:i') }}</small>
                                                    @else
                                                        <small class="text-muted">-</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('super.admin.destek.show', $ticket->id) }}" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        
                                                        @if($ticket->status == 'kapali')
                                                            <form action="{{ route('super.admin.destek.reopen', $ticket->id) }}" 
                                                                  method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-sm btn-outline-success" 
                                                                        onclick="return confirm('Bu talebi yeniden açmak istediğinizden emin misiniz?')">
                                                                    <i class="fas fa-undo"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('super.admin.destek.close', $ticket->id) }}" 
                                                                  method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-sm btn-outline-secondary" 
                                                                        onclick="return confirm('Bu talebi kapatmak istediğinizden emin misiniz?')">
                                                                    <i class="fas fa-times"></i>
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

                            <!-- Sayfalama -->
                            <div class="d-flex justify-content-center mt-3">
                                {{ $tickets->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                                @if(request()->hasAny(['status', 'priority', 'category', 'tenant_id', 'search']))
                                    <h5 class="text-muted">Filtrelere uygun destek talebi bulunamadı</h5>
                                    <p class="text-muted">Filtre kriterlerinizi değiştirerek tekrar deneyebilirsiniz.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('super.admin.destek.index') }}" class="btn btn-primary">
                                            <i class="fas fa-refresh me-1"></i> Filtreleri Temizle
                                        </a>
                                    </div>
                                @else
                                    <h5 class="text-muted">Henüz sistemde destek talebi bulunmuyor</h5>
                                    <p class="text-muted">Kullanıcılar destek talebi oluşturdukça burada görünecektir.</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection