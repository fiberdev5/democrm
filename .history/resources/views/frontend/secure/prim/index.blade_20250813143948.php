<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid mt-1" >
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="card-title">Personel Prim Hesaplama</h4> 
        </div>
        <div class="card-body">
          <!-- Prim Ayarları Özeti -->
          <div class="row mb-1">
            <div class="col-12">
              <div class="alert alert-info">
                <h6><i class="fas fa-info-circle"></i> Aktif Prim Ayarları:</h6>
                <div class="row">
                  @foreach($primAyarlari as $ayar)
                    <div class="col-md-4">
                      <div class="card bg-light" style="margin-bottom: 0;">
                        <div class="card-body p-2">
                          <h6 class="card-title mb-1">Teknisyen</h6>
                          <small class="text-muted">
                            Günlük {{ number_format($ayar->teknisyenPrimTutari, 0, ',', '.') }} TL üzeri teklif = %{{ $ayar->teknisyenPrim }} prim </span>
                          </small>
                                                 
                          <h6 class="card-title mb-1">Operator</h6>
                          <small class="text-muted">
                            Günlük {{ $ayar->operatorPrimTutari }} servis = Servis başı {{ $ayar->operatorPrim }} TL prim                                              
                          </small>

                          <h6 class="card-title mb-1">Atölye Ustası</h6>
                          <small class="text-muted">
                            Günlük {{ $ayar->atolyePrimTutari }} tamamlama = Servis başı {{ $ayar->atolyePrim }} TL prim                                        
                          </small>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>

          <form id="primForm" class="row g-3">
            @csrf
            <div class="col-md-3">
              <label for="personel_id" class="form-label">Personel</label>
              <select class="form-select" id="personel_id" name="personel_id" required>
                <option value="">Personel Seçiniz</option>
                @foreach($personeller as $personel)
                  <option value="{{ $personel->user_id }}">
                    {{ $personel->name }} 
                    @if($personel->roles->isNotEmpty())
                      ({{ $personel->roles->pluck('name')->join(', ') }})
                    @endif
                  </option>
                @endforeach
              </select>
            </div>
                        
            <div class="col-md-2">
              <label for="tarih1prim" class="form-label">Başlangıç Tarihi</label>
              <input type="date" class="form-control" id="tarih1prim" name="tarih1prim" placeholder="gg/aa/yyyy" required>
            </div>
                        
            <div class="col-md-2">
              <label for="tarih2prim" class="form-label">Bitiş Tarihi</label>
              <input type="date" class="form-control" id="tarih2prim" name="tarih2prim" placeholder="gg/aa/yyyy" required>
            </div>
                        
            <div class="col-md-3">
              <label class="form-label">&nbsp;</label>
              <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-sm">
                  <i class="fas fa-calculator"></i> Prim Hesapla
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Sonuçlar Tablosu -->
  <div class="row mt-1" id="sonuclarContainer" style="display: none;">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Prim Hesaplama Sonuçları</h5>
          <div id="sonucBilgi" class="text-muted small"></div>
        </div>
        <div class="card-body">
          <!-- Özet Kartları -->
          <div class="row mb-4" id="ozetKartlari">
            <div class="col-md-3">
              <div class="card bg-primary text-white">
                <div class="card-body">
                  <div class="d-flex justify-content-between">
                    <div>
                      <h4 id="toplamPrimTutar">0 ₺</h4>
                      <small>Toplam Prim</small>
                    </div>
                    <div class="align-self-center">
                      <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-success text-white">
                <div class="card-body">
                  <div class="d-flex justify-content-between">
                    <div>
                      <h4 id="primliGunSayisi">0</h4>
                      <small>Primli Gün Sayısı</small>
                    </div>
                    <div class="align-self-center">
                      <i class="fas fa-calendar-check fa-2x"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-info text-white">
                <div class="card-body">
                  <div class="d-flex justify-content-between">
                    <div>
                      <h4 id="ortalamaPrim">0 ₺</h4>
                      <small>Ortalama Günlük Prim</small>
                    </div>
                    <div class="align-self-center">
                      <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card bg-warning text-white">
                <div class="card-body">
                  <div class="d-flex justify-content-between">
                    <div>
                      <h4 id="toplamPerformans">0</h4>
                      <small>Toplam Performans</small>
                    </div>
                    <div class="align-self-center">
                      <i class="fas fa-trophy fa-2x"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-striped table-hover" id="sonuclarTable">
              <thead class="title">
                <tr>
                  <th>#</th>
                  <th>Tarih</th>
                  <th>Performans</th>
                  <th>Sınır</th>
                  <th>Prim Oranı</th>
                  <th>Prim Tutarı</th>
                  <th>İşlemler</th>
                </tr>
              </thead>
              <tbody id="sonuclarTableBody">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Günlük Detay Modal -->
<div class="modal fade" id="gunlukDetayModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Günlük Prim Detayları</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="gunlukDetayContent">
        <!-- Detay içeriği buraya gelecek -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
      </div>
    </div>
  </div>
</div>

<!-- Loading Spinner -->
<div class="text-center" id="loadingSpinner" style="display: none;">
  <div class="spinner-border text-primary" role="status">
    <span class="visually-hidden">Yükleniyor...</span>
  </div>
</div>

<style>
  .table th {
    font-weight: 600;
    font-size: 0.875rem;
  }
  .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
  }
  .text-success {
    color: #198754 !important;
  }
  .text-warning {
    color: #ffc107 !important;
  }
  .badge {
    font-size: 0.75em;
  }
  .card-body h4 {
    margin-bottom: 0;
  }
  .bg-light .card-body {
    background-color: #f8f9fa !important;
  }
  /* Modern Prim Detay Modal Stilleri */
:root {
    --primary-color: #3b82f6;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --info-color: #06b6d4;
    --light-bg: #f8fafc;
    --border-color: #e2e8f0;
    --text-muted: #64748b;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
}

/* Modal Genel Stiller */
#gunlukDetayModal .modal-dialog {
    box-shadow: var(--shadow-lg);
}

#gunlukDetayModal .modal-content {
    border: none;
    border-radius: 16px;
    overflow: hidden;
}

#gunlukDetayModal .modal-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #1e40af 100%);
    color: white;
    border-bottom: none;
    padding: 2rem 2rem 1.5rem;
    position: relative;
}

#gunlukDetayModal .modal-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, rgba(255,255,255,0.2), rgba(255,255,255,0.5), rgba(255,255,255,0.2));
}

#gunlukDetayModal .modal-title {
    font-size: 1.5rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

#gunlukDetayModal .modal-title i {
    background: rgba(255,255,255,0.2);
    padding: 0.5rem;
    border-radius: 10px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

#gunlukDetayModal .btn-close {
    background: rgba(255,255,255,0.1);
    border-radius: 8px;
    opacity: 0.8;
    transition: all 0.2s;
}

#gunlukDetayModal .btn-close:hover {
    background: rgba(255,255,255,0.2);
    opacity: 1;
    transform: scale(1.1);
}

#gunlukDetayModal .modal-body {
    padding: 2rem;
    background: var(--light-bg);
}

/* Bilgi Kartı */
.info-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
    margin-bottom: 1.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.info-item .icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.info-item .icon.user { 
    background: linear-gradient(135deg, #3b82f6, #1d4ed8); 
    color: white; 
}

.info-item .icon.role { 
    background: linear-gradient(135deg, #10b981, #047857); 
    color: white; 
}

.info-item .icon.date { 
    background: linear-gradient(135deg, #f59e0b, #d97706); 
    color: white; 
}

.info-item .icon.performance { 
    background: linear-gradient(135deg, #8b5cf6, #7c3aed); 
    color: white; 
}

.info-content {
    flex: 1;
}

.info-label {
    font-size: 0.75rem;
    color: var(--text-muted);
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.info-value {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
}

/* Bölüm Başlığı */
.section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--border-color);
}

.section-header i {
    background: linear-gradient(135deg, var(--primary-color), #1e40af);
    color: white;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

/* Modern Tablo */
.modern-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
}

.modern-table table {
    margin: 0;
}

.modern-table th {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    color: #374151;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 1rem;
    border: none;
    border-bottom: 1px solid var(--border-color);
}

.modern-table td {
    padding: 1rem;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.modern-table tbody tr:last-child td {
    border-bottom: none;
}

.modern-table tbody tr {
    transition: all 0.2s;
}

.modern-table tbody tr:hover {
    background: linear-gradient(135deg, #fefeff, #f8fafc);
    transform: translate;
}
</style>


<script>
  $(document).ready(function() {
    // Tarih seçicileri başlat
    flatpickr("#tarih1prim, #tarih2prim", {
      dateFormat: "Y-m-d",
      locale: "tr",
      allowInput: true,
      defaultDate: "today", //Sayfa ilk açıldığında tarih aralığını bugün yapmakta
    });

    // Form submit
    $('#primForm').on('submit', function(e) {
        e.preventDefault();
        primHesapla();
    });
  });

  function primHesapla() {
    const formData = new FormData($('#primForm')[0]);
    
    // Loading göster
    $('#loadingSpinner').show();
    $('#sonuclarContainer').hide();
    
    $.ajax({
      url: '{{ route("prim.hesapla", $firma->id) }}',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        $('#loadingSpinner').hide();
        if (response.success) {
          showSonuclar(response.data);
        } else {
          showErrors(response.message);
        }
      },
      error: function(xhr) {
        $('#loadingSpinner').hide();
  
        if (xhr.status === 422) {
          const errors = xhr.responseJSON.errors;
          showErrors(errors);
        } else {
          toastr.error('Bir hata oluştu. Lütfen tekrar deneyin.');
        }
      }
    });
  }

  function showSonuclar(data) {
    const sonuclar = data.sonuclar;
    const personel = data.personel;
    const grup = data.grup;
    const tarihAraligi = data.tarih_araligi;
    const toplamKayit = data.toplam_kayit;
    const toplamPrim = data.toplam_prim;
    
    // Özet kartlarını güncelle
    updateOzetKartlari(sonuclar, toplamPrim);
    
    // Bilgi alanını güncelle
    $('#sonucBilgi').html(`
      <strong>Personel:</strong> ${personel.name} (${grup}) | 
      <strong>Tarih:</strong> ${tarihAraligi.baslangic} - ${tarihAraligi.bitis} | 
      <strong>Toplam:</strong> ${toplamKayit} primli gün
    `);
    
    // Tablo içeriğini temizle
    $('#sonuclarTableBody').empty();
    
    if (sonuclar.length === 0) {
      $('#sonuclarTableBody').html(`
        <tr>
          <td colspan="7" class="text-center py-4">
            <i class="fas fa-info-circle text-muted"></i> 
            Belirtilen kriterlere uygun prim bulunamadı.
          </td>
        </tr>
      `);
    } else {
      sonuclar.forEach(function(sonuc, index) {
        let performansText = '';
        let performansValue = 0;
  
        if (sonuc.teklif_toplami) {
          // Teknisyen
          performansText = `${numberFormat(sonuc.teklif_toplami)} TL`;
          performansValue = sonuc.teklif_toplami;
        } else if (sonuc.servis_sayisi) {
          // Operator
          performansText = `${sonuc.servis_sayisi} Servis`;
          performansValue = sonuc.servis_sayisi;
        } else if (sonuc.tamamlanan_sayisi) {
          // Atölye Ustası
          performansText = `${sonuc.tamamlanan_sayisi} Tamamlama`;
          performansValue = sonuc.tamamlanan_sayisi;
        }
            
        const row = `
          <tr>
            <td>${index + 1}</td>
            <td>
              <span class="">${formatTarih(sonuc.tarih)}</span>
            </td>
            <td>
              <span class=" fw-bold">${performansText}</span>
            </td>
            <td>
              <span class="text-muted">${numberFormat(sonuc.gunluk_sinir)}</span>
            </td>
            <td>
              <span class="">${sonuc.prim_orani}%</span>
            </td>
            <td>
              <span class="text-success fw-bold fs-6">${numberFormat(sonuc.prim_tutari)} ₺</span>
            </td>
            <td>
              <button type="button" class="btn btn-info btn-sm" 
                onclick="gunlukDetayGoster('${personel.user_id}', '${sonuc.tarih}')">
                <i class="fas fa-eye"></i> Detay
              </button>
            </td>
          </tr>
          `;
          $('#sonuclarTableBody').append(row);
        });
      }
    
      $('#sonuclarContainer').show();
      toastr.success('Prim hesaplama işlemi başarıyla tamamlandı.');
  }

  function updateOzetKartlari(sonuclar, toplamPrim) {
    const primliGunSayisi = sonuclar.length;
    const ortalamaPrim = primliGunSayisi > 0 ? toplamPrim / primliGunSayisi : 0;
    
    // Toplam performans (rol göre farklı hesaplama)
    let toplamPerformans = 0;
    sonuclar.forEach(function(sonuc) {
      if (sonuc.teklif_toplami) {
        toplamPerformans += sonuc.teklif_toplami;
      } else if (sonuc.servis_sayisi) {
        toplamPerformans += sonuc.servis_sayisi;
      } else if (sonuc.tamamlanan_sayisi) {
        toplamPerformans += sonuc.tamamlanan_sayisi;
      }
    });
    
    $('#toplamPrimTutar').text(numberFormat(toplamPrim) + ' ₺');
    $('#primliGunSayisi').text(primliGunSayisi);
    $('#ortalamaPrim').text(numberFormat(ortalamaPrim) + ' ₺');
    $('#toplamPerformans').text(numberFormat(toplamPerformans));
  }

  function showErrors(errors) {
    if (typeof errors === 'string') {
      toastr.error(errors);
    } else {
      $.each(errors, function(field, messages) {
        if (Array.isArray(messages)) {
          $.each(messages, function(index, message) {
            toastr.error(message);
          });
        } else {
          toastr.error(messages);
        }
      });
    }
  }

  function gunlukDetayGoster(personelId, tarih) {
    $.ajax({
      url: '{{ route("prim.detay", $firma->id) }}',
      type: 'GET',
      data: { 
        personel_id: personelId,
        tarih: tarih 
      },
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        if (response.success) {
          const detay = response.data;
          let detayHtml = `
            <div class="row mb-3">
              <div class="col-md-6">
                <h6><i class="fas fa-user"></i> Personel Bilgileri</h6>
                <table class="table table-sm">
                  <tr><td><strong>Ad:</strong></td><td>${detay.personel.name}</td></tr>
                  <tr><td><strong>Rol:</strong></td><td><span class="">${detay.rol}</span></td></tr>
                  <tr><td><strong>Tarih:</strong></td><td>${formatTarih(detay.tarih)}</td></tr>
                </table>
              </div>
            </div> 
            <hr>
                    
            <h6><i class="fas fa-list"></i> Günlük İşlemler</h6>
            <div class="table-responsive">
              <table class="table table-sm table-striped">
                `;
                
                if (detay.rol === 'Teknisyen') {
                  detayHtml += `
                    <thead class="title">
                      <tr>
                        <th>Servis ID</th>
                        <th>Müşteri</th>
                        <th>Teklif Tutarı</th>
                        <th>Saat</th>
                      </tr>
                    </thead>
                    <tbody>
                      `;
                    
                      if (detay.islemler.length > 0) {
                        detay.islemler.forEach(function(islem) {
                          detayHtml += `
                            <tr>
                              <td><span class="badge bg-info">${islem.servis_id}</span></td>
                              <td>${islem.musteri_adi}</td>
                              <td class="text-success fw-bold">${numberFormat(islem.cevap)} ₺</td>
                              <td>${formatSaat(islem.created_at)}</td>
                            </tr>
                          `;
                        });
                      }
                } else if (detay.rol === 'Operatör') {
                  detayHtml += `
                    <thead class="title">
                      <tr>
                        <th>Servis ID</th>
                        <th>Müşteri</th>
                        <th>Kayıt Saati</th>
                        <th>Durum</th>
                      </tr>
                    </thead>
                    <tbody>
                      `;
                    
                      if (detay.islemler.length > 0) {
                        detay.islemler.forEach(function(islem) {
                          detayHtml += `
                            <tr>
                              <td><span class="badge bg-info">${islem.id}</span></td>
                              <td>${islem.musteri_adi}</td>
                              <td>${formatSaat(islem.created_at)}</td>
                              <td><span class="badge bg-success">Kaydedildi</span></td>
                            </tr>
                          `;
                        });
                      }
                } else { // Atölye Ustası
                  detayHtml += `
                    <thead class="title">
                      <tr>
                        <th>Servis ID</th>
                        <th>Müşteri</th>
                        <th>Tamamlama Saati</th>
                        <th>İşlem</th>
                      </tr>
                    </thead>
                    <tbody>
                      `;
                    
                      if (detay.islemler.length > 0) {
                        detay.islemler.forEach(function(islem) {
                          detayHtml += `
                            <tr>
                              <td><span class="badge bg-info">${islem.servis_id}</span></td>
                              <td>${islem.musteri_adi}</td>
                              <td>${formatSaat(islem.created_at)}</td>
                              <td><span class="badge bg-success">Teslimata Hazır</span></td>
                            </tr>
                          `;
                        });
                      }
                }
                
                if (detay.islemler.length === 0) {
                  detayHtml += `
                    <tr>
                      <td colspan="4" class="text-center text-muted">Bu tarihte işlem bulunamadı.</td>
                    </tr>
                  `;
                }
                
                detayHtml += `
                  </tbody>
                </table>
              </div>
            `;
                
                $('#gunlukDetayContent').html(detayHtml);
                $('#gunlukDetayModal').modal('show');
            } else {
                toastr.error('Günlük detaylar alınamadı.');
            }
        },
        error: function() {
            toastr.error('Günlük detaylar alınırken hata oluştu.');
        }
    });
  }

  // Yardımcı fonksiyonlar
  function numberFormat(number) {
    return new Intl.NumberFormat('tr-TR', {
      minimumFractionDigits: 0,
      maximumFractionDigits: 2
    }).format(number);
  }

  function formatTarih(tarih) {
    return new Date(tarih).toLocaleDateString('tr-TR');
  }

  function formatSaat(datetime) {
    return new Date(datetime).toLocaleTimeString('tr-TR', {
      hour: '2-digit',
      minute: '2-digit'
    });
  }

  // Personel seçimi değiştiğinde
  $('#personel_id').on('change', function() {
    const selectedPersonel = $(this).find('option:selected').text();
    if (selectedPersonel && selectedPersonel !== 'Personel Seçiniz') {
      toastr.info(`${selectedPersonel} personeli seçildi.`);
    }
  });

  // Tarih validasyonu
  $('#tarih2prim').on('change', function() {
    const tarih1 = $('#tarih1prim').val();
    const tarih2 = $('#tarih2prim').val();
    
    if (tarih1 && tarih2) {
      const date1 = new Date(tarih1);
      const date2 = new Date(tarih2);
        
      if (date2 < date1) {
        toastr.warning('Bitiş tarihi başlangıç tarihinden önce olamaz.');
        $('#tarih2prim').val('');
      }
    }
  });
</script>
