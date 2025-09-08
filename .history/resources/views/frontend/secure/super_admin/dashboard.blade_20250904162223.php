@extends('frontend.secure.user_master')
@section('user')

<div class="page-content">
    <div class="container-fluid">

        <!-- Başlık -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Super Admin Dashboard</h4>
                </div>
            </div>
        </div>

        <!-- İstatistik Kartları -->
        <div class="row g-3">
            <div class="col-xl-3 col-md-6">
                <div class="card shadow-sm border-0 bg-ticket-primary text-center p-3">
                    <h6 class="text-muted">Toplam Firma</h6>
                    <h2 class="fw-bold">{{ $stats['total_tenants'] }}</h2>
                    <i class="fas fa-building fa-2x text-primary"></i>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card shadow-sm border-0 bg-ticket-success text-center p-3">
                    <h6 class="text-muted">Aktif Firma</h6>
                    <h2 class="fw-bold">{{ $stats['active_tenants'] }}</h2>
                    <i class="fas fa-check-circle fa-2x text-success"></i>
                    <small class="text-success d-block">
                        {{ round(($stats['active_tenants'] / max($stats['total_tenants'],1)) * 100) }}%
                    </small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card shadow-sm border-0 bg-ticket-info text-center p-3">
                    <h6 class="text-muted">Toplam Kullanıcı</h6>
                    <h2 class="fw-bold">{{ $stats['total_users'] }}</h2>
                    <i class="fas fa-users fa-2x text-info"></i>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card shadow-sm border-0 bg-ticket-warning text-center p-3">
                    <h6 class="text-muted">Aktif Kullanıcı</h6>
                    <h2 class="fw-bold">{{ $stats['active_users'] }}</h2>
                    <i class="fas fa-user-check fa-2x text-warning"></i>
                    <small class="text-success d-block">
                        {{ round(($stats['active_users'] / max($stats['total_users'],1)) * 100) }}%
                    </small>
                </div>
            </div>
        </div>

        <!-- Hızlı Erişim ve Sistem Durumu -->
        <div class="row mt-4">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0">Hızlı Erişim</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <a href="{{ route('super.admin.tenants') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-building text-primary me-2"></i> Tüm Firmaları Yönet
                                </div>
                                <span class="badge bg-light text-dark">{{ $stats['total_tenants'] }}</span>
                            </a>
                            <a href="{{ route('super.admin.users') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-users text-success me-2"></i> Kullanıcı Yönetimi
                                </div>
                                <span class="badge bg-light text-dark">{{ $stats['total_users'] }}</span>
                            </a>
                            <a href="{{ route('super.admin.reports') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-chart-line text-info me-2"></i> Sistem Raporları
                                </div>
                                <span class="badge bg-light text-dark">12</span>
                            </a>
                            <a href="{{ route('super.admin.settings') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-cog text-secondary me-2"></i> Sistem Ayarları
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sistem Durumu -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0">Sistem Durumu</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1">Aktif Firma Oranı</p>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-success" role="progressbar" 
                                style="width: {{ round(($stats['active_tenants'] / max($stats['total_tenants'],1)) * 100) }}%">
                                {{ round(($stats['active_tenants'] / max($stats['total_tenants'],1)) * 100) }}%
                            </div>
                        </div>

                        <p class="mb-1">Aktif Kullanıcı Oranı</p>
                        <div class="progress">
                            <div class="progress-bar bg-info" role="progressbar" 
                                style="width: {{ round(($stats['active_users'] / max($stats['total_users'],1)) * 100) }}%">
                                {{ round(($stats['active_users'] / max($stats['total_users'],1)) * 100) }}%
                            </div>
                        </div>

                        <div class="alert alert-success mt-3 mb-0">
                            <i class="fas fa-check-circle me-2"></i> Sistem optimal düzeyde çalışıyor.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Super Admin Yetkileri -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0">Super Admin Yetkileri</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-user-shield me-2"></i>
                            Super Admin olarak giriş yaptınız.
                            <ul class="mt-2 mb-0">
                                <li>Tüm firmaları görüntüleme ve yönetme</li>
                                <li>Herhangi bir firmanın herhangi bir kullanıcısı olarak giriş yapma</li>
                                <li>Sistem genelinde istatistikler görme</li>
                                <li>Firma durumlarını aktif/pasif yapma</li>
                                <li>Tüm impersonation işlemlerini gerçekleştirme</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
