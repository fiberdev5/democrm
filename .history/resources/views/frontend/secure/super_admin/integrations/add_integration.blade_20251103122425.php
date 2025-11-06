<style>
  
@media (min-width: 767px) {
.custom-p-r{
    padding-right: 0px !important;
  }
  .custom-p-l{
    padding-left: 0px !important;
  }

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
                                <textarea  name="description" type="text" class="form-control" rows="3" placeholder="Entegrasyon hakkında kısa açıklama yazınız..."></textarea>
                            </div>
                        </div>
                        
                        <div class="row">
                            <label class="col-sm-3 custom-p-r">Detaylı Açıklama</label>
                            <div class="col-sm-9 custom-p-l">
                                <textarea id="elm1" name="explanation" type="text" class="form-control" aria-hidden="true"></textarea>
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

                        <div class=" row form-group">
    <label>API Form Alanları (JSON)</label>
    <textarea 
        name="api_fields" 
        id="api_fields" 
        class="form-control" 
        rows="15"
        placeholder='[{"name":"username","label":"Kullanıcı Adı","type":"text","required":true}]'
    >{{ old('api_fields', $integration->api_fields ? json_encode($integration->api_fields, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
    <small class="text-muted">
        API form alanlarını JSON formatında tanımlayın. 
        <a href="#" data-bs-toggle="modal" data-bs-target="#apiFieldsExamplesModal">Örnekleri göster</a>
    </small>
</div>

{{-- Örnekler Modal --}}
<div class="modal fade" id="apiFieldsExamplesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">API Fields Örnekleri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#sms-example">SMS (NETGSM)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#accounting-example">Muhasebe (Paraşüt)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#invoice-example">Fatura (eFatura)</a>
                    </li>
                </ul>
                
                <div class="tab-content mt-3">
                    <div class="tab-pane fade show active" id="sms-example">
<pre><code>[
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
                        <button class="btn btn-sm btn-primary" onclick="copyToClipboard('sms-example')">
                            <i class="fas fa-copy"></i> Kopyala
                        </button>
                    </div>
                    
                    <div class="tab-pane fade" id="accounting-example">
<pre><code>[
    {
        "name": "company_id",
        "label": "Şirket ID",
        "type": "text",
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
                        <button class="btn btn-sm btn-primary" onclick="copyToClipboard('accounting-example')">
                            <i class="fas fa-copy"></i> Kopyala
                        </button>
                    </div>
                    
                    <div class="tab-pane fade" id="invoice-example">
<pre><code>[
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
        "required": true,
        "help": "10 veya 11 haneli numara"
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
                        <button class="btn btn-sm btn-primary" onclick="copyToClipboard('invoice-example')">
                            <i class="fas fa-copy"></i> Kopyala
                        </button>
                    </div>
                </div>
            </div>
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

<script>
    
    $('.buyukYaz').keyup(function(){
        this.value = this.value.toUpperCase();
    });

    $(document).ready(function () {
        $('#addIntegration').submit(function (event) {
            if (tinymce.get('elm1')) {
                tinymce.get('elm1').save();
            }
            var formIsValid = true;
            $(this).find('input, select').each(function () {
                var isRequired = $(this).prop('required');
                var isEmpty = !$(this).val();
                if (isRequired && isEmpty) {
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

    function copyToClipboard(tabId) {
    const codeElement = document.querySelector(`#${tabId} pre code`);
    const text = codeElement.textContent;
    
    navigator.clipboard.writeText(text).then(() => {
        toastr.success('JSON kopyalandı!');
    });
}

// JSON Validation
$('#api_fields').on('blur', function() {
    try {
        const json = $(this).val();
        if (json) {
            JSON.parse(json);
            $(this).removeClass('is-invalid').addClass('is-valid');
        }
    } catch (e) {
        $(this).removeClass('is-valid').addClass('is-invalid');
        toastr.error('Geçersiz JSON formatı!');
    }
});
</script>