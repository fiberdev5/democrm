<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body {
      margin: 0;
      padding: 0;
      text-align: center;
      font-family: Arial, sans-serif;
      font-size: 12px;
    }
    .etiket {
      width: 100%;
      height: 100%;
      padding: 10px 0;
    }
    .barcode {
      margin-top: 5px;
    }
  </style>
</head>
<body>
  <div class="etiket">
    <div class="barcode">
      {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 1.5, 40) !!}
    </div>
    <p>{{ $stock->urunKodu }}</p>
  </div>
</body>
</html>
