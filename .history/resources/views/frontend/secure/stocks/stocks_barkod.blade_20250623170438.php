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
      width: 141.7pt;  /* Yaklaşık 50mm */
      height: 70.85pt; /* Yaklaşık 25mm */
      margin: 0;
      padding: 8pt;
      text-align: center;
    }
    .barcode-area, .text-area {
      display: block;
      width: 120px;       /* Barkod ve yazı genişliği */
      margin: 0 auto;
      text-align: center;
    }
    .barcode-area svg {
      width: 100%;       /* Kapsayıcı genişliğine göre */
      height: auto;
      display: block;
    }
    .text-area {
      font-size: 4pt;      /* Küçük yazı */
      font-weight: normal; /* Koyu değil */
      color: #555555;      /* Açık koyu gri renk */
      margin-top: 2pt;
      line-height: 1;
      white-space: nowrap; /* Satır kırılmasın */
      overflow: hidden;    /* Taşmayı engelle */
      text-overflow: ellipsis;
    }
  </style>
</head>
<body>
  <div class="barcode-area">
    {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 1.2, 40) !!}
  </div>
  <div class="text-area">
    {{ $stock->urunKodu }}
  </div>
</body>
</html>
