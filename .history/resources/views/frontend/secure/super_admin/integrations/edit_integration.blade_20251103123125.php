<form method="post" id="editIntegration" action="{{ route('super.admin.integration.update', $integration->id)}}" enctype="multipart/form-data">
    @csrf   
    
    <div class="row">
        <label class="col-sm-3 custom-p-r">Entegrasyon Adı<span style="font-weight: bold; color: red;">*</span></label>
        <div class="col-sm-9 custom-p-l">
            <input name="name" class="form-control buyukYaz" type="text" placeholder="Entegrasyon adını giriniz" value="{{ $integration->name }}" required>
        </div>
    </div>
    
    <div class="row">
        <label class="col-sm-3 custom-p-r">Kategori<span style="font-weight: bold; color: red;">*</span></label>
        <div class="col-sm-9 custom-p-l">
            <select name="category" class="form-select" required>
                <option value="" disabled>-Seçiniz-</option>
                <option value="invoice" {{ $integration->category == 'invoice' ? 'selected' : '' }}>Fatura</option>
                <option value="sms" {{ $integration->category == 'sms' ? 'selected' : '' }}>SMS</option>
                <option value="accounting" {{ $integration->category == 'accounting' ? 'selected' : '' }}>Muhasebe</option>
                <option value="other" {{ $integration->category == 'other' ? 'selected' : '' }}>Diğer</option>
            </select>
        </div>
    </div>
    
    <div class="row">
        <label class="col-sm-3 custom-p-r">Fiyat (₺)</label>
        <div class="col-sm-9 custom-p-l">
            <input name="price" class="form-control" type="number" step="0.01" min="0" placeholder="0.00" value="{{ $integration->price }}">
            <small class="text-muted">Ücretsiz ise boş bırakabilirsiniz</small>
        </div>
    </div>
    
    <div class="row">
        <label class="col-sm-3 custom-p-r">Logo</label>
        <div class="col-sm-9 custom-p-l">
            <input name="logo" class="form-control" type="file" accept="image/*" id="logoInput">
            @if($integration->logo)
                <div class="mt-3" id="logoPreviewContainer">
                    <p class="text-muted mb-2">Mevcut Logo:</p>
                    <img src="{{ asset($integration->logo) }}" alt="Mevcut Logo" style="max-width: 100px; max-height: 100px; border: 1px solid #ddd; padding: 10px; border-radius: 8px; background: #f8f9fa;" id="logoPreview">
                    <p class="text-muted mt-2"><small>Yeni logo seçerseniz güncellenecektir</small></p>
                </div>
            @else
                <div class="mt-3" id="logoPreviewContainer" style="display: none;">
                    <p class="text-muted mb-2">Önizleme:</p>
                    <img src="" alt="Logo Önizleme" style="max-width: 100px; max-height: 100px; border: 1px solid #ddd; padding: 10px; border-radius: 8px; background: #f8f9fa;" id="logoPreview">
                </div>
            @endif
        </div>
    </div>
    
    <div class="row">
        <label class="col-sm-3 custom-p-r">Kısa Açıklama</label>
        <div class="col-sm-9 custom-p-l">
            <textarea name="description" type="text" class="form-control" rows="3" placeholder="Entegrasyon hakkında kısa açıklama yazınız...">{{ $integration->description }}</textarea>
        </div>
    </div>
    
    <div class="row">
        <label class="col-sm-3 custom-p-r">Detaylı Açıklama</label>
        <div class="col-sm-9 custom-p-l">
            <textarea id="elm1" name="explanation" type="text" class="form-control" aria-hidden="true">{{ $integration->explanation }}</textarea>
        </div>
    </div>
    
    <div class="row">
        <label class="col-sm-3 custom-p-r">API Form Alanları</label>
        <div class="col-sm-9 custom-p-l">
            <textarea name="api_fields" id="api_fields" class="form-control json-editor" rows="12" placeholder='[{"name":"username","label":"Kullanıcı Adı","type":"text","required":true}]'>{{ $integration->api_fields ? json_encode($integration->api_fields, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '' }}</textarea>
            <small class="text-muted">
                Firmalar için gerekli API alanlarını JSON formatında tanımlayın. 
                <span class="examples-link" data-bs-toggle="modal" data-bs-target="#apiFieldsModal">Örnekleri göster</span>
            </small>
            <div id="jsonValidation" class="mt-2" style="display: none;"></div>
        </div>
    </div>
    
    <div class="row">
        <label class="col-sm-3 custom-p-r">Durum</label>
        <div class="col-sm-9 custom-p-l">
            <div class="form-check form-switch">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $integration->is_active ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Entegrasyon Aktif</label>
            </div>
        </div>
    </div>
    
    <div class="row">               
        <div class="col-sm-12 gonderBtn">
            <input type="submit" class="btn btn-sm btn-info waves-effect waves-light" value="Güncelle">
            <a href="{{route('super.admin.integrations')}}" class="btn btn-sm btn-light waves-effect">İptal</a>
        </div>
    </div>
</form>

<!-- API Fields Örnekleri Modal (Aynı modal, create'teki ile aynı) -->
<div class="modal fade" id="apiFieldsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">API Form Alanları Örnekleri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#sms-tab">SMS (NETGSM)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#accounting-tab">Muhasebe (Paraşüt)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#invoice-tab">e-Fatura</a>
                    </li>
                </ul>
                
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="sms-tab">
<pre style="background: #f5f5f5; padding: 15px; border-radius: 5px; max-height: 400px; overflow-y: auto;"><code id="sms-code">[
    {
        "name": "username",
        "label": "API Kullanıcı Adı",
        "type": "text",
        "placeholder": "850xxxxxxx",
        "required": true,
        "help": "NETGSM panelinden aldığınız kullanıcı adı"
    },
    {
        "name": "password",
        "label": "API Şifresi",
        "type": "password",
        "placeholder": "••••••••",
        "required": true,
        "help": "NETGSM panel şifreniz"
    },
    {
        "name": "sender_name",
        "label": "Gönderici Başlığı",
        "type": "text",
        "placeholder": "FIRMADI",
        "required": false,
        "help": "SMS gönderici adı (max 11 karakter)"
    }
]</code></pre>
                        <button class="btn btn-sm btn-primary" onclick="copyExample('sms-code')">
                            <i class="fas fa-copy"></i> Kopyala ve Kullan
                        </button>
                    </div>
                    
                    <div class="tab-pane fade" id="accounting-tab">
<pre style="background: #f5f5f5; padding: 15px; border-radius: 5px; max-height: 400px; overflow-y: auto;"><code id="accounting-code">[
    {
        "name": "company_id",
        "label": "Şirket ID",
        "type": "text",
        "placeholder": "123456",
        "required": true,
        "help": "Paraşüt hesabınızdaki şirket ID"
    },
    {
        "name": "client_id",
        "label": "Client ID",
        "type": "text",
        "required": true
    },
    {
        "name": "client_secret",
        "label": "Client Secret",
        "type": "password",
        "required": true
    }
]</code></pre>
                        <button class="btn btn-sm btn-primary" onclick="copyExample('accounting-code')">
                            <i class="fas fa-copy"></i> Kopyala ve Kullan
                        </button>
                    </div>
                    
                    <div class="tab-pane fade" id="invoice-tab">
<pre style="background: #f5f5f5; padding: 15px; border-radius: 5px; max-height: 400px; overflow-y: auto;"><code id="invoice-code">[
    {
        "name": "username",
        "label": "Kullanıcı Adı",
        "type": "text",
        "required": true
    },
    {
        "name": "password",
        "label": "Şifre",
        "type": "password",
        "required": true
    },
    {
        "name": "vkn_tckn",
        "label": "VKN / TCKN",
        "type": "text",
        "placeholder": "10 veya 11 haneli",
        "required": true,
        "help": "Vergi Kimlik No veya TC Kimlik No"
    },
    {
        "name": "integration_type",
        "label": "Entegrasyon Tipi",
        "type": "select",
        "options": {
            "test": "Test Ortamı",
            "prod": "Canlı Ortam"
        },
        "required": true
    }
]</code></pre>
                        <button class="btn btn-sm btn-primary" onclick="copyExample('invoice-code')">
                            <i class="fas fa-copy"></i> Kopyala ve Kullan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$('.buyukYaz').keyup(function(){
    this.value = this.value.toUpperCase();
});

// Logo önizleme
$('#logoInput').change(function(){
    var file = this.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#logoPreview').attr('src', e.target.result).show();
            $('#logoPreviewContainer').show();
        }
        reader.readAsDataURL(file);
    }
});

// JSON Validation
$('#api_fields').on('input blur', function() {
    validateJSON(this);
});

function validateJSON(element) {
    const $element = $(element);
    const value = $element.val().trim();
    const $validation = $('#jsonValidation');
    
    if (!value) {
        $element.removeClass('json-valid json-invalid');
        $validation.hide();
        return true;
    }
    
    try {
        JSON.parse(value);
        $element.removeClass('json-invalid').addClass('json-valid');
        $validation.html('<span class="text-success"><i class="fas fa-check-circle"></i> Geçerli JSON formatı</span>').show();
        return true;
    } catch (e) {
        $element.removeClass('json-valid').addClass('json-invalid');
        $validation.html('<span class="text-danger"><i class="fas fa-times-circle"></i> Geçersiz JSON formatı: ' + e.message + '</span>').show();
        return false;
    }
}

function copyExample(codeId) {
    const code = document.getElementById(codeId).textContent;
    $('#api_fields').val(code);
    validateJSON($('#api_fields')[0]);
    $('#apiFieldsModal').modal('hide');
    
    if (typeof toastr !== 'undefined') {
        toastr.success('Örnek JSON kopyalandı!');
    } else {
        alert('Örnek JSON kopyalandı!');
    }
}

$(document).ready(function () {
    // Sayfa yüklendiğinde mevcut JSON'u validate et
    if ($('#api_fields').val().trim()) {
        validateJSON($('#api_fields')[0]);
    }
    
    $('#editIntegration').submit(function (event) {
        if (tinymce.get('elm1')) {
            tinymce.get('elm1').save();
        }
        
        // JSON validation kontrolü
        const apiFieldsValue = $('#api_fields').val().trim();
        if (apiFieldsValue && !validateJSON($('#api_fields')[0])) {
            event.preventDefault();
            alert('API Fields alanında geçersiz JSON formatı var. Lütfen düzeltin.');
            return false;
        }
        
        var formIsValid = true;
        $(this).find('input[required], select[required]').each(function () {
            if (!$(this).val()) {
                formIsValid = false;
                return false;
            }
        });
    
        if (!formIsValid) {
            event.preventDefault();
            alert('Lütfen zorunlu alanları doldurun.');
            return false;
        }
    });
});
</script>