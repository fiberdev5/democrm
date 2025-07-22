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
      height: 20mm;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .barcode-area svg {
      width: 100%;
      height: 100%;
    }

    .text-area {
      font-size: 10pt;
      font-weight: bold;
      text-align: center;
      margin-top: 1mm;
    }
  </style>
</head>
<body>
  <div class="barcode-area">
    {!! DNS1D::getBarcodeSVG($stock->urunKodu, 'C128', 1.8, 50, 'black', false) !!}
  </div>
  <div class="text-area">
    {{ $stock->urunKodu }}
  </div>
</body>
</html>
