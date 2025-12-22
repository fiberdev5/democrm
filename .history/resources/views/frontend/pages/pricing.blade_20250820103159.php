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
    <h2>Fiyatlar</h2>
    <ol>
      <li><a href="{{route('home')}}">Anasayfa</a></li>
      <li>Fiyatlar</li>
    </ol>
  </div>
</div><!-- End Breadcrumbs -->

<section class="py-16 bg-gray-50">
  <div class="max-w-6xl mx-auto px-4">
    <div class="text-center mb-12">
      <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Fiyat Planlarımız</h2>
      <p class="text-gray-500 mt-2">İhtiyaçlarınıza en uygun planı seçin</p>
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

          
          <a href="#" class="inline-block w-full py-3 rounded-full bg-gradient-to-r from-blue-500 to-cyan-400 text-white font-semibold transition hover:opacity-90">
            Satın Al
          </a>

           <button class="toggle-btn mb-4 flex w-full items-center justify-center py-2 rounded-full border border-gray-300 text-gray-700 transition hover:bg-gray-100">
            <span class="mr-2">Özellikler</span>
            <!-- Aşağı bakan ikon (Başlangıçta görünür) -->
            <svg class="icon-down h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
            <!-- Yukarı bakan ikon (Başlangıçta gizli) -->
            <svg class="icon-up hidden h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
          </button>

          <!-- Başta gizli description -->
          <div class="price-description hidden text-gray-600 mb-6 text-center">
            {!! $price->description !!}
          </div>
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

      // Önce tüm açık kartları ve ikonları resetle
      document.querySelectorAll(".pricing-card").forEach(card => {
        // Açıklamayı gizle
        card.querySelector(".price-description").classList.add("hidden");
        // İkonları başlangıç durumuna getir (aşağı ikon görünsün, yukarı gizlensin)
        card.querySelector(".icon-down").classList.remove("hidden");
        card.querySelector(".icon-up").classList.add("hidden");
      });
      
      // Eğer tıklanan kart zaten kapalıysa, onu aç ve ikonunu değiştir
      if (isHidden) {
        description.classList.remove("hidden");
        // Tıklanan kartın ikonlarını güncelle (aşağı ikon gizlensin, yukarı görünsün)
        clickedCard.querySelector(".icon-down").classList.add("hidden");
        clickedCard.querySelector(".icon-up").classList.remove("hidden");
      }
    });
  });
});
</script>

@endsection