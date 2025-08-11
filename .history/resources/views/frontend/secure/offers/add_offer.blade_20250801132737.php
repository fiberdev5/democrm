<form method="post" id="addOffer" class="servisModal" action="{{ route('store.offer', $firma->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf
    <div class="row" style="margin: 0">
      <div class="card col-sm-6 card1">
        <div class="card-header" style="color: black;font-weight:bold">Müşteri Bilgileri</div>
        <div class="card-body">
          <div class="row form-group ">
            <label class="col-sm-3 col-form-label rw1">Müşteri<span style="font-weight: bold; color: red;">*</span></label>
            <div class="col-md-9">
              <input id="search" type="text" name="adSoyad" class="form-control adSoyad rw2" data-id="" autocomplete="off" placeholder="Müşteri Adı" required>
              <input type="hidden" name="musteri" class="mid" />
              <ul id="result" style="margin: 0; padding: 0"></ul>
            </div>
          </div>
  
          <div class="row form-group">
            <div class="col-md-3 rw1 col-form-label"><label>Müşteri Bilgileri</label></div>
            <div class="col-md-9 rw2 col-form-label"><textarea class="form-control musBilgileri" disabled style="height: 77px;resize: none !important"></textarea></div>
          </div>
        </div>
      </div>
  
      <div class="card col-sm-6 card2">
        <div class="card-header" style="color: black;font-weight:bold">Teklif Bilgileri</div>
        <div class="card-body">  
        <div class="row form-group">
            <div class="col-md-3 rw1 col-form-label"><label><span class="musteriAdiSpan">Tarih</span> <span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-9 rw2">
              <input name="kayitTarihi" class="form-control datepicker kayitTarihi" type="date" value="{{date('Y-m-d')}}" style="width: 110px;display: inline-block;background:#fff;text-align:center" required>
            </div>
          </div>  
          <div class="row form-group">
            <div class="col-md-3 rw1 col-form-label"><label>Başlık 1</label></div>
            <div class="col-md-9 rw2 col-form-label">
              <input type="text" name="baslik1" class="form-control baslik1" placeholder="Başlık 1" value="Teknik Servis">
            </div>
          </div> 
          <div class="row form-group">
            <div class="col-md-3 rw1 col-form-label"><label>Başlık 2</label></div>
            <div class="col-md-9 rw2 col-form-label">
              <input type="text" name="baslik2" class="form-control baslik2" placeholder="Başlık 2" value="Teklif Formu">
            </div>
          </div>
          
        </div>
      </div>
    </div>
  
    <div class="card card3">
      <div class="card-body">
        <div class="row form-group head">
          <div class="col-5 rw1 col-form-label"><label>Cinsi</label></div>
          <div class="col-2 rw2 col-form-label"><label>Miktar</label></div>
          <div class="col-2 rw3 col-form-label"><label>Fiyat</label></div>
          <div class="col-3 rw4 col-form-label"><label>Tutar</label></div>
        </div>
  
        <div class="satirBody">
          <div class="row form-group">
            <div class="col-5 rw1 col-form-label"><input type="text" name="aciklama[]" class="form-control aciklama aciklama0" placeholder="Ürün" autocomplete="off" required></div>
            <div class="col-2 rw2 col-form-label"><input type="text" name="miktar[]" onkeyup="sayiKontrol(this)" class="form-control miktar miktar0" autocomplete="off" required></div>
            <div class="col-2 rw3 col-form-label"><input type="text" name="fiyat[]" onkeyup="sayiKontrol(this)" class="form-control fiyat fiyat0" autocomplete="off" required></div>
            <div class="col-3 rw4 col-form-label"><input type="text" name="tutar[]" onkeyup="sayiKontrol(this)" class="form-control tutar tutar0" autocomplete="off" required></div>
          </div>
        </div>
  
        <div class="row form-group">
          <button type="button" class="col-xs-12 form-control btn btn-primary satirEkle" data-id="1" style="color: #fff;display: inline-block;margin: 5px">Satır Ekle</button>
        </div>
      </div>
    </div>
  
    <div class="row" style="margin: 0">
      <div class="card col-sm-6 card4">
        <div class="card-body">
          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1 col-form-label"><label>Durum</label></div>
            <div class="col-md-8 rw2">
              <select class="form-select durum" name="durum">
                <option value="0">Beklemede</option>
                <option value="1">Onaylandı</option>
                <option value="2">Onaylanmadı</option>
                <option value="3">Cevap Gelmedi</option>
              </select>
            </div>
          </div>
  
          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1 col-form-label"><label>Toplam Yazıyla</label></div>
            <div class="col-md-8 rw2 col-form-label"><input type="text" name="toplamYazi" autocomplete="off" class="form-control toplamYazi"></div>
          </div>
  
          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1 col-form-label"><label>Döviz Kuru</label></div>
            <div class="col-md-8 rw2 col-form-label">
              <input type="text" onkeyup="sayiKontrol(this)" name="dovizKuru" autocomplete="off" class="form-control dovizKuru">
            </div>
          </div>
        </div>
      </div>
  
      <div class="card col-sm-6 card5">
        <div class="card-body">
          <div class="row form-group">
            <div class="col-md-8 rw1 col-form-label"><label>Toplam</label></div>
            <div class="col-md-4 rw2 col-form-label"><input type="text" onkeyup="sayiKontrol(this)" name="toplam" autocomplete="off" class="form-control toplam"></div>
          </div>
          
          <div class="row form-group">
            <div class="col-md-6 rw1 col-form-label"><label>KDV</label></div>
              <div class="col-md-2 rw2 col-form-label"><input type="text" onkeyup="sayiKontrol(this)" name="kdvTutar" autocomplete="off" class="form-control kdvTutar" value="20" style="text-align: center;"></div>
              <div class="col-md-4 rw2 col-form-label"><input type="text" onkeyup="sayiKontrol(this)" name="kdv" class="form-control kdv"></div>
            </div>
  
            <div class="row form-group" style="padding-bottom: 0">
              <div class="col-md-8 rw1 col-form-label"><label>Genel Toplam</label></div>
              <div class="col-md-4 rw2 col-form-label"><input type="text" onkeyup="sayiKontrol(this)" name="genelToplam" autocomplete="off" class="form-control genelToplam"></div>
            </div>
          </div>
        </div>
  
        <textarea name="aciklamalar" class="form-control aciklamalar" rows="6" placeholder="Açıklamalar"></textarea>
      </div>
  
      <div class="row">
        <div class="col-sm-12 gonderBtn">
          <input type="submit" class="btn btn-sm btn-info waves-effect waves-light" value="Kaydet">
        </div>
      </div>
  </form>
  
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
      $('#search').keyup(function () {
        $('#result').html('');
          var searchField = turkceKucukHarfeDonustur($('#search').val());
          var veriler = 'musteriGetir=' + searchField;
          if (searchField.length > 2) {
            var filteredMusteriler = musteriListesi.filter(function (musteri) {
              var adiKucukHarf = turkceKucukHarfeDonustur(musteri.adSoyad);
              return adiKucukHarf.includes(searchField);
            });
            $.each(filteredMusteriler, function (key, value) {
              var tip = value.musteriTipi == "1" ? "Bireysel" : "Kurumsal";
              var ilceAdi = value.ilce ? value.state.ilceName : '';
              var ilAdi = value.il ? value.country.name : '';
              var adresFormatli = (value.adres && value.adres.trim() !== "") ? value.adres : ''; // null veya boşsa boş bırak

              // Adresin formatını oluşturma
              var adresDisplay = adresFormatli ? adresFormatli + " - " + ilceAdi + "/" + ilAdi : ilceAdi + "/" + ilAdi;
              $('#result').append('<li class="list-group-item link-class" data-id="' + value.id + '" data-adSoyad="' + value.adSoyad + '" data-tel="' + value.tel1 + '" data-adres="' + adresDisplay + '" ><span style="font-weight:500;">Ad Soyad: </span>' + value.adSoyad  +' <br><span style="font-weight:500;">Telefon: </span>' + value.tel1 + '<br><span style="font-weight:500;">Adres: </span>' + adresDisplay + '</li>');
            });
          }
        });
        $('#result').on('click', 'li', function () {
          var click_id = $(this).attr('data-id');
          var click_adSoyad = $(this).attr('data-adSoyad');
          var click_tel = $(this).attr('data-tel');
          var click_adres = $(this).attr('data-adres');
          $('.mid').attr('value', click_id);
          $('.adSoyad').val(click_adSoyad);
          $('#addOffer .musBilgileri').val(click_adSoyad+"\n"+click_tel+"\n"+click_adres);
          $("#result").html('');
        });
  
        $(document).click(function (e) {
          if (!$(e.target).closest('.adSoyad').length) {
            $("#result").html('');
          }
        });
      });
  </script>
  
  <script>
    $(document).ready(function () {
      $('#addOffer').submit(function (event) {
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
    $(document).ready(function (e) {
  
  var sonucToplam = 0;
  var sonuc = 0;
  
  $('.satirBody').keyup(function() {
    sonucToplam = 0;
    $('.miktar').each(function(index, data) {
      var fiyat = Number($(".fiyat"+index).val());
      var miktar = Number($(this).val());
      sonuc = fiyat*miktar;
      sonucToplam = sonucToplam + sonuc;
      $(".tutar"+index).val(sonuc)
      kdvHesapla(sonucToplam)
    });
  });
  
  function kdvHesapla(toplam){
    var kdvTutar = Number($(".kdvTutar").val());
    var kdv = (((toplam)*kdvTutar)/100);
    var araToplam = (toplam);
    var genelToplam = ((toplam) + kdv);
  
    $(".toplam").val(toplam);
    $(".genelToplam").val(genelToplam);
    $(".kdv").val(kdv);
  }
  
  $('.kdvTutar').on('keyup', function() {
    var kdvTutar = Number($(".kdvTutar").val());
    var kdv = (((sonucToplam)*kdvTutar)/100);
    var genelToplam = ((sonucToplam) + kdv);
  
    $(".genelToplam").val(genelToplam);
    $(".kdv").val(kdv);
  });
  
 
     $(".satirEkle").click(function(){
        var dataNum = Number($(this).attr("data-id"));
        var satirClone = '<div class="row form-group"><div class="col-5 rw1"><input type="text" name="aciklama[]" class="form-control aciklama" placeholder="Ürün" autocomplete="off"></div><div class="col-2 rw2"><input type="text" name="miktar[]" onkeyup="sayiKontrol(this)" class="form-control miktar miktar'+dataNum+'" autocomplete="off"></div><div class="col-2 rw3"><input type="text" name="fiyat[]" onkeyup="sayiKontrol(this)" class="form-control fiyat fiyat'+dataNum+'" autocomplete="off"></div><div class="col-3 rw4"><input type="text" name="tutar[]" onkeyup="sayiKontrol(this)" class="form-control tutar tutar'+dataNum+'" autocomplete="off"></div></div>';
        $(".satirBody").append(satirClone);
        dataNum = dataNum+1;
        $(this).attr("data-id",dataNum);
      });
    });
  </script>
  
  <script type="text/javascript">
    function sayiKontrol(v) {
      var isNum = /^[0-9-'.']*$/;
      if (!isNum.test(v.value)) { 
        v.value = v.value.replace(/[^0-9-',']/g, "");
      }                   
    }
  </script>
  
  <script>
    $(document).ready(function(){
      $('#addOffer').submit(function(e){
        e.preventDefault();
        if (this.checkValidity() === false) {
          e.stopPropagation();
        } else {
        var formData = $(this).serialize();
        $.ajax({
          url: $(this).attr('action'),
          type: 'POST',
          data: formData,
          success: function(response) {
            alert("Teklif başarıyla eklendi");
            $('#datatableOffer').DataTable().ajax.reload();
            $('#addOfferModal').modal('hide');
          },
          error: function(xhr, status, error) {
            console.error(xhr.responseText);
          }
        });
      }
      });
    });
  </script>