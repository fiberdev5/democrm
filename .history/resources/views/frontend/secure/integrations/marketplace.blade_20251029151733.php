@extends('frontend.secure.user_master')
@section('user')

<div class="page-content" id="passwords">
    <div class="container-fluid staff-header-top">
      <div class="row pageDetail">
        <div class="col-12">
<div class="integration-marketplace">
    <div class="marketplace-header">
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
                           href="{{ route('tenant.integrations.marketplace', ['tenant_id' => $tenant->id, 'category' => $key, 'search' => request('search')]) }}">
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
            <a href="" class="btn btn-outline-primary">
                <i class="fas fa-check-circle"></i> Aktif Entegrasyonlarım (0)
            </a>
        </div>

        @if($integrations->count() > 0)
        <div class="integration-grid">
            @foreach($integrations as $integration)
            <div class="integration-card">
                
                
                @if($integration->logo)
                <img src="{{ asset($integration->logo) }}" alt="{{ $integration->name }}" class="integration-logo">
                @else
                <div class="integration-logo d-flex align-items-center justify-content-center">
                    <i class="fas fa-puzzle-piece fa-3x text-muted"></i>
                </div>
                @endif

                <h3 class="integration-name">{{ $integration->name }}</h3>


<p class="integration-description">
    {{ Illuminate\Support\Str::limit($integration->explanation ?? 'Bu entegrasyon için açıklama bulunmuyor.', 100, '...') }}
</p>
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
</div>
</div>
</div>
</div>

@endsection