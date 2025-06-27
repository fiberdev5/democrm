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
 <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($stock->urunKodu, 'C128') }}" alt="Barkod">

  <p>{{ $stock->urunKodu }}</p>
</body>
</html>
