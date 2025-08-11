<form method="post" id="musteriDuz" class="editCust" action="{{ route('update.customer', [$firma->id, $customer->id]) }}" enctype="multipart/form-data">
  @csrf
  <div class="row">
    <label class="col-sm-4">Kayıt Tarihi<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <input name="kayitTarihi" class="form-control datepicker kayitTarihi" type="date" value="{{\Carbon\Carbon::parse($customer->created_at)->format('Y-m-d')}}" style="border: 1px solid #ced4da;" required>
    </div>
  </div>
  
  <div class="row">
    <label class="col-sm-4">Müşteri Tipi: </label>
    <div class="col-sm-8">
      <select name="mTipi" class="form-select musteriTipi" required>
        <option value="1" {{ $customer->musteriTipi == "1" ? 'selected' : ''}}>BİREYSEL</option>
        <option value="2" {{ $customer->musteriTipi == "2" ? 'selected' : ''}}>KURUMSAL</option>
      </select>
    </div>
  </div> <!--end row-->
  
  <div class="row">
    <label class="col-sm-4"><span class="musteriAdiSpan">Müşteri Adı</span><span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <input name="name" class="form-control buyukYaz" type="text" placeholder="Müşteri Adı" value="{{$customer->adSoyad}}" required>
    </div>
  </div>
  
  <div class="row">
    <label class="col-sm-4">Telefon:</label>
    <div class="col-sm-4">
      <input name="tel1" class="form-control phone" value="{{$customer->tel1}}" type="text" required>
    </div>
    <div class="col-sm-4">
      <input name="tel2" class="form-control phone" value="{{$customer->tel2}}" type="text">
    </div>
  </div>

  <div class="row">
    <div class="col-sm-4"><label>İl/İlçe</label></div>
    <div class="col-sm-4">
      <select name="il" id="sehirSelect" class="form-control form-select" style="width:100%!important;">
        <option value="" selected disabled>-Seçiniz-</option>
        @foreach($countries as $item)
          <option value="{{ $item->id }}" {{ $customer->il == $item->id ? 'selected' : ''}}>{{ $item->name}}</option>
        @endforeach
      </select>
    </div>
    <div class="col-sm-4">
      <select name="ilce" id="ilceSelect" class="form-control form-select" style="width:100%!important;">
        <option value="" selected disabled>-Seçiniz-</option>                              
      </select>
    </div>
  </div> 
  
  <div class="row">
    <label class="col-sm-4">Adress:</label>
    <div class="col-sm-8">
      <textarea name="address" type="text" class="form-control" rows="2">{{$customer->adres}}</textarea>
    </div>
  </div>
    
    
  <div class="row" id="tcNo">
    <label class="col-sm-4">T.C. No <span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <input name="tcno" id="tcKimlik" class="form-control" type="text" placeholder="T.C No" value="{{$customer->tcNo}}">
    </div>
  </div>
    
  <div class="row vergi-box" id="vergiBox">
    <label class="col-sm-4">Vergi No/Dairesi</label>
    <div class="col-sm-4">
      <input name="vergiNo" id="vergiNo" class="form-control " type="text" placeholder="Vergi No" value="{{$customer->vergiNo}}">
    </div>
    <div class="col-sm-4">
      <input name="vergiDairesi" class="form-control " type="text" placeholder="Vergi Dairesi" value="{{$customer->vergiDairesi}}">
    </div>
  </div>
   
  <div class="row">
    <div class="col-sm-12 gonderBtn">
      <input type="hidden" name="id" value="{{$customer->id}}">
      <input type="submit" class="btn btn-sm btn-info waves-effect waves-light" value="Kaydet">
    </div>
  </div>
</form>

<script>
  $('.buyukYaz').keyup(function(){
    this.value = this.value.toUpperCase();
  });

  $(document).ready(function () {
    $(".phone").mask("9999 999 9999");
  });

  $(document).ready(function(){
      $("#tcNo").mask("99999999999");
  });

  $(document).ready(function(){
    $("#vergiNo").mask("99999999999");
  });
</script>

<script>
  $(document).ready(function() {
    var selectedCountryId ={{ $customer->il == '' ? '0' : $customer->il}} ;
    if(selectedCountryId){
      $.get("/get-states/" + selectedCountryId, function(data) {
        $.each(data, function(index, city) {
          ilceSelect.append(new Option(city.ilceName, city.id));
          if(city.id == {{ $customer->ilce == '' ? '0' : $customer->ilce}}){
            $("#ilceSelect").val(city.id).change();
          } 
        });
      });
    }
    // Ülke seçildiğinde
    $("#sehirSelect").change(function() {
      var selectedCountryId = $(this).val();
      // Şehirleri getir ve ikinci select'i güncelle
      $.get("/get-states/" + selectedCountryId, function(data) {
        var citySelect = $("#ilceSelect");
        citySelect.empty(); // Önceki seçenekleri temizle
        $.each(data, function(index, city) {         // 'each()' fonksiyonuyla dolaşılıp ikinci selecte eklenir.
          citySelect.append(new Option(city.ilceName, city.id));
        });
      });
    });
  });
</script>

<script>
  $(document).ready(function () {
    $('#musteriDuz').submit(function (event) {
      var formIsValid = true;
      // Boş alan kontrolü
      $(this).find('input[name="m_adi"], input[name="telefon"]').each(function () {
        var inputField = $(this);
        if (inputField.prop('required') && !inputField.val()) {
          formIsValid = false;
          return false; // Döngüyü sonlandır
        }
      });
      if (!formIsValid) {
        // Boş alan varsa formu gönderme
        event.preventDefault();
        alert('Lütfen Müşteri Adı, Telefon, Ülke, İl/İlçe ve Adres alanlarını doldurun.');
        return false;
      }
    });
  });
</script>

<script>
  $(document).ready(function () {
    $('#musteriDuz').submit(function (event) {
      var formIsValid = true;
      $(this).find('textarea, select').each(function () {
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
    $('#musteriDuz').submit(function (event) {
      event.preventDefault();
      var formData = new FormData(this);
      $.ajax({
        url: $(this).attr("action"),
        type: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
          if (data.customer) {
            var musteriTipiText = '';
            if (data.customer.musteriTipi == 1) {
              musteriTipiText = 'Bireysel';
            } else if (data.customer.musteriTipi == 2) {
              musteriTipiText = 'Kurumsal';
            }

            var tel1 = data.customer.tel1 || '';
var tel2 = data.customer.tel2 || '';

var telHtml = '';

if (tel1) {
  telHtml += '<a href="tel:' + tel1 + '" style="color:red;">' + tel1 + '</a>';
}
if (tel2) {
  telHtml += ' - <a href="tel:' + tel2 + '" style="color:red;">' + tel2 + '</a>';
}

            // Ana modaldaki müşteri bilgilerini güncelle
            $('#musBilCek strong').text(data.customer.adSoyad + ' ( ' + musteriTipiText + ' )');
    $('#tele').html(telHtml);          
$('#maps').text(data.customer.adres);
            $('#vergi').text((data.customer.vergiNo || '') + ' / ' + (data.customer.vergiDairesi || ''));
  
            $('#editServiceCustomerModal').modal('hide');
            $('#datatableService').DataTable().ajax.reload();
            $('.nav1').trigger('click');
          } else {
            alert(data.message);
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