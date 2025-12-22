@extends('frontend.secure.user_master')
@section('user')
<div class="page-content">
    <div class="container-fluid">
        
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Entegrasyonlar Sayfası Yönetimi</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Entegrasyonlar</li>
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
                                <a class="nav-link active" data-bs-toggle="tab" href="#page-header" role="tab">
                                    <i class="fas fa-heading me-1"></i> Sayfa Başlığı
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#marquee-logos" role="tab">
                                    <i class="fas fa-images me-1"></i> Marquee Logoları
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#categories" role="tab">
                                    <i class="fas fa-th-large me-1"></i> Entegrasyon Kategorileri
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#faqs" role="tab">
                                    <i class="fas fa-question-circle me-1"></i> SSS
                                </a>
                            </li>
                        </ul>

                        <form action="{{ route('super.admin.frontend.integrations-content.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="tab-content p-3">
                                
                                <!-- PAGE HEADER TAB -->
                                <div class="tab-pane active" id="page-header" role="tabpanel">
                                    <h5 class="mb-3">Hero Bölümü</h5>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Ana Başlık</label>
                                        <input type="text" class="form-control" name="header_title" 
                                               value="{{ $integrations->content['page_header']['title'] ?? 'Serbis Entegrasyonları ile Tüm Süreçlerinizi Entegre Edin' }}">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Alt Başlık</label>
                                        <textarea class="form-control" name="header_subtitle" rows="2">{{ $integrations->content['page_header']['subtitle'] ?? 'Serbis uygulama mağazasındaki uygulama ve entegrasyonlar ile teknik servis sitenizi çok yönlü hale getirin.' }}</textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Buton Metni</label>
                                            <input type="text" class="form-control" name="header_button_text" 
                                                   value="{{ $integrations->content['page_header']['button_text'] ?? 'Deneme Hesabı Oluştur' }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Buton URL</label>
                                            <input type="text" class="form-control" name="header_button_url" 
                                                   value="{{ $integrations->content['page_header']['button_url'] ?? '/kullanici-girisi' }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- MARQUEE LOGOS TAB -->
                                <div class="tab-pane" id="marquee-logos" role="tabpanel">
                                    <h5 class="mb-3">Marquee (Kayan) Logoları</h5>
                                    <p class="text-muted">Hero bölümünde kayan logo bandı için entegrasyon logoları</p>
                                    
                                    <div id="marqueeLogosContainer">
                                        @if(isset($integrations->content['marquee_logos']))
                                            @foreach($integrations->content['marquee_logos'] as $index => $logo)
                                                <div class="card mb-2 marquee-logo-item">
                                                    <div class="card-body">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-2">
                                                                @if(isset($logo['logo']))
                                                                    <img src="{{ asset($logo['logo']) }}" style="height: 50px; width: auto;" class="mb-2">
                                                                @endif
                                                                <input type="file" class="form-control form-control-sm marquee-logo-file" accept="image/*">
                                                                <input type="hidden" class="marquee-logo-current" value="{{ $logo['logo'] ?? '' }}">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label">İsim</label>
                                                                <input type="text" class="form-control marquee-logo-name" value="{{ $logo['name'] }}" placeholder="NetGSM">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label">Kategori</label>
                                                                <input type="text" class="form-control marquee-logo-category" value="{{ $logo['category'] }}" placeholder="SMS">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="form-label">&nbsp;</label>
                                                                <button type="button" class="btn btn-danger btn-sm w-100 remove-marquee-logo">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-primary" id="addMarqueeLogo">
                                        <i class="fas fa-plus me-1"></i> Logo Ekle
                                    </button>
                                </div>

                                <!-- CATEGORIES TAB -->
                                <div class="tab-pane" id="categories" role="tabpanel">
                                    <h5 class="mb-3">Entegrasyon Kategorileri</h5>
                                    
                                    <div id="categoriesContainer">
                                        @if(isset($integrations->content['categories']))
                                            @foreach($integrations->content['categories'] as $catIndex => $category)
                                                <div class="card mb-4 category-item">
                                                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0 text-white">Kategori {{ $catIndex + 1 }}</h6>
                                                        <button type="button" class="btn btn-danger btn-sm remove-category">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row mb-3">
                                                            <div class="col-md-4">
                                                                <label class="form-label">Kategori Başlığı</label>
                                                                <input type="text" class="form-control category-title" value="{{ $category['title'] }}" placeholder="SMS Entegrasyonları">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label">Alt Başlık</label>
                                                                <input type="text" class="form-control category-subtitle" value="{{ $category['subtitle'] }}" placeholder="Kısa açıklama">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label">Arka Plan Stili</label>
                                                                <select class="form-control category-bg-style">
                                                                    <option value="white" {{ ($category['bg_style'] ?? 'white') == 'white' ? 'selected' : '' }}>Beyaz</option>
                                                                    <option value="gray" {{ ($category['bg_style'] ?? 'white') == 'gray' ? 'selected' : '' }}>Gri</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <h6 class="mt-3 mb-2">Entegrasyonlar</h6>
                                                        <div class="integrations-container">
                                                            @if(isset($category['integrations']))
                                                                @foreach($category['integrations'] as $intIndex => $integration)
                                                                    <div class="card mb-3 integration-item">
                                                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                                            <span>{{ $integration['name'] }}</span>
                                                                            <button type="button" class="btn btn-danger btn-sm remove-integration">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div class="row mb-3">
                                                                                <div class="col-md-3">
                                                                                    @if(isset($integration['logo']))
                                                                                        <img src="{{ asset($integration['logo']) }}" style="height: 60px; width: auto;" class="mb-2 d-block">
                                                                                    @endif
                                                                                    <label class="form-label">Logo</label>
                                                                                    <input type="file" class="form-control form-control-sm integration-logo-file" accept="image/*">
                                                                                    <input type="hidden" class="integration-logo-current" value="{{ $integration['logo'] ?? '' }}">
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <label class="form-label">İsim</label>
                                                                                    <input type="text" class="form-control integration-name" value="{{ $integration['name'] }}" placeholder="NETGSM">
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <label class="form-label">Kategori Etiketi</label>
                                                                                    <input type="text" class="form-control integration-category-tag" value="{{ $integration['category_tag'] }}" placeholder="SMS">
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <label class="form-label">Kısa Açıklama</label>
                                                                                    <input type="text" class="form-control integration-description" value="{{ $integration['description'] }}" placeholder="Kısa açıklama">
                                                                                </div>
                                                                            </div>

                                                                            <div class="mb-3">
                                                                                <label class="form-label">Detay Açıklama (Hover)</label>
                                                                                <textarea class="form-control integration-detail" rows="2">{{ $integration['detail'] }}</textarea>
                                                                            </div>

                                                                            <div>
                                                                                <label class="form-label">Özellikler (Her satıra bir özellik)</label>
                                                                                <textarea class="form-control integration-features" rows="3" placeholder="Toplu SMS gönderimi&#10;SMS şablonları">{{ isset($integration['features']) ? implode("\n", $integration['features']) : '' }}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                        <button type="button" class="btn btn-secondary btn-sm add-integration">
                                                            <i class="fas fa-plus me-1"></i> Entegrasyon Ekle
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <button type="button" class="btn btn-primary" id="addCategory">
                                        <i class="fas fa-plus me-1"></i> Yeni Kategori Ekle
                                    </button>
                                </div>

                                <!-- FAQS TAB -->
                                <div class="tab-pane" id="faqs" role="tabpanel">
                                    <h5 class="mb-3">Sıkça Sorulan Sorular</h5>
                                    
                                    <div id="faqsContainer">
                                        @if(isset($integrations->content['faqs']))
                                            @foreach($integrations->content['faqs'] as $index => $faq)
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

                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Tüm Değişiklikleri Kaydet
                                </button>
                                <a href="{{ url('/entegrasyonlar') }}" target="_blank" class="btn btn-info">
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