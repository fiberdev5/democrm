<h2>2. SMS Doğrulama</h2>

@if(session('status'))
    <p>{{ session('status') }}</p>
@endif

<form method="POST" action="{{ route('firma.register.sms.post') }}">
    @csrf
    <input type="text" name="sms_code" placeholder="SMS Kodunu Giriniz"><br>
    @error('sms_code') <small>{{ $message }}</small><br> @enderror

    <button type="submit">Doğrula</button>
</form>