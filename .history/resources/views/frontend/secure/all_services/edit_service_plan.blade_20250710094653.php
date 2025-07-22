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

<div class="row form-group"> {{-- Yeni eklenen satır --}}
    <div class="col-lg-12"> {{-- Yeni eklenen satır --}}
        <label>{{ $soru->soru }}</label> {{-- Soru etiketini buraya taşıdık --}}

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

        <input id="urunAraInput_konsinye_{{ $plan->id }}" type="text"
               class="form-control urunAraInput consignmentSearchInput" {{-- Yeni sınıf ekledik: consignmentSearchInput --}}
               autocomplete="off" placeholder="Konsinye cihaz adı veya kodu"
               data-soru-id="{{ $plan->id }}"> {{-- Soru ID'sini data özelliğine ekledik --}}

        <div class="konsinye-dropdown myKonsinyeList" data-soru-id="{{ $plan->id }}" style="width:100%; border: 1px solid #eee; max-height: 200px; overflow-y: auto; padding: 10px;"> {{-- Stil ve data özelliği eklendi --}}
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
                    <div class="checkbox stock-item consignment-item" style="padding:3px 0;" {{-- consignment-item sınıfı eklendi --}}
                         data-plan-id="{{ $plan->id }}"
                         data-product-id="{{ $konsinyeId }}" {{-- Ürün ID'si eklendi --}}
                         data-product-name="{{ $konsinyeCihaz->urunAdi ?? $konsinyeCihaz->urun_adi ?? 'N/A' }}"
                         data-product-code="{{ $konsinyeCihaz->urunKodu ?? '' }}">

                        <label style="width: calc(100% - 70px); display: inline-block; text-transform: capitalize;"> {{-- Genişlik ayarlandı --}}
                            <input type="checkbox"
                                   name="konsinye_cihaz[{{ $plan->id }}][{{ $konsinyeId }}]"
                                   class="consignment-checkbox"
                                   value="{{ $konsinyeId }}"
                                   data-available="{{ $konsinyeAdet }}"
                                   {{ $isChecked ? 'checked' : '' }}
                                   style="position: relative; top:2px; margin-right:3px;">
                            {{ $konsinyeCihaz->urunAdi ?? $konsinyeCihaz->urun_adi ?? 'Ürün Adı Bulunamadı' }} (Mevcut: {{ $konsinyeAdet }})
                        </label>

                        <input type="number"
                               name="konsinye_adet[{{ $plan->id }}][{{ $konsinyeId }}]"
                               value="{{ $selectedAdet }}"
                               min="1" max="{{ $konsinyeAdet }}"
                               class="form-control quantity-input consignment-quantity-input"
                               autocomplete="off"
                               style="width: 60px; display: inline-block; text-align: center; vertical-align: middle; {{ $isChecked ? '' : 'display: none;' }}"> {{-- vertical-align eklendi, genişlik ayarlandı --}}
                    </div>
                @endif
            @empty
                {{-- Boş döngü durumunda konsinye_say değeri 0 kalır --}}
            @endforelse

            @if($konsinye_say == 0)
                <label style="color:red">Uyumlu Konsinye Cihaz Bulunamadı.</label>
            @endif
        </div>

        <input type="hidden" name="soru[{{ $plan->id }}]" class="form-control" value="Konsinye Cihaz"/>
    </div>
</div> {{-- Yeni eklenen div'lerin kapanışı --}}
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
                        ->with('roles') // roles ilişkisini önceden yükle
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

    // Mevcut ürün arama (Personel Stoğu) filtresi
    // Bu kısım zaten var, ama genel bir "urunAraInput" yerine
    // spesifik ID'lerle çalışmak daha iyi olabilir.
    // Eğer sadece bir tane ürün arama input'unuz varsa bu haliyle sorun yok.
    // Birden fazla varsa, alttaki "consignmentSearchInput" örneği gibi revize edilebilir.
    $('.urunAraInput').on('keyup', function() {
        const searchText = $(this).val().toLowerCase();
        // Sadece bu arama input'una ait olan dropdown'ı bul
        const $targetDropdown = $(this).next('.myParcaList'); // Eğer her arama inputunun hemen altında kendi listesi varsa
        
        $targetDropdown.find('.stock-item').each(function() {
            const productName = $(this).data('product-name') ? $(this).data('product-name').toLowerCase() : '';
            const productCode = String($(this).data('product-code') || '').toLowerCase(); 

            if (productName.includes(searchText) || productCode.includes(searchText)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });


    // YENİ: Konsinye Cihaz Arama Filtreleme
    $('.consignmentSearchInput').on('keyup', function() {
        const searchText = $(this).val().toLowerCase();
        const soruId = $(this).data('soru-id'); // Hangi soruya ait olduğunu al

        // İlgili konsinye listesini bul
        const $targetDropdown = $(`.myKonsinyeList[data-soru-id="${soruId}"]`);
        
        $targetDropdown.find('.consignment-item').each(function() {
            const productName = $(this).data('product-name') ? $(this).data('product-name').toLowerCase() : '';
            const productCode = String($(this).data('product-code') || '').toLowerCase(); 

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

    // Konsinye Cihaz checkbox değiştiğinde adet girişini göster/gizle ve maksimumu ayarla
    // Mevcut kodunuzda zaten vardı, sadece sınıf adını tutarlı hale getirdik
    $(document).on('change', '.consignment-checkbox', function() {
        const quantityInput = $(this).closest('.consignment-item').find('.consignment-quantity-input');
        const availableQuantity = parseInt($(this).data('available'));

        if ($(this).is(':checked')) {
            quantityInput.attr('max', availableQuantity).val(1).show();
        } else {
            quantityInput.val(1).removeAttr('max').hide();
        }
    });

    // Adet girişlerine sadece sayı girilmesini sağla ve min/max kontrolü yap
    // Bu fonksiyon hem parça hem konsinye için geçerli olmalı, sınıfı genel tutalım.
    $(document).on('input', '.quantity-input', function() {
        let value = $(this).val();
        const max = parseInt($(this).attr('max'));
        const min = parseInt($(this).attr('min')) || 1; 

        value = parseInt(value); 

        // Eğer değer boşsa veya NaN ise, min değere ayarla
        if (isNaN(value) || value < min) {
            value = min;
        }
        
        if (value > max) {
            alert('Maksimum mevcut adeti (' + max + ') aşamazsınız.');
            value = max;
        } 
        
        $(this).val(value);
    });

    // Form Submit
    $("#servisPlanGuncelle").on('submit', function(e) {
        e.preventDefault();
        let formIsValid = true;
        const $form = $(this);

        // Tüm zorunlu alanları (checkbox'lar hariç) kontrol et
        $form.find('[required]').not('.stock-checkbox').each(function() {
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

        // Seçili "Personel Stoğu" ürünlerini FormData'ya manuel olarak ekle
        $('.stock-checkbox:checked').each(function() {
            // Soru ID'sini en yakın form grubundan al
            const stageId = $(this).closest('.form-group').find('input[name^="soru["]').attr('name').match(/\[(\d+)\]/)[1];
            const stockId = $(this).val();
            const quantityInput = $(this).closest('.stock-item').find('.quantity-input');
            const quantity = quantityInput.val();

            formData.append(`parca[${stageId}][${stockId}]`, stockId);
            formData.append(`adet[${stageId}][${stockId}]`, quantity);
        });

        // Seçili "Konsinye Cihaz" ürünlerini FormData'ya manuel olarak ekle
        $('.consignment-checkbox:checked').each(function() {
            const stageId = $(this).closest('.consignment-item').data('plan-id'); // data-plan-id'den alınmalı
            const consignmentId = $(this).val();
            const quantityInput = $(this).closest('.consignment-item').find('.consignment-quantity-input');
            const quantity = quantityInput.val();

            formData.append(`konsinye_cihaz[${stageId}][${consignmentId}]`, consignmentId);
            formData.append(`konsinye_adet[${stageId}][${consignmentId}]`, quantity);
        });

        // İşaretlenmemiş personel stoğu checkbox'larının verilerini FormData'dan sil
        $('.stock-checkbox:not(:checked)').each(function() {
            const stageIdMatch = $(this).closest('.form-group').find('input[name^="soru["]').attr('name').match(/\[(\d+)\]/);
            const stageId = stageIdMatch ? stageIdMatch[1] : null;
            const stockId = $(this).val();

            if (stageId && stockId) {
                // formData.delete(`parca[${stageId}][${stockId}]`); // Bu zaten gönderilmediği için silmeye gerek olmayabilir
                // formData.delete(`adet[${stageId}][${stockId}]`); // Bu da
            }
        });

        // YENİ: İşaretlenmemiş konsinye cihaz checkbox'larının verilerini FormData'dan sil
        $('.consignment-checkbox:not(:checked)').each(function() {
            const stageId = $(this).closest('.consignment-item').data('plan-id');
            const consignmentId = $(this).val();
            
            if (stageId && consignmentId) {
                // Bu değerler checked olmadığında zaten formda yer almayacağı için
                // explicit olarak silmeye gerek kalmayabilir.
                // Eğer input'lar her zaman oluşuyor ve değerleri gönderiliyorsa aşağıdaki satırlar gerekli olabilir.
                // formData.delete(`konsinye_cihaz[${stageId}][${consignmentId}]`);
                // formData.delete(`konsinye_adet[${stageId}][${consignmentId}]`);
            }
        });
        
        $.ajax({
            url: $(this).attr('action'),
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
                    } else {
                        // Eğer loadServiceHistory fonksiyonu yoksa veya servis_id gelmezse
                        // response.error kontrolü yerine gelen data.error kontrolü daha doğru
                        alert(data.error || 'Bir hata oluştu'); 
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

    // Dropdown tıklama engelleme (Hem parça hem konsinye için geçerli olsun)
    $(document).on('click', '.parcalar-dropdown, .konsinye-dropdown', function(e) {
        e.stopPropagation();
    });
    
    // --- Başlangıç Ayarları ---
    // Sayfa yüklendiğinde işaretli olmayan stok ürünlerinin adet girişlerini gizle
    $('.stock-checkbox:not(:checked)').closest('.stock-item').find('.quantity-input').hide();
    
    // İşaretli olmayan konsinye cihazlarının adet girişlerini gizle
    $('.consignment-checkbox:not(:checked)').closest('.consignment-item').find('.consignment-quantity-input').hide();
});
</script>