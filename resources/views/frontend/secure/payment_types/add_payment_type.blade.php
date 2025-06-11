<form method="post" id="addPaymentType" action="{{ route('store.payment.type', $firma->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf
    <div class="row mb-3">
      <label class="col-sm-4">Ödeme Türü :<span style="font-weight: bold; color: red;">*</span></label>
      <div class="col-sm-8">
        <input name="odemeTuru" class="form-control" type="text" required>
      </div>
    </div>

    <div class="row mb-3">
      <label class="col-sm-4">Sorulacak Sorular :<span style="font-weight: bold; color: red;">*</span></label>
      <div class="col-sm-8">
        <label style="display: block;width: 100%;"><input value="1" type="checkbox" name="cevaplar[]"> Açıklama Sor</label>
        <label style="display: block;width: 100%;"><input value="2" type="checkbox" name="cevaplar[]"> Personel Sor</label>
        <label style="display: block;width: 100%;"><input value="3" type="checkbox" name="cevaplar[]"> Servis No Sor</label>
        <label style="display: block;width: 100%;"><input value="4" type="checkbox" name="cevaplar[]"> Tedarikçi Sor</label>
        <label style="display: block;width: 100%;"><input value="5" type="checkbox" name="cevaplar[]"> Cihaz Marka Sor</label>
        <label style="display: block;width: 100%;"><input value="6" type="checkbox" name="cevaplar[]"> Cihaz Türü Sor</label>      
      </div>
    </div>

    <div class="row mb-3">
      <label class="col-sm-4">Diğer İşlemler :<span style="font-weight: bold; color: red;">*</span></label>
      <div class="col-sm-8">
        <label style="display: block;width: 100%;"><input value="1" type="checkbox" name="stokSor" > Stok işlemlerinde kullanılsın mı? </label>
        <label style="display: block;width: 100%;"><input value="1" type="checkbox" name="servisSor" > Servis para işlemlerinde kullanılsın mı? </label>
        <label style="display: block;width: 100%;"><input value="1" type="checkbox" name="parcaSor"> Servis parça işlemlerinde kullanılsın mı? </label>
        <label style="display: block;width: 100%;"><input value="1" type="checkbox" name="personelSor"> Personel kasalarında kullanılsın mı? </label>
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
      $('#addPaymentType').submit(function (event) {
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
      $('#addPaymentType').submit(function(e){
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
              alert("Ödeme türü başarıyla eklendi");
              var newRow = `<tr>
                <td><a class="t-link editPaymentType" href="javascript:void(0);" data-bs-id="${response.id}" data-bs-toggle="modal" data-bs-target="#editPaymentTypeModal"><div class="mobileTitle">Ö. Türü:</div>${response.odemeTuru} </a></td>
                <td>
                  <a href="javascript:void(0);" data-bs-id="${response.id}" class="btn btn-warning btn-sm editPaymentType mobilBtn mbuton1" data-bs-toggle="modal" data-bs-target="#editPaymentTypeModal" title="Düzenle"><i class="fas fa-edit"></i> <span> Düzenle</span></a>
                  <a href="javascript:void(0);"  class="btn btn-danger btn-sm mobilBtn deletePaymentType" data-bs-id="${response.id}" title="Sil"><i class="fas fa-trash-alt"></i> <span> Sil</span></a>
                </td>
              </tr>`;
              $('#datatablePaymentType tbody').prepend(newRow);
              $('#addPaymentTypeModal').modal('hide');
            },
            error: function(xhr, status, error) {
              console.error(xhr.responseText);
            }
          });
        }
      });
    });
</script>