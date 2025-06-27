<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Barkod</title>
  <style>
    body {
      font-family: sans-serif;
      text-align: center;
      margin-top: 50px;
    }
  </style>
</head>
<body>
  <h2>{{ $stock->urunAdi }}</h2>
  <div>
    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($stock->urunKodu, 'C128') }}" alt="Barkod">
    <p>{{ $stock->urunKodu }}</p>
  </div>
</body>
</html>
