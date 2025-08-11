@extends('frontend.secure.user_master')

@section('user')
<div class="page-content">
  <div class="container-fluid">
    <div class="row pageDetail">
      <div class="col-12">
        <div class="card">
          <div class="card-header sayfaBaslik">
            İstatistikler
          </div>
          <div class="card-body">

            <a href="{{ route('statistics', $tenant_id) }}" method="GET" >
              <i class="fas fa-industry"></i> 
            </a>

          </div>
          </div>
          </div>
          </div>
          </div>
          </div>

@endsection