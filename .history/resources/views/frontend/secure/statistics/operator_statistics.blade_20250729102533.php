@extends('frontend.secure.user_master')
@section('user')
<div id="wrapper">
    @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
    <div id="content-wrapper">
        <div class="container-fluid">
            <div class="girisEkrani istatistiklerGirisEkrani">     
            </div>
          
            <div class="card mb-3 pageDetail istatistikSonuclariPage durumSonuclari">
                <div class="card-header sayfaBaslik">
                    <span class="sayfaBaslik">Operatör İstatistikleri</span>
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">FİLTRELE </button>
                        <ul class="dropdown-menu">
                            <form id="operatorStatsForm"> {{-- Form'a bir id ekledik --}}
                                @csrf {{-- Laravel CSRF token'ı --}}
                                <input type="hidden" class="servisKaynak" value="">
                                <input type="hidden" class="cihazMarka" value="">
                                <input type="hidden" class="cihazTur" value="">

                                <div class="row form-group" style="margin-bottom: 0">
                                    <div class="col-lg-4 rw1"><label>Tarih Aralığı</label></div>
                                    <div class="col-lg-8 rw2">
                                        <input type="text" name="tarih1" class="form-control datepicker tarih1" readonly="" value="{{ old('tarih1', $tarih1) }}" style="background:#fff;margin-bottom: 3px;">
                                        <input type="text" name="tarih2" class="form-control datepicker tarih2" readonly="" value="{{ old('tarih2', $tarih2) }}" style="background:#fff;margin-bottom: 2px;">
                                        <div class="tarihAraliklari">
                                            @php
                                                $asSon7 = \Carbon\Carbon::now()->subDays(7)->format('d/m/Y');
                                                $asSon15 = \Carbon\Carbon::now()->subDays(15)->format('d/m/Y');
                                                $asSon30 = \Carbon\Carbon::now()->subMonth()->format('d/m/Y');
                                                $asDun = \Carbon\Carbon::now()->subDay()->format('d/m/Y');
                                                $asBugun = \Carbon\Carbon::now()->format('d/m/Y');
                                            @endphp

                                            <button type="button" id="asSon30" class="tarihDegistirBtn"
                                                    data-tarih1="{{ $asSon30 }}"
                                                    data-tarih2="{{ $asBugun }}"
                                                    data-tarih3="{{ \Carbon\Carbon::createFromFormat('d/m/Y', $asSon30)->format('Y,m,d') }}"
                                                    data-tarih4="{{ \Carbon\Carbon::createFromFormat('d/m/Y', $asBugun)->format('Y,m,d') }}">Son 1 Ay</button>
                                            <button type="button" id="asSon15" class="tarihDegistirBtn"
                                                    data-tarih1="{{ $asSon15 }}"
                                                    data-tarih2="{{ $asBugun }}"
                                                    data-tarih3="{{ \Carbon\Carbon::createFromFormat('d/m/Y', $asSon15)->format('Y,m,d') }}"
                                                    data-tarih4="{{ \Carbon\Carbon::createFromFormat('d/m/Y', $asBugun)->format('Y,m,d') }}">Son 15 Gün</button>
                                            <button type="button" id="asSon7" class="tarihDegistirBtn"
                                                    data-tarih1="{{ $asSon7 }}"
                                                    data-tarih2="{{ $asBugun }}"
                                                    data-tarih3="{{ \Carbon\Carbon::createFromFormat('d/m/Y', $asSon7)->format('Y,m,d') }}"
                                                    data-tarih4="{{ \Carbon\Carbon::createFromFormat('d/m/Y', $asBugun)->format('Y,m,d') }}">Son 7 Gün</button>
                                            <button type="button" id="asDun" class="tarihDegistirBtn"
                                                    data-tarih1="{{ $asDun }}"
                                                    data-tarih2="{{ $asDun }}"
                                                    data-tarih3="{{ \Carbon\Carbon::createFromFormat('d/m/Y', $asDun)->format('Y,m,d') }}"
                                                    data-tarih4="{{ \Carbon\Carbon::createFromFormat('d/m/Y', $asDun)->format('Y,m,d') }}">Dün</button>
                                            <button type="button" id="asBugun" class="tarihDegistirBtn"
                                                    data-tarih1="{{ $asBugun }}"
                                                    data-tarih2="{{ $asBugun }}"
                                                    data-tarih3="{{ \Carbon\Carbon::createFromFormat('d/m/Y', $asBugun)->format('Y,m,d') }}"
                                                    data-tarih4="{{ \Carbon\Carbon::createFromFormat('d/m/Y', $asBugun)->format('Y,m,d') }}">Bugün</button>

                                        </div>
                                    </div>
                                </div>
                                <input type="button" class="btn btn-primary btn-sm optSonuclariListele btn-block" value="ARA"/>
                            </form>
                        </ul>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="card-body">
                    {{-- Tablo içeriği buraya yüklenecek veya başlangıçta direkt render edilecek --}}
                    <img src="{{ asset('images/ajax_load.gif') }}" alt="Yükleniyor">
                    <div id="operatorStatisticsTable">
                        {{-- AJAX ile yüklenecek veya ilk yüklemede gösterilecek tablo --}}
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('vendor/momentjs/moment-with-locales.js') }}"></script>
{{-- Datepicker dil dosyası --}}
<script type="text/javascript">
    !function(a){a.fn.datepicker.dates.tr={days:["Pazar","Pazartesi","Salı","Çarşamba","Perşembe","Cuma","Cumartesi"],daysShort:["Pz","Pzt","Sal","Çrş","Prş","Cu","Cts"],daysMin:["Pz","Pzt","Sa","Çr","Pr","Cu","Ct"],months:["Ocak","Şubat","Mart","Nisan","Mayıs","Haziran","Temmuz","Ağustos","Eylül","Ekim","Kasım","Aralık"],monthsShort:["Oca","Şub","Mar","Nis","May","Haz","Tem","Ağu","Eyl","Eki","Kas","Ara"],today:"Bugün",clear:"Temizle",weekStart:1,format:"dd/mm/yyyy"}}(jQuery);
</script>

<script type="text/javascript">
    $(document).ready(function(e) {
        $('.datepicker').datepicker({
            language: 'tr',
            autoclose: true,
            endDate: new Date()
        });

        $("#operatorStatsForm .tarihDegistirBtn").click(function(){
            var tarih1 = $(this).attr('data-tarih1');
            var tarih2 = $(this).attr('data-tarih2');
            var tarih3 = $(this).attr('data-tarih3');
            var tarih4 = $(this).attr('data-tarih4');

            $("#operatorStatsForm .tarih1").datepicker("setDate", new Date(tarih3));
            $("#operatorStatsForm .tarih2").datepicker("setDate", new Date(tarih4));

            $("#operatorStatsForm .tarih1").val(tarih1);
            $("#operatorStatsForm .tarih2").val(tarih2);
        });

        $("#operatorStatsForm .dropdown").on('click', function(e) {
            e.stopPropagation();
        });

        function loadOperatorStatistics() {
            // Yükleniyor görselini göster
            $(".durumSonuclari .card-body > img").show();
            $("#operatorStatisticsTable").empty(); // Önceki tablo içeriğini temizle

            var tarih1 = $("#operatorStatsForm .tarih1").val();
            var tarih2 = $("#operatorStatsForm .tarih2").val();
            var veriler = {
                optTabloGetir: tarih1 + "---" + tarih2,
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token'ı doğrudan nesneye ekle
            };

            $.ajax({
                url: "{{ route('operator.statistics.data', ['tenant_id' => request()->route('tenant_id')]) }}",
                method: "POST",
                data: veriler,
                success: function (data) {
                    $("#operatorStatisticsTable").html(data);
                    $(".durumSonuclari .card-body > img").hide(); // Yükleniyor görselini gizle
                    // DataTable'ı başlat (AJAX ile yeni içerik geldiğinde tekrar başlatılabilir)
                    // Önceki DataTable instance'ını yok etmeden yeniden başlatmak sorunlara neden olabilir.
                    // Bu yüzden mevcut instance'ı kontrol edip yok edelim.
                    if ($.fn.DataTable.isDataTable('#dataTable')) {
                        $('#dataTable').DataTable().destroy();
                    }
                    $('#dataTable').DataTable({
                        "bLengthChange": false,
                        "paging":  false,
                        "info": false,
                        "order": [ 1, 'desc' ],
                        "language": {
                            "sEmptyTable": "Herhangi bir servis bulunamadı.",
                            "sInfoEmpty": "-",
                        },
                    });
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Hatası: " + status + " " + error);
                    $("#operatorStatisticsTable").html('<p class="text-danger">Veri yüklenirken bir hata oluştu.</p>');
                    $(".durumSonuclari .card-body > img").hide();
                }
            });
        }

        // Sayfa yüklendiğinde ilk çağrıyı yap
        loadOperatorStatistics();

        // Ara butonuna tıklandığında verileri çek
        $("#operatorStatsForm .optSonuclariListele").click(function(){
            $(this).parents('.dropdown').find('button.dropdown-toggle').dropdown('toggle');
            loadOperatorStatistics();
        });
    });
</script>
@endsection