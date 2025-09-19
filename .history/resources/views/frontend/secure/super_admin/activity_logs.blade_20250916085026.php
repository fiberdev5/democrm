<!-- resources/views/secure/super_admin/activity_logs.blade.php -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Sistem Log Kayıtları (Son 7 Gün)</h5>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> 
                Log kayıtları 7 gün sonra otomatik olarak silinir
            </small>
        </div>
        <div class="card-body">
            <!-- Filtreler -->
            <div class="row mb-3">
             <div class="row mb-3">
                <div class="col-md-2">
                    <label class="form-label">Firma</label>
                    <select class="form-select" id="tenant_filter">
                        <option value="">Tüm Firmalar</option>
                        @foreach($users->unique('tenant_id') as $user)
                            @if($user->tenant)
                                <option value="{{ $user->tenant->id }}">{{ $user->tenant->firma_adi }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Kullanıcı</label>
                    <select class="form-select" id="user_filter">
                        <option value="all">Tüm Kullanıcılar</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" data-tenant="{{ $user->tenant_id }}">
                                {{ $user->name }} @if($user->tenant)({{ $user->tenant->firma_adi }})@endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Başlangıç Tarihi</label>
                    <input type="date" class="form-control" id="start_date" value="{{ date('Y-m-d', strtotime('-7 days')) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Bitiş Tarihi</label>
                    <input type="date" class="form-control" id="end_date" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">İşlem Türü</label>
                    <select class="form-select" id="action_filter">
                        <option value="all">Tüm İşlemler</option>
                        <option value="login">Giriş</option>
                        <option value="logout">Çıkış</option>
                        <option value="login_failed">Başarısız Giriş</option>
                        <option value="service_created">Servis Oluşturma</option>
                        <option value="service_updated">Servis Güncelleme</option>
                        <option value="service_plan_added">Servis Aşama Ekleme</option>
                        {{-- <option value="customer_created">Müşteri Oluşturma</option> --}}
                        <option value="stock_created">Stok Oluşturma</option>
                        <option value="stock_action">Stok Hareketi</option>
                        <option value="consignment_created">Konsinye Oluşturma</option>
                        <option value="cash_transaction">Kasa İşlemi</option>
                        <option value="cash_transaction_updated">Kasa Güncelleme</option>
                        <option value="cash_transaction_deleted">Kasa Silme</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary me-2" onclick="loadLogs()" title="Ara">
                        <i class="fas fa-search"></i>
                    </button>
                    <button class="btn btn-secondary" onclick="resetFilters()" title="Temizle">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>

            <!-- Arama ve İstatistikler -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" id="search_input" placeholder="IP, kullanıcı adı, firma veya açıklama ile arama yapın...">
                        <button class="btn btn-outline-secondary" onclick="loadLogs()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <span class="text-muted" id="log_count">0 kayıt</span>
                    
                </div>
            </div>

            <!-- Log Listesi -->
            <div class="table-responsive">
                <div id="loading" class="text-center py-3" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i> Yükleniyor...
                </div>
                
                <div id="log_container">
                    <textarea class="form-control" id="log_display" rows="25" readonly 
                              style="font-family: 'Courier New', monospace; font-size: 11px; padding: 15px;"></textarea>
                </div>
            </div>

            <!-- Sayfalama -->
            <div class="row mt-3">
                <div class="col-md-6">
                    <nav id="pagination_container"></nav>
                </div>
                <div class="col-md-6 text-end">
                    <select class="form-select d-inline-block w-auto" id="per_page" onchange="loadLogs()">
                        <option value="50">50 kayıt</option>
                        <option value="100" selected>100 kayıt</option>
                        <option value="200">200 kayıt</option>
                        <option value="500">500 kayıt</option>
                        <option value="1000">1000 kayıt</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Log Temizleme Modal -->
{{-- <div class="modal fade" id="clearLogsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Kayıtlarını Temizle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Temizleme Seçeneği</label>
                    <select class="form-select" id="clear_option">
                        <option value="older_than">Belirli günden eski kayıtları sil</option>
                        <option value="specific_tenant">Belirli firmaya ait kayıtları sil</option>
                        <option value="all">Tüm kayıtları sil (DİKKAT!)</option>
                    </select>
                </div>
                
                <div id="days_option" class="mb-3">
                    <label class="form-label">Kaç günden eski kayıtlar silinsin?</label>
                    <select class="form-select" id="older_than_days">
                        <option value="30">30 günden eski</option>
                        <option value="60">60 günden eski</option>
                        <option value="90">90 günden eski</option>
                        <option value="180">180 günden eski</option>
                        <option value="365">1 yıldan eski</option>
                    </select>
                </div>

                <div id="tenant_option" class="mb-3" style="display: none;">
                    <label class="form-label">Hangi firma?</label>
                    <select class="form-select" id="clear_tenant_id">
                        <option value="">Firma Seçin</option>
                        @foreach($users->unique('tenant_id') as $user)
                            @if($user->tenant)
                                <option value="{{ $user->tenant->id }}">{{ $user->tenant->firma_adi }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Bu işlem geri alınamaz! Silinen log kayıtları tamamen kaybolacaktır.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-danger" onclick="clearLogs()">
                    <i class="fas fa-trash"></i> Temizle
                </button>
            </div>
        </div>
    </div>
</div> --}}

<script>
$(document).ready(function() {
    loadLogs();
    
    // Enter tuşu ile arama
    $('#search_input').on('keypress', function(e) {
        if (e.which == 13) {
            loadLogs();
        }
    });

    // Firma filtresine göre kullanıcıları filtrele
    $('#tenant_filter').on('change', function() {
        const tenantId = $(this).val();
        const userFilter = $('#user_filter');
        
        userFilter.find('option').each(function() {
            const option = $(this);
            if (option.val() === 'all') return;
            
            if (!tenantId || option.data('tenant') == tenantId) {
                option.show();
            } else {
                option.hide();
            }
        });
        
        userFilter.val('all');
    });

});

function loadLogs(page = 1) {
    const filters = {
        tenant_id: $('#tenant_filter').val(),
        user_id: $('#user_filter').val(),
        start_date: $('#start_date').val(),
        end_date: $('#end_date').val(),
        action: $('#action_filter').val(),
        search: $('#search_input').val(),
        per_page: $('#per_page').val(),
        page: page
    };

    $('#loading').show();
    $('#log_display').val('');

    $.ajax({
        url: `/super-admin/activity-logs/data`,
        type: 'GET',
        data: filters,
        success: function(response) {
            $('#loading').hide();
            
            if (response.success) {
                let logText = '';
                response.data.forEach(function(log) {
                    const firmaBilgisi = log.tenant_name ? ` [${log.tenant_name}] ` : ' ';
                    logText += log.ip_address + ' - ' + (log.user_id || '') + ' - ' + 
                              (log.user_role || '') + ' - ' + log.date + ' -' + firmaBilgisi + 
                              log.description + '\n';
                });
                
                $('#log_display').val(logText);
                $('#log_count').text(`${response.pagination.total} kayıt`);
                
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
        html += '<ul class="pagination pagination-sm">';
        
        if (pagination.current_page > 1) {
            html += `<li class="page-item">
                        <a class="page-link" href="#" onclick="loadLogs(${pagination.current_page - 1})">Önceki</a>
                    </li>`;
        }
        
        let startPage = Math.max(1, pagination.current_page - 2);
        let endPage = Math.min(pagination.last_page, pagination.current_page + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="loadLogs(${i})">${i}</a>
                    </li>`;
        }
        
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
    $('#tenant_filter').val('');
    $('#user_filter').val('all');
    $('#start_date').val('{{ date('Y-m-d', strtotime('-7 days')) }}');
    $('#end_date').val('{{ date('Y-m-d') }}');
    $('#action_filter').val('all');
    $('#search_input').val('');
    $('#per_page').val('100');
    
    // Kullanıcı filtresini sıfırla
    $('#user_filter option').show();
    
    loadLogs();
}

function showClearModal() {
    $('#clearLogsModal').modal('show');
}

function clearLogs() {
    const option = $('#clear_option').val();
    let data = {};
    
    if (option === 'older_than') {
        data.older_than_days = $('#older_than_days').val();
    } else if (option === 'specific_tenant') {
        data.tenant_id = $('#clear_tenant_id').val();
        if (!data.tenant_id) {
            alert('Lütfen bir firma seçin.');
            return;
        }
    } else if (option === 'all') {
        if (!confirm('TÜM LOG KAYITLARI SİLİNECEK! Emin misiniz?')) {
            return;
        }
        data.clear_all = true;
    }

    $.ajax({
        url: '/super-admin/activity-logs/clear',
        type: 'POST',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                alert(response.message);
                $('#clearLogsModal').modal('hide');
                loadLogs();
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function(xhr) {
            alert('Temizleme sırasında hata oluştu: ' + (xhr.responseJSON?.message || 'Bilinmeyen hata'));
        }
    });
}

function exportLogs() {
    // Mevcut filtreleri al ve export et
    const filters = {
        tenant_id: $('#tenant_filter').val(),
        user_id: $('#user_filter').val(),
        start_date: $('#start_date').val(),
        end_date: $('#end_date').val(),
        action: $('#action_filter').val(),
        search: $('#search_input').val(),
        export: true
    };
    
    // Export URL'si oluştur
    const queryString = new URLSearchParams(filters).toString();
    window.open(`/super-admin/activity-logs/export?${queryString}`, '_blank');
}
</script>