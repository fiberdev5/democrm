<!-- <section id="projects" class="projects">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2>Usage Areas</h2>
        </div>

        <div class="portfolio-isotope" data-portfolio-filter="*" data-portfolio-layout="masonry" data-portfolio-sort="original-order">


          <div class="row gy-4 portfolio-container" data-aos="fade-up" data-aos-delay="200">
            @foreach($products as $item)
            <div class="col-lg-4 col-md-6 portfolio-item filter-construction">
              <div class="portfolio-content h-100">
                <img src="{{asset($item->image)}}" class="img-fluid" alt="">
                <a class="link-tik" href="{{route('product.details', $item->slug)}}">
                  <div class="portfolio-info">
                    <div class="urun"><p>{{$item->title}}</p></div>
                  </div>
                </a>
              </div>
            </div>
            @endforeach
          </div>
        </div>
      </div>
</section> -->

<section>
  <div class="container-xxl urunlerWrap pt-3 pb-3">
    <div class="container" data-aos="fade-up">
      <div class="section-header">
        <h2>Usage Areas</h2>
      </div>
      <div class="row g-4 project-carousel wow fadeInUp" data-wow-delay="0.1s">
        @foreach($products as $item)
          <div class="col-lg-3 col-md-6 col-sm-6 wow fadeInUp">
            <div class="project-item">
              <div class="position-relative">
                <img class="img-fluid" src="{{asset($item->image)}}" alt="">
                <a class="project-overlay" href="{{route('product.details', $item->slug)}}">
                  <button class="btn btn-lg-square btn-light m-1" type="button"><i class="fa fa-link"></i> Review</button>
                </a>
              </div>
              <a class="d-block p-4 m-0 h5" href="{{route('product.details', $item->slug)}}" style="font-size:medium">{{$item->title}}</a>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

<section>
  <div class="demo">
    <div class="container" data-aos="fade-up">
      <div class="section-header">
        <h2>Pricing</h2>
      </div>
      <div class="row">
        @foreach($pricing as $price)
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