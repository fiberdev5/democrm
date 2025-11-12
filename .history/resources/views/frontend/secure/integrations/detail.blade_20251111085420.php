@extends('frontend.secure.user_master')
@section('user')

<div class="page-content" id="passwords">
    <div class="container-fluid">
        <div class="row pageDetail">
            <div class="col-12">
                <div class="integration-detail">
                    <!-- Breadcrumb -->
                    <div class="breadcrumb-custom">
                        <a href="{{ route('tenant.integrations.marketplace', $tenant->id) }}">
                            <i class="fas fa-arrow-left"></i> Entegrasyonlar
                        </a>
                        <span class="separator">›</span>
                        <span>{{ $integration->name }}</span>
                    </div>

                    <div class="detail-container">
                        <!-- Sol Taraf - Detaylar -->
                        <div class="detail-left">
                            <div class="integration-header">
                                @if($integration->logo)
                                <img src="{{ asset($integration->logo) }}" alt="{{ $integration->name }}" class="integration-logo-large">
                                @else
                                <div class="integration-logo-large d-flex align-items-center justify-content-center">
                                    <i class="fas fa-puzzle-piece fa-3x text-muted"></i>
                                </div>
                                @endif

                                <div class="integration-title-section">
                                    <h1>{{ $integration->name }}</h1>
                                    <p style="color: #757575; font-size: 16px;">{{ $integration->description }}</p>
                                    
                                    <div class="integration-meta">
                                        <div class="meta-item">
                                            <span class="meta-label">Kategori</span>
                                            <span class="meta-value">
                                                @if($integration->category == 'invoice')
                                                    Fatura
                                                @elseif($integration->category == 'sms')
                                                    SMS
                                                @elseif($integration->category == 'accounting')
                                                    Muhasebe
                                                @else
                                                    Diğer
                                                @endif
                                            </span>
                                        </div>
                                        <div class="meta-item">
                                            <span class="meta-label">Son Güncelleme</span>
                                            <span class="meta-value">{{ $integration->updated_at->format('d M Y') }}</span>
                                        </div>
                                    </div>

                                    <div class="integration-tags">
                                        <span class="tag">{{ $integration->category }}</span>
                                        @if($integration->price == 0)
                                        <span class="tag">ücretsiz</span>
                                        @endif
                                        <span class="tag">entegrasyon</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Detaylı Açıklama -->
                            <div class="integration-description">
                                <h2>{{ $integration->name }} Hakkında</h2>
                                {!! $integration->explanation ?: '<p>Bu entegrasyon için detaylı açıklama henüz eklenmemiştir.</p>' !!}
                            </div>
                        </div>

                        <!-- Sağ Taraf - Fiyatlandırma ve Aksiyon -->
                        <div class="detail-right">
                            <div class="pricing-box">
                                <div class="price-display">
                                    @if($integration->price > 0)
                                    <div class="price-amount">₺{{ number_format($integration->price) }}</div>
                                    @else
                                    <div class="price-free">
                                        <i class="fas fa-gift"></i> Ücretsiz
                                    </div>
                                    @endif
                                </div>

                                @if($isPurchased)
                                    @if($isActive)
                                        <div class="alert alert-success" style="padding: 10px; border-radius: 8px;">
                                            <i class="fas fa-check-circle"></i> Bu entegrasyon aktif
                                        </div>
                                    @endif
                                @else
                                    @if($integration->price > 0)
                                        <a href="{{ route('tenant.integrations.purchase', [$tenant->id, $integration->id]) }}" class="action-button-primary">
                                            <i class="fas fa-shopping-cart"></i> Şimdi Satın Al
                                        </a>
                                    @endif
                                @endif
                            </div>

                            <!-- Tab Yapısı -->
                            <div class="info-tabs mt-3">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="features-tab" data-bs-toggle="tab" data-bs-target="#features" type="button" role="tab">
                                            <i class="fas fa-star"></i> Özellikler
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="setup-tab" data-bs-toggle="tab" data-bs-target="#setup" type="button" role="tab">
                                            <i class="fas fa-cogs"></i> Kurulum
                                        </button>
                                    </li>
                                    
                                    {{-- API Ayarları Tabı - Sadece Aktif Entegrasyonlar İçin --}}
                                    @if($isPurchased && $isActive)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="api-tab" data-bs-toggle="tab" data-bs-target="#api" type="button" role="tab">
                                            <i class="fas fa-key"></i> API
                                        </button>
                                    </li>
                                    @endif
                                    
                                    @if($isPurchased && $isActive)

                                    @else
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="support-tab" data-bs-toggle="tab" data-bs-target="#support" type="button" role="tab">
                                                <i class="fas fa-life-ring"></i> Destek
                                            </button>
                                        </li>
                                    @endif
                                </ul>
                                <div class="tab-content">
                                    <!-- Özellikler Tab -->
                                    <div class="tab-pane fade show active" id="features" role="tabpanel">
                                        <ul class="features-list">
                                            <li>
                                                <i class="fas fa-check-circle"></i>
                                                <span>Kolay Kurulum ve Kullanım</span>
                                            </li>
                                            <li>
                                                <i class="fas fa-check-circle"></i>
                                                <span>Otomatik Veri Senkronizasyonu</span>
                                            </li>
                                            <li>
                                                <i class="fas fa-check-circle"></i>
                                                <span>Gerçek Zamanlı Bildirimler</span>
                                            </li>
                                            <li>
                                                <i class="fas fa-check-circle"></i>
                                                <span>Kapsamlı Raporlama</span>
                                            </li>
                                            <li>
                                                <i class="fas fa-check-circle"></i>
                                                <span>API Desteği</span>
                                            </li>
                                            <li>
                                                <i class="fas fa-check-circle"></i>
                                                <span>Güvenli Veri İletimi</span>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Kurulum Tab -->
                                    <div class="tab-pane fade" id="setup" role="tabpanel">
                                        <div class="setup-step">
                                            <h4>
                                                <span class="setup-step-number">1</span>
                                                Entegrasyonu Aktifleştirin
                                            </h4>
                                            <p>Yukarıdaki "Satın Al" butonuna tıklayarak entegrasyonu aktifleştirin.</p>
                                        </div>

                                        <div class="setup-step">
                                            <h4>
                                                <span class="setup-step-number">2</span>
                                                API Bilgilerinizi Girin
                                            </h4>
                                            <p>Entegrasyon ayarları sayfasından gerekli API anahtarlarını ve kimlik bilgilerini girin.</p>
                                        </div>

                                        <div class="setup-step">
                                            <h4>
                                                <span class="setup-step-number">3</span>
                                                Bağlantıyı Test Edin
                                            </h4>
                                            <p>Ayarlar sayfasındaki "Bağlantıyı Test Et" butonuna tıklayarak kurulumun doğru yapıldığından emin olun.</p>
                                        </div>

                                        <div class="setup-step">
                                            <h4>
                                                <span class="setup-step-number">4</span>
                                                Kullanmaya Başlayın
                                            </h4>
                                            <p>Entegrasyon aktif ve hazır! Artık sisteminizdeki veriler otomatik olarak senkronize edilecek.</p>
                                        </div>
                                    </div>

                                    {{-- API Ayarları Tab --}}
                                    {{-- API Ayarları Tab --}}
@if($isPurchased && $isActive)
<div class="tab-pane fade" id="api" role="tabpanel">
    @php
        $hasCredentials = $purchase->credentials && (is_array($purchase->credentials) ? count($purchase->credentials) : count(json_decode($purchase->credentials, true) ?? [])) > 0;
        
        // api_fields'i array'e çevir
        $apiFields = [];
        if ($integration->api_fields) {
            if (is_string($integration->api_fields)) {
                $apiFields = json_decode($integration->api_fields, true) ?? [];
            } elseif (is_array($integration->api_fields)) {
                $apiFields = $integration->api_fields;
            }
        }
        
        // Hipcall kontrolü
        $isHipcall = $integration->slug === 'hipcall';
    @endphp
    
    {{-- HIPCALL İÇİN ÖZEL GÖRÜNÜM --}}
    @if($isHipcall)
        <div class="hipcall-integration-setup">
            {{-- Status Badge --}}
            <div class="api-status-badge {{ $purchase->webhook_url ? 'configured' : 'not-configured' }}">
                <i class="fas fa-{{ $purchase->webhook_url ? 'check-circle' : 'exclamation-circle' }}"></i>
                {{ $purchase->webhook_url ? 'Webhook URL Hazır' : 'Webhook URL Oluşturuluyor...' }}
            </div>

            @if($purchase->webhook_url)
                {{-- Adım 1: Webhook URL'i Kopyala --}}
                <div class="setup-step-box">
                    <div class="step-header">
                        <span class="step-badge">1</span>
                        <h4>Webhook URL'inizi Kopyalayın</h4>
                    </div>
                    <div class="webhook-url-container">
                        <label class="form-label">Webhook URL</label>
                        <div class="input-group">
                            <input 
                                type="text" 
                                class="form-control webhook-url-input" 
                                id="hipcallWebhookUrl"
                                value="{{ $purchase->webhook_url }}" 
                                readonly
                            >
                            <button class="btn btn-primary" type="button" onclick="copyHipcallWebhookUrl()">
                                <i class="fas fa-copy"></i> Kopyala
                            </button>
                        </div>
                        <small class="text-muted">Bu URL'i Hipcall paneline girmeniz gerekiyor</small>
                    </div>
                </div>

                {{-- Adım 2: Hipcall Paneline Git --}}
                <div class="setup-step-box">
                    <div class="step-header">
                        <span class="step-badge">2</span>
                        <h4>Hipcall Paneline Gidin</h4>
                    </div>
                    <p class="text-muted mb-2">Webhook'u Hipcall sistemine eklemek için:</p>
                    <a href="https://use.hipcall.com.tr/portal/settings/marketplace/" 
                       target="_blank" 
                       class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-external-link-alt"></i> Hipcall Entegrasyonlar Sayfası
                    </a>
                </div>

                {{-- Adım 3: Webhook Oluştur --}}
                <div class="setup-step-box">
                    <div class="step-header">
                        <span class="step-badge">3</span>
                        <h4>Hipcall'da Webhook Oluşturun</h4>
                    </div>
                    <div class="hipcall-instructions">
                        <p><strong>"Yeni bir entegrasyon kur"</strong> butonuna tıklayın ve formu doldurun:</p>
                        <ul class="instruction-list">
                            <li><strong>Ad:</strong> SerbisERP Entegrasyonu</li>
                            <li><strong>Durum:</strong> Aktif</li>
                            <li><strong>URL:</strong> Yukarıdaki webhook URL'i yapıştırın</li>
                        </ul>
                        
                        <div class="alert alert-info mt-3">
                            <strong>Seçmeniz Gereken Olaylar:</strong>
                            <ul class="mb-0">
                                <li>✅ Çağrı başlangıcı (Önerilen)</li>
                                <li>✅ Çağrı kapanışı (Önerilen)</li>
                                <li>⚪ Çağrı bağlanışı (Opsiyonel)</li>
                                <li>⚪ Diğer olaylar (İhtiyaç durumuna göre)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Adım 4: Test Et --}}
                <div class="setup-step-box">
                    <div class="step-header">
                        <span class="step-badge">4</span>
                        <h4>Bağlantıyı Test Edin</h4>
                    </div>
                    <p class="text-muted mb-3">Hipcall'da webhook'u kaydettikten sonra test edin:</p>
                    <button type="button" class="btn btn-success btn-sm" onclick="testHipcallWebhook()">
                        <i class="fas fa-check-circle"></i> Webhook Bağlantısını Test Et
                    </button>
                    <a href="" 
                       class="btn btn-info btn-sm ms-2"
                       target="_blank">
                        <i class="fas fa-history"></i> Çağrı Geçmişini Görüntüle
                    </a>
                </div>

                {{-- Özellikler --}}
                <div class="features-box mt-4">
                    <h5><i class="fas fa-star text-warning"></i> Hipcall Entegrasyonu Özellikleri</h5>
                    <ul class="features-list">
                        <li><i class="fas fa-check text-success"></i> Otomatik arama kaydı</li>
                        <li><i class="fas fa-check text-success"></i> Müşteri tanıma sistemi</li>
                        <li><i class="fas fa-check text-success"></i> Detaylı arama geçmişi</li>
                        <li><i class="fas fa-check text-success"></i> Ses kaydı saklama</li>
                        <li><i class="fas fa-check text-success"></i> Gerçek zamanlı bildirimler</li>
                    </ul>
                </div>
            @else
                {{-- Webhook URL henüz oluşmadıysa --}}
                <div class="alert alert-warning">
                    <i class="fas fa-spinner fa-spin"></i>
                    <strong>Webhook URL oluşturuluyor...</strong>
                    <p class="mb-0">Lütfen sayfayı yenileyin veya birkaç saniye bekleyin.</p>
                </div>
                <button type="button" class="btn btn-primary mt-2" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i> Sayfayı Yenile
                </button>
            @endif
        </div>

    @else
        {{-- DİĞER ENTEGRASYONLAR İÇİN NORMAL API FORMU --}}
        <div class="api-status-badge {{ $hasCredentials ? 'configured' : 'not-configured' }}">
            <i class="fas fa-{{ $hasCredentials ? 'check-circle' : 'exclamation-circle' }}"></i>
            {{ $hasCredentials ? 'API Bilgileri Yapılandırılmış' : 'API Bilgileri Bekleniyor' }}
        </div>

        <form id="apiSettingsForm" action="{{ route('tenant.integrations.save_settings', [$tenant->id, $integration->id]) }}" method="POST">
            @csrf
            <div class="api-form">
                @if(count($apiFields) > 0)
                    {{-- Dinamik API Alanları --}}
                    @foreach($apiFields as $field)
                        <div class="api-form-group">
                            <label class="api-form-label">
                                {{ $field['label'] ?? 'Alan' }}
                                @if(($field['required'] ?? false))
                                    <span class="required">*</span>
                                @endif
                            </label>
                            
                            @php
                                $fieldType = $field['type'] ?? 'text';
                                $fieldName = $field['name'] ?? '';
                                $fieldValue = '';
                                
                                if ($purchase->credentials) {
                                    if (is_string($purchase->credentials)) {
                                        $credentials = json_decode($purchase->credentials, true) ?? [];
                                        $fieldValue = $credentials[$fieldName] ?? '';
                                    } elseif (is_array($purchase->credentials)) {
                                        $fieldValue = $purchase->credentials[$fieldName] ?? '';
                                    }
                                }
                            @endphp
                            
                            @if($fieldType == 'password')
                                <div class="password-toggle">
                                    <input 
                                        type="password" 
                                        name="credentials[{{ $fieldName }}]" 
                                        class="api-form-input password-input" 
                                        value="{{ $fieldValue }}"
                                        placeholder="{{ $field['placeholder'] ?? '' }}"
                                        {{ ($field['required'] ?? false) ? 'required' : '' }}
                                    >
                                    <button type="button" class="password-toggle-btn" onclick="togglePassword(this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            @elseif($fieldType == 'select')
                                <select 
                                    name="credentials[{{ $fieldName }}]" 
                                    class="api-form-input"
                                    {{ ($field['required'] ?? false) ? 'required' : '' }}
                                >
                                    <option value="">Seçiniz</option>
                                    @if(isset($field['options']) && is_array($field['options']))
                                        @foreach($field['options'] as $optKey => $optValue)
                                            <option value="{{ $optKey }}" {{ $fieldValue == $optKey ? 'selected' : '' }}>
                                                {{ $optValue }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            @elseif($fieldType == 'textarea')
                                <textarea 
                                    name="credentials[{{ $fieldName }}]" 
                                    class="api-form-input" 
                                    rows="4"
                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                    {{ ($field['required'] ?? false) ? 'required' : '' }}
                                >{{ $fieldValue }}</textarea>
                            @else
                                <input 
                                    type="{{ $fieldType }}" 
                                    name="credentials[{{ $fieldName }}]" 
                                    class="api-form-input" 
                                    value="{{ $fieldValue }}"
                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                    {{ ($field['required'] ?? false) ? 'required' : '' }}
                                >
                            @endif
                            
                            @if(isset($field['help']))
                                <small class="api-form-help">{{ $field['help'] }}</small>
                            @endif
                        </div>
                    @endforeach
                @else
                    {{-- Varsayılan API Alanları --}}
                    @php
                        $credentials = [];
                        if ($purchase->credentials) {
                            if (is_string($purchase->credentials)) {
                                $credentials = json_decode($purchase->credentials, true) ?? [];
                            } elseif (is_array($purchase->credentials)) {
                                $credentials = $purchase->credentials;
                            }
                        }
                    @endphp
                    
                    <div class="api-form-group">
                        <label class="api-form-label">
                            API Kullanıcı Adı / ID
                            <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="credentials[username]" 
                            class="api-form-input" 
                            value="{{ $credentials['username'] ?? '' }}"
                            placeholder="API kullanıcı adınız veya ID"
                            required
                        >
                        <small class="api-form-help">Entegrasyon sağlayıcısından aldığınız kullanıcı adı</small>
                    </div>

                    <div class="api-form-group">
                        <label class="api-form-label">
                            API Anahtarı / Şifre
                            <span class="required">*</span>
                        </label>
                        <div class="password-toggle">
                            <input 
                                type="password" 
                                name="credentials[api_key]" 
                                class="api-form-input password-input" 
                                value="{{ $credentials['api_key'] ?? '' }}"
                                placeholder="API anahtarınız veya şifreniz"
                                required
                            >
                            <button type="button" class="password-toggle-btn" onclick="togglePassword(this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="api-form-help">API anahtarınızı güvenli bir şekilde saklayın</small>
                    </div>

                    <div class="api-form-group">
                        <label class="api-form-label">
                            API URL (Opsiyonel)
                        </label>
                        <input 
                            type="url" 
                            name="credentials[api_url]" 
                            class="api-form-input" 
                            value="{{ $credentials['api_url'] ?? '' }}"
                            placeholder="https://api.example.com"
                        >
                        <small class="api-form-help">Özel API endpoint kullanıyorsanız girin</small>
                    </div>
                @endif
            </div>

            <div class="api-form-actions">
                <button type="submit" class="btn-save-api btn-sm">
                    <i class="fas fa-save"></i>
                    Kaydet
                </button>
            </div>
        </form>
    @endif
</div>
@endif

                                    <!-- Destek Tab -->
                                    <div class="tab-pane fade" id="support" role="tabpanel">
                                        <div class="info-item">
                                            <i class="fas fa-clock"></i>
                                            <span class="info-item-text">7/24 Canlı Destek Hizmeti - Her zaman yanınızdayız</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-book"></i>
                                            <span class="info-item-text">Detaylı Dokümantasyon - Adım adım kurulum rehberi</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-shield-alt"></i>
                                            <span class="info-item-text">Güvenli Entegrasyon - SSL şifrelemeli veri aktarımı</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-credit-card"></i>
                                            <span class="info-item-text">Güvenli Ödeme - 3D Secure ile korumalı ödeme</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-sync-alt"></i>
                                            <span class="info-item-text">Otomatik Güncellemeler - Her zaman en son sürüm</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Bootstrap Tab Aktivasyonu
document.addEventListener('DOMContentLoaded', function () {
    var triggerTabList = [].slice.call(document.querySelectorAll('.nav-tabs button'));
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl);
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });
});

// Şifre göster/gizle
function togglePassword(button) {
    const input = button.parentElement.querySelector('.password-input');
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Bağlantı testi
function testConnection() {
    const form = document.getElementById('apiSettingsForm');
    const formData = new FormData(form);
    
    Swal.fire({
        title: 'Bağlantı Test Ediliyor',
        html: 'API bağlantısı kontrol ediliyor...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Bağlantı Başarılı!',
                text: data.message || 'API bağlantısı başarıyla test edildi.',
                confirmButtonText: 'Tamam'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Bağlantı Hatası',
                text: data.message || 'API bağlantısı kurulamadı.',
                confirmButtonText: 'Tamam'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Hata',
            text: 'Bağlantı testi sırasında bir hata oluştu.',
            confirmButtonText: 'Tamam'
        });
    });
}

// Form submit
$('#apiSettingsForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Başarılı!',
                    text: 'API ayarları kaydedildi.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Hata',
                text: 'API ayarları kaydedilemedi.',
                confirmButtonText: 'Tamam'
            });
        }
    });
});
</script>

@endsection