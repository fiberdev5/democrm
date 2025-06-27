<ul class="nav nav-pills" role="tablist" style="margin-bottom: 5px;">
  <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#tab1" data-id="{{ $consignmentDevice->id }}" role="tab">Ürün Bilgileri</a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#tab2" data-id="{{ $consignmentDevice->id }}" role="tab">Stok Hareketleri</a></li>
  <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#tab3" data-id="{{ $consignmentDevice->id }}" role="tab">Fotoğraflar</a></li>
</ul>

<div class="tab-content">
  <div id="tab1" class="tab-pane active">
    {{-- Konsinye cihaz bilgileri formu --}}
    <div class="tab-content">
  <div id="tab1" class="tab-pane active" style="padding: 0">
    <form method="POST" id="editConsignmentDevice" action="{{ route('update.consignment.device', [$firma->id, $consignmentDevice->id]) }}">
      @csrf
      
      <div class="row mb-2">
        <label class="col-sm-4">Markalar</label>
        <div class="col-sm-8">
          <select name="marka_id" class="form-control form-select" required>
            <option value="" disabled>Seçiniz</option>
            @foreach($markalar as $marka)
              <option value="{{ $marka->id }}" {{ $consignmentDevice->marka_id == $marka->id ? 'selected' : '' }}>
                {{ $marka->marka }}
              </option>
            @endforeach
          </select>
        </div>
      </div>
      
      <div class="row mb-2">
        <label class="col-sm-4">Cihaz Türleri</label>
        <div class="col-sm-8">
          <select name="cihaz_id" class="form-control form-select" required>
            <option value="" disabled>Seçiniz</option>
            @foreach($cihazlar as $cihaz)
              <option value="{{ $cihaz->id }}" {{ $consignmentDevice->cihaz_id == $cihaz->id ? 'selected' : '' }}>
                {{ $cihaz->cihaz }}
              </option>
            @endforeach
          </select>
        </div>
      </div>
    
      
      <div class="row mb-2">
        <label class="col-sm-4">Raf Seç</label>
        <div class="col-sm-8">
          <select name="raf_id" class="form-control form-select" required>
            <option value="" disabled>Seçiniz</option>
            @foreach($rafListesi as $raf)
              <option value="{{ $raf->id }}" {{ $consignmentDevice->raf_id == $raf->id ? 'selected' : '' }}>
                {{ $raf->raf_adi }}
              </option>
            @endforeach
          </select>
        </div>
      </div>
      
      <div class="row mb-2">
        <label class="col-sm-4">Ürün Adı</label>
        <div class="col-sm-8">
          <input type="text" name="urunAdi" class="form-control" value="{{ $consignmentDevice->urunAdi }}" required>
        </div>
      </div>

<div class="row mb-2">
  <label class="col-sm-4">Ürün Kodu</label>
  <div class="col-sm-8">
    <div class="d-flex align-items-center">
      <input type="text" name="urunKodu" class="form-control @error('urunKodu') is-invalid @enderror me-2" value="{{ old('urunKodu', $consignmentDevice->urunKodu) }}">
      <a href="{{ route('consignment.device.barcode.pdf', [$firma->id, $consignmentDevice->id]) }}" target="_blank" class="btn btn-warning btn-sm text-nowrap">
        Barkodu Yazdır
      </a>
    </div>
    @error('urunKodu')
      <div class="invalid-feedback d-block">
        {{ $message }}
      </div>
    @enderror
  </div>
</div>
      
      <div class="row mb-2">
        <label class="col-sm-4">Satış Fiyatı</label>
        <div class="col-sm-8">
          <div class="row g-2">
            <div class="col-8">
              <input name="fiyat" type="number" min="0" step="0.01" class="form-control" value="{{ $consignmentDevice->fiyat }}" required>
            </div>
            <div class="col-4">
              <select name="fiyatBirim" class="form-select" required>
                <option value="" disabled>Birim</option>
                <option value="1" {{ $consignmentDevice->fiyatBirim == 1 ? 'selected' : '' }}>TL</option>
                <option value="2" {{ $consignmentDevice->fiyatBirim == 2 ? 'selected' : '' }}>USD</option>
                <option value="3" {{ $consignmentDevice->fiyatBirim == 3 ? 'selected' : '' }}>EUR</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row mb-3">
        <label class="col-sm-4">Açıklama</label>
        <div class="col-sm-8">
          <textarea name="aciklama" class="form-control" rows="2">{{ $consignmentDevice->aciklama }}</textarea>
        </div>
      </div>
      
      {{-- Stok bilgileri (toplam, personel stokları) --}}
      @php
        $toplamGiris = \App\Models\StockAction::where('stokId', $consignmentDevice->id)->where('islem', 1)->sum('adet');
        $toplamCikis = \App\Models\StockAction::where('stokId', $consignmentDevice->id)->whereIn('islem', [2, 3])->sum('adet');
        $kalanStok = $toplamGiris - $toplamCikis;
        $personelAdet = \App\Models\PersonelStock::where('stokId', $consignmentDevice->id)->sum('adet');
      @endphp

      <div class="row mb-2">
        <label class="col-sm-4 col-form-label">Stok</label>
        <div class="col-sm-8 d-flex flex-wrap justify-content-between">
          <div class="stok-info-box">Toplam: {{ $kalanStok }} Adet</div>
          <div class="stok-info-box">Personel: {{ $personelAdet }} Adet</div>
        </div>
      </div>
      
      <div class="text-end">
        <input type="submit" class="btn btn-info btn-sm" value="Kaydet">
      </div>
    </form>
  </div>
  <div id="tab2" class="tab-pane fade"></div>
  <div id="tab3" class="tab-pane fade"></div>
</div>

<script>
  $(document).ready(function(){
    var originalTab1Content = $('#tab1').html(); // ilk içeriği al

    $('#editConsignmentDevice').submit(function(e){
      var stokId = $(this).data('bs-id');
      var urunKodu = $('input[name="urunKodu"]').val().trim();
      if(urunKodu.length !== 13){
        e.preventDefault();
        alert('Ürün kodu tam 13 haneli olmalıdır!');
        $('input[name="urunKodu"]').focus();
        return false;
      }
    });

    $(".nav-link[href='#tab2']").click(function() {
      var id = $(this).data('id');
      var tenant_id = {{ $firma->id }};
      $.ajax({
        url: "/" + tenant_id + "/stok-konsinye-hareketleri/" + id
      }).done(function(data) {
        $('#tab2').html(data);
      });
    });

    $(".nav-link[href='#tab3']").click(function() {
      var id = $(this).data('id');
      var tenant_id = {{ $firma->id }};
      $.ajax({
        url: "/" + tenant_id + "/stok-konsinye-fotograflar/" + id
      }).done(function(data) {
        $('#tab3').html(data);
      });
    });

    $(".nav-link[href='#tab1']").click(function() {
      $('#tab1').html(originalTab1Content); // ilk halini geri koy
    });
  });
</script>
