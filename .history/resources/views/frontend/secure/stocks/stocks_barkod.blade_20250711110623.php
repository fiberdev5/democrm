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
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: Arial, sans-serif;
      width: 50mm;
      height: 25mm;
      margin: 0;
      padding: 1mm;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      background: white;
    }
    
    .barcode-container {
      width: 100%;
      height: 20mm;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 0.5mm;
    }
    
    .barcode-area {
      width: 100%;
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    
    .barcode-area svg {
      width: 48mm !important;
      height: 18mm !important;
      max-width: none;
      max-height: none;
    }
    
    .text-area {
      width: 100%;
      font-size: 7pt;
      font-weight: bold;
      color: #000;
      text-align: center;
      line-height: 1;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      height: 3mm;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    /* Yazdırma optimizasyonu */
    @media print {
      body {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      
      .barcode-area svg {
        width: 48mm !important;
        height: 18mm !important;
      }
    }
  </style>
</head>
<body>
  <div class="barcode-container">
    <div class="barcode-area">
      {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 2, 50) !!}
    </div>
  </div>
  <div class="text-area">
    {{ $stock->urunKodu }}
  </div>
</body>
</html>