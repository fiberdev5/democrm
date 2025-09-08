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

        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card card-h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-2">Toplam Firma</p>
                                <h4 class="mb-0">{{ $stats['total_tenants'] }}</h4>
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title bg-light text-primary rounded-3 font-size-22">
                                    <i class="fas fa-building"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-2">Aktif Firma</p>
                                <h4 class="mb-0">{{ $stats['active_tenants'] }}</h4>
                            </div>
                             <div class="avatar-sm">
                                <span class="avatar-title bg-light text-success rounded-3 font-size-22">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mt-2">
                             @php
                                $tenant_percentage = $stats['total_tenants'] > 0 ? round(($stats['active_tenants'] / $stats['total_tenants']) * 100) : 0;
                            @endphp
                            <span class="badge bg-success-subtle text-success">{{ $tenant_percentage }}%</span>
                            <span class="text-muted ms-1">oranla aktif</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-2">Toplam Kullanıcı</p>
                                <h4 class="mb-0">{{ $stats['total_users'] }}</h4>
                            </div>
                             <div class="avatar-sm">
                                <span class="avatar-title bg-light text-info rounded-3 font-size-22">
                                    <i class="fas fa-users"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card card-h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-2">Aktif Kullanıcı</p>
                                <h4 class="mb-0">{{ $stats['active_users'] }}</h4>
                            </div>
                             <div class="avatar-sm">
                                <span class="avatar-title bg-light text-warning rounded-3 font-size-22">
                                    <i class="fas fa-user-check"></i>
                                </span>
                            </div>
                        </div>
                         <div class="mt-2">
                             @php
                                $user_percentage = $stats['total_users'] > 0 ? round(($stats['active_users'] / $stats['total_users']) * 100) : 0;
                            @endphp
                            <span class="badge bg-success-subtle text-success">{{ $user_percentage }}%</span>
                            <span class="text-muted ms-1">oranla aktif</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Hızlı Erişim</h4>
                        <a href="{{ route('super.admin.tenants') }}" class="d-flex align-items-center p-3 rounded bg-light mb-3">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <span class="avatar-title rounded-circle bg-primary-subtle text-primary font-size-22"><i class="fas fa-building"></i></span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="font-size-15 mb-1">Tüm Firmaları Yönet</h5>
                                <p class="text-muted mb-0">Sistemdeki tüm firmaları görüntüleyip yönetebilirsiniz.</p>
                            </div>
                            <div class="flex-shrink-0">
                               <span class="badge rounded-pill bg-primary-subtle text-primary font-size-12">{{ $stats['total_tenants'] }}</span>
                            </div>
                        </a>
                        <a href="#" class="d-flex align-items-center p-3 rounded bg-light mb-3">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <span class="avatar-title rounded-circle bg-success-subtle text-success font-size-22"><i class="fas fa-users-cog"></i></span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="font-size-15 mb-1">Kullanıcı Yönetimi</h5>
                                <p class="text-muted mb-0">Sistem kullanıcılarını görüntüleyin ve düzenleyin.</p>
                            </div>
                             <div class="flex-shrink-0">
                               <span class="badge rounded-pill bg-primary-subtle text-primary font-size-12">{{ $stats['total_users'] }}</span>
                            </div>
                        </a>
                         <a href="#" class="d-flex align-items-center p-3 rounded bg-light mb-3">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <span class="avatar-title rounded-circle bg-info-subtle text-info font-size-22"><i class="fas fa-chart-pie"></i></span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="font-size-15 mb-1">Sistem Raporları</h5>
                                <p class="text-muted mb-0">Detaylı sistem analitiklerini inceleyin.</p>
                            </div>
                             <div class="flex-shrink-0">
                               <span class="badge rounded-pill bg-primary-subtle text-primary font-size-12">12</span>
                            </div>
                        </a>
                         <a href="#" class="d-flex align-items-center p-3 rounded bg-light">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <span class="avatar-title rounded-circle bg-secondary-subtle text-secondary font-size-22"><i class="fas fa-cogs"></i></span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="font-size-15 mb-1">Sistem Ayarları</h5>
                                <p class="text-muted mb-0">Genel sistem konfigürasyonlarını yönetin.</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card">
                     <div class="card-body">
                        <h4 class="card-title mb-4">Sistem Durumu</h4>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Aktif Firma Oranı</span>
                                <span class="fw-bold">{{ $tenant_percentage }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $tenant_percentage }}%;" aria-valuenow="{{ $tenant_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                         <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Aktif Kullanıcı Oranı</span>
                                <span class="fw-bold">{{ $user_percentage }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $user_percentage }}%;" aria-valuenow="{{ $user_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        
                        <div class="alert alert-success mt-4 mb-0">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="alert-heading">Sistem Performansı</h5>
                                    <p class="mb-0">Sistem optimal düzeyde çalışıyor. Tüm servisler aktif ve kullanıcı deneyimi sorunsuz.</p>
                                </div>
                            </div>
                        </div>

                     </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-info border-0 mb-0">
                            <h5 class="alert-heading">
                                <i class="fas fa-user-shield me-2"></i>Super Admin Olarak Giriş Yaptınız
                            </h5>
                            <p class="mt-3">Super Admin olarak sahip olduğunuz yetkiler:</p>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Tüm firmaları görüntüleme ve yönetme</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>Herhangi bir firmanın herhangi bir kullanıcısı olarak giriş yapma</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Tüm impersonation işlemlerini gerçekleştirme</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection