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
      padding: 4pt;
      margin: 0 auto;
      overflow: visible;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      align-items: center;
      box-sizing: border-box;
    }
    .barcode {
      margin: 0;
      padding: 0;
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
    }
    .product-code {
      margin: 0;
      padding: 2pt 0;
      line-height: 1;
      font-size: 10px;
      font-weight: bold;
      word-wrap: break-word;
      text-align: center;
      width: 100%;
    }
  </style>
</head>
<body>
  <div class="etiket">
    <div class="barcode">
      {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 1.2, 35) !!}
    </div>
    <div class="product-code">{{ $stock->urunKodu }}</div>
  </div>
</body>
</html>