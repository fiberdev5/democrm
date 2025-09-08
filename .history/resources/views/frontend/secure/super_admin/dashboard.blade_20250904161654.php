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
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Toplam Firma</p>
                                <h4 class="mb-0">{{ $stats['total_tenants'] }}</h4>
                            </div>
                            <div class="mini-stat-icon me-3">
                                <span class="avatar-title bg-soft-primary rounded-circle text-primary font-size-24">
                                    <i class="fas fa-building"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Aktif Firma</p>
                                <h4 class="mb-0">
                                    {{ $stats['active_tenants'] }}
                                    <span class="font-size-12 text-success ms-2">
                                        <i class="mdi mdi-arrow-up me-1"></i>
                                        {{ $stats['total_tenants'] > 0 ? round(($stats['active_tenants'] / $stats['total_tenants']) * 100) : 0 }}%
                                    </span>
                                </h4>
                            </div>
                            <div class="mini-stat-icon me-3">
                                <span class="avatar-title bg-soft-success rounded-circle text-success font-size-24">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Toplam Kullanıcı</p>
                                <h4 class="mb-0">{{ $stats['total_users'] }}</h4>
                            </div>
                            <div class="mini-stat-icon me-3">
                                <span class="avatar-title bg-soft-info rounded-circle text-info font-size-24">
                                    <i class="fas fa-users"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-truncate font-size-14 mb-2">Aktif Kullanıcı</p>
                                <h4 class="mb-0">
                                    {{ $stats['active_users'] }}
                                    <span class="font-size-12 text-warning ms-2">
                                        <i class="mdi mdi-arrow-up me-1"></i>
                                        {{ $stats['total_users'] > 0 ? round(($stats['active_users'] / $stats['total_users']) * 100) : 0 }}%
                                    </span>
                                </h4>
                            </div>
                            <div class="mini-stat-icon me-3">
                                <span class="avatar-title bg-soft-warning rounded-circle text-warning font-size-24">
                                    <i class="fas fa-user-check"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End İstatistik Kartları -->

        <div class="row">
            <!-- Hızlı Erişim -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Hızlı Erişim</h4>
                        <div class="list-group list-group-flush">
                            <a href="{{ route('super.admin.tenants') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs me-3">
                                        <span class="avatar-title rounded-circle bg-soft-primary text-primary font-size-18">
                                            <i class="fas fa-building"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h5 class="font-size-14 mb-1">Tüm Firmaları Yönet</h5>
                                        <p class="font-size-13 text-muted mb-0">Sistemdeki tüm firmaları görüntüleyip yönetebilirsiniz.</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark font-size-13 me-2">{{ $stats['total_tenants'] }}</span>
                                    <i class="mdi mdi-arrow-right font-size-16"></i>
                                </div>
                            </a>
                            <a href="{{ route('super.admin.users') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs me-3">
                                        <span class="avatar-title rounded-circle bg-soft-info text-info font-size-18">
                                            <i class="fas fa-users"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h5 class="font-size-14 mb-1">Kullanıcı Yönetimi</h5>
                                        <p class="font-size-13 text-muted mb-0">Sistem kullanıcılarını görüntüleyin ve düzenleyin.</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark font-size-13 me-2">{{ $stats['total_users'] }}</span>
                                    <i class="mdi mdi-arrow-right font-size-16"></i>
                                </div>
                            </a>
                            <a href="{{ route('super.admin.reports') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs me-3">
                                        <span class="avatar-title rounded-circle bg-soft-danger text-danger font-size-18">
                                            <i class="fas fa-chart-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h5 class="font-size-14 mb-1">Sistem Raporları</h5>
                                        <p class="font-size-13 text-muted mb-0">Detaylı sistem analitiklerini inceleyin.</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark font-size-13 me-2">12</span> <!-- Örnek: Gerçek veri ile değiştirilebilir -->
                                    <i class="mdi mdi-arrow-right font-size-16"></i>
                                </div>
                            </a>
                            <a href="{{ route('super.admin.settings') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs me-3">
                                        <span class="avatar-title rounded-circle bg-soft-secondary text-secondary font-size-18">
                                            <i class="fas fa-cog"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h5 class="font-size-14 mb-1">Sistem Ayarları</h5>
                                        <p class="font-size-13 text-muted mb-0">Genel sistem konfigürasyonlarını yönetin.</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="mdi mdi-arrow-right font-size-16"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Hızlı Erişim -->

            <!-- Sistem Durumu -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Sistem Durumu</h4>

                        <div class="mb-4">
                            <p class="mb-2">Aktif Firma Oranı <span class="float-end">{{ $stats['total_tenants'] > 0 ? round(($stats['active_tenants'] / $stats['total_tenants']) * 100) : 0 }}%</span></p>
                            <div class="progress animated-progress custom-progress mt-2">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $stats['total_tenants'] > 0 ? round(($stats['active_tenants'] / $stats['total_tenants']) * 100) : 0 }}%" aria-valuenow="{{ $stats['total_tenants'] > 0 ? round(($stats['active_tenants'] / $stats['total_tenants']) * 100) : 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="mb-2">Aktif Kullanıcı Oranı <span class="float-end">{{ $stats['total_users'] > 0 ? round(($stats['active_users'] / $stats['total_users']) * 100) : 0 }}%</span></p>
                            <div class="progress animated-progress custom-progress mt-2">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $stats['total_users'] > 0 ? round(($stats['active_users'] / $stats['total_users']) * 100) : 0 }}%" aria-valuenow="{{ $stats['total_users'] > 0 ? round(($stats['active_users'] / $stats['total_users']) * 100) : 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-light rounded">
                            <h5 class="font-size-14 mb-2">Sistem Performansı</h5>
                            <p class="text-muted mb-0">Sistem optimal düzeyde çalışıyor. Tüm servisler aktif ve kullanıcı deneyimi sorunsuz.</p>
                        </div>

                    </div>
                </div>
            </div>
            <!-- End Sistem Durumu -->
        </div>

        <!-- Super Admin Yetkileri -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Super Admin Yetkileri</h4>
                        <div class="alert alert-primary p-4" role="alert">
                            <h5 class="mb-3">
                                <span class="badge bg-primary me-2">Super Admin Olarak Giriş Yaptınız</span>
                            </h5>
                            <p class="font-size-14 mb-3">Super Admin olarak sahip olduğunuz yetkiler:</p>
                            <ul class="list-unstyled mb-0">
                                <li class="py-1"><i class="mdi mdi-check-circle text-success me-2"></i>Tüm firmaları görüntüleme ve yönetme</li>
                                <li class="py-1"><i class="mdi mdi-check-circle text-success me-2"></i>Herhangi bir firmanın herhangi bir kullanıcısı olarak giriş yapma</li>
                                <li class="py-1"><i class="mdi mdi-check-circle text-success me-2"></i>Sistem genelinde istatistikler görme</li>
                                <li class="py-1"><i class="mdi mdi-check-circle text-success me-2"></i>Firma durumlarını aktif/pasif yapma</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Super Admin Yetkileri -->

    </div>
</div>

@endsection