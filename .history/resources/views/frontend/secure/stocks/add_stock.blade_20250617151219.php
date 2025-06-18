<form method="post" id="addPers" action="{{ route('store.staff',$firma->id)}}" enctype="multipart/form-data" >
  @csrf   
  <div class="row">
    <label class="col-sm-4">Başlama Tarihi<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <input name="baslamaTarihi" class="form-control datepicker kayitTarihi" type="date" style="border: 1px solid #ced4da;" value="{{date('Y-m-d')}}" required>
    </div>
  </div>

  <div class="row">
    <label class="col-sm-4">Personel Adı<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <input name="name" class="form-control" type="text" required>
    </div>
  </div>

  <div class="row">
    <label class="col-sm-4">Telefon</label>
    <div class="col-sm-8">
      <input name="tel" class="form-control phone" type="text" required>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-4"><label>İl/İlçe</label></div>
    <div class="col-sm-4">
      <select name="il" id="countrySelect" class="form-control form-select" style="width:100%!important;">
        <option value="" selected disabled>-Seçiniz-</option>
        @foreach($countries as $item)
          <option value="{{ $item->id }}">{{ $item->name}}</option>
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
      <textarea name="address" type="text" class="form-control" rows="2"></textarea>
    </div>
  </div>

  <div class="row">
    <label class="col-sm-4">Personel Grubu<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <select name="roles" class="form-select" required>
        <option selected disabled value="">-Seçiniz-</option>
        @foreach($roles as $role)
          <option value="{{$role->id}}">{{$role->name}}</option>
        @endforeach
      </select>
    </div>
  </div> <!--end row-->

  <div class="row">
    <label class="col-sm-4">Kullanıcı Adı<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <input name="username" class="form-control" type="text" required>
    </div>
  </div>
            
  <div class="row mb-3">
    <label class="col-sm-4">Şifre:</label>
    <div class="col-sm-8">
      <input name="password" class="form-control" type="password" required>
    </div>
  </div>

  <div class="row">               
    <div class="col-sm-12 gonderBtn">
      <input type="submit" class="btn btn-sm btn-info waves-effect waves-light" value="Kaydet">
    </div>
  </div>
</form>

<script>
  $(document).ready(function () {
    $(".phone").mask("999 999 9999");
  });
</script>

<script>
  $(document).ready(function () {
    $('#addPers').submit(function (event) {
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
  // Ülke seçildiğinde şehirleri getir
  $("#countrySelect").change(function() {
    var selectedCountryId = $(this).val();
    if (selectedCountryId) {
      loadCities(selectedCountryId);
    }
  });

  // Şehirleri yüklemek için kullanılan fonksiyon
  function loadCities(countryId) {
    var citySelect = $("#citySelect");
    citySelect.empty(); // Önceki seçenekleri temizle
    citySelect.append(new Option("Yükleniyor...", "")); // Kullanıcıya yükleniyor bilgisi ver

    // AJAX isteğiyle şehirleri al
    $.get("/get-states/" + countryId, function(data) {
      citySelect.empty(); // Yükleniyor mesajını temizle
      citySelect.append(new Option("-Seçiniz-", "")); // İlk boş seçeneği ekle
      $.each(data, function(index, city) {
        citySelect.append(new Option(city.ilceName, city.id));
      });
    }).fail(function() {
      citySelect.empty(); // Hata durumunda temizle
      citySelect.append(new Option("Unable to load cities", ""));
    });
  }
});
</script>