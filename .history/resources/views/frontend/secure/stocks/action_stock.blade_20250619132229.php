
  <div class="card" style="margin-bottom: 3px;">
    <div class="card-header ch1" style="padding: 3px 0;">
      <div class="row" style="margin-left: -10px; margin-right: -10px;">
        <div class="col-7 text-end">
          <label style="width: auto; display: inline-block; margin: 0;">İşlem : </label>
          <select class="form-control islemSec" name="islemSec" style="display: inline-block; width: fit-content;">
            <option value="0">Hepsi</option>
            <option value="1">Alış</option>
            <option value="2">Serviste Kullanım</option>
            <option value="3">Personel'e Gönder</option>
          </select>
        </div>
      </div>
    </div>

    <div class="card-body" style="padding: 0;">
      <div class="table-responsive">
        <table class="table table-bordered table-sm mb-0">
          <thead>
            <tr>
              <th style="display:none;"></th>
              <th style="width: 50px;">Tarih</th>
              <th>İşlem</th>
              <th>No</th>
              <th>Adet</th>
              <th>Fiyat</th>
              <th style="width: 55px;">Sil</th>
            </tr>
          </thead>
          <tbody>
            @php $toplam = 0; @endphp
            @foreach($stokHareketleri as $stokIslem)
              @php
                $tarihSaat = explode(' ', $stokIslem->created_at);
                $tarih = explode('-', $tarihSaat[0]);
                $toplam += $stokIslem->adet;

                $islem = '';
                $renk = '';

                if($stokIslem->islem == 1){
                  $islem = "Alış";
                  $renk = 'background-color: rgb(135, 255, 135);';
                } elseif($stokIslem->islem == 2){
                  $islem = "Serviste Kullanım";
                } elseif($stokIslem->islem == 3){
                  $islem = "Personel Depo";
                  $renk = 'background-color: rgb(255, 119, 119);';

                  $perKasa = \App\Models\PersonelStok::find($stokIslem->perStokId);
                  $perSec = $perKasa ? \App\Models\User::find($perKasa->pid) : \App\Models\User::find($stokIslem->personel);
                }
              @endphp

              <tr style="{{ $renk }}">
                <td class="tdNumber" style="display:none;">0,{{ $stokIslem->islem }}</td>
                <td>{{ $tarih[2] }}/{{ $tarih[1] }}/{{ $tarih[0] }}</td>
                <td>{{ $islem }}</td>
                <td>
                  @if($stokIslem->islem == 1)
                    {{ $stokIslem->tedarikci }}
                  @elseif($stokIslem->islem == 2)
                    Servis: {{ $stokIslem->servisid }} ({{ $stokIslem->name }})
                  @elseif($stokIslem->islem == 3)
                    {{ $perSec->name ?? '' }}
                  @endif
                </td>
                <td>{{ $stokIslem->adet }}</td>
                <td>{{ $stokIslem->fiyat }} TL</td>
                <td>
                  <a href="#" class="btn btn-danger btn-sm stokHareketSil" data-id="{{ $stokIslem->id }}">Sil</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

<script>
  $(document).ready(function () {
    $('.islemSec').on('change', function() {
      let val = $(this).val();

      if(val == "1") $(".thBaslik").html("Tedarikçi");
      else if(val == "2") $(".thBaslik").html("Servis No");
      else if(val == "3") $(".thBaslik").html("Personel");
      else $(".thBaslik").html("No");

      let dataset = $('.filterTable tbody tr');
      dataset.show();

      if(val !== "0"){
        dataset.filter(function() {
          return $(this).find('.tdNumber').text().split(',').indexOf(val) === -1;
        }).hide();
      }

      // Toplamları hesapla
      let totalQuantity = 0;
      let totalPrice = 0;
      dataset.filter(':visible').each(function() {
        totalQuantity += parseInt($(this).find('td:eq(4)').text()) || 0;
        totalPrice += parseFloat($(this).find('td:eq(5)').text()) || 0;
      });

      let totalRow = `<tr><td colspan="3"></td><td style="font-weight:bold;">${totalQuantity} Adet</td><td style="font-weight:bold;">${totalPrice} TL</td><td></td></tr>`;

      $('.filterTable tbody tr.total-row').remove();
      $('.filterTable tbody').prepend(totalRow);
    });


    $(document).on('click', '.stokHareketSil', function(e) {
      e.preventDefault();
      let id = $(this).data('id');
      if(confirm('Silmek istediğinizden emin misiniz?')){
        $.post("{{ url('stok-hareket-sil') }}", { id: id, _token: "{{ csrf_token() }}" }, function(data){
          if(data === "-1"){
            window.location.href= "{{ url('stoklar') }}";
          } else if(data === "-2"){
            alert("Paketinizin kullanım süresi bitmiştir. Yeni paket için iletişime geçin.");
          } else {
            alert(data);
            $('#hareketEkleModal').modal('hide');
            // Sayfayı veya tab içeriğini yenileyebilirsin
            location.reload();
          }
        });
      }
    });
  });
</script> 

