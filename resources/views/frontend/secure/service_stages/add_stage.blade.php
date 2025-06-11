<form method="post" id="addStage" action="{{ route('store.service.stage', $firma->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf
    <div class="row mb-3">
      <label class="col-sm-4">Aşama:<span style="font-weight: bold; color: red;">*</span></label>
      <div class="col-sm-8">
        <input name="asama" class="form-control" type="text" required>
      </div>
    </div>

    <div class="row mb-3">
        <label class="col-sm-4">Alt Aşamalar:<span style="font-weight: bold; color: red;">*</span></label>
        <div class="col-sm-8">
            @foreach($stages as $stage)
            <div class="d-flex align-items-center ">
                <input type="checkbox" id="altAsama{{$stage->id}}" name="altAsamalar[]" value="{{$stage->id}}" class="form-check-input me-2">
                <label for="altAsama{{$stage->id}}" class="form-check-label w-100 text-truncate">
                    {{ $stage->asama }}
                </label>
            </div>
            @endforeach
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
      $('#addStage').submit(function (event) {
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
      $('#addStage').submit(function(e){
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
              alert("Servis aşaması başarıyla eklendi");
              var newRow = `<tr>
                <td class="gizli"><a class="t-link editServiceStage idWrap" href="javascript:void(0);" data-bs-id="${response.id}" data-bs-toggle="modal" data-bs-target="#editServiceStageModal">${response.id}</a></td>
                <td><a class="t-link editServiceStage" href="javascript:void(0);" data-bs-id="${response.id}" data-bs-toggle="modal" data-bs-target="#editServiceStageModal"><div class="mobileTitle">Aşama:</div>${response.asama}</a></td>
                <td>
                  <a href="javascript:void(0);" data-bs-id="${response.id}" class="btn btn-warning btn-sm editServiceStage mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editServiceStageModal" title="Düzenle"><i class="fas fa-edit"></i> <span> Düzenle</span></a>
                  <a href="javascript:void(0);"  class="btn btn-danger btn-sm mobilBtn deleteServiceStage" data-bs-id="${response.id}" title="Sil"><i class="fas fa-trash-alt"></i> <span> Sil</span></a>
                </td>
              </tr>`;
              $('#datatableServiceStage tbody').prepend(newRow);
              $('#addServiceStageModal').modal('hide');
            },
            error: function(xhr, status, error) {
              console.error(xhr.responseText);
            }
          });
        }
      });
    });
  </script>