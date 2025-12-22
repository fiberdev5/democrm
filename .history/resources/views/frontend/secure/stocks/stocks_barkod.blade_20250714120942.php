<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { 
            margin: 0; 
            size: 50mm 25mm; 
        }
        
        body {
            margin: 0;
            padding: 1mm;
            width: 50mm;
            height: 25mm;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-sizing: border-box;
        }
        
        .barcode-area {
            width: 48mm;
            height: 18mm;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
            margin: 0;
            margin-bottom: 1mm;
        }
        
        .barcode-area img {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
        }
        
        .text-area {
            font-size: 8pt;
            font-weight: bold;
            text-align: center;
            line-height: 1;
            width: 48mm;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="barcode-area">
        <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($stock->urunKodu, 'C128', 2, 70) }}" alt="Barkod">
    </div>
    <div class="text-area">
        {{ $stock->urunKodu }}
    </div>
</body>
</html>