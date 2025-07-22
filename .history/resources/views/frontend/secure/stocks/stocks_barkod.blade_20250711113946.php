<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <style>
    @page {
      margin: 0;
      padding: 0;
      size: 50mm 25mm; /* Kağıt boyutu */
    }
    body {
      margin: 0;
      padding: 0;
      width: 50mm;
      height: 25mm;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: Arial, sans-serif;
    }
    .barcode-area {
      width: 100%;
    }
    .barcode-area svg {
      width: 100%;
      height: 60px; /* Barkod yüksekliği */
      display: block;
      margin: 0 auto;
    }
  </style>
</head>
<body>
  <div class="barcode-area">
    {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 2.5, 60, true) !!}
    {{-- 5.parametre true olursa kod otomatik altına yazılır --}}
  </div>
</body>
</html>
