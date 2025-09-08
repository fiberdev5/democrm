{{-- resources/views/frontend/secure/super_admin/support/dashboard.blade.php --}}

@extends('frontend.secure.user_master')

@section('user')

<style>
/* Gradient Backgrounds */
.bg-gradient-primary {
    background: #667eea;
}

.bg-gradient-info {
    background: #4facfe;
}

.bg-gradient-warning {
    background: #fa709a;
}

.bg-gradient-danger {
    background:#f093fb;
}

.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}
.bg-gradient-light {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.bg-gradient-dark {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Card Animations */
.card-animate {
    transition: all 0.3s ease;
}

.card-animate:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

/* Stats Cards */
.stats-card {
    border-radius: 15px;
    border: 0;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    position: relative;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    transform: translate(30%, -30%);
}

/* Quick Actions */
.quick-action-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
}

.quick-action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.quick-action-btn {
    border-radius: 10px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.quick-action-btn:hover {
    transform: translateY(-2px);
    border-color: var(--bs-primary);
    background-color: rgba(var(--bs-primary-rgb), 0.1);
}

/* Avatar Improvements */
.avatar-xs { height: 1.5rem; width: 1.5rem; }
.avatar-sm { height: 2rem; width: 2rem; }
.avatar-xl { height: 4rem; width: 4rem; }

.avatar-title {
    align-items: center;
    display: flex;
    font-size: 1rem;
    font-weight: 500;
    height: 100%;
    justify-content: center;
    width: 100%;
}

/* Table Improvements */
.table-modern {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.table-modern .table {
    margin-bottom: 0;
}

.table-modern .table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
    transition: background-color 0.15s ease-in-out;
}

.table-modern .table td, 
.table-modern .table th {
    vertical-align: middle;
    border-color: #f1f3f4;
    padding: 1rem 0.75rem;
}

/* Badge Improvements */
.badge-modern {
    font-weight: 500;
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
}

/* Counter Animation */
.counter-value {
    display: inline-block;
    animation: countUp 1s ease-out forwards;
}

@keyframes countUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Loading Animations */
.fa-spinner {
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Empty State Animation */
.empty-state-icon {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

/* Responsive */
@media (max-width: 768px) {
    .stats-card .card-body {
        padding: 1rem;
    }
    
    .quick-action-btn {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
    }
    
    .table-responsive table {
        font-size: 0.875rem;
    }
}
</style>

<div class="page-content">
    <div class="container-fluid">
        <!-- Başlık -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        <i class="fas fa-user-shield text-warning me-2"></i>
                        Destek Talepleri Dashboard
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.dashboard') }}" class="text-decoration-none">Super Admin</a></li>
                            <li class="breadcrumb-item active">Destek Talepleri</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- İstatistik Kartları -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card bg-gradient-primary text-white card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-medium text-white-50 text-truncate mb-0">Toplam Talep</p>
                                <div class="d-flex align-items-end justify-content-between mt-3">
                                    <div>
                                        <h4 class="fs-22 fw-semibold mb-0">
                                            <span class="counter-value">{{ $stats['total'] ?? 0 }}</span>
                                        </h4>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-white bg-opacity-20 rounded">
                                                <i class="fas fa-ticket-alt fs-3 text-white"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card bg-gradient-warning text-white card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-medium text-white-50 text-truncate mb-0">Açık Talepler</p>
                                <div class="d-flex align-items-end justify-content-between mt-3">
                                    <div>
                                        <h4 class="fs-22 fw-semibold mb-0">
                                            <span class="counter-value">{{ $stats['open'] ?? 0 }}</span>
                                        </h4>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-white bg-opacity-20 rounded">
                                                <i class="fas fa-clock fs-3 text-white"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card bg-gradient-info text-white card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-medium text-white-50 text-truncate mb-0">Cevaplanan</p>
                                <div class="d-flex align-items-end justify-content-between mt-3">
                                    <div>
                                        <h4 class="fs-22 fw-semibold mb-0">
                                            <span class="counter-value">{{ $stats['answered'] ?? 0 }}</span>
                                        </h4>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-white bg-opacity-20 rounded">
                                                <i class="fas fa-reply fs-3 text-white"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stats-card bg-gradient-danger text-white card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-medium text-white-50 text-truncate mb-0">Yüksek Öncelik</p>
                                <div class="d-flex align-items-end justify-content-between mt-3">
                                    <div>
                                        <h4 class="fs-22 fw-semibold mb-0">
                                            <span class="counter-value">{{ $stats['high_priority'] ?? 0 }}</span>
                                        </h4>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title bg-white bg-opacity-20 rounded">
                                                <i class="fas fa-exclamation-triangle fs-3 text-white"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hızlı Erişim -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card quick-action-card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-3">
                                    <div class="avatar-title bg-primary bg-opacity-10 text-primary rounded">
                                        <i class="fas fa-bolt"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">Hızlı Erişim</h5>
                                    <p class="text-muted mb-0 small">Sık kullanılan işlemler</p>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('super.admin.destek.index') }}" class="quick-action-btn btn btn-outline-primary w-100 d-flex align-items-center">
                                    <i class="fas fa-list me-2"></i>
                                    <div class="text-start">
                                        <div class="fw-semibold">Tüm Talepler</div>
                                        <small class="text-muted">Listeyi görüntüle</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('super.admin.destek.index', ['status' => 'acik']) }}" class="quick-action-btn btn btn-outline-warning w-100 d-flex align-items-center">
                                    <i class="fas fa-clock me-2"></i>
                                    <div class="text-start">
                                        <div class="fw-semibold">Açık Talepler</div>
                                        <small class="text-muted">Bekleyen talepler</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('super.admin.destek.index', ['priority' => 'acil']) }}" class="quick-action-btn btn btn-outline-danger w-100 d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <div class="text-start">
                                        <div class="fw-semibold">Acil Talepler</div>
                                        <small class="text-muted">Öncelikli çözüm</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('super.admin.destek.index', ['status' => 'cevaplandi']) }}" class="quick-action-btn btn btn-outline-info w-100 d-flex align-items-center">
                                    <i class="fas fa-reply me-2"></i>
                                    <div class="text-start">
                                        <div class="fw-semibold">Cevaplanan</div>
                                        <small class="text-muted">Yanıtlanan talepler</small>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Son Talepler -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-3">
                                    <div class="avatar-title bg-success bg-opacity-10 text-success rounded">
                                        <i class="fas fa-history"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">Son Destek Talepleri</h5>
                                    <p class="text-muted mb-0 small">En son oluşturulan destek talepleri</p>
                                </div>
                            </div>
                            <a href="{{ route('super.admin.destek.index') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-right me-1"></i> Tümünü Görüntüle
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body p-0">
                        @if(isset($recentTickets) && $recentTickets->count() > 0)
                            <div class="table-responsive table-modern">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0 fw-bold">Talep No</th>
                                            <th class="border-0 fw-bold">Firma</th>
                                            <th class="border-0 fw-bold">Kullanıcı</th>
                                            <th class="border-0 fw-bold">Konu</th>
                                            <th class="border-0 fw-bold">Durum</th>
                                            <th class="border-0 fw-bold">Öncelik</th>
                                            <th class="border-0 fw-bold">Tarih</th>
                                            <th class="border-0 fw-bold text-center">İşlem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentTickets as $ticket)
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
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-2">
                                                            <div class="avatar-title rounded-circle bg-soft-info text-info">
                                                                <i class="fas fa-building"></i>
                                                            </div>
                                                        </div>
                                                        <span>{{ $ticket->tenant->firma_adi ?? 'N/A' }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-2">
                                                            <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                                <i class="fas fa-user"></i>
                                                            </div>
                                                        </div>
                                                        <span>{{ $ticket->user->name }}</span>
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
                                                    @php
                                                        $statusConfig = [
                                                            'acik' => ['color' => 'warning', 'icon' => 'fas fa-clock', 'text' => 'Açık'],
                                                            'cevaplandi' => ['color' => 'info', 'icon' => 'fas fa-reply', 'text' => 'Cevaplandı'],
                                                            'kapali' => ['color' => 'success', 'icon' => 'fas fa-check-circle', 'text' => 'Kapalı']
                                                        ];
                                                        $currentStatus = $statusConfig[$ticket->status] ?? ['color' => 'secondary', 'icon' => 'fas fa-question', 'text' => $ticket->status_text ?? $ticket->status];
                                                    @endphp
                                                    
                                                    <span class="badge badge-modern bg-{{ $currentStatus['color'] }} bg-opacity-10 text-{{ $currentStatus['color'] }} border border-{{ $currentStatus['color'] }} border-opacity-25">
                                                        <i class="{{ $currentStatus['icon'] }} me-1"></i>{{ $currentStatus['text'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $priorityColors = [
                                                            'acil' => 'danger',
                                                            'kritik' => 'warning',
                                                            'yuksek' => 'primary',
                                                            'orta' => 'info',
                                                            'dusuk' => 'secondary'
                                                        ];
                                                        
                                                        $priorityIcons = [
                                                            'acil' => 'fas fa-exclamation-circle',
                                                            'kritik' => 'fas fa-shield-alt',
                                                            'yuksek' => 'fas fa-exclamation-triangle',
                                                            'orta' => 'fas fa-clock',
                                                            'dusuk' => 'fas fa-chevron-down'
                                                        ];
                                                    @endphp

                                                    <span class="badge badge-modern bg-{{ $priorityColors[$ticket->priority] ?? 'secondary' }} bg-opacity-10 text-{{ $priorityColors[$ticket->priority] ?? 'secondary' }} border border-{{ $priorityColors[$ticket->priority] ?? 'secondary' }} border-opacity-25">
                                                        <i class="{{ $priorityIcons[$ticket->priority] ?? 'fas fa-question' }} me-1"></i>
                                                        {{ $ticket->priority_text ?? ucfirst($ticket->priority) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="text-nowrap">
                                                        <div class="fw-medium">{{ $ticket->created_at->format('d.m.Y') }}</div>
                                                        <small class="text-muted">{{ $ticket->created_at->format('H:i') }}</small>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('super.admin.destek.show', $ticket->id) }}" 
                                                       class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                        <i class="fas fa-eye me-1"></i> Detay
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5 mx-3">
                                <div class="empty-state">
                                    <div class="empty-state-icon mb-4">
                                        <div class="avatar-xl mx-auto">
                                            <div class="avatar-title rounded-circle bg-light">
                                                <i class="fas fa-inbox fa-2x text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <h5 class="text-muted mb-3">Henüz destek talebi bulunmuyor</h5>
                                    <p class="text-muted mb-4">Kullanıcılar destek talebi oluşturdukça burada görünecektir.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Yardımcı Bilgi Kartı -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 bg-gradient-light">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm">
                                            <div class="avatar-title rounded-circle bg-warning bg-opacity-10 text-warning">
                                                <i class="fas fa-lightbulb"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Super Admin Destek Yönetimi</h6>
                                        <p class="mb-0 text-muted">Sistemdeki tüm destek taleplerini buradan yönetebilir, istatistiklerini takip edebilir ve raporlar oluşturabilirsiniz.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                                <a href="{{ route('super.admin.destek.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-list me-1"></i> Tüm Talepler
                                </a>
                                <a href="#" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-chart-bar me-1"></i> Raporlar
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