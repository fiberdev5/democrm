@extends('frontend.secure.user_master')
@section('user')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

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
                                <button class="btn btn-dark btn-sm dropdown-toggle filtrele" type="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                                                        @foreach($deviceTypes as $deviceType)
                                                            <option value="{{ $deviceType->id }}">{{ $deviceType->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <label class="col-sm-4">Tarih Aralığı:</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="tarih1" class="form-control form-control-sm datepicker tarih1" readonly value="{{ date('d/m/Y') }}" style="background:#fff;margin-bottom: 3px;">
                                                    <input type="text" name="tarih2" class="form-control form-control-sm datepicker tarih2" readonly value="{{ date('d/m/Y') }}" style="background:#fff;margin-bottom: 2px;">
                                                    
                                                    <div class="tarihAraliklari mt-2">
                                                        <button type="button" class="btn btn-sm btn-secondary tarihDegistirBtn" 
                                                                data-tarih1="{{ date('d/m/Y', strtotime('-1 month')) }}" 
                                                                data-tarih2="{{ date('d/m/Y') }}">Son 1 Ay</button>
                                                        <button type="button" class="btn btn-sm btn-secondary tarihDegistirBtn" 
                                                                data-tarih1="{{ date('d/m/Y', strtotime('-15 days')) }}" 
                                                                data-tarih2="{{ date('d/m/Y') }}">Son 15 Gün</button>
                                                        <button type="button" class="btn btn-sm btn-secondary tarihDegistirBtn" 
                                                                data-tarih1="{{ date('d/m/Y', strtotime('-7 days')) }}" 
                                                                data-tarih2="{{ date('d/m/Y') }}">Son 7 Gün</button>
                                                        <button type="button" class="btn btn-sm btn-secondary tarihDegistirBtn" 
                                                                data-tarih1="{{ date('d/m/Y', strtotime('-1 day')) }}" 
                                                                data-tarih2="{{ date('d/m/Y', strtotime('-1 day')) }}">Dün</button>
                                                        <button type="button" class="btn btn-sm btn-secondary tarihDegistirBtn" 
                                                                data-tarih1="{{ date('d/m/Y') }}" 
                                                                data-tarih2="{{ date('d/m/Y') }}">Bugün</button>
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
                        <div class="text-center p-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Yükleniyor...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>



<script type="text/javascript">
$(document).ready(function() {
    // Datepicker başlat
    $('.datepicker').datepicker({
        language: 'tr',
        autoclose: true,
        endDate: new Date(new Date().setDate(new Date().getDate())),
        format: 'dd/mm/yyyy'
    });

    // Tarih değiştirme butonları
    $(".tarihDegistirBtn").click(function(){
        var tarih1 = $(this).attr('data-tarih1');
        var tarih2 = $(this).attr('data-tarih2');

        $(".tarih1").val(tarih1);
        $(".tarih2").val(tarih2);
    });

    // Dropdown'ın kapanmasını önle
    $(".dropdown-menu").click(function(e) {
        e.stopPropagation();
    });

    // Sayfa yüklendiğinde varsayılan verileri getir
    loadTechnicianStatistics();

    // Arama butonu
    $(".persSonuclariListele").click(function(){
        $(this).closest('.dropdown').find('button.dropdown-toggle').dropdown('toggle');
        loadTechnicianStatistics();
    });

    function loadTechnicianStatistics() {
        $("#statisticsTableContainer").html(`
            <div class="text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Yükleniyor...</span>
                </div>
            </div>
        `);

        var tarih1 = $(".tarih1").val();
        var tarih2 = $(".tarih2").val();
        var cihazTur = $(".cihazTur").val();
        var veriler = 'personelTabloGetir=' + tarih1 + "---" + tarih2 + "&cihazTur=" + cihazTur;

        $.ajax({
            url: "{{ route('technician.statistics.data', $tenant_id) }}",
            method: "POST",
            data: veriler,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                $("#statisticsTableContainer").html(data);
            },
            error: function(xhr, status, error) {
                console.error('Ajax error:', error);
                $("#statisticsTableContainer").html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Veriler yüklenirken bir hata oluştu. Lütfen tekrar deneyin.
                    </div>
                `);
            }
        });
    }

    // Detay görüntüleme için event delegation kullan
    $(document).on('click', 'tr.tdDetayBtn', function(e){
        $("table tr.altSatir").remove();
        $("table tr.clicked").removeClass('clicked');
        
        var persid = $(this).attr("data-persid");
        var current = $(this).index();
        
        if($(this).hasClass('clicked')){
            $(this).removeClass('clicked');
        } else {
            $(this).addClass('clicked');
            var next = current + 1;
            $("table tr:eq(" + next + ")").after(`
                <tr class="altSatir">
                    <td colspan='9' class="p-0">
                        <div class="text-center p-3">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Yükleniyor...</span>
                            </div>
                        </div>
                    </td>
                </tr>
            `);

            var tarih1 = $(".tarih1").val();
            var tarih1 = $(".tarih2").val();
            var cihazTur = $(".cihazTur").val();
            
            
            $.ajax({
                url: "{{ route('technician.statistics.detail', $tenant_id) }}",
                method: "POST",
                data: {
                _token: "{{ csrf_token() }}",
                tarih1: tarih1,
                tarih1: tarih2,
                cihazTur: cihazTur
            },
                success: function (data) {
                    $('table tr.altSatir td').html(data);
                },
                error: function(xhr, status, error) {
                    console.error('Ajax error:', error);
                    $('table tr.altSatir td').html(`
                        <div class="alert alert-danger m-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Detay verileri yüklenirken bir hata oluştu.
                        </div>
                    `);
                }
            });
        }
    });
});
</script>

@endsection