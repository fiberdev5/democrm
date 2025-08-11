@extends('frontend.secure.user_master')
@section('user')
<div class="page-content">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])

        {{-- Yetki kontrolü --}}
    
        <div class="card mb-3 pageDetail istatistikSonuclarPage personelSonuclari">
            <div class="card-header sayfaBaslik d-flex justify-content-between align-items-center">
                <span class="sayfaBaslik">Personel İstatistikleri</span>
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">FİLTRELE</button>
                    <ul class="dropdown-menu">
                        <form id="filterForm">
                            <div class="row form-group">
                                <div class="col-lg-4 rw1"><label>Cihaz Türü</label></div>
                                <div class="col-lg-8 rw2">
                                    <select class="form-control cihazTur" name="cihazTur">
                                        <option value="">Hepsi</option>
                                        @foreach ($cihazTurleri as $cihaz)
                                            <option value="{{ $cihaz->id }}">{{ $cihaz->cihaz }}</option>
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
                                        <button type="button" id="asSon30" class="tarihDegistirBtn" data-tarih1="{{ date('d/m/Y', strtotime('-1 month')) }}" data-tarih2="{{ date('d/m/Y') }}">Son 1 Ay</button>
                                        <button type="button" id="asSon15" class="tarihDegistirBtn" data-tarih1="{{ date('d/m/Y', strtotime('-15 days')) }}" data-tarih2="{{ date('d/m/Y') }}">Son 15 Gün</button>
                                        <button type="button" id="asSon7" class="tarihDegistirBtn" data-tarih1="{{ date('d/m/Y', strtotime('-7 days')) }}" data-tarih2="{{ date('d/m/Y') }}">Son 7 Gün</button>
                                        <button type="button" id="asDun" class="tarihDegistirBtn" data-tarih1="{{ date('d/m/Y', strtotime('-1 days')) }}" data-tarih2="{{ date('d/m/Y', strtotime('-1 days')) }}">Dün</button>
                                        <button type="button" id="asBugun" class="tarihDegistirBtn" data-tarih1="{{ date('d/m/Y') }}" data-tarih2="{{ date('d/m/Y') }}">Bugün</button>
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
                <img src="{{ asset('images/ajax_load.gif') }}">
                {{-- AJAX ile yüklenecek tablo buraya gelecek --}}
            </div>
        </div>
        @else
        <div class="alert alert-warning" role="alert">
            Bu sayfayı görüntülemek için yeterli yetkiniz yok.
        </div>
        @endif
    </div>
</div>

@section('scripts')
{{-- Datepicker ve Chart.js için asset yollarını doğru belirttiğinizden emin olun --}}
<script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap-datepicker/locales/bootstrap-datepicker.tr.min.js') }}" charset="UTF-8"></script>
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script> {{-- Datatables JS --}}
<script src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script> {{-- Datatables Bootstrap entegrasyonu --}}


<script type="text/javascript">
    $(document).ready(function(e) {
        $('.datepicker').datepicker({
            language: 'tr',
            autoclose: true,
            endDate: new Date() // endDate: new Date(new Date().setDate(new Date().getDate())) yerine direkt new Date()
        });

        $("form .tarihDegistirBtn").click(function(){
            var tarih1 = $(this).attr('data-tarih1');
            var tarih2 = $(this).attr('data-tarih2');

            // Datepicker'a tarihleri set etmek için d/m/Y formatını kullanın
            // Datepicker'ın setDate metodu genellikle Date objesi veya m/d/Y formatında string bekler.
            // Bu yüzden d/m/Y formatını m/d/Y'ye çevirmek daha doğru olabilir
            var tarih1Parsed = new Date(tarih1.split('/')[2], tarih1.split('/')[1] - 1, tarih1.split('/')[0]);
            var tarih2Parsed = new Date(tarih2.split('/')[2], tarih2.split('/')[1] - 1, tarih2.split('/')[0]);

            $("form .tarih1").datepicker("setDate", tarih1Parsed);
            $("form .tarih2").datepicker("setDate", tarih2Parsed);

            // Input alanlarının değerlerini güncelle
            $("form .tarih1").val(tarih1);
            $("form .tarih2").val(tarih2);
        });

        $("form .dropdown").on('click', function(e) {
             e.stopPropagation(); // Dropdown'ın kapanmasını engelle
        });


        function loadTechnicianData() {
            $(".personelSonuclari .card-body").html('<img src="{{ asset('images/ajax_load.gif') }}">');

            var tarih1 = $("form .tarih1").val();
            var tarih2 = $("form .tarih2").val();
            var cihazTur = $("form .cihazTur").val();

            // Laravel route'unu kullanmak için
            var url = "{{ route('technician.statistics.data', ['tenant_id' => $tenant_id]) }}";

            $.ajax({
                url: url,
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}', // Laravel CSRF token
                    tarih1: tarih1,
                    tarih2: tarih2,
                    cihazTur: cihazTur
                },
                success: function (data) {
                    $(".personelSonuclari .card-body").html(data);
                    // Datatables'ı yeniden başlatmak gerekebilir, technician_statistics_table içinde yapabiliriz
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    $(".personelSonuclari .card-body").html('<div class="alert alert-danger">Veri yüklenirken bir hata oluştu.</div>');
                }
            });
        }

        // Sayfa yüklendiğinde ve filtre butona basıldığında
        loadTechnicianData(); // İlk yükleme
        $(".persSonuclariListele").click(function(){
            $(this).parents('.dropdown').find('button.dropdown-toggle').dropdown('toggle'); // Dropdown'ı kapat
            loadTechnicianData();
        });
    });
</script>
@endsection
@endsection