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
    <option value="3">Personel'e Gönder</option>
    <option value="2">Serviste Kullanım</option>
  </select>
  <div class="mt-2 toplam-bilgi" style="display: none;">
    <small class="text-muted">
      <span class="badge bg-info">
        <i class="bi bi-calculator"></i> 
        Toplam: <span class="toplam-adet">0</span> Adet - <span class="toplam-tutar">0.00</span> TL
      </span>
    </small>
  </div>
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
                
                $filterIslem = 0;
                $islem = '';
                $renk = '';

               if ($stokIslem->islem == 1) {
                  $islem = "Alış";
                  $renk = 'background-color: rgb(135, 255, 135);';
                  $filterIslem = 1;
                  $toplam += $stokIslem->adet;
                } elseif ($stokIslem->islem == 2) {
                  $islem = "Serviste Kullanım";
                  $filterIslem = 2;
                } elseif ($stokIslem->islem == 3) {
                  $islem = "Personel Depo";
                  $renk = 'background-color: rgb(255, 119, 119);';
                  $filterIslem = 3;
                
                  $perKasa = \App\Models\PersonelStock::find($stokIslem->perStokId);
                  $perSec = $perKasa ? \App\Models\User::find($perKasa->pid) : \App\Models\User::find($stokIslem->personel);
                  $toplam -= $stokIslem->adet; 
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
    <form id="hareketEkleForm" method="POST" action="{{ route('store.stock.action', request()->route('tenant_id')) }}">
      @csrf
      <input type="hidden" name="stok_id" id="modalStokId">
      <input type="hidden" name="islem" id="modalIslem" value="2" />

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Stok Hareketi Ekle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
        </div>
        <div class="modal-body">

          <div class="mb-2">
            <label>Tedarikçi</label>
            <select name="tedarikci" class="form-control">
              <option value="">Seçiniz</option>
              @foreach(\App\Models\StockSupplier::all() as $tedarikci)
                <option value="{{ $tedarikci->id }}">{{ $tedarikci->tedarikci }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-2 d-none" id="personelSelectDiv">
            <label>Personel</label>
            <select name="personel" class="form-control">
              <option value="">Seçiniz</option>
              @foreach(\App\Models\User::where('tenant_id', request()->route('tenant_id'))->get() as $personel)
                <option value="{{ $personel->user_id }}">{{ $personel->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label for="adet">Adet</label>
            <input type="number" name="adet" class="form-control" required min="1">
          </div>

          <div class="mb-3" id="fiyatInputDiv">
            <label for="fiyat">Fiyat (TL)</label>
            <input type="text" name="fiyat" class="form-control" >
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
  $(document).ready(function() {
    // Modal açılırken stok id ve islem değerini ayarla
    $('.hareketEkleBtn').on('click', function() {
      let stokId = $(this).data('stokid');
      $('#modalStokId').val(stokId);

      // Filtre dropdowndan seçili islem değerini al (default 2)
      let islemSecVal = $('.islemSec').val() || '2';
      $('#modalIslem').val(islemSecVal);

      // Modalu göster
      $('#hareketEkleModal').modal('show');

      // İşleme göre modal içeriği ayarla
      updateModalFields(islemSecVal);
    });

    // Filtre değiştiğinde modal açıksa gizli input ve alanları güncelle
    $('.islemSec').on('change', function() {
      let selected = $(this).val();

      if ($('#hareketEkleModal').hasClass('show')) {
        $('#modalIslem').val(selected);
        updateModalFields(selected);
      }
    });

    // Modal içindeki alanları işlem tipine göre göster/gizle fonksiyonu
    function updateModalFields(islem) {
      if (islem == '1') {
        // Alış
        $('#fiyatInputDiv').show();
        $('#fiyatInputDiv input').prop('required', true);

        $('#personelSelectDiv').addClass('d-none');
        $('#personelSelectDiv select').prop('required', false);

        $('select[name="tedarikci"]').closest('.mb-2').show();
      } else if (islem == '3') {
        // Personel'e Gönder
        $('#fiyatInputDiv').hide();
        $('#fiyatInputDiv input').prop('required', false).val('');

        $('#personelSelectDiv').removeClass('d-none');
        $('#personelSelectDiv select').prop('required', true);

        $('select[name="tedarikci"]').closest('.mb-2').hide();
        $('select[name="tedarikci"]').val('');
      } else {
        // Serviste Kullanım (2) ve diğer durumlar
        $('#fiyatInputDiv').hide();
        $('#fiyatInputDiv input').prop('required', false).val('');

        $('#personelSelectDiv').addClass('d-none');
        $('#personelSelectDiv select').prop('required', false);

        $('select[name="tedarikci"]').closest('.mb-2').hide();
        $('select[name="tedarikci"]').val('');
      }
    }
  });
</script>



