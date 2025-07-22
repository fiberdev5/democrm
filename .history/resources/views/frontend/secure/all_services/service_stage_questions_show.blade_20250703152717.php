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
            {{-- Ek olarak gizli bir input daha ekliyoruz --}}
            <input type="hidden" name="parca[{{ $stage->id }}][consignment_name]" class="consignment-device-name-input" value=""/>
            {{-- ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ BURAYI EKLEYİN ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ --}}

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
    // Mevcut keyup fonksiyonunuzu sadece .myParcaList içindeki checkbox'ları etkileyecek şekilde güncelledim
    // Konsinye dropdown'u için ayrıca bir arama inputu eklemedim, isteğe bağlı eklenebilir.
    $('#urunAraInput').on('keyup', function() {
        var searchText = $(this).val().toLowerCase();
        $(this).closest('.form-group').find('.myParcaList .checkbox').each(function() { // Sadece bu section'daki parçaları etkile
            var productName = $(this).find('label').text().toLowerCase();
            var productCode = $(this).data('product-code');
            if (productCode !== undefined && productCode !== null) {
                productCode = String(productCode).toLowerCase();
            } else {
                productCode = '';
            }
            if (productName.includes(searchText) || productCode.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Konsinye cihaz dropdown değiştiğinde adet inputunu göster/gizle ve ayarla
    $(document).on('change', '.consignment-device-select', function() {
        var selectedOption = $(this).find('option:selected');
        var quantityGroup = $(this).closest('.col-lg-12').find('.consignment-quantity-group');
        var quantityInput = quantityGroup.find('.quantity-input');
        var availableQuantityText = quantityGroup.find('.available-quantity-text');

        if (selectedOption.val()) { // Bir cihaz seçildiyse
            var available = parseInt(selectedOption.data('available'));
            quantityInput.attr('max', available); // Maksimum adeti ayarla
            quantityInput.val(1); // Varsayılan adeti 1 yap
            availableQuantityText.text('(Mevcut: ' + available + ')');
            quantityGroup.show(); // Adet input grubunu göster
        } else { // Hiçbir cihaz seçilmediyse
            quantityGroup.hide(); // Adet input grubunu gizle
            quantityInput.val(1); // Adeti sıfırla (gizli olsa da)
            quantityInput.removeAttr('max'); // Max özelliğini kaldır
            availableQuantityText.text('(Mevcut: 0)');
        }
    });

    // Sayı inputlarına sadece sayı girilmesini sağla (hem personel hem konsinye için)
    $(document).on('keypress', '.quantity-input', function(e) {
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            e.preventDefault();
        }
    });

    // Maksimum değeri kontrol et (hem personel hem konsinye için)
    $(document).on('change', '.quantity-input', function() {
        var max = parseInt($(this).attr('max'));
        var val = parseInt($(this).val());
        if (val > max) {
            alert('Maksimum mevcut adeti (' + max + ') aşamazsınız.');
            $(this).val(max);
        } else if (val < 1) {
            $(this).val(1);
        }
    });

    // Form submit edildiğinde FormData'yı güncelle
    $('#servisPlanKaydet').on('submit', function(e) {
        e.preventDefault(); 

        var formIsValid = true;
        // Tüm required alanları kontrol et, hem checkbox hem de select/inputlar için
        $(this).find('[required]').each(function() {
            if ($(this).is(':checkbox')) {
                // Eğer checkbox'larrequired ise ve işaretli değilse
                // NOT: Konsinye cihazlar artık checkbox değil, bu kısım personel stokları için geçerli.
                // Personel stokları için checkbox'lar hala required olarak işaretlenirse bu kontrol çalışır.
                // Eğer personel stok checkbox'ları da opsiyonel ise bu kısmı kaldırabilirsiniz.
                // if (!$(this).is(':checked')) {
                //     formIsValid = false;
                //     return false;
                // }
            } else if (!$(this).val()) { // Diğer tüm required input ve selectler için
                formIsValid = false;
                return false;
            }
        });

        // Ek konsinye cihaz seçimi ve adet kontrolü
        $('.consignment-device-select').each(function() {
            if ($(this).attr('required') && !$(this).val()) {
                formIsValid = false;
                return false;
            }
            if ($(this).val()) { // Eğer bir konsinye cihaz seçilmişse, adet kontrolü yap
                var quantityInput = $(this).closest('.col-lg-12').find('.consignment-quantity-group .quantity-input');
                if (!quantityInput.val() || parseInt(quantityInput.val()) <= 0) {
                    formIsValid = false;
                    alert('Lütfen konsinye cihaz adedini girin.');
                    return false;
                }
            }
        });


        if (!formIsValid) {
            alert('Lütfen tüm zorunlu alanları doldurun.');
            return;
        }

        var formData = new FormData(this);

        // Personel Stokları (Checkbox'lar)
        $('.stock-checkbox:checked').each(function() {
            const full_name = $(this).attr('name');
            const value = $(this).val();
            const quantityInput = $(this).closest('.checkbox').find('.quantity-input'); // `.stock-item` yerine `.checkbox` kullandım, sizin HTML'e göre ayarlayın
            const quantity = quantityInput.val();

            if (quantity && parseInt(quantity) > 0) {
                formData.append(full_name, value);
                formData.append(quantityInput.attr('name'), quantity);
            }
        });

        // Konsinye Cihaz (Dropdown)
        $('.consignment-device-select').each(function() {
            var selectedValue = $(this).val();
            if (selectedValue) { // Eğer bir cihaz seçildiyse
                var stageId = $(this).data('stage-id');
                var quantityInput = $(this).closest('.col-lg-12').find('.consignment-quantity-group .quantity-input');
                var quantity = quantityInput.val();

                // parca[stage_id][C_device_id] formatı için
                formData.append('parca[' + stageId + '][' + selectedValue + ']', selectedValue);
                // adet[stage_id][selected_consignment_quantity] formatı için
                formData.append('adet[' + stageId + '][selected_consignment_quantity]', quantity);
            }
        });

        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    // ... (Mevcut başarılı işlem kodlarınız) ...
                    if (response.altAsamalar) {
                        var altAsamalarSelect = $('.servisAsamalari .altAsamalar');
                        altAsamalarSelect.empty();
                        altAsamalarSelect.append('<option value="">-Seçiniz-</option>');

                        $.each(response.altAsamalar, function(index, item) {
                            altAsamalarSelect.append('<option value="' + item.id + '">' + item.asama + '</option>');
                        });
                        altAsamalarSelect.prop('selectedIndex', 0);
                    }
                    $('.servisAsamalari .kayitAlan span').text(response.asama);
                    if (typeof loadServiceHistory === 'function') {
                        loadServiceHistory({{ $service_id->id }});
                    }
                    if ($.fn.DataTable && $('#datatableService').length) {
                        $('#datatableService').DataTable().ajax.reload();
                    }
                    $('#servisPlanKaydet').hide(); 
                } else {
                    alert('Hata: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                try {
                    var errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        alert('Sunucu Hatası: ' + errorResponse.message);
                    } else {
                        alert('Bilinmeyen bir hata oluştu.');
                    }
                } catch (e) {
                    alert('AJAX yanıtı işlenirken bir hata oluştu.');
                }
            }
        });
    });
});
</script>