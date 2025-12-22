@extends('frontend.main_master')
@section('main')

<div class="breadcrumbs d-flex align-items-center" style="background-image: url({{asset('frontend/img/call-to-action.jpg')}});">
  <div class="container position-relative d-flex flex-column align-items-center" data-aos="fade">
    <h2>Pricing</h2>
    <ol>
      <li><a href="{{route('home')}}">Home</a></li>
      <li>Pricing</li>
    </ol>
  </div>
</div><!-- End Breadcrumbs -->

<section class="py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold">Our Pricing Plans</h2>
      <p class="text-muted">Choose the plan that best fits your needs</p>
    </div>

    <div class="row g-4">
      @foreach($prices as $price)
      <div class="col-md-4">
        <div class="card border-0 shadow-lg h-100 pricing-card text-center position-relative">
          
          {{-- Popüler plan etiketi --}}
          @if($loop->index == 1)
          <span class="badge bg-primary position-absolute top-0 start-50 translate-middle-x mt-2">
            Most Popular
          </span>
          @endif

          <div class="card-body p-4">
            <div class="mb-3">
              <i class="{{$price->icon}} fs-1 text-primary"></i>
            </div>

            <h5 class="card-title fw-bold">{{$price->name}}</h5>
            <h2 class="fw-bold my-3">${{$price->price}} 
              <small class="text-muted fs-6">/month</small>
            </h2>

            <div class="text-muted mb-4">
              {!! $price->description !!}
            </div>

            <a href="#" class="btn btn-gradient w-100 py-2 rounded-pill fw-semibold">
              Sign Up
            </a>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>


@endsection