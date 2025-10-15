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
  $(document).ready(function () {
    // İlk form olan servisOptNotuEkle'nin submit işlemi (Operatör Notu Ekleme)
    $('#servisOptNotuEkle').submit(function (event) {
      event.preventDefault(); // Sayfanın varsayılan yenilenmesini engelle

      var formIsValid = true;
      $(this).find('textarea[required]').each(function () { // Sadece zorunlu textarea'ları kontrol et
        var isEmpty = $.trim($(this).val()) === ""; // Boşlukları da kontrol et
        if (isEmpty) {
          formIsValid = false;
          return false; // Döngüyü kır
        }
      });

      if (!formIsValid) {
        alert('Lütfen zorunlu alanları doldurun.');
        return false;
      }

      var formData = new FormData(this);
      $.ajax({
        url: $(this).attr("action"), // Formun action özelliğindeki URL'yi kullan
        type: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
          // İsteğe bağlı: Yükleniyor mesajı gösterebilirsiniz
          // $(".btnWrap").html("Yükleniyor. Bekleyin..");
        },
        success: function (data) {
          if (data.success) {
            alert("Servis operatör notu başarıyla eklendi.");
            $('.aciklama').val(''); // Not textarea'sını temizle
            $('#datatableService').DataTable().ajax.reload(); // Eğer ana servis tablosu da güncelleniyorsa
            loadServiceHistory({{ $servis->id }}); // **ÖNEMLİ: Servis geçmişini yeniden yükle**
            $('.nav8').trigger('click'); // Eğer ilgili sekmeyi tetiklemesi gerekiyorsa
          } else {
              alert("Kayıt yapılamadı.");
              // Gerekirse tam sayfa yenileme: window.location.reload(true);
          }
        },
        error: function (xhr, status, error) {
          alert("Kayıt başarısız!");
          console.error("Operatör Notu Ekleme Hatası:", xhr.responseText);
        },
      });
    });
  });
</script>