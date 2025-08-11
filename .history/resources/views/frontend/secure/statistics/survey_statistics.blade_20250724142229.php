@extends('frontend.secure.user_master')
@section('user')
<div class="page-content">
  <div class="container-fluid">
     @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
    <div class="card">
      <div class="card-header sayfaBaslik d-flex justify-content-between align-items-center">
        <span>Anket İstatistikleri</span>
        <div class="d-flex gap-2">
        </div>
      </div>
@endsection
