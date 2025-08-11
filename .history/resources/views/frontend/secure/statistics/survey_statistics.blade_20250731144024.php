@extends('frontend.secure.user_master')
@section('user')

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="page-content servis-istatistik" id="surveyStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
        <!-- Anket Sonuçları Kartı -->
        <div class="row pageDetail mb-4">
            <div class="col-12">
                <div class="table-modern">
                    <div class="card-header">
                        <i class="fas fa-chart-pie me-2"></i>Anket Sonuçları
                        <div class="searchWrap float-end">
                            <div class="btn-group mb-2">
                                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Filtrele <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu p-3" style="min-width: 350px;">
                                    <div class="item">
                                        <div class="row mb-3">
                                            <label class="col-sm-4">Bayi:</label>
                                            <div class="col-sm-8">
                                                <select id="bayiSelect" class="form-select form-select-sm">
                                                    <option value="">Hepsi</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4">Cihaz Türü:</label>
                                            <div class="col-sm-8">
                                                <select id="deviceTypeResults" class="form-select form-select-sm">
                                                    <option value="">Hepsi</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4">Tarih Aralığı:</label>
                                            <div class="col-sm-8">
                                                <input id="daterangeResults" class="form-control form-control-sm tarih-araligi" />
                                                <div class="tarihAraligi mt-2 mb-2">
                                                    <button id="lastMonthResults" class="btn btn-sm btn-secondary me-1">Son 1 Ay</button>
                                                    <button id="last15DaysResults" class="btn btn-sm btn-secondary me-1">Son 15 Gün</button>
                                                    <button id="lastWeekResults" class="btn btn-sm btn-secondary me-1">Son 7 Gün</button>
                                                    <button id="yesterdayResults" class="btn btn-sm btn-secondary me-1">Dün</button>
                                                    <button id="todayResults" class="btn btn-sm btn-secondary">Bugün</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Özet Bilgiler -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h4 id="totalSurveysCount">0</h4>
                                        <p class="mb-0">Toplam Anket</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h4 id="positiveRate">0%</h4>
                                        <p class="mb-0">Ortalama Memnuniyet</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h4 id="neutralRate">0%</h4>
                                        <p class="mb-0">Kararsız Cevaplar</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h4 id="negativeRate">0%</h4>
                                        <p class="mb-0">Olumsuz Cevaplar</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Grafik ve Tablo -->
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Soru Bazında Sonuçlar</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="surveyChart" width="400" height="300"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-table me-2"></i>Detaylı Sonuçlar</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th style="width: 40%">Sorular</th>
                                                        <th class="text-center">Evet</th>
                                                        <th class="text-center">Hayır</th>
                                                        <th class="text-center">Belli Değil</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="surveyResultsTable">
                                                    <tr>
                                                        <td><strong>Teknisyen dediği saatte geldi mi?</strong></td>
                                                        <td class="text-center"><span class="badge bg-success" id="soru1-evet">0</span></td>
                                                        <td class="text-center"><span class="badge bg-danger" id="soru1-hayir">0</span></td>
                                                        <td class="text-center"><span class="badge bg-warning" id="soru1-bd">0</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Teknisyen davranışlarından memnun musunuz?</strong></td>
                                                        <td class="text-center"><span class="badge bg-success" id="soru2-evet">0</span></td>
                                                        <td class="text-center"><span class="badge bg-danger" id="soru2-hayir">0</span></td>
                                                        <td class="text-center"><span class="badge bg-warning" id="soru2-bd">0</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Teknisyen cihazınızla yeterince ilgilendi mi?</strong></td>
                                                        <td class="text-center"><span class="badge bg-success" id="soru3-evet">0</span></td>
                                                        <td class="text-center"><span class="badge bg-danger" id="soru3-hayir">0</span></td>
                                                        <td class="text-center"><span class="badge bg-warning" id="soru3-bd">0</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Genel servis hizmetinden memnun musunuz?</strong></td>
                                                        <td class="text-center"><span class="badge bg-success" id="soru5-evet">0</span></td>
                                                        <td class="text-center"><span class="badge bg-danger" id="soru5-hayir">0</span></td>
                                                        <td class="text-center"><span class="badge bg-warning" id="soru5-bd">0</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mevcut Personel İstatistikleri -->
        <div class="row pageDetail">
            <div class="col-12">
                <div class="table-modern">
                    <div class="card-header">
                        <i class="fas fa-users me-2"></i>Personel Bazında Anket İstatistikleri
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
let surveyChart;

$(document).ready(function () {
    let today = moment();
    let start_date = moment().subtract(1, 'month'); 
    let end_date = moment(); 

    // Anket sonuçları için tarih seçici
    $('#daterangeResults').daterangepicker({
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
        loadSurveyResults();
    });

    // Personel istatistikleri için tarih seçici
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

    // Anket sonuçları kısayol butonları
    $('#lastMonthResults').click(function() {
        updateResultsRange(moment().subtract(1, 'month'), today);
    });
    $('#last15DaysResults').click(function() {
        updateResultsRange(moment().subtract(15, 'days'), today);
    });
    $('#lastWeekResults').click(function() {
        updateResultsRange(moment().subtract(7, 'days'), today);
    });
    $('#yesterdayResults').click(function() {
        updateResultsRange(moment().subtract(1, 'days'), moment().subtract(1, 'days'));
    });
    $('#todayResults').click(function() {
        updateResultsRange(today, today);
    });

    // Personel istatistikleri kısayol butonları
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

    function updateResultsRange(start, end) {
        $('#daterangeResults').data('daterangepicker').setStartDate(start);
        $('#daterangeResults').data('daterangepicker').setEndDate(end);
        loadSurveyResults();
    }

    function updateRange(start, end) {
        $('#daterange').data('daterangepicker').setStartDate(start);
        $('#daterange').data('daterangepicker').setEndDate(end);
        loadSurveyStatistics();
    }

    // Filtre değişiklikleri
    $('#bayiSelect, #deviceTypeResults').change(function() {
        loadSurveyResults();
    });

    $('#deviceType').change(function() {
        loadSurveyStatistics();
    });

    // Anket sonuçlarını yükle
    function loadSurveyResults() {
        var fromDate = $('#daterangeResults').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var toDate = $('#daterangeResults').data('daterangepicker').endDate.format('YYYY-MM-DD');
        var deviceTypeId = $('#deviceTypeResults').val();
        var bayiId = $('#bayiSelect').val();

        $.ajax({
            url: "{{ route('survey.results.data', $tenant_id) }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                from_date: fromDate,
                to_date: toDate,
                device_type_id: deviceTypeId,
                bayi_id: bayiId
            },
            success: function (response) {
                // Bayiler dropdown'ını doldur
                if ($('#bayiSelect option').length <= 1) {
                    response.bayiler.forEach(function(bayi) {
                        $('#bayiSelect').append('<option value="' + bayi.user_id + '">' + bayi.name + '</option>');
                    });
                }

                // Cihaz türleri dropdown'ını doldur
                if ($('#deviceTypeResults option').length <= 1) {
                    response.deviceTypes.forEach(function(deviceType) {
                        $('#deviceTypeResults').append('<option value="' + deviceType.id + '">' + deviceType.cihaz + '</option>');
                    });
                }

                // Özet bilgileri güncelle
                updateSummaryCards(response);

                // Tabloyu güncelle
                updateResultsTable(response.questionStats);

                // Grafiği güncelle
                updateChart(response.questionStats, response.questionPercentages);
            },
            error: function() {
                console.error('Anket sonuçları yüklenirken hata oluştu.');
            }
        });
    }

    // Özet kartları güncelle
    function updateSummaryCards(response) {
        $('#totalSurveysCount').text(response.totalSurveys);
        
        // Ortalama memnuniyet hesapla (tüm soruların evet cevapları)
        var totalAnswers = 0;
        var totalPositive = 0;
        var totalNeutral = 0;
        var totalNegative = 0;

        Object.values(response.questionStats).forEach(function(stat) {
            totalAnswers += stat.evet + stat.hayir + stat.belli_degil;
            totalPositive += stat.evet;
            totalNeutral += stat.belli_degil;
            totalNegative += stat.hayir;
        });

        var positiveRate = totalAnswers > 0 ? Math.round((totalPositive / totalAnswers) * 100) : 0;
        var neutralRate = totalAnswers > 0 ? Math.round((totalNeutral / totalAnswers) * 100) : 0;
        var negativeRate = totalAnswers > 0 ? Math.round((totalNegative / totalAnswers) * 100) : 0;

        $('#positiveRate').text(positiveRate + '%');
        $('#neutralRate').text(neutralRate + '%');
        $('#negativeRate').text(negativeRate + '%');
    }

    // Sonuçlar tablosunu güncelle
    function updateResultsTable(questionStats) {
        $('#soru1-evet').text(questionStats.soru1.evet);
        $('#soru1-hayir').text(questionStats.soru1.hayir);
        $('#soru1-bd').text(questionStats.soru1.belli_degil);

        $('#soru2-evet').text(questionStats.soru2.evet);
        $('#soru2-hayir').text(questionStats.soru2.hayir);
        $('#soru2-bd').text(questionStats.soru2.belli_degil);

        $('#soru3-evet').text(questionStats.soru3.evet);
        $('#soru3-hayir').text(questionStats.soru3.hayir);
        $('#soru3-bd').text(questionStats.soru3.belli_degil);

        $('#soru5-evet').text(questionStats.soru5.evet);
        $('#soru5-hayir').text(questionStats.soru5.hayir);
        $('#soru5-bd').text(questionStats.soru5.belli_degil);
    }

    // Grafiği güncelle
    function updateChart(questionStats, questionPercentages) {
        const ctx = document.getElementById('surveyChart').getContext('2d');
        
        if (surveyChart) {
            surveyChart.destroy();
        }

        const questions = ['Zamanında Geldi', 'Davranış Memnuniyeti', 'Cihaz İlgisi', 'Genel Memnuniyet'];
        const evetData = [questionStats.soru1.evet, questionStats.soru2.evet, questionStats.soru3.evet, questionStats.soru5.evet];
        const hayirData = [questionStats.soru1.hayir, questionStats.soru2.hayir, questionStats.soru3.hayir, questionStats.soru5.hayir];
        const belliDegilData = [questionStats.soru1.belli_degil, questionStats.soru2.belli_degil, questionStats.soru3.belli_degil, questionStats.soru5.belli_degil];

        surveyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: questions,
                datasets: [{
                    label: 'Evet',
                    data: evetData,
                    backgroundColor: '#28a745',
                    borderColor: '#28a745',
                    borderWidth: 1
                }, {
                    label: 'Hayır',
                    data: hayirData,
                    backgroundColor: '#dc3545',
                    borderColor: '#dc3545',
                    borderWidth: 1
                }, {
                    label: 'Belli Değil',
                    data: belliDegilData,
                    backgroundColor: '#ffc107',
                    borderColor: '#ffc107',
                    borderWidth: 1
                }]
            },