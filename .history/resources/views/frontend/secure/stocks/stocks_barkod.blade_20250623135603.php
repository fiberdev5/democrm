<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page {
      margin: 0px;
    }
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      font-size: 9px;
      text-align: center;
    }
    .etiket {
      width: 100%;
      height: 100%;
      padding-top: 3px;
    }
    .barcode {
      margin: 0;
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
