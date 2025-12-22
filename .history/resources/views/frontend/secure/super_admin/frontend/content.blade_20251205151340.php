@extends('frontend.secure.user_master')
@section('user')
<div class="page-content">
    <div class="container-fluid">
        
        <!-- Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">İçerik Yönetimi</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('super.admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">İçerik Yönetimi</li>
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
                                <a class="nav-link active" data-bs-toggle="tab" href="#hero" role="tab">
                                    <i class="fas fa-star me-1"></i> Hero Bölümü
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#headers" role="tab">
                                    <i class="fas fa-heading me-1"></i> Bölüm Başlıkları
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#contact" role="tab">
                                    <i class="fas fa-address-card me-1"></i> İletişim
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#cta" role="tab">
                                    <i class="fas fa-bullhorn me-1"></i> CTA (Call to Action)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#video" role="tab">
                                    <i class="fas fa-video me-1"></i> Video
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content p-3">
                            <!-- Hero Tab -->
                            <div class="tab-pane active" id="hero" role="tabpanel">
                                <h5 class="mb-3">Hero Bölümü</h5>
                                <form id="heroForm">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Başlık (İlk Kısım)</label>
                                            <input type="text" class="form-control" id="hero_title" value="{{ $hero->content['title'] ?? '' }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Vurgulanan Kelime</label>
                                            <input type="text" class="form-control" id="hero_highlight" value="{{ $hero->content['highlight'] ?? '' }}">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Açıklama</label>
                                        <textarea class="form-control" id="hero_description" rows="3">{{ $hero->content['description'] ?? '' }}</textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Birincil Buton Metni</label>
                                            <input type="text" class="form-control" id="hero_primary_btn" value="{{ $hero->content['primary_button_text'] ?? '' }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">İkincil Buton Metni</label>
                                            <input type="text" class="form-control" id="hero_secondary_btn" value="{{ $hero->content['secondary_button_text'] ?? '' }}">
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Kaydet</button>
                                </form>
                            </div>

                            <!-- Devam edecek... -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Hero Form Submit
$('#heroForm').on('submit', function(e) {
    e.preventDefault();
    
    const data = {
        section: 'hero',
        content: {
            title: $('#hero_title').val(),
            highlight: $('#hero_highlight').val(),
            description: $('#hero_description').val(),
            primary_button_text: $('#hero_primary_btn').val(),
            secondary_button_text: $('#hero_secondary_btn').val()
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
            toastr.success('Hero bölümü güncellendi');
        },
        error: function(xhr) {
            toastr.error('Bir hata oluştu');
        }
    });
});
</script>
@endsection