@extends('frontend.secure.user_master')
@section('user')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Anket İstatistikleri
                        </h5>
                        
                        <!-- Filtreleme Alanı -->
                        <div class="d-flex gap-2">
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-filter me-1"></i>Filtrele
                                </button>
                                <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 350px;">
                                    <form id="filterForm">
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Cihaz Türü</label>
                                            <div class="col-sm-8">
                                                <select name="device_type" id="deviceType" class="form-select">
                                                    <option value="">Tüm Cihazlar</option>
                                                    @foreach($deviceTypes as $device)
                                                        <option value="{{ $device->id }}">{{ $device->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Başlangıç</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="start_date" id="startDate" class="form-control datepicker" 
                                                       value="{{ $defaultDate }}" readonly>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Bitiş</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="end_date" id="endDate" class="form-control datepicker" 
                                                       value="{{ $defaultDate }}" readonly>
                                            </div>
                                        </div>
                                        
                                        <!-- Hızlı Tarih Seçimi -->
                                        <div class="mb-3">
                                            <label class="form-label">Hızlı Seçim</label>
                                            <div class="d-flex flex-wrap gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-secondary quick-date" data-days="0">Bugün</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary quick-date" data-days="1">Dün</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary quick-date" data-days="7">Son 7 Gün</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary quick-date" data-days="15">Son 15 Gün</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary quick-date" data-days="30">Son 30 Gün</button>
                                            </div>
                                        </div>
                                        
                                        <div class="d-grid">
                                            <button type="button" class="btn btn-primary" id="applyFilter">
                                                <i class="fas fa-search me-1"></i>Filtrele
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <button class="btn btn-success" id="exportExcel">
                                <i class="fas fa-file-excel me-1"></i>Excel'e Aktar
                            </button>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- İstatistik Kartları -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card border-primary">
                                    <div class="card-body text-center">
                                        <i class="fas fa-clipboard-list fa-2x text-primary mb-2"></i>
                                        <h4 class="mb-1" id="totalServices">-</h4>
                                        <p class="text-muted mb-0">Toplam Servis</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <i class="fas fa-poll fa-2x text-success mb-2"></i>
                                        <h4 class="mb-1" id="totalSurveys">-</h4>
                                        <p class="text-muted mb-0">Yapılan Anket</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                        <i class="fas fa-percentage fa-2x text-warning mb-2"></i>
                                        <h4 class="mb-1" id="completionRate">-%</h4>
                                        <p class="text-muted mb-0">Tamamlanma Oranı</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-info">
                                    <div class="card-body text-center">
                                        <i class="fas fa-smile fa-2x text-info mb-2"></i>
                                        <h4 class="mb-1" id="satisfactionRate">-%</h4>
                                        <p class="text-muted mb-0">Memnuniyet Oranı</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Grafikler -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Günlük Anket Trendi</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="dailyChart" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Memnuniyet Dağılımı</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="satisfactionChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Ana Tablo -->
                        <div class="table-responsive">
                            <table id="statisticsTable" class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Personel</th>
                                        <th>Yapılan Anket</th>
                                        <th>Tamamlanma Oranı</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detay Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-list me-2"></i>Anket Detayları - <span id="modalPersonelName"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="detailTable" class="table table-bordered table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Servis No</th>
                                <th>Müşteri</th>
                                <th>Cihaz Türü</th>
                                <th>Genel Puan</th>
                                <th>Değerlendirmeler</th>
                                <th>Anket Tarihi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    let statisticsTable;
    let detailTable;
    let dailyChart;
    let satisfactionChart;

    // DataTable başlatma
    function initializeTable() {
        if (statisticsTable) {
            statisticsTable.destroy();
        }
        
        statisticsTable = $('#statisticsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('survey.statistics.data', $firma->id) }}",
                data: function(d) {
                    d.start_date = $('#startDate').val();
                    d.end_date = $('#endDate').val();
                    d.device_type = $('#deviceType').val();
                }
            },
            columns: [
                { data: 'personel_name', name: 'personel_name' },
                { 
                    data: 'yapilan_anket_sayisi', 
                    name: 'yapilan_anket_sayisi',
                    className: 'text-center fw-bold'
                },
                { data: 'progress', name: 'progress', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[1, 'desc']],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Turkish.json"
            },
            drawCallback: function(settings) {
                var json = settings.json;
                if (json) {
                    $('#totalServices').text(json.totalCompletedServices || 0);
                    $('#totalSurveys').text(json.totalSurveys || 0);
                    $('#completionRate').text((json.completionRate || 0) + '%');
                }
            }
        });
    }

    // Datepicker
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        language: 'tr',
        autoclose: true,
        todayHighlight: true,
        endDate: new Date()
    });

    // Hızlı tarih seçimi
    $('.quick-date').click(function() {
        var days = parseInt($(this).data('days'));
        var today = new Date();
        var startDate, endDate;

        if (days === 0) {
            startDate = endDate = today;
        } else {
            startDate = new Date(today.getTime() - (days * 24 * 60 * 60 * 1000));
            endDate = today;
        }

        $('#startDate').datepicker('setDate', startDate);
        $('#endDate').datepicker('setDate', endDate);
    });

    // Filtre uygulama
    $('#applyFilter').click(function() {
        $('.dropdown-toggle').dropdown('hide');
        statisticsTable.ajax.reload();
        loadChartData();
    });

    // Detay modal
    $(document).on('click', '.survey-details', function() {
        var personelId = $(this).data('personel-id');
        var startDate = $(this).data('start-date');
        var endDate = $(this).data('end-date');
        var deviceType = $(this).data('device-type');
        
        var personelName = $(this).closest('tr').find('td:first').text();
        $('#modalPersonelName').text(personelName);

        if (detailTable) {
            detailTable.destroy();
        }

        detailTable = $('#detailTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('survey.statistics.details', $firma->id) }}",
                data: {
                    personel_id: personelId,
                    start_date: startDate,
                    end_date: endDate,
                    device_type: deviceType
                }
            },
            columns: [
                { data: 'service_number', name: 'service_number' },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'device_type', name: 'device_type' },
                { data: 'overall_rating', name: 'overall_rating', orderable: false },
                { data: 'rating_badges', name: 'rating_badges', orderable: false },
                { data: 'survey_date', name: 'survey_date' }
            ],
            order: [[5, 'desc']],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Turkish.json"
            }
        });

        $('#detailModal').modal('show');
    });

    // Grafik verileri yükleme
    function loadChartData() {
        $.ajax({
            url: "{{ route('survey.statistics.chart', $firma->id) }}",
            data: {
                start_date: $('#startDate').val(),
                end_date: $('#endDate').val(),
                device_type: $('#deviceType').val()
            },
            success: function(data) {
                updateCharts(data);
            }
        });
    }

    // Grafikleri güncelleme
    function updateCharts(data) {
        // Günlük grafik
        if (dailyChart) {
            dailyChart.destroy();
        }
        
        var ctx1 = document.getElementById('dailyChart').getContext('2d');
        dailyChart = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: data.daily_stats.map(item => item.date),
                datasets: [{
                    label: 'Günlük Anket Sayısı',
                    data: data.daily_stats.map(item => item.count),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Memnuniyet grafiği
        if (satisfactionChart) {
            satisfactionChart.destroy();
        }
        
        var ctx2 = document.getElementById('satisfactionChart').getContext('2d');
        var satisfiedCount = data.satisfaction_stats.satisfied || 0;
        var totalCount = data.satisfaction_stats.total || 0;
        var unsatisfiedCount = totalCount - satisfiedCount;
        
        satisfactionChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Memnun', 'Memnun Değil'],
                datasets: [{
                    data: [satisfiedCount, unsatisfiedCount],
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // Memnuniyet oranını güncelle
        var satisfactionRate = totalCount > 0 ? Math.round((satisfiedCount / totalCount) * 100) : 0;
        $('#satisfactionRate').text(satisfactionRate + '%');
    }


    // Sayfa yüklendiğinde
    initializeTable();
    loadChartData();
});
</script>
@endsection