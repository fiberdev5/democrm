<!-- resources/views/frontend/secure/general_settings/activity_logs.blade.php -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Sistem Log Kayıtları (Son 7 Gün)</h5>
        <small class="text-muted">
            <i class="fas fa-info-circle"></i> 
            Log kayıtları 7 gün sonra otomatik olarak silinir
        </small>
    </div>
    <div class="card-body">
        <!-- Filtreler ve Arama -->
        <div class="row mb-3">
            <div class="col-md-2">
                <label class="form-label">Personel</label>
                <select class="form-select form-select-sm" id="user_filter">
                    <option value="all">Tüm Personeller</option>
                    @foreach($users as $user)
                        <option value="{{ $user->user_id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label">Başlangıç</label>
                <input type="date" class="form-control form-control-sm" id="start_date" value="{{ date('Y-m-d', strtotime('-7 days')) }}">
            </div>
            <div class="col-md-1">
                <label class="form-label">Bitiş</label>
                <input type="date" class="form-control form-control-sm" id="end_date" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">İşlem Türü</label>
                <select class="form-select form-select-sm" id="action_filter">
                    <option value="all">Tüm İşlemler</option>
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
            <div class="col-md-1">
                <label class="form-label">Modül</label>
                <select class="form-select form-select-sm" id="module_filter">
                    <option value="all">Tümü</option>
                    <option value="auth">Giriş</option>
                    <option value="service">Servis</option>
                    <option value="customer">Müşteri</option>
                    <option value="staff">Personel</option>
                    <option value="dealer">Bayi</option>
                    <option value="stock">Stok</option>
                    <option value="invoice">Fatura</option>
                    <option value="offer">Teklif</option>
                    <option value="cash">Kasa</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Arama</label>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="search_input" placeholder="IP, kullanıcı, servis ID, açıklama...">
                    <button class="btn btn-outline-secondary" onclick="loadLogs()" title="Ara">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary btn-sm me-2" onclick="loadLogs()" title="Filtrele">
                    <i class="fas fa-filter"></i> Filtrele
                </button>
                <button class="btn btn-secondary btn-sm" onclick="resetFilters()" title="Temizle">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>

        <!-- İstatistik Bilgileri -->
        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-muted" id="log_count">0 kayıt</span>
            </div>
            <div class="col-md-6 text-end">
                <select class="form-select form-select-sm d-inline-block w-auto" id="per_page" onchange="loadLogs()">
                    <option value="50">50 kayıt</option>
                    <option value="100" selected>100 kayıt</option>
                    <option value="200">200 kayıt</option>
                    <option value="500">500 kayıt</option>
                </select>
            </div>
        </div>

        <!-- Log Listesi -->
        <div class="table-responsive">
            <div id="loading" class="text-center py-3" style="display: none;">
                <i class="fas fa-spinner fa-spin"></i> Yükleniyor...
            </div>
            
            <div id="log_container">
                <textarea class="form-control" id="log_display" rows="20" readonly 
                          style="font-family: 'Courier New', monospace; font-size: 11px; background-color: #f8f9fa; line-height: 1.4;"></textarea>
            </div>
        </div>

        <!-- Sayfalama -->
        <div class="row mt-3">
            <div class="col-md-12 text-center">
                <nav id="pagination_container"></nav>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadLogs();
    
    // Enter tuşu ile arama
    $('#search_input').on('keypress', function(e) {
        if (e.which == 13) {
            loadLogs();
        }
    });
    
    // Filtre değişikliklerinde otomatik arama (debounce ile)
    let searchTimeout;
    $('#search_input').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadLogs();
        }, 500); // 500ms bekle
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
            alert('Loglar yüklenirken hata oluştu: ' + (xhr.responseJSON?.message || 'Bilinmeyen hata'));
        }
    });
}

function createPagination(pagination) {
    let html = '';
    
    if (pagination.last_page > 1) {
        html += '<ul class="pagination pagination-sm justify-content-center">';
        
        // İlk sayfa
        if (pagination.current_page > 3) {
            html += `<li class="page-item">
                        <a class="page-link" href="#" onclick="loadLogs(1)">1</a>
                    </li>`;
            if (pagination.current_page > 4) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }
        
        // Önceki sayfa
        if (pagination.current_page > 1) {
            html += `<li class="page-item">
                        <a class="page-link" href="#" onclick="loadLogs(${pagination.current_page - 1})">‹</a>
                    </li>`;
        }
        
        // Mevcut sayfa çevresindeki sayfalar
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
                        <a class="page-link" href="#" onclick="loadLogs(${pagination.current_page + 1})">›</a>
                    </li>`;
        }
        
        // Son sayfa
        if (pagination.current_page < pagination.last_page - 2) {
            if (pagination.current_page < pagination.last_page - 3) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            html += `<li class="page-item">
                        <a class="page-link" href="#" onclick="loadLogs(${pagination.last_page})">${pagination.last_page}</a>
                    </li>`;
        }
        
        html += '</ul>';
    }
    
    $('#pagination_container').html(html);
}

function resetFilters() {
    $('#user_filter').val('all');
    $('#start_date').val('{{ date('Y-m-d', strtotime('-7 days')) }}');
    $('#end_date').val('{{ date('Y-m-d') }}');
    $('#action_filter').val('all');
    $('#module_filter').val('all');
    $('#search_input').val('');
    $('#per_page').val('100');
    loadLogs();
}
</script>