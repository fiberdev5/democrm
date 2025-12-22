<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <style>
    @page {
      margin: 0;
      padding: 0;
    }
    html, body {
      margin: 0;
      padding: 0;
      width: 141.7pt; /* 50 mm */
      height: 70.85pt; /* 25 mm */
      overflow: hidden;
    }
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100%;
      font-family: Arial, sans-serif;
    }
    .barcode-container {
      width: 100%;
      height: 100%;
    }
    .barcode-container svg {
      width: 100% !important;
      height: 100% !important;
      display: block;
    }
    .code-text {
      display: none; /* Etiket için sadece barkod görünsün istersen */
    }
  </style>
</head>
<body>
  <div class="barcode-container">
    {!! DNS1D::getBarcodeSVG($stock->urunKodu, 'C128', 3, 50) !!}
  </div>
  <div class="code-text">{{ $stock->urunKodu }}</div>
</body>
</html>
