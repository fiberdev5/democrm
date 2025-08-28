<div class="modal-header">
    <h5 class="modal-title" id="editStockModal">Stok Detayları ve Yönetimi [{{ $stock->urunAdi }}]</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
</div>
<div class="modal-body">
    <div class="row">

        <div class="col-lg-5">
          <!-- 1. KART: ÜRÜN BİLGİLERİ -->
            <div class="card mb-3">
                <div class="card-header py-2"> <!-- Üst ve alt padding azaltıldı -->
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Ürün Bilgileri</h5>
                </div>
                <div class="card-body p-1"> <!-- Kart içeriğinin her yönden padding'i azaltıldı -->
                    <form method="POST" id="editStockUnifiedForm" action="{{ route('update.stock', [$firma->id, $stock->id]) }}">
                        @csrf
                        <div class="row g-1 align-items-center">
                            <label class="col-sm-4 col-form-label col-form-label-sm">Markalar</label>
                            <div class="col-sm-8">
                                <select name="marka_id" class="form-select form-select-sm" required>
                                    <option value="" selected disabled>Seçiniz</option>
                                    @foreach($markalar as $marka)
                                    <option value="{{ $marka->id }}" {{ $stock->stok_marka == $marka->id ? 'selected' : '' }}>
                                        {{ $marka->marka }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 mb-1 align-items-center">
                            <label class="col-sm-4 col-form-label col-form-label-sm">Cihaz Türleri</label>
                            <div class="col-sm-8">
                                <select name="cihaz_id" class="form-select form-select-sm" required>
                                    <option value="" selected disabled>Seçiniz</option>
                                    @foreach($cihazlar as $cihaz)
                                    <option value="{{ $cihaz->id }}" {{ $stock->stok_cihaz == $cihaz->id ? 'selected' : '' }}>
                                        {{ $cihaz->cihaz }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 mb-1 align-items-center">
                            <label class="col-sm-4 col-form-label col-form-label-sm">Kategori</label>
                            <div class="col-sm-8">
                                <select name="urunKategori" class="form-select form-select-sm">
                                    <option value="" selected disabled>Seçiniz</option>
                                    @foreach($kategoriler as $kategori)
                                    <option value="{{ $kategori->id }}" {{ $stock->urunKategori == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->kategori }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 mb-1 align-items-center">
                            <label class="col-sm-4 col-form-label col-form-label-sm">Raf</label>
                            <div class="col-sm-8">
                                <select name="raf_id" class="form-select form-select-sm" required>
                                    <option value="" selected disabled>Seçiniz</option>
                                    @foreach($rafListesi as $raf)
                                    <option value="{{ $raf->id }}" {{ $stock->urunDepo == $raf->id ? 'selected' : '' }}>
                                        {{ $raf->raf_adi }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="row g-2 mb-1 align-items-center">
                            <label class="col-sm-4 col-form-label col-form-label-sm">Ürün Adı</label>
                            <div class="col-sm-8">
                                <input type="text" name="urunAdi" class="form-control form-control-sm" value="{{ $stock->urunAdi }}">
                            </div>
                        </div>

                        <div class="row g-2 mb-1 align-items-center">
                            <label class="col-sm-4 col-form-label col-form-label-sm">Ürün Kodu</label>
                            <div class="col-sm-8">
                                <input type="text" name="urunKodu" class="form-control form-control-sm @error('urunKodu') is-invalid @enderror" value="{{ old('urunKodu', $stock->urunKodu) }}">
                            </div>
                        </div>

                        <div class="row g-2 mb-1 align-items-center">
                            <label class="col-sm-4 col-form-label col-form-label-sm">Satış Fiyatı</label>
                            <div class="col-sm-8">
                                <div class="input-group input-group-sm">
                                    <input name="fiyat" type="number" min="0" step="0.01" class="form-control" placeholder="Fiyat" value="{{ $stock->fiyat }}">
                                    <select name="fiyatBirim" class="form-select">
                                        <option value="" {{ $stock->fiyatBirim == '' ? 'selected' : '' }}>Birim</option>
                                        <option value="1" {{ $stock->fiyatBirim == 1 ? 'selected' : '' }}>TL</option>
                                        <option value="2" {{ $stock->fiyatBirim == 2 ? 'selected' : '' }}>USD</option>
                                        <option value="3" {{ $stock->fiyatBirim == 3 ? 'selected' : '' }}>EUR</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-1 align-items-center">
                            <label class="col-sm-4 col-form-label col-form-label-sm">Açıklama</label>
                            <div class="col-sm-8">
                                <textarea name="aciklama" class="form-control form-control-sm" rows="2">{{ $stock->aciklama }}</textarea>
                            </div>
                        </div>

                        @php
                            $toplamGiris = \App\Models\StockAction::where('stokId', $stock->id)->where('islem', 1)->sum('adet');
                            $toplamCikis = \App\Models\StockAction::where('stokId', $stock->id)->where('islem', 3)->sum('adet');
                            $kalanStok = $toplamGiris - $toplamCikis;
                            $personelAdet = \App\Models\PersonelStock::where('stokId', $stock->id)->sum('adet');
                            $genelToplam = $kalanStok + $personelAdet;
                        @endphp
                        <div class="row g-2 align-items-center">
                            <label class="col-sm-4 col-form-label col-form-label-sm">Stok Durumu</label>
                            <div class="col-sm-8">
                                <div class="alert alert-secondary py-1 px-2 mb-0"> <!-- Daha sade bir uyarı rengi ve daha az padding -->
                                    <small><strong>Toplam:</strong> {{ $genelToplam }} / <strong>Personelde:</strong> {{ $personelAdet }}</small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 2. KART: FOTOĞRAFLAR -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-images"></i> Fotoğraflar</h5>
                </div>
                <div class="card-body">
                    <form method="POST" id="stokFotoEkle" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-2">
                            <input type="file" class="form-control form-control-sm" name="resim" id="customFile" accept="image/jpeg,image/png">
                            <input type="hidden" name="stock_id" value="{{ $stock->id }}">
                        </div>
                        <div class="imgLoad" style="display: none;">
                            <div class="progress my-1" style="height: 5px;"><div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div></div>
                        </div>
                    </form>
                    <hr class="my-2">
                    <div class="row imgBox">
                        {{-- Daha küçük fotoğraflar için col-4 col-md-3 (3'lü veya 4'lü sıra) --}}
                        @foreach($photos as $foto)
                            <div class="col-4 col-md-3 stn mb-2" data-id="{{ $foto->id }}">
                                <img src="{{ Storage::url($foto->resimyol) }}" class="img-fluid border rounded" style="width: 100%;">
                                <button class="btn btn-danger btn-sm w-100 stokFotoSil mt-1 py-0" data-id="{{ $foto->id }}"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <!-- 1. KART: STOK HAREKETLERİ (KAYDIRILABİLİR) -->
            <div class="card mb-3">
                <div class="card-header ch1">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5><i class="bi bi-arrows-move"></i> Stok Hareketleri</h5>
                        <div>
                        <button type="button" class="btn btn-success btn-sm hareketEkleBtn" data-stokid="{{ $stock->id }}">
                            Hareket Ekle
                        </button>
                        <select class="form-control-select islemSec d-inline-block" name="islemSec" style="width: auto;">
                            <option value="0">Hepsi</option>
                            <option value="1">Alış</option>
                            <option value="3">Personele Gönder</option>
                            <option value="2">Serviste Kullanım</option>
                        </select>
                        </div>
                    </div>
                </div>
                <div class="card-body scrollable-card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead>
                                <tr>
                                <th style="display:none;"></th>
                                <th style="width: 50px;">Tarih</th>
                                <th>İşlem</th>
                                <th>Detay</th>
                                <th>Adet</th>
                                <th>Fiyat</th>
                                <th style="width: 55px;">Sil</th>
                                </tr>
                                <tr class="toplam-header-row" style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                                    <td style="display:none;"></td>
                                    <td colspan="3"></td>
                                    <td class="toplam-adet-header" style="font-weight: bold;">0 Adet</td>
                                    <td class="toplam-fiyat-header" style="font-weight: bold;">0 TL</td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody>
                                @php $toplam = 0; @endphp
                                @foreach($stokHareketleri as $stokIslem)
                                @php
                                $tarihSaat = explode(' ', $stokIslem->created_at);
                                $tarih = explode('-', $tarihSaat[0]);
                                
                                $islem = '';
                                $renk = '';

                                if($stokIslem->islem == 1) {
                                  $islem = "Alış";
                                  $renk = 'background-color: #d4edda;';
                                  $toplam += $stokIslem->adet;
                                }elseif ($stokIslem->islem == 2) {
                                $islem = "Serviste Kullanım";
                                $teknisyenAdi = $stokIslem->performer_name ?? 'Bilinmiyor'; 
                                }elseif ($stokIslem->islem == 3) {
                                  $islem = "Personel Depo";
                                  $renk = 'background-color: #f8d7da;';
                                  $perSec = \App\Models\User::find($stokIslem->pid);
                                  $toplam -= $stokIslem->adet; 
                                }
                                @endphp
                                <tr style="{{ $renk }}">
                                <td class="tdNumber" style="display:none;">0,{{ $stokIslem->islem }}</td>
                                <td>{{ $tarih[2] }}/{{ $tarih[1] }}/{{ $tarih[0] }}</td>
                                <td>{{ $islem }}</td>
                                <td>
                                  @if($stokIslem->islem == 1)
                                    {{ $stokIslem->tedarikci ?? '-' }}
                                  @elseif($stokIslem->islem == 2)
                                  <a href="{{ route('all.services', [$firma->id,'did'=>$stokIslem->servisid]) }}" target="_blank" class="link-stock">
                                    Servis: {{ $stokIslem->servisid }}
                                  </a> ({{ $teknisyenAdi }})
                                  @elseif($stokIslem->islem == 3)
                                    {{ $perSec->name ?? '' }}
                                  @endif
                                </td>
                                <td>{{ $stokIslem->adet }}</td>
                                <td>
                                @if($stokIslem->islem == 1 && $stokIslem->fiyat > 0)
                                  {{ number_format($stokIslem->fiyat, 2, '.', '') }} TL
                                @else
                                  -
                                @endif
                              </td>
                                <td>
                                <button type="button" class="btn btn-danger btn-sm stokSilBtn" data-id="{{ $stokIslem->id }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                </td>
                              </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- 2. KART: PERSONEL STOKLARI (KAYDIRILABİLİR) -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-people"></i> Personel Stokları</h5>
                </div>
                <div class="card-body scrollable-card-body p-0">
                    <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0">
                        <thead>
                        <tr>
                            <th>Personel</th>
                            <th>Adet</th>
                            <th>Tarih</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($hareketler as $hareket)
                            @php
                                $alici = $hareket->aliciPersonel->name ?? 'Bilinmiyor';
                            @endphp
                            <tr>
                                <td>{{ $alici }}</td>
                                <td>{{ $hareket->guncel_adet ?? '-' }}</td>
                                <td>{{ $hareket->created_at->format('d.m.Y') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
    <button type="submit" class="btn btn-info" form="editStockUnifiedForm">Ürün Bilgilerini Kaydet</button>
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
<style>
#editStockModal.modal-stack {
    opacity: 0.8; /* Hafifçe şeffaf yap */
    filter: blur(1px); /* Daha modern bir görünüm için hafifçe bulanıklaştır (isteğe bağlı) */
    transition: all 0.3s ease-in-out; /* Geçişi yumuşat */
}

/* Bootstrap'in z-index yönetimini desteklemek için ikinci modal'ın arka planı için ek ayar */
.modal-backdrop:nth-of-type(2) {
    z-index: 1056; /* İkinci modal'ın arka planını birinci modal'ın üzerine çıkarır */
}

#hareketEkleModal {
    z-index: 1057; /* İkinci modal'ı en üste çıkarır */
}
/* Sol ve sağ tarafı dengelemek için scrollable alanın yüksekliği ayarlandı */
.scrollable-card-body {
    max-height: 280px; 
    overflow-y: auto;
}
.scrollable-card-body thead th {
    position: sticky; top: 0; background-color: #f8f9fa; z-index: 1;
}
/* Form etiketleri için küçük font boyutu */
.col-form-label-sm {
    font-size: .800rem;
}
 .fs-sm {
        font-size: .875rem; /* form-control-sm ile uyumlu */
    }
</style>
<script>
$(document).ready(function() {
    
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

    // 'hareketEkleModal' isimli modal kapandığında bu fonksiyon çalışacak.
    $('#hareketEkleModal').on('hidden.bs.modal', function (e) {
        if ($('#editStockModal').is(':visible')) {
            $('body').addClass('modal-open');
        }
    });
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

