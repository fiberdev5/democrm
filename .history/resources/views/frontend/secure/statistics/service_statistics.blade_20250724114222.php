@extends('frontend.secure.user_master')

@section('user')
<div class="page-content">
  <div class="container-fluid">
    <div class="row pageDetail">
      <div class="col-12">
        <div class="card">
          <div class="card-header sayfaBaslik">
            Depo Stoklar
          </div>
          <div class="card-body">

            <a data-bs-toggle="modal" data-bs-target="#addStockModal" class="btn btn-success btn-sm addStock">
              <i class="fas fa-plus"></i> <span>Ürün Ekle</span>
            </a>
            <a href="{{ route('consignmentdevice', $firma->id) }}"  data-bs-target="#supplierModal" class="btn btn-info btn-sm supplierBtn">
              <i class="fas fa-industry"></i> Konsinye Cihazlar 
            </a>

          </div>
          </div>
          </div>
          </div>
          </div>
          </div>

@endsection