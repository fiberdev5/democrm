<form id="kodEkle" method="POST">
    @csrf
    <input type="hidden" name="marka_id" value="{{ $marka_id }}">
    <input type="hidden" name="model_id" value="{{ $model_id }}">
    
    <div class="form-group">
        <label for="kod">Hata Kodu <span class="text-danger">*</span></label>
        <input type="text" 
               name="kod" 
               id="kod"
               class="form-control kod" 
               placeholder="Örn: E01, F12, C4..." 
               required>
    </div>
    <div class="form-group">
        <label for="baslik">Başlık</label>
        <input type="text" 
               name="baslik" 
               id="baslik"
               class="form-control baslik" 
               placeholder="Kısa açıklama başlığı">
    </div>
    <div class="form-group">
        <label for="aciklama">Açıklama <span class="text-danger">*</span></label>
        <textarea name="aciklama" 
                  id="aciklama"
                  class="form-control aciklama" 
                  rows="5"
                  placeholder="Detaylı arıza açıklaması ve çözüm önerileri..."
                  required></textarea>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-success" id="kodKaydetBtn">
            <i class="fa fa-save"></i> Kaydet
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    let isSubmitting = false;  // Çift tıklama kontrolü
    
    $("#kodEkle").on('submit', function(e) {
        e.preventDefault();
        
        // Eğer zaten gönderiliyor ise, tekrar gönderme
        if (isSubmitting) {
            return false;
        }
        
        var kod = $.trim($(".kod").val());
        var aciklama = $.trim($(".aciklama").val());
        
        if (kod.length === 0) {
            alert("Hata kodu boş geçilemez");
            $(".kod").focus();
            return false;
        }
        
        if (aciklama.length === 0) {
            alert("Açıklama boş geçilemez");
            $(".aciklama").focus();
            return false;
        }
        
        // Submit işlemini başlat
        isSubmitting = true;
        $("#kodKaydetBtn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Kaydediliyor...');
        
        $.ajax({
            url: "{{ route('super.admin.kodlar.store') }}",
            type: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                $('#kodEkleModal').modal('hide');
                alert(data.message);
                location.reload();
            },
            error: function(xhr) {
                // Hata durumunda submit'i tekrar aktif et
                isSubmitting = false;
                $("#kodKaydetBtn").prop('disabled', false).html('<i class="fa fa-save"></i> Kaydet');
                
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Bir hata oluştu";
                alert("Hata: " + msg);
            }
        });
        
        return false;
    });
    
    // Modal tamamen kapandığında isSubmitting'i sıfırla
    $('#kodEkleModal').on('hidden.bs.modal', function () {
        isSubmitting = false;
        $("#kodKaydetBtn").prop('disabled', false).html('<i class="fa fa-save"></i> Kaydet');
    });
});
</script>