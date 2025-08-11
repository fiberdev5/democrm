@extends('frontend.secure.user_master')
@section('user')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1/daterangepicker.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />

<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <h4 class="card-title">Servis Durum İstatistikleri</h4>
            </div>
            <div class="col-md-6 text-end">
                <input type="text" id="daterange" class="form-control" style="max-width: 300px; display: inline-block;">
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table id="datatableStateStats" class="table table-striped table-modern" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Durum</th>
                            <th>Servis Sayısı</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(window.location.search);
            return results === null ? null : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        var tenantId = '{{ request()->route("tenant_id") }}';
        var table = $('#datatableStateStats').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/state-statistics/" + tenantId,
                data: function (d) {
                    d.tarih1 = getUrlParameter('tarih1');
                    d.tarih2 = getUrlParameter('tarih2');
                    d.search = $('div.dataTables_filter input').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'durum_adi', name: 'durum_adi' },
                { data: 'toplam', name: 'toplam' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $('div.dataTables_filter input').unbind().bind('keyup', function (e) {
            if (e.keyCode == 13) {
                table.search(this.value).draw();
            }
        });

        $('#daterange').daterangepicker({
            opens: 'left',
            locale: {
                format: 'YYYY-MM-DD',
                applyLabel: "Uygula",
                cancelLabel: "İptal"
            }
        }, function (start, end) {
            const url = new URL(window.location.href);
            url.searchParams.set('tarih1', start.format('YYYY-MM-DD'));
            url.searchParams.set('tarih2', end.format('YYYY-MM-DD'));
            window.location.href = url.href;
        });

        var tarih1 = getUrlParameter('tarih1');
        var tarih2 = getUrlParameter('tarih2');
        if (tarih1 && tarih2) {
            $('#daterange').data('daterangepicker').setStartDate(moment(tarih1));
            $('#daterange').data('daterangepicker').setEndDate(moment(tarih2));
        }
    });
</script>
@endsection
