<section id="hero" class="hero">
  <div id="hero-carousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
    @php
      $slideSay = 0;
    @endphp

    @foreach($slide as $slider)
      @php
        $slideSay++;
      @endphp

      <div class="carousel-item {{ $slideSay == 1 ? 'active' : '' }}" style="background-image: url({{ asset($slider->home_image) }})">
        <div class="info d-flex align-items-center">
          <div class="container">
            <div class="row justify-content-start">
              <div class="col-lg-7 text-start">
                <p data-aos="fade-up text-white">{{ $slider->title }}</p>
                <h2 data-aos="fade-down text-white animated slideInRight">{{ $slider->sub_title }}</h2>
                <a data-aos="fade-up" data-aos-delay="200" href="tel:{{$settings->company_phone}}" class="btn-get-started">Book a Demo</a>
                <div class="play-store"><a href="" target="_blank" class="custom-btn d-inline-flex align-items-center"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M325.3 234.3L104.6 13l280.8 161.2-60.1 60.1zM47 0C34 6.8 25.3 19.2 25.3 35.3v441.3c0 16.1 8.7 28.5 21.7 35.3l256.6-256L47 0zm425.2 225.6l-58.9-34.1-65.7 64.5 65.7 64.5 60.1-34.1c18-14.3 18-46.5-1.2-60.8zM104.6 499l280.8-161.2-60.1-60.1L104.6 499z"></path></svg><p>Get it on<span>Google Play</span></p></a>
                <a href="" target="_blank" class="custom-btn d-inline-flex align-items-center"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 384 512" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M318.7 268.7c-.2-36.7 16.4-64.4 50-84.8-18.8-26.9-47.2-41.7-84.7-44.6-35.5-2.8-74.3 20.7-88.5 20.7-15 0-49.4-19.7-76.4-19.7C63.3 141.2 4 184.8 4 273.5q0 39.3 14.4 81.2c12.8 36.7 59 126.7 107.2 125.2 25.2-.6 43-17.9 75.8-17.9 31.8 0 48.3 17.9 76.4 17.9 48.6-.7 90.4-82.5 102.6-119.3-65.2-30.7-61.7-90-61.7-91.9zm-56.6-164.2c27.3-32.4 24.8-61.9 24-72.5-24.1 1.4-52 16.4-67.9 34.9-17.5 19.8-27.8 44.3-25.6 71.9 26.1 2 49.9-11.4 69.5-34.3z"></path></svg>
                  <p>Available on the<span>App Store</span></p>
                  </a>
              </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endforeach

    <a class="carousel-control-prev" href="#hero-carousel" role="button" data-bs-slide="prev">
      <span class="carousel-control-prev-icon bi bi-chevron-left" aria-hidden="true"></span>
    </a>

    <a class="carousel-control-next" href="#hero-carousel" role="button" data-bs-slide="next">
      <span class="carousel-control-next-icon bi bi-chevron-right" aria-hidden="true"></span>
    </a>
  </div>

</section><!-- End Hero Section -->