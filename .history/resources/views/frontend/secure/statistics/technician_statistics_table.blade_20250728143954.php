<div class="table-responsive mailTable">
    <table class="table table-hover table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead class="title">
            <tr>
                <th class="name"><span class="desktop">Personel</span><span class="mobile">Personel</span></th>
                <th style="width: 85px"><span class="desktop">Atanan Servis</span><span class="mobile">A</span></th>
                <th style="width: 85px"><span class="desktop">Tamamlanan Servis</span><span class="mobile">T</span></th>
                <th style="width: 85px"><span class="desktop">Şikayetçi Servis</span><span class="mobile">Ş</span></th>
                <th style="width: 85px"><span class="desktop">İptal <br> Servis</span><span class="mobile">İ</span></th>
                <th style="width: 85px"><span class="desktop">Haber Verecek</span><span class="mobile">H</span></th>
                <th style="width: 85px"><span class="desktop">Fiyatta Anlaşılamadı</span><span class="mobile">F</span></th>
                <th style="width: 85px"><span class="desktop">Alınan <br> Ücret</span><span class="mobile">Ü</span></th>
                <th style="width: 85px"><span class="desktop">Verilen Teklif</span><span class="mobile">T</span></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($results as $item)
                <tr data-persid="{{ $item['personel']->user_id }}" class="tdDetayBtn">
                    <td><strong>{{ $item['personel']->name }}</strong></td>
                    <td><strong>{{ $item['servisSay'] }}</strong></td>
                    <td><strong>{{ $item['tamamlanan'] }}</strong></td>
                    <td><strong>{{ $item['sikayetciSay'] }}</strong></td>
                    <td><strong>{{ $item['iptalSay'] }}</strong></td>
                    <td><strong>{{ $item['haberSay'] }}</strong></td>
                    <td><strong>{{ $item['fiyatSay'] }}</strong></td>
                    <td data-sort="{{ $item['paraToplam'] }}"><strong>{{ number_format($item['paraToplam'], 2) }} TL</strong></td>
                    <td data-sort="{{ $item['teklifToplam'] }}"><strong>{{ number_format($item['teklifToplam'], 2) }} TL</strong></td>
                </tr>
            @endforeach

            @foreach ($personelNotAllUsers as $perSec)
                <tr>
                    <td><strong>{{ $perSec->name }}</strong></td>
                    <td><strong>0</strong></td>
                    <td><strong>0</strong></td>
                    <td><strong>0</strong></td>
                    <td><strong>0</strong></td>
                    <td><strong>0</strong></td>
                    <td><strong>0</strong></td>
                    <td><strong>0 TL</strong></td>
                    <td><strong>0 TL</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#dataTable').DataTable({
            "bLengthChange": false,
            "paging": false,
            "info": false,
            "order": [ 7, 'desc' ],
            "language": {
                "sEmptyTable": "Herhangi bir servis hareketi bulunamadı.",
                "sInfoEmpty": "-",
                "sSearch": "Ara:", // Arama çubuğu olmadığından önemli değil
                "oPaginate": { // Sayfalama olmadığı için önemli değil
                    "sFirst": "İlk",
                    "sLast": "Son",
                    "sNext": "Sonraki",
                    "sPrevious": "Önceki"
                }
            },
            "aoColumnDefs": [
                { "bSortable": false, "aTargets": [ "_all" ] } // Tüm kolonlarda sıralamayı kapat
            ]
        });

        // Detay butonu tıklaması
        $('table').on('click', 'tr.tdDetayBtn', function(e){
            // Detay içeriği zaten varsa kaldır
            $("table tr.altSatir").remove();

            var persid = $(this).attr("data-persid");
            var current=$(this).index();

            if($(this).hasClass('clicked')){
                $(this).removeClass('clicked');
            } else {
                $('table tr.tdDetayBtn').removeClass('clicked'); // Diğerlerini kapat
                $(this).addClass('clicked');
                var next = current + 1;

                // Yükleniyor animasyonu
                $(this).after("<tr><td colspan='9' class='altSatir' style='padding:0'><img src='{{ asset('images/ajax_load.gif') }}' style='display:block; margin: 10px auto;'></td></tr>");


                var tarih1 = $("form .tarih1").val();
                var tarih2 = $("form .tarih2").val();
                var cihazTur = $("form .cihazTur").val();

                var url = "{{ route('technician.statistics.detail', ['tenant_id' => Request::route('tenant_id')]) }}";

                $.ajax({
                    url: url,
                    method: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        personelTabloDetayGetir: persid,
                        tarih1: tarih1,
                        tarih2: tarih2,
                        cihazTur: cihazTur
                    },
                    success: function (data) {
                        $('table tr:eq('+(current + 1)+')').remove(); // Yükleniyor animasyonunu kaldır
                        $('table tr:eq('+(current)+')').after("<tr class=\"altSatir\"><td colspan='9' style='padding:0'>"+data+"</td></tr>");
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Detay Error:", status, error);
                        $('table tr:eq('+(current + 1)+')').remove(); // Yükleniyor animasyonunu kaldır
                        $('table tr:eq('+(current)+')').after("<tr class=\"altSatir\"><td colspan='9' class='text-danger'>Detaylar yüklenirken bir hata oluştu.</td></tr>");
                    }
                });
            }
        });
    });
</script>