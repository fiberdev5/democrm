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

    // Tablo oluştur - ÖNEMLİ DEĞİŞİKLİKLER
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
            
            // Detay satırı ekle
            const detayHtml = `
                <tr class="detay-satir">
                    <td colspan="9">
                        <div class="p-3">
                            <div class="text-center">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Detay yükleniyor...</span>
                                </div>
                                <div class="mt-2">Detay bilgiler yükleniyor...</div>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
            
            $this.after(detayHtml);
            
            // Burada teknisyen detay bilgilerini yükleyebilirsiniz
            setTimeout(function() {
                $('.detay-satir').html(`
                    <td colspan="9">
                        <div class="p-3">
                            <h6>Teknisyen Detay Bilgileri</h6>
                            <p>Bu bölümde teknisyenin detaylı servis bilgileri gösterilebilir.</p>
                            <p><small class="text-muted">Detay sayfası geliştirme aşamasındadır.</small></p>
                        </div>
                    </td>
                `);
            }, 1000);
        }
    });

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

