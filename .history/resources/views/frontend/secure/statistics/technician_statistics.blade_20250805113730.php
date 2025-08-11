@extends('frontend.secure.user_master')
@section('user')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<div class="page-content servis-istatistik" id="technicianStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])

        @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Manager'))
        <div class="row pageDetail">
            <div class="col-12">
                <div class="table-modern">
                    <div class="card-header">
                        <span class="sayfaBaslik">Personel İstatistikleri</span>
                        <div class="searchWrap float-end">
                            <div class="btn-group mb-2">
                                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown">
                                    Filtrele <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <form id="filterForm">
                                        <div class="item">
                                            <div class="row mb-3">
                                                <label class="col-sm-4">Cihaz Türü:</label>
                                                <div class="col-sm-8">
                                                    <select class="form-select form-select-sm cihazTur" name="cihazTur">
                                                        <option value="">Hepsi</option>
                                                        @foreach($cihazTurleri as $deviceType)
                                                            <option value="{{ $deviceType->id }}">{{ $deviceType->cihaz }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label class="col-sm-4">Tarih Aralığı:</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="tarih1" class="form-control form-control-sm datepicker tarih1" readonly value="{{ date('d/m/Y') }}">
                                                    <input type="text" name="tarih2" class="form-control form-control-sm datepicker tarih2" readonly value="{{ date('d/m/Y') }}">
                                                    <div class="tarihAraliklari mt-2">
                                                        <button type="button" class="btn btn-sm btn-secondary tarihDegistirBtn" data-tarih1="{{ date('d/m/Y', strtotime('-1 month')) }}" data-tarih2="{{ date('d/m/Y') }}">Son 1 Ay</button>
                                                        <button type="button" class="btn btn-sm btn-secondary tarihDegistirBtn" data-tarih1="{{ date('d/m/Y', strtotime('-15 days')) }}" data-tarih2="{{ date('d/m/Y') }}">Son 15 Gün</button>
                                                        <button type="button" class="btn btn-sm btn-secondary tarihDegistirBtn" data-tarih1="{{ date('d/m/Y', strtotime('-7 days')) }}" data-tarih2="{{ date('d/m/Y') }}">Son 7 Gün</button>
                                                        <button type="button" class="btn btn-sm btn-secondary tarihDegistirBtn" data-tarih1="{{ date('d/m/Y', strtotime('-1 day')) }}" data-tarih2="{{ date('d/m/Y', strtotime('-1 day')) }}">Dün</button>
                                                        <button type="button" class="btn btn-sm btn-secondary tarihDegistirBtn" data-tarih1="{{ date('d/m/Y') }}" data-tarih2="{{ date('d/m/Y') }}">Bugün</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm persSonuclariListele w-100">ARA</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="card-body" id="statisticsTableContainer">
                        @if(isset($results))
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Teknisyen</th>
                                            <th>Atanan</th>
                                            <th>Tamamlanan</th>
                                            <th>Şikayet</th>
                                            <th>İptal</th>
                                            <th>Haber Ver</th>
                                            <th>Fiyat Sorunu</th>
                                            <th>Ücret</th>
                                            <th>Teklif</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($results as $item)
                                        <tr class="tdDetayBtn" data-persid="{{ $item['id'] }}" style="cursor:pointer;">
                                            <td>{{ $item['name'] }}</td>
                                            <td>{{ $item['assigned_services'] }}</td>
                                            <td>{{ $item['completed_services'] }}</td>
                                            <td>{{ $item['complaint_services'] }}</td>
                                            <td>{{ $item['cancelled_services'] }}</td>
                                            <td>{{ $item['callback_services'] }}</td>
                                            <td>{{ $item['price_disagreement'] }}</td>
                                            <td>{{ number_format($item['collected_amount'], 2) }} TL</td>
                                            <td>{{ number_format($item['given_offers'], 2) }} TL</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted">Veri bulunamadı.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@if(isset($detailData))
<div class="p-3 bg-light">
    <div class="row">
        <div class="col-md-12">
            <h6 class="mb-3"><i class="fas fa-user-cog me-2"></i>Teknisyen Detay Bilgileri</h6>
            <div class="row g-3">
                @foreach ([
                    ['icon' => 'tasks', 'label' => 'Toplam Görev', 'value' => $detailData['total_tasks'] ?? 0, 'bg' => 'primary'],
                    ['icon' => 'check-circle', 'label' => 'Tamamlanan', 'value' => $detailData['completed_tasks'] ?? 0, 'bg' => 'success'],
                    ['icon' => 'exclamation-triangle', 'label' => 'Şikayetli', 'value' => $detailData['complaint_tasks'] ?? 0, 'bg' => 'warning'],
                    ['icon' => 'money-bill', 'label' => 'Toplam Gelir', 'value' => number_format($detailData['total_income'] ?? 0, 2).' TL', 'bg' => 'info'],
                ] as $card)
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-{{ $card['icon'] }} text-{{ $card['bg'] }} mb-2" style="font-size: 24px;"></i>
                            <h6 class="mb-1">{{ $card['label'] }}</h6>
                            <span class="badge bg-{{ $card['bg'] }}">{{ $card['value'] }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-4">
                <h6 class="mb-3"><i class="fas fa-chart-line me-2"></i>Performans Özeti</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <tbody>
                            <tr><td><i class="fas fa-clipboard-list text-primary me-2"></i>Atanan Servisler</td><td class="text-end"><strong>{{ $detailData['assigned_services'] ?? 0 }}</strong></td></tr>
                            <tr><td><i class="fas fa-check-circle text-success me-2"></i>Tamamlanan Servisler</td><td class="text-end"><strong>{{ $detailData['completed_services'] ?? 0 }}</strong></td></tr>
                            <tr><td><i class="fas fa-frown text-danger me-2"></i>Şikayetçi Servisler</td><td class="text-end"><strong>{{ $detailData['complaint_services'] ?? 0 }}</strong></td></tr>
                            <tr><td><i class="fas fa-times-circle text-secondary me-2"></i>İptal Edilen Servisler</td><td class="text-end"><strong>{{ $detailData['cancelled_services'] ?? 0 }}</strong></td></tr>
                            <tr><td><i class="fas fa-phone text-info me-2"></i>Haber Verecek Servisler</td><td class="text-end"><strong>{{ $detailData['callback_services'] ?? 0 }}</strong></td></tr>
                            <tr><td><i class="fas fa-hand-holding-usd text-warning me-2"></i>Fiyat Anlaşmazlığı</td><td class="text-end"><strong>{{ $detailData['price_disagreement'] ?? 0 }}</strong></td></tr>
                            <tr class="table-primary"><td><i class="fas fa-money-bill-wave text-success me-2"></i>Toplam Alınan Ücret</td><td class="text-end"><strong>{{ number_format($detailData['collected_amount'] ?? 0, 2) }} TL</strong></td></tr>
                            <tr class="table-info"><td><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Toplam Verilen Teklif</td><td class="text-end"><strong>{{ number_format($detailData['given_offers'] ?? 0, 2) }} TL</strong></td></tr>
                        </tbody>
                    </table>
                </div>

                @if(isset($detailData['recent_services']) && count($detailData['recent_services']) > 0)
                <div class="mt-4">
                    <h6 class="mb-3"><i class="fas fa-clock me-2"></i>Son Servisler</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr><th>Servis No</th><th>Müşteri</th><th>Durum</th><th>Tarih</th></tr>
                            </thead>
                            <tbody>
                                @foreach($detailData['recent_services'] as $service)
                                <tr>
                                    <td>#{{ $service['id'] }}</td>
                                    <td>{{ $service['customer_name'] }}</td>
                                    <td><span class="badge bg-{{ $service['status_color'] }}">{{ $service['status_text'] }}</span></td>
                                    <td>{{ $service['date'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<script>
$(document).ready(function () {
    $('.datepicker').datepicker({ language: 'tr', autoclose: true, endDate: new Date(), format: 'dd/mm/yyyy' });

    $(".tarihDegistirBtn").click(function () {
        $(".tarih1").val($(this).data('tarih1'));
        $(".tarih2").val($(this).data('tarih2'));
    });

    $(".dropdown-menu").click(function (e) { e.stopPropagation(); });

    $(".persSonuclariListele").click(function () {
        loadTechnicianStatistics();
        $(this).closest('.dropdown').find('button.dropdown-toggle').dropdown('toggle');
    });

    function loadTechnicianStatistics() {
        $("#statisticsTableContainer").html('<div class="text-center p-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Yükleniyor...</span></div></div>');

        $.ajax({
            url: "{{ route('technician.statistics.data', $tenant_id) }}",
            method: "POST",
            data: {
                tarih1: $(".tarih1").val(),
                tarih2: $(".tarih2").val(),
                cihazTur: $(".cihazTur").val(),
                _token: "{{ csrf_token() }}"
            },
            success: function (data) {
                $("#statisticsTableContainer").html(data);
            },
            error: function () {
                $("#statisticsTableContainer").html('<div class="alert alert-danger">Veriler yüklenirken bir hata oluştu.</div>');
            }
        });
    }
});
</script>
@endsection
