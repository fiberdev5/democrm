<form method="post" id="servisOptNotuEkle" action="{{ route('store.service.opt.note', $firma->id) }}" class="col-sm-6" style="margin: 0 auto;padding:10px;">
  @csrf

  <div class="row form-group">
    <div class="col-lg-12 rw2">
      <textarea type="text" name="aciklama" class="form-control aciklama" placeholder="Buraya yazın.." rows="3" style="resize: none;" autocomplete="off" required></textarea>
    </div>
  </div>

  <div style="text-align: center;margin-top: 5px;">
    <input type="hidden" name="servisid" class="servisid" value="{{$servis->id}}"/>
    <input type="submit" class="btn btn-primary btn-sm btnSubmit" value="Gönder"/>
  </div>
</form>

<script>
  $(document).ready(function () {
    var isSubmitting = false;
    
    $('#servisOptNotuEkle').off('submit').on('submit', function (event) {
      event.preventDefault();
      
      if (isSubmitting) {
        return false;
      }
      
      // Validation
      var formIsValid = true;
      $(this).find('textarea[required]').each(function () {
        if (!$(this).val().trim()) {
          formIsValid = false;
          return false;
        }
      });
      
      if (!formIsValid) {
        alert('Lütfen zorunlu alanları doldurun.');
        return false;
      }
      
      isSubmitting = true;
      
      var formData = new FormData(this);
      var firma_id = {{ $firma->id }};
      var servis_id = {{ $servis->id }};
      var $addModal = $('#servisOptNotuEkle').closest('.modal');
      var $form = $(this);
      
      $.ajax({
        url: $(this).attr("action"),
        type: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
          $('.btnSubmit').prop('disabled', true).val('Kaydediliyor...');
        },
        success: function (response) {
          if (response.success) {
            
            // 1. ÖNCE Tab içeriğini yenile ve BİTMESİNİ BEKLE
            $.ajax({
              url: "/" + firma_id + "/servis-operator-notlari/" + servis_id,
              success: function(tabData) {
                // Tab içeriği yüklendi
                $('#tab8').html(tabData);
                console.log('✅ Tab içeriği yüklendi');
                
                // 2. DataTable'ı yenile
                try {
                  if ($('#datatableService').length > 0 && $.fn.DataTable.isDataTable('#datatableService')) {
                    $('#datatableService').DataTable().ajax.reload(null, false);
                    console.log('✅ DataTable yenilendi');
                  }
                } catch (error) {
                  console.warn('⚠️ DataTable yenilenemedi:', error);
                }
                
                // 3. HER ŞEY BİTTİKTEN SONRA modal'ı kapat
                if ($addModal.length) {
                  $addModal.modal('hide');
                }
                
                // 4. Başarı mesajı
                alert(response.message || "Servis operatör notu başarıyla eklendi.");
                
              },
              error: function() {
                console.error('Tab yüklenemedi');
                // Hata olsa bile modal'ı kapat
                if ($addModal.length) {
                  $addModal.modal('hide');
                }
                alert("Not eklendi ancak liste yüklenemedi. Sayfayı yenileyin.");
              }
            });
            
          } else {
            alert("Kayıt yapılamadı.");
          }
        },
        error: function (xhr, status, error) {
          console.error('Hata:', xhr.responseText);
          alert("Güncelleme başarısız! Lütfen tekrar deneyin.");
        },
        complete: function() {
          isSubmitting = false;
          $('.btnSubmit').prop('disabled', false).val('Gönder');
        }
      });
    });
  });
</script>