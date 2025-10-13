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
  .servisiAc{
    color: #fff !important;
    background-color: #f32f53 !important;
  }
  @media (max-width: 767px) {
    .fatura-mobil-add{
    --bs-gutter-x: 2px !important;
  }
  }
  
</style>


<form method="post" id="addInvo" action="{{ route('store.invoices', $firma->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
  @csrf
  <div class="card f5">
    <div class="card-header ch1" style="padding: 3px 10px;">
      <div class="tarihWrap">
        <label style="text-align: left;width: auto;display: inline-block;margin: 0; margin-right: 2px;">Tarih<span style="font-weight: bold; color: red;">*</span></label>
        <input type="date" name="faturaTarihi" class="form-control datepicker kayitTarihi"  value="{{date('Y-m-d')}}" style="width: 150px!important;display: inline-block;background:#fff" required>
      </div>

      <div class="clearfix"></div>
    </div>
  </div> 

  <div class="card f2">
     <div class="card-header">MÜŞTERİ BİLGİSİ</div>
     <div class="card-body">
        <div class="row">
           <div class="col-sm-6 s1">
              <div class="row form-group ">
                <div class="col-md-4 rw1"><label>Servis Ara</label></div>
                <div class="col-md-8 rw2 d-flex flex-wrap align-items-center gap-3">
                    <input id="search" type="text" name="servisid" class="form-control servisid" data-bs-id="" autocomplete="off" placeholder="Servis ID" style="flex: 1 1 auto; max-width: 160px;">

                    <a href="#" target="_blank" class="servisiAc btn btn-outline-danger px-2 py-1"style="font-size: 13px; line-height: 1.3;">Servisi Aç</a>
                </div>
              </div>
              <div class="row form-group">
                 <div class="col-md-4 rw1"><label><span class="musteriAdiSpan">Müşteri Adı</span> <span style="font-weight: bold; color: red;">*</span></label></div>
                 <div class="col-md-8 rw2">
                   <input type="text" name="adSoyad" class="form-control buyukYaz adSoyad" data-id="" autocomplete="off" placeholder="Müşteri Adı">
                    
                </div>
              </div>
              <input type="hidden" name="mid" class="eskiMusteriId" value="">
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
                 <div class="col-sm-2"><label>İl/İlçe</label></div>
                <div class="col-sm-5">
                <select name="il" id="country" class="form-control form-select" style="width:100%!important;">
                    <option value="" selected disabled>-Seçiniz-</option>
                    @foreach($countries as $item)
                    <option value="{{ $item->id }}">{{ $item->name}}</option>
                    @endforeach
                </select>
                </div>
                <div class="col-sm-5">
                <select name="ilce" id="city" class="form-control form-select" style="width:100%!important;">
                    <option value="" selected disabled>-Seçiniz-</option>                              
                </select>
                </div>
              </div>

              <div class="row form-group">
                 <div class="col-md-2 rw1"><label>Adres <span style="font-weight: bold; color: red;font-size:12px;">*</span></label></div>
                 <div class="col-md-10 rw2"><textarea name="adres" class="form-control buyukYaz adres" placeholder="Adres" rows="3" style="resize: none !important"></textarea></div>
              </div>
           </div>
        </div>
     </div>
  </div>

  <div class="card f2">
    <div class="card-body">
      <div class="row form-group head">
        <div class="col-3 rw1 "><label>Cinsi</label></div>
        <div class="col-3 rw2 "><label>Miktar</label></div>
        <div class="col-3 rw3 "><label>Fiyat</label></div>
        <div class="col-3 rw4 "><label>Tutar</label></div>
      </div>

      <div class="satirBody mb-1">
        <div class="row form-group fatura-mobil-add">
          <div class="col-3 rw1 "><input type="text" name="aciklama[]" class="form-control aciklama aciklama0 buyukYaz" placeholder="Ürün" autocomplete="off"></div>
          <div class="col-3 rw2 "><input type="text" name="miktar[]" onkeyup="sayiKontrol(this)" class="form-control miktar miktar0" autocomplete="off"></div>
          <div class="col-3 rw3 "><input type="text" name="fiyat[]" onkeyup="sayiKontrol(this)" class="form-control fiyat fiyat0" autocomplete="off"></div>
          <div class="col-3 rw4 "><input type="text" name="tutar[]" onkeyup="sayiKontrol(this)" class="form-control tutar tutar0" autocomplete="off"></div>
        </div>
      </div>

      <div class="row form-group" style="margin: 0;border: 0;">
        <button type="button" class="col-xs-12 form-control btn btn-primary2 satirEkle" data-id="1" style="color: #fff;display: inline-block;">Satır Ekle</button>
      </div>
    </div>
  </div>
       
  <div class="row cardRow1 fatura-mobil-add">
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
            <div class="col-md-4 rw1"><label>Toplam<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-8 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="toplam" autocomplete="off" class="form-control toplam" required></div>
          </div>

          <div class="row form-group">
          <div class="col-md-4 rw1"><label>İndirim</label></div>
          <div class="col-md-8 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="indirim" autocomplete="off" class="form-control indirim" value="0.00"></div>
        </div>
        <div class="row form-group">
          <div class="col-md-4 rw1"><label>Ara Toplam</label></div>
          <div class="col-md-8 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="araToplam" autocomplete="off" class="form-control araToplam"></div>
        </div>

          <div class="row form-group">
            <div class="col-md-4 rw1"><label>KDV %</label></div>
            <div class="col-md-2 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="kdvTutar" autocomplete="off" class="form-control kdvTutar" value="20" ></div>
            <div class="col-md-6 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="kdv" class="form-control kdv" value="0"></div>
          </div>

          <div class="row form-group" style="padding-bottom: 0">
            <div class="col-md-4 rw1"><label>Genel Toplam<span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-8 rw2"><input type="text" onkeyup="sayiKontrol(this)" name="genelToplam" autocomplete="off" class="form-control genelToplam" required></div>
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
  function sayiKontrol(v) {
    var isNum = /^[0-9-'.']*$/;
    if (!isNum.test(v.value)) { 
      v.value = v.value.replace(/[^0-9-',']/g, "");
    }                   
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
      var kdvTutar = Number($(".kdvTutar").val());
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
          <div class="col-3 rw1">
            <input type="text" name="aciklama[]" class="form-control aciklama aciklama0 buyukYaz" placeholder="Ürün" autocomplete="off">
          </div>
          <div class="col-3 rw2">
            <input type="text" name="miktar[]" onkeyup="sayiKontrol(this)" class="form-control miktar miktar${dataNum}" autocomplete="off">
          </div>
          <div class="col-3 rw3">
            <input type="text" name="fiyat[]" onkeyup="sayiKontrol(this)" class="form-control fiyat fiyat${dataNum}" autocomplete="off">
          </div>
          <div class="col-3 rw4">
            <input type="text" name="tutar[]" onkeyup="sayiKontrol(this)" class="form-control tutar tutar${dataNum}" autocomplete="off">
          </div>

        </div>
      `;  
      $(".satirBody").append(satirClone);
      $(this).attr("data-id", dataNum + 1);
    });
    $(document).on('click', '.satirSil', function () {
      $(this).closest('.satir').remove();
    });
  });
</script>

<script>
  $(document).ready(function () {
    // SERVİS ID -> Müşteri bilgilerini getir
    $('.servisid').on("input", function () {
      var servisId = $(this).val();
        
      // Servisi Aç linkini güncelle
      $(".servisiAc").attr("href", "/{{$firma->id}}/servisler?did=" + servisId);
        
      // Servis ID 2 karakterden fazla ise müşteri bilgilerini getir
      if (servisId.length >= 2) {
        $.ajax({
          url: "{{ route('fatura.musteri.getir', $firma->id) }}", // Bu route'u oluşturmanız gerekiyor
          type: "POST",
          data: {
            servisId: servisId,
            _token: '{{ csrf_token() }}'
          },
          success: function(response) {
            if (response.success && response.data) {
              var musteri = response.data;     
              // Form alanlarını doldur
              $('.adSoyad').val(musteri.adSoyad);
              $('.adres').val(musteri.adres);
              $('.vergiNo').val(musteri.vergiNo);
              $('.vergiDairesi').val(musteri.vergiDairesi);
                        
              // İl ve ilçe seçimi
              if (musteri.il) {
                $('#country').val(musteri.il).trigger('change');
                setTimeout(function() {
                  if (musteri.ilce) {
                    $('#city').val(musteri.ilce);
                  }
                }, 1000);
              }

              // ID'yi sakla
              $('.mid').html("M.No: " + musteri.musteri_id);
              $('.eskiMusteriId').val(musteri.musteri_id);
              $('#alici').val(musteri.musteri_id);

              // Bilgileri göster
              $('.musteriCikart').show();
              $('.musteriCikart .mid').html("Servis ID: " + servisId);
                        
              // Uyarı varsa kaldır
              $('.servis-uyari').remove();
            } else {
              temizleFormAlanlari();
              // Daha önce eklenmiş uyarı varsa kaldır
              $('.servis-uyari').remove();

              // Yeni uyarı mesajı ekle
              $('.servisid').after(`<div class="servis-uyari" style="color:red;margin-top:5px;">Bu servis ID'ye ait müşteri bilgisi bulunamadı.</div>`);
            }
          },
          error: function(xhr, status, error) {
            console.log('Hata:', error);
            temizleFormAlanlari();
          }
        });
      } else {
        // Servis ID çok kısa ise form alanlarını temizle
          temizleFormAlanlari();
      }
    });

    // Form alanlarını temizleme fonksiyonu
    function temizleFormAlanlari() {
      $('.adSoyad').val('');
      $('.adres').val('');
      $('.vergiNo').val('');
      $('.vergiDairesi').val('');
      $('#country').val('').trigger('change');
      $('#city').val('');
      $('.mid').html('');
      $('.eskiMusteriId').val('');
      $('#alici').val('');
      $('.musteriCikart').hide();
      $('.servis-uyari').remove(); 
    }

    // Müşteri çıkart butonuna tıklanınca
    $('.musteriCikart .fa-times-circle').click(function() {
      temizleFormAlanlari();
      $('.servisid').val('');
    });    
    // Müşteri arama (mevcut kod)
    $('#search').keyup(function () {
      var searchField = $('#search').val();
      $('#result').html('');
      if (searchField.length >= 2) {
        $.post("{{ route('fatura.musteri.getir',$firma->id) }}", {
          faturaMusteriGetir: searchField
        }, function (data) {
          $('#result').html('');
          var obj = JSON.parse(data);
          $.each(obj, function (key, value) {
            let tip = value.musteriTipi == "1" ? "Bireysel" : "Kurumsal";
            $('#result').append(
              `<li class="list-group-item link-class" 
                data-id="${value.id}" 
                data-adSoyad="${value.adSoyad}" 
                data-adres="${value.adres}" 
                data-ilce="${value.ilce}" 
                data-il="${value.il}" 
                data-vno="${value.vergiNo}" 
                data-vdairesi="${value.vergiDairesi}">
                <span style="font-weight:500;">Ad Soyad: </span>${value.adSoyad} (${tip})<br>
                <span style="font-weight:500;">Telefon: </span>${value.tel1}<br>
                <span style="font-weight:500;">Adres: </span>${value.adres} - ${value.ilce}/${value.il}<br>
                <span style="font-weight:500;">Cihaz: </span>${value.marka} - ${value.cihaz}
              </li>`
            );
          });
        });
      }
    });

    // Liste öğesi tıklanınca bilgileri form alanlarına doldur (mevcut kod)
    $('#result').on('click', 'li', function () {
      $('.mid').html("M.No: " + $(this).data('id'));
      $('.eskiMusteriId').val($(this).data('id'));
      $('#alici').val($(this).data('id'));
      $('.adSoyad').val($(this).data('adsoyad'));
      $('.adres').val($(this).data('adres'));
      $('.vergiNo').val($(this).data('vno'));
      $('.vergiDairesi').val($(this).data('vdairesi'));
      $('.il').val($(this).data('il')).trigger('change');
      $('#eskiİlce').val($(this).data('ilce'));
      $('#result').html('');
    });

    // İl seçilince ilçeleri getir (mevcut kod)
    $('#country').change(function () {
      let selectedId = $(this).val();
      if (!selectedId) return;
      let citySelect = $('#city');
      citySelect.empty().append(new Option("Yükleniyor...", ""));
      $.get(`/get-states/${selectedId}`, function (data) {
        citySelect.empty().append(new Option("-Seçiniz-", ""));
        $.each(data, function (i, city) {
          citySelect.append(new Option(city.ilceName, city.id));
        });
      }).fail(function () {
        citySelect.empty().append(new Option("Yüklenemedi", ""));
      });
    });

  });
</script>

<script>
$(document).ready(function() {
    $('#addInvo').submit(function(e) {
        e.preventDefault(); // Normal submit'i engelle
        
        // Form validasyonu
        let formIsValid = true;
        $(this).find('input, select, textarea').each(function() {
            if ($(this).prop('required') && !$(this).val()) {
                formIsValid = false;
                $(this).css('border-color', 'red');
            } else {
                $(this).css('border-color', '');
            }
        });

        if (!formIsValid) {
            alert('Lütfen zorunlu alanları doldurun.');
            return;
        }

        // Form data'yı al
        var formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    
                    // Storage warning varsa göster
                    if (response.storage_warning) {
                        alert(response.storage_warning);
                    }
                    
                    window.location.reload();
                } else {
                    alert(response.message || 'Bir hata oluştu');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errorResponse = xhr.responseJSON;
                    if (errorResponse && errorResponse.error_type === 'storage_limit_exceeded') {
                        alert(errorResponse.message);
                    } else if (errorResponse && errorResponse.message) {
                        alert(errorResponse.message);
                    } else {
                        alert('Form doğrulama hatası');
                    }
                } else {
                    alert('Sunucu hatası oluştu');
                }
                // Hata durumunda da sayfayı yenile
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            }
        });
    });
});
</script>

  
