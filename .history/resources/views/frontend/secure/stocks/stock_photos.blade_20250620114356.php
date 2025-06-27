<form method="POST" id="stokFotoEkle" enctype="multipart/form-data" onsubmit="return false;">
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

    // ❗ Formun otomatik GET ile submit edilmesini engelle
    $('#stokFotoEkle').on('submit', function(e) {
        e.preventDefault();
    });

    // Resim yükleme işlemi
    $('#customFile').on("change", function () {
        let file = this.files[0];
        if (!file) return;

        // Validasyon
        if (file.size > 5242880) {
            alert("Dosya 5MB'dan büyük olamaz.");
            return;
        }

        if (!["image/jpeg", "image/png", "image/jpg"].includes(file.type)) {
            alert("Sadece JPG ve PNG yüklenebilir.");
            return;
        }

        let formData = new FormData($('#stokFotoEkle')[0]);

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

                // Yeni görseli ekle
                $('.imgBox').prepend(`
                    <div class="col-6 col-sm-3 stn mb-2" data-id="${res.id}">
                        <img src="${res.resim_yolu}" class="img-fluid border" style="width: 100%;">
                        <button class="btn btn-danger btn-sm btn-block stokFotoSil mt-1" data-id="${res.id}">Sil</button>
                    </div>
                `);
            },
            error: function () {
                $(".imgLoad").hide();
                alert("Yükleme başarısız.");
            }
        });
    });

    // Fotoğraf silme işlemi
    $(document).on('click', '.stokFotoSil', function (e) {
        e.preventDefault();
        if (!confirm("Fotoğraf silinsin mi?")) return;

        let id = $(this).data('id');
        let fotoDiv = $('.stn[data-id="' + id + '"]');

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
});
</script>
