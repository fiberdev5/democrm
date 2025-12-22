@extends('frontend.secure.user_master')
@section('user')

<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="text-center bg-white p-5 rounded shadow" style="max-width: 600px; width: 100%;">
        <img src="{{ asset('images/expired.png') }}" alt="Abonelik Bitti" style="width: 120px; margin-bottom: 20px;">
        <h2 class="mb-3" style="font-weight: bold; color: #d9534f;">Abonelik Süreniz Doldu</h2>
        <p class="mb-4" style="color: #555;">
            {{ auth()->user()->tenant->firma_adi }} firması için tanımlı abonelik süresi sona ermiştir. Sistemi kullanmaya devam edebilmek için lütfen aboneliğinizi yenileyin.
        </p>
        <a href="" class="btn btn-primary btn-lg">
            Aboneliği Yenile
        </a>
        <p class="mt-3 text-muted" style="font-size: 14px;">
            Sorularınız için <a href="">iletişim</a> sayfasından bize ulaşabilirsiniz.
        </p>
    </div>
</div>
@endsection
