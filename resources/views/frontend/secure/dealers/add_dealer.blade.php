<style>
  
@media (min-width: 767px) {
.custom-p-r{
    padding-right: 0px !important;
  }
  .custom-p-l{
    padding-left: 0px !important;
  }
}
@media (max-width: 767px) {
    #addBayi label{margin-bottom: 3px !important;}
  }
</style>
<form method="post" id="addBayi" action="{{ route('store.dealer',$firma->id)}}" enctype="multipart/form-data" >
  @csrf   
  <div class="row">
    <label class="col-sm-4 custom-p-r">Başlama Tarihi<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8 custom-p-l">
      <input name="baslamaTarihi" class="form-control datepicker kayitTarihi" type="date" style="border: 1px solid #ced4da;" value="{{date('Y-m-d')}}" required>
    </div>
  </div>

  <div class="row">
    <label class="col-sm-4 custom-p-r">Ad Soyad<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8 custom-p-l">
      <input name="name" class="form-control" type="text" required>
    </div>
  </div>

<div class="row">
  <label class="col-sm-4 custom-p-r">Vergi No/Dairesi<span style="font-weight: bold; color: red;">*</span></label>
  <div class="col-sm-4 col-6 custom-p-r-m-md custom-p-l">
    <input name="vergiNo" id="vergiNo" class="form-control" type="text" required>
  </div>
  <div class="col-sm-4 col-6 custom-p-m-md custom-p-l">
    <input name="vergiDairesi" class="form-control" type="text" required>
  </div>
</div>

  <div class="row">
    <label class="col-sm-4 custom-p-r">Bayi Belgesi<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8 custom-p-l">
      <input name="belgePdf[]" id="belgePdf" class="form-control" type="file" accept=".pdf,.jpg,.jpeg,.png,.svg" multiple required>
      <small class="text-muted">Maksimum 2 dosya seçebilirsiniz. PDF, JPG, PNG, SVG formatları kabul edilir.</small>
    </div>
  </div>

  <div class="row">
    <label class="col-sm-4 custom-p-r">Telefon<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8 custom-p-l">
      <input name="tel" id="tel" class="form-control phone" type="text" required>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-4 custom-p-r"><label>İl/İlçe<span style="font-weight: bold; color: red;">*</span></label></div>
    <div class="col-sm-4 col-6 custom-p-r-m-md custom-p-l">
      <select name="il" id="countrySelect" class="form-control form-select" style="width:100%!important;" required>
        <option value="" selected disabled>-Seçiniz-</option>
        @foreach($countries as $item)
          <option value="{{ $item->id }}">{{ $item->name}}</option>
        @endforeach
      </select>
    </div>
    <div class="col-sm-4 col-6 custom-p-m-md custom-p-l">
      <select name="ilce" id="citySelect" class="form-control form-select" style="width:100%!important;" required>
        <option value="" selected disabled>-Seçiniz-</option>                              
      </select>
    </div>
  </div> 
  <div class="row">
    <label class="col-sm-4 custom-p-r">Adress:</label>
    <div class="col-sm-8 custom-p-l">
      <textarea name="address" type="text" class="form-control" rows="2"></textarea>
    </div>
  </div>


  <div class="row">
    <label class="col-sm-4 custom-p-r">Kullanıcı Adı<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8 custom-p-l">
      <input name="username" class="form-control" type="text" required>
    </div>
  </div>
            
  <div class="row mb-3">
    <label class="col-sm-4 custom-p-r">Şifre:<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8 custom-p-l">
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
    $(document).ready(function(){
    $("#vergiNo").mask("9999999999");
  });
</script>
<script>
  // Maksimum 2 dosya kontrolü
  $(document).ready(function() {
    $('#belgePdf').on('change', function() {
      if (this.files.length > 2) {
        alert('Maksimum 2 dosya seçebilirsiniz!');
        this.value = '';
      }
    });
  });
</script>

<script>
$(document).ready(function () {
    $('#addBayi').submit(function (event) {
        var formIsValid = true;
        
        // Mevcut validation kodları...
        $(this).find('input, select').each(function () {
            var isRequired = $(this).prop('required');
            var isEmpty = !$(this).val();

            if (isRequired && isEmpty) {
                formIsValid = false;
                return false;
            }
        });

        // İsim uzunluğu kontrolü
        var name = $('input[name="name"]').val();
        if (name && name.length < 2) {
            formIsValid = false;
            alert('Bayi adı en az 2 karakter olmalıdır.');
            return false;
        }

        // Vergi numarası kontrolü
        var vergiNo = $('input[name="vergiNo"]').val();
        if (vergiNo && vergiNo.length !== 10) {
            formIsValid = false;
            alert('Vergi numarası 10 haneli olmalıdır.');
            return false;
        }

        if (!formIsValid) {
            event.preventDefault();
            if (!name) {
                alert('Lütfen zorunlu alanları doldurun.');
            }
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