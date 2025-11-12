@extends('frontend.secure.user_master')
@section('user')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="page-title mb-0">
                        <i class="fas fa-sync-alt"></i> Müşterileri Hipcall'a Gönder
                    </h4>
                    <div>
                        <button type="button" class="btn btn-primary" onclick="syncSelectedCustomers()">
                            <i class="fas fa-check"></i> Seçilenleri Gönder
                        </button>
                        <button type="button" class="btn btn-success" onclick="syncAllCustomers()">
                            <i class="fas fa-users"></i> Tümünü Gönder
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Bilgilendirme:</strong> Müşterileriniz Hipcall rehberine gönderilecek.
                            Telefon numarası olan <strong>{{ count($customers) }}</strong> müşteri bulundu.
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                                <i class="fas fa-check-square"></i> Tümünü Seç
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                                <i class="fas fa-square"></i> Seçimi Kaldır
                            </button>
                            <span class="ms-3 text-muted">
                                <span id="selectedCount">0</span> müşteri seçili
                            </span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="selectAllCheckbox" onclick="toggleAll(this)">
                                        </th>
                                        <th>Ad Soyad</th>
                                        <th>Telefon</th>
                                        <th>E-posta</th>
                                        <th>Firma</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customers as $customer)
                                    <tr>
                                        <td>
                                            <input type="checkbox" 
                                                   class="customer-checkbox" 
                                                   value="{{ $customer->id }}"
                                                   onchange="updateSelectedCount()">
                                        </td>
                                        <td>{{ $customer->adSoyad }} </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $customer->tel1 ?? $customer->telefon }}
                                            </span>
                                        </td>
                                        <td>{{ $customer->eposta ?? '-' }}</td>
                                        <td>{{ $customer->firma ?? '-' }}</td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-sm btn-primary" 
                                                    onclick="syncSingle({{ $customer->id }})">
                                                <i class="fas fa-paper-plane"></i> Gönder
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            Telefon numarası olan müşteri bulunamadı
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Sonuç Modal -->
        <div class="modal fade" id="resultsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Senkronizasyon Sonucu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="resultsContent">
                        <!-- Dinamik içerik -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Seçim işlemleri
function toggleAll(checkbox) {
    document.querySelectorAll('.customer-checkbox').forEach(cb => {
        cb.checked = checkbox.checked;
    });
    updateSelectedCount();
}

function selectAll() {
    document.querySelectorAll('.customer-checkbox').forEach(cb => {
        cb.checked = true;
    });
    document.getElementById('selectAllCheckbox').checked = true;
    updateSelectedCount();
}

function deselectAll() {
    document.querySelectorAll('.customer-checkbox').forEach(cb => {
        cb.checked = false;
    });
    document.getElementById('selectAllCheckbox').checked = false;
    updateSelectedCount();
}

function updateSelectedCount() {
    const count = document.querySelectorAll('.customer-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = count;
}

// Seçili müşterileri gönder
function syncSelectedCustomers() {
    const selectedIds = Array.from(document.querySelectorAll('.customer-checkbox:checked'))
        .map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Lütfen en az bir müşteri seçin');
        return;
    }
    
    if (!confirm(`${selectedIds.length} müşteriyi Hipcall'a göndermek istediğinizden emin misiniz?`)) {
        return;
    }
    
    if (typeof toastr !== 'undefined') {
        toastr.info('Müşteriler gönderiliyor...');
    }
    
    $.ajax({
        url: '{{ route("tenant.integrations.hipcall.sync-selected", $tenant->id) }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            customer_ids: selectedIds
        },
        success: function(response) {
            if (response.success) {
                showResults(response.results);
                
                if (typeof toastr !== 'undefined') {
                    toastr.success(response.message);
                } else {
                    alert('✓ ' + response.message);
                }
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(response.message);
                } else {
                    alert('✗ ' + response.message);
                }
            }
        },
        error: function() {
            if (typeof toastr !== 'undefined') {
                toastr.error('Gönderim sırasında hata oluştu');
            } else {
                alert('✗ Gönderim sırasında hata oluştu');
            }
        }
    });
}

// Tüm müşterileri gönder
function syncAllCustomers() {
    const totalCount = {{ count($customers) }};
    
    if (!confirm(`Tüm müşteriler (${totalCount} adet) Hipcall'a gönderilecek. Devam etmek istiyor musunuz?`)) {
        return;
    }
    
    if (typeof toastr !== 'undefined') {
        toastr.info(`${totalCount} müşteri gönderiliyor, lütfen bekleyin...`);
    }
    
    $.ajax({
        url: '{{ route("tenant.integrations.hipcall.sync-all", $tenant->id) }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                showResults(response.results);
                
                if (typeof toastr !== 'undefined') {
                    toastr.success(response.message);
                } else {
                    alert('✓ ' + response.message);
                }
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(response.message);
                } else {
                    alert('✗ ' + response.message);
                }
            }
        },
        error: function() {
            if (typeof toastr !== 'undefined') {
                toastr.error('Toplu gönderim sırasında hata oluştu');
            } else {
                alert('✗ Toplu gönderim sırasında hata oluştu');
            }
        }
    });
}

// Tek müşteri gönder
function syncSingle(customerId) {
    if (typeof toastr !== 'undefined') {
        toastr.info('Müşteri gönderiliyor...');
    }
    
    $.ajax({
        url: `/{{ $tenant->id }}/integrations/hipcall/sync-single/${customerId}`,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                if (typeof toastr !== 'undefined') {
                    toastr.success(response.message);
                } else {
                    alert('✓ ' + response.message);
                }
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(response.message);
                } else {
                    alert('✗ ' + response.message);
                }
            }
        },
        error: function() {
            if (typeof toastr !== 'undefined') {
                toastr.error('Gönderim hatası');
            } else {
                alert('✗ Gönderim hatası');
            }
        }
    });
}

// Sonuçları göster
function showResults(results) {
    let html = `
        <div class="alert alert-info">
            <h5>Özet</h5>
            <ul class="mb-0">
                <li>Toplam: <strong>${results.total}</strong></li>
                <li>Başarılı: <strong class="text-success">${results.success}</strong></li>
                <li>Başarısız: <strong class="text-danger">${results.failed}</strong></li>
            </ul>
        </div>
    `;
    
    if (results.errors && results.errors.length > 0) {
        html += `
            <div class="alert alert-warning">
                <h6>Hatalar:</h6>
                <ul>
        `;
        results.errors.forEach(error => {
            html += `<li>${error.customer_name}: ${error.error}</li>`;
        });
        html += `</ul></div>`;
    }
    
    document.getElementById('resultsContent').innerHTML = html;
    new bootstrap.Modal(document.getElementById('resultsModal')).show();
}
</script>

@endsection