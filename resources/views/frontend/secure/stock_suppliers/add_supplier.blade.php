<form method="post" id="addStokSupplier" action="{{ route('store.stock.supplier', $firma->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf
    <div class="row mb-3">
      <label class="col-sm-3">Tedarikçi Adı :<span style="font-weight: bold; color: red;">*</span></label>
      <div class="col-sm-9">
        <input name="tedarikci" class="form-control" type="text" required>
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
      $('#addStokSupplier').submit(function (event) {
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
      $('#addStokSupplier').submit(function(e){
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
              alert("Stok tedarikçisi başarıyla eklendi");
              var newRow = `<tr>
                <td><a class="t-link editStockSupplier" href="javascript:void(0);" data-bs-id="${response.id}" data-bs-toggle="modal" data-bs-target="#editStockSupplierModal"><div class="mobileTitle">Tedarikçi:</div>${response.tedarikci} </a></td>
                <td>
                  <a href="javascript:void(0);" data-bs-id="${response.id}" class="btn btn-warning btn-sm editStockSupplier mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editStockSupplierModal" title="Düzenle"><i class="fas fa-edit"></i> <span> Düzenle</span></a>
                  <a href="javascript:void(0);"  class="btn btn-danger btn-sm mobilBtn deleteStockSupplier" data-bs-id="${response.id}" title="Sil"><i class="fas fa-trash-alt"></i> <span> Sil</span></a>
                </td>
              </tr>`;
              $('#datatableStockSupplier tbody').prepend(newRow);
              $('#addStockSupplierModal').modal('hide');
            },
            error: function(xhr, status, error) {
              console.error(xhr.responseText);
            }
          });
        }
      });
    });
</script>