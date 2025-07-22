{{-- Normal Servis Formu --}}
<form method="post" id="servisPlanKaydet" action="{{ route('save.service.plan', $firma->id) }}" class="col-sm-6" style="margin: 0 auto;padding:10px;">
  @csrf
  @foreach($stage_questions as $stage)
      @if($stage->cevapTuru == "[Parca]")
<div class="row form-group">
    <div class="col-lg-12">
      <label>{{ $stage->soru }}</label>
      <input id="urunAraInput" type="text" class="form-control" data-id="" autocomplete="off" autofocus="on" placeholder="Ürün adı veya ürün kodu">
      <div class="parcalar-dropdown myParcaList" style="width:100%">
        @php $say = 0; @endphp
      
        <p>Toplam Stok Sayısı: {{ $stoklar->count() }}</p>
        
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
            <div class="checkbox" style="padding:3px 0;" data-product-code="{{ $stokSec->urunKodu ?? '' }}">
              <label style="width: calc(100% - 40px);display: inline-block;text-transform: capitalize;">
                <input type="checkbox" name="stokCheck{{ $stokId }}" value="on" style="position: relative; top:2px; margin-right:3px;">
                {{ $stokSec->urunAdi ?? $stokSec->urun_adi ?? 'Ürün Adı Bulunamadı' }} (Mevcut: {{ $stokAdet }})
              </label>
              <input type="number" name="stokAdet{{ $stokId }}" value="1" min="1" max="{{ $stokAdet }}" class="form-control" autocomplete="off" style="width: 40px;display: inline-block;text-align:center;">
            </div>
          @endif
        @empty
          <p style="color:orange;">Stok verisi bulunamadı.</p>
        @endforelse

        @if($say == 0)
          <label style="color:red">Uyumlu Parça Bulunamadı.</label>
        @endif
      </div>
    </div>
    <input type="hidden" name="soru[{{ $stage->id }}]" class="form-control" value="Parca"/>
  </div>
      @else
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

<script>
$(document).ready(function() {
  $('#servisPlanKaydet').on('submit', function(e) {
    e.preventDefault(); // Formun normal submit işlemini durdur

    // Formun gerekli alanlarının dolu olup olmadığını kontrol et
    var formIsValid = true;
    $(this).find('[required]').each(function() {
      if ($(this).is(':checkbox') && !$(this).is(':checked')) {
        formIsValid = false;
        return false;
      } else if (!$(this).val()) {
        formIsValid = false;
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

 
  $('#urunAraInput').on('keyup', function() {
    var searchText = $(this).val().toLowerCase();
    $('.myParcaList .checkbox').each(function() {
      var productName = $(this).find('label').text().toLowerCase();
      if (productName.includes(searchText)) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });
});
</script>