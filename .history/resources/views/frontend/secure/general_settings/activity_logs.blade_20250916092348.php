<!-- resources/views/frontend/secure/general_settings/activity_logs.blade.php -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Sistem Log Kayıtları</h5>
    </div>
    <div class="card-body">
        <!-- Filtreler ve Arama -->
       <div class="row mb-3 align-items-end compact-filters">
    <div class="col-md-2">
        <label class="form-label">Personel</label>
        <select class="form-select" id="user_filter">
            <option value="all">Tümü</option>
            @foreach($users as $user)
                <option value="{{ $user->user_id }}">{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">İşlem Türü</label>
        <select class="form-select" id="action_filter">
            <option value="all">Tümü</option>
            <option value="login">Giriş</option>
            <option value="logout">Çıkış</option>
            <option value="service_created">Servis Oluşturma</option>
            <option value="service_updated">Servis Güncelleme</option>
            <option value="service_plan_added">Servis Aşama Ekleme</option>
            <option value="stock_created">Stok Oluşturma</option>
            <option value="stock_action">Stok Hareketi</option>
            <option value="consignment_created">Konsinye Oluşturma</option>
            <option value="cash_transaction">Kasa İşlemi</option>
            <option value="cash_transaction_updated">Kasa Güncelleme</option>
            <option value="cash_transaction_deleted">Kasa Silme</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Modül</label>
        <select class="form-select" id="module_filter">
            <option value="all">Tümü</option>
            <option value="auth">Giriş-Çıkış</option>
            <option value="service">Servis</option>
            <option value="customer">Müşteri</option>
            <option value="staff">Personel</option>
            <option value="dealer">Bayi</option>
            <option value="stock">Depo(Stok)</option>
            <option value="invoice">Fatura</option>
            <option value="offer">Teklif</option>
            <option value="cash">Kasa</option>
        </select>
    </div>
    <div class="col-md-1">
        <label class="form-label">Başlangıç</label>
        <input type="date" class="form-control" id="start_date" value="{{ date('Y-m-d', strtotime('-30 days')) }}">
    </div>
    <div class="col-md-1">
        <label class="form-label">Bitiş</label>
        <input type="date" class="form-control" id="end_date" value="{{ date('Y-m-d') }}">
    </div>
    <div class="col-md-2">
        <label class="form-label">Arama</label>
        <input type="text" class="form-control" id="search_input" placeholder="IP, kullanıcı, açıklama...">
    </div>
    <div class="col-md-3 d-flex align-items-end gap-1">
        <button class="btn btn-primary btn-sm" onclick="loadLogs()" title="Ara">
            <i class="fas fa-search"></i>
        </button>
        <button class="btn btn-secondary btn-sm" onclick="resetFilters()" title="Temizle">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>
</div>

        <!-- Log Listesi -->
        <div class="table-responsive">
            <div id="loading" class="text-center py-3" style="display: none;">
                <i class="fas fa-spinner fa-spin"></i> Yükleniyor...
            </div>
            
            <div id="log_container">
                <textarea class="form-control" id="log_display" rows="20" readonly style="font-family: monospace; font-size: 12px; background-color: #f8f9fa;"></textarea>
            </div>
        </div>

        <!-- Sayfalama ve Kayıt Sayısı -->
        <div class="row mt-3">
            <div class="col-md-6">
                <nav id="pagination_container"></nav>
            </div>
            <div class="col-md-3">
                <span class="text-muted small" id="log_count">0 kayıt</span>
            </div>
            <div class="col-md-3 text-end">
                <select class="form-select form-select-sm d-inline-block w-auto" id="per_page" onchange="loadLogs()">
                    <option value="50">50 kayıt</option>
                    <option value="100" selected>100 kayıt</option>
                    <option value="200">200 kayıt</option>
                    <option value="500">500 kayıt</option>
                </select>
            </div>
        </div>
    </div>
</div>

<style>
.compact-filters {
    gap: 6px !important;
}
.compact-filters .form-label {
    font-size: 0.75rem;
    margin-bottom: 2px;
    font-weight: 500;
}
.compact-filters .form-select,
.compact-filters .form-control {
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
    height: auto;
}
</style>


<script>
$(document).ready(function() {
    loadLogs();
    
    // Enter tuşu ile arama
    $('#search_input').on('keypress', function(e) {
        if (e.which == 13) {
            loadLogs();
        }
    });
});

function loadLogs(page = 1) {
    const filters = {
        user_id: $('#user_filter').val(),
        start_date: $('#start_date').val(),
        end_date: $('#end_date').val(),
        action: $('#action_filter').val(),
        module: $('#module_filter').val(),
        search: $('#search_input').val(),
        per_page: $('#per_page').val(),
        page: page
    };

    $('#loading').show();
    $('#log_display').val('');

    $.ajax({
        url: `/{{$tenant_id}}/activity-logs/data`,
        type: 'GET',
        data: filters,
        success: function(response) {
            $('#loading').hide();
            
            if (response.success) {
                let logText = '';
                response.data.forEach(function(log) {
                    logText += log.formatted_text + '\n';
                });
                
                $('#log_display').val(logText);
                $('#log_count').text(`${response.pagination.total} kayıt`);
                
                // Sayfalama oluştur
                createPagination(response.pagination);
            } else {
                alert('Loglar yüklenirken hata oluştu: ' + response.message);
            }
        },
        error: function(xhr) {
            $('#loading').hide();
            alert('Loglar yüklenirken hata oluştu: ' + xhr.responseJSON?.message || 'Bilinmeyen hata');
        }
    });
}

function createPagination(pagination) {
    let html = '';
    
    if (pagination.last_page > 1) {
        html += '<ul class="pagination pagination-sm">';
        
        // Önceki sayfa
        if (pagination.current_page > 1) {
            html += `<li class="page-item">
                        <a class="page-link" href="#" onclick="loadLogs(${pagination.current_page - 1})">Önceki</a>
                    </li>`;
        }
        
        // Sayfa numaraları (sadece birkaç sayfa göster)
        let startPage = Math.max(1, pagination.current_page - 2);
        let endPage = Math.min(pagination.last_page, pagination.current_page + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="loadLogs(${i})">${i}</a>
                    </li>`;
        }
        
        // Sonraki sayfa
        if (pagination.current_page < pagination.last_page) {
            html += `<li class="page-item">
                        <a class="page-link" href="#" onclick="loadLogs(${pagination.current_page + 1})">Sonraki</a>
                    </li>`;
        }
        
        html += '</ul>';
    }
    
    $('#pagination_container').html(html);
}

function resetFilters() {
    $('#user_filter').val('all');
    $('#start_date').val('{{ date('Y-m-d', strtotime('-30 days')) }}');
    $('#end_date').val('{{ date('Y-m-d') }}');
    $('#action_filter').val('all');
    $('#module_filter').val('all');
    $('#search_input').val('');
    $('#per_page').val('100');
    loadLogs();
}
</script>