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
    console.log('1. Success bloğu çalıştı');
    alert("Servis operatör notu başarıyla eklendi.");
    
    // DataTable kontrolü
    console.log('2. DataTable var mı?', $.fn.DataTable);
    console.log('3. #datatableService var mı?', $('#datatableService').length);
    console.log('4. DataTable başlatılmış mı?', $.fn.DataTable.isDataTable('#datatableService'));
    
    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#datatableService')) {
      console.log('5. DataTable reload ediliyor...');
      $('#datatableService').DataTable().ajax.reload();
      console.log('6. DataTable reload edildi');
    } else {
      console.log('5. DataTable YOK veya başlatılmamış!');
    }
    
    // loadServiceHistory kontrolü
    console.log('7. loadServiceHistory fonksiyonu var mı?', typeof loadServiceHistory);
    
    if (typeof loadServiceHistory === 'function') {
      console.log('8. loadServiceHistory çağrılıyor...');
      loadServiceHistory({{ $servis->id }});
      console.log('9. loadServiceHistory çağrıldı');
    } else {
      console.log('8. loadServiceHistory fonksiyonu YOK!');
    }
    
    console.log('10. Tab değiştiriliyor...');
    $('.nav8').trigger('click');
    console.log('11. Tab değiştirildi');
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