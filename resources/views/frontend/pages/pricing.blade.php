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

<section>
    <div class="demo">
    <div class="container" data-aos="fade-up">
        <div class="section-header">
          <h2>Pricing</h2>
        </div>
        <div class="row">
            @foreach($prices as $price)
            <div class="col-md-4 col-sm-6">
                <div class="pricingTable {{$price->color}}">
                    <div class="pricingTable-header">
                        <i class="{{$price->icon}}"></i>
                        <div class="price-value"> ${{$price->price}} <span class="month">per month</span> </div>
                    </div>
                    <h3 class="heading">{{$price->name}}</h3>
                    <div class="pricing-content">
                        {!! $price->description !!}
                    </div>
                    <div class="pricingTable-signup">
                        <a href="#">sign up</a>
                    </div>
                </div>
            </div>
            @endforeach
           
        </div>
    </div>
</div>
</section>

@endsection