{{-- Normal Servis Formu --}}
<form method="post" id="servisPlanKaydet" action="{{ route('save.service.plan', $firma->id) }}" class="col-sm-6" style="margin: 0 auto;padding:10px;">
    @csrf
    @foreach($stage_questions as $stage)
        {{-- Ana koşul: Bu aşama bir Parça veya Konsinye Cihaz sorusu mu? --}}
        @if($stage->cevapTuru == "[Parca]" || $stage->cevapTuru == '[Konsinye Cihaz]')
            <div class="row form-group">
                <div class="col-lg-12">
                    <label>{{ $stage->soru }}</label>
                    <input id="urunAraInput" type="text" class="form-control" data-id="" autocomplete="off" autofocus="on" placeholder="Ürün adı veya ürün kodu">
                    <div class="parcalar-dropdown myParcaList" style="width:100%">
                        
                        {{-- Personel Stokları Listesi --}}
                        @if($stage->cevapTuru == "[Parca]")
                            @php $say = 0; @endphp
                            <p>Toplam Personel Stok Sayısı: {{ $toplamPersonelStokAdedi }}</p>
                            
                            @forelse($stoklar as $stok)
                                @php
                                    $stokId = $stok->stokid ?? $stok->stok_id ?? $stok->id;
                                    $stokAdet = $stok->adet ?? $stok->quantity ?? 0;
                                    
                                    // Stok bilgisini al
                                    $stokSec = null;
                                    if($stokId) {
                                        $stokSec = App\Models\Stock::where('firma_id', $firma->id)->where('id', $stokId)->first();
                                    }
                                @endphp
                                
                                @if($stokSec && $stokAdet > 0)
                                    @php $say++; @endphp
                                    <div class="checkbox stock-item" style="padding:3px 0;" data-product-code="{{ $stokSec->urunKodu ?? '' }}" data-product-name="{{ $stokSec->urunAdi ?? 'N/A' }}">
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
                                {{-- Bu boş bırakılabilir, aşağıdaki if ile mesaj gösterilecek --}}
                            @endforelse

                            @if($say == 0)
                                <label style="color:red">Personel Stoğunuzda Uyumlu Parça Bulunamadı.</label>
                            @endif

                        {{-- Konsinye Cihaz Listesi (DÜZELTİLDİ) --}}
                        @elseif($stage->cevapTuru == "[Konsinye Cihaz]")
                            @php $say = 0; @endphp
                            <p>Toplam Konsinye Cihaz Sayısı: {{ $toplamConsignmentAdedi }}</p>
                            @forelse($consignmentDevices as $device)
                                @if($device->current_stock_quantity > 0)
                                    @php $say++; @endphp
                                    <div class="checkbox stock-item" style="padding:3px 0;" data-product-code="{{ $device->urunKodu ?? '' }}" data-product-name="{{ $device->name ?? 'N/A' }}">
                                        <label style="width: calc(100% - 40px);display: inline-block;text-transform: capitalize;">
                                            <input type="checkbox" name="parca[{{ $stage->id }}][C_{{ $device->id }}]" 
                                                   class="stock-checkbox"
                                                   value="C_{{ $device->id }}" 
                                                   data-available="{{ $device->current_stock_quantity }}"
                                                   style="position: relative; top:2px; margin-right:3px;">
                                            {{ $device->name ?? 'Cihaz Adı Bulunamadı' }} (Mevcut: {{ $device->current_stock_quantity }})
                                        </label>
                                        <input type="number" name="adet[{{ $stage->id }}][C_{{ $device->id }}]" 
                                               value="1" min="1" max="{{ $device->current_stock_quantity }}" 
                                               class="form-control quantity-input" autocomplete="off" 
                                               style="width: 40px;display: inline-block;text-align:center; display:none;">
                                    </div>
                                @endif
                            @empty
                                {{-- Bu boş bırakılabilir, aşağıdaki if ile mesaj gösterilecek --}}
                            @endforelse

                            @if($say == 0)
                                <label style="color:red">Konsinye Cihaz Bulunamadı.</label>
                            @endif
                        @endif {{-- İçerideki @if($stage->cevapTuru == "[Parca]") veya @elseif($stage->cevapTuru == "[Konsinye Cihaz]") bloğunu kapatır --}}
                    </div>
                </div>
                <input type="hidden" name="soru[{{ $stage->id }}]" class="form-control" value="{{ $stage->cevapTuru }}"/>
            </div>
        @else
            {{-- Diğer soru türleri --}}
            <div class="row form-group">
                <div class="col-lg-4"><label>{{ $stage->soru }}</label></div>
                <div class="col-lg-8">
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
                                ->get()
                            @endphp
                            <select class="form-control" name="soru[{{ $stage->id }}]" required>
                                <option value="">-Seçiniz-</option>
                                @foreach($bayiler as $bayi)
                                    <option value="{{ $bayi->user_id }}">{{ $bayi->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
            @endif {{-- Ana @if($stage->cevapTuru == "[Parca]" || $stage->cevapTuru == '[Konsinye Cihaz]') bloğunu kapatır --}}
        @endforeach
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
    // Arama fonksiyonu
    $('#urunAraInput').on('keyup', function() {
        var searchText = $(this).val().toLowerCase();
        $('.myParcaList .stock-item').each(function() { // .checkbox yerine .stock-item kullanıldı
            var productName = $(this).data('product-name').toLowerCase();
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

    // Checkbox değiştiğinde adet inputunu göster/gizle
    $('.stock-checkbox').on('change', function() {
        const quantityInput = $(this).closest('.stock-item').find('.quantity-input');
        if ($(this).is(':checked')) {
            quantityInput.show();
        } else {
            quantityInput.hide();
            quantityInput.val(1);
        }
    });

    // Sayfa yüklendiğinde seçili olanlar için adet inputlarını göster
    $('.stock-checkbox:checked').each(function() {
        $(this).closest('.stock-item').find('.quantity-input').show();
    });

    // Adet inputunda max değeri kontrol et
    $('.quantity-input').on('change blur', function() {
        let val = parseInt($(this).val());
        const max = parseInt($(this).attr('max'));
        
        if (isNaN(val) || val < 1) {
            $(this).val(1);
        } else if (val > max) {
            $(this).val(max);
            alert('Mevcut adetten fazla giremezsiniz!');
        }
    });

    $('#servisPlanKaydet').on('submit', function(e) {
        e.preventDefault();

        var formIsValid = true;
        $(this).find('[required]').each(function() {
            if ($(this).is('select') && !$(this).val()) {
                formIsValid = false;
                $(this).addClass('is-invalid');
                return false;
            } else if ($(this).is(':checkbox') && $(this).prop('required') && !$(this).is(':checked')) {
                formIsValid = false;
                return false;
            } else if (!$(this).is(':checkbox') && !$(this).val()) {
                formIsValid = false;
                $(this).addClass('is-invalid');
                return false;
            }
        });

        if (!formIsValid) {
            alert('Lütfen tüm zorunlu alanları doldurun.');
            return;
        }

        var formData = new FormData(this);
        $('.stock-checkbox:checked').each(function() {
            const full_name = $(this).attr('name');
            const value = $(this).val();
            const quantityInput = $(this).closest('.stock-item').find('.quantity-input');
            const quantity = quantityInput.val();

            if (quantity && parseInt(quantity) > 0) {
                formData.append(full_name, value);
                formData.append(quantityInput.attr('name'), quantity);
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