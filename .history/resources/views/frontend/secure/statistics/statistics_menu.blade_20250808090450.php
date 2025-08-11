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

.statistics-menu {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 0.75rem;
    width: 100%;
}


</style>