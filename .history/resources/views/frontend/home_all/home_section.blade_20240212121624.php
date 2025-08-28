 <!-- ======= Get Started Section ======= -->
 <section id="get-started" class="get-started">
      <div class="container">

        <div class="row justify-content-between gy-4">

          <div class="col-lg-5" data-aos="fade">
            <form action="{{route('store.message')}}" method="post" class="php-email-form">
              @csrf

              @if(Session::has("success"))
                <div class="alert alert-success alert-dismissible"><button type="button" class="close">&times;</button>{{Session::get('success')}}</div>
              @elseif(Session::has("failed"))
                <div class="alert alert-danger alert-dismissible"><button type="button" class="close">&times;</button>{{Session::get('failed')}}</div>
              @endif
              <h3>Contact Us</h3>
              <p>If you have any questions, suggestions or requests about our program, you can fill out our contact form below.</p>
              <div class="row gy-3">

               
                  <div class="col-md-6">
                    <input type="text" name="name" class="form-control" placeholder="Name" required>
                  </div>

                  <div class="col-md-6 ">
                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                  </div>

                <div class="col-md-12">
                  <input type="text" class="form-control" name="phone" placeholder="Phone" required>
                </div>

                <div class="col-md-12">
                  <textarea class="form-control" name="message" rows="6" placeholder="Message" required></textarea>
                </div>

                <div class="col-md-12 text-center">
                  <div class="loading">Loading</div>
                  <div class="error-message"></div>
                  <div class="sent-message">Your quote request has been sent successfully. Thank you!</div>

                  <button type="submit">Send</button>
                </div>

              </div>
            </form>
          </div><!-- End Quote Form -->
          <div class="col-lg-6 d-flex " >
            <div class="content iletisim-content">
              <h2>For More Informations</h2>
              <h3>{{$home_section->title}}</h3>
              <p>{!! $home_section->description !!}</p>
              <a data-aos="fade-up" data-aos-delay="200" href="tel:{{$settings->company_phone}}" class="btn-get-started">Contact Us</a>
            </div>
          </div>

          

        </div>

      </div>
    </section><!-- End Get Started Section -->