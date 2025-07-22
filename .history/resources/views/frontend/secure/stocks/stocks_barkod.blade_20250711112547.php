<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page {
      margin: 0;
    }
    html, body {
      margin: 0;
      padding: 0;
      width: 141.7pt;
      height: 70.85pt;
    }
    .barcode-wrapper {
      width: 100%;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      font-family: Arial, sans-serif;
    }
    .barcode {
      width: 90%;
    }
    .barcode svg {
      width: 100%;
      height: auto;
    }
    .code-text {
      font-size: 6pt;
      margin-top: 2pt;
    }
  </style>
</head>
<body>
  <div class="barcode-wrapper">
    <div class="barcode">
      {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 1.4, 40) !!}
    </div>
    <div class="code-text">{{ $stock->urunKodu }}</div>
  </div>
</body>
</html>
