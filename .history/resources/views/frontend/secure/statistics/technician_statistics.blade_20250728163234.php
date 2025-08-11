@extends('frontend.secure.user_master')
@section('user')
<div class="page-content">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Teknisyen İstatistikleri</h3>
                </div>
                
                <div class="card-body">
                    <!-- Filtre Alanı -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="tarihAraligi">Tarih Aralığı:</label>
                            <input type="text" id="tarihAraligi" class="form-control" placeholder="Tarih seçiniz">
                        </div>
                        <div class="col-md-4">
                            <label for="cihazTur">Cihaz Türü:</label>
                            <select id="cihazTur" class="form-control">
                                <option value="">Tümü</option>
                                @foreach($cihazTurleri as $cihaz)
                                    <option value="{{ $cihaz->id }}">{{ $cihaz->cihaz }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label><br>
                            <button type="button" id="filterBtn" class="btn btn-primary">
                                <i class="fas fa-search"></i> Listele
                            </button>
                        </div>
                    </div>

                    <!-- Loading -->
                    <div id="loading" class="text-center" style="display: none;">
                        <img src="{{ asset('images/ajax_load.gif') }}" alt="Yükleniyor...">
                        <p>Veriler yükleniyor...</p>
                    </div>

                    <!-- Tablo -->
                    <div id="tableContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="name">
                                            <span class="d-none d-md-inline">Personel</span>
                                            <span class="d-md-none">Personel</span>
                                        </th>
                                        <th style="width: 85px">
                                            <span class="d-none d-md-inline">Atanan Servis</span>
                                            <span class="d-md-none">A</span>
                                        </th>
                                        <th style="width: 85px">
                                            <span class="d-none d-md-inline">Tamamlanan Servis</span>
                                            <span class="d-md-none">T</span>
                                        </th>
                                        <th style="width: 85px">
                                            <span class="d-none d-md-inline">Şikayetçi Servis</span>
                                            <span class="d-md-none">Ş</span>
                                        </th>
                                        <th style="width: 85px">
                                            <span class="d-none d-md-inline">İptal<br>Servis</span>
                                            <span class="d-md-none">İ</span>
                                        </th>
                                        <th style="width: 85px">
                                            <span class="d-none d-md-inline">Haber Verecek</span>
                                            <span class="d-md-none">H</span>
                                        </th>
                                        <th style="width: 85px">
                                            <span class="d-none d-md-inline">Fiyatta Anlaşılamadı</span>
                                            <span class="d-md-none">F</span>
                                        </th>
                                        <th style="width: 85px">
                                            <span class="d-none d-md-inline">Alınan<br>Ücret</span>
                                            <span class="d-md-none">Ü</span>
                                        </th>
                                        <th style="width: 85px">
                                            <span class="d-none d-md-inline">Verilen Teklif</span>
                                            <span class="d-md-none">T</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <!-- AJAX ile doldurulacak -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Detay Modal -->
                    <div class="modal fade" id="detayModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Teknisyen Detay İstatistikleri</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body" id="detayModalBody">
                                    <!-- Detay içeriği buraya gelecek -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
       
    </div>
</div>
@endsection

<style>
.detayGrafikler {
    margin: 20px 0;
}

.detayGrafikler .card-body {
    padding: 15px;
}

.detayGrafikler .col-md-4 {
    text-align: center;
    margin-bottom: 20px;
}

.detayGrafikler canvas {
    max-height: 200px;
}

.detayAsamalar {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 20px;
}

.detayAsamalar .cols {
    flex: 1;
    min-width: 150px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 10px;
    text-align: center;
}

.detayAsamalar .cols p {
    font-size: 12px;
    margin-bottom: 5px;
    font-weight: 500;
}

.detayAsamalar .cols h2 {
    font-size: 24px;
    font-weight: bold;
    color: #007bff;
    margin: 0;
}

.table tbody tr.clickable-row {
    cursor: pointer;
}

.table tbody tr.clickable-row:hover {
    background-color: #f5f5f5;
}

.table tbody tr.active {
    background-color: #e3f2fd;
}

@media (max-width: 768px) {
    .detayAsamalar .cols {
        min-width: 120px;
    }
    
    .detayAsamalar .cols h2 {
        font-size: 18px;
    }
}
</style>

<script>
$(document).ready(function() {
    let dataTable;
    
    // Date Range Picker
    $('#tarihAraligi').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY',
            separator: ' - ',
            applyLabel: 'Uygula',
            cancelLabel: 'İptal',
            fromLabel: 'Başlangıç',
            toLabel: 'Bitiş',
            customRangeLabel: 'Özel',
            weekLabel: 'H',
            daysOfWeek: ['Paz', 'Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt'],
            monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
                        'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
            firstDay: 1
        },
        startDate: moment().subtract(7, 'days'),
        endDate: moment(),
        ranges: {
            'Son 7 Gün': [moment().subtract(6, 'days'), moment()],
            'Son 15 Gün': [moment().subtract(14, 'days'), moment()],
            'Son 30 Gün': [moment().subtract(29, 'days'), moment()],
            'Bu Ay': [moment().startOf('month'), moment().endOf('month')],
            'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    // Filtre butonu
    $('#filterBtn').click(function() {
        loadTechnicianStats();
    });

    // Sayfa yüklendiğinde varsayılan verileri getir
    loadTechnicianStats();

    function loadTechnicianStats() {
        const tarihAraligi = $('#tarihAraligi').val();
        const cihazTur = $('#cihazTur').val();
        
        if (!tarihAraligi) {
            alert('Lütfen tarih aralığı seçiniz!');
            return;
        }

        const tarihler = tarihAraligi.split(' - ');
        const formattedRange = tarihler[0] + '---' + tarihler[1];

        $('#loading').show();
        $('#tableContainer').hide();

        $.ajax({
            url: '{{ route("technician.statistics.data", $tenant_id) }}',
            method: 'POST',
            data: {
                tarihAraligi: formattedRange,
                cihazTur: cihazTur,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#loading').hide();
                
                if (response.success) {
                    renderTable(response.data);
                    $('#tableContainer').show();
                } else {
                    alert('Hata: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                $('#loading').hide();
                alert('Bir hata oluştu: ' + error);
                console.error('Ajax Error:', xhr.responseText);
            }
        });
    }

    function renderTable(data) {
        if (dataTable) {
            dataTable.destroy();
        }

        let tableHTML = '';
        data.forEach(function(item) {
            tableHTML += `
                <tr class="clickable-row" data-persid="${item.id}">
                    <td><strong>${item.name}</strong></td>
                    <td><strong>${item.atanan_servis}</strong></td>
                    <td><strong>${item.tamamlanan_servis}</strong></td>
                    <td><strong>${item.sikayetci_servis}</strong></td>
                    <td><strong>${item.iptal_servis}</strong></td>
                    <td><strong>${item.haber_verecek}</strong></td>
                    <td><strong>${item.fiyat_anlasma}</strong></td>
                    <td data-sort="${item.alinan_ucret}"><strong>${item.alinan_ucret} TL</strong></td>
                    <td data-sort="${item.verilen_teklif}"><strong>${item.verilen_teklif} TL</strong></td>
                </tr>
            `;
        });

        $('#tableBody').html(tableHTML);

        // DataTable başlat
        dataTable = $('#dataTable').DataTable({
            "bLengthChange": false,
            "paging": false,
            "info": false,
            "order": [[7, 'desc']],
            "language": {
                "sEmptyTable": "Herhangi bir servis hareketi bulunamadı.",
                "sInfoEmpty": "-",
                "search": "Ara:",
                "zeroRecords": "Kayıt bulunamadı."
            }
        });
    }

    // Satır tıklama eventi
    $(document).on('click', '.clickable-row', function() {
        const persid = $(this).data('persid');
        const tarihAraligi = $('#tarihAraligi').val();
        const cihazTur = $('#cihazTur').val();
        
        if (!tarihAraligi) return;

        const tarihler = tarihAraligi.split(' - ');
        
        // Aktif satırı işaretle
        $('.clickable-row').removeClass('active');
        $(this).addClass('active');

        // Modal içeriğini temizle ve loading göster
        $('#detayModalBody').html('<div class="text-center"><img src="{{ asset("images/ajax_load.gif") }}" alt="Yükleniyor..."></div>');
        $('#detayModal').modal('show');

        // Detay verilerini getir
        $.ajax({
            url: '{{ url("/technician-statistics-detail") }}',
            method: 'POST',
            data: {
                personelTabloDetayGetir: persid,
                tarih1: tarihler[0],
                tarih2: tarihler[1],
                cihazTur: cihazTur,
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                $('#detayModalBody').html(data);
            },
            error: function() {
                $('#detayModalBody').html('<div class="alert alert-danger">Detay bilgileri yüklenirken hata oluştu.</div>');
            }
        });
    });
});
</script>
