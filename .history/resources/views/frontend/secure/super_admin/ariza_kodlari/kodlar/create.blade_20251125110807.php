<form id="kodEkle" method="POST">
    @csrf
    <input type="hidden" name="form_token" id="kodEkleFormToken" value="">
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
    // Flag değişkeni
    let kodFormSubmitting = false;
    
    // Benzersiz token oluşturucu
    function generateKodToken() {
        return Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    // Kod Ekle Butonuna Tıklandığında (Modal Yükleme)
    $(document).on('click', '.kodEkleBtn', function(){
        $.ajax({
            url: "{{ route('super.admin.kodlar.create') }}?marka_id={{ $marka_id }}&model_id={{ $model_id }}"
        }).done(function(data) {
            $('#kodEkleModal .modal-body').html(data);
            
            // FORM YÜKLENDİKTEN SONRA TOKEN OLUŞTUR VE ATA
            $('#kodEkleFormToken').val(generateKodToken());
            kodFormSubmitting = false; // Flag'i sıfırla
            
            $('#kodEkleModal').modal('show');
        });
    });

    // Form Submit İşlemi
    $(document).on('submit', '#kodEkle', function(e) {
        e.preventDefault();
        
        // Token ve Flag kontrolü
        if (kodFormSubmitting) {
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

        // Kayıt başlıyor - Butonu kilitle
        kodFormSubmitting = true;
        let submitBtn = $('#kodEkleSubmitBtn');
        let originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Kaydediliyor...');
        
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
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Bir hata oluştu";
                alert("Hata: " + msg);
                
                // Hata durumunda yeni token oluştur ve butonu aç
                $('#kodEkleFormToken').val(generateKodToken());
                kodFormSubmitting = false;
                submitBtn.prop('disabled', false).html(originalText);
            }
        });

        // Emniyet sübabı: 3 saniye sonra buton hala kilitliyse aç (timeout vb. durumlar için)
        setTimeout(function() {
            if(kodFormSubmitting) {
                $('#kodEkleFormToken').val(generateKodToken());
                kodFormSubmitting = false;
                submitBtn.prop('disabled', false).html(originalText);
            }
        }, 3000);
    });
});
</script>