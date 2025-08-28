@extends('frontend.main_master')
@section('main')

<div class="container d-flex flex-column align-items-center justify-content-center" style="min-height:70vh;">
    <div class="card shadow-lg p-5 text-center" style="border-radius: 1rem;">
        <h1 class="text-success mb-4">🎉 Teşekkürler!</h1>
        <p class="fs-5">Kayıt işleminiz başarıyla tamamlandı.</p>
        <p class="text-muted">Artık sistemimizi kullanmaya başlayabilirsiniz.</p>
        
        <a href="{{ url('/') }}" class="btn btn-gradient mt-4">Anasayfaya Dön</a>
    </div>
</div>

@endsection
