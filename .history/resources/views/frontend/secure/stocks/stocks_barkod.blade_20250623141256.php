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
      box-sizing: border-box;
    }
    body {
      font-family: Arial, sans-serif;
      width: 141.7pt;
      height: 70.85pt;
      display: table;
    }
    .etiket {
      width: 100%;
      height: 100%;
      display: table-cell;
      vertical-align: middle;
      text-align: center;
      padding: 3pt;
    }
    .barcode-container {
      height: 45pt;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 2pt;
    }
    .product-code {
      height: 15pt;
      font-size: 9pt;
      font-weight: bold;
      line-height: 15pt;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="etiket">
    <div class="barcode-container">
      {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 1.5, 40) !!}
    </div>
    <div class="product-code">{{ $stock->urunKodu }}</div>
  </div>
</body>
</html>