<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page {
      margin: 0;
      padding: 0;
    }
    * {
      margin: 0;
      padding: 0;
    }
    body {
      font-family: Arial, sans-serif;
      width: 141.7pt;
      height: 70.85pt;
      position: relative;
    }
    .etiket {
      width: 141.7pt;
      height: 70.85pt;
      text-align: center;
      padding: 5pt;
      position: relative;
    }
    .barcode-container {
      width: 100%;
      height: 40pt;
      text-align: center;
      margin-top: 3pt;
      margin-bottom: 8pt;
    }
    .product-code {
      width: 100%;
      font-size: 9pt;
      font-weight: bold;
      text-align: center;
      margin-top: 5pt;
      padding: 0 5pt;
    }
  </style>
</head>
<body>
  <div class="etiket">
    <div class="barcode-container">
      {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 1.4, 30) !!}
    </div>
    <div class="product-code">{{ $stock->urunKodu }}</div>
  </div>
</body>
</html>