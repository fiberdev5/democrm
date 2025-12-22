@extends('frontend.secure.user_master')
@section('user')
<div class="page-content">
    <div class="container-fluid">
        
        <!-- Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Ana Sayfa Yönetimi</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Ana Sayfa</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#stats" role="tab">
                                    <i class="fas fa-chart-line me-1"></i> İstatistikler
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#modules" role="tab">
                                    <i class="fas fa-cube me-1"></i> Modüller
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#sectors" role="tab">
                                    <i class="fas fa-industry me-1"></i> Sektörler
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#integrations" role="tab">
                                    <i class="fas fa-plug me-1"></i> Entegrasyonlar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#testimonials" role="tab">
                                    <i class="fas fa-quote-left me-1"></i> Yorumlar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#faqs" role="tab">
                                    <i class="fas fa-question-circle me-1"></i> SSS
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content p-3">
                            <!-- İstatistikler Tab -->
                            <div class="tab-pane active" id="stats" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>İstatistikler</h5>
                                    <button class="btn btn-primary btn-sm" onclick="addStat()">
                                        <i class="fas fa-plus me-1"></i> Yeni Ekle
                                    </button>
                                </div>
                                
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle"> <!-- align-middle tüm satırı dikey ortalar -->
        <thead class="table-light">
            <tr>
                <th width="50" class="text-center">Sıra</th>
                <th>Sayı</th>
                <th>Etiket</th>
                <!-- Başlıkları ortalıyoruz -->
                <th width="120" class="text-center">Durum</th>
                <th width="120" class="text-center">İşlemler</th>
            </tr>
        </thead>
        <tbody id="statsTable">
            @foreach($stats as $stat)
            <tr>
                <!-- Sıra Numarası Ortalandı -->
                <td class="text-center fw-bold">{{ $stat->order }}</td>
                
                <td>{{ $stat->data['number'] ?? '' }}</td>
                <td>{{ $stat->data['label'] ?? '' }}</td>
                
                <!-- Durum Kısmı: Ortalandı ve Badge Tasarımı İyileştirildi -->
                <td class="text-center">
                    <span class="badge rounded-pill bg-{{ $stat->is_active ? 'success' : 'danger' }} font-size-12" style="min-width: 60px;">
                        {{ $stat->is_active ? 'Aktif' : 'Pasif' }}
                    </span>
                </td>
                
                <!-- İşlemler Kısmı: Ortalandı ve Butonlar Şeffaf Yapıldı -->
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-3"> <!-- Butonları yanyana ve ortalı tutar -->
                        <button type="button" 
                                class="btn btn-link p-0" 
                                onclick="editStat({{ $stat->id }})" 
                                data-bs-toggle="tooltip" 
                                title="Düzenle">
                            <i class="fas fa-edit text-warning" style="font-size: 18px;"></i>
                        </button>
                        
                        <button type="button" 
                                class="btn btn-link p-0" 
                                onclick="deleteStat({{ $stat->id }})" 
                                data-bs-toggle="tooltip" 
                                title="Sil">
                            <i class="fas fa-trash-alt text-danger" style="font-size: 18px;"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
                            </div>

                            <!-- Diğer tablar için benzer yapı -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- İstatistik Modal -->
<div class="modal fade" id="statModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statModalLabel">İstatistik Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statForm">
                <div class="modal-body">
                    <input type="hidden" id="stat_id" name="id">
                    
                    <div class="mb-3">
                        <label class="form-label">Sıra</label>
                        <input type="number" class="form-control" id="stat_order" name="order" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Sayı</label>
                        <input type="text" class="form-control" id="stat_number" name="number" placeholder="500+" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Etiket</label>
                        <input type="text" class="form-control" id="stat_label" name="label" placeholder="Aktif Firma" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Durum</label>
                        <select class="form-control" id="stat_status" name="is_active">
                            <option value="1">Aktif</option>
                            <option value="0">Pasif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
// İstatistik Ekleme
function addStat() {
    $('#statModalLabel').text('İstatistik Ekle');
    $('#statForm')[0].reset();
    $('#stat_id').val('');
    $('#statModal').modal('show');
}

// İstatistik Düzenleme
function editStat(id) {
    $.ajax({
        url: `/super-admin/frontend/home/stat/${id}`,
        method: 'GET',
        success: function(response) {
            $('#statModalLabel').text('İstatistik Düzenle');
            $('#stat_id').val(response.id);
            $('#stat_order').val(response.order);
            $('#stat_number').val(response.data.number);
            $('#stat_label').val(response.data.label);
            $('#stat_status').val(response.is_active ? 1 : 0);
            $('#statModal').modal('show');
        }
    });
}

// İstatistik Kaydetme
$('#statForm').on('submit', function(e) {
    e.preventDefault();
    
    const id = $('#stat_id').val();
    const url = id ? `/super-admin/frontend/home/stat/${id}` : '/super-admin/frontend/home/stat';
    const method = id ? 'PUT' : 'POST';
    
    const data = {
        section: 'home_stats',
        order: $('#stat_order').val(),
        is_active: $('#stat_status').val(),
        data: {
            number: $('#stat_number').val(),
            label: $('#stat_label').val()
        }
    };
    
    $.ajax({
        url: url,
        method: method,
        data: JSON.stringify(data),
        contentType: 'application/json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $('#statModal').modal('hide');
            toastr.success('İstatistik başarıyla kaydedildi');
            location.reload();
        },
        error: function(xhr) {
            toastr.error('Bir hata oluştu');
        }
    });
});

// İstatistik Silme
function deleteStat(id) {
    Swal.fire({
        title: 'Emin misiniz?',
        text: "Bu işlem geri alınamaz!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet, sil!',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/super-admin/frontend/home/stat/${id}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success('İstatistik silindi');
                    location.reload();
                }
            });
        }
    });
}
</script>
@endsection
