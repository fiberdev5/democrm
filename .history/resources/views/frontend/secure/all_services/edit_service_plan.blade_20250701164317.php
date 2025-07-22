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
                <input id="urunAraInput_update_plan_{{ $soru->id }}" type="text" class="form-control urunAraInput" data-id="{{ $soru->id }}" autocomplete="off" placeholder="Ürün adı veya ürün kodu">
                <div class="parcalar-dropdown myParcaList" id="myParcaList_update_plan_{{ $soru->id }}" style="width:100%; max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 5px;">
                    @php $say = 0; @endphp

                    <p>Toplam Stok Sayısı: {{ $stoklar->count() }}</p>

                    @forelse($stoklar as $stok)
                        @php
                            $stokId = $stok->id;
                            // Controller'dan gelen $selectedParts dizisi, her bir planCevap->id için parçaları tutuyor.
                            // Bizim için önemli olan, o anki $plan->id (yani ServiceStageAnswer'ın ID'si) ile eşleşen parçalar.
                            $isChecked = isset($selectedParts[$plan->id][$stokId]) ? 'checked' : '';
                            $currentQuantity = isset($selectedParts[$plan->id][$stokId]) ? $selectedParts[$plan->id][$stokId] : 1;
                        @endphp

                        @if($stok->adet > 0) {{-- Stok miktarı 0'dan büyükse göster --}}
                            @php $say++; @endphp
                            <div class="checkbox" style="padding:3px 0;">
                                <label style="width: calc(100% - 40px);display: inline-block;text-transform: capitalize;">
                                    <input type="checkbox" name="stokCheck[{{ $soru->id }}][{{ $stokId }}]" value="on" {{ $isChecked }} style="position: relative; top:2px; margin-right:3px;">
                                    {{ $stok->urunAdi ?? $stok->urun_adi ?? 'Ürün Adı Bulunamadı' }} (Mevcut: {{ $stok->adet }})
                                </label>
                                <input type="number" name="stokAdet[{{ $soru->id }}][{{ $stokId }}]" value="{{ $currentQuantity }}" min="1" max="{{ $stok->adet }}" class="form-control" autocomplete="off" style="width: 40px;display: inline-block;text-align:center;">
                            </div>
                        @endif
                    @empty
                        <p style="color:orange;">Stok verisi bulunamadı.</p>
                    @endforelse

                    @if($say == 0)
                        <label style="color:red">Uyumlu Parça Bulunamadı.</label>
                    @endif
                </div>
                {{-- Bu hidden input, Controller'ın bu cevabın bir 'Parca' tipi olduğunu anlaması için kritik. --}}
                {{-- $plan->id, ServiceStageAnswer modelinin ID'sidir. --}}
                <input type="hidden" name="soru{{ $plan->id }}" class="form-control" value="Parca"/>
            </div>
        </div>
    @else {{-- Bu else bloğu, yukarıdaki @if($soru->cevapTuru == "[Parca]") kontrolüne aittir. --}}
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
    @endif {{-- Bu @endif, @if($soru->cevapTuru == "[Parca]") bloğunu kapatır. --}}
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
      $('.urunAraInput').keyup(function() {
        var value = $(this).val().toLowerCase();
        // data-id özniteliğinden, bu arama kutusunun hangi StageQuestion ID'sine ait olduğunu alıyoruz.
        var soruId = $(this).data('id');
        // İlgili parça listesi div'inin ID'sini oluşturuyoruz.
        var targetListId = '#myParcaList_update_plan_' + soruId;

        // Hedeflenen parça listesi içindeki '.checkbox' sınıfına sahip öğeleri filtrele
        $(targetListId + ' .checkbox').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Parçalar dropdown'ına tıklamayı engelleme
    // Bu, dropdown açıldığında, içindeki bir yere tıklamanın dropdown'ı kapatmamasını sağlar.
    $(document).on('click', '.parcalar-dropdown', function(e) {
        e.stopPropagation();
    });
    
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
                        alert(response.error || 'Bir hata oluştu');
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

    // Parça arama
    $('#urunAraInput').keyup(function() {
        var value = $(this).val().toLowerCase();
        $(".myParcaList .checkbox").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // Dropdown tıklama engelleme
    $(document).on('click', '.parcalar-dropdown', function(e) {
        e.stopPropagation();
    });
});
</script>
