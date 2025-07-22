<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    @page {
      margin: 0;
      padding: 0;
      size: 50mm 25mm; /* Tam etiket boyutu */
    }
    
    * {
      box-sizing: border-box;
    }
    
    body {
      font-family: Arial, sans-serif;
      width: 50mm;
      height: 25mm;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      background: white;
      overflow: hidden;
    }
    
    .barcode-container {
      width: 100%;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 1mm;
    }
    
    .barcode-area {
      width: 48mm;
      height: 18mm;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 1mm;
    }
    
    .barcode-area svg {
      width: 100% !important;
      height: 100% !important;
      max-width: 48mm;
      max-height: 18mm;
    }
    
    .text-area {
      font-size: 8pt;
      font-weight: bold;
      color: #000;
      text-align: center;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      width: 48mm;
      line-height: 1;
    }
    
    /* Alternatif: Daha büyük barkod için */
    .large-barcode .barcode-area {
      height: 20mm;
      margin-bottom: 0.5mm;
    }
    
    .large-barcode .text-area {
      font-size: 6pt;
    }
  </style>
</head>
<body>
  <div class="barcode-container">
    <div class="barcode-area">
      <!-- PHP kodunuzda: DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 2, 60) -->
      <!-- Genişlik: 2, Yükseklik: 60 px -->
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 60">
        <rect width="200" height="60" fill="white"/>
        <g fill="black">
          <rect x="10" y="10" width="2" height="40"/>
          <rect x="15" y="10" width="1" height="40"/>
          <rect x="18" y="10" width="3" height="40"/>
          <rect x="23" y="10" width="1" height="40"/>
          <rect x="26" y="10" width="2" height="40"/>
          <!-- Örnek barkod çizgileri -->
        </g>
      </svg>
    </div>
    <div class="text-area">
      ORNEK-URUN-KODU
    </div>
  </div>
</body>
</html>