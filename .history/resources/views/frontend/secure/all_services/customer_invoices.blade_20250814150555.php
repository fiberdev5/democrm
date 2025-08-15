<div class="table-responsive" style="margin: 0">
  <!-- Tabloyu sadece veri varsa göster -->
  <table class="table table-hover table-striped" id="teklifTablo" width="100%" cellspacing="0" style="margin: 0; display: none;">
    <thead class="title">
      <tr>
        <th style="padding: 5px 10px;font-size: 12px;width: 70px">Tarih</th>
        <th style="padding: 5px 10px;font-size: 12px;">Genel Toplam</th>
        <th style="padding: 5px 10px;font-size: 12px;">Durum</th>
        <th style="padding: 5px 10px;font-size: 12px;width: 50px"></th>
      </tr>
    </thead>
    <tbody>
      <!-- Teklif verileri buraya gelecek -->
    </tbody>
  </table>

  <!-- Boş durum mesajı -->
  <div id="noTeklif" class="text-center text-muted" style="padding: 40px 20px;">
    <i class="fas fa-file-invoice-dollar fa-3x mb-3" style="font-size: 2.5em; color: #ddd;"></i>
    <p style="font-size: 14px; color: #6c757d; margin: 0;">Henüz fatura eklenmemiş</p>
    <small style="color: #adb5bd;">Bu servis için henüz hiç fatura hazırlanmamıştır</small>
  </div>
</div>

<script>
$(document).ready(function() {
  // Teklif sayısını kontrol eden fonksiyon
  function checkTeklifCount() {
    const teklifCount = $('#teklifTablo tbody tr').length;
    if (teklifCount === 0) {
      $('#teklifTablo').hide();
      $('#noTeklif').show();
    } else {
      $('#teklifTablo').show();
      $('#noTeklif').hide();
    }
  }

  // Sayfa yüklendikten sonra kontrol et
  checkTeklifCount();

  // AJAX ile teklif eklendikten sonra tekrar kontrol et
  // Örnek: Teklif ekleme işlemi başarılı olduğunda
  $(document).on('teklifEklendi', function() {
    checkTeklifCount();
  });

  // Teklif silme işlemi örneği
  $(document).on('click', '.teklifSil', function() {
    // Silme işlemi kodunuz
    // Başarılı silme işlemi sonrası:
    $(this).closest('tr').fadeOut(300, function() {
      $(this).remove();
      checkTeklifCount();
    });
  });
});
</script>