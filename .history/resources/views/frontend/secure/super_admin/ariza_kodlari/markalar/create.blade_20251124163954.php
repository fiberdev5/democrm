<form id="markaEkle" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="form_token" id="markaEkleToken" value="">
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
    let formSubmitting = false;
    
    // Benzersiz token oluştur
    function generateToken() {
        return Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    // Sayfa yüklendiğinde ilk token'ı oluştur
    $('#markaEkleToken').val(generateToken());
    
    // Form submit
    $('#markaEkle').submit(function(event) {
        // Token kontrolü
        if (formSubmitting) {
            event.preventDefault();
            return false;
        }
        
        // Butonu disable et
        formSubmitting = true;
        $(this).find('button[type="submit"]').prop('disabled', true);
        
        // 3 saniye sonra yeniden aktif et
        setTimeout(function() {
            $('#markaEkleToken').val(generateToken());
            formSubmitting = false;
            $('#markaEkle button[type="submit"]').prop('disabled', false);
        }, 3000);
        
        return true;
    });
});
</script>