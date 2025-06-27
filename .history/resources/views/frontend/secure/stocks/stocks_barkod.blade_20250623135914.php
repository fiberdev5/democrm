<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
@page {
    margin: 0 !important;
    padding: 0 !important;
}

body, html {
    margin: 0 !important;
    padding: 0 !important;
    height: 100%;
}

.etiket {
    width: 141.7pt;
    height: 70.85pt;
    overflow: hidden;
    padding: 0;
    margin: 0 auto;
}

    .barcode {
      margin: 0;
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
