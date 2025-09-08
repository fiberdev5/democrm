@extends('frontend.secure.user_master')
@section('user')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Super Admin Dashboard</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- İstatistik Kartları -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-1 overflow-hidden">
                                <p class="text-truncate font-size-14 mb-2">Toplam Firma</p>
                                <h4 class="mb-0">{{ $stats['total_tenants'] }}</h4>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-building fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-1 overflow-hidden">
                                <p class="text-truncate font-size-14 mb-2">Aktif Firma</p>
                                <h4 class="mb-0">{{ $stats['active_tenants'] }}</h4>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-1 overflow-hidden">
                                <p class="text-truncate font-size-14 mb-2">Toplam Kullanıcı</p>
                                <h4 class="mb-0">{{ $stats['total_users'] }}</h4>
                            </div>
                            <div class="text-info">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-1 overflow-hidden">
                                <p class="text-truncate font-size-14 mb-2">Aktif Kullanıcı</p>
                                <h4 class="mb-0">{{ $stats['active_users'] }}</h4>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-user-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hızlı Erişim Kartları -->
        <div class="row d-flex align-items-stretch">
            <div class="col-lg-6">
                <div class="card h-90">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Hızlı Erişim</h4>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('super.admin.tenants') }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">
                                        <i class="fas fa-building me-2 text-primary"></i>
                                        Tüm Firmaları Yönet
                                    </h5>
                                    <small class="text-muted">{{ $stats['total_tenants'] }} firma</small>
                                </div>
                                <p class="mb-1">Sistemdeki tüm firmaları görüntüleyip yönetebilirsiniz.</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-90">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Sistem Durumu</h4>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-success mb-1">
                                        {{ round(($stats['active_tenants'] / max($stats['total_tenants'], 1)) * 100) }}%
                                    </h4>
                                    <p class="text-muted mb-0">Aktif Firma Oranı</p>
                                </div>
                            </div>
                            <div class="col-6">
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

        <!-- Son İşlemler -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Super Admin Yetkileri</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5 class="alert-heading">
                                <i class="fas fa-user-shield me-2"></i>Super Admin Olarak Giriş Yaptınız
                            </h5>
                            <p class="mb-2">Super Admin olarak sahip olduğunuz yetkiler:</p>
                            <ul class="mb-0">
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