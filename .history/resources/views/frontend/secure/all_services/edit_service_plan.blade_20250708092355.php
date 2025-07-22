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
                                    $selectedPartIds = []; // Seçili parça ID'lerini tutmak için
                                    if ($plan->cevap) {
                                        $cevaplarArray = explode(', ', $plan->cevap);
                                        foreach ($cevaplarArray as $cevapItem) {
                                            list($itemStokId, $itemAdet) = array_pad(explode('---', $cevapItem), 2, 0);
                                            $kullanilanStok = App\Models\Stock::find($itemStokId);
                                            if ($kullanilanStok) {
                                                $kullanilanParcalarArray[] = $kullanilanStok->urunAdi . ' (Adet: ' . $itemAdet . ')';
                                                $selectedPartIds[$itemStokId] = $itemAdet; // ID ve adedi kaydet
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

                                            $isChecked = isset($selectedPartIds[$stokId]); // Kaydedilmiş parça ID'si var mı?
                                            $selectedAdet = $isChecked ? $selectedPartIds[$stokId] : 1; // Varsa adedi, yoksa 1
                                        @endphp

                                        @if($stok && $stokAdet > 0)
                                            <div class="checkbox" style="padding:3px 0;" data-product-code="{{ $stok->urunKodu ?? '' }}">
                                                <label style="width: calc(100% - 40px);display: inline-block;text-transform: capitalize;">
                                                    <input type="checkbox" name="stokCheck{{ $stokId }}" value="on" style="position: relative; top:2px; margin-right:3px;" {{ $isChecked ? 'checked' : '' }}>
                                                    {{ $stok->urunAdi ?? $stok->urun_adi ?? 'Ürün Adı Bulunamadı' }} (Mevcut: {{ $stokAdet }})
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
                                                $currentConsignmentId = (int)$parts[0];
                                                $currentConsignmentQuantity = (int)$parts[1];
                                            }
                                        }
                                        // Eski format: "C_ID---Adet"
                                        elseif (str_starts_with($plan->cevap, 'C_')) {
                                            $parts = explode('---', substr($plan->cevap, 2));
                                            if (count($parts) === 2) {
                                                $currentConsignmentId = (int)$parts[0];
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
                                                    ->where('planId', $plan->planid) // Bu satırı ekledik veya kontrol ettik
                                                    ->sum('adet');
                                                
                                                $existingActionForThisPlan = App\Models\StockAction::where('stokId', $currentConsignmentId)
                                                                                            ->where('islem', 2)
                                                                                            ->where('planId', $plan->planid)
                                                                                            ->first();
                                                if ($existingActionForThisPlan) {
                                                    $selectedConsignmentDeviceCurrentStock = max(0, $girisAdet - ($cikisAdet - $existingActionForThisPlan->adet));
                                                } else {
                                                    $selectedConsignmentDeviceCurrentStock = max(0, $girisAdet - $cikisAdet);
                                                }
                                            }
                                        }
                                    }
                                    // Debugging output for PHP variables
                                    echo "<script>";
                                    echo "console.log('--- Konsinye Cihaz Debug (Plan ID: {$plan->id}) ---');";
                                    echo "console.log('plan->cevap: " . json_encode($plan->cevap) . "');";
                                    echo "console.log('currentConsignmentId: " . json_encode($currentConsignmentId) . "');";
                                    echo "console.log('currentConsignmentQuantity: " . json_encode($currentConsignmentQuantity) . "');";
                                    echo "console.log('currentConsignmentName: " . json_encode($currentConsignmentName) . "');";
                                    echo "console.log('selectedConsignmentDeviceCurrentStock: " . json_encode($selectedConsignmentDeviceCurrentStock) . "');";
                                    echo "</script>";
                                @endphp

                                {{-- Mevcut seçili cihazı göster --}}
                                @if($currentConsignmentName && $currentConsignmentId)
                                    <div id="currentConsignmentDisplay_{{ $plan->id }}" style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;">
                                        <strong>Seçili Cihaz:</strong> {{ $currentConsignmentName }} (Adet: <span class="current-consignment-quantity-display">{{ $currentConsignmentQuantity }}</span>)
                                    </div>
                                @else
                                    <div id="currentConsignmentDisplay_{{ $plan->id }}" style="display:none; margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;">
                                        <strong>Seçili Cihaz:</strong> <span class="current-consignment-name-display"></span> (Adet: <span class="current-consignment-quantity-display"></span>)
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
                                                ->where('planId', '!=', $plan->planid) // Mevcut planın kendi kullanımını çıkarmayın
                                                ->sum('adet');

                                            $availableStock = max(0, $girisAdet - $cikisAdet);

                                            // Eğer şu anki cihaz önceden seçiliyse, o adedi mevcut stoktan çıkarmayın
                                            if ($currentConsignmentId == $device->id) {
                                                $availableStock += $currentConsignmentQuantity;
                                            }
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
                                <div class="consignment-quantity-group" id="consignmentQuantityGroup_{{ $plan->id }}" style="display: {{ $currentConsignmentId ? 'block' : 'none' }}; margin-top: 10px;">
                                    <label>Adet:</label>
                                    <input type="number"
                                            name="consignment_quantity_{{ $plan->id }}"
                                            class="form-control quantity-input"
                                            value="{{ $currentConsignmentQuantity }}"
                                            min="0"
                                            max="{{ $selectedConsignmentDeviceCurrentStock }}"
                                            style="width: 100px; display: inline-block;">
                                    <span class="available-quantity-text" style="margin-left: 10px; color: #555;">
                                        (Mevcut: <span class="current-max-stock">{{ $selectedConsignmentDeviceCurrentStock }}</span>)
                                    </span>
                                </div>

                                {{-- Gizli input - JavaScript ile Cihaz Adı: C_ID---Adet formatında güncellenecek --}}
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
    // Konsinye Cihaz Seçim Mantığı
    $('.consignment-device-select').on('change', function() {
        var $select = $(this);
        var stageId = $select.data('stage-id');
        var selectedOption = $select.find('option:selected');
        var selectedValue = selectedOption.val(); // C_ID formatında gelir
        var productName = selectedOption.data('product-name');
        var availableStock = selectedOption.data('available');
        var $quantityGroup = $('#consignmentQuantityGroup_' + stageId);
        var $quantityInput = $quantityGroup.find('.quantity-input');
        var $currentMaxStockSpan = $quantityGroup.find('.current-max-stock');
        var $currentConsignmentDisplay = $('#currentConsignmentDisplay_' + stageId);

        if (selectedValue) {
            // Seçim yapıldığında adet grubunu göster
            $quantityGroup.show();
            $quantityInput.attr('max', availableStock);
            $currentMaxStockSpan.text(availableStock);
            $quantityInput.val(1); // Varsayılan olarak 1 adet seçili olsun

            // Seçili cihaz bilgisini gösteren div'i güncelle
            $currentConsignmentDisplay.find('.current-consignment-name-display').text(productName);
            $currentConsignmentDisplay.find('.current-consignment-quantity-display').text($quantityInput.val());
            $currentConsignmentDisplay.show();

        } else {
            // Seçim kaldırıldığında veya boş seçildiğinde adet grubunu gizle
            $quantityGroup.hide();
            $quantityInput.val(0);
            $currentConsignmentDisplay.hide();
            $currentConsignmentDisplay.find('.current-consignment-name-display').text('');
            $currentConsignmentDisplay.find('.current-consignment-quantity-display').text('');
        }
        // Hidden input'u hemen güncelle
        updateConsignmentCombinedValue($select);
    });

    // Adet değiştiğinde hidden input'u güncelle
    $('.quantity-input').on('change', function() {
        var $input = $(this);
        var stageId = $input.closest('.consignment-quantity-group').prevAll('.consignment-device-select').data('stage-id');
        var $select = $('.consignment-device-select[data-stage-id="' + stageId + '"]');
        var $currentConsignmentDisplay = $('#currentConsignmentDisplay_' + stageId);

        // Seçili cihaz bilgisini gösteren div'deki adet bilgisini güncelle
        $currentConsignmentDisplay.find('.current-consignment-quantity-display').text($input.val());

        updateConsignmentCombinedValue($select);
    });

    // Form Gönderimi
    $("#servisPlanGuncelle").on('submit', function(e) {
        e.preventDefault();

        // Form gönderilmeden önce tüm konsinye cihaz değerlerini güncelle
        // Her select elementi için updateConsignmentCombinedValue fonksiyonunu çağırıyoruz.
        $('.consignment-device-select').each(function() {
            updateConsignmentCombinedValue($(this));
        });

        // Debug: Form verilerini konsola yazdır
        var formData = new FormData(this);
        console.log('Form gönderilmeden önce:');
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                if (data.error) {
                    alert(data.error);
                } else {
                    alert('Plan başarıyla güncellendi');

                     $('#editServicePlanModal').modal('hide');
        
                    if (typeof loadServiceHistory === 'function' && data.servis_id) {
                        loadServiceHistory(data.servis_id);
                    } else {
                        console.log('loadServiceHistory fonksiyonu bulunamadı veya servis_id eksik. Sayfa yenileniyor...');
                        //location.reload(); 
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Hatası: ", status, error);
                console.error("Yanıt Metni: ", xhr.responseText);
                var errorMsg = "Bilinmeyen bir hata oluştu.";
                try {
                    var jsonResponse = JSON.parse(xhr.responseText);
                    if (jsonResponse.error) {
                        errorMsg = jsonResponse.error;
                    }
                } catch (e) {
                    // Yanıt JSON değilse, direkt metni kullan
                    errorMsg = xhr.responseText;
                }
                alert('Plan güncellenirken bir hata oluştu: ' + errorMsg);
            }
        });
    });

    // Konsinye Cihaz için gizli input değerini güncelleyen fonksiyon
    function updateConsignmentCombinedValue($selectElement) {
        var stageId = $selectElement.data('stage-id');
        var selectedOption = $selectElement.find('option:selected');
        var selectedValue = selectedOption.val(); // C_ID formatında
        var productName = selectedOption.data('product-name');
        var $quantityInput = $('#consignmentQuantityGroup_' + stageId).find('.quantity-input');
        var quantity = $quantityInput.val();

        var $hiddenInput = $('#consignment-combined-value-' + stageId);

        if (selectedValue) {
            var combinedValue = productName + ': ' + selectedValue + '---' + (quantity || 1);
            $hiddenInput.val(combinedValue);
        } else {
            $hiddenInput.val('');
        }
        console.log(`Hidden input (soru${stageId}) güncellendi: ${$hiddenInput.val()}`);
    }

    // Sayfa yüklendiğinde, eğer mevcut bir konsinye seçimi varsa adet grubunu göster ve değerleri set et
    // Bu kısım, sayfa ilk yüklendiğinde mevcut seçimi doğru şekilde yansıtmak için önemlidir.
    $('.consignment-device-select').each(function() {
        var $select = $(this);
        var stageId = $select.data('stage-id');
        var selectedValue = $select.val(); // "C_ID" formatında
        var $quantityGroup = $('#consignmentQuantityGroup_' + stageId);
        var $quantityInput = $quantityGroup.find('.quantity-input');
        var $currentMaxStockSpan = $quantityGroup.find('.current-max-stock');
        var $currentConsignmentDisplay = $('#currentConsignmentDisplay_' + stageId);

        if (selectedValue) {
            var selectedOption = $select.find('option:selected');
            var productName = selectedOption.data('product-name');
            var availableStock = selectedOption.data('available');

            $quantityGroup.show();
            $quantityInput.attr('max', availableStock);
            $currentMaxStockSpan.text(availableStock);

            // Mevcut seçili cihazın bilgilerini güncelle ve göster
            $currentConsignmentDisplay.find('.current-consignment-name-display').text(productName);
            $currentConsignmentDisplay.find('.current-consignment-quantity-display').text($quantityInput.val());
            $currentConsignmentDisplay.show();

        } else {
             $quantityGroup.hide();
             $currentConsignmentDisplay.hide();
        }
        updateConsignmentCombinedValue($select);
    });
});
</script>
