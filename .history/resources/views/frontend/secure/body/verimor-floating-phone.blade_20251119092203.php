
@php

    // Verimor Santral aktif mi kontrol et
    $verimorActive = false;
    
    if (Auth::check() && Auth::user()->tenant_id) {
        $verimorActive = \App\Models\IntegrationPurchase::where('tenant_id', Auth::user()->tenant_id)
            ->whereHas('integration', function($q) {
                $q->where('slug', 'verimor-santral');
            })
            ->where('status', 'completed')
            ->where('is_active', true)
            ->exists();
    }
@endphp

@if($verimorActive)
<!-- Floating Web Phone Widget -->
<div id="floatingWebPhone" class="floating-webphone hidden">
    <!-- Header -->
    <div class="floating-phone-header" id="phoneHeader">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span class="phone-title">
                <i class="fas fa-phone-volume"></i> Web Telefonu
            </span>
            <div>
                <button class="phone-control-btn" id="minimizeBtn" title="Küçült">
                    <i class="fas fa-minus"></i>
                </button>
                <button class="phone-control-btn" id="closeBtn" title="Kapat">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Body (iframe container) -->
    <div class="floating-phone-body" id="phoneBody">
        <div class="phone-loading" id="phoneLoading">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p class="mt-2">Yükleniyor...</p>
        </div>
        <div id="phoneIframeContainer" style="display: none;"></div>
    </div>
    
    <!-- Minimized (Küçültülmüş) -->
    <div class="floating-phone-minimized" id="phoneMinimized" style="display: none;">
        <button class="phone-restore-btn" id="restoreBtn">
            <i class="fas fa-phone-volume fa-lg"></i>
        </button>
    </div>
</div>

<!-- Açma Butonu (Floating) -->
<button id="openFloatingPhone" class="floating-open-btn" title="Web Telefonunu Aç">
    <i class="fas fa-phone fa-lg"></i>
</button>

<style>
.floating-open-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    z-index: 9998;
    transition: all 0.3s ease;
}

.floating-open-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

.floating-webphone {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: white;
}

.floating-webphone.hidden {
    display: none;
}

.floating-phone-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 15px;
    cursor: move;
    user-select: none;
}

.phone-title {
    font-weight: 600;
    font-size: 14px;
}

.phone-control-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 28px;
    height: 28px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    margin-left: 5px;
}

.phone-control-btn:hover {
    background: rgba(255, 255, 255, 0.3);
}

.floating-phone-body {
    width: 300px;
    height: 550px;
    background: #f5f5f5;
    position: relative;
}

.phone-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #667eea;
}

.floating-phone-minimized {
    padding: 0;
}

.phone-restore-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.phone-restore-btn:hover {
    transform: scale(1.1);
}

.floating-webphone.minimized .floating-phone-header,
.floating-webphone.minimized .floating-phone-body {
    display: none;
}

.floating-webphone.minimized .floating-phone-minimized {
    display: block !important;
}
</style>

<script>
$(document).ready(function() {
    const floatingPhone = $('#floatingWebPhone');
    const openBtn = $('#openFloatingPhone');
    let isLoaded = false;
    
    // Açma butonu
    openBtn.click(function() {
        floatingPhone.removeClass('hidden');
        openBtn.hide();
        
        if (!isLoaded) {
            loadWebPhone();
            isLoaded = true;
        }
    });
    
    // Web telefonunu yükle
    function loadWebPhone() {
        $.ajax({
            url: '{{ route("tenant.integrations.verimor-santral.get-iframe", ["tenant_id" => Auth::user()->firma_id]) }}',
            type: 'GET',
            data: { width: 300, height: 550 },
            success: function(response) {
                if (response.success) {
                    $('#phoneLoading').hide();
                    $('#phoneIframeContainer').html(response.html).show();
                } else {
                    $('#phoneLoading').html('<div class="text-danger"><i class="fas fa-times"></i><p>' + response.message + '</p></div>');
                }
            },
            error: function() {
                $('#phoneLoading').html('<div class="text-danger"><i class="fas fa-times"></i><p>Yüklenemedi</p></div>');
            }
        });
    }
    
    // Minimize
    $('#minimizeBtn').click(function() {
        floatingPhone.addClass('minimized');
    });
    
    // Restore
    $('#restoreBtn').click(function() {
        floatingPhone.removeClass('minimized');
    });
    
    // Kapat
    $('#closeBtn').click(function() {
        floatingPhone.addClass('hidden');
        openBtn.show();
    });
});
</script>
@endif