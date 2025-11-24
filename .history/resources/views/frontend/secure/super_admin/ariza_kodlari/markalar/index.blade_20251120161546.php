@extends('frontend.secure.user_master')

@section('user')
<!-- DataTables Bootstrap CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
<!-- FontAwesome (İkonlar için - eğer projenizde yoksa) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    /* Tabloyu biraz daha ferahlatmak için özel stil */
    .table td, .table th { vertical-align: middle; }
    .action-btn { margin-right: 5px; }
    .marka-img { width: 40px; height: 40px; object-fit: contain; border-radius: 4px; border: 1px solid #dee2e6; background: #fff; }
</style>

<div class="container-fluid mt-3">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="m-0 font-weight-bold text-primary">Marka Listesi</h5>
                <button type="button" class="btn btn-success btn-sm shadow-sm markaEkleBtn">
                    <i class="fas fa-plus"></i> Yeni Marka Ekle
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="10%">Resim</th>
                            <th>Marka Adı</th>
                            <th width="30%">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($markalar as $marka)
                        <tr>
                            <td class="text-center">
                                @if($marka->resimyol)
                                    <img src="{{ asset('upload/'.$marka->resimyol) }}" class="marka-img" alt="Marka Logo">
                                @else
                                    <span class="badge badge-secondary">Yok</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $marka->marka }}</strong>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('super.admin.modeller.index', $marka->id) }}" class="btn btn-info btn-sm action-btn" title="Modeller">
                                        <i class="fas fa-car"></i> <span class="d-none d-md-inline">Modeller</span>
                                    </a>
                                    <a href="{{ route('super.admin.kodlar.index', ['marka_id' => $marka->id, 'model_id' => 0]) }}" class="btn btn-warning btn-sm action-btn text-white" title="Arıza Kodları">
                                        <i class="fas fa-code"></i> <span class="d-none d-md-inline">Kodlar</span>
                                    </a>
                                    <button type="button" data-id="{{ $marka->id }}" class="btn btn-primary btn-sm action-btn markaDuzenleBtn" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" data-id="{{ $marka->id }}" class="btn btn-danger btn-sm action-btn markaSil" title="Sil">
                                        <i class="fas fa-trash"></i>
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

<!-- Marka Ekle Modal -->
<div class="modal fade" id="markaEkleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Yeni Marka Ekle</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Kapat">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <!-- İçerik AJAX ile gelecek -->
                <div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Marka Düzenle Modal -->
<div class="modal fade" id="markaDuzenleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Marka Düzenle</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Kapat">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <!-- İçerik AJAX ile gelecek -->
                <div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Jquery ve Bootstrap JS (Zaten projenizde varsa tekrar eklemeyin) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    
    // CSRF Token Ayarı
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // DataTables Başlatma (Türkçe Dil Desteğiyle)
    $('#dataTable').DataTable({
        responsive: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json"
        },
        columnDefs: [
            { orderable: false, targets: [0, 2] } // Resim ve İşlemler sütununda sıralamayı kapat
        ],
        order: [[ 1, 'asc' ]] // Marka adına göre sırala
    });
    
    // --- EKLEME İŞLEMLERİ ---
    $(document).on('click', '.markaEkleBtn', function() {
        $('#markaEkleModal .modal-body').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i><br>Yükleniyor...</div>');
        $('#markaEkleModal').modal('show');
        
        $.get("{{ route('super.admin.markalar.create') }}", function(data) {
            $('#markaEkleModal .modal-body').html(data);
        });
    });
    
    // Ekleme Form Submit
    $(document).on('submit', '#markaEkle', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        
        // Basit Validasyon
        if ($.trim($(this).find('input[name="marka"]').val()) == "") {
            alert("Marka adı boş bırakılamaz!");
            return false;
        }
        
        $.ajax({
            url: "{{ route('super.admin.markalar.store') }}",
            type: "POST",
            data: formData,
            contentType: false, // Dosya yükleme için gerekli
            processData: false, // Dosya yükleme için gerekli
            success: function(response) {
                $('#markaEkleModal').modal('hide');
                alert(response.message); // veya toastr.success(response.message);
                location.reload(); // Tabloyu yenilemek için
            },
            error: function(xhr) {
                alert("Hata oluştu: " + (xhr.responseJSON.message || "Bilinmeyen hata"));
            }
        });
    });
    
    // --- DÜZENLEME İŞLEMLERİ ---
    $(document).on('click', '.markaDuzenleBtn', function() {
        var id = $(this).data('id');
        $('#markaDuzenleModal .modal-body').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i><br>Veriler getiriliyor...</div>');
        $('#markaDuzenleModal').modal('show');
        
        $.get("{{ route('super.admin.markalar.edit', '') }}/" + id, function(data) {
            $('#markaDuzenleModal .modal-body').html(data);
        });
    });

    // Düzenleme Form Submit
    $(document).on('submit', '#markaDuzenle', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        
   
        var url = $(this).attr('action');

        $.ajax({
            url: url,
            type: "POST", // Laravel'de PUT işlemleri için method spoofing kullanılır (_method: PUT)
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#markaDuzenleModal').modal('hide');
                alert(response.message);
                location.reload();
            },
            error: function(xhr) {
                alert("Hata oluştu: " + (xhr.responseJSON.message || "Güncelleme başarısız"));
            }
        });
    });
    
    // --- SİLME İŞLEMİ ---
    $(document).on('click', '.markaSil', function() {
        if(confirm("Bu markayı ve bağlı resmi silmek istediğinize emin misiniz?")) {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('super.admin.markalar.destroy', '') }}/" + id,
                type: "DELETE", // Resource controller standartı DELETE
                data: {
                    _token: "{{ csrf_token() }}" // Delete için token şart
                },
                success: function(response) {
                    alert(response.message);
                    location.reload();
                },
                error: function(xhr) {
                    alert("Silme işlemi başarısız.");
                }
            });
        }
    });
});
</script>
@endsection