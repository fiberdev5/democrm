<style>
  .payment-selection {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-top: 10px;
    max-height: 250px;
    overflow-y: auto;
    flex: 1; /* Kalan alanı kapla */
  }

  .payment-item {
    background: white;
    border: 1px solid #e3e6f0;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .payment-item:hover {
    border-color: #4e73df;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }

  .payment-item.selected {
    border-color: #4e73df;
    background-color: #f8f9fa;
  }

  .payment-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .payment-details {
    flex: 1;
  }

  .payment-amount {
    font-weight: 600;
    color: #2e59d9;
    font-size: 14px;
  }

  .payment-description {
    color: #5a5c69;
    font-size: 13px;
    margin-bottom: 4px;
  }

  .payment-date {
    color: #858796;
    font-size: 11px;
  }

  .loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  .tooltip-container {
  position: relative;
  display: inline-block;
}

.tooltip-icon {
  display: inline-block;
  width: 16px;
  height: 16px;
  background: #17a2b8;
  color: white;
  border-radius: 50%;
  text-align: center;
  font-size: 10px;
  line-height: 16px;
  cursor: help;
  margin-left: 5px;
}

.tooltip-content {
  visibility: hidden;
  width: 300px;
  background-color: #555;
  color: #fff;
  text-align: left;
  border-radius: 6px;
  padding: 8px;
  position: absolute;
  z-index: 1000;
  top: 125%; 
  left: 50%;
  margin-left: -150px;
  opacity: 0;
  transition: opacity 0.3s;
  font-size: 11px;
  line-height: 1.4;
}

.tooltip-content::after {
  content: "";
  position: absolute;
  bottom: 100%;
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: transparent transparent #555 transparent;
}

.tooltip-container:hover .tooltip-content {
  visibility: visible;
  opacity: 1;
}

.product-section {
  background-color: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 20px;
  border: 1px solid #dee2e6;
  display: none;
}

.product-info {
  background: white;
  padding: 15px;
  border-radius: 6px;
  border: 1px solid #e3e6f0;
}

.readonly-field {
  background-color: #f8f9fa;
  border: 1px solid #e3e6f0;
  padding: 8px 12px;
  border-radius: 4px;
  color: #495057;
  font-weight: 500;
  min-height: 38px;
  display: flex;
  align-items: center;
}
</style>

<form method="post" id="addInvo" action="{{ route('super.admin.invoices.store')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
  @csrf
  <div class="card f5">
    <div class="card-header ch1" style="padding: 3px 10px;">
      <div class="tarihWrap">
        <label style="text-align: left;width: auto;display: inline-block;margin: 0;">Tarih<span style="font-weight: bold; color: red;">*</span></label>
        <input type="date" name="faturaTarihi" class="form-control datepicker kayitTarihi"  value="{{date('Y-m-d')}}" style="width: 100px!important;display: inline-block;background:#fff" required>
      </div>
      <div class="clearfix"></div>
    </div>
  </div> 

  <!-- Firma ve Ödeme Seçimi Yan Yana -->
  <div class="row">
    <!-- FİRMA BİLGİSİ -->
    <div class="col-lg-6">
      <div class="card f2" style="min-height: 106px;">
        <div class="card-header">FİRMA BİLGİSİ</div>
        <div class="card-body">
          <div class="row form-group">
            <div class="col-md-3 rw1"><label>Firma Ara <span style="font-weight: bold; color: red;">*</span></label></div>
            <div class="col-md-9 rw2">
              <input type="text" id="firmaArama" class="form-control" placeholder="Firma adı yazın..." autocomplete="off">
              <ul id="firmaListesi" class="list-group" style="position: absolute; z-index: 1000; width: 97%; display: none;"></ul>
              <input type="hidden" name="firma_id" id="seciliFirmaId" required>
              <div id="seciliFirma" style="display: none; background: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; margin-top: 5px; border-radius: 4px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                  <div>
                    <div style="font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 14px;" id="seciliFirmaAdi"></div>
                    <div style="font-size: 12px; color: #6c757d; line-height: 1.4;" id="seciliFirmaDetay"></div>
                  </div>
                  <span style="cursor: pointer; color: #dc3545; font-size: 16px; font-weight: bold;" onclick="firmaTemizle()" title="Firmayı Temizle">&times;</span>
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

    <!-- ÖDEME SEÇİMİ -->
    <div class="col-lg-6">
      <div class="card f6">
        <div class="card-header">ÖDEME SEÇİMİ</div>
        <div class="card-body">
          <div class="alert alert-info" style="padding: 6px; font-size: 11px;">
            <strong>Bilgi:</strong> Önce bir firma seçin, ardından o firmaya ait tamamlanmış ödemeleri göreceksiniz.
          </div>
          
          <div id="odemeYukleniyor" style="display: none; text-align: center; padding: 15px;">
            <div class="loading-spinner"></div>
            <span style="margin-left: 10px; font-size: 13px;">Ödemeler yükleniyor...</span>
          </div>
          
          <div id="odemeListesi" style="display: none;">
            <h6 style="font-size: 14px; margin-bottom: 10px;">Fatura Oluşturulacak Ödeme:</h6>
            <div id="odemeSecenekleri" class="payment-selection"></div>
          </div>
          
          <!-- Hidden inputs for payment -->
          <input type="hidden" name="payment_type" id="selectedPaymentType">
          <input type="hidden" name="payment_id" id="selectedPaymentId">
        </div>
      </div>
    </div>
  </div>

  <!-- Hidden inputs for form submission -->
  <input type="hidden" name="aciklama[]" id="hiddenDescription">
  <input type="hidden" name="miktar[]" id="hiddenQuantity" value="1">
  <input type="hidden" name="fiyat[]" id="hiddenPrice">
  <input type="hidden" name="tutar[]" id="hiddenTotal">
       
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
            <div class="col-md-8 rw1"><label>Toplam (KDV Hariç)<span style="font-weight: bold; color: red;">*</span></label></div>
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
            <div class="col-md-5 rw1">
              <label>KDV %
                <div class="tooltip-container">
                  <span class="tooltip-icon">i</span>
                  <div class="tooltip-content">
                    <strong>💡 KDV Hesaplama:</strong><br>
                    • Ödeme seçildiğinde: KDV dahil tutar → KDV hariç tutara çevrilir<br>
                    • Tüm alanları manuel değiştirebilirsiniz<br>
                    • KDV oranı değiştirildiğinde otomatik yeniden hesaplanır
                  </div>
                </div>
              </label>
            </div>
            <div class="col-md-3 rw2">
              <input type="text" onkeyup="sayiKontrol(this)" name="kdvTutar" autocomplete="off" class="form-control kdvTutar" value="20" style="text-align: center;" title="KDV oranını değiştirebilirsiniz">
            </div>
            <div class="col-md-4 rw2">
              <input type="text" onkeyup="sayiKontrol(this)" name="kdv" class="form-control kdv" value="0" title="KDV tutarını manuel değiştirebilirsiniz">
            </div>
          </div>

          <div class="row form-group" style="padding-bottom: 0">
            <div class="col-md-8 rw1"><label>Genel Toplam (KDV Dahil)<span style="font-weight: bold; color: red;">*</span></label></div>
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
        '<div>📞 ' + (tel1 || 'Belirtilmemiş') + (tel2 ? ' / ' + tel2 : '') + '</div>' +
        '<div>📍 ' + (il || '') + '/' + (ilce || '') + '</div>' +
        '<div>🏢 ' + (vergiNo || 'Belirtilmemiş') + (vergiDairesi ? ' - ' + vergiDairesi : '') + '</div>' +
        '<div>📧 ' + (adres || 'Adres belirtilmemiş') + '</div>'
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
    
    // Firma seçildiğinde ödemeleri yükle
    loadCompletedPayments(id);
  }

  window.firmaTemizle = function() {
    $('#seciliFirmaId').val('');
    $('#seciliFirma').hide();
    $('#odemeListesi').hide();
    $('#selectedPaymentType').val('');
    $('#selectedPaymentId').val('');
    
    // Form alanlarını temizle
    $('#hiddenDescription').val('');
    $('#hiddenQuantity').val('1');
    $('#hiddenPrice').val('');
    $('#hiddenTotal').val('');
    
    $('.toplam').val('');
    $('.araToplam').val('');
    $('.kdv').val('0');
    $('.genelToplam').val('');
    
    // Hidden inputları da temizle
    $('.vergiNo').val('');
    $('.vergiDairesi').val('');
    $('.tel1').val('');
    $('.tel2').val('');
    $('.il').val('');
    $('.ilce').val('');
    $('.adres').val('');
  }

  // Ödeme seçimi fonksiyonu
  window.selectPayment = function(paymentId, paymentType, amount, description) {
    // Tüm seçeneklerin selected class'ını kaldır
    $('.payment-item').removeClass('selected');
    
    // Seçilen öğeye selected class ekle
    $('#payment-' + paymentType + '-' + paymentId).addClass('selected');
    
    // Hidden inputları güncelle
    $('#selectedPaymentType').val(paymentType);
    $('#selectedPaymentId').val(paymentId);
    
    // Form verilerini otomatik doldur
    autoFillFormFromPayment(amount, description);
  }

  // Tamamlanmış ödemeleri yükle
  function loadCompletedPayments(tenantId) {
    $('#odemeYukleniyor').show();
    $('#odemeListesi').hide();
    
    $.ajax({
      url: '{{ route("super.admin.invoices.payments") }}',
      type: 'GET',
      data: { tenant_id: tenantId },
      success: function(payments) {
        $('#odemeYukleniyor').hide();
        
        if (payments.length === 0) {
          $('#odemeSecenekleri').html('<div class="alert alert-warning" style="padding: 8px; font-size: 12px;">Bu firmaya ait fatura oluşturulmamış tamamlanmış ödeme bulunamadı.</div>');
        } else {
          var html = '';
          payments.forEach(function(payment) {
            var paymentDate = new Date(payment.paid_at).toLocaleDateString('tr-TR');
            var paymentTime = new Date(payment.paid_at).toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
            
            html += '<div class="payment-item" id="payment-' + payment.type + '-' + payment.id + '" onclick="selectPayment(' + payment.id + ', \'' + payment.type + '\', ' + payment.amount + ', \'' + payment.description.replace(/'/g, "\\'") + '\')">';
            html += '  <div class="payment-info">';
            html += '    <div class="payment-details">';
            html += '      <div class="payment-description">' + payment.description + '</div>';
            html += '      <div class="payment-date">' + paymentDate + ' ' + paymentTime + ' - ' + payment.payment_method + '</div>';
            html += '    </div>';
            html += '    <div class="payment-amount">' + payment.amount + ' ' + payment.currency + '</div>';
            html += '  </div>';
            html += '</div>';
          });
          $('#odemeSecenekleri').html(html);
        }
        
        $('#odemeListesi').show();
      },
      error: function() {
        $('#odemeYukleniyor').hide();
        $('#odemeSecenekleri').html('<div class="alert alert-danger" style="padding: 8px; font-size: 12px;">Ödemeler yüklenirken hata oluştu.</div>');
        $('#odemeListesi').show();
      }
    });
  }

  // Seçilen ödemeden form verilerini otomatik doldur (KDV dahil tutarı KDV hariç tutara çevir)
  function autoFillFormFromPayment(amount, description) {
    // Ürün açıklamasını doldur
    $('#hiddenDescription').val(description);
    
    // Miktar 1 yap
    $('#hiddenQuantity').val('1');
    
    // KDV dahil tutar olduğunu varsayarak KDV'siz tutarı hesapla
    var kdvOrani = parseFloat($('.kdvTutar').val()) || 20;
    var kdvDahilTutar = amount;
    var kdvOraniFaktor = (100 + kdvOrani) / 100;
    var kdvHaricTutar = kdvDahilTutar / kdvOraniFaktor;
    var kdvTutari = kdvDahilTutar - kdvHaricTutar;
    
    // Hidden alanları doldur
    $('#hiddenPrice').val(kdvHaricTutar.toFixed(2));
    $('#hiddenTotal').val(kdvHaricTutar.toFixed(2));
    
    // Toplam alanlarını güncelle
    $('.toplam').val(kdvHaricTutar.toFixed(2));
    $('.araToplam').val(kdvHaricTutar.toFixed(2));
    $('.kdv').val(kdvTutari.toFixed(2));
    $('.genelToplam').val(kdvDahilTutar.toFixed(2));
  }

  // KDV hesaplama fonksiyonu
  function kdvHesapla(toplam) {
    var indirim = Number($(".indirim").val()) || 0;
    var kdvTutar = Number($(".kdvTutar").val()) || 0;
    var kdv = ((toplam - indirim) * kdvTutar) / 100;
    var araToplam = toplam - indirim;
    var genelToplam = araToplam + kdv;

    $(".toplam").val(toplam.toFixed(2));
    $(".araToplam").val(araToplam.toFixed(2));
    $(".genelToplam").val(genelToplam.toFixed(2));
    $(".kdv").val(kdv.toFixed(2));
  }

  $('.buyukYaz').keyup(function(){
    this.value = this.value.toUpperCase();
  });
</script>

<script>
  $(document).ready(function (e) {
    // KDV oranı değiştirildiğinde yeniden hesapla
    $('.kdvTutar').on('keyup change', function() {
      var toplam = Number($(".toplam").val()) || 0;
      if (toplam > 0) {
        kdvHesapla(toplam);
      }
    });

    // İndirim değiştirildiğinde yeniden hesapla
    $('.indirim').on('keyup change', function() {
      var toplam = Number($(".toplam").val()) || 0;
      if (toplam > 0) {
        kdvHesapla(toplam);
      }
    });

    // Toplam manuel değiştirildiğinde KDV'yi yeniden hesapla
    $('.toplam').on('keyup change', function() {
      var toplam = Number($(this).val()) || 0;
      kdvHesapla(toplam);
    });

    // Ara toplam manuel değiştirildiğinde genel toplamı hesapla
    $('.araToplam').on('keyup change', function() {
      var araToplam = Number($(this).val()) || 0;
      var kdvTutar = Number($(".kdvTutar").val()) || 0;
      var kdv = (araToplam * kdvTutar) / 100;
      var genelToplam = araToplam + kdv;
      
      $(".kdv").val(kdv.toFixed(2));
      $(".genelToplam").val(genelToplam.toFixed(2));
      $(".toplam").val(araToplam.toFixed(2));
    });

    // KDV tutarı manuel değiştirildiğinde genel toplamı hesapla
    $('.kdv').on('keyup change', function() {
      var kdv = Number($(this).val()) || 0;
      var araToplam = Number($(".araToplam").val()) || 0;
      var genelToplam = araToplam + kdv;
      
      $(".genelToplam").val(genelToplam.toFixed(2));
    });

    // Genel toplam manuel değiştirildiğinde KDV'yi hesapla
    $('.genelToplam').on('keyup change', function() {
      var genelToplam = Number($(this).val()) || 0;
      var araToplam = Number($(".araToplam").val()) || 0;
      var kdv = genelToplam - araToplam;
      
      $(".kdv").val(kdv.toFixed(2));
      
      // KDV oranını da güncelle
      if (araToplam > 0) {
        var kdvOrani = (kdv / araToplam) * 100;
        $(".kdvTutar").val(kdvOrani.toFixed(0));
      }
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

                var item = '<li class="list-group-item" style="cursor: pointer; border: none; padding: 8px; margin-bottom: 2px; background: #f8f9fa; border-radius: 4px;" onclick="firmaSec(' + 
                    firma.id + ', \'' + firmaAdi + '\', \'' + tel1 + '\', \'' + tel2 + 
                    '\', \'' + il + '\', \'' + ilce + '\', \'' + adres + '\', \'' + 
                    vergiNo + '\', \'' + vergiDairesi + '\')">' +
                    '<div style="font-weight: 600; color: #495057; margin-bottom: 2px; font-size: 13px;">' + firma.firma_adi + '</div>' +
                    '<div style="font-size: 11px; color: #6c757d; line-height: 1.3;">' +
                    '<div>📞 ' + (firma.tel1 || 'Belirtilmemiş') + '</div>' +
                    '<div>📍 ' + (firma.il || '') + '/' + (firma.ilce || '') + '</div>' +
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