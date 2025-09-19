<!-- Tenant Detay Modal -->
<!-- Header -->
<div class="card-header bg-primary text-white d-flex align-items-center justify-content-between p-3" >
  <div class="d-flex align-items-center">
    {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-3">
      <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path>
      <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path>
      <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path>
      <path d="M10 6h4"></path>
      <path d="M10 10h4"></path>
      <path d="M10 14h4"></path>
      <path d="M10 18h4"></path>
    </svg> --}}
    <div>
      <h4 class="py-2 mb-0" style="padding: 16px;">
      <span style="text-transform: uppercase;color:white ! important">{{ $tenant->firma_adi }} </span>
      @if($tenant->status == 1)
        <span class="badge bg-success text-white" style="margin-left: 4px;">Aktif</span>
      @else
        <span class="badge bg-danger">Pasif</span>
      @endif
    </h4>
    </div>
  </div>
  <button type="button" class="btn-close btn-close-white" style="padding-left: 21px" data-bs-dismiss="modal"></button>
</div>

<!-- Body -->
<div class="card-body p-2">
  <div class="row g-2">
     <!-- İletişim Bilgileri -->
    <div class="col-md-6">
      <div class="border rounded p-2 h-100">
        <h6 class="mb-2 fw-semibold text-dark">İletişim Bilgileri</h6>
        
        <div class="row mb-2">
          <div class="col-6">
            <small class="text-muted d-block">Telefon Numarası</small>
            <span class="fw-medium">{{ $tenant->tel1 ?? $tenant->tel2 }}</span>
          </div>
          <div class="col-6">
            <small class="text-muted d-block">E-Posta</small>
            <span class="fw-medium">{{ $tenant->eposta }}</span>
          </div>
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
      <div class="border rounded p-2 h-100">
        <h6 class="mb-2 fw-semibold text-dark">Firma Bilgileri</h6>
        
        <div class="row mb-2">
          <div class="col-6">
            <small class="text-muted d-block">Adres</small>
            <span class="fw-medium">{{ $tenant->adres }}</span>
          </div>
          <div class="col-6">
            <small class="text-muted d-block">İletişim Kişisi</small>
            <span class="fw-medium">{{ $tenant->name ?? '-' }}</span>
          </div>
        </div>
        
        <div class="row">
          <div class="col-6">
            <small class="text-muted d-block">Kayıt Tarihi</small>
            <span class="fw-medium">{{ $tenant->created_at->format('d.m.Y H:i') }}</span>
          </div>
          <div class="col-6">
            <small class="text-muted d-block">Bitiş Tarihi</small>
            <span class="fw-medium">{{ \Carbon\Carbon::parse($tenant->bitisTarihi)->format('d.m.Y') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Abonelik Bilgileri -->
  <div class="border rounded p-2 mt-2">
    <h6 class="mb-2 fw-semibold text-dark">Abonelik Bilgileri</h6>
    
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

    @if($isTrialFromTenant || ($currentSubscription && $currentSubscription->plansubs))
      <!-- Abonelik Durumu -->
      <div class="border border-2 rounded p-2 mb-2" style="border-color: #e3f2fd !important;">
        <div class="row text-center">
          <div class="col-md-2">
            <small class="text-muted d-block">Paket</small>
            <div class="fw-semibold">
              @if($isTrialFromTenant)
                Ücretsiz Deneme
              @else
                {{ $currentSubscription->plansubs->name }}
              @endif
            </div>
          </div>
          <div class="col-md-2">
            <small class="text-muted d-block">Durum</small>
            <div>
              @if($isTrialFromTenant)
                <span class="badge bg-info">Deneme</span>
              @elseif($currentSubscription->status === 'active')
                <span class="badge bg-success">Aktif</span>
              @elseif($currentSubscription->status === 'trial')
                <span class="badge bg-info">Deneme</span>
              @else
                <span class="badge bg-secondary">{{ ucfirst($currentSubscription->status) }}</span>
              @endif
            </div>
          </div>
          <div class="col-md-2">
            <small class="text-muted d-block">Fiyat</small>
            <div class="fw-semibold">
              @if($isTrialFromTenant)
                Ücretsiz
              @else
                {{ $currentSubscription->plansubs->getFormattedPrice() }}
              @endif
            </div>
          </div>
          <div class="col-md-3">
            <small class="text-muted d-block">Kalan Süre</small>
            <div class="fw-semibold">
              @if($isTrialFromTenant)
                {{ $tenant->trial_ends_at->diffInDays(now()) }} gün
              @else
                {{ $currentSubscription->getRemainingDays() }} gün
              @endif
            </div>
          </div>
          <div class="col-md-3">
            <small class="text-muted d-block">Bitiş Tarihi</small>
            <div class="fw-semibold">
              @if($isTrialFromTenant)
                {{ $tenant->trial_ends_at->format('d.m.Y') }}
              @else
                {{ $currentSubscription->ends_at ? $currentSubscription->ends_at->format('d.m.Y') : 'Belirsiz' }}
              @endif
            </div>
          </div>
        </div>
      </div>
      
      <!-- Paket Özellikleri -->
      <div>
        <div class="row g-2">
          <div class="col-md-2 col-6">
            <div class="text-center p-1 rounded">
              <small class="text-muted d-block">Kullanıcı</small>
              <div class="fw-semibold">
                @if($isTrialFromTenant)
                  @if($tenant->personelSayisi == -1)
                    Sınırsız
                  @elseif($tenant->personelSayisi)
                    {{ $tenant->personelSayisi }}
                  @else
                    Belirsiz
                  @endif
                @elseif(isset($currentSubscription->plansubs->limits['users']))
                  @if($currentSubscription->plansubs->limits['users'] == -1)
                    Sınırsız
                  @elseif($currentSubscription->plansubs->limits['users'] == 0)
                    Yok
                  @else
                    {{ number_format($currentSubscription->plansubs->limits['users']) }}
                  @endif
                @else
                  -
                @endif
              </div>
            </div>
          </div>
          <div class="col-md-2 col-6">
            <div class="text-center p-1 rounded">
              <small class="text-muted d-block">Bayi</small>
              <div class="fw-semibold">
                @if($isTrialFromTenant)
                  @if($tenant->bayiSayisi == -1)
                    Sınırsız
                  @elseif($tenant->bayiSayisi)
                    {{ $tenant->bayiSayisi }}
                  @else
                    Belirsiz
                  @endif
                @elseif(isset($currentSubscription->plansubs->limits['dealers']))
                  @if($currentSubscription->plansubs->limits['dealers'] == -1)
                    Sınırsız
                  @elseif($currentSubscription->plansubs->limits['dealers'] == 0)
                    Yok
                  @else
                    {{ number_format($currentSubscription->plansubs->limits['dealers']) }}
                  @endif
                @else
                  -
                @endif
              </div>
            </div>
          </div>
          <div class="col-md-2 col-6">
            <div class="text-center p-1 rounded">
              <small class="text-muted d-block">Stok Ürün</small>
              <div class="fw-semibold">
                @if($isTrialFromTenant)
                  @if($tenant->stokSayisi == -1)
                    Sınırsız
                  @elseif($tenant->stokSayisi)
                    {{ $tenant->stokSayisi }}
                  @else
                    Belirsiz
                  @endif
                @elseif(isset($currentSubscription->plansubs->limits['stocks']))
                  @if($currentSubscription->plansubs->limits['stocks'] == -1)
                    Sınırsız
                  @elseif($currentSubscription->plansubs->limits['stocks'] == 0)
                    Yok
                  @else
                    {{ number_format($currentSubscription->plansubs->limits['stocks']) }}
                  @endif
                @else
                  -
                @endif
              </div>
            </div>
          </div>
          <div class="col-md-2 col-6">
            <div class="text-center p-1 rounded">
              <small class="text-muted d-block">Konsinye</small>
              <div class="fw-semibold">
                @if($isTrialFromTenant)
                  @if($tenant->konsinyeSayisi == -1)
                    Sınırsız
                  @elseif($tenant->konsinyeSayisi)
                    {{ $tenant->konsinyeSayisi }}
                  @else
                    Belirsiz
                  @endif
                @elseif(isset($currentSubscription->plansubs->limits['konsinye']))
                  @if($currentSubscription->plansubs->limits['konsinye'] == -1)
                    Sınırsız
                  @elseif($currentSubscription->plansubs->limits['konsinye'] == 0)
                    Yok
                  @else
                    {{ number_format($currentSubscription->plansubs->limits['konsinye']) }}
                  @endif
                @else
                  -
                @endif
              </div>
            </div>
          </div>
          @if(!$isTrialFromTenant && isset($currentSubscription->plansubs->limits['tickets_per_month']))
            <div class="col-md-2 col-6">
              <div class="text-center p-1 rounded">
                <small class="text-muted d-block">Aylık Destek</small>
                <div class="fw-semibold">
                  @if($currentSubscription->plansubs->limits['tickets_per_month'] == -1)
                    Sınırsız
                  @elseif($currentSubscription->plansubs->limits['tickets_per_month'] == 0)
                    Yok
                  @else
                    {{ number_format($currentSubscription->plansubs->limits['tickets_per_month']) }}
                  @endif
                </div>
              </div>
            </div>
          @endif
          @if(!$isTrialFromTenant && isset($currentSubscription->plansubs->limits['storage_gb']))
            <div class="col-md-2 col-6">
              <div class="text-center p-1 rounded">
                <small class="text-muted d-block">Depolama</small>
                <div class="fw-semibold">
                  @if($currentSubscription->plansubs->limits['storage_gb'] == -1)
                    Sınırsız
                  @elseif($currentSubscription->plansubs->limits['storage_gb'] == 0)
                    Yok
                  @else
                    {{ number_format($currentSubscription->plansubs->limits['storage_gb']) }} GB
                  @endif
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>

      <!-- Abonelik Geçmişi -->
      @if(isset($subscriptionHistory) && $subscriptionHistory->count() > 1)
        <div class="mt-2">
          <h6 class="mb-1 fw-medium">Abonelik Geçmişi</h6>
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
  <div class="border rounded p-2 mt-2">
    <h6 class="mb-2 fw-semibold text-dark">Servis İstatistikleri</h6>

    <div class="period-statistics">
      <!-- Tab Navigation -->
      <div class="nav nav-pills nav-fill mb-2" role="tablist">
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
            <div class="row g-2">
              <!-- Markalar -->
              <div class="col-lg-3 col-md-6">
                <div class="card h-100">
                  <div class="card-header bg-light py-1">
                    <small class="fw-semibold">Markalar</small>
                  </div>
                  <div class="card-body p-1">
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
                  <div class="card-header bg-light py-1">
                    <small class="fw-semibold">Cihaz Türleri</small>
                  </div>
                  <div class="card-body p-1">
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
                  <div class="card-header bg-light py-1">
                    <small class="fw-semibold">Kaynaklar</small>
                  </div>
                  <div class="card-body p-1">
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
                  <div class="card-header bg-light py-1">
                    <small class="fw-semibold">Personeller</small>
                  </div>
                  <div class="card-body p-1">
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
<div class="card-footer bg-light d-flex justify-content-between align-items-center p-2">
  <div>
    <small class="text-muted">Toplam Servis Sayısı</small>
    <div class="fw-bold text-primary">{{ $topServisSayisi ?? 0 }}</div>
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