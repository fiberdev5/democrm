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
      justify-content: space-between;
      align-items: center;
      box-sizing: border-box;
      padding: 2mm;
    }

    .barcode-area {
      width: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .barcode-area img {
      max-width: 100%;
      max-height: 100%;
      display: block;
    }

    .text-area {
      font-size: 8pt;
      font-weight: bold;
      text-align: center;
      line-height: 1;
      width: 100%;
    }
  </style>
</head>
<body>
  <div class="barcode-area">
    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($stock->urunKodu, 'C128', 2, 82) }}" alt="Barkod">
  </div>
  <div class="text-area">
    {{ $stock->urunKodu }}
  </div>
</body>
</html>
