
<form id="markaEkle" method="POST" enctype="multipart/form-data">
    @csrf
        <input type="hidden" name="form_token" id="markaEkleFormToken" value="">
    <div class="form-group">
        <input type="text" name="marka" class="form-control marka" placeholder="Marka Adı" required>
    </div>
    <div class="form-group">
        <input type="file" name="resim" class="form-control-file">
        <small class="text-muted">Sadece jpg, png dosya türlerini yükleyebilirsiniz.</small>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-primary btn-sm">Gönder</button>
    </div>
</form>

<script>
$(document).ready(function() {
    let markaFormSubmitting = false;
    
    // Benzersiz token oluştur
    function generateMarkaToken() {
        return Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    // Sayfa yüklendiğinde ilk token'ı oluştur
    $('#markaEkleFormToken').val(generateMarkaToken());
    
    // Marka Ekle Modal açılınca yeni token oluştur
    $(document).on('click', '.markaEkleBtn', function(){
        $('#markaEkleFormToken').val(generateMarkaToken());
        markaFormSubmitting = false;
        $('#markaEkleSubmit').prop('disabled', false);
    });
    
    // Mevcut submit handler'ı override et
    $('#markaEkleForm').off('submit').on('submit', function(event) {
        event.preventDefault();
        
        // Token kontrolü
        if (markaFormSubmitting) {
            return false;
        }
        
        let submitBtn = $('#markaEkleSubmit');
        let originalText = submitBtn.html();
        
        // Butonu disable et
        markaFormSubmitting = true;
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Kaydediliyor...');
        
        $.ajax({
            url: "{{ route('super.admin.markalar.store') }}",
            type: "POST",
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(res){
                $('#markaEkleModal').modal('hide');
                alert(res.message);
                location.reload();
            },
            error: function(xhr){
                let errorMsg = xhr.responseJSON?.message || "Bir hata oluştu!";
                alert("Hata: " + errorMsg);
                
                // Yeni token oluştur ve formu yeniden aktif et
                $('#markaEkleFormToken').val(generateMarkaToken());
                markaFormSubmitting = false;
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
        
        // 3 saniye sonra yeniden aktif et
        setTimeout(function() {
            $('#markaEkleFormToken').val(generateMarkaToken());
            markaFormSubmitting = false;
            $('#markaEkleSubmit').prop('disabled', false).html(originalText);
        }, 3000);
        
        return false;
    });
    
    // Modal kapandığında token yenile
    $('#markaEkleModal').on('hidden.bs.modal', function () {
        $('#markaEkleFormToken').val(generateMarkaToken());
        markaFormSubmitting = false;
        $('#markaEkleSubmit').prop('disabled', false);
    });
});
</script>