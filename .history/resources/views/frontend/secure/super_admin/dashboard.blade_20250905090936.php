@extends('frontend.secure.user_master')
@section('user')

<style>
    .modern-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        position: relative;
    }

    .modern-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    }

    .modern-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--accent-gradient);
    }

    .stat-card.primary { --accent-gradient: linear-gradient(90deg, #3498db, #5dade2); }
    .stat-card.success { --accent-gradient: linear-gradient(90deg, #27ae60, #58d68d); }
    .stat-card.info { --accent-gradient: linear-gradient(90deg, #8e44ad, #af7ac5); }
    .stat-card.warning { --accent-gradient: linear-gradient(90deg, #f39c12, #f8c471); }

    .stat-icon {
        width: 55px;
        height: 55px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--accent-gradient);
        color: white;
        font-size: 1.5rem;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 5px;
        counter-reset: value var(--target);
    }

    .stat-label {
        color: #7f8c8d;
        font-size: 0.95rem;
        font-weight: 500;
    }

    .chart-container {
        height: 300px;
        position: relative;
    }

    .action-item {
        padding: 15px;
        border-radius: 10px;
        transition: all 0.3s ease;
        cursor: pointer;
        border: 1px solid #ecf0f1;
        margin-bottom: 10px;
        text-decoration: none;
        color: inherit;
    }

    .action-item:hover {
        background: #f8f9fa;
        border-color: #3498db;
        color: inherit;
        text-decoration: none;
        transform: translateX(5px);
    }

    .action-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 1.3rem;
        color: white;
    }



    .status-item {
        text-align: center;
        padding: 20px;
        border-radius: 12px;
        background: #f8f9fa;
        border: 2px solid #ecf0f1;
        transition: all 0.3s ease;
    }

    .status-item:hover {
        border-color: #3498db;
        background: #ebf3fd;
    }

    .status-percentage {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .status-percentage.high { color: #27ae60; }
    .status-percentage.medium { color: #f39c12; }
    .status-percentage.low { color: #e74c3c; }

    .badge-danger { background-color: #e74c3c !important; }
    .badge-warning { background-color: #f39c12 !important; }
    .badge-success { background-color: #27ae60 !important; }

    @keyframes countUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-count {
        animation: countUp 0.8s ease-out;
    }

    .gradient-bg {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
    }
</style>

<div class="page-content gradient-bg">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-sm-0 text-primary">
                            <i class="fas fa-tachometer-alt me-2"></i>Super Admin Dashboard
                        </h4>
                        <p class="text-muted mb-0">Teknik Servis Yönetim Sistemi - Genel Bakış</p>
                    </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- İstatistik Kartları -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card modern-card stat-card primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label">Toplam Firma</p>
                                <h4 class="stat-value animate-count" data-target="{{ $stats['total_tenants'] }}">{{ $stats['total_tenants'] }}</h4>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card modern-card stat-card success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label">Aktif Firma</p>
                                <h4 class="stat-value animate-count" data-target="{{ $stats['active_tenants'] }}">{{ $stats['active_tenants'] }}</h4>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card modern-card stat-card info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label">Toplam Kullanıcı</p>
                                <h4 class="stat-value animate-count" data-target="{{ $stats['total_users'] }}">{{ $stats['total_users'] }}</h4>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card modern-card stat-card warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="stat-label">Aktif Kullanıcı</p>
                                <h4 class="stat-value animate-count" data-target="{{ $stats['active_users'] }}">{{ $stats['active_users'] }}</h4>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik ve Sistem Durumu -->
        <div class="row mb-4">
            <div class="col-lg-8 mb-3">
                <div class="card modern-card">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line text-primary me-2"></i>Son 7 Günlük Sistem Aktivitesi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="activityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-3">
                <div class="card modern-card">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie text-success me-2"></i>Sistem Durumu
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="status-item">
                                    <div class="status-percentage {{ $stats['active_tenant_percentage'] >= 80 ? 'high' : ($stats['active_tenant_percentage'] >= 60 ? 'medium' : 'low') }}">
                                        {{ $stats['active_tenant_percentage'] }}%
                                    </div>
                                    <div class="status-label">Aktif Firma Oranı</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="status-item">
                                    <div class="status-percentage {{ $stats['active_user_percentage'] >= 80 ? 'high' : ($stats['active_user_percentage'] >= 60 ? 'medium' : 'low') }}">
                                        {{ $stats['active_user_percentage'] }}%
                                    </div>
                                    <div class="status-label">Aktif Kullanıcı Oranı</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hızlı Erişim Kartları -->
        <div class="row mb-4">
            <!-- Firma Yönetimi -->
            <div class="col-lg-4 mb-3">
                <div class="card modern-card">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-building text-primary me-2"></i>Firma Yönetimi
                        </h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('super.admin.tenants') }}" class="action-item d-flex align-items-center">
                            <div class="action-icon" style="background: linear-gradient(135deg, #3498db, #5dade2);">
                                <i class="fas fa-list"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Tüm Firmaları Listele</h6>
                                <small class="text-muted">{{ $stats['total_tenants'] }} firma</small>
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </a>
                        
                        
                        <a href="{{ route('super.admin.tenants') }}" class="action-item d-flex align-items-center">
                            <div class="action-icon" style="background: linear-gradient(135deg, #27ae60, #58d68d);">
                                <i class="fas fa-user-secret"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Impersonation</h6>
                                <small class="text-muted">Herhangi bir kullanıcı olarak giriş</small>
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                             </a>
                        
                            <a href="{{ route('super.admin.tenants') }}" class="action-item d-flex align-items-center">
                            <div class="action-icon" style="background: linear-gradient(135deg, #8e44ad, #af7ac5);">
                                <i class="fas fa-plus-circle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Yeni Firma Ekle</h6>
                                <small class="text-muted">Sisteme yeni firma kaydı</small>
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                            </a>
            
                         
                    </div>
                </div>
            </div>

            <!-- Destek Talepleri -->
            <div class="col-lg-4 mb-3">
                <div class="card modern-card">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-headset text-danger me-2"></i>Destek Talepleri
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(isset($supportStats))
                        <a href="{{ route('super.admin.destek.index', ['priority' => 'acil']) }}" class="action-item d-flex align-items-center">
                            <div class="action-icon" style="background: linear-gradient(135deg, #e74c3c, #ec7063);">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Acil Talepler</h6>
                                <small class="text-muted">Hemen ilgilenilmesi gereken</small>
                            </div>
                            <span class="badge badge-danger">{{ $supportStats['urgent_tickets'] }}</span>
                        </a>

                        <a href="{{ route('super.admin.destek.index', ['status' => 'acik']) }}" class="action-item d-flex align-items-center">
                            <div class="action-icon" style="background: linear-gradient(135deg, #f39c12, #f8c471);">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Yeni Talepler</h6>
                                <small class="text-muted">Henüz yanıtlanmamış</small>
                            </div>
                            <span class="badge badge-warning">{{ $supportStats['new_tickets'] }}</span>
                        </a>

                        <a href="{{ route('super.admin.destek.index') }}" class="action-item d-flex align-items-center">
                            <div class="action-icon" style="background: linear-gradient(135deg, #3498db, #5dade2);">
                                <i class="fas fa-list-alt"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Tüm Talepler</h6>
                                <small class="text-muted">Geçmiş ve mevcut</small>
                            </div>
                            <span class="badge badge-success">{{ $supportStats['total_tickets'] }}</span>
                        </a>
                        @else
                        <div class="text-center py-4">
                            <i class="fas fa-info-circle text-muted fa-2x mb-2"></i>
                            <p class="text-muted">Destek talebi verileri yükleniyor...</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Raporlar ve Analiz -->
            <div class="col-lg-4 mb-3">
                <div class="card modern-card">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar text-success me-2"></i>Raporlar & Analiz
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="action-item d-flex align-items-center">
                            <div class="action-icon" style="background: linear-gradient(135deg, #27ae60, #58d68d);">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Firma Performansı</h6>
                                <small class="text-muted">Detaylı performans raporları</small>
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>

                        <div class="action-item d-flex align-items-center">
                            <div class="action-icon" style="background: linear-gradient(135deg, #3498db, #5dade2);">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Kullanıcı Aktivitesi</h6>
                                <small class="text-muted">Sistem kullanım istatistikleri</small>
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>

                        <div class="action-item d-flex align-items-center">
                            <div class="action-icon" style="background: linear-gradient(135deg, #8e44ad, #af7ac5);">
                                <i class="fas fa-download"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Sistem Raporları</h6>
                                <small class="text-muted">Excel/PDF formatında</small>
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Aktivite Grafiği
    const ctx = document.getElementById('activityChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [{
                    label: 'Yeni Kayıtlar',
                    data: {!! json_encode($chartData['new_registrations']) !!},
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Aktif Kullanıcılar',
                    data: {!! json_encode($chartData['active_users']) !!},
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                }
            }
        });
    }

    // Counter animasyonu
    const counters = document.querySelectorAll('.animate-count');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        let count = 0;
        const increment = target / 50;
        
        const timer = setInterval(() => {
            count += increment;
            if (count >= target) {
                counter.textContent = target;
                clearInterval(timer);
            } else {
                counter.textContent = Math.ceil(count);
            }
        }, 20);
    });
});
</script>

@endsection