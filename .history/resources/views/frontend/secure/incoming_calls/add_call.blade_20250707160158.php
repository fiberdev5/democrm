<form method="post" id="addCall" action="{{ route('store.call', $firma->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf
    <div class="row">
        <label class="col-sm-4">Servis Kaynağı<span style="font-weight: bold; color: red;">*</span></label>
        <div class="col-sm-8">
        <select name="serviceResource" class="form-select" required>
            <option selected disabled value="">-Seçiniz-</option>
            @foreach($service_resources as $resource)
            <option value="{{$resource->id}}">{{$resource->kaynak}}</option>
            @endforeach
        </select>
        </div>
    </div> <!--end row-->

    <div class="row">
        <label class="col-sm-4">Marka<span style="font-weight: bold; color: red;">*</span></label>
        <div class="col-sm-8">
        <select name="deviceBrand" class="form-select" required>
            <option selected disabled value="">-Seçiniz-</option>
            @foreach($device_brands as $brand)
            <option value="{{$brand->id}}">{{$brand->marka}}</option>
            @endforeach
        </select>
        </div>
    </div> <!--end row-->

    <div class="row">
      <div class="col-md-4 rw1"><label style="text-align: left;width: auto;display: inline-block;margin: 0;">Yetkisi Servis Tel </label></div>
      <div class="col-md-8">
        <input type="text" class="form-control markaTelefon" disabled>
      </div>
    </div>

    <div class="row form-group ">
      <div class="col-md-4 rw1"><label style="text-align: left;width: auto;display: inline-block;margin: 0;">Açıklama <span style="font-weight: bold; color: red;">*</span></label></div>
      <div class="col-md-8">
        <input id="arizaSearch" type="text" name="cihazAriza"  class="form-control cihazAriza" autocomplete="off">
        <ul id="arizaResult" style="margin: 0;padding: 0"></ul>
      </div>
   </div>

    <div class="row">
      <div class="col-sm-12 gonderBtn">
        <input type="submit" class="btn btn-info btn-sm waves-effect waves-light" value="Kaydet">
      </div>
    </div>
  </form>
  
  <script>
    $(document).ready(function () {
      $('#addCall').submit(function (event) {
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
  // Tüm AJAX isteklerinde CSRF token otomatik gönderilsin
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  // Marka değiştiğinde telefon getir
  $(document).on('change', 'select[name="deviceBrand"]', function () {
    var brandId = $(this).val();

    if (!brandId) {
      $('.markaTelefon').val('');
      return;
    }

    $.ajax({
      url: '{{ route("get.brand.phone") }}',
      type: 'POST',
      data: { brand_id: brandId },
      success: function (res) {
        $('.markaTelefon').val(res.phone ?? '');
      },
      error: function (xhr) {
        console.error("Telefon getirilemedi:", xhr.responseText);
        $('.markaTelefon').val('');
      }
    });
  });
</script>
  
  <script>
    $(document).ready(function(){
      $('#addCall').submit(function(e){
        e.preventDefault();
        if (this.checkValidity() === false) {
          e.stopPropagation();
        } else {
          var formData = $(this).serialize();
          $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            success: function(response) {
              alert("Cihaz markası başarıyla eklendi");
              
              $('#gelenCagriModal').modal('hide');
            },
            error: function(xhr, status, error) {
              console.error(xhr.responseText);
            }
          });
        }
      });
    });
  </script>