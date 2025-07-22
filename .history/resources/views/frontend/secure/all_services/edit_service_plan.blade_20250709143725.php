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
                                            <div class="checkbox stock-item" style="padding:3px 0;" data-product-code="{{ $stok->urunKodu ?? '' }}" data-product-name="{{ $stok->urunAdi ?? $stok->urun_adi ?? 'N/A' }}">
                                                <label style="width: calc(100% - 40px);display: inline-block;text-transform: capitalize;">
                                                    <input type="checkbox" name="stokCheck{{ $stokId }}" value="on" class="stock-checkbox" style="position: relative; top:2px; margin-right:3px;" {{ $isChecked ? 'checked' : '' }} data-available="{{ $stokAdet }}">
                                                    {{ $stok->urunAdi ?? $stok->urun_adi ?? 'Ürün Adı Bulunamadı' }} (Mevcut: {{ $stokAdet }})
                                                </label>
                                                <input type="number" name="stokAdet{{ $stokId }}" value="{{ $selectedAdet }}" min="1" max="{{ $stokAdet }}" class="form-control quantity-input" autocomplete="off" style="width: 40px;display: inline-block;text-align:center; {{ $isChecked ? '' : 'display:none;' }}">
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
                    {{-- Konsinye Cihaz Seçimi --}}
                    @elseif($soru->cevapTuru == "Konsinye Cihaz")
                        <div class="row form-group">
                            <div class="col-lg-12">
                                <label>{{ $soru->soru }}</label>
                                @php
                                    $kullanilanKonsinyelerArray = [];
                                    $selectedConsignmentIds = []; // Seçili konsinye ID'lerini tutmak için
                                    if ($plan->cevap) {
                                        $cevaplarArray = explode(', ', $plan->cevap);
                                        foreach ($cevaplarArray as $cevapItem) {
                                            list($itemKonsinyeId, $itemAdet) = array_pad(explode('---', $cevapItem), 2, 0);
                                            $kullanilanKonsinye = App\Models\Stock::find($itemKonsinyeId);
                                            if ($kullanilanKonsinye) {
                                                $kullanilanKonsinyelerArray[] = $kullanilanKonsinye->urunAdi . ' (Adet: ' . $itemAdet . ')';
                                                $selectedConsignmentIds[$itemKonsinyeId] = $itemAdet; // ID ve adedi kaydet
                                            }
                                        }
                                    }
                                @endphp

                                @if(!empty($kullanilanKonsinyelerArray))
                                    <div style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9;">
                                        <ul>
                                            @foreach($kullanilanKonsinyelerArray as $konsinyeText)
                                                <li>{{ $konsinyeText }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <input id="konsinyeAraInput_edit_{{ $plan->id }}" type="text" class="form-control urunAraInput" placeholder="Konsinye cihaz adı veya kodu" autocomplete="off">
                                <div class="konsinye-dropdown myKonsinyeList" data-soru-id="{{ $plan->id }}" style="width:100%">
                                    <p>Toplam Konsinye Cihaz Sayısı: {{ $toplamKonsinyeCihazAdedi }}</p>

                                    @forelse($konsinyeCihazlar as $konsinyeCihaz)
                                        @php
                                            $konsinyeId = $konsinyeCihaz->id;
                                            $konsinyeAdet = $konsinyeCihaz->current_stock_quantity ?? 0;

                                            $seciliMi = isset($selectedConsignmentIds[$konsinyeId]);
                                            $seciliAdet = $seciliMi ? $selectedConsignmentIds[$konsinyeId] : 1;
                                        @endphp

                                        @if($konsinyeAdet > 0)
                                            <div class="checkbox stock-item" style="padding:3px 0;"
                                                 data-product-code="{{ $konsinyeCihaz->urunKodu ?? '' }}"
                                                 data-product-name="{{ $konsinyeCihaz->urunAdi ?? $konsinyeCihaz->urun_adi ?? 'N/A' }}">
                                                <label style="width: calc(100% - 40px);display: inline-block;">
                                                    <input type="checkbox" name="konsinyeCheck{{ $konsinyeId }}"
                                                           class="consignment-checkbox"
                                                           value="{{ $konsinyeId }}"
                                                           data-available="{{ $konsinyeAdet }}"
                                                           {{ $seciliMi ? 'checked' : '' }}>
                                                    {{ $konsinyeCihaz->urunAdi ?? $konsinyeCihaz->urun_adi ?? 'Ürün Adı Bulunamadı' }} (Mevcut: {{ $konsinyeAdet }})
                                                </label>
                                                <input type="number" name="konsinyeAdet{{ $konsinyeId }}"
                                                       value="{{ $seciliAdet }}" min="1" max="{{ $konsinyeAdet }}"
                                                       class="form-control quantity-input"
                                                       style="width: 40px;display: inline-block;text-align:center; {{ $seciliMi ? '' : 'display:none;' }}">
                                            </div>
                                        @endif
                                    @empty
                                        <label style="color:red">Uyumlu Konsinye Cihaz Bulunamadı.</label>
                                    @endforelse
                                </div>
                                <input type="hidden" name="soru{{ $soru->id }}" value="Konsinye Cihaz"/>
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

        $('.urunAraInput').on('keyup', function() {
            const searchText = $(this).val().toLowerCase();
            const container = $(this).closest('.form-group'); // Form grubunu bul
            const dropdown = $(this).next('.myParcaList, .myKonsinyeList'); // İlgili dropdown'ı bul

            // Sadece bu dropdown içindeki .stock-item'ları filtrele
            dropdown.find('.stock-item').each(function() {
                const productName = $(this).data('product-name').toLowerCase();
                const productCode = String($(this).data('product-code')).toLowerCase();

                if (productName.includes(searchText) || productCode.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Checkbox değiştiğinde adet girişini göster/gizle
        $(document).on('change', '.stock-checkbox, .consignment-checkbox', function() {
            const quantityInput = $(this).closest('.stock-item').find('.quantity-input');
            const availableQuantity = parseInt($(this).data('available'));

            if ($(this).is(':checked')) {
                quantityInput.attr('max', availableQuantity).val(1).show();
            } else {
                quantityInput.val(1).removeAttr('max').hide();
            }
        });

        // Adet girişi kontrolü
        $(document).on('input', '.quantity-input', function() {
            let value = $(this).val();
            const max = parseInt($(this).attr('max'));
            const min = parseInt($(this).attr('min')) || 1;

            value = parseInt(value) || min;

            if (isNaN(value) || value < min) { // NaN kontrolü ve min değerden küçük olma durumu
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
            // FormData'yı burada oluşturmak yerine, aşağıdaki AJAX isteğinde new FormData(this) kullanın.
            // const formData = new FormData(this); // Bu satır gereksiz

            // Konsinye ve Parça Seçimlerini Dinamik Olarak Topla
            const allSelectedItems = {};

            // Parça seçimlerini topla
            $('.parcalar-dropdown').each(function() {
                const soruId = $(this).data('soru-id');
                const selectedParts = [];
                $(this).find('.stock-checkbox:checked').each(function() {
                    const stockId = $(this).val();
                    const quantity = $(this).closest('.stock-item').find('.quantity-input').val();
                    selectedParts.push(stockId + '---' + quantity);
                });
                if (selectedParts.length > 0) {
                    allSelectedItems[`soru${soruId}`] = selectedParts.join(', ');
                }
            });

            // Konsinye seçimlerini topla
            $('.konsinye-dropdown').each(function() {
                const soruId = $(this).data('soru-id');
                const selectedConsignments = [];
                $(this).find('.consignment-checkbox:checked').each(function() {
                    const consignmentId = $(this).val();
                    const quantity = $(this).closest('.stock-item').find('.quantity-input').val();
                    selectedConsignments.push(consignmentId + '---' + quantity);
                });
                if (selectedConsignments.length > 0) {
                    allSelectedItems[`soru${soruId}`] = selectedConsignments.join(', ');
                }
            });

            // Normal form verilerini al
            const form = $(this);
            const formData = new FormData(form[0]); // Form öğesinden FormData oluştur

            // Dinamik olarak toplanan değerleri FormData'ya ekle/güncelle
            for (const key in allSelectedItems) {
                formData.set(key, allSelectedItems[key]);
            }

            $.ajax({
                url: form.attr('action'),
                type: "POST",
                data: formData, // Güncellenmiş formData'yı gönder
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
                        } else {
                            // Eğer loadServiceHistory fonksiyonu yoksa veya servis_id gelmezse
                            // Bu kısım alert(response.error || 'Bir hata oluştu'); şeklinde değil,
                            // data.error kontrolü yukarıda yapıldığı için başka bir alert olabilir.
                            // Örneğin: console.warn('loadServiceHistory fonksiyonu bulunamadı veya servis_id yok.');
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

        // Dropdown tıklama engelleme (konsinye dropdown için de ekledim)
        $(document).on('click', '.parcalar-dropdown, .konsinye-dropdown', function(e) {
            e.stopPropagation();
        });
    });
</script>