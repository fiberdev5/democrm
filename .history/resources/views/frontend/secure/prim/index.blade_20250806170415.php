@extends('frontend.secure.user_master')
@section('user')
<div class="container-fluid " style="margin-top: 35px;">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Tekniyen Prim Hesaplama</h4>
                </div>
                <div class="card-body">
                    <form id="primForm" class="row g-3">
                        <div class="col-md-3">
                            <label for="personel_id" class="form-label">Personel</label>
                            <select class="form-select" id="personel_id" name="personel_id" required>
                                <option value="">Personel Seçiniz</option>
                                @foreach($personeller as $personel)
                                    <option value="{{ $personel->user_id }}">{{ $personel->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="tarih1" class="form-label">Başlangıç Tarihi</label>
                            <input type="date" class="form-control datepicker" id="tarih1" name="tarih1" 
                                   placeholder="gg/aa/yyyy" required>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="tarih2" class="form-label">Bitiş Tarihi</label>
                            <input type="date" class="form-control datepicker" id="tarih2" name="tarih2" 
                                   placeholder="gg/aa/yyyy" required>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="durum" class="form-label">Durum</label>
                            <select class="form-select" id="durum" name="durum" required>
                                <option value="">Durum Seçiniz</option>
                                <option value="1">Aktif</option>
                                <option value="0">Pasif</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
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

    <!-- Sonuçlar Tablosu -->
    <div class="row mt-4" id="sonuclarContainer" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Prim Hesaplama Sonuçları</h5>
                    <div id="sonucBilgi" class="text-muted small"></div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="sonuclarTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Servis ID</th>
                                    <th>Müşteri</th>
                                    <th>Cihaz Markası</th>
                                    <th>Cihaz Türü</th>
                                    <th>Personel</th>
                                    <th>Opt Prim</th>
                                    <th>Aty Prim</th>
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

<!-- Servis Detay Modal -->
<div class="modal fade" id="servisDetayModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Servis Detayları</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="servisDetayContent">
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


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
</style>


<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/tr.js"></script>
<script>
$(document).ready(function() {
    // Tarih seçicileri başlat
    flatpickr("#tarih1, #tarih2", {
        dateFormat: "Y-m-d",
        locale: "tr",
        allowInput: true
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
                showErrors(response.errors);
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
    
    // Bilgi alanını güncelle
    $('#sonucBilgi').html(`
        <strong>Personel:</strong> ${personel.adsoyad} | 
        <strong>Tarih:</strong> ${tarihAraligi.baslangic} - ${tarihAraligi.bitis} | 
        <strong>Toplam:</strong> ${toplamKayit} kayıt
    `);
    
    // Tablo içeriğini temizle
    $('#sonuclarTableBody').empty();
    
    if (sonuclar.length === 0) {
        $('#sonuclarTableBody').html(`
            <tr>
                <td colspan="9" class="text-center py-4">
                    <i class="fas fa-info-circle text-muted"></i> 
                    Belirtilen kriterlere uygun sonuç bulunamadı.
                </td>
            </tr>
        `);
    } else {
        sonuclar.forEach(function(sonuc, index) {
            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td><span class="badge bg-primary">${sonuc.id || sonuc.servisid || '-'}</span></td>
                    <td>${sonuc.adSoyad || sonuc.musteri_adi || '-'}</td>
                    <td>${sonuc.marka || '-'}</td>
                    <td>${sonuc.cihaz || '-'}</td>
                    <td>${sonuc.adsoyad || sonuc.personel_adi || '-'}</td>
                    <td>
                        <span class="text-success fw-bold">
                            ${sonuc.mOptPrim || sonuc.cOptPrim || '0'} ₺
                        </span>
                    </td>
                    <td>
                        <span class="text-warning fw-bold">
                            ${sonuc.mAtyPrim || sonuc.cAtyPrim || '0'} ₺
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-info btn-sm" 
                                onclick="servisDetayGoster(${sonuc.id || sonuc.servisid})">
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

function showErrors(errors) {
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

function servisDetayGoster(servisId) {
    $.ajax({
        url: '{{ route("prim.detay", $firma->id) }}',
        type: 'GET',
        data: { servis_id: servisId },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                const servis = response.data.servis;
                const planlama = response.data.planlama;
                
                let detayHtml = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-info-circle"></i> Servis Bilgileri</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Servis ID:</strong></td><td>${servis.id}</td></tr>
                                <tr><td><strong>Müşteri:</strong></td><td>${servis.musteri_adi}</td></tr>
                                <tr><td><strong>Telefon:</strong></td><td>${servis.telefon || '-'}</td></tr>
                                <tr><td><strong>Cihaz:</strong></td><td>${servis.marka} ${servis.cihaz}</td></tr>
                                <tr><td><strong>Durum:</strong></td><td>
                                    <span class="badge ${servis.durum == '1' ? 'bg-success' : 'bg-danger'}">
                                        ${servis.durum == '1' ? 'Aktif' : 'Pasif'}
                                    </span>
                                </td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-user"></i> Personel Bilgileri</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Kayıt Alan:</strong></td><td>${servis.personel_adi}</td></tr>
                                <tr><td><strong>Kayıt Tarihi:</strong></td><td>${servis.kayitTarih || '-'}</td></tr>
                                <tr><td><strong>Son Güncelleme:</strong></td><td>${servis.guncellemeTarih || '-'}</td></tr>
                            </table>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6><i class="fas fa-tasks"></i> Servis Planlama Aşamaları</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Tarih</th>
                                    <th>Personel</th>
                                    <th>İşlem</th>
                                    <th>Açıklama</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                if (planlama.length > 0) {
                    planlama.forEach(function(plan) {
                        detayHtml += `
                            <tr>
                                <td>${new Date(plan.tarih).toLocaleDateString('tr-TR')}</td>
                                <td>${plan.personel_adi}</td>
                                <td><span class="badge bg-info">${plan.gidenIslem}</span></td>
                                <td>${plan.aciklama || '-'}</td>
                            </tr>
                        `;
                    });
                } else {
                    detayHtml += `
                        <tr>
                            <td colspan="4" class="text-center text-muted">Planlama aşaması bulunamadı.</td>
                        </tr>
                    `;
                }
                
                detayHtml += `
                            </tbody>
                        </table>
                    </div>
                `;
                
                $('#servisDetayContent').html(detayHtml);
                $('#servisDetayModal').modal('show');
            } else {
                toastr.error('Servis detayları alınamadı.');
            }
        },
        error: function() {
            toastr.error('Servis detayları alınırken hata oluştu.');
        }
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
$('#tarih2').on('change', function() {
    const tarih1 = $('#tarih1').val();
    const tarih2 = $('#tarih2').val();
    
    if (tarih1 && tarih2) {
        const date1 = new Date(tarih1.split('/').reverse().join('-'));
        const date2 = new Date(tarih2.split('/').reverse().join('-'));
        
        if (date2 < date1) {
            toastr.warning('Bitiş tarihi başlangıç tarihinden önce olamaz.');
            $('#tarih2').val('');
        }
    }
});
</script>
@endsection