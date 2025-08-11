@extends('frontend.secure.user_master')
@section('user')

<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{asset('backend/assets/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
<script src="{{asset('backend/assets/libs/jquery/jquery.min.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<style>
.sayfaBaslik {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    padding: 15px 20px;
    border-radius: 8px 8px 0 0;
}

.searchWrap .dropdown-menu {
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border: none;
    padding: 20px;
    min-width: 350px;
}

.searchWrap .item {
    margin-bottom: 15px;
}

.searchWrap .item:last-child {
    margin-bottom: 0;
}

.tarihAraligi .btn {
    font-size: 12px;
    padding: 5px 12px;
    border-radius: 20px;
    margin: 2px;
}

.tarihAraligi .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.table {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.table .title {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
}

.table-hover tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.05);
    transform: scale(1.002);
    transition: all 0.2s ease;
    cursor: pointer;
}

.teknisyen-detay-btn.clicked {
    background-color: rgba(102, 126, 234, 0.1) !important;
    border-left: 4px solid #667eea;
}

.detay-satir {
    background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
    animation: slideIn 0.3s ease;
}

.detay-satir td {
    border-top: 2px solid #667eea;
}

.btn-dark {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    border: none;
    border-radius: 25px;
    padding: 8px 20px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-dark:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(44, 62, 80, 0.3);
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    overflow: hidden;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
    border-width: 0.3em;
}

.tarih-araligi {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
    padding: 8px 12px;
    width: 100%;
}

.tarih-araligi:focus {
    border-color: #667eea;
    box-shadow: 0 0 15px rgba(102, 126, 234, 0.2);
    outline: none;
}

.form-select {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 15px rgba(102, 126, 234, 0.2);
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.currency-text {
    font-weight: 600;
    color: #2c5282;
}

.pageDetail {
    margin-bottom: 20px;
}

/* DataTable özelleştirmeleri */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    margin: 10px 0;
}

.dataTables_wrapper .dataTables_filter input {
    border-radius: 20px;
    padding: 8px 15px;
    border: 2px solid #e9ecef;
    margin-left: 10px;
}

.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #667eea;
    box-shadow: 0 0 10px rgba(102, 126, 234, 0.2);
    outline: none;
}

.pagination-rounded .page-link {
    border-radius: 50px !important;
    margin: 0 2px;
    border: none;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.pagination-rounded .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}
</style>

<div class="page-content" id="passwords">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
        <div class="row pageDetail">
            <div class="col-12">
                <div class="card">
                    <div class="card-header sayfaBaslik">
                        <i class="fas fa-chart-bar me-2"></i>Teknisyen İstatistikleri
                    </div>
                    <div class="card-body">
                        <table id="teknisyenTable" class="table table-bordered table-hover dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            
                            <div class="searchWrap float-end">
                                <div class="btn-group mb-2">
                                    <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filtrele <i class="mdi mdi-chevron-down"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <div class="item">
                                            <div class="row">
                                                <label class="col-sm-4">Cihaz Türü</label>
                                                <div class="col-sm-8">
                                                    <select name="cihazTur" id="cihazTur" class="form-select">
                                                        <option value="">Hepsi</option>
                                                        @foreach($cihazTurleri as $cihaz)
                                                            <option value="{{ $cihaz->id }}">{{ $cihaz->cihaz }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="item">
                                            <div class="row">
                                                <label class="col-sm-4">Tarih Aralığı:</label>
                                                <div class="col-sm-8">
                                                    <input id="daterange" class="tarih-araligi">
                                                    <div class="tarihAraligi mt-2 mb-2">
                                                        <button id="lastYear" class="btn btn-sm btn-secondary">Son 1 Yıl</button>
                                                        <button id="lastMonth" class="btn btn-sm btn-secondary">Son 1 Ay</button>
                                                        <button id="lastWeek" class="btn btn-sm btn-secondary">Son 7 Gün</button>
                                                        <button id="yesterday" class="btn btn-sm btn-secondary">Dün</button>
                                                        <button id="today" class="btn btn-sm btn-secondary">Bugün</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
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

                        <!-- Loading indicator -->
                        <div id="loadingDiv" class="text-center py-4" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Yükleniyor...</span>
                            </div>
                            <div class="mt-2">Veriler yükleniyor...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    
    // Sayfa yüklendiğinde loading'i göster
    $('#loadingDiv').show();
    $('#teknisyenTable').hide();
    
    // DateRange picker initialize
    var start_date = moment().subtract(30, 'days');
    var end_date = moment();

    $('#daterange').daterangepicker({
        startDate: start_date,
        endDate: end_date,
        locale: {
            format: 'DD/MM/YYYY',
            separator: ' - ',
            applyLabel: 'Uygula',
            cancelLabel: 'İptal',
            weekLabel: 'H',
            daysOfWeek: ['Pz', 'Pzt', 'Sal', 'Çrş', 'Prş', 'Cm', 'Cmt'],
            monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
            firstDay: 1
        }
    }, function(start_date, end_date) {
        $('#daterange').html(start_date.format('DD/MM/YYYY') + ' - ' + end_date.format('DD/MM/YYYY'));
        loadTechnicianStatistics();
    });

    // Tarih butonları
    $('#lastYear').on('click', function() {
        var lastYear = moment().subtract(1, 'year');
        var today = moment();
        $('#daterange').data('daterangepicker').setStartDate(lastYear);
        $('#daterange').data('daterangepicker').setEndDate(today);
        loadTechnicianStatistics();
    });

    $('#lastMonth').on('click', function() {
        var lastMonth = moment().subtract(1, 'month');
        var today = moment();
        $('#daterange').data('daterangepicker').setStartDate(lastMonth);
        $('#daterange').data('daterangepicker').setEndDate(today);
        loadTechnicianStatistics();
    });

    $('#lastWeek').on('click', function() {
        var lastWeek = moment().subtract(7, 'days');
        var today = moment();
        $('#daterange').data('daterangepicker').setStartDate(lastWeek);
        $('#daterange').data('daterangepicker').setEndDate(today);
        loadTechnicianStatistics();
    });

    $('#yesterday').on('click', function() {
        var yesterday = moment().subtract(1, 'days');
        $('#daterange').data('daterangepicker').setStartDate(yesterday);
        $('#daterange').data('daterangepicker').setEndDate(yesterday);
        loadTechnicianStatistics();
    });

    $('#today').on('click', function() {
        var today = moment();
        $('#daterange').data('daterangepicker').setStartDate(today);
        $('#daterange').data('daterangepicker').setEndDate(today);
        loadTechnicianStatistics();
    });

    // Cihaz türü değişikliği
    $('#cihazTur').change(function() {
        loadTechnicianStatistics();
    });

    // Dropdown içindeki form gönderimini engelle
    $('.dropdown-menu').click(function(e) {
        e.stopPropagation();
    });

    // Sayfa yüklendiğinde varsayılan verileri getir
    loadTechnicianStatistics();

    // Teknisyen istatistiklerini yükle
    function loadTechnicianStatistics() {
        $('#loadingDiv').show();
        $('#teknisyenTable').hide();

        var dateRange = $('#daterange').data('daterangepicker');
        var startDate = dateRange.startDate.format('DD/MM/YYYY');
        var endDate = dateRange.endDate.format('DD/MM/YYYY');
        var cihazTur = $('#cihazTur').val();

        $.ajax({
            url: '/{{ $tenant_id }}/teknisyen-istatistikleri/data',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                tarihAraligi: startDate + '---' + endDate,
                cihazTur: cihazTur
            },
            success: function(response) {
                console.log('Response:', response);
                if (response.success) {
                    buildStatisticsTable(response.data);
                } else {
                    showError(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('Error:', xhr.responseText);
                showError('Bir hata oluştu: ' + error);
            },
            complete: function() {
                $('#loadingDiv').hide();
                $('#teknisyenTable').show();
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
                        <td class="text-center currency-text"><strong>${formatCurrency(teknisyen.alinan_ucret)}</strong></td>
                        <td class="text-center currency-text"><strong>${formatCurrency(teknisyen.verilen_teklif)}</strong></td>
                    </tr>
                `;
            });
        }

        $('#teknisyenTableBody').html(html);

        // DataTable initialize
        if ($.fn.DataTable.isDataTable('#teknisyenTable')) {
            $('#teknisyenTable').DataTable().destroy();
        }

        var table = $('#teknisyenTable').DataTable({
            "paging": true,
            "info": true,
            "searching": true,
            "ordering": true,
            "order": [[7, 'desc']], // Alınan ücrete göre sırala
            "language": {
                "sEmptyTable": "Herhangi bir teknisyen verisi bulunamadı.",
                "sInfo": "Teknisyen Sayısı: _TOTAL_",
                "sInfoEmpty": "Kayıt yok",
                "sInfoFiltered": "",
                "sSearch": "Teknisyen Ara:",
                "sZeroRecords": "Eşleşen kayıt bulunamadı",
                "oPaginate": {
                    "sNext": '<i class="mdi mdi-chevron-right">',
                    "sPrevious": '<i class="mdi mdi-chevron-left">'
                },
                "sLengthMenu": "_MENU_",
                "sLoadingRecords": "Yükleniyor...",
                "sProcessing": "İşleniyor..."
            },
            "columnDefs": [
                { "orderable": false, "targets": 0 }
            ],
            "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "Tümü"]],
            drawCallback: function() {
                $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
            },
            dom: '<"top"lf>rt<"bottom"ip><"clear">'
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
                        <div class="p-4">
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
            
            // Teknisyen detay bilgilerini yükle
            setTimeout(function() {
                $('.detay-satir').html(`
                    <td colspan="9">
                        <div class="p-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-user-cog me-2"></i>Teknisyen Detay Bilgileri
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Toplam Atanan Servis:</strong> <span class="text-primary">--</span></p>
                                            <p><strong>Başarı Oranı:</strong> <span class="text-success">--</span></p>
                                            <p><strong>Ortalama Tamamlama Süresi:</strong> <span class="text-info">--</span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Bu Ay Tamamlanan:</strong> <span class="text-primary">--</span></p>
                                            <p><strong>Müşteri Memnuniyet Oranı:</strong> <span class="text-success">--</span></p>
                                            <p><strong>Ortalama Kazanç:</strong> <span class="text-warning">--</span></p>
                                        </div>
                                    </div>
                                    <p class="text-muted mt-3">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <small>Detay sayfası geliştirme aşamasındadır.</small>
                                    </p>
                                </div>
                            </div>
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