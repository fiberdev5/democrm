@extends('frontend.secure.user_master')
@section('user')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Süper Yönetici Paneli</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item active">Gösterge Paneli</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- İstatistik Kartları -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-lg">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <span class="avatar-sm rounded-circle bg-light text-primary d-flex align-items-center justify-content-center">
                                    <i class="fas fa-building fa-lg"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-truncate font-size-14 text-muted mb-1">Toplam Firma</p>
                                <h4 class="mb-0">{{ $stats['total_tenants'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-lg">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <span class="avatar-sm rounded-circle bg-light text-success d-flex align-items-center justify-content-center">
                                    <i class="fas fa-check-circle fa-lg"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-truncate font-size-14 text-muted mb-1">Aktif Firma</p>
                                <h4 class="mb-0">{{ $stats['active_tenants'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-lg">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <span class="avatar-sm rounded-circle bg-light text-info d-flex align-items-center justify-content-center">
                                    <i class="fas fa-users fa-lg"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-truncate font-size-14 text-muted mb-1">Toplam Kullanıcı</p>
                                <h4 class="mb-0">{{ $stats['total_users'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm rounded-lg">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <span class="avatar-sm rounded-circle bg-light text-warning d-flex align-items-center justify-content-center">
                                    <i class="fas fa-user-check fa-lg"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-truncate font-size-14 text-muted mb-1">Aktif Kullanıcı</p>
                                <h4 class="mb-0">{{ $stats['active_users'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hızlı Erişim & Sistem Durumu Kartları -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-lg">
                    <div class="card-header bg-white pb-0">
                        <h5 class="card-title mb-0">Hızlı Erişim</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush border-top-0">
                            <a href="{{ route('super.admin.tenants') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                                <i class="fas fa-building me-3 text-secondary fa-lg"></i>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Tüm Firmaları Yönet</h6>
                                    <p class="font-size-13 text-muted mb-0">Sistemdeki tüm firmaları görüntüleyip yönetebilirsiniz.</p>
                                </div>
                                <span class="badge bg-light text-dark ms-auto">{{ $stats['total_tenants'] }} Firma</span>
                            </a>
                            <!-- Buraya başka hızlı erişim linkleri eklenebilir -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-lg">
                    <div class="card-header bg-white pb-0">
                        <h5 class="card-title mb-0">Sistem Durumu</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="py-3 border-end">
                                    <h4 class="text-success mb-1">
                                        {{ round(($stats['active_tenants'] / max($stats['total_tenants'], 1)) * 100) }}%
                                    </h4>
                                    <p class="text-muted mb-0">Aktif Firma Oranı</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="py-3">
                                    <h4 class="text-info mb-1">
                                        {{ round(($stats['active_users'] / max($stats['total_users'], 1)) * 100) }}%
                                    </h4>
                                    <p class="text-muted mb-0">Aktif Kullanıcı Oranı</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Son İşlemler / Süper Admin Yetkileri -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-lg">
                    <div class="card-header bg-white pb-0">
                        <h5 class="card-title mb-0">Süper Yönetici Yetkileri</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-primary bg-light-primary border-0 rounded-lg p-4 mb-0">
                            <h5 class="alert-heading text-primary mb-3">
                                <i class="fas fa-user-shield me-2"></i>Süper Yönetici Olarak Giriş Yaptınız
                            </h5>
                            <p class="mb-2 text-dark">Süper Yönetici olarak sahip olduğunuz yetkiler:</p>
                            <ul class="list-unstyled mb-0 text-dark">
                                <li class="mb-1"><i class="fas fa-caret-right me-2 text-primary"></i>Tüm firmaları görüntüleme ve yönetme</li>
                                <li class="mb-1"><i class="fas fa-caret-right me-2 text-primary"></i>Herhangi bir firmanın herhangi bir kullanıcısı olarak giriş yapma (Impersonation)</li>
                                <li class="mb-1"><i class="fas fa-caret-right me-2 text-primary"></i>Sistem genelinde detaylı istatistikler görme</li>
                                <li class="mb-1"><i class="fas fa-caret-right me-2 text-primary"></i>Firma durumlarını aktif/pasif yapma</li>
                                <li><i class="fas fa-caret-right me-2 text-primary"></i>Tüm impersonation işlemlerini gerçekleştirme</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection