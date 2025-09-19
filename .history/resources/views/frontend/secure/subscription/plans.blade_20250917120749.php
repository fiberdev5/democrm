@extends('frontend.secure.user_master')
@section('user')

<style>
  .btn-gradient {
    background: linear-gradient(135deg, #fb923c 0%, #f9b233 100%);
    color: #fff;
    border: none;
    transition: all 0.3s ease;
  }

  .btn-gradient:hover {
    opacity: 0.9;
    transform: translateY(-2px);
  }

  .pricing-card {
    border-radius: 1.2rem;
    transition: all 0.3s ease;
  }

  .pricing-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
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

  .text-gray-900 {
    --tw-text-opacity: 1;
    color: #212529;
  }

  .bg-bls-teal-50 {
    background-color: #cff3fa;
  }

  .text-bls-success-700 {
    color: #027a48;
  }

  .current-plan-card {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 20px;
  }

  .storage-info-card {
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
  }

  .storage-progress {
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
    margin: 10px 0;
  }

  .storage-progress-bar {
    height: 100%;
    transition: width 0.3s ease;
  }

  .extra-storage-badge {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
</style>

<script src="https://cdn.tailwindcss.com"></script>

<div class="page-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Aboneliklerim</h4>
            <div class="d-flex gap-2">
              <a href="{{ route('storage.packages', $tenant->id) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-hdd me-1"></i>Ek Depolama
              </a>
            </div>
          </div>
          <div class="card-body">

            {{-- Aktif Abonelik Durumu --}}
            @if($currentPlan)
              @php $storageInfo = $tenant->getStorageInfo(); @endphp
              
              <div class="current-plan-card">
                <div class="row align-items-center">
                  <div class="col-md-8">
                    <h5 class="mb-2">
                      <i class="fas fa-crown me-2"></i>
                      Aktif Plan: {{ $currentPlan->name }}
                    </h5>
                    <p class="mb-2 opacity-90">
                      {{ $currentPlan->getFormattedPrice() }} / {{ $currentPlan->getBillingCycleText() }}
                    </p>
                    <div class="d-flex align-items-center gap-3">
                      <small><i class="fas fa-users me-1"></i>{{ $currentPlan->limits['users'] == -1 ? 'Sınırsız' : $currentPlan->limits['users'] }} Kullanıcı</small>
                      <small><i class="fas fa-hdd me-1"></i>{{ $storageInfo['total_limit_gb'] }} GB Toplam Depolama</small>
                      @if($storageInfo['has_extra_storage'])
                        <div class="extra-storage-badge">
                          <i class="fas fa-plus-circle"></i>
                          +{{ $storageInfo['extra_storage_gb'] }} GB Ek
                        </div>
                      @endif
                    </div>
                  </div>
                  <div class="col-md-4 text-end">
                    <div class="text-white-50">Son Ödeme</div>
                    <div class="fw-bold">{{ $tenant->subscription_ends_at?->format('d.m.Y') ?? 'Süresiz' }}</div>
                  </div>
                </div>
              </div>

              {{-- Storage Durumu --}}
              <div class="storage-info-card">
                <div class="row">
                  <div class="col-md-8">
                    <h6 class="mb-2">
                      <i class="fas fa-database text-primary me-2"></i>
                      Depolama Kullanımı
                    </h6>
                    <div class="d-flex justify-content-between mb-2">
                      <span>{{ $storageInfo['current_usage_formatted'] }} / {{ $storageInfo['limit_formatted'] }} kullanılıyor</span>
                      <span class="fw-bold text-{{ $storageInfo['danger_threshold'] ? 'danger' : ($storageInfo['warning_threshold'] ? 'warning' : 'success') }}">
                        %{{ $storageInfo['usage_percentage'] }}
                      </span>
                    </div>
                    <div class="storage-progress">
                      <div class="storage-progress-bar bg-{{ $storageInfo['danger_threshold'] ? 'danger' : ($storageInfo['warning_threshold'] ? 'warning' : 'success') }}" 
                           style="width: {{ $storageInfo['usage_percentage'] }}%"></div>
                    </div>
                    <small class="text-muted">Kalan alan: {{ $storageInfo['remaining_formatted'] }}</small>
                  </div>
                  <div class="col-md-4 text-end">
                    @if($storageInfo['warning_threshold'])
                      <a href="{{ route('storage.packages', $tenant->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-plus-circle me-1"></i>Ek Alan Satın Al
                      </a>
                    @endif
                  </div>
                </div>
              </div>

              {{-- Plan Yükseltme --}}
              @php $maxPlan = $plans->sortByDesc('price')->first(); @endphp
              @if($currentPlan->id !== $maxPlan->id)
                <div class="alert alert-info">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <i class="fas fa-arrow-up me-2"></i>
                      <strong>Daha fazla özellik mi istiyorsunuz?</strong>
                      <div class="small">Planınızı yükselterek daha fazla kullanıcı ve depolama alanına sahip olun.</div>
                    </div>
                    <a href="#plans" class="btn btn-primary btn-sm">Planları Görüntüle</a>
                  </div>
                </div>
              @endif

            {{-- Trial Dönemi --}}
            @elseif($onTrial)
              <div class="alert alert-warning">
                <div class="row align-items-center">
                  <div class="col-md-8">
                    <h6 class="mb-1">
                      <i class="fas fa-clock me-2"></i>
                      Deneme Süresi Aktif
                    </h6>
                    <p class="mb-0">{{ $remainingTrialDays }} gün deneme hakkınız kaldı. Süreniz dolmadan bir plan seçin.</p>
                  </div>
                  <div class="col-md-4 text-end">
                    <a href="#plans" class="btn btn-warning">Plan Seç</a>
                  </div>
                </div>
              </div>

            {{-- Trial Bitmiş --}}
            @else
              <div class="alert alert-danger">
                <div class="row align-items-center">
                  <div class="col-md-8">
                    <h6 class="mb-1">
                      <i class="fas fa-exclamation-triangle me-2"></i>
                      Deneme Süreniz Sona Erdi
                    </h6>
                    <p class="mb-0">Hizmetleri kullanmaya devam etmek için bir plan seçmeniz gerekiyor.</p>
                  </div>
                  <div class="col-md-4 text-end">
                    <a href="#plans" class="btn btn-danger">Acil Plan Seç</a>
                  </div>
                </div>
              </div>
            @endif

            {{-- Plan Seçenekleri --}}
            @if(!$currentPlan || $onTrial)
              <div id="plans">
                <hr class="my-4">
                <h5 class="mb-4 text-center">
                  <i class="fas fa-star text-warning me-2"></i>
                  Planlarımız
                </h5>
                
                <section class="py-4">
                  <div class="container">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:items-start">
                      @foreach($plans as $i => $plan)
                        @php $isPopular = ($i === 1); @endphp

                        <div class="relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition transform hover:-translate-y-2 p-8 flex flex-col text-center pricing-card">
                          
                          @if($isPopular)
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-orange-500 to-[#f9b233] text-white text-xs font-semibold px-3 py-1 rounded-full shadow">
                              Önerilen
                            </span>
                          @endif

                          <div class="flex justify-center mb-6">
                            <i class="{{$plan->icon}} text-5xl text-[#f9b233]"></i>
                          </div>

                          <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $plan->name }}</h3>
                          <p class="text-gray-500 font-normal mb-2" style="font-size: 14px;">
                            Teknik servis süreçlerinizi dijitalleştirin, müşteri memnuniyetini artırın.
                          </p>

                          <div class="text-4xl font-extrabold text-gray-900 mb-4">
                            ₺ {{ number_format($plan->price) }}
                            <span class="text-base text-gray-500 font-normal">
                              / {{ $plan->getBillingCycleText() }}
                            </span>
                          </div>

                          <div class="flex justify-between items-center my-4 text-sm text-gray-600 bg-gray-50 rounded-lg p-3">
                            <div class="flex items-center space-x-1">
                              <i class="fas fa-users text-blue-500"></i>
                              <span class="font-medium">
                                {{ $plan->limits['users'] == -1 ? 'Sınırsız' : $plan->limits['users'] }} Kullanıcı
                              </span>
                            </div>
                            <div class="flex items-center space-x-1">
                              <i class="fas fa-hdd text-green-500"></i>
                              <span class="font-medium">
                                {{ $plan->limits['storage_gb'] ?? '1' }} GB
                              </span>
                            </div>
                          </div>

                          <a href="{{ route('subscription.subscribe', [$tenant->id, $plan->id]) }}"
                            class="inline-block w-full py-2 rounded-full bg-gradient-to-r from-orange-500 to-[#f9b233] text-white font-semibold transition hover:opacity-90 mb-4">
                            {{ $onTrial ? 'Planı Seç' : 'Planı Satın Al' }}
                          </a>

                          <hr style="border-color: rgb(132 145 173);">

                          <button class="toggle-btn mt-3 flex w-full items-center justify-center py-2 rounded-full text-gray-700 transition hover:bg-gray-100">
                            <span class="mr-2">Özellikler</span>
                            <svg class="icon-down h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            <svg class="icon-up hidden h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                            </svg>
                          </button>

                          <div class="price-description hidden text-gray-600 mt-3 text-center">
                            {!! $plan->description !!}
                          </div>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </section>
              </div>
            @endif

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

        document.querySelectorAll(".pricing-card").forEach(card => {
          card.querySelector(".price-description").classList.add("hidden");
          card.querySelector(".icon-down").classList.remove("hidden");
          card.querySelector(".icon-up").classList.add("hidden");
        });

        if (isHidden) {
          description.classList.remove("hidden");
          clickedCard.querySelector(".icon-down").classList.add("hidden");
          clickedCard.querySelector(".icon-up").classList.remove("hidden");
        }
      });
    });
  });
</script>
@endsection