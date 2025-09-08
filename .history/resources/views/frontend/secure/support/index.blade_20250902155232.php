{{-- resources/views/support/index.blade.php --}}

@extends('frontend.secure.user_master')

@section('title', 'Destek Taleplerim')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Başlık -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Destek Taleplerim</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('secure.home', Auth::user()->tenant_id) }}">Ana Sayfa</a></li>
                            <li class="breadcrumb-item active">Destek Taleplerim</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yeni Talep Butonu -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Destek Taleplerim</h5>
                            <a href="{{ route('support.create', Auth::user()->tenant_id) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Yeni Destek Talebi
                            </a>
                        </div>

                        @if($tickets->count() > 0)
                            <div class="table-responsive">
                                <table id="supportTicketsTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Talep No</th>
                                            <th>Konu</th>
                                            <th>Kategori</th>
                                            <th>Öncelik</th>
                                            <th>Durum</th>
                                            <th>Oluşturma Tarihi</th>
                                            <th>Son Yanıt</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tickets as $ticket)
                                            <tr>
                                                <td>{{ $ticket->ticket_number }}</td>
                                                <td>{{ $ticket->subject }}</td>
                                                <td>
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
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $ticket->status_color }}">
                                                        {{ $ticket->status_text }}
                                                    </span>
                                                </td>
                                                <td>{{ $ticket->created_at->format('d.m.Y H:i') }}</td>
                                                <td>
                                                    @if($ticket->last_reply_at)
                                                        {{ $ticket->last_reply_at->format('d.m.Y H:i') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('support.show', [$ticket->tenant_id, $ticket->id]) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i> Detay
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- DataTables kendi sayfalama ve aramasını yapacağı için Laravel'in sayfalama linklerini kaldırıyoruz --}}
                            {{-- <div class="d-flex justify-content-center mt-3">
                                {{ $tickets->links() }}
                            </div> --}}
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Henüz bir destek talebiniz bulunmuyor</h5>
                                <p class="text-muted">İlk destek talebinizi oluşturmak için yukarıdaki butona tıklayın.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#supportTicketsTable').DataTable({
            "paging": true,       // Sayfalama aktif mi?
            "searching": true,    // Arama kutusu aktif mi?
            "ordering": true,     // Sıralama aktif mi?
            "info": true,         // Kayıt sayısı bilgisini göster
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/tr.json" // Türkçe dil desteği
            }
        });
    });
</script>
@endpush
@endsection