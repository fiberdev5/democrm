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
                                    <p>Toplam Stok Sayısı: {{ $toplamPersonelStokAdedi }}</p>

                                    @forelse($stoklar as $stok)
                                        @php
                                            $personelStokKaydi = $stok->personelStoklar->where('pid', Auth::user()->user_id)->first();
                                            $stokId = $stok->id;
                                            $stokAdet = $personelStokKaydi ? ($personelStokKaydi->adet ?? 0) : 0;
                                            $stokSec = $stok;

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

                                        @if($stokSec && $stokAdet > 0)
                                            <div class="checkbox" style="padding:3px 0;" data-product-code="{{ $stokSec->urunKodu ?? '' }}">
                                                <label style="width: calc(100% - 40px);display: inline-block;text-transform: capitalize;">
                                                    <input type="checkbox" name="stokCheck{{ $stokId }}" value="on" style="position: relative; top:2px; margin-right:3px;" {{ $isChecked ? 'checked' : '' }}>
                                                    {{ $stokSec->urunAdi ?? $stokSec->urun_adi ?? 'Ürün Adı Bulunamadı' }} (Mevcut: {{ $stokAdet }})
                                                </label>
                                                <input type="number" name="stokAdet{{ $stokId }}" value="{{ $selectedAdet }}" min="1" max="{{ $stokAdet }}" class="form-control" autocomplete="off" style="width: 40px;display: inline-block;text-align:center;">
                                            </div>
                                        @endif
                                    @empty
                                    @endforelse
                                    @if($stoklar->isEmpty())
                                        <label style="color:red">Uyumlu Parça Bulunamadı.</label>
                                    @endif
                                </div>
                            </div>
                            <input type="hidden" name="soru{{ $plan->id }}" class="form-control" value="Parca"/>
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
                $currentConsignmentDeviceName = 'Belirtilmemiş';
                $currentConsignmentIdWithPrefix = null; // Yeni eklenen değişken
                $selectedConsignmentDeviceCurrentStock = 0; // Başlangıç değeri

                // Mevcut cevabı parse et
                if ($plan->cevap) {
                    $cevapParts = explode('---', $plan->cevap);
                    if (count($cevapParts) >= 2) {
                        $currentConsignmentIdWithPrefix = $cevapParts[0]; // C_ID formatında
                        $currentConsignmentId = str_replace('C_', '', $currentConsignmentIdWithPrefix); // Sadece ID
                        $currentConsignmentQuantity = (int)$cevapParts[1]; // Tam sayıya çevir

                        // Seçili cihazın adını ve mevcut stok miktarını bul
                        $selectedDevice = $detailedConsignmentStocks->firstWhere('id', $currentConsignmentId);
                        if ($selectedDevice) {
                            $currentConsignmentDeviceName = $selectedDevice->urunAdi ?? $selectedDevice->urun_adi ?? 'Cihaz Adı Bulunamadı';
                            $selectedConsignmentDeviceCurrentStock = $selectedDevice->current_stock_quantity;
                        } else {
                            // Eğer detailedConsignmentStocks içinde bulunamazsa, direkt Stock modelinden al
                            $originalDevice = App\Models\Stock::find($currentConsignmentId);
                            if ($originalDevice) {
                                $currentConsignmentDeviceName = $originalDevice->urunAdi ?? $originalDevice->urun_adi ?? 'Cihaz Adı Bulunamadı';
                                // Stok miktarını StockAction tablosundan hesapla (mevcut kodun mantığına göre)
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
                }
            @endphp

            {{-- Mevcut atanmış cihazı göster --}}
            @if($currentConsignmentId)
                <div style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9; border-radius: 8px;">
                    <p><strong>Atanan Cihaz:</strong> {{ $currentConsignmentDeviceName }} (Adet: {{ $currentConsignmentQuantity }})</p>
                </div>
            @endif

            {{-- Konsinye cihaz seçimi dropdown'u --}}
            <select id="consignment-device-select-{{ $plan->id }}"
                    class="form-control consignment-device-select"
                    data-plan-id="{{ $plan->id }}">
                <option value="">-- Konsinye Cihaz Seçiniz --</option>
                @foreach($detailedConsignmentStocks as $device)
                    <option value="C_{{ $device->id }}"
                            data-available="{{ $device->current_stock_quantity }}"
                            data-product-name="{{ $device->urunAdi ?? $device->urun_adi ?? 'N/A' }}"
                            data-product-code="{{ $device->urunKodu ?? '' }}"
                           @if(isset($plan) && $plan->cevap && strpos($plan->cevap, 'C_') === 0 && explode('---', $plan->cevap)[0] == 'C_'.$device->id) selected @endif> 
                        {{ $device->urunAdi ?? $device->urun_adi ?? 'Cihaz Adı Bulunamadı' }} (Mevcut: {{ $device->current_stock_quantity }})
                    </option>
                @endforeach
            </select>

            {{-- Adet girişi --}}
            <div class="form-group consignment-quantity-group" style="margin-top: 10px; {{ $currentConsignmentId ? 'display:block;' : 'display:none;' }}">
                <label>Adet:</label>
                <input type="number"
                        id="consignment-quantity-{{ $plan->id }}"
                        value="{{ $currentConsignmentQuantity }}"
                        min="1"
                        max="{{ $selectedConsignmentDeviceCurrentStock }}" 
                        class="form-control quantity-input"
                        style="width: 80px; display:inline-block;">
                <span class="text-sm text-gray-500 available-quantity-text">(Mevcut: {{ $selectedConsignmentDeviceCurrentStock }})</span>
            </div>

            {{-- Gizli input - form submit'te bu değer gönderilecek --}}
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
    $(document).ready(function(e) {

        // Form Gönderimi (Mevcut mantık, değişiklik yok)
        $("#servisPlanGuncelle").on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        alert('Plan başarıyla güncellendi');

                        if (typeof loadServiceHistory === 'function' && data.servis_id) {
                            loadServiceHistory(data.servis_id);
                        } else {
                            console.log('loadServiceHistory fonksiyonu bulunamadı veya servis_id eksik. Yine de başarılı.');
                        }

                        $('#editServicePlanModal').modal('hide');
                        $('.nav1').trigger('click');
                    }
                },
                error: function(e) {
                    alert("Hata: " + e.responseText);
                }
            });
        });

        // Parça Arama (Mevcut mantık, değişiklik yok)
        $('.urunAraInput').keyup(function() {
            var searchText = $(this).val().toLowerCase();
            var myParcaList = $(this).next('.myParcaList');

            myParcaList.find(".checkbox").each(function() {
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

        // Dropdown Tıklama Engelleme (Mevcut mantık, değişiklik yok)
        $(document).on('click', '.parcalar-dropdown', function(e) {
            e.stopPropagation();
        });

        // --- KONSİNYE CİHAZ YÖNETİMİ BAŞLANGICI ---

        // Sayfa yüklendiğinde her bir konsinye cihaz seçimini ayarla
        $('.consignment-device-select').each(function() {
            const selectElement = $(this);
            const selectedOption = selectElement.find('option:selected');
            const quantityGroup = selectElement.closest('.col-lg-8').find('.consignment-quantity-group');
            const quantityInput = quantityGroup.find('.quantity-input');
            const availableQuantityText = quantityGroup.find('.available-quantity-text');
            const combinedValueInput = selectElement.closest('.col-lg-8').find('[id^="consignment-combined-value-"]');

            if (selectedOption.val()) { // Eğer bir cihaz seçiliyse (edit modunda genelde seçili gelir)
                const available = parseInt(selectedOption.data('available'));
                const currentQuantity = parseInt(quantityInput.val()) || 1; // Mevcut adeti al, yoksa 1

                quantityInput.attr('max', available);
                availableQuantityText.text('(Mevcut: ' + available + ')');

                // Mevcut adet kullanılabilir adetten fazlaysa, max değere çek
                if (currentQuantity > available) {
                    quantityInput.val(available);
                } else if (currentQuantity < 1) { // Min değerinden küçükse 1'e çek
                    quantityInput.val(1);
                }

                quantityGroup.show(); // Adet alanını göster

                // Gizli input'u güncelle (C_ID---Adet formatında)
                const deviceId = selectedOption.val().replace('C_', '');
                combinedValueInput.val(`C_${deviceId}---${quantityInput.val()}`);
            } else {
                // Seçili cihaz yoksa adet alanını gizle
                quantityGroup.hide();
                quantityInput.val(1).removeAttr('max');
                availableQuantityText.text('(Mevcut: 0)');
                combinedValueInput.val('');
            }
        });

        // Konsinye cihaz dropdown'ı değiştiğinde
        $(document).on('change', '.consignment-device-select', function() {
            const selectElement = $(this);
            const selectedOption = selectElement.find('option:selected');
            const quantityGroup = selectElement.closest('.col-lg-8').find('.consignment-quantity-group');
            const quantityInput = quantityGroup.find('.quantity-input');
            const availableQuantityText = quantityGroup.find('.available-quantity-text');
            const combinedValueInput = selectElement.closest('.col-lg-8').find('[id^="consignment-combined-value-"]');

            if (selectedOption.val()) { // Bir cihaz seçildiyse
                const available = parseInt(selectedOption.data('available'));
                quantityInput.attr('max', available).val(1); // Yeni seçimde adeti 1'e çek ve max'ı ayarla
                availableQuantityText.text('(Mevcut: ' + available + ')');
                quantityGroup.show();

                // Gizli input'u güncelle
                const deviceId = selectedOption.val().replace('C_', '');
                combinedValueInput.val(`C_${deviceId}---${quantityInput.val()}`);
            } else { // Hiçbir cihaz seçilmediyse
                quantityGroup.hide();
                quantityInput.val(1).removeAttr('max');
                availableQuantityText.text('(Mevcut: 0)');
                combinedValueInput.val('');
            }
        });

        // Adet girişine sadece sayı girilmesini sağla ve min/max kontrolü yap
        $(document).on('input', '.consignment-quantity-group .quantity-input', function() {
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

            // Adet değiştiğinde gizli kombine değeri de güncelle
            const selectElement = $(this).closest('.col-lg-8').find('.consignment-device-select');
            const combinedValueInput = $(this).closest('.col-lg-8').find('[id^="consignment-combined-value-"]');
            const selectedValue = selectElement.val();

            if (selectedValue) {
                const deviceId = selectedValue.replace('C_', '');
                combinedValueInput.val(`C_${deviceId}---${value}`);
            }
        });

        // --- KONSİNYE CİHAZ YÖNETİMİ SONU ---

    });
</script>

