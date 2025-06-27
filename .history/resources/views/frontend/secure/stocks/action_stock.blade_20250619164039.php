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
                } elseif($stokIslem->islem == 3){
                  $islem = "Personel Depo";
                  $renk = 'background-color: rgb(255, 119, 119);';

                  $perKasa = \App\Models\PersonelStock::find($stokIslem->perStokId);
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

  <!-- Hareket Ekle Modal -->
<div class="modal fade" id="hareketEkleModal" tabindex="-1" aria-labelledby="hareketEkleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="hareketEkleForm" method="POST" action="{{ route('store.stock.action', request()->route('tenant_id')) }}">
      @csrf
      <input type="hidden" name="stok_id" id="modalStokId">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Stok Hareketi Ekle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
        </div>
        <div class="modal-body">
          
          <div class="mb-3">
            <label for="islem">İşlem</label>
            <select name="islem" class="form-control" required>
              <option value="">Seçiniz</option>
              <option value="1">Alış</option>
              <option value="2">Serviste Kullanım</option>
              <option value="3">Personel'e Gönder</option>
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
  $(document).on('click', '.hareketEkleBtn', function() {
    let stokId = $(this).data('stokid');
    $('#modalStokId').val(stokId);
    $('#hareketEkleModal').modal('show');
  });


</script>
