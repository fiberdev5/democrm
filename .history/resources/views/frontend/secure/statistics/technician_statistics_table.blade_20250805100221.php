<div class="table-responsive">
    <table class="table table-hover table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead class="title">
            <tr>
                <th class="name">
                    <span class="desktop">Personel</span>
                    <span class="mobile">Personel</span>
                </th>
                <th style="width: 85px">
                    <span class="desktop">Atanan Servis</span>
                    <span class="mobile">A</span>
                </th>
                <th style="width: 85px">
                    <span class="desktop">Tamamlanan Servis</span>
                    <span class="mobile">T</span>
                </th>
                <th style="width: 85px">
                    <span class="desktop">Şikayetçi Servis</span>
                    <span class="mobile">Ş</span>
                </th>
                <th style="width: 85px">
                    <span class="desktop">İptal <br> Servis</span>
                    <span class="mobile">İ</span>
                </th>
                <th style="width: 85px">
                    <span class="desktop">Haber Verecek</span>
                    <span class="mobile">H</span>
                </th>
                <th style="width: 85px">
                    <span class="desktop">Fiyatta Anlaşılamadı</span>
                    <span class="mobile">F</span>
                </th>
                <th style="width: 85px">
                    <span class="desktop">Alınan <br> Ücret</span>
                    <span class="mobile">Ü</span>
                </th>
                <th style="width: 85px">
                    <span class="desktop">Verilen Teklif</span>
                    <span class="mobile">T</span>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $result)
            <tr data-persid="{{ $result['id'] }}" class="tdDetayBtn" style="cursor: pointer;">
                <td><strong>{{ $result['name'] }}</strong></td>
                <td><strong>{{ $result['assigned_services'] }}</strong></td>
                <td><strong>{{ $result['completed_services'] }}</strong></td>
                <td><strong>{{ $result['complaint_services'] }}</strong></td>
                <td><strong>{{ $result['cancelled_services'] }}</strong></td>
                <td><strong>{{ $result['callback_services'] }}</strong></td>
                <td><strong>{{ $result['price_disagreement'] }}</strong></td>
                <td data-sort="{{ $result['collected_amount'] }}">
                    <strong>{{ number_format($result['collected_amount'], 2) }} TL</strong>
                </td>
                <td data-sort="{{ $result['given_offers'] }}">
                    <strong>{{ number_format($result['given_offers'], 2) }} TL</strong>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $('#dataTable').DataTable({
        "bLengthChange": false,
        "paging": false,
        "info": false,
        "order": [[ 7, 'desc' ]], // Alınan ücrete göre sırala
        "language": {
            "sEmptyTable": "Herhangi bir servis hareketi bulunamadı.",
            "sInfoEmpty": "-",
            "search": "Personel Ara:",
            "zeroRecords": "Eşleşen kayıt bulunamadı"
        },
        "columnDefs": [
            {
                "targets": [1, 2, 3, 4, 5, 6, 7, 8], // Sayısal sütunlar
                "className": "text-center"
            }
        ]
    });
});
</script>