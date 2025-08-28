
<h2>1. Firma Bilgileri</h2>

<form method="POST" action="{{ route('firma.register.step1.post') }}">
    @csrf
    <input type="text" name="firma_adi" placeholder="Firma Adı" value="{{ old('firma_adi') }}"><br>
    @error('firma_adi') <small>{{ $message }}</small><br> @enderror

    <input type="text" name="yetkili" placeholder="Yetkili" value="{{ old('yetkili') }}"><br>
    @error('yetkili') <small>{{ $message }}</small><br> @enderror

    <input type="text" name="telefon" placeholder="Telefon" value="{{ old('telefon') }}"><br>
    @error('telefon') <small>{{ $message }}</small><br> @enderror

    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}"><br>
    @error('email') <small>{{ $message }}</small><br> @enderror

    <button type="submit">Devam Et</button>
</form>