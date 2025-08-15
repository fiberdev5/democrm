<form method="post" id="editBayi" action="{{ route('update.dealer', [$firma->id, $bayi->user_id]) }}" enctype="multipart/form-data">
  @csrf

  <div class="row">
    <label class="col-sm-4">Başlama Tarihi<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <input name="baslamaTarihi" class="form-control" type="date" value="{{ $bayi->baslamaTarihi }}" required>
    </div>
  </div>

  <div class="row">
      <label class="col-sm-4">Bayi Durumu:<span style="font-weight: bold; color: red;">*</span></label>
      <div class="col-sm-8">
        <select name="status" class="form-select durum" required>
          <option value="1" {{ $bayi->status == "1" ? 'selected' : ''}}>Çalışıyor</option>
          <option value="0" {{ $bayi->status == "0" ? 'selected' : ''}}>Ayrıldı</option>
        </select>
      </div>
  </div> <!--end row-->
  
  <div class="row ayrilmaTarihi">
      <label class="col-sm-4">Ayrılma Tarihi:<span style="font-weight: bold; color: red;">*</span></label>
      <div class="col-sm-8">
          <input name="ayrilmaTarihi" class="form-control datepicker ayrilmaTarihi" type="date" value="{{$bayi->ayrilmaTarihi}}" style="border: 1px solid #ced4da;">
      </div>
  </div>

  <div class="row">
    <label class="col-sm-4">Ad Soyad<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <input name="name" class="form-control" type="text" value="{{ $bayi->name }}" required>
    </div>
  </div>

  <div class="row">
    <label class="col-sm-4">Vergi No<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <input name="vergiNo" class="form-control" type="text" value="{{ $bayi->vergiNo }}" required>
    </div>
  </div>

  <div class="row">
    <label class="col-sm-4">Vergi Dairesi<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <input name="vergiDairesi" class="form-control" type="text" value="{{ $bayi->vergiDairesi }}" required>
    </div>
  </div>

  
 <div class="row">
    <label class="col-sm-4">Bayi Belgesi</label>
    <div class="col-sm-8">
      @if($bayi->belgePdf)
        @php
          $belgeler = json_decode($bayi->belgePdf, true) ?: [$bayi->belgePdf];
        @endphp
        
        <div class="mb-2">
          <small class="text-success">Mevcut belgeler:</small>
          @foreach($belgeler as $index => $belge)
            @if(Storage::disk('local')->exists($belge))
              @php
                $fileExtension = pathinfo($belge, PATHINFO_EXTENSION);
                $documentUrl = route('dealer.document', [$firma->id, $bayi->user_id, $index]);
              @endphp
              
              <div class="mt-1 p-2 border rounded">
                @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png']))
                  <i class="fas fa-image text-success"></i> Resim {{ $index + 1 }}
                @else
                  <i class="fas fa-file-pdf text-danger"></i> PDF {{ $index + 1 }}
                @endif
                <a href="{{ $documentUrl }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                  <i class="fas fa-eye"></i> Görüntüle
                </a>
              </div>
            @endif
          @endforeach
        </div>
      @else
        <small class="text-warning">Bu bayinin belgesi bulunamadı.</small>
      @endif
      
      <input name="belgePdf[]" id="belgePdfEdit" class="form-control mt-2" type="file" accept=".pdf,.jpg,.jpeg,.png,.svg" multiple>
      <small class="text-muted">Yeni belgeler yüklerseniz eskiler değiştirilir. Maksimum 2 dosya. PDF, JPG, PNG, SVG kabul edilir.</small>
    </div>
  </div>


    <div class="row">
        <div class="col-sm-4"><label>İl/İlçe<span style="font-weight: bold; color: red;">*</span></label></div>
        <div class="col-sm-4">
          <select name="il" id="countrySelect" class="form-control form-select" style="width:100%!important;" required>
            <option value="" selected disabled>-Seçiniz-</option>
            @foreach($countries as $item)
              <option value="{{ $item->id }}" {{ $bayi->il == $item->id ? 'selected' : ''}}>{{ $item->name}}</option>
            @endforeach
          </select>
        </div>
        <div class="col-sm-4">
          <select name="ilce" id="citySelect" class="form-control form-select" style="width:100%!important;" required>
            <option value="" selected disabled>-Seçiniz-</option>                              
          </select>
        </div>
      </div> 


    <div class="row">
      <label class="col-sm-4">Telefon<span style="font-weight: bold; color: red;">*</span></label>
      <div class="col-sm-8">
        <input name="tel" id="tel" class="form-control phone" type="text" value="{{ $bayi->tel }}" required>
      </div>
    </div>

    <div class="row">
      <label class="col-sm-4">Adress:</label>
      <div class="col-sm-8">
      <textarea name="address" type="text" class="form-control">{{$bayi->address}}</textarea>
      </div>
    </div>

  <div class="row">
    <label class="col-sm-4">Kullanıcı Adı<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <input name="username" class="form-control" type="text" value="{{ $bayi->username }}" required>
    </div>
  </div>

  <div class="row">
    <label class="col-sm-4">Yeni Şifre:<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <input name="password" class="form-control" type="password" placeholder="Şifre değiştirmek istemiyorsan boş bırak">
    </div>
  </div>

  <div class="row">
      <div class="col-sm-12 gonderBtn">
        <input type="hidden" name="id" value="{{$bayi->user_id}}">
        <input type="submit" class="btn btn-sm btn-info waves-effect waves-light" value="Kaydet">
      </div>
    </div>
  </form>

<script>
  $(document).ready(function () {
    $(".phone").mask("999 999 9999");
  });
</script>

<script type="text/javascript">
  var getDurum = $(".durum").val();
  if (getDurum == 1) {
    $(".ayrilmaTarihi").hide();
  } else if (getDurum == 0) {
    $(".ayrilmaTarihi").show();
  }

  $(".durum").change(function () {
    var getDurum = $(".durum").val();
    if (getDurum == 1) {
      $(".ayrilmaTarihi").hide();
    } else if (getDurum == 0) {
      $(".ayrilmaTarihi").show();
    }
  });
</script>
<script>
  // Düzenleme formunda maksimum 2 dosya kontrolü
  $(document).ready(function() {
    $('#belgePdfEdit').on('change', function() {
      if (this.files.length > 2) {
        alert('Maksimum 2 dosya seçebilirsiniz!');
        this.value = '';
      }
    });
  });
</script>

<script>
  $(document).ready(function () {
    $('#editBayi').submit(function (event) {
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
  $(document).ready(function () {
    var selectedCountryId = {{ $bayi->il == '' ? '0' : $bayi->il }};
    var selectedCityId = {{ $bayi->ilce == '' ? '0' : $bayi->ilce }};
    var citySelect = $("#citySelect");

    if (selectedCountryId) {
      $.get("/get-states/" + selectedCountryId, function (data) {
        citySelect.empty().append(new Option("-Seçiniz-", ""));
        $.each(data, function (index, city) {
          var isSelected = (city.id == selectedCityId);
          citySelect.append(new Option(city.ilceName, city.id, isSelected, isSelected));
        });
      });
    }

    $("#countrySelect").change(function () {
      var selectedIl = $(this).val();
      if (selectedIl) {
        $.get("/get-states/" + selectedIl, function (data) {
          citySelect.empty().append(new Option("-Seçiniz-", ""));
          $.each(data, function (index, city) {
            citySelect.append(new Option(city.ilceName, city.id));
          });
        });
      }
    });
  });
</script>

<script>
  $(document).ready(function () {
    $("#editBayi").submit(function (event) {
      event.preventDefault();
      var formData = new FormData(this);

       $.ajax({
        url: $(this).attr("action"),
        type: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
          $(".btnWrap").html("Yükleniyor. Bekleyin..");
        },
        success: function (data) {
          if (data === false) {
            
            window.location.reload(true);
          } else {
            alert("Bayi bilgileri güncellendi");
            $('#datatableBayi').DataTable().ajax.reload();
            $('#editBayiModal').modal('hide');
            
          }
        },
        error: function (xhr, status, error) {
          alert("Güncelleme başarısız!");
          window.location.reload(true);
        },
      });
    });
  });
  </script>
  
