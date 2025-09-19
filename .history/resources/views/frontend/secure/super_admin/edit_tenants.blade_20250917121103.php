<!-- Tenant Detay Modal -->
<!-- Header -->
<div class="card-header bg-primary text-white d-flex align-items-center justify-content-between p-3">
  <div class="d-flex align-items-center">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-3">
      <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path>
      <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path>
      <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path>
      <path d="M10 6h4"></path>
      <path d="M10 10h4"></path>
      <path d="M10 14h4"></path>
      <path d="M10 18h4"></path>
    </svg>
    <div>
      <h4 class="mb-0">{{ $tenant->firma_adi }}</h4>
      @if($tenant->status == 1)
        <span class="badge bg-success">Aktif</span>
      @else
        <span class="badge bg-secondary">Aktif Değil</span>
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
      <div class="border rounded p-3 h-100">
        <h6 class="mb-3 fw-semibold text-dark">İletişim Bilgileri</h6>
        
        <div class="mb-3">
          <small class="text-muted d-block">Telefon Numarası</small>
          <div class="d-flex align-items-center justify-content-between">
            <span class="fw-medium">{{ $tenant->tel1 ?? $tenant->tel2 }}</span>
            <button class="btn btn-sm btn-outline-secondary copy-btn" 
                    onclick="copyToClipboard('{{ $tenant->tel1 ?? $tenant->tel }}', this)">
              Kopyala
            </button>
          </div>
        </div>
        
        <div class="mb-3">
          <small class="text-muted d-block">E-Posta</small>
          <span class="fw-medium">{{ $tenant->eposta }}</span>
        </div>
        
        <div class="row">
          <div class="col-6">
            <small class="text-muted d-block">Vergi No</small>
            <span class="fw-medium">{{ $tenant->vergiNo ?? '-' }}</span>
          </div>
          <div class="col-6">
            <small class="text-muted d-block">Vergi Dairesi</small>
            <span class="fw-medium">{{ $tenant->vergiDairesi ?? '-' }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Firma Bilgileri -->
    <div class="col-md-6">
      <div class="border rounded p-3 h-100">
        <h6 class="mb-3 fw-semibold text-dark">Firma Bilgileri</h6>
        
        <div class="mb-3">
          <small class="text-muted d-block">Adres</small>
          <span class="fw-medium">{{ $tenant->adres }}</span>
        </div>
        
        <div class="row mb-3">
          <div class="col-6">
            <small class="text-muted d-block">Kayıt Tarihi</small>
            <span class="fw-medium">{{ $tenant->created_at->format('d.m.Y H:i') }}</span>
          </div>
          <div class="col-6">
            <small class="text-muted d-block">Bitiş Tarihi</small>
            <span class="fw-medium">{{ \Carbon\Carbon::parse($tenant->bitisTarihi)->format('d.m.Y') }}</span>
          </div>
        </div>
        
        <div>
          <small class="text-muted d-block">İletişim Kişisi</small>
          <span class="fw-medium">{{ $tenant->name ?? '-' }}</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Abonelik Bilgileri -->
  <div class="border rounded p-3 mt-4">
    <h6 class="mb-3 fw-semibold text-dark">Abonelik Bilgileri</h6>
    
    @php
      // Tenant'ın subscription_status'una göre kontrol et
      $currentSubscription = null;
      $isTrialFromTenant = false;
      
      // Tenant aktif mi kontrol et (status = 1)
      if ($tenant->status == 1) {
          // Firma aktifse, subscription durumunu kontrol et
          if ($tenant->subscription_status === 'trial' && $tenant->trial_ends_at && $tenant->trial_ends_at->isFuture()) {
              $isTrialFromTenant = true;
              // Trial durumunda currentSubscription kullan
              $currentSubscription = $tenant->currentSubscription;
          } else if ($tenant->activeSubscription) {
              // Active subscription var
              $currentSubscription = $tenant->activeSubscription;
          } else {
              // Son aboneliği göster
              $currentSubscription = $tenant->currentSubscription;
          }
      } else {
          // Firma pasifse de bilgileri gösterebiliriz
          $currentSubscription = $tenant->currentSubscription;
      }
    @endphp

    @if($isTrialFromTenant)
      <!-- Trial durumu -->
      <div class="bg-light rounded p-3">
        <div class="row align-items-center">
          <div class="col-md-3">
            <h5 class="mb-0">Ücretsiz Deneme</h5>
            <small class="text-muted">Deneme Süresi</small>
          </div>
          <div class="col-md-2 text-center">
            <span class="badge bg-info">Deneme</span>
          </div>
          <div class="col-md-2 text-center">
            <div class="fw-bold">Ücretsiz</div>
            <small class="text-muted">Fiyat</small>
          </div>
          <div class="col-md-2 text-center">
            <div class="fw-bold">{{ $tenant->trial_ends_at->diffInDays(now()) }} gün</div>
            <small class="text-muted">Kalan süre</small>
          </div>
          <div class="col-md-3 text-center">
            <div class="fw-bold">{{ $tenant->trial_ends_at->format('d.m.Y') }}</div>
            <small class="text-muted">Bitiş tarihi</small>
          </div>
        </div>
      </div>
      
      <!-- Deneme Paketi Özellikleri -->
      <div class="mt-3">
        <h6 class="mb-2 fw-medium">Paket Özellikleri</h6>
        <div class="row g-3">
          <div class="col-md-3 col-6">
            <div class="text-center p-2 bg-light rounded">
              <div class="fw-semibold">
                @if($tenant->personelSayisi == -1)
                  Sınırsız
                @elseif($tenant->personelSayisi)
                  {{ $tenant->personelSayisi }}
                @else
                  Belirsiz
                @endif
              </div>
              <small class="text-muted">Kullanıcı</small>
            </div>
          </div>
          <div class="col-md-3 col-6">
            <div class="text-center p-2 bg-light rounded">
              <div class="fw-semibold">
                @if($tenant->bayiSayisi == -1)
                  Sınırsız
                @elseif($tenant->bayiSayisi)
                  {{ $tenant->bayiSayisi }}
                @else
                  Belirsiz
                @endif
              </div>
              <small class="text-muted">Bayi</small>
            </div>
          </div>
          <div class="col-md-3 col-6">
            <div class="text-center p-2 bg-light rounded">
              <div class="fw-semibold">
                @if($tenant->stokSayisi == -1)
                  Sınırsız
                @elseif($tenant->stokSayisi)
                  {{ $tenant->stokSayisi }}
                @else
                  Belirsiz
                @endif
              </div>
              <small class="text-muted">Stok Ürün</small>
            </div>
          </div>
          <div class="col-md-3 col-6">
            <div class="text-center p-2 bg-light rounded">
              <div class="fw-semibold">
                @if($tenant->konsinyeSayisi == -1)
                  Sınırsız
                @elseif($tenant->konsinyeSayisi)
                  {{ $tenant->konsinyeSayisi }}
                @else
                  Belirsiz
                @endif
              </div>
              <small class="text-muted">Konsinye</small>
            </div>
          </div>
        </div>
      </div>

    @elseif($currentSubscription && $currentSubscription->plansubs)
      <!-- Aktif Abonelik -->
      <div class="bg-light rounded p-3">
        <div class="row align-items-center">
          <div class="col-md-3">
            <h5 class="mb-0">{{ $currentSubscription->plansubs->name }}</h5>
            <small class="text-muted">{{ $currentSubscription->plansubs->getBillingCycleText() }}</small>
          </div>
          <div class="col-md-2 text-center">
            @if($currentSubscription->status === 'active')
              <span class="badge bg-success">Aktif</span>
            @elseif($currentSubscription->status === 'trial')
              <span class="badge bg-info">Deneme</span>
            @else
              <span class="badge bg-secondary">{{ ucfirst($currentSubscription->status) }}</span>
            @endif
          </div>
          <div class="col-md-2 text-center">
            <div class="fw-bold">{{ $currentSubscription->plansubs->getFormattedPrice() }}</div>
            <small class="text-muted">Fiyat</small>
          </div>
          <div class="col-md-2 text-center">
            <div class="fw-bold">{{ $currentSubscription->getRemainingDays() }} gün</div>
            <small class="text-muted">Kalan süre</small>
          </div>
          <div class="col-md-3 text-center">
            <div class="fw-bold">
              {{ $currentSubscription->ends_at ? $currentSubscription->ends_at->format('d.m.Y') : 'Belirsiz' }}
            </div>
            <small class="text-muted">Bitiş tarihi</small>
          </div>
        </div>
      </div>
      
      <!-- Paket Özellikleri -->
      @if($currentSubscription->plansubs->limits)
        <div class="mt-3">
          <h6 class="mb-2 fw-medium">Paket Özellikleri</h6>
          <div class="row g-3">
            @if(isset($currentSubscription->plansubs->limits['users']))
              <div class="col-md-3 col-6">
                <div class="text-center p-2 bg-light rounded">
                  <div class="fw-semibold">
                    @if($currentSubscription->plansubs->limits['users'] == -1)
                      Sınırsız
                    @elseif($currentSubscription->plansubs->limits['users'] == 0)
                      Yok
                    @else
                      {{ number_format($currentSubscription->plansubs->limits['users']) }}
                    @endif
                  </div>
                  <small class="text-muted">Kullanıcı</small>
                </div>
              </div>
            @endif
            
            @if(isset($currentSubscription->plansubs->limits['dealers']))
              <div class="col-md-3 col-6">
                <div class="text-center p-2 bg-light rounded">
                  <div class="fw-semibold">
                    @if($currentSubscription->plansubs->limits['dealers'] == -1)
                      Sınırsız
                    @elseif($currentSubscription->plansubs->limits['dealers'] == 0)
                      Yok
                    @else
                      {{ number_format($currentSubscription->plansubs->limits['dealers']) }}
                    @endif
                  </div>
                  <small class="text-muted">Bayi</small>
                </div>
              </div>
            @endif
            
            @if(isset($currentSubscription->plansubs->limits['stocks']))
              <div class="col-md-3 col-6">
                <div class="text-center p-2 bg-light rounded">
                  <div class="fw-semibold">
                    @if($currentSubscription->plansubs->limits['stocks'] == -1)
                      Sınırsız
                    @elseif($currentSubscription->plansubs->limits['stocks'] == 0)
                      Yok
                    @else
                      {{ number_format($currentSubscription->plansubs->limits['stocks']) }}
                    @endif
                  </div>
                  <small class="text-muted">Stok Ürün</small>
                </div>
              </div>
            @endif
            
            @if(isset($currentSubscription->plansubs->limits['konsinye']))
              <div class="col-md-3 col-6">
                <div class="text-center p-2 bg-light rounded">
                  <div class="fw-semibold">
                    @if($currentSubscription->plansubs->limits['konsinye'] == -1)
                      Sınırsız
                    @elseif($currentSubscription->plansubs->limits['konsinye'] == 0)
                      Yok
                    @else
                      {{ number_format($currentSubscription->plansubs->limits['konsinye']) }}
                    @endif
                  </div>
                  <small class="text-muted">Konsinye</small>
                </div>
              </div>
            @endif
            
            @if(isset($currentSubscription->plansubs->limits['tickets_per_month']))
              <div class="col-md-3 col-6">
                <div class="text-center p-2 bg-light rounded">
                  <div class="fw-semibold">
                    @if($currentSubscription->plansubs->limits['tickets_per_month'] == -1)
                      Sınırsız
                    @elseif($currentSubscription->plansubs->limits['tickets_per_month'] == 0)
                      Yok
                    @else
                      {{ number_format($currentSubscription->plansubs->limits['tickets_per_month']) }}
                    @endif
                  </div>
                  <small class="text-muted">Aylık Destek</small>
                </div>
              </div>
            @endif
            
            @if(isset($currentSubscription->plansubs->limits['storage_gb']))
              <div class="col-md-3 col-6">
                <div class="text-center p-2 bg-light rounded">
                  <div class="fw-semibold">
                    @if($currentSubscription->plansubs->limits['storage_gb'] == -1)
                      Sınırsız
                    @elseif($currentSubscription->plansubs->limits['storage_gb'] == 0)
                      Yok
                    @else
                      {{ number_format($currentSubscription->plansubs->limits['storage_gb']) }} GB
                    @endif
                  </div>
                  <small class="text-muted">Depolama</small>
                </div>
              </div>
            @endif
          </div>
        </div>
      @endif

      <!-- Abonelik Geçmişi -->
      @if(isset($subscriptionHistory) && $subscriptionHistory->count() > 1)
        <div class="mt-4">
          <h6 class="mb-2 fw-medium">Abonelik Geçmişi</h6>
          <div class="table-responsive">
            <table class="table table-sm table-bordered">
              <thead class="table-light">
                <tr>
                  <th>Paket</th>
                  <th>Durum</th>
                  <th>Başlangıç</th>
                  <th>Bitiş</th>
                  <th>Fiyat</th>
                </tr>
              </thead>
              <tbody>
                @foreach($subscriptionHistory->take(5) as $subscription)
                  <tr>
                    <td>{{ $subscription->plansubs->name ?? 'Bilinmeyen' }}</td>
                    <td>
                      @if($subscription->status === 'active')
                        <span class="badge bg-success">Aktif</span>
                      @elseif($subscription->status === 'trial')
                        <span class="badge bg-info">Deneme</span>
                      @elseif($subscription->status === 'expired')
                        <span class="badge bg-danger">Süresi Dolmuş</span>
                      @else
                        <span class="badge bg-secondary">{{ ucfirst($subscription->status) }}</span>
                      @endif
                    </td>
                    <td>{{ $subscription->starts_at->format('d.m.Y') }}</td>
                    <td>{{ $subscription->ends_at ? $subscription->ends_at->format('d.m.Y') : '-' }}</td>
                    <td>{{ $subscription->plansubs->getFormattedPrice() ?? '-' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif

    @else
      <div class="alert alert-warning mb-0">
        Bu firmanın aktif bir aboneliği bulunmamaktadır.
      </div>
    @endif
  </div>

  <!-- Servis İstatistikleri -->
  <div class="border rounded p-3 mt-4">
    <h6 class="mb-3 fw-semibold text-dark">Servis İstatistikleri</h6>

    <div class="period-statistics">
      <!-- Tab Navigation -->
      <div class="nav nav-pills nav-fill mb-3" role="tablist">
        @foreach($periodStats as $key => $period)
          <button class="nav-link {{ $key === 'bugun' ? 'active' : '' }}" 
                  data-period="{{ $key }}" 
                  data-target="#period-{{ $key }}"
                  type="button">
            {{ $period['label'] }}
            <span class="badge bg-light text-dark ms-1">{{ $period['toplam'] }}</span>
          </button>
        @endforeach
      </div>

      <!-- Tab Content -->
      <div class="tab-content">
        @foreach($periodStats as $key => $period)
          <div class="tab-pane {{ $key === 'bugun' ? 'show active' : '' }}" 
               id="period-{{ $key }}">
            <div class="row g-3">
              <!-- Markalar -->
              <div class="col-lg-3 col-md-6">
                <div class="card h-100">
                  <div class="card-header bg-light">
                    <h6 class="mb-0">Markalar</h6>
                  </div>
                  <div class="card-body p-2">
                    @forelse($period['markalar'] as $marka)
                      <div class="d-flex justify-content-between py-1 border-bottom">
                        <small>{{ $marka->marka }}</small>
                        <span class="badge bg-secondary">{{ $marka->sayi }}</span>
                      </div>
                    @empty
                      <small class="text-muted">Kayıt yok</small>
                    @endforelse
                  </div>
                </div>
              </div>

              <!-- Türler -->
              <div class="col-lg-3 col-md-6">
                <div class="card h-100">
                  <div class="card-header bg-light">
                    <h6 class="mb-0">Cihaz Türleri</h6>
                  </div>
                  <div class="card-body p-2">
                    @forelse($period['turler'] as $tur)
                      <div class="d-flex justify-content-between py-1 border-bottom">
                        <small>{{ $tur->cihaz }}</small>
                        <span class="badge bg-secondary">{{ $tur->sayi }}</span>
                      </div>
                    @empty
                      <small class="text-muted">Kayıt yok</small>
                    @endforelse
                  </div>
                </div>
              </div>

              <!-- Kaynaklar -->
              <div class="col-lg-3 col-md-6">
                <div class="card h-100">
                  <div class="card-header bg-light">
                    <h6 class="mb-0">Kaynaklar</h6>
                  </div>
                  <div class="card-body p-2">
                    @forelse($period['kaynaklar'] as $kaynak)
                      <div class="d-flex justify-content-between py-1 border-bottom">
                        <small>{{ $kaynak->kaynak }}</small>
                        <span class="badge bg-secondary">{{ $kaynak->sayi }}</span>
                      </div>
                    @empty
                      <small class="text-muted">Kayıt yok</small>
                    @endforelse
                  </div>
                </div>
              </div>

              <!-- Personeller -->
              <div class="col-lg-3 col-md-6">
                <div class="card h-100">
                  <div class="card-header bg-light">
                    <h6 class="mb-0">Personeller</h6>
                  </div>
                  <div class="card-body p-2">
                    @forelse($period['operatorler'] as $operator)
                      <div class="d-flex justify-content-between py-1 border-bottom">
                        <small>{{ $operator->name }}</small>
                        <span class="badge bg-secondary">{{ $operator->sayi }}</span>
                      </div>
                    @empty
                      <small class="text-muted">Kayıt yok</small>
                    @endforelse
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
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
    <form action="{{ route('super.admin.tenant.toggle.status', [$tenant->id,$tenant->id]) }}" method="POST" style="display: inline;">
      @csrf
      @if($tenant->status == 1)
        <button type="submit" class="btn btn-outline-danger btn-sm">Pasif Yap</button>
      @else
        <button type="submit" class="btn btn-outline-success btn-sm">Aktif Et</button>
      @endif
    </form>
  </div>
</div>

<script>
$(document).ready(function() {
    $('.nav-link[data-period]').on('click', function(e) {
        e.preventDefault();
        
        var $this = $(this);
        var targetSelector = $this.data('target');
        
        // Tab navigation
        $('.nav-link[data-period]').removeClass('active');
        $this.addClass('active');
        
        // Tab content
        $('.tab-pane').removeClass('show active');
        $(targetSelector).addClass('show active');
    });
});
</script>