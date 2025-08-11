@extends('frontend.secure.user_master')
@section('user')
<div class="page-content">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
        <div class="card ">
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
                            <thead class="table-dark">
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


<script>
$(document).ready(function() {
    $('#filtreDropdown').on('shown.bs.dropdown', function () {
        // Form elementlerini yeniden render et
        $('#filtreForm').trigger('reset');
        setTimeout(function() {
            $('#araBtn').show();
        }, 100);
    });
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

//    // Ara butonu
//     $('#araBtn').click(function() {
//         $('.dropdown-toggle').dropdown('hide');
//         loadTechnicianStatistics();
//     });

    // Teknisyen istatistiklerini yükle
    function loadTechnicianStatistics() {
        $('#loadingDiv').show();
        $('#istatistikTable').hide();

        const tarih1 = $('#tarih1').val();
        const tarih2 = $('#tarih2').val();
        const cihazTur = $('#cihazTur').val();

        // DÜZELTME: Doğru URL formatı - tenant_id değişkenini doğru kullan
        $.ajax({
            url: '/{{ $tenant_id }}/teknisyen-istatistikleri/data',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                tarihAraligi: tarih1 + '---' + tarih2,
                cihazTur: cihazTur
            },
            success: function(response) {
                console.log('Response:', response); // Debug için
                if (response.success) {
                    buildStatisticsTable(response.data);
                } else {
                    showError(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('Error:', xhr.responseText); // Debug için
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
        let html = '';
        
        if (data.length === 0) {
            html = '<tr><td colspan="9" class="text-center">Veri bulunamadı.</td></tr>';
        } else {
            data.forEach(function(teknisyen) {
                html += `
                    <tr class="teknisyen-detay-btn" data-teknisyen-id="${teknisyen.id}">
                        <td><strong>${teknisyen.name}</strong></td>
                        <td class="text-center"><strong>${teknisyen.atanan_servis}</strong></td>
                        <td class="text-center"><strong>${teknisyen.tamamlanan_servis}</strong></td>
                        <td class="text-center"><strong>${teknisyen.sikayetci_servis}</strong></td>
                        <td class="text-center"><strong>${teknisyen.iptal_servis}</strong></td>
                        <td class="text-center"><strong>${teknisyen.haber_verecek}</strong></td>
                        <td class="text-center"><strong>${teknisyen.fiyat_anlasma}</strong></td>
                        <td class="text-center"><strong>${formatCurrency(teknisyen.alinan_ucret)}</strong></td>
                        <td class="text-center"><strong>${formatCurrency(teknisyen.verilen_teklif)}</strong></td>
                    </tr>
                `;
            });
        }

        $('#teknisyenTableBody').html(html);

        // DataTable initialize
        if ($.fn.DataTable.isDataTable('#teknisyenTable')) {
            $('#teknisyenTable').DataTable().destroy();
        }

        $('#teknisyenTable').DataTable({
            "paging": false,
            "info": false,
            "searching": false,
            "ordering": true,
            "order": [[7, 'desc']], // Alınan ücrete göre sırala
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
    }

    // Teknisyen detayına tıklama
    $(document).on('click', '.teknisyen-detay-btn', function() {
        const teknisyenId = $(this).data('teknisyen-id');
        const $this = $(this);
        
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
            
            // Burada tekniszen detay bilgilerini yükleyebilirsiniz
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
        return parseFloat(amount || 0).toLocaleString('tr-TR') + ' TL';
    }

    function showError(message) {
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