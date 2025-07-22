{{-- Normal Servis Formu --}}
<form method="post" id="servisPlanKaydet" action="{{ route('save.service.plan', $firma->id) }}" class="col-sm-6" style="margin: 0 auto;padding:10px;">
    @csrf
    @foreach($stage_questions as $question) {{-- $stage yerine $question kullandım, daha okunur olması için --}}
        @if($question->soruTuru == "[Parca]") {{-- `cevapTuru` yerine `soruTuru` kullandım, sizin controller'ınızda bu isimdeydi --}}
            <div class="row form-group">
                <div class="col-lg-12">
                    <label>{{ $question->soru }}</label>
                    
                    {{-- YENİ EKLENEN KISIM: Tab Yapısı ve İçerikleri --}}
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" id="myTab-{{ $question->id }}" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="personal-stock-tab-{{ $question->id }}" data-bs-toggle="tab" data-bs-target="#personal-stock-{{ $question->id }}" type="button" role="tab" aria-controls="personal-stock-{{ $question->id }}" aria-selected="true">
                                        Personel Stokları ({{ $toplamPersonelStokAdedi }})
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="consignment-devices-tab-{{ $question->id }}" data-bs-toggle="tab" data-bs-target="#consignment-devices-{{ $question->id }}" type="button" role="tab" aria-controls="consignment-devices-{{ $question->id }}" aria-selected="false">
                                        Konsinye Cihazlar ({{ $toplamConsignmentAdedi }})
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="myTabContent-{{ $question->id }}">
                                {{-- Personel Stokları Sekmesi İçeriği --}}
                                <div class="tab-pane fade show active" id="personal-stock-{{ $question->id }}" role="tabpanel" aria-labelledby="personal-stock-tab-{{ $question->id }}">
                                    <input type="text" class="form-control mb-2" id="searchPersonalStock-{{ $question->id }}" placeholder="Personel Stoğu Ara...">
                                    <div class="personal-stock-list myParcaList" style="max-height: 300px; overflow-y: auto;">
                                        @php $say = 0; @endphp
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
                                                @php $say++; @endphp
                                                <div class="form-check d-flex justify-content-between align-items-center mb-2 stock-item" data-product-name="{{ $stokSec->urunAdi ?? 'N/A' }}" data-product-code="{{ $stokSec->urunKodu ?? '' }}">
                                                    <input class="form-check-input stock-checkbox" type="checkbox"
                                                           name="parca[{{ $question->id }}][]"
                                                           value="{{ $stokId }}"
                                                           id="personal_stock_{{ $question->id }}_{{ $stok->id }}"
                                                           data-type="personal"
                                                           data-available="{{ $stokAdet }}"
                                                           @if(!empty(old('parca')[$question->id]) && in_array($stokId, old('parca')[$question->id])) checked @endif
                                                    >
                                                    <label class="form-check-label flex-grow-1 ms-2" for="personal_stock_{{ $question->id }}_{{ $stok->id }}">
                                                        {{ $stokSec->urunAdi ?? 'Ürün Adı Bulunamadı' }} (Mevcut: {{ $stokAdet }})
                                                    </label>
                                                    <input type="number" class="form-control form-control-sm w-25 quantity-input"
                                                           name="adet[{{ $question->id }}][{{ $stokId }}]"
                                                           min="1" max="{{ $stokAdet }}" value="1" style="display:none;"
                                                           @if(!empty(old('parca')[$question->id]) && in_array($stokId, old('parca')[$question->id])) style="display:block;" @endif
                                                    >
                                                </div>
                                            @endif
                                        @empty
                                            <p class="text-muted">Personel stoğunuz bulunmamaktadır.</p>
                                        @endforelse
                                        @if($say == 0)
                                            <label style="color:red">Uyumlu Parça Bulunamadı.</label>
                                        @endif
                                    </div>
                                </div>

                                {{-- Konsinye Cihazlar Sekmesi İçeriği --}}
                                <div class="tab-pane fade" id="consignment-devices-{{ $question->id }}" role="tabpanel" aria-labelledby="consignment-devices-tab-{{ $question->id }}">
                                    <input type="text" class="form-control mb-2" id="searchConsignmentDevice-{{ $question->id }}" placeholder="Konsinye Cihaz Ara...">
                                    <div class="consignment-device-list myParcaList" style="max-height: 300px; overflow-y: auto;">
                                        @forelse($consignmentDevices as $device)
                                            <div class="form-check d-flex justify-content-between align-items-center mb-2 stock-item" data-product-name="{{ $device->name ?? 'N/A' }}" data-product-code="{{ $device->urunKodu ?? '' }}">
                                                <input class="form-check-input stock-checkbox" type="checkbox"
                                                       name="parca[{{ $question->id }}][]"
                                                       value="C_{{ $device->id }}" {{-- Konsinye ID'leri için 'C_' öneki --}}
                                                       id="consignment_device_{{ $question->id }}_{{ $device->id }}"
                                                       data-type="consignment"
                                                       data-available="{{ $device->current_stock_quantity ?? 1 }}" {{-- Controller'dan gelmeli --}}
                                                       @if(!empty(old('parca')[$question->id]) && in_array('C_'.$device->id, old('parca')[$question->id])) checked @endif
                                                >
                                                <label class="form-check-label flex-grow-1 ms-2" for="consignment_device_{{ $question->id }}_{{ $device->id }}">
                                                    {{ $device->name ?? 'Cihaz Adı Yok' }} (Mevcut: {{ $device->current_stock_quantity ?? 'Kontrol Edilecek' }})
                                                </label>
                                                <input type="number" class="form-control form-control-sm w-25 quantity-input"
                                                       name="adet[{{ $question->id }}][C_{{ $device->id }}]"
                                                       min="1" max="{{ $device->current_stock_quantity ?? 1 }}" value="1" style="display:none;"
                                                       @if(!empty(old('parca')[$question->id]) && in_array('C_'.$device->id, old('parca')[$question->id])) style="display:block;" @endif
                                                >
                                            </div>
                                        @empty
                                            <p class="text-muted">Konsinye cihaz bulunmamaktadır.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- YENİ EKLENEN KISIM SONU --}}

                </div>
                <input type="hidden" name="soru[{{ $question->id }}]" class="form-control" value="Parca"/>
            </div>
        @else
            {{-- Mevcut diğer soru türleri --}}
            <div class="row form-group">
                <div class="col-lg-4"><label>{{ $question->soru }}</label></div>
                <div class="col-lg-8">
                    @if($question->cevapTuru == "[Aciklama]")
                        <input type="text" name="soru[{{ $question->id }}]" class="form-control" autocomplete="off" />
                    @elseif(str_contains($question->cevapTuru, 'Grup'))
                        @if(str_contains($question->cevapTuru, 'Grup-0'))
                            @php
                                $adminPersonel = App\Models\User::where('tenant_id', $firma->id)
                                    ->where('status', '1')
                                    ->whereHas('roles', function($query) {
                                        $query->where('name', 'Admin');
                                    })
                                    ->orderBy('name', 'asc')
                                    ->get();
                            @endphp
                            <select class="form-control" name="soru[{{ $question->id }}]" required>
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
                            <select class="form-control" name="soru[{{ $question->id }}]" required>
                                <option value="">-Seçiniz-</option>
                                @foreach($teknisyenPersonel->filter(function($personel) {
                                    return $personel->roles->pluck('name')->intersect(['Teknisyen', 'Teknisyen Yardımcısı'])->isNotEmpty();
                                }) as $personel)
                                    <option value="{{ $personel->user_id }}">{{ $personel->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    @elseif($question->cevapTuru == "[Tarih]")
                        @php
                            $bugun = date('w');
                            $date = ($bugun == 6)
                                ? date('Y-m-d', strtotime('+2 days'))
                                : date('Y-m-d', strtotime('+1 day'));
                        @endphp
                        <input type="date" name="soru[{{ $question->id }}]" class="form-control datepicker" value="{{ $date }}" style="background:#fff;" required>
                    @elseif($question->cevapTuru == "[Saat]")
                        @php
                            $hours = [
                                "08:00-10:00", "09:00-11:00", "10:00-12:00",
                                "11:00-13:00", "12:00-14:00", "13:00-15:00",
                                "14:00-16:00", "15:00-17:00", "16:00-18:00",
                                "17:00-19:00", "18:00-20:00", "19:00-21:00",
                                "20:00-22:00", "21:00-23:00"
                            ];
                        @endphp
                        <select class="form-control" name="soru[{{ $question->id }}]" required>
                            <option value="">-Seçiniz-</option>
                            @foreach($hours as $hour)
                                <option value="{{ $hour }}">{{ $hour }}</option>
                            @endforeach
                        </select>
                    @elseif($question->cevapTuru == "[Arac]")
                        <select class="form-control" name="soru[{{ $question->id }}]" required>
                            <option value="">-Seçiniz-</option>
                            @foreach($araclar as $arac)
                                <option value="{{ $arac->id }}">{{ $arac->arac }}</option>
                            @endforeach
                        </select>
                    @elseif($question->cevapTuru == "[Fiyat]")
                        <input type="number" name="soru[{{ $question->id }}]" class="form-control" autocomplete="off" required/>
                    @elseif($question->cevapTuru == "[Teklif]")
                        <input type="number" name="soru[{{ $question->id }}]" class="form-control" autocomplete="off" required/>
                        <span style="font-size: 12px; color: red; font-weight: 500; margin: 0; padding: 0;display: block;">Bu alan sadece teklif vermek için kullanılır.</span>
                    @elseif($question->cevapTuru == "[Bayi]")
                        @php
                            $bayiler = App\Models\User::where('tenant_id', $firma->id)
                                ->where('status', '1')
                                ->whereHas('roles', function($query) {
                                    $query->whereIn('name', ['Bayi']);
                                })
                                ->orderBy('name', 'asc')
                                ->get();
                        @endphp
                        <select class="form-control" name="soru[{{ $question->id }}]" required>
                            <option value="">-Seçiniz-</option>
                            @foreach($bayiler as $bayi)
                                <option value="{{ $bayi->user_id }}">{{ $bayi->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
        @endif
    @endforeach
    <div class="row">
        <div class="col-lg-12" style="text-align: center;margin-top: 2px;">
            <input type="hidden" name="servis" class="servisid" value="{{ $service_id->id }}"/>
            {{-- JSON string olarak gönderiliyor --}}
            <input type="hidden" name="gelenIslem" value="{{ json_encode($islem) }}"/>
            <input type="hidden" name="gidenIslem" value="{{ $stage_id->id }}"/>
            <input type="submit" class="btn btn-info btn-sm" value="Kaydet"/>
        </div>
    </div>
</form>

{{-- JavaScript Bölümü --}}
<script>
$(document).ready(function() {
    // Genel arama fonksiyonu
    function filterStockItems(searchText, listSelector) {
        const searchTextLower = searchText.toLowerCase();
        $(listSelector + ' .stock-item').each(function() {
            const productName = $(this).data('product-name').toLowerCase();
            const productCode = $(this).data('product-code') ? String($(this).data('product-code')).toLowerCase() : '';

            if (productName.includes(searchTextLower) || productCode.includes(searchTextLower)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    // Personel Stoğu Arama
    $('[id^="searchPersonalStock-"]').on('keyup', function() {
        const questionId = $(this).attr('id').split('-')[1];
        filterStockItems($(this).val(), '#personal-stock-' + questionId);
    });

    // Konsinye Cihaz Arama
    $('[id^="searchConsignmentDevice-"]').on('keyup', function() {
        const questionId = $(this).attr('id').split('-')[1];
        filterStockItems($(this).val(), '#consignment-devices-' + questionId);
    });

    // Checkbox değiştiğinde adet inputunu göster/gizle
    $('.stock-checkbox').on('change', function() {
        const quantityInput = $(this).closest('.stock-item').find('.quantity-input');
        if ($(this).is(':checked')) {
            quantityInput.show();
        } else {
            quantityInput.hide();
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
        
        if (isNaN(val) || val < 1) { // Boş veya geçersiz giriş için
            $(this).val(1);
        } else if (val > max) {
            $(this).val(max);
            alert('Mevcut adetten fazla giremezsiniz!');
        }
    });

    $('#servisPlanKaydet').on('submit', function(e) {
        e.preventDefault(); // Formun normal submit işlemini durdur

        // Formun gerekli alanlarının dolu olup olmadığını kontrol et
        var formIsValid = true;
        $(this).find('[required]').each(function() {
            if ($(this).is('select') && !$(this).val()) { // Select kutuları için boş değer kontrolü
                formIsValid = false;
                $(this).addClass('is-invalid'); // Hata sınıfı ekle (isteğe bağlı)
                return false;
            } else if ($(this).is(':checkbox') && $(this).prop('required') && !$(this).is(':checked')) { // Zorunlu checkbox kontrolü
                formIsValid = false;
                return false;
            } else if (!$(this).is(':checkbox') && !$(this).val()) { // Diğer zorunlu inputlar için boş değer kontrolü
                formIsValid = false;
                $(this).addClass('is-invalid'); // Hata sınıfı ekle (isteğe bağlı)
                return false;
            }
        });

        if (!formIsValid) {
            alert('Lütfen tüm zorunlu alanları doldurun.');
            return; // Formu göndermeyi durdur
        }

        var formData = new FormData(this); // Form verilerini al

        $.ajax({
            url: $(this).attr('action'), // Formun action değerini kullan
            type: $(this).attr('method'), // Formun method değerini kullan
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    // Alt aşamaları güncelle
                    if (response.altAsamalar) {
                        var altAsamalarSelect = $('.servisAsamalari .altAsamalar');
                        altAsamalarSelect.empty();
                        altAsamalarSelect.append('<option value="">-Seçiniz-</option>');

                        $.each(response.altAsamalar, function(index, item) {
                            altAsamalarSelect.append('<option value="' + item.id + '">' + item.asama + '</option>');
                        });
                        altAsamalarSelect.prop('selectedIndex', 0); // Hiçbir seçenek seçili olmasın
                    }

                    // Güncel aşama bilgisini güncelle
                    $('.servisAsamalari .kayitAlan span').text(response.asama);

                    // Servis geçmişini ve datatable'ı yenile
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