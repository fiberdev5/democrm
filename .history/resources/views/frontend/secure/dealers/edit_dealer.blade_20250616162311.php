<form method="post" id="editBayi" action="{{ route('update.dealer', [$firma->id, $bayi->user_id]) }}" enctype="multipart/form-data">
  @csrf

  <div class="row">
    <label class="col-sm-4">Başlama Tarihi*</label>
    <div class="col-sm-8">
      <input name="baslamaTarihi" class="form-control" type="date" value="{{ $bayi->baslamaTarihi }}" required>
    </div>
  </div>

  <div class="row">
      <label class="col-sm-4">Bayi Durumu: </label>
      <div class="col-sm-8">
        <select name="status" class="form-select durum" required>
          <option value="1" {{ $bayi->status == "1" ? 'selected' : ''}}>Çalışıyor</option>
          <option value="0" {{ $bayi->status == "0" ? 'selected' : ''}}>Ayrıldı</option>
        </select>
      </div>
  </div> <!--end row-->
  
  <div class="row ayrilmaTarihi">
      <label class="col-sm-4">Ayrılma Tarihi:</label>
      <div class="col-sm-8">
          <input name="ayrilmaTarihi" class="form-control datepicker ayrilmaTarihi" type="date" value="{{$bayi->ayrilmaTarihi}}" style="border: 1px solid #ced4da;">
      </div>
  </div>

  <div class="row">
    <label class="col-sm-4">Ad Soyad*</label>
    <div class="col-sm-8">
      <input name="name" class="form-control" type="text" value="{{ $bayi->name }}" required>
    </div>
  </div>

  <div class="row">
    <label class="col-sm-4">Vergi No*</label>
    <div class="col-sm-8">
      <input name="vergiNo" class="form-control" type="text" value="{{ $bayi->vergiNo }}" required>
    </div>
  </div>

  <div class="row">
    <label class="col-sm-4">Vergi Dairesi*</label>
    <div class="col-sm-8">
      <input name="vergiDairesi" class="form-control" type="text" value="{{ $bayi->vergiDairesi }}" required>
    </div>
  </div>

  <div class="row">
    <label class="col-sm-4">Telefon</label>
    <div class="col-sm-8">
      <input name="tel" class="form-control phone" type="text" value="{{ $bayi->tel }}">
    </div>
  </div>
    <div class="row">
        <div class="col-sm-4"><label>İl/İlçe</label></div>
        <div class="col-sm-4">
          <select name="il" id="countrySelect" class="form-control form-select" style="width:100%!important;">
            <option value="" selected disabled>-Seçiniz-</option>
            @foreach($countries as $item)
              <option value="{{ $item->id }}" {{ $bayi->il == $item->id ? 'selected' : ''}}>{{ $item->name}}</option>
            @endforeach
          </select>
        </div>
        <div class="col-sm-4">
          <select name="ilce" id="citySelect" class="form-control form-select" style="width:100%!important;">
            <option value="" selected disabled>-Seçiniz-</option>                              
          </select>
        </div>
      </div> 
  

    <div class="row">
      <label class="col-sm-4">Adress:</label>
      <div class="col-sm-8">
      <textarea name="address" type="text" class="form-control">{{$bayi->address}}</textarea>
      </div>
    </div>

  <div class="row">
    <label class="col-sm-4">Kullanıcı Adı*</label>
    <div class="col-sm-8">
      <input name="username" class="form-control" type="text" value="{{ $bayi->username }}" required>
    </div>
  </div>

  <div class="row">
    <label class="col-sm-4">Yeni Şifre:</label>
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
  
