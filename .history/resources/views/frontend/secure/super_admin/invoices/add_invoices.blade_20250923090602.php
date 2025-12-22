 <style>
        .payment-selection {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            max-height: 250px;
            overflow-y: auto;
            flex: 1;
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

        .card {
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .card-header {
            background-color: #f8f9fa;
            font-weight: bold;
            padding: 10px 15px;
        }

        .product-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
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
        }
    </style>


<div class="container mt-4">
        <form method="post" id="addInvo" action="#" enctype="multipart/form-data" class="needs-validation" novalidate>
            <!-- Tarih -->
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <label class="mb-0">Tarih<span style="font-weight: bold; color: red;">*</span></label>
                        </div>
                        <div class="col-auto">
                            <input type="date" name="faturaTarihi" class="form-control" value="2025-01-15" required>
                        </div>
                    </div>
                </div>
            </div> 

            <!-- Firma ve Ödeme Seçimi -->
            <div class="row">
                <!-- FİRMA BİLGİSİ -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">FİRMA BİLGİSİ</div>
                        <div class="card-body">
                            <div class="row form-group">
                                <div class="col-md-3"><label>Firma Ara <span style="font-weight: bold; color: red;">*</span></label></div>
                                <div class="col-md-9">
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
                    <div class="card">
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

            <!-- ÜRÜN BİLGİSİ - Sadece Görüntüleme -->
            <div class="product-section" id="productSection" style="display: none;">
                <h5 class="mb-3">Ürün Bilgisi</h5>
                <div class="product-info">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Ürün Açıklaması</label>
                            <div class="readonly-field" id="productDescription">-</div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Miktar</label>
                            <div class="readonly-field" id="productQuantity">1</div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Fiyat (KDV Hariç)</label>
                            <div class="readonly-field" id="productPrice">0.00</div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tutar</label>
                            <div class="readonly-field" id="productTotal">0.00</div>
                        </div>
                    </div>
                </div>
                
                <!-- Hidden inputs for form submission -->
                <input type="hidden" name="aciklama[]" id="hiddenDescription">
                <input type="hidden" name="miktar[]" id="hiddenQuantity" value="1">
                <input type="hidden" name="fiyat[]" id="hiddenPrice">
                <input type="hidden" name="tutar[]" id="hiddenTotal">
            </div>

            <!-- Ödeme Bilgileri ve Toplam -->           
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4"><label>Ödeme Şekli<span style="font-weight: bold; color: red;">*</span></label></div>
                                <div class="col-md-8">
                                    <select class="form-select" name="odemeSekli" required>
                                        <option value="">Seçiniz</option>
                                        <option value="1">Nakit</option>
                                        <option value="2">Havale</option>
                                        <option value="3">Kredi Kartı</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4"><label>Toplam Yazıyla</label></div>
                                <div class="col-md-8"><input type="text" name="toplamYazi" class="form-control" required></div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4"><label>Fatura No<span style="font-weight: bold; color: red;">*</span></label></div>
                                <div class="col-md-8">
                                    <input type="text" name="faturaNumarasi" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4"><label>E-Arşiv<span style="font-weight: bold; color: red;">*</span></label></div>
                                <div class="col-md-8">
                                    <input type="file" class="form-control" name="document" required>
                                </div>
                            </div>       
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-8"><label>Toplam (KDV Hariç)<span style="font-weight: bold; color: red;">*</span></label></div>
                                <div class="col-md-4"><input type="text" name="toplam" class="form-control toplam" required></div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-8"><label>İndirim</label></div>
                                <div class="col-md-4"><input type="text" name="indirim" class="form-control indirim" value="0.00"></div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-8"><label>Ara Toplam</label></div>
                                <div class="col-md-4"><input type="text" name="araToplam" class="form-control araToplam"></div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-5">
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
                                <div class="col-md-3">
                                    <input type="text" name="kdvTutar" class="form-control kdvTutar" value="20" style="text-align: center;">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="kdv" class="form-control kdv" value="0">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-8"><label>Genel Toplam (KDV Dahil)<span style="font-weight: bold; color: red;">*</span></label></div>
                                <div class="col-md-4"><input type="text" name="genelToplam" class="form-control genelToplam" required></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-sm-12 text-center">
                    <input type="submit" class="btn btn-success" value="Kaydet">
                </div>
            </div>
        </form>
    </div>

    <script>
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
            $('#productSection').hide();
            $('#selectedPaymentType').val('');
            $('#selectedPaymentId').val('');
            
            // Form alanlarını temizle
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

        // Tamamlanmış ödemeleri yükle (demo data)
        function loadCompletedPayments(tenantId) {
            $('#odemeYukleniyor').show();
            $('#odemeListesi').hide();
            
            // Demo data - gerçek uygulamada AJAX ile çekilecek
            setTimeout(function() {
                $('#odemeYukleniyor').hide();
                
                var payments = [
                    {id: 1, type: 'service', amount: 1200.00, description: 'Laptop Tamir Hizmeti', paid_at: '2025-01-15T10:30:00', payment_method: 'Kredi Kartı', currency: 'TL'},
                    {id: 2, type: 'product', amount: 850.00, description: 'SSD Disk Satışı', paid_at: '2025-01-14T14:20:00', payment_method: 'Havale', currency: 'TL'},
                    {id: 3, type: 'service', amount: 500.00, description: 'Telefon Ekran Değişimi', paid_at: '2025-01-13T16:45:00', payment_method: 'Nakit', currency: 'TL'}
                ];
                
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
            }, 1000);
        }

        // Seçilen ödemeden form verilerini otomatik doldur
        function autoFillFormFromPayment(amount, description) {
            // Ürün bilgilerini göster
            $('#productSection').show();
            
            // Ürün açıklamasını doldur
            $('#productDescription').text(description);
            $('#hiddenDescription').val(description);
            
            // Miktar 1 yap
            $('#productQuantity').text('1');
            $('#hiddenQuantity').val('1');
            
            // KDV dahil tutar olduğunu varsayarak KDV'siz tutarı hesapla
            var kdvOrani = parseFloat($('.kdvTutar').val()) || 20;
            var kdvDahilTutar = amount;
            var kdvOraniFaktor = (100 + kdvOrani) / 100;
            var kdvHaricTutar = kdvDahilTutar / kdvOraniFaktor;
            var kdvTutari = kdvDahilTutar - kdvHaricTutar;
            
            // Görünen alanları doldur
            $('#productPrice').text(kdvHaricTutar.toFixed(2) + ' TL');
            $('#productTotal').text(kdvHaricTutar.toFixed(2) + ' TL');
            
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

        $(document).ready(function() {
            // Firma arama (demo)
            $('#firmaArama').on('input', function() {
                const aramaMetni = $(this).val().trim();
                
                if (aramaMetni.length < 2) {
                    $('#firmaListesi').hide();
                    return;
                }

                // Demo firmalar
                var firmalar = [
                    {id: 1, firma_adi: 'ABC Teknoloji Ltd.', tel1: '0212 555 0101', tel2: '', il: 'İstanbul', ilce: 'Kadıköy', adres: 'Test Mahallesi No:123', vergiNo: '1234567890', vergiDairesi: 'Kadıköy VD'},
                    {id: 2, firma_adi: 'XYZ Bilişim A.Ş.', tel1: '0216 444 0202', tel2: '0532 111 2233', il: 'İstanbul', ilce: 'Üsküdar', adres: 'Örnek Sokak No:456', vergiNo: '0987654321', vergiDairesi: 'Üsküdar VD'}
                ];

                firmaListesiGoster(firmalar.filter(f => f.firma_adi.toLowerCase().includes(aramaMetni.toLowerCase())));
            });

            function firmaListesiGoster(firmalar) {
                const liste = $('#firmaListesi');
                liste.empty();

                if (firmalar.length === 0) {
                    liste.append('<li class="list-group-item">Firma bulunamadı</li>');
                } else {
                    firmalar.forEach(function(firma) {
                        var item = '<li class="list-group-item" style="cursor: pointer; border: none; padding: 8px; margin-bottom: 2px; background: #f8f9fa; border-radius: 4px;" onclick="firmaSec(' + 
                            firma.id + ', \'' + firma.firma_adi + '\', \'' + firma.tel1 + '\', \'' + firma.tel2 + 
                            '\', \'' + firma.il + '\', \'' + firma.ilce + '\', \'' + firma.adres + '\', \'' + 
                            firma.vergiNo + '\', \'' + firma.vergiDairesi + '\')">' +
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

            // KDV hesaplama event'leri
            $('.kdvTutar, .indirim').on('keyup change', function() {
                var toplam = Number($(".toplam").val()) || 0;
                if (toplam > 0) {
                    kdvHesapla(toplam);
                }
            });

            $('.toplam').on('keyup change', function() {
                var toplam = Number($(this).val()) || 0;
                kdvHesapla(toplam);
            });

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