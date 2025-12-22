<form id="markaEkle" method="POST" enctype="multipart/form-data">
    @csrf
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
// Event delegation kullan - modal body'ye delegate et
$("#markaEkleModal").on('submit', '#markaEkle', function(e) {
    e.preventDefault();
    var marka = $.trim($(".marka").val());
    
    if (marka.length === 0) {
        alert("Marka adı boş geçilemez");
        $(".marka").focus();
        return false;
    }
    
    $.ajax({
        url: "{{ route('super.admin.markalar.store') }}",
        type: "POST",
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
            alert(data.message);
            $('#markaEkleModal').modal('hide');
            location.reload();
        },
        error: function(xhr) {
            alert("Hata: " + (xhr.responseJSON?.message || "Bir hata oluştu"));
        }
    });
});
</script>