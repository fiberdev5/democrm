<div class="container">
  <div class="row">
    <div class="col-md-12">
      <form method="POST" id="servisPlanGuncelle" action="{{route('update.service.plan', $tenant_id)}}">
        @csrf
        <input type="hidden" name="planid" value="{{ $servisPlan->id }}">
        <input type="hidden" name="tenant_id" value="{{ $tenant_id }}">

        {{-- İşlemi Yapan Personel Seçimi --}}
        <div class="row form-group">
          <div class="col-lg-12">
            <label>İşlemi Yapan</label>
          </div>
          <div class="col-lg-12">
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
          @endphp   {{-- Parça--}}
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
            <input id="urunAraInput_stok" type="text" class="form-control urunAraInput" autocomplete="off" placeholder="Ürün adı veya kodu">
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
                        <div class="checkbox stock-item"
                            style="padding:3px 0;"
                            data-product-code="{{ $stok->urunKodu ?? '' }}"
                            data-product-name="{{ $stok->urunAdi ?? $stok->urun_adi ?? 'N/A' }}">
                            <label style="width: calc(100% - 40px);display: inline-block;text-transform: capitalize;">
                                <input type="checkbox" 
                                       name="stokCheck{{ $stokId }}"  
                                       class="stock-checkbox" {{-- EKLENDİ --}}
                                       value="{{ $stokId }}" {{-- DEĞİŞTİRİLDİ --}}
                                       data-available="{{ $stokAdet }}"
                                       style="position: relative; top:2px; margin-right:3px;" 
                                       {{ $isChecked ? 'checked' : '' }}>
                                {{ $stok->urunAdi ?? $stok->urun_adi ?? 'Ürün Adı Bulunamadı' }} (Mevcut: {{ $stokAdet }})
                            </label>
                            <input type="number" 
                                   name="stokAdet{{ $stokId }}" 
                                   value="{{ $selectedAdet }}" 
                                   min="1" 
                                   max="{{ $stokAdet }}" 
                                   class="form-control quantity-input" {{-- EKLENDİ --}}
                                   autocomplete="off" 
                                   style="width: 40px;display: inline-block;text-align:center; {{ $isChecked ? '' : 'display: none;' }}">
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
            {{-- Konsinye Cihaz --}}            
            @elseif($soru->cevapTuru == "[Konsinye Cihaz]")
            @php
                $seciliKonsinyeCihazlar = [];
                $kullanilanKonsinyeArray = [];

                if ($plan->cevap) {
                    $cevaplarArray = explode(', ', $plan->cevap);
                    foreach ($cevaplarArray as $cevapItem) {
                        list($itemStokId, $itemAdet) = array_pad(explode('---', $cevapItem), 2, 0);
                        $stok = App\Models\Stock::find($itemStokId);
                        if ($stok) {
                            $kullanilanKonsinyeArray[] = $stok->urunAdi . ' (Adet: ' . $itemAdet . ')';
                            $seciliKonsinyeCihazlar[$itemStokId] = $itemAdet;
                        }
                    }
                }
            @endphp

            @if(!empty($kullanilanKonsinyeArray))
                <div style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;">
                    <b>Kullanılan Konsinye Cihazlar:</b>
                    <ul style="margin-bottom:0;">
                        @foreach($kullanilanKonsinyeArray as $cihazText)
                            <li>{{ $cihazText }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <input id="urunAraInput_konsinye" type="text" class="form-control urunAraInput" autocomplete="off" placeholder="Konsinye cihaz adı veya kodu">

            <div class="konsinye-dropdown myKonsinyeList" style="width:100%">
                <p>Toplam Konsinye Cihaz Sayısı: {{ $toplamKonsinyeCihazAdedi }}</p>
                @php $konsinye_say = 0; @endphp

                @forelse($konsinyeCihazlar as $konsinyeCihaz)
                    @php
                        $konsinyeId = $konsinyeCihaz->id;
                        $konsinyeAdet = $konsinyeCihaz->current_stock_quantity ?? 0;
                        $isChecked = isset($seciliKonsinyeCihazlar[$konsinyeId]);
                        $selectedAdet = $isChecked ? $seciliKonsinyeCihazlar[$konsinyeId] : 1;
                    @endphp

                    @if($konsinyeAdet > 0)
                        @php $konsinye_say++; @endphp
                        <div class="checkbox stock-item"
                          style="padding:3px 0;"
                          data-product-code="{{ $konsinyeCihaz->urunKodu ?? '' }}"
                          data-product-name="{{ $konsinyeCihaz->urunAdi ?? $konsinyeCihaz->urun_adi ?? 'N/A' }}">
                            <label style="width: calc(100% - 40px); display: inline-block; text-transform: capitalize;">
                                <input type="checkbox"
                                  name="konsinyeCheck{{ $konsinyeId }}"
                                  class="consignment-checkbox"
                                  value="{{ $konsinyeId }}"
                                  data-available="{{ $konsinyeAdet }}"
                                  {{ $isChecked ? 'checked' : '' }}
                                  style="position: relative; top:2px; margin-right:3px;">

                                {{ $konsinyeCihaz->urunAdi ?? $konsinyeCihaz->urun_adi ?? 'Ürün Adı Bulunamadı' }} (Mevcut: {{ $konsinyeAdet }})
                            </label>

                          <input type="number"
                            name="konsinyeAdet{{ $konsinyeId }}"
                            value="{{ $selectedAdet }}"
                            min="1" max="{{ $konsinyeAdet }}"
                            class="form-control quantity-input consignment-quantity-input"
                            autocomplete="off"
                            style="width: 40px; display: inline-block; text-align: center; {{ $isChecked ? '' : 'display: none;' }}">

                        </div>
                    @endif
                @empty
                @endforelse

                @if($konsinye_say == 0)
                    <label style="color:red">Uyumlu Konsinye Cihaz Bulunamadı.</label>
                @endif
            </div>
            <input type="hidden" name="soru{{ $plan->id }}" class="form-control" value="Konsinye Cihaz"/>
            @else
            {{-- Diğer Soru Tipleri --}}
            <div class="row form-group">
              <div class="col-lg-4">
                <label>{{ $soru->soru }}</label>
              </div>
              <div class="col-lg-12">
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
                            <option value="{{ $personel->user_id }}" {{ $plan->cevap == $personel->user_id ? 'selected' : '' }}>
                                {{ $personel->name }}
                            </option>
                        @endforeach
                    </select>
                @else
                    {{-- Belirli Grup Personelleri --}}
                    @php
                        // Grupları çöz
                        $roller = [];
                        preg_match_all('/Grup-(\d+)/', $soru->cevapTuru, $matches);
                        $grupKodlari = $matches[1] ?? [];

                        // Rol ataması
                        if (array_intersect([261, 262], $grupKodlari)) {
                            $roller = ['Atölye Ustası'];
                        } elseif (array_intersect([4, 5], $grupKodlari)) {
                            $roller = ['Teknisyen', 'Teknisyen Yardımcısı'];
                        }

                        // Personelleri çek
                        $grupPersoneller = App\Models\User::where('tenant_id', $tenant_id)
                            ->where('status', '1')
                            ->whereHas('roles', function($query) use ($roller) {
                                $query->whereIn('name', $roller);
                            })
                            ->orderBy('name', 'asc')
                            ->get();
                    @endphp

                    <select class="form-control" name="soru{{ $plan->id }}">
                        <option value="">-Seçiniz-</option>
                        @foreach($grupPersoneller as $personel)
                            <option value="{{ $personel->user_id }}" {{ $plan->cevap == $personel->user_id ? 'selected' : '' }}>
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

        {{-- Form Butonları --}}
        <div class="row">
          <div class="col-lg-12" style="text-align: center; margin-bottom: 0px; margin-top: 5px;">
            <input type="submit" class="btn btn-primary btn-sm" value="Güncelle">
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function(e) {

// Başlangıçtaki stok ve konsinye durumlarını kaydet
const initialStockState = {};
const initialConsignmentState = {};

// Sayfa yüklendiğinde mevcut durumu kaydet
$('.stock-checkbox').each(function() {
    const stockId = $(this).val();
    const isChecked = $(this).is(':checked');
    const quantity = $(this).closest('.stock-item').find('.quantity-input').val() || 1;
    
    initialStockState[stockId] = {
        checked: isChecked,
        quantity: parseInt(quantity)
    };
});

$('.consignment-checkbox').each(function() {
    const stockId = $(this).val();
    const isChecked = $(this).is(':checked');
    const quantity = $(this).closest('.stock-item').find('.consignment-quantity-input').val() || 1;
    
    initialConsignmentState[stockId] = {
        checked: isChecked,
        quantity: parseInt(quantity)
    };
});

console.log('Başlangıç Personel Stoğu Durumu:', initialStockState);
console.log('Başlangıç Konsinye Durumu:', initialConsignmentState);

// Ürün Arama Filtreleme
$(document).on('keyup', '.urunAraInput', function () {
    const searchText = $(this).val().toLowerCase();
    let $stockContainer = $(this).siblings('.myParcaList, .myKonsinyeList');

    $stockContainer.find('.stock-item').each(function () {
        const productName = ($(this).data('product-name') || '').toLowerCase();
        const productCode = String($(this).data('product-code') || '').toLowerCase();

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

// Adet girişlerine sadece sayı girilmesini sağla ve min/max kontrolü yap
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

// Form Submit
$("#servisPlanGuncelle").on('submit', function(e) {
    e.preventDefault();
    let formIsValid = true;
    const $form = $(this);

    // Tüm zorunlu alanları kontrol et
    $form.find('[required]').not('.stock-checkbox, .consignment-checkbox').each(function() {
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

    // FormData oluştur
    const formData = new FormData(this);

    // Tüm checkbox verilerini temizle
    $form.find('input[type="checkbox"]').each(function() {
        const checkboxName = $(this).attr('name');
        if (checkboxName) {
            formData.delete(checkboxName);
        }
    });

    // Tüm quantity input verilerini temizle
    $form.find('.quantity-input').each(function() {
        const inputName = $(this).attr('name');
        if (inputName) {
            formData.delete(inputName);
        }
    });

    // PERSONEL STOĞU - Sadece değişiklik yapılanları gönder
    let stockChanged = false;
    $('.stock-checkbox').each(function() {
        const stockId = $(this).val();
        const isChecked = $(this).is(':checked');
        const quantityInput = $(this).closest('.stock-item').find('.quantity-input');
        const quantity = parseInt(quantityInput.val()) || 1;

        // Bu stokta değişiklik var mı kontrol et
        const initial = initialStockState[stockId];
        const hasChanged = !initial || 
                          initial.checked !== isChecked || 
                          (isChecked && initial.quantity !== quantity);

        if (hasChanged) {
            stockChanged = true;
            console.log('Personel Stoğu Değişti - ID:', stockId, 'Checked:', isChecked, 'Adet:', quantity);
        }
    });

    // Eğer herhangi bir stokta değişiklik varsa, TÜM mevcut durumu gönder
    if (stockChanged) {
        console.log('Personel Stoğu değişiklik algılandı, tüm durum gönderiliyor...');
        $('.stock-checkbox:checked').each(function() {
            const stockId = $(this).val();
            const checkboxName = $(this).attr('name');
            const quantityInput = $(this).closest('.stock-item').find('.quantity-input');
            const quantity = quantityInput.val() || 1;
            const quantityName = quantityInput.attr('name');

            formData.append(checkboxName, stockId);
            formData.append(quantityName, quantity);
        });
    } else {
        console.log('Personel Stoğu değişiklik YOK, mevcut durum korunuyor.');
        // Değişiklik yoksa backend'e işaret gönder (opsiyonel)
        formData.append('stok_degisiklik_yok', '1');
    }

    // KONSİNYE CİHAZ - Sadece değişiklik yapılanları gönder
    let consignmentChanged = false;
    $('.consignment-checkbox').each(function() {
        const stockId = $(this).val();
        const isChecked = $(this).is(':checked');
        const quantityInput = $(this).closest('.stock-item').find('.consignment-quantity-input');
        const quantity = parseInt(quantityInput.val()) || 1;

        const initial = initialConsignmentState[stockId];
        const hasChanged = !initial || 
                          initial.checked !== isChecked || 
                          (isChecked && initial.quantity !== quantity);

        if (hasChanged) {
            consignmentChanged = true;
            console.log('Konsinye Değişti - ID:', stockId, 'Checked:', isChecked, 'Adet:', quantity);
        }
    });

    if (consignmentChanged) {
        console.log('Konsinye Cihaz değişiklik algılandı, tüm durum gönderiliyor...');
        $('.consignment-checkbox:checked').each(function() {
            const stockId = $(this).val();
            const checkboxName = $(this).attr('name');
            const quantityInput = $(this).closest('.stock-item').find('.consignment-quantity-input');
            const quantity = quantityInput.val() || 1;
            const quantityName = quantityInput.attr('name');

            formData.append(checkboxName, stockId);
            formData.append(quantityName, quantity);
        });
    } else {
        console.log('Konsinye Cihaz değişiklik YOK, mevcut durum korunuyor.');
        formData.append('konsinye_degisiklik_yok', '1');
    }

    // Debug
    console.log('=== GÖNDERİLEN FORMDATA ===');
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    $.ajax({
        url: $form.attr('action'),
        type: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
            if(data.error) {
                alert(data.error);
            } else {
                alert('Plan başarıyla güncellendi');

                if(typeof loadServiceHistory === 'function' && data.servis_id) {
                    loadServiceHistory(data.servis_id);
                }

                $('#editServicePlanModal').modal('hide');
                $('.nav1').trigger('click');
            }
        },
        error: function(e) {
            console.error('AJAX Hatası:', e);
            alert("Hata: " + (e.responseText || 'Bilinmeyen hata'));
        }
    });
});

// Dropdown tıklama engelleme
$(document).on('click', '.parcalar-dropdown, .konsinye-dropdown', function(e) {
    e.stopPropagation();
});

// Başlangıç Ayarları
$('.stock-checkbox:not(:checked)').closest('.stock-item').find('.quantity-input').hide();
$('.consignment-checkbox:not(:checked)').closest('.stock-item').find('.consignment-quantity-input').hide();

});
</script>