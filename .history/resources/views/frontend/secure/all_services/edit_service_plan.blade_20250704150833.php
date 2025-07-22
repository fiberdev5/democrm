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

            // Mevcut cevabı parse et
            if ($plan->cevap) {
                $cevapParts = explode('---', $plan->cevap);
                if (count($cevapParts) >= 2) {
                    $currentConsignmentIdWithPrefix = $cevapParts[0];
                    $currentConsignmentQuantity = $cevapParts[1];
                    $currentConsignmentId = str_replace('C_', '', $currentConsignmentIdWithPrefix);
                    
                    // Seçili cihazın adını bul
                    $selectedDevice = $detailedConsignmentStocks->firstWhere('id', $currentConsignmentId);
                    if ($selectedDevice) {
                        $currentConsignmentDeviceName = $selectedDevice->urunAdi ?? $selectedDevice->urun_adi ?? 'Cihaz Adı Bulunamadı';
                    } else {
                        $originalDevice = App\Models\Stock::find($currentConsignmentId);
                        if ($originalDevice) {
                            $currentConsignmentDeviceName = $originalDevice->urunAdi ?? $originalDevice->urun_adi ?? 'Cihaz Adı Bulunamadı';
                        }
                    }
                }
            }

            // Seçili cihazın mevcut stok miktarını hesapla
            $selectedConsignmentDeviceCurrentStock = 0;
            if ($currentConsignmentId) {
                $selectedDeviceInView = $detailedConsignmentStocks->firstWhere('id', $currentConsignmentId);
                if ($selectedDeviceInView) {
                    $selectedConsignmentDeviceCurrentStock = $selectedDeviceInView->current_stock_quantity;
                } else {
                    $girisAdet = App\Models\StockAction::where('stokId', $currentConsignmentId)
                        ->whereIn('islem', [1, 4])
                        ->sum('adet');
                    $cikisAdet = App\Models\StockAction::where('stokId', $currentConsignmentId)
                        ->where('islem', 2)
                        ->sum('adet');
                    $selectedConsignmentDeviceCurrentStock = max(0, $girisAdet - $cikisAdet);
                }
            }
        @endphp

        {{-- Mevcut atanmış cihazı göster --}}
        @if($currentConsignmentId)
            <div style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9; border-radius: 8px;">
                <p><strong>Halihazırda Atanan Cihaz:</strong> {{ $currentConsignmentDeviceName }} (Adet: {{ $currentConsignmentQuantity }})</p>
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
                        @if($currentConsignmentId == $device->id) selected @endif>
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
                                        ->get()
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

<script>
$(document).ready(function(e) {

    // Form Submit
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
                if(data.error) {
                    alert(data.error);
                } else {
                    alert('Plan başarıyla güncellendi');

                    // Servis geçmişini güncelle
                    if(typeof loadServiceHistory === 'function' && data.servis_id) {
                        loadServiceHistory(data.servis_id);
                    }else {
                        console.log('loadServiceHistory fonksiyonu bulunamadı veya servis_id eksik. Yine de başarılı.');
                    }

                    $('#editServicePlanModal').modal('hide');
                    $('.nav1').trigger('click'); // Navigasyonu tetikleyerek sayfanın yenilenmesini sağla
                }
            },
            error: function(e) {
                alert("Hata: " + e.responseText);
            }
        });
    });

    // Parça arama fonksiyonunu dinamikleştirme
    $('.urunAraInput').keyup(function() {
        var searchText = $(this).val().toLowerCase(); // Arama metnini al
        // İlgili myParcaList div'ini bul
        var myParcaList = $(this).next('.myParcaList');

        myParcaList.find(".checkbox").each(function() {
            var productName = $(this).find('label').text().toLowerCase(); // Ürün adını al
            var productCode = $(this).data('product-code'); // data-product-code'u al

            // productCode'u güvenli bir şekilde string'e çevir ve küçük harf yap
            if (productCode !== undefined && productCode !== null) {
                productCode = String(productCode).toLowerCase();
            } else {
                productCode = '';
            }

            // Hem ürün adında hem de ürün kodunda arama yap
            if (productName.includes(searchText) || productCode.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });


    // Dropdown tıklama engelleme
    $(document).on('click', '.parcalar-dropdown', function(e) {
        e.stopPropagation();
    });
});
</script>

<script>
$(document).ready(function() {
    // Konsinye cihaz seçimi değiştiğinde
    $(document).on('change', '.consignment-device-select', function() {
        var planId = $(this).data('plan-id');
        var selectedOption = $(this).find('option:selected');
        var availableQuantity = selectedOption.data('available');
        var selectedValue = selectedOption.val();
        
        var quantityGroup = $(this).closest('.col-lg-8').find('.consignment-quantity-group');
        var quantityInput = $('#consignment-quantity-' + planId);
        var availableQuantityText = quantityGroup.find('.available-quantity-text');
        var hiddenInput = $('#consignment-combined-value-' + planId);

        if (selectedValue) {
            // Cihaz seçildi
            quantityGroup.slideDown();
            quantityInput.attr('max', availableQuantity);
            quantityInput.val(1);
            availableQuantityText.text('(Mevcut: ' + availableQuantity + ')');
            
            // Hidden input'u güncelle
            hiddenInput.val(selectedValue + '---1');
        } else {
            // Cihaz seçimi kaldırıldı
            quantityGroup.slideUp();
            quantityInput.val(0);
            availableQuantityText.text('(Mevcut: 0)');
            hiddenInput.val('');
        }
    });

    // Adet değiştiğinde hidden input'u güncelle
    $(document).on('change keyup', '.quantity-input', function() {
        var planId = $(this).attr('id').replace('consignment-quantity-', '');
        var dropdown = $('#consignment-device-select-' + planId);
        var selectedValue = dropdown.val();
        var quantity = $(this).val();
        var max = parseInt($(this).attr('max'));
        
        // Adet kontrolü
        if (quantity > max) {
            quantity = max;
            $(this).val(max);
        } else if (quantity < 1) {
            quantity = 1;
            $(this).val(1);
        }
        
        // Hidden input'u güncelle
        if (selectedValue) {
            $('#consignment-combined-value-' + planId).val(selectedValue + '---' + quantity);
        }
    });

    // Sayfa yüklendiğinde mevcut seçimleri kontrol et
    $('.consignment-device-select').each(function() {
        var planId = $(this).data('plan-id');
        var selectedOption = $(this).find('option:selected');
        
        if (selectedOption.val()) {
            var availableQuantity = selectedOption.data('available');
            var quantityGroup = $(this).closest('.col-lg-8').find('.consignment-quantity-group');
            var quantityInput = $('#consignment-quantity-' + planId);
            var availableQuantityText = quantityGroup.find('.available-quantity-text');

            quantityGroup.show();
            quantityInput.attr('max', availableQuantity);
            availableQuantityText.text('(Mevcut: ' + availableQuantity + ')');
        }
    });
});
</script>