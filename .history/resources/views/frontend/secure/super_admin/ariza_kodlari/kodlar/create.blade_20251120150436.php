// resources/views/frontend/secure/super_admin/ariza_kodlari/kodlar/create.blade.php

<form id="kodEkle" method="POST">
    @csrf
    <input type="hidden" name="marka_id" value="{{ $marka_id }}">
    <input type="hidden" name="model_id" value="{{ $model_id }}">
    
    <div class="form-group">
        <input type="text" name="kod" class="form-control kod" placeholder="Hata Kodu" required>
    </div>
    <div class="form-group">
        <input type="text" name="baslik" class="form-control baslik" placeholder="Başlık">
    </div>
    <div class="form-group">
        <textarea name="aciklama" class="form-control aciklama" rows="4" 
                  placeholder="Açıklama" required></textarea>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-primary btn-sm">Gönder</button>
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
            url: "{{ route('kodlar.store') }}",
            type: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                alert(data.message);
                window.location.href = "{{ route('kodlar.index', ['marka_id' => $marka_id, 'model_id' => $model_id]) }}";
            },
            error: function(xhr) {
                alert("Hata: " + xhr.responseJSON.message);
            }
        });
    });
});
</script>