@extends('frontend.secure.user_master')
@section('user')
<div class="page-content">
    <div class="container-fluid">
        
        <!-- Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Navbar & Footer Yönetimi</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Navbar & Footer</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#navbar" role="tab">
                                    <i class="fas fa-bars me-1"></i> Navbar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#footer" role="tab">
                                    <i class="fas fa-stream me-1"></i> Footer
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content p-3">
                            <!-- Navbar Tab -->
                            <div class="tab-pane active" id="navbar" role="tabpanel">
                                <h5 class="mb-3">Navbar İçeriği</h5>
                                <form id="navbarForm">
                                    <div class="mb-3">
                                        <label class="form-label">Navbar İçeriği (JSON Format)</label>
                                        <textarea class="form-control" id="navbar_content" rows="25" style="font-family: monospace; font-size: 0.9rem;">{{ json_encode($navbar->content ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</textarea>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <strong>Format Örneği:</strong>
                                        <pre style="margin-top: 10px; font-size: 0.85rem;">{
  "logo": "frontend/img/logo_turkce.png",
  "menu_items": [
    {
      "title": "Anasayfa",
      "url": "/",
      "type": "link"
    },
    {
      "title": "Özellikler",
      "type": "dropdown",
      "items": [
        {"title": "Müşteri Yönetimi", "url": "/feature/musteri-yonetimi"},
        {"divider": true},
        {"title": "Tümü →", "url": "/ozellikler", "bold": true}
      ]
    }
  ],
  "login_button": {
    "text": "Giriş Yap",
    "icon": "fas fa-sign-in-alt",
    "url": "/kullanici-girisi",
    "target": "_blank"
  },
  "cta_button": {
    "text": "Ücretsiz Dene",
    "url": "/kullanici-girisi",
    "target": "_blank"
  }
}</pre>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Kaydet
                                    </button>
                                </form>
                            </div>

                            <!-- Footer Tab -->
                            <div class="tab-pane" id="footer" role="tabpanel">
                                <h5 class="mb-3">Footer İçeriği</h5>
                                <form id="footerForm">
                                    <div class="mb-3">
                                        <label class="form-label">Footer İçeriği (JSON Format)</label>
                                        <textarea class="form-control" id="footer_content" rows="30" style="font-family: monospace; font-size: 0.9rem;">{{ json_encode($footer->content ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</textarea>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <strong>Format Örneği:</strong>
                                        <pre style="margin-top: 10px; font-size: 0.85rem;">{
  "about": {
    "title": "Hakkımızda",
    "description": "Teknik servis yönetim sistemi."
  },
  "product_menu": {
    "title": "Ürün",
    "links": [
      {"title": "Anasayfa", "url": "/"},
      {"title": "Hakkımızda", "url": "/hakkimizda"}
    ]
  },
  "features_menu": {
    "title": "Özellikler",
    "links": [
      {"title": "Müşteri Yönetimi", "url": "/feature/musteri-yonetimi"}
    ]
  },
  "contact_menu": {
    "title": "İletişim",
    "items": [
      {"icon": "fas fa-phone", "text": "0212 909 2861", "url": "tel:02129092861"},
      {"icon": "fas fa-envelope", "text": "info@serbis.com", "url": "mailto:info@serbis.com"}
    ],
    "contact_form_url": "/iletisim"
  },
  "mobile_apps": {
    "title": "Mobil Uygulamayı İndirin",
    "app_store": "#",
    "google_play": "#"
  },
  "social_media": {
    "facebook": "#",
    "twitter": "#",
    "instagram": "#",
    "linkedin": "#"
  },
  "legal_links": [
    {"title": "Gizlilik Politikası", "url": "/gizlilik"},
    {"title": "Kullanım Şartları", "url": "/kullanim-sartlari"},
    {"title": "KVKK", "url": "/kvkk"}
  ],
  "copyright": "© 2024 Serbis. Tüm hakları saklıdır."
}</pre>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Kaydet
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    // Tab hash kontrolü
    let hash = window.location.hash;
    if (hash) {
        $('.nav-tabs a[href="' + hash + '"]').tab('show');
    }
    
    $('.nav-tabs a').on('shown.bs.tab', function(e) {
        window.location.hash = e.target.hash;
    });
});

// Navbar Form Submit
$('#navbarForm').on('submit', function(e) {
    e.preventDefault();
    
    try {
        const content = JSON.parse($('#navbar_content').val());
        
        const data = {
            section: 'navbar_content',
            content: content
        };
        
        $.ajax({
            url: '{{ route("super.admin.frontend.content.update") }}',
            method: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                toastr.success('Navbar içeriği güncellendi');
                window.location.hash = '#navbar';
            },
            error: function(xhr) {
                toastr.error('Bir hata oluştu');
            }
        });
    } catch(e) {
        toastr.error('Geçersiz JSON formatı! Lütfen kontrol edin.');
        console.error(e);
    }
});

// Footer Form Submit
$('#footerForm').on('submit', function(e) {
    e.preventDefault();
    
    try {
        const content = JSON.parse($('#footer_content').val());
        
        const data = {
            section: 'footer_content',
            content: content
        };
        
        $.ajax({
            url: '{{ route("super.admin.frontend.content.update") }}',
            method: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                toastr.success('Footer içeriği güncellendi');
                window.location.hash = '#footer';
            },
            error: function(xhr) {
                toastr.error('Bir hata oluştu');
            }
        });
    } catch(e) {
        toastr.error('Geçersiz JSON formatı! Lütfen kontrol edin.');
        console.error(e);
    }
});
</script>
@endsection