@extends('frontend.secure.user_master')
@section('user')

<style>
  .btn-gradient {
    background: linear-gradient(135deg, #fb923c 0%, #f9b233 100%);
    color: #fff;
    border: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(251, 146, 60, 0.3);
  }

  .btn-gradient:hover {
    opacity: 0.9;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(251, 146, 60, 0.4);
  }

  .pricing-card {
    border-radius: 1.2rem;
    transition: all 0.3s ease;
    border: 2px solid transparent;
  }

  .pricing-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    border-color: #f9b233;
  }

  .price-description ul {
    list-style: none;
    padding: 0;
    text-align: left;
    margin: 0 auto;
    display: inline-block;
  }

  .price-description ul li {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
  }

  .price-description ul li::before {
    content: '';
    display: inline-block;
    width: 1.25rem;
    height: 1.25rem;
    margin-right: 0.5rem;
    background-color: #027a48;
    -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor'%3E%3Cpath fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' clip-rule='evenodd' /%3E%3C/svg%3E") no-repeat center;
    mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor'%3E%3Cpath fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' clip-rule='evenodd' /%3E%3C/svg%3E") no-repeat center;
    background-size: contain;
  }

  .current-plan-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 25px;
    position: relative;
    overflow: hidden;
  }

  .current-plan-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    transform: translate(30px, -30px);
  }

  .storage-dashboard {
    background: #ffffff;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    border: 1px solid #e2e8f0;
  }

  .storage-progress {
    height: 12px;
    background: #f1f5f9;
    border-radius: 6px;
    overflow: hidden;
    margin: 15px 0;
    position: relative;
  }

  .storage-progress-bar {
    height: 100%;
    transition: width 0.8s ease;
    position: relative;
    overflow: hidden;
  }

  .storage-progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
  }

  @keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
  }

  .storage-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 20px;
  }

  .storage-stat {
    text-align: center;
    background: #f8fafc;
    padding: 15px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
  }

  .storage-stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 5px;
  }

  .storage-stat-label {
    font-size: 0.85rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .extra-storage-badge {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 13px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
  }

  .plan-feature-highlight {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 2px solid #f59e0b;
    border-radius: 12px;
    padding: 20px;
    margin: 20px 0;
  }

  .trial-countdown {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: #92400e;
    padding: 20px;
    border-radius: 15px;
    text-align: center;
    margin-bottom: 25px;
  }

  .trial-countdown .countdown-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: #dc2626;
  }

  .upgrade-incentive {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    border: 2px solid #3b82f6;
    border-radius: 15px;
    padding: 25px;
    margin: 20px 0;
  }

  .feature-comparison {
    background: #f8fafc;
    border-radius: 12px;
    padding: 20px;
    margin: 15px 0;
  }

  .feature-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #e2e8f0;
  }

  .feature-item:last-child {
    border-bottom: none;
  }

  .benefit-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
    margin-bottom: 15px;
    transition: all 0.3s ease;
  }

  .benefit-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
  }

  .pricing-highlight {
    position: relative;
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    padding: 15px;
    border-radius: 12px;
    margin: 15px 0;
    border: 2px solid #f59e0b;
  }

  .save-badge {
    position: absolute;
    top: -10px;
    right: 15px;
    background: #dc2626;
    color: white;
    padding: 5px 15px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
  }

  .text-gray-900 { color: #212529; }
  .bg-bls-teal-50 { background-color: #cff3fa; }
  .text-bls-success-700 { color: #027a48; }
</style>

<script src="https://cdn.tailwindcss.com"></script>

<div class="page-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
            <div>
              <h4 class="mb-0">Abonelik Yönetimi</h4>
              <small>Planınızı yönetin, kullanımınızı takip edin</small>
            </div>
            <div class="d-flex gap-2">
              <a href="{{ route('storage.packages', $tenant->id) }}" class="btn btn-light btn-sm">
                <i class="fas fa-hdd me-1"></i>Ek Depolama
              </a>
            </div>
          </div>
          <div class="card-body p-4">

            {{-- Aktif Abonelik Durumu --}}
            @if($currentPlan)
              @php $storageInfo = $tenant->getStorageInfo(); @endphp
              
              <div class="current-plan-card">
                <div class="row align-items-center">
                  <div class="col-md-8">
                    <div class="d-flex align-items-center mb-3">
                      <i class="fas fa-crown fa-2x me-3"></i>
                      <div>
                        <h4 class="mb-1">{{ $currentPlan->name }} Planınız Aktif</h4>
                        <p class="mb-0 opacity-90">Premium deneyimin keyfini çıkarıyorsunuz</p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="d-flex align-items-center mb-2">
                          <i class="fas fa-money-bill-wave me-2"></i>
                          <span>{{ $currentPlan->getFormattedPrice() }} / {{ $currentPlan->getBillingCycleText() }}</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                          <i class="fas fa-users me-2"></i>
                          <span>{{ $currentPlan->limits['users'] == -1 ? 'Sınırsız' : $currentPlan->limits['users'] }} Kullanıcı</span>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="d-flex align-items-center mb-2">
                          <i class="fas fa-calendar-alt me-2"></i>
                          <span>{{ $tenant->subscription_ends_at?->format('d F Y') ?? 'Süresiz' }} bitiş</span>
                        </div>
                        @if($storageInfo['has_extra_storage'])
                          <div class="extra-storage-badge">
                            <i class="fas fa-plus-circle"></i>
                            +{{ $storageInfo['extra_storage_gb'] }} GB Ek Depolama
                          </div>
                        @endif
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4 text-end">
                    <div class="text-white-50 small">Son Ödeme Tarihi</div>
                    <div class="h5 mb-0">{{ $tenant->subscription_ends_at?->format('d.m.Y') ?? 'Süresiz' }}</div>
                  </div>
                </div>
              </div>

              {{-- Detaylı Storage Dashboard --}}
              <div class="storage-dashboard">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="mb-0">
                    <i class="fas fa-database text-primary me-2"></i>
                    Depolama Alanı Kullanımı
                  </h5>
                  @if($storageInfo['warning_threshold'])
                    <span class="badge bg-warning">Dikkat Gerekli</span>
                  @elseif($storageInfo['danger_threshold'])
                    <span class="badge bg-danger">Limit Doldu</span>
                  @else
                    <span class="badge bg-success">Normal Kullanım</span>
                  @endif
                </div>

                <div class="row mb-4">
                  <div class="col-md-8">
                    <div class="d-flex justify-content-between mb-2">
                      <span class="fw-medium">{{ $storageInfo['current_usage_formatted'] }} / {{ $storageInfo['limit_formatted'] }} kullanılıyor</span>
                      <span class="fw-bold fs-5 text-{{ $storageInfo['danger_threshold'] ? 'danger' : ($storageInfo['warning_threshold'] ? 'warning' : 'success') }}">
                        %{{ $storageInfo['usage_percentage'] }}
                      </span>
                    </div>
                    <div class="storage-progress">
                      <div class="storage-progress-bar bg-gradient-{{ $storageInfo['danger_threshold'] ? 'danger' : ($storageInfo['warning_threshold'] ? 'warning' : 'success') }}" 
                           style="width: {{ $storageInfo['usage_percentage'] }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                      <span>0 GB</span>
                      <span>{{ $storageInfo['limit_formatted'] }}</span>
                    </div>
                  </div>
                  <div class="col-md-4 text-center">
                    @if($storageInfo['danger_threshold'])
                      <div class="alert alert-danger p-2 mb-2">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div class="small">Alan doldu!</div>
                      </div>
                      <a href="{{ route('storage.packages', $tenant->id) }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-plus-circle me-1"></i>Acil Ek Alan
                      </a>
                    @elseif($storageInfo['warning_threshold'])
                      <div class="alert alert-warning p-2 mb-2">
                        <i class="fas fa-exclamation-circle"></i>
                        <div class="small">Alan azalıyor</div>
                      </div>
                      <a href="{{ route('storage.packages', $tenant->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-plus-circle me-1"></i>Ek Alan Satın Al
                      </a>
                    @else
                      <div class="alert alert-success p-2 mb-2">
                        <i class="fas fa-check-circle"></i>
                        <div class="small">Yeterli alan</div>
                      </div>
                    @endif
                  </div>
                </div>

                {{-- Storage İstatistikleri --}}
                <div class="storage-stats">
                  <div class="storage-stat">
                    <div class="storage-stat-value text-primary">{{ $storageInfo['current_usage_formatted'] }}</div>
                    <div class="storage-stat-label">Kullanılan</div>
                  </div>
                  <div class="storage-stat">
                    <div class="storage-stat-value text-success">{{ $storageInfo['remaining_formatted'] }}</div>
                    <div class="storage-stat-label">Kalan</div>
                  </div>
                  <div class="storage-stat">
                    <div class="storage-stat-value text-info">{{ $storageInfo['base_limit_gb'] }} GB</div>
                    <div class="storage-stat-label">Plan Limiti</div>
                  </div>
                  @if($storageInfo['has_extra_storage'])
                    <div class="storage-stat">
                      <div class="storage-stat-value text-purple">{{ $storageInfo['extra_storage_gb'] }} GB</div>
                      <div class="storage-stat-label">Ek Depolama</div>
                    </div>
                  @endif
                </div>
              </div>

              {{-- Plan Yükseltme Önerisi --}}
              @php $maxPlan = $plans->sortByDesc('price')->first(); @endphp
              @if($currentPlan->id !== $maxPlan->id)
                <div class="upgrade-incentive">
                  <div class="row align-items-center">
                    <div class="col-md-8">
                      <h6 class="text-primary mb-2">
                        <i class="fas fa-rocket me-2"></i>
                        Daha Güçlü Özelliklere Erişin
                      </h6>
                      <p class="mb-2">Planınızı yükselterek daha fazla kullanıcı, depolama alanı ve premium özelliklerden yararlanın.</p>
                      <div class="feature-comparison">
                        <div class="small fw-medium mb-2">Yükseltme ile kazanacaklarınız:</div>
                        <div class="feature-item">
                          <span>Kullanıcı Sayısı</span>
                          <span class="text-success fw-bold">{{ $maxPlan->limits['users'] == -1 ? 'Sınırsız' : $maxPlan->limits['users'] }} Kullanıcı</span>
                        </div>
                        <div class="feature-item">
                          <span>Depolama Alanı</span>
                          <span class="text-success fw-bold">{{ $maxPlan->limits['storage_gb'] ?? '10' }} GB</span>
                        </div>
                        <div class="feature-item">
                          <span>Premium Destek</span>
                          <span class="text-success fw-bold">7/24 Öncelikli</span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4 text-center">
                      <div class="pricing-highlight">
                        @php $savings = ($maxPlan->price - $currentPlan->price) * 0.2; @endphp
                        @if($savings > 0)
                          <div class="save-badge">%20 Tasarruf</div>
                        @endif
                        <div class="h4 text-primary mb-1">{{ $maxPlan->getFormattedPrice() }}</div>
                        <div class="small text-muted mb-3">{{ $maxPlan->getBillingCycleText() }}</div>
                      </div>
                      <a href="#plans" class="btn btn-gradient w-100">
                        <i class="fas fa-arrow-up me-2"></i>Planı Yükselt
                      </a>
                    </div>
                  </div>
                </div>
              @endif

            {{-- Trial Dönemi --}}
            @elseif($onTrial)
              <div class="trial-countdown">
                <div class="row align-items-center">
                  <div class="col-md-4 text-center">
                    <div class="countdown-number">{{ $remainingTrialDays }}</div>
                    <div class="fw-bold">GÜN KALDI</div>
                  </div>
                  <div class="col-md-8">
                    <h5 class="mb-2">
                      <i class="fas fa-clock me-2"></i>
                      Deneme Süreniz Devam Ediyor
                    </h5>
                    <p class="mb-3">{{ $remainingTrialDays }} gün içinde bir plan seçmeyi unutmayın. Tüm özelliklerden yararlanmaya devam edin!</p>
                    <div class="d-flex gap-2">
                      <a href="#plans" class="btn btn-warning">
                        <i class="fas fa-star me-1"></i>Plan Seç ve Tasarruf Et
                      </a>
                      <a href="{{ route('storage.packages', $tenant->id) }}" class="btn btn-outline-warning">
                        <i class="fas fa-hdd me-1"></i>Ek Depolama
                      </a>
                    </div>
                  </div>
                </div>
              </div>

              {{-- Trial Faydaları --}}
              <div class="row">
                <div class="col-md-4">
                  <div class="benefit-card text-center">
                    <i class="fas fa-users fa-2x text-primary mb-3"></i>
                    <h6>Çoklu Kullanıcı</h6>
                    <p class="small text-muted">Ekibinizle birlikte çalışın</p>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="benefit-card text-center">
                    <i class="fas fa-shield-alt fa-2x text-success mb-3"></i>
                    <h6>Güvenli Veriler</h6>
                    <p class="small text-muted">Verileriniz tamamen korunuyor</p>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="benefit-card text-center">
                    <i class="fas fa-headset fa-2x text-info mb-3"></i>
                    <h6>7/24 Destek</h6>
                    <p class="small text-muted">Her zaman yanınızdayız</p>
                  </div>
                </div>
              </div>

            {{-- Trial Bitmiş --}}
            @else
              <div class="alert alert-danger border-0 shadow-sm">
                <div class="row align-items-center">
                  <div class="col-md-8">
                    <h5 class="alert-heading mb-2">
                      <i class="fas fa-exclamation-triangle me-2"></i>
                      Deneme Süreniz Sona Erdi
                    </h5>
                    <p class="mb-2">Hizmetlerimizden yararlanmaya devam etmek için aşağıdaki planlardan birini seçin.</p>
                    <p class="mb-0 small">İlk ay %30 indirim fırsatını kaçırmayın!</p>
                  </div>
                  <div class="col-md-4 text-center">
                    <a href="#plans" class="btn btn-danger btn-lg pulse-animation">
                      <i class="fas fa-bolt me-2"></i>Hemen Plan Seç
                    </a>
                  </div>
                </div>
              </div>
            @endif

            {{-- Plan Seçenekleri --}}
            @if(!$currentPlan || $onTrial)
              <div id="plans">
                <div class="text-center mb-5 mt-4">
                  <h4 class="mb-2">
                    <i class="fas fa-star text-warning me-2"></i>
                    Size En Uygun Planı Seçin
                  </h4>
                  <p class="text-muted">Tüm planlar 14 gün ücretsiz deneme ile birlikte gelir</p>
                </div>
                
                <section class="py-4">
                  <div class="container">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:items-start">
                      @foreach($plans as $i => $plan)
                        @php 
                          $isPopular = ($i === 1);
                          $savings = $i > 0 ? (($plans[$i-1]->price * 1.5) - $plan->price) : 0;
                        @endphp

                        <div class="relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition transform hover:-translate-y-2 p-8 flex flex-col text-center pricing-card {{ $isPopular ? 'border-warning' : '' }}">
                          
                          @if($isPopular)
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-orange-500 to-[#f9b233] text-white text-xs font-semibold px-4 py-2 rounded-full shadow-lg">
                              <i class="fas fa-crown me-1"></i>En Popüler
                            </span>
                          @endif

                          @if($savings > 0)
                            <div class="save-badge">%{{ round(($savings / $plans[$i-1]->price) * 100) }} Tasarruf</div>
                          @endif

                          <div class="flex justify-center mb-6">
                            <div class="relative">
                              <i class="{{$plan->icon}} text-6xl text-[#f9b233]"></i>
                              @if($isPopular)
                                <div class="absolute -top-2 -right-2 bg-success text-white rounded-full" style="width:24px;height:24px;font-size:12px;display:flex;align-items:center;justify-content:center;">
                                  <i class="fas fa-check"></i>
                                </div>
                              @endif
                            </div>
                          </div>

                          <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $plan->name }}</h3>
                          <p class="text-gray-500 font-normal mb-4" style="font-size: 15px;">
                            @if($plan->name == 'Başlangıç')
                              Yeni başlayan işletmeler için ideal çözüm
                            @elseif($plan->name == 'Profesyonel')
                              Büyüyen işletmeler için kapsamlı özellikler
                            @else
                              Kurumsal seviye tam kontrol ve sınırsız erişim
                            @endif
                          </p>

                          <div class="pricing-highlight mb-4">
                            <div class="text-4xl font-extrabold text-gray-900 mb-1">
                              ₺ {{ number_format($plan->price) }}
                            </div>
                            <div class="text-base text-gray-500 font-normal">
                              / {{ $plan->getBillingCycleText() }}
                            </div>
                            @if($savings > 0)
                              <div class="small text-success mt-1">
                                <i class="fas fa-piggy-bank me-1"></i>Yıllık ₺{{ number_format($savings) }} tasarruf
                              </div>
                            @endif
                          </div>

                          {{-- Plan Özellikleri Özeti --}}
                          <div class="feature-comparison mb-4">
                            <div class="feature-item">
                              <div class="d-flex align-items-center">
                                <i class="fas fa-users text-primary me-2"></i>
                                <span>Kullanıcı</span>
                              </div>
                              <span class="fw-bold text-success">
                                {{ $plan->limits['users'] == -1 ? 'Sınırsız' : $plan->limits['users'] }}
                              </span>
                            </div>
                            <div class="feature-item">
                              <div class="d-flex align-items-center">
                                <i class="fas fa-hdd text-info me-2"></i>
                                <span>Depolama</span>
                              </div>
                              <span class="fw-bold text-success">{{ $plan->limits['storage_gb'] ?? '1' }} GB</span>
                            </div>
                            <div class="feature-item">
                              <div class="d-flex align-items-center">
                                <i class="fas fa-headset text-warning me-2"></i>
                                <span>Destek</span>
                              </div>
                              <span class="fw-bold text-success">
                                {{ $isPopular ? '7/24' : 'İş Saatleri' }}
                              </span>
                            </div>
                          </div>

                          <a href="{{ route('subscription.subscribe', [$tenant->id, $plan->id]) }}"
                            class="btn {{ $isPopular ? 'btn-gradient' : 'btn-outline-primary' }} w-100 py-3 mb-4 fw-bold">
                            @if($onTrial)
                              <i class="fas fa-rocket me-2"></i>Bu Planı Seç
                            @else
                              <i class="fas fa-credit-card me-2"></i>Hemen Başla
                            @endif
                          </a>

                          <hr style="border-color: rgb(132 145 173);">

                          <button class="toggle-btn mt-3 d-flex w-100 align-items-center justify-content-center py-2 rounded-full text-muted transition hover:bg-light">
                            <span class="me-2 fw-medium">Tüm Özellikler</span>
                            <svg class="icon-down" style="width:20px;height:20px;" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            <svg class="icon-up hidden" style="width:20px;height:20px;" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                            </svg>
                          </button>

                          <div class="price-description hidden text-gray-600 mt-3 text-start">
                            {!! $plan->description !!}
                            
                            {{-- Özel Plan Detayları --}}
                            <div class="mt-3 pt-3 border-top">
                              <div class="small fw-medium mb-2">Bu plana dahil:</div>
                              <ul class="small list-unstyled">
                                @if($plan->name == 'Başlangıç')
                                  <li><i class="fas fa-check text-success me-2"></i>Temel servis takibi</li>
                                  <li><i class="fas fa-check text-success me-2"></i>Müşteri yönetimi</li>
                                  <li><i class="fas fa-check text-success me-2"></i>Rapor oluşturma</li>
                                  <li><i class="fas fa-check text-success me-2"></i>Email destek</li>
                                @elseif($plan->name == 'Profesyonel')
                                  <li><i class="fas fa-check text-success me-2"></i>Gelişmiş raporlama</li>
                                  <li><i class="fas fa-check text-success me-2"></i>Stok yönetimi</li>
                                  <li><i class="fas fa-check text-success me-2"></i>SMS entegrasyonu</li>
                                  <li><i class="fas fa-check text-success me-2"></i>Telefon + Email destek</li>
                                  <li><i class="fas fa-check text-success me-2"></i>Backup & Güvenlik</li>
                                @else
                                  <li><i class="fas fa-check text-success me-2"></i>Tüm premium özellikler</li>
                                  <li><i class="fas fa-check text-success me-2"></i>API erişimi</li>
                                  <li><i class="fas fa-check text-success me-2"></i>Özel entegrasyonlar</li>
                                  <li><i class="fas fa-check text-success me-2"></i>Öncelikli 7/24 destek</li>
                                  <li><i class="fas fa-check text-success me-2"></i>Kişisel hesap yöneticisi</li>
                                @endif
                              </ul>
                            </div>
                          </div>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </section>
              </div>
            @endif

            {{-- Alt Bilgilendirme ve SSS --}}
            <div class="row mt-5">
              <div class="col-12">
                <div class="card bg-light border-0">
                  <div class="card-body p-4">
                    <h6 class="mb-4 text-center">
                      <i class="fas fa-question-circle text-primary me-2"></i>
                      Sık Sorulan Sorular
                    </h6>
                    <div class="row">
                      <div class="col-md-4">
                        <div class="mb-4">
                          <h6 class="text-primary">Ek depolama kalıcı mı?</h6>
                          <p class="small text-muted mb-0">Evet, satın aldığınız ek depolama alanı kalıcıdır ve abonelik süresi boyunca kullanılabilir.</p>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="mb-4">
                          <h6 class="text-primary">Plan değiştirme nasıl olur?</h6>
                          <p class="small text-muted mb-0">İstediğiniz zaman planınızı yükseltebilir veya değiştirebilirsiniz. Fark ücreti otomatik hesaplanır.</p>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="mb-4">
                          <h6 class="text-primary">İptal koşulları neler?</h6>
                          <p class="small text-muted mb-0">Aboneliğinizi istediğiniz zaman iptal edebilirsiniz. Mevcut dönem sonuna kadar hizmet devam eder.</p>
                        </div>
                      </div>
                    </div>
                    
                    <div class="text-center pt-3 border-top">
                      <p class="mb-2">
                        <i class="fas fa-shield-alt text-success me-2"></i>
                        <strong>Güvenli Ödeme:</strong> Tüm ödemeler SSL şifreli bağlantıyla korunmaktadır.
                      </p>
                      <p class="small text-muted mb-0">
                        Sorularınız için bizimle iletişime geçin: 
                        <a href="mailto:destek@example.com" class="text-primary">destek@example.com</a> | 
                        <a href="tel:+905551234567" class="text-primary">0555 123 45 67</a>
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const allToggleButtons = document.querySelectorAll(".toggle-btn");

    allToggleButtons.forEach(button => {
      button.addEventListener("click", function () {
        const clickedCard = this.closest(".pricing-card");
        const description = clickedCard.querySelector(".price-description");
        const isHidden = description.classList.contains("hidden");

        // Tüm kartları kapat
        document.querySelectorAll(".pricing-card").forEach(card => {
          card.querySelector(".price-description").classList.add("hidden");
          card.querySelector(".icon-down").classList.remove("hidden");
          card.querySelector(".icon-up").classList.add("hidden");
        });

        // Tıklanan kartı aç/kapat
        if (isHidden) {
          description.classList.remove("hidden");
          clickedCard.querySelector(".icon-down").classList.add("hidden");
          clickedCard.querySelector(".icon-up").classList.remove("hidden");
        }
      });
    });

    // Smooth scroll to plans
    document.querySelectorAll('a[href="#plans"]').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('plans').scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      });
    });

    // Storage progress animasyonu
    const progressBars = document.querySelectorAll('.storage-progress-bar');
    progressBars.forEach(bar => {
      const width = bar.style.width;
      bar.style.width = '0%';
      setTimeout(() => {
        bar.style.width = width;
      }, 500);
    });
  });

  // Pulse animasyon CSS'i
  const style = document.createElement('style');
  style.textContent = `
    .pulse-animation {
      animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }
  `;
  document.head.appendChild(style);
</script>
@endsection