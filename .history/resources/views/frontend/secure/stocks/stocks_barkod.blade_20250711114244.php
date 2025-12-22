<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Barkod Yazdır</title>
  <style>
    html, body {
      margin: 0;
      padding: 0;
      width: 141.7pt;   /* 50mm */
      height: 70.85pt;  /* 25mm */
    }

    body {
      font-family: Arial, sans-serif;
      text-align: center;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    .barcode-area {
      width: 100%;
    }

    .barcode-area svg {
      width: 100%;
      height: 60px;
    }
  </style>
</head>
<body onload="window.print()">
  <div class="barcode-area">
    {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 2.5, 60, true) !!}
  </div>
</body>
</html>
