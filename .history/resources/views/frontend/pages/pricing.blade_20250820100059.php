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
  <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    @foreach($prices as $i => $price)
      <div class="pricing-card bg-white rounded-2xl shadow-lg p-8 flex flex-col text-center relative">
        <div class="flex justify-center mb-6">
          <i class="{{$price->icon}} text-5xl text-blue-500"></i>
        </div>

        <h3 class="text-xl font-bold text-gray-800 mb-2">{{$price->name}}</h3>
        <div class="text-4xl font-extrabold text-gray-900 mb-4">
          ${{$price->price}} <span class="text-base text-gray-500 font-normal">/month</span>
        </div>

        <button class="toggle-btn mb-4 inline-block w-full py-2 rounded-full border border-gray-300 text-gray-700 font-medium transition hover:bg-gray-100">
          Detayları Gör
        </button>

        <a href="#" class="inline-block w-full py-3 rounded-full bg-gradient-to-r from-blue-500 to-cyan-400 text-white font-semibold transition hover:opacity-90">
          Sign Up
        </a>
      </div>

      <!-- Detay bölümü kartın hemen altına, grid dışında -->
      <div class="price-description max-h-0 overflow-hidden transition-all duration-300 bg-white rounded-xl shadow-md mb-8 px-8 py-4 text-gray-600">
        {!! $price->description !!}
      </div>
    @endforeach
  </div>
</div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const toggleBtns = document.querySelectorAll(".toggle-btn");
  toggleBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      // kartın hemen altındaki detay div’i
      const card = btn.closest(".pricing-card");
      const desc = card.nextElementSibling;

      if(desc.classList.contains("max-h-0")) {
        desc.classList.remove("max-h-0");
        desc.classList.add("max-h-96");
        btn.textContent = "Detayları Gizle";
      } else {
        desc.classList.add("max-h-0");
        desc.classList.remove("max-h-96");
        btn.textContent = "Detayları Gör";
      }
    });
  });
});
</script>




@endsection