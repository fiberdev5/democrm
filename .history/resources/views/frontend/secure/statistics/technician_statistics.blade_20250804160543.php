@extends('frontend.secure.user_master')

@section('user')

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="page-content servis-istatistik">
    <div class="container-fluid">
        {{-- İstatistik menüsünü dahil ediyoruz --}}
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
        
        <div class="card mb-3 pageDetail istatistikSonuclarPage personelSonuclari">
            <div class="card-header sayfaBaslik">
                <span class="sayfaBaslik">Personel İstatistikleri</span>
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">FİLTRELE </button>
                    <ul class="dropdown-menu">
                        <form id="technicianStatsFilterForm">
                            <div class="row form-group">
                                <div class="col-lg-4 rw1"><label>Cihaz Türü</label></div>
                                <div class="col-lg-8 rw2">
                                    <select class="form-control cihazTur" name="cihazTur">
                                        <option value="">Hepsi</option>
                                        {{-- Controller'dan gelen $deviceTypes değişkeni kullanılıyor --}}
                                        @foreach ($cihazTurleri as $device)
                                            <option value="{{ $device->id }}">{{ $device->cihaz }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row form-group" style="margin-bottom: 0">
                                <div class="col-lg-4 rw1"><label>Tarih Aralığı</label></div>
                                <div class="col-lg-8 rw2">
                                    <input type="text" name="tarih1" class="form-control datepicker tarih1" readonly="" value="{{ date('d/m/Y') }}" style="background:#fff;margin-bottom: 3px;">
                                    <input type="text" name="tarih2" class="form-control datepicker tarih2" readonly="" value="{{ date('d/m/Y') }}" style="background:#fff;margin-bottom: 2px;">
                                    <div class="tarihAraliklari">
                                        @php
                                            $today = Carbon\Carbon::now();
                                            $yesterday = $today->copy()->subDay();
                                            $last7Days = $today->copy()->subDays(6);
                                            $last15Days = $today->copy()->subDays(14);
                                            $last30Days = $today->copy()->subMonth();
                                        @endphp
                                        <button type="button" class="tarihDegistirBtn" data-tarih1="{{ $last30Days->format('d/m/Y') }}" data-tarih2="{{ $today->format('d/m/Y') }}">Son 1 Ay</button>
                                        <button type="button" class="tarihDegistirBtn" data-tarih1="{{ $last15Days->format('d/m/Y') }}" data-tarih2="{{ $today->format('d/m/Y') }}">Son 15 Gün</button>
                                        <button type="button" class="tarihDegistirBtn" data-tarih1="{{ $last7Days->format('d/m/Y') }}" data-tarih2="{{ $today->format('d/m/Y') }}">Son 7 Gün</button>
                                        <button type="button" class="tarihDegistirBtn" data-tarih1="{{ $yesterday->format('d/m/Y') }}" data-tarih2="{{ $yesterday->format('d/m/Y') }}">Dün</button>
                                        <button type="button" class="tarihDegistirBtn" data-tarih1="{{ $today->format('d/m/Y') }}" data-tarih2="{{ $today->format('d/m/Y') }}">Bugün</button>
                                    </div>
                                </div>
                            </div>
                            <input type="button" class="btn btn-primary btn-sm persSonuclariListele btn-block" value="ARA"/>
                        </form>
                    </ul>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="card-body">
                <img src="{{ asset('images/ajax_load.gif') }}" alt="Loading...">
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Tarih seçiciyi etkinleştirme
        $('.datepicker').datepicker({
            language: 'tr',
            autoclose: true,
            endDate: new Date()
        });
        
        // Tarih aralığı butonlarının işlevini tanımlama
        $("#technicianStatsFilterForm .tarihDegistirBtn").click(function(){
            var tarih1 = $(this).data('tarih1');
            var tarih2 = $(this).data('tarih2');
            $("#technicianStatsFilterForm .tarih1").val(tarih1);
            $("#technicianStatsFilterForm .tarih2").val(tarih2);
        });

        // Dropdown menü kapanmasını engelleme
        $("#technicianStatsFilterForm .dropdown").on('click', function(e) {
            e.stopPropagation();
        });

        // Sayfa yüklendiğinde varsayılan tabloyu yükle
        loadTechnicianStatisticsTable();

        // 'ARA' butonuna tıklandığında tabloyu yeniden yükleme
        $(".persSonuclariListele").click(function(){
            $(this).parents('.dropdown').find('button.dropdown-toggle').dropdown('toggle');
            $(".personelSonuclari .card-body").html('<img src="{{ asset('images/ajax_load.gif') }}" alt="Loading...">');
            loadTechnicianStatisticsTable();
        });

        // AJAX ile tabloyu yükleyen fonksiyon
        function loadTechnicianStatisticsTable() {
            var veriler = {
                _token: "{{ csrf_token() }}",
                tarih1: $("#technicianStatsFilterForm .tarih1").val(),
                tarih2: $("#technicianStatsFilterForm .tarih2").val(),
                cihazTur: $("#technicianStatsFilterForm .cihazTur").val()
            };
            $.ajax({
                url: "{{ route('technician.statistics.table', ['tenant_id' => $tenant_id]) }}",
                method: "POST",
                data: veriler,
                success: function (data) {
                    
                    $(".personelSonuclari .card-body").html(data);
                    // DataTable'ı başlatma
                    $('#dataTable').DataTable({
                        "bLengthChange": false,
                        "paging": false,
                        "info": false,
                        "order": [ 7, 'desc' ],
                        "language": {
                            "sEmptyTable": "Herhangi bir servis hareketi bulunamadı.",
                            "sInfoEmpty": "-",
                        },
                    });
                }
            });
        }
        
        {{-- Yeni eklenecek olan detay tablosu için event listener --}}
        $(document).on('click', '.tdDetayBtn', function() {
            var personelId = $(this).data('persid');
            loadTechnicianStatisticsDetailTable(personelId);
        });

        function loadTechnicianStatisticsDetailTable(personelId) {
            var veriler = {
                _token: "{{ csrf_token() }}",
                tarih1: $("#technicianStatsFilterForm .tarih1").val(),
                tarih2: $("#technicianStatsFilterForm .tarih2").val(),
                cihazTur: $("#technicianStatsFilterForm .cihazTur").val(),
                personelTabloDetayGetir: personelId
            };
            $.ajax({
                url: "{{ route('technician.statistics.detail.table', ['tenant_id' => $tenant_id]) }}",
                method: "POST",
                data: veriler,
                success: function (data) {
                    // Detay tablosunu bir modal veya ayrı bir bölümde göstermek için
                    // Burada bir modal açıp içine veriyi yerleştirebilirsin.
                    // Örnek bir modal açılışı:
                    $('#detailModal .modal-body').html(data);
                    $('#detailModal').modal('show');
                }
            });
        }
    });
</script>

@endsection