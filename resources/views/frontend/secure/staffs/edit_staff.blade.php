<form method="post" id="editPers" action="{{ route('update.personel', [$firma->id, $staff->user_id]) }}" enctype="multipart/form-data">
    @csrf
    <div class="row">
      <label class="col-sm-4">Başlama Tarihi<span style="font-weight: bold; color: red;">*</span></label>
      <div class="col-sm-8">
        <input name="baslamaTarihi" class="form-control datepicker kayitTarihi" type="date" value="{{$staff->baslamaTarihi}}" style="border: 1px solid #ced4da;" required>
      </div>
    </div>
  
    <div class="row">
      <label class="col-sm-4">Personel Durumu: </label>
      <div class="col-sm-8">
        <select name="status" class="form-select durum" required>
          <option value="1" {{ $staff->status == "1" ? 'selected' : ''}}>Çalışıyor</option>
          <option value="0" {{ $staff->status == "0" ? 'selected' : ''}}>Ayrıldı</option>
        </select>
      </div>
    </div> <!--end row-->
  
    <div class="row ayrilmaTarihi">
      <label class="col-sm-4">Ayrılma Tarihi:</label>
      <div class="col-sm-8">
          <input name="ayrilmaTarihi" class="form-control datepicker ayrilmaTarihi" type="date" value="{{$staff->ayrilmaTarihi}}" style="border: 1px solid #ced4da;">
      </div>
    </div>
  
    <div class="row">
      <label class="col-sm-4">Personel Adı<span style="font-weight: bold; color: red;">*</span></label>
      <div class="col-sm-8">
        <input name="name" class="form-control" value="{{$staff->name}}" type="text" required>
      </div>
    </div>
  
    <div class="row">
      <label class="col-sm-4">Telefon:</label>
      <div class="col-sm-8">
        <input name="tel" class="form-control phone" value="{{$staff->tel}}" type="text" required>
      </div>
    </div>

    <div class="row">
        <div class="col-sm-4"><label>İl/İlçe</label></div>
        <div class="col-sm-4">
          <select name="il" id="countrySelect" class="form-control form-select" style="width:100%!important;">
            <option value="" selected disabled>-Seçiniz-</option>
            @foreach($countries as $item)
              <option value="{{ $item->id }}" {{ $staff->il == $item->id ? 'selected' : ''}}>{{ $item->name}}</option>
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
      <textarea name="address" type="text" class="form-control" rows="2">{{$staff->address}}</textarea>
      </div>
    </div>
  
    <div class="row">
      <label class="col-sm-4">Personel Grubu<span style="font-weight: bold; color: red;">*</span></label>
      <div class="col-sm-8">
        <select name="roles" class="form-select" required>
          <option selected value="">-Seçiniz-</option>
          @foreach($roles as $role)
            <option value="{{$role->id}}" {{ $staff->hasRole($role->name) ? 'selected' : ''}}>{{$role->name}}</option>
          @endforeach
        </select>
      </div>
    </div> <!--end row-->
  
    <div class="row">
      <label class="col-sm-4">Kullanıcı Adı<span style="font-weight: bold; color: red;">*</span></label>
      <div class="col-sm-8">
        <input name="username" class="form-control" value="{{$staff->username}}" type="text" required>
      </div>
    </div>
              
    <div class="row mb-3">
      <label class="col-sm-4">Şifre</label>
      <div class="col-sm-8">
        <input name="password" class="form-control" type="password" placeholder="**********">
      </div>
    </div>
  
    <div class="row">
      <div class="col-sm-12 gonderBtn">
        <input type="hidden" name="id" value="{{$staff->user_id}}">
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
    if(getDurum==1){
      $(".ayrilmaTarihi").hide();
    }else if(getDurum==0){
      $(".ayrilmaTarihi").show();
    }
  
    $(".durum").change(function(){
      var getDurum = $(".durum").val();
      console.log(getDurum);
      if(getDurum==1){
        $(".ayrilmaTarihi").hide();
      }else if(getDurum==0){
        $(".ayrilmaTarihi").show();
      }
    });
  
  </script>
  
  <script>
    $(document).ready(function () {
      $('#editPers').submit(function (event) {
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
      var selectedCountryId ={{ $staff->il == '' ? '0' : $staff->il}} ;
      if(selectedCountryId){
        $.get("/get-states/" + selectedCountryId, function(data) {
          $.each(data, function(index, city) {
            citySelect.append(new Option(city.ilceName, city.id));
            if(city.id == {{ $staff->ilce == '' ? '0' : $staff->ilce}}){
              $("#citySelect").val(city.id).change();
            } 
          });
        });
      }
      // Ülke seçildiğinde
      $("#countrySelect").change(function() {
        var selectedCountryId = $(this).val();
        // Şehirleri getir ve ikinci select'i güncelle
        $.get("/get-states/" + selectedCountryId, function(data) {
          var citySelect = $("#citySelect");
          citySelect.empty(); // Önceki seçenekleri temizle
          $.each(data, function(index, city) {         // 'each()' fonksiyonuyla dolaşılıp ikinci selecte eklenir.
            citySelect.append(new Option(city.ilceName, city.id));
          });
        });
      });
    });
  </script>
  
  <script>
    $(document).ready(function (e) {
    $("#editPers").submit(function (event) {
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
            alert("Personel bilgileri güncellendi");
            $('#datatablePersonel').DataTable().ajax.reload();
            $('#editPersonelModal').modal('hide');
            
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
  