
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Yönetimi</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .section-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: white;
            height: 100%;
        }
        
        .section-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 16px;
            border-radius: 8px 8px 0 0;
            font-weight: 600;
            font-size: 14px;
        }
        
        .section-body {
            padding: 16px;
            height: calc(100% - 52px);
            overflow-y: auto;
        }
        
        .stok-info-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .table-container {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .photo-grid {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .photo-item {
            position: relative;
            margin-bottom: 10px;
        }
        
        .photo-item img {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
        }
        
        .photo-delete-btn {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }
        
        .compact-form .form-label {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .compact-form .form-control,
        .compact-form .form-select {
            font-size: 13px;
            padding: 6px 10px;
        }
        
        .compact-table {
            font-size: 12px;
        }
        
        .compact-table th,
        .compact-table td {
            padding: 6px 8px;
            vertical-align: middle;
        }
        
        .action-buttons {
            display: flex;
            gap: 6px;
            align-items: center;
        }
        
        .main-container {
            padding: 20px;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .page-header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        
        .stats-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stats-label {
            font-size: 12px;
            opacity: 0.9;
        }
        
        @media (max-width: 768px) {
            .section-body {
                padding: 12px;
            }
            .main-container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Sayfa Başlığı -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-1">
                        <i class="fas fa-box text-primary me-2"></i>
                        Stok Yönetimi - iPhone 15 Pro Max
                    </h4>
                    <p class="text-muted mb-0">Ürün Kodu: 1234567890123</p>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="stats-value">245</div>
                        <div class="stats-label">Toplam Stok</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ana İçerik -->
        <div class="row g-3">
            <!-- Genel Stok Düzenleme Butonu -->
            <div class="col-12 text-center">
                <button class="btn btn-primary btn-lg" id="editStockBtn">
                    <i class="fas fa-edit me-2"></i>
                    Stok Düzenleme Panelini Aç
                </button>
            </div>

            <!-- Özet Kartlar -->
            <div class="col-lg-3 col-md-6">
                <div class="stats-card bg-primary">
                    <div class="stats-value">245</div>
                    <div class="stats-label">Toplam Stok</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card bg-success">
                    <div class="stats-value">35</div>
                    <div class="stats-label">Personel Stoku</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card bg-warning">
                    <div class="stats-value">15</div>
                    <div class="stats-label">Son Hareket</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card bg-info">
                    <div class="stats-value">8</div>
                    <div class="stats-label">Fotoğraf</div>
                </div>
            </div>
        </div>
                                <div class="col-4">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-warning btn-sm w-100">
                                        <i class="fas fa-barcode me-1"></i> Barkod
                                    </button>
                                </div>
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col-8">
                                    <label class="form-label">Satış Fiyatı</label>
                                    <input type="number" class="form-control" value="45000">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Para Birimi</label>
                                    <select class="form-select">
                                        <option value="1">TL</option>
                                        <option value="2">USD</option>
                                        <option value="3">EUR</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Açıklama</label>
                                <textarea class="form-control" rows="2">256GB Deep Purple</textarea>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="stok-info-box">
                                        Toplam: 245 Adet
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stok-info-box">
                                        Personel: 35 Adet
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary btn-sm px-4">
                                    <i class="fas fa-save me-1"></i> Kaydet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sol Alt: Personel Stokları -->
            <div class="col-lg-6">
                <div class="section-card">
                    <div class="section-header">
                        <i class="fas fa-users me-2"></i>
                        Personel Stokları
                    </div>
                    <div class="section-body">
                        <div class="table-container">
                            <table class="table table-bordered compact-table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Personel</th>
                                        <th>Adet</th>
                                        <th>Tarih</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Ahmet Yılmaz</td>
                                        <td>15</td>
                                        <td>15.08.2025</td>
                                        <td>
                                            <button class="btn btn-info btn-sm detay-btn">
                                                <i class="fas fa-eye me-1"></i> Detay
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Mehmet Kaya</td>
                                        <td>10</td>
                                        <td>14.08.2025</td>
                                        <td>
                                            <button class="btn btn-info btn-sm detay-btn">
                                                <i class="fas fa-eye me-1"></i> Detay
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Ayşe Demir</td>
                                        <td>10</td>
                                        <td>13.08.2025</td>
                                        <td>
                                            <button class="btn btn-info btn-sm detay-btn">
                                                <i class="fas fa-eye me-1"></i> Detay
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sağ Alt: Stok Fotoğrafları -->
            <div class="col-lg-6">
                <div class="section-card">
                    <div class="section-header">
                        <i class="fas fa-camera me-2"></i>
                        Stok Fotoğrafları
                    </div>
                    <div class="section-body">
                        <div class="mb-3">
                            <input type="file" class="form-control form-control-sm" accept="image/*" multiple>
                            <small class="text-muted">Dosya boyutu 5mb'dan büyük olamaz. Sadece jpg ve png uzantılı dosyalar.</small>
                        </div>
                        
                        <div class="photo-grid">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="photo-item">
                                        <img src="https://via.placeholder.com/150x80/667eea/fff?text=iPhone+15" alt="Ürün Fotoğrafı">
                                        <button class="btn btn-danger photo-delete-btn">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="photo-item">
                                        <img src="https://via.placeholder.com/150x80/764ba2/fff?text=Kutu" alt="Ürün Fotoğrafı">
                                        <button class="btn btn-danger photo-delete-btn">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="photo-item">
                                        <img src="https://via.placeholder.com/150x80/42a5f5/fff?text=Aksesuar" alt="Ürün Fotoğrafı">
                                        <button class="btn btn-danger photo-delete-btn">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="photo-item">
                                        <img src="https://via.placeholder.com/150x80/66bb6a/fff?text=Garanti" alt="Ürün Fotoğrafı">
                                        <button class="btn btn-danger photo-delete-btn">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detay Modal (Personel Stokları için) -->
        <div class="modal fade" id="personelDetailModal" tabindex="-1">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Personel Stok Detayı</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Personel:</strong> <span id="modalPersonel">Ahmet Yılmaz</span></p>
                        <p><strong>Adet:</strong> <span id="modalAdet">15</span></p>
                        <p><strong>Tarih:</strong> <span id="modalTarih">15.08.2025 14:30</span></p>
                        <p class="mb-0"><strong>Not:</strong> <span id="modalNot">Saha çalışması için alındı.</span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stok Düzenleme Modal -->
        <div class="modal fade" id="editStockModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-edit me-2"></i>
                            Stok Düzenleme - iPhone 15 Pro Max
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <!-- Modal içinde grid layout -->
                        <div class="row g-0">
                            <!-- Sol Üst: Stok Hareketleri -->
                            <div class="col-lg-6 border-end border-bottom">
                                <div class="p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6 class="mb-0">
                                            <i class="fas fa-exchange-alt me-2 text-primary"></i>
                                            Stok Hareketleri
                                        </h6>
                                        <div class="action-buttons">
                                            <button class="btn btn-success btn-sm">
                                                <i class="fas fa-plus me-1"></i> Ekle
                                            </button>
                                            <select class="form-select form-select-sm" style="width: auto;">
                                                <option value="0">Tümü</option>
                                                <option value="1">Alış</option>
                                                <option value="3">Personel</option>
                                                <option value="2">Servis</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="table-container" style="max-height: 300px; overflow-y: auto;">
                                        <table class="table table-bordered compact-table mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Tarih</th>
                                                    <th>İşlem</th>
                                                    <th>Detay</th>
                                                    <th>Adet</th>
                                                    <th>Fiyat</th>
                                                    <th>Sil</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr style="background-color: rgb(135, 255, 135);">
                                                    <td>15/08</td>
                                                    <td>Alış</td>
                                                    <td>ABC Tedarik</td>
                                                    <td>50</td>
                                                    <td>25,000 TL</td>
                                                    <td><button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></td>
                                                </tr>
                                                <tr style="background-color: rgb(255, 119, 119);">
                                                    <td>14/08</td>
                                                    <td>Personel</td>
                                                    <td>Ahmet Y.</td>
                                                    <td>5</td>
                                                    <td>-</td>
                                                    <td><button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></td>
                                                </tr>
                                                <tr>
                                                    <td>13/08</td>
                                                    <td>Servis</td>
                                                    <td>S-12345</td>
                                                    <td>2</td>
                                                    <td>-</td>
                                                    <td><button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Sağ Üst: Stok Düzenleme Formu -->
                            <div class="col-lg-6 border-bottom">
                                <div class="p-3">
                                    <h6 class="mb-3">
                                        <i class="fas fa-cog me-2 text-success"></i>
                                        Ürün Bilgileri
                                    </h6>
                                    
                                    <form class="compact-form">
                                        <div class="row g-2 mb-2">
                                            <div class="col-md-6">
                                                <label class="form-label">Marka</label>
                                                <select class="form-select form-select-sm">
                                                    <option>Apple</option>
                                                    <option>Samsung</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Cihaz Türü</label>
                                                <select class="form-select form-select-sm">
                                                    <option>Telefon</option>
                                                    <option>Tablet</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="row g-2 mb-2">
                                            <div class="col-md-6">
                                                <label class="form-label">Kategori</label>
                                                <select class="form-select form-select-sm">
                                                    <option>Akıllı Telefon</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Raf</label>
                                                <select class="form-select form-select-sm">
                                                    <option>A-1</option>
                                                    <option>A-2</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label">Ürün Adı</label>
                                            <input type="text" class="form-control form-control-sm" value="iPhone 15 Pro Max">
                                        </div>

                                        <div class="row g-2 mb-2">
                                            <div class="col-8">
                                                <label class="form-label">Ürün Kodu</label>
                                                <input type="text" class="form-control form-control-sm" value="1234567890123">
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="button" class="btn btn-warning btn-sm w-100">
                                                    <i class="fas fa-barcode"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="row g-2 mb-2">
                                            <div class="col-8">
                                                <label class="form-label">Satış Fiyatı</label>
                                                <input type="number" class="form-control form-control-sm" value="45000">
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label">Para Birimi</label>
                                                <select class="form-select form-select-sm">
                                                    <option value="1">TL</option>
                                                    <option value="2">USD</option>
                                                    <option value="3">EUR</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label">Açıklama</label>
                                            <textarea class="form-control form-control-sm" rows="2">256GB Deep Purple</textarea>
                                        </div>

                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="stok-info-box">
                                                    <i class="fas fa-boxes me-1"></i>
                                                    Toplam: 245 Adet
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="stok-info-box">
                                                    <i class="fas fa-users me-1"></i>
                                                    Personel: 35 Adet
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Sol Alt: Personel Stokları -->
                            <div class="col-lg-6 border-end">
                                <div class="p-3">
                                    <h6 class="mb-3">
                                        <i class="fas fa-users me-2 text-info"></i>
                                        Personel Stokları
                                    </h6>
                                    
                                    <div class="table-container" style="max-height: 250px; overflow-y: auto;">
                                        <table class="table table-bordered compact-table mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Personel</th>
                                                    <th>Adet</th>
                                                    <th>Tarih</th>
                                                    <th>İşlem</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Ahmet Yılmaz</td>
                                                    <td>15</td>
                                                    <td>15.08.25</td>
                                                    <td>
                                                        <button class="btn btn-info btn-sm detay-btn">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Mehmet Kaya</td>
                                                    <td>10</td>
                                                    <td>14.08.25</td>
                                                    <td>
                                                        <button class="btn btn-info btn-sm detay-btn">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Ayşe Demir</td>
                                                    <td>10</td>
                                                    <td>13.08.25</td>
                                                    <td>
                                                        <button class="btn btn-info btn-sm detay-btn">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Sağ Alt: Stok Fotoğrafları -->
                            <div class="col-lg-6">
                                <div class="p-3">
                                    <h6 class="mb-3">
                                        <i class="fas fa-camera me-2 text-warning"></i>
                                        Stok Fotoğrafları
                                    </h6>
                                    
                                    <div class="mb-2">
                                        <input type="file" class="form-control form-control-sm" accept="image/*" multiple>
                                        <small class="text-muted">Max 5MB, JPG/PNG</small>
                                    </div>
                                    
                                    <div class="photo-grid" style="max-height: 200px; overflow-y: auto;">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="photo-item">
                                                    <img src="https://via.placeholder.com/120x60/667eea/fff?text=iPhone" alt="Foto">
                                                    <button class="btn btn-danger photo-delete-btn">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="photo-item">
                                                    <img src="https://via.placeholder.com/120x60/764ba2/fff?text=Kutu" alt="Foto">
                                                    <button class="btn btn-danger photo-delete-btn">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="photo-item">
                                                    <img src="https://via.placeholder.com/120x60/42a5f5/fff?text=Aks" alt="Foto">
                                                    <button class="btn btn-danger photo-delete-btn">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="photo-item">
                                                    <img src="https://via.placeholder.com/120x60/66bb6a/fff?text=Gar" alt="Foto">
                                                    <button class="btn btn-danger photo-delete-btn">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>
                            Değişiklikleri Kaydet
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>
                            İptal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hareket Ekle Modal (Küçük) -->
        <div class="modal fade" id="hareketEkleModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Stok Hareketi Ekle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form class="compact-form">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">İşlem Türü</label>
                                    <select class="form-select" id="islemTuru">
                                        <option value="">Seçiniz</option>
                                        <option value="1">Alış</option>
                                        <option value="3">Personel'e Gönder</option>
                                        <option value="2">Serviste Kullanım</option>
                                    </select>
                                </div>
                                <div class="col-md-6" id="tedarikciDiv">
                                    <label class="form-label">Tedarikçi</label>
                                    <select class="form-select">
                                        <option value="">Seçiniz</option>
                                        <option value="1">ABC Tedarik</option>
                                        <option value="2">XYZ Elektronik</option>
                                        <option value="3">DEF Wholesale</option>
                                    </select>
                                </div>
                                <div class="col-md-6 d-none" id="personelDiv">
                                    <label class="form-label">Personel Seç</label>
                                    <select class="form-select">
                                        <option value="">Seçiniz</option>
                                        <option value="1">Ahmet Yılmaz</option>
                                        <option value="2">Mehmet Kaya</option>
                                        <option value="3">Ayşe Demir</option>
                                    </select>
                                </div>
                                <div class="col-md-6 d-none" id="servisDiv">
                                    <label class="form-label">Servis No</label>
                                    <input type="text" class="form-control" placeholder="Servis numarası girin">
                                </div>
                            </div>
                            <div class="row g-3 mt-2">
                                <div class="col-md-4">
                                    <label class="form-label">Adet</label>
                                    <input type="number" class="form-control" min="1" required>
                                </div>
                                <div class="col-md-4" id="fiyatDiv">
                                    <label class="form-label">Fiyat</label>
                                    <input type="number" class="form-control" step="0.01" placeholder="0.00">
                                </div>
                                <div class="col-md-4" id="birimDiv">
                                    <label class="form-label">Para Birimi</label>
                                    <select class="form-select">
                                        <option value="1">TL</option>
                                        <option value="2">USD</option>
                                        <option value="3">EUR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="form-label">Açıklama (Opsiyonel)</label>
                                <textarea class="form-control" rows="2" placeholder="İşlem hakkında not ekleyebilirsiniz..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm">Kaydet</button>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">İptal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Personel detay modalı
            $(document).on('click', '.detay-btn', function() {
                $('#personelDetailModal').modal('show');
            });

            // Hareket ekle modalı
            $(document).on('click', '.btn-success', function() {
                if ($(this).find('i').hasClass('fa-plus')) {
                    $('#hareketEkleModal').modal('show');
                }
            });

            // Fotoğraf yükleme önizleme
            $('input[type="file"]').on('change', function() {
                const files = this.files;
                if (files.length > 0) {
                    // Dosya yükleme işlemi burada yapılacak
                    console.log('Dosyalar seçildi:', files);
                }
            });

            // Fotoğraf silme
            $(document).on('click', '.photo-delete-btn', function(e) {
                e.preventDefault();
                if (confirm('Fotoğrafı silmek istediğinizden emin misiniz?')) {
                    $(this).closest('.col-6').fadeOut(300, function() {
                        $(this).remove();
                    });
                }
            });

            // İşlem türü değiştiğinde form alanlarını göster/gizle
            $(document).on('change', '#islemTuru', function() {
                const islemTuru = $(this).val();
                
                // Önce tüm opsiyonel alanları gizle
                $('#tedarikciDiv, #personelDiv, #servisDiv').addClass('d-none');
                $('#fiyatDiv, #birimDiv').removeClass('d-none');
                
                if (islemTuru === '1') { // Alış
                    $('#tedarikciDiv').removeClass('d-none');
                    $('#fiyatDiv, #birimDiv').removeClass('d-none');
                } else if (islemTuru === '3') { // Personel'e Gönder
                    $('#personelDiv').removeClass('d-none');
                    $('#fiyatDiv, #birimDiv').addClass('d-none');
                } else if (islemTuru === '2') { // Serviste Kullanım
                    $('#servisDiv').removeClass('d-none');
                    $('#fiyatDiv, #birimDiv').addClass('d-none');
                }
            });
        });
    </script>
</body>
</html>