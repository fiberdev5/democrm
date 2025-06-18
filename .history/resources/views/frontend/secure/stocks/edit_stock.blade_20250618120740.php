<form method="POST" id="editStockForm" action="{{ route('update.stock', [$firma->id, $stock->id]) }}">
  @csrf
  <!-- Form Alanları (Marka, Cihaz, Raf, Kod, Ad, Adet, Fiyat, Açıklama) -->
</form>