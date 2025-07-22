<form method="post" id="addCat" action="{{ route('store.device', $firma->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
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
      $('#addCat').submit(function (event) {
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
      $('#addCat').submit(function(e){
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
              var newRow = `<tr>
                <td class="gizli"><a class="t-link editDevice idWrap" href="javascript:void(0);" data-bs-id="${response.id}" data-bs-toggle="modal" data-bs-target="#editDeviceModal">${response.id}</a></td>
                <td><a class="t-link editDevice" href="javascript:void(0);" data-bs-id="${response.id}" data-bs-toggle="modal" data-bs-target="#editDeviceModal"><div class="mobileTitle">Marka:</div>${response.marka}</a></td>
                <td><a class="t-link editDevice" href="javascript:void(0);" data-bs-id="${response.id}" data-bs-toggle="modal" data-bs-target="#editDeviceModal"><div class="mobileTitle">Telefon:</div>${response.aciklama}</a></td>
                <td><a class="t-link editDevice" href="javascript:void(0);" data-bs-id="${response.id}" data-bs-toggle="modal" data-bs-target="#editDeviceModal"><div class="mobileTitle">S.Ü.:</div>${response.servisUcreti}</a></td>
                <td><a class="t-link editDevice" href="javascript:void(0);" data-bs-id="${response.id}" data-bs-toggle="modal" data-bs-target="#editDeviceModal"><div class="mobileTitle">Opt Prim:</div>${response.operatorPrim}</a></td>
                <td><a class="t-link editDevice" href="javascript:void(0);" data-bs-id="${response.id}" data-bs-toggle="modal" data-bs-target="#editDeviceModal"><div class="mobileTitle">Atolye Prim:</div>${response.atolyePrim}</a></td>
                <td>
                  <a href="javascript:void(0);" data-bs-id="${response.id}" class="btn btn-warning btn-sm editDevice mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editDeviceModal" title="Düzenle"><i class="fas fa-edit"></i> <span> Düzenle</span></a>
                  <a href="javascript:void(0);"  class="btn btn-danger btn-sm mobilBtn deleteDevice" data-bs-id="${response.id}" title="Sil"><i class="fas fa-trash-alt"></i> <span> Sil</span></a>
                </td>
              </tr>`;
              $('#datatableDeviceBrand tbody').prepend(newRow);
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