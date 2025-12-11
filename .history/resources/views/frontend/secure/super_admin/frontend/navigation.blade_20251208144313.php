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
                                    <i class="fas fa-bars me-1"></i> Navbar Ayarları
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#footer-about" role="tab">
                                    <i class="fas fa-info-circle me-1"></i> Footer - Hakkımızda
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#footer-menus" role="tab">
                                    <i class="fas fa-list me-1"></i> Footer - Menüler
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#footer-contact" role="tab">
                                    <i class="fas fa-phone me-1"></i> Footer - İletişim
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#footer-social" role="tab">
                                    <i class="fas fa-share-alt me-1"></i> Footer - Sosyal Medya
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#footer-legal" role="tab">
                                    <i class="fas fa-file-contract me-1"></i> Footer - Yasal
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content p-3">
<!-- NAVBAR TAB -->
<!-- NAVBAR TAB -->
<div class="tab-pane active" id="navbar" role="tabpanel">
    <h5 class="mb-3">Navbar Ayarları</h5>
    <form id="navbarForm">
        <!-- Logo -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">Logo</h6>
            </div>
            <div class="card-body">
                @if(isset($navbar->content['logo']))
                    <div class="mb-2">
                        <img src="{{ asset($navbar->content['logo']) }}" alt="Logo" style="height: 60px;">
                    </div>
                @endif
                <input type="file" class="form-control" id="navbar_logo_file">
                <input type="hidden" id="navbar_logo_current" value="{{ $navbar->content['logo'] ?? '' }}">
            </div>
        </div>

        <!-- Menüler -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">Menüler (JSON Format)</h6>
            </div>
            <div class="card-body">
                <textarea class="form-control" id="menu_items_json" rows="15" style="font-family: monospace;">{{ json_encode($navbar->content['menu_items'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</textarea>
                <small class="text-muted d-block mt-2">JSON formatında düzenleyin</small>
            </div>
        </div>

        <!-- Butonlar -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">Butonlar</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Giriş Butonu</h6>
                        <input type="text" class="form-control mb-2" id="login_text" placeholder="Buton Metni" value="{{ $navbar->content['login_button']['text'] ?? 'Giriş Yap' }}">
                        <input type="text" class="form-control mb-2" id="login_icon" placeholder="İkon" value="{{ $navbar->content['login_button']['icon'] ?? 'fas fa-sign-in-alt' }}">
                        <input type="text" class="form-control mb-2" id="login_url" placeholder="URL" value="{{ $navbar->content['login_button']['url'] ?? '/kullanici-girisi' }}">
                        <select class="form-control" id="login_target">
                            <option value="_self">Aynı Sayfa</option>
                            <option value="_blank" selected>Yeni Sekme</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <h6>CTA Butonu</h6>
                        <input type="text" class="form-control mb-2" id="cta_text" placeholder="Buton Metni" value="{{ $navbar->content['cta_button']['text'] ?? 'Ücretsiz Dene' }}">
                        <input type="text" class="form-control mb-2" id="cta_url" placeholder="URL" value="{{ $navbar->content['cta_button']['url'] ?? '/kullanici-girisi' }}">
                        <select class="form-control" id="cta_target">
                            <option value="_self">Aynı Sayfa</option>
                            <option value="_blank" selected>Yeni Sekme</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Kaydet</button>
    </form>
</div>

                            <!-- FOOTER HAKKIMIZDA TAB -->
                            <div class="tab-pane" id="footer-about" role="tabpanel">
                                <h5 class="mb-3">Footer - Hakkımızda Bölümü</h5>
                                <form id="footerAboutForm">
                                    <div class="mb-3">
                                        <label class="form-label">Başlık</label>
                                        <input type="text" class="form-control" id="about_title" 
                                               value="{{ $footer->content['about']['title'] ?? 'Hakkımızda' }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Açıklama</label>
                                        <textarea class="form-control" id="about_description" rows="3">{{ $footer->content['about']['description'] ?? 'Teknik servis işletmeleri için yeni nesil, bulut tabanlı yönetim sistemi.' }}</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Kaydet
                                    </button>
                                </form>
                            </div>

                            <!-- FOOTER MENÜLER TAB -->
                            <div class="tab-pane" id="footer-menus" role="tabpanel">
                                <h5 class="mb-3">Footer - Menü Linkleri</h5>
                                
                                <div class="accordion" id="menuAccordion">
                                    <!-- Ürün Menüsü -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#productMenu">
                                                Ürün Menüsü
                                            </button>
                                        </h2>
                                        <div id="productMenu" class="accordion-collapse collapse show" data-bs-parent="#menuAccordion">
                                            <div class="accordion-body">
                                                <form id="productMenuForm">
                                                    <div class="mb-3">
                                                        <label class="form-label">Menü Başlığı</label>
                                                        <input type="text" class="form-control" id="product_title" 
                                                               value="{{ $footer->content['product_menu']['title'] ?? 'Menü' }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Linkler (Her satıra: Başlık|URL)</label>
                                                        <textarea class="form-control" id="product_links" rows="6" placeholder="Anasayfa|/&#10;Hakkımızda|/hakkimizda">{{ isset($footer->content['product_menu']['links']) ? collect($footer->content['product_menu']['links'])->map(function($link) { return $link['title'] . '|' . $link['url']; })->implode("\n") : '' }}</textarea>
                                                        <small class="text-muted">Örnek: Anasayfa|/</small>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save me-1"></i> Kaydet
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Özellikler Menüsü -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#featuresMenu">
                                                Özellikler Menüsü
                                            </button>
                                        </h2>
                                        <div id="featuresMenu" class="accordion-collapse collapse" data-bs-parent="#menuAccordion">
                                            <div class="accordion-body">
                                                <form id="featuresMenuForm">
                                                    <div class="mb-3">
                                                        <label class="form-label">Menü Başlığı</label>
                                                        <input type="text" class="form-control" id="features_title" 
                                                               value="{{ $footer->content['features_menu']['title'] ?? 'Özellikler' }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Linkler (Her satıra: Başlık|URL)</label>
                                                        <textarea class="form-control" id="features_links" rows="6" placeholder="Müşteri Yönetimi|/feature/musteri-yonetimi">{{ isset($footer->content['features_menu']['links']) ? collect($footer->content['features_menu']['links'])->map(function($link) { return $link['title'] . '|' . $link['url']; })->implode("\n") : '' }}</textarea>
                                                        <small class="text-muted">Örnek: Müşteri Yönetimi|/feature/musteri-yonetimi</small>
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

                            <!-- FOOTER İLETİŞİM TAB -->
                            <div class="tab-pane" id="footer-contact" role="tabpanel">
                                <h5 class="mb-3">Footer - İletişim Bilgileri</h5>
                                <form id="footerContactForm">
                                    <div class="mb-3">
                                        <label class="form-label">Bölüm Başlığı</label>
                                        <input type="text" class="form-control" id="contact_title" 
                                               value="{{ $footer->content['contact_menu']['title'] ?? 'İletişim' }}">
                                    </div>
                                    
                                    <h6 class="mt-4">İletişim Bilgileri</h6>
                                    <div id="contactItems">
                                        @if(isset($footer->content['contact_menu']['items']))
                                            @foreach($footer->content['contact_menu']['items'] as $index => $item)
                                                <div class="card mb-2 contact-item">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <label class="form-label">İkon</label>
                                                                <input type="text" class="form-control contact-icon" value="{{ $item['icon'] }}">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label">Metin</label>
                                                                <input type="text" class="form-control contact-text" value="{{ $item['text'] }}">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label">URL (opsiyonel)</label>
                                                                <input type="text" class="form-control contact-url" value="{{ $item['url'] ?? '' }}">
                                                            </div>
                                                            <div class="col-md-1">
                                                                <label class="form-label">&nbsp;</label>
                                                                <button type="button" class="btn btn-danger btn-sm w-100 remove-contact">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    
                                    <button type="button" class="btn btn-secondary mb-3" id="addContactItem">
                                        <i class="fas fa-plus me-1"></i> İletişim Bilgisi Ekle
                                    </button>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">İletişim Formu URL</label>
                                        <input type="text" class="form-control" id="contact_form_url" 
                                               value="{{ $footer->content['contact_menu']['contact_form_url'] ?? '/iletisim' }}">
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Kaydet
                                    </button>
                                </form>
                            </div>

                            <!-- FOOTER SOSYAL MEDYA TAB -->
                            <div class="tab-pane" id="footer-social" role="tabpanel">
                                <h5 class="mb-3">Footer - Sosyal Medya & Mobil Uygulamalar</h5>
                                <form id="footerSocialForm">
                                    <h6>Mobil Uygulamalar</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Başlık</label>
                                        <input type="text" class="form-control" id="mobile_title" 
                                               value="{{ $footer->content['mobile_apps']['title'] ?? 'Mobil Uygulamayı İndirin' }}">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">App Store URL</label>
                                            <input type="text" class="form-control" id="app_store" 
                                                   value="{{ $footer->content['mobile_apps']['app_store'] ?? '#' }}" 
                                                   placeholder="https://apps.apple.com/...">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Google Play URL</label>
                                            <input type="text" class="form-control" id="google_play" 
                                                   value="{{ $footer->content['mobile_apps']['google_play'] ?? '#' }}" 
                                                   placeholder="https://play.google.com/...">
                                        </div>
                                    </div>

                                    <hr>

                                    <h6>Sosyal Medya</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Facebook</label>
                                            <input type="text" class="form-control" id="social_facebook" 
                                                   value="{{ $footer->content['social_media']['facebook'] ?? '#' }}" 
                                                   placeholder="https://facebook.com/...">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Twitter</label>
                                            <input type="text" class="form-control" id="social_twitter" 
                                                   value="{{ $footer->content['social_media']['twitter'] ?? '#' }}" 
                                                   placeholder="https://twitter.com/...">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Instagram</label>
                                            <input type="text" class="form-control" id="social_instagram" 
                                                   value="{{ $footer->content['social_media']['instagram'] ?? '#' }}" 
                                                   placeholder="https://instagram.com/...">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">LinkedIn</label>
                                            <input type="text" class="form-control" id="social_linkedin" 
                                                   value="{{ $footer->content['social_media']['linkedin'] ?? '#' }}" 
                                                   placeholder="https://linkedin.com/...">
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Kaydet
                                    </button>
                                </form>
                            </div>

                            <!-- FOOTER YASAL TAB -->
                            <div class="tab-pane" id="footer-legal" role="tabpanel">
                                <h5 class="mb-3">Footer - Yasal Linkler & Copyright</h5>
                                <form id="footerLegalForm">
                                    <div class="mb-3">
                                        <label class="form-label">Yasal Linkler (Her satıra: Başlık|URL)</label>
                                        <textarea class="form-control" id="legal_links" rows="4" placeholder="Gizlilik Politikası|/gizlilik&#10;KVKK|/kvkk">{{ isset($footer->content['legal_links']) ? collect($footer->content['legal_links'])->map(function($link) { return $link['title'] . '|' . $link['url']; })->implode("\n") : '' }}</textarea>
                                        <small class="text-muted">Örnek: Gizlilik Politikası|/gizlilik</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Copyright Metni</label>
                                        <input type="text" class="form-control" id="copyright" 
                                               value="{{ $footer->content['copyright'] ?? '© ' . date('Y') . ' Serbis. Tüm hakları saklıdır.' }}">
                                        <small class="text-muted">{{ '{YEAR}' }} otomatik olarak güncel yıl ile değiştirilir</small>
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

// Menü Ekleme - Düzeltilmiş
$('#addMenuItem').on('click', function() {
    const index = $('.menu-item').length;
    const html = `
        <div class="card mb-3 menu-item" data-index="${index}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h6 class="mb-0">Menü ${index + 1}</h6>
                    <button type="button" class="btn btn-danger btn-sm remove-menu">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Başlık</label>
                        <input type="text" class="form-control menu-title" placeholder="ANASAYFA">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tip</label>
                        <select class="form-control menu-type">
                            <option value="link" selected>Link</option>
                            <option value="dropdown">Dropdown</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3 menu-url-field">
                        <label class="form-label">URL</label>
                        <input type="text" class="form-control menu-url" placeholder="/" value="">
                    </div>
                </div>
                
                <div class="dropdown-items-section mt-3" style="display:none;">
                    <label class="form-label">Dropdown İçeriği (Her satıra: Başlık|URL veya divider)</label>
                    <textarea class="form-control dropdown-items" rows="5" placeholder="Müşteri Yönetimi|/feature/musteri-yonetimi"></textarea>
                    <small class="text-muted">Örnek: Müşteri Yönetimi|/feature/musteri-yonetimi veya "divider" yazın</small>
                </div>
            </div>
        </div>
    `;
    $('#menuItems').append(html);
    
    // Test için console'a yaz
    console.log('Yeni menü eklendi. Toplam menü:', $('.menu-item').length);
});
// Menü Silme
$(document).on('click', '.remove-menu', function() {
    $(this).closest('.menu-item').remove();
    $('.menu-item').each(function(index) {
        $(this).attr('data-index', index);
        $(this).find('h6').text('Menü ' + (index + 1));
    });
});

// Tip değiştiğinde URL/Dropdown alanını göster/gizle
$(document).on('change', '.menu-type', function() {
    const $parent = $(this).closest('.menu-item');
    const type = $(this).val();
    
    if (type === 'dropdown') {
        $parent.find('.menu-url-field').hide();
        $parent.find('.dropdown-items-section').show();
    } else {
        $parent.find('.menu-url-field').show();
        $parent.find('.dropdown-items-section').hide();
    }
});

// İletişim bilgisi ekleme
$('#addContactItem').on('click', function() {
    const html = `
        <div class="card mb-2 contact-item">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">İkon</label>
                        <input type="text" class="form-control contact-icon" placeholder="fas fa-phone">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Metin</label>
                        <input type="text" class="form-control contact-text">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">URL (opsiyonel)</label>
                        <input type="text" class="form-control contact-url">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm w-100 remove-contact">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('#contactItems').append(html);
});

// İletişim bilgisi silme
$(document).on('click', '.remove-contact', function() {
    $(this).closest('.contact-item').remove();
});

// Helper Functions
function parseLinks(text) {
    const links = [];
    const lines = text.split('\n');
    lines.forEach(line => {
        line = line.trim();
        if (line) {
            const parts = line.split('|');
            if (parts.length === 2) {
                links.push({
                    title: parts[0].trim(),
                    url: parts[1].trim()
                });
            }
        }
    });
    return links;
}

function updateFooterSection(section, data) {
    const existingFooter = {!! json_encode($footer->content ?? []) !!};
    existingFooter[section] = data;
    saveContent('footer_content', existingFooter);
}

function saveContent(section, content, hash) {
    $.ajax({
        url: '{{ route("super.admin.frontend.content.update") }}',
        method: 'POST',
        data: JSON.stringify({
            section: section,
            content: content
        }),
        contentType: 'application/json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            toastr.success('İçerik güncellendi');
            if (hash) {
                window.location.hash = hash;
            }
            setTimeout(function() {
                location.reload();
            }, 1500);
        },
        error: function(xhr) {
            toastr.error('Bir hata oluştu');
            console.error(xhr.responseText);
        }
    });
}

// NAVBAR FORM
$('#navbarForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    const logoFile = $('#navbar_logo_file')[0].files[0];
    
    // JSON'dan menüleri al
    let menuItems = [];
    try {
        menuItems = JSON.parse($('#menu_items_json').val());
    } catch(e) {
        toastr.error('Geçersiz JSON formatı!');
        return;
    }
    
    const content = {
        logo: $('#navbar_logo_current').val(),
        menu_items: menuItems,
        login_button: {
            text: $('#login_text').val(),
            icon: $('#login_icon').val(),
            url: $('#login_url').val(),
            target: $('#login_target').val()
        },
        cta_button: {
            text: $('#cta_text').val(),
            url: $('#cta_url').val(),
            target: $('#cta_target').val()
        }
    };
    
    formData.append('section', 'navbar_content');
    formData.append('content', JSON.stringify(content));
    
    if (logoFile) {
        formData.append('logo', logoFile);
    }
    
    $.ajax({
        url: '{{ route("super.admin.frontend.content.update") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            toastr.success('Kaydedildi');
            setTimeout(() => location.reload(), 1000);
        },
        error: function(xhr) {
            toastr.error('Hata oluştu');
        }
    });
});
// FOOTER ABOUT FORM
$('#footerAboutForm').on('submit', function(e) {
    e.preventDefault();
    updateFooterSection('about', {
        title: $('#about_title').val(),
        description: $('#about_description').val()
    });
});

// PRODUCT MENU FORM
$('#productMenuForm').on('submit', function(e) {
    e.preventDefault();
    const links = parseLinks($('#product_links').val());
    updateFooterSection('product_menu', {
        title: $('#product_title').val(),
        links: links
    });
});

// FEATURES MENU FORM
$('#featuresMenuForm').on('submit', function(e) {
    e.preventDefault();
    const links = parseLinks($('#features_links').val());
    updateFooterSection('features_menu', {
        title: $('#features_title').val(),
        links: links
    });
});

// FOOTER CONTACT FORM
$('#footerContactForm').on('submit', function(e) {
    e.preventDefault();
    
    const items = [];
    $('.contact-item').each(function() {
        const item = {
            icon: $(this).find('.contact-icon').val(),
            text: $(this).find('.contact-text').val(),
            url: $(this).find('.contact-url').val() || null
        };
        items.push(item);
    });
    
    updateFooterSection('contact_menu', {
        title: $('#contact_title').val(),
        items: items,
        contact_form_url: $('#contact_form_url').val()
    });
});

// FOOTER SOCIAL FORM
$('#footerSocialForm').on('submit', function(e) {
    e.preventDefault();
    
    const existingFooter = {!! json_encode($footer->content ?? []) !!};
    
    const content = {
        ...existingFooter,
        mobile_apps: {
            title: $('#mobile_title').val(),
            app_store: $('#app_store').val(),
            google_play: $('#google_play').val()
        },
        social_media: {
            facebook: $('#social_facebook').val(),
            twitter: $('#social_twitter').val(),
            instagram: $('#social_instagram').val(),
            linkedin: $('#social_linkedin').val()
        }
    };
    
    saveContent('footer_content', content, '#footer-social');
});

// FOOTER LEGAL FORM
$('#footerLegalForm').on('submit', function(e) {
    e.preventDefault();
    
    const links = parseLinks($('#legal_links').val());
    const existingFooter = {!! json_encode($footer->content ?? []) !!};
    
    const content = {
        ...existingFooter,
        legal_links: links,
        copyright: $('#copyright').val()
    };
    
    saveContent('footer_content', content, '#footer-legal');
});
</script>
@endsection