@extends('frontend.secure.user_master')
@section('user')

<style>
.api-form {
    padding: 20px 0;
}

.api-form-group {
    margin-bottom: 20px;
}

.api-form-label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #212529;
    font-size: 14px;
}

.api-form-label .required {
    color: #dc3545;
    margin-left: 4px;
}

.api-form-input {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s;
}

.api-form-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.api-form-help {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
    display: block;
}

.api-form-actions {
    display: flex;
    gap: 10px;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

.btn-save-api {
    padding: 12px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-save-api:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-test-api {
    padding: 12px 24px;
    background: white;
    color: #667eea;
    border: 2px solid #667eea;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-test-api:hover {
    background: #667eea;
    color: white;
}

.api-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 20px;
}

.api-status-badge.configured {
    background: #d4edda;
    color: #155724;
}

.api-status-badge.not-configured {
    background: #fff3cd;
    color: #856404;
}

.password-toggle {
    position: relative;
}

.password-toggle-btn {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    padding: 5px;
}

.password-toggle-btn:hover {
    color: #667eea;
}
</style>

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
                                        <div class="alert alert-success mb-3" style="padding: 12px; border-radius: 8px;">
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
                                            <i class="fas fa-key"></i> API Ayarları
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
                                    @if($isPurchased && $isActive)
                                    <div class="tab-pane fade" id="api" role="tabpanel">
                                        @php
                                            $hasCredentials = $purchase->credentials && count($purchase->credentials) > 0;
                                        @endphp
                                        
                                        <div class="api-status-badge {{ $hasCredentials ? 'configured' : 'not-configured' }}">
                                            <i class="fas fa-{{ $hasCredentials ? 'check-circle' : 'exclamation-circle' }}"></i>
                                            {{ $hasCredentials ? 'API Bilgileri Yapılandırılmış' : 'API Bilgileri Bekleniyor' }}
                                        </div>

                                        <form id="apiSettingsForm" action="{{ route('tenant.integrations.save_settings', [$tenant->id, $integration->id]) }}" method="POST">
                                            @csrf
                                            <div class="api-form">
                                                @if($integration->api_fields && count($integration->api_fields) > 0)
                                                    {{-- Dinamik API Alanları --}}
                                                    @foreach($integration->api_fields as $field)
                                                        <div class="api-form-group">
                                                            <label class="api-form-label">
                                                                {{ $field['label'] }}
                                                                @if($field['required'] ?? false)
                                                                    <span class="required">*</span>
                                                                @endif
                                                            </label>
                                                            
                                                            @if($field['type'] == 'password')
                                                                <div class="password-toggle">
                                                                    <input 
                                                                        type="password" 
                                                                        name="credentials[{{ $field['name'] }}]" 
                                                                        class="api-form-input password-input" 
                                                                        value="{{ $purchase->credentials[$field['name']] ?? '' }}"
                                                                        placeholder="{{ $field['placeholder'] ?? '' }}"
                                                                        {{ ($field['required'] ?? false) ? 'required' : '' }}
                                                                    >
                                                                    <button type="button" class="password-toggle-btn" onclick="togglePassword(this)">
                                                                        <i class="fas fa-eye"></i>
                                                                    </button>
                                                                </div>
                                                            @elseif($field['type'] == 'select')
                                                                <select 
                                                                    name="credentials[{{ $field['name'] }}]" 
                                                                    class="api-form-input"
                                                                    {{ ($field['required'] ?? false) ? 'required' : '' }}
                                                                >
                                                                    <option value="">Seçiniz</option>
                                                                    @foreach($field['options'] as $optKey => $optValue)
                                                                        <option value="{{ $optKey }}" {{ ($purchase->credentials[$field['name']] ?? '') == $optKey ? 'selected' : '' }}>
                                                                            {{ $optValue }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            @elseif($field['type'] == 'textarea')
                                                                <textarea 
                                                                    name="credentials[{{ $field['name'] }}]" 
                                                                    class="api-form-input" 
                                                                    rows="4"
                                                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                                                    {{ ($field['required'] ?? false) ? 'required' : '' }}
                                                                >{{ $purchase->credentials[$field['name']] ?? '' }}</textarea>
                                                            @else
                                                                <input 
                                                                    type="{{ $field['type'] ?? 'text' }}" 
                                                                    name="credentials[{{ $field['name'] }}]" 
                                                                    class="api-form-input" 
                                                                    value="{{ $purchase->credentials[$field['name']] ?? '' }}"
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
                                                    <div class="api-form-group">
                                                        <label class="api-form-label">
                                                            API Kullanıcı Adı / ID
                                                            <span class="required">*</span>
                                                        </label>
                                                        <input 
                                                            type="text" 
                                                            name="credentials[username]" 
                                                            class="api-form-input" 
                                                            value="{{ $purchase->credentials['username'] ?? '' }}"
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
                                                                value="{{ $purchase->credentials['api_key'] ?? '' }}"
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
                                                            value="{{ $purchase->credentials['api_url'] ?? '' }}"
                                                            placeholder="https://api.example.com"
                                                        >
                                                        <small class="api-form-help">Özel API endpoint kullanıyorsanız girin</small>
                                                    </div>
                                                @endif

                                                {{-- Ek Ayarlar --}}
                                                <div class="api-form-group">
                                                    <label class="api-form-label">
                                                        Test Modu
                                                    </label>
                                                    <select name="settings[test_mode]" class="api-form-input">
                                                        <option value="0" {{ ($purchase->settings['test_mode'] ?? 0) == 0 ? 'selected' : '' }}>Canlı Mod</option>
                                                        <option value="1" {{ ($purchase->settings['test_mode'] ?? 0) == 1 ? 'selected' : '' }}>Test Modu</option>
                                                    </select>
                                                    <small class="api-form-help">Test modunda gerçek işlem yapılmaz</small>
                                                </div>
                                            </div>

                                            <div class="api-form-actions">
                                                <button type="submit" class="btn-save-api">
                                                    <i class="fas fa-save"></i>
                                                    Kaydet
                                                </button>
                                                <button type="button" class="btn-test-api" onclick="testConnection()">
                                                    <i class="fas fa-plug"></i>
                                                    Bağlantıyı Test Et
                                                </button>
                                            </div>
                                        </form>
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