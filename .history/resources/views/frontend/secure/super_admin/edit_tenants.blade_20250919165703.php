<!-- Header -->
<div class="card-header bg-primary text-white d-flex align-items-center justify-content-between p-3" >
  <!-- Add this hidden input in your modal -->
<input type="hidden" id="current-tenant-id" value="{{ $tenant->id ?? '' }}">
  <div class="d-flex align-items-center">
    <div>
      <h4 class="py-2 mb-0" style="padding: 16px;">
      <span style="text-transform: uppercase;color:white ! important">{{ $tenant->firma_adi }} </span>
    </h4>
    </div>
  </div>
  <button type="button" class="btn-close btn-close-white" style="padding-left: 21px" data-bs-dismiss="modal"></button>
</div>

<!-- Body -->
<div class="card-body p-3">
  <!-- Nav Tabs - 2 Tab -->
  <div class="nav nav-pills nav-fill mb-3" role="tablist">
    <!-- İlk Tab: Müşteri ve Abonelik Bilgileri -->
    <button class="nav-link active" id="customer-subscription-tab" data-bs-toggle="pill" data-bs-target="#customer-subscription" type="button">
      <i class="fas fa-building me-1"></i>Müşteri ve Abonelik Bilgileri
    </button>
    
    <!-- İkinci Tab: Ödeme Bilgileri -->
    <button class="nav-link" id="payment-info-tab" data-bs-toggle="pill" data-bs-target="#payment-info" type="button">
      <i class="fas fa-money-bill-wave me-1"></i>Ödeme Bilgileri
    </button>
  </div>

  <!-- Tab Content -->
  <div class="tab-content">
    
    <!-- İLK TAB: MÜŞTERİ VE ABONELİK BİLGİLERİ -->
    <div class="tab-pane fade show active" id="customer-subscription">
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
          // Mevcut abonelik kodu (blade php kısmını koruyoruz)
          $currentSubscription = null;
          $isTrialFromTenant = false;
          
          if ($tenant->status == 1) {
              if ($tenant->subscription_status === 'trial' && $tenant->trial_ends_at && $tenant->trial_ends_at->isFuture()) {
                  $isTrialFromTenant = true;
                  $currentSubscription = $tenant->currentSubscription;
              } else if ($tenant->activeSubscription) {
                  $currentSubscription = $tenant->activeSubscription;
              } else {
                  $currentSubscription = $tenant->currentSubscription;
              }
          } else {
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
    
    <!-- İKİNCİ TAB: ÖDEME BİLGİLERİ -->
    <div class="tab-pane fade" id="payment-info">
      <!-- Loading spinner -->
      <div id="payment-loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Yükleniyor...</span>
        </div>
        <p class="mt-2 text-muted">Ödeme bilgileri yükleniyor...</p>
      </div>
      
      <!-- Ana içerik -->
      <div id="payment-content" style="display: none;">
        
        <!-- Ödeme Özeti Kartları -->
        <div class="row mb-4">
          <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-success">
              <div class="card-body text-center">
                <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                <h4 class="text-success mb-1" id="summary-completed">₺0</h4>
                <small class="text-muted">Tamamlanan Ödemeler</small>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-warning">
              <div class="card-body text-center">
                <i class="fas fa-clock text-warning fa-2x mb-2"></i>
                <h4 class="text-warning mb-1" id="summary-pending">₺0</h4>
                <small class="text-muted">Bekleyen Ödemeler</small>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-danger">
              <div class="card-body text-center">
                <i class="fas fa-times-circle text-danger fa-2x mb-2"></i>
                <h4 class="text-danger mb-1" id="summary-failed">₺0</h4>
                <small class="text-muted">Başarısız Ödemeler</small>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-info">
              <div class="card-body text-center">
                <i class="fas fa-undo text-info fa-2x mb-2"></i>
                <h4 class="text-info mb-1" id="summary-refunded">₺0</h4>
                <small class="text-muted">İade Edilen</small>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Filtre ve Arama -->
        <div class="card mb-3">
          <div class="card-header bg-light">
            <div class="row align-items-center">
              <div class="col-md-6">
                <h6 class="mb-0">Ödeme Geçmişi</h6>
              </div>
              <div class="col-md-6">
                <div class="d-flex gap-2 justify-content-end">
                  <!-- Durum Filtresi -->
                  <select class="form-select form-select-sm" id="status-filter" style="width: auto;">
                    <option value="">Tüm Durumlar</option>
                    <option value="completed">Tamamlandı</option>
                    <option value="pending">Bekliyor</option>
                    <option value="failed">Başarısız</option>
                    <option value="refunded">İade Edildi</option>
                    <option value="canceled">İptal Edildi</option>
                  </select>
                  
                  <!-- Tür Filtresi -->
                  <select class="form-select form-select-sm" id="type-filter" style="width: auto;">
                    <option value="">Tüm Türler</option>
                    <option value="subscription">Abonelik</option>
                    <option value="storage">Depolama</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0" id="payments-table">
                <thead class="table-light">
                  <tr>
                    <th>Tarih</th>
                    <th>Tür</th>
                    <th>Açıklama</th>
                    <th>Tutar</th>
                    <th>Ödeme Yöntemi</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                  </tr>
                </thead>
                <tbody id="payments-table-body">
                  <!-- Dinamik içerik buraya gelecek -->
                </tbody>
              </table>
            </div>
            
            <!-- Boş durum mesajı -->
            <div id="no-payments-message" class="text-center py-5" style="display: none;">
              <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
              <h5 class="text-muted">Henüz ödeme kaydı bulunmamaktadır</h5>
              <p class="text-muted">Bu firmaya ait herhangi bir ödeme işlemi görünmüyor.</p>
            </div>
          </div>
        </div>
        
        <!-- Sayfalama -->
        <div class="d-flex justify-content-between align-items-center">
          <nav>
            <ul class="pagination pagination-sm mb-0" id="payments-pagination">
              <!-- Dinamik sayfalama -->
            </ul>
          </nav>
        </div>
      </div>
      
      <!-- Hata durumu -->
      <div id="payment-error" class="text-center py-5" style="display: none;">
        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
        <h5 class="text-warning">Ödeme bilgileri yüklenemedi</h5>
        <p class="text-muted">Bir hata oluştu. Lütfen sayfayı yenileyin veya daha sonra tekrar deneyin.</p>
        <button class="btn btn-outline-primary" onclick="PaymentModule.loadPaymentInfo(PaymentModule.currentTenantId)">
          <i class="fas fa-sync-alt me-1"></i>Tekrar Dene
        </button>
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


<!-- Payment Detail Modal -->
<div class="modal fade" id="paymentDetailModal" tabindex="-1" aria-labelledby="paymentDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="paymentDetailModalLabel">
          <i class="fas fa-receipt me-2"></i>Ödeme Detayları
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="paymentDetailContent">
        <!-- Loading State -->
        <div id="paymentDetailLoading" class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Yükleniyor...</span>
          </div>
          <p class="mt-2 text-muted">Ödeme detayları yükleniyor...</p>
        </div>
        
        <!-- Content will be loaded here -->
        <div id="paymentDetailData" style="display: none;">
          <!-- Dynamic content -->
        </div>
        
        <!-- Error State -->
        <div id="paymentDetailError" style="display: none;" class="text-center py-4">
          <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
          <h5 class="text-warning">Detaylar yüklenemedi</h5>
          <p class="text-muted">Bir hata oluştu. Lütfen tekrar deneyin.</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
      </div>
    </div>
  </div>
</div>


<script>
// Modül pattern kullanarak namespace çakışmalarını önle
window.PaymentModule = (function() {
  'use strict';

  // Ödeme detayını göster - Updated implementation
  function showPaymentDetail(type, paymentId) {
    console.log('Ödeme detayı isteniyor:', type, paymentId, 'Tenant ID:', currentTenantId);
    
    if (!currentTenantId || !paymentId) {
      console.error('Geçersiz parametreler');
      return;
    }
    
    // Modal'ı aç
    const modal = new bootstrap.Modal(document.getElementById('paymentDetailModal'));
    modal.show();
    
    // Loading durumunu göster
    $('#paymentDetailLoading').show();
    $('#paymentDetailData').hide();
    $('#paymentDetailError').hide();
    
    // AJAX ile detayları getir
    $.ajax({
      url: `/super-admin/tenant/${currentTenantId}/payment-detail/${type}/${paymentId}`,
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        console.log('Ödeme detayı başarılı:', response);
        if (response.success) {
          displayPaymentDetail(response.payment);
        } else {
          showPaymentDetailError();
        }
      },
      error: function(xhr, status, error) {
        console.error('Ödeme detayı hatası:', {
          status: status,
          error: error,
          response: xhr.responseText
        });
        showPaymentDetailError();
      }
    });
  }
  
  // Ödeme detayını göster
  function displayPaymentDetail(payment) {
    const detailHtml = createPaymentDetailHtml(payment);
    $('#paymentDetailData').html(detailHtml).show();
    $('#paymentDetailLoading').hide();
    $('#paymentDetailError').hide();
  }
  
  // Ödeme detay HTML'i oluştur
  function createPaymentDetailHtml(payment) {
    const statusBadge = getStatusBadge(payment.status);
    const typeBadge = getTypeBadge(payment.type, payment.type_label);
    
    return `
      <div class="row">
        <div class="col-md-6">
          <div class="card mb-3">
            <div class="card-header bg-light">
              <h6 class="mb-0">Genel Bilgiler</h6>
            </div>
            <div class="card-body">
              <div class="row mb-2">
                <div class="col-5"><strong>Ödeme ID:</strong></div>
                <div class="col-7">#${payment.id}</div>
              </div>
              <div class="row mb-2">
                <div class="col-5"><strong>Tür:</strong></div>
                <div class="col-7">${typeBadge}</div>
              </div>
              <div class="row mb-2">
                <div class="col-5"><strong>Durum:</strong></div>
                <div class="col-7">${statusBadge}</div>
              </div>
              <div class="row mb-2">
                <div class="col-5"><strong>Tutar:</strong></div>
                <div class="col-7"><span class="fw-bold">₺${formatMoney(payment.amount || 0)}</span></div>
              </div>
              <div class="row mb-2">
                <div class="col-5"><strong>Açıklama:</strong></div>
                <div class="col-7">${payment.description || '-'}</div>
              </div>
              ${payment.plan_name ? `
              <div class="row mb-2">
                <div class="col-5"><strong>Plan:</strong></div>
                <div class="col-7">${payment.plan_name}</div>
              </div>
              ` : ''}
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="card mb-3">
            <div class="card-header bg-light">
              <h6 class="mb-0">Ödeme Bilgileri</h6>
            </div>
            <div class="card-body">
              <div class="row mb-2">
                <div class="col-5"><strong>Ödeme Yöntemi:</strong></div>
                <div class="col-7">${payment.payment_method || 'Belirtilmemiş'}</div>
              </div>
              ${payment.gateway ? `
              <div class="row mb-2">
                <div class="col-5"><strong>Gateway:</strong></div>
                <div class="col-7">${payment.gateway}</div>
              </div>
              ` : ''}
              ${payment.transaction_id ? `
              <div class="row mb-2">
                <div class="col-5"><strong>İşlem ID:</strong></div>
                <div class="col-7">${payment.transaction_id}</div>
              </div>
              ` : ''}
              <div class="row mb-2">
                <div class="col-5"><strong>Oluşturulma:</strong></div>
                <div class="col-7">${formatDate(payment.created_at)}</div>
              </div>
              ${payment.paid_at ? `
              <div class="row mb-2">
                <div class="col-5"><strong>Ödenme Tarihi:</strong></div>
                <div class="col-7">${formatDate(payment.paid_at)}</div>
              </div>
              ` : ''}
            </div>
          </div>
        </div>
      </div>
      
      ${payment.storage_gb ? `
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-light">
              <h6 class="mb-0">Depolama Bilgileri</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4">
                  <strong>Depolama Miktarı:</strong><br>
                  <span class="badge bg-success">${payment.storage_gb} GB</span>
                </div>
                ${payment.expires_at ? `
                <div class="col-md-4">
                  <strong>Bitiş Tarihi:</strong><br>
                  ${formatDate(payment.expires_at)}
                </div>
                ` : ''}
              </div>
            </div>
          </div>
        </div>
      </div>
      ` : ''}
    `;
  }
  
  // Ödeme detay hatasını göster
  function showPaymentDetailError() {
    $('#paymentDetailLoading').hide();
    $('#paymentDetailData').hide();
    $('#paymentDetailError').show();
  }
  
  // Ödeme detayını göster
  function displayPaymentDetail(payment) {
    const detailHtml = createPaymentDetailHtml(payment);
    $('#paymentDetailData').html(detailHtml).show();
    $('#paymentDetailLoading').hide();
    $('#paymentDetailError').hide();
  }
  
  // Ödeme detay HTML'i oluştur
  function createPaymentDetailHtml(payment) {
    const statusBadge = getStatusBadge(payment.status);
    const typeBadge = getTypeBadge(payment.type, payment.type_label);
    
    return `
      <div class="row">
        <div class="col-md-6">
          <div class="card mb-3">
            <div class="card-header bg-light">
              <h6 class="mb-0">Genel Bilgiler</h6>
            </div>
            <div class="card-body">
              <div class="row mb-2">
                <div class="col-5"><strong>Ödeme ID:</strong></div>
                <div class="col-7">#${payment.id}</div>
              </div>
              <div class="row mb-2">
                <div class="col-5"><strong>Tür:</strong></div>
                <div class="col-7">${typeBadge}</div>
              </div>
              <div class="row mb-2">
                <div class="col-5"><strong>Durum:</strong></div>
                <div class="col-7">${statusBadge}</div>
              </div>
              <div class="row mb-2">
                <div class="col-5"><strong>Tutar:</strong></div>
                <div class="col-7"><span class="fw-bold">₺${formatMoney(payment.amount || 0)}</span></div>
              </div>
              <div class="row mb-2">
                <div class="col-5"><strong>Açıklama:</strong></div>
                <div class="col-7">${payment.description || '-'}</div>
              </div>
              ${payment.plan_name ? `
              <div class="row mb-2">
                <div class="col-5"><strong>Plan:</strong></div>
                <div class="col-7">${payment.plan_name}</div>
              </div>
              ` : ''}
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="card mb-3">
            <div class="card-header bg-light">
              <h6 class="mb-0">Ödeme Bilgileri</h6>
            </div>
            <div class="card-body">
              <div class="row mb-2">
                <div class="col-5"><strong>Ödeme Yöntemi:</strong></div>
                <div class="col-7">${payment.payment_method || 'Belirtilmemiş'}</div>
              </div>
              ${payment.gateway ? `
              <div class="row mb-2">
                <div class="col-5"><strong>Gateway:</strong></div>
                <div class="col-7">${payment.gateway}</div>
              </div>
              ` : ''}
              ${payment.transaction_id ? `
              <div class="row mb-2">
                <div class="col-5"><strong>İşlem ID:</strong></div>
                <div class="col-7">${payment.transaction_id}</div>
              </div>
              ` : ''}
              <div class="row mb-2">
                <div class="col-5"><strong>Oluşturulma:</strong></div>
                <div class="col-7">${formatDate(payment.created_at)}</div>
              </div>
              ${payment.paid_at ? `
              <div class="row mb-2">
                <div class="col-5"><strong>Ödenme Tarihi:</strong></div>
                <div class="col-7">${formatDate(payment.paid_at)}</div>
              </div>
              ` : ''}
            </div>
          </div>
        </div>
      </div>
      
      ${payment.storage_gb ? `
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-light">
              <h6 class="mb-0">Depolama Bilgileri</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4">
                  <strong>Depolama Miktarı:</strong><br>
                  <span class="badge bg-success">${payment.storage_gb} GB</span>
                </div>
                ${payment.expires_at ? `
                <div class="col-md-4">
                  <strong>Bitiş Tarihi:</strong><br>
                  ${formatDate(payment.expires_at)}
                </div>
                ` : ''}
              </div>
            </div>
          </div>
        </div>
      </div>
      ` : ''}
    `;
  }
  
  // Ödeme detay hatasını göster
  function showPaymentDetailError() {
    $('#paymentDetailLoading').hide();
    $('#paymentDetailData').hide();
    $('#paymentDetailError').show();
  }
  
  // Private değişkenler
  let currentTenantId = null;
  let allPayments = [];
  let filteredPayments = [];
  let currentPage = 1;
  const itemsPerPage = 10;
  let initialized = false;

  // Ödeme bilgilerini yükle
  function loadPaymentInfo(tenantId) {
    if (!tenantId || tenantId === 'null' || tenantId === null) {
      console.error('Geçersiz tenant ID:', tenantId);
      showPaymentError();
      return;
    }
    
    
    showPaymentLoading();
    
    $.ajax({
      url: `/super-admin/tenant/${tenantId}/payments`,
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        if (response.success) {
          allPayments = response.payments || [];
          updatePaymentSummary(response.summary);
          applyFilters();
          showPaymentContent();
        } else {
          console.error('API yanıt hatası:', response.message);
          showPaymentError();
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX hatası:', {
          status: status,
          error: error,
          response: xhr.responseText,
          xhr: xhr
        });
        showPaymentError();
      }
    });
  }

  // UI durumları
  function showPaymentLoading() {
    $('#payment-loading').show();
    $('#payment-content').hide();
    $('#payment-error').hide();
  }

  function showPaymentContent() {
    $('#payment-loading').hide();
    $('#payment-content').show();
    $('#payment-error').hide();
  }

  function showPaymentError() {
    $('#payment-loading').hide();
    $('#payment-content').hide();
    $('#payment-error').show();
  }

  // Ödeme özetini güncelle
  function updatePaymentSummary(summary) {
    $('#summary-completed').text('₺' + (summary.completed || '0'));
    $('#summary-pending').text('₺' + (summary.pending || '0'));
    $('#summary-failed').text('₺' + (summary.failed || '0'));
    $('#summary-refunded').text('₺' + (summary.refunded || '0'));
  }

  // Filtreleri uygula
  function applyFilters() {
    const statusFilter = $('#status-filter').val();
    const typeFilter = $('#type-filter').val();
    
    filteredPayments = allPayments.filter(payment => {
      const statusMatch = !statusFilter || payment.status === statusFilter;
      const typeMatch = !typeFilter || payment.type === typeFilter;
      return statusMatch && typeMatch;
    });
    
    currentPage = 1;
    updatePaymentsTable();
    updatePagination();
  }

  // Ödeme tablosunu güncelle
  function updatePaymentsTable() {
    const tbody = $('#payments-table-body');
    tbody.empty();
    
    $('#total-payments-count').text(filteredPayments.length);
    
    if (filteredPayments.length === 0) {
      $('#payments-table').hide();
      $('#no-payments-message').show();
      return;
    }
    
    $('#payments-table').show();
    $('#no-payments-message').hide();
    
    // Sayfalama için veriyi böl
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const pagePayments = filteredPayments.slice(startIndex, endIndex);
    
    pagePayments.forEach(payment => {
      const row = createPaymentRow(payment);
      tbody.append(row);
    });
  }

  // Ödeme satırı oluştur
  function createPaymentRow(payment) {
    const statusBadge = getStatusBadge(payment.status);
    const typeBadge = getTypeBadge(payment.type, payment.type_label);
    const formattedDate = formatDate(payment.created_at);
    const paidDate = payment.paid_at ? formatDate(payment.paid_at) : null;
    
    return `
      <tr>
        <td>
          <div class="fw-medium">${formattedDate}</div>
          ${paidDate ? `<small class="text-muted">Ödendi: ${paidDate}</small>` : ''}
        </td>
        <td>${typeBadge}</td>
        <td>
          <div class="fw-medium">${payment.description || '-'}</div>
          ${payment.storage_gb ? `<small class="text-muted">${payment.storage_gb} GB</small>` : ''}
          ${payment.expires_at ? `<small class="text-muted d-block">Bitiş: ${formatDate(payment.expires_at)}</small>` : ''}
        </td>
        <td>
          <span class="fw-medium">₺${formatMoney(payment.amount || 0)}</span>
          ${payment.currency && payment.currency !== 'TRY' ? `<br><small class="text-muted">${payment.currency}</small>` : ''}
        </td>
        <td>
          <div>${payment.payment_method || 'Belirtilmemiş'}</div>
          ${payment.gateway ? `<small class="text-muted">${payment.gateway}</small>` : ''}
        </td>
        <td>${statusBadge}</td>
        <td>
          <div class="btn-group btn-group-sm">
            ${createActionButtons(payment)}
          </div>
        </td>
      </tr>
    `;
  }

  // Durum badge'i oluştur
  function getStatusBadge(status) {
    const badges = {
      'completed': '<span class="badge bg-success">Tamamlandı</span>',
      'pending': '<span class="badge bg-warning">Bekliyor</span>',
      'failed': '<span class="badge bg-danger">Başarısız</span>',
      'refunded': '<span class="badge bg-info">İade Edildi</span>',
      'canceled': '<span class="badge bg-secondary">İptal Edildi</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Bilinmeyen</span>';
  }

  // Tür badge'i oluştur
  function getTypeBadge(type, label) {
    if (type === 'subscription') {
      return '<span class="badge bg-primary bg-opacity-10 text-primary">Abonelik</span>';
    } else if (type === 'storage') {
      return '<span class="badge bg-success bg-opacity-10 text-success">Depolama</span>';
    }
    return `<span class="badge bg-secondary bg-opacity-10 text-secondary">${label}</span>`;
  }

  // Aksiyon butonları oluştur
  function createActionButtons(payment) {
    let buttons = '';
    
    // Fatura butonu
    if (payment.invoice_path) {
        let invoPath = '/' + payment.invoice_path; // veya asset URL'nizi buraya
        buttons += `<a href="${invoPath}" class="btn btn-outline-primary btn-sm" title="Faturayı Görüntüle" target="_blank">
                      <i class="fas fa-file-pdf"></i>
                    </a>`;
    }
    
    //Detay butonu
    buttons += `<button class="btn btn-outline-info btn-sm" onclick="PaymentModule.showPaymentDetail('${payment.type}', ${payment.id})" title="Detayları Görüntüle">
                  <i class="fas fa-info-circle"></i>
                </button>`;
    
    return buttons || '<span class="text-muted">-</span>';
  }

  // Sayfalama güncelle
  function updatePagination() {
    const totalPages = Math.ceil(filteredPayments.length / itemsPerPage);
    const pagination = $('#payments-pagination');
    pagination.empty();
    
    if (totalPages <= 1) return;
    
    // Önceki sayfa
    const prevDisabled = currentPage === 1 ? 'disabled' : '';
    pagination.append(`
      <li class="page-item ${prevDisabled}">
        <a class="page-link" href="#" onclick="PaymentModule.changePage(${currentPage - 1}); return false;">Önceki</a>
      </li>
    `);
    
    // Sayfa numaraları
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
      const active = i === currentPage ? 'active' : '';
      pagination.append(`
        <li class="page-item ${active}">
          <a class="page-link" href="#" onclick="PaymentModule.changePage(${i}); return false;">${i}</a>
        </li>
      `);
    }
    
    // Sonraki sayfa
    const nextDisabled = currentPage === totalPages ? 'disabled' : '';
    pagination.append(`
      <li class="page-item ${nextDisabled}">
        <a class="page-link" href="#" onclick="PaymentModule.changePage(${currentPage + 1}); return false;">Sonraki</a>
      </li>
    `);
  }

  // Sayfa değiştir
  function changePage(page) {
    const totalPages = Math.ceil(filteredPayments.length / itemsPerPage);
    if (page < 1 || page > totalPages) return;
    
    currentPage = page;
    updatePaymentsTable();
    updatePagination();
  }


  // Yardımcı fonksiyonlar
  function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('tr-TR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  }

  function formatMoney(amount) {
    return new Intl.NumberFormat('tr-TR', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }).format(amount || 0);
  }

  // Event handler'ları başlat
  function initEventHandlers() {
    if (initialized) return;
    
    // Modal açıldığında tenant ID'yi al - birden fazla yöntemle dene
    $(document).off('show.bs.modal', '#editTenantModal').on('show.bs.modal', '#editTenantModal', function(event) {
      const button = $(event.relatedTarget);
      let tenantId = null;
      
      // Farklı yöntemlerle tenant ID'yi almaya çalış
      tenantId = button.data('bs-id') || button.attr('data-bs-id') || button.data('id') || button.attr('data-id');
      
    
      
      if (tenantId && tenantId !== 'null' && tenantId !== null) {
        currentTenantId = tenantId;
        $('#current-tenant-id').val(tenantId);
        
      } else {
        
      }
    });

    // Alternatif olarak, editTenant class'ına sahip elementlere click event'i ekle
    $(document).off('click', '.editTenant').on('click', '.editTenant', function() {
      const tenantId = $(this).data('bs-id') || $(this).attr('data-bs-id');
     
      
      if (tenantId) {
        currentTenantId = tenantId;
        $('#current-tenant-id').val(tenantId);
      }
    });

    // Payment tab'ına tıklandığında
    $(document).off('shown.bs.tab', '#payment-info-tab').on('shown.bs.tab', '#payment-info-tab', function() {
     
      
      // Eğer currentTenantId null ise, modal'dan almaya çalış
      if (!currentTenantId) {
        const modalTenantId = $('#current-tenant-id').val();
       
        if (modalTenantId) {
          currentTenantId = modalTenantId;
        }
      }
      
      if (currentTenantId && currentTenantId !== 'null' && currentTenantId !== null) {
        loadPaymentInfo(currentTenantId);
      } else {
        console.error('Tenant ID hala geçersiz:', currentTenantId);
        showPaymentError();
      }
    });

    // Filtre değişiklikleri
    $(document).off('change', '#status-filter, #type-filter').on('change', '#status-filter, #type-filter', function() {
      applyFilters();
    });

    // Servis istatistikleri period switching
    $(document).off('click', '.nav-link[data-period]').on('click', '.nav-link[data-period]', function(e) {
      e.preventDefault();
      
      var $this = $(this);
      var targetSelector = $this.data('target');
      
      // Tab navigation
      $('.nav-link[data-period]').removeClass('active');
      $this.addClass('active');
      
      // Tab content
      $('.tab-pane[id^="period-"]').removeClass('show active');
      $(targetSelector).addClass('show active');
    });
    
    initialized = true;
  }

  // Public API
  return {
    init: initEventHandlers,
    loadPaymentInfo: loadPaymentInfo,
    changePage: changePage,
    showPaymentDetail: showPaymentDetail,
    get currentTenantId() { return currentTenantId; }
  };
})();

// Document ready event
$(document).ready(function() {
  PaymentModule.init();
});
</script>

<style>
/* Ana Tab Stilleri */
.nav-pills .nav-link {
  border-radius: 0.375rem;
  font-weight: 500;
  transition: all 0.3s ease;
}

.nav-pills .nav-link:hover {
  background-color: rgba(13, 110, 253, 0.1);
  color: #0d6efd;
}

.nav-pills .nav-link.active {
  background-color: #0d6efd;
  border-color: #0d6efd;
}

/* Ek stiller */
.badge.bg-opacity-10 {
  --bs-bg-opacity: 0.1;
}

.table th {
  font-weight: 600;
  font-size: 0.875rem;
  color: #495057;
  border-bottom: 2px solid #dee2e6;
}

.table td {
  vertical-align: middle;
  font-size: 0.875rem;
}

.btn-group-sm .btn {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
}

/* Özet kartları hover efekti */
.card:hover {
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
  transition: box-shadow 0.3s ease-in-out;
}

/* Loading spinner */
.spinner-border {
  width: 3rem;
  height: 3rem;
}

/* Pagination düzenlemeleri */
.pagination-sm .page-link {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

/* Tab content padding */
.tab-content > .tab-pane {
  padding: 0;
}

/* Servis istatistikleri period navigation */
.period-statistics .nav-pills .nav-link {
  font-size: 0.875rem;
  padding: 0.5rem 0.75rem;
}

.period-statistics .nav-pills .nav-link .badge {
  font-size: 0.7rem;
  padding: 0.2rem 0.4rem;
}

/* Responsive düzenlemeler */
@media (max-width: 768px) {
  .table-responsive {
    font-size: 0.75rem;
  }
  
  .btn-group-sm .btn {
    padding: 0.2rem 0.4rem;
  }
  
  .badge {
    font-size: 0.7rem;
  }
  
  .nav-pills .nav-link {
    padding: 0.5rem;
    font-size: 0.875rem;
  }
  
  .nav-pills .nav-link i {
    display: none; /* Mobilde iconları gizle */
  }
  
  .card-body h4 {
    font-size: 1.25rem;
  }
  
  /* Period navigation mobil düzen */
  .period-statistics .nav-pills {
    flex-direction: column;
  }
  
  .period-statistics .nav-pills .nav-link {
    text-align: center;
    margin-bottom: 0.25rem;
  }
}

@media (max-width: 576px) {
  /* Özet kartları mobilde tam genişlik */
  .row > [class*="col-lg-3"] {
    flex: 0 0 100%;
    max-width: 100%;
    margin-bottom: 1rem;
  }
  
  /* Filtre dropdownları mobilde alt alta */
  .d-flex.gap-2 {
    flex-direction: column;
    gap: 0.5rem !important;
  }
  
  .form-select-sm {
    width: 100% !important;
  }
}

/* Tablo satırları hover efekti */
.table-hover tbody tr:hover {
  background-color: rgba(13, 110, 253, 0.05);
}

/* Badge renkleri */
.text-primary { color: #0d6efd !important; }
.text-success { color: #198754 !important; }
.text-warning { color: #ffc107 !important; }
.text-danger { color: #dc3545 !important; }
.text-info { color: #0dcaf0 !important; }

/* Boş durum mesajı */
#no-payments-message i {
  opacity: 0.5;
}

/* Card border renkleri */
.border-success { border-color: #198754 !important; }
.border-warning { border-color: #ffc107 !important; }
.border-danger { border-color: #dc3545 !important; }
.border-info { border-color: #0dcaf0 !important; }

/* Loading state */
#payment-loading {
  min-height: 300px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

/* Error state */
#payment-error {
  min-height: 300px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

/* Payment Detail Modal Styles */
#paymentDetailModal .modal-dialog {
  max-width: 800px;
}

#paymentDetailModal .card {
  border: 1px solid #e3e6f0;
}

#paymentDetailModal .card-header {
  background-color: #f8f9fc !important;
  border-bottom: 1px solid #e3e6f0;
  font-weight: 600;
}

#paymentDetailModal .row.mb-2 {
  border-bottom: 1px solid #f1f1f1;
  padding-bottom: 0.5rem;
  margin-bottom: 0.5rem;
}

#paymentDetailModal .row.mb-2:last-child {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

/* Loading, Error states */
#paymentDetailLoading,
#paymentDetailError {
  min-height: 200px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

/* Responsive */
@media (max-width: 768px) {
  #paymentDetailModal .modal-dialog {
    margin: 1rem;
  }
  
  #paymentDetailModal .row.mb-2 .col-5,
  #paymentDetailModal .row.mb-2 .col-7 {
    flex: 0 0 100%;
    max-width: 100%;
  }
  
  #paymentDetailModal .row.mb-2 .col-5 {
    margin-bottom: 0.25rem;
    font-weight: 600;
  }
}
</style>