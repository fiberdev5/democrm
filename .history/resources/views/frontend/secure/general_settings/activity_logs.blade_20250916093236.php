<!-- resources/views/frontend/secure/general_settings/activity_logs.blade.php -->
<!-- Moment.js (Date Range Picker için gereklidir) -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<!-- Date Range Picker -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Sistem Log Kayıtları</h5>
    </div>
    <div class="card-body">
        <!-- Filtreler ve Arama (Tek Satır) -->
        <div class="row mb-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label">Personel</label>
                <select class="form-select form-select-sm" id="user_filter">
                    <option value="all">Tüm Personeller</option>
                    @foreach($users as $user)
                        <option value="{{ $user->user_id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tarih Aralığı</label>
                <input type="text" class="form-control form-control-sm" id="date_range_filter"/>
            </div>
            <div class="col-md-2">
                <label class="form-label">İşlem Türü</label>
                <select class="form-select form-select-sm" id="action_filter">
                    <option value="all">Tüm İşlemler</option>
                    <option value="login">Giriş</option>
                    <option value="logout">Çıkış</option>
                    <option value="service_created">Servis Oluşturma</option>
                    <option value="stock_action">Stok Hareketi</option>
                    <option value="cash_transaction">Kasa İşlemi</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Modül</label>
                <select class="form-select form-select-sm" id="module_filter">
                    <option value="all">Tüm Modüller</option>
                    <option value="auth">Giriş-Çıkış</option>
                    <option value="service">Servis</option>
                    <option value="stock">Depo(Stok)</option>
                    <option value="cash">Kasa</option>
                </select>
            </div>
            <div class="col-md-3">
                 <label class="form-label">Arama</label>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="search_input" placeholder="IP, kullanıcı adı veya açıklama...">
                    <button class="btn btn-outline-secondary" onclick="loadLogs()" title="Ara"><i class="fas fa-search"></i></button>
                </div>
            </div>
            <div class="col-auto">
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
        <div class="row mt-3 align-items-center">
            <div class="col-md-5">
                <nav id="pagination_container"></nav>
            </div>
            <div class="col-md-2 text-center">
                <span class="text-muted" id="log_count">0 kayıt</span>
            </div>
            <div class="col-md-5 text-end">
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

<script>
$(document).ready(function() {
    // Date Range Picker'ı başlat
    const startDate = moment().subtract(29, 'days');
    const endDate = moment();

    $('#date_range_filter').daterangepicker({
        startDate: startDate,
        endDate: endDate,
        ranges: {
           'Bugün': [moment(), moment()],
           'Dün': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Son 7 Gün': [moment().subtract(6, 'days'), moment()],
           'Son 30 Gün': [moment().subtract(29, 'days'), moment()],
           'Bu Ay': [moment().startOf('month'), moment().endOf('month')],
           'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: {
            format: 'YYYY-MM-DD',
            applyLabel: 'Uygula',
            cancelLabel: 'İptal',
            fromLabel: 'Başlangıç',
            toLabel: 'Bitiş',
            customRangeLabel: 'Özel Aralık',
            daysOfWeek: ['Pa', 'Pt', 'Sa', 'Ça', 'Pe', 'Cu', 'Ct'],
            monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
        }
    });
    
    loadLogs();
    
    // Enter tuşu ile arama
    $('#search_input').on('keypress', function(e) {
        if (e.which == 13) {
            loadLogs();
        }
    });
});

function loadLogs(page = 1) {
    // Tarih aralığı seçicisinden başlangıç ve bitiş tarihlerini al
    const startDate = $('#date_range_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
    const endDate = $('#date_range_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

    const filters = {
        user_id: $('#user_filter').val(),
        start_date: startDate, // Güncellendi
        end_date: endDate,     // Güncellendi
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
        html += '<ul class="pagination pagination-sm mb-0">';
        
        if (pagination.current_page > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="event.preventDefault(); loadLogs(${pagination.current_page - 1})">Önceki</a></li>`;
        }
        
        let startPage = Math.max(1, pagination.current_page - 2);
        let endPage = Math.min(pagination.last_page, pagination.current_page + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}"><a class="page-link" href="#" onclick="event.preventDefault(); loadLogs(${i})">${i}</a></li>`;
        }
        
        if (pagination.current_page < pagination.last_page) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="event.preventDefault(); loadLogs(${pagination.current_page + 1})">Sonraki</a></li>`;
        }
        
        html += '</ul>';
    }
    
    $('#pagination_container').html(html);
}

function resetFilters() {
    $('#user_filter').val('all');
    $('#action_filter').val('all');
    $('#module_filter').val('all');
    $('#search_input').val('');
    $('#per_page').val('100');
    
    // Tarih aralığını varsayılan değere (son 30 gün) sıfırla
    const drp = $('#date_range_filter').data('daterangepicker');
    drp.setStartDate(moment().subtract(29, 'days'));
    drp.setEndDate(moment());

    loadLogs();
}
</script>