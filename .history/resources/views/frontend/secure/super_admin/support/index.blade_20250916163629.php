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
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.dashboard') }}" class="text-decoration-none">Super Admin</a></li>
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
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label text-muted small">Durum</label>
                                <select id="statusFilter" class="form-select">
                                    <option value="">Tüm Durumlar</option>
                                    <option value="acik">Açık</option>
                                    <option value="cevaplandi">Cevaplandı</option>
                                    <option value="kapali">Kapalı</option>
                                </select>
                            </div>
                        
                            <div class="col-md-2">
                                <label class="form-label text-muted small">Öncelik</label>
                                <select id="priorityFilter" class="form-select">
                                    <option value="">Tüm Öncelikler</option>
                                    <option value="acil">Acil</option>
                                    <option value="kritik">Kritik</option>
                                    <option value="yuksek">Yüksek</option>
                                    <option value="orta">Orta</option>
                                    <option value="dusuk">Düşük</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="form-label text-muted small">Kategori</label>
                                <select id="categoryFilter" class="form-select">
                                    <option value="">Tüm Kategoriler</option>
                                    <option value="teknik_sorun">Teknik Sorun</option>
                                    <option value="faturalandirma">Faturalandırma</option>
                                    <option value="ozellik_talebi">Özellik Talebi</option>
                                    <option value="genel_destek">Genel Destek</option>
                                    <option value="hesap_sorunu">Hesap Sorunu</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label text-muted small">Firma</label>
                                <select id="tenantFilter" class="form-select">
                                    <option value="">Tüm Firmalar</option>
                                    @if(isset($tenants))
                                        @foreach($tenants as $tenant)
                                            <option value="{{ $tenant->id }}">{{ $tenant->firma_adi }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
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
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="superAdminSupportTable">
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
                                    @if(isset($tickets) && $tickets->count() > 0)
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
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-2">
                                                            <div class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                                <i class="fas fa-building"></i>
                                                            </div>
                                                        </div>
                                                        <span>{{ $ticket->tenant->firma_adi ?? 'Bilinmiyor' }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-2">
                                                            <div class="avatar-title rounded-circle bg-soft-primary text-primary">
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
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Datatable pagination stilleri */
.pagination .page-item.active .page-link {
    background-color: #505d69 !important;
    border-color: #505d69 !important;
}

.pagination .page-link {
    height: 30px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.dataTables_wrapper .dataTables_length {
    float: right !important;
    margin-left: 10px !important;
}

.dataTables_wrapper .dataTables_length select {
    height: 30px !important;
    padding: 6px 12px !important;
    background-color: #505d69 !important;
    color: white !important;
    border-color: #505d69 !important;
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e") !important;
    background-repeat: no-repeat !important;
    background-position: right 8px center !important;
    background-size: 16px !important;
    padding-right: 32px !important;
    cursor: pointer !important;
}

.dataTables_wrapper .dataTables_paginate {
    display: flex !important;
    align-items: center !important;
}

.dataTables_wrapper .dataTables_paginate .pagination {
    margin: 0 !important;
    display: flex !important;
    align-items: center !important;
}

.dataTables_wrapper .bottom {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    flex-wrap: wrap !important;
    gap: 10px !important;
}
</style>

<script>
$(document).ready(function() {
    var table = $('#superAdminSupportTable').DataTable({
        processing: false,
        serverSide: false,
        order: [[0, 'desc']],
        
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            },
            sEmptyTable: "Sistemde destek talebi bulunmuyor",
            sInfo: "Talep Sayısı: _TOTAL_",
            sInfoEmpty: "Kayıt yok",
            sZeroRecords: "Eşleşen kayıt bulunamadı",
            sLengthMenu: "_MENU_",
            oPaginate: {
                sFirst: "İlk",
                sLast: "Son",
                sNext: '<i class="fas fa-angle-right"></i>',
                sPrevious: '<i class="fas fa-angle-left"></i>'
            }
        },
        
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
        },
        
        dom: '<"top">rt<"bottom"i<"float-end"lp>><"clear">',
        lengthMenu: [ [25, 50, 100], [25, 50, 100] ]
    });

    // Filtreler değiştiğinde tabloyu yeniden çiz
    $('#statusFilter, #priorityFilter, #categoryFilter, #tenantFilter').change(function() {
        table.draw();
    });

    // Özel filtreleme fonksiyonu
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'superAdminSupportTable') {
                return true;
            }

            var statusFilter = $('#statusFilter').val();
            var priorityFilter = $('#priorityFilter').val();
            var categoryFilter = $('#categoryFilter').val();
            var tenantFilter = $('#tenantFilter').val();

            // Durum filtresi
            if (statusFilter && !data[6].includes(statusFilter)) {
                return false;
            }

            // Öncelik filtresi
            if (priorityFilter && !data[5].includes(priorityFilter)) {
                return false;
            }

            // Kategori filtresi
            if (categoryFilter && !data[4].includes(categoryFilter)) {
                return false;
            }

            // Firma filtresi
            if (tenantFilter && !data[1].includes(tenantFilter)) {
                return false;
            }

            return true;
        }
    );
});
</script>
@endsection