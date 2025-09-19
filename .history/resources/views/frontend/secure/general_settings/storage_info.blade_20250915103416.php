

<div class="storage-info-widget">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-hdd"></i> Storage Kullanımı
            </h6>
            <small class="text-muted">
                {{ $storageInfo['current_usage_formatted'] }} / {{ $storageInfo['limit_formatted'] }}
            </small>
        </div>
        
        <div class="card-body">
            <!-- Progress Bar -->
            <div class="progress mb-3" style="height: 8px;">
                <div class="progress-bar 
                    @if($storageInfo['danger_threshold']) bg-danger
                    @elseif($storageInfo['warning_threshold']) bg-warning  
                    @else bg-success
                    @endif"
                    role="progressbar" 
                    style="width: {{ $storageInfo['usage_percentage'] }}%"
                    aria-valuenow="{{ $storageInfo['usage_percentage'] }}" 
                    aria-valuemin="0" 
                    aria-valuemax="100">
                </div>
            </div>
            
            <!-- Storage Detayları -->
            <div class="row text-center">
                <div class="col-4">
                    <small class="text-muted d-block">Kullanılan</small>
                    <strong class="d-block">{{ $storageInfo['current_usage_formatted'] }}</strong>
                </div>
                <div class="col-4">
                    <small class="text-muted d-block">Kalan</small>
                    <strong class="d-block">{{ $storageInfo['remaining_formatted'] }}</strong>
                </div>
                <div class="col-4">
                    <small class="text-muted d-block">Toplam</small>
                    <strong class="d-block">{{ $storageInfo['limit_formatted'] }}</strong>
                </div>