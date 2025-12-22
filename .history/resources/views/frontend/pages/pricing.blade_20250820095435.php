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

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
  <!-- Kart 1 -->
  <div class="bg-white shadow-lg rounded-2xl p-6 relative">
    <h3 class="text-xl font-semibold mb-3">Başlangıç Paketi</h3>
    <p class="text-gray-600">Küçük işletmeler için ideal.</p>
    <button class="mt-4 text-blue-600 font-medium toggle-btn" data-target="desc1">
      Detayları Gör
    </button>
    <div id="desc1" class="overflow-hidden transition-all duration-500 max-h-0 opacity-0">
      <p class="mt-3 text-gray-500">
        Bu paket, temel ihtiyaçlarınızı karşılamak için uygundur.
      </p>
    </div>
  </div>

  <!-- Kart 2 -->
  <div class="bg-white shadow-lg rounded-2xl p-6 relative">
    <h3 class="text-xl font-semibold mb-3">Standart Paket</h3>
    <p class="text-gray-600">Büyüyen işletmeler için.</p>
    <button class="mt-4 text-blue-600 font-medium toggle-btn" data-target="desc2">
      Detayları Gör
    </button>
    <div id="desc2" class="overflow-hidden transition-all duration-500 max-h-0 opacity-0">
      <p class="mt-3 text-gray-500">
        Daha fazla özellik ve esneklik sağlar.
      </p>
    </div>
  </div>

  <!-- Kart 3 -->
  <div class="bg-white shadow-lg rounded-2xl p-6 relative">
    <h3 class="text-xl font-semibold mb-3">Uzman Paketi</h3>
    <p class="text-gray-600">Profesyonel kullanım için.</p>
    <button class="mt-4 text-blue-600 font-medium toggle-btn" data-target="desc3">
      Detayları Gör
    </button>
    <div id="desc3" class="overflow-hidden transition-all duration-500 max-h-0 opacity-0">
      <p class="mt-3 text-gray-500">
        Tüm gelişmiş özellikleri ve destekleri içerir.
      </p>
    </div>
  </div>
</div>

<script>
  document.querySelectorAll('.toggle-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const target = document.getElementById(btn.dataset.target);

      if (target.classList.contains('max-h-0')) {
        target.classList.remove('max-h-0', 'opacity-0');
        target.classList.add('max-h-40', 'opacity-100');
        btn.textContent = "Kapat";
      } else {
        target.classList.add('max-h-0', 'opacity-0');
        target.classList.remove('max-h-40', 'opacity-100');
        btn.textContent = "Detayları Gör";
      }
    });
  });
</script>




@endsection