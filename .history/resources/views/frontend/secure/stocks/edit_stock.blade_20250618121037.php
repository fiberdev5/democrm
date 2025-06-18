<form method="POST" id="editStockForm" action="{{ route('stok.update', [$firma->id, $stock->id]) }}">
  @csrf
  <div class="row mb-2">
    <label class="col-sm-4">Marka</label>
    <div class="col-sm-8">
      <select name="marka_id" class="form-select" required>
        <option value="" disabled selected>Seçiniz</option>
        @foreach($brands as $brand)
          <option value="{{ $brand->id }}" {{ $stock->marka_id == $brand->id ? 'selected' : '' }}>
            {{ $brand->brandName }}
          </option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Cihaz</label>
    <div class="col-sm-8">
      <select name="cihaz_id" class="form-select" required>
        <option value="" disabled selected>Seçiniz</option>
        @foreach($types as $type)
          <option value="{{ $type->id }}" {{ $stock->cihaz_id == $type->id ? 'selected' : '' }}>
            {{ $type->deviceType }}
          </option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Raf</label>
    <div class="col-sm-8">
      <select name="urunDepo" class="form-select" required>
        <option value="" disabled selected>Seçiniz</option>
        @foreach($shelves as $shelf)
          <option value="{{ $shelf->id }}" {{ $stock->urunDepo == $shelf->id ? 'selected' : '' }}>
            {{ $shelf->name }}
          </option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Ürün Adı</label>
    <div class="col-sm-8">
      <input type="text" name="urunAdi" class="form-control" value="{{ $stock->urunAdi }}" required>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Ürün Kodu</label>
    <div class="col-sm-8">
      <input type="text" name="urunKodu" class="form-control" value="{{ $stock->urunKodu }}">
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Adet</label>
    <div class="col-sm-8">
      <input type="number" name="adet" class="form-control" value="{{ $stock->adet }}" required>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Fiyat</label>
    <div class="col-sm-8">
      <input type="text" name="fiyat" class="form-control" value="{{ $stock->fiyat }}">
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-4">Açıklama</label>
    <div class="col-sm-8">
      <textarea name="aciklama" class="form-control" rows="2">{{ $stock->aciklama }}</textarea>
    </div>
  </div>

  <div class="text-end">
    <input type="submit" class="btn btn-info btn-sm" value="Kaydet">
  </div>
</form>
