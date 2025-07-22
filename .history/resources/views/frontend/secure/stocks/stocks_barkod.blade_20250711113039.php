<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page {
      margin: 0;
      padding: 0;
      size: 50mm 25mm; /* 50mm genişlik, 25mm yükseklik */
    }
    body {
      font-family: Arial, sans-serif;
      width: 50mm;
      height: 25mm;
      margin: 0;
      padding: 0;
      text-align: center;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }
    .barcode-area {
      width: 100%;
      margin: 0;
      padding: 0;
    }
    .barcode-area svg {
      width: 100%;
      height: 40px; /* Yüksekliği artırdık */
    }
    .text-area {
      font-size: 10pt; /* Yazı fontunu büyüttük */
      font-weight: bold;
      color: #000;
      margin-top: 2pt;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      user-select: none;
    }
  </style>
</head>
<body>
  <div class="barcode-area">
    {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 2.2, 40) !!}
  </div>
  <div class="text-area">
    {{ $stock->urunKodu }}
  </div>
</body>
</html>
