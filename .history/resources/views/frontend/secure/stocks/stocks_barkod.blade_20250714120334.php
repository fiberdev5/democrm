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
      justify-content: center;
      align-items: center;
    }

    .barcode-area {
      width: 46mm;
      height: 18mm;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 0;
      margin: 0;
    }

    .barcode-area img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    .text-area {
      font-size: 11pt;
      font-weight: bold;
      text-align: center;
      margin-top: -2mm;
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
