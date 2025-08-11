<form method="post" id="editStage" action="{{ route('update.service.stage', $firma->id) }}" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf
    <div class="row mb-3">
      <label class="col-sm-3">Aşama:</label>
      <div class="col-sm-9">
        <input name="asama" class="form-control" value="{{ $stage_id->asama }}" type="text" required>
      </div>
    </div>

    <div class="row mb-3">
      <label class="col-sm-4">Alt Aşamalar:<span style="font-weight: bold; color: red;">*</span></label>
      <div class="col-sm-8">
          @foreach($stages as $stage)
          <div class="d-flex align-items-center ">
              <input type="checkbox" id="altAsama{{$stage->id}}" @if(in_array($stage->id, $selectedAltAsamalar)) checked @endif name="altAsamalar[]" value="{{$stage->id}}" class="form-check-input me-2">
              <label for="altAsama{{$stage->id}}" class="form-check-label w-100 text-truncate">
                  {{ $stage->asama }}
              </label>
          </div>
          @endforeach
      </div>
  </div>
  
    <div class="row">
      <div class="col-sm-12 gonderBtn">
        <input type="hidden" name="id" value="{{ $stage_id->id }}">
        <input type="submit" class="btn btn-info btn-sm waves-effect waves-light" value="Kaydet">
      </div>
    </div>
  </form>
  
  <script>
    $(document).ready(function () {
      $('#editStage').submit(function (event) {
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
      $('#editStage').submit(function(e){
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
              alert("Servis aşaması başarıyla güncellendi");
              var rowToUpdate = $('#datatableServiceStage tbody tr[data-id="' + response.id + '"]');
              rowToUpdate.find('td:eq(1)').html(`<a class="t-link editServiceStage" href="javascript:void(0);" data-bs-id="${response.id}" data-bs-toggle="modal" data-bs-target="#editServiceStageModal"><div class="mobileTitle">Aşama:</div>${response.asama}</a>`);    
              $('#editServiceStageModal').modal('hide');
            },
            error: function(xhr, status, error) {
              console.error(xhr.responseText);
            }
          });
        }
      });
    });
</script>