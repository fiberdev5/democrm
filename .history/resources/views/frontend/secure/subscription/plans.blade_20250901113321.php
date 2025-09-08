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
  list-style: none; /* Varsayılan madde imini kaldır */
  padding: 0;
  text-align: left; /* Listenin sola hizalı olmasını garantile */
  margin: 0 auto; /* Eğer gerekirse listeyi ortalamak için */
  display: inline-block; /* İçerik kadar yer kaplaması için */
}

.price-description ul li {
  display: flex;
  align-items: center;
  margin-bottom: 0.75rem; /* Her satır arasına boşluk */
  font-size: 0.95rem;
}

.price-description ul li::before {
  content: '';
  display: inline-block;
  width: 1.25rem;  /* 20px */
  height: 1.25rem; /* 20px */
  margin-right: 0.5rem; /* İkon ve metin arasına boşluk */
  background-color: #027a48; /* Marka renginiz */
  -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor'%3E%3Cpath fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' clip-rule='evenodd' /%3E%3C/svg%3E") no-repeat center;
  mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor'%3E%3Cpath fill-rule='evenodd' d='M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z' clip-rule='evenodd' /%3E%3C/svg%3E") no-repeat center;
  background-size: contain;
}
.text-gray-900 {
    --tw-text-opacity: 1;
    color: #212529;
}
.bg-bls-teal-50
 {
    background-color: #cff3fa;
}
.text-bls-success-700 {
    color: #027a48;
}
</style>

<script src="https://cdn.tailwindcss.com"></script>
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
