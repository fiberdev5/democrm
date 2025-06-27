<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page {
      margin: 0 !important;
      padding: 0 !important;
    }
    html, body {
      margin: 0 !important;
      padding: 0 !important;
      height: 100%;
      font-family: Arial, sans-serif;
      font-size: 9px;
      text-align: center;
    }
    .etiket {
      width: 141.7pt;
      height: 70.85pt;
      padding: 0;
      margin: 0 auto;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      gap: 2px; /* aradaki boşluk */
    }
    .barcode {
      margin: 0;
      padding: 0;
    }
    .barcode + div {
      margin-top: 0; /* barkod altındaki yazının üst boşluğu */
      line-height: 1;
    }
  </style>
</head>
<body>
  <div class="etiket">
    <div class="barcode">
      {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 0.9, 25) !!}
    </div>
    <div>{{ $stock->urunKodu }}</div>
  </div>
</body>
</html>
