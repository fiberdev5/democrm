<ul class="nav nav-pills " role="tablist" style="margin-bottom: 5px;">
  <li class="nav-item" style="font-size: 14px;"><a class="nav-link nav1 active" data-bs-toggle="pill" href="#tab1" data-id="{{$stock->id}}" role="tab">Ürün Bilgileri</a></li>
  <li class="nav-item" style="font-size: 14px;"><a class="nav-link nav2" data-bs-toggle="pill" href="#tab2" data-id="{{$stock->id}}" role="tab">Stok Haraketleri</a></li>
  <li class="nav-item" style="font-size: 14px;"><a class="nav-link nav2" data-bs-toggle="pill" href="#tab2" data-id="{{$stock->id}}" role="tab">Personel Stokları</a></li>
  <li class="nav-item" style="font-size: 14px;"><a class="nav-link nav2" data-bs-toggle="pill" href="#tab2" data-id="{{$stock->id}}" role="tab">Fotoğraflar</a></li>
</ul>

<div class="tab-content">
  <div id="tab1" class="tab-pane active" style="padding: 0">
<form method="POST" id="editStock" action="{{ route('update.stock', [$firma->id, $stock->id]) }}">
  @csrf

  <div class="row mb-2">
    <label class="col-sm-4">Markalar</label>
    <div class="col-sm-8">
      <select name="marka_id" class="form-select" required>
        <option value="" disabled>Seçiniz</option>
        @foreach($markalar as $marka)
          <option value="{{ $marka->id }}" {{ $stock->stok_marka == $marka->id ? 'selected' : '' }}>
            {{ $marka->marka }}
          </option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Cihaz Türleri</label>
    <div class="col-sm-8">
      <select name="cihaz_id" class="form-select" required>
        <option value="" disabled>Seçiniz</option>
        @foreach($cihazlar as $cihaz)
          <option value="{{ $cihaz->id }}" {{ $stock->stok_cihaz == $cihaz->id ? 'selected' : '' }}>
            {{ $cihaz->cihaz }}
          </option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Raf</label>
    <div class="col-sm-8">
      <select name="raf_id" class="form-select" required>
        <option value="" disabled>Seçiniz</option>
        @foreach($rafListesi as $shelf)
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
      <input type="number" name="adet" class="form-control" value="{{ optional($stock->sonHareket)->adet ?? '' }}" required>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Fiyat</label>
    <div class="col-sm-8">
      <input type="text" name="fiyat" class="form-control" value="{{ optional($stock->sonHareket)->fiyat ?? '' }}">
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

  </div>
  </div>