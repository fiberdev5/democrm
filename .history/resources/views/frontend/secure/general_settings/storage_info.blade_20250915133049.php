@extends('frontend.secure.user_master')
@section('user')

{{-- resources/views/components/storage-widget.blade.php --}}

@php
    $storageInfo = auth()->user()->tenant->getStorageInfo();
    $progressColorClass = '';
    $iconClass = 'fas fa-database';
    $statusText = '';
    
    if ($storageInfo['danger_threshold']) {
        $progressColorClass = 'bg-gradient-danger';
        $iconClass = 'fas fa-exclamation-triangle';
        $statusText = 'Kritik Seviye';
    } elseif ($storageInfo['warning_threshold']) {
        $progressColorClass = 'bg-gradient-warning';
        $iconClass = 'fas fa-exclamation-circle';
        $statusText = 'Dikkat Gerekli';
    } else {
        $progressColorClass = 'bg-gradient-success';
        $iconClass = 'fas fa-check-circle';
        $statusText = 'Normal';
    }
@endphp

<div class="page-content" id="passwords">
  <div class="container-fluid">
<div class="storage-widget" id="storageWidget">
    <!-- Widget Header -->
    <div class="widget-header">
        <div class="widget-title">
            <i class="{{ $iconClass }} widget-icon"></i>
            <span>Depolama Alanı</span>
            <span class="status-badge status-{{ $storageInfo['danger_threshold'] ? 'danger' : ($storageInfo['warning_threshold'] ? 'warning' : 'success') }}">
                {{ $statusText }}
            </span>
        </div>
        <div class="widget-actions">
            <button class="btn-refresh" onclick="refreshStorageInfo()" title="Yenile">
                <i class="fas fa-sync-alt"></i>
            </button>
            <button class="btn-details" onclick="toggleStorageDetails()" title="Detaylar">
                <i class="fas fa-info-circle"></i>
            </button>
        </div>
    </div>

    <!-- Storage Overview -->
    <div class="storage-overview">
        <div class="usage-container">
            <!-- Circular Progress -->
            <div class="circular-progress">
                <svg class="progress-ring" width="120" height="120">
                    <circle class="progress-ring-circle-bg" cx="60" cy="60" r="50"></circle>
                    <circle class="progress-ring-circle progress-{{ $storageInfo['danger_threshold'] ? 'danger' : ($storageInfo['warning_threshold'] ? 'warning' : 'success') }}" 
                            cx="60" cy="60" r="50" 
                            stroke-dasharray="314" 
                            stroke-dashoffset="{{ 314 - (314 * $storageInfo['usage_percentage'] / 100) }}">
                    </circle>
                </svg>
                <div class="progress-text">
                    <span class="percentage">{{ $storageInfo['usage_percentage'] }}%</span>
                    <span class="label">Kullanım</span>
                </div>
            </div>

            <!-- Usage Stats -->
            <div class="usage-stats">
                <div class="stat-item">
                    <div class="stat-value">{{ $storageInfo['current_usage_formatted'] }}</div>
                    <div class="stat-label">Kullanılan</div>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <div class="stat-value">{{ $storageInfo['remaining_formatted'] }}</div>
                    <div class="stat-label">Kalan</div>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <div class="stat-value">{{ $storageInfo['limit_formatted'] }}</div>
                    <div class="stat-label">Toplam</div>
                </div>
            </div>
        </div>

        <!-- Linear Progress Bar -->
        <div class="linear-progress-container">
            <div class="progress-info">
                <span class="current-usage">{{ $storageInfo['current_usage_formatted'] }}</span>
                <span class="total-limit">{{ $storageInfo['limit_formatted'] }}</span>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill {{ $progressColorClass }}" 
                         style="width: {{ $storageInfo['usage_percentage'] }}%"
                         data-percentage="{{ $storageInfo['usage_percentage'] }}">
                        <div class="progress-bar-glow"></div>
                    </div>
                </div>
                <div class="progress-markers">
                    <div class="marker marker-25" style="left: 25%"></div>
                    <div class="marker marker-50" style="left: 50%"></div>
                    <div class="marker marker-75" style="left: 75%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if($storageInfo['danger_threshold'])
        <div class="storage-alert alert-danger" style="color: #db7588;">
            <i class="fas fa-exclamation-triangle"></i>
            <div class="alert-content">
                <strong>Depolama Alanı Doldu!</strong>
                <p>Yeni dosya yükleyemezsiniz. Lütfen eski dosyaları silin veya planınızı yükseltin.</p>
            </div>
        </div>
    @elseif($storageInfo['warning_threshold'])
        <div class="storage-alert alert-warning">
            <i class="fas fa-exclamation-circle"></i>
            <div class="alert-content">
                <strong>Depolama Alanı Azalıyor</strong>
                <p>%{{ $storageInfo['usage_percentage'] }} kullanıldı. Yakında limit dolacak.</p>
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="storage-actions">
        @if($storageInfo['warning_threshold'])
            <a href="{{ route('subscription.plans',$firma->id) }}" class="btn btn-upgrade">
                <i class="fas fa-arrow-up"></i>
                <span>Planı Yükselt</span>
            </a>
        @endif
        
        <button class="btn btn-manage" onclick="toggleStorageDetails()">
            <i class="fas fa-folder-open"></i>
            <span>Dosyaları Yönet</span>
        </button>
    </div>

    <!-- Detailed Information (Hidden by default) -->
    <div class="storage-details" id="storageDetails" style="display: none;">
        <div class="details-grid">
            <div class="detail-item">
                <i class="fas fa-images text-primary"></i>
                <div class="detail-info">
                    <span class="detail-label">Servis Fotoğrafları</span>
                    <span class="detail-value" id="servicePhotosCount">-</span>
                </div>
            </div>
            <div class="detail-item">
                <i class="fas fa-boxes text-info"></i>
                <div class="detail-info">
                    <span class="detail-label">Stok Resimleri</span>
                    <span class="detail-value" id="stockPhotosCount">-</span>
                </div>
            </div>
            <div class="detail-item">
                <i class="fas fa-file-alt text-secondary"></i>
                <div class="detail-info">
                    <span class="detail-label">Diğer Dosyalar</span>
                    <span class="detail-value" id="otherFilesCount">-</span>
                </div>
            </div>
        </div>
        
        <div class="plan-info">
            <h6><i class="fas fa-crown"></i> Mevcut Paket</h6>
            <div class="plan-details">
                <span class="plan-name">{{ auth()->user()->tenant->plan()?->name ?? 'Temel' }}</span>
                <span class="plan-limit">{{ $storageInfo['limit_formatted'] }} Depolama</span>
            </div>
        </div>
    </div>
</div>
</div>
</div>

{{-- CSS Styles --}}
<style>
.storage-widget {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 24px;
    color: white;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.storage-widget::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.03)"/><circle cx="80" cy="80" r="1" fill="rgba(255,255,255,0.03)"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grain)"/></svg>');
    pointer-events: none;
}

.storage-widget:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
}

.widget-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    position: relative;
    z-index: 2;
}

.widget-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 18px;
    font-weight: 600;
}

.widget-icon {
    font-size: 20px;
    opacity: 0.9;
}

.status-badge {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 12px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-success {
    background: rgba(40, 167, 69, 0.3);
    border: 1px solid rgba(40, 167, 69, 0.5);
}

.status-warning {
    background: rgba(255, 193, 7, 0.3);
    border: 1px solid rgba(255, 193, 7, 0.5);
}

.status-danger {
    background: rgba(220, 53, 69, 0.3);
    border: 1px solid rgba(220, 53, 69, 0.5);
}

.widget-actions {
    display: flex;
    gap: 8px;
}

.btn-refresh, .btn-details {
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 8px;
    padding: 8px;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.btn-refresh:hover, .btn-details:hover {
    background: rgba(255,255,255,0.2);
    transform: scale(1.05);
}

.storage-overview {
    margin-bottom: 20px;
    position: relative;
    z-index: 2;
}

.usage-container {
    display: flex;
    align-items: center;
    gap: 30px;
    margin-bottom: 25px;
}

.circular-progress {
    position: relative;
    flex-shrink: 0;
}

.progress-ring {
    transform: rotate(-90deg);
}

.progress-ring-circle-bg {
    fill: none;
    stroke: rgba(255,255,255,0.1);
    stroke-width: 8;
}

.progress-ring-circle {
    fill: none;
    stroke-width: 8;
    stroke-linecap: round;
    transition: stroke-dashoffset 0.8s ease-in-out;
}

.progress-success {
    stroke: #28a745;
    filter: drop-shadow(0 0 6px rgba(40, 167, 69, 0.4));
}

.progress-warning {
    stroke: #ffc107;
    filter: drop-shadow(0 0 6px rgba(255, 193, 7, 0.4));
}

.progress-danger {
    stroke: #dc3545;
    filter: drop-shadow(0 0 6px rgba(220, 53, 69, 0.4));
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.percentage {
    display: block;
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 2px;
}

.label {
    font-size: 12px;
    opacity: 0.8;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.usage-stats {
    flex: 1;
    display: flex;
    justify-content: space-around;
    align-items: center;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 4px;
    display: block;
}

.stat-label {
    font-size: 12px;
    opacity: 0.8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-divider {
    width: 1px;
    height: 40px;
    background: rgba(255,255,255,0.2);
}

.linear-progress-container {
    margin-top: 20px;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 13px;
    opacity: 0.9;
}

.progress-bar-container {
    position: relative;
}

.progress-bar-bg {
    background: rgba(255,255,255,0.15);
    height: 8px;
    border-radius: 4px;
    overflow: hidden;
    position: relative;
}

.progress-bar-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.8s ease-in-out;
    position: relative;
    overflow: hidden;
}

.bg-gradient-success {
    background: linear-gradient(90deg, #28a745, #20c997);
}

.bg-gradient-warning {
    background: linear-gradient(90deg, #ffc107, #fd7e14);
}

.bg-gradient-danger {
    background: linear-gradient(90deg, #dc3545, #e91e63);
}

.progress-bar-glow {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.progress-markers {
    position: absolute;
    top: -2px;
    left: 0;
    right: 0;
    height: 12px;
    pointer-events: none;
}

.marker {
    position: absolute;
    width: 2px;
    height: 12px;
    background: rgba(255,255,255,0.4);
    border-radius: 1px;
    transform: translateX(-50%);
}

.storage-alert {
    display: flex;
    align-items: baseline;
    gap: 12px;
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 20px;
    position: relative;
    z-index: 2;
}

.alert-danger {
    background: rgba(220, 53, 69, 0.15);
    border: 1px solid rgba(220, 53, 69, 0.3);
    color: #db7588;
}

.alert-warning {
    background: rgba(255, 193, 7, 0.15);
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.alert-content strong {
    display: block;
    margin-bottom: 4px;
    font-size: 14px;
    color: #d9d3d3;
}

.alert-content p {
    margin: 0;
    font-size: 13px;
    opacity: 0.9;
}

.storage-actions {
    display: flex;
    gap: 12px;
    position: relative;
    z-index: 2;
}

.btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    font-size: 14px;
}

.btn-upgrade {
    background:  rgba(255,255,255,0.1);
    color: white;
    flex: 1;
}

.btn-upgrade:hover {
    background: rgba(255,255,255,0.1);
    color: white;
    flex: 1;
}

.btn-manage {
    background: rgba(255,255,255,0.1);
    color: white;
    border: 1px solid rgba(255,255,255,0.2);
    flex: 1;
}

.btn-manage:hover {
    background: rgba(255,255,255,0.1);
    color: white;
    border: 1px solid rgba(255,255,255,0.2);
    flex: 1;
}

.storage-details {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,0.1);
    position: relative;
    z-index: 2;
}

.details-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
    margin-bottom: 20px;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px;
    background: rgba(255,255,255,0.05);
    border-radius: 8px;
}

.detail-info {
    flex: 1;
    display: flex;
    justify-content: space-between;
}

.detail-label {
    font-size: 13px;
    opacity: 0.9;
}

.detail-value {
    font-weight: 600;
    font-size: 13px;
}

.plan-info {
    background: rgba(255,255,255,0.05);
    padding: 15px;
    border-radius: 10px;
}

.plan-info h6 {
    margin: 0 0 10px 0;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.plan-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.plan-name {
    font-weight: 600;
    font-size: 16px;
}

.plan-limit {
    font-size: 13px;
    opacity: 0.8;
}

/* Responsive Design */
@media (max-width: 768px) {
    .storage-widget {
        padding: 20px;
    }
    
    .usage-container {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .usage-stats {
        width: 100%;
    }
    
    .storage-actions {
        flex-direction: column;
    }
    
    .widget-title {
        font-size: 16px;
    }
    
    .percentage {
        font-size: 20px;
    }
    
    .stat-value {
        font-size: 18px;
    }
}

/* Dark mode compatibility */
@media (prefers-color-scheme: dark) {
    .storage-widget {
        background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
    }
}

/* Loading state */
.storage-widget.loading {
    opacity: 0.7;
    pointer-events: none;
}

.storage-widget.loading .btn-refresh i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

{{-- JavaScript Functionality --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    initStorageWidget();
});

function initStorageWidget() {
    // Auto refresh every 60 seconds
    //setInterval(refreshStorageInfo, 60000);
    
    // Load detailed info on page load
    loadStorageDetails();
}

function refreshStorageInfo() {
    const widget = document.getElementById('storageWidget');
    const refreshBtn = widget.querySelector('.btn-refresh');
    
    // Add loading state
    widget.classList.add('loading');
    refreshBtn.querySelector('i').style.animation = 'spin 1s linear infinite';
    
    fetch(`/{{ auth()->user()->tenant->id }}/depolama-alani/bilgisi`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStorageDisplay(data.storage_info);
                showToast('Storage bilgileri güncellendi', 'success');
            }
        })
        .catch(error => {
            console.error('Storage info update error:', error);
            showToast('Güncelleme sırasında hata oluştu', 'error');
        })
        .finally(() => {
            // Remove loading state
            widget.classList.remove('loading');
            refreshBtn.querySelector('i').style.animation = '';
        });
}

function updateStorageDisplay(info) {
    // Update circular progress
    const progressCircle = document.querySelector('.progress-ring-circle');
    const circumference = 314;
    const offset = circumference - (circumference * info.usage_percentage / 100);
    progressCircle.style.strokeDashoffset = offset;
    
    // Update percentage text
    document.querySelector('.percentage').textContent = info.usage_percentage + '%';
    
    // Update stat values
    const statValues = document.querySelectorAll('.stat-value');
    statValues[0].textContent = info.current_usage_formatted;
    statValues[1].textContent = info.remaining_formatted;
    statValues[2].textContent = info.limit_formatted;
    
    // Update linear progress
    const progressFill = document.querySelector('.progress-bar-fill');
    progressFill.style.width = info.usage_percentage + '%';
    
    // Update info text
    document.querySelector('.current-usage').textContent = info.current_usage_formatted;
    document.querySelector('.total-limit').textContent = info.limit_formatted;
    
    // Update colors based on threshold
    updateProgressColors(info);
}

function updateProgressColors(info) {
    const progressCircle = document.querySelector('.progress-ring-circle');
    const progressFill = document.querySelector('.progress-bar-fill');
    
    // Remove existing classes
    progressCircle.classList.remove('progress-success', 'progress-warning', 'progress-danger');
    progressFill.classList.remove('bg-gradient-success', 'bg-gradient-warning', 'bg-gradient-danger');
    
    // Add appropriate classes
    if (info.danger_threshold) {
        progressCircle.classList.add('progress-danger');
        progressFill.classList.add('bg-gradient-danger');
    } else if (info.warning_threshold) {
        progressCircle.classList.add('progress-warning');
        progressFill.classList.add('bg-gradient-warning');
    } else {
        progressCircle.classList.add('progress-success');
        progressFill.classList.add('bg-gradient-success');
    }
}

function toggleStorageDetails() {
    const details = document.getElementById('storageDetails');
    const isVisible = details.style.display !== 'none';
    
    if (isVisible) {
        details.style.display = 'none';
    } else {
        details.style.display = 'block';
        loadStorageDetails();
    }
}

function loadStorageDetails() {
    console.log('Loading storage details...');
    
    // Load detailed storage breakdown
    fetch(`/{{ auth()->user()->tenant->id }}/depolama-alani/detaylar`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Storage details received:', data);
            
            if (data.success) {
                updateDetailedInfo(data.details);
            } else {
                throw new Error(data.message || 'API yanıtında hata');
            }
        })
        .catch(error => {
            console.error('Storage details error:', error);
            
            // Fallback boş data ile
            updateDetailedInfo({
                service_photos: { count: 0, total_size_formatted: '0 B' },
                stock_photos: { count: 0, total_size_formatted: '0 B' },
                other_files: { 
                    breakdown: {
                        ticket_attachments: { count: 0, size_formatted: '0 B' },
                        documents: { count: 0, size_formatted: '0 B' },
                        reports: { count: 0, size_formatted: '0 B' },
                        attachments: { count: 0, size_formatted: '0 B' }
                    }
                }
            });
        });
}

function updateDetailedInfo(details) {
    try {
        console.log('Updating detailed info:', details);
        
        // Service photos
        if (details.service_photos) {
            const servicePhotosCount = document.getElementById('servicePhotosCount');
            if (servicePhotosCount) {
                const count = details.service_photos.count || 0;
                const size = details.service_photos.total_size_formatted || '0 B';
                servicePhotosCount.textContent = `${count} dosya (${size})`;
            }
        }
        
        // Stock photos
        if (details.stock_photos) {
            const stockPhotosCount = document.getElementById('stockPhotosCount');
            if (stockPhotosCount) {
                const count = details.stock_photos.count || 0;
                const size = details.stock_photos.total_size_formatted || '0 B';
                stockPhotosCount.textContent = `${count} dosya (${size})`;
            }
        }
        
        // Ticket attachments
        if (details.other_files && details.other_files.breakdown && details.other_files.breakdown.ticket_attachments) {
            const ticketAttachmentsCount = document.getElementById('ticketAttachmentsCount');
            if (ticketAttachmentsCount) {
                const ticketData = details.other_files.breakdown.ticket_attachments;
                const count = ticketData.count || 0;
                const size = ticketData.size_formatted || '0 B';
                const ticketCount = ticketData.ticket_count || 0;
                ticketAttachmentsCount.textContent = `${count} dosya (${size}) - ${ticketCount} ticket`;
            }
        }
        
        // Other files (documents, reports, attachments)
        if (details.other_files && details.other_files.breakdown) {
            const otherFilesCount = document.getElementById('otherFilesCount');
            if (otherFilesCount) {
                const breakdown = details.other_files.breakdown;
                let totalOtherCount = 0;
                let totalOtherSize = '0 B';
                
                // Documents, reports, attachments'ları topla (ticket_attachments hariç)
                ['documents', 'reports', 'attachments'].forEach(type => {
                    if (breakdown[type]) {
                        totalOtherCount += breakdown[type].count || 0;
                    }
                });
                
                // Toplam boyutu hesapla (API'den geliyorsa kullan)
                if (details.other_files.total_size_formatted) {
                    // Ticket attachments boyutunu çıkar
                    const ticketSize = breakdown.ticket_attachments?.size || 0;
                    const totalSize = details.other_files.total_size || 0;
                    const otherSize = totalSize - ticketSize;
                    totalOtherSize = formatBytes(otherSize);
                }
                
                otherFilesCount.textContent = `${totalOtherCount} dosya (${totalOtherSize})`;
            }
        }
        
        console.log('Detailed info updated successfully');
        
    } catch (error) {
        console.error('Error updating detailed info:', error);
        
        // Hata durumunda fallback değerleri göster
        document.getElementById('servicePhotosCount').textContent = '0 dosya';
        document.getElementById('stockPhotosCount').textContent = '0 dosya';
        document.getElementById('ticketAttachmentsCount').textContent = '0 dosya';
        document.getElementById('otherFilesCount').textContent = '0 dosya';
    }
}

// Byte formatter (JavaScript versiyonu)
function formatBytes(bytes, precision = 2) {
    if (bytes === null || bytes < 0 || isNaN(bytes)) {
        return '0 B';
    }
    
    const units = ['B', 'KB', 'MB', 'GB', 'TB'];
    let i = 0;
    
    while (bytes >= 1024 && i < units.length - 1) {
        bytes /= 1024;
        i++;
    }
    
    return Math.round(bytes * Math.pow(10, precision)) / Math.pow(10, precision) + ' ' + units[i];
}



function showToast(message, type = 'info') {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 8px;
        color: white;
        z-index: 9999;
        opacity: 0;
        transition: opacity 0.3s ease;
    `;
    
    if (type === 'success') {
        toast.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
    } else if (type === 'error') {
        toast.style.background = 'linear-gradient(135deg, #dc3545, #e91e63)';
    } else {
        toast.style.background = 'linear-gradient(135deg, #17a2b8, #007bff)';
    }
    
    document.body.appendChild(toast);
    
    setTimeout(() => toast.style.opacity = '1', 100);
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => document.body.removeChild(toast), 300);
    }, 3000);
}

// File upload validation
window.validateFileUpload = function(input) {
    const files = input.files;
    if (!files.length) return true;
    
    let totalSize = 0;
    for (let file of files) {
        totalSize += file.size;
    }
    
    const remainingBytes = {{ $storageInfo['remaining_gb'] }} * 1024 * 1024 * 1024;
    
    if (totalSize > remainingBytes) {
        showToast(`Dosya boyutu storage limitinizi aşıyor. Kalan alan: {{ $storageInfo['remaining_formatted'] }}`, 'error');
        input.value = '';
        return false;
    }
    
    return true;
};
</script>
@endsection