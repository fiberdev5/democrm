@extends('frontend.secure.user_master')
@section('user')

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<div class="page-content servis-istatistik" id="surveyStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        <div class="row pageDetail">
            <div class="col-12">
                <div class="table-modern">
                    <div class="card-header">
                        Anket İstatistikleri
                        <div class="searchWrap float-end">
                            <div class="btn-group mb-2">
                                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Filtrele <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu p-3" style="min-width: 350px;">
                                    <div class="item">
                                        <div class="row mb-3">
                                            <label class="col-sm-4">Cihaz Türü:</label>
                                            <div class="col-sm-8">
                                                <select id="deviceType" class="form-select form-select-sm">
                                                    <option value="">Hepsi</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4">Tarih Aralığı:</label>
                                            <div class="col-sm-8">
                                                <input id="daterange" class="form-control form-control-sm tarih-araligi" />
                                                <div class="tarihAraligi mt-2 mb-2">
                                                    <button id="lastMonth" class="btn btn-sm btn-secondary me-1">Son 1 Ay</button>
                                                    <button id="last15Days" class="btn btn-sm btn-secondary me-1">Son 15 Gün</button>
                                                    <button id="lastWeek" class="btn btn-sm btn-secondary me-1">Son 7 Gün</button>
                                                    <button id="yesterday" class="btn btn-sm btn-secondary me-1">Dün</button>
                                                    <button id="today" class="btn btn-sm btn-secondary">Bugün</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatableSurveyStats" class="table table-hover mb-0">
                                <thead class="title">
                                    <tr>
                                        <th><i class="fas fa-user me-2"></i>Personel</th>
                                        <th style="width: 170px"><i class="fas fa-check-circle me-2"></i>Sonlanan Servisler</th>
                                        <th style="width: 170px"><i class="fas fa-poll me-2"></i>Yapılan Anketler</th>
                                        <th style="width: 170px">İşlem</th>
                                    </tr>
                                </thead>
                                <tbody id="surveyStatsTableBody">
                                    <!-- Data will be loaded here -->
                                </tbody>
                                <tfoot>
                                  <tr>
                                      <td colspan="4" class="text-start">
                                          <div><strong>Toplam Tamamlanan Servis:</strong> <span id="totalCompletedServices">0</span></div>
                                          <div><strong>Toplam Yapılan Anket:</strong> <span id="totalSurveyedServices">0</span></div>
                                      </td>
                                  </tr>
                              </tfoot>
                            </table>
                        </div>

                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    let today = moment();
    let start_date = moment().subtract(1, 'month'); 
    let end_date = moment(); 

    // Cihaz türlerini yükle
    function loadDeviceTypes() {
        $.get("{{ route('survey.statistics', $tenant_id) }}", function(response) {
            $('#deviceType').empty().append('<option value="">Hepsi</option>');
            // Bu kısım veri yükleme sırasında doldurulacak
        });
    }

    // Kısayol butonları
    $('#lastMonth').click(function() {
        updateRange(moment().subtract(1, 'month'), today);
    });
    $('#last15Days').click(function() {
        updateRange(moment().subtract(15, 'days'), today);
    });
    $('#lastWeek').click(function() {
        updateRange(moment().subtract(7, 'days'), today);
    });
    $('#yesterday').click(function() {
        updateRange(moment().subtract(1, 'days'), moment().subtract(1, 'days'));
    });
    $('#today').click(function() {
        updateRange(today, today);
    });

    function updateRange(start, end) {
        $('#daterange').data('daterangepicker').setStartDate(start);
        $('#daterange').data('daterangepicker').setEndDate(end);
        loadSurveyStatistics();
    }

    // Tarih seçici
    $('#daterange').daterangepicker({
        startDate: start_date,
        endDate: end_date,
        locale: {
            format: 'DD-MM-YYYY',
            separator: ' - ',
            applyLabel: 'Uygula',
            cancelLabel: 'İptal',
            weekLabel: 'H',
            daysOfWeek: ['Pz', 'Pzt', 'Sal', 'Çrş', 'Prş', 'Cm', 'Cmt'],
            monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
            firstDay: 1
        }
    }, function(start, end) {
        loadSurveyStatistics();
    });

    // Cihaz türü değişikliği
    $('#deviceType').change(function() {
        loadSurveyStatistics();
    });

    // Ana veri yükleme fonksiyonu
    function loadSurveyStatistics() {
        var fromDate = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var toDate = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
        var deviceTypeId = $('#deviceType').val();

        $.ajax({
            url: "{{ route('survey.statistics.data', $tenant_id) }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                from_date: fromDate,
                to_date: toDate,
                device_type_id: deviceTypeId
            },
            beforeSend: function() {
                $('#surveyStatsTableBody').html('<tr><td colspan="4" class="text-center"><i class="fas fa-spinner fa-spin"></i> Yükleniyor...</td></tr>');
            },
            success: function (response) {
                // Cihaz türlerini doldur (ilk yüklemede)
                if ($('#deviceType option').length <= 1) {
                    response.deviceTypes.forEach(function(deviceType) {
                        $('#deviceType').append('<option value="' + deviceType.id + '">' + deviceType.cihaz + '</option>');
                    });
                }

                // Tablo verisini oluştur
                var tableHtml = '';
                if (Object.keys(response.groupedSurveys).length > 0) {
                    Object.values(response.groupedSurveys).forEach(function(personel) {
                        var surveyCount = personel.servisler.length;
                        var detailUrl = "{{ url($tenant_id . '/servisler') }}" + 
                                       "?anketArama=" + personel.personel_id + 
                                       "&tarih1=" + fromDate + 
                                       "&tarih2=" + toDate;
                        
                        tableHtml += '<tr>';
                        tableHtml += '<td><strong>' + personel.adsoyad + '</strong></td>';
                        tableHtml += '<td><strong>' + response.totalCompletedServices + '</strong></td>';
                        tableHtml += '<td><strong>' + surveyCount + '</strong></td>';
                        tableHtml += '<td><a href="' + detailUrl + '" target="_blank" class="btn btn-action btn-sm" ><i class="fas fa-eye me-1">Anketleri Göster</a></td>';
                        tableHtml += '</tr>';
                    });
                } else {
                    tableHtml = '<tr><td colspan="4" class="text-center">Herhangi bir anket bulunamadı.</td></tr>';
                }

                $('#surveyStatsTableBody').html(tableHtml);

                // Özet bilgileri güncelle
                $('#totalCompletedServices').text(response.totalCompletedServices);
                $('#totalSurveyedServices').text(response.totalSurveyedServices);
            },
            error: function() {
                $('#surveyStatsTableBody').html('<tr><td colspan="4" class="text-center text-danger">Veri yüklenirken hata oluştu.</td></tr>');
            }
        });
    }

    // Dropdown'un kapanmasını engelle
    $('.dropdown-menu').click(function(e) {
        e.stopPropagation();
    });

    // Sayfa yüklendiğinde verileri getir
    loadSurveyStatistics();
});
</script>

@endsection