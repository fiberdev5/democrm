<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('vendor/fancybox/fancybox.min.css') }}">
<script src="{{ asset('vendor/fancybox/fancybox.min.js') }}"></script>

<form id="servisFotoEkle" enctype="multipart/form-data">
	  <span class="imgLoad" style="font-size: 14px;display: none">Yükleniyor. Lütfen Bekleyin.. <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 80%;height: 15px;border-radius: 10px;display: inline-block;position: relative;top: 2px;"></div></span>

	  <div class="custom-file">
	    <input name="belge" class="form-control resimInput" id="resimInput" type="file" required>
	    <span style="font-size: 12px;color: red;line-height: initial;display: block;margin-top: 5px;padding-left: 1px;">Dosya boyutu 5mb'dan büyük olamaz. Sadece jpg ve png uzantılı dosyalar yükleyebilirsiniz.</span>
	  </div>
	  <input type="hidden" name="servisFotoEkle" value="Ekle">
	  <input type="hidden" name="servisid" value="">
	  <input type="submit" value="" style="display: none;">
	</form>

@if(!empty($photos) && count($photos) > 0)
  <div class="row imgBox" id="imgBox">
        @foreach ($photos as $foto)
            <div class="col-6 col-sm-2 stn"><a href="{{asset($foto->resimyol)}}" data-fancybox="galeriGroup"><img src="{{asset($foto->resimyol)}}" style="width: 100%;"></a><a href="#" class="btn btn-danger btn-sm btn-block servisFotoSil" data-id="">Foto Sil</a></div>
        @endforeach
      
  </div>
@endif

<script>
  $(function () {
      // Fancybox init
      $('[data-fancybox="gallery"]').fancybox();

      // Otomatik yükleme
      $('#resimInput').on('change', function () {
          let formData = new FormData($('#servisFotoEkle')[0]);
          $.ajax({
              url: @json(route('store.service.photo', $firma->id)),
              method: 'POST',
              data: formData,
              processData: false,
              contentType: false,
              beforeSend()  { /* loader göstermek isterseniz */ },
              success(resp) { $('#imgBox').prepend(resp.html); },
              error(xhr)    { alert(xhr.responseJSON.message ?? 'Hata'); }
          });
      });

      // Silme
      $(document).on('click','.js-delete-photo', function (e) {
          e.preventDefault();
          if (!confirm('Silinsin mi?')) return;
          const $btn = $(this);
          $.ajax({
              url: $btn.data('href'),
              method: 'DELETE',
              data: { _token: @json(csrf_token()) },
              success() { $btn.closest('.photo-col').remove(); },
              error()   { alert('Silinemedi'); }
          });
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

