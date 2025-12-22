<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page {
      margin: 0;
      padding: 0;
    }
    html, body {
      margin: 0;
      padding: 0;
      width: 141.7pt;
      height: 70.85pt;
    }
    .barcode-area {
      width: 100%;
      height: 100%;
    }
    .barcode-area svg {
      width: 100%;
      height: 100%;
      display: block;
    }
  </style>
</head>
<body>
  <div class="barcode-area">
    {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 1.0, 70) !!}
  </div>
</body>
</html>
