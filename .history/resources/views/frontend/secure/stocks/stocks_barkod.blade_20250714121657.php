<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page { margin: 0; size: 50mm 25mm; }

    body {
      margin: 0;
      padding: 0;
      width: 50mm;
      height: 25mm;
      font-family: Arial, sans-serif;
      display: flex;
      flex-direction: column;
      justify-content: space-between; /* Öğeleri dikeyde üst ve alt kenarlara yasla, aralarına boşluk bırak */
      align-items: center;
      overflow: hidden; /* İçerik taşmasını engelle */
    }

    .barcode-area {
      width: 46mm;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 0;
      margin: 1mm 0; /* Barkod alanı için üstten ve alttan 1mm boşluk */
      flex-grow: 1; /* Mevcut dikey alanı kaplamasını sağla */
    }

    .barcode-area img {
      width: 100%;
      max-height: 100%; /* Resmin maksimum yüksekliğini kapsayıcısına göre ayarla */
      object-fit: contain; /* Resmi oranlarını bozmadan sığdır */
    }

    .text-area {
      font-size: 9pt; /* Ürün kodunun font boyutunu küçült */
      font-weight: bold;
      text-align: center;
      margin: 1mm 0; /* Metin alanı için üstten ve alttan 1mm boşluk */
      line-height: 1; /* Satır yüksekliğini ayarla */
      white-space: nowrap; /* Metnin tek satırda kalmasını sağla */
      overflow: hidden; /* Taşan metni gizle */
      text-overflow: ellipsis; /* Taşan metin yerine üç nokta (...) göster */
    }
  </style>
</head>
<body>
  <div class="barcode-area">
    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($stock->urunKodu, 'C128', 2, 70) }}" alt="Barkod">
  </div>
  <div class="text-area">
    {{ $stock->urunKodu }}
  </div>
</body>
</html>