{{-- resources/views/frontend/secure/super_admin/support/dashboard.blade.php --}}

@extends('frontend.secure.user_master')

@section('user')

<style>
/* Gradient Backgrounds */
.bg-ticket-secondary{
    background: #495057;
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
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    border: 1px solid #dee2e6 !important;
    border-radius: 15px;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    color: #495057 !important;
    text-decoration: none;
    font-weight: 500;
    padding: 1.25rem 1.5rem;
    position: relative;
    overflow: hidden;
    transform: perspective(1px) translateZ(0);
}

/* Buton içindeki metinler */
.quick-action-btn .fw-semibold,
.quick-action-btn small,
.quick-action-btn h4 {
    color: #495057 !important;
}

/* İkonlar için farklı renkler - her buton tipi için */
.quick-action-btn.btn-all i {
    color: #0d6efd !important;
}

.quick-action-btn.btn-open i {
    color: #ffc107 !important;
}

.quick-action-btn.btn-urgent i {
    color: #dc3545 !important;
}

.quick-action-btn.btn-answered i {
    color: #198754 !important;
}

/* Hover efektleri */
.quick-action-btn:hover {
    transform: translateY(-4px) scale(1.01);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    color: #495057 !important;
    text-decoration: none;
    border-color: #ced4da !important;
}

/* Hover durumunda metinler */
.quick-action-btn:hover .fw-semibold,
.quick-action-btn:hover small,
.quick-action-btn:hover h4 {
    color: #495057 !important;
}

/* Active durumu */
.quick-action-btn:active {
    transform: translateY(-2px) scale(1.005);
}

/* Focus durumu */
.quick-action-btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    border-color: #86b7fe !important;
}

/* Şimmer efekti - daha sakin */
.quick-action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255,255,255,0.3),
        transparent
    );
    transition: left 0.6s ease;
}

.quick-action-btn:hover::before {
    left: 100%;
}

/* İkon animasyonları - daha sakin */
.quick-action-btn i {
    transition: transform 0.3s ease;
    margin-right: 0.75rem;
    font-size: 1.5rem;
}

.quick-action-btn:hover i {
    transform: scale(1.05);
}

/* Sayı animasyonları */
.quick-action-count h4 {
    transition: all 0.3s ease;
}

.quick-action-btn:hover .quick-action-count h4 {
    transform: scale(1.05);
}

/* Loading state */
.quick-action-btn.loading {
    pointer-events: none;
    opacity: 0.8;
}

.quick-action-btn.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    right: 1rem;
    width: 16px;
    height: 16px;
    border: 2px solid #495057;
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive düzenlemeler */
@media (max-width: 768px) {
    .quick-action-btn {
        font-size: 0.875rem;
        padding: 1rem 1.25rem !important;
        margin-bottom: 0.75rem;
    }
    
    .quick-action-btn:hover {
        transform: translateY(-3px) scale(1.005);
    }
    
    .quick-action-btn i {
        font-size: 1.25rem;
        margin-right: 0.5rem;
    }
    
    .quick-action-count h4 {
        font-size: 1.5rem;
    }
    
    .quick-action-content {
        flex-direction: column;
        text-align: center;
    }
    
    .quick-action-count {
        margin-left: 0;
        margin-top: 0.5rem;
    }
}

/* İstatistik kartları ile uyum için alternatif stiller */
.quick-action-variant {
    background: #ffffff !important;
    border: 1px solid #e3e6f0 !important;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}

.quick-action-variant:hover {
    box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.2) !important;
}

/* Kart içindeki içerik düzenlemesi */
.quick-action-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
}

.quick-action-info {
    flex: 1;
}

.quick-action-count {
    flex-shrink: 0;
    margin-left: 1rem;
    text-align: right;
}

.quick-action-count h4 {
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
}

/* Metin geçişleri */
.quick-action-btn .text-start div {
    transition: all 0.3s ease;
    margin-bottom: 0.25rem;
}

.quick-action-btn:hover .text-start div {
    letter-spacing: 0.2px;
}

.quick-action-btn small {
    opacity: 0.75;
    transition: opacity 0.3s ease;
    font-size: 0.8rem;
}

.quick-action-btn:hover small {
    opacity: 0.9;
}

/* Subtle border animation */
.quick-action-btn::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 15px;
    padding: 1px;
    background: linear-gradient(45deg, transparent, rgba(13, 110, 253, 0.1), transparent);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: subtract;
    mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    mask-composite: subtract;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.quick-action-btn:hover::after {
    opacity: 1;
}

/* Kart içeriği düzenlemesi - Sayılar yanda */
.quick-action-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
}

.quick-action-info {
    flex: 1;
}

.quick-action-count {
    flex-shrink: 0;
    margin-left: 1rem;
    text-align: right;
}

.quick-action-count h4 {
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
}

/* Loading state için pulse animasyonu - daha sakin */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

.quick-action-btn:focus {
    animation: pulse 1s ease-in-out;
    outline: none;
    box-shadow: 0 0 0 3px rgba(255,255,255,0.2);
}

/* Responsive düzenlemeler */
@media (max-width: 768px) {
    .quick-action-btn {
        font-size: 0.875rem;
        padding: 1rem 1.25rem !important;
        margin-bottom: 0.75rem;
    }
    
    .quick-action-btn:hover {
        transform: translateY(-4px) scale(1.01);
    }
    
    .quick-action-btn i {
        font-size: 1.25rem;
        margin-right: 0.5rem;
    }
    
    .quick-action-count h4 {
        font-size: 1.5rem;
    }
    
    .quick-action-content {
        flex-direction: column;
        text-align: center;
    }
    
    .quick-action-count {
        margin-left: 0;
        margin-top: 0.5rem;
    }
}

/* Avatar Improvements */
.avatarsupport-xs { height: 1.5rem; width: 1.5rem; }
.avatarsupport-sm { height: 2rem; width: 2rem; }
.avatarsupport-xl { height: 4rem; width: 4rem; }

.avatarsupport-title {
    align-items: center;
    display: flex;
    font-size: 1rem;
    font-weight: 500;
    height: 100%;
    justify-content: center;
    width: 100%;
}

/* Table Improvements */
.support-dashboard-table-modern {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.support-dashboard-table-modern .table {
    margin-bottom: 0;
}

.support-dashboard-table-modern .table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
    transition: background-color 0.15s ease-in-out;
}

.support-dashboard-table-modern .table td, 
.support-dashboard-table-modern .table th {
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
        padding: 0.75rem 1rem !important;
        margin-bottom: 0.5rem;
    }
    
    .quick-action-btn:hover {
        transform: translateY(-4px) scale(1.01);
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
                        Destek Talepleri
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
        <!-- Hızlı Erişim -->
        <div class="row ">
            <div class="col-12">
                <div class="card quick-action-card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="d-flex align-items-center">
                                <div class="avatarsupport-sm me-3">
                                    <div class="avatarsupport-title bg-primary bg-opacity-10 text-primary rounded">
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
                                <a href="{{ route('super.admin.destek.index') }}" class="quick-action-btn btn-all btn w-100 d-flex align-items-center">
                                    <div class="quick-action-content">
                                        <div class="quick-action-info d-flex align-items-center">
                                            <i class="fas fa-list me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-semibold">Tüm Talepler</div>
                                                <small>Listeyi görüntüle</small>
                                            </div>
                                        </div>
                                        <div class="quick-action-count">
                                            <h4 class="fs-22 fw-semibold mb-0 text-white">
                                                <span class="counter-value">{{ $stats['total'] ?? 0 }}</span>
                                            </h4>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('super.admin.destek.index', ['status' => 'acik']) }}" class="quick-action-btn btn-open btn w-100 d-flex align-items-center">
                                    <div class="quick-action-content">
                                        <div class="quick-action-info d-flex align-items-center">
                                            <i class="fas fa-clock me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-semibold">Açık Talepler</div>
                                                <small>Bekleyen talepler</small>
                                            </div>
                                        </div>
                                        <div class="quick-action-count">
                                            <h4 class="fs-22 fw-semibold mb-0 text-white">
                                                <span class="counter-value">{{ $stats['open'] ?? 0 }}</span>
                                            </h4>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('super.admin.destek.index', ['priority' => 'acil']) }}" class="quick-action-btn btn-urgent btn w-100 d-flex align-items-center">
                                    <div class="quick-action-content">
                                        <div class="quick-action-info d-flex align-items-center">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-semibold">Acil Talepler</div>
                                                <small>Öncelikli çözüm</small>
                                            </div>
                                        </div>
                                        <div class="quick-action-count">
                                            <h4 class="fs-22 fw-semibold mb-0 text-white">
                                                <span class="counter-value">{{ $stats['high_priority'] ?? 0 }}</span>
                                            </h4>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <a href="{{ route('super.admin.destek.index', ['status' => 'cevaplandi']) }}" class="quick-action-btn btn-answered btn w-100 d-flex align-items-center">
                                    <div class="quick-action-content">
                                        <div class="quick-action-info d-flex align-items-center">
                                            <i class="fas fa-reply me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-semibold">Cevaplanan</div>
                                                <small>Yanıtlanan talepler</small>
                                            </div>
                                        </div>
                                        <div class="quick-action-count">
                                            <h4 class="fs-22 fw-semibold mb-0 text-white">
                                                <span class="counter-value">{{ $stats['answered'] ?? 0 }}</span>
                                            </h4>
                                        </div>
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
                                <div class="avatarsupport-sm me-3">
                                    <div class="avatarsupport-title bg-success bg-opacity-10 text-success rounded">
                                        <i class="fas fa-history"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">Son Destek Talepleri</h5>
                                    <p class="text-muted mb-0 small">En son oluşturulan destek talepleri</p>
                                </div>
                            </div>
                            <a href="{{ route('super.admin.destek.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right me-1"></i> Tümünü Görüntüle
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body p-0">
                        @if(isset($recentTickets) && $recentTickets->count() > 0)
                            <div class="table-responsive support-dashboard-table-modern">
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
                                                        <div class="avatarsupport-xs me-2">
                                                            <div class="avatarsupport-title rounded-circle bg-light text-primary">
                                                                <i class="fas fa-hashtag"></i>
                                                            </div>
                                                        </div>
                                                        <span class="fw-bold">{{ $ticket->ticket_number }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatarsupport-xs me-2">
                                                            <div class="avatarsupport-title rounded-circle bg-soft-primary text-primary">
                                                                <i class="fas fa-building"></i>
                                                            </div>
                                                        </div>
                                                        <span>{{ $ticket->tenant->firma_adi ?? 'N/A' }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatarsupport-xs me-2">
                                                            <div class="avatarsupport-title rounded-circle bg-soft-primary text-primary">
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
                                                            'acik' => ['color' => 'primary', 'icon' => 'fas fa-spinner', 'text' => 'Açık'],
                                                            'cevaplandi' => ['color' => 'warning', 'icon' => 'fas fa-check-circle', 'text' => 'Cevaplandı'],
                                                            'kapali' => ['color' => 'danger', 'icon' => 'fas fa-times-circle', 'text' => 'Kapatıldı']
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
                                                    class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                                                    title="Detay">
                                                        <i class="fas fa-eye"></i>
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
                                        <div class="avatarsupport-xl mx-auto">
                                            <div class="avatarsupport-title rounded-circle bg-light">
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
                                        <div class="avatarsupport-sm">
                                            <div class="avatarsupport-title rounded-circle bg-warning bg-opacity-10 text-warning">
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
