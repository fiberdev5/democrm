<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<meta name="csrf-token" content="{{ csrf_token() }}">


<form method="post" id="servisOptNotuEkle" action="{{ route('store.service.opt.note', $firma->id) }}" class="col-sm-6" style="margin: 0 auto;padding:10px;">
  @csrf

  <div class="row form-group">
    <div class="col-lg-12 rw2">
      <textarea type="text" name="aciklama" class="form-control aciklama" placeholder="Buraya yazın.." rows="3" style="resize: none;" autocomplete="off" required></textarea>
    </div>
  </div>

  <div style="text-align: center;margin-top: 5px;">
    <input type="hidden" name="servisid" class="servisid" value="{{$servis->id}}"/>
    <input type="submit" class="btn btn-primary btn-sm" value="Gönder"/>
  </div>
    
</form>

<script>
  $(document).ready(function () {
    $('#servisOptNotuEkle').submit(function (event) {
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
    $("#servisOptNotuEkle").submit(function (event) {
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
          if (data.success) { 
            alert("Servis operatör notu başarıyla eklendi.");
            $('#datatableService').DataTable().ajax.reload();
            loadServiceHistory({{ $servis->id }});
            $('.nav8').trigger('click');   
            
          } else {
              alert("Kayıt yapılamadı.");
              window.location.reload(true);
          }
        },
        error: function (xhr, status, error) {
          alert("Güncelleme başarısız!");
          
        },
      });
    }
    });
  });
</script>