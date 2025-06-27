<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Barkod</title>
  <style>
    body { font-family: sans-serif; text-align: center; margin-top: 50px; }
  </style>
</head>
<body>
  <h3>{{ $stock->urunAdi }}</h3>
  {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 2, 60) !!}
  <p>{{ $stock->urunKodu }}</p>
</body>
</html>
