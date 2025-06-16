<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="card card1" style="margin-bottom: 5px">
  <div class="card-header ch1" style="padding: 3px 10px!important;">
    <div class="row">
      <div class="col-4 col-md-4 left">
        <label>Tarih: </label>
        <input type="text" name="tarih" class="form-control tarih" value="{{ Carbon\Carbon::parse($service_id->created_at)->format('d/m/Y H:i:s')}}" disabled="" style="width: 120px;display: inline-block;background: #fff;padding: 3px 5px;font-size:12px;">
      </div>
      <div class="col-8 col-md-8 text-align-right" style="text-align: right;"> 
        <label>Müşteri Kaynağı: </label>
        <select class="form-control form-select kaynak" name="kaynak" style="width: 110px; display: inline-block;padding: 3px 5px;font-size:12px;">
          <option value="">-Seçiniz-</option>
          @foreach($service_resources as $resource)
            <option value="{{$resource->id}}" {{ $service_id->servisKaynak == $resource->id ? 'selected' : ''}}>{{$resource->kaynak}}</option>
          @endforeach
        </select>

        <label>Operatör: <span class="kayitAlan">{{$service_id->users->name}}</span> </label>

      </div>
    </div>
  </div>      
</div>

<div class="row cardWrap2">
  <div class="col-sm-6">
    <div class="card card2">
      <div class="card-header" style="padding: 7px 10px!important;">MÜŞTERİ BİLGİSİ
        <span><a href="#" data-id="{{$service_id->musteri->id}}" class="servisMusteriDuzenleBtn"><i class="fas fa-edit" style="font-size: 15px;color: red;text-shadow: none;"></i></a></span>
      </div>
      <div class="card-body" id="card2">
        @if(!empty($service_id->musteri->adSoyad))<span class="musBilCek" id="musBilCek"><strong>{{$service_id->musteri->adSoyad}}       
        @if($service_id->musteri->musteriTipi == '1')
          (BİREYSEL)
        @elseif($service_id->musteri->musteriTipi == '2')
          (KURUMSAL)
        @endif
        </strong></span>@endif
        @if(!empty($service_id->musteri->tel1))<span id="tele"><a href="tel:{{$service_id->musteri->tel1}}" style="color:red">{{$service_id->musteri->tel1}}</a> - <a href="tel:{{$service_id->musteri->tel2}}" style="color:red">{{$service_id->musteri->tel2}}</a></span>@endif  
        @if(!empty($service_id->musteri->adres))<span id="maps">{{$service_id->musteri->adres}}</span>@endif
        @if(!empty($service_id->musteri->vergiNo))<span id="vergi">{{$service_id->musteri->vergiNo}} / {{$service_id->musteri->vergiDairesi}}</span>@endif
      </div>
    </div>
    <div class="card b1">
      <div class="card-body" style="padding-top: 6px;">
        <div class="row form-group" style="border: 0;margin-bottom:0;">
          <div class="col-md-4 rw1"><label>Müsait Olma Zamanı</label></div>
            <div class="col-md-8 rw2">
              <input name="musaitTarih" type="date" class="form-control datepicker kayitTarihi" value="{{$service_id->musaitTarih}}" style="background:#fff;display: inline-block;width: 105px;" data-has-listeners="true">
              <select name="musaitSaat1" class="form-control form-select musaitSaat1" style="width: 75px;display: inline-block;">
                @php
                  $saatler = [
                    "08:00","08:30","09:00","09:30","10:00","10:30","11:00","11:30",
                    "12:00","12:30","13:00","13:30","14:00","14:30","15:00","15:30",
                    "16:00","16:30","17:00","17:30","18:00","18:30","19:00","19:30",
                    "20:00","20:30","21:00","21:30","22:00","22:30","23:00"
                  ];
                @endphp
                @foreach ($saatler as $saat)
                  <option value="{{ $saat }}" {{ $service_id->musaitSaat1 == $saat ? 'selected' : '' }}>
                    {{ $saat }}
                  </option>
                @endforeach
              </select>

              <select name="musaitSaat2" class="form-control form-select musaitSaat2" style="width: 75px;display: inline-block;">
                @foreach ($saatler as $saat)
                  <option value="{{ $saat }}" {{ $service_id->musaitSaat2 == $saat ? 'selected' : '' }}>
                    {{ $saat }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>
        
        <div class="row form-group" style="border: 0;margin-bottom:0;">
          <div class="col-md-4 rw1"><label>Konsinye Cihaz</label></div>
          <div class="col-md-8 rw2">
              <select class="form-control form-select konsinye" name="konsinye">
                <option value="">-Seçiniz-</option>
              </select>
          </div>
        </div>
        
        <div class="row form-group" style="border: 0;margin-bottom:0;">
          <div class="col-md-4 rw1"><label>Fatura Numarası</label></div>
          <div class="col-md-8 rw2">
            <input type="text" name="faturaNumarasi" class="form-control buyukYaz" autocomplete="off" value="{{$service_id->faturaNumarasi}}"></div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-sm-6">
      <div class="card card5">
        <div class="card-header" style="padding:3px 7px!important;">CİHAZ BİLGİSİ</div>
        <div class="card-body">
                <div class="row form-group ">
                  <div class="col-md-4 rw1"><label>Cihaz Markası <span style="font-weight: bold; color: red;">*</span></label></div>
                  <div class="col-md-8">
                    <select class="form-control form-select cihazMarka" name="cihazMarka" required>
                      <option value="">-Seçiniz-</option>
                      @foreach($device_brands as $marka)
                        <option value="{{ $marka->id }}" {{$service_id->cihazMarka == $marka->id ? 'selected' : ''}}>{{ $marka->marka }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="row form-group ">
                  <div class="col-md-4 rw1"><label>Cihaz Türü <span style="font-weight: bold; color: red;">*</span></label></div>
                  <div class="col-md-8">
                    <select class="form-control form-select cihazTur" name="cihazTur" required>
                      <option value="">-Seçiniz-</option>
                      @foreach($device_types as $cihaz)
                        <option value="{{ $cihaz->id }}" {{$service_id->cihazTur == $cihaz->id ? 'selected' : ''}}>{{ $cihaz->cihaz }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="row form-group">
                  <div class="col-md-4 rw1"><label>Cihaz Modeli</label></div>
                  <div class="col-md-8"><input type="text" name="cihazModel" class="form-control" autocomplete="off" value="{{$service_id->cihazModel}}"></div>
                </div>
                <div class="row form-group">
                  <div class="col-md-4 rw1"><label>Cihaz Arızası <span style="font-weight: bold; color: red;">*</span></label></div>
                  <div class="col-md-8">
                    <input id="arizaSearch" type="text" name="cihazAriza" class="form-control buyukYaz cihazAriza" autocomplete="off" value="{{$service_id->cihazAriza}}" required>
                    <ul id="arizaResult" style="margin: 0;padding: 0"></ul>
                  </div>
                </div>
                <div class="row form-group">
                  <div class="col-md-4 rw1"><label>Operatör Notu</label></div>
                  <div class="col-md-8"><input type="text" name="opNot" class="form-control opNot" autocomplete="off" value="{{$service_id->operatorNotu}}"></div>
                </div>
                <div class="row form-group" style="margin-bottom: 0; border: 0;">
                  <div class="col-md-4 rw1"><label>Garanti Süresi</label></div>
                  <div class="col-md-8">
                    <select class="form-control form-select" name="cihazGaranti" style="display: inline-block;width: 26%; width: intrinsic; margin-right: 5px;">
                      <option value="">-Seçiniz-</option>
                      @foreach($warranty_periods as $index => $garanti)
                        <option value="{{ $garanti->id }}" {{ $service_id->garantiSuresi == $garanti->id ? 'selected' : ''}}>
                          {{ $garanti->garanti }} Ay
                        </option>
                      @endforeach
                    </select>
                    {{-- Gün gösterimi --}}
                    @php 
                      use Carbon\Carbon;
                      // kayıt tarihi (örnek: "2024-05-01")
                      $kayitTarihi = Carbon::parse($service_id->kayitTarihi);

                      // Seçili garanti süresi
                      $garantiSuresi = $service_id->warranty->garanti; // örneğin 12 (ay)

                      // Garanti bitiş tarihi
                      $garantiBitis = $kayitTarihi->copy()->addMonths($garantiSuresi);

                      // Bugün ile karşılaştır
                      $kalanGun = Carbon::now()->diffInDays($garantiBitis, false); // negatifse süre bitmiştir
                    @endphp
                    @if($kalanGun !== null)
                      <span style="display:inline-block; margin-left: 10px;">
                        @if($kalanGun >= 0)
                          {{ $garantiBitis->format('d/m/Y') }} ({{ $kalanGun }} gün)
                        @else
                          Garanti süresi {{ abs($kalanGun) }} gün önce doldu ({{ $garantiBitis->format('d/m/Y') }})
                        @endif
                      </span>
                    @endif
                  </div>
                </div>
              </div>             
    </div>
  </div>
</div>

<div class="servisAsamalari">
  <div class="card card3">
    <div class="card-header" style="padding: 3px 7px!important;">
      <div class="row">
        <div class="col-4 col-sm-6 left">
          <label class="kayitAlan">  
            <span>{{$service_id->users["name"]}}</span>                  
          </label>     
          <label class="servisAcilLabel servisAcilBtn" style="user-select: none;-ms-user-select: none;-moz-user-select: none;-webkit-user-select: none;-webkit-touch-callout: none;position: relative;margin: 0; color: #fff; background: #343a40; border: 1px solid #212529;padding: 0 5px;border-radius: 3px;height: 25px;top: 1px;line-height: 25px;">
              <span>Acil</span>
              <input type="checkbox" style="display: none;" {{$service_id->acil !== 0 ? 'checked' : ''}}>
              <div class="checkmark"><i class="fas fa-check"></i></div>
            </label>
            <input type="hidden" name="acil" class="acil" value="0"/>          
        </div>
        <div class="col-8 col-sm-6 right">
          <label>Yapılacak işlem: </label>
          <select class="form-control altAsamalar" name="altAsamalar" style="padding:3px 5px;">
            <option value="">-Seçiniz-</option>        
            @foreach ($altAsamalar as $item)
                <option value="{{$item->id}}">{{$item->asama}}</option> 
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="card-body altSecenekler" style="padding:0!important"></div>   
  </div>
</div>

<div class="card card4">
  <div class="card-body" style="padding: 0">
    <div id="no-more-tables">
      <div class="table-responsive" style="margin: 0">
        <table class="table table-hover table-striped servisAsamaTable" id="servisAsamaTable" width="100%" cellspacing="0" style="margin: 0">
          <thead class="title">
            <tr>
              <th style="padding: 5px 10px;font-size: 12px;">Tarih</th>
              <th style="padding: 5px 10px;font-size: 12px;">İşlemi Yapan</th>
              <th style="padding: 5px 10px;font-size: 12px;">İşlem Adı</th>
              <th style="padding: 5px 10px;font-size: 12px;">Açıklama</th>
              <th style="padding: 5px 10px;font-size: 12px;"></th>
            </tr>
          </thead>
          <tbody id="serviceHistoryTableBody">
            
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(".servisMusteriDuzenleBtn").click(function(){
    var id = {{$service_id->musteri_id}};
    var firma_id = {{$firma->id}};
    $('#editServiceCustomerModal').modal('show');
    $.ajax({
      url: "/" + firma_id + "/servis-musteri/duzenle/" + id
    }).done(function(data) {
      if($.trim(data)==="-1"){
        window.location.reload(true);
      }else{
        $('#editServiceCustomerModal .modal-body').html(data);
      }
    });
  });
  
</script>

<script>
  $(document).ready(function () {
    var musteriAdSoyad = "{{$service_id->musteri->adSoyad}}";
    var musteriFirmaAdi = "{{$service_id->id}}";
    $("#editServiceDescModal .modal-title").html(musteriAdSoyad + " (" + musteriFirmaAdi + ")");
  });
</script>

<script type="text/javascript">
  var csrfToken = $('meta[name="csrf-token"]').attr('content');
  $('.kategori').on('change', function(){
    var id = $(this).val();
    var musteri = "{{$service_id->id}}";
    $.ajax({
      url: "",
      method: "POST",
      data: {
        id:id,
        musteri:musteri,
        _token: csrfToken,
      },
    }).done(function(data){
      alert("Müşteri türü güncellendi");
    });
  });

  $('.kaynak').on('change', function(){
    var id = $(this).val();
    var musteri = "{{$service_id->id}}";
    $.ajax({
      url: "",
      method:"POST",
      data: {
        id:id,
        musteri:musteri,
        _token:csrfToken,
      },
    }).done(function(data){
      alert("Müşteri kaynağı güncellendi");
    });
  });

  $(".altAsamalar").on("change", function () {
    var id = $(this).val();
    var service = {{$service_id->id}};
    var firma_id = {{$firma->id}};
    if(id){
      $.ajax({
        url: "/" + firma_id + "/servis-asama-sorusu-getir/" + id + "/" + service 
      }).done(function(data) {
        if($.trim(data)==="-1"){
          window.location.reload(true);
        }else{
          $('.altSecenekler').html(data);
        }
      });
    }else{
      $('.altSecenekler').html("");
    }
  });

  $(".opNotEkleBtn").click(function() {
    var not = $(".opNot").val();
    var musteri = {{$service_id->id}};
    $.ajax({
      url: "",
      method: "POST",
      data: {
        cnote:not,
        id:musteri,
        _token:csrfToken,
      },
    }).done(function(data){
      if(data === false){
        window.location.reload(true);
      }else{
        $(".opNot").val("");
        $('#servisAsamaTable tbody').html(data);
        $('#datatableCustomer').DataTable().ajax.reload();
        $('.nav1').trigger('click');
      }
    });
  });
</script>

<script>
  $(document).ready(function() {
    $('#servisAsamaTable').on('click', '.musteriPlanSil', function(e) {
      e.preventDefault();
      var confirmDelete = confirm("Bu müşteri aşamasını silmek istediğinizden emin misiniz?");
      if (confirmDelete) {
        var id = $(this).attr('data-id');
        $.ajax({
          url: '',
          type: 'POST',
          data: {
            _method: 'DELETE', 
            _token: '{{ csrf_token() }}'
          },
          success: function(data) {
            if (data) {
              $('#servisAsamaTable tbody').html(data);
              $('#datatableCustomer').DataTable().ajax.reload();
            } else {
              alert("Silme işlemi başarısız oldu.");
            }
          },
          error: function(xhr, status, error) {
            console.error(xhr.responseText);
          }
        });
      }
    });
  });
</script>

<script>
  $(document).ready(function(){
    $('#servisAsamaTable').on('click', '.musNotDuzenle', function(e) {
      var id = $(this).attr("data-bs-id");
      $('#editCustomerNotModal').modal('show');
      $.ajax({
        url: ""
      }).done(function(data) {
        if ($.trim(data) === "-1") {
          window.location.reload(true);
        } else {  
          $('#editCustomerNotModal .modal-body').html(data);              
        }
      });
    });

    $('#servisAsamaTable').on('click', '.musPlanDuzenle', function(e) {
      var id = $(this).attr("data-bs-id");
      $('#editCustomerPlanModal').modal('show');
      $.ajax({
        url: ""
      }).done(function(data) {
        if ($.trim(data) === "-1") {
          window.location.reload(true);
        } else {       
          $('#editCustomerPlanModal .modal-body').html(data);               
        }
      });
    });
  });
</script>