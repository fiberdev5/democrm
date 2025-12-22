@extends('frontend.main_master')
@section('main')

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
            <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-orange-500 to-[#f9b233] text-white text-xs font-semibold px-3 py-1 rounded-full shadow">
              Önerilen
            </span>
          @endif

          <div class="flex justify-center mb-6">
            <i class="{{$price->icon}} text-5xl text-[#f9b233]"></i>
          </div>

          <h3 class="text-xl font-bold text-gray-800 mb-2">{{$price->name}}</h3>
          <p class="text-gray-500 font-normal mb-2">Teknik servis süreçlerinizi dijitalleştirin, müşteri memnuniyetini artırın.</p>
          <div class="text-4xl font-extrabold text-gray-900 mb-4">
            ₺ {{$price->price}} <span class="text-base text-gray-500 font-normal">/aylık</span> <span class="text-[11px] sm:text-xs bg-bls-teal-50 text-bls-success-700 px-2 py-0.5 rounded-xl ml-2">%<!-- -->10<!-- --> Kazanın</span>
          </div>

          
          <a href="#" class="inline-block w-full py-2 rounded-full bg-gradient-to-r from-orange-500 to-[#f9b233] text-white font-semibold transition hover:opacity-90">
            Satın Al
          </a>

           <button class="toggle-btn mb-2 mt-3 flex w-full items-center justify-center py-2 rounded-full text-gray-700 transition hover:bg-gray-100">
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