@extends('frontend.secure.user_master')
@section('user')

<style>
.integration-marketplace {
    background: #f6f7f7;
    min-height: 100vh;
    padding: 20px 0;
}

.marketplace-header {
    background: white;
    padding: 30px 0;
    margin-bottom: 30px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.marketplace-title {
    font-size: 32px;
    font-weight: 600;
    color: #1e1e1e;
    margin-bottom: 10px;
}

.marketplace-subtitle {
    color: #757575;
    font-size: 16px;
}

.search-box {
    max-width: 600px;
    margin: 20px auto 0;
}

.search-box input {
    height: 50px;
    font-size: 16px;
    border-radius: 4px;
    border: 1px solid #dcdcde;
}

.category-tabs {
    background: white;
    padding: 15px 0;
    margin-bottom: 20px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.category-tabs .nav-pills {
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
}

.category-tabs .nav-link {
    color: #50575e;
    padding: 10px 20px;
    border-radius: 4px;
    font-weight: 500;
    transition: all 0.2s;
}

.category-tabs .nav-link:hover {
    background: #f6f7f7;
}

.category-tabs .nav-link.active {
    background: #2271b1;
    color: white;
}

.integration-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.integration-card {
    background: white;
    border: 1px solid #dcdcde;
    border-radius: 4px;
    padding: 20px;
    transition: all 0.2s;
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.integration-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.integration-logo {
    width: 80px;
    height: 80px;
    object-fit: contain;
    border: 1px solid #dcdcde;
    border-radius: 4px;
    padding: 10px;
    margin: 0 auto 15px;
    background: white;
    display: block;
}

.integration-name {
    font-size: 18px;
    font-weight: 600;
    color: #1e1e1e;
    margin-bottom: 8px;
    text-align: center;
}

.integration-description {
    font-size: 14px;
    color: #50575e;
    margin-bottom: 15px;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex-grow: 1;
}

.integration-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #f0f0f1;
    margin-top: auto;
}

.integration-price {
    font-size: 16px;
    font-weight: 600;
    color: #2271b1;
}

.integration-price.free {
    color: #008a20;
}

.integration-category {
    display: inline-block;
    padding: 4px 10px;
    background: #f6f7f7;
    color: #50575e;
    font-size: 12px;
    border-radius: 3px;
    font-weight: 500;
}

.active-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #008a20;
    color: white;
    padding: 5px 12px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
}

.btn-activate {
    background: #2271b1;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 3px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-activate:hover {
    background: #135e96;
    color: white;
}

.btn-deactivate {
    background: #dc3545;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 3px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-deactivate:hover {
    background: #c82333;
    color: white;
}

.btn-detail {
    background: white;
    color: #2271b1;
    border: 1px solid #2271b1;
    padding: 8px 16px;
    border-radius: 3px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-detail:hover {
    background: #2271b1;
    color: white;
}

.no-results {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 4px;
}

.no-results h4 {
    color: #1e1e1e;
    margin-bottom: 10px;
}

.no-results p {
    color: #757575;
}

.action-buttons {
    margin-top: 15px;
    display: flex;
    gap: 10px;
}

.action-buttons a {
    flex: 1;
    text-align: center;
}

@media (max-width: 768px) {
    .integration-grid {
        grid-template-columns: 1fr;
    }
    
    .marketplace-title {
        font-size: 24px;
    }
    
    .category-tabs .nav-pills {
        justify-content: flex-start;
        overflow-x: auto;
        flex-wrap: nowrap;
        padding: 0 15px;
    }
}
</style>

<div class="integration-marketplace">
    <div class="marketplace-header">
        <div class="container">
            <div class="text-center">
                <h1 class="marketplace-title">Entegrasyon Mağazası</h1>
                <p class="marketplace-subtitle">{{ $tenant->firma_adi }} için mevcut entegrasyonları keşfedin ve işletmenizi güçlendirin</p>
                
                <div class="search-box">
                    <form action="{{ route('tenant.integrations.marketplace', $tenant->id) }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Entegrasyon ara..." value="{{ request('search') }}">
                            <input type="hidden" name="category" value="{{ request('category') }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Ara
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="category-tabs">
        <div class="container">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link {{ !request('category') || request('category') == 'all' ? 'active' : '' }}" 
                       href="{{ route('tenant.integrations.marketplace', $tenant->id) }}">
                        <i class="fas fa-th"></i> Tümü
                    </a>
                </li>
                @foreach($categories as $key => $name)
                    @if($key != 'all')
                    <li class="nav-item">
                        <a class="nav-link {{ request('category') == $key ? 'active' : '' }}" 
                           href="{{ route('tenant.integrations.marketplace', ['category' => $key, 'search' => request('search')]) }}">
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
                    </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">{{ $integrations->count() }} Entegrasyon Bulundu</h5>
            <a href="{{ route('tenant.integrations.my') }}" class="btn btn-outline-primary">
                <i class="fas fa-check-circle"></i> Aktif Entegrasyonlarım (0)
            </a>
        </div>

        @if($integrations->count() > 0)
        <div class="integration-grid">
            @foreach($integrations as $integration)
            <div class="integration-card">
                
                
                @if($integration->logo)
                <img src="{{ asset('frontend/' . $integration->logo) }}" alt="{{ $integration->name }}" class="integration-logo">
                @else
                <div class="integration-logo d-flex align-items-center justify-content-center">
                    <i class="fas fa-puzzle-piece fa-3x text-muted"></i>
                </div>
                @endif

                <h3 class="integration-name">{{ $integration->name }}</h3>
                <p class="integration-description">{{ $integration->description ?? 'Bu entegrasyon için açıklama bulunmuyor.' }}</p>

                <div class="integration-footer">
                    <span class="integration-category">
                        {{ $categories[$integration->category] ?? $integration->category }}
                    </span>
                    @if($integration->price > 0)
                    <span class="integration-price">{{ number_format($integration->price, 2) }} ₺/ay</span>
                    @else
                    <span class="integration-price free"><i class="fas fa-gift"></i> Ücretsiz</span>
                    @endif
                </div>

                <div class="action-buttons">
                    
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="no-results">
            <i class="fas fa-search fa-4x text-muted mb-3"></i>
            <h4>Sonuç Bulunamadı</h4>
            <p>Aradığınız kriterlere uygun entegrasyon bulunamadı.</p>
            <a href="{{ route('tenant.integrations.marketplace', $tenant->id) }}" class="btn btn-primary mt-3">
                <i class="fas fa-redo"></i> Tüm Entegrasyonları Göster
            </a>
        </div>
        @endif
    </div>
</div>

@endsection