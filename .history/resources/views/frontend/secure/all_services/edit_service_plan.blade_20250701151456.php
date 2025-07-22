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
                        {{-- Parça Seçimi --}}
                        <div class="row form-group">
                            <div class="col-lg-12">
                                <label>{{ $soru->soru }}</label>
                                {{-- Ürün arama inputu, ID'si dinamik olmalı --}}
                                <input id="urunAraInput_{{ $soru->id }}" type="text" class="form-control urun-ara-input" data-id="{{ $soru->id }}" autocomplete="off" autofocus="on" placeholder="Ürün adı veya ürün kodu">
                                <div class="parcalar-dropdown myParcaList" style="width:100%">
                                    @php
                                        $say = 0;
                                        // $plan->cevap alanı kaydedilmiş JSON verisini içerir.
                                        // Bu JSON'u çözerek daha önce seçilen parçaların ID'lerini ve miktarlarını alıyoruz.
                                        $selectedParts = json_decode($plan->cevap, true);
                                        $selectedPartIds = collect($selectedParts)->pluck('id')->toArray();
                                    @endphp

                                    {{-- "Toplam Stok Sayısı" kaldırıldı --}}

                                    {{-- Mevcut tüm stokları döngüye alıyoruz --}}
                                    @forelse($stoklar as $stok)
                                        @php
                                            $stokId = $stok->stokid ?? $stok->stok_id ?? $stok->id;
                                            $stokAdet = $stok->adet ?? $stok->quantity ?? 0;

                                            // Stok bilgisini veritabanından getir
                                            $stokSec = null;
                                            if($stokId) {
                                                $stokSec = App\Models\Stock::where('firma_id', $tenant_id)->where('id', $stokId)->first();
                                            }

                                            // Bu stoğun daha önce seçilip seçilmediğini kontrol et
                                            $isChecked = in_array($stokId, $selectedPartIds);
                                            // Eğer seçilmişse, kayıtlı miktarı al, yoksa varsayılan olarak 1 kullan
                                            $selectedQuantity = 1;
                                            if ($isChecked) {
                                                $partInAnswer = collect($selectedParts)->firstWhere('id', $stokId);
                                                $selectedQuantity = $partInAnswer['quantity'] ?? 1;
                                            }
                                        @endphp

                                        {{-- Sadece stokSec varsa ve adet 0'dan büyükse göster --}}
                                        @if($stokSec && $stokAdet > 0)
                                            @php $say++; @endphp
                                            <div class="checkbox" style="padding:3px 0;">
                                                <label style="width: calc(100% - 40px);display: inline-block;text-transform: capitalize;">
                                                    {{-- Checkbox'ı daha önce seçildiyse 'checked' olarak işaretle --}}
                                                    <input type="checkbox" name="stokCheck_{{ $soru->id }}[{{ $stokId }}]" value="on" style="position: relative; top:2px; margin-right:3px;" {{ $isChecked ? 'checked' : '' }}>
                                                    {{ $stokSec->urunAdi ?? $stokSec->urun_adi ?? 'Ürün Adı Bulunamadı' }} (Mevcut: {{ $stokAdet }})
                                                </label>
                                                {{-- Miktar inputunu daha önce kaydedilen miktar ile doldur --}}
                                                <input type="number" name="stokAdet_{{ $soru->id }}[{{ $stokId }}]" value="{{ $selectedQuantity }}" min="1" max="{{ $stokAdet }}" class="form-control" autocomplete="off" style="width: 40px;display: inline-block;text-align:center;">
                                            </div>
                                        @endif
                                    @empty
                                        {{-- Bu kısım, $stoklar dizisi tamamen boş olduğunda çalışır --}}
                                        <p style="color:orange;" class="no-stock-message">Hiç stok verisi bulunamadı.</p>
                                    @endforelse

                                    {{-- Eğer hiç uyumlu parça bulunamadıysa (yani $say hala 0 ise) ve stoklar boş değilse --}}
                                    @if($say == 0 && $stoklar->isNotEmpty())
                                        <label style="color:red" class="no-match-message">Uyumlu Parça Bulunamadı.</label>
                                    @endif
                                </div>
                            </div>
                            {{-- Bu hidden input, sorunun parça tipi olduğunu belirtir --}}
                            <input type="hidden" name="soru[{{ $soru->id }}]" class="form-control" value="[Parca]"/>
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

---

### Güncellenmiş JavaScript

```javascript
<script>
$(document).ready(function(e) {

    // Form Submit
    $("#servisPlanGuncelle").on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this); // Form verilerini al

        // "[Parca]" tipi sorular için seçili parçaları ve miktarlarını FormData'ya manuel olarak ekle
        $('.urun-ara-input').each(function() {
            var questionId = $(this).data('id'); // Soru ID'sini al
            var selectedParts = [];
            // Bu soruya ait seçili checkbox'ları bul
            $(this).closest('.form-group').find('.myParcaList .checkbox input[type="checkbox"]:checked').each(function() {
                // Checkbox'ın 'name' özelliğinden stok ID'sini çıkar (örn: stokCheck_123[456] -> 456)
                var stokIdMatch = $(this).attr('name').match(/\[(\d+)\]$/);
                if (stokIdMatch && stokIdMatch[1]) {
                    var stokId = stokIdMatch[1];
                    // İlgili miktar input'unun değerini al
                    var quantity = $(this).closest('.checkbox').find('input[type="number"]').val();
                    selectedParts.push({ id: stokId, quantity: quantity });
                }
            });
            // Eğer seçili parça varsa, JSON formatında FormData'ya ekle
            if (selectedParts.length > 0) {
                formData.append('soru[' + questionId + ']', JSON.stringify(selectedParts));
            } else {
                // Eğer hiçbir parça seçilmediyse, boş bir dizi olarak gönder
                formData.append('soru[' + questionId + ']', '[]');
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

                    // Servis geçmişini güncelle (varsa)
                    if(typeof loadServiceHistory === 'function' && data.servis_id) {
                        loadServiceHistory(data.servis_id);
                    }

                    // Modalı kapat ve nav'ı tetikle
                    $('#editServicePlanModal').modal('hide');
                    $('.nav1').trigger('click');
                }
            },
            error: function(e) {
                alert("Hata: " + e.responseText);
            }
        });
    });

    // Parça arama inputu için event delegation (dinamik ID'ler için)
    $(document).on('keyup', '.urun-ara-input', function() {
        var value = $(this).val().toLowerCase();
        var $myParcaList = $(this).closest('.form-group').find(".myParcaList");
        var $checkboxes = $myParcaList.find(".checkbox");
        var foundMatches = false;

        $checkboxes.filter(function() {
            var isMatch = $(this).text().toLowerCase().indexOf(value) > -1;
            $(this).toggle(isMatch);
            if (isMatch) {
                foundMatches = true;
            }
            return isMatch;
        });

        // Arama sonucuna göre mesajları göster/gizle
        if (value.length > 0 && !foundMatches) {
            $myParcaList.find('.no-match-message').show(); // "Uyumlu Parça Bulunamadı" göster
            $myParcaList.find('.no-stock-message').hide(); // "Hiç stok verisi bulunamadı" gizle
        } else if (value.length > 0 && foundMatches) {
            $myParcaList.find('.no-match-message').hide(); // "Uyumlu Parça Bulunamadı" gizle
            $myParcaList.find('.no-stock-message').hide(); // "Hiç stok verisi bulunamadı" gizle
        } else {
            // Arama inputu boşsa, tüm checkbox'ları göster ve orijinal mesaj durumuna dön
            $checkboxes.show();
            if ($myParcaList.find('.checkbox:visible').length === 0 && $myParcaList.find('.no-stock-message').length > 0) {
                $myParcaList.find('.no-stock-message').show();
                $myParcaList.find('.no-match-message').hide();
            } else {
                $myParcaList.find('.no-stock-message').hide();
                $myParcaList.find('.no-match-message').hide();
            }
        }
    });

    // Dropdown tıklama engelleme
    $(document).on('click', '.parcalar-dropdown', function(e) {
        e.stopPropagation();
    });
});
</script>