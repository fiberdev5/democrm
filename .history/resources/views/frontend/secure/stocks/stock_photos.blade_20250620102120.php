<link href="{{ asset('vendor/fancybox/fancybox.min.css') }}" rel="stylesheet">
<script src="{{ asset('vendor/fancybox/fancybox.min.js') }}"></script>

<div class="stokFotolar">
    {{-- Fotoğraf Yükleme Formu --}}
    <form id="stokFotoEkle" enctype="multipart/form-data">
        @csrf
        <span class="imgLoad" style="font-size: 14px; display: none;">
            Yükleniyor... <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 64%; height: 15px; border-radius: 10px; display: inline-block; position: relative; top: 2px;"></div>
        </span>

        <div class="custom-file mb-2">
            <input type="file" class="custom-file-input" name="resim" id="customFile">
            <label class="custom-file-label" for="customFile">Dosya Seç</label>
            <span style="font-size: 12px; color: red;">Max 5MB, sadece jpg ve png yüklenebilir.</span>
        </div>

        <input type="hidden" name="stock_id" value="{{ $stock_id }}">
    </form>

    {{-- Fotoğraf Galerisi --}}
    <div class="row imgBox">
        @foreach($photos as $foto)
            <div class="col-6 col-sm-3 stn mb-2">
                <a href="{{ $foto->resimyol }}" data-fancybox="galeriGroup">
                    <img src="{{ $foto->resimyol }}" class="img-fluid" style="width: 100%;">
                </a>
                <button class="btn btn-danger btn-sm btn-block stokFotoSil mt-1" data-id="{{ $foto->id }}">Foto Sil</button>
            </div>
        @endforeach
    </div>
</div>

<script>
$(document).ready(function () {
    // Fancybox init
    Fancybox.bind("[data-fancybox]", {});

    // Dosya seçildiğinde otomatik gönder
    $('#customFile').on("change", function (e) {
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
                $('.nav4').trigger('click'); // Tabı yeniden yükle
            },
            error: function (err) {
                $(".imgLoad").hide();
                alert("Yükleme başarısız.");
            }
        });
    });

    // Foto silme
    $('.stokFotoSil').click(function (e) {
        e.preventDefault();
        if (!confirm("Silmek istediğinize emin misiniz?")) return;

        var id = $(this).data('id');

        $.ajax({
            url: "{{ url('/' . $tenant_id . '/stok-foto-sil') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id
            },
            success: function () {
                $('.nav4').trigger('click'); // Tabı yeniden yükle
            },
            error: function () {
                alert("Silme işlemi başarısız.");
            }
        });
    });
});
</script>
