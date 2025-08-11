<div class="odemeAciklamalariAlt" id="odemeAciklamalariAlt">
    @if (strpos($cash_payment_id["cevaplar"], '6') !== false)
    <div class="row form-group">
      <label class="col-sm-4">Cihazlar<span style="font-weight: bold; color: red;">*</span></label>
      <div class="col-md-8">
        <select class="form-select cihazlar" name="cihazlar" style="font-weight: 500">
          <option value="">-Seçiniz-</option>
          @foreach ($cihazlar as $item)
            <option value="{{$item->id}}">{{$item->cihaz}}</option>
          @endforeach
        </select>
      </div>
    </div>
  @endif
    @if (strpos($cash_payment_id["cevaplar"], '5') !== false)
    <div class="row form-group">
      <label class="col-sm-4">Markalar<span style="font-weight: bold; color: red;">*</span></label>
      <div class="col-md-8">
        <select class="form-select markalar" name="markalar" style="font-weight: 500">
          <option value="">-Seçiniz-</option>
          @foreach ($markalar as $item)
            <option value="{{$item->id}}">{{$item->marka}}</option>
          @endforeach
        </select>
      </div>
    </div>
  @endif
  @if (strpos($cash_payment_id["cevaplar"], '4') !== false)
    <div class="row form-group">
      <label class="col-sm-4">Tedarikçiler<span style="font-weight: bold; color: red;">*</span></label>
      <div class="col-md-8">
        <select class="form-select personeller" name="personeller" style="font-weight: 500">
          <option value="">-Seçiniz-</option>
          @foreach ($tedarikciler as $item)
            <option value="{{$item->id}}">{{$item->tedarikci}}</option>
          @endforeach
        </select>
      </div>
    </div>
  @endif

  @if (strpos($cash_payment_id["cevaplar"], '3') !== false)
    <div class="row ">
      <div class="col-sm-4"><label>Servis</label></div>
      <div class="col-sm-8">
        <input type="text" name="servis" class="form-control servis" data-id="" autocomplete="off"  required>

      </div>
    </div>
  @endif

  @if(strpos($cash_payment_id["cevaplar"], '2') !== false)
    <div class="row">
      <div class="col-sm-4"><label>Personeller</label></div>
      <div class="col-sm-8">
        <select class="form-select personeller" name="personeller" style="font-weight: 500">
          <option value="">-Seçiniz-</option>
          @foreach ($personeller as $personel)
            <option value="{{$personel->user_id}}">{{$personel->name}}</option>
          @endforeach
        </select>
      </div>
    </div>
  @endif

  @if (strpos($cash_payment_id["cevaplar"], '1') !== false)
    <div class="row">
      <div class="col-sm-4"><label>Açıklama</label></div>
      <div class="col-sm-8">
        <input type="text" name="aciklama" class="form-control aciklama" autocomplete="off" style="font-weight: 500">
      </div>
    </div>
  @endif
</div>

<script>
  var musteriListesi = @json($musteriler);
  function turkceKucukHarfeDonustur(text) {
    if (!text) return '';
    return text.replace(/Ğ/g, 'ğ')
               .replace(/Ü/g, 'ü')
               .replace(/Ş/g, 'ş')
               .replace(/İ/g, 'i')
               .replace(/Ö/g, 'ö')
               .replace(/Ç/g, 'ç')
               .toLowerCase();
  }

  $(document).ready(function () {
    $('#ara').keyup(function () {
      $('#sonuc').html('');
      var searchField = turkceKucukHarfeDonustur($('#ara').val());
      var veriler = 'musteriGetir=' + searchField;
      if (searchField.length > 2) {
        var filteredMusteriler = musteriListesi.filter(function (musteri) {
          var adiKucukHarf = turkceKucukHarfeDonustur(musteri.m_adi);
          var firmaAdiKucukHarf = turkceKucukHarfeDonustur(musteri.firma_adi);
          return adiKucukHarf.includes(searchField) || firmaAdiKucukHarf.includes(searchField);
        });
        $.each(filteredMusteriler, function (key, value) {
          var tip = value.musteriTipi == "1" ? "Bireysel" : "Kurumsal";
          $('#sonuc').append('<li class="list-group-item link-class" data-id="' + value.id + '" data-adSoyad="' + value.m_adi + '" data-firmaAdi="' + value.firma_adi + '" data-tel="' + value.telefon + '" data-adres="' + value.adres + '" ><span style="font-weight:500;">Ad Soyad: </span>' + value.m_adi + ' (' + value.firma_adi + ')<br><span style="font-weight:500;">Telefon: </span>' + value.telefon + '<br><span style="font-weight:500;">Adres: </span>' + value.adres + '</li>');
        });
      }
    });
    $('#sonuc').on('click', 'li', function () {
      var click_id = $(this).attr('data-id');
      var click_adSoyad = $(this).attr('data-adSoyad');
      var click_firmaAdi = $(this).attr('data-firmaAdi');
      $('.m_id').attr('value', click_id);
      $('.adSoyad').val(click_adSoyad + " (" + click_firmaAdi + ")");
      $("#sonuc").html('');
    });

    $(document).click(function (e) {
      if (!$(e.target).closest('.adSoyad').length) {
        $("#sonuc").html('');
      }
    });
  });
</script>