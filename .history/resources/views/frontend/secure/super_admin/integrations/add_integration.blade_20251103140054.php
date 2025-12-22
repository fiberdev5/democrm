<style>
@media (min-width: 767px) {
.custom-p-r{
    padding-right: 0px !important;
  }
  .custom-p-l{
    padding-left: 0px !important;
  }
}

.json-editor {
    font-family: 'Courier New', monospace;
    font-size: 13px;
}

.json-valid {
    border-color: #28a745 !important;
}

.json-invalid {
    border-color: #dc3545 !important;
}

.examples-link {
    color: #667eea;
    cursor: pointer;
    text-decoration: underline;
}

.examples-link:hover {
    color: #5568d3;
}
</style>

<form method="post" id="addIntegration" action="{{ route('super.admin.integration.store')}}" enctype="multipart/form-data">
    @csrf   
    
    <div class="row">
        <label class="col-sm-3 custom-p-r">Entegrasyon Adı<span style="font-weight: bold; color: red;">*</span></label>
        <div class="col-sm-9 custom-p-l">
            <input name="name" class="form-control buyukYaz" type="text" placeholder="Entegrasyon adını giriniz" required>
        </div>
    </div>
    
    <div class="row">
        <label class="col-sm-3 custom-p-r">Kategori<span style="font-weight: bold; color: red;">*</span></label>
        <div class="col-sm-9 custom-p-l">
            <select name="category" class="form-select" required>
                <option value="" selected disabled>-Seçiniz-</option>
                <option value="invoice">Fatura</option>
                <option value="sms">SMS</option>
                <option value="accounting">Muhasebe</option>
                <option value="other">Diğer</option>
            </select>
        </div>
    </div>
    
    <div class="row">
        <label class="col-sm-3 custom-p-r">Fiyat (₺)</label>
        <div class="col-sm-9 custom-p-l">
            <input name="price" class="form-control" type="number" step="0.01" min="0" placeholder="0.00">
            <small class="text-muted">Ücretsiz ise boş bırakabilirsiniz</small>
        </div>
    </div>
    
    <div class="row">
        <label class="col-sm-3 custom-p-r">Logo</label>
        <div class="col-sm-9 custom-p-l">
            <input name="logo" class="form-control" type="file" accept="image/*">
        </div>
    </div>
    
    <div class="row">
        <label class="col-sm-3 custom-p-r">Kısa Açıklama</label>
        <div class="col-sm-9 custom-p-l">
            <textarea name="description" type="text" class="form-control" rows="3" placeholder="Entegrasyon hakkında kısa açıklama yazınız..."></textarea>
        </div>
    </div>
    
    <div class="row">
        <label class="col-sm-3 custom-p-r">Detaylı Açıklama</label>
        <div class="col-sm-9 custom-p-l">
            <textarea id="elm1" name="explanation" type="text" class="form-control" aria-hidden="true"></textarea>
        </div>
    </div>
    
    <div class="row">
        <label class="col-sm-3 custom-p-r">API Form Alanları</label>
        <div class="col-sm-9 custom-p-l">
            <textarea name="api_fields" id="api_fields" class="form-control json-editor" rows="12" placeholder='[{"name":"username","label":"Kullanıcı Adı","type":"text","required":true}]'></textarea>
            <small class="text-muted">
                Firmalar için gerekli API alanlarını JSON formatında tanımlayın. 
                <a href="javascript:void(0);" class="examples-link" onclick="openApiFieldsModal()">Örnekleri göster</a>
            </small>
            <div id="jsonValidation" class="mt-2" style="display: none;"></div>
        </div>
    </div>
    
    <div class="row">
        <label class="col-sm-3 custom-p-r">Durum</label>
        <div class="col-sm-9 custom-p-l">
            <div class="form-check form-switch">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                <label class="form-check-label" for="is_active">Entegrasyon Aktif</label>
            </div>
        </div>
    </div>
    
    <div class="row">               
        <div class="col-sm-12 gonderBtn">
            <input type="submit" class="btn btn-sm btn-info waves-effect waves-light" value="Kaydet">
            <a href="{{route('super.admin.integrations')}}" class="btn btn-sm btn-light waves-effect">İptal</a>
        </div>
    </div>
</form>

<!-- API Fields Örnekleri Modal - FORM DIŞINDA -->
<div class="modal fade" id="apiFieldsModal" tabindex="-1" aria-labelledby="apiFieldsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="apiFieldsModalLabel">API Form Alanları Örnekleri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="sms-tab-btn" data-bs-toggle="tab" data-bs-target="#sms-tab" type="button" role="tab">SMS (NETGSM)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="accounting-tab-btn" data-bs-toggle="tab" data-bs-target="#accounting-tab" type="button" role="tab">Muhasebe (Paraşüt)</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="invoice-tab-btn" data-bs-toggle="tab" data-bs-target="#invoice-tab" type="button" role="tab">e-Fatura</button>
                    </li>
                </ul>
                
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="sms-tab" role="tabpanel">
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
                    
                    <div class="tab-pane fade" id="accounting-tab" role="tabpanel">
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
                    
                    <div class="tab-pane fade" id="invoice-tab" role="tabpanel">
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

// Modal açma fonksiyonu
function openApiFieldsModal() {
    var myModal = new bootstrap.Modal(document.getElementById('apiFieldsModal'));
    myModal.show();
}

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
    
    // Modal'ı kapat
    var myModalEl = document.getElementById('apiFieldsModal');
    var modal = bootstrap.Modal.getInstance(myModalEl);
    modal.hide();
    
    // Bildirim
    if (typeof toastr !== 'undefined') {
        toastr.success('Örnek JSON kopyalandı!');
    } else {
        alert('Örnek JSON kopyalandı!');
    }
}

$(document).ready(function () {
    $('#addIntegration').submit(function (event) {
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