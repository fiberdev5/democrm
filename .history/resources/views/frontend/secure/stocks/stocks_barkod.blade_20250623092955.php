<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <title>Barkod Çıktısı</title>
  <style>
    body {
      font-family: sans-serif;
      text-align: center;
      margin: 0;
      padding: 0;
    }

    .page {
      width: 100%;
      height: 100%;
      padding: 100px 50px;
      box-sizing: border-box;
    }

    h1 {
      font-size: 24pt;
      margin-bottom: 40px;
    }

    .barcode {
      margin: 40px auto;
    }

    .barcode img {
      width: auto;
      height: 100px;
    }

    .urun-kodu {
      margin-top: 20px;
      font-size: 14pt;
      letter-spacing: 2px;
    }
  </style>
</head>
<body>
  <div class="page">
    <h1>{{ $stock->urunAdi }}</h1>

    <div class="barcode">
      <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($stock->urunKodu, 'C128', 3, 100) }}" alt="Barkod">
    </div>

    <div class="urun-kodu">{{ $stock->urunKodu }}</div>
  </div>
</body>
</html>
