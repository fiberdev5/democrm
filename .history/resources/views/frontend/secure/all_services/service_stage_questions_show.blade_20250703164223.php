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
                    <input id="urunAraInput" type="text" class="form-control" data-id="" autocomplete="off" autofocus="on" placeholder="Ürün adı veya ürün kodu">
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
    <div class="row">
        <div class="col-lg-12">
            <p>Toplam Konsinye Cihaz Stoğu: {{ $toplamConsignmentAdedi }}</p>
            <select name="parca[{{ $stage->id }}][consignment_select]"
                    class="form-control consignment-device-select"
                    data-stage-id="{{ $stage->id }}"
                    required>
                <option value="">-- Konsinye Cihaz Seçiniz --</option>
                @foreach($consignmentDevices as $device)
                    @if($device->current_stock_quantity > 0)
                        <option value="C_{{ $device->id }}"
                                data-available="{{ $device->current_stock_quantity }}"
                                data-product-name="{{ $device->urunAdi ?? $device->urun_adi ?? 'N/A' }}"
                                data-product-code="{{ $device->urunKodu ?? '' }}">
                            {{ $device->urunAdi ?? $device->urun_adi ?? 'Cihaz Adı Bulunamadı' }} (Mevcut: {{ $device->current_stock_quantity }})
                        </option>
                    @endif
                @endforeach
            </select>
            <div class="form-group consignment-quantity-group" style="display:none; margin-top: 10px;">
                <label for="consignment-quantity-{{ $stage->id }}">Adet:</label>
                <input type="number"
                       name="adet[{{ $stage->id }}][selected_consignment_quantity]"
                       id="consignment-quantity-{{ $stage->id }}"
                       value="1"
                       min="1"
                       class="form-control quantity-input"
                       autocomplete="off"
                       style="width: 80px; display:inline-block;">
                <span class="text-muted available-quantity-text">(Mevcut: 0)</span>
            </div>
            <input type="hidden" name="parca[{{ $stage->id }}][consignment_name]" class="consignment-device-name-input" value=""/>
            @if($toplamConsignmentAdedi == 0)
                <label style="color:red; margin-top: 5px;">Uyumlu Konsinye Cihaz Bulunamadı.</label>
            @endif
        </div>
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
    // --- Olay Dinleyiciler ---

    // Ürün Arama (Personel Stoğu) Filtreleme
    $('#urunAraInput').on('keyup', function() {
        const searchText = $(this).val().toLowerCase();
        // Sadece bu form grubu içindeki .myParcaList'teki ürünleri filtrele
        $(thsis).closest('.form-group').find('.myParcaList .stock-item').each(function() {
            const productName = $(this).data('product-name').toLowerCase();
            const productCode = String($(this).data('product-code')).toLowerCase(); // String olduğundan emin ol

            if (productName.includes(searchText) || productCode.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Personel Stoğu checkbox değiştiğinde adet girişini göster/gizle ve maksimumu ayarla
    $(document).on('change', '.stock-checkbox', function() {
        const quantityInput = $(this).closest('.stock-item').find('.quantity-input');
        const availableQuantity = parseInt($(this).data('available'));

        if ($(this).is(':checked')) {
            quantityInput.attr('max', availableQuantity).val(1).show();
        } else {
            quantityInput.val(1).removeAttr('max').hide();
        }
    });

    // Konsinye Cihaz dropdown değiştiğinde adet girişini yönet
    $(document).on('change', '.consignment-device-select', function() {
        const selectedOption = $(this).find('option:selected');
        const quantityGroup = $(this).closest('.col-lg-12').find('.consignment-quantity-group');
        const quantityInput = quantityGroup.find('.quantity-input');
        const availableQuantityText = quantityGroup.find('.available-quantity-text');
        const consignmentNameInput = $(this).closest('.col-lg-12').find('.consignment-device-name-input');

        if (selectedOption.val()) { // Bir cihaz seçildiyse
            const available = parseInt(selectedOption.data('available'));
            const productName = selectedOption.data('product-name');

            quantityInput.attr('max', available).val(1);
            availableQuantityText.text('(Mevcut: ' + available + ')');
            consignmentNameInput.val(productName); // Gizli inputa ürün adını set et
            quantityGroup.show();
        } else { // Hiçbir cihaz seçilmediyse
            quantityGroup.hide();
            quantityInput.val(1).removeAttr('max');
            availableQuantityText.text('(Mevcut: 0)');
            consignmentNameInput.val(''); // Gizli inputu temizle
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

        // Konsinye Cihaz seçilmişse adetini kontrol et
        $form.find('.consignment-device-select').each(function() {
            if ($(this).val()) { // Bir cihaz seçilmişse
                const quantityInput = $(this).closest('.col-lg-12').find('.consignment-quantity-group .quantity-input');
                if (!quantityInput.val() || parseInt(quantityInput.val()) <= 0) {
                    formIsValid = false;
                    quantityInput.addClass('is-invalid');
                    alert('Lütfen konsinye cihaz adedini girin ve geçerli bir değer olduğundan emin olun.');
                    return false; // Döngüyü kır
                } else {
                    quantityInput.removeClass('is-invalid');
                }
            }
        });

        if (!formIsValid) {
            alert('Lütfen tüm zorunlu alanları doldurun.');
            return; // Geçersizse işlemi durdur
        }

        const formData = new FormData(this);

        // Seçili "Personel Stoğu" ürünlerini FormData'ya manuel olarak ekle
        $('.stock-checkbox:checked').each(function() {
            // Soru ID'sini en yakın form grubundan al
            const stageId = $(this).closest('.form-group').find('label').closest('.col-lg-12').find('input[name^="soru["]').attr('name').match(/\[(\d+)\]/)[1];
            const stockId = $(this).val();
            const quantityInput = $(this).closest('.stock-item').find('.quantity-input');
            const quantity = quantityInput.val();

            formData.append(`parca[${stageId}][${stockId}]`, stockId);
            formData.append(`adet[${stageId}][${stockId}]`, quantity);
        });

        // Seçili "Konsinye Cihaz"ı FormData'ya manuel olarak ekle
        $('.consignment-device-select').each(function() {
            const selectedValue = $(this).val();
            if (selectedValue) {
                const stageId = $(this).data('stage-id');
                const quantityInput = $(this).closest('.col-lg-12').find('.consignment-quantity-group .quantity-input');
                const quantity = quantityInput.val();
                const productName = $(this).find('option:selected').data('product-name');

                // Backend'e gönderilecek format
                formData.append(`parca[${stageId}][consignment_id]`, selectedValue.replace('C_', '')); // Sadece ID'yi gönder
                formData.append(`adet[${stageId}][consignment_quantity]`, quantity);
                formData.append(`parca[${stageId}][consignment_name]`, productName); // Referans için ürün adını da ekle
            }
        });

        // İşaretlenmemiş personel stoğu checkbox'larının verilerini FormData'dan sil
        // Bu, önceden işaretlenip sonra kaldırılan değerlerin gönderilmesini önler.
        $('.stock-checkbox:not(:checked)').each(function() {
            const fullName = $(this).attr('name');
            const quantityInputName = $(this).closest('.stock-item').find('.quantity-input').attr('name');
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
    // Konsinye cihaz adet giriş grubunu gizle
    $('.consignment-quantity-group').hide();
});
</script>