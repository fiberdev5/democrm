<div class="modal-header py-2">
    <h6 class="modal-title" id="editStockModalLabel">Stok Detayları ve Yönetimi [{{ $stock->urunAdi }}]</h6>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
</div>

<div class="modal-body">
    <div class="row">
        <!-- ============================================= -->
        <!-- ============== SOL SÜTUN (ÜRÜN BİLGİLERİ VE FOTOĞRAFLAR) ============== -->
        <!-- ============================================= -->
        <div class="col-lg-5">
            
            <!-- 1. KART: ÜRÜN BİLGİLERİ -->
            <div class="card mb-3">
                <div class="card-header py-2">
                    <h6 class="mb-0"><i class="bi bi-pencil-square"></i> Ürün Bilgileri</h6>
                </div>
                <div class="card-body py-2 px-3">
                    <form method="POST" id="editStockUnifiedForm" action="{{ route('update.stock', [$firma->id, $stock->id]) }}" class="tight-form">
                        @csrf
                        
                        <div class="row align-items-center">
                            <label class="col-4 col-form-label-sm">Marka</label>
                            <div class="col-8">
                                <select name="marka_id" class="form-select form-select-sm" required>
                                    @foreach($markalar as $marka)
                                    <option value="{{ $marka->id }}" {{ $stock->stok_marka == $marka->id ? 'selected' : '' }}>
                                        {{ $marka->marka }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <label class="col-4 col-form-label-sm">Cihaz</label>
                            <div class="col-8">
                                <select name="cihaz_id" class="form-select form-select-sm" required>
                                    @foreach($cihazlar as $cihaz)
                                    <option value="{{ $cihaz->id }}" {{ $stock->stok_cihaz == $cihaz->id ? 'selected' : '' }}>
                                        {{ $cihaz->cihaz }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <label class="col-4 col-form-label-sm">Kategori</label>
                            <div class="col-8">
                                <select name="urunKategori" class="form-select form-select-sm">
                                    @foreach($kategoriler as $kategori)
                                    <option value="{{ $kategori->id }}" {{ $stock->urunKategori == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->kategori }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <label class="col-4 col-form-label-sm">Raf</label>
                            <div class="col-8">
                                <select name="raf_id" class="form-select form-select-sm" required>
                                    @foreach($rafListesi as $raf)
                                    <option value="{{ $raf->id }}" {{ $stock->urunDepo == $raf->id ? 'selected' : '' }}>
                                        {{ $raf->raf_adi }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="row align-items-center">
                            <label class="col-4 col-form-label-sm">Ürün Adı</label>
                            <div class="col-8">
                                <input type="text" name="urunAdi" class="form-control form-control-sm" value="{{ $stock->urunAdi }}">
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <label class="col-4 col-form-label-sm">Ürün Kodu</label>
                            <div class="col-8">
                                <input type="text" name="urunKodu" class="form-control form-control-sm" value="{{ $stock->urunKodu }}" maxlength="13">
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <label class="col-4 col-form-label-sm">Satış Fiyatı</label>
                            <div class="col-8">
                                <div class="row g-1">
                                    <div class="col-8">
                                        <input name="fiyat" type="number" min="0" step="0.01" class="form-control form-control-sm" placeholder="Fiyat" value="{{ $stock->fiyat }}">
                                    </div>
                                    <div class="col-4">
                                        <select name="fiyatBirim" class="form-select form-select-sm">
                                            <option value="1" {{ $stock->fiyatBirim == 1 ? 'selected' : '' }}>TL</option>
                                            <option value="2" {{ $stock->fiyatBirim == 2 ? 'selected' : '' }}>USD</option>
                                            <option value="3" {{ $stock->fiyatBirim == 3 ? 'selected' : '' }}>EUR</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-start">
                            <label class="col-4 col-form-label-sm">Açıklama</label>
                            <div class="col-8">
                                <textarea name="aciklama" class="form-control form-control-sm" rows="2" placeholder="Ürün açıklaması...">{{ $stock->aciklama }}</textarea>
                            </div>
                        </div>

                        @php
                            $toplamGiris = \App\Models\StockAction::where('stokId', $stock->id)->where('islem', 1)->sum('adet');
                            $toplamCikis = \App\Models\StockAction::where('stokId', $stock->id)->where('islem', 3)->sum('adet');
                            $kalanStok = $toplamGiris - $toplamCikis;
                            $personelAdet = \App\Models\PersonelStock::where('stokId', $stock->id)->sum('adet');
                            $genelToplam = $kalanStok + $personelAdet;
                        @endphp
                        <div class="row align-items-center">
                            <label class="col-4 col-form-label-sm">Stok Durumu</label>
                            <div class="col-8">
                                <div class="alert alert-info py-2 px-2 mb-0">
                                    <small>
                                        <strong>Toplam:</strong> {{ $genelToplam }} Adet<br>
                                        <strong>Personelde:</strong> {{ $personelAdet }} Adet
                                    </small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 2. KART: FOTOĞRAFLAR -->
            <div class="card">
                <div class="card-header py-2">
                    <h6 class="mb-0"><i class="bi bi-images"></i> Fotoğraflar</h6>
                </div>
                <div class="card-body py-2">
                    <form method="POST" id="stokFotoEkle" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-2">
                            <input type="file" class="form-control form-control-sm" name="resim" id="customFile" accept="image/jpeg,image/png">
                            <input type="hidden" name="stock_id" value="{{ $stock->id }}">
                        </div>
                        <div class="imgLoad" style="display: none;">
                            <div class="progress mb-2" style="height: 5px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                            </div>
                        </div>
                    </form>
                    
                    <div class="imgBox">
                        <div class="photo-grid mb-2" id="photoGrid" style="{{ $photos->isEmpty() ? 'display: none;' : '' }}">
                            @foreach($photos as $foto)
                            <div class="photo-item" data-id="{{ $foto->id }}">
                                <img src="{{ Storage::url($foto->resimyol) }}" alt="Stok Fotoğrafı">
                                <button class="btn btn-danger btn-sm photo-delete stokFotoSil" data-id="{{ $foto->id }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="empty-photos" id="emptyPhotos" style="{{ !$photos->isEmpty() ? 'display: none;' : '' }}">
                            <i class="bi bi-camera text-muted" style="font-size: 2rem;"></i>
                            <p class="mb-0 mt-2">
                                <small class="text-muted">
                                    Henüz fotoğraf eklenmemiş.<br>
                                    Yukarıdan fotoğraf yükleyebilirsiniz.
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ============== SAĞ SÜTUN (HAREKETLER) ============== -->
        <!-- ================================================= -->
        <div class="col-lg-7">
            
            <!-- 1. KART: STOK HAREKETLERİ (KAYDIRILABİLİR) -->
            <div class="card mb-3">
                <div class="card-header py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="bi bi-arrows-move"></i> Stok Hareketleri</h6>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-success btn-sm hareketEkleBtn" data-stokid="{{ $stock->id }}">
                                <i class="bi bi-plus-circle"></i> Hareket Ekle
                            </button>
                            <select class="form-select form-select-sm islemSec" name="islemSec" style="width: 120px;">
                                <option value="0">Hepsi</option>
                                <option value="1">Alış</option>
                                <option value="3">Personele Gönder</option>
                                <option value="2">Serviste Kullanım</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body scrollable-card-body p-0">
                    {{-- Stok Hareketleri Tablosu --}}
                     @include('frontend.secure.stocks.partials.stock_movements_table')
                </div>
            </div>

            <!-- 2. KART: PERSONEL STOKLARI (KAYDIRILABİLİR) -->
            <div class="card">
                <div class="card-header py-2">
                    <h6 class="mb-0"><i class="bi bi-people"></i> Personel Stokları</h6>
                </div>
                <div class="card-body scrollable-card-body p-0">
                    {{-- Personel Stokları Tablosu --}}
                    @include('frontend.secure.stocks.partials.personnel_stocks_table')
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer py-2">
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Kapat</button>
    <button type="submit" class="btn btn-info btn-sm" form="editStockUnifiedForm">
        <i class="bi bi-check-circle"></i> Ürün Bilgilerini Kaydet
    </button>
</div>
<!-- Hareket Ekle Modal -->
<div class="modal fade " id="hareketEkleModal" tabindex="-1" aria-labelledby="hareketEkleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="hareketEkleForm" method="POST" action="{{ route('store.stock.action', request()->route('tenant_id')) }}">
      @csrf
      <input type="hidden" name="stok_id" id="modalStokId">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Stok Hareketi Ekle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="islem">İşlem</label>
            <select name="islem" class="form-control" required>
              <option value="">Seçiniz</option>
              <option value="1" selected>Alış</option>
              <option value="3">Personel'e Gönder</option>
            </select>
          </div>
          <div class="mb-2">
            <label>Tedarikçi</label>
            <select name="tedarikci" class="form-control">
              <option value="">Seçiniz</option>
              @foreach(\App\Models\StockSupplier::all() as $tedarikci)
                <option value="{{ $tedarikci->id }}">{{ $tedarikci->tedarikci }}</option>
              @endforeach
            </select>
          </div>

        <div class="mb-2 d-none" id="personelSelectDiv">
          <label>Personel</label>
          <select name="personel" class="form-control">
            <option value="">Seçiniz</option>
            @foreach(\App\Models\User::where('tenant_id', request()->route('tenant_id'))->get() as $personel)
              <option value="{{ $personel->user_id }}">{{ $personel->name }}</option>
            @endforeach
          </select>
        </div>

          <div class="mb-3">
            <label for="adet">Adet</label>
            <input type="number" name="adet" class="form-control" required min="1">
          </div>

          <div class="mb-3" id="fiyatInputDiv">
            <label for="fiyat">Fiyat (TL)</label>
            <input type="text" name="fiyat" class="form-control" required>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Kaydet</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
        </div>
      </div>
      
    </form>
  </div>
</div>

<!-- Personel Stok Detay Modal -->
<div class="modal fade " id="hareketEkleModal" tabindex="-1" aria-labelledby="hareketEkleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="hareketEkleForm" method="POST" action="{{ route('store.stock.action', request()->route('tenant_id')) }}">
      @csrf
      <input type="hidden" name="stok_id" id="modalStokId">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Stok Hareketi Ekle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="islem">İşlem</label>
            <select name="islem" class="form-control" required>
              <option value="">Seçiniz</option>
              <option value="1" selected>Alış</option>
              <option value="3">Personel'e Gönder</option>
            </select>
          </div>
          <div class="mb-2">
            <label>Tedarikçi</label>
            <select name="tedarikci" class="form-control">
              <option value="">Seçiniz</option>
              @foreach(\App\Models\StockSupplier::all() as $tedarikci)
                <option value="{{ $tedarikci->id }}">{{ $tedarikci->tedarikci }}</option>
              @endforeach
            </select>
          </div>

        <div class="mb-2 d-none" id="personelSelectDiv">
          <label>Personel</label>
          <select name="personel" class="form-control">
            <option value="">Seçiniz</option>
            @foreach(\App\Models\User::where('tenant_id', request()->route('tenant_id'))->get() as $personel)
              <option value="{{ $personel->user_id }}">{{ $personel->name }}</option>
            @endforeach
          </select>
        </div>

          <div class="mb-3">
            <label for="adet">Adet</label>
            <input type="number" name="adet" class="form-control" required min="1">
          </div>

          <div class="mb-3" id="fiyatInputDiv">
            <label for="fiyat">Fiyat (TL)</label>
            <input type="text" name="fiyat" class="form-control" required>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Kaydet</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
        </div>
      </div>
      
    </form>
  </div>
</div>
<style>
/* Sol ve sağ tarafı dengelemek için scrollable alanın yüksekliği ayarlandı */
.scrollable-card-body {
    max-height: 350px; 
    overflow-y: auto;
}
.scrollable-card-body thead th {
    position: sticky; top: 0; background-color: #f8f9fa; z-index: 1;
}

/* Sıkı form düzeni */
.tight-form .row {
    margin-bottom: 0.4rem;
}
.tight-form .col-form-label-sm {
    font-size: 0.75rem; font-weight: 500;
}
.tight-form .form-control-sm, .tight-form .form-select-sm {
    font-size: 0.8rem; padding: 0.25rem 0.5rem;
}

/* Fotoğraf alanı stilleri */
.empty-photos {
    text-align: center; padding: 2rem; color: #6c757d; background-color: #f8f9fa;
    border-radius: 0.375rem; border: 2px dashed #dee2e6;
}
.photo-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 0.5rem;
}
.photo-item {
    position: relative; aspect-ratio: 1;
}
.photo-item img {
    width: 100%; height: 100%; object-fit: cover; border-radius: 0.375rem;
}
.photo-delete {
    position: absolute; top: -5px; right: -5px; width: 20px; height: 20px;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    font-size: 0.7rem; line-height: 1;
}
</style>


<!-- =================================================================== -->
<!-- ================== TÜM SCRIPTLER ================== -->
<!-- =================================================================== -->
<script>
$(document).ready(function() {
    
    //-----------------------------------------------------
    // ANA FORM İLE İLGİLİ SCRIPTLER
    //-----------------------------------------------------

    // Ürün kodu maskesi
    $('input[name="urunKodu"]').mask('0000000000000', { placeholder: '_____________' });

    // Ürün kodu 13 haneli olmalı kontrolü
    $('#editStockUnifiedForm').submit(function(e){
        var urunKodu = $('input[name="urunKodu"]').val().trim();
        if(urunKodu.length > 0 && urunKodu.length !== 13){
            e.preventDefault();
            alert('Ürün kodu tam 13 haneli olmalıdır!');
            $('input[name="urunKodu"]').focus();
            return false;
        }
    });

    //-----------------------------------------------------
    // STOK HAREKETLERİ İLE İLGİLİ SCRIPTLER
    //-----------------------------------------------------

    // Hareket Ekle modalını aç
    $(document).on('click', '.hareketEkleBtn', function() {     
        let stokId = $(this).data('stokid');     
        $('#modalStokId').val(stokId);     
        $('#hareketEkleModal').modal('show');   
    });    

    // Hareketleri filtrele
    $('.islemSec').on('change', function () {       
        var selected = $(this).val();       
        var card = $(this).closest('.card');       
        var rows = card.find('table tbody tr');              
        var toplamAdet = 0;       
        var toplamFiyat = 0;        
        rows.each(function () {         
            var tdNumber = $(this).find('.tdNumber').text().trim();
            if (selected == 0) {           
                $(this).show();                      
            } else {           
                if (tdNumber.endsWith(',' + selected)) {             
                    $(this).show();                          
                    var adet = parseInt($(this).find('td').eq(4).text()) || 0;
                    var fiyat = parseFloat($(this).find('td').eq(5).text().replace(' TL', '').trim()) || 0;
                    toplamAdet += adet;             
                    toplamFiyat += fiyat * adet; // Fiyatı adet ile çarp                       
                } else {             
                    $(this).hide();           
                }       
            }     
        });

        if (selected == 0) {
            card.find('.toplam-adet-header').text('');
            card.find('.toplam-fiyat-header').text('');
        } else {
            card.find('.toplam-adet-header').text(toplamAdet + ' Adet');       
            card.find('.toplam-fiyat-header').text(toplamFiyat.toFixed(2)+' TL');
        }
    }).trigger('change'); // Sayfa yüklenince çalıştır

    // Stok hareketini sil
    $(document).on('click', '.stokSilBtn', function () {
        var id = $(this).data('id');
        var tenant_id = "{{ request()->route('tenant_id') }}";
        if (confirm("Stok hareketini silmek istediğinize emin misiniz?")) {
            $.ajax({
                url: '/'+tenant_id+'/stok-haraket-sil/' + id,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                success: function (res) {
                    alert(res.message);
                    if (res.status === 'success') location.reload();
                },
                error: function () { alert('Silme işlemi sırasında hata oluştu.'); }
            });
        }
    });

    // Hareket ekleme modalındaki formun davranışları
    $('select[name="islem"]').on('change', function() {       
        var val = $(this).val();             
        if (val == '1') { // Alış         
            $('#fiyatInputDiv').show();         
            $('#fiyatInputDiv input').prop('required', true);          
            $('#personelSelectDiv').addClass('d-none');         
            $('#personelSelectDiv select').prop('required', false);          
            $('select[name="tedarikci"]').closest('.mb-2').show();       
        } else if (val == '3') { // Personel'e Gönder         
            $('#fiyatInputDiv').hide();         
            $('#fiyatInputDiv input').prop('required', false).val('');          
            $('#personelSelectDiv').removeClass('d-none');         
            $('#personelSelectDiv select').prop('required', true);          
            $('select[name="tedarikci"]').closest('.mb-2').hide().val('');       
        }     
    });   
    
    //-----------------------------------------------------
    // PERSONEL STOKLARI İLE İLGİLİ SCRIPTLER
    //-----------------------------------------------------
    
    // Personel stok detay modalını aç
    $(document).on('click', '.detayBtn', function() {
        $('#modalAlici').text($(this).data('alici'));
        $('#modalAdet').text($(this).data('adet'));
        $('#modalTarih').text($(this).data('tarih'));
        $('#personelStokModal').modal('show');
    });

    //-----------------------------------------------------
    // FOTOĞRAFLAR İLE İLGİLİ SCRIPTLER
    //-----------------------------------------------------

    // Fotoğraf seçildiğinde otomatik yükle
    $('#customFile').on("change", function () {
        let file = this.files[0];
        if (!file) return;

        if (file.size > 5242880) { alert("Dosya 5MB'dan büyük olamaz."); $(this).val(''); return; }
        if (!["image/jpeg", "image/png"].includes(file.type)) { alert("Sadece JPG ve PNG yüklenebilir."); $(this).val(''); return; }

        let formData = new FormData($('#stokFotoEkle')[0]);
        $.ajax({
            url: "/{{ $tenant_id }}/stok-foto-ekle",
            method: "POST",
            data: formData,
            contentType: false, processData: false,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            beforeSend: function () { $(".imgLoad").show(); },
            success: function (res) {
                $(".imgLoad").hide();
                $('#customFile').val('');
                $('.imgBox').prepend(`<div class="col-6 col-sm-3 stn mb-2" data-id="${res.id}"><img src="${res.resim_yolu}" class="img-fluid border" style="width: 100%;"><button class="btn btn-danger btn-sm w-100 stokFotoSil mt-1" data-id="${res.id}"><i class="fas fa-trash-alt"></i></button></div>`);
            },
            error: function (xhr) {
                $(".imgLoad").hide();
                alert(xhr.responseJSON ? xhr.responseJSON.message : "Yükleme başarısız.");
            }
        });
    });

    // Fotoğrafı sil
    $(document).on('click', '.stokFotoSil', function (e) {
        e.preventDefault();
        if (!confirm("Fotoğraf silinsin mi?")) return;

        let id = $(this).data('id');
        let fotoDiv = $('.stn[data-id="' + id + '"]');
        $.ajax({
            url: "/{{ $tenant_id }}/stok-foto-sil",
            method: "POST",
            data: { _token: "{{ csrf_token() }}", id: id },
            success: function (res) {
                alert(res.message);
                fotoDiv.fadeOut(300, function () { $(this).remove(); });
            },
            error: function (xhr) {
                alert(xhr.responseJSON ? xhr.responseJSON.message : "Silme işlemi başarısız.");
            }
        });
    });

});
</script>

