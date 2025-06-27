@if(isset($service_id) && $service_id->bid != 0)
  {{-- Bayi Servisi Formu --}}
  <form method="post" id="servisBayiPlanKaydet" class="col-sm-6" style="margin: 0 auto;padding:10px;">
    @csrf
    @foreach($stage_questions as $stage)
      <div class="row form-group">
        <div class="col-lg-4"><label>{{$stage->soru}}</label></div>
        <div class="col-lg-8">
          @if($stage->cevapTuru == "[Aciklama]")
            <input type="text" name="soru[{{ $stage->id }}]" class="form-control" autocomplete="off" />
          @elseif($stage->cevapTuru == "[Tarih]")
            @php
              $gun = date('l');
              if($gun == "Saturday"){
                $date = date('d/m/Y', strtotime(date("Y-m-d") . ' + 2 days'));
              } else {
                $date = date('d/m/Y', strtotime(date("Y-m-d") . ' + 1 days'));
              }
            @endphp
            <input type="text" name="soru[{{ $stage->id }}]" class="form-control datepicker" readonly value="{{ $date }}" style="background:#fff;" required>
          @elseif($stage->cevapTuru == "[Saat]")
            <select class="form-control" name="soru[{{ $stage->id }}]" required>
              <option value="">-Seçiniz-</option>
              <option value="08:00-10:00">08:00-10:00</option>
              <option value="09:00-11:00">09:00-11:00</option>
              <option value="10:00-12:00">10:00-12:00</option>
              <option value="11:00-13:00">11:00-13:00</option>
              <option value="12:00-14:00">12:00-14:00</option>
              <option value="13:00-15:00">13:00-15:00</option>
              <option value="14:00-16:00">14:00-16:00</option>
              <option value="15:00-17:00">15:00-17:00</option>
              <option value="16:00-18:00">16:00-18:00</option>
              <option value="17:00-19:00">17:00-19:00</option>
              <option value="18:00-20:00">18:00-20:00</option>
              <option value="19:00-21:00">19:00-21:00</option>
              <option value="20:00-22:00">20:00-22:00</option>
              <option value="21:00-23:00">21:00-23:00</option>
            </select>
          @endif
        </div>
      </div>
    @endforeach   
    <div class="row">
      <div class="col-lg-12" style="text-align: center;margin-top: 2px;">
        <input type="hidden" name="servis" class="servisid" value="{{ $service_id->id }}"/>
        <input type="hidden" name="gelenIslem" value="{{ $islem }}"/>
        <input type="hidden" name="gidenIslem" value="{{ $stage_id->id }}"/>
        <input type="submit" class="btn btn-info btn-sm" value="Kaydet"/>
      </div>
    </div>
  </form>
@elseif($islem == "238")
  {{-- Parça Talep Formu --}}
  <form method="post" id="servisPlanParcaKaydet" class="col-sm-6" style="margin: 0 auto;padding:10px;">
    @csrf
    @foreach($stage_questions as $stage)
      <div class="card card-body secilenlerDiv"></div>
      <input id="urunAraInputDepo" type="text" class="form-control" data-id="" autocomplete="off" autofocus="on" placeholder="Ürün adı veya ürün kodu arayın" style="border:2px solid #ff0000 !important"> 
      <div class="card" style="margin-top:5px;">
        <div class="card-body myParcaListDepo" style="max-height: 170px; overflow: auto;"></div>
      </div>  
      <input type="hidden" name="soru[{{ $stage->id }}]" class="form-control" value="Parca"/>
    @endforeach
        
    <div class="col-lg-12" style="text-align: center;margin-top: 10px;">
      <input type="hidden" name="servis" class="servisid" value="{{ $service_id->id }}"/>
      <input type="hidden" name="gelenIslem" value="{{ $islem }}"/>
      <input type="hidden" name="gidenIslem" value="{{ $stage_id->id }}"/>
      <input type="hidden" name="secilenUrunlerInput" class="secilenUrunlerInput" value=""/>
      <input type="submit" class="btn btn-primary" value="Kaydet"/>
    </div>
  </form>
@else
  {{-- Normal Servis Formu --}}
  <form method="post" id="servisPlanKaydet" action="{{ route('save.service.plan', $firma->id) }}" class="col-sm-6" style="margin: 0 auto;padding:10px;">
    @csrf
    @foreach($stage_questions as $stage)
      @if($stage->cevapTuru == "[Parca]")
        <div class="row form-group">
          <div class="col-lg-12">
            <input id="urunAraInput" type="text" class="form-control" data-id="" autocomplete="off" autofocus="on" placeholder="Ürün adı veya ürün kodu">                   
            <div class="parcalar-dropdown myParcaList" style="width:100%">
              @php $say = 0; @endphp
              @foreach($stoklar as $stok)
                @if($stok->adet > 0)
                  @php 
                    $stokSec = DB::table('stoklar')->where('id', $stok->stokid)->first();
                    $say++;
                  @endphp
                  <div class="checkbox" style="padding:3px 0;">
                    <label style="width: calc(100% - 40px);display: inline-block;text-transform: capitalize;">
                      <input type="checkbox" name="stokCheck{{ $stok->stokid }}" value="{{ $stok->stokid }}" style="position: relative; top:2px; margin-right:3px;text-transform: capitalize;">
                      {{ $stokSec->urunAdi }} ({{ $stok->adet }})
                    </label>
                    <input type="number" name="stokAdet{{ $stok->stokid }}" value="1" class="form-control" autocomplete="off" style="width: 40px;display: inline-block;text-align:center;">
                  </div>
                @endif
              @endforeach
                            
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
                  $asamaGrup = explode(", ", $stage->cevapTuru);
                  $gruplar = [];
                  foreach($asamaGrup as $grup) {
                    $grupNo = explode("-", $grup)[1];
                    $grupNo = rtrim($grupNo, ')');
                    $gruplar[] = $grupNo;
                  }
                  $grupStr = implode(',', $gruplar);
                @endphp
                @php
                    $teknisyenPersonel = App\Models\User::where('tenant_id', $firma->id)
                        ->where('status', '1')
                        ->whereHas('roles', function($query) {
                            $query->whereIn('name', ['Teknisyen', 'Teknisyen Yardımcısı']);
                        })
                        ->with('roles') // roles ilişkisini önceden yükle
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
                  $bugun = date('w'); // 0: Pazar, 6: Cumartesi
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
        <input type="hidden" name="gelenIslem" value="{{ $islem }}"/>
        <input type="hidden" name="gidenIslem" value="{{ $stage_id->id }}"/>
        <input type="submit" class="btn btn-info btn-sm" value="Kaydet"/>
      </div>
    </div>
  </form>
@endif

<script>
$(document).ready(function() {
    // Normal servis form submit
    $('#servisPlanKaydet2').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    // Sayfayı yenile veya başka bir işlem yap
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
            }
        });
    });
  });
</script>

<script>
  $(document).ready(function () {
    $('#servisPlanKaydet').submit(function (event) {
      var formIsValid = true;
      $(this).find('input, select').each(function () {
        var isRequired = $(this).prop('required');
        var isEmpty = !$(this).val();
        if (isRequired && isEmpty) {
          formIsValid = false;
          return false;
        }
      });
      if (!formIsValid) {
        event.preventDefault();
        alert('Lütfen zorunlu alanları doldurun.');
        return false;
      }
    });
  });
</script>

<script>
  $(document).ready(function() {
    $('#servisPlanKaydet').on('submit', function(e) {
      e.preventDefault(); // Formun normal submit işlemini durdur
      var formData = new FormData(this); // Form verilerini al
      $.ajax({
        url: $(this).attr('action'), // Formun action değerini kullan
        type: $(this).attr('method'), // Formun method değerini kullan
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response) {
            alert('Aşama başarıyla eklendi');                   
            $('#servisAsamaTable tbody').html(response);
            $('.nav1').trigger('click');                    
            // Formu temizle
            $('#servisPlanKaydet').trigger('reset');
            // Select ve datepicker elementlerini sıfırla
            $('#servisPlanKaydet select').prop('selectedIndex', 0);
            $('#servisPlanKaydet input[type="date"]').val('');
            $('#servisPlanKaydet textarea').val('');

            loadServiceHistory({{ $service_id->id }});
          } else {
            alert('Veri kaydedilemedi.');
          }
        },
        error: function(xhr, status, error) {
          console.error(xhr.responseText);
          alert('Bir hata oluştu.');
        }
      });
    });
  });
</script>