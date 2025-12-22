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
            width: 141.7pt; /* 50mm */
            height: 70.85pt; /* 25mm */
            margin: 0;
            padding: 2pt;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .barcode-area {
            width: 135pt; /* Almost full width minus padding */
            margin: 0 auto;
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .barcode-area svg {
            display: block;
            width: 100%;
            height: 45pt; /* Increased height to fill more space */
            margin: 0 auto;
        }
        
        .text-area {
            font-size: 6pt; /* Slightly larger for better readability */
            font-weight: bold;
            color: #000;
            margin-top: 1pt;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="barcode-area">
        {!! DNS1D::getBarcodeHTML($stock->urunKodu, 'C128', 1.5, 45) !!}
    </div>
    <div class="text-area">
        {{ $stock->urunKodu }}
    </div>
</body>
</html>