
<form id="modelEkle" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="mid" value="{{ $marka_id }}">
    
    <div class="form-group">
        <input type="text" name="model" class="form-control model" placeholder="Model Adı" required>
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
    $("#modelEkle").on('submit', function(e) {
        e.preventDefault();
        var model = $.trim($(".model").val());
        
        if (model.length === 0) {
            alert("Model adı boş geçilemez");
            $(".model").focus();
            return false;
        }
        
        $.ajax({
            url: "{{ route('modeller.store') }}",
            type: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                alert(data.message);
                window.location.href = "{{ route('modeller.index', $marka_id) }}";
            },
            error: function(xhr) {
                alert("Hata: " + xhr.responseJSON.message);
            }
        });
    });
});
</script>