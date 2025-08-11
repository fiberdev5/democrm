@extends('frontend.secure.user_master')
@section('user')

@section('content')
<div class="container text-center">
    <h2>Abonelik Süreniz Dolmuştur</h2>
    <p>Lütfen devam etmek için aboneliğinizi yenileyin.</p>
    <a href="{{ route('billing') }}" class="btn btn-primary">Aboneliği Yenile</a>
</div>
@endsection
