<form id="kodEkle" method="POST">
    @csrf
    <input type="hidden" name="marka_id" value="{{ $marka_id }}">
    <input type="hidden" name="model_id" value="{{ $model_id ?? 0 }}">

    
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
        <button type="submit" class="btn btn-success">
            <i class="fa fa-save"></i> Kaydet
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    $("#kodEkle").on('submit', function(e) {
        e.preventDefault();
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
        
        $.ajax({
            url: "{{ route('super.admin.kodlar.store') }}",
            type: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                $('#kodEkleModal').modal('hide');  // Modal'ı kapat
                alert(data.message);
                 location.reload();  // Sayfayı yenile - aynı sayfada kalır
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Bir hata oluştu";
                alert("Hata: " + msg);
            }
        });
    });
});
</script>