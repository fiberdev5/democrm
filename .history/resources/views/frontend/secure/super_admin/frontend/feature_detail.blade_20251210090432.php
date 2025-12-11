@extends('frontend.secure.user_master')
@section('user')
<div class="page-content">
    <div class="container-fluid">
        
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Özellik Detay Yönetimi: {{ ucfirst(str_replace('-', ' ', $slug)) }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.frontend.features-content') }}">Özellikler</a></li>
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
                                <a class="nav-link" data-bs-toggle="tab" href="#benefits" role="tab">
                                    <i class="fas fa-star me-1"></i> Avantajlar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#features-stats" role="tab">
                                    <i class="fas fa-list me-1"></i> Özellikler & İstatistikler
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#faqs" role="tab">
                                    <i class="fas fa-question-circle me-1"></i> SSS
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#cta" role="tab">
                                    <i class="fas fa-bullhorn me-1"></i> CTA
                                </a>
                            </li>
                        </ul>

                        <form action="{{ route('super.admin.frontend.feature-detail.update', $slug) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="tab-content p-3">
                                
                                <!-- HERO TAB -->
                                <div class="tab-pane active" id="hero" role="tabpanel">
                                    <h5 class="mb-3">Hero Bölümü</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Başlık</label>
                                            <input type="text" class="form-control" name="title" value="{{ $feature->content['title'] ?? '' }}" placeholder="Müşteri Yönetimi">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Alt Başlık</label>
                                            <input type="text" class="form-control" name="subtitle" value="{{ $feature->content['subtitle'] ?? '' }}" placeholder="Tüm müşteri bilgilerinizi tek merkezden yönetin">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Açıklama</label>
                                        <textarea class="form-control" name="description" rows="4">{{ $feature->content['description'] ?? '' }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Hero Görseli</label>
                                        @if(isset($feature->content['hero_image']))
                                            <div class="mb-2">
                                                <img src="{{ asset($feature->content['hero_image']) }}" style="height: 100px; border-radius: 5px;">
                                            </div>
                                        @endif
                                        <input type="file" class="form-control" name="hero_image" accept="image/*">
                                        <input type="hidden" name="hero_image_current" value="{{ $feature->content['hero_image'] ?? '' }}">
                                    </div>
                                </div>

                                <!-- BENEFITS TAB -->
                                <div class="tab-pane" id="benefits" role="tabpanel">
                                    <h5 class="mb-3">Avantajlar Bölümü</h5>
                                    
                                    <div id="benefitsContainer">
                                        @if(isset($feature->content['benefits']))
                                            @foreach($feature->content['benefits'] as $index => $benefit)
                                                <div class="card mb-3 benefit-item">
                                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0">Avantaj {{ $index + 1 }}</h6>
                                                        <button type="button" class="btn btn-danger btn-sm remove-benefit">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Başlık</label>
                                                            <input type="text" class="form-control benefit-title" value="{{ $benefit['title'] }}" placeholder="Detaylı Müşteri Profilleri">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Açıklama</label>
                                                            <textarea class="form-control benefit-description" rows="2">{{ $benefit['description'] }}</textarea>
                                                        </div>

                                                        <h6 class="mt-3">Mini Özellikler</h6>
                                                        <div class="mini-features-container">
                                                            @if(isset($benefit['mini_features']))
                                                                @foreach($benefit['mini_features'] as $miniIndex => $mini)
                                                                    <div class="row mb-2 mini-feature-item">
                                                                        <div class="col-md-5">
                                                                            <input type="text" class="form-control mini-icon" value="{{ $mini['icon'] }}" placeholder="fas fa-bolt">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text" class="form-control mini-label" value="{{ $mini['label'] }}" placeholder="Hızlı Kayıt">
                                                                        </div>
                                                                        <div class="col-md-1">
                                                                            <button type="button" class="btn btn-danger btn-sm w-100 remove-mini-feature">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                        <button type="button" class="btn btn-secondary btn-sm add-mini-feature">
                                                            <i class="fas fa-plus me-1"></i> Mini Özellik Ekle
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-primary" id="addBenefit">
                                        <i class="fas fa-plus me-1"></i> Yeni Avantaj Ekle
                                    </button>
                                </div>

                                <!-- FEATURES & STATS TAB -->
                                <div class="tab-pane" id="features-stats" role="tabpanel">
                                    <h5 class="mb-3">Özellikler Listesi ve İstatistikler</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Özellikler Listesi (Her satıra bir özellik)</h6>
                                            <textarea class="form-control" name="features_list" rows="10" placeholder="Detaylı müşteri kartları&#10;Toplu SMS gönderimi">{{ isset($feature->content['features_list']) ? implode("\n", $feature->content['features_list']) : '' }}</textarea>
                                        </div>

                                        <div class="col-md-6">
                                            <h6>İstatistikler</h6>
                                            <div id="statsContainer">
                                                @if(isset($feature->content['stats']))
                                                    @foreach($feature->content['stats'] as $index => $stat)
                                                        <div class="card mb-2 stat-item">
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-5">
                                                                        <label class="form-label">Sayı</label>
                                                                        <input type="text" class="form-control stat-number" value="{{ $stat['number'] }}" placeholder="%60">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label">Etiket</label>
                                                                        <input type="text" class="form-control stat-label" value="{{ $stat['label'] }}" placeholder="Daha Hızlı">
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
                                    </div>
                                </div>

                                <!-- FAQS TAB -->
                                <div class="tab-pane" id="faqs" role="tabpanel">
                                    <h5 class="mb-3">Sıkça Sorulan Sorular</h5>
                                    
                                    <div id="faqsContainer">
                                        @if(isset($feature->content['faqs']))
                                            @foreach($feature->content['faqs'] as $index => $faq)
                                                <div class="card mb-2 faq-item">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-11">
                                                                <div class="mb-2">
                                                                    <label class="form-label">Soru</label>
                                                                    <input type="text" class="form-control faq-question" value="{{ $faq['question'] }}" placeholder="Soru buraya">
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
                                    <button type="button" class="btn btn-primary" id="addFaq">
                                        <i class="fas fa-plus me-1"></i> Soru Ekle
                                    </button>
                                </div>

                                <!-- CTA TAB -->
                                <div class="tab-pane" id="cta" role="tabpanel">
                                    <h5 class="mb-3">CTA (Call to Action) Bölümü</h5>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Başlık</label>
                                        <input type="text" class="form-control" name="cta_title" value="{{ $feature->content['cta']['title'] ?? '' }}" placeholder="Müşteri Yönetimi Modülünü Hemen Deneyin!">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Açıklama</label>
                                        <textarea class="form-control" name="cta_description" rows="2">{{ $feature->content['cta']['description'] ?? '' }}</textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Buton Metni</label>
                                            <input type="text" class="form-control" name="cta_button_text" value="{{ $feature->content['cta']['button_text'] ?? 'Hemen Ücretsiz Başlayın' }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Buton URL</label>
                                            <input type="text" class="form-control" name="cta_button_url" value="{{ $feature->content['cta']['button_url'] ?? '/kullanici-girisi' }}">
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-1"></i> Tüm Değişiklikleri Kaydet
                                </button>
                                <a href="{{ route('feature.detail', $slug) }}" target="_blank" class="btn btn-info">
                                    <i class="fas fa-external-link-alt me-1"></i> Sayfayı Görüntüle
                                </a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>