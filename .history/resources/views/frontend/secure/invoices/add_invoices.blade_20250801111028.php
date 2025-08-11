<form method="post" id="addInvo" action="{{ route('store.invoices', $firma->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
  @csrf
  <div class="card f5">
    <div class="card-header ch1" style="padding: 3px 10px;">
      <div class="tarihWrap">
        <label style="text-align: left;width: auto;display: inline-block;margin: 0;">Tarih<span style="font-weight: bold; color: red;">*</span></label>
        <input type="date" name="faturaTarihi" class="form-control datepicker kayitTarihi"  value="{{date('Y-m-d')}}" style="width: 150px;display: inline-block;background:#fff" required>
      </div>
      <span class="musteriCikart" style="display: none;"><i class="mid"></i> <i class="fas fa-times-circle"></i></span>

      <div class="clearfix"></div>
    </div>
  </div> 

  <div class="card f2">
     <div class="card-header">MÜŞTERİ BİLGİSİ</div>
     <div class="card-body">
        <div class="row">
           <div class="col-sm-6 s1">
              <div class="row form-group">
                <div class="col-md-4 rw1"><label>Servis Ara</label></div>
                <div class="col-md-8 rw2">
                  <input id="search" type="text" name="servisid" class="form-control servisid" data-id="" autocomplete="off" placeholder="Servis ID" style="width: 116px;display: inline-block;">
                  <a href="#" target="_blank" class="servisiAc" style="display: inline-block;color: red">Servisi Aç</a>
                  <ul id="result" style="margin: 0;padding: 0"></ul>
                </div>
              </div>
              <div class="row form-group">
                 <div class="col-md-4 rw1"><label><span class="musteriAdiSpan">Müşteri Adı</span> <span style="font-weight: bold; color: red;">*</span></label></div>
                 <div class="col-md-8 rw2">
                   <input type="text" name="adSoyad" class="form-control buyukYaz adSoyad" data-id="" autocomplete="off" placeholder="Müşteri Adı">
                 </div>
              </div>
              <div class="row form-group">
                 <div class="col-md-4 rw1"><label>Vergi No/Dairesi</label></div>
                 <div class="col-md-4 col-6 rw2">
                    <input type="number" name="vergiNo" class="form-control vergiNo" placeholder="Vergi No" autocomplete="off">
                 </div>
                 <div class="col-md-4 col-6 rw2">
                    <input type="text" name="vergiDairesi" class="form-control buyukYaz vergiDairesi" placeholder="Vergi Dairesi" autocomplete="off">
                 </div>
              </div>
           </div>
           <div class="col-sm-6 s2">
              <div class="row form-group">
                 <div class="col-sm-4"><label>İl/İlçe</label></div>
    <div class="col-sm-4">
      <select name="il" id="country" class="form-control form-select" style="width:100%!important;">
        <option value="" selected disabled>-Seçiniz-</option>
        @foreach($countries as $item)
          <option value="{{ $item->id }}">{{ $item->name}}</option>
        @endforeach
      </select>
    </div>
    <div class="col-sm-4">
      <select name="ilce" id="city" class="form-control form-select" style="width:100%!important;">
        <option value="" selected disabled>-Seçiniz-</option>                              
      </select>
    </div>
              </div>

              <div class="row form-group">
                 <div class="col-md-4 rw1"><label>Adres <span style="font-weight: bold; color: red;">*</span></label></div>
                 <div class="col-md-8 rw2"><textarea name="adres" class="form-control buyukYaz adres" placeholder="Adres" rows="3" style="resize: none !important"></textarea></div>
              </div>
           </div>
        </div>
     </div>
  </div>
    
  {{-- <div class="card f1">
    <div class="card-body">
      <div class="row form-group">
        <div class="col-md-3 col-lg-2 rw1"><label><span class="musteriAdiSpan">Müşteri Adı</span> <span style="font-weight: bold; color: red;">*</span></label></div>
        <div class="col-md-8 col-lg-10 rw2">
          <input id="ara" type="text" name="adSoyad" class="form-control buyukYaz adSoyad" data-id="" autocomplete="off" placeholder="Müşteri Adı" required>
          <input type="hidden" name="musteri" class="mid" />
          <ul id="sonuc" style="margin: 0;padding: 0;top: 27px"></ul>
        </div>
      </div> 
      <div class="row form-group">
        <div class="col-md-3 col-lg-2 rw1"><label>Müşteri Bilgileri</label></div>
        <div class="col-md-8 col-lg-10 rw2"><textarea class="form-control musBilgileri" disabled style="height: 77px;resize: none !important"></textarea></div>
      </div>  
    </div>
  </div> --}}

  <div class="card f2">
    <div class="card-body">
      <div class="row form-group head">
        <div class="col-5 rw1"><label>Cinsi</label></div>
        <div class="col-2 rw2"><label>Miktar</label></div>
        <div class="col-2 rw3"><label>Fiyat</label></div>
        <div class="col-3 rw4"><label>Tutar</label></div>
      </div>

      <div class="row form-group">
        <div class="col-5 rw1"><input type="text" name="aciklama[]"  class="form-control buyukYaz aciklama1" placeholder="1.Ürün" autocomplete="off"></div>
        <div class="col-2 rw2"><input type="text" name="miktar[]" onkeyup="sayiKontrol(this)" class="form-control buyukYaz" autocomplete="off"></div>
        <div class="col-2 rw3"><input type="text" name="fiyat[]" onkeyup="sayiKontrol(this)" class="form-control buyukYaz" autocomplete="off"></div>
        <div class="col-3 rw4"><input type="text" name="tutar[]" onkeyup="sayiKontrol(this)" class="form-control buyukYaz" autocomplete="off"></div>
      </div>

      <div class="row form-group">
        <div class="col-5 rw1"><input type="text" name="aciklama[]"  class="form-control buyukYaz aciklama2" placeholder="2.Ürün" autocomplete="off"></div>
        <div class="col-2 rw2"><input type="text" name="miktar[]" onkeyup="sayiKontrol(this)" class="form-control buyukYaz" autocomplete="off"></div>
        <div class="col-2 rw3"><input type="text" name="fiyat[]" onkeyup="sayiKontrol(this)" class="form-control buyukYaz" autocomplete="off"></div>
        <div class="col-3 rw4"><input type="text" name="tutar[]" onkeyup="sayiKontrol(this)" class="form-control buyukYaz" autocomplete="off"></div>
      </div>

      <div class="row form-group">
        <div class="col-5 rw1"><input type="text" name="aciklama[]"  class="form-control buyukYaz" placeholder="3.Ürün" autocomplete="off"></div>
        <div class="col-2 rw2"><input type="text" name="miktar[]" onkeyup="sayiKontrol(this)" class="form-control buyukYaz" autocomplete="off"></div>
        <div class="col-2 rw3"><input type="text" name="fiyat[]" onkeyup="sayiKontrol(this)" class="form-control buyukYaz" autocomplete="off"></div>
        <div class="col-3 rw4"><input type="text" name="tutar[]" onkeyup="sayiKontrol(this)" class="form-control buyukYaz" autocomplete="off"></div>
      </div>

      <div class="row form-group">
        <div class="col-5 rw1"><input type="text" name="aciklama[]"  class="form-control buyukYaz" placeholder="4.Ürün" autocomplete="off"></div>
        <div class="col-2 rw2"><input type="text" name="miktar[]" onkeyup="sayiKontrol(this)" class="form-control buyukYaz" autocomplete="off"></div>
        <div class="col-2 rw3"><input type="text" name="fiyat[]" onkeyup="sayiKontrol(this)" class="form-control buyukYaz" autocomplete="off"></div>
        <div class="col-3 rw4"><input type="text" name="tutar[]" onkeyup="sayiKontrol(this)" class="form-control buyukYaz" autocomplete="off"></div>
      </div>

      <div class="row form-group">
        <div class="col-5 rw1"><input type="text" name="aciklama[]"  class="form-control buyukYaz" placeholder="5.Ürün" autocomplete="off"></div>
        <div class="col-2 rw2"><input type="text" name="miktar[]" onkeyup="sayiKontrol(this)" class="form-control buyukYaz" autocomplete="off"></div>
        <div class="col-2 rw3"><input type="text" name="fiyat[]" onkeyup="sayiKontrol(this)" class="form-control buyukYaz" autocomplete="off"></div>
        <div class="col-3 rw4"><input type="text" name="tutar[]" onkeyup="sayiKontrol(this)" class="form-control buyukYaz" autocomplete="off"></div>
      </div>

      <div class="row form-group" style="margin-bottom: 0;border: 0">
        <div class="col-5 rw1"><input type="text" name="aciklama[]"  class="form-control buyukYaz" placeholder="6.Ürün" autocomplete="off"></div>
        <div class="col-2 rw2"><input type="text" name="miktar[]" onkeyup="sayiKontrol(this)" class="form-control buyukYaz" autocomplete="off"></div>
        <div class="col-2 rw3"><input type="text" name="fiyat[]" onkeyup="sayiKontrol(this)" class="form-control buyukYaz" autocomplete="off"></div>
        <div class="col-3 rw4"><input type="text" name="tutar[]" onkeyup="sayiKontrol(this)" class="form-control buyukYaz" autocomplete="off"></div>
      </div>
    </div>
  </div>
       
  <div class="row cardRow1">
    <div class="card col-lg-6 f3">
      <div class="card-body">
        <div class="row" style="border:0">
          <div class="col-md-4 rw1"><label>Ödeme Ş<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-4 rw2">
              <select class="form-select odemeSekilleri" name="odemeSekli" required>
                <option value="">Seçiniz</option>
                @foreach($payment_methods as $method)
                  <option value="{{$method->id}}">{{$method->odeme_sekli}}</option>
                @endforeach
              </select>
            </div>
            <div class=" col-md-4 rw3" style="padding-right: 0">
              <select class="form-select odemeDurum" name="odemeDurum" required>
                <option value="0">Ödenmedi</option>
                <option value="1">Ödendi</option>
              </select>
             </div>
          </div>

          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1"><label>Toplam Yazıyla</label></div>
            <div class="col-md-8 rw2"><input type="text" name="toplamYazi" autocomplete="off" class="form-control buyukYaz toplamYazi"></div>
          </div>

          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1"><label>Fatura No<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-8 rw2">
              <input type="text" name="faturaNumarasi" class="form-control buyukYaz faturaNumarasi" value="" required>
            </div>
          </div>

          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1"><label>E-Arşiv<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-8 rw2">
              <input type="file" class="form-control" name="document" id="customFile" required>
            </div>
          </div>       
        </div>
      </div>

      <div class="card col-lg-6 f4">
        <div class="card-body" style="padding:17px 5px">
          <div class="row form-group">
            <div class="col-md-8 rw1"><label>Toplam<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-4 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="toplam" autocomplete="off" class="form-control toplam" required></div>
          </div>

          <div class="row form-group">
            <div class="col-md-6 rw1"><label>KDV %</label></div>
            <div class="col-md-2 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="kdvTutar" autocomplete="off" class="form-control kdvTutar" value="20" style="text-align: center;"></div>
            <div class="col-md-4 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="kdv" class="form-control kdv" value="0"></div>
          </div>

          <div class="row form-group" style="padding-bottom: 0">
            <div class="col-md-8 rw1"><label>Genel Toplam<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-4 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="genelToplam" autocomplete="off" class="form-control genelToplam" required></div>
          </div>
          <div class="row form-group" style="padding-bottom: 0">
            <div class="col-md-8 rw1"><label>Para Birimi</label></div>
            <div class="col-md-4 rw2">
              <select class="form-control paraBirimi" name="paraBirimi">
                <option value="1">₺ (TL)</option>
                <option value="2">$ (USD)</option>
                <option value="3">€ (EURO)</option>
              </select>
            </div>
          </div>         
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12 gonderBtn">
        <input type="submit" class="btn btn-sm btn-info waves-effect waves-light" value="Kaydet">
      </div>
    </div>
  </div>
</form>

{{-- <script>
  $('.buyukYaz').keyup(function(){
      this.value = this.value.toUpperCase();
    });
</script> --}}

<script type="text/javascript">
  function sayiKontrol(v) {
    var isNum = /^[0-9-'.']*$/;
    if (!isNum.test(v.value)) { 
      v.value = v.value.replace(/[^0-9-',']/g, "");
    }                   
  }
</script>

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
        $('.mid').attr('value', click_id);
        $('.adSoyad').val(click_adSoyad + " (" + click_firmaAdi + ")");
        $("#sonuc").html('');
      });

      $(document).click(function (e) {
        if (!$(e.target).closest('.adSoyad').length) {
          $("#sonuc").html('');
        }
      });
      $('#addInvoiceModal').click(function(e) {  
      $("#addInvoice #sonuc").html('');
    });
    });

    $('#addInvo #sonuc').on('click', 'li', function () {
    var click_id = $(this).attr('data-id');
    var click_adSoyad = $(this).attr('data-adSoyad');
    var click_tel = $(this).attr('data-tel');
    var click_adres = $(this).attr('data-adres');
    var click_firmaAdi = $(this).attr('data-firmaAdi');
    var click_vergi_no = $(this).attr('data-vergi_no');
    var click_vergi_dairesi = $(this).attr('data-vergi_dairesi');

    //$(".musteriCikart").show();
    $('#addInvo #alici').attr('value', click_id);
    $('#addInvo .adSoyad').val(click_adSoyad);
    $('#addInvo .musBilgileri').val(click_firmaAdi+"\n"+click_tel+"\n"+click_adres+" ");
    $("#addInvo #sonuc").html('');
    });

    $('#addInvoiceModal').click(function(e) {  
    $("#addInvo #sonuc").html('');
    });
</script>

<script>
  $(document).ready(function () {
    $('#addInvo').submit(function (event) {
      var formIsValid = true;
      $(this).find('input, select').each(function () {
        var isRequired = $(this).prop('required');
        var isEmpty = !$(this).val();

        if (isRequired && isEmpty) {
          formIsValid = false;
          return false;
        }
      });

      if (!formIsValid) {
        event.preventDefault();
        alert('Lütfen zorunlu alanları doldurun.');
        return false;
      }
    });
  });
</script>
<script>
  $(document).ready(function() {
    // Ülke seçildiğinde şehirleri getir
    $("#country").change(function() {
      var selectedCountryId = $(this).val();
      if (selectedCountryId) {
        loadCities(selectedCountryId);
      }
    });
    // Şehirleri yüklemek için kullanılan fonksiyon
    function loadCities(countryId) {
      var citySelect = $("#city");
      citySelect.empty(); // Önceki seçenekleri temizle
      citySelect.append(new Option("Yükleniyor...", "")); // Kullanıcıya yükleniyor bilgisi ver
  
      // AJAX isteğiyle şehirleri al
      $.get("/get-states/" + countryId, function(data) {
        citySelect.empty(); // Yükleniyor mesajını temizle
        citySelect.append(new Option("-Seçiniz-", "")); // İlk boş seçeneği ekle
        $.each(data, function(index, city) {
          citySelect.append(new Option(city.ilceName, city.id));
        });
      }).fail(function() {
        citySelect.empty(); // Hata durumunda temizle
        citySelect.append(new Option("Unable to load cities", ""));
      });
    }
  });
</script>