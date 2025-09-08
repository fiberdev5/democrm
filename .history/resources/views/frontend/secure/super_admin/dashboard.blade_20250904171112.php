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
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-medium text-muted mb-0">Toplam Firma</p>
                            </div>
                            <div class="flex-shrink-0">
                                <h5 class="text-primary fs-4 mb-0">
                                    <i class="ri-building-4-fill align-bottom"></i>
                                </h5>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h2 class="fw-semibold mb-0 fs-2">{{ $stats['total_tenants'] }}</h2>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-light text-primary rounded fs-3">
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
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-medium text-muted mb-0">Aktif Firma</p>
                            </div>
                            <div class="flex-shrink-0">
                                <h5 class="text-success fs-4 mb-0">
                                    <i class="ri-checkbox-circle-fill align-bottom"></i>
                                </h5>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h2 class="fw-semibold mb-0 fs-2">{{ $stats['active_tenants'] }}</h2>
                                @if($stats['total_tenants'] > 0)
                                    <p class="text-muted mb-0"><span class="badge bg-success-subtle text-success mb-0"><i class="ri-arrow-up-s-line align-middle"></i> {{ round(($stats['active_tenants'] / $stats['total_tenants']) * 100) }}%</span> Aktif</p>
                                @endif
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-light text-success rounded fs-3">
                                    <i class="ri-check-line"></i>
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
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-medium text-muted mb-0">Toplam Kullanıcı</p>
                            </div>
                            <div class="flex-shrink-0">
                                <h5 class="text-info fs-4 mb-0">
                                    <i class="ri-group-fill align-bottom"></i>
                                </h5>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h2 class="fw-semibold mb-0 fs-2">{{ $stats['total_users'] }}</h2>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-light text-info rounded fs-3">
                                    <i class="ri-user-2-line"></i>
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
                            <div class="flex-grow-1">
                                <p class="text-uppercase fw-medium text-muted mb-0">Aktif Kullanıcı</p>
                            </div>
                            <div class="flex-shrink-0">
                                <h5 class="text-warning fs-4 mb-0">
                                    <i class="ri-user-shared-fill align-bottom"></i>
                                </h5>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h2 class="fw-semibold mb-0 fs-2">{{ $stats['active_users'] }}</h2>
                                @if($stats['total_users'] > 0)
                                    <p class="text-muted mb-0"><span class="badge bg-warning-subtle text-warning mb-0"><i class="ri-arrow-up-s-line align-middle"></i> {{ round(($stats['active_users'] / $stats['total_users']) * 100) }}%</span> Aktif</p>
                                @endif
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-light text-warning rounded fs-3">
                                    <i class="ri-user-follow-line"></i>
                                </span>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->

        <!-- Hızlı Erişim & Sistem Durumu -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Hızlı Erişim</h4>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('super.admin.tenants') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-xs">
                                            <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-4">
                                                <i class="ri-building-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">Tüm Firmaları Yönet</h6>
                                        <small class="text-muted">Sistemdeki tüm firmaları görüntüleyip yönetebilirsiniz.</small>
                                    </div>
                                </div>
                                <span class="badge bg-primary-subtle text-primary">{{ $stats['total_tenants'] }}</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-xs">
                                            <span class="avatar-title bg-success-subtle text-success rounded-circle fs-4">
                                                <i class="ri-user-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">Kullanıcı Yönetimi</h6>
                                        <small class="text-muted">Sistem kullanıcılarını görüntüleyin ve düzenleyin.</small>
                                    </div>
                                </div>
                                <span class="badge bg-success-subtle text-success">{{ $stats['total_users'] }}</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-xs">
                                            <span class="avatar-title bg-info-subtle text-info rounded-circle fs-4">
                                                <i class="ri-file-chart-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">Sistem Raporları</h6>
                                        <small class="text-muted">Detaylı sistem analitiklerini inceleyin.</small>
                                    </div>
                                </div>
                                <span class="badge bg-info-subtle text-info">12</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-xs">
                                            <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-4">
                                                <i class="ri-settings-4-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">Sistem Ayarları</h6>
                                        <small class="text-muted">Genel sistem konfigürasyonlarını yönetin.</small>
                                    </div>
                                </div>
                                <i class="ri-arrow-right-s-line fs-4 text-muted"></i>
                            </a>
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col -->

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Sistem Durumu</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <p class="text-muted mb-2">Aktif Firma Oranı</p>
                            @php
                                $activeTenantPercentage = ($stats['total_tenants'] > 0) ? round(($stats['active_tenants'] / $stats['total_tenants']) * 100) : 0;
                            @endphp
                            <div class="progress animated-progress custom-progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $activeTenantPercentage }}%" aria-valuenow="{{ $activeTenantPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <p class="text-end mb-0 mt-1"><span class="fw-medium">{{ $activeTenantPercentage }}%</span></p>
                        </div>
                        <div class="mb-4">
                            <p class="text-muted mb-2">Aktif Kullanıcı Oranı</p>
                            @php
                                $activeUserPercentage = ($stats['total_users'] > 0) ? round(($stats['active_users'] / $stats['total_users']) * 100) : 0;
                            @endphp
                            <div class="progress animated-progress custom-progress">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $activeUserPercentage }}%" aria-valuenow="{{ $activeUserPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <p class="text-end mb-0 mt-1"><span class="fw-medium">{{ $activeUserPercentage }}%</span></p>
                        </div>
                        <div class="alert alert-success d-flex align-items-center mb-0" role="alert">
                            <i class="ri-checkbox-circle-fill me-2 fs-4"></i>
                            <div>
                                <h6 class="alert-heading mb-1">Sistem Performansı</h6>
                                <p class="mb-0">Sistem optimal düzeyde çalışıyor. Tüm servisler aktif ve kullanıcı deneyimi sorunsuz.</p>
                            </div>
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->

        <!-- Super Admin Yetkileri -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Super Admin Yetkileri</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-primary alert-top-border d-flex align-items-center mb-0" role="alert">
                            <i class="ri-user-star-fill me-3 fs-4"></i>
                            <div>
                                <h5 class="alert-heading mb-1">Super Admin Olarak Giriş Yaptınız</h5>
                                <p class="mb-2">Super Admin olarak sahip olduğunuz yetkiler:</p>
                                <ul class="list-unstyled mb-0">
                                    <li><i class="ri-check-double-line text-success me-1"></i> Tüm firmaları görüntüleme ve yönetme</li>
                                    <li><i class="ri-check-double-line text-success me-1"></i> Herhangi bir firmanın herhangi bir kullanıcısı olarak giriş yapma</li>
                                    <li><i class="ri-check-double-line text-success me-1"></i> Sistem genelinde istatistikler görme</li>
                                    <li><i class="ri-check-double-line text-success me-1"></i> Firma durumlarını aktif/pasif yapma</li>
                                    <li><i class="ri-check-double-line text-success me-1"></i> Tüm impersonation işlemlerini gerçekleştirme</li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->
    </div><!-- container-fluid -->
</div><!-- End Page-content -->

@endsection

@section('script')
    <!-- Ekstra scriptler buraya eklenebilir -->
@endsection