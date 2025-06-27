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
.barcode-area svg {
  width: 120px; /* ya da uygun gördüğün kesin px değeri */
  height: auto;
  display: block;
  margin: 0 auto;
}

    .barcode-area svg {
      width: 100%; /* Barkod svg genişliğini %100 yap */
      height: auto; /* Yüksekliği otomatik */
    }
.text-area {
  font-size: 7pt;      /* Daha küçük yazı boyutu */
  font-weight: normal; /* Normal kalınlık (koyu değil) */
  color: #555555;      /* Açık koyu gri renk, tamamen siyah değil */
  margin-top: 0;
  line-height: 1;
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
