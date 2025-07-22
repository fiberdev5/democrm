<div class="container">
    <div class="row">
        <div class="col-md-12">
            <form method="POST" id="servisPlanGuncelle" action="{{route('update.service.plan', $tenant_id)}}">
                @csrf
                <input type="hidden" name="planid" value="{{ $servisPlan->id }}">
                <input type="hidden" name="tenant_id" value="{{ $tenant_id }}">

                {{-- İşlemi Yapan Personel Seçimi --}}
                <div class="row form-group">
                    <div class="col-lg-4">
                        <label>İşlemi Yapan</label>
                    </div>
                    <div class="col-lg-8">
                        <select name="planIslemiYapan" class="form-control planIslemiYapan">
                            @foreach($personellerAll as $personel)
                                <option value="{{ $personel->user_id }}"
                                    {{ $personel->user_id == $servisPlan->pid ? 'selected' : '' }}>
                                    {{ $personel->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- Plan Cevapları --}}
                @foreach($planCevaplar as $plan)
                    @php
                        $soru = App\Models\StageQuestion::find($plan->soruid);
                    @endphp

                    @if($soru->cevapTuru == "[Parca]")
                        <div class="row form-group">
                            <div class="col-lg-12">
                                <label>{{ $soru->soru }}</label>
                                @php
                                    $kullanilanParcalarArray = [];
                                    if ($plan->cevap) {
                                        $cevaplarArray = explode(', ', $plan->cevap);
                                        foreach ($cevaplarArray as $cevapItem) {
                                            list($itemStokId, $itemAdet) = array_pad(explode('---', $cevapItem), 2, 0);
                                            $kullanilanStok = App\Models\Stock::find($itemStokId); // Kullanılan stoğu bul
                                            if ($kullanilanStok) {
                                                $kullanilanParcalarArray[] = $kullanilanStok->urunAdi . ' (Adet: ' . $itemAdet . ')';
                                            }
                                        }
                                    }
                                @endphp

                                @if(!empty($kullanilanParcalarArray))
                                    <div style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;">
                                        <ul>
                                            @foreach($kullanilanParcalarArray as $parcaText)
                                                <li>{{ $parcaText }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <input id="urunAraInput_edit_{{ $plan->id }}" type="text" class="form-control urunAraInput" autocomplete="off" autofocus="on" placeholder="Ürün adı veya ürün kodu">
                                <div class="parcalar-dropdown myParcaList" data-soru-id="{{ $plan->id }}" style="width:100%">
                                    <p>Toplam Personel Stok Sayısı: {{ $toplamPersonelStokAdedi }}</p>

                                    @forelse($stoklar as $personelStokKaydi)
                                        @php
                                            $stok = $personelStokKaydi->stok; 
                                            $stokId = $stok->id;
                                            $stokAdet = $personelStokKaydi->adet;

                                            $isChecked = false;
                                            $selectedAdet = 1;
                                            if ($plan->cevap) {
                                                $cevaplarArray = explode(', ', $plan->cevap);
                                                foreach ($cevaplarArray as $cevapItem) {
                                                    list($itemStokId, $itemAdet) = array_pad(explode('---', $cevapItem), 2, 0);
                                                    if ($itemStokId == $stokId) {
                                                        $isChecked = true;
                                                        $selectedAdet = $itemAdet;
                                                        break;
                                                    }
                                                }
                                            }
                                        @endphp

                                        @if($stok && $stokAdet > 0)
                                            <div class="checkbox stock-item" style="padding:3px 0;" data-product-name="{{ $stok->urunAdi ?? $stok->urun_adi }}" data-product-code="{{ $stok->urunKodu ?? '' }}">
                                                <label style="width: calc(100% - 40px);display: inline-block;text-transform: capitalize;">
                                                    <input type="checkbox" name="parca[{{ $plan->id }}][{{ $stokId }}]" value="{{ $stokId }}" class="stock-checkbox" data-available="{{ $stokAdet }}" style="position: relative; top:2px; margin-right:3px;" {{ $isChecked ? 'checked' : '' }}>
                                                    {{ $stok->urunAdi ?? $stok->urun_adi ?? 'Ürün Adı Bulunamadı' }} (Mevcut: {{ $stokAdet }})
                                                </label>
                                                <input type="number" name="adet[{{ $plan->id }}][{{ $stokId }}]" value="{{ $selectedAdet }}" min="1" max="{{ $stokAdet }}" class="form-control quantity-input" autocomplete="off" style="width: 40px;display: inline-block;text-align:center; {{ $isChecked ? '' : 'display:none;' }}">
                                            </div>
                                        @endif
                                    @empty
                                    @endforelse
                                    @if($stoklar->isEmpty())
                                        <label style="color:red">Uyumlu Parça Bulunamadı.</label>
                                    @endif
                                </div>
                                <input type="hidden" name="soru{{ $plan->id }}" class="form-control" value="Parca"/>
                            </div>
                        </div>
                    @elseif($soru->cevapTuru == "[Konsinye Cihaz]")
                        <div class="row form-group">
                            <div class="col-lg-4">
                                <label>{{ $soru->soru }}</label>
                            </div>
                            <div class="col-lg-8">
                                @php
                                    $currentConsignmentId = null;
                                    $currentConsignmentQuantity = 1;
                                    $currentConsignmentName = '';
                                    $selectedConsignmentDeviceCurrentStock = 0;

                                    // stored 'cevap' string'ini ayrıştır
                                    if ($plan->cevap) {
                                        // Yeni format: "Cihaz Adı: C_ID---Adet"
                                        if (strpos($plan->cevap, ': C_') !== false) {
                                            $mainParts = explode(': C_', $plan->cevap);
                                            $currentConsignmentName = $mainParts[0];
                                            $deviceInfo = 'C_' . $mainParts[1];
                                            
                                            $parts = explode('---', substr($deviceInfo, 2));
                                            if (count($parts) === 2) {
                                                $currentConsignmentId = $parts[0];
                                                $currentConsignmentQuantity = (int)$parts[1];
                                            }
                                        } 
                                        // Eski format: "C_ID---Adet"
                                        elseif (str_starts_with($plan->cevap, 'C_')) {
                                            $parts = explode('---', substr($plan->cevap, 2));
                                            if (count($parts) === 2) {
                                                $currentConsignmentId = $parts[0];
                                                $currentConsignmentQuantity = (int)$parts[1];
                                            }
                                        }

                                        if ($currentConsignmentId) {
                                            $originalDevice = App\Models\Stock::find($currentConsignmentId);
                                            if ($originalDevice) {
                                                // Eğer cihaz adı yoksa, veritabanından al
                                                if (!$currentConsignmentName) {
                                                    $currentConsignmentName = $originalDevice->urunAdi ?? $originalDevice->urun_adi ?? 'Cihaz Adı Bulunamadı';
                                                }
                                                
                                                $girisAdet = App\Models\StockAction::where('stokId', $currentConsignmentId)
                                                    ->whereIn('islem', [1, 4])
                                                    ->sum('adet');
                                                $cikisAdet = App\Models\StockAction::where('stokId', $currentConsignmentId)
                                                    ->where('islem', 2)
                                                    ->sum('adet');
                                                $selectedConsignmentDeviceCurrentStock = max(0, $girisAdet - $cikisAdet);
                                            }
                                        }
                                    }
                                @endphp

                                {{-- Mevcut seçili cihazı göster --}}
                                @if($currentConsignmentName && $currentConsignmentId)
                                    <div style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;">
                                        <strong>Seçili Cihaz:</strong> {{ $currentConsignmentName }} (Adet: {{ $currentConsignmentQuantity }})
                                    </div>
                                @endif

                                {{-- Konsinye cihaz seçimi dropdown'u --}}
                                <select name="consignment_device_id_{{ $plan->id }}" class="form-control consignment-device-select" data-stage-id="{{ $plan->id }}">
                                    <option value="">- Seçiniz -</option>
                                    @foreach($tumConsinyeCihazlar as $device)
                                        @php
                                            $girisAdet = App\Models\StockAction::where('stokId', $device->id)
                                                ->whereIn('islem', [1, 4])
                                                ->sum('adet');
                                            $cikisAdet = App\Models\StockAction::where('stokId', $device->id)
                                                ->where('islem', 2)
                                                ->sum('adet');
                                            $availableStock = max(0, $girisAdet - $cikisAdet);
                                        @endphp
                                        <option value="C_{{ $device->id }}"
                                                data-product-name="{{ $device->urunAdi ?? $device->urun_adi }}"
                                                data-available="{{ $availableStock }}"
                                                {{ $currentConsignmentId == $device->id ? 'selected' : '' }}>
                                            {{ $device->urunAdi ?? $device->urun_adi }} (Mevcut: {{ $availableStock }})
                                        </option>
                                    @endforeach
                                </select>

                                {{-- Adet giriş alanı --}}
                                <div class="consignment-quantity-group" style="display: {{ $currentConsignmentId ? 'block' : 'none' }}; margin-top: 10px;">
                                    <label>Adet:</label>
                                    <input type="number"
                                            name="consignment_quantity_{{ $plan->id }}"
                                            class="form-control quantity-input"
                                            value="{{ $currentConsignmentQuantity }}"
                                            min="1"
                                            max="{{ $selectedConsignmentDeviceCurrentStock }}"
                                            style="width: 100px; display: inline-block;">
                                    <span class="available-quantity-text" style="margin-left: 10px; color: #555;">
                                        (Mevcut: {{ $selectedConsignmentDeviceCurrentStock }})
                                    </span>
                                </div>
                                
                                {{-- Cihaz adını tutan hidden input --}}
                                <input type="hidden" name="parca[{{ $plan->id }}][consignment_name]" 
                                        class="consignment-device-name-input" 
                                        value="{{ $currentConsignmentName }}"/>
                                
                                {{-- Gizli input - JavaScript ile C_ID---Adet formatında güncellenecek --}}
                                <input type="hidden"
                                        name="soru{{ $plan->id }}"
                                        id="consignment-combined-value-{{ $plan->id }}"
                                        value="{{ $plan->cevap ?? '' }}"/>
                            </div>
                        </div>
                    @else
                        {{-- Diğer Soru Tipleri --}}
                        <div class="row form-group">
                            <div class="col-lg-4">
                                <label>{{ $soru->soru }}</label>
                            </div>
                            <div class="col-lg-8">
                                @if($soru->cevapTuru == "[Aciklama]")
                                    <input type="text" name="soru{{ $plan->id }}" class="form-control" value="{{ $plan->cevap }}">
                                @elseif(strpos($soru->cevapTuru, 'Grup') !== false)
                                    {{-- Grup Seçimi --}}
                                    @if(strpos($soru->cevapTuru, 'Grup-0') !== false)
                                        <select class="form-control" name="soru{{ $plan->id }}">
                                            @php
                                                $adminPersonel = App\Models\User::where('tenant_id', $tenant_id)
                                                    ->where('status', '1')
                                                    ->whereHas('roles', function($query) {
                                                        $query->where('name', 'Admin');
                                                    })
                                                    ->orderBy('name', 'asc')
                                                    ->get();
                                            @endphp
                                            @foreach($adminPersonel as $personel)
                                                <option value="{{ $personel->user_id }}"
                                                    {{ $plan->cevap == $personel->user_id ? 'selected' : '' }}>
                                                    {{ $personel->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        {{-- Belirli Grup Personelleri --}}
                                        @php
                                            $gruplar = [];
                                            $soruGruplar = explode(', ', $soru->cevapTuru);
                                            foreach($soruGruplar as $grup) {
                                                $grupId = substr(explode('-', $grup)[1], 0, -1);
                                                $gruplar[] = $grupId;
                                            }
                                            $grupPersoneller = App\Models\User::where('tenant_id', $tenant_id)
                                                ->where('status', '1')
                                                ->whereHas('roles', function($query) {
                                                    $query->whereIn('name', ['Teknisyen', 'Teknisyen Yardımcısı']);
                                                })
                                                ->with('roles')
                                                ->orderBy('name', 'asc')
                                                ->get();
                                        @endphp
                                        <select class="form-control" name="soru{{ $plan->id }}">
                                            <option value="">-Seçiniz-</option>
                                            @foreach($grupPersoneller as $personel)
                                                <option value="{{ $personel->user_id }}"
                                                    {{ $plan->cevap == $personel->user_id ? 'selected' : '' }}>
                                                    {{ $personel->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif

                                @elseif($soru->cevapTuru == "[Tarih]")
                                    <input type="date" name="soru{{ $plan->id }}" class="form-control datepicker" value="{{ $plan->cevap }}" style="background:#fff;">
                                @elseif($soru->cevapTuru == "[Saat]")
                                    <select class="form-control" name="soru{{ $plan->id }}">
                                        @php
                                            $saatler = [
                                                '08:00-10:00', '09:00-11:00', '10:00-12:00', '11:00-13:00',
                                                '12:00-14:00', '13:00-15:00', '14:00-16:00', '15:00-17:00',
                                                '16:00-18:00', '17:00-19:00', '18:00-20:00', '19:00-21:00',
                                                '20:00-22:00', '21:00-23:00'
                                            ];
                                        @endphp
                                        @foreach($saatler as $saat)
                                            <option value="{{ $saat }}"
                                                {{ $plan->cevap == $saat ? 'selected' : '' }}>
                                                {{ $saat }}
                                            </option>
                                        @endforeach
                                    </select>

                                @elseif($soru->cevapTuru == "[Arac]")
                                    @php
                                        $araclar = App\Models\Car::where('firma_id', $tenant_id)
                                            ->orderBy('id', 'ASC')
                                            ->get();
                                    @endphp
                                    <select class="form-control" name="soru{{ $plan->id }}">
                                        @foreach($araclar as $arac)
                                            <option value="{{ $arac->id }}" {{ $plan->cevap == $arac->id ? 'selected' : '' }}>
                                                {{ $arac->arac }}
                                            </option>
                                        @endforeach
                                    </select>

                                @elseif($soru->cevapTuru == "[Fiyat]")
                                    <input type="number" name="soru{{ $plan->id }}" class="form-control" value="{{ $plan->cevap }}">
                                @elseif($soru->cevapTuru == "[Teklif]")
                                    <input type="number" name="soru{{ $plan->id }}" class="form-control" value="{{ $plan->cevap }}">
                                    <span style="font-size: 12px; color: red; font-weight: 500; margin: 0; padding: 0; display: block;">
                                        Bu alan sadece teklif vermek için kullanılır.
                                    </span>
                                @elseif($soru->cevapTuru == "[Bayi]")
                                    @php
                                        $bayiler = App\Models\User::where('tenant_id', $tenant_id)
                                            ->where('status', '1')
                                            ->whereHas('roles', function($query) {
                                                $query->whereIn('name', ['Bayi']);
                                            })
                                            ->orderBy('name', 'asc')
                                            ->get();
                                    @endphp
                                    <select class="form-control" name="soru{{ $plan->id }}">
                                        @foreach($bayiler as $bayi)
                                            <option value="{{ $bayi->user_id }}" {{ $plan->cevap == $bayi->user_id ? 'selected' : '' }}>
                                                {{ $bayi->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="eskiBayi" value="{{ $plan->cevap }}">
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
                <div class="row form-group">
                    <div class="col-lg-12 text-right">
                        <button type="submit" class="btn btn-primary" style="margin-top: 20px;">
                            Planı Güncelle
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {

    // Konsinye cihaz dropdown'ı değiştiğinde (Mevcut mantık, değişiklik yok)
    $(document).on('change', '.consignment-device-select', function() {
        const selectElement = $(this);
        const selectedOption = selectElement.find('option:selected');
        const quantityGroup = selectElement.closest('.col-lg-8, .col-lg-12').find('.consignment-quantity-group');
        const quantityInput = quantityGroup.find('.quantity-input');
        const availableQuantityText = quantityGroup.find('.available-quantity-text');
        const combinedValueInput = selectElement.closest('.col-lg-8, .col-lg-12').find('[id^="consignment-combined-value-"]');
        
        const deviceNameInput = selectElement.closest('.col-lg-8, .col-lg-12').find('.consignment-device-name-input');

        if (selectedOption.val()) { // Bir cihaz seçildiyse
            const available = parseInt(selectedOption.data('available'));
            const deviceName = selectedOption.data('product-name') || selectedOption.text().split(' (Mevcut:')[0];
            
            quantityInput.val(1); 
            quantityInput.attr('max', available);
            availableQuantityText.text('(Mevcut: ' + available + ')');
            quantityGroup.show();

            deviceNameInput.val(deviceName);

            const deviceId = selectedOption.val().replace('C_', '');
            combinedValueInput.val(`C_${deviceId}---${quantityInput.val()}`);
        } else {
            quantityGroup.hide();
            quantityInput.val(1).removeAttr('max');
            availableQuantityText.text('(Mevcut: 0)');
            combinedValueInput.val('');
            deviceNameInput.val('');
        }
    });

    // Sayfa yüklendiğinde her bir konsinye cihaz seçimini ayarla (EKLEMEDEKİ GİBİ REVİZE EDİLDİ)
    $('.consignment-device-select').each(function() {
        const selectElement = $(this);
        const selectedOption = selectElement.find('option:selected');
        const quantityGroup = selectElement.closest('.col-lg-8, .col-lg-12').find('.consignment-quantity-group');
        const quantityInput = quantityGroup.find('.quantity-input');
        const availableQuantityText = quantityGroup.find('.available-quantity-text');
        const combinedValueInput = selectElement.closest('.col-lg-8, .col-lg-12').find('[id^="consignment-combined-value-"]');
        const deviceNameInput = selectElement.closest('.col-lg-8, .col-lg-12').find('.consignment-device-name-input');

        if (selectedOption.val()) { // Eğer bir cihaz seçiliyse (edit modunda genelde seçili gelir)
            const available = parseInt(selectedOption.data('available'));
            const deviceName = selectedOption.data('product-name') || selectedOption.text().split(' (Mevcut:')[0];
            let currentQuantity = parseInt(quantityInput.val()); // Mevcut adeti al

            // Eğer mevcut adet tanımsız veya NaN ise varsayılan 1 yap
            if (isNaN(currentQuantity) || currentQuantity < 1) {
                currentQuantity = 1;
            }

            // Mevcut adeti, kullanılabilir stok ve min (1) arasında tut
            currentQuantity = Math.max(1, Math.min(currentQuantity, available));
            quantityInput.val(currentQuantity);

            quantityInput.attr('max', available);
            availableQuantityText.text('(Mevcut: ' + available + ')');
            
            quantityGroup.show(); // Adet alanını göster

            // Cihaz adını hidden input'a kaydet
            deviceNameInput.val(deviceName);

            // Gizli input'u güncelle (C_ID---Adet formatında)
            const deviceId = selectedOption.val().replace('C_', '');
            combinedValueInput.val(`C_${deviceId}---${currentQuantity}`);
        } else {
            // Seçili cihaz yoksa adet alanını gizle
            quantityGroup.hide();
            quantityInput.val(1).removeAttr('max');
            availableQuantityText.text('(Mevcut: 0)');
            combinedValueInput.val('');
            deviceNameInput.val('');
        }
    });

    // --- Ortak Ürün Arama Fonksiyonu ---
    function filterProductList(searchInput, productListSelector) {
        const searchText = searchInput.val().toLowerCase();
        searchInput.closest('.form-group').find(productListSelector).each(function() {
            const productName = $(this).data('product-name') ? $(this).data('product-name').toLowerCase() : '';
            const productCode = $(this).data('product-code') ? String($(this).data('product-code')).toLowerCase() : ''; 

            if (productName.includes(searchText) || productCode.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // Parça Arama (Genel) (Mevcut mantık, değişiklik yok)
    $(document).on('keyup', '.urunAraInput', function() {
        filterProductList($(this), '.myParcaList .stock-item'); // .checkbox yerine .stock-item kullanıldı
    });

    // Personel Stoğu checkbox değiştiğinde adet girişini göster/gizle ve maksimumu ayarla (Mevcut mantık, değişiklik yok)
    $(document).on('change', '.stock-checkbox', function() {
        const quantityInput = $(this).closest('.stock-item').find('.quantity-input');
        const availableQuantity = parseInt($(this).data('available'));

        if ($(this).is(':checked')) {
            quantityInput.attr('max', availableQuantity).val(1).show();
        } else {
            quantityInput.val(1).removeAttr('max').hide();
        }
    });

    // Dropdown Tıklama Engelleme (Mevcut mantık, değişiklik yok)
    $(document).on('click', '.parcalar-dropdown', function(e) {
        e.stopPropagation();
    });

    // Adet girişlerine sadece sayı girilmesini sağla ve min/max kontrolü yap (Mevcut mantık, değişiklik yok)
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

    // --- Form Gönderimi --- (Mevcut mantık, değişiklik yok)
    $('#servisPlanGuncelle').on('submit', function(e) {
        e.preventDefault();

        let formIsValid = true;
        const $form = $(this);

        // Zorunlu alan kontrolü
        $form.find('[required]').not('.stock-checkbox').each(function() {
            if (!$(this).val()) {
                formIsValid = false;
                $(this).addClass('is-invalid');
                return false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        // Konsinye cihaz adet kontrolü
        $form.find('.consignment-device-select').each(function() {
            if ($(this).val()) { // Cihaz seçiliyse adeti kontrol et
                const quantityInput = $(this).closest('.col-lg-8, .col-lg-12').find('.consignment-quantity-group .quantity-input');
                if (!quantityInput.val() || parseInt(quantityInput.val()) <= 0) {
                    formIsValid = false;
                    quantityInput.addClass('is-invalid');
                    alert('Lütfen konsinye cihaz adedini girin ve geçerli bir değer olduğundan emin olun.');
                    return false;
                } else {
                    quantityInput.removeClass('is-invalid');
                }
            }
        });

        if (!formIsValid) {
            alert('Lütfen tüm zorunlu alanları doldurun.');
            return;
        }

        const formData = new FormData(this);

        // Personel Stoğu verilerini formDataya ekle
        $('.stock-checkbox:checked').each(function() {
            const stageIdMatch = $(this).closest('.form-group').find('label').closest('.col-lg-12').find('input[name^="soru["]').attr('name').match(/\[(\d+)\]/);
            const stageId = stageIdMatch ? stageIdMatch[1] : null;

            if (stageId) {
                const stockId = $(this).val();
                const quantityInput = $(this).closest('.stock-item').find('.quantity-input');
                const quantity = quantityInput.val();

                // `name="parca[stageId][stockId]"` ve `name="adet[stageId][stockId]"` formatında eklenir
                formData.append(`parca[${stageId}][${stockId}]`, stockId);
                formData.append(`adet[${stageId}][${stockId}]`, quantity);
            }
        });

        // Konsinye Cihaz verilerini formDataya ekle
        $('.consignment-device-select').each(function() {
            var selectedValue = $(this).val();
            if (selectedValue) {
                var stageId = $(this).data('stage-id');
                var quantityInput = $(this).closest('.col-lg-8, .col-lg-12').find('.consignment-quantity-group .quantity-input');
                var quantity = quantityInput.val();
                var productName = $(this).find('option:selected').data('product-name') || $(this).find('option:selected').text().split(' (Mevcut:')[0];

                // Hidden input'u JavaScript tarafında C_ID---Adet olarak zaten güncelliyoruz,
                // burada ek olarak consignment_name ve consignment_quantity gönderiyoruz.
                // Sunucu tarafında bu verileri nasıl işlediğinize bağlı olarak burası değişebilir.
                formData.append('parca[' + stageId + '][consignment_id]', selectedValue.replace('C_', ''));
                formData.append('adet[' + stageId + '][consignment_quantity]', quantity);
                formData.append('parca[' + stageId + '][consignment_name]', productName);
            }
        });

        // İşaretli olmayan stok ürünlerinin verilerini formData'dan sil
        $('.stock-checkbox:not(:checked)').each(function() {
            const stageIdMatch = $(this).attr('name').match(/parca\[(\d+)\]\[(\d+)\]/);
            if (stageIdMatch && stageIdMatch[1] && stageIdMatch[2]) {
                const stageId = stageIdMatch[1];
                const stockId = stageIdMatch[2];
                formData.delete(`parca[${stageId}][${stockId}]`);
                formData.delete(`adet[${stageId}][${stockId}]`);
            }
        });

        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);

                    if (response.altAsamalar) {
                        const altAsamalarSelect = $('.servisAsamalari .altAsamalar');
                        altAsamalarSelect.empty().append('<option value="">-Seçiniz-</option>');
                        $.each(response.altAsamalar, function(index, item) {
                            altAsamalarSelect.append('<option value="' + item.id + '">' + item.asama + '</option>');
                        });
                        altAsamalarSelect.prop('selectedIndex', 0);
                    }

                    $('.servisAsamalari .kayitAlan span').text(response.asama);

                    if (typeof loadServiceHistory === 'function') {
                        // Laravel blade'den gelen $service_id'yi doğru şekilde kullanmak için dikkat edin.
                        // Eğer servis_id response içinde dönmüyorsa, Blade'den çekilen değeri kullanmak gerekebilir.
                        // Örnek: loadServiceHistory({{ $servis_id->id }}); // Eğer response.servis_id yoksa
                        // Şu anki kodunuzda response.servis_id kullanılmış.
                        loadServiceHistory(response.servis_id); 
                    }

                    if ($.fn.DataTable && $('#datatableService').length) {
                        $('#datatableService').DataTable().ajax.reload();
                    }

                    $('#editServicePlanModal').modal('hide'); // Modal'ı gizle
                    $('.nav1').trigger('click'); // Nav elemanını tetikleyerek listeyi yenile
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

    // --- Başlangıç Ayarları ---
    // Sayfa yüklendiğinde işaretli olmayan stok ürünlerinin adet girişlerini gizle (Mevcut mantık, değişiklik yok)
    $('.stock-checkbox:not(:checked)').closest('.stock-item').find('.quantity-input').hide();
    
    // Konsinye cihaz adet giriş grubunu başlangıçta gizle (Mevcut mantık, değişiklik yok)
    // Bu kısım artık .each() döngüsü içinde ele alınıyor, ancak seçili olmayanlar için hala gerekli olabilir.
    // Başlangıçta tüm konsinye adet gruplarını gizleyip sadece seçili olanları göstermek daha güvenli.
    $('.consignment-quantity-group').hide();
});
</script>