<ul class="nav nav-pills " role="tablist" style="margin-bottom: 5px;">
  <li class="nav-item" style="font-size: 14px;"><a class="nav-link nav1 active" data-bs-toggle="pill" href="#tab1" data-id="{{$stock->id}}" role="tab">Ürün Bilgileri</a></li>
  <li class="nav-item" style="font-size: 14px;"><a class="nav-link nav2" data-bs-toggle="pill" href="#tab2" data-id="{{$stock->id}}" role="tab">Stok Haraketleri</a></li>
  <li class="nav-item" style="font-size: 14px;"><a class="nav-link nav2" data-bs-toggle="pill" href="#tab2" data-id="{{$stock->id}}" role="tab">Personel Stokları</a></li>
  <li class="nav-item" style="font-size: 14px;"><a class="nav-link nav2" data-bs-toggle="pill" href="#tab2" data-id="{{$stock->id}}" role="tab">Fotoğraflar</a></li>
</ul>




<div class="tab-content">
  <div id="tab1" class="tab-pane active" style="padding: 0">
<form method="POST" id="editStock" action="{{ route('update.stock', [$firma->id, $stock->id]) }}">
  @csrf

  <div class="row mb-2">
    <label class="col-sm-4">Markalar</label>
    <div class="col-sm-8">
      <select name="marka_id" class="form-control form-select" required>
        <option value="" selected disabled>Seçiniz</option>
        @foreach($markalar as $marka)
          <option value="{{ $marka->id }}" {{ $stock->stok_marka == $marka->id ? 'selected' : '' }}>
            {{ $marka->marka }}
          </option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Cihaz Türleri</label>
    <div class="col-sm-8">
      <select name="cihaz_id" class="form-control form-select" required>
        <option value="" selected disabled>Seçiniz</option>
        @foreach($cihazlar as $cihaz)
          <option value="{{ $cihaz->id }}" {{ $stock->stok_cihaz == $cihaz->id ? 'selected' : '' }}>
            {{ $cihaz->cihaz }}
          </option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Raf Seç</label>
    <div class="col-sm-8">
      <select name="raf_id" class="form-control form-select" required>
        <option value="" selected disabled>Seçiniz</option>
        @foreach($rafListesi as $raf)
          <option value="{{ $raf->id }}" {{ $stock->urunDepo == $raf->id ? 'selected' : '' }}>
            {{ $raf->raf_adi }}
          </option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Ürün Adı</label>
    <div class="col-sm-8">
      <input type="text" name="urunAdi" class="form-control" value="{{ $stock->urunAdi }}">
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Ürün Kodu</label>
    <div class="col-sm-8">
      <input type="text" name="urunKodu" class="form-control" value="{{ $stock->urunKodu }}">
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Adet</label>
    <div class="col-sm-8">
      <input type="number" min="1" name="adet" class="form-control" value="{{ optional($stock->sonHareket)->adet ?? '' }}" required>
    </div>
  </div>

  <div class="row mb-2">
    <label class="col-sm-4">Fiyat</label>
    <div class="col-sm-8">
      <input type="text" name="fiyat" class="form-control" value="{{ optional($stock->sonHareket)->fiyat ?? '' }}">
    </div>
  </div>

  <div class="row mb-3">
    <label class="col-sm-4">Açıklama</label>
    <div class="col-sm-8">
      <textarea name="aciklama" class="form-control" rows="2">{{ $stock->aciklama }}</textarea>
    </div>
  </div>

  <div class="text-end">
    <input type="submit" class="btn btn-info btn-sm" value="Kaydet">
  </div>
</form>
  </div>
  </div>


 <!--///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
 
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


{{-- <script>
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

    $(".hareketEkleBtn").click(function(){
      let id = $(this).data('id');
      if(id){
        $.ajax({
          url: "{{ url('stok-hareket-ekle') }}?id=" + id
        }).done(function(data) {
          if($.trim(data) === "-1"){
            location.reload(true);
          } else {
            $('#hareketEkleModal').modal('show');
            $('#hareketEkleModal .modal-body').html(data);
          }
        });
      }
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
</script> --}}

