@extends('frontend.secure.user_master')
@section('user')

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<div class="page-content servis-istatistik" id="technicianStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
        <div class="row pageDetail">
            <div class="col-12">
                <div class="table-modern">
                    <div class="card-header">
                        <span class="sayfaBaslik">Teknisyen İstatistikleri</span>
                        <div class="searchWrap float-end">
                            <div class="btn-group mb-2">
                                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Filtrele <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu p-3" style="min-width: 350px;">
                                    <form id="filterForm">
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Cihaz Türü:</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" name="cihazTur" id="cihazTur">
                                                    <option value="">Hepsi</option>
                                                    @foreach($cihazTurleri as $cihaz)
                                                        <option value="{{ $cihaz->id }}">{{ $cihaz->cihaz }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Tarih Aralığı:</label>
                                            <div class="col-sm-8">
                                                <input id="daterange" class="form-control tarih-araligi mb-2" />
                                                <div class="tarihAraligi">
                                                    <button type="button" class="btn btn-sm btn-secondary tarihDegistirBtn" data-days="30">Son 1 Ay</button>
                                                    <button type="button" class="btn btn-sm btn-secondary tarihDegistirBtn" data-days="15">Son 15 Gün</button>
                                                    <button type="button" class="btn btn-sm btn-secondary tarihDegistirBtn" data-days="7">Son 7 Gün</button>
                                                    <button type="button" class="btn btn-sm btn-secondary tarihDegistirBtn" data-days="1">Dün</button>
                                                    <button type="button" class="btn btn-sm btn-secondary tarihDegistirBtn" data-days="0">Bugün</button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="button" class="btn btn-primary btn-sm w-100" id="searchBtn">
                                            <i class="fas fa-search me-1"></i> Ara
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="card-body" id="statisticsContent">
                        <div class="text-center">
                            <img src="{{ asset('images/ajax_load.gif') }}" alt="Yükleniyor...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.tdDetayBtn {
    cursor: pointer;
}
.tdDetayBtn:hover {
    background-color: #f8f9fa;
}
.clicked {
    background-color: #e3f2fd !important;
}
.altSatir td {
    background-color: #f8f9fa;
}
.detayGrafikler {
    padding: 20px;
}
.detayGrafikler .col-md-4 {
    margin-bottom: 20px;
}
.detayGrafikler span {
    display: block;
    font-weight: bold;
    margin-bottom: 10px;
    text-align: center;
}
.detayAsamalar {
    padding: 20px;
}
.detayAsamalar .cols {
    display: inline-block;
    width: 24%;
    margin: 0.5%;
    border: 1px solid #ddd;
    padding: 15px;
    text-align: center;
    border-radius: 5px;
    margin-bottom: 10px;
}
.detayAsamalar .cols .capt p {
    margin: 0;
    font-size: 12px;
    color: #666;
}
.detayAsamalar .cols .capt h2 {
    margin: 5px 0 0 0;
    font-size: 20px;
    font-weight: bold;
    color: #333;
}
@media (max-width: 768px) {
    .detayAsamalar .cols {
        width: 48%;
    }
}
@media (max-width: 480px) {
    .detayAsamalar .cols {
        width: 100%;
    }
}
</style>

<script>
$(document).ready(function() {
    // Tarih aralığı başlangıç değerleri
    var start_date = moment().format('DD/MM/YYYY');       
    var end_date = moment().format('DD/MM/YYYY');

    // Date Range Picker başlat
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
        }, function(start, end, label) {
            // Datepicker tam olarak yüklendikten sonra ilk kez çağrılır
            loadStatistics(); // <-- ilk yükleme burada yapılacak
        }
});


    // Tarih değiştirme butonları
    $('.tarihDegistirBtn').click(function() {
        var days = $(this).data('days');
        var startDate, endDate;
        
        if (days == 0) { // Bugün
            startDate = moment();
            endDate = moment();
        } else if (days == 1) { // Dün
            startDate = moment().subtract(1, 'days');
            endDate = moment().subtract(1, 'days');
        } else { // Son X gün
            startDate = moment().subtract(days, 'days');
            endDate = moment();
        }
        
        $('#daterange').data('daterangepicker').setStartDate(startDate);
        $('#daterange').data('daterangepicker').setEndDate(endDate);
    });

    // İlk yükleme
    

    // Arama butonu
    $('#searchBtn').click(function() {
        loadStatistics();
        // Dropdown'u kapat
        $(this).closest('.dropdown-menu').prev('.dropdown-toggle').dropdown('toggle');
    });

    // Dropdown dışına tıklanınca kapanmasını engelle
    $('.dropdown-menu').click(function(e) {
        e.stopPropagation();
    });

    // İstatistikleri yükle
    function loadStatistics() {
        $('#statisticsContent').html('<div class="text-center"><img src="{{ asset('images/ajax_load.gif') }}" alt="Yükleniyor..."></div>');
        
        var tarih1 = $('#daterange').data('daterangepicker').startDate.format('DD/MM/YYYY');
        var tarih2 = $('#daterange').data('daterangepicker').endDate.format('DD/MM/YYYY');
        var cihazTur = $('#cihazTur').val();
        
        var veriler = {
            'personelTabloGetir': tarih1 + '---' + tarih2,
            'cihazTur': cihazTur,
            '_token': '{{ csrf_token() }}'
        };

        $.ajax({
            url: "{{ route('technician.statistics.data', $tenant_id) }}",
            method: "POST",
            data: veriler,
            success: function(response) {
                $('#statisticsContent').html(response.html);
                initializeDataTable();
                initializeDetailButtons();
            },
            error: function(xhr, status, error) {
                $('#statisticsContent').html('<div class="alert alert-danger">Bir hata oluştu: ' + error + '</div>');
            }
        });
    }

    // DataTable başlat
    function initializeDataTable() {
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().destroy();
        }
        
        $('#dataTable').DataTable({
            "bLengthChange": false,
            "paging": false,
            "info": false,
            "order": [[7, 'desc']],
            "language": {
                "sEmptyTable": "Herhangi bir servis hareketi bulunamadı.",
                "sInfoEmpty": "-",
                "search": "Ara:",
                "zeroRecords": "Eşleşen kayıt bulunamadı"
            },
            "dom": 'rt<"bottom"f><"clear">'
        });
    }

    // Detay butonlarını başlat
    function initializeDetailButtons() {
        $(document).off('click', 'tr.tdDetayBtn');
        $(document).on('click', 'tr.tdDetayBtn', function(e) {
            $("table tr.altSatir").remove();
            
            var persid = $(this).attr("data-persid");
            var current = $(this).index();
            
            if ($(this).hasClass('clicked')) {
                $(this).removeClass('clicked');
            } else {
                $('tr.clicked').removeClass('clicked');
                $(this).addClass('clicked');
                var next = current + 1;
                $("table tr:eq(" + next + ")").after("<tr class='altSatir'><td colspan='9'><div class='text-center'><img src='{{ asset('images/ajax_load.gif') }}' alt='Yükleniyor...'></div></td></tr>");

                var tarih1 = $('#daterange').data('daterangepicker').startDate.format('DD/MM/YYYY');
                var tarih2 = $('#daterange').data('daterangepicker').endDate.format('DD/MM/YYYY');
                var cihazTur = $('#cihazTur').val();
                
                var veriler = {
                    'personelTabloDetayGetir': persid,
                    'tarih1': tarih1,
                    'tarih2': tarih2,
                    'cihazTur': cihazTur,
                    '_token': '{{ csrf_token() }}'
                };

                $.ajax({
                    url: "{{ route('technician.statistics.detail', $tenant_id) }}",
                    method: "POST",
                    data: veriler,
                    success: function(response) {
                        $('table tr.altSatir td').html(response.html);
                    },
                    error: function(xhr, status, error) {
                        $('table tr.altSatir td').html('<div class="alert alert-danger">Detay verileri yüklenirken hata oluştu.</div>');
                    }
                });
            }
        });
    }
});
</script>

@endsection