{{-- statistics_menu.blade.php --}}
<div class="row pageDetail statistics-menu-wrapper">
    <div class="col-12">
        <div class="statistics-menu-container">
            <div class="statistics-menu">
                <a href="{{ route('statistics', $tenant_id) }}" 
                   class="statistic-btn btn-service {{ Request::routeIs('statistics') ? 'active' : '' }}">
                    <i class="fas fa-tools"></i>
                    <span>Servis İstatistikleri</span>
                </a>
                
                <a href="{{ route('technician.statistics', $tenant_id) }}" 
                   class="statistic-btn btn-technician {{ Request::routeIs('technician.statistics') ? 'active' : '' }}">
                    <i class="fas fa-user-cog"></i>
                    <span>Teknisyen İstatistikleri</span>
                </a>
                
                <a href="{{ route('operator.statistics', $tenant_id) }}" 
                   class="statistic-btn btn-operator {{ Request::routeIs('operator.statistics') ? 'active' : '' }}">
                    <i class="fas fa-headset"></i>
                    <span>Operatör İstatistikleri</span>
                </a>
                
                <a href="{{ route('cash.statistics', $tenant_id) }}" 
                   class="statistic-btn btn-cash {{ Request::routeIs('cash.statistics') ? 'active' : '' }}">
                    <i class="fas fa-money-bill"></i>
                    <span>Kasa İstatistikleri</span>
                </a>
                
                <a href="{{ route('state.statistics', $tenant_id) }}" 
                   class="statistic-btn btn-status {{ Request::routeIs('state.statistics') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Durum İstatistikleri</span>
                </a>
                
                <a href="{{ route('stage.statistics', $tenant_id) }}" 
                   class="statistic-btn btn-stage {{ Request::routeIs('stage.statistics') ? 'active' : '' }}">
                    <i class="fas fa-stream"></i>
                    <span>Aşama İstatistikleri</span>
                </a>
                
                <a href="{{ route('stock.statistics', $tenant_id) }}" 
                   class="statistic-btn btn-stock {{ Request::routeIs('stock.statistics') ? 'active' : '' }}">
                    <i class="fas fa-warehouse"></i>
                    <span>Depo İstatistikleri</span>
                </a>
                
                <a href="{{ route('ilce.statistics', $tenant_id) }}" 
                   class="statistic-btn btn-district {{ Request::routeIs('ilce.statistics') ? 'active' : '' }}">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>İlçe İstatistikleri</span>
                </a>
                
                <a href="{{ route('survey.statistics', $tenant_id) }}" 
                   class="statistic-btn btn-survey {{ Request::routeIs('survey.statistics') ? 'active' : '' }}">
                    <i class="fas fa-poll"></i>
                    <span>Anket İstatistikleri</span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Statistics Menu Responsive Styles */
.statistics-menu-wrapper {
    margin-bottom: 1.5rem;
}

.statistics-menu-container {
    width: 100%;
}

.statistics-menu {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.75rem;
    width: 100%;
}

.statistic-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem 1rem;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    white-space: nowrap;
    min-height: 48px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.statistic-btn i {
    margin-right: 0.5rem;
    font-size: 1rem;
    flex-shrink: 0;
}

.statistic-btn span {
    flex: 1;
}

.statistic-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    text-decoration: none;
}

.statistic-btn.active {
    box-shadow: 0 0 0 2px rgba(255,255,255,0.5), 0 4px 12px rgba(0,0,0,0.2);
    transform: translateY(-1px);
}

/* Color scheme for buttons */
.btn-service { background-color: #343a40; color: white; }
.btn-service:hover, .btn-service.active { background-color: #23272b; color: white; }

.btn-technician { background-color: #28a745; color: white; }
.btn-technician:hover, .btn-technician.active { background-color: #1e7e34; color: white; }

.btn-operator { background-color: #17a2b8; color: white; }
.btn-operator:hover, .btn-operator.active { background-color: #117a8b; color: white; }

.btn-cash { background-color: #dc3545; color: white; }
.btn-cash:hover, .btn-cash.active { background-color: #c82333; color: white; }

.btn-status { background-color: #8e44ad; color: white; }
.btn-status:hover, .btn-status.active { background-color: #7d3c98; color: white; }

.btn-stage { background-color: #ffc107; color: #212529; }
.btn-stage:hover, .btn-stage.active { background-color: #e0a800; color: #212529; }

.btn-stock { background-color: #6e4c1e; color: white; }
.btn-stock:hover, .btn-stock.active { background-color: #5a3e19; color: white; }

.btn-district { background-color: #1c3aa9; color: white; }
.btn-district:hover, .btn-district.active { background-color: #162d85; color: white; }

.btn-survey { background-color: #4e6e3f; color: white; }
.btn-survey:hover, .btn-survey.active { background-color: #3d5632; color: white; }

/* Responsive breakpoints */
@media (max-width: 1400px) {
    .statistics-menu {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }
}

@media (max-width: 1200px) {
    .statistics-menu {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 992px) {
    .statistics-menu {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }
    
    .statistic-btn {
        font-size: 0.85rem;
        padding: 0.6rem 0.8rem;
    }
}

@media (max-width: 768px) {
    .statistics-menu {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .statistic-btn {
        justify-content: flex-start;
        padding: 0.75rem;
    }
}

@media (max-width: 576px) {
    .statistic-btn {
        font-size: 0.8rem;
        padding: 0.6rem;
    }
    
    .statistic-btn i {
        margin-right: 0.4rem;
        font-size: 0.9rem;
    }
}

/* Loading animation */
.statistic-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255,255,255,0.2),
        transparent
    );
    transition: left 0.5s;
}

.statistic-btn:hover::before {
    left: 100%;
}
</style>