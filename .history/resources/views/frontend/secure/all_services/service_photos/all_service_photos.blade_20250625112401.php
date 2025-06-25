<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servis Fotoğraf Yükleme</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
    <style>
        .upload-zone {
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .upload-zone:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }
        .upload-zone.dragover {
            border-color: #28a745;
            background-color: #d4edda;
        }
        .progress-container {
            margin-top: 15px;
            display: none;
        }
        .photo-item {
            position: relative;
            margin-bottom: 15px;
        }
        .photo-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
        }
        .delete-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(220, 53, 69, 0.9);
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            color: white;
            font-size: 12px;
        }
        .photo-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
            color: white;
            padding: 10px;
            border-radius: 0 0 8px 8px;
            font-size: 12px;
        }
        .error-message {
            color: #dc3545;
            font-size: 12px;
        }
        .success-message {
            color: #28a745;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h3>Servis Fotoğrafları</h3>
        
        <!-- Fotoğraf Yükleme Formu -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Yeni Fotoğraf Ekle</h5>
            </div>
            <div class="card-body">
                <form id="servisFotoEkle" enctype="multipart/form-data">
                    <div class="upload-zone" onclick="document.getElementById('resimInput').click()">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                        <p class="mb-2">Fotoğraf yüklemek için tıklayın veya dosyaları buraya sürükleyin</p>
                        <p class="text-muted small">Desteklenen formatlar: JPG, PNG, JPEG (Max: 5MB)</p>
                        <input name="belge" class="d-none" id="resimInput" type="file" accept=".jpg,.jpeg,.png" multiple>
                    </div>
                    
                    <div class="progress-container">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 0%"></div>
                        </div>
                        <small class="text-muted">Yükleniyor...</small>
                    </div>
                    
                    <div id="uploadMessages" class="mt-2"></div>
                    
                    <input type="hidden" name="servisFotoEkle" value="Ekle">
                    <input type="hidden" name="servisid" value="{{$servis->id}}">
                </form>
            </div>
        </div>

        <!-- Mevcut Fotoğraflar -->
        <div class="card">
            <div class="card-header">
                <h5>Mevcut Fotoğraflar</h5>
            </div>
            <div class="card-body">
                <div class="row" id="photoGallery">
                    <!-- Mevcut fotoğraflar buraya gelecek -->
                    <div class="col-md-3 col-sm-6">
                        <div class="photo-item">
                            <a href="https://via.placeholder.com/400x300" data-fancybox="gallery">
                                <img src="https://via.placeholder.com/300x200" alt="Servis Fotoğrafı">
                            </a>
                            <button class="delete-btn servisFotoSil" data-id="1" title="Sil">×</button>
                            <div class="photo-overlay">
                                <small>Yükleme: 25.06.2025</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="photo-item">
                            <a href="https://via.placeholder.com/400x300/0066cc" data-fancybox="gallery">
                                <img src="https://via.placeholder.com/300x200/0066cc" alt="Servis Fotoğrafı">
                            </a>
                            <button class="delete-btn servisFotoSil" data-id="2" title="Sil">×</button>
                            <div class="photo-overlay">
                                <small>Yükleme: 24.06.2025</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="noPhotos" class="text-center text-muted py-5" style="display: none;">
                    <i class="fas fa-images fa-3x mb-3"></i>
                    <p>Henüz fotoğraf yüklenmemiş</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

    <script>
        $(document).ready(function() {
            // CSRF token ayarı
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Fancybox başlatma
            $('[data-fancybox="gallery"]').fancybox({
                buttons: ['zoom', 'share', 'slideShow', 'fullScreen', 'download', 'thumbs', 'close'],
                animationEffect: 'fade',
                transitionEffect: 'slide'
            });

            // Drag & Drop işlemleri
            const uploadZone = $('.upload-zone');
            
            uploadZone.on('dragover dragenter', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('dragover');
            });

            uploadZone.on('dragleave dragend', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
            });

            uploadZone.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
                
                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    handleFileUpload(files);
                }
            });

            // Dosya seçimi
            $('#resimInput').on('change', function() {
                const files = this.files;
                if (files.length > 0) {
                    handleFileUpload(files);
                }
            });

            // Dosya yükleme işlemi
            function handleFileUpload(files) {
                const maxSize = 5 * 1024 * 1024; // 5MB
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                
                // Dosya validasyonu
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    
                    if (!allowedTypes.includes(file.type)) {
                        showMessage('Sadece JPG, PNG ve JPEG dosyaları yükleyebilirsiniz.', 'error');
                        return;
                    }
                    
                    if (file.size > maxSize) {
                        showMessage('Dosya boyutu 5MB\'dan büyük olamaz.', 'error');
                        return;
                    }
                }

                // Her dosya için ayrı ayrı yükleme
                for (let i = 0; i < files.length; i++) {
                    uploadSingleFile(files[i]);
                }
            }

            // Tek dosya yükleme
            function uploadSingleFile(file) {
                const formData = new FormData();
                formData.append('belge', file);
                formData.append('servisFotoEkle', 'Ekle');
                formData.append('servisid', $('input[name="servisid"]').val());

                $('.progress-container').show();
                var firma_id = {{$firma->id}};
                $.ajax({
                    url: '/' + firma_id + '/store.service.photo', // Gerçek URL'nizi buraya yazın
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        const xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percentComplete = (e.loaded / e.total) * 100;
                                $('.progress-bar').css('width', percentComplete + '%');
                            }
                        });
                        return xhr;
                    },
                    success: function(response) {
                        showMessage('Fotoğraf başarıyla yüklendi!', 'success');
                        addPhotoToGallery(response.photo);
                        resetUploadForm();
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Yükleme sırasında hata oluştu.';
                        showMessage(message, 'error');
                        resetUploadForm();
                    }
                });
            }

            // Fotoğrafı galeriye ekleme
            function addPhotoToGallery(photo) {
                const photoHtml = `
                    <div class="col-md-3 col-sm-6">
                        <div class="photo-item">
                            <a href="${photo.url}" data-fancybox="gallery">
                                <img src="${photo.thumbnail}" alt="Servis Fotoğrafı">
                            </a>
                            <button class="delete-btn servisFotoSil" data-id="${photo.id}" title="Sil">×</button>
                            <div class="photo-overlay">
                                <small>Yükleme: ${photo.created_at}</small>
                            </div>
                        </div>
                    </div>
                `;
                
                $('#photoGallery').prepend(photoHtml);
                $('#noPhotos').hide();
                
                // Yeni eklenen fotoğraf için fancybox başlatma
                $('[data-fancybox="gallery"]').fancybox();
            }

            // Fotoğraf silme
            $(document).on('click', '.servisFotoSil', function(e) {
                e.preventDefault();
                
                if (!confirm('Bu fotoğrafı silmek istediğinizden emin misiniz?')) {
                    return;
                }

                const photoId = $(this).data('id');
                const $photoItem = $(this).closest('.col-md-3');

                $.ajax({
                    url: `/api/delete-service-photo/${photoId}`, // Gerçek URL'nizi buraya yazın
                    method: 'DELETE',
                    success: function(response) {
                        $photoItem.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Hiç fotoğraf kalmadıysa mesaj göster
                            if ($('#photoGallery .col-md-3').length === 0) {
                                $('#noPhotos').show();
                            }
                        });
                        showMessage('Fotoğraf başarıyla silindi.', 'success');
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Silme işlemi başarısız oldu.';
                        showMessage(message, 'error');
                    }
                });
            });

            // Mesaj gösterme
            function showMessage(message, type) {
                const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
                const messageHtml = `
                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                
                $('#uploadMessages').html(messageHtml);
                
                // 5 saniye sonra mesajı otomatik kapat
                setTimeout(function() {
                    $('.alert').fadeOut();
                }, 5000);
            }

            // Upload formunu sıfırlama
            function resetUploadForm() {
                $('#resimInput').val('');
                $('.progress-container').hide();
                $('.progress-bar').css('width', '0%');
            }
        });
    </script>
</body>
</html>