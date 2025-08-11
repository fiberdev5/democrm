@extends('frontend.secure.user_master')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.tr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
@section('user')
<div class="page-content servis-istatistik">
    <div class="container-fluid">
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
                                            @foreach ($deviceTypes as $device)
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
@endsection