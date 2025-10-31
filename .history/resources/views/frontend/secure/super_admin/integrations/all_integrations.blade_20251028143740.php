@extends('frontend.secure.user_master')
@section('user')

<div class="page-content" id="passwords">
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Entegrasyonlar</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{route('super.admin.dashboard')}}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Tüm Entegrasyonlar</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{route('super.admin.integration.add')}}" class="btn btn-primary"><i class='bx bx-plus'></i> Yeni Entegrasyon</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="card">
        <div class="card-body">
            <div class="d-lg-flex align-items-center mb-4 gap-3">
                <div class="position-relative">
                    <input type="text" class="form-control ps-5 radius-30" id="searchInput" placeholder="Entegrasyon Ara...">
                    <span class="position-absolute top-50 product-show translate-middle-y"><i class="bx bx-search"></i></span>
                </div>

                <div class="position-relative ms-auto">
                    <label>Kategori:</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="">Tümü</option>
                        <option value="payment">Ödeme</option>
                        <option value="email">E-posta</option>
                        <option value="sms">SMS</option>
                        <option value="crm">CRM</option>
                        <option value="accounting">Muhasebe</option>
                        <option value="storage">Depolama</option>
                        <option value="other">Diğer</option>
                    </select>
                </div>

                <div class="ms-2">
                    <label>Başlangıç:</label>
                    <input type="date" class="form-control" id="from_date">
                </div>
                <div class="ms-2">
                    <label>Bitiş:</label>
                    <input type="date" class="form-control" id="to_date">
                </div>
                <div class="ms-2 d-flex align-items-end">
                    <button type="button" class="btn btn-secondary" id="filterBtn">Filtrele</button>
                </div>
                <div class="ms-2 d-flex align-items-end">
                    <button type="button" class="btn btn-warning" id="clearFilterBtn">Temizle</button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="integrationTable" class="table mb-0 mytable">
                    <thead class="table-light">
                        <tr>
                            <th>Id</th>
                            <th>Logo</th>
                            <th>Entegrasyon Adı</th>
                            <th>Kategori</th>
                            <th>Fiyat</th>
                            <th>Durum</th>
                            <th>Oluşturma Tarihi</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Integration Modal -->
<div class="modal fade" id="editIntegrationModal" tabindex="-1" aria-labelledby="editIntegrationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editIntegrationModalLabel">Entegrasyon Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editIntegrationForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="integration_id" name="integration_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Entegrasyon Adı <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_category" name="category" required>
                                <option value="">Seçiniz</option>
                                <option value="payment">Ödeme</option>
                                <option value="email">E-posta</option>
                                <option value="sms">SMS</option>
                                <option value="crm">CRM</option>
                                <option value="accounting">Muhasebe</option>
                                <option value="storage">Depolama</option>
                                <option value="other">Diğer</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Fiyat (₺)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="edit_price" name="price" placeholder="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Durum</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" checked>
                                <label class="form-check-label" for="edit_is_active">Aktif</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Logo</label>
                        <input type="file" class="form-control" id="edit_logo" name="logo" accept="image/*">
                        <small class="text-muted">Mevcut logo güncellenecektir.</small>
                        <div id="current_logo_preview" class="mt-2"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kısa Açıklama</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Detaylı Açıklama</label>
                        <textarea class="form-control" id="edit_explanation" name="explanation" rows="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
$(function(){
    var table = $('#integrationTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('super.admin.integrations') }}",
            data: function (d) {
                d.category = $('#categoryFilter').val();
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
                d.search = $('#searchInput').val();
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'logo', name: 'logo', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'category', name: 'category'},
            {data: 'price', name: 'price'},
            {data: 'status', name: 'is_active'},
            {data: 'created_at', name: 'created_at'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false},
        ],
        "language": {
            "sProcessing": "İşleniyor...",
            "sLengthMenu": "Sayfada _MENU_ Kayıt Göster",
            "sZeroRecords": "Eşleşen Kayıt Bulunmadı",
            "sInfo": "  _TOTAL_ Kayıttan _START_ - _END_ Arası Kayıtlar",
            "sInfoEmpty": "Kayıt Yok",
            "sInfoFiltered": "( _MAX_ Kayıt İçerisinden Bulunan)",
            "sInfoPostFix": "",
            "sSearch": "Bul:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "İlk",
                "sPrevious": "Önceki",
                "sNext": "Sonraki",
                "sLast": "Son"
            }
        },
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tümü"]],
        "pageLength": 10,
        "order": [[0, 'desc']],
        "responsive": true,
        "autoWidth": false,
    });

    // Arama inputu
    $('#searchInput').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Filtre butonu
    $('#filterBtn').on('click', function() {
        table.draw();
    });

    // Filtreleri temizle
    $('#clearFilterBtn').on('click', function() {
        $('#categoryFilter').val('');
        $('#from_date').val('');
        $('#to_date').val('');
        $('#searchInput').val('');
        table.search('').draw();
    });

    // Kategori filtresi değiştiğinde
    $('#categoryFilter').on('change', function() {
        table.draw();
    });

    // Modal açıldığında entegrasyon bilgilerini yükle
    $(document).on('click', '.editIntegration', function() {
        var integrationId = $(this).data('bs-id');
        
        $.ajax({
            url: '/super-admin/integration/get/' + integrationId,
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    var integration = response.integration;
                    
                    $('#integration_id').val(integration.id);
                    $('#edit_name').val(integration.name);
                    $('#edit_category').val(integration.category);
                    $('#edit_price').val(integration.price);
                    $('#edit_description').val(integration.description);
                    $('#edit_explanation').val(integration.explanation);
                    $('#edit_is_active').prop('checked', integration.is_active == 1);
                    
                    // Mevcut logoyu göster
                    if(integration.logo) {
                        $('#current_logo_preview').html('<img src="/' + integration.logo + '" alt="Logo" style="max-width: 150px; max-height: 150px;">');
                    } else {
                        $('#current_logo_preview').html('');
                    }
                    
                    // Form action'ı güncelle
                    $('#editIntegrationForm').attr('action', '/super-admin/integration/update/' + integration.id);
                }
            },
            error: function() {
                toastr.error('Entegrasyon bilgileri yüklenirken hata oluştu!');
            }
        });
    });

    // Form submit
    $('#editIntegrationForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var url = $(this).attr('action');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#editIntegrationModal').modal('hide');
                toastr.success('Entegrasyon başarıyla güncellendi!');
                table.draw();
            },
            error: function(xhr) {
                if(xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]);
                    });
                } else {
                    toastr.error('Bir hata oluştu!');
                }
            }
        });
    });
});

// Silme işlemi
$(document).on("click", "#delete", function(e){
    e.preventDefault();
    var link = $(this).attr("href");

    Swal.fire({
        title: 'Emin misiniz?',
        text: "Bu entegrasyonu silmek istediğinize emin misiniz?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet, Sil!',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = link;
        }
    });
});
</script>

@endsection