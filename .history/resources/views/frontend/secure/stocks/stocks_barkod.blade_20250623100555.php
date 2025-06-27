<!doctype html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <title>Barkod Çıktısı</title>
  <style>
    @media print {
      @page {
        size: A4;
        margin: 0;
      }
      
      body {
        margin: 0;
        padding: 0;
      }
    }
    
    body {
      font-family: sans-serif;
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .page {
      text-align: center;
      max-width: 400px;
      padding: 20px;
    }
    
    h1 {
      font-size: 18pt;
      margin-bottom: 30px;
      color: #333;
    }
    
    .barcode {
      margin: 20px auto;
    }
    
    .barcode img {
      width: auto;
      height: 60px;
      max-width: 300px;
    }
    
    .urun-kodu {
      margin-top: 15px;
      font-size: 12pt;
      letter-spacing: 1px;
      color: #666;
      font-weight: 500;
    }
  </style>
</head>
<body>
  <div class="page">
    <h1>{{ $stock->urunAdi }}</h1>
    
    <div class="barcode">
      <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($stock->urunKodu, 'C128', 2, 60) }}" alt="Barkod">
    </div>
    
    <div class="urun-kodu">{{ $stock->urunKodu }}</div>
  </div>
</body>
</html>