<form id="stokFotoEkle" enctype="multipart/form-data">
    @csrf
    <div class="mb-2">
        <input type="file" class="form-control" name="resim" id="customFile" accept="image/jpeg,image/png">
        <input type="hidden" name="stock_id" value="{{ $stock_id }}">
        <small class="text-muted">Maks. 5MB | JPG veya PNG</small>
    </div>
    <div class="imgLoad" style="display: none;">
        <div class="progress my-1" style="height: 10px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
        </div>
    </div>
</form>

<div class="row imgBox">
    @foreach($photos as $foto)
        <div class="col-6 col-sm-3 stn mb-2" data-id="{{ $foto->id }}">
            <img src="{{ $foto->resimyol }}" class="img-fluid border" style="width: 100%;">
            <button class="btn btn-danger btn-sm btn-block stokFotoSil mt-1" data-id="{{ $foto->id }}">Sil</button>
        </div>
    @endforeach
</div>
<script>
$(document).ready(function () {

    // ❗ Formun GET ile submit olmasını engelle (bu çok önemli!)
    $('#stokFotoEkle').on('submit', function(e) {
        e.preventDefault();
    });

    // Dinamik silme butonlarını etkinleştir
    $(document).on('click', '.stokFotoSil', function (e) {
        e.preventDefault();
        if (!confirm("Silmek istediğinize emin misiniz?")) return;

        var id = $(this).data('id');
        var fotoDiv = $('.stn[data-id="' + id + '"]');

        $.ajax({
            url: "{{ url('/' . $tenant_id . '/stok-foto-sil') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id
            },
            success: function () {
                fotoDiv.fadeOut(300, function () { $(this).remove(); });
            },
            error: function () {
                alert("Silme işlemi başarısız.");
            }
        });
    });

    // Yükleme işlemi
    $('#customFile').on("change", function () {
        var file = this.files[0];
        if (!file) return;

        if (file.size > 5242880) {
            alert("Dosya 5MB'dan büyük olamaz.");
            return;
        }

        var fileType = file.type;
        if (!["image/jpeg", "image/png", "image/jpg"].includes(fileType)) {
            alert("Sadece JPG ve PNG yüklenebilir.");
            return;
        }

        var formData = new FormData($('#stokFotoEkle')[0]);

        $.ajax({
            url: "{{ url('/' . $tenant_id . '/stok-foto-ekle') }}",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $(".imgLoad").show();
            },
            success: function (res) {
                $(".imgLoad").hide();
                $('#customFile').val('');

                // Yeni görseli ekle (önceki gibi sayfayı yenilemeden)
                $('.imgBox').prepend(`
                    <div class="col-6 col-sm-3 stn mb-2" data-id="${res.id}">
                        <img src="${res.resim_yolu}" class="img-fluid border" style="width: 100%;">
                        <button class="btn btn-danger btn-sm btn-block stokFotoSil mt-1" data-id="${res.id}">Sil</button>
                    </div>
                `);
            },
            error: function (err) {
                $(".imgLoad").hide();
                alert("Yükleme başarısız.");
            }
        });
    });

});
</script>
