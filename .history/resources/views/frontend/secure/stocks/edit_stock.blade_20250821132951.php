<div class="modal-header">
    <h5 class="modal-title" id="editStockModal">Stok Detayları ve Yönetimi [{{ $stock->urunAdi }}]</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
</div>
<div class="modal-body">
    <div class="row">

        <div class="col-lg-5">
          <!-- 1. KART: ÜRÜN BİLGİLERİ -->
            <div class="card mb-3">
                <div class="card-header py-2"> 
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Ürün Bilgileri</h5>
                </div>
                <div class="card-body p-1">
                    <form method="POST" id="editStockUnifiedForm" action="{{ route('update.stock', [$firma->id, $stock->id]) }}">
                        @csrf
                        <div class="row g-1 align-items-center">
                            <label class="col-sm-3 col-form-label col-form-label-sm">Markalar<span style="color:red;">*</span></label>
                            <div class="col-sm-9">
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

                        <div class="row g-1  align-items-center">
                            <label class="col-sm-3 col-form-label col-form-label-sm">Cihaz Türü<span style="color:red;">*</span></label>
                            <div class="col-sm-9">
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

                        <div class="row g-1  align-items-center">
                            <label class="col-sm-3 col-form-label col-form-label-sm">Kategori<span style="color:red;">*</span></label>
                            <div class="col-sm-9">
                                <select name="urunKategori" class="form-select form-select-sm" required>
                                    <option value="" selected disabled>Seçiniz</option>
                                    @foreach($kategoriler as $kategori)
                                    <option value="{{ $kategori->id }}" {{ $stock->urunKategori == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->kategori }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-1  align-items-center">
                            <label class="col-sm-3 col-form-label col-form-label-sm">Raf<span style="color:red;">*</span></label>
                            <div class="col-sm-9">
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
                        
                        <div class="row g-1  align-items-center">
                            <label class="col-sm-3 col-form-label col-form-label-sm">Ürün Adı<span style="color:red;">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" name="urunAdi" class="form-control form-control-sm" value="{{ $stock->urunAdi }}" required>
                            </div>
                        </div>

                        <div class="row g-1  align-items-center">
                            <label class="col-sm-3 col-form-label col-form-label-sm">Ürün Kodu<span style="color:red;">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" name="urunKodu" class="form-control form-control-sm @error('urunKodu') is-invalid @enderror" value="{{ old('urunKodu', $stock->urunKodu) }}" required>
                            </div>
                        </div>

                        <div class="row g-1 align-items-center">
                            <label class="col-sm-3 col-form-label col-form-label-sm">Satış Fiyatı<span style="color:red;">*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group input-group-sm">
                                    <input name="fiyat" type="number" min="0" step="0.01" class="form-control" placeholder="Fiyat" value="{{ $stock->fiyat }}" required>
                                    <select name="fiyatBirim" class="form-select">
                                        <option value="" {{ $stock->fiyatBirim == '' ? 'selected' : '' }}>Birim</option>
                                        <option value="1" {{ $stock->fiyatBirim == 1 ? 'selected' : '' }}>TL</option>
                                        <option value="2" {{ $stock->fiyatBirim == 2 ? 'selected' : '' }}>USD</option>
                                        <option value="3" {{ $stock->fiyatBirim == 3 ? 'selected' : '' }}>EUR</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row g-1 align-items-center">
                            <label class="col-sm-3 col-form-label col-form-label-sm">Açıklama</label>
                            <div class="col-sm-9">
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
                        <div class="row g-1 align-items-center">
                            <label class="col-sm-3 col-form-label col-form-label-sm">Stok Durumu</label>
                            <div class="col-sm-9">
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
                    {{-- Yükleme formu artık her zaman DOM'da olacak, sadece CSS ile gizlenip gösterilecek --}}
                    <div id="uploadFormContainer" style="display: {{ $photos->isEmpty() ? 'block' : 'none' }};">
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
                    </div>
                    <div id="photoLimitWarning" class="alert alert-warning  text-center" role="alert" style="display: {{ !$photos->isEmpty() ? 'block' : 'none' }};font-size: 1em; height:60px">
                        <small>
                            <i class="fas fa-exclamation-triangle"></i> Yalnızca 1 fotoğraf ekleyebilirsiniz. <br>
                            Değiştirmek için mevcut fotoğrafı silin.
                        </small>
                    </div>

                    <div class="row imgBox">
                        @foreach($photos as $foto)
                            <div class="col-4 col-md-3 stn mb-2" data-id="{{ $foto->id }}">
                                <img src="{{ Storage::url($foto->resimyol) }}" class="img-fluid border rounded" style="width: 100%;">
                                <button class="btn btn-danger btn-sm w-100 stokFotoSil mt-1 py-0" data-id="{{ $foto->id }}"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        @endforeach
                    </div>
                    
                    <div id="noPhotos" class="text-center text-muted"  style="display: {{ $photos->isEmpty() ? 'block' : 'none' }};">
                        <i class="fas fa-images fa-3x mb-2" style="font-size: 2em;"></i>
                        <p>Henüz fotoğraf yüklenmemiş</p>
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
                                @if($stokHareketleri->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">
                                            <i class="fas fa-inbox fa-2x" style="font-size: 2em;"></i>

                                            <p class="mb-0 mt-2">Henüz stok hareketi bulunmuyor</p>
                                            <small>İlk stok hareketini eklemek için "Hareket Ekle" butonunu kullanın</small>
                                        </td>
                                    </tr>
                                @else
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
                                @endif
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
                            @if($hareketler->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">
                                        <i class="fas fa-users fa-2x" style="font-size: 2em;"></i>
                                        <p class="mb-0 mt-2">Henüz personel stoku bulunmuyor</p>
                                        <small>Personele stok göndermek için stok hareketi ekleyin</small>
                                    </td>
                                </tr>
                            @else
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
                            @endif
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
<div class="modal fade " id="hareketEkleModal" tabindex="-1" aria-labelledby="hareketEkleModalLabel" aria-hidden="true" style="padding-top: 70px;background: rgba(0, 0, 0, 0.50);">
  <div class="modal-dialog">
    <form id="hareketEkleForm" method="POST" action="{{ route('store.stock.action', request()->route('tenant_id')) }}"  data-ajax="true">
      @csrf
      <input type="hidden" name="stok_id" id="modalStokId">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Stok Hareketi Ekle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
        </div>
        <div class="modal-body">
          <div>
            <label for="islem">İşlem</label>
            <select name="islem" class="form-control" required>
              <option value="">Seçiniz</option>
              <option value="1" selected>Alış</option>
              <option value="3">Personel'e Gönder</option>
            </select>
          </div>
        <div class>
        <label>Tedarikçi</label>
        <div class="input-group">
            <!-- ID eklendi ve yeni değişken kullanıldı -->
            <select name="tedarikci" id="tedarikciSelect" class="form-control">
                <option value="">Seçiniz</option>
                @foreach($sonTedarikciler as $tedarikci)
                <option value="{{ $tedarikci->id }}">{{ $tedarikci->tedarikci }}</option>
                @endforeach
            </select>
            <button class="btn btn-success" type="button" id="addNewSupplierBtn">+</button>
            </div>
        </div>

        <div class="d-none" id="personelSelectDiv">
            <label>Personel</label>
            <!-- ID eklendi ve yeni değişken kullanıldı -->
            <select name="personel" id="personelSelect" class="form-control">
                <option value="">Seçiniz</option>
                @foreach($sonPersoneller as $personel)
                <option value="{{ $personel->user_id }}">{{ $personel->name }}</option>
                @endforeach
            </select>
        </div>

          <div>
            <label for="adet">Adet</label>
            <input type="number" name="adet" class="form-control" required min="1">
          </div>

          <div id="fiyatInputDiv">
            <label for="fiyat">Alış Fiyatı(TL)</label>
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
<!--  Tedarikçi Ekle Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" aria-labelledby="addSupplierModalLabel" aria-hidden="true" style="padding-top: 100px; background: rgba(0, 0, 0, 0.7);">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSupplierModalLabel">Yeni Tedarikçi Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <form method="post" id="addStokSupplier" action="{{ route('store.stock.supplier', $firma->id)}}">
                    @csrf
                    <div class="row mb-3">
                        <label class="col-sm-12">Tedarikçi Adı :<span style="font-weight: bold; color: red;">*</span></label>
                        <div class="col-sm-12">
                            <input name="tedarikci" class="form-control" type="text" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-end">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">İptal</button>
                            <input type="submit" class="btn btn-info btn-sm" value="Kaydet">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
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
/* Hareket ekleme modalındaki selectleri eşitle */
#hareketEkleModal .form-select,
#hareketEkleModal .select2-container .select2-selection--single {
    height: 38px;              /* Bootstrap default input height */
    padding: 6px 12px;         /* Input padding */
    font-size: 14px;           /* Uyumlu font size */
    line-height: 1.5;          /* Dikey hizalama */
    border: 1px solid #ced4da; /* Normal input border */
    border-radius: 4px;        /* Köşe yuvarlama */
}

/* Select2 seçili alanı için (üsttekiyle aynı yükseklik) */
#hareketEkleModal .select2-container .select2-selection--single {
    display: flex;
    align-items: center;       /* Metni ortala */
}

/* Select2 açılır liste (dropdown) için */
#hareketEkleModal .select2-container .select2-results__option {
    font-size: 14px;           /* Yazı boyutu uyumlu */
    padding: 6px 12px;         /* Liste item aralığı */
}

/* Arama kutusu için */
#hareketEkleModal .select2-container .select2-search__field {
    font-size: 14px;
    padding: 6px 10px;
    height: 34px;
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
            alert('Ürün kodu tam 13 haneli olmalıdır!');
            $('input[name="urunKodu"]').focus();
            return false;
        }
    });

    // Hareket Ekle modalını aç
    $(document).on('click', '.hareketEkleBtn', function() {
        let stokId = $(this).data('stokid');
        $('#modalStokId').val(stokId);

        // Select2'yi AJAX ile başlatma
        $('#tedarikciSelect').select2({
            theme: "bootstrap-5",
            dropdownParent: $('#hareketEkleModal'),
            placeholder: 'Tedarikçi ara...',
            ajax: {
                url: '/{{ $tenant_id }}/search-suppliers',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });

        $('#personelSelect').select2({
            theme: "bootstrap-5",
            dropdownParent: $('#hareketEkleModal'),
            placeholder: 'Personel ara...',
            ajax: {
                url: '/{{ $tenant_id }}/search-personnel',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });

        $('#hareketEkleModal').modal('show');
    });

    // Modal kapandığında Select2'yi imha et
    $('#hareketEkleModal').on('hidden.bs.modal', function () {
        $('#tedarikciSelect').select2('destroy');
        $('#personelSelect').select2('destroy');
        
        // Arkadaki modal için scroll düzeltmesi
        if ($('#editStockModal').is(':visible')) {
            $('body').addClass('modal-open');
        }
    });

    // Modal açıldığında - Hareket türü değiştiğinde çalışacak fonksiyon
    $('#hareketEkleModal').on('shown.bs.modal', function () {
        // İlk açılışta trigger et
        $('select[name="islem"]').trigger('change');
    });

    // Hareket türü seçimi - Tek bir event listener
    $(document).on('change', 'select[name="islem"]', function() {
        // Sadece modal içindeki select için çalış
        if (!$(this).closest('#hareketEkleModal').length) return;
        
        var val = $(this).val();
        var tedarikciBlok = $('#tedarikciSelect').closest('div').not('#personelSelectDiv');
        var personelBlok = $('#personelSelectDiv');

        if (val == '1') { // Alış
            $('#fiyatInputDiv').show();
            $('#fiyatInputDiv input').prop('required', true);

            personelBlok.addClass('d-none');
            personelBlok.find('select').prop('required', false);
            $('#personelSelect').val(null).trigger('change');
            
            tedarikciBlok.show();

        } else if (val == '3') { // Personel'e Gönder
            $('#fiyatInputDiv').hide();
            $('#fiyatInputDiv input').prop('required', false).val('');

            personelBlok.removeClass('d-none');
            personelBlok.find('select').prop('required', true);

            tedarikciBlok.hide();
            $('#tedarikciSelect').val(null).trigger('change'); 
        }
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
                    toplamFiyat += fiyat * adet;                       
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
    }).trigger('change');

    // Stok hareketini sil - Benzersiz class ile işaretleme
    $(document).on('click', '.stokSilBtn', function (e) {
        e.preventDefault();
        
        // Eğer bu buton zaten işlem görüyorsa dur
        if ($(this).hasClass('processing')) {
            return false;
        }
        
        var id = $(this).data('id');
        var tenant_id = "{{ request()->route('tenant_id') }}";
        var $button = $(this);
        
        if (confirm("Stok hareketini silmek istediğinize emin misiniz?")) {
            // Butonu işlem durumuna al
            $button.addClass('processing').prop('disabled', true);
            
            $.ajax({
                url: '/'+tenant_id+'/stok-haraket-sil/' + id,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                success: function (res) {
                    alert(res.message);
                    if (res.status === 'success') {
                        location.reload();
                    } else {
                        $button.removeClass('processing').prop('disabled', false);
                    }
                },
                error: function () { 
                    alert('Silme işlemi sırasında hata oluştu.');
                    $button.removeClass('processing').prop('disabled', false);
                }
            });
        }
        
        return false;
    });

    // Fotoğraf seçildiğinde otomatik yükle
   $(document).off('change', '#customFile').on('change', '#customFile', function () {
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
                $('.imgBox').prepend(`<div class="col-4 col-md-3 stn mb-2" data-id="${res.id}"><img src="${res.resim_yolu}" class="img-fluid border rounded" style="width: 100%;"><button class="btn btn-danger btn-sm w-100 stokFotoSil mt-1 py-0" data-id="${res.id}"><i class="fas fa-trash-alt"></i></button></div>`);
                
                $('#noPhotos').hide();
                $('#uploadFormContainer').hide(); //Yükleme formunu gizleme

                $('#photoLimitWarning').show(); // Uyarı mesajını göster
            },
            error: function (xhr) {
                $(".imgLoad").hide();
                alert(xhr.responseJSON ? xhr.responseJSON.message : "Yükleme başarısız.");
            }
        });
    });

    // Fotoğrafı sil - Benzersiz class ile işaretleme
     $(document).off('click', '.stokFotoSil').on('click', '.stokFotoSil', function (e) {
        e.preventDefault();
        
        if ($(this).hasClass('processing')) {
            return false;
        }
        
        if (!confirm("Fotoğraf silinsin mi?")) {
            return false;
        }

        var $button = $(this);
        var id = $button.data('id');
        
        $button.addClass('processing').prop('disabled', true);
        
        let fotoDiv = $('.stn[data-id="' + id + '"]');
        
        $.ajax({
            url: "/{{ $tenant_id }}/stok-foto-sil",
            method: "POST",
            data: { _token: "{{ csrf_token() }}", id: id },
            success: function (res) {
                alert(res.message);
                fotoDiv.fadeOut(300, function () { 
                    $(this).remove(); 
                    
                    if ($('.imgBox .stn').length === 0) {
                        $('#noPhotos').show();
                        $('#uploadFormContainer').show(); //Yükleme formunu göster 

                        $('#photoLimitWarning').hide(); // Uyarı mesajını gizle
                    }
                });
            },
            error: function (xhr) {
                alert(xhr.responseJSON ? xhr.responseJSON.message : "Silme işlemi başarısız.");
                $button.removeClass('processing').prop('disabled', false);
            }
        });
        
        return false;
    });

    // Tedarikçi ekleme formu
    $(document).on('submit', '#addStokSupplier', function(e){
        e.preventDefault();
        if (this.checkValidity() === false) {
            e.stopPropagation();
        } else {
            var formData = $(this).serialize();
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                success: function(response) {
                    alert("Tedarikçi başarıyla eklendi.");
                    
                    var newOption = new Option(response.tedarikci, response.id, true, true);
                    $('#hareketEkleModal select[name="tedarikci"]').append(newOption).trigger('change');
                    
                    $('#addStokSupplier')[0].reset();
                    $('#addSupplierModal').modal('hide');
                },
                error: function(xhr) {
                    alert("Bir hata oluştu: " + xhr.responseText);
                }
            });
        }
    });
    
    // Tedarikçi Ekleme modalı kapandığında scroll problemini düzelt
    $('#addSupplierModal').on('hidden.bs.modal', function (e) {
        if ($('#hareketEkleModal').is(':visible')) {
            $('body').addClass('modal-open');
        }
    });

    // Tedarikçi Ekle (+) butonuna tıklandığında modalı aç
    $(document).on('click', '#addNewSupplierBtn', function () {
        var supplierModal = new bootstrap.Modal(document.getElementById('addSupplierModal'));
        supplierModal.show();
    });

});
</script>

