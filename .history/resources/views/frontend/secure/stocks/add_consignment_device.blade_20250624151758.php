

    <form action="{{ route('store.consignment.device', $tenant_id) }}" method="POST">
      @csrf

      <div class="mb-3">
        <label>Ürün Adı <span class="text-danger">*</span></label>
        <input type="text" name="urunAdi" class="form-control" value="{{ old('urunAdi') }}" required>
      </div>

      <div class="mb-3">
        <label>Ürün Kodu <span class="text-danger">*</span></label>
        <input type="text" name="urunKodu" class="form-control" value="{{ old('urunKodu') }}" required>
        <small class="text-danger">Ürün kodu tam 13 haneli olmalıdır.</small>
        @error('urunKodu') <div class="text-danger">{{ $message }}</div> @enderror
      </div>

      <div class="mb-3">
        <label>Kategori <span class="text-danger">*</span></label>
        <select name="urunKategori" class="form-select" required>
          @foreach($kategoriler as $kategori)
            <option value="{{ $kategori->id }}" {{ old('urunKategori') == $kategori->id ? 'selected' : '' }}>
              {{ $kategori->kategori_adi }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label>Raf</label>
        <select name="raf_id" class="form-select">
          <option value="">Seçiniz</option>
          @foreach($rafListesi as $raf)
            <option value="{{ $raf->id }}" {{ old('raf_id') == $raf->id ? 'selected' : '' }}>{{ $raf->raf_adi }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label>Marka</label>
        <select name="marka_id" class="form-select">
          <option value="">Seçiniz</option>
          @foreach($markalar as $marka)
            <option value="{{ $marka->id }}" {{ old('marka_id') == $marka->id ? 'selected' : '' }}>{{ $marka->marka }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label>Cihaz</label>
        <select name="cihaz_id" class="form-select">
          <option value="">Seçiniz</option>
          @foreach($cihazlar as $cihaz)
            <option value="{{ $cihaz->id }}" {{ old('cihaz_id') == $cihaz->id ? 'selected' : '' }}>{{ $cihaz->cihaz }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label>Açıklama</label>
        <textarea name="aciklama" class="form-control">{{ old('aciklama') }}</textarea>
      </div>

      <div class="mb-3">
        <label>Adet <span class="text-danger">*</span></label>
        <input type="number" name="adet" class="form-control" value="{{ old('adet') ?? 1 }}" min="1" required>
      </div>

      <div class="mb-3">
        <label>Fiyat</label>
        <input type="text" name="fiyat" class="form-control" value="{{ old('fiyat') }}">
      </div>

      <div class="mb-3">
        <label>Fiyat Birimi</label>
        <input type="text" name="fiyatBirim" class="form-control" value="{{ old('fiyatBirim') ?? '₺' }}">
      </div>

      <button type="submit" class="btn btn-primary">Kaydet</button>
      <a href="{{ route('consignmentdevice', $tenant_id) }}" class="btn btn-secondary">Geri</a>
    </form>




