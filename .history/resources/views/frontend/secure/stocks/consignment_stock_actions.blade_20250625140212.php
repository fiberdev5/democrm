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
              }elseif($stokIslem->islem == 4){
                $islem = "Müşteriden Geri Alma";
                $renk = 'background-color: rgb(135, 206, 235);'; // Açık mavi gibi
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
       <input type="hidden" name="fiyat" id="hiddenToplamFiyat"> <!-- Controller'a gönderilecek toplam fiyat -->
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Konsinye Stok Hareketi Ekle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
        </div>
        <div class="modal-body">

          <div class="mb-3">
            <label for="islem">İşlem</label>
            <select name="islem" class="form-control" required>
              <option value="">Seçiniz</option>
              <option value="1">Alış</option>
              <option value="2">Serviste Kullanım</option>
              <option value="4">Müşteriden Geri Alma</option>
            </select>
          </div>

          <div class="mb-2">
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
            <input type="number" name="adet" id="inputAdet" class="form-control" required min="1">
          </div>

          <div class="mb-3">
            <label for="birimFiyat">Birim Fiyat (TL)</label>
            <input type="number" step="0.01" name="fiyatBirim" id="inputBirimFiyat" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Toplam Fiyat</label>
            <input type="text" class="form-control" id="toplamFiyatGoster" readonly>
          </div>
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
  $(document).on('click', '.hareketEkleBtn', function() {
    let stokId = $(this).data('stokid');
    $('#modalStokId').val(stokId);
    $('#hareketEkleModal').modal('show');
  });

  $(document).ready(function () {
    $('.islemSec').on('change', function () {
      var selected = $(this).val();
      var card = $(this).closest('.card');
      var rows = card.find('table tbody tr');

      rows.each(function () {
        var tdNumber = $(this).find('.tdNumber').text().trim();

        if (selected == 0) {
          $(this).show();
        } else {
          if (tdNumber.endsWith(',' + selected)) {
            $(this).show();
          } else {
            $(this).hide();
          }
        }
      });
    });
  });
</script>
