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

  /* Yeni stiller */
  .storage-info-badge {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 15px;
  }

  .current-plan-indicator {
    position: absolute;
    top: -3px;
    right: -3px;
    background: #10b981;
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
  }

  .storage-quick-actions {
    background: #f8fafc;
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    margin: 20px 0;
  }

  .notification-bar {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .plan-comparison-hint {
    background: #fef3c7;
    border-left: 4px solid #f59e0b;
    padding: 12px 16px;
    margin: 15px 0;
    border-radius: 0 8px 8px 0;
  }
</style>

<script src="https://cdn.tailwindcss.com"></script>  

<div class="page-content">
  <div class="container-fluid">
    
    <!-- Mevcut Plan ve Storage Durumu -->
    @if($tenant->hasActiveSubscription())
      <div class="notification-bar">
        <div class="d-flex align-items-center">
          <i class="fas fa-crown me-3 fa-lg"></i>
          <div>
            <strong>Mevcut Planınız: {{ $tenant->plan()?->name ?? 'Aktif Plan' }}</strong>
            <div class="small">
              Storage: {{ $tenant->getStorageInfo()['current_usage_formatted'] }} / {{ $tenant->getStorageInfo()['limit_formatted'] }} kullanılıyor
              @if($tenant->getStorageInfo()['has_extra_storage'])
                <span class="badge bg-success ms-2">+{{ $tenant->getStorageInfo()['extra_storage_gb'] }} GB Ek Alan</span>
              @endif
            </div>
          </div>
        </div>
        <div>
          @if($tenant->getStorageInfo()['warning_threshold'])
            <a href="{{ route('storage.packages', $tenant->id) }}" class="btn btn-warning btn-sm me-2">
              <i class="fas fa-hdd me-1"></i>Ek Depolama Al
            </a>
          @endif
          <small class="opacity-75">Bitiş: {{ $tenant->subscription_ends_at?->format('d.m.Y') }}</small>
        </div>
      </div>
    @endif

    <!-- Ek Storage Hızlı Erişim -->
    @if($tenant->getStorageInfo()['danger_threshold'] || $tenant->getStorageInfo()['warning_threshold'])
      <div class="storage-quick-actions">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h6 class="mb-2 text-danger">
              <i class="fas fa-exclamation-triangle me-2"></i>
              Depolama Alanınız {% {{ $tenant->getStorageInfo()['usage_percentage'] }} Dolu
            </h6>
            <p class="mb-0 text-muted">
              Kalan alan: {{ $tenant->getStorageInfo()['remaining_formatted'] }} - 
              Yeni dosya yükleyebilmek için ek depolama alanı satın alın.
            </p>
          </div>
          <div class="col-md-4 text-end">
            <a href="{{ route('storage.packages', $tenant->id) }}" class="btn btn-primary">
              <i class="fas fa-plus-circle me-2"></i>Ek Depolama Al
            </a>
          </div>
        </div>
      </div>
    @endif

    <div class="row">
      <div class="col-md-12">
        <section class="py-8 bg-gray-50">
          <div class="container">
            
            <!-- Başlık ve Plan Karşılaştırma İpucu -->
            <div class="text-center mb-6">
              <h2 class="text-3xl font-bold text-gray-800 mb-4">Abonelik Planları</h2>
              <div class="plan-comparison-hint">
                <i class="fas fa-lightbulb me-2"></i>
                <strong>İpucu:</strong> Özellikler butonuna tıklayarak plan detaylarını karşılaştırabilirsiniz.
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:items-start">
              @foreach($plans as $i => $plan)
                @php
                  $isPopular = ($i === 1);
                  $isCurrentPlan = $tenant->hasActiveSubscription() && $tenant->plan()?->id == $plan->id;
                @endphp

                <div class="relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition transform hover:-translate-y-2 p-8 flex flex-col text-center pricing-card">
                  
                  @if($isCurrentPlan)
                    <div class="current-plan-indicator">
                      <i class="fas fa-check"></i>
                    </div>
                    <div class="storage-info-badge">
                      <i class="fas fa-star"></i>
                      Mevcut Planınız
                    </div>
                  @elseif($isPopular)
                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-orange-500 to-[#f9b233] text-white text-xs font-semibold px-3 py-1 rounded-full shadow">
                      Önerilen
                    </span>
                  @endif

                  <!-- Ek Storage Bilgisi -->
                  @if($tenant->getStorageInfo()['has_extra_storage'] && !$isCurrentPlan)
                    <div class="storage-info-badge mb-4">
                      <i class="fas fa-plus-circle"></i>
                      +{{ $tenant->getStorageInfo()['extra_storage_gb'] }} GB Ek Depolama Aktif
                    </div>
                  @endif

                  <div class="flex justify-center mb-6">
                    <i class="{{$plan->icon}} text-5xl text-[#f9b233]"></i>
                  </div>
                  
                  <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $plan->name }}</h3>
                  <p class="text-gray-500 font-normal mb-2" style="font-size: 14px;">
                    {{ $isCurrentPlan ? 'Şu anda kullandığınız plan' : 'Teknik servis süreçlerinizi dijitalleştirin, müşteri memnuniyetini artırın.' }}
                  </p>

                  <div class="text-4xl font-extrabold text-gray-900 mb-4">
                    ₺ {{ number_format($plan->price) }}
                    <span class="text-base text-gray-500 font-normal">
                      / {{ $plan->getBillingCycleText() }}
                    </span>
                  </div>

                  <!-- Geliştirilmiş plan özellikleri -->
                  <div class="flex justify-between items-center my-4 text-sm text-gray-600 bg-gray-50 rounded-lg p-3">
                    <div class="flex items-center space-x-2">
                      <i class="fas fa-users text-blue-500"></i>
                      <span class="font-medium">
                        {{ $plan?->limits['users'] == -1 ? 'Sınırsız' : $plan?->limits['users'] }} Kullanıcı
                      </span>
                    </div>
                    
                    <div class="flex items-center space-x-1">
                      <i class="fas fa-hdd text-green-500"></i>
                      <span class="font-medium">
                        {{ $plan?->limits['storage_gb'] ?? '1' }} GB Depolama
                      </span>
                    </div>
                  </div>

                  @if($isCurrentPlan)
                    <div class="inline-block w-full py-2 rounded-full bg-green-100 text-green-700 font-semibold mb-4">
                      <i class="fas fa-check-circle me-2"></i>Aktif Plan
                    </div>
                  @else
                    <a href="{{ route('subscription.subscribe', [$tenant->id, $plan->id]) }}"
                      class="inline-block w-full py-2 rounded-full bg-gradient-to-r from-orange-500 to-[#f9b233] text-white font-semibold transition hover:opacity-90 mb-4">
                      {{ $tenant->hasActiveSubscription() ? 'Plana Geç' : 'Planı Satın Al' }}
                    </a>
                  @endif

                  <hr class="mt-2 mb-4" style="--tw-border-opacity: 1;border-color: rgb(132 145 173);">

                  <button class="toggle-btn mb-2 flex w-full items-center justify-center py-2 rounded-full text-gray-700 transition hover:bg-gray-100">
                    <span class="mr-2">Özellikler</span>
                    <svg class="icon-down h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                    <svg class="icon-up hidden h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/>
                    </svg>
                  </button>

                  <div class="price-description hidden text-gray-600 mb-6 text-center">
                    {!! $plan->description !!}
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </section>
      </div>
    </div>

    <!-- Alt bilgi kısmı -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="card bg-light">
          <div class="card-body text-center">
            <h6 class="mb-3">
              <i class="fas fa-info-circle text-primary me-2"></i>
              Depolama Alanınız Yetersiz mi?
            </h6>
            <p class="mb-3 text-muted">
              Mevcut planınıza ek olarak ekstra depolama alanı satın alabilirsiniz. 
              Ek depolama kalıcıdır ve süre sınırı yoktur.
            </p>
            <a href="{{ route('storage.packages', $tenant->id) }}" class="btn btn-outline-primary">
              <i class="fas fa-hdd me-2"></i>Ek Depolama Paketlerini İncele
            </a>
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