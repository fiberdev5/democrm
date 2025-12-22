@extends('frontend.secure.user_master')
@section('user')
<div class="page-content" id="passwords">
    <div class="container-fluid">
        <div class="row pageDetail">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">{{ $titleSec }} - Arıza Kodları</h4>
                    </div>
                    <div class="card-body">
                        <!-- Üst Alan: Butonlar ve Arama Kutusu -->
                        <div class="align-items-center justify-content-between">
                            <!-- Sol Taraf: Butonlar -->
                            <div class="col-auto">
                                <button type="button" class="btn btn-success btn-sm kodEkleBtn px-3">
                                    <i class="fa fa-plus"></i> <span>Arıza Kodu Ekle</span>
                                </button>
                                <a href="{{ $model_id > 0 ? route('super.admin.modeller.index', $marka_id) : route('super.admin.markalar.index') }}" 
                                   class="btn btn-secondary btn-sm px-3">
                                    <i class="fa fa-arrow-left"></i> <span>Geri</span>
                                </a>
                            </div>
                            
                            <!-- Sağ Taraf: Arama Kutusu Alanı -->
                            <div class="col-auto">
                                <div id="searchPlaceholder"></div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="kodTable" class="table table-hover table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th width="20%">Kod</th>
                                        <th width="40%">Başlık</th>
                                        <th width="40%">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kodlar as $kod)
                                    <tr data-id="{{ $kod->id }}">
                                        <td class="align-middle">
                                            <strong>{{ $kod->kodu }}</strong>
                                        </td>
                                        <td class="align-middle">
                                            {{ $kod->baslik }}
                                        </td>
                                        <td class="align-middle">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-primary kodDuzenleBtn" 
                                                        data-id="{{ $kod->id }}"
                                                        data-kod="{{ $kod->kodu }}"
                                                        data-baslik="{{ $kod->baslik }}"
                                                        data-aciklama="{{ $kod->aciklama }}"
                                                        title="Düzenle">
                                                    <i class="fa fa-edit"></i> Düzenle
                                                </button>
                                                <button class="btn btn-danger kodSil" 
                                                        data-id="{{ $kod->id }}"
                                                        title="Sil">
                                                    <i class="fa fa-trash"></i> Sil
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

{{-- Arıza Kodu Ekle Modal --}}
<div class="modal fade" id="kodEkleModal" tabindex="-1" role="dialog" aria-labelledby="kodEkleTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="kodEkleTitle">
                    <i class="fa fa-plus-circle"></i> Yeni Arıza Kodu Ekle
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Kapat">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="kodEkleForm" method="POST">
                @csrf
                <input type="hidden" name="marka_id" value="{{ $marka_id }}">
                <input type="hidden" name="model_id" value="{{ $model_id }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="kod">Hata Kodu <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="kod" 
                               id="kod"
                               class="form-control" 
                               placeholder="Örn: E01, F12, C4..." 
                               required>
                    </div>
                    <div class="form-group">
                        <label for="baslik">Başlık</label>
                        <input type="text" 
                               name="baslik" 
                               id="baslik"
                               class="form-control" 
                               placeholder="Kısa açıklama başlığı">
                    </div>
                    <div class="form-group">
                        <label for="aciklama">Açıklama <span class="text-danger">*</span></label>
                        <textarea name="aciklama" 
                                  id="aciklama"
                                  class="form-control" 
                                  rows="5"
                                  placeholder="Detaylı arıza açıklaması ve çözüm önerileri..."
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="kodEkleSubmit">
                        <i class="fa fa-save"></i> Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Arıza Kodu Düzenle Modal --}}
<div class="modal fade" id="kodDuzenleModal" tabindex="-1" role="dialog" aria-labelledby="kodDuzenleTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="kodDuzenleTitle">
                    <i class="fa fa-edit"></i> Arıza Kodu Düzenle
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Kapat">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="kodDuzenleForm" method="POST">
                @csrf
                <input type="hidden" name="kod_id" id="duzenle_kod_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="duzenle_kod">Hata Kodu <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="kod" 
                               id="duzenle_kod"
                               class="form-control" 
                               required>
                    </div>
                    <div class="form-group">
                        <label for="duzenle_baslik">Başlık</label>
                        <input type="text" 
                               name="baslik" 
                               id="duzenle_baslik"
                               class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="duzenle_aciklama">Açıklama <span class="text-danger">*</span></label>
                        <textarea name="aciklama" 
                                  id="duzenle_aciklama"
                                  class="form-control" 
                                  rows="5"
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="kodDuzenleSubmit">
                        <i class="fa fa-save"></i> Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* DataTable özelleştirme - Sadece arama kutusu */
    #kodTable_wrapper .dataTables_filter {
        float: right;
        margin-bottom: 15px;
    }
    #kodTable_wrapper .dataTables_filter label {
        font-weight: normal;
    }
    #kodTable_wrapper .dataTables_filter input {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 6px 12px;
        margin-left: 5px;
        width: 250px;
    }
    #kodTable_wrapper .dataTables_filter input:focus {
        outline: none;
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    /* Modal açılma animasyonu */
    .modal.fade .modal-dialog {
        transition: transform 0.2s ease-out;
    }
    
    /* Modal kapatma butonu */
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
        white-space: nowrap;
    }

    #searchPlaceholder .dataTables_filter input {
        margin-left: 10px !important;
        height: 34px;
        padding: 4px 10px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        outline: none;
        width: 200px;
    }

    #searchPlaceholder .dataTables_filter input:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .kodEkleBtn, .btn-secondary {
        position: relative;
        margin-right: 5px;
    }
    
    .kodEkleBtn i, .btn-secondary i {
        display: none;
    }
    
    @media (max-width: 767px) {
        .kodEkleBtn span, .btn-secondary span {
            display: none;
        }
        .kodEkleBtn i, .btn-secondary i {
            display: block;
        }
    }
</style>

<script>
$(document).ready(function(){
    let table;

    // DataTable başlat
    table = $('#kodTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/tr.json"
        },
        "order": [[0, "asc"]],
        "paging": false,
        "info": false,
        "responsive": true,
        "dom": 'frti',
        "columnDefs": [
            { "orderable": false, "targets": 2 }
        ]
    });
    
    // Arama kutusunu sağ tarafa taşı
    $('#kodTable_filter').appendTo('#searchPlaceholder');

    /** Kod Ekle Modal **/
    $(document).on('click', '.kodEkleBtn', function(){
        $('#kodEkleForm')[0].reset();
        $('#kodEkleModal').modal('show');
    });

    /** Kod Ekle Form Submit **/
    $(document).on('submit', '#kodEkleForm', function(e){
        e.preventDefault();
        
        let kod = $.trim($('#kod').val());
        let aciklama = $.trim($('#aciklama').val());
        
        if (kod.length === 0) {
            alert("Hata kodu boş geçilemez");
            $('#kod').focus();
            return false;
        }
        
        if (aciklama.length === 0) {
            alert("Açıklama boş geçilemez");
            $('#aciklama').focus();
            return false;
        }
        
        let submitBtn = $('#kodEkleSubmit');
        let originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Kaydediliyor...');

        $.ajax({
            url: "{{ route('super.admin.kodlar.store') }}",
            type: "POST",
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(res){
                $('#kodEkleModal').modal('hide');
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

    /** Kod Düzenle Modal **/
    $(document).on('click', '.kodDuzenleBtn', function(){
        let id = $(this).data('id');
        let kod = $(this).data('kod');
        let baslik = $(this).data('baslik');
        let aciklama = $(this).data('aciklama');
        
        $('#duzenle_kod_id').val(id);
        $('#duzenle_kod').val(kod);
        $('#duzenle_baslik').val(baslik);
        $('#duzenle_aciklama').val(aciklama);
        
        $('#kodDuzenleModal').modal('show');
    });

    /** Kod Düzenle Form Submit **/
    $(document).on('submit', '#kodDuzenleForm', function(e){
        e.preventDefault();
        
        let kod = $.trim($('#duzenle_kod').val());
        let aciklama = $.trim($('#duzenle_aciklama').val());
        
        if (kod.length === 0) {
            alert("Hata kodu boş geçilemez");
            $('#duzenle_kod').focus();
            return false;
        }
        
        if (aciklama.length === 0) {
            alert("Açıklama boş geçilemez");
            $('#duzenle_aciklama').focus();
            return false;
        }
        
        let id = $('#duzenle_kod_id').val();
        let submitBtn = $('#kodDuzenleSubmit');
        let originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Güncelleniyor...');

        $.ajax({
            url: "{{ route('super.admin.kodlar.update', '') }}/" + id,
            type: "POST",
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(res){
                $('#kodDuzenleModal').modal('hide');
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

    /** Kod Sil **/
    $(document).on('click', '.kodSil', function(){
        if(!confirm('Bu arıza kodunu silmek istediğinize emin misiniz?')) return;
        
        let id = $(this).data('id');
        let row = $(this).closest('tr');

        $.ajax({
            url: "{{ route('super.admin.kodlar.destroy', '') }}/" + id,
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

    // Modal kapatma olayları
    $(document).on('click', '[data-dismiss="modal"]', function(){
        $(this).closest('.modal').modal('hide');
    });

    $(document).on('click', '.modal', function(e){
        if($(e.target).hasClass('modal')) {
            $(this).modal('hide');
        }
    });

    $(document).on('keydown', function(e){
        if(e.key === 'Escape') {
            $('.modal').modal('hide');
        }
    });

    // Modal kapandığında formu temizle
    $('#kodEkleModal, #kodDuzenleModal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
    });
});
</script>
@endsection