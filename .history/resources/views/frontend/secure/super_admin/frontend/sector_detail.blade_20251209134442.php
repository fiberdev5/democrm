@extends('frontend.secure.user_master')
@section('user')
<div class="page-content">
    <div class="container-fluid">
        
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Sektör Detay Yönetimi: {{ ucfirst(str_replace('-', ' ', $slug)) }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.frontend.sectors-content') }}">Sektörler</a></li>
                            <li class="breadcrumb-item active">{{ $slug }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        
                        <!-- Tabs -->
                        <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#hero" role="tab">
                                    <i class="fas fa-image me-1"></i> Hero Bölümü
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#stats" role="tab">
                                    <i class="fas fa-chart-bar me-1"></i> İstatistikler
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#features" role="tab">
                                    <i class="fas fa-star me-1"></i> Özellikler
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#services" role="tab">
                                    <i class="fas fa-list me-1"></i> Hizmetler
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#faqs" role="tab">
                                    <i class="fas fa-question-circle me-1"></i> SSS
                                </a>
                            </li>
                        </ul>

                        <form action="{{ route('super.admin.frontend.sector-detail.update', $slug) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="tab-content p-3">
                                
                                <!-- HERO TAB -->
                                <div class="tab-pane active" id="hero" role="tabpanel">
                                    <h5 class="mb-3">Hero Bölümü</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <label class="form-label">Sayfa Başlığı</label>
                                            <input type="text" class="form-control" name="title" value="{{ $sector->content['title'] ?? '' }}" placeholder="Elektrik-Elektronik Teknik Servis Programı">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">İkon</label>
                                            <input type="text" class="form-control" name="icon" value="{{ $sector->content['icon'] ?? 'fas fa-plug' }}" placeholder="fas fa-plug">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Açıklama</label>
                                        <textarea class="form-control" name="description" rows="4">{{ $sector->content['description'] ?? '' }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Hero Görseli</label>
                                        @if(isset($sector->content['hero_image']))
                                            <div class="mb-2">
                                                <img src="{{ asset($sector->content['hero_image']) }}" style="height: 100px; border-radius: 5px;">
                                            </div>
                                        @endif
                                        <input type="file" class="form-control" name="hero_image" accept="image/*">
                                        <input type="hidden" name="hero_image_current" value="{{ $sector->content['hero_image'] ?? '' }}">
                                    </div>
                                </div>

                                <!-- STATS TAB -->
                                <div class="tab-pane" id="stats" role="tabpanel">
                                    <h5 class="mb-3">İstatistikler</h5>
                                    
                                    <div id="statsContainer">
                                        @if(isset($sector->content['stats']))
                                            @foreach($sector->content['stats'] as $index => $stat)
                                                <div class="card mb-2 stat-item">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-5">
                                                                <label class="form-label">Sayı</label>
                                                                <input type="text" class="form-control stat-number" value="{{ $stat['number'] }}" placeholder="500+">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Etiket</label>
                                                                <input type="text" class="form-control stat-label" value="{{ $stat['label'] }}" placeholder="Aktif İşletme">
                                                            </div>
                                                            <div class="col-md-1">
                                                                <label class="form-label">&nbsp;</label>
                                                                <button type="button" class="btn btn-danger btn-sm w-100 remove-stat">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-secondary btn-sm" id="addStat">
                                        <i class="fas fa-plus me-1"></i> İstatistik Ekle
                                    </button>
                                </div>

                                <!-- FEATURES TAB -->
                                <div class="tab-pane" id="features" role="tabpanel">
                                    <h5 class="mb-3">Özellikler</h5>
                                    
                                    <div id="featuresContainer">
                                        @if(isset($sector->content['features']))
                                            @foreach($sector->content['features'] as $index => $feature)
                                                <div class="card mb-2 feature-item">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-2">
                                                                <label class="form-label">İkon</label>
                                                                <input type="text" class="form-control feature-icon" value="{{ $feature['icon'] }}" placeholder="fas fa-tasks">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label">Başlık</label>
                                                                <input type="text" class="form-control feature-title" value="{{ $feature['title'] }}" placeholder="Arıza Takip Sistemi">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Açıklama</label>
                                                                <textarea class="form-control feature-description" rows="2">{{ $feature['description'] }}</textarea>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <label class="form-label">&nbsp;</label>
                                                                <button type="button" class="btn btn-danger btn-sm w-100 remove-feature">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-secondary btn-sm" id="addFeature">
                                        <i class="fas fa-plus me-1"></i> Özellik Ekle
                                    </button>
                                </div>

                                <!-- SERVICES TAB -->
                                <div class="tab-pane" id="services" role="tabpanel">
                                    <h5 class="mb-3">Hizmetler ve Avantajlar</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Hizmetler</h6>
                                            <div class="mb-3">
                                                <label class="form-label">Hizmetler Listesi (Her satıra bir hizmet)</label>
                                                <textarea class="form-control" name="services" rows="8" placeholder="Servis Kayıt ve Takip&#10;Müşteri Yönetimi (CRM)">{{ isset($sector->content['services']) ? implode("\n", $sector->content['services']) : '' }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Avantajlar</h6>
                                            <div class="mb-3">
                                                <label class="form-label">Avantajlar Listesi (Her satıra bir avantaj)</label>
                                                <textarea class="form-control" name="benefits" rows="8" placeholder="Kolay kullanılabilir arayüz&#10;Mobil uyumlu">{{ isset($sector->content['benefits']) ? implode("\n", $sector->content['benefits']) : '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- FAQS TAB -->
                                <div class="tab-pane" id="faqs" role="tabpanel">
                                    <h5 class="mb-3">Sıkça Sorulan Sorular</h5>
                                    
                                    <div id="faqsContainer">
                                        @if(isset($sector->content['faqs']))
                                            @foreach($sector->content['faqs'] as $index => $faq)
                                                <div class="card mb-2 faq-item">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-11">
                                                                <div class="mb-2">
                                                                    <label class="form-label">Soru</label>
                                                                    <input type="text" class="form-control faq-question" value="{{ $faq['question'] }}" placeholder="Programı kullanmak için teknik bilgiye ihtiyacım var mı?">
                                                                </div>
                                                                <div>
                                                                    <label class="form-label">Cevap</label>
                                                                    <textarea class="form-control faq-answer" rows="2">{{ $faq['answer'] }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1">
                                                                <label class="form-label">&nbsp;</label>
                                                                <button type="button" class="btn btn-danger btn-sm w-100 remove-faq">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-secondary btn-sm" id="addFaq">
                                        <i class="fas fa-plus me-1"></i> Soru Ekle
                                    </button>
                                </div>

                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-1"></i> Tüm Değişiklikleri Kaydet
                                </button>
                                <a href="{{ route('sector.detail', $slug) }}" target="_blank" class="btn btn-info">
                                    <i class="fas fa-external-link-alt me-1"></i> Sayfayı Görüntüle
                                </a>
                                <a href="{{ route('super.admin.frontend.sectors-content') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Geri Dön
                                </a>
                            </div>
                        </form>

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

    // ========== STATS ==========
    $('#addStat').on('click', function() {
        const html = `
            <div class="card mb-2 stat-item">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <label class="form-label">Sayı</label>
                            <input type="text" class="form-control stat-number" placeholder="500+">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Etiket</label>
                            <input type="text" class="form-control stat-label" placeholder="Aktif İşletme">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-sm w-100 remove-stat">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#statsContainer').append(html);
    });

    $(document).on('click', '.remove-stat', function() {
        $(this).closest('.stat-item').remove();
    });

    // ========== FEATURES ==========
    $('#addFeature').on('click', function() {
        const html = `
            <div class="card mb-2 feature-item">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <label class="form-label">İkon</label>
                            <input type="text" class="form-control feature-icon" placeholder="fas fa-tasks">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Başlık</label>
                            <input type="text" class="form-control feature-title" placeholder="Özellik Başlığı">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Açıklama</label>
                            <textarea class="form-control feature-description" rows="2"></textarea>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-sm w-100 remove-feature">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#featuresContainer').append(html);
    });

    $(document).on('click', '.remove-feature', function() {
        $(this).closest('.feature-item').remove();
    });

    // ========== FAQS ==========
    $('#addFaq').on('click', function() {
        const html = `
            <div class="card mb-2 faq-item">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-11">
                            <div class="mb-2">
                                <label class="form-label">Soru</label>
                                <input type="text" class="form-control faq-question" placeholder="Soru buraya">
                            </div>
                            <div>
                                <label class="form-label">Cevap</label>
                                <textarea class="form-control faq-answer" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-sm w-100 remove-faq">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('#faqsContainer').append(html);
    });

    $(document).on('click', '.remove-faq', function() {
        $(this).closest('.faq-item').remove();
    });

    // ========== FORM SUBMIT ==========
    $('form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Stats Topla
        const stats = [];
        $('.stat-item').each(function() {
            const stat = {
                number: $(this).find('.stat-number').val(),
                label: $(this).find('.stat-label').val()
            };
            if(stat.number && stat.label) {
                stats.push(stat);
            }
        });
        
        // Features Topla
        const features = [];
        $('.feature-item').each(function() {
            const feature = {
                icon: $(this).find('.feature-icon').val(),
                title: $(this).find('.feature-title').val(),
                description: $(this).find('.feature-description').val()
            };
            if(feature.title && feature.description) {
                features.push(feature);
            }
        });
        
        // Services & Benefits (satır satır)
        const servicesText = $('[name="services"]').val();
        const services = servicesText.split('\n').map(s => s.trim()).filter(s => s.length > 0);
        
        const benefitsText = $('[name="benefits"]').val();
        const benefits = benefitsText.split('\n').map(b => b.trim()).filter(b => b.length > 0);
        
        // FAQs Topla
        const faqs = [];
        $('.faq-item').each(function() {
            const faq = {
                question: $(this).find('.faq-question').val(),
                answer: $(this).find('.faq-answer').val()
            };
            if(faq.question && faq.answer) {
                faqs.push(faq);
            }
        });
        
        // JSON Content Oluştur
        const content = {
            title: $('[name="title"]').val(),
            icon: $('[name="icon"]').val(),
            hero_image: $('[name="hero_image_current"]').val(),
            description: $('[name="description"]').val(),
            stats: stats,
            features: features,
            services: services,
            benefits: benefits,
            faqs: faqs
        };
        
        console.log('Gönderilecek Content:', content);
        
        formData.append('content', JSON.stringify(content));
        
        // AJAX Submit
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success('Sektör detay içeriği güncellendi!');
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                toastr.error('Bir hata oluştu!');
                console.error(xhr);
            }
        });
    });
});
</script>
@endsection