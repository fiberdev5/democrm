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
      height: 45pt;
      text-align: center;
      margin-top: 5pt;
      margin-bottom: 3pt;
    }
    .product-code {
      width: 100%;
      font-size: 8pt;
      font-weight: bold;
      text-align: center;
      position: absolute;
      bottom: 3pt;
      left: 0;
    }
  </style>
</head>
<body>
  <div class="etiket">
    <div class="barcode-container">
      {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 1.4, 35) !!}
    </div>
    <div class="product-code">{{ $stock->urunKodu }}</div>
  </div>
</body>
</html>