<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page {
      margin: 0;
      padding: 0;
      size: 50mm 25mm;
    }
    
    body {
      font-family: Arial, sans-serif;
      width: 50mm;
      height: 25mm;
      margin: 0;
      padding: 2mm;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      background: white;
    }
    
    .barcode-area {
      width: 46mm;
      height: 15mm;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 2mm;
      border: 1px solid #ddd; /* Debug için */
    }
    
    .barcode-area svg {
      width: 100% !important;
      height: 100% !important;
    }
    
    .text-area {
      font-size: 10pt;
      font-weight: bold;
      color: #000;
      text-align: center;
      line-height: 1;
    }
    
    /* Eğer SVG çalışmıyorsa, basit çizgiler */
    .manual-barcode {
      width: 46mm;
      height: 15mm;
      background: repeating-linear-gradient(
        to right,
        #000 0px,
        #000 2px,
        #fff 2px,
        #fff 4px
      );
      display: flex;
      align-items: center;
      justify-content: center;
      color: transparent;
    }
  </style>
</head>
<body>
  <div class="barcode-area">
    <!-- PHP'de bu şekilde kullanın -->
    <!-- {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 1.5, 60) !!} -->
    
    <!-- Eğer yukarısı çalışmıyorsa alternatif: -->
    <div class="manual-barcode">|||</div>
  </div>
  <div class="text-area">
    ORNEK-URUN-KODU
  </div>
</body>
</html>