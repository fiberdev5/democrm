{{-- resources/views/frontend/secure/super_admin/support/dashboard.blade.php --}}

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
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.dashboard') }}">Super Admin</a></li>
                            <li class="breadcrumb-item active">Destek Talepleri</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- İstatistikler -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Toplam Talep</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <span class="counter-value">{{ $stats['total'] }}</span>
                                </h4>
                            </div>
                            <div class="flex-shrink-0">
    <i class="fas fa-ticket-alt fs-3 text-primary"></i>
</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Açık Talepler</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <span class="counter-value text-warning">{{ $stats['open'] }}</span>
                                </h4>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock fs-3 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Cevaplanan</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <span class="counter-value text-info">{{ $stats['answered'] }}</span>
                                </h4>
                            </div>
                            <div class="flex-shrink-0">
    <i class="fas fa-reply fs-3 text-info"></i>
</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Yüksek Öncelik</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                    <span class="counter-value text-danger">{{ $stats['high_priority'] }}</span>
                                </h4>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-danger-subtle rounded fs-3">
                                    <i class="fas fa-exclamation-triangle text-danger"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hızlı Erişim -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Hızlı Erişim</h5>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <a href="{{ route('super.admin.destek.index') }}" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="fas fa-list me-2"></i>Tüm Talepler
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('super.admin.destek.index', ['status' => 'acik']) }}" class="btn btn-outline-warning w-100 mb-2">
                                    <i class="fas fa-clock me-2"></i>Açık Talepler
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('super.admin.destek.index', ['priority' => 'yuksek']) }}" class="btn btn-outline-danger w-100 mb-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Acil Talepler
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('super.admin.destek.index', ['status' => 'cevaplandi']) }}" class="btn btn-outline-info w-100 mb-2">
                                    <i class="fas fa-reply me-2"></i>Cevaplanan
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
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Son Destek Talepleri</h5>
                            <a href="{{ route('super.admin.destek.index') }}" class="btn btn-sm btn-primary">
                                Tümünü Görüntüle <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        
                        @if($recentTickets && $recentTickets->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-nowrap">
                                    <thead>
                                        <tr>
                                            <th>Talep No</th>
                                            <th>Firma</th>
                                            <th>Kullanıcı</th>
                                            <th>Konu</th>
                                            <th>Durum</th>
                                            <th>Öncelik</th>
                                            <th>Tarih</th>
                                            <th>İşlem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentTickets as $ticket)
                                            <tr>
                                                <td><strong>{{ $ticket->ticket_number }}</strong></td>
                                                <td>{{ $ticket->tenant->firma_adi ?? 'N/A' }}</td>
                                                <td>{{ $ticket->user->name }}</td>
                                                <td>
                                                    <div style="max-width: 200px;">
                                                        {{ Str::limit($ticket->subject, 40) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $ticket->status_color }}">
                                                        {{ $ticket->status_text }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $ticket->priority == 'yuksek' ? 'danger' : 'secondary' }}">
                                                        {{ $ticket->priority_text }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small>{{ $ticket->created_at->format('d.m.Y H:i') }}</small>
                                                </td>
                                                <td>
                                                    <a href="{{ route('super.admin.destek.show', $ticket->id) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">Henüz destek talebi bulunmuyor</h6>
                                <p class="text-muted">Kullanıcılar destek talebi oluşturdukça burada görünecektir.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection