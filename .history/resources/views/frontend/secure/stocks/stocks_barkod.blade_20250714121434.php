<!doctype html>
<html>
<head>
  <meta charset="utf-8">
<style>
  @page { margin: 0; size: 50mm 25mm; }

  body {
    margin: 0;
    padding: 0;
    width: 50mm;
    height: 25mm;
    font-family: Arial, sans-serif;
    display: flex;
    flex-direction: column;
    justify-content: flex-start; /* İçeriği yukarıdan başlat */
    align-items: center;
    overflow: hidden; /* Taşmaları gizle */
  }

  .barcode-area {
    width: 46mm;
    /* height: 18mm; Bu sabitlemeyi kaldırın, içerik yüksekliğine göre ayarlanır */
    flex-grow: 1; /* Mevcut alanı kaplasın */
    display: flex;
    justify-content: center;
    align-items: flex-end; /* Barkodu aşağıya doğru hizala */
    padding: 0;
    margin: 0;
    margin-top: 1mm; /* Barkod için üstten biraz boşluk */
  }

  .barcode-area img {
    width: 100%;
    max-height: 100%; /* Resmin max yüksekliği */
    object-fit: contain;
  }

  .text-area {
    font-size: 9pt;
    font-weight: bold;
    text-align: center;
    margin-top: 0mm; /* Negatif margini kaldır */
    margin-bottom: 1mm; /* Metin için alttan boşluk */
    line-height: 1; /* Satır yüksekliğini ayarla */
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
