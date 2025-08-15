{{-- CSRF Token Meta Tag --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="page-title" style="height:40px;"></div>

<div class="dashboard-container">
    <!-- Hata mesajı -->
    <div id="errorMessage" class="error-message"></div>
    
    <!-- Üst İstatistik Kartları -->
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="#" class="stat-card blue" id="totalServicesCard">
                <i class="fas fa-tools stat-icon"></i>
                <div class="stat-value" id="totalServices">-</div>
                <div class="stat-label">Toplam Servis Sayısı</div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="{{ route('customers', ['tenant_id' => request()->route('tenant_id')]) }}" class="stat-card green" id="totalCustomersCard">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-value" id="totalCustomers">-</div>
                <div class="stat-label">Müşteri Sayısı</div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="{{ route('staffs', ['tenant_id' => request()->route('tenant_id')]) }}" class="stat-card red" id="totalPersonnelCard">
                <i class="fas fa-user-tie stat-icon"></i>
                <div class="stat-value" id="totalPersonnel">-</div>
                <div class="stat-label">Personel Sayısı</div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
             <a href="#" onclick="window.location.href = buildCashUrl('total')" 
               class="stat-card teal" id="totalCashCard">
                <i class="fas fa-lira-sign stat-icon"></i>
                <div class="stat-value" id="totalCash">0,00 TL</div>
                <div class="stat-label">Kasa</div>
            </a>
        </div>
    </div>

    <!-- Servis Özeti -->
    <div class="service-summary">
        <h5><i class="fas fa-chart-bar"></i> Servis Sayıları</h5>
        <div class="row">
            <div class="col-md-4">
                <a href="#" class="service-item today" id="todayServicesCard">
                    <div class="service-count" id="todayServices">-</div>
                    <div>BUGÜN Alınan Servis Sayısı</div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="#" class="service-item yesterday" id="yesterdayServicesCard">
                    <div class="service-count" id="yesterdayServices">-</div>
                    <div>DÜN Alınan Servis Sayısı</div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="#" class="service-item previous" id="previousServicesCard">
                    <div class="service-count" id="previousServices">-</div>
                    <div>ÖNCEKİ GÜN Alınan Servis Sayısı</div>
                </a>
            </div>
        </div>
    </div>

    <!-- Grafikler -->
    <div class="row">
        <div class="col-lg-6">
            <div class="chart-container">
                <div class="chart-header">
                    <h5><i class="fas fa-chart-line"></i> Günlük Servis Trendi</h5>
                    <div class="time-filter">
                        <button class="filter-btn active" data-period="7" data-chart="daily">7 Gün</button>
                        <button class="filter-btn" data-period="15" data-chart="daily">15 Gün</button>
                        <button class="filter-btn" data-period="30" data-chart="daily">30 Gün</button>
                    </div>
                </div>
                <div class="loading"><i class="fas fa-spinner fa-spin"></i> Yükleniyor...</div>
                <div class="chart-canvas">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="chart-container">
                <div class="chart-header">
                    <h5><i class="fas fa-clock"></i> Saatlik Servis Dağılımı</h5>
                    <div class="time-filter">
                        <button class="filter-btn active" data-period="7" data-chart="hourly">7 Gün</button>
                        <button class="filter-btn" data-period="15" data-chart="hourly">15 Gün</button>
                        <button class="filter-btn" data-period="30" data-chart="hourly">30 Gün</button>
                    </div>
                </div>
                <div class="loading"><i class="fas fa-spinner fa-spin"></i> Yükleniyor...</div>
                <div class="chart-canvas">
                    <canvas id="hourlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>