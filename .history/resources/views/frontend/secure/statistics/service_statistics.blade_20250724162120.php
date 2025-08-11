@extends('frontend.secure.user_master')

@section('user')
<div class="page-content servis-istatistik">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])

        <!-- Modern Header Card -->
        <div class="card shadow-sm mb-4 istatisti-card">
            <div class="card-header bg-gradient-primary text-white">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="mb-0"><i class="fas fa-chart-line me-2"></i>Servis İstatistikleri</h4>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filterPanel">
                            <i class="fas fa-filter me-1"></i>Filtrele
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filter Panel -->
            <div class="collapse" id="filterPanel">
                <div class="card-body bg-light">
                    <form id="istatistikAra" action="{{ route('statistics', $tenant_id) }}" method="get">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Personel</label>
                                <select name="personeller" class="form-select">
                                    <option value="0">Tüm Personeller</option>
                                    @foreach($personeller as $personel)
                                        <option value="{{ $personel->user_id }}" 
                                            {{ (isset($request) && $request->personeller == $personel->user_id) ? 'selected' : '' }}>
                                            {{ $personel->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Servis Kaynağı</label>
                                <select name="servisKaynak" class="form-select">
                                    <option value="0">Tüm Kaynaklar</option>
                                    @foreach($servisKaynaklari as $kaynak)
                                        <option value="{{ $kaynak->id }}" 
                                            {{ (isset($request) && $request->servisKaynak == $kaynak->id) ? 'selected' : '' }}>
                                            {{ $kaynak->kaynak }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Başlangıç Tarihi</label>
                                <input type="date" name="tarih1" class="form-control"
                                    value="{{ (isset($request->tarih1) ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->tarih1)->format('Y-m-d') : \Carbon\Carbon::now()->subDays(29)->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Bitiş Tarihi</label>
                                <input type="date" name="tarih2" class="form-control"
                                    value="{{ (isset($request->tarih2) ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->tarih2)->format('Y-m-d') : \Carbon\Carbon::now()->format('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="btn-group flex-wrap" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm quick-date" data-days="1">Bugün</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm quick-date" data-days="7">Son 7 Gün</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm quick-date" data-days="30">Son 30 Gün</button>
                                    <button type="button" class="btn btn-outline-primary btn-sm quick-date" data-days="365">Son 1 Yıl</button>
                                </div>
                                <button type="submit" name="servisSayListele" class="btn btn-primary ms-3">
                                    <i class="fas fa-search me-1"></i>Ara
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if(isset($statistics))
            <!-- Filtered Results -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="card-title mb-0">Arama Sonuçları</h5>
                                <span class="badge bg-primary fs-6 istatistik-badge">Toplam: {{ $statistics['toplam'] }}</span>
                            </div>
                            
                            <div class="row g-4">
                                <!-- Markalar -->
                                <div class="col-lg-3 col-md-6">
                                    <div class="card h-100 border-0 bg-light">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0"><i class="fas fa-tags me-1"></i>Markalar</h6>
                                        </div>
                                        <div class="card-body p-2">
                                            @forelse($statistics['markalar'] as $marka)
                                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                                    <span class="text-truncate">{{ $marka->marka }}</span>
                                                    <span class="badge bg-info">{{ $marka->sayi }}</span>
                                                </div>
                                            @empty
                                                <div class="text-muted text-center">No data</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <!-- Türler -->
                                <div class="col-lg-3 col-md-6">
                                    <div class="card h-100 border-0 bg-light">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0"><i class="fas fa-cube me-1"></i>Türler</h6>
                                        </div>
                                        <div class="card-body p-2">
                                            @forelse($statistics['turler'] as $tur)
                                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                                    <span class="text-truncate">{{ $tur->cihaz }}</span>
                                                    <span class="badge bg-success">{{ $tur->sayi }}</span>
                                                </div>
                                            @empty
                                                <div class="text-muted text-center">No data</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <!-- Kaynaklar -->
                                <div class="col-lg-3 col-md-6">
                                    <div class="card h-100 border-0 bg-light">
                                        <div class="card-header bg-warning text-dark">
                                            <h6 class="mb-0"><i class="fas fa-source-branch me-1"></i>Kaynaklar</h6>
                                        </div>
                                        <div class="card-body p-2">
                                            @forelse($statistics['kaynaklar'] as $kaynak)
                                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                                    <span class="text-truncate">{{ $kaynak->kaynak }}</span>
                                                    <span class="badge bg-warning text-dark">{{ $kaynak->sayi }}</span>
                                                </div>
                                            @empty
                                                <div class="text-muted text-center">No data</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <!-- Operatörler -->
                                <div class="col-lg-3 col-md-6">
                                    <div class="card h-100 border-0 bg-light">
                                        <div class="card-header bg-danger text-white">
                                            <h6 class="mb-0"><i class="fas fa-users me-1"></i>Operatörler</h6>
                                        </div>
                                        <div class="card-body p-2">
                                            @forelse($statistics['operatorler'] as $operator)
                                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                                    <span class="text-truncate">{{ $operator->name }}</span>
                                                    <span class="badge bg-danger">{{ $operator->sayi }}</span>
                                                </div>
                                            @empty
                                                <div class="text-muted text-center">No data</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Default Period Statistics -->
            <div class="accordion" id="periodAccordion">
                @foreach($periodStats as $key => $period)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ $key }}">
                            <button class="accordion-button istatistik-button {{ $key !== 'bugun' ? 'collapsed' : '' }}" type="button" 
                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $key }}">
                                <i class="fas fa-calendar-day me-2"></i>
                                {{ $period['label'] }}: <span class="badge bg-primary ms-2">{{ $period['toplam'] }}</span>
                            </button>
                        </h2>
                        <div id="collapse{{ $key }}" class="accordion-collapse collapse {{ $key === 'bugun' ? 'show' : '' }}" 
                             data-bs-parent="#periodAccordion">
                            <div class="accordion-body">
                                <div class="row g-4">
                                    <!-- Markalar -->
                                    <div class="col-lg-3 col-md-6">
                                        <div class="card border-info">
                                            <div class="card-header bg-info text-white text-center">
                                                <small><i class="fas fa-tags me-1"></i>Markalar</small>
                                            </div>
                                            <div class="card-body p-2">
                                                @forelse($period['markalar'] as $marka)
                                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                                        <small class="text-truncate">{{ $marka->marka }}</small>
                                                        <span class="badge bg-info">{{ $marka->sayi }}</span>
                                                    </div>
                                                @empty
                                                    <small class="text-muted">No data</small>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Türler -->
                                    <div class="col-lg-3 col-md-6">
                                        <div class="card border-success">
                                            <div class="card-header bg-success text-white text-center">
                                                <small><i class="fas fa-cube me-1"></i>Türler</small>
                                            </div>
                                            <div class="card-body p-2">
                                                @forelse($period['turler'] as $tur)
                                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                                        <small class="text-truncate">{{ $tur->cihaz }}</small>
                                                        <span class="badge bg-success">{{ $tur->sayi }}</span>
                                                    </div>
                                                @empty
                                                    <small class="text-muted">No data</small>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kaynaklar -->
                                    <div class="col-lg-3 col-md-6">
                                        <div class="card border-warning">
                                            <div class="card-header bg-warning text-dark text-center">
                                                <small><i class="fas fa-source-branch me-1"></i>Kaynaklar</small>
                                            </div>
                                            <div class="card-body p-2">
                                                @forelse($period['kaynaklar'] as $kaynak)
                                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                                        <small class="text-truncate">{{ $kaynak->kaynak }}</small>
                                                        <span class="badge bg-warning text-dark">{{ $kaynak->sayi }}</span>
                                                    </div>
                                                @empty
                                                    <small class="text-muted">No data</small>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Operatörler -->
                                    <div class="col-lg-3 col-md-6">
                                        <div class="card border-danger">
                                            <div class="card-header bg-danger text-white text-center">
                                                <small><i class="fas fa-users me-1"></i>Operatörler</small>
                                            </div>
                                            <div class="card-body p-2">
                                                @forelse($period['operatorler'] as $operator)
                                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                                        <small class="text-truncate">{{ $operator->name }}</small>
                                                        <span class="badge bg-danger">{{ $operator->sayi }}</span>
                                                    </div>
                                                @empty
                                                    <small class="text-muted">No data</small>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Charts Section -->
        <div class="row mt-4">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Servis Sayıları Grafiği</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="serviceChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Saatlik Dağılım</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="hourlyChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quick date buttons
    document.querySelectorAll('.quick-date').forEach(button => {
        button.addEventListener('click', function() {
            const days = parseInt(this.dataset.days);
            const endDate = new Date();
            const startDate = new Date();

            if (days === 1) { // Bugün için
                startDate.setDate(endDate.getDate());
            } else { // Diğer durumlarda N gün öncesi
                startDate.setDate(endDate.getDate() - days + 1);
            }

            document.querySelector('input[name="tarih1"]').value = startDate.toISOString().split('T')[0];
            document.querySelector('input[name="tarih2"]').value = endDate.toISOString().split('T')[0];
            
            // Tarihler değiştiğinde otomatik filtreleme yapabilirsiniz, opsiyonel
            // document.getElementById('istatistikAra').submit();
        });
    });

    // Service Chart
    const rawChartData = @json($statistics['chartData'] ?? $chartData ?? []);
    if (rawChartData && rawChartData.length > 0) {
        const ctx = document.getElementById('serviceChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: rawChartData.map(item => item.date),
                datasets: [{
                    label: 'Servis Sayısı',
                    data: rawChartData.map(item => item.count),
                    borderColor: '#667eea',
                    backgroundColor: '#667eea20',
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
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f0f0f0'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    } else {
        console.log('Servis Sayıları Grafiği için veri bulunamadı.');
        document.getElementById('serviceChart').closest('.card-body').innerHTML = '<p class="text-muted text-center">Grafik verisi bulunamadı.</p>';
    }


    // Hourly Chart
    const rawHourlyData = @json($statistics['hourlyData'] ?? $hourlyData ?? []);
    if (rawHourlyData && rawHourlyData.length > 0) {
        const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
        new Chart(hourlyCtx, {
            type: 'bar',
            data: {
                labels: rawHourlyData.map(item => item.hour),
                datasets: [{
                    label: 'Servis Sayısı',
                    data: rawHourlyData.map(item => item.count),
                    backgroundColor: '#764ba2',
                    borderColor: '#764ba2',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f0f0f0'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    } else {
        console.log('Saatlik Dağılım Grafiği için veri bulunamadı.');
        document.getElementById('hourlyChart').closest('.card-body').innerHTML = '<p class="text-muted text-center">Grafik verisi bulunamadı.</p>';
    }
});
</script>
@endsection