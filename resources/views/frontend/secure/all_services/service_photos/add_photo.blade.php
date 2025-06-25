<form method="post" id="addDocu" action="{{ route('store.documents')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
  @csrf
  <div class="row">
    <label class="col-sm-4">Belge Adı<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <input name="baslik" class="form-control" type="text" placeholder="Belge Adı" required>
    </div>
  </div>
  <div class="row">
    <label class="col-sm-4">Belge<span style="font-weight: bold; color: red;">*</span></label>
    <div class="col-sm-8">
      <input name="belge" class="form-control" type="file" required>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-12 gonderBtn">
      <input type="hidden" name="id" value="{{$customerId}}">
      <input type="submit" class="btn btn-sm btn-info waves-effect waves-light" value="Kaydet">
    </div>
  </div>
</form>

<script>
  $(document).ready(function () {
    $('#addDocu').submit(function (event) {
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
  $(document).ready(function (e) {
    $("#addDocu").submit(function (event) {
      event.preventDefault();
      if (this.checkValidity() === false) {
        e.stopPropagation();
      } else {
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
            alert("Belge başarıyla yüklendi");
            $('#datatableCustomer').DataTable().ajax.reload();
            $('.nav8').trigger('click');       
          }
        },
        error: function (xhr, status, error) {
          alert("Güncelleme başarısız!");
          window.location.reload(true);
        },
      });
    }
    });
  });
</script>