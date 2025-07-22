<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page {
      margin: 0;
    }

    html, body {
      margin: 0;
      padding: 0;
      width: 141.7pt;
      height: 70.85pt;
    }

    body {
      font-family: Arial, sans-serif;
      text-align: center;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    .barcode-area {
      width: 100%;
      height: 60%;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .barcode-area svg {
      width: 100%;
      height: 100%;
    }

    .text-area {
      font-size: 6pt;
      margin-top: 1pt;
      width: 100%;
      text-align: center;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
    }
  </style>
</head>
<body>
  <div class="barcode-area">
    {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 1.4, 30) !!}
  </div>
  <div class="text-area">
    {{ $stock->urunKodu }}
  </div>
</body>
</html>
