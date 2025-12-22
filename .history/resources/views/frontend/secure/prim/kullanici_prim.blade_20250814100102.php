{{-- Bu dosya, mevcut index.blade.php dosyanızın bir kopyası olup düzenlenmiştir. --}}

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid mt-1" >
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="card-title">Primlerim</h4>
        </div>
        <div class="card-body">
          <!-- Prim Ayarları Özeti (Mevcut kodunuzdaki gibi kalabilir) -->
          <div class="row mb-1">
            {{-- ... Mevcut prim ayarları özeti bölümü ... --}}
          </div>

          {{-- Personel seçim alanı kaldırıldı --}}
          <form id="primForm" class="row g-3 align-items-end">
            @csrf
            <div class="col-md-3">
              <label for="tarih1prim" class="form-label">Başlangıç Tarihi</label>
              <input type="date" class="form-control" id="tarih1prim" name="tarih1prim" required>
            </div>
                        
            <div class="col-md-3">
              <label for="tarih2prim" class="form-label">Bitiş Tarihi</label>
              <input type="date" class="form-control" id="tarih2prim" name="tarih2prim" required>
            </div>
                        
            <div class="col-md-3">
              <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-calculator"></i> Prim Hesapla
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Sonuçlar ve Modal (Mevcut kodunuzdaki gibi kalabilir) -->
  <div class="row mt-1" id="sonuclarContainer" style="display: none;">
    {{-- ... Mevcut sonuçlar tablosu, özet kartları ve modal ... --}}
  </div>
</div>

<!-- Günlük Detay Modal (Mevcut kodunuzdaki gibi kalabilir) -->
<div class="modal fade" id="gunlukDetayModal" ...>
  {{-- ... Mevcut modal içeriği ... --}}
</div>

<!-- Loading Spinner (Mevcut kodunuzdaki gibi kalabilir) -->
<div class="text-center" id="loadingSpinner" ...>
  {{-- ... --}}
</div>

<style>
  {{-- ... Mevcut CSS stilleriniz ... --}}
</style>

<script>
  // Mevcut JavaScript kodunuzu buraya kopyalayın ve AJAX URL'lerini güncelleyin.
  $(document).ready(function() {
    // ... (flatpickr başlatma)

    $('#primForm').on('submit', function(e) {
        e.preventDefault();
        primHesapla();
    });
  });

  function primHesapla() {
    const formData = new FormData($('#primForm')[0]);
    $('#loadingSpinner').show();
    $('#sonuclarContainer').hide();
    
    $.ajax({
      // URL'yi yeni rotanızla değiştirin
      url: '{{ route("prim.kullanici.hesapla") }}',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        $('#loadingSpinner').hide();
        if (response.success) {
          showSonuclar(response.data); // Bu fonksiyon aynı kalabilir
        } else {
          showErrors(response.message); // Bu fonksiyon aynı kalabilir
        }
      },
      error: function(xhr) {
        // ... (hata yönetimi aynı kalabilir)
      }
    });
  }

  // gunlukDetayGoster fonksiyonundaki AJAX URL'ini güncelleyin
  function gunlukDetayGoster(personelId, tarih) {
    // ... (modal'ı gösterme ve loading state)

    $.ajax({
        // URL'yi yeni rotanızla değiştirin
        url: '{{ route("prim.kullanici.detay") }}',
        type: 'GET',
        data: { 
            tarih: tarih // personelId göndermeye gerek yok, controllerda Auth::user() ile alınacak
        },
        // ... (geri kalan AJAX ayarları)
        success: function(response) {
            // ... (başarı durumu aynı kalabilir)
        },
        error: function(xhr) {
            // ... (hata durumu aynı kalabilir)
        }
    });
  }
  
  // showSonuclar, updateOzetKartlari, showErrors, renderModernDetay gibi diğer tüm
  // JavaScript fonksiyonlarınız HİÇBİR DEĞİŞİKLİK YAPILMADAN çalışmaya devam edecektir.
  // Sadece butonun onclick event'inden personelId parametresini kaldırabilirsiniz.
  // Örnek: onclick="gunlukDetayGoster('${sonuc.tarih}')"
  
  // ... (Geri kalan tüm JavaScript fonksiyonlarınızı buraya yapıştırın)
</script>