<link href="{{ asset('frontend/css/staff/add_staff.css') }}" rel="stylesheet" type="text/css" />
<form method="post" id="addPers" action="{{ route('store.staff',$firma->id)}}" enctype="multipart/form-data" >
  @csrf   
  <input type="hidden" name="form_token" id="formToken" value="">
  <div class="row">
    <label class="col-sm-4 custom-p-r">Başlama Tarihi<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8 custom-p-l">
      <input name="baslamaTarihi" class="form-control datepicker kayitTarihi" type="date" style="border: 1px solid #ced4da;" value="{{date('Y-m-d')}}" required>
    </div>
  </div>

  <div class="row">
    <label class="col-sm-4 custom-p-r">Personel Adı<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8 custom-p-l">
      <input name="name" class="form-control" type="text" required>
    </div>
  </div>

  <div class="row">
    <label class="col-sm-4 custom-p-r">Telefon<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8 custom-p-l">
      <input name="tel" class="form-control phone" type="text" required>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-4 custom-p-r"><label>İl/İlçe</label></div>
    <div class="col-sm-4 col-6 custom-p-r-m-md custom-p-l">
      <select name="il" id="countrySelect" class="form-control form-select" style="width:100%!important;">
        <option value="" selected disabled>-Seçiniz-</option>
        @foreach($countries as $item)
          <option value="{{ $item->id }}">{{ $item->name}}</option>
        @endforeach
      </select>
    </div>
    <div class="col-sm-4 col-6 custom-p-m-md custom-p-l">
      <select name="ilce" id="citySelect" class="form-control form-select" style="width:100%!important;">
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
    <label class="col-sm-4 custom-p-r">Personel Grubu<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8 custom-p-l">
      <select name="roles" class="form-select" required>
        <option selected disabled value="">-Seçiniz-</option>
        @foreach($roles as $role)
          <option value="{{$role->id}}">{{$role->name}}</option>
        @endforeach
      </select>
    </div>
  </div> <!--end row-->

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
$(document).ready(function () {
    $('#addPers').submit(function (event) {
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
            alert('Personel adı en az 2 karakter olmalıdır.');
            return false;
        }

        // Kullanıcı adı uzunluğu kontrolü
        var username = $('input[name="username"]').val();
        if (username && username.length < 3) {
            formIsValid = false;
            alert('Kullanıcı adı en az 3 karakter olmalıdır.');
            return false;
        }

        if (!formIsValid) {
            event.preventDefault();
            if (!name || !username) {
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
<script>
$(document).ready(function() {
    let formSubmitting = false;
    
    // Benzersiz token oluştur
    function generateToken() {
        return Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    // Sayfa yüklendiğinde ilk token'ı oluştur
    $('#formToken').val(generateToken());
    
    // Form submit
    $('#addPers').submit(function(event) {
        // Token kontrolü
        if (formSubmitting) {
            event.preventDefault();
            return false;
        }
        
        // Mevcut validasyon
        var formIsValid = true;
        
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
            alert('Personel adı en az 2 karakter olmalıdır.');
            return false;
        }

        // Kullanıcı adı uzunluğu kontrolü
        var username = $('input[name="username"]').val();
        if (username && username.length < 3) {
            formIsValid = false;
            alert('Kullanıcı adı en az 3 karakter olmalıdır.');
            return false;
        }

        if (!formIsValid) {
            event.preventDefault();
            if (!name || !username) {
                alert('Lütfen zorunlu alanları doldurun.');
            }
            return false;
        }
        
        // Butonu disable et
        formSubmitting = true;
        $(this).find('input[type="submit"]').prop('disabled', true);
        
        // 3 saniye sonra yeniden aktif et
        setTimeout(function() {
            $('#formToken').val(generateToken());
            formSubmitting = false;
            $('#addPers input[type="submit"]').prop('disabled', false);
        }, 3000);
        
        return true;
    });
});
</script>
<script>
$(document).ready(function() {
    let isSubmitting = false;
    let shouldReload = false;
    
    // Form submit edildiğinde flag'i ayarla
    $('#addPers').submit(function() {
        isSubmitting = true;
    });
    
    // Modal kapatılmaya çalışıldığında
    $('#addPersonelModal').on('hide.bs.modal', function(e) {
        if (isSubmitting) {
            isSubmitting = false;
            return true;
        }
        
        // Her zaman onay iste
        if (!confirm('Kapatmak istediğinizden emin misiniz?')) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        
        shouldReload = true; // Tamam'a basıldı, yenile
        isSubmitting = false;
    });
    
    // Modal tamamen kapandığında sayfayı yenile
    $('#addPersonelModal').on('hidden.bs.modal', function() {
        isSubmitting = false;
        if (shouldReload) {
            shouldReload = false;
            location.reload(); // Sayfayı yenile
        }
    });
});
</script>