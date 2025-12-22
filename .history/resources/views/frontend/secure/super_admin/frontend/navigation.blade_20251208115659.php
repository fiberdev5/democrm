@extends('frontend.secure.user_master')
@section('user')
<div class="page-content">
    <div class="container-fluid">
        
        <!-- Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Menü & Link Yönetimi</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Menü & Linkler</li>
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
                                    <i class="fas fa-bars me-1"></i> Navbar Linkleri
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#footer" role="tab">
                                    <i class="fas fa-link me-1"></i> Footer Linkleri
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#social" role="tab">
                                    <i class="fab fa-facebook me-1"></i> Sosyal Medya
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#apps" role="tab">
                                    <i class="fas fa-mobile-alt me-1"></i> Mobil Uygulamalar
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content p-3">
                            <!-- Navbar Tab -->
                            <div class="tab-pane active" id="navbar" role="tabpanel">
                                <h5 class="mb-3">Navbar Menü Linkleri</h5>
                                <form id="navbarForm">
                                    <div class="mb-3">
                                        <label class="form-label">Navbar Linkleri (JSON Format)</label>
                                        <textarea class="form-control" id="navbar_links" rows="15" style="font-family: monospace;">{{ json_encode($navbar->content ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</textarea>
                                        <small class="text-muted">JSON formatında navbar linklerini düzenleyin</small>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <strong>Format Örneği:</strong>
                                        <pre style="margin-top: 10px;">{
  "links": [
    {
      "title": "Anasayfa",
      "url": "/",
      "target": "_self"
    },
    {
      "title": "Hakkımızda",
      "url": "/hakkimizda",
      "target": "_self"
    }
  ]
}</pre>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Kaydet</button>
                                </form>
                            </div>

                            <!-- Footer Tab -->
                            <div class="tab-pane" id="footer" role="tabpanel">
                                <h5 class="mb-3">Footer Menü Linkleri</h5>
                                <form id="footerForm">
                                    <div class="mb-3">
                                        <label class="form-label">Footer Linkleri (JSON Format)</label>
                                        <textarea class="form-control" id="footer_links" rows="20" style="font-family: monospace;">{{ json_encode($footer->content ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</textarea>
                                        <small class="text-muted">JSON formatında footer linklerini düzenleyin</small>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <strong>Format Örneği:</strong>
                                        <pre style="margin-top: 10px;">{
  "product": [
    {
      "title": "Anasayfa",
      "url": "/"
    }
  ],
  "features": [
    {
      "title": "Müşteri Yönetimi",
      "url": "/feature/musteri-yonetimi"
    }
  ],
  "contact": [
    {
      "title": "İletişim",
      "url": "/iletisim"
    }
  ]
}</pre>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Kaydet</button>
                                </form>
                            </div>

                            <!-- Social Media Tab -->
                            <div class="tab-pane" id="social" role="tabpanel">
                                <h5 class="mb-3">Sosyal Medya Linkleri</h5>
                                <form id="socialForm">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Facebook</label>
                                            <input type="text" class="form-control" id="social_facebook" value="{{ $socialMedia->content['facebook'] ?? '' }}" placeholder="https://facebook.com/...">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Twitter</label>
                                            <input type="text" class="form-control" id="social_twitter" value="{{ $socialMedia->content['twitter'] ?? '' }}" placeholder="https://twitter.com/...">
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Instagram</label>
                                            <input type="text" class="form-control" id="social_instagram" value="{{ $socialMedia->content['instagram'] ?? '' }}" placeholder="https://instagram.com/...">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">LinkedIn</label>
                                            <input type="text" class="form-control" id="social_linkedin" value="{{ $socialMedia->content['linkedin'] ?? '' }}" placeholder="https://linkedin.com/...">
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Kaydet</button>
                                </form>
                            </div>

                            <!-- Mobile Apps Tab -->
                            <div class="tab-pane" id="apps" role="tabpanel">
                                <h5 class="mb-3">Mobil Uygulama Linkleri</h5>
                                <form id="appsForm">
                                    <div class="mb-3">
                                        <label class="form-label">App Store URL</label>
                                        <input type="text" class="form-control" id="app_store_url" value="{{ $mobileApps->content['app_store'] ?? '' }}" placeholder="https://apps.apple.com/...">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Google Play URL</label>
                                        <input type="text" class="form-control" id="google_play_url" value="{{ $mobileApps->content['google_play'] ?? '' }}" placeholder="https://play.google.com/...">
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Kaydet</button>
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
        const content = JSON.parse($('#navbar_links').val());
        
        const data = {
            section: 'navbar_links',
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
                toastr.success('Navbar linkleri güncellendi');
                window.location.hash = '#navbar';
            },
            error: function(xhr) {
                toastr.error('Bir hata oluştu');
            }
        });
    } catch(e) {
        toastr.error('Geçersiz JSON formatı!');
    }
});

// Footer Form Submit
$('#footerForm').on('submit', function(e) {
    e.preventDefault();
    
    try {
        const content = JSON.parse($('#footer_links').val());
        
        const data = {
            section: 'footer_links',
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
                toastr.success('Footer linkleri güncellendi');
                window.location.hash = '#footer';
            },
            error: function(xhr) {
                toastr.error('Bir hata oluştu');
            }
        });
    } catch(e) {
        toastr.error('Geçersiz JSON formatı!');
    }
});

// Social Media Form Submit
$('#socialForm').on('submit', function(e) {
    e.preventDefault();
    
    const data = {
        section: 'social_media',
        content: {
            facebook: $('#social_facebook').val(),
            twitter: $('#social_twitter').val(),
            instagram: $('#social_instagram').val(),
            linkedin: $('#social_linkedin').val()
        }
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
            toastr.success('Sosyal medya linkleri güncellendi');
            window.location.hash = '#social';
        },
        error: function(xhr) {
            toastr.error('Bir hata oluştu');
        }
    });
});

// Mobile Apps Form Submit
$('#appsForm').on('submit', function(e) {
    e.preventDefault();
    
    const data = {
        section: 'mobile_apps',
        content: {
            app_store: $('#app_store_url').val(),
            google_play: $('#google_play_url').val()
        }
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
            toastr.success('Mobil uygulama linkleri güncellendi');
            window.location.hash = '#apps';
        },
        error: function(xhr) {
            toastr.error('Bir hata oluştu');
        }
    });
});
</script>
@endsection