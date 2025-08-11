@extends('frontend.secure.user_master')
@section('user')
<div class="page-content">
  <div class="container-fluid">
    <div class="card">
      <div class="card-header sayfaBaslik d-flex justify-content-between align-items-center">
        <span>Anket İstatistikleri</span>
        <div class="d-flex gap-2">
          <input type="text" class="form-control form-control-sm me-1 datepicker" id="start_date" value="{{ date('d/m/Y', strtotime('-7 days')) }}">
          <input type="text" class="form-control form-control-sm me-2 datepicker" id="end_date" value="{{ date('d/m/Y') }}">
          <button id="filterBtn" class="btn btn-sm btn-primary">Filtrele</button>
        </div>
      </div>

@endsection
