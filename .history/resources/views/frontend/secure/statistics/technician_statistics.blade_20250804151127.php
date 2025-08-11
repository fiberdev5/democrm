@extends('frontend.secure.user_master')
@section('user')
<div class="page-content servis-istatistik">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
      
    </div>
</div>
@endsection