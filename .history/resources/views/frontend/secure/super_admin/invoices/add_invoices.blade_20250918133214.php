<style>
  .card {
    border-radius: 10px;
    border: none;
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    margin-bottom: 20px;
  }

  .card-header {
    background-color: #f8f9fa;
    font-weight: 600;
    font-size: 16px;
    padding: 10px 20px;
  }

  .rw1, .rw2 {
    margin-bottom: 0;
  }

  /* Tooltip veya yardımcı yazılar */
  .form-text {
    font-size: 12px;
    color: #6c757d;
  }
</style>

<form method="post" id="addInvo" action="{{ route('super.admin.invoices.store')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
  @csrf
  <div class="card f5">
    <div class="card-header ch1" style="padding: 3px 10px;">
      <div class="tarihWrap">
        <label style="text-align: left;width: auto;display: inline-block;margin: 0;">Tarih<span style="font-weight: bold; color: red;">*</span></label>
        <input type="date" name="faturaTarihi" class="form-control datepicker kayitTarihi"  value="{{date('Y-m-d')}}" style="width: 150px!important;display: inline-block;background:#fff" required>
      </div>

      <div class="clearfix"></div>
    </div>
  </div> 

  <div class="card f2">
     <div class="card-header">FİRMA BİLGİSİ</div>
     <div class="card-body">
        <div class="row">
           <div class="col-sm-6 s1">
              <div class="row form-group">
                <div class="row form-group">
   <div class="col-md-4 rw1"><label>Firma Ara <span style="font-weight: bold; color: red;">*</span></label></div>
   <div class="col-md-8 rw2">
     <input type="text" id="firmaArama" class="form-control" placeholder="Firma adı yazın..." autocomplete="off">
     <ul id="firmaListesi" class="list-group" style="position: absolute; z-index: 1000; width: 100%; display: none;"></ul>
     <input type="hidden" name="firma_id" id="seciliFirmaId" required>
     <div id="seciliFirma" style="display: none; background: #e3f2fd; padding: 8px; margin-top: 5px; border-radius: 4px;">
       <strong id="seciliFirmaAdi"></strong>
       <span style="float: right; cursor: pointer; color: red;" onclick="firmaTemizle()">&times;</span>
       <br><small id="seciliFirmaDetay"></small>
     </div>
   </div>
</div>
              
              <div class="row form-group">
                 <div class="col-md-4 rw1"><label>Vergi No/Dairesi</label></div>
                 <div class="col-md-4 col-6 rw2">
                    <input type="text" name="vergiNo" class="form-control vergiNo" placeholder="Vergi No" autocomplete="off" readonly>
                 </div>
                 <div class="col-md-4 col-6 rw2">
                    <input type="text" name="vergiDairesi" class="form-control buyukYaz vergiDairesi" placeholder="Vergi Dairesi" autocomplete="off" readonly>
                 </div>
              </div>
           </div>
           <div class="col-sm-6 s2">
              <div class="row form-group">
                 <div class="col-sm-2"><label>İl/İlçe</label></div>
                <div class="col-sm-5">
                  <input type="text" name="il" class="form-control il" placeholder="İl" readonly>
                </div>
                <div class="col-sm-5">
                  <input type="text" name="ilce" class="form-control ilce" placeholder="İlçe" readonly>
                </div>
              </div>
              <!-- TELEFON ALANLARI EKLE -->
                <div class="row form-group">
                <div class="col-md-4 rw1"><label>Telefon</label></div>
                <div class="col-md-4 col-6 rw2">
                    <input type="text" name="tel1" class="form-control tel1" placeholder="Telefon 1" readonly>
                </div>
                <div class="col-md-4 col-6 rw2">
                    <input type="text" name="tel2" class="form-control tel2" placeholder="Telefon 2" readonly>
                </div>
                </div>

              <div class="row form-group">
                 <div class="col-md-2 rw1"><label>Adres <span style="font-weight: bold; color: red;font-size:12px;">*</span></label></div>
                 <div class="col-md-10 rw2"><textarea name="adres" class="form-control buyukYaz adres" placeholder="Adres" rows="3" style="resize: none !important" readonly></textarea></div>
              </div>
           </div>
        </div>
     </div>
  </div>

  <div class="card f2">
    <div class="card-body">
      <div class="row form-group head">
        <div class="col-5 rw1 "><label>Cinsi</label></div>
        <div class="col-2 rw2 "><label>Miktar</label></div>
        <div class="col-2 rw3 "><label>Fiyat</label></div>
        <div class="col-3 rw4 "><label>Tutar</label></div>
      </div>

      <div class="satirBody">
        <div class="row form-group">
          <div class="col-5 rw1 "><input type="text" name="aciklama[]" class="form-control aciklama aciklama0 buyukYaz" placeholder="Ürün" autocomplete="off"></div>
          <div class="col-2 rw2 "><input type="text" name="miktar[]" onkeyup="sayiKontrol(this)" class="form-control miktar miktar0" autocomplete="off"></div>
          <div class="col-2 rw3 "><input type="text" name="fiyat[]" onkeyup="sayiKontrol(this)" class="form-control fiyat fiyat0" autocomplete="off"></div>
          <div class="col-3 rw4 "><input type="text" name="tutar[]" onkeyup="sayiKontrol(this)" class="form-control tutar tutar0" autocomplete="off"></div>
        </div>
      </div>

      <div class="row form-group" style="margin: 0;border: 0;">
        <button type="button" class="col-xs-12 form-control btn btn-primary2 satirEkle" data-id="1" style="color: #fff;display: inline-block;">Satır Ekle</button>
      </div>
    </div>
  </div>
       
  <div class="row cardRow1">
    <div class="card col-lg-6 f3">
      <div class="card-body">
        <div class="row" style="border:0">
          <div class="col-md-4 rw1"><label>Ödeme Şekli<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-8 rw2">
              <select class="form-select odemeSekilleri" name="odemeSekli" required>
                <option value="">Seçiniz</option>
                @foreach($payment_methods as $method)
                  <option value="{{$method->id}}">{{$method->odemeSekli}}</option>
                @endforeach
              </select>
            </div>
           
          </div>

          <div class="row form-group" style="border:0">
            <div class="col-md-4 rw1"><label>Toplam Yazıyla</label></div>
            <div class="col-md-8 rw2"><input type="text" name="toplamYazi" autocomplete="off" class="form-control buyukYaz toplamYazi" required></div>
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
          <div class="col-md-8 rw1"><label>İndirim</label></div>
          <div class="col-md-4 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="indirim" autocomplete="off" class="form-control indirim" value="0.00"></div>
        </div>
        <div class="row form-group">
          <div class="col-md-8 rw1"><label>Ara Toplam</label></div>
          <div class="col-md-4 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="araToplam" autocomplete="off" class="form-control araToplam"></div>
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

<script type="text/javascript">
  // Global fonksiyonları window nesnesine ekle
  window.sayiKontrol = function(v) {
    var isNum = /^[0-9-'.']*$/;
    if (!isNum.test(v.value)) { 
      v.value = v.value.replace(/[^0-9-',']/g, "");
    }                   
  }

  // Firma seçme fonksiyonunu global olarak tanımla
  window.firmaSec = function(id, firmaAdi, tel1, tel2, il, ilce, adres, vergiNo, vergiDairesi) {
    $('#seciliFirmaId').val(id);
    $('#seciliFirmaAdi').text(firmaAdi);
    $('#seciliFirmaDetay').html(`Tel: ${tel1 || 'Yok'} | ${il}/${ilce}`);
    
    // Form alanlarını doldur
    $('.tel1').val(tel1 || '');
    $('.tel2').val(tel2 || '');
    $('.il').val(il || '');
    $('.ilce').val(ilce || '');
    $('.adres').val(adres || '');
    $('.vergiNo').val(vergiNo || '');
    $('.vergiDairesi').val(vergiDairesi || '');
    
    $('#firmaArama').val('');
    $('#firmaListesi').hide();
    $('#seciliFirma').show();
  }

  window.firmaTemizle = function() {
    $('#seciliFirmaId').val('');
    $('#seciliFirma').hide();
    $('.tel1, .tel2, .il, .ilce, .adres, .vergiNo, .vergiDairesi').val('');
  }

  $('.buyukYaz').keyup(function(){
    this.value = this.value.toUpperCase();
  });

  $('.satirBody').on('keyup', '.buyukYaz', function () {
    this.value = this.value.toUpperCase();
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
      var indirim = Number($(".indirim").val());
      var kdvTutar = Number($(".kdvTutar").val());
      var araToplam = Number($(".araToplam").val());
      var kdv = (((toplam-indirim)*kdvTutar)/100);
      var araToplam = (toplam-indirim);
      var genelToplam = ((toplam-indirim) + kdv);

      $(".toplam").val(toplam);
      $(".araToplam").val(araToplam);
      $(".genelToplam").val(genelToplam);
      $(".kdv").val(kdv);
    }

    $('.kdvTutar').on('keyup', function() {
      var indirim = Number($(".indirim").val());
      var kdvTutar = Number($(".kdvTutar").val());
      var araToplam = Number($(".araToplam").val());
      var kdv = (((sonucToplam-indirim)*kdvTutar)/100);
      var araToplam = (sonucToplam-indirim);
      var genelToplam = ((sonucToplam-indirim) + kdv);

      $(".araToplam").val(araToplam);
      $(".genelToplam").val(genelToplam);
      $(".kdv").val(kdv);
    });

    $('.indirim').on('keyup', function() {
      var indirim = Number($(".indirim").val());
      var kdvTutar = Number $(".kdvTutar").val());
      var araToplam = Number($(".araToplam").val());
      var kdv = (((sonucToplam-indirim)*kdvTutar)/100);
      var araToplam = (sonucToplam-indirim);
      var genelToplam = ((sonucToplam-indirim) + kdv);

      $(".araToplam").val(araToplam);
      $(".genelToplam").val(genelToplam);
      $(".kdv").val(kdv);
    });
  
    $(".satirEkle").click(function () {
      var dataNum = Number($(this).attr("data-id")); 
      var satirClone = `
        <div class="row form-group align-items-center satir">
          <div class="col-5 rw1">
            <input type="text" name="aciklama[]" class="form-control aciklama aciklama0 buyukYaz" placeholder="Ürün" autocomplete="off">
          </div>
          <div class="col-2 rw2">
            <input type="text" name="miktar[]" onkeyup="sayiKontrol(this)" class="form-control miktar miktar${dataNum}" autocomplete="off">
          </div>
          <div class="col-2 rw3">
            <input type="text" name="fiyat[]" onkeyup="sayiKontrol(this)" class="form-control fiyat fiyat${dataNum}" autocomplete="off">
          </div>
          <div class="col-2 rw4">
            <input type="text" name="tutar[]" onkeyup="sayiKontrol(this)" class="form-control tutar tutar${dataNum}" autocomplete="off">
          </div>
          <div class="col-1 text-end">
            <button type="button" class="btn btn-danger btn-sm satirSil" title="Satırı Sil"><strong>&times;</strong></button>
          </div>
        </div>
      `;  
      $(".satirBody").append(satirClone);
      $(this).attr("data-id", dataNum + 1);
    });
    
    $(document).on('click', '.satirSil', function () {
      $(this).closest('.satir').remove();
    });

// Firma seçildiğinde bilgileri doldur
let firmaAramaTimeout;

$('#firmaArama').on('input', function() {
    const aramaMetni = $(this).val().trim();
    
    clearTimeout(firmaAramaTimeout);
    
    if (aramaMetni.length < 2) {
        $('#firmaListesi').hide();
        return;
    }

    firmaAramaTimeout = setTimeout(function() {
        firmaAra(aramaMetni);
    }, 300);
});

function firmaAra(aramaMetni) {
    $.ajax({
        url: '{{ route("super.admin.firma.ara") }}',
        type: 'POST',
        data: {
            arama: aramaMetni,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            firmaListesiGoster(response);
        },
        error: function() {
            console.log('Arama hatası');
        }
    });
}

function firmaListesiGoster(firmalar) {
    const liste = $('#firmaListesi');
    liste.empty();

    if (firmalar.length === 0) {
        liste.append('<li class="list-group-item">Firma bulunamadı</li>');
    } else {
        firmalar.forEach(function(firma) {
            const item = `
                <li class="list-group-item" style="cursor: pointer;" onclick="firmaSec(${firma.id}, '${firma.firma_adi}', '${firma.tel1 || ''}', '${firma.tel2 || ''}', '${firma.il || ''}', '${firma.ilce || ''}', '${firma.adres || ''}', '${firma.vergNo || ''}', '${firma.vergiDairesi || ''}')">
                    <strong>${firma.firma_adi}</strong><br>
                    <small>Tel: ${firma.tel1 || 'Belirtilmemiş'} | ${firma.il || ''}/${firma.ilce || ''}</small>
                </li>
            `;
            liste.append(item);
        });
    }
    liste.show();
}

// Dışarı tıklayınca listeyi kapat
$(document).click(function(e) {
    if (!$(e.target).closest('#firmaArama, #firmaListesi').length) {
        $('#firmaListesi').hide();
    }
});

    // Form validasyonu
    $('#addInvo').submit(function (event) {
      let formIsValid = true;
      $(this).find('input, select, textarea').each(function () {
        if ($(this).prop('required') && !$(this).val()) {
          formIsValid = false;
          $(this).css('border-color', 'red');
        } else {
          $(this).css('border-color', '');
        }
      });

      if (!formIsValid) {
        event.preventDefault();
        alert('Lütfen zorunlu alanları doldurun.');
      }
    });
  
</script>