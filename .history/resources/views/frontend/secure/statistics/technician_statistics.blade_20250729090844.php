@extends('frontend.secure.user_master')

@section('user')
<style>
    .filter-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .filter-section .card-title {
        color: white;
        font-weight: 600;
        margin-bottom: 20px;
    }
    
    .date-quick-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }
    
    .date-quick-buttons .btn {
        border-radius: 20px;
        padding: 8px 16px;
        font-size: 12px;
        transition: all 0.3s ease;
        border: 2px solid rgba(255,255,255,0.3);
        color: white;
        background: rgba(255,255,255,0.1);
    }
    
    .date-quick-buttons .btn:hover {
        background: rgba(255,255,255,0.2);
        transform: translateY(-2px);
        border-color: rgba(255,255,255,0.5);
    }
    
    .statistics-card {
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        border: none;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .statistics-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .statistics-card .card-header {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        border: none;
        padding: 20px;
    }
    
    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .table thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        font-weight: 600;
        padding: 15px 10px;
        text-align: center;
        font-size: 13px;
    }
    
    .table tbody tr {
        transition: all 0.3s ease;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
    }
    
    .table tbody td {
        text-align: center;
        vertical-align: middle;
        padding: 12px 8px;
        border-color: #e9ecef;
    }
    
    .table tbody tr.clicked {
        background-color: #e3f2fd !important;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    
    .detail-row {
        background-color: #f8f9fa;
        border-left: 4px solid #007bff;
    }
    
    .detail-charts {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .detail-chart-item {
        flex: 1;
        background: white;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.1);
    }
    
    .detail-chart-item h6 {
        margin-bottom: 15px;
        color: #495057;
        font-weight: 600;
    }
    
    .stage-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }
    
    .stage-stat-item {
        background: white;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .stage-stat-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    
    .stage-stat-item .stat-number {
        font-size: 24px;
        font-weight: bold;
        color: #007bff;
        margin-bottom: 5px;
    }
    
    .stage-stat-item .stat-label {
        font-size: 12px;
        color: #6c757d;
        font-weight: 500;
    }
    
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    
    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .cursor-pointer {
        cursor: pointer;
    }
    
    .form-control, .form-select {
        border-radius: 10px;
        border: 2px solid rgba(255,255,255,0.3);
        background: rgba(255,255,255,0.9);
        color: #495057;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: rgba(255,255,255,0.8);
        box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.25);
        background: white;
    }
    
    .btn-search {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        border-radius: 25px;
        padding: 12px 30px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-search:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(40,167,69,0.3);
        color: white;
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
        <!-- Filter Section -->
        <div class="filter-section">
            <h5 class="card-title">
                <i class="fas fa-filter me-2"></i>
                Teknisyen İstatistikleri Filtresi
            </h5>
            
            <form id="filterForm">
                <div class="row">
                    <div class="col-lg-4 mb-3">
                        <label class="form-label text-white">
                            <i class="fas fa-microchip me-1"></i>
                            Cihaz Türü
                        </label>
                        <select class="form-select" name="cihazTur" id="cihazTur">
                            <option value="">Tüm Cihazlar</option>
                            @foreach($cihazTurleri as $cihaz)
                                <option value="{{ $cihaz->id }}">{{ $cihaz->cihaz }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-lg-4 mb-3">
                        <label class="form-label text-white">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Başlangıç Tarihi
                        </label>
                        <input type="text" name="tarih1" class="form-control datepicker" id="tarih1" 
                               readonly value="{{ date('d/m/Y') }}" style="background: rgba(255,255,255,0.9);">
                    </div>
                    
                    <div class="col-lg-4 mb-3">
                        <label class="form-label text-white">
                            <i class="fas fa-calendar-check me-1"></i>
                            Bitiş Tarihi
                        </label>
                        <input type="text" name="tarih2" class="form-control datepicker" id="tarih2" 
                               readonly value="{{ date('d/m/Y') }}" style="background: rgba(255,255,255,0.9);">
                    </div>
                </div>
                
                <div class="date-quick-buttons">
                    <button type="button" class="btn btn-outline-light btn-sm" data-days="0">Bugün</button>
                    <button type="button" class="btn btn-outline-light btn-sm" data-days="1">Dün</button>
                    <button type="button" class="btn btn-outline-light btn-sm" data-days="7">Son 7 Gün</button>
                    <button type="button" class="btn btn-outline-light btn-sm" data-days="15">Son 15 Gün</button>
                    <button type="button" class="btn btn-outline-light btn-sm" data-days="30">Son 30 Gün</button>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-search">
                        <i class="fas fa-search me-2"></i>
                        İstatistikleri Getir
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Statistics Results -->
        <div class="statistics-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    Teknisyen Performans İstatistikleri
                </h5>
            </div>
            <div class="card-body position-relative" id="statisticsContainer">
                <div class="loading-overlay">
                    <div class="loading-spinner"></div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
// jQuery'nin tamamen yüklendiğinden emin olmak için
jQuery(document).ready(function($) {
    console.log('jQuery loaded:', typeof $);
    
    // Datepicker initialization
    $('.datepicker').datepicker({
        language: 'tr',
        autoclose: true,
        endDate: new Date(),
        format: 'dd/mm/yyyy'
    });
    
    // Quick date buttons
    $('.date-quick-buttons .btn').click(function() {
        const days = parseInt($(this).data('days'));
        let startDate, endDate;
        
        if (days === 0) { // Bugün
            startDate = endDate = new Date();
        } else if (days === 1) { // Dün
            startDate = endDate = new Date(Date.now() - 86400000);
        } else {
            endDate = new Date();
            startDate = new Date(Date.now() - (days * 86400000));
        }
        
        $('#tarih1').datepicker('setDate', startDate);
        $('#tarih2').datepicker('setDate', endDate);
        
        // Visual feedback
        $('.date-quick-buttons .btn').removeClass('active');
        $(this).addClass('active');
    });
    
    // Form submission
    $('#filterForm').submit(function(e) {
        e.preventDefault();
        loadStatistics();
    });
    
    // Load initial statistics
    loadStatistics();
    
    function loadStatistics() {
        const formData = {
            dateRange: $('#tarih1').val() + '---' + $('#tarih2').val(),
            cihazTur: $('#cihazTur').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        showLoading();
        
        $.ajax({
            url: '{{ route("technician.statistics.data", $tenant_id) }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#statisticsContainer').html(response.html);
                initializeDataTable();
                hideLoading();
            },
            error: function(xhr) {
                console.error('Error loading statistics:', xhr);
                $('#statisticsContainer').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        İstatistikler yüklenirken bir hata oluştu. Lütfen tekrar deneyin.
                    </div>
                `);
                hideLoading();
            }
        });
    }
    
    function showLoading() {
        $('#statisticsContainer .loading-overlay').show();
    }
    
    function hideLoading() {
        $('#statisticsContainer .loading-overlay').hide();
    }
    
    function initializeDataTable() {
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().destroy();
        }
        
        if ($('#dataTable').length > 0) {
            $('#dataTable').DataTable({
                "bLengthChange": false,
                "paging": false,
                "info": false,
                "order": [[7, 'desc']],
                "language": {
                    "sEmptyTable": "Herhangi bir veri bulunamadı.",
                    "sInfoEmpty": "-",
                    "search": "Ara:",
                    "searchPlaceholder": "Teknisyen adı..."
                },
                "columnDefs": [
                    { "width": "20%", "targets": 0 },
                    { "width": "10%", "targets": [1,2,3,4,5,6] },
                    { "width": "15%", "targets": [7,8] }
                ]
            });
        }
    }
    
    // Detail view toggle
    $(document).on('click', '.tdDetayBtn', function() {
        const $this = $(this);
        const technicianId = $this.data('persid');
        const $existingDetail = $this.next('.detail-row');
        
        // Remove other open details
        $('.detail-row').remove();
        $('.tdDetayBtn').removeClass('clicked');
        
        if ($existingDetail.length > 0) {
            return; // Already open, just close it
        }
        
        $this.addClass('clicked');
        
        // Insert loading row
        const colCount = $this.find('td').length;
        const $loadingRow = $(`
            <tr class="detail-row">
                <td colspan="${colCount}" style="padding: 30px; text-align: center;">
                    <div class="loading-spinner" style="margin: 0 auto;"></div>
                    <p class="mt-3 text-muted">Detaylar yükleniyor...</p>
                </td>
            </tr>
        `);
        $this.after($loadingRow);
        
        // Load detail data
        const formData = {
            technicianId: technicianId,
            tarih1: $('#tarih1').val(),
            tarih2: $('#tarih2').val(),
            cihazTur: $('#cihazTur').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        $.ajax({
            url: '{{ route("technician.statistics.detail", $tenant_id) }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                $loadingRow.find('td').html(response.html);
                initializeDetailCharts();
            },
            error: function(xhr) {
                console.error('Error loading detail:', xhr);
                $loadingRow.find('td').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Detaylar yüklenirken bir hata oluştu.
                    </div>
                `);
            }
        });
    });
    
    function initializeDetailCharts() {
        if (typeof window.chartData === 'undefined') return;
        
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        };
        
        // Completed services chart
        const completedCtx = document.getElementById('completedChart');
        if (completedCtx) {
            new Chart(completedCtx, {
                type: 'line',
                data: {
                    labels: window.chartData.labels,
                    datasets: [{
                        data: window.chartData.completed,
                        borderColor: 'rgba(255,255,255,0.8)',
                        backgroundColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: chartOptions
            });
        }
        
        // Cancelled services chart
        const cancelledCtx = document.getElementById('cancelledChart');
        if (cancelledCtx) {
            new Chart(cancelledCtx, {
                type: 'line',
                data: {
                    labels: window.chartData.labels,
                    datasets: [{
                        data: window.chartData.cancelled,
                        borderColor: 'rgba(255,255,255,0.8)',
                        backgroundColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: chartOptions
            });
        }
        
        // Revenue chart
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx) {
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: window.chartData.labels,
                    datasets: [{
                        data: window.chartData.revenue,
                        borderColor: 'rgba(255,255,255,0.8)',
                        backgroundColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: chartOptions
            });
        }
    }
});
</script>
@endsection