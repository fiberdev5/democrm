<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page {
      margin: 0 !important;
      padding: 0 !important;
    }
    html, body {
      margin: 0 !important;
      padding: 0 !important;
      height: 100%;
      font-family: Arial, sans-serif;
      font-size: 8px;
      text-align: center;
    }
    .etiket {
      width: 141.7pt;
      height: 70.85pt;
      padding: 2pt;
      margin: 0 auto;
      overflow: visible;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      gap: 1pt;
      box-sizing: border-box;
    }
    .barcode {
      margin: 0;
      padding: 0;
      flex-shrink: 0;
      max-width: 100%;
    }
    .barcode svg {
      max-width: 100%;
      height: auto;
    }
    .product-code {
      margin-top: 1pt;
      line-height: 1.2;
      font-size: 7px;
      font-weight: bold;
      word-wrap: break-word;
      max-width: 100%;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="etiket">
    <div class="barcode">
      {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 0.8, 22) !!}
    </div>
    <div class="product-code">{{ $stock->urunKodu }}</div>
  </div>
</body>
</html>