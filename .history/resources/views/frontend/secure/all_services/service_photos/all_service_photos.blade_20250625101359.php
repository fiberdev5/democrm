<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="card" style="margin-bottom: 3px;margin-top:5px;">
  <div class="card-header" style="padding: 3px 5px;">
    <button type="button" class="btn btn-success btn-sm addBelge mb-2" id="openModalBtn" data-bs-id="{{$servis->id}}">Belge Ekle</button>
  </div>
  <div class="card-body notList" style="padding: 0;display: none;"></div>
</div>

@if(!empty($documents) && count($documents) > 0)
  <div class="row imgBox">
        @foreach ($servisFoto as $foto)
            <div class="col-6 col-sm-2 stn"><a href="" data-fancybox="galeriGroup"><img src="" style="width: 100%;"></a><a href="#" class="btn btn-danger btn-sm btn-block servisFotoSil" data-id="">Foto Sil</a></div>
        @endforeach
      
  </div>
@endif

<script type="text/javascript">
  $(document).ready(function () {
    $(".addBelge").click(function(){
      var id = $(this).attr("data-bs-id");
      if(id){
        $.ajax({
          url: "/"+ id
        }).done(function(data) {
          if($.trim(data)==="-1"){
            window.location.reload(true);
          }else{
            $('.notList').html(data).show();
          }
        });
      }
    });
  });
</script>

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
        alert('Lütfen dosya seçiniz.');
        return false;
      }
    });
  });
</script>

<script>
  $(document).ready(function() {
    $('#belgeTable').on('click', '.musteriBelgeSil', function(e) {
      e.preventDefault();
      var confirmDelete = confirm("Bu müşteri aşamasını silmek istediğinizden emin misiniz?");
      if (confirmDelete) {
        var id = $(this).attr('data-id');
        $.ajax({
          url: '/' + id,
          type: 'POST',
          data: {
            _method: 'DELETE', 
            _token: '{{ csrf_token() }}'
          },
          success: function(data) {
            if (data) {
              alert("Fotoğraf başarıyla silindi.");
              $('#datatableService').DataTable().ajax.reload();
              $('.nav5').trigger('click');
            } else {
              alert("Silme işlemi başarısız oldu.");
            }
          },
          error: function(xhr, status, error) {
            console.error(xhr.responseText);
          }
        });
      }
    });
  });
</script>

