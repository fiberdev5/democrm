@extends('frontend.secure.user_master')
@section('user')
<div class="page-content servis-istatistik">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])
      <div class="row mb-3">
    <div class="col-md-3">
        <input type="text" class="form-control" id="tarih1" placeholder="Başlangıç Tarihi">
    </div>
    <div class="col-md-3">
        <input type="text" class="form-control" id="tarih2" placeholder="Bitiş Tarihi">
    </div>
    <div class="col-md-3">
        <select class="form-control" id="cihazTur">
            <option value="">Tümü</option>
            @foreach($cihazTurleri as $tur)
                <option value="{{ $tur->id }}">{{ $tur->cihaz }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <button class="btn btn-primary" id="filtrele">Filtrele</button>
    </div>
</div>

<table class="table table-bordered" id="istatistikTablo">
    <thead>
        <tr>
            <th>Personel</th>
            <th>Servis</th>
            <th>Tamam</th>
            <th>İptal</th>
            <th>Beklemede</th>
            <th>Şikayet</th>
            <th>Ödeme</th>
            <th>Teklif</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
    </div>
</div>
@endsection

<script>
$(document).ready(function() {
    $('#filtrele').on('click', function() {
        let tarih1 = $('#tarih1').val();
        let tarih2 = $('#tarih2').val();
        let cihazTur = $('#cihazTur').val();
        let tenantId = '{{ $tenant_id }}';

        $.ajax({
            url: `/${tenantId}/teknisyen-istatistik-veri`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                tarih1: tarih1,
                tarih2: tarih2,
                cihazTur: cihazTur
            },
            success: function(response) {
                let rows = '';
                response.data.forEach(item => {
                    rows += `
                        <tr>
                            <td>${item.adsoyad}</td>
                            <td>${item.servis}</td>
                            <td>${item.tamam}</td>
                            <td>${item.iptal}</td>
                            <td>${item.beklemede}</td>
                            <td>${item.sikayet}</td>
                            <td>${item.odeme}</td>
                            <td>${item.teklif}</td>
                        </tr>`;
                });
                $('#istatistikTablo tbody').html(rows);
            }
        });
    });
});
</script>
