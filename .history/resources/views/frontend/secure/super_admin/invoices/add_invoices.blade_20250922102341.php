<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatura Formu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
  

        .payment-selection {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            max-height: 300px;
            overflow-y: auto;
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
            font-size: 16px;
        }

        .payment-description {
            color: #5a5c69;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .payment-date {
            color: #858796;
            font-size: 12px;
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

        .firma-secili {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-top: 10px;
            border-radius: 8px;
        }

        .firma-detay {
            font-size: 13px;
            color: #6c757d;
            line-height: 1.5;
        }

        .firma-adi {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            font-size: 16px;
        }

        .close-btn {
            cursor: pointer;
            color: #dc3545;
            font-size: 18px;
            font-weight: bold;
        }

        /* Responsive düzenlemeler */
        @media (max-width: 768px) {
            .payment-selection {
                max-height: 200px;
            }
        }

        .info-section {
            height: 100%;
            min-height: 100px;
        }

        .payment-section {
            height: 100%;
            min-height: 100px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <form method="post" id="addInvo" action="#" enctype="multipart/form-data" class="needs-validation" novalidate>
            <!-- Tarih Kartı -->
            <div class="card f5">
                <div class="card-header ch1" style="padding: 3px 10px;">
                    <div class="tarihWrap">
                        <label style="text-align: left;width: auto;display: inline-block;margin: 0;">Tarih<span style="font-weight: bold; color: red;">*</span></label>
                        <input type="date" name="faturaTarihi" class="form-control datepicker kayitTarihi" value="2025-09-22" style="width: 150px!important;display: inline-block;background:#fff" required>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>

            <!-- Firma Bilgisi ve Ödeme Seçimi Yan Yana -->
            <div class="row">
                <!-- Sol Taraf - Firma Bilgisi -->
                <div class="col-lg-6">
                    <div class="card f2 info-section">
                        <div class="card-header">FİRMA BİLGİSİ</div>
                        <div class="card-body">
                            <div class="row form-group">
                                <div class="col-md-3 rw1">
                                    <label>Firma Ara <span style="font-weight: bold; color: red;">*</span></label>
                                </div>
                                <div class="col-md-9 rw2">
                                    <input type="text" id="firmaArama" class="form-control" placeholder="Firma adı yazın..." autocomplete="off">
                                    <ul id="firmaListesi" class="list-group" style="position: absolute; z-index: 1000; width: 97%; display: none;"></ul>
                                    <input type="hidden" name="firma_id" id="seciliFirmaId" required>
                                    
                                    <div id="seciliFirma" style="display: none;" class="firma-secili">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                            <div>
                                                <div class="firma-adi" id="seciliFirmaAdi"></div>
                                                <div class="firma-detay" id="seciliFirmaDetay"></div>
                                            </div>
                                            <span class="close-btn" onclick="firmaTemizle()" title="Firmayı Temizle">&times;</span>
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

                <!-- Sağ Taraf - Ödeme Seçimi -->
                <div class="col-lg-6">
                    <div class="card f6 payment-section">
                        <div class="card-header">ÖDEME SEÇİMİ</div>
                        <div class="card-body">
                            <div class="alert alert-info" style="font-size: 14px; padding: 10px;">
                                <strong>💡 Bilgi:</strong> Önce bir firma seçin, ardından o firmaya ait tamamlanmış ödemeleri göreceksiniz.
                            </div>
                            
                            <div id="odemeYukleniyor" style="display: none; text-align: center; padding: 20px;">
                                <div class="loading-spinner"></div>
                                <span style="margin-left: 10px;">Ödemeler yükleniyor...</span>
                            </div>
                            
                            <div id="odemeListesi" style="display: none;">
                                <h6 style="margin-bottom: 10px;">Fatura Oluşturulacak Ödeme:</h6>
                                <div id="odemeSecenekleri" class="payment-selection"></div>
                            </div>
                            
                            <!-- Hidden inputs for payment -->
                            <input type="hidden" name="payment_type" id="selectedPaymentType">
                            <input type="hidden" name="payment_id" id="selectedPaymentId">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ürün Bilgileri -->
            <div class="card f2">
                <div class="card-body">
                    <div class="row form-group head">
                        <div class="col-5 rw1"><label>Cinsi</label></div>
                        <div class="col-2 rw2"><label>Miktar</label></div>
                        <div class="col-2 rw3"><label>Fiyat (KDV Hariç)</label></div>
                        <div class="col-3 rw4"><label>Tutar</label></div>
                    </div>

                    <div class="satirBody">
                        <div class="row form-group">
                            <div class="col-5 rw1">
                                <input type="text" name="aciklama[]" class="form-control aciklama aciklama0 buyukYaz" placeholder="Ürün" autocomplete="off">
                            </div>
                            <div class="col-2 rw2">
                                <input type="text" name="miktar[]" onkeyup="sayiKontrol(this)" class="form-control miktar miktar0" autocomplete="off">
                            </div>
                            <div class="col-2 rw3">
                                <input type="text" name="fiyat[]" onkeyup="sayiKontrol(this)" class="form-control fiyat fiyat0" autocomplete="off">
                            </div>
                            <div class="col-3 rw4">
                                <input type="text" name="tutar[]" onkeyup="sayiKontrol(this)" class="form-control tutar tutar0" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="row form-group" style="margin: 0;border: 0;">
                        <button type="button" class="col-xs-12 form-control btn btn-primary satirEkle" data-id="1" style="color: #fff;display: inline-block;">Satır Ekle</button>
                    </div>
                </div>
            </div>

            <!-- Alt Kısım - Ödeme Bilgileri ve Hesaplamalar -->
            <div class="row cardRow1">
                <div class="card col-lg-6 f3">
                    <div class="card-body">
                        <div class="row" style="border:0">
                            <div class="col-md-4 rw1">
                                <label>Ödeme Şekli<span style="font-weight: bold; color: red;">*</span></label>
                            </div>
                            <div class="col-md-8 rw2">
                                <select class="form-select odemeSekilleri" name="odemeSekli" required>
                                    <option value="">Seçiniz</option>
                                    <option value="1">Nakit</option>
                                    <option value="2">Kredi Kartı</option>
                                    <option value="3">Havale</option>
                                </select>
                            </div>
                        </div>

                        <div class="row form-group" style="border:0">
                            <div class="col-md-4 rw1"><label>Toplam Yazıyla</label></div>
                            <div class="col-md-8 rw2">
                                <input type="text" name="toplamYazi" autocomplete="off" class="form-control buyukYaz toplamYazi" required>
                            </div>
                        </div>

                        <div class="row form-group" style="border:0">
                            <div class="col-md-4 rw1">
                                <label>Fatura No<span style="font-weight: bold; color: red;">*</span></label>
                            </div>
                            <div class="col-md-8 rw2">
                                <input type="text" name="faturaNumarasi" class="form-control buyukYaz faturaNumarasi" value="" required>
                            </div>
                        </div>

                        <div class="row form-group" style="border:0">
                            <div class="col-md-4 rw1">
                                <label>E-Arşiv<span style="font-weight: bold; color: red;">*</span></label>
                            </div>
                            <div class="col-md-8 rw2">
                                <input type="file" class="form-control" name="document" id="customFile" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card col-lg-6 f4">
                    <div class="card-body" style="padding:17px 5px">
                        <div class="row form-group">
                            <div class="col-md-8 rw1">
                                <label>Toplam (KDV Hariç)<span style="font-weight: bold; color: red;">*</span></label>
                            </div>
                            <div class="col-md-4 rw2">
                                <input type="text" onkeyup="sayiKontrol(this)" name="toplam" autocomplete="off" class="form-control toplam" required>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-8 rw1"><label>İndirim</label></div>
                            <div class="col-md-4 rw2">
                                <input type="text" onkeyup="sayiKontrol(this)" name="indirim" autocomplete="off" class="form-control indirim" value="0.00">
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-8 rw1"><label>Ara Toplam</label></div>
                            <div class="col-md-4 rw2">
                                <input type="text" onkeyup="sayiKontrol(this)" name="araToplam" autocomplete="off" class="form-control araToplam">
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-5 rw1"><label>KDV %</label></div>
                            <div class="col-md-3 rw2">
                                <input type="text" onkeyup="sayiKontrol(this)" name="kdvTutar" autocomplete="off" class="form-control kdvTutar" value="20" style="text-align: center;" title="KDV oranını değiştirebilirsiniz">
                            </div>
                            <div class="col-md-4 rw2">
                                <input type="text" onkeyup="sayiKontrol(this)" name="kdv" class="form-control kdv" value="0" title="KDV tutarını manuel değiştirebilirsiniz">
                            </div>
                        </div>

                        <div class="row form-group" style="padding-bottom: 0">
                            <div class="col-md-8 rw1">
                                <label>Genel Toplam (KDV Dahil)<span style="font-weight: bold; color: red;">*</span></label>
                            </div>
                            <div class="col-md-4 rw2">
                                <input type="text" onkeyup="sayiKontrol(this)" name="genelToplam" autocomplete="off" class="form-control genelToplam" required>
                            </div>
                        </div>

                        <!-- KDV Bilgi Notu -->
                        <div class="row form-group" style="margin-top: 15px;">
                            <div class="col-12">
                                <div class="alert alert-info" style="font-size: 12px; padding: 8px; margin: 0;">
                                    <strong>💡 KDV Hesaplama:</strong>
                                    <br>• Ödeme seçildiğinde: KDV dahil tutar → KDV hariç tutara çevrilir
                                    <br>• Tüm alanları manuel değiştirebilirsiniz
                                    <br>• KDV oranı değiştirildiğinde otomatik yeniden hesaplanır
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 gonderBtn">
                    <input type="submit" class="btn btn-info waves-effect waves-light" value="Kaydet">
                </div>
            </div>
        </form>
    </div>

    <script>
        // Demo veri için örnek firmalar
        const demoFirmalar = [
            {
                id: 1,
                firma_adi: "ABC Teknoloji Ltd.",
                tel1: "0212 555 0101",
                tel2: "0532 555 0101",
                il: "İstanbul",
                ilce: "Kadıköy",
                adres: "Moda Mahallesi, Örnek Sokak No:123",
                vergiNo: "1234567890",
                vergiDairesi: "Kadıköy"
            },
            {
                id: 2,
                firma_adi: "XYZ İnşaat A.Ş.",
                tel1: "0312 555 0202",
                tel2: "",
                il: "Ankara",
                ilce: "Çankaya",
                adres: "Kızılay Mahallesi, Test Caddesi No:456",
                vergiNo: "9876543210",
                vergiDairesi: "Çankaya"
            },
            {
                id: 3,
                firma_adi: "DEF Danışmanlık",
                tel1: "0232 555 0303",
                tel2: "0545 555 0303",
                il: "İzmir",
                ilce: "Konak",
                adres: "Alsancak Mahallesi, Demo Bulvarı No:789",
                vergiNo: "5555555555",
                vergiDairesi: "Konak"
            }
        ];

        // Demo ödemeler
        const demoPayments = {
            1: [
                {
                    id: 101,
                    type: 'service',
                    amount: 2400,
                    currency: '₺',
                    description: 'Web Sitesi Geliştirme Hizmeti',
                    paid_at: '2025-09-20T14:30:00',
                    payment_method: 'Banka Havalesi'
                },
                {
                    id: 102,
                    type: 'product',
                    amount: 1800,
                    currency: '₺',
                    description: 'Yazılım Lisansı',
                    paid_at: '2025-09-18T09:15:00',
                    payment_method: 'Kredi Kartı'
                }
            ],
            2: [
                {
                    id: 201,
                    type: 'construction',
                    amount: 15000,
                    currency: '₺',
                    description: 'İnşaat Projesi 1. Etap',
                    paid_at: '2025-09-15T16:45:00',
                    payment_method: 'Banka Havalesi'
                }
            ],
            3: [
                {
                    id: 301,
                    type: 'consulting',
                    amount: 3600,
                    currency: '₺',
                    description: 'Yönetim Danışmanlığı',
                    paid_at: '2025-09-19T11:20:00',
                    payment_method: 'Nakit'
                }
            ]
        };

        // Global fonksiyonlar
        window.sayiKontrol = function(v) {
            var isNum = /^[0-9-'.']*$/;
            if (!isNum.test(v.value)) { 
                v.value = v.value.replace(/[^0-9-',']/g, "");
            }                   
        }

        window.firmaSec = function(id, firmaAdi, tel1, tel2, il, ilce, adres, vergiNo, vergiDairesi) {
            $('#seciliFirmaId').val(id);
            $('#seciliFirmaAdi').text(firmaAdi);
            $('#seciliFirmaDetay').html(
                '<div>📞 Telefon: ' + (tel1 || 'Belirtilmemiş') + (tel2 ? ' / ' + tel2 : '') + '</div>' +
                '<div>📍 Konum: ' + (il || '') + '/' + (ilce || '') + '</div>' +
                '<div>🏢 Vergi: ' + (vergiNo || 'Belirtilmemiş') + (vergiDairesi ? ' - ' + vergiDairesi : '') + '</div>' +
                '<div>📧 Adres: ' + (adres || 'Adres belirtilmemiş') + '</div>'
            );
            
            // Hidden inputları doldur
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
            
            // Ödemeleri yükle
            loadCompletedPayments(id);
        }

        window.firmaTemizle = function() {
            $('#seciliFirmaId').val('');
            $('#seciliFirma').hide();
            $('#odemeListesi').hide();
            $('#selectedPaymentType').val('');
            $('#selectedPaymentId').val('');
            
            // Form alanlarını temizle
            $('.aciklama0').val('');
            $('.miktar0').val('');
            $('.fiyat0').val('');
            $('.tutar0').val('');
            $('.toplam').val('');
            $('.araToplam').val('');
            $('.kdv').val('0');
            $('.genelToplam').val('');
            
            // Hidden inputları temizle
            $('.vergiNo').val('');
            $('.vergiDairesi').val('');
            $('.tel1').val('');
            $('.tel2').val('');
            $('.il').val('');
            $('.ilce').val('');
            $('.adres').val('');
        }

        window.selectPayment = function(paymentId, paymentType, amount, description) {
            $('.payment-item').removeClass('selected');
            $('#payment-' + paymentType + '-' + paymentId).addClass('selected');
            
            $('#selectedPaymentType').val(paymentType);
            $('#selectedPaymentId').val(paymentId);
            
            autoFillFormFromPayment(amount, description);
        }

        function loadCompletedPayments(tenantId) {
            $('#odemeYukleniyor').show();
            $('#odemeListesi').hide();
            
            // Demo veri kullanıyoruz
            setTimeout(function() {
                $('#odemeYukleniyor').hide();
                
                const payments = demoPayments[tenantId] || [];
                
                if (payments.length === 0) {
                    $('#odemeSecenekleri').html('<div class="alert alert-warning">Bu firmaya ait fatura oluşturulmamış tamamlanmış ödeme bulunamadı.</div>');
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
            }, 1000); // Demo için 1 saniye bekleme
        }

        function autoFillFormFromPayment(amount, description) {
            $('.aciklama0').val(description);
            $('.miktar0').val('1');
            
            var kdvOrani = parseFloat($('.kdvTutar').val()) || 20;
            var kdvDahilTutar = amount;
            var kdvOraniFaktor = (100 + kdvOrani) / 100;
            var kdvHaricTutar = kdvDahilTutar / kdvOraniFaktor;
            var kdvTutari = kdvDahilTutar - kdvHaricTutar;
            
            $('.fiyat0').val(kdvHaricTutar.toFixed(2));
            $('.tutar0').val(kdvHaricTutar.toFixed(2));
            $('.toplam').val(kdvHaricTutar.toFixed(2));
            $('.araToplam').val(kdvHaricTutar.toFixed(2));
            $('.kdv').val(kdvTutari.toFixed(2));
            $('.genelToplam').val(kdvDahilTutar.toFixed(2));
        }

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

        // Firma arama
        let firmaAramaTimeout;
        $('#firmaArama').on('input', function() {
            const aramaMetni = $(this).val().trim().toLowerCase();
            
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
            // Demo veri ile arama
            const bulunanFirmalar = demoFirmalar.filter(firma => 
                firma.firma_adi.toLowerCase().includes(aramaMetni)
            );
            firmaListesiGoster(bulunanFirmalar);
        }

        function firmaListesiGoster(firmalar) {
            const liste = $('#firmaListesi');
            liste.empty();

            if (firmalar.length === 0) {
                liste.append('<li class="list-group-item">Firma bulunamadı</li>');
            } else {
                firmalar.forEach(function(firma) {
                    var firmaAdi = firma.firma_adi.replace(/'/g, "\\'");
                    var tel1 = (firma.tel1 || '').replace(/'/g, "\\'");
                    var tel2 = (firma.tel2 || '').replace(/'/g, "\\'");
                    var il = (firma.il || '').replace(/'/g, "\\'");
                    var ilce = (firma.ilce || '').replace(/'/g, "\\'");
                    var adres = (firma.adres || '').replace(/'/g, "\\'");
                    var vergiNo = (firma.vergiNo || '').replace(/'/g, "\\'");
                    var vergiDairesi = (firma.vergiDairesi || '').replace(/'/g, "\\'");

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

        // Event listeners
        $(document).ready(function() {
            var sonucToplam = 0;
            var sonuc = 0;

            // Büyük harf dönüştürme
            $('.buyukYaz').keyup(function(){
                this.value = this.value.toUpperCase();
            });

            $('.satirBody').on('keyup', '.buyukYaz', function () {
                this.value = this.value.toUpperCase();
            });

            // Satır ekleme ve hesaplama
            $('.satirBody').keyup(function() {
                sonucToplam = 0;
                $('.miktar').each(function(index, data) {
                    var fiyat = Number($(".fiyat"+index).val()) || 0;
                    var miktar = Number($(this).val()) || 0;
                    sonuc = fiyat * miktar;
                    sonucToplam = sonucToplam + sonuc;
                    $(".tutar"+index).val(sonuc.toFixed(2));
                });
                kdvHesapla(sonucToplam);
            });

            // KDV hesaplamaları
            $('.kdvTutar').on('keyup change', function() {
                var toplam = Number($(".toplam").val()) || 0;
                if (toplam > 0) {
                    kdvHesapla(toplam);
                }
            });

            $('.indirim').on('keyup change', function() {
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

            $('.kdv').on('keyup change', function() {
                var kdv = Number($(this).val()) || 0;
                var araToplam = Number($(".araToplam").val()) || 0;
                var genelToplam = araToplam + kdv;
                
                $(".genelToplam").val(genelToplam.toFixed(2));
            });

            $('.genelToplam').on('keyup change', function() {
                var genelToplam = Number($(this).val()) || 0;
                var araToplam = Number($(".araToplam").val()) || 0;
                var kdv = genelToplam - araToplam;
                
                $(".kdv").val(kdv.toFixed(2));
                
                if (araToplam > 0) {
                    var kdvOrani = (kdv / araToplam) * 100;
                    $(".kdvTutar").val(kdvOrani.toFixed(0));
                }
            });

            // Satır ekleme
            $(".satirEkle").click(function () {
                var dataNum = Number($(this).attr("data-id")); 
                var satirClone = '<div class="row form-group align-items-center satir">' +
                    '<div class="col-5 rw1">' +
                    '<input type="text" name="aciklama[]" class="form-control aciklama aciklama' + dataNum + ' buyukYaz" placeholder="Ürün" autocomplete="off">' +
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

            // Satır silme
            $(document).on('click', '.satirSil', function () {
                $(this).closest('.satir').remove();
                sonucToplam = 0;
                $('.miktar').each(function(index, data) {
                    var fiyat = Number($(".fiyat"+index).val()) || 0;
                    var miktar = Number($(this).val()) || 0;
                    sonuc = fiyat * miktar;
                    sonucToplam = sonucToplam + sonuc;
                });
                kdvHesapla(sonucToplam);
            });

            // Dışarı tıklayınca firma listesini kapat
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
</body>
</html>