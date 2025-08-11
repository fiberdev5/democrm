@extends('frontend.secure.user_master')
@section('user')
<div class="page-content servis-istatistik">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        <div class="card">
            <div class="card-header sayfaBaslik d-flex justify-content-between align-items-center">
                <span>Teknisyen İstatistikleri</span>
                <!-- Filtre Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="filtreDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        FİLTRELE
                    </button>
                    <div class="dropdown-menu p-3 dropdown-menu-end" style="min-width: 300px;">
                        <form id="filtreForm">
                            <div class="row mb-3">
                                <label class="col-4 col-form-label">Cihaz Türü</label>
                                <div class="col-8">
                                    <select class="form-select" name="cihazTur" id="cihazTur">
                                        <option value="">Hepsi</option>
                                        @foreach($cihazTurleri as $cihaz)
                                            <option value="{{ $cihaz->id }}">{{ $cihaz->cihaz }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-4 col-form-label">Tarih Aralığı</label>
                                <div class="col-8">
                                    <input type="text" name="tarih1" id="tarih1" class="form-control datepicker mb-2" readonly value="{{ date('d/m/Y') }}" style="background:#fff;">
                                    <input type="text" name="tarih2" id="tarih2" class="form-control datepicker mb-2" readonly value="{{ date('d/m/Y') }}" style="background:#fff;">
                                    
                                    <div class="tarih-butonlari">
                                        <button type="button" class="btn btn-sm btn-outline-secondary tarih-btn me-1 mb-1" data-days="30">Son 1 Ay</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary tarih-btn me-1 mb-1" data-days="15">Son 15 Gün</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary tarih-btn me-1 mb-1" data-days="7">Son 7 Gün</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary tarih-btn me-1 mb-1" data-days="1" data-yesterday="true">Dün</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary tarih-btn me-1 mb-1" data-days="0">Bugün</button>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-primary btn-sm w-100" id="araBtn">ARA</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div id="loadingDiv" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                </div>

                <div id="istatistikTable" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="teknisyenTable">
                            <thead class="title">
                                <tr>
                                    <th>Teknisyen</th>
                                    <th class="text-center" style="width: 90px;">Atanan<br><small>Servis</small></th>
                                    <th class="text-center" style="width: 90px;">Tamamlanan<br><small>Servis</small></th>
                                    <th class="text-center" style="width: 90px;">Şikayetçi<br><small>Servis</small></th>
                                    <th class="text-center" style="width: 90px;">İptal<br><small>Servis</small></th>
                                    <th class="text-center" style="width: 90px;">Haber<br><small>Verecek</small></th>
                                    <th class="text-center" style="width: 90px;">Fiyatta<br><small>Anlaşılamadı</small></th>
                                    <th class="text-center" style="width: 90px;">Alınan<br><small>Ücret</small></th>
                                    <th class="text-center" style="width: 90px;">Verilen<br><small>Teklif</small></th>
                                </tr>
                            </thead>
                            <tbody id="teknisyenTableBody">
                                <!-- AJAX ile doldurulacak -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dropdown-menu {
    border: 1px solid #dee2e6;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.tarih-butonlari .btn {
    font-size: 0.75rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.85rem;
}

.table td {
    vertical-align: middle;
    font-size: 0.9rem;
}

.table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.teknisyen-detay-btn {
    cursor: pointer;
    transition: background-color 0.2s;
}

.teknisyen-detay-btn:hover {
    background-color: rgba(0, 123, 255, 0.1);
}

.teknisyen-detay-btn.clicked {
    background-color: rgba(0, 123, 255, 0.15);
}

.detay-satir {
    background-color: #f8f9fa;
}

.detay-satir td {
    padding: 0;
}
.dropdown-menu {
    min-width: 320px !important;
    min-height: 220px;
    padding: 15px;
    overflow-y: auto;
}

.dropdown-menu form {
    width: 100%;
}

.dropdown-menu .form-select,
.dropdown-menu input.form-control {
    width: 100%;
    margin-bottom: 10px;
}

</style>

<script>
$(document).ready(function() {
    // Global DataTable referansı
    let dataTable = null;

    // Datepicker initialize
    $('.datepicker').datepicker({
        language: 'tr',
        autoclose: true,
        format: 'dd/mm/yyyy',
        endDate: new Date()
    });

    // Tarih butonları
    $('.tarih-btn').click(function() {
        const days = $(this).data('days');
        const isYesterday = $(this).data('yesterday');
        
        let tarih1, tarih2;
        
        if (isYesterday) {
            // Dün
            tarih1 = moment().subtract(1, 'days').format('DD/MM/YYYY');
            tarih2 = moment().subtract(1, 'days').format('DD/MM/YYYY');
        } else if (days === 0) {
            // Bugün
            tarih1 = moment().format('DD/MM/YYYY');
            tarih2 = moment().format('DD/MM/YYYY');
        } else {
            // Belirtilen gün sayısı kadar geriye
            tarih1 = moment().subtract(days - 1, 'days').format('DD/MM/YYYY');
            tarih2 = moment().format('DD/MM/YYYY');
        }
        
        $('#tarih1').val(tarih1);
        $('#tarih2').val(tarih2);
    });

    // Dropdown içindeki form gönderimini engelle
    $('.dropdown-menu').click(function(e) {
        e.stopPropagation();
    });

    // Sayfa yüklendiğinde varsayılan verileri getir
    loadTechnicianStatistics();

    // Ara butonu
    $('#araBtn').click(function() {
        $('.dropdown-toggle').dropdown('hide');
        loadTechnicianStatistics();
    });

    // Teknisyen istatistiklerini yükle
    function loadTechnicianStatistics() {
        $('#loadingDiv').show();
        $('#istatistikTable').hide();

        const tarih1 = $('#tarih1').val();
        const tarih2 = $('#tarih2').val();
        const cihazTur = $('#cihazTur').val();

        console.log('Gönderilen parametreler:', { tarih1, tarih2, cihazTur }); // Debug

        $.ajax({
            url: '/{{ $tenant_id }}/teknisyen-istatistikleri/data',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                tarihAraligi: tarih1 + '---' + tarih2,
                cihazTur: cihazTur
            },
            success: function(response) {
                console.log('AJAX Success Response:', response); // Debug için
                if (response.success) {
                    buildStatisticsTable(response.data);
                } else {
                    console.error('Response error:', response.message);
                    showError(response.message || 'Bilinmeyen bir hata oluştu');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    error: error
                });
                showError('Bir hata oluştu: ' + error);
            },
            complete: function() {
                $('#loadingDiv').hide();
                $('#istatistikTable').show();
            }
        });
    }

    // Tablo oluştur
    function buildStatisticsTable(data) {
        console.log('buildStatisticsTable çağrıldı, data:', data); // Debug

        // Önce varsa DataTable'ı tamamen yok et
        if (dataTable && $.fn.DataTable.isDataTable('#teknisyenTable')) {
            console.log('DataTable destroy ediliyor...');
            dataTable.destroy();
            dataTable = null;
        }

        // Detay satırlarını temizle
        $('.detay-satir').remove();
        $('.teknisyen-detay-btn').removeClass('clicked');

        let html = '';
        
        if (!data || data.length === 0) {
            html = '<tr><td colspan="9" class="text-center">Veri bulunamadı.</td></tr>';
            console.log('Veri bulunamadı');
        } else {
            console.log('Tablo oluşturuluyor, veri sayısı:', data.length);
            data.forEach(function(teknisyen, index) {
                console.log(`Teknisyen ${index + 1}:`, teknisyen);
                
                html += `
                    <tr class="teknisyen-detay-btn" data-teknisyen-id="${teknisyen.id || 'unknown'}">
                        <td><strong>${teknisyen.name || 'Bilinmeyen'}</strong></td>
                        <td class="text-center"><strong>${teknisyen.atanan_servis || 0}</strong></td>
                        <td class="text-center"><strong>${teknisyen.tamamlanan_servis || 0}</strong></td>
                        <td class="text-center"><strong>${teknisyen.sikayetci_servis || 0}</strong></td>
                        <td class="text-center"><strong>${teknisyen.iptal_servis || 0}</strong></td>
                        <td class="text-center"><strong>${teknisyen.haber_verecek || 0}</strong></td>
                        <td class="text-center"><strong>${teknisyen.fiyat_anlasma || 0}</strong></td>
                        <td class="text-center"><strong>${formatCurrency(teknisyen.alinan_ucret || 0)}</strong></td>
                        <td class="text-center"><strong>${formatCurrency(teknisyen.verilen_teklif || 0)}</strong></td>
                    </tr>
                `;
            });
        }

        console.log('HTML oluşturuldu, tbody güncelleniyor...');
        $('#teknisyenTableBody').html(html);
        
        // HTML güncellemesinden sonra kısa bir gecikme ekle
        setTimeout(function() {
            console.log('DataTable yeniden initialize ediliyor...');
            
            // DataTable'ı yeniden initialize et
            try {
                dataTable = $('#teknisyenTable').DataTable({
                    "paging": false,
                    "info": false,
                    "searching": false,
                    "ordering": true,
                    "order": [[7, 'desc']], // Alınan ücrete göre sırala
                    "destroy": true, // Önemli: varsa öncekini yok et
                    "language": {
                        "sEmptyTable": "Herhangi bir teknisyen verisi bulunamadı.",
                        "sSearch": "Teknisyen Ara:",
                        "sZeroRecords": "Eşleşen kayıt bulunamadı",
                        "oPaginate": {
                            "sNext": "Sonraki",
                            "sPrevious": "Önceki"
                        }
                    },
                    "columnDefs": [
                        { "orderable": false, "targets": 0 }
                    ]
                });
                console.log('DataTable başarıyla oluşturuldu');
            } catch (error) {
                console.error('DataTable oluşturma hatası:', error);
            }
        }, 100); // 100ms gecikme
    }

// Teknisyen detayına tıklama
$(document).on('click', '.teknisyen-detay-btn', function() {
    const teknisyenId = $(this).data('teknisyen-id');
    const $this = $(this);


    console.log('Teknisyen detayına tıklandı, ID:', teknisyenId);
    
    // Önceki detay satırlarını kapat
    $('.detay-satir').remove();
    $('.teknisyen-detay-btn').removeClass('clicked');

    if ($this.hasClass('clicked')) {
        $this.removeClass('clicked');
    } else {
        $this.addClass('clicked');
        
        // Loading detay satırı ekle
        const detayHtml = `
            <tr class="detay-satir">
                <td colspan="9">
                    <div class="p-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                        <div class="text-center mb-3">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Detay yükleniyor...</span>
                            </div>
                            <div class="mt-2 text-muted">Teknisyen detay bilgileri yükleniyor...</div>
                        </div>
                    </div>
                </td>
            </tr>
        `;
        
        $this.after(detayHtml);
        
        // Detay verilerini yükle
        loadTechnicianDetail(teknisyenId);
    }
});

// Teknisyen detay verilerini yükle
function loadTechnicianDetail(teknisyenId) {
    const tarih1 = $('#tarih1').val();
    const tarih2 = $('#tarih2').val();
    const cihazTur = $('#cihazTur').val();

    $.ajax({
        url: '/{{ $tenant_id }}/teknisyen-detay/data',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            teknisyen_id: teknisyenId,
            tarihAraligi: tarih1 + '---' + tarih2,
            cihazTur: cihazTur
        },
        success: function(response) {
            if (response.success) {
                renderTechnicianDetail(response.data);
            } else {
                showDetailError(response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Detay yükleme hatası:', error);
            showDetailError('Detay bilgileri yüklenirken hata oluştu: ' + error);
        }
    });
}

// Teknisyen detayını render et
function renderTechnicianDetail(data) {
    const detayHtml = `
        <td colspan="9">
            <div class="p-4" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-user-cog me-2"></i>
                            ${data.teknisyen_adi} - Detaylı İstatistikler
                        </h5>
                    </div>
                </div>

                <!-- Grafikler -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title text-success">Tamamlanan Servisler</h6>
                                <canvas id="tamamlananChart_${data.teknisyen_adi.replace(/\s+/g, '')}" height="120"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title text-danger">İptal Servisler</h6>
                                <canvas id="iptalChart_${data.teknisyen_adi.replace(/\s+/g, '')}" height="120"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title text-info">Alınan Ücretler</h6>
                                <canvas id="gelirChart_${data.teknisyen_adi.replace(/\s+/g, '')}" height="120"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detaylı Aşama Sayıları -->
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-dark mb-3">
                            <i class="fas fa-chart-bar me-2"></i>
                            Servis Aşamaları Detayı
                        </h6>
                    </div>
                </div>

                <div class="row g-3">
                    ${generateStageCards(data.detay_sayilari)}
                </div>
            </div>
        </td>
    `;
    
    $('.detay-satir').html(detayHtml);
    
    // Grafikleri çiz
    requestAnimationFrame(() => {
        drawDetailCharts(data);
    });

}

// Aşama kartlarını oluştur
function generateStageCards(detaySayilari) {
    const stages = [
        { key: 'atanan_servis', label: 'Atanan Servisler', color: 'primary' },
        { key: 'tamamlanan_servis', label: 'Tamamlanan Servisler', color: 'success' },
        { key: 'sikayetci_servis', label: 'Şikayetçi Servisler', color: 'warning' },
        { key: 'iptal_servis', label: 'İptal Servisler', color: 'danger' },
        { key: 'haber_verecek', label: 'Haber Verecek', color: 'info' },
        { key: 'atolyede_tamir', label: 'Atölyede Tamir', color: 'secondary' },
        { key: 'atolyeye_aldir', label: 'Atölyeye Aldır', color: 'dark' },
        { key: 'cihaz_atolyede', label: 'Cihaz Atölyede', color: 'light' },
        { key: 'tamir_edilemiyor', label: 'Tamir Edilemiyor', color: 'danger' },
        { key: 'cihaz_teslim', label: 'Cihaz Teslim', color: 'success' },
        { key: 'cihaz_teslim_parca', label: 'Cihaz Teslim (Parça)', color: 'success' },
        { key: 'fiyat_anlasilamadi', label: 'Fiyat Anlaşılamadı', color: 'warning' },
        { key: 'musteri_atolyeye_getirdi', label: 'Müşteri Atölyeye Getirdi', color: 'info' },
        { key: 'musteriye_ulasilamadi', label: 'Müşteriye Ulaşılamadı', color: 'warning' },
        { key: 'nakliye_gonder', label: 'Nakliye Gönder', color: 'primary' },
        { key: 'nakliye_teslim', label: 'Nakliye Teslim', color: 'primary' },
        { key: 'parca_hazir', label: 'Parça Hazır', color: 'success' },
        { key: 'parca_sipariste', label: 'Parça Siparişte', color: 'warning' },
        { key: 'parca_tek_yon', label: 'Parça Tek. Yönlendir', color: 'info' },
        { key: 'parca_atolyeye_alindi', label: 'Parça Atölyeye Alındı', color: 'secondary' },
        { key: 'tahsilata_gonder', label: 'Tahsilata Gönder', color: 'success' },
        { key: 'teslimata_hazir', label: 'Teslimata Hazır', color: 'success' },
        { key: 'garantili_cikti', label: 'Garantili Çıktı', color: 'success' },
        { key: 'yeniden_tek_yon', label: 'Yeniden Tek. Yönlendir', color: 'info' },
        { key: 'yerinde_bakim', label: 'Yerinde Bakım', color: 'success' }
    ];

    let html = '';
    stages.forEach(stage => {
        const value = detaySayilari[stage.key] || 0;
        const badgeClass = value > 0 ? `bg-${stage.color}` : 'bg-light text-dark';
        
        html += `
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center p-3">
                        <div class="mb-2">
                            <span class="badge ${badgeClass} fs-6 px-3 py-2">${value}</span>
                        </div>
                        <p class="card-text small mb-0" style="font-size: 0.85rem;">
                            ${stage.label}
                        </p>
                    </div>
                </div>
            </div>
        `;
    });

    return html;
}

// Detay grafiklerini çiz
function drawDetailCharts(data) {
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    };

    // Tamamlanan Servisler Grafiği
    const tamamlananCtx = document.getElementById(`tamamlananChart_${data.teknisyen_adi.replace(/\s+/g, '')}`);
    if (tamamlananCtx) {
        new Chart(tamamlananCtx, {
            type: 'line',
            data: {
                labels: data.grafik_data.labels,
                datasets: [{
                    data: data.grafik_data.tamamlanan,
                    borderColor: 'rgba(40, 167, 69, 1)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: chartOptions
        });
    }

    // İptal Servisler Grafiği
    const iptalCtx = document.getElementById(`iptalChart_${data.teknisyen_adi.replace(/\s+/g, '')}`);
    if (iptalCtx) {
        new Chart(iptalCtx, {
            type: 'line',
            data: {
                labels: data.grafik_data.labels,
                datasets: [{
                    data: data.grafik_data.iptal,
                    borderColor: 'rgba(220, 53, 69, 1)',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: chartOptions
        });
    }

    // Gelir Grafiği
    const gelirCtx = document.getElementById(`gelirChart_${data.teknisyen_adi.replace(/\s+/g, '')}`);
    if (gelirCtx) {
        new Chart(gelirCtx, {
            type: 'line',
            data: {
                labels: data.grafik_data.labels,
                datasets: [{
                    data: data.grafik_data.gelir,
                    borderColor: 'rgba(23, 162, 184, 1)',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: chartOptions
        });
    }
}

// Detay hata göster
function showDetailError(message) {
    $('.detay-satir').html(`
        <td colspan="9">
            <div class="p-4 text-center">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${message}
                </div>
            </div>
        </td>
    `);
}
    // Yardımcı fonksiyonlar
    function formatCurrency(amount) {
        const num = parseFloat(amount) || 0;
        return num.toLocaleString('tr-TR') + ' TL';
    }

    function showError(message) {
        console.error('Hata:', message);
        // toastr varsa kullan, yoksa alert kullan
        if (typeof toastr !== 'undefined') {
            toastr.error(message, 'Hata');
        } else {
            alert('Hata: ' + message);
        }
    }
});

// Moment.js için Türkçe lokalizasyon
if (typeof moment !== 'undefined') {
    moment.locale('tr');
}
</script>
@endsection

