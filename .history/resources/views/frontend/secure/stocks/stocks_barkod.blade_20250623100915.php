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
      position: relative;
    }

    .tarih {
      position: absolute;
      top: 10px;
      right: 20px;
      font-size: 14px;
      color: #555;
    }
  </style>
</head>
<body>
  <div class="tarih">{{ date('d.m.Y H:i') }}</div>

  <h2>{{ $stock->urunAdi }}</h2>
  <div>
    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($stock->urunKodu, 'C128') }}" alt="Barkod">
    <p>{{ $stock->urunKodu }}</p>
  </div>
</body>
</html>
