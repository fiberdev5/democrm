<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Barkod Yazdır</title>
  <style>
    @media print {
      body {
        margin: 0;
        padding: 0;
        width: 50mm;
        height: 25mm;
      }
    }

    body {
      margin: 0;
      padding: 0;
      width: 50mm;
      height: 25mm;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: Arial, sans-serif;
    }

    .barcode-area {
      width: 100%;
      text-align: center;
    }

    .barcode-area svg {
      width: 100%;
      height: 60px;
      display: block;
      margin: 0 auto;
    }
  </style>
</head>
<body onload="window.print();">
  <div class="barcode-area">
    {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 2.5, 60, true) !!}
  </div>
</body>
</html>
