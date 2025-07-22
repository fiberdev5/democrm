<form method="post" id="addCall" action="{{ route('store.call', $firma->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf
    <div class="row">
        <label class="col-sm-4">Servis Kaynağı<span style="font-weight: bold; color: red;">*</span></label>
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
        <label class="col-sm-4">Marka<span style="font-weight: bold; color: red;">*</span></label>
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
              
              $('#addDeviceModal').modal('hide');
            },
            error: function(xhr, status, error) {
              console.error(xhr.responseText);
            }
          });
        }
      });
    });
  </script>