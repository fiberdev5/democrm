@extends('frontend.secure.user_master')
@section('user')

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
                                <img src="{{ asset($integration->logo) }}" alt="{{ $integration->name }}" class="integration-logo">
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