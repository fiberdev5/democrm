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
Tabii, [Konsinye Cihaz] kısmını checkbox'lardan form-select'e dönüştürelim. Bu değişiklik, aynı anda birden fazla konsinye cihazı seçme yerine, yalnızca bir konsinye cihazı seçmeye olanak tanır. Eğer çoklu seçim hala gerekliyse, select etiketine multiple özelliğini eklemek veya farklı bir yaklaşıma gitmek gerekebilir. Ancak şu anki isteğiniz doğrultusunda tekli seçimli bir select yapısı kurgulayacağım.

Konsinye cihaz seçimi için gerekli HTML ve JavaScript değişiklikleri aşağıdadır.

HTML

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
                            $seciliKonsinyeCihazId = null;
                            $seciliKonsinyeAdet = 1; // Varsayılan adet
                            $kullanilanKonsinyeArray = [];

                            if ($plan->cevap) {
                                $cevaplarArray = explode(', ', $plan->cevap);
                                // Konsinye cihazı için tek bir seçim yapıldığı varsayımıyla ilkini alıyoruz
                                if (!empty($cevaplarArray[0])) {
                                    list($itemStokId, $itemAdet) = array_pad(explode('---', $cevaplarArray[0]), 2, 0);
                                    $stok = App\Models\Stock::find($itemStokId);
                                    if ($stok) {
                                        $kullanilanKonsinyeArray[] = $stok->urunAdi . ' (Adet: ' . $itemAdet . ')';
                                        $seciliKonsinyeCihazId = $itemStokId;
                                        $seciliKonsinyeAdet = $itemAdet;
                                    }
                                }
                            }
                        @endphp

                        <div class="row form-group">
                            <div class="col-lg-12">
                                <label>{{ $soru->soru }}</label>
                                @if(!empty($kullanilanKonsinyeArray))
                                    <div style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;">
                                        <b>Kullanılan Konsinye Cihaz:</b>
                                        <ul style="margin-bottom:0;">
                                            @foreach($kullanilanKonsinyeArray as $cihazText)
                                                <li>{{ $cihazText }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <select name="konsinye_cihaz_select[{{ $plan->id }}]" class="form-control konsinye-cihaz-select">
                                    <option value="">- Seçiniz -</option>
                                    @foreach($konsinyeCihazlar as $konsinyeCihaz)
                                        @if($konsinyeCihaz->current_stock_quantity > 0)
                                            <option value="{{ $konsinyeCihaz->id }}"
                                                    data-available="{{ $konsinyeCihaz->current_stock_quantity }}"
                                                    {{ $seciliKonsinyeCihazId == $konsinyeCihaz->id ? 'selected' : '' }}>
                                                {{ $konsinyeCihaz->urunAdi ?? $konsinyeCihaz->urun_adi ?? 'Ürün Adı Bulunamadı' }} (Mevcut: {{ $konsinyeCihaz->current_stock_quantity }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>

                                <input type="number"
                                       name="konsinye_adet_select[{{ $plan->id }}]"
                                       value="{{ $seciliKonsinyeAdet }}"
                                       min="1"
                                       max="{{ $seciliKonsinyeCihazId ? App\Models\Stock::find($seciliKonsinyeCihazId)->current_stock_quantity ?? 1 : 1 }}"
                                       class="form-control consignment-quantity-input mt-2"
                                       autocomplete="off"
                                       style="width: 100px; {{ $seciliKonsinyeCihazId ? '' : 'display: none;' }}">
                                
                                @if($konsinyeCihazlar->isEmpty() || $konsinyeCihazlar->where('current_stock_quantity', '>', 0)->count() == 0)
                                    <label style="color:red">Uyumlu Konsinye Cihaz Bulunamadı.</label>
                                @endif
                                <input type="hidden" name="soru[{{ $plan->id }}]" class="form-control" value="Konsinye Cihaz"/>
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

    // Ürün Arama (Personel Stoğu) Filtreleme
    $('#urunAraInput').on('keyup', function() {
        const searchText = $(this).val().toLowerCase();
        // Sadece bu form grubu içindeki .myParcaList'teki ürünleri filtrele
        $(this).closest('.form-group').find('.myParcaList .stock-item').each(function() {
            const productName = $(this).data('product-name').toLowerCase();
            const productCode = String($(this).data('product-code')).toLowerCase(); 

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

    // Konsinye Cihaz select değiştiğinde adet girişini göster/gizle ve maksimumu ayarla
    $(document).on('change', '.konsinye-cihaz-select', function() {
        const selectedOption = $(this).find('option:selected');
        const quantityInput = $(this).next('.consignment-quantity-input'); // Select'ten sonraki inputu bul
        const availableQuantity = parseInt(selectedOption.data('available')) || 0;

        if (selectedOption.val() !== "") {
            quantityInput.attr('max', availableQuantity).val(1).show();
        } else {
            quantityInput.val(1).removeAttr('max').hide();
        }
    });

    // Adet girişlerine sadece sayı girilmesini sağla ve min/max kontrolü yap
    $(document).on('input', '.quantity-input, .consignment-quantity-input', function() {
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

    // Form Submit
    $("#servisPlanGuncelle").on('submit', function(e) {
        e.preventDefault();
        let formIsValid = true;
        const $form = $(this);

        // Tüm zorunlu alanları (checkbox'lar ve select'ler hariç) kontrol et
        $form.find('[required]').not('.stock-checkbox').not('.konsinye-cihaz-select').each(function() {
            if (!$(this).val()) {
                formIsValid = false;
                $(this).addClass('is-invalid'); // Geçersizse CSS sınıfı ekle
                return false; // Döngüyü kır
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        // Konsinye cihaz select kontrolü
        $('.konsinye-cihaz-select').each(function() {
            const planId = $(this).attr('name').match(/\[(\d+)\]/)[1];
            // Eğer "Konsinye Cihaz" alanı seçilmişse ve boş değilse kontrol et
            if ($form.find('input[name="soru[' + planId + ']"]').val() === 'Konsinye Cihaz') {
                if (!$(this).val()) {
                    formIsValid = false;
                    $(this).addClass('is-invalid');
                    return false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            }
        });
        
        if (!formIsValid) {
            alert('Lütfen tüm zorunlu alanları doldurun.');
            return; // Geçersizse işlemi durdur
        }

        // FormData nesnesini oluştur
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

        // İşaretlenmemiş personel stoğu checkbox'larının verilerini FormData'dan sil
        $('.stock-checkbox:not(:checked)').each(function() {
            const fullName = $(this).attr('name');
            const quantityInputName = $(this).closest('.stock-item').find('.quantity-input').attr('name');
            formData.delete(fullName);
            if (quantityInputName) {
                formData.delete(quantityInputName);
            }
        });
        
        // Konsinye Cihaz seçimi için veri yönetimi
        // `select` ve adet `input`'u zaten doğru isimlerle geldiği için ekstra manipülasyona gerek yok.
        // Controller tarafında bu alanları yakalayıp işleyebiliriz.


        // Debug: FormData içeriğini konsola yazdır
        console.log('FormData içeriği:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        
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
                    
                    // Servis geçmişini güncelle
                    if(typeof loadServiceHistory === 'function' && data.servis_id) {
                        loadServiceHistory(data.servis_id);
                    }

                    $('#editServicePlanModal').modal('hide');
                    $('.nav1').trigger('click');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                alert("Hata: " + xhr.responseText);
            }
        });
    });

    // Dropdown tıklama engelleme (Personel Stoğu için hala geçerli)
    $(document).on('click', '.parcalar-dropdown', function(e) {
        e.stopPropagation();
    });
    
    // --- Başlangıç Ayarları ---
    // Sayfa yüklendiğinde işaretli olmayan stok ürünlerinin adet girişlerini gizle
    $('.stock-checkbox:not(:checked)').closest('.stock-item').find('.quantity-input').hide();
    
    // Konsinye cihaz select başlangıçta seçili değilse adet inputunu gizle
    $('.konsinye-cihaz-select').each(function() {
        if (!$(this).val()) {
            $(this).next('.consignment-quantity-input').hide();
        }
    });
});
</script>