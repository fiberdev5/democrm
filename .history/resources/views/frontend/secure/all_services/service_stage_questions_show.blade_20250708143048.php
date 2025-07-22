{{-- Normal Servis Formu --}}
<form method="post" id="servisPlanKaydet" action="{{ route('save.service.plan', $firma->id) }}" class="col-sm-6" style="margin: 0 auto;padding:10px;">
    @csrf
    @foreach($stage_questions as $stage)
        {{-- Her bir aşama için yeni bir satır ve sütun başlangıcı --}}
        <div class="row form-group">
            <div class="col-lg-12">
                <label>{{ $stage->soru }}</label>
                {{-- Parça (Personel Stoğu) Bölümü --}}
                @if($stage->cevapTuru == "[Parca]")
                    <input id="urunAraInput" type="text" class="form-control product-search-input" data-target-list=".myParcaList" autocomplete="off" autofocus="on" placeholder="Ürün adı veya ürün kodu">
                    <div class="parcalar-dropdown myParcaList" style="width:100%">
                        @php $parca_say = 0; @endphp
                        <p>Toplam Personel Stok Sayısı: {{ $toplamPersonelStokAdedi }}</p>

                        @forelse($stoklar as $stok)
                            @php
                                $stokId = $stok->stokid ?? $stok->stok_id ?? $stok->id;
                                $stokAdet = $stok->adet ?? $stok->quantity ?? 0;

                                $stokSec = null;
                                if($stokId) {
                                    $stokSec = App\Models\Stock::where('firma_id', $firma->id)->where('id', $stokId)->first();
                                }
                            @endphp

                            @if($stokSec && $stokAdet > 0)
                                @php $parca_say++; @endphp
                                <div class="checkbox stock-item" style="padding:3px 0;" data-product-code="{{ $stokSec->urunKodu ?? '' }}" data-product-name="{{ $stokSec->urunAdi ?? $stokSec->urun_adi ?? 'N/A' }}">
                                    <label style="width: calc(100% - 40px);display: inline-block;text-transform: capitalize;">
                                        <input type="checkbox" name="parca[{{ $stage->id }}][{{ $stokId }}]"
                                               class="stock-checkbox"
                                               value="{{ $stokId }}"
                                               data-available="{{ $stokAdet }}"
                                               style="position: relative; top:2px; margin-right:3px;">
                                        {{ $stokSec->urunAdi ?? $stokSec->urun_adi ?? 'Ürün Adı Bulunamadı' }} (Mevcut: {{ $stokAdet }})
                                    </label>
                                    <input type="number" name="adet[{{ $stage->id }}][{{ $stokId }}]"
                                           value="1" min="1" max="{{ $stokAdet }}"
                                           class="form-control quantity-input" autocomplete="off"
                                           style="width: 40px;display: inline-block;text-align:center; display:none;">
                                </div>
                            @endif
                        @empty
                        @endforelse
                        @if($parca_say == 0)
                            <label style="color:red">Uyumlu Parça Bulunamadı.</label>
                        @endif
                    </div> {{-- .parcalar-dropdown --}}
                    <input type="hidden" name="soru[{{ $stage->id }}]" class="form-control" value="Parca"/>

                {{-- Konsinye Cihaz Bölümü --}}
                @elseif($stage->cevapTuru == "[Konsinye Cihaz]")
                    <input id="konsinyeAraInput" type="text" class="form-control product-search-input" data-target-list=".myKonsinyeList" autocomplete="off" placeholder="Konsinye cihaz adı veya kodu">
                    <div class="konsinye-dropdown myKonsinyeList" style="width:100%">
                        @php $konsinye_say = 0; @endphp
                        <p>Toplam Konsinye Cihaz Sayısı: {{ $toplamKonsinyeCihazAdedi }}</p>

                        @forelse($konsinyeCihazlar as $konsinyeCihaz)
                            @php
                                $konsinyeId = $konsinyeCihaz->stokid ?? $konsinyeCihaz->stok_id ?? $konsinyeCihaz->id;
                                $konsinyeAdet = $konsinyeCihaz->adet ?? $konsinyeCihaz->quantity ?? 0;

                                $konsinyeSec = null;
                                if($konsinyeId) {
                                    $konsinyeSec = App\Models\Stock::where('firma_id', $firma->id)->where('id', $konsinyeId)->first();
                                }
                            @endphp

                            @if($konsinyeSec && $konsinyeAdet > 0)
                                @php $konsinye_say++; @endphp
                                <div class="checkbox stock-item" style="padding:3px 0;" data-product-code="{{ $konsinyeSec->urunKodu ?? '' }}" data-product-name="{{ $konsinyeSec->urunAdi ?? $konsinyeSec->urun_adi ?? 'N/A' }}">
                                    <label style="width: calc(100% - 40px);display: inline-block;text-transform: capitalize;">
                                        <input type="checkbox" name="konsinye_cihaz[{{ $stage->id }}][{{ $konsinyeId }}]"
                                               class="stock-checkbox" {{-- Ortak sınıf kullanıldı --}}
                                               value="{{ $konsinyeId }}"
                                               data-available="{{ $konsinyeAdet }}"
                                               style="position: relative; top:2px; margin-right:3px;">
                                        {{ $konsinyeSec->urunAdi ?? $konsinyeSec->urun_adi ?? 'Ürün Adı Bulunamadı' }} (Mevcut: {{ $konsinyeAdet }})
                                    </label>
                                    <input type="number" name="konsinye_adet[{{ $stage->id }}][{{ $konsinyeId }}]"
                                           value="1" min="1" max="{{ $konsinyeAdet }}"
                                           class="form-control quantity-input" {{-- Ortak sınıf kullanıldı --}}
                                           autocomplete="off"
                                           style="width: 40px;display: inline-block;text-align:center; display:none;">
                                </div>
                            @endif
                        @empty
                        @endforelse
                        @if($konsinye_say == 0)
                            <label style="color:red">Uyumlu Konsinye Cihaz Bulunamadı.</label>
                        @endif
                    </div> {{-- .konsinye-dropdown --}}
                    <input type="hidden" name="soru[{{ $stage->id }}]" class="form-control" value="Konsinye Cihaz"/>

                {{-- Diğer Cevap Türleri --}}
                @else
                    <div class="col-lg-8" style="margin-left: -15px;">
                        @if($stage->cevapTuru == "[Aciklama]")
                            <input type="text" name="soru[{{ $stage->id }}]" class="form-control" autocomplete="off" />
                        @elseif(str_contains($stage->cevapTuru, 'Grup'))
                            @if(str_contains($stage->cevapTuru, 'Grup-0'))
                                @php
                                    $adminPersonel = App\Models\User::where('tenant_id', $firma->id)
                                        ->where('status', '1')
                                        ->whereHas('roles', function($query) {
                                            $query->where('name', 'Admin');
                                        })
                                        ->orderBy('name', 'asc')
                                        ->get();
                                @endphp
                                <select class="form-control" name="soru[{{ $stage->id }}]" required>
                                    <option value="">-Seçiniz-</option>
                                    @foreach($adminPersonel as $personel)
                                        <option value="{{ $personel->user_id }}">{{ $personel->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                @php
                                    $teknisyenPersonel = App\Models\User::where('tenant_id', $firma->id)
                                        ->where('status', '1')
                                        ->whereHas('roles', function($query) {
                                            $query->whereIn('name', ['Teknisyen', 'Teknisyen Yardımcısı']);
                                        })
                                        ->with('roles')
                                        ->orderBy('name', 'asc')
                                        ->get();
                                @endphp
                                <select class="form-control" name="soru[{{ $stage->id }}]" required>
                                    <option value="">-Seçiniz-</option>
                                    @foreach($teknisyenPersonel->filter(function($personel) {
                                        return $personel->roles->pluck('name')->intersect(['Teknisyen', 'Teknisyen Yardımcısı'])->isNotEmpty();
                                    }) as $personel)
                                        <option value="{{ $personel->user_id }}">{{ $personel->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        @elseif($stage->cevapTuru == "[Tarih]")
                            @php
                                $bugun = date('w');
                                $date = ($bugun == 6)
                                    ? date('Y-m-d', strtotime('+2 days'))
                                    : date('Y-m-d', strtotime('+1 day'));
                            @endphp
                            <input type="date" name="soru[{{ $stage->id }}]" class="form-control datepicker" value="{{ $date }}" style="background:#fff;" required>
                        @elseif($stage->cevapTuru == "[Saat]")
                            @php
                                $hours = [
                                    "08:00-10:00", "09:00-11:00", "10:00-12:00",
                                    "11:00-13:00", "12:00-14:00", "13:00-15:00",
                                    "14:00-16:00", "15:00-17:00", "16:00-18:00",
                                    "17:00-19:00", "18:00-20:00", "19:00-21:00",
                                    "20:00-22:00", "21:00-23:00"
                                ];
                            @endphp
                            <select class="form-control" name="soru[{{ $stage->id }}]" required>
                                <option value="">-Seçiniz-</option>
                                @foreach($hours as $hour)
                                    <option value="{{ $hour }}">{{ $hour }}</option>
                                @endforeach
                            </select>
                        @elseif($stage->cevapTuru == "[Arac]")
                            <select class="form-control" name="soru[{{ $stage->id }}]" required>
                                <option value="">-Seçiniz-</option>
                                @foreach($araclar as $arac)
                                    <option value="{{ $arac->id }}">{{ $arac->arac }}</option>
                                @endforeach
                            </select>
                        @elseif($stage->cevapTuru == "[Fiyat]")
                            <input type="number" name="soru[{{ $stage->id }}]" class="form-control" autocomplete="off" required/>
                        @elseif($stage->cevapTuru == "[Teklif]")
                            <input type="number" name="soru[{{ $stage->id }}]" class="form-control" autocomplete="off" required/>
                            <span style="font-size: 12px; color: red; font-weight: 500; margin: 0; padding: 0;display: block;">Bu alan sadece teklif vermek için kullanılır.</span>
                        @elseif($stage->cevapTuru == "[Bayi]")
                            @php
                                $bayiler = App\Models\User::where('tenant_id', $firma->id)
                                    ->where('status', '1')
                                    ->whereHas('roles', function($query) {
                                        $query->whereIn('name', ['Bayi']);
                                    })
                                    ->orderBy('name', 'asc')
                                    ->get();
                            @endphp
                            <select class="form-control" name="soru[{{ $stage->id }}]" required>
                                <option value="">-Seçiniz-</option>
                                @foreach($bayiler as $bayi)
                                    <option value="{{ $bayi->user_id }}">{{ $bayi->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div> {{-- .col-lg-8 --}}
                @endif {{-- $stage->cevapTuru ifadesinin kapanışı --}}
            </div> {{-- .col-lg-12 --}}
        </div> {{-- .row form-group --}}
    @endforeach {{-- stage_questions foreach --}}

    {{-- Formun diğer kısımları (servis, gelenIslem, gidenIslem, vb.) --}}
    <div class="row">
        <div class="col-lg-12" style="text-align: center;margin-top: 2px;">
            <input type="hidden" name="servis" class="servisid" value="{{ $service_id->id }}"/>
            <input type="hidden" name="gelenIslem" value="{{ json_encode($islem) }}"/>
            <input type="hidden" name="gidenIslem" value="{{ $stage_id->id }}"/>
            <input type="submit" class="btn btn-info btn-sm" value="Kaydet"/>
        </div>
    </div>
</form>
<script>
$(document).ready(function() {

    // Ortak ürün arama fonksiyonu
    $('.product-search-input').on('keyup', function() {
        const searchText = $(this).val().toLowerCase();
        const targetListSelector = $(this).data('target-list'); // Hangi listeyi hedefleyeceğini al
        
        $(this).closest('.form-group').find(targetListSelector + ' .stock-item').each(function() {
            const productName = $(this).data('product-name').toLowerCase();
            const productCode = String($(this).data('product-code')).toLowerCase(); 

            if (productName.includes(searchText) || productCode.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Hem Personel Stoğu hem de Konsinye Cihaz için ortak checkbox değişimi
    $(document).on('change', '.stock-checkbox', function() {
        const quantityInput = $(this).closest('.stock-item').find('.quantity-input');
        const availableQuantity = parseInt($(this).data('available'));

        if ($(this).is(':checked')) {
            quantityInput.attr('max', availableQuantity).val(1).show();
        } else {
            quantityInput.val(1).removeAttr('max').hide();
        }
    });

    // Adet girişlerine sadece sayı girilmesini sağla ve min/max kontrolü yap
    $(document).on('input', '.quantity-input', function() {
        let value = $(this).val();
        const max = parseInt($(this).attr('max'));
        const min = parseInt($(this).attr('min')) || 1; // Belirtilmemişse varsayılan minimum 1

        // Geçerli bir sayı olduğundan emin ol
        value = parseInt(value) || min; // Sayı değilse minimuma ayarla

        if (value > max) {
            alert('Maksimum mevcut adeti (' + max + ') aşamazsınız.');
            value = max;
        } else if (value < min) {
            value = min;
        }
        $(this).val(value);
    });

    // --- Form Gönderimi ---
    $('#servisPlanKaydet').on('submit', function(e) {
        e.preventDefault(); // Formun varsayılan gönderimini engelle

        let formIsValid = true;
        const $form = $(this);

        // Tüm zorunlu alanları (checkbox'lar hariç) kontrol et
        $form.find('[required]').not('.stock-checkbox').each(function() {
            if (!$(this).val()) {
                formIsValid = false;
                $(this).addClass('is-invalid'); // Geçersizse CSS sınıfı ekle
                return false; // Döngüyü kır
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!formIsValid) {
            alert('Lütfen tüm zorunlu alanları doldurun.');
            return; // Geçersizse işlemi durdur
        }

        const formData = new FormData(this);

        // Seçili "Personel Stoğu" ve "Konsinye Cihaz" ürünlerini FormData'ya manuel olarak ekle
        $('.stock-checkbox:checked').each(function() {
            const checkboxName = $(this).attr('name'); // örn: parca[123][456] veya konsinye_cihaz[123][456]
            const stockId = $(this).val();
            const quantityInput = $(this).closest('.stock-item').find('.quantity-input');
            const quantity = quantityInput.val();

            // Soru ID'sini ve ürün türünü (parca veya konsinye_cihaz) checkbox adından çıkar
            const nameParts = checkboxName.match(/^(parca|konsinye_cihaz)\[(\d+)\]\[(\d+)\]$/);
            if (nameParts) {
                const productType = nameParts[1]; // "parca" veya "konsinye_cihaz"
                const stageId = nameParts[2];
                
                formData.append(`${productType}[${stageId}][${stockId}]`, stockId);
                formData.append(`${productType === 'parca' ? 'adet' : 'konsinye_adet'}[${stageId}][${stockId}]`, quantity);
            }
        });

        // İşaretlenmemiş stok checkbox'larının verilerini FormData'dan sil
        // Bu, önceden işaretlenip sonra kaldırılan değerlerin gönderilmesini önler.
        $('.stock-checkbox:not(:checked)').each(function() {
            const fullName = $(this).attr('name'); // örn: parca[123][456]
            const quantityInputName = $(this).closest('.stock-item').find('.quantity-input').attr('name'); // örn: adet[123][456]

            formData.delete(fullName);
            if (quantityInputName) {
                formData.delete(quantityInputName);
            }
        });

        // AJAX isteği gönder
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: formData,
            processData: false, // FormData kullanılırken gerekli
            contentType: false, // FormData kullanılırken gerekli
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);

                    // Alt aşamalar dropdown'ını güncelle
                    if (response.altAsamalar) {
                        const altAsamalarSelect = $('.servisAsamalari .altAsamalar');
                        altAsamalarSelect.empty().append('<option value="">-Seçiniz-</option>');
                        $.each(response.altAsamalar, function(index, item) {
                            altAsamalarSelect.append('<option value="' + item.id + '">' + item.asama + '</option>');
                        });
                        altAsamalarSelect.prop('selectedIndex', 0); // "Seçiniz" seçeneğine sıfırla
                    }

                    // Mevcut aşama bilgisini güncelle
                    $('.servisAsamalari .kayitAlan span').text(response.asama);

                    // Servis geçmişini yeniden yükle (eğer fonksiyon tanımlıysa)
                    if (typeof loadServiceHistory === 'function') {
                        loadServiceHistory({{ $service_id->id }});
                    }

                    // DataTable'ı yeniden yükle (eğer tanımlıysa)
                    if ($.fn.DataTable && $('#datatableService').length) {
                        $('#datatableService').DataTable().ajax.reload();
                    }

                    // Formu gizle
                    $('#servisPlanKaydet').hide();
                } else {
                    alert('Hata: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error('AJAX Hatası:', xhr.responseText);
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        alert('Sunucu Hatası: ' + errorResponse.message);
                    } else if (errorResponse.errors) { // Laravel validation errors
                        let errorMessage = 'Lütfen aşağıdaki hataları düzeltin:\n';
                        $.each(errorResponse.errors, function(key, value) {
                            errorMessage += '- ' + value[0] + '\n';
                        });
                        alert(errorMessage);
                    } else {
                        alert('Bilinmeyen bir hata oluştu.');
                    }
                } catch (e) {
                    alert('AJAX yanıtı işlenirken bir hata oluştu.');
                }
            }
        });
    });

    // --- Başlangıç Ayarları ---
    // Sayfa yüklendiğinde işaretli olmayan stok ürünlerinin adet girişlerini gizle
    $('.stock-checkbox:not(:checked)').closest('.stock-item').find('.quantity-input').hide();
    
    // Konsinye cihaz adet giriş grubunu gizle (bu satır önceki kodda fazlalık olabilir, .stock-checkbox ile hallediyoruz)
    // $('.consignment-quantity-group').hide(); // Bu satır kaldırılabilir veya düzeltilebilir
});
</script>