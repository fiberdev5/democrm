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
$(document).on('submit', '#markaEkle', function(e) {
    e.preventDefault();
    console.log('Form submit tetiklendi'); // Debug için
    
    var marka = $.trim($(this).find(".marka").val());
    
    if (marka.length === 0) {
        alert("Marka adı boş geçilemez");
        $(this).find(".marka").focus();
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
            console.log(xhr); // Debug için
            alert("Hata: " + (xhr.responseJSON?.message || "Bir hata oluştu"));
        }
    });
});
</script>