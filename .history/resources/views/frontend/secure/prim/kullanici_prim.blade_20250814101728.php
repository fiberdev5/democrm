{{-- Bu dosya, mevcut index.blade.php dosyanızın bir kopyası olup düzenlenmiştir. --}}

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid mt-1" >
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="card-title">Primlerim</h4>
        </div>
        <div class="card-body">
          <!-- Prim Ayarları Özeti (Mevcut kodunuzdaki gibi kalabilir) -->
          <div class="row mb-1">
            {{-- ... Mevcut prim ayarları özeti bölümü ... --}}
          </div>

          {{-- Personel seçim alanı kaldırıldı --}}
          <form id="primForm" class="row g-3 align-items-end">
            @csrf
            <div class="col-md-3">
              <label for="tarih1prim" class="form-label">Başlangıç Tarihi</label>
              <input type="date" class="form-control" id="tarih1prim" name="tarih1prim" required>
            </div>
                        
            <div class="col-md-3">
              <label for="tarih2prim" class="form-label">Bitiş Tarihi</label>
              <input type="date" class="form-control" id="tarih2prim" name="tarih2prim" required>
            </div>
                        
            <div class="col-md-3">
              <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-calculator"></i> Prim Hesapla
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Sonuçlar ve Modal (Mevcut kodunuzdaki gibi kalabilir) -->
  <div class="row mt-1" id="sonuclarContainer" style="display: none;">
    {{-- ... Mevcut sonuçlar tablosu, özet kartları ve modal ... --}}
  </div>
</div>

<!-- Günlük Detay Modal (Mevcut kodunuzdaki gibi kalabilir) -->
<div class="modal fade" id="gunlukDetayModal" ...>
  {{-- ... Mevcut modal içeriği ... --}}
</div>

<!-- Loading Spinner (Mevcut kodunuzdaki gibi kalabilir) -->
<div class="text-center" id="loadingSpinner" ...>
  {{-- ... --}}
</div>

<style>
  {{-- ... Mevcut CSS stilleriniz ... --}}
</style>

<script>
  // Mevcut JavaScript kodunuzu buraya kopyalayın ve AJAX URL'lerini güncelleyin.
  $(document).ready(function() {
    // ... (flatpickr başlatma)

    $('#primForm').on('submit', function(e) {
        e.preventDefault();
        primHesapla();
    });
  });

  function primHesapla() {
    const formData = new FormData($('#primForm')[0]);
    $('#loadingSpinner').show();
    $('#sonuclarContainer').hide();
    
    $.ajax({
      // URL'yi yeni rotanızla değiştirin
      url: '{{ route("prim.kullanici.hesapla", $firma->id) }}',
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
          showSonuclar(response.data); // Bu fonksiyon aynı kalabilir
        } else {
          showErrors(response.message); // Bu fonksiyon aynı kalabilir
        }
      },
      error: function(xhr) {
        // ... (hata yönetimi aynı kalabilir)
      }
    });
  }

  // gunlukDetayGoster fonksiyonundaki AJAX URL'ini güncelleyin
  function gunlukDetayGoster(personelId, tarih) {
    // ... (modal'ı gösterme ve loading state)

    $.ajax({
        // URL'yi yeni rotanızla değiştirin
        url: '{{ route("prim.kullanici.detay", $firma->id) }}',
        type: 'GET',
        data: { 
            tarih: tarih // personelId göndermeye gerek yok, controllerda Auth::user() ile alınacak
        },
        // ... (geri kalan AJAX ayarları)
        success: function(response) {
            // ... (başarı durumu aynı kalabilir)
        },
        error: function(xhr) {
            // ... (hata durumu aynı kalabilir)
        }
    });
  }
  
  
</script>
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
                onclick="gunlukDetayGoster('${sonuc.tarih}')">
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

  // Modern günlük detay gösterme fonksiyonu
function gunlukDetayGoster(personelId, tarih) {
    // Loading state göster
    const modalContent = document.getElementById('gunlukDetayContent');
    modalContent.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Yükleniyor...</span>
            </div>
            <div class="mt-3">
                <h6 class="text-muted">Detaylar getiriliyor...</h6>
                <p class="small text-muted mb-0">Lütfen bekleyin...</p>
            </div>
        </div>
    `;

    // Modal'ı göster
    const modal = new bootstrap.Modal(document.getElementById('gunlukDetayModal'));
    modal.show();

    // AJAX çağrısı
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
                renderModernDetay(response.data);
            } else {
                showErrorState('Detaylar alınamadı.');
            }
        },
        error: function(xhr) {
            console.error('Detay yükleme hatası:', xhr);
            showErrorState('Günlük detaylar alınırken hata oluştu.');
        }
    });
}

// Modern detay render fonksiyonu
function renderModernDetay(detay) {
    const modalContent = document.getElementById('gunlukDetayContent');
    
    // Performans hesaplama
    let performansValue = '';
    let performansIcon = '';
    
    if (detay.rol === 'Teknisyen' || detay.rol === 'Teknisyen Yardımcısı') {
        const toplamTeklif = detay.islemler.reduce((sum, item) => sum + (parseFloat(item.cevap) || 0), 0);
        performansValue = numberFormat(toplamTeklif) + ' TL';
        performansIcon = 'fa-lira-sign';
    } else if (detay.rol === 'Operatör') {
        performansValue = detay.islemler.length + ' Servis';
        performansIcon = 'fa-clipboard-list';
    } else {
        performansValue = detay.islemler.length + ' Tamamlama';
        performansIcon = 'fa-check-circle';
    }
    
    let detayHtml = `
        <!-- Personel Bilgileri Kartı -->
        <div class="info-card">
            <div class="info-grid">
                <div class="info-item">
                    <div class="icon user">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Personel Adı</div>
                        <div class="info-value">${detay.personel.name}</div>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="icon role">
                        <i class="fas fa-user-tag"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Rol</div>
                        <div class="info-value">${detay.rol}</div>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="icon date">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Tarih</div>
                        <div class="info-value">${formatTarih(detay.tarih)}</div>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="icon performance">
                        <i class="fas ${performansIcon}"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Günlük Performans</div>
                        <div class="info-value">${performansValue}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- İşlemler Bölümü -->
        <div class="section-header">
            <i class="fas fa-list-ul"></i>
            <h6 class="section-title">Günlük İşlemler (${detay.islemler.length})</h6>
        </div>
    `;

    if (detay.islemler.length === 0) {
        detayHtml += `
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h5>Bu tarihte işlem bulunamadı</h5>
                <p class="mb-0">Seçilen tarihte herhangi bir primli işlem gerçekleştirilmemiş.</p>
            </div>
        `;
    } else {
        detayHtml += '<div class="modern-table"><table class="table table-hover mb-0">';
        
        // Rol göre tablo başlıkları ve içeriği
        if (detay.rol === 'Teknisyen' || detay.rol === 'Teknisyen Yardımcısı') {
            detayHtml += `
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag me-1"></i>Servis</th>
                        <th><i class="fas fa-user me-1"></i>Müşteri</th>
                        <th><i class="fas fa-lira-sign me-1"></i>Teklif Tutarı</th>
                        <th><i class="far fa-clock me-1"></i>İşlem Saati</th>
                        <th><i class="fas fa-check-circle me-1"></i>Durum</th>
                    </tr>
                </thead>
                <tbody>
            `;
            
            detay.islemler.forEach(function(islem, index) {
                const initials = getInitials(islem.musteri_adi);
                const avatarColor = getAvatarColor(islem.musteri_adi);
                
                detayHtml += `
                    <tr>
                        <td>
                            <div class="service-badge">
                                <i class="fas fa-hashtag"></i>
                                <a href="/{{$firma->id}}/servisler?did=${islem.servis_id}" class="service-link" target="_blank" style="color: #111111a1;">
                                    S-${islem.servis_id}
                                </a>
                                
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle ${avatarColor} text-white me-2" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.875rem;">
                                    ${initials}
                                </div>
                                <div>
                                    <span class="fw-medium d-block">${islem.musteri_adi}</span>
                                    <small class="text-muted">Müşteri #${index + 1}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="amount-text">
                                <i class="fas fa-lira-sign"></i>
                                ${numberFormat(islem.cevap)}
                            </div>
                        </td>
                        <td>
                            <div class="time-text">
                                <i class="far fa-clock"></i>
                                ${formatSaat(islem.created_at)}
                            </div>
                        </td>
                        <td>
                            <div class="status-badge success">
                                <i class="fas fa-check-circle"></i>
                                Teklif Verildi
                            </div>
                        </td>
                    </tr>
                `;
            });
            
        } else if (detay.rol === 'Operatör') {
            detayHtml += `
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag me-1"></i>Servis ID</th>
                        <th><i class="fas fa-user me-1"></i>Müşteri</th>
                        <th><i class="far fa-clock me-1"></i>Kayıt Saati</th>
                        <th><i class="fas fa-info-circle me-1"></i>Durum</th>
                    </tr>
                </thead>
                <tbody>
            `;
            
            detay.islemler.forEach(function(islem, index) {
                const initials = getInitials(islem.musteri_adi);
                const avatarColor = getAvatarColor(islem.musteri_adi);
                
                detayHtml += `
                    <tr>
                        <td>
                            <div class="service-badge">
                                <i class="fas fa-hashtag"></i>
                                <a href="/{{$firma->id}}/servisler?did=${islem.id}" class="service-link" target="_blank" style="color: #111111a1;">
                                    S-${islem.id}
                                </a>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle ${avatarColor} text-white me-2" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.875rem;">
                                    ${initials}
                                </div>
                                <div>
                                    <span class="fw-medium d-block">${islem.musteri_adi}</span>
                                    <small class="text-muted">Servis #${index + 1}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="time-text">
                                <i class="far fa-clock"></i>
                                ${formatSaat(islem.created_at)}
                            </div>
                        </td>
                        <td>
                            <div class="status-badge success">
                                <i class="fas fa-check-circle"></i>
                                Kaydedildi
                            </div>
                        </td>
                    </tr>
                `;
            });
            
        } else { // Atölye Ustası veya Çırak
            detayHtml += `
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag me-1"></i>Servis ID</th>
                        <th><i class="fas fa-user me-1"></i>Müşteri</th>
                        <th><i class="far fa-clock me-1"></i>Tamamlama Saati</th>
                        <th><i class="fas fa-wrench me-1"></i>İşlem</th>
                    </tr>
                </thead>
                <tbody>
            `;
            
            detay.islemler.forEach(function(islem, index) {
                const initials = getInitials(islem.musteri_adi);
                const avatarColor = getAvatarColor(islem.musteri_adi);
                
                detayHtml += `
                    <tr>
                        <td>
                            <div class="service-badge">
                                <i class="fas fa-hashtag"></i>
                                <a href="/{{$firma->id}}/servisler?did=${islem.servis_id}" class="service-link" target="_blank" style="color: #111111a1;">
                                    S-${islem.servis_id}
                                </a>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle ${avatarColor} text-white me-2" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.875rem;">
                                    ${initials}
                                </div>
                                <div>
                                    <span class="fw-medium d-block">${islem.musteri_adi}</span>
                                    <small class="text-muted">İşlem #${index + 1}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="time-text">
                                <i class="far fa-clock"></i>
                                ${formatSaat(islem.created_at)}
                            </div>
                        </td>
                        <td>
                            <div class="status-badge success">
                                <i class="fas fa-shipping-fast"></i>
                                Teslimata Hazır
                            </div>
                        </td>
                    </tr>
                `;
            });
        }
        
        detayHtml += '</tbody></table></div>';
    }

    modalContent.innerHTML = detayHtml;
    
    // Başarılı yüklenme bildirimi
    showToast('success', 'Detaylar başarıyla yüklendi', 'fas fa-check-circle');
}

// Hata durumu gösterme
function showErrorState(message) {
    const modalContent = document.getElementById('gunlukDetayContent');
    modalContent.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-exclamation-triangle text-warning"></i>
            <h5 class="text-danger">Bir Hata Oluştu</h5>
            <p class="mb-3">${message}</p>
            <button type="button" class="btn btn-outline-primary btn-modern" onclick="$('#gunlukDetayModal').modal('hide');">
                <i class="fas fa-arrow-left"></i>
                Geri Dön
            </button>
        </div>
    `;
}

// Toast bildirimi gösterme
function showToast(type, message, icon) {
    const toastTypes = {
        success: 'text-bg-success',
        error: 'text-bg-danger',
        warning: 'text-bg-warning',
        info: 'text-bg-info'
    };
    
    const toastHtml = `
        <div class="toast align-items-center ${toastTypes[type]} border-0" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="${icon} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    const toastContainer = document.createElement('div');
    toastContainer.innerHTML = toastHtml;
    document.body.appendChild(toastContainer);
    
    const toast = new bootstrap.Toast(toastContainer.querySelector('.toast'));
    toast.show();
    
    // Toast kapandıktan sonra container'ı temizle
    setTimeout(() => {
        toastContainer.remove();
    }, 5000);
}

// Yardımcı fonksiyonlar
function getInitials(name) {
    if (!name) return '??';
    return name.split(' ')
        .map(n => n.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2);
}

function getAvatarColor(name) {
    const colors = ['bg-primary', 'bg-success', 'bg-warning', 'bg-info', 'bg-secondary', 'bg-danger'];
    if (!name) return colors[0];
    
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }
    const index = Math.abs(hash) % colors.length;
    return colors[index];
}

function formatTarih(tarih) {
    if (!tarih) return 'Belirtilmemiş';
    
    const date = new Date(tarih);
    return date.toLocaleDateString('tr-TR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        weekday: 'long'
    });
}

function formatSaat(datetime) {
    if (!datetime) return '--:--';
    
    const date = new Date(datetime);
    return date.toLocaleTimeString('tr-TR', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

function numberFormat(number) {
    if (!number || isNaN(number)) return '0';
    
    return new Intl.NumberFormat('tr-TR', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    }).format(number);
}

// Modal kapanma olayları
document.getElementById('gunlukDetayModal').addEventListener('hidden.bs.modal', function() {
    // Modal kapandığında içeriği temizle
    document.getElementById('gunlukDetayContent').innerHTML = '';
});

// Klavye kısayolları
document.addEventListener('keydown', function(e) {
    // ESC tuşu ile modal kapatma
    if (e.key === 'Escape') {
        const modal = bootstrap.Modal.getInstance(document.getElementById('gunlukDetayModal'));
        if (modal) {
            modal.hide();
        }
    }
});

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