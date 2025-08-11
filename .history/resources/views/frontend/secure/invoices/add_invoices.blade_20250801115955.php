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
                  <input id="search" type="text" name="servisid" class="form-control servisid" data-bs-id="" autocomplete="off" placeholder="Servis ID" style="width: 116px;display: inline-block;">
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
                        
                        // İl seçimini yap
                        if (musteri.il) {
                            $('#country').val(musteri.il).trigger('change');
                            
                            // İlçe seçimi için biraz bekle (ajax tamamlansın diye)
                            setTimeout(function() {
                                if (musteri.ilce) {
                                    $('#city').val(musteri.ilce);
                                }
                            }, 1000);
                        }
                        
                        // Müşteri ID'sini sakla
                        $('.mid').html("M.No: " + musteri.musteri_id);
                        $('.eskiMusteriId').val(musteri.musteri_id);
                        $('#alici').val(musteri.musteri_id);
                        
                        // Müşteri bilgilerini göster
                        $('.musteriCikart').show();
                        $('.musteriCikart .mid').html("Servis ID: " + servisId);
                    } else {
                        // Müşteri bulunamadı
                        temizleFormAlanlari();
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
    }

    // Müşteri çıkart butonuna tıklanınca
    $('.musteriCikart .fa-times-circle').click(function() {
        temizleFormAlanlari();
        $('.servisid').val('');
    });

    // Diğer mevcut kodlarınız...
    
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

    // Form validasyonu (mevcut kod)
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
