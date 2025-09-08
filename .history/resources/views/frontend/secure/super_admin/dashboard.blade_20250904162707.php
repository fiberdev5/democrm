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
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-truncate font-size-14 mb-2 text-muted">Toplam Firma</p>
                                <h4 class="mb-0">{{ $stats['total_tenants'] }}</h4>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-primary-subtle text-primary rounded fs-3">
                                    <i class="ri-building-line"></i>
                                </span>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-truncate font-size-14 mb-2 text-muted">Aktif Firma</p>
                                <h4 class="mb-0">{{ $stats['active_tenants'] }} <small class="text-success font-size-12 ms-1"><i class="mdi mdi-arrow-up"></i> {{ round(($stats['active_tenants'] / max($stats['total_tenants'], 1)) * 100) }}%</small></h4>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-success-subtle text-success rounded fs-3">
                                    <i class="ri-check-double-line"></i>
                                </span>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-truncate font-size-14 mb-2 text-muted">Toplam Kullanıcı</p>
                                <h4 class="mb-0">{{ $stats['total_users'] }}</h4>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-info-subtle text-info rounded fs-3">
                                    <i class="ri-group-line"></i>
                                </span>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->

            <div class="col-xl-3 col-md-6">
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-truncate font-size-14 mb-2 text-muted">Aktif Kullanıcı</p>
                                <h4 class="mb-0">{{ $stats['active_users'] }} <small class="text-success font-size-12 ms-1"><i class="mdi mdi-arrow-up"></i> {{ round(($stats['active_users'] / max($stats['total_users'], 1)) * 100) }}%</small></h4>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-warning-subtle text-warning rounded fs-3">
                                    <i class="ri-user-follow-line"></i>
                                </span>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->

        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Hızlı Erişim</h4>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('super.admin.tenants') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs flex-shrink-0 me-3">
                                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-4">
                                            <i class="ri-building-line"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Tüm Firmaları Yönet</h6>
                                        <p class="font-size-13 text-muted mb-0">Sistemdeki tüm firmaları görüntüleyip yönetebilirsiniz.</p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 text-end">
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $stats['total_tenants'] }}</span>
                                    <i class="ri-arrow-right-s-line align-middle ms-1"></i>
                                </div>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs flex-shrink-0 me-3">
                                        <span class="avatar-title bg-success-subtle text-success rounded-circle fs-4">
                                            <i class="ri-user-settings-line"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Kullanıcı Yönetimi</h6>
                                        <p class="font-size-13 text-muted mb-0">Sistemdeki tüm kullanıcıları görüntüleyip düzenleyin.</p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 text-end">
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $stats['total_users'] }}</span>
                                    <i class="ri-arrow-right-s-line align-middle ms-1"></i>
                                </div>
                            </a>
                             <a href="#" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs flex-shrink-0 me-3">
                                        <span class="avatar-title bg-info-subtle text-info rounded-circle fs-4">
                                            <i class="ri-line-chart-line"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Sistem Raporları</h6>
                                        <p class="font-size-13 text-muted mb-0">Detaylı sistem analizlerini inceleyin.</p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 text-end">
                                    <span class="badge bg-secondary-subtle text-secondary">12</span>
                                    <i class="ri-arrow-right-s-line align-middle ms-1"></i>
                                </div>
                            </a>
                             <a href="#" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs flex-shrink-0 me-3">
                                        <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-4">
                                            <i class="ri-settings-4-line"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Sistem Ayarları</h6>
                                        <p class="font-size-13 text-muted mb-0">Genel sistem konfigürasyonlarını yönetin.</p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 text-end">
                                    <i class="ri-arrow-right-s-line align-middle ms-1"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Sistem Durumu</h4>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-2">Aktif Firma Oranı</h6>
                        <div class="progress animated-progress custom-progress progress-label mb-3">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ round(($stats['active_tenants'] / max($stats['total_tenants'], 1)) * 100) }}%" aria-valuenow="{{ round(($stats['active_tenants'] / max($stats['total_tenants'], 1)) * 100) }}" aria-valuemin="0" aria-valuemax="100"><div class="label">{{ round(($stats['active_tenants'] / max($stats['total_tenants'], 1)) * 100) }}%</div></div>
                        </div>

                        <h6 class="mb-2">Aktif Kullanıcı Oranı</h6>
                        <div class="progress animated-progress custom-progress progress-label mb-4">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ round(($stats['active_users'] / max($stats['total_users'], 1)) * 100) }}%" aria-valuenow="{{ round(($stats['active_users'] / max($stats['total_users'], 1)) * 100) }}" aria-valuemin="0" aria-valuemax="100"><div class="label">{{ round(($stats['active_users'] / max($stats['total_users'], 1)) * 100) }}%</div></div>
                        </div>

                        <div class="alert alert-info border-0 mb-0">
                            <h6 class="alert-heading text-info">Sistem Performansı</h6>
                            <p class="mb-0 font-size-13">Sistem optimal düzeyde çalışıyor. Tüm servisler aktif ve kullanıcı deneyimi sorunsuz.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Super Admin Yetkileri -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Super Admin Yetkileri</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-primary-subtle text-primary fs-6 py-2 px-3 rounded-pill">
                                <i class="ri-shield-user-line me-1"></i> Super Admin Olarak Giriş Yaptınız
                            </span>
                        </div>
                        <p class="font-size-15 mb-3">Super Admin olarak sahip olduğunuz yetkiler:</p>
                        <ul class="list-unstyled mb-0 vstack gap-2">
                            <li><i class="ri-check-line align-middle text-success me-1"></i> Tüm firmaları görüntüleme ve yönetme</li>
                            <li><i class="ri-check-line align-middle text-success me-1"></i> Herhangi bir firmanın herhangi bir kullanıcısı olarak giriş yapma</li>
                            <li><i class="ri-check-line align-middle text-success me-1"></i> Sistem genelinde istatistikler görme</li>
                            <li><i class="ri-check-line align-middle text-success me-1"></i> Firma durumlarını aktif/pasif yapma</li>
                            <li><i class="ri-check-line align-middle text-success me-1"></i> Tüm impersonation işlemlerini gerçekleştirme</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection