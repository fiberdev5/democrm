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
                                    <button class="btn btn-info btn-sm" onclick="addStat()">
                                        <i class="fas fa-plus me-1"></i> Yeni Ekle
                                    </button>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle"> 
                                        <thead class="table-dark">
                                            <tr>
                                                <th width="50" class="text-center">Sıra</th>
                                                <th>Sayı</th>
                                                <th>Etiket</th>
                                                <th width="120" class="text-center">Durum</th>
                                                <th width="120" class="text-center">İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody id="statsTable">
                                            @foreach($stats as $stat)
                                            <tr>
                                                <td class="text-center fw-bold">{{ $stat->order }}</td>
                                                <td>{{ $stat->data['number'] ?? '' }}</td>
                                                <td>{{ $stat->data['label'] ?? '' }}</td>
                                        
                                                <td class="text-center">
                                                    <span class="badge rounded-pill bg-{{ $stat->is_active ? 'success' : 'danger' }} font-size-12" style="min-width: 60px;">
                                                        {{ $stat->is_active ? 'Aktif' : 'Pasif' }}
                                                    </span>
                                                </td>
                                                
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-3"> 
                                                        <button type="button" 
                                                                class="btn btn-link p-0" 
                                                                onclick="editStat({{ $stat->id }})" 
                                                                data-bs-toggle="tooltip" 
                                                                title="Düzenle">
                                                            <i class="fas fa-edit text-warning" ></i>
                                                        </button>
                                                        
                                                        <button type="button" 
                                                                class="btn btn-link p-0" 
                                                                onclick="deleteStat({{ $stat->id }})" 
                                                                data-bs-toggle="tooltip" 
                                                                title="Sil">
                                                            <i class="fas fa-trash-alt text-danger"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Modüller Tab -->
                                <div class="tab-pane" id="modules" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5>Modüller / Özellikler</h5>
                                        <button class="btn btn-info btn-sm" onclick="addModule()">
                                            <i class="fas fa-plus me-1"></i> Yeni Ekle
                                        </button>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover align-middle">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th width="50" class="text-center">Sıra</th>
                                                    <th width="80" class="text-center">İkon</th>
                                                    <th>Başlık</th>
                                                    <th>Açıklama</th>
                                                    <th width="100" class="text-center">Renk</th>
                                                    <th width="120" class="text-center">Durum</th>
                                                    <th width="120" class="text-center">İşlemler</th>
                                                </tr>
                                            </thead>
                                            <tbody id="modulesTable">
                                                @foreach($modules as $module)
                                                <tr>
                                                    <td class="text-center fw-bold">{{ $module->order }}</td>
                                                    <td class="text-center">
                                                        <i class="{{ $module->data['icon'] ?? '' }}" style="color: {{ $module->data['color'] == 'orange' ? '#f37021' : '#49657B' }};"></i>
                                                    </td>
                                                    <td>{{ $module->data['title'] ?? '' }}</td>
                                                    <td>{{ Str::limit($module->data['description'] ?? '', 60) }}</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-{{ $module->data['color'] == 'orange' ? 'warning' : 'primary' }}">
                                                            {{ $module->data['color'] == 'orange' ? 'Turuncu' : 'Mavi' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge rounded-pill bg-{{ $module->is_active ? 'success' : 'danger' }} font-size-12" style="min-width: 60px;">
                                                            {{ $module->is_active ? 'Aktif' : 'Pasif' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-3">
                                                            <button type="button" 
                                                                    class="btn btn-link p-0" 
                                                                    onclick="editModule({{ $module->id }})" 
                                                                    data-bs-toggle="tooltip" 
                                                                    title="Düzenle">
                                                                <i class="fas fa-edit text-warning"></i>
                                                            </button>
                                                            
                                                            <button type="button" 
                                                                    class="btn btn-link p-0" 
                                                                    onclick="deleteModule({{ $module->id }})" 
                                                                    data-bs-toggle="tooltip" 
                                                                    title="Sil">
                                                                <i class="fas fa-trash-alt text-danger"></i>
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
<!-- Modül Modal -->
<div class="modal fade" id="moduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moduleModalLabel">Modül Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="moduleForm">
                <div class="modal-body">
                    <input type="hidden" id="module_id" name="id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sıra</label>
                            <input type="number" class="form-control" id="module_order" name="order" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Renk</label>
                            <select class="form-control" id="module_color" name="color" required>
                                <option value="blue">Mavi</option>
                                <option value="orange">Turuncu</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">İkon (FontAwesome)</label>
                        <input type="text" class="form-control" id="module_icon" name="icon" placeholder="fas fa-users" required>
                        <small class="text-muted">Örnek: fas fa-users, fas fa-boxes, fas fa-chart-line</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Başlık</label>
                        <input type="text" class="form-control" id="module_title" name="title" placeholder="Müşteri Yönetimi" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea class="form-control" id="module_description" name="description" rows="3" placeholder="Modül açıklaması..." required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Durum</label>
                        <select class="form-control" id="module_status" name="is_active">
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
// Sayfa yüklendiğinde aktif tab'ı kontrol et
$(document).ready(function() {
    // URL'den hash'i al
    let hash = window.location.hash;
    if (hash) {
        // Hash varsa o tab'ı aktif yap
        $('.nav-tabs a[href="' + hash + '"]').tab('show');
    }
    
    // Tab değiştiğinde URL hash'ini güncelle
    $('.nav-tabs a').on('shown.bs.tab', function(e) {
        window.location.hash = e.target.hash;
    });
});
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
// ============ MODÜLLER ============

// Modül Ekleme
function addModule() {
    $('#moduleModalLabel').text('Modül Ekle');
    $('#moduleForm')[0].reset();
    $('#module_id').val('');
    $('#moduleModal').modal('show');
}

// Modül Düzenleme
function editModule(id) {
    $.ajax({
        url: `/super-admin/frontend/home/module/${id}`,
        method: 'GET',
        success: function(response) {
            $('#moduleModalLabel').text('Modül Düzenle');
            $('#module_id').val(response.id);
            $('#module_order').val(response.order);
            $('#module_icon').val(response.data.icon);
            $('#module_title').val(response.data.title);
            $('#module_description').val(response.data.description);
            $('#module_color').val(response.data.color);
            $('#module_status').val(response.is_active ? 1 : 0);
            $('#moduleModal').modal('show');
        },
        error: function(xhr) {
            toastr.error('Modül bilgileri alınamadı');
        }
    });
}

// Modül Kaydetme
$('#moduleForm').on('submit', function(e) {
    e.preventDefault();
    
    const id = $('#module_id').val();
    const url = id ? `/super-admin/frontend/home/module/${id}` : '/super-admin/frontend/home/module';
    const method = id ? 'PUT' : 'POST';
    
    const data = {
        section: 'home_modules',
        order: $('#module_order').val(),
        is_active: $('#module_status').val(),
        data: {
            icon: $('#module_icon').val(),
            title: $('#module_title').val(),
            description: $('#module_description').val(),
            color: $('#module_color').val()
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
            $('#moduleModal').modal('hide');
            toastr.success('Modül başarıyla kaydedildi');
            location.reload();
        },
        error: function(xhr) {
            toastr.error('Bir hata oluştu');
        }
    });
});

// Modül Silme
function deleteModule(id) {
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
                url: `/super-admin/frontend/home/module/${id}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success('Modül silindi');
                    location.reload();
                },
                error: function(xhr) {
                    toastr.error('Silme işlemi başarısız');
                }
            });
        }
    });
}
</script>
@endsection
