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
           <div class="col-12">
              <div class="row form-group">
                 <div class="col-md-2 rw1"><label>Firma Ara <span style="font-weight: bold; color: red;">*</span></label></div>
                 <div class="col-md-10 rw2">
                   <input type="text" id="firmaArama" class="form-control" placeholder="Firma adı yazın..." autocomplete="off">
                   <ul id="firmaListesi" class="list-group" style="position: absolute; z-index: 1000; width: 97%; display: none;"></ul>
                   <input type="hidden" name="firma_id" id="seciliFirmaId" required>
                   <div id="seciliFirma" style="display: none; background: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; margin-top: 5px; border-radius: 4px;">
                     <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                       <div>
                         <div style="font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 16px;" id="seciliFirmaAdi"></div>
                         <div style="font-size: 13px; color: #6c757d; line-height: 1.5;" id="seciliFirmaDetay"></div>
                       </div>
                       <span style="cursor: pointer; color: #dc3545; font-size: 18px; font-weight: bold;" onclick="firmaTemizle()" title="Firmayı Temizle">&times;</span>
                     </div>
                   </div>
                 </div>
              </div>
              
              <!-- Hidden inputs for form submission -->
              <input type="hidden" name="vergiNo" class="vergiNo">
              <input type="hidden" name="vergiDairesi" class="vergiDairesi">
              <input type="hidden" name="tel1" class="tel1">
              <input type="hidden" name="tel2" class="tel2">
              <input type="hidden" name="il" class="il">
              <input type="hidden" name="ilce" class="ilce">
              <textarea name="adres" class="adres" style="display: none;"></textarea>
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
    $('#seciliFirmaDetay').html(
        '<div>📞 Telefon: ' + (tel1 || 'Belirtilmemiş') + (tel2 ? ' / ' + tel2 : '') + '</div>' +
        '<div>📍 Konum: ' + (il || '') + '/' + (ilce || '') + '</div>' +
        '<div>🏢 Vergi: ' + (vergiNo || 'Belirtilmemiş') + (vergiDairesi ? ' - ' + vergiDairesi : '') + '</div>' +
        '<div>📧 Adres: ' + (adres || 'Adres belirtilmemiş') + '</div>'
    );
    
    // Hidden inputları form submit için doldur
    $('.vergiNo').val(vergiNo || '');
    $('.vergiDairesi').val(vergiDairesi || '');
    $('.tel1').val(tel1 || '');
    $('.tel2').val(tel2 || '');
    $('.il').val(il || '');
    $('.ilce').val(ilce || '');
    $('.adres').val(adres || '');
    
    $('#firmaArama').val('');
    $('#firmaListesi').hide();
    $('#seciliFirma').show();
  }

  window.firmaTemizle = function() {
    $('#seciliFirmaId').val('');
    $('#seciliFirma').hide();
    // Hidden inputları da temizle
    $('.vergiNo').val('');
    $('.vergiDairesi').val('');
    $('.tel1').val('');
    $('.tel2').val('');
    $('.il').val('');
    $('.ilce').val('');
    $('.adres').val('');
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
      var kdvTutar = Number($(".kdvTutar").val()); // BURADA HATA VARDI - Parantez eksikti
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
      var satirClone = '<div class="row form-group align-items-center satir">' +
        '<div class="col-5 rw1">' +
          '<input type="text" name="aciklama[]" class="form-control aciklama aciklama0 buyukYaz" placeholder="Ürün" autocomplete="off">' +
        '</div>' +
        '<div class="col-2 rw2">' +
          '<input type="text" name="miktar[]" onkeyup="sayiKontrol(this)" class="form-control miktar miktar' + dataNum + '" autocomplete="off">' +
        '</div>' +
        '<div class="col-2 rw3">' +
          '<input type="text" name="fiyat[]" onkeyup="sayiKontrol(this)" class="form-control fiyat fiyat' + dataNum + '" autocomplete="off">' +
        '</div>' +
        '<div class="col-2 rw4">' +
          '<input type="text" name="tutar[]" onkeyup="sayiKontrol(this)" class="form-control tutar tutar' + dataNum + '" autocomplete="off">' +
        '</div>' +
        '<div class="col-1 text-end">' +
          '<button type="button" class="btn btn-danger btn-sm satirSil" title="Satırı Sil"><strong>&times;</strong></button>' +
        '</div>' +
      '</div>';
      
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
            var firmaAdi = firma.firma_adi ? firma.firma_adi.replace(/'/g, "\\'") : '';
            var tel1 = firma.tel1 ? firma.tel1.replace(/'/g, "\\'") : '';
            var tel2 = firma.tel2 ? firma.tel2.replace(/'/g, "\\'") : '';
            var il = firma.il ? firma.il.replace(/'/g, "\\'") : '';
            var ilce = firma.ilce ? firma.ilce.replace(/'/g, "\\'") : '';
            var adres = firma.adres ? firma.adres.replace(/'/g, "\\'") : '';
            var vergiNo = firma.vergiNo ? firma.vergiNo.replace(/'/g, "\\'") : '';
            var vergiDairesi = firma.vergiDairesi ? firma.vergiDairesi.replace(/'/g, "\\'") : '';

            var item = '<li class="list-group-item" style="cursor: pointer; border: none; padding: 10px; margin-bottom: 2px; background: #f8f9fa; border-radius: 4px;" onclick="firmaSec(' + 
                firma.id + ', \'' + firmaAdi + '\', \'' + tel1 + '\', \'' + tel2 + 
                '\', \'' + il + '\', \'' + ilce + '\', \'' + adres + '\', \'' + 
                vergiNo + '\', \'' + vergiDairesi + '\')">' +
                '<div style="font-weight: 600; color: #495057; margin-bottom: 3px;">' + firma.firma_adi + '</div>' +
                '<div style="font-size: 12px; color: #6c757d; line-height: 1.4;">' +
                '<div>Telefon: ' + (firma.tel1 || 'Belirtilmemiş') + '</div>' +
                '<div>' + (firma.il || '') + '/' + (firma.ilce || '') + '</div>' +
                '<div>' + (firma.adres || 'Adres belirtilmemiş') + '</div>' +
                '</div>' +
                '</li>';
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
  });
</script>