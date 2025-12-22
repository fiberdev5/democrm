<style>
  
@media (min-width: 767px) {
.custom-p-r{
    padding-right: 0px !important;
  }
  .custom-p-l{
    padding-left: 0px !important;
  }

}
</style>
<form method="post" id="addCust" action="{{ route('store.customer', $firma->id)}}" enctype="multipart/form-data" >
  @csrf   
  <div class="row">
    <label class="col-sm-3 custom-p-r">Müşteri Tipi<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-9 custom-p-l">
      <select name="mTipi" class="form-select musteriTipi" required>
        <option  value="1">BİREYSEL</option>
        <option value="2">KURUMSAL </option>
      </select>
    </div>
  </div>
  
  <div class="row">
    <label class="col-sm-3 custom-p-r"><span class="musteriAdiSpan">Müşteri Adı</span><span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-9 custom-p-l">
      <input name="name" class="form-control buyukYaz" type="text" placeholder="" required>
    </div>
  </div>
  
  <div class="row">
    <label class="col-sm-3 custom-p-r">Telefon</label>
    <div class="col-sm-4 col-sm-custom col-6 custom-p-r-m-md custom-p-l">
      <input name="tel1" class="form-control phone" type="text" placeholder="Telefon 1" required>
    </div>
    <div class="col-sm-4 col-sm-custom col-6 custom-p-m-md custom-p-l">
      <input name="tel2" class="form-control phone" type="text" placeholder="Telefon 2" >
    </div>
  </div>
  <div class="row">
    <div class="col-sm-3 custom-p-r"><label>İl/İlçe</label></div>
    <div class="col-sm-4 col-sm-custom col-6 custom-p-r-m-md custom-p-l ">
      <select name="il" id="country" class="form-control form-select" style="width:100%!important;">
        <option value="" selected disabled>-Seçiniz-</option>
        @foreach($countries as $item)
          <option value="{{ $item->id }}">{{ $item->name}}</option>
        @endforeach
      </select>
    </div>
    <div class="col-sm-4 col-sm-custom col-6 custom-p-m-md custom-p-l">
      <select name="ilce" id="city" class="form-control form-select" style="width:100%!important;">
        <option value="" selected disabled>-Seçiniz-</option>                              
      </select>
    </div>
  </div> 
  <div class="row">
    <label class="col-sm-3 custom-p-r">Adress:</label>
    <div class="col-sm-9 custom-p-l">
      <textarea name="address" type="text" class="form-control" rows="2" placeholder=""></textarea>
    </div>
  </div>
  
  <div class="row" id="tcNo">
    <label class="col-sm-3 custom-p-r">T.C. No <span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-9 custom-p-l">
      <input name="tcno" id="tcKimlik" class="form-control" type="text" placeholder="">
    </div>
  </div>

  <div class="row vergi-box" id="vergiBox">
    <label class="col-sm-3 custom-p-r">Vergi No/Dairesi</label>
    <div class="col-sm-4 col-sm-custom col-6 custom-p-l">
      <input name="vergiNo" id="vergiNo" class="form-control " type="text" placeholder="Vergi No" >
    </div>
    <div class="col-sm-4 col-sm-custom col-6 custom-p-l">
      <input name="vergiDairesi" class="form-control " type="text" placeholder="Vergi Dairesi" >
    </div>
  </div>
  
  <div class="row">               
    <div class="col-sm-12 gonderBtn">
      <input type="submit" class="btn btn-sm btn-info waves-effect waves-light" value="Kaydet">
    </div>
  </div>
</form>
  
<script>
  $('.buyukYaz').keyup(function(){
    this.value = this.value.toUpperCase();
  });


  $(document).ready(function () {
    $(".phone").mask("999 999 9999");
  });

  $(document).ready(function(){
    $("#tcKimlik").mask("99999999999");
  });

  $(document).ready(function(){
    $("#vergiNo").mask("9999999999");
  });

  $('#addCust #vergiBox').hide();

  $(document).ready(function (e) {
    $('#addCust .musteriTipi').on('change', function () {
      var val = $(this).val();
      if (val == 2) {
        $("#addCust .musteriAdiSpan").text("Firma Adı");
        $('#addCust #vergiBox').show();
        $('#addCust #tcNo').hide();
      } else {
        $("#addCust .musteriAdiSpan").text("Müşteri Adı");
        $('#addCust #vergiBox').hide();
        $('#addCust #tcNo').show();
      }
    });
  });
</script>
  
<script>
  $(document).ready(function () {
    $('#addCust').submit(function (event) {
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
    $("#country").change(function() {
      var selectedCountryId = $(this).val();
      if (selectedCountryId) {
        loadCities(selectedCountryId);
      }
    });
    // Şehirleri yüklemek için kullanılan fonksiyon
    function loadCities(countryId) {
      var citySelect = $("#city");
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