<form method="post" id="addWarranty" action="{{ route('store.warranty', $firma->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf
    <div class="row mb-3">
      <label class="col-sm-3">Garanti Süresi:<span style="font-weight: bold; color: red;">*</span></label>
      <div class="col-sm-9">
        <input name="garanti" class="form-control" type="text" required>
      </div>
    </div>
    <span style="font-size: 13px;color: red;">Ay hesabına göre ekleyin. Örneğin: 6 Ay için sadece 6 yazın.</span>

    <div class="row">
      <div class="col-sm-12 gonderBtn">
        <input type="submit" class="btn btn-info btn-sm waves-effect waves-light" value="Kaydet">
      </div>
    </div>
  </form>
  
  <script>
    $(document).ready(function () {
      $('#addWarranty').submit(function (event) {
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
      $('#addWarranty').submit(function(e){
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
              alert("Garanti süresi başarıyla eklendi");
              var newRow = `<tr>
                <td><a class="t-link editWarranty" href="javascript:void(0);" data-bs-id="${response.id}" data-bs-toggle="modal" data-bs-target="#editDeviceTypeModal"><div class="mobileTitle">Garanti:</div>${response.garanti} Ay</a></td>
                <td>
                  <a href="javascript:void(0);" data-bs-id="${response.id}" class="btn btn-warning btn-sm editWarranty mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editWarrantyModal" title="Düzenle"><i class="fas fa-edit"></i> <span> Düzenle</span></a>
                  <a href="javascript:void(0);"  class="btn btn-danger btn-sm mobilBtn deleteWarranty" data-bs-id="${response.id}" title="Sil"><i class="fas fa-trash-alt"></i> <span> Sil</span></a>
                </td>
              </tr>`;
              $('#datatableWarranty tbody').prepend(newRow);
              $('#addWarrantyModal').modal('hide');
            },
            error: function(xhr, status, error) {
              console.error(xhr.responseText);
            }
          });
        }
      });
    });
  </script>