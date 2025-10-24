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
                    <input id="urunAraInput_stok" type="text" class="form-control urunAraInput" autocomplete="off" placeholder="Ürün adı veya kodu">
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
                    </div>
                    <input type="hidden" name="soru[{{ $stage->id }}]" class="form-control" value="Parca"/>
                
                @elseif($stage->cevapTuru == "[Konsinye Cihaz]")
                    <input id="urunAraInput_konsinye" type="text" class="form-control urunAraInput" autocomplete="off" placeholder="Konsinye cihaz adı veya kodu">
                    <div class="konsinye-dropdown myKonsinyeList" style="width:100%">
                        @php $konsinye_say = 0; @endphp
                        <p>Toplam Konsinye Cihaz Sayısı: {{ $toplamKonsinyeCihazAdedi }}</p>

                        @forelse($konsinyeCihazlar as $konsinyeCihaz)
                            @php
                                $konsinyeId = $konsinyeCihaz->id;
                                $konsinyeAdet = $konsinyeCihaz->current_stock_quantity ?? 0;
                            @endphp

                            @if($konsinyeAdet > 0)
                                @php $konsinye_say++; @endphp
                                <div class="checkbox stock-item" style="padding:3px 0;" 
                                     data-product-code="{{ $konsinyeCihaz->urunKodu ?? '' }}" 
                                     data-product-name="{{ $konsinyeCihaz->urunAdi ?? $konsinyeCihaz->urun_adi ?? 'N/A' }}">
                                    <label style="width: calc(100% - 40px); display: inline-block; text-transform: capitalize;">
                                        <input type="checkbox" name="konsinye_cihaz[{{ $stage->id }}][{{ $konsinyeId }}]"
                                            class="consignment-checkbox"
                                            value="{{ $konsinyeId }}"
                                            data-available="{{ $konsinyeAdet }}"
                                            style="position: relative; top:2px; margin-right:3px;">
                                        {{ $konsinyeCihaz->urunAdi ?? $konsinyeCihaz->urun_adi ?? 'Ürün Adı Bulunamadı' }} (Mevcut: {{ $konsinyeAdet }})
                                    </label>
                                    <input type="number" name="konsinye_adet[{{ $stage->id }}][{{ $konsinyeId }}]"
                                        value="1" min="1" max="{{ $konsinyeAdet }}"
                                        class="form-control quantity-input consignment-quantity-input"
                                        autocomplete="off"
                                        style="width: 40px; display: inline-block; text-align: center; display: none;">
                                </div>
                            @endif
                        @empty
                        @endforelse

                        @if($konsinye_say == 0)
                            <label style="color:red">Uyumlu Konsinye Cihaz Bulunamadı.</label>
                        @endif
                    </div>
                    <input type="hidden" name="soru[{{ $stage->id }}]" class="form-control" value="Konsinye Cihaz"/>
                
                {{-- GRUP TÜRLERİ İÇİN GÜNCELLEME --}}
                @elseif(str_contains($stage->cevapTuru, 'Grup'))
                    @php
                        // Birden fazla grup değeri kontrol et (örn: "[Grup-4], [Grup-5]")
                        $gruplar = [];
                        preg_match_all('/\[Grup-(\d+)\]/', $stage->cevapTuru, $matches);
                        
                        if (!empty($matches[1])) {
                            $gruplar = $matches[1];
                        }
                        
                        // Her grup için rol belirleme
                        $grupRolleri = [];
                        foreach ($gruplar as $grupKodu) {
                            if (in_array($grupKodu, ['261', '262'])) {
                                $grupRolleri[$grupKodu] = ['rol' => 'Atölye Ustası', 'label' => 'Atölye Ustası'];
                            } elseif ($grupKodu == '4') {
                                $grupRolleri[$grupKodu] = ['rol' => 'Teknisyen', 'label' => 'Teknisyen'];
                            } elseif ($grupKodu == '5') {
                                $grupRolleri[$grupKodu] = ['rol' => 'Teknisyen', 'label' => 'Teknisyen Yardımcısı'];
                            }
                        }
                    @endphp
                    
                    @if(count($grupRolleri) > 0)
                        <div class="grup-inputs-container">
                            @foreach($grupRolleri as $grupKodu => $grupBilgi)
                                @php
                                    // Her grup için ilgili personelleri al
                                    $grupPersoneller = App\Models\User::where('tenant_id', $firma->id)
                                        ->where('status', '1')
                                        ->whereHas('roles', function($query) use ($grupBilgi) {
                                            $query->where('name', $grupBilgi['rol']);
                                        })
                                        ->orderBy('name', 'asc')
                                        ->get();
                                @endphp
                                
                                <div class="grup-input-row" style="margin-bottom: 10px;">
                                    <label style="font-size: 13px; color: #666; margin-bottom: 5px;">
                                        {{ $grupBilgi['label'] }}:
                                    </label>
                                    
                                    @if($grupPersoneller->count())
                                        <select class="form-control" name="soru_grup_{{ $grupKodu }}[{{ $stage->id }}]" 
                                                @if(count($grupRolleri) == 1) required @endif>
                                            <option value="">-Seçiniz-</option>
                                            @foreach($grupPersoneller as $personel)
                                                <option value="{{ $personel->user_id }}">{{ $personel->name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <p style="color: #999; font-style: italic;">Bu gruba ait personel bulunamadı.</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                        {{-- Seçilen değerleri birleştirmek için hidden input --}}
                        <input type="hidden" name="soru[{{ $stage->id }}]" class="grup-combined-value" value="">
                    @else
                        <p style="color: red;">Grup tanımlaması bulunamadı.</p>
                    @endif
                
                {{-- Diğer Cevap Türleri --}}
                @else
                    <div class="col-lg-12"> 
                        @if($stage->cevapTuru == "[Aciklama]")
                            <input type="text" name="soru[{{ $stage->id }}]" class="form-control" autocomplete="off" />
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
                            <select class="form-control" name="soru[{{ $stage->id }}]">
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
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    {{-- Formun diğer kısımları --}}
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
    // Ürün Arama Filtreleme
    $(document).on('keyup', '.urunAraInput', function() {
        const searchText = $(this).val().toLowerCase();
        $(this).closest('.form-group').find('.stock-item').each(function() {
            const productName = $(this).data('product-name').toLowerCase();
            const productCode = String($(this).data('product-code')).toLowerCase();

            if (productName.includes(searchText) || productCode.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Personel Stoğu checkbox değiştiğinde
    $(document).on('change', '.stock-checkbox', function() {
        const quantityInput = $(this).closest('.stock-item').find('.quantity-input');
        const availableQuantity = parseInt($(this).data('available'));

        if ($(this).is(':checked')) {
            quantityInput.attr('max', availableQuantity).val(1).show();
        } else {
            quantityInput.val(1).removeAttr('max').hide();
        }
    });

    // Konsinye Cihaz checkbox değiştiğinde
    $(document).on('change', '.consignment-checkbox', function() {
        const quantityInput = $(this).closest('.stock-item').find('.consignment-quantity-input');
        const availableQuantity = parseInt($(this).data('available'));

        if ($(this).is(':checked')) {
            quantityInput.attr('max', availableQuantity).val(1).show();
        } else {
            quantityInput.val(1).removeAttr('max').hide();
        }
    });

    // Grup select değiştiğinde - birden fazla grup değerini birleştir
    $(document).on('change', '[name^="soru_grup_"]', function() {
        const container = $(this).closest('.grup-inputs-container');
        const hiddenInput = container.siblings('.grup-combined-value');
        const selectedValues = [];
        
        // Tüm grup selectlerinden değerleri topla
        container.find('select[name^="soru_grup_"]').each(function() {
            const value = $(this).val();
            if (value) {
                selectedValues.push(value);
            }
        });
        
        // Değerleri virgülle ayırarak hidden inputa yaz
        hiddenInput.val(selectedValues.join(','));
    });

    // Adet girişlerine sadece sayı girilmesini sağla
    $(document).on('input', '.quantity-input', function() {
        let value = $(this).val();
        const max = parseInt($(this).attr('max'));
        const min = parseInt($(this).attr('min')) || 1;

        value = parseInt(value) || min;

        if (value > max) {
            alert('Maksimum mevcut adeti (' + max + ') aşamazsınız.');
            value = max;
        } else if (value < min) {
            value = min;
        }
        $(this).val(value);
    });

    // Form Gönderimi
    $('#servisPlanKaydet').on('submit', function(e) {
        e.preventDefault();

        let formIsValid = true;
        const $form = $(this);

        // Grup inputları için özel validasyon
        $form.find('.grup-inputs-container').each(function() {
            const container = $(this);
            const hiddenInput = container.siblings('.grup-combined-value');
            const selects = container.find('select[name^="soru_grup_"]');
            
            // En az bir grup seçilmeli
            let hasSelection = false;
            selects.each(function() {
                if ($(this).val()) {
                    hasSelection = true;
                }
            });
            
            if (selects.length === 1 && !hasSelection) {
                // Tek grup varsa zorunlu
                formIsValid = false;
                selects.addClass('is-invalid');
            } else if (selects.length > 1 && !hasSelection) {
                // Birden fazla grup varsa en az biri seçilmeli (opsiyonel kontrol)
                // İsterseniz bu kontrolü kaldırabilirsiniz
            } else {
                selects.removeClass('is-invalid');
            }
        });

        // Diğer zorunlu alanları kontrol et
        $form.find('[required]').not('.stock-checkbox').not('[name^="soru_grup_"]').each(function() {
            if (!$(this).val()) {
                formIsValid = false;
                $(this).addClass('is-invalid');
                return false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!formIsValid) {
            alert('Lütfen tüm zorunlu alanları doldurun.');
            return;
        }

        const formData = new FormData(this);

        // Seçili stok ürünlerini ekle
        $('.stock-checkbox:checked').each(function() {
            const stageId = $(this).closest('.form-group').find('label').closest('.col-lg-12').find('input[name^="soru["]').attr('name').match(/\[(\d+)\]/)[1];
            const stockId = $(this).val();
            const quantityInput = $(this).closest('.stock-item').find('.quantity-input');
            const quantity = quantityInput.val();

            formData.append(`parca[${stageId}][${stockId}]`, stockId);
            formData.append(`adet[${stageId}][${stockId}]`, quantity);
        });

        // Seçili konsinye cihazları ekle
        $('.consignment-checkbox:checked').each(function() {
            const stageId = $(this).closest('.form-group').find('label').closest('.col-lg-12').find('input[name^="soru["]').attr('name').match(/\[(\d+)\]/)[1];
            const consignmentId = $(this).val();
            const quantityInput = $(this).closest('.stock-item').find('.consignment-quantity-input');
            const quantity = quantityInput.val();

            formData.append(`konsinye_cihaz[${stageId}][${consignmentId}]`, consignmentId);
            formData.append(`konsinye_adet[${stageId}][${consignmentId}]`, quantity);
        });

        // İşaretlenmemiş checkbox'ları temizle
        $('.stock-checkbox:not(:checked)').each(function() {
            const fullName = $(this).attr('name');
            const quantityInputName = $(this).closest('.stock-item').find('.quantity-input').attr('name');
            formData.delete(fullName);
            if (quantityInputName) {
                formData.delete(quantityInputName);
            }
        });

        // AJAX isteği
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: formData,
            processData: false,
            contentType: false,
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
                        altAsamalarSelect.prop('selectedIndex', 0);
                    }

                    // Mevcut aşama bilgisini güncelle
                    $('.servisAsamalari .kayitAlan span').text(response.asama);

                    // Servis geçmişini yeniden yükle
                    if (typeof loadServiceHistory === 'function') {
                        loadServiceHistory({{ $service_id->id }});
                    }

                    // DataTable'ı yeniden yükle
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
                    } else if (errorResponse.errors) {
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

    // Başlangıç Ayarları
    $('.stock-checkbox:not(:checked)').closest('.stock-item').find('.quantity-input').hide();
    $('.consignment-quantity-group').hide();
});
</script>