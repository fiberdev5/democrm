@extends('frontend.secure.user_master')
@section('user')
<div class="page-content">
    <div class="container-fluid">
        <div class="row ">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Aboneliklerim</h4>
                    </div>
                    <div class="card-body">
                        
                        {{-- 1. Trial dönemi --}}
                        @if($onTrial)
                            <div class="alert alert-warning">
                                <strong>Deneme Süresi:</strong> {{ $remainingTrialDays }} gün kaldı.
                            </div>
                        
                        {{-- 2. Trial bitmiş ve abonelik yok --}}
                        @elseif(!$currentPlan)
                            <div class="alert alert-danger mb-6">
    Deneme süreniz sona erdi. Devam edebilmek için bir plan seçmeniz gerekiyor.
  </div>

  <section class="py-8 bg-gray-50">
    <div class="container">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:items-start">
        @foreach($plans as $i => $plan)
          @php
            // 2. kartı "önerilen" yap
            $isPopular = ($i === 1);
          @endphp

          <div class="relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition transform hover:-translate-y-2 p-8 flex flex-col text-center pricing-card">
            
            @if($isPopular)
              <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-orange-500 to-[#f9b233] text-white text-xs font-semibold px-3 py-1 rounded-full shadow">
                Önerilen
              </span>
            @endif

            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $plan->name }}</h3>

            <div class="text-4xl font-extrabold text-gray-900 mb-4">
              ₺ {{ number_format($plan->price, 2) }}
              <span class="text-base text-gray-500 font-normal">
                / {{ $plan->getBillingCycleText() }}
              </span>
            </div>

            <a href="{{ route('subscription.subscribe', [$tenant->id,$plan->id]) }}"
               class="inline-block w-full py-2 rounded-full bg-gradient-to-r from-orange-500 to-[#f9b233] text-white font-semibold transition hover:opacity-90">
              Planı Satın Al
            </a>

            <hr class="mt-4" style="--tw-border-opacity: 1;border-color: rgb(132 145 173);">

            <button class="toggle-btn mb-2 mt-3 flex w-full items-center justify-center py-2 rounded-full text-gray-700 transition hover:bg-gray-100">
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
                        
                        {{-- 3. Aktif abonelik var --}}
                        @else
                            <div class="alert alert-success">
                                Şu anda <strong>{{ $currentPlan->name }}</strong> paketini kullanıyorsunuz.
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <p><strong>Paket Adı:</strong> {{ $currentPlan->name }}</p>
                                    <p><strong>Fiyat:</strong> {{ $currentPlan->getFormattedPrice() }} / {{ $currentPlan->getBillingCycleText() }}</p>
                                    <p><strong>Açıklama:</strong> {!! $currentPlan->description !!}</p>
                                </div>
                                <div class="card-footer text-center">
                                    <a href="{{ route('subscription.upgrade', [$tenant->id,$currentPlan->id]) }}" class="btn btn-warning">
                                        Paketi Yükselt
                                    </a>
                                </div>
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
