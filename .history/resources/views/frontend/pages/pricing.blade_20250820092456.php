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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      @foreach($prices as $price)
      <div 
        class="relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition transform hover:-translate-y-2 p-8 flex flex-col text-center"
      >
        {{-- Popüler Plan Etiketi --}}
        @if($loop->index == 1)
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

        <div class="text-gray-600 flex-1 mb-6">
          {!! $price->description !!}
        </div>

        <a 
          href="#"
          class="inline-block w-full py-3 rounded-full bg-gradient-to-r from-blue-500 to-cyan-400 text-white font-semibold transition hover:opacity-90"
        >
          Sign Up
        </a>
      </div>
      @endforeach
    </div>
  </div>
</section>



@endsection