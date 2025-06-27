<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      text-align: center;
      font-size: 9px;
    }
    .etiket {
      padding-top: 3px;
    }
    .barcode {
      margin: 0 auto;
    }
  </style>
</head>
<body>
  <div class="etiket">
    <div class="barcode">
      {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 1.0, 30) !!}
    </div>
    <div>{{ $stock->urunKodu }}</div>
  </div>
</body>
</html>
