@extends('frontend.secure.user_master')
@section('user')

<style>
  .subscription-overview {
        background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
  }
  
  .subscription-overview::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
  }

  .storage-info-card {
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
  }

  .storage-progress {
    height: 10px;
    background: #e2e8f0;
    border-radius: 5px;
    overflow: hidden;
    margin: 10px 0;
  }

  .storage-progress-bar {
    height: 100%;
    transition: width 0.8s ease;
  }

  .extra-storage-list {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  .extra-storage-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
  }

  /* .extra-storage-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
  } */

  .btn-light{
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
  }
  .btn-light:hover{
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
  }
  .subscription-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
  }

  .stat-card {
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
  }

  .stat-value {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 5px;
  }

  .stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .btn-outline-primary:hover{color: #28a745!important;}
  .pricing-card {
    border-radius: 1.2rem;
    transition: all 0.3s ease;
  }

  .pricing-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  }

  /* Diğer mevcut stiller... */
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
</style>

<script src="https://cdn.tailwindcss.com"></script>

<div class="page-content">
  <div class="container-fluid">

    {{-- Üst Kısım: Detaylı Abonelik Bilgileri --}}
    @if($currentPlan)
      @php $storageInfo = $tenant->getStorageInfo(); @endphp
      
      <div class="subscription-overview">
        <div class="row align-items-center mb-4">
          <div class="col-md-8" >
            <h2 class="mb-2" style="color: #e2e8f0;font-size:18px;">
              <i class="fas fa-crown"></i>
              {{ $currentPlan->name }} Planı
            </h2>
            <p class="mb-0 opacity-90">{{ $currentPlan->getFormattedPrice() }} / {{ $currentPlan->getBillingCycleText() }} - Aktif aboneliğiniz</p>
          </div>
          <div class="col-md-4 text-end">
            <div class="opacity-75">Bitiş Tarihi</div>
            <div class="h4 mb-0">{{ $tenant->subscription_ends_at?->format('d.m.Y') ?? 'Süresiz' }}</div>
          </div>
        </div>

        <div class="subscription-stats">
          <div class="stat-card">
            <div class="stat-value">{{ $currentPlan->limits['users'] == -1 ? '∞' : $currentPlan->limits['users'] }}</div>
            <div class="stat-label">Kullanıcı Hakkı</div>
          </div>
          <div class="stat-card">
            <div class="stat-value">{{ $storageInfo['base_limit_gb'] }} GB</div>
            <div class="stat-label">Plan Depolama</div>
          </div>
          @if($storageInfo['has_extra_storage'])
            <div class="stat-card">
              <div class="stat-value">{{ $storageInfo['extra_storage_gb'] }} GB</div>
              <div class="stat-label">Ek Depolama</div>
            </div>
          @endif
          <div class="stat-card">
            <div class="stat-value">{{ $storageInfo['total_limit_gb'] }} GB</div>
            <div class="stat-label">Toplam Depolama</div>
          </div>
        </div>

        {{-- Storage Durumu --}}
       

        <div class="row text-center">
          <div class="col-md-4">
            <a href="{{ route('storage.packages', $tenant->id) }}" class="btn btn-light btn-sm">
              <i class="fas fa-hdd me-1"></i>Ek Depolama
            </a>
          </div>
          <div class="col-md-4">
            @php $maxPlan = $plans->sortByDesc('price')->first(); @endphp
            @if($currentPlan->id !== $maxPlan->id)
              <a href="{{route('abonelikler', $tenant->id)}}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-up me-1"></i>Planı Yükselt
              </a>
            @endif
          </div>
          <div class="col-md-4">
            <a href="{{route('depolama.bilgisi', $tenant->id)}}" class="btn btn-light btn-sm">
              <i class="fas fa-file-invoice me-1"></i>Depolama Alanı Yönetimi
            </a>
          </div>
        </div>
      </div>

    @elseif($onTrial)
      <div class="subscription-overview">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h3 class="mb-2">
              <i class="fas fa-clock me-3"></i>
              Deneme Süresi Aktif
            </h3>
            <p class="mb-0 opacity-90">{{ $remainingTrialDays }} gün deneme hakkınız kaldı</p>
          </div>
          <div class="col-md-4 text-end">
            <div class="display-4 fw-bold">{{ $remainingTrialDays }}</div>
            <div class="opacity-75">GÜN KALDI</div>
          </div>
        </div>
      </div>

    @else
      <div class="alert alert-danger">
        <h5><i class="fas fa-exclamation-triangle me-2"></i>Deneme Süreniz Sona Erdi</h5>
        <p class="mb-0">Hizmetleri kullanmaya devam etmek için bir plan seçmeniz gerekiyor.</p>
      </div>
    @endif

    {{-- Alt Kısım: Ek Storage Paketleri --}}
    @if($currentPlan && $storageInfo['has_extra_storage'])
      <div class="row">
        <div class="col-12">
          <div class="extra-storage-list">
            <h5 class="mb-4">
              <i class="fas fa-plus-circle text-primary me-2"></i>
              Satın Aldığınız Ek Depolama Paketleri
            </h5>
            
            @php
              $extraPackages = $tenant->storagePurchases()
                                    ->where('status', 'completed')
                                    ->with('package')
                                    ->orderBy('purchased_at', 'desc')
                                    ->get();
            @endphp

            @foreach($extraPackages as $purchase)
              <div class="extra-storage-item">
                <div class="row align-items-center">
                  <div class="col-md-3">
                    <div class="d-flex align-items-center">
                      <i class="fas fa-database fa-2x me-3" style="color:#28a745"></i>
                      <div>
                        <h6 class="mb-1">{{ $purchase->package->name ?? 'Ek Depolama' }}</h6>
                        <small class="text-muted">{{ $purchase->storage_gb }} GB</small>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="text-center">
                      <div class="fw-bold " style="">{{ number_format($purchase->amount, 2) }} ₺</div>
                      <small class="text-muted">Tek seferlik ödeme</small>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="text-center">
                      <div class="fw-bold">{{ $purchase->purchased_at?->format('d.m.Y') }}</div>
                      <small class="text-muted">Satın alma tarihi</small>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="text-center">
                      <span class="badge px-3 py-2" style="color: #fff;
    background-color: #28a745 !important;
    border-color: #28a745 !important;">
                        <i class="fas fa-check-circle me-1"></i>Aktif
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach

            <div class="text-center mt-4">
              <a href="{{ route('storage.packages', $tenant->id) }}" class="btn btn-outline-primary" style="color: #28a745;">
                <i class="fas fa-plus me-2"></i>Daha Fazla Depolama Al
              </a>
            </div>
          </div>
        </div>
      </div>
    @endif

    {{-- Plan Seçenekleri (Trial bitmiş veya plan yükseltme için) --}}
    @if(!$currentPlan || $onTrial)
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h4 class="mb-0">{{ $onTrial ? 'Plan Seçenekleri' : 'Plan Seçin' }}</h4>
            </div>
            <div class="card-body">
              <section class="py-4 bg-gray-50">
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
                            <span class="font-medium">{{ $plan->limits['storage_gb'] ?? '1' }} GB</span>
                          </div>
                        </div>

                        <a href="{{ route('subscription.subscribe', [$tenant->id, $plan->id]) }}"
                          class="inline-block w-full py-2 rounded-full bg-gradient-to-r from-orange-500 to-[#f9b233] text-white font-semibold transition hover:opacity-90 mb-4">
                          {{ $onTrial ? 'Bu Planı Seç' : 'Planı Satın Al' }}
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
          </div>
        </div>
      </div>
    @endif

  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Toggle buttons için event listener
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
</script>
@endsection