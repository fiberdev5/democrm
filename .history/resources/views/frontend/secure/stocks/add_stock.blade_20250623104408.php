<!-- Modal -->
<div class="modal fade" id="addStockModal" tabindex="-1" aria-labelledby="addStockModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <form method="post" id="addStock" action="{{ route('store.stock', $firma->id) }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="addStockModalLabel">Stok Ekle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
        </div>

        <div class="modal-body">
          <!-- Form içeriğin aynen buraya yapıştır -->
          <div class="row mb-2">
            <label class="col-sm-4 col-form-label">Markalar</label>
            <div class="col-sm-8">
              <select name="marka_id" class="form-select" required>
                <option value="" disabled selected>- Seçiniz -</option>
                @foreach($markalar as $marka)
                  <option value="{{ $marka->id }}">{{ $marka->marka }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <!-- Diğer alanlar da aynı şekilde devam eder -->
          <div class="row mb-2">
            <label class="col-sm-4 col-form-label">Cihaz Türleri</label>
            <div class="col-sm-8">
              <select name="cihaz_id" class="form-select" required>
                <option value="" disabled selected>- Seçiniz -</option>
                @foreach($cihazlar as $cihaz)
                  <option value="{{ $cihaz->id }}">{{ $cihaz->cihaz }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="row mb-2">
            <label class="col-sm-4 col-form-label">Raf Seç</label>
            <div class="col-sm-8">
              <select name="raf_id" class="form-select" required>
                <option value="" disabled selected>- Seçiniz -</option>
                @foreach($rafListesi as $raf)
                  <option value="{{ $raf->id }}">{{ $raf->raf_adi }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="row mb-2">
            <label class="col-sm-4 col-form-label">Ürün Kodu<span class="text-danger">*</span></label>
            <div class="col-sm-8">
              <input name="urunKodu" type="text" class="form-control" required>
            </div>
          </div>

          <div class="row mb-2">
            <label class="col-sm-4 col-form-label">Ürün Adı<span class="text-danger">*</span></label>
            <div class="col-sm-8">
              <input name="urunAdi" type="text" class="form-control" required>
            </div>
          </div>

          <div class="row mb-2">
            <label class="col-sm-4 col-form-label">Adet <span class="text-danger">*</span></label>
            <div class="col-sm-8">
              <input name="adet" type="number" min="1" class="form-control" required>
            </div>
          </div>

          <div class="row mb-2">
            <label class="col-sm-4 col-form-label">Satış Fiyatı</label>
            <div class="col-sm-8">
              <div class="row g-2">
                <div class="col-8">
                  <input name="fiyat" type="number" min="0" step="0.01" class="form-control" placeholder="Fiyat">
                </div>
                <div class="col-4">
                  <select name="fiyatBirim" class="form-select">
                    <option value="" disabled selected>Birim</option>
                    <option value="1">TL</option>
                    <option value="2">USD</option>
                    <option value="3">EUR</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <label class="col-sm-4 col-form-label">Açıklama</label>
            <div class="col-sm-8">
              <textarea name="aciklama" rows="3" class="form-control"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Kaydet</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
        </div>
      </form>
    </div>
  </div>
</div>
