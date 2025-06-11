<section id="alt-services" class="alt-services">
  <div class="container" data-aos="fade-up">
    <div class="row justify-content-around gy-4">
      <div class="col-lg-12 d-flex flex-column justify-content-center">
        <h3>{{$home_about->question}}</h3>
        <p>{!! $home_about->description !!}</p>
        <div class="row">
          <div class="col-lg-3">              
            <div class="icon-box d-flex position-relative " data-aos="fade-up" data-aos-delay="100">
              <div class="box">
                <i class="bi bi-people-fill float-left"></i>
                <h4><a class="stretched-link">Customer Management</a></h4>
                <p class="">You can list all your customers on single screen,categorize them and detailed searches.</p>
              </div>
            </div><!-- End Icon Box -->
          </div>           
          <div class="col-lg-3">
            <div class="icon-box d-flex position-relative" data-aos="fade-up" data-aos-delay="200">
              <div class="box">
                <i class="bi bi-bar-chart-fill float-left"></i>
                <h4><a  class="stretched-link">Staff Management</a></h4>
                <p>You can list all your personnel on a single screen, divide them into groups and perform detailed searches.</p>
              </div>
            </div><!-- End Icon Box -->
          </div>
          <div class="col-lg-3">               
            <div class="icon-box d-flex position-relative " data-aos="fade-up" data-aos-delay="100">
              <div class="box">
                <i class="bi bi-briefcase-fill float-left"></i>
                <h4><a class="stretched-link">Cash Tracking</a></h4>
                <p class="">You can list all your cash transactions on a single screen and list your income and expenses in detail</p>
              </div>
            </div><!-- End Icon Box -->
          </div>
          <div class="col-lg-3">               
            <div class="icon-box d-flex position-relative " data-aos="fade-up" data-aos-delay="100">
              <div class="box">
                <i class="bi bi-box2-fill float-left"></i>
                <h4><a class="stretched-link">Stock Management</a></h4>
                <p class="">You can list all your stock products on a single screen, separate them by device and brand, and search.</p>
              </div>
            </div><!-- End Icon Box -->
          </div>
        </div>
      </div>
    </div>
  </div>
</section><!-- End Alt Services Section -->

<section class="">
  <div class="container" data-aos="fade-up">
    <div class="col-lg-12 d-flex flex-column justify-content-center">
    <div class="section-header" >
          <h2>Advanced Service Management</h2>
        </div>
      <p>{!! $home_about->description !!}</p>
      <div class="row">
        <div class="col-lg-6">
		      <div class="accordion" id="accordionPanelsStayOpenExample">
            @foreach($faqs as $faq)
            <div class="accordion-item">
              <h2 class="accordion-header" id="panelsStayOpen-heading{{$faq->id}}">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse{{$faq->id}}" aria-expanded="true" aria-controls="panelsStayOpen-collapse{{$faq->id}}">
                  {{$faq->name}}
                </button>
              </h2>
              <div id="panelsStayOpen-collapse{{$faq->id}}" class="accordion-collapse collapse {{$faq->job == '1' ? 'show' : ''}}" aria-labelledby="panelsStayOpen-heading{{$faq->id}}">
                <div class="accordion-body">
                  {!! $faq->message !!}
                </div>
              </div>
            </div>
            @endforeach           
          </div>
        </div>
        <div class="col-lg-6">
          <iframe class="desktopVideo" width="90%" height="330" src="{{asset('frontend/img/serbiscrm.mp4')}}" title="SERBİS - Servis Bilişim Sistemleri Teknik Servis Programı" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen=""></iframe>
        </div>
      </div>
    </div>
  </div>
</section>

   