@extends('frontend.secure.user_master')
@section('admin')
<div class="page-content">
    <div class="container-fluid">
        
        <!-- Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Ana Sayfa Yönetimi</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Ana Sayfa</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#stats" role="tab">
                                    <i class="fas fa-chart-line me-1"></i> İstatistikler
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#modules" role="tab">
                                    <i class="fas fa-cube me-1"></i> Modüller
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#sectors" role="tab">
                                    <i class="fas fa-industry me-1"></i> Sektörler
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#integrations" role="tab">
                                    <i class="fas fa-plug me-1"></i> Entegrasyonlar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#testimonials" role="tab">
                                    <i class="fas fa-quote-left me-1"></i> Yorumlar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#faqs" role="tab">
                                    <i class="fas fa-question-circle me-1"></i> SSS
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content p-3">
                            <!-- İstatistikler Tab -->
                            <div class="tab-pane active" id="stats" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>İstatistikler</h5>
                                    <button class="btn btn-primary btn-sm" onclick="addStat()">
                                        <i class="fas fa-plus me-1"></i> Yeni Ekle
                                    </button>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="50">Sıra</th>
                                                <th>Sayı</th>
                                                <th>Etiket</th>
                                                <th width="100">Durum</th>
                                                <th width="150">İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody id="statsTable">
                                            @foreach($stats as $stat)
                                            <tr>
                                                <td>{{ $stat->order }}</td>
                                                <td>{{ $stat->data['number'] ?? '' }}</td>
                                                <td>{{ $stat->data['label'] ?? '' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $stat->is_active ? 'success' : 'danger' }}">
                                                        {{ $stat->is_active ? 'Aktif' : 'Pasif' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" onclick="editStat({{ $stat->id }})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteStat({{ $stat->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Diğer tablar için benzer yapı -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection