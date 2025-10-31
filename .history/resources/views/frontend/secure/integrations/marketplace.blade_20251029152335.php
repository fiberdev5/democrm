@extends('frontend.secure.user_master')
@section('user')

<style>
.integration-marketplace {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 0;
}

.marketplace-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px 0 80px;
    color: white;
    margin-bottom: -60px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.marketplace-title {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 8px;
    color: white;
}

.marketplace-subtitle {
    font-size: 15px;
    opacity: 0.95;
    color: rgba(255,255,255,0.9);
}

.search-filter-container {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-top: 30px;
}

.search-wrapper {
    position: relative;
    flex: 1;
    max-width: 400px;
}

.search-input {
    width: 100%;
    height: 48px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 0 50px 0 20px;
    font-size: 15px;
    transition: all 0.3s;
}

.search-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    outline: none;
}

.search-button {
    position: absolute;
    right: 5px;
    top: 5px;
    height: 38px;
    width: 38px;
    border: none;
    background: #667eea;
    color: white;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.search-button:hover {
    background: #5568d3;
    transform: scale(1.05);
}

.category-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    flex: 1;
}

.category-btn {
    padding: 10px 20px;
    border: 2px solid #e9ecef;
    background: white;
    color: #495057;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.category-btn:hover {
    border-color: #667eea;
    color: #667eea;
    background: #f8f9ff;
    transform: translateY(-2px);
}

.category-btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: transparent;
}

.stats-bar {
    background: white;
    border-radius: 10px;
    padding: 15px 20px;
    margin: 20px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.stats-count {
    font-size: 15px;
    color: #495057;
    font-weight: 500;
}

.stats-count strong {
    color: #667eea;
    font-size: 18px;
}

.my-integrations-btn {
    padding: 10px 20px;
    background: #667eea;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.my-integrations-btn:hover {
    background: #5568d3;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.integration-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

.integration-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    transition: all 0.3s;
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;
    border: 1px solid #e9ecef;
}

.integration-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.12);
    border-color: #667eea;
}

.integration-logo {
    width: 70px;
    height: 70px;
    object-fit: contain;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 12px;
    margin: 0 auto 20px;
    background: #f8f9fa;
    display: block;
}

.integration-name {
    font-size: 18px;
    font-weight: 700;
    color: #212529;
    margin-bottom: 12px;
    text-align: center;
}

.integration-description {
    font-size: 14px;
    color: #6c757d;
    margin-bottom: 20px;
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex-grow: 1;
    text-align: center;
}

.integration-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 16px;
    margin-top: auto;
    border-top: 1px solid #f1f3f5;
}

.integration-price {
    font-size: 16px;
    font-weight: 700;
    color: #667eea;
}

.integration-price.free {
    color: #28a745;
}

.integration-category {
    display: inline-block;
    padding: 6px 12px;
    background: linear-gradient(135deg, #f8f9ff 0%, #f1f3ff 100%);
    color: #667eea;
    font-size: 12px;
    border-radius: 6px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.active-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
}

.action-buttons {
    margin-top: 16px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.btn-activate {
    grid-column: 1 / -1;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 12px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
    text-decoration: none;
    display: inline-block;
}

.btn-activate:hover {
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
}

.btn-deactivate {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    border: none;
    padding: 12px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
    text-decoration: none;
    display: inline-block;
}

.btn-deactivate:hover {
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(220, 53, 69, 0.4);
}

.btn-detail {
    background: white;
    color: #667eea;
    border: 2px solid #667eea;
    padding: 12px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
    text-align: center;
    text-decoration: none;
    display: inline-block;
}

.btn-detail:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
}

.no-results {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.no-results i {
    color: #dee2e6;
    margin-bottom: 20px;
}

.no-results h4 {
    color: #212529;
    margin-bottom: 12px;
    font-weight: 600;
}

.no-results p {
    color: #6c757d;
    margin-bottom: 24px;
}

@media (max-width: 991px) {
    .search-filter-container {
        flex-direction: column;
    }
    
    .search-wrapper {
        max-width: 100%;
        margin-bottom: 15px;
    }
    
    .category-buttons {
        justify-content: center;
    }
    
    .integration-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 16px;
    }
}

@media (max-width: 576px) {
    .marketplace-header {
        padding: 30px 0 60px;
    }
    
    .marketplace-title {
        font-size: 22px;
    }
    
    .stats-bar {
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }
    
    .integration-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="page-content" id="passwords">
    <div class="container-fluid">
        <div class="row pageDetail">
            <div class="col-12">
                <div class="integration-marketplace">
                    <!-- Header -->
                    <div class="marketplace-header">
                        <div class="container">
                            <h1 class="marketplace-title">
                                <i class="fas fa-store"></i> Entegrasyon Mağazası
                            </h1>
                            <p class="marketplace-subtitle">{{ $tenant->firma_adi }} için mevcut entegrasyonları keşfedin</p>
                        </div>
                    </div>

                    <!-- Arama ve Filtreler -->
                    <div class="container">
                        <div class="search-filter-container d-flex align-items-center gap-3">
                            <!-- Arama -->
                            <div class="search-wrapper">
                                <form action="{{ route('tenant.integrations.marketplace', $tenant->id) }}" method="GET">
                                    <input type="hidden" name="category" value="{{ request('category') }}">
                                    <input type="text" 
                                           name="search" 
                                           class="search-input" 
                                           placeholder="Entegrasyon ara..." 
                                           value="{{ request('search') }}">
                                    <button type="submit" class="search-button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Kategori Butonları -->
                            <div class="category-buttons">
                                <a href="{{ route('tenant.integrations.marketplace', $tenant->id) }}" 
                                   class="category-btn {{ !request('category') || request('category') == 'all' ? 'active' : '' }}">
                                    <i class="fas fa-th"></i> Tümü
                                </a>
                                @foreach($categories as $key => $name)
                                    @if($key != 'all')
                                    <a href="{{ route('tenant.integrations.marketplace', ['tenant_id' => $tenant->id, 'category' => $key, 'search' => request('search')]) }}"
                                       class="category-btn {{ request('category') == $key ? 'active' : '' }}">
                                        @if($key == 'invoice')
                                            <i class="fas fa-file-invoice"></i>
                                        @elseif($key == 'sms')
                                            <i class="fas fa-sms"></i>
                                        @elseif($key == 'accounting')
                                            <i class="fas fa-calculator"></i>
                                        @else
                                            <i class="fas fa-puzzle-piece"></i>
                                        @endif
                                        {{ $name }}
                                    </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- İstatistikler -->
                        <div class="stats-bar">
                            <div class="stats-count">
                                <strong>{{ $integrations->count() }}</strong> Entegrasyon Bulundu
                            </div>
                            <a href="" class="my-integrations-btn">
                                <i class="fas fa-check-circle"></i>
                                Aktif Entegrasyonlarım (0)
                            </a>
                        </div>

                        <!-- Entegrasyonlar Grid -->
                        @if($integrations->count() > 0)
                        <div class="integration-grid">
                            @foreach($integrations as $integration)
                            <div class="integration-card">
                              
                                
                                @if($integration->logo)
                                <img src="{{ asset('frontend/' . $integration->logo) }}" alt="{{ $integration->name }}" class="integration-logo">
                                @else
                                <div class="integration-logo d-flex align-items-center justify-content-center">
                                    <i class="fas fa-puzzle-piece fa-2x text-muted"></i>
                                </div>
                                @endif

                                <h3 class="integration-name">{{ $integration->name }}</h3>

                                <p class="integration-description">
                                    {!! Illuminate\Support\Str::limit(strip_tags($integration->explanation ?? 'Bu entegrasyon için açıklama bulunmuyor.'), 100, '...') !!}
                                </p>

                                <div class="integration-footer">
                                    <span class="integration-category">
                                        {{ $categories[$integration->category] ?? $integration->category }}
                                    </span>
                                    @if($integration->price > 0)
                                    <span class="integration-price">{{ number_format($integration->price, 2) }} ₺</span>
                                    @else
                                    <span class="integration-price free">
                                        <i class="fas fa-gift"></i> Ücretsiz
                                    </span>
                                    @endif
                                </div>

                                <div class="action-buttons">
                                    
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="no-results">
                            <i class="fas fa-search fa-5x"></i>
                            <h4>Sonuç Bulunamadı</h4>
                            <p>Aradığınız kriterlere uygun entegrasyon bulunamadı.</p>
                            <a href="{{ route('tenant.integrations.marketplace', $tenant->id) }}" class="btn-activate" style="display: inline-block; width: auto; padding: 12px 32px;">
                                <i class="fas fa-redo"></i> Tüm Entegrasyonları Göster
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection