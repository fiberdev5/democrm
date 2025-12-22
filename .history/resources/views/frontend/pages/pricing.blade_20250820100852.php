@extends('frontend.main_master')
@section('main')

<style>
  .btn-gradient {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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

</style>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/alpinejs" defer></script>


<div class="breadcrumbs d-flex align-items-center" style="background-image: url({{asset('frontend/img/call-to-action.jpg')}});">
  <div class="container position-relative d-flex flex-column align-items-center" data-aos="fade">
    <h2>Pricing</h2>
    <ol>
      <li><a href="{{route('home')}}">Home</a></li>
      <li>Pricing</li>
    </ol>
  </div>
</div><!-- End Breadcrumbs -->

<section class="py-16 bg-gray-50">
  <div class="max-w-6xl mx-auto px-4">
    <div class="text-center mb-12">
      <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Our Pricing Plans</h2>
      <p class="text-gray-500 mt-2">Choose the plan that best fits your needs</p>
    </div>

    <!-- *** BURASI GÜNCELLENDİ *** -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:items-start">
      @foreach($prices as $i => $price)
        @php
          // 2. kartı popüler yap (0-based index)
          $isPopular = ($i === 1);
        @endphp

        <div class="relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition transform hover:-translate-y-2 p-8 flex flex-col text-center pricing-card">
          @if($isPopular)
            <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-blue-500 to-cyan-400 text-white text-xs font-semibold px-3 py-1 rounded-full shadow">
              Most Popular
            </span>
          @endif

          <div class="flex justify-center mb-6">
            <i class="{{$price->icon}} text-5xl text-blue-500"></i>
          </div>

          <h3 class="text-xl font-bold text-gray-800 mb-2">{{$price->name}}</h3>
          <div class="text-4xl font-extrabold text-gray-900 mb-4">
            ${{$price->price}} <span class="text-base text-gray-500 font-normal">/month</span>
          </div>

          <!-- Başta gizli description -->
          <div class="price-description hidden text-gray-600 mb-6 text-left">
            {!! $price->description !!}
          </div>

          <!-- Toggle butonu -->
          <button class="toggle-btn mb-4 inline-block w-full py-2 rounded-full border border-gray-300 text-gray-700 font-medium transition hover:bg-gray-100">
            Detayları Gör
          </button>

          <a href="#" class="inline-block w-full py-3 rounded-full bg-gradient-to-r from-blue-500 to-cyan-400 text-white font-semibold transition hover:opacity-90">
            Sign Up
          </a>
        </div>
      @endforeach
    </div>
  </div>
</section>

<!-- *** JAVASCRIPT GÜNCELLENDİ *** -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const allToggleButtons = document.querySelectorAll(".toggle-btn");

  allToggleButtons.forEach(button => {
    button.addEventListener("click", function () {
      const clickedCard = this.closest(".pricing-card");
      const description = clickedCard.querySelector(".price-description");
      const isHidden = description.classList.contains("hidden");

      // Önce tüm açık kartları kapat
      document.querySelectorAll(".pricing-card").forEach(card => {
        card.querySelector(".price-description").classList.add("hidden");
        card.querySelector(".toggle-btn").textContent = "Detayları Gör";
      });
      
      // Eğer tıklanan kart zaten kapalıysa, onu aç
      if (isHidden) {
        description.classList.remove("hidden");
        this.textContent = "Detayları Gizle";
      }
    });
  });
});
</script>

@endsection