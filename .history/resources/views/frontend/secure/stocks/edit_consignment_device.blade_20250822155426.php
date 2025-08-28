<div class="modal-header">
    <h5 class="modal-title" id="editConsignmentModalTitle">Konsinye Cihaz Detayları [{{ $stock->urunAdi }}]</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
</div>

<div class="modal-body">
    <div class="row">
        {{-- SOL SÜTUN: ÜRÜN BİLGİLERİ VE FOTOĞRAFLAR --}}
        <div class="col-lg-5">
          <!-- 1. KART: ÜRÜN BİLGİLERİ -->
            <div class="card mb-3">
                <div class="card-header py-2">
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Ürün Bilgileri</h5>
                </div>
                <div class="card-body p-1">
                    <div id="updateConsignmentFormMessages" class="px-2"></div>
                    <form method="POST" id="editConsignmentUnifiedForm" action="{{ route('update.consignment.device', [$firma->id, $stock->id]) }}">
                        @csrf
                        {{-- Marka --}}
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
                        {{-- Cihaz Türü --}}
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
                        {{-- Raf --}}
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

                        {{-- Ürün Adı --}}
                        <div class="row g-1 align-items-center">
                            <label class="col-sm-3 col-form-label col-form-label-sm">Ürün Adı<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" name="urunAdi" class="form-control form-control-sm" value="{{ $stock->urunAdi }}" required>
                            </div>
                        </div>

                        {{-- Ürün Kodu --}}
                        <div class="row g-1  align-items-center">
                            <label class="col-sm-3">Ürün Kodu <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="d-flex align-items-center">
                                <input type="text" name="urunKodu" 
                                        class="form-control @error('urunKodu') is-invalid @enderror me-2" 
                                        value="{{ old('urunKodu', $stock->urunKodu) }}" required>

                                <a href="{{ route('consignment.device.barcode.pdf', [$firma->id, $stock->id]) }}" 
                                    target="_blank" 
                                    class="btn btn-warning btn-sm text-nowrap px-2 d-flex">
                                    <i class="ri-printer-line me-1"></i> Barkodu Yazdır
                                </a>
                                </div>
                                @error('urunKodu')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            </div>

                        {{-- Satış Fiyatı --}}
                        <div class="row g-1 align-items-center">
                            <label class="col-sm-3 col-form-label col-form-label-sm">Satış Fiyatı<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group input-group-sm">
                                    <input name="fiyat" type="number" min="0" step="0.01" class="form-control" placeholder="Fiyat" value="{{ $stock->fiyat }}" required>
                                    <select name="fiyatBirim" class="form-select">
                                        <option value="1" {{ $stock->fiyatBirim == 1 ? 'selected' : '' }}>TL</option>
                                        <option value="2" {{ $stock->fiyatBirim == 2 ? 'selected' : '' }}>USD</option>
                                        <option value="3" {{ $stock->fiyatBirim == 3 ? 'selected' : '' }}>EUR</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Açıklama --}}
                        <div class="row g-1 align-items-center">
                            <label class="col-sm-3 col-form-label col-form-label-sm">Açıklama</label>
                            <div class="col-sm-9">
                                <textarea name="aciklama" class="form-control form-control-sm" rows="2">{{ $stock->aciklama }}</textarea>
                            </div>
                        </div>

                        {{-- Stok Durumu (Sadece Depo Stoku) --}}
                        @php
                            $toplamGiris = \App\Models\StockAction::where('stokId', $stock->id)->whereIn('islem', [1, 4])->sum('adet');
                            $toplamCikis = \App\Models\StockAction::where('stokId', $stock->id)->where('islem', 2)->sum('adet');
                            $kalanStok = $toplamGiris - $toplamCikis;
                        @endphp
                        <div class="row g-1 align-items-center">
                            <label class="col-sm-3 col-form-label col-form-label-sm">Stok Durumu</label>
                            <div class="col-sm-9">
                                <div class="alert alert-secondary py-1 px-2 mb-0">
                                    <small>{{ $kalanStok }} Adet</small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 2. KART: FOTOĞRAFLAR -->
            <div class="card">
                <div class="card-header py-2">
                    <h5 class="mb-0"><i class="bi bi-images"></i> Fotoğraflar</h5>
                </div>
                <div class="card-body p-2">
                    <div id="uploadFormContainer" style="display: {{ $photos->isEmpty() ? 'block' : 'none' }};">
                        <form method="POST" id="consignmentFotoEkle" enctype="multipart/form-data">
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
                     <div id="photoLimitWarning" class="alert alert-warning text-center p-2" role="alert" style="display: {{ !$photos->isEmpty() ? 'block' : 'none' }};">
                        <small><i class="fas fa-exclamation-triangle"></i> Yalnızca 1 fotoğraf ekleyebilirsiniz. Değiştirmek için mevcut fotoğrafı silin.</small>
                    </div>

                    <div class="row imgBox">
                        @foreach($photos as $foto)
                            <div class="col-4 col-md-3 stn mb-2" data-id="{{ $foto->id }}">
                                <img src="{{ Storage::url($foto->resimyol) }}" class="img-fluid border rounded">
                                <button class="btn btn-danger btn-sm w-100 stokFotoSil mt-1 py-0" data-id="{{ $foto->id }}"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        @endforeach
                    </div>

                    <div id="noPhotos" class="text-center text-muted" style="display: {{ $photos->isEmpty() ? 'block' : 'none' }};">
                        <i class="fas fa-images" style="font-size: 2em;"></i>
                        <p class="mb-0 mt-1">Henüz fotoğraf yüklenmemiş</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- SAĞ SÜTUN: STOK HAREKETLERİ --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-arrows-move"></i> Stok Hareketleri</h5>
                        <div>
                            <button type="button" class="btn btn-success btn-sm hareketEkleBtn" data-stokid="{{ $stock->id }}">Hareket Ekle</button>
                            <select class="form-select-sm d-inline-block" name="islemSec" style="width: auto;">
                                <option value="0">Hepsi</option>
                                <option value="1">Alış</option>
                                <option value="4">Müşteriden Geri Alma</option>
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
                                    <th>Tarih</th>
                                    <th>İşlem</th>
                                    <th>Detay</th>
                                    <th>Adet</th>
                                    <th>Fiyat</th>
                                    <th style="width: 55px;">Sil</th>
                                </tr>
                                <tr class="toplam-header-row">
                                    <td style="display:none;"></td>
                                    <td colspan="3"></td>
                                    <td class="toplam-adet-header fw-bold"></td>
                                    <td class="toplam-fiyat-header fw-bold"></td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody>
                                @if($stokHareketleri->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">
                                            <i class="fas fa-inbox" style="font-size: 2em;"></i>
                                            <p class="mb-0 mt-2">Henüz stok hareketi bulunmuyor</p>
                                            <small>İlk stok hareketini eklemek için "Hareket Ekle" butonunu kullanın</small>
                                        </td>
                                    </tr>
                                @else
                                    @foreach($stokHareketleri as $stokIslem)
                                    @php
                                        $tarih = \Carbon\Carbon::parse($stokIslem->created_at)->format('d/m/Y');
                                        $islem = ''; $renk = '';

                                        if($stokIslem->islem == 1) { $islem = "Alış"; $renk = 'background-color: #d4edda;'; }
                                        elseif ($stokIslem->islem == 4) { $islem = "Müşteriden Geri Alma"; $renk = 'background-color: #cce5ff;'; }
                                        elseif ($stokIslem->islem == 2) { $islem = "Serviste Kullanım"; $teknisyenAdi = $stokIslem->performer_name ?? 'Bilinmiyor'; }
                                    @endphp
                                    <tr style="{{ $renk }}">
                                        <td class="tdNumber" style="display:none;">0,{{ $stokIslem->islem }}</td>
                                        <td>{{ $tarih }}</td>
                                        <td>{{ $islem }}</td>
                                        <td>
                                            @if($stokIslem->islem == 1) {{ $stokIslem->tedarikci_adi ?? '-' }}
                                            @elseif($stokIslem->islem == 2)
                                                <a href="{{ route('all.services', [$firma->id, 'did' => $stokIslem->servisid]) }}" target="_blank">Servis: {{ $stokIslem->servisid }}</a> ({{ $teknisyenAdi }})
                                            @elseif($stokIslem->islem == 4)
                                                Müşteri: {{ $stokIslem->servis->musteri->adSoyad ?? '-' }}
                                            @endif
                                        </td>
                                        <td>{{ $stokIslem->adet }}</td>
                                        <td>{{ $stokIslem->islem == 1 && $stokIslem->fiyat > 0 ? number_format($stokIslem->fiyat, 2, '.', '').' TL' : '-' }}</td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm stokSilBtn" data-id="{{ $stokIslem->id }}"><i class="fas fa-trash-alt"></i></button>
                                        </td>
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
    <button type="submit" class="btn btn-info" id="saveConsignmentInfoBtn" form="editConsignmentUnifiedForm">Ürün Bilgilerini Kaydet</button>
</div>

<!-- Hareket Ekle Modal -->
<div class="modal fade" id="hareketEkleModal" tabindex="-1" aria-hidden="true" style="padding-top: 70px; background: rgba(0,0,0,0.5);">
  <div class="modal-dialog">
    <form id="hareketEkleForm" method="POST" action="{{ route('store.consignment.stock.action', $firma->id) }}">
      @csrf
      <input type="hidden" name="stok_id" id="modalStokId">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Konsinye Stok Hareketi Ekle</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div class="mb-3">
            <label>İşlem</label>
            <select name="islem" class="form-control" required>
              <option value="1" selected>Alış</option>
            </select>
          </div>
          <div id="tedarikciSelectDiv" class="mb-3">
            <label>Tedarikçi</label>
            <div class="input-group">
                <select name="tedarikci" class="form-select">
                    <option value="">Seçiniz</option>
                    @foreach($sonTedarikciler as $tedarikci)
                    <option value="{{ $tedarikci->id }}">{{ $tedarikci->tedarikci }}</option>
                    @endforeach
                </select>
                <button class="btn btn-success" type="button" id="addNewSupplierBtn">+</button>
            </div>
          </div>
          <div class="mb-3">
            <label>Adet</label>
            <input type="number" name="adet" class="form-control" required min="1">
          </div>
          <div id="fiyatInputDiv" class="mb-3">
            <label>Alış Fiyatı(TL)</label>
            <input type="number" step="0.01" name="fiyat" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
            <button type="submit" class="btn btn-primary">Kaydet</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Tedarikçi Ekle Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" style="padding-top: 100px; background: rgba(0,0,0,0.7);">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Yeni Tedarikçi Ekle</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form method="post" id="addStokSupplier" action="{{ route('store.stock.supplier', $firma->id) }}">
                    @csrf
                    <div class="mb-3"><label>Tedarikçi Adı :<span class="text-danger">*</span></label><input name="tedarikci" class="form-control" type="text" required></div>
                    <div class="text-end"><input type="submit" class="btn btn-info btn-sm" value="Kaydet"></div>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
.scrollable-card-body { max-height: 450px; overflow-y: auto; }
.scrollable-card-body thead th { position: sticky; top: 0; background-color: #f8f9fa; z-index: 1; }
.col-form-label-sm { font-size: .800rem; }
.toplam-header-row { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; }
</style>

<script>
$(document).ready(function() {
    // Select2'leri AJAX ile başlat
    $('.select2-ajax').each(function() {
        $(this).select2({
            theme: "bootstrap-5",
            dropdownParent: $(this).closest('.modal-body'),
            placeholder: 'Arama yapın...',
            ajax: {
                url: $(this).data('url'),
                dataType: 'json',
                delay: 250,
                processResults: function (data) { return { results: data }; },
                cache: true
            }
        });
    });

    // Ürün kodu maskesi
    $('input[name="urunKodu"]').mask('0000000000000', { placeholder: '_____________' });

    // Ürün Bilgilerini Kaydetme Formu
    $('#editConsignmentUnifiedForm').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this), 
            $submitButton = $('#saveConsignmentInfoBtn'), 
            $messageDiv = $('#updateConsignmentFormMessages');
            
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            beforeSend: function() {
                $submitButton.prop('disabled', true).text('Kaydediliyor...');
            },
            success: function(response) {
                // Response kontrolü
                if (response && response.message) {
                    $messageDiv.html('<div class="alert alert-success py-1 px-2">' + response.message + '</div>');
                    
                    // Modal başlığını güncelle
                    var urunAdi = $form.find('input[name="urunAdi"]').val();
                    if (urunAdi) {
                        $('#editConsignmentModalTitle').text('Konsinye Cihaz Detayları [' + urunAdi + ']');
                    }
                    
                    setTimeout(function() {
                        $messageDiv.fadeOut('slow', function() { 
                            $(this).html('').show(); 
                        });
                    }, 3000);
                } else {
                    $messageDiv.html('<div class="alert alert-warning py-1 px-2">İşlem tamamlandı ancak yanıt mesajı alınamadı.</div>');
                }
            },
            error: function(jqXHR) {
                var errorMessage = 'Bir hata oluştu.';
                
                // Hata mesajını belirle
                if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                    errorMessage = jqXHR.responseJSON.message;
                } else if (jqXHR.responseText) {
                    try {
                        var errorData = JSON.parse(jqXHR.responseText);
                        if (errorData.message) {
                            errorMessage = errorData.message;
                        }
                    } catch(e) {
                        errorMessage = 'Sunucu hatası oluştu.';
                    }
                }
                
                $messageDiv.html('<div class="alert alert-danger py-1 px-2">' + errorMessage + '</div>');
            },
            complete: function() {
                $submitButton.prop('disabled', false).text('Ürün Bilgilerini Kaydet');
            }
        });
    });

    // Modal içeriğini yenileme fonksiyonu
    function refreshConsignmentDetails(stockId, tenantId) {
        $.get('/' + tenantId + '/konsinye-cihazlar/duzenle/' + stockId, function(response) {
            if (response && response.html) {
                $('#editConsignmentModal .modal-content').html(response.html);
                $('input[name="urunKodu"]').mask('0000000000000', { placeholder: '_____________' });
            }
        }).fail(function() {
            alert('Detaylar yenilenirken bir hata oluştu.');
        });
    }

    // Hareket Ekleme Formu
    $('#hareketEkleForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this), 
            submitButton = form.find('button[type="submit"]');
            
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            beforeSend: function() {
                submitButton.prop('disabled', true).text('Kaydediliyor...');
            },
            success: function(response) {
                $('#hareketEkleModal').modal('hide');
                form[0].reset();
                
                // Response kontrolü
                if (response && response.message) {
                    alert(response.message);
                } else {
                    alert('Hareket başarıyla eklendi.');
                }
                
                // Sayfayı yenile
                var stockId = $('#modalStokId').val();
                var tenantId = "{{ $firma->id ?? '' }}";
                if (stockId && tenantId) {
                    refreshConsignmentDetails(stockId, tenantId);
                }
            },
            error: function(xhr) {
                var errorMessage = 'Bir hata oluştu.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        var errorData = JSON.parse(xhr.responseText);
                        if (errorData.message) {
                            errorMessage = errorData.message;
                        }
                    } catch(e) {
                        errorMessage = 'Sunucu hatası oluştu.';
                    }
                }
                
                alert(errorMessage);
            },
            complete: function() {
                submitButton.prop('disabled', false).text('Kaydet');
            }
        });
    });

    // Hareket Ekle modalını aç
    $(document).on('click', '.hareketEkleBtn', function() {
        $('#modalStokId').val($(this).data('stokid'));
        $('#hareketEkleModal').modal('show');
    });

    // Hareket türü seçimi
    $(document).on('change', 'select[name="islem"]', function() {
        var val = $(this).val();
        $('#fiyatInputDiv, #tedarikciSelectDiv').hide();
        if (val == '1') { // Alış
            $('#fiyatInputDiv, #tedarikciSelectDiv').show();
        }
    }).trigger('change');

    // Hareketleri filtrele
    $('select[name="islemSec"]').on('change', function () {
        var selected = $(this).val(), 
            $card = $(this).closest('.card');
        var toplamAdet = 0, toplamFiyat = 0;
        
        $card.find('table tbody tr').each(function () {
            var $row = $(this);
            var tdNumber = $row.find('.tdNumber').text();
            var isVisible = (selected == 0 || tdNumber.endsWith(',' + selected));
            $row.toggle(isVisible);
            
            if(isVisible && selected != 0){
                var adetText = $row.find('td').eq(4).text();
                var fiyatText = $row.find('td').eq(5).text().replace(' TL', '');
                
                toplamAdet += parseInt(adetText) || 0;
                toplamFiyat += parseFloat(fiyatText) || 0;
            }
        });
        
        $card.find('.toplam-adet-header').text(selected != 0 ? toplamAdet + ' Adet' : '');
        $card.find('.toplam-fiyat-header').text(selected != 0 ? toplamFiyat.toFixed(2) + ' TL' : '');
    }).trigger('change');

    // Stok hareketini sil
    $(document).on('click', '.stokSilBtn', function (e) {
        e.preventDefault();
        if (!confirm("Bu hareketi silmek istediğinize emin misiniz?")) return;
        
        var id = $(this).data('id'), 
            $button = $(this), 
            $row = $button.closest('tr');
            
        $.ajax({
            url: '/{{ $firma->id ?? "" }}/stok-konsinye-hareket-sil/' + id,
            method: 'POST',
            data: { 
                _token: '{{ csrf_token() }}', 
                _method: 'DELETE' 
            },
            success: function(res) {
                if (res && res.message) {
                    alert(res.message);
                    if (res.status === 'success') {
                        $row.fadeOut(400, function() { 
                            $row.remove(); 
                        });
                    }
                } else {
                    alert('İşlem tamamlandı.');
                    $row.fadeOut(400, function() { 
                        $row.remove(); 
                    });
                }
            },
            error: function() {
                alert('Silme işlemi sırasında hata oluştu.');
            }
        });
    });

    // Fotoğraf seçildiğinde otomatik yükle
    $(document).on('change', '#customFile', function () {
        if(!this.files[0]) return;
        
        let formData = new FormData($('#consignmentFotoEkle')[0]);
        
        $.ajax({
            url: "/{{ $firma->id ?? '' }}/stok-konsinye-foto-ekle",
            method: "POST", 
            data: formData, 
            contentType: false, 
            processData: false,
            beforeSend: function() {
                $(".imgLoad").show();
            },
            success: function (res) {
                if (res && res.id && res.resim_yolu) {
                    $('.imgBox').html(`
                        <div class="col-4 col-md-3 stn mb-2" data-id="${res.id}">
                            <img src="${res.resim_yolu}" class="img-fluid border rounded">
                            <button class="btn btn-danger btn-sm w-100 stokFotoSil mt-1 py-0" data-id="${res.id}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    `);
                    $('#noPhotos, #uploadFormContainer').hide();
                    $('#photoLimitWarning').show();
                }
            },
            error: function(xhr) {
                var errorMessage = "Yükleme başarısız.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
            },
            complete: function() {
                $(".imgLoad").hide(); 
                $('#customFile').val('');
            }
        });
    });

    // Fotoğrafı sil
    $(document).on('click', '.stokFotoSil', function (e) {
        e.preventDefault();
        if (!confirm("Fotoğraf silinsin mi?")) return;
        
        var id = $(this).data('id'), 
            $fotoDiv = $('.stn[data-id="' + id + '"]');
            
        $.post("/{{ $firma->id ?? '' }}/stok-konsinye-foto-sil", { 
            _token: "{{ csrf_token() }}", 
            id: id 
        }, function(res) {
            if (res && res.message) {
                alert(res.message);
            } else {
                alert('Fotoğraf silindi.');
            }
            
            $fotoDiv.fadeOut(300, function () {
                $(this).remove();
                $('#noPhotos, #uploadFormContainer').show();
                $('#photoLimitWarning').hide();
            });
        }).fail(function() {
            alert("Silme işlemi başarısız.");
        });
    });

    // Tedarikçi Ekleme
    $('#addNewSupplierBtn').on('click', function() {
        $('#addSupplierModal').modal('show');
    });
    
    $('#addSupplierModal').on('hidden.bs.modal', function() {
        if ($('#hareketEkleModal').is(':visible')) {
            $('body').addClass('modal-open');
        }
    });
    
    $('#addStokSupplier').on('submit', function(e) {
        e.preventDefault();
        
        $.post($(this).attr('action'), $(this).serialize(), function(response) {
            if (response && response.tedarikci && response.id) {
                var newOption = new Option(response.tedarikci, response.id, true, true);
                $('#hareketEkleModal select[name="tedarikci"]').append(newOption).trigger('change');
                $('#addSupplierModal').modal('hide');
            }
        }).fail(function() {
            alert("Tedarikçi eklenemedi.");
        });
    });
});
</script>