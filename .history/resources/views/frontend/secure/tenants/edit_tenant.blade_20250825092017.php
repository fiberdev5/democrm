<!-- Tenant Detay Modal -->

      <!-- Header -->
      <div class="card-header bg-gradient bg-primary text-white d-flex align-items-center justify-content-between p-3" style="padding: 5px!important;">
        <div class="d-flex align-items-center">
          <svg xmlns="http://www.w3.org/2000/svg" style="margin-left: 10px;"  width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 w-12 h-12 me-3"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path><path d="M10 6h4"></path><path d="M10 10h4"></path><path d="M10 14h4"></path><path d="M10 18h4"></path></svg>
          <div>
            <h4 class="mb-0">{{ $tenant->firma_adi }}</h4>
            @if($tenant->status == 1)
              <span class="badge bg-success"><i class="mdi mdi-check-circle"></i> Aktif</span>
            @else
              <span class="badge bg-danger"><i class="mdi mdi-close-circle"></i> Aktif Değil</span>
            @endif
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <!-- Body -->
      <div class="card-body p-4">
        <div class="row g-4">

          <!-- İletişim Bilgileri -->
          <div class="col-md-6">
            <h6 class="mb-3"><i class="mdi mdi-phone me-2"></i>İletişim Bilgileri</h6>
            <div class="mb-3 d-flex align-items-center">
              <div class="icon bg-light text-primary rounded-circle d-flex justify-content-center align-items-center me-2" style="width:40px; height:40px;">
                <i class="mdi mdi-phone"></i>
              </div>
              <div class="flex-grow-1">
            <small class="text-muted d-block">Telefon Numarası</small>
            <div class="fw-semibold text-dark">{{ $tenant->tel1 ?? $tenant->tel2 }}</div>
          </div>
          <button class="btn btn-sm btn-outline-primary copy-btn" 
                  onclick="copyToClipboard('{{ $tenant->tel1 ?? $tenant->tel }}', this)">
            <i class="mdi mdi-content-copy"></i>
          </button>
            </div>
            <div class="mb-3 d-flex align-items-center">
              <div class="icon bg-light text-danger rounded-circle d-flex justify-content-center align-items-center me-2" style="width:40px; height:40px;">
                <i class="mdi mdi-email"></i>
              </div>
              <div>
                <small class="text-muted">E-Posta</small>
                <div class="fw-semibold">{{ $tenant->eposta }}</div>
              </div>
            </div>
            <div class="d-flex align-items-center">
              <div class="icon bg-light text-info rounded-circle d-flex justify-content-center align-items-center me-2" style="width:40px; height:40px;">
                <i class="mdi mdi-web"></i>
              </div>
              <div >
                <small class="text-muted">Vergi No</small>
                <div class="fw-semibold">{{ $tenant->vergiNo ?? '-' }} </div>
              </div>
              <div class="ms-4">
                <small class="text-muted">Vergi Dairesi</small>
                <div class="fw-semibold">{{ $tenant->vergiDairesi ?? '-'  }}</div>
              </div>
            </div>
          </div>

          <!-- Firma Bilgileri -->
          <div class="col-md-6">
            <h6 class="mb-3"><i class="mdi mdi-office-building me-2"></i>Firma Bilgileri</h6>
            <div class="mb-3 d-flex align-items-center">
              <div class="icon bg-light text-success rounded-circle d-flex justify-content-center align-items-center me-2" style="width:40px; height:40px;">
                <i class="mdi mdi-map-marker"></i>
              </div>
              <div>
                <small class="text-muted">Adres</small>
                <div class="fw-semibold">{{ $tenant->adres }}</div>
              </div>
            </div>
            <div class="mb-3 d-flex align-items-center">
              <div class="icon bg-light text-warning rounded-circle d-flex justify-content-center align-items-center me-2" style="width:40px; height:40px;">
                <i class="mdi mdi-calendar"></i>
              </div>
              <div>
                <small class="text-muted">Kayıt Tarihi</small>
                <div class="fw-semibold">{{ $tenant->created_at->format('d.m.Y H:i') }}</div>
              </div>
              <div class="ms-4">
                <small class="text-muted">Bitiş Tarihi</small>
                <div class="fw-semibold">
                  {{ \Carbon\Carbon::parse($tenant->bitisTarihi)->format('d.m.Y')}}
                </div>
              </div>
            </div>
            <div class="mb-3 d-flex align-items-center">
              <div class="icon bg-light text-secondary rounded-circle d-flex justify-content-center align-items-center me-2" style="width:40px; height:40px;">
                <i class="mdi mdi-account"></i>
              </div>
              <div>
                <small class="text-muted">İletişim Kişisi</small>
                <div class="fw-semibold">{{ $tenant->name ?? '-' }}</div>
              </div>
            </div>
            
          </div>
        </div>
      </div>
      <hr>
      <div class="" style="padding: 10px;">
      <h6 class="mb-3"><i class="fas fa-chart-line me-2"></i>Servis İstatistikleri</h6>

      <div class="period-statistics">
                <!-- Compact Tab Headers -->
                <div class="period-tabs-compact d-flex mb-2">
                    @foreach($periodStats as $key => $period)
                        <button class="period-tab-compact flex-fill {{ $key === 'bugun' ? 'active' : '' }}" 
                                data-period="{{ $key }}" 
                                data-target="#period-{{ $key }}"
                                type="button">
                            <i class="fas fa-calendar-day me-1"></i>
                            {{ $period['label'] }}
                            <span class="badge bg-light text-dark ms-1">{{ $period['toplam'] }}</span>
                        </button>
                    @endforeach
                </div>
                <!-- Accordion Content -->
                <div class="period-accordion">
                    @foreach($periodStats as $key => $period)
                        <div class="collapse {{ $key === 'bugun' ? 'show' : '' }}" 
                             id="period-{{ $key }}" >
                            <div class="card shadow-sm mb-3">
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        <!-- Markalar -->
                                        <div class="col-lg-3 col-md-6">
                                            <div class="card border-secondary">
                                                <div class="card-header bg-secondary text-white text-center">
                                                    <small><i class="fas fa-tags me-1"></i>Markalar</small>
                                                </div>
                                                <div class="card-body p-2">
                                                    @forelse($period['markalar'] as $marka)
                                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                                            <small class="text-truncate">{{ $marka->marka }}</small>
                                                            <span class="badge bg-secondary">{{ $marka->sayi }}</span>
                                                        </div>
                                                    @empty
                                                        <small class="text-muted">Kayıt Yok</small>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Türler -->
                                        <div class="col-lg-3 col-md-6">
                                            <div class="card border-secondary">
                                                <div class="card-header bg-secondary text-white text-center">
                                                    <small><i class="fas fa-cube me-1"></i>Türler</small>
                                                </div>
                                                <div class="card-body p-2">
                                                    @forelse($period['turler'] as $tur)
                                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                                            <small class="text-truncate">{{ $tur->cihaz }}</small>
                                                            <span class="badge bg-secondary">{{ $tur->sayi }}</span>
                                                        </div>
                                                    @empty
                                                        <small class="text-muted">Kayıt Yok</small>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Kaynaklar -->
                                        <div class="col-lg-3 col-md-6">
                                            <div class="card border-secondary">
                                                <div class="card-header bg-secondary text-white text-center">
                                                    <small><i class="fas fa-compass me-1"></i>Kaynaklar</small>
                                                </div>
                                                <div class="card-body p-2">
                                                    @forelse($period['kaynaklar'] as $kaynak)
                                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                                            <small class="text-truncate">{{ $kaynak->kaynak }}</small>
                                                            <span class="badge bg-secondary text-white">{{ $kaynak->sayi }}</span>
                                                        </div>
                                                    @empty
                                                        <small class="text-muted">Kayıt Yok</small>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Operatörler -->
                                        <div class="col-lg-3 col-md-6">
                                            <div class="card border-secondary">
                                                <div class="card-header bg-secondary text-white text-center">
                                                    <small><i class="fas fa-users me-1"></i>Personeller</small>
                                                </div>
                                                <div class="card-body p-2">
                                                    @forelse($period['operatorler'] as $operator)
                                                        <div class="d-flex justify-content-between py-1 border-bottom">
                                                            <small class="text-truncate">{{ $operator->name }}</small>
                                                            <span class="badge bg-secondary">{{ $operator->sayi }}</span>
                                                        </div>
                                                    @empty
                                                        <small class="text-muted">Kayıt Yok</small>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
      </div>
      <!-- Footer -->
      <div class="card-footer bg-light d-flex justify-content-between align-items-center p-3">
        <div>
          <small class="text-muted">Toplam Servis Sayısı</small>
          <div class="fw-bold text-primary fs-5">{{ $topServisSayisi ?? 0 }}</div>
        </div>
        <div>
          <!-- Firmanın durumunu değiştirecek olan form -->
          <form action="{{ route('tenant.changeStatus', [$tenant->id,$tenant->id]) }}" method="POST" style="display: inline;">
              @csrf <!-- Laravel Güvenlik Token'ı -->
              
              @if($tenant->status == 0)
                  <!-- Firma zaten aktifse, pasif yapma butonu göster -->
                  <button type="submit" class="btn btn-success btn-sm">Aktif Et</button>
              @endif
          </form>

          <!-- Sil butonu aynı kalabilir -->
          <button type="button" class="btn btn-danger btn-sm">Sil</button>
        </div>
</div>
  
<script>
$(document).ready(function() {

    // '.period-tab-compact' sınıfına sahip tüm butonlara bir tıklama olayı ata
    $('.period-tab-compact').on('click', function(e) {
        e.preventDefault();

        // Tıklanan butonu bir değişkene alalım
        var $this = $(this);

        // Önce TÜM butonlardan 'active' sınıfını kaldır
        $('.period-tab-compact').removeClass('active');
        // Sonra SADECE tıklanan bu butona 'active' sınıfını ekle
        $this.addClass('active');

        // --- İçeriklerin Görünürlüğünü Yönet ---
        // Tıklanan butonun 'data-target' özelliğindeki değeri al (ör: #period-bugun)
        var targetSelector = $this.data('target');

        // Önce TÜM içerik alanlarından 'show' sınıfını kaldır (hepsini gizle)
        $('.period-accordion .collapse').removeClass('show');
        
        // Sadece hedeflenen içeriğe 'show' sınıfını ekle (görünür yap)
        $(targetSelector).addClass('show');
    });

});
</script>