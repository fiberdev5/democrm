 <div id="tab2" class="tab-pane fade" style="padding: 0;">
  <div class="card" style="margin-bottom: 3px;">
    <div class="card-header ch1" style="padding: 3px 0;">
      <div class="row" style="margin-left: -10px;margin-right: -10px;">
        <div class="col-5">
          <button type="button" class="btn btn-success btn-sm hareketEkleBtn" data-id="{{ $stock->id }}">Hareket Ekle</button>
        </div>
        <div class="col-7" style="text-align: right;">
          <label style="text-align: left;width: auto;display: inline-block;margin: 0;">İşlem : </label>
          <select class="form-control islemSec" name="islemSec" style="display: inline-block; width: fit-content;">
            <option value="0">Hepsi</option>
            <option value="1">Alış</option>
            <option value="2">Serviste Kullanım</option>
            <option value="3">Personel'e Gönder</option>
          </select>
        </div>
      </div>
    </div>
  </div>

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
      $renk = '';
    } elseif($stokIslem->islem == 3){
      $islem = "Personel Depo";
      $renk = 'background-color: rgb(255, 119, 119);';

      $perKasa = \App\Models\PersonelStok::find($stokIslem->perStokId);
      $perSec = $perKasa ? \App\Models\User::find($perKasa->pid) : \App\Models\User::find($stokIslem->personel);
    }
  @endphp

  <tr style="{{ $renk }}">
    <td class="tdNumber" style="display:none;">0,{{ $stokIslem->islem }}</td>
    <td style="vertical-align: middle; width: 50px; font-size: 11px; padding: 0 10px; font-weight: 500;">
      {{ $tarih[2] }}/{{ $tarih[1] }}/{{ $tarih[0] }}
    </td>
    <td style="vertical-align: middle; font-size: 11px; padding: 0 10px; font-weight: 500;">
      {{ $islem }}
    </td>
    <td style="vertical-align: middle; font-size: 11px; padding: 0 10px; font-weight: 500;">
      @if($stokIslem->islem == 1)
        {{ $stokIslem->tedarikci }}
      @elseif($stokIslem->islem == 2)
        @if(in_array(1, $grup_izinler ?? []))
          <a href="{{ url('servisler/'.$stokIslem->servisid) }}" target="_blank">
            Servis: {{ $stokIslem->servisid }} ({{ $stokIslem->name }})
          </a>
        @else
          Servis: {{ $stokIslem->servisid }} ({{ $stokIslem->name }})
        @endif
      @elseif($stokIslem->islem == 3)
        {{ $perSec->name ?? '' }}
      @endif
    </td>
    <td style="vertical-align: middle; font-size: 11px; padding: 0 10px; font-weight: 500; width:100px;">
      {{ $stokIslem->adet }}
    </td>
    <td style="vertical-align: middle; font-size: 11px; padding: 0 10px; font-weight: 500;">
      {{ $stokIslem->fiyat }} TL
    </td>
    @if($kalanGun >= 0)
    <td style="vertical-align: middle; width: 55px; padding: 0 10px;">
      <a href="#" style="font-size: 11px; position: relative; top: -1px;" class="btn btn-danger btn-sm stokHareketSil" data-id="{{ $stokIslem->id }}">Sil</a>
    </td>
    @endif
  </tr>
@endforeach