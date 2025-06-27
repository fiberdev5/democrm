<div class="card" style="margin-bottom: 3px;">
  <div class="card-header ch1" style="padding: 3px 0;">
    <div class="row" style="margin-left: -10px; margin-right: -10px;">
      <div class="col-5">
        <button type="button"
                class="btn btn-success btn-sm hareketEkleBtn"
                style="margin-left: 10px;"
                data-stokid="{{ $stock->id }}">
          Hareket Ekle
        </button>
      </div>
      <div class="col-7 text-end">
        <label style="width: auto; display: inline-block; margin: 0;">
          <i class="bi bi-filter-circle text-primary"></i> İşlem :
        </label>
        <select class="form-control-select islemSec d-inline-block" name="islemSec" style="width: auto; min-width: 150px;">
          <option value="0">Hepsi</option>
          <option value="1">Alış</option>
          <option value="2">Serviste Kullanım</option>
          <option value="4">Müşteriden Geri Alma</option>
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
            <th>Detay</th>
            <th>Adet</th>
            <th>Fiyat</th>
            <th style="width: 55px;">Sil</th>
          </tr>
      <tr class="toplam-header-row" style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
        <td style="display:none;"></td>
        <td colspan="3"></td>
        <td class="toplam-adet-header" style="font-weight: bold; text-align: left; color: #050505;">
          0 Adet
        </td>
        <td class="toplam-fiyat-header" style="font-weight: bold; text-align: left; color: #050505;">
          0 TL
        </td>
        <td></td>
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
              }elseif($stokIslem->islem == 4){
                $islem = "Müşteriden Geri Alma";
                $renk = 'background-color: rgb(135, 206, 235);'; 
            }
            @endphp

            <tr style="{{ $renk }}">
              <td class="tdNumber" style="display:none;">0,{{ $stokIslem->islem }}</td>
              <td>{{ $tarih[2] }}/{{ $tarih[1] }}/{{ $tarih[0] }}</td>
              <td>{{ $islem }}</td>
              <td>
                @if($stokIslem->islem == 1)
                  {{ $stokIslem->tedarikci }}
                @endif
              </td>
              <td>{{ $stokIslem->adet }}</td>
              <td>{{ $stokIslem->fiyat }} TL</td>
              <td>
                <form action="{{ route('delete.stock.action', ['tenant_id' => request()->route('tenant_id'), 'id' => $stokIslem->id]) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?');" style="display:inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Hareket Ekle Modal -->
<div class="modal fade" id="hareketEkleModal" tabindex="-1" aria-labelledby="hareketEkleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="hareketEkleForm" method="POST" action="{{ route('store.consignment.stock.action', request()->route('tenant_id')) }}">
      @csrf
      <input type="hidden" name="stok_id" id="modalStokId">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Konsinye Stok Hareketi Ekle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
        </div>
        <div class="modal-body">

          <div class="mb-3">
            <label for="islem">İşlem</label>
            <select name="islem" class="form-control" id="islemSecModal" required>
              <option value="">Seçiniz</option>
              <option value="1" selected>Alış</option>
              <option value="4">Müşteriden Geri Alma</option>
            </select>
          </div>

          <div class="mb-2"  id="tedarikciGroup">
            <label>Tedarikçi</label>
            <select name="tedarikci" class="form-control">
              <option value="">Seçiniz</option>
              @foreach(\App\Models\StockSupplier::all() as $tedarikci)
                <option value="{{ $tedarikci->id }}">{{ $tedarikci->tedarikci }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label for="adet">Adet</label>
            <input type="number" name="adet" class="form-control" required min="1">
          </div>

          <div class="mb-3">
            <label for="fiyat">Fiyat (TL)</label>
            <input type="text" name="fiyat" class="form-control" required>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Kaydet</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  $(document).ready(function () {

    // Modal açıldığında stok ID'yi yerleştir ve tedarikçi kontrolü yap
    $(document).on('click', '.hareketEkleBtn', function() {
      let stokId = $(this).data('stokid');
      $('#modalStokId').val(stokId);
      $('#hareketEkleModal').modal('show');

      // Modal açıldığında işlem tipine göre tedarikçiyi kontrol et
      setTimeout(kontrolTedarikciGoster, 100); // DOM tam yüklensin
    });

    $(document).ready(function () {
    $('.islemSec').on('change', function () {
      var selected = $(this).val();
      var card = $(this).closest('.card');
      var rows = card.find('table tbody tr');
      
      var toplamAdet = 0;
      var toplamFiyat = 0;

      rows.each(function () {
        var tdNumber = $(this).find('.tdNumber').text().trim();

        if (selected == 0) {
          $(this).show();
          
          // Toplam hesapla
          var adet = parseInt($(this).find('td').eq(4).text()) || 0;
          var fiyatText = $(this).find('td').eq(5).text().replace(' TL', '').replace(',', '.');
          var fiyat = parseFloat(fiyatText) || 0;
          
          toplamAdet += adet;
          toplamFiyat += fiyat;
          
        } else {
          if (tdNumber.endsWith(',' + selected)) {
            $(this).show();
            
            // Toplam hesapla
            var adet = parseInt($(this).find('td').eq(4).text()) || 0;
            var fiyatText = $(this).find('td').eq(5).text().replace(' TL', '').replace(',', '.');
            var fiyat = parseFloat(fiyatText) || 0;
            
            toplamAdet += adet;
            toplamFiyat += fiyat;
            
          } else {
            $(this).hide();
          }
        }
      });

      // Header'daki toplam bilgileri güncelle
      card.find('.toplam-adet-header').text(toplamAdet );
      card.find('.toplam-fiyat-header').text(toplamFiyat.toFixed(2));
    });

    // Sayfa yüklendiğinde toplam hesapla
    $('.islemSec').trigger('change');
  });

    // Modal içindeki işlem tipi değişince tedarikçi göster/gizle
    $('#islemSecModal').on('change', function () {
      kontrolTedarikciGoster();
    });

    // Tedarikçi alanını kontrol eden fonksiyon
    function kontrolTedarikciGoster() {
      let islem = $('#islemSecModal').val();
      if (islem == '1') {
        $('#tedarikciGroup').show();
      } else {
        $('#tedarikciGroup').hide();
        $('#tedarikciGroup select').val('');
      }
    }

    // Sayfa ilk yüklendiğinde tedarikçi alanı gizli olsun
    $('#tedarikciGroup').hide();
  });
</script>
