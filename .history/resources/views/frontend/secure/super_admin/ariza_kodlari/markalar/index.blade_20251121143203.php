@extends('frontend.secure.user_master')
@section('user')
<div class="page-content" id="passwords">
    <div class="container-fluid">
        <div class="row pageDetail">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Markalar</h4>
                    </div>
                        <div class="card-body">
                            <!-- Üst Alan: Buton ve Arama Kutusu -->
                            <div class=" align-items-center justify-content-between ">
                                <!-- Sol Taraf: Marka Ekle Butonu -->
                                <div class="col-auto">
                                    <button type="button" class="btn btn-success btn-sm markaEkleBtn px-3">
                                        <i class="fa fa-plus"></i> <span>Marka Ekle</span>
                                    </button>
                                </div>
                                
                                <!-- Sağ Taraf: Arama Kutusu Alanı -->
                                <div class="col-auto">
                                    <div id="searchPlaceholder"></div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="markaTable" class="table table-hover table-striped" style="width:100%">
                                    <!-- Tablo içeriği aynı kalacak -->
                                    <thead>
                                        <tr>
                                            <th width="60%">Marka</th>
                                            <th width="40%">İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($markalar as $marka)
                                        <tr data-id="{{ $marka->id }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($marka->resimyol)
                                                        <img src="{{ asset('upload/ariza_kodlari/'.$marka->resimyol) }}" 
                                                            width="50" 
                                                            height="50" 
                                                            style="object-fit: cover; min-width: 50px;" 
                                                            class="mr-2 border rounded"
                                                            loading="lazy"
                                                            alt="{{ $marka->marka }}">
                                                    @else
                                                        <div class="mr-2 border rounded bg-light d-flex align-items-center justify-content-center" 
                                                            style="width: 50px; height: 50px; min-width: 50px;">
                                                            <i class="fa fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <strong class="marka-adi-link" 
                                                            data-id="{{ $marka->id }}"
                                                            data-marka="{{ $marka->marka }}"
                                                            data-resim="{{ $marka->resimyol }}"
                                                            style="cursor: pointer; color: #007bff;"
                                                            title="Düzenlemek için tıklayın">
                                                        {{ $marka->marka }}
                                                    </strong>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('super.admin.modeller.index', $marka->id) }}" 
                                                    class="btn btn-info" title="Modeller">
                                                        <i class="fa fa-list"></i> Modeller
                                                    </a>
                                                    <a href="{{ route('super.admin.kodlar.index', ['marka_id'=>$marka->id,'model_id'=>0]) }}" 
                                                    class="btn btn-warning" title="Arıza Kodları">
                                                        <i class="fa fa-wrench"></i> Kodlar
                                                    </a>
                                                    <button class="btn btn-primary markaDuzenleBtn" 
                                                            data-id="{{ $marka->id }}"
                                                            data-marka="{{ $marka->marka }}"
                                                            data-resim="{{ $marka->resimyol }}"
                                                            title="Düzenle">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-danger markaSil" 
                                                            data-id="{{ $marka->id }}"
                                                            title="Sil">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Marka Ekle Modal --}}
<div class="modal fade" id="markaEkleModal" tabindex="-1" role="dialog" aria-labelledby="markaEkleTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="markaEkleTitle">
                    <i class="fa fa-plus-circle"></i> Yeni Marka Ekle
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Kapat">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="markaEkleForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="marka_adi">Marka Adı <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="marka" 
                               id="marka_adi"
                               class="form-control" 
                               placeholder="Örn: Bosch, Siemens, Arçelik..." 
                               required>
                    </div>
                    <div class="form-group">
                        <label for="marka_resim">Marka Logosu</label>
                        <div class="custom-file">
                            <input type="file" 
                                   name="resim" 
                                   id="marka_resim"
                                   class="custom-file-input" 
                                   accept="image/jpeg,image/jpg,image/png,image/svg+xml">
                            <label class="custom-file-label" for="marka_resim">Dosya Seçin</label>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fa fa-info-circle"></i> Sadece JPG, PNG, SVG (Max: 2MB)
                        </small>
                        <div id="resimOnizleme" class="mt-2" style="display:none;">
                            <img src="" alt="Önizleme" class="img-thumbnail" style="max-width: 150px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="markaEkleSubmit">
                        <i class="fa fa-save"></i> Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Marka Düzenle Modal --}}
<div class="modal fade" id="markaDuzenleModal" tabindex="-1" role="dialog" aria-labelledby="markaDuzenleTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="markaDuzenleTitle">
                    <i class="fa fa-edit"></i> Marka Düzenle
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Kapat">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="markaDuzenleForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="marka_id" id="duzenle_marka_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="duzenle_marka_adi">Marka Adı <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="marka" 
                               id="duzenle_marka_adi"
                               class="form-control" 
                               required>
                    </div>
                    <div class="form-group">
                        <label for="duzenle_marka_resim">Marka Logosu</label>
                        <div class="custom-file">
                            <input type="file" 
                                   name="resim" 
                                   id="duzenle_marka_resim"
                                   class="custom-file-input" 
                                   accept="image/jpeg,image/jpg,image/png,image/svg+xml">
                            <label class="custom-file-label" for="duzenle_marka_resim">Dosya Seçin</label>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fa fa-info-circle"></i> Değiştirmek için yeni dosya seçin
                        </small>
                        <div id="mevcutResim" class="mt-2">
                            <img src="" alt="Mevcut Logo" class="img-thumbnail" style="max-width: 150px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="markaDuzenleSubmit">
                        <i class="fa fa-save"></i> Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    /* DataTable özelleştirme - Sadece arama kutusu */
    #markaTable_wrapper .dataTables_filter {
        float: right;
        margin-bottom: 15px;
    }
    #markaTable_wrapper .dataTables_filter label {
        font-weight: normal;
    }
    #markaTable_wrapper .dataTables_filter input {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 6px 12px;
        margin-left: 5px;
        width: 250px;
    }
    #markaTable_wrapper .dataTables_filter input:focus {
        outline: none;
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    /* Modal açılma animasyonu */
    .modal.fade .modal-dialog {
        transition: transform 0.2s ease-out;
    }
    
    /* Modal kapatma butonu - Küçük ve şık */
    .modal-header .close {
        padding: 0.5rem 0.75rem;
        margin: -0.5rem -0.75rem -0.5rem auto;
        opacity: 0.8;
        font-size: 1.3rem;
        font-weight: 700;
        line-height: 1;
        text-shadow: 0 1px 0 rgba(0,0,0,0.3);
        background: transparent;
        border: none;
        cursor: pointer;
    }
    .modal-header .close:hover {
        opacity: 1;
        transform: scale(1.1);
    }
    .modal-header .close:focus {
        outline: none;
    }
    
    /* Buton hover efektleri */
    .btn-group-sm .btn {
        transition: all 0.2s;
    }
    .btn-group-sm .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    /* Modal footer butonları ortala */
    .modal-footer {
        justify-content: center;
        padding: 0.75rem;
    }

  /* DataTables Arama Kutusu Özelleştirme */
    #searchPlaceholder .dataTables_filter {
        float: none !important;
        margin: 0 !important;
        text-align: right;
    }

    #searchPlaceholder .dataTables_filter label {
        margin: 0 !important;
        display: flex !important;
        align-items: center;
        font-weight: normal;
        white-space: nowrap; /* Metnin aşağı kaymasını engeller */
    }

    #searchPlaceholder .dataTables_filter input {
        margin-left: 10px !important;
        height: 34px; /* Buton yüksekliği ile eşitlemek için (btn-sm yaklaşık 31-34px) */
        padding: 4px 10px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        outline: none;
        width: 200px; /* Arama kutusu genişliği */
    }

    #searchPlaceholder .dataTables_filter input:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .markaEkleBtn{
            position: absolute;
    }
    .markaEkleBtn i{
            display: none;
        }
    @media (max-width: 767px) {
        .markaEkleBtn span{
            display: none;
        }
        .markaEkleBtn i{
            display: block;
        }
    }
    /* Marka adı hover efekti */
.marka-adi-link {
    transition: all 0.2s;
}
.marka-adi-link:hover {
    color: #0056b3 !important;
    text-decoration: underline;
}
</style>

<script>
$(document).ready(function(){
    let table; // DataTable referansı

    // DataTable başlat - Sayfalama KAPALI, sadece arama
    table = $('#markaTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/tr.json"
        },
        "order": [[0, "asc"]],
        "paging": false,
        "info": false,
        "responsive": true,
        "dom": 'frti',
        "columnDefs": [
            { "orderable": false, "targets": 1 }
        ]
    });
    // Arama kutusunu sağ tarafa taşı
    $('#markaTable_filter').appendTo('#searchPlaceholder');
    // Custom file input label güncelleme
    $(document).on('change', '.custom-file-input', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass("selected").html(fileName);
        
        if($(this).attr('id') === 'marka_resim') {
            let reader = new FileReader();
            reader.onload = function(e) {
                $('#resimOnizleme img').attr('src', e.target.result);
                $('#resimOnizleme').show();
            }
            if(this.files[0]) {
                reader.readAsDataURL(this.files[0]);
            }
        }
    });

    /** Marka Ekle Modal **/
    $(document).on('click', '.markaEkleBtn', function(){
        $('#markaEkleForm')[0].reset();
        $('#resimOnizleme').hide();
        $('.custom-file-label').removeClass('selected').html('Dosya Seçin');
        $('#markaEkleModal').modal('show');
    });

    /** Marka Ekle Form Submit **/
    $(document).on('submit', '#markaEkleForm', function(e){
        e.preventDefault();
        
        let submitBtn = $('#markaEkleSubmit');
        let originalText = submitBtn.html();
        
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
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    /** Marka Düzenle Modal **/
    $(document).on('click', '.markaDuzenleBtn', function(){
        let id = $(this).data('id');
        let marka = $(this).data('marka');
        let resim = $(this).data('resim');
        
        $('#duzenle_marka_id').val(id);
        $('#duzenle_marka_adi').val(marka);
        
        if(resim) {
            $('#mevcutResim img').attr('src', '{{ asset("upload/ariza_kodlari/") }}/' + resim);
            $('#mevcutResim').show();
        } else {
            $('#mevcutResim').hide();
        }
        
        $('#duzenle_marka_resim').val('');
        $('.custom-file-label').removeClass('selected').html('Dosya Seçin');
        
        $('#markaDuzenleModal').modal('show');
    });

    /** Marka Düzenle Form Submit **/
    $(document).on('submit', '#markaDuzenleForm', function(e){
        e.preventDefault();
        
        let id = $('#duzenle_marka_id').val();
        let submitBtn = $('#markaDuzenleSubmit');
        let originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Güncelleniyor...');

        $.ajax({
            url: "/super-admin/marka-duzenle/" + id,
            type: "POST",
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(res){
                $('#markaDuzenleModal').modal('hide');
                alert(res.message);
                location.reload();
            },
            error: function(xhr){
                let errorMsg = xhr.responseJSON?.message || "Bir hata oluştu!";
                alert("Hata: " + errorMsg);
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    /** Marka Sil **/
    $(document).on('click', '.markaSil', function(){
        if(!confirm('Bu markayı silmek istediğinize emin misiniz?\n\nİlişkili tüm modeller ve arıza kodları da silinecektir!')) return;
        
        let id = $(this).data('id');
        let row = $(this).closest('tr');

        $.ajax({
            url: "/super-admin/marka-sil/" + id,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(res){
                table.row(row).remove().draw();
                alert(res.message);
            },
            error: function(xhr){
                let errorMsg = xhr.responseJSON?.message || "Silme işlemi başarısız!";
                alert("Hata: " + errorMsg);
            }
        });
    });

    // Modal kapatma olaylarını manuel ekle
    $(document).on('click', '[data-dismiss="modal"]', function(){
        $(this).closest('.modal').modal('hide');
    });

    // Modal dışına tıklayınca kapat
    $(document).on('click', '.modal', function(e){
        if($(e.target).hasClass('modal')) {
            $(this).modal('hide');
        }
    });

    // ESC tuşuyla kapat
    $(document).on('keydown', function(e){
        if(e.key === 'Escape') {
            $('.modal').modal('hide');
        }
    });

    // Modal kapandığında formu temizle
    $('#markaEkleModal, #markaDuzenleModal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        $(this).find('.custom-file-label').removeClass('selected').html('Dosya Seçin');
        $('#resimOnizleme').hide();
        $('#mevcutResim').hide();
    });
});
</script>
@endsection