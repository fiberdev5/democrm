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
      width: 141.7pt;
      height: 70.85pt;
      margin: 0;
      padding: 8pt;
      text-align: center;
    }
    .barcode-area {
      margin-top: 8pt;
      margin-bottom: 8pt;
    }
    .text-area {
      font-size: 10pt;
      font-weight: bold;
      margin-top: 8pt;
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