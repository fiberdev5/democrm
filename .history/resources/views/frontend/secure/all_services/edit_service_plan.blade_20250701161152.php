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
<div class="parca-secim-container">
    {{-- Daha Önce Kullanılan Parçalar (Mevcut Parçalar) --}}
    <div class="selected-parcalar-list-{{ $plan->id }}">
        @php
            $selectedParts = json_decode($plan->cevap, true) ?? [];
        @endphp

        @forelse($selectedParts as $part)
            @php
                $stokSec = App\Models\Stock::find($part['id']);
            @endphp
            @if($stokSec)
                <div class="checkbox selected-parca-item" style="padding:3px 0;">
                    <label style="width: calc(100% - 40px);display: inline-block;text-transform: capitalize;">
                        <input type="checkbox" name="stokCheck_{{ $plan->id }}[]" value="{{ $stokSec->id }}" style="position: relative; top:2px; margin-right:3px;" checked>
                        {{ $stokSec->urunAdi ?? $stokSec->urun_adi ?? 'Ürün Adı Bulunamadı' }}
                    </label>
                    <input type="number" name="stokAdet_{{ $plan->id }}[{{ $stokSec->id }}]" value="{{ $part['adet'] }}" min="1" class="form-control" autocomplete="off" style="width: 40px;display: inline-block;text-align:center;">
                </div>
            @endif
        @empty
            <p class="no-selected-parts">Daha önce seçilmiş parça bulunmamaktadır.</p>
        @endforelse
    </div>

    {{-- Yeni Parça Ekleme Alanı --}}
    <div class="new-parca-add-section" style="margin-top: 10px;">
        <input type="text" class="form-control urunAraInput_edit_new" data-planid="{{ $plan->id }}" autocomplete="off" placeholder="Yeni parça eklemek için ürün adı veya kodu">
        <div class="search-results-dropdown" style="display:none; border:1px solid #ddd; max-height: 200px; overflow-y: auto; background:#fff; z-index:100; position:absolute; width:100%;">
            {{-- Arama sonuçları buraya AJAX ile yüklenecek --}}
        </div>
    </div>
</div>
{{-- Bu gizli input parça seçimi olduğunu belirtmek için kullanılır --}}
<input type="hidden" name="soru{{ $plan->id }}" class="form-control" value="Parca"/>
                
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
    $(document).on('change', '.selected-parca-item input[type="checkbox"]', function() {
        var quantityInput = $(this).closest('.selected-parca-item').find('input[type="number"]');
        if ($(this).is(':checked')) {
            quantityInput.prop('disabled', false);
        } else {
            quantityInput.prop('disabled', true);
            quantityInput.val(1); // Kutucuk işaretlenmezse adeti varsayılan değere ayarla
        }
    });

    // Yeni parça ekleme alanı için arama işlevi
    $(document).on('keyup', '.urunAraInput_edit_new', function() {
        var searchText = $(this).val();
        var planId = $(this).data('planid');
        var resultsDropdown = $(this).siblings('.search-results-dropdown');

        if (searchText.length < 3) { // Minimum 3 karakter arama için
            resultsDropdown.hide().empty();
            return;
        }

        // Search isteği atılmadan önce mevcut isteği iptal et (performans için)
        if (this.currentSearchXhr) {
            this.currentSearchXhr.abort();
        }

        const self = this;
        this.currentSearchXhr = $.ajax({
           url: "{{ route('search.stock', ['tenant_id' => $tenant_id]) }}",
            type: "GET",
            data: {
                query: searchText,
                tenant_id: "{{ $tenant_id }}"
            },
            success: function(response) {
                resultsDropdown.empty();
                if (response.length > 0) {
                    $.each(response, function(index, stok) {
                        // Seçilen parçalar listesinde zaten var mı kontrol et
                        var isAlreadySelected = false;
                        $('.selected-parcalar-list-' + planId + ' input[type="checkbox"]').each(function() {
                            if ($(this).val() == stok.id) {
                                isAlreadySelected = true;
                                return false; // Döngüyü kır
                            }
                        });

                        if (!isAlreadySelected) {
                            var stokItem = `
                                <div class="p-2 border-bottom hover-bg-light search-result-item" data-id="${stok.id}" data-name="${stok.urunAdi}" data-planid="${planId}" data-stok-adet="${stok.adet}">
                                    ${stok.urunAdi} (Mevcut: ${stok.adet})
                                </div>
                            `;
                            resultsDropdown.append(stokItem);
                        }
                    });
                    resultsDropdown.show();
                } else {
                    resultsDropdown.append('<div class="p-2">Sonuç bulunamadı.</div>').show();
                }
            },
            error: function(xhr, status, error) {
                if (status === "abort") {
                    // İstek iptal edildi, hata mesajı gösterme
                    return;
                }
                console.error("Stok arama hatası:", xhr.responseText);
                resultsDropdown.empty().append('<div class="p-2 text-danger">Arama sırasında bir hata oluştu.</div>').show();
            }
        });
    });

    // Arama sonucuna tıklayınca parçayı forma ekle
    $(document).on('click', '.search-results-dropdown .search-result-item', function() {
        var stokId = $(this).data('id');
        var stokAdi = $(this).data('name');
        var planId = $(this).data('planid');
        var stokAdetMax = $(this).data('stok-adet'); // Stok adetini al
        var resultsDropdown = $(this).closest('.search-results-dropdown');
        var currentInput = resultsDropdown.siblings('.urunAraInput_edit_new');

        // Mevcut parçalar listesine yeni parçayı ekle
        var newItem = `
            <div class="checkbox selected-parca-item" style="padding:3px 0;">
                <label style="width: calc(100% - 40px);display: inline-block;text-transform: capitalize;">
                    <input type="checkbox" name="stokCheck_${planId}[]" value="${stokId}" style="position: relative; top:2px; margin-right:3px;" checked>
                    ${stokAdi}
                </label>
                <input type="number" name="stokAdet_${planId}[${stokId}]" value="1" min="1" max="${stokAdetMax}" class="form-control" autocomplete="off" style="width: 40px;display: inline-block;text-align:center;">
            </div>
        `;
        $('.selected-parcalar-list-' + planId).append(newItem);
        $('.selected-parcalar-list-' + planId).find('.no-selected-parts').remove(); // "Daha önce seçilmiş parça yok" yazısını kaldır

        // Arama alanını temizle ve sonuçları gizle
        currentInput.val('');
        resultsDropdown.hide().empty();
    });

    // Arama sonuçları dışında bir yere tıklayınca dropdown'ı gizle
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.new-parca-add-section').length) {
            $('.search-results-dropdown').hide().empty();
        }
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

    // // Parça arama
    // $('#urunAraInput').keyup(function() {
    //     var value = $(this).val().toLowerCase();
    //     $(".myParcaList .checkbox").filter(function() {
    //         $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    //     });
    // });

    // Dropdown tıklama engelleme
    $(document).on('click', '.parcalar-dropdown', function(e) {
        e.stopPropagation();
    });
});
</script>
