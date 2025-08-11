@extends('frontend.secure.user_master')
@section('user')

<div class="page-content servis-istatistik" id="depotStats">
    <div class="container-fluid">
        @include('frontend.secure.statistics.statistics_menu', ['tenant_id' => $tenant_id])

        <div class="row pageDetail">
            <div class="col-12">
                <div class="table-modern">
                    <div class="card-header">
                        Personel Depo İstatistikleri
                        <div class="searchWrap float-end">
                            <input id="daterange" class="tarih-araligi" />
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatableDepotStats" class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Personel</th>
                                    <th>Toplam Adet</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    let start_date = moment().startOf('month');
    let end_date = moment();

    $('#daterange').daterangepicker({
        startDate: start_date,
        endDate: end_date,
        locale: {
            format: 'DD-MM-YYYY',
            applyLabel: 'Uygula',
            cancelLabel: 'İptal',
            daysOfWeek: ['Pz', 'Pzt', 'Sal', 'Çrş', 'Prş', 'Cm', 'Cmt'],
            monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
            firstDay: 1
        }
    }, function(start, end) {
        table.draw();
    });

    var table = $('#datatableDepotStats').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "{{ route('depot.statistics.data', $tenant_id) }}",
            type: "POST",
            data: function(d) {
                d._token = '{{ csrf_token() }}';
                d.from_date = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
                d.to_date = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
        },
        columns: [
            { data: 'personel' },
            { data: 'toplam' },
            { data: 'action', orderable: false }
        ]
    });
});
</script>
@endsection
