<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page {
      margin: 0;
      padding: 0;
    }
    body {
      font-family: Arial, sans-serif;
      width: 141.7pt;   /* 50mm */
      height: 70.85pt;  /* 25mm */
      margin: 0;
      padding: 6pt;
      text-align: center;
    }
    .barcode-area {
      width: 120px;
      margin: 0 auto;
    }
    .barcode-area svg {
      display: block;
      width: 100%;
      height: 28px;
      margin: 0 auto;
    }
    .text-area {
      font-size: 4pt;
      font-weight: normal;
      color: #333;
      margin-top: 1pt;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  </style>
</head>
<body>
  <div class="barcode-area">
    {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 1.2, 28) !!}
  </div>
  <div class="text-area">
    {{ $stock->urunKodu }}
  </div>
</body>
</html>
