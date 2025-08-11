@extends('frontend.secure.user_master')
@section('user')

<div class="page-content" >
  <div class="container-fluid">
    <div class="row anasayfa-card">
      <div class="col-xl-3 col-md-6 col-sm-6 col-6">
        <div class="card">
          <a href="" class="card-body">
            <div class="d-flex">
              <div class="flex-grow-1">
                <p class="text-truncate font-size-14 text-body mb-2">Toplam Servis Sayısı</p>
                <h4 class="mb-0">1</h4>
              </div>
              <div class="avatar-sm mt-1">
                <span class="avatar-title bg-light text-primary rounded-3">
                <i class="ri-mail-open-line font-size-24"></i>
                </span>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 col-sm-6 col-6">
        <div class="card">
          <a href="" class="card-body">
            <div class="d-flex">
              <div class="flex-grow-1">
                <p class="text-truncate font-size-14 text-body mb-2">Müşteri Sayısı</p>
                <h4 class="mb-0">1</h4>
              </div>
              <div class="avatar-sm mt-1">
                <span class="avatar-title bg-light text-primary rounded-3">
                <i class="ri-team-line font-size-24"></i>
                </span>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 col-sm-6 col-6">
        <div class="card">
          <a href="" class="card-body">
            <div class="d-flex">
              <div class="flex-grow-1">
                <p class="text-truncate font-size-14 text-body mb-2">Personel Sayısı</p>
                <h4 class="mb-0">5</h4>
              </div>
              <div class="avatar-sm mt-1">
                <span class="avatar-title bg-light text-primary rounded-3">
                <i class="ri-pencil-line font-size-24"></i>
                </span>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 col-sm-6 col-6">
        <div class="card">
          <a href="" class="card-body">
            <div class="d-flex">
              <div class="flex-grow-1">
                <p class="text-truncate font-size-14 text-body mb-2">Kasa</p>
                <h4 class="mb-0">12</h4>
              </div>
              <div class="avatar-sm mt-1">
                <span class="avatar-title bg-light text-primary rounded-3">
                <i class="ri-message-3-line font-size-24"></i>
                </span>
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>
    
      <div class="col-md-6 col-lg-6 col-xl-6 center"></div>
      
      <div class="col-md-12 col-lg-12 col-xl-12 calendarCardWrap-right">
        <div class="card mb-3 calendarCardNumbers">
            <div class="card-header" style="padding: 5px!important;">
               <i class="fas fa-chart-area"></i> Servis Sayıları
            </div>
            <div class="card-body"> 
                <div class="row">      
                    <div class="col-md-4">                          
                        <div class=" digerSayilar">
                        <a class="card text-white bg-info o-hidden" href="">
                            <div class="card-body">
                            <div class="card-body-icon">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                            <div> <strong>Bugün</strong> Alınan Servis Sayısı (0) </div>
                            </div>
                        </a>
                        </div>
                    </div>

                    <div class="col-md-4">                          
                        <div class=" digerSayilar">
                        <a class="card text-white bg-info o-hidden" href="">
                            <div class="card-body">
                            <div class="card-body-icon">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                            <div> <strong>Bugün</strong> Alınan Servis Sayısı (0) </div>
                            </div>
                        </a>
                        </div>
                    </div>

                    <div class="col-md-4">                          
                        <div class=" digerSayilar">
                        <a class="card text-white bg-info o-hidden" href="">
                            <div class="card-body">
                            <div class="card-body-icon">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                            <div> <strong>Bugün</strong> Alınan Servis Sayısı (0) </div>
                            </div>
                        </a>
                        </div>
                    </div>
                </div>     
            </div>
        </div> 
      </div>
    </div>
  </div>
</div>



@endsection
