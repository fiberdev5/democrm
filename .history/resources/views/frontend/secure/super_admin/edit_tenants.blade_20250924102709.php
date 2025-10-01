<!-- Header -->
<div class="card-header text-white d-flex align-items-center justify-content-between p-3" >
  <!-- Add this hidden input in your modal -->
<input type="hidden" id="current-tenant-id" value="{{ $tenant->id ?? '' }}">
  <div class="d-flex align-items-center">
    <div>
      <h4 class="py-2 mb-0" style="padding: 16px;">
      <span style="text-transform: uppercase;">{{ $tenant->firma_adi }} </span>
    </h4>
    </div>
  </div>
  <button type="button" class="btn-close" style="padding-left: 21px" data-bs-dismiss="modal"></button>
</div>

<!-- Body -->
<div class="card-body p-3">
  <!-- Nav Tabs - 3 Tab -->
  <div class="nav nav-pills nav-fill mb-3" role="tablist">
    <!-- İlk Tab: Müşteri ve Abonelik Bilgileri -->
    <button class="nav-link active" id="customer-subscription-tab" data-bs-toggle="pill" data-bs-target="#customer-subscription" type="button">
      <i class="fas fa-building me-1"></i>Müşteri ve Abonelik Bilgileri
    </button>
    
    <!-- İkinci Tab: Ödeme Bilgileri -->
    <button class="nav-link" id="payment-info-tab" data-bs-toggle="pill" data-bs-target="#payment-info" type="button">
      <i class="fas fa-money-bill-wave me-1"></i>Ödeme Bilgileri
    </button>

    <!-- Üçüncü Tab: Servis İstatistikleri -->
    <button class="nav-link" id="service-stats-tab" data-bs-toggle="pill" data-bs-target="#service-stats" type="button">
      <i class="fas fa-chart-bar me-1"></i>Servis İstatistikleri
    </button>
  </div>

  <!-- Tab Content -->
  <div class="tab-content">
    
    <!-- İLK TAB: MÜŞTERİ VE ABONELİK BİLGİLERİ -->
    <div class="tab-pane fade show active" id="customer-subscription">
      <div class="row g-1">
        <!-- Firma Bilgileri -->
        <div class="col-md-6">
          <div class="tenant-info-card">
            <h6 class="tenant-info-card-title">Firma Bilgileri</h6>
            <div class="tenant-info-content">
              <div class="tenant-info-item">
                <small class="text-muted">Firma Adı:</small>
                <span class="tenant-info-value fw-bold">{{ $tenant->firma_adi }}</span>
              </div>
              <div class="tenant-info-item">
                <small class="text-muted">Adres:</small>
                <span class="tenant-info-value">{{ $tenant->adres }}</span>
              </div>
              <div class="tenant-info-item">
                <small class="text-muted">İletişim Kişisi:</small>
                <span class="tenant-info-value">{{ $tenant->name ?? '-' }}</span>
              </div>
              <div class="tenant-info-item">
                <small class="text-muted">Vergi No:</small>
                <span class="tenant-info-value">{{ $tenant->vergiNo ?? '-' }}</span>
              </div>
              <div class="tenant-info-item">
                <small class="text-muted">Vergi Dairesi:</small>
                <span class="tenant-info-value">{{ $tenant->vergiDairesi ?? '-' }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- İletişim Bilgileri -->
        <div class="col-md-6">
          <div class="tenant-info-card">
            <h6 class="tenant-info-card-title">İletişim Bilgileri</h6>
            <div class="tenant-info-content">
              <div class="tenant-info-item">
                <small class="text-muted">Telefon:</small>
                <span class="tenant-info-value">{{ $tenant->tel1 ?? $tenant->tel2 }}</span>
              </div>
              <div class="tenant-info-item">
                <small class="text-muted">E-posta:</small>
                <span class="tenant-info-value">{{ $tenant->eposta }}</span>
              </div>
              <div class="tenant-info-item">
                <small class="text-muted">Kayıt tarihi:</small>
                <span class="tenant-info-value">{{ $tenant->created_at->format('d.m.Y H:i') }}</span>
              </div>
              <div class="tenant-info-item">
                <small class="text-muted">Bitiş tarihi:</small>
                <span class="tenant-info-value">{{ \Carbon\Carbon::parse($tenant->bitisTarihi)->format('d.m.Y') }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Abonelik Bilgileri -->
      <div class="tenant-subscription-section">
        <div class="tenant-subscription-header">
          <h6 class="tenant-subscription-title">Abonelik Bilgileri</h6>
        </div>
        
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
          <div class="subscription-content">
            <!-- Abonelik Detayları - Tam Genişlik -->
            <div class="tenant-subscription-details">
              <div class="tenant-detail-row">
                <div class="tenant-detail-label">Paket:</div>
                <div class="tenant-detail-value">
                  @if($isTrialFromTenant)
                    Ücretsiz Deneme
                  @else
                    {{ $currentSubscription->plansubs->name }}
                  @endif
                </div>
              </div>
              <div class="tenant-detail-row">
                <div class="tenant-detail-label">Durum:</div>
                <div class="tenant-detail-value">
                  @if($isTrialFromTenant)
                    <span class="tenant-status-active">Deneme</span>
                  @elseif($currentSubscription->status === 'active')
                    <span class="tenant-status-active">Aktif</span>
                  @elseif($currentSubscription->status === 'trial')
                    <span class="tenant-status-trial">Deneme</span>
                  @else
                    <span class="tenant-status-inactive">{{ ucfirst($currentSubscription->status) }}</span>
                  @endif
                </div>
              </div>
              <div class="tenant-detail-row">
                <div class="tenant-detail-label">Fiyat:</div>
                <div class="tenant-detail-value price">
                  @if($isTrialFromTenant)
                    Ücretsiz
                  @else
                    {{ $currentSubscription->plansubs->getFormattedPrice() }}
                  @endif
                </div>
              </div>
              <div class="tenant-detail-row">
                <div class="tenant-detail-label">Kalan Süre:</div>
                <div class="tenant-detail-value tenant-status-remaining">
                  @if($isTrialFromTenant)
                    {{ $tenant->trial_ends_at->diffInDays(now()) }} gün
                  @else
                    {{ $currentSubscription->getRemainingDays() }} gün
                  @endif
                </div>
              </div>
              <div class="tenant-detail-row">
                <div class="tenant-detail-label">Bitiş Tarihi:</div>
                <div class="tenant-detail-value">
                  @if($isTrialFromTenant)
                    {{ $tenant->trial_ends_at->format('d.m.Y') }}
                  @else
                    {{ $currentSubscription->ends_at ? $currentSubscription->ends_at->format('d.m.Y') : 'Belirsiz' }}
                  @endif
                </div>
              </div>
              <div class="tenant-detail-row">
                <div class="tenant-detail-label">Kullanıcı:</div>
                <div class="tenant-detail-value">
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
              <div class="tenant-detail-row">
                <div class="tenant-detail-label">Bayi:</div>
                <div class="tenant-detail-value">
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
              <div class="tenant-detail-row">
                <div class="tenant-detail-label">Stok Ürün:</div>
                <div class="tenant-detail-value">
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
              <div class="tenant-detail-row">
                <div class="tenant-detail-label">Konsinye:</div>
                <div class="tenant-detail-value">
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
              @if(!$isTrialFromTenant && isset($currentSubscription->plansubs->limits['tickets_per_month']))
                <div class="tenant-detail-row">
                  <div class="tenant-detail-label">Aylık Destek:</div>
                  <div class="tenant-detail-value">
                    @if($currentSubscription->plansubs->limits['tickets_per_month'] == -1)
                      Sınırsız
                    @elseif($currentSubscription->plansubs->limits['tickets_per_month'] == 0)
                      Yok
                    @else
                      {{ number_format($currentSubscription->plansubs->limits['tickets_per_month']) }}
                    @endif
                  </div>
                </div>
              @endif
              @if(!$isTrialFromTenant && isset($currentSubscription->plansubs->limits['storage_gb']))
                <div class="tenant-detail-row">
                  <div class="tenant-detail-label">Depolama:</div>
                  <div class="tenant-detail-value">
                    @if($currentSubscription->plansubs->limits['storage_gb'] == -1)
                      {{ $storageInfo['current_usage_formatted'] ?? '0 B' }} / Sınırsız
                    @elseif($currentSubscription->plansubs->limits['storage_gb'] == 0)
                      Yok
                    @else
                      @php
                        $totalGB = isset($storageInfo['total_limit_gb']) ? $storageInfo['total_limit_gb'] : 0;
                        $extraStorage = isset($storageInfo['extra_storage_gb']) ? $storageInfo['extra_storage_gb'] : 0;
                        $percentage = isset($storageInfo['usage_percentage']) ? $storageInfo['usage_percentage'] : 0;
                      @endphp
                      {{ $storageInfo['current_usage_formatted'] ?? '0 B' }} / {{ number_format($totalGB, 0) }} GB
                      @if($extraStorage > 0)
                        <small class="storage-extra">(+{{ number_format($extraStorage, 0) }} GB ek)</small>
                      @endif
                      @if($percentage > 0)
                        <div class="storage-progress">
                          <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ min($percentage, 100) }}%"></div>
                          </div>
                        </div>
                      @endif
                    @endif
                  </div>
                </div>
              @endif
            </div>

            <!-- Depolama Detayları Accordion -->
            <div class="tenant-storage-accordion-section mt-3">
              <div class="accordion tenant-storage-accordion" id="storageAccordion">
                <div class="accordion-item">
                  <h2 class="accordion-header" id="storageHeading">
                    <button class="accordion-button collapsed" type="button" 
                            data-bs-toggle="collapse" data-bs-target="#storageCollapse" 
                            aria-expanded="false" aria-controls="storageCollapse">
                      Depolama Detayları
                    </button>
                  </h2>
                  <div id="storageCollapse" class="accordion-collapse collapse" 
                       aria-labelledby="storageHeading" data-bs-parent="#storageAccordion">
                    <div class="accordion-body">
                      <div id="storage-breakdown-container">
                        <div class="text-center py-3">
                          <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                          </div>
                          <p class="mt-2 mb-0 small text-muted">Depolama bilgileri yükleniyor...</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- subscription-content sonu -->

          <!-- Abonelik Geçmişi -->
          @if(isset($subscriptionHistory) && $subscriptionHistory->count() > 1)
            <div class="subscription-history">
              <h6 class="history-title">Abonelik Geçmişi</h6>
              <div class="history-table-wrapper">
                <table class="history-table">
                  <thead>
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
                        <td>{{ ucfirst($subscription->status) }}</td>
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
          <div class="no-subscription">
            <div class="no-subscription-text">
              Bu firmanın aktif bir aboneliği bulunmamaktadır.
            </div>
          </div>
        @endif
      </div>
      <!-- tenant-subscription-section sonu -->
    </div>
    <!-- customer-subscription tab-pane sonu -->

    <!-- İKİNCİ TAB: ÖDEME BİLGİLERİ -->
    <div class="tab-pane fade" id="payment-info">
      <!-- Loading spinner -->
      <div id="payment-loading" class="tenant-payment-main-loading text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Yükleniyor...</span>
        </div>
        <p class="mt-2 text-muted">Ödeme bilgileri yükleniyor...</p>
      </div>
      
      <!-- Ana içerik -->
      <div id="payment-content" style="display: none;">
        
        <!-- Ödeme Özeti Kartları -->
        <div class="row tenant-payment-cards">
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
        
        <!-- Ödeme Geçmişi -->
        <div class="card tenant-payment-history-card">
          <div class="card-header bg-light tenant-payment-history-header">
            <div class="row align-items-center">
              <div class="col-md-6">
                <h6 class="mb-0">Ödeme Geçmişi</h6>
              </div>
              <div class="col-md-6 text-end">
                <div class="d-flex gap-2 justify-content-end">
                  <!-- Durum Filtresi -->
                  <select class="form-select form-select-sm" id="status-filter" style="width: 120px;">
                    <option value="">Tüm Durumlar</option>
                    <option value="completed">Tamamlandı</option>
                    <option value="pending">Bekliyor</option>
                    <option value="failed">Başarısız</option>
                    <option value="refunded">İade Edildi</option>
                    <option value="canceled">İptal Edildi</option>
                  </select>
                  
                  <!-- Tür Filtresi -->
                  <select class="form-select form-select-sm" id="type-filter" style="width: 120px;">
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
              <table class="table table-hover mb-0 tenant-payments-table" id="payments-table">
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
            <div id="no-payments-message" class="tenant-no-payments-message text-center py-5" style="display: none;">
              <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
              <h5 class="text-muted">Henüz ödeme kaydı bulunmamaktadır</h5>
              <p class="text-muted">Bu firmaya ait herhangi bir ödeme işlemi görünmüyor.</p>
            </div>
          </div>
        </div>
        
        <!-- Ödeme Özeti - Sadece Toplam -->
        <div class="tableToplamaAlani tenant-payment-summary">
          <div class="row r1">
            <div class="sol"><strong>Toplam Ödenen</strong></div>
            <div class="sag">
              <div class="tur t1 completedAmount"><span>Tamamlanan:</span></div>
              <div class="tur t2 pendingAmount"><span>Bekleyen:</span></div>
              <div class="tur t3 failedAmount"><span>Başarısız:</span></div>
              <div class="tur t5 refundedAmount"><span>İade:</span></div>
              <div class="tur t4 totalPaidAmount"><span>Toplam:</span></div>
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
      <div id="payment-error" class="tenant-payment-main-error text-center py-5" style="display: none;">
        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
        <h5 class="text-warning">Ödeme bilgileri yüklenemedi</h5>
        <p class="text-muted">Bir hata oluştu. Lütfen sayfayı yenileyin veya daha sonra tekrar deneyin.</p>
        <button class="btn btn-outline-primary" onclick="PaymentModule.loadPaymentInfo(PaymentModule.currentTenantId)">
          <i class="fas fa-sync-alt me-1"></i>Tekrar Dene
        </button>
      </div>
    </div>
    <!-- payment-info tab-pane sonu -->

    <!-- ÜÇÜNCÜ TAB: SERVİS İSTATİSTİKLERİ -->
    <div class="tab-pane fade" id="service-stats">
      <!-- Servis İstatistikleri -->
      <div class="border rounded p-2">
        <!-- Period Navigation - Daha dar tasarım -->
        <div class="tenant-service-period-nav mb-2" role="tablist">
          @foreach($periodStats as $key => $period)
            <button class="tenant-service-period-btn {{ $key === 'bugun' ? 'active' : '' }}" 
                    data-period="{{ $key }}" 
                    data-target="#period-{{ $key }}"
                    type="button">
              <span class="tenant-period-label">{{ $period['label'] }}</span>
              <span class="tenant-period-count">{{ $period['toplam'] }}</span>
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
                  <div class="tenant-service-stats-card">
                    <div class="tenant-service-stats-card-header">
                      <span class="tenant-stats-title">Markalar</span>
                    </div>
                    <div class="tenant-service-stats-card-body">
                      @forelse($period['markalar'] as $marka)
                        <div class="tenant-stats-empty">Kayıt yok</div>
                      @endforelse
                    </div>
                  </div>
                </div>

                <!-- Personeller -->
                <div class="col-lg-3 col-md-6">
                  <div class="tenant-service-stats-card">
                    <div class="tenant-service-stats-card-header">
                      <span class="tenant-stats-title">Personeller</span>
                    </div>
                    <div class="tenant-service-stats-card-body">
                      @forelse($period['operatorler'] as $operator)
                        <div class="tenant-stats-item">
                          <span class="tenant-stats-name">{{ $operator->name }}</span>
                          <span class="tenant-stats-count">{{ $operator->sayi }}</span>
                        </div>
                      @empty
                        <div class="tenant-stats-empty">Kayıt yok</div>
                      @endforelse
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
        
        <!-- Toplam Servis Sayısı - Alt kısma taşındı -->
        <div class="tenant-service-total-footer">
          <div class="tenant-service-total-badge">
            <span class="tenant-service-total-number">{{ $topServisSayisi ?? 0 }}</span>
            <small class="tenant-service-total-label">Toplam Genel Servis Sayısı</small>
          </div>
        </div>
      </div>
    </div>
    <!-- service-stats tab-pane sonu -->

  </div>
  <!-- tab-content sonu -->
</div>
<!-- card-body sonu -->

<!-- Footer -->
<div class="card-footer bg-light d-flex justify-content-end align-items-center p-2">
  <div>
    <form action="{{ route('super.admin.tenant.toggle.status', [$tenant->id,$tenant->id]) }}" method="POST" style="display: inline;">
      @csrf
      @if($tenant->status == 1)
        <button type="submit" class="btn btn-danger btn-sm">Pasif Yap</button>
      @else
        <button type="submit" class="btn btn-success btn-sm">Aktif Et</button>
      @endif
    </form>
  </div>
</div>

<!-- Payment Detail Modal -->
<div class="modal fade tenant-payment-detail-modal" id="paymentDetailModal" tabindex="-1" aria-labelledby="paymentDetailModalLabel" aria-hidden="true" style="padding-top: 70px; background: rgba(0, 0, 0, 0.50);">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title text-white" id="paymentDetailModalLabel">
           ÖDEME DETAYLARI
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="paymentDetailContent">
        <!-- Loading State -->
        <div id="paymentDetailLoading" class="tenant-payment-loading text-center py-4">
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
        <div id="paymentDetailError" class="tenant-payment-error" style="display: none;" class="text-center py-4">
          <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
          <h5 class="text-warning">Detaylar yüklenemedi</h5>
          <p class="text-muted">Bir hata oluştu. Lütfen tekrar deneyin.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
/// Global depolama fonksiyonu - BASİT VERSİYON
function loadStorageDetails() {
  const tenantId = $('#current-tenant-id').val();
  const container = $('#storage-breakdown-container');
  
  if (!tenantId) {
    container.html('<div class="text-center py-3"><small class="text-muted">Tenant ID bulunamadı</small></div>');
    return;
  }
  
  $.ajax({
    url: `/super-admin/tenant/${tenantId}/storage-details`,
    method: 'GET',
    success: function(response) {
      console.log('Storage Response:', response);
      
      if (response.success) {
        let storageHtml = '<div class="storage-breakdown-list">';
        
        // Servis Fotoğrafları
        storageHtml += `
          <div class="storage-item">
            <div class="storage-type">Servis Fotoğrafları</div>
            <div class="storage-size">${response.details.service_photos.count} adet</div>
          </div>`;
        
        // Stok Fotoğrafları
        storageHtml += `
          <div class="storage-item">
            <div class="storage-type">Stok Fotoğrafları</div>
            <div class="storage-size">${response.details.stock_photos.count} adet</div>
          </div>`;
        
        // Diğer Dosyalar
        storageHtml += `
          <div class="storage-item">
            <div class="storage-type">Diğer Dosyalar</div>
            <div class="storage-size">${response.details.other_files.total_count} adet</div>
          </div>`;
        
        // Toplam
        let totalSize = response.storage_info.current_usage_formatted || '0 B';
        storageHtml += `
          <div class="storage-item total">
            <div>Toplam Kullanım</div>
            <div class="storage-size">${totalSize}</div>
          </div>
        </div>`;
        
        container.html(storageHtml);
        
      } else {
        container.html(`
          <div class="text-center py-3">
            <small class="text-muted text-danger">Depolama bilgileri yüklenemedi</small>
          </div>
        `);
      }
    },
    error: function(xhr, status, error) {
      console.error('Storage AJAX Error:', xhr.responseText);
      container.html(`
        <div class="text-center py-3">
          <small class="text-muted text-danger">Hata oluştu</small>
        </div>
      `);
    }
  });
}

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
      url: `/super-admin/tenant/${currentTenantId}/payment/${type}/${paymentId}`,
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
  
  // Ödeme detay HTML'i oluştur (Depolama bilgileri kaldırıldı)
  function createPaymentDetailHtml(payment) {
    const statusBadge = getStatusBadge(payment.status);
    const typeBadge = getTypeBadge(payment.type, payment.type_label);
    
    return `
      <div class="row">
        <div class="col-md-6">
          <div class="card mb-3">
            <div class="card-header bg-light ms-2">
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
            <div class="card-header bg-light ms-2">
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
                <div class="col-5"><strong>Oluşturulma Tarihi:</strong></div>
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
          updatePaymentCards(response.summary);  // Üst kartlar
          updatePaymentSummary(response.summary); // Alt toplam alanı
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
  // Üst kartlar
  function updatePaymentCards(summary) {
      $('#summary-completed').text('₺' + (summary.completed_formatted || '0,00'));
      $('#summary-pending').text('₺' + (summary.pending_formatted || '0,00'));
      $('#summary-failed').text('₺' + (summary.failed_formatted || '0,00'));
      $('#summary-refunded').text('₺' + (summary.refunded_formatted || '0,00'));
  }
  // Alt toplam alanını güncelle
   function updatePaymentSummary(summary) {
    $('.completedAmount').html('<span>Tamamlanan:</span> ₺' + (summary.completed_formatted || '0,00'));
    $('.pendingAmount').html('<span>Bekleyen:</span> ₺' + (summary.pending_formatted || '0,00'));
    $('.failedAmount').html('<span>Başarısız:</span> ₺' + (summary.failed_formatted || '0,00'));
    $('.refundedAmount').html('<span>İade:</span> ₺' + (summary.refunded_formatted || '0,00'));
    $('.totalPaidAmount').html('<span>Toplam:</span> ₺' + (summary.total_amount_formatted || '0,00'));
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
  
  // Satır tıklama olayı ekle
  $('.payment-row-clickable').off('click').on('click', function() {
    const paymentType = $(this).data('payment-type');
    const paymentId = $(this).data('payment-id');
    showPaymentDetail(paymentType, paymentId);
  });
}
  // Ödeme satırı oluştur
function createPaymentRow(payment) {
  const statusBadge = getStatusBadge(payment.status);
  const typeBadge = getTypeBadge(payment.type, payment.type_label);
  const formattedDate = formatDate(payment.created_at);
  const paidDate = payment.paid_at ? formatDate(payment.paid_at) : null;
  
  return `
    <tr class="tenant-payment-row-clickable" data-payment-type="${payment.type}" data-payment-id="${payment.id}" style="cursor: pointer; height: 60px;">
      <td class="align-middle">
        <div class="fw-medium">${formattedDate}</div>
        ${paidDate ? `<small class="text-success">Ödendi: ${paidDate}</small>` : ''}
      </td>
      <td class="align-middle">${typeBadge}</td>
      <td class="align-middle">
        <div class="fw-medium">${payment.description || '-'}</div>
      </td>
      <td class="align-middle">
        <span class="fw-bold">₺${formatMoney(payment.amount || 0)}</span>
        ${payment.currency && payment.currency !== 'TRY' ? `<br><small class="text-muted">${payment.currency}</small>` : ''}
      </td>
      <td class="align-middle">
        <div>${payment.payment_method || 'Belirtilmemiş'}</div>
        ${payment.gateway ? `<small class="text-muted">${payment.gateway}</small>` : ''}
      </td>
      <td class="align-middle">${statusBadge}</td>
      <td class="align-middle">
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
      'completed': '<span >Tamamlandı</span>',
      'pending': '<span >Bekliyor</span>',
      'failed': '<span >Başarısız</span>',
      'refunded': '<span >İade Edildi</span>',
      'canceled': '<span >İptal Edildi</span>'
    };
    return badges[status] || '<span >Bilinmeyen</span>';
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
        let invoPath = '/' + payment.invoice_path;
        buttons += `<a href="${invoPath}" class="btn btn-outline-primary btn-sm" title="Faturayı Görüntüle" target="_blank">
                      <i class="fas fa-file-pdf"></i>
                    </a>`;
    }
    
    // Detay butonu
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
        
        // Depolama detaylarını da yükle
        setTimeout(function() {
          if ($('#storage-breakdown-container').length > 0) {
            loadStorageDetails();
          }
        }, 500);
      }
    });
    // Storage accordion açıldığında veri yükle
    $(document).off('shown.bs.collapse', '#storageCollapse').on('shown.bs.collapse', '#storageCollapse', function() {
      if ($('#storage-breakdown-container').length > 0) {
        loadStorageDetails();
      }
    });

    // Alternatif olarak, editTenant class'ına sahip elementlere click event'i ekle
    $(document).off('click', '.editTenant').on('click', '.editTenant', function() {
      const tenantId = $(this).data('bs-id') || $(this).attr('data-bs-id');
      
      if (tenantId) {
        currentTenantId = tenantId;
        $('#current-tenant-id').val(tenantId);
        
        // Depolama detaylarını da yükle
        setTimeout(function() {
          if ($('#storage-breakdown-container').length > 0) {
            loadStorageDetails();
          }
        }, 500);
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

    // Customer subscription tab'ına tıklandığında depolama detaylarını yükle
    $(document).off('shown.bs.tab', '#customer-subscription-tab').on('shown.bs.tab', '#customer-subscription-tab', function() {
      setTimeout(function() {
        if ($('#storage-breakdown-container').length > 0) {
          loadStorageDetails();
        }
      }, 500);
    });

    // Filtre değişiklikleri
    $(document).off('change', '#status-filter, #type-filter').on('change', '#status-filter, #type-filter', function() {
      applyFilters();
    });

    // Servis istatistikleri period switching
    $(document).off('click', '.service-period-btn').on('click', '.service-period-btn', function(e) {
      e.preventDefault();
      
      var $this = $(this);
      var targetSelector = $this.data('target');
      
      // Tab navigation
      $('.service-period-btn').removeClass('active');
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
  
  // Kısa gecikme ile depolama bilgilerini yükle
  setTimeout(function() {
    if ($('#storage-breakdown-container').length > 0) {
      loadStorageDetails();
    }
  }, 1000);
});
</script>

<style>
/* TEKNİK TENANT MODAL ÖZEL STİLLERİ */

/* Info Cards - Müşteri Bilgi Kartları */
.tenant-info-card {
  background: #ffffff;
  border: 2px solid #e5e7eb;
  border-radius: 6px;
  padding: 10px;
  height: 96%;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.tenant-info-card-title {
  color: #374151;
  font-weight: 600;
  font-size: 0.9rem;
  padding-bottom: 4px;
  border-bottom: 1px solid #e5e7eb;
}

.tenant-info-content {
  display: flex;
  flex-direction: column;
}

.tenant-info-item {
  display: flex;
  align-items: center;
  gap: 6px;
  min-height: 18px;
  padding: 2px 0;
}

.tenant-info-value {
  color: #1f2937;
  font-size: 0.8rem;
  font-weight: 500;
}

.tenant-info-item small {
  font-size: 0.7rem;
  color: #6b7280;
  min-width: 35px;
}

/* Subscription Section - Abonelik Bölümü */
.tenant-subscription-section {
  margin-top: 15px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.tenant-subscription-header {
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
  padding: 8px 12px;
  border-radius: 6px 6px 0 0;
}

.tenant-subscription-title {
  color: #374151;
  font-weight: 600;
  font-size: 0.9rem;
  margin: 0;
}

/* Subscription Details - Abonelik Detayları */
.tenant-subscription-details {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  padding: 10px;
  width: 100%;
}

.tenant-detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 3px 0;
  border-bottom: 1px solid #e2e8f0;
}

.tenant-detail-row:last-child {
  border-bottom: none;
}

.tenant-detail-label {
  font-size: 0.85rem;
  min-width: 100px;
  color: #1e293b;
  font-weight: 600;
}

.tenant-detail-value {
  font-size: 0.8rem;
  color: #64748b;
}

/* Status Styling - Durum Stilleri */
.tenant-status-active {
  color: #16a34a;
}

.tenant-status-trial {
  color: #ea580c;
}

.tenant-status-inactive {
  color: #dc2626;
}

.tenant-status-remaining {
  color: #7c3aed;
}

/* Storage Accordion Styles - Depolama Accordion */
.tenant-storage-accordion-section {
  margin-top: 5px !important;
}

.tenant-storage-accordion .accordion-button {
  background-color: #f8f9fa;
  font-weight: 500;
  border: none;
  padding: 8px 12px;
  font-size: 0.8rem;
}

.tenant-storage-accordion .accordion-button:not(.collapsed) {
  color: #212529 !important;
  background-color: #e7f1ff;
  box-shadow: none !important;
}

.tenant-storage-accordion .accordion-body {
  padding: 8px 12px;
  background: #ffffff;
}

/* Storage breakdown listesi */
.tenant-storage-breakdown-list {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.tenant-storage-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 6px 8px;
  background: #f8f9fa;
  border-radius: 4px;
  border: 1px solid #e9ecef;
}

.tenant-storage-item.total {
  margin-top: 5px;
  font-weight: 600;
}

.tenant-storage-type {
  font-size: 0.85rem;
  min-width: 100px;
  color: #1e293b;
  font-weight: 600;
}

.tenant-storage-size {
  font-size: 0.8rem;
}

.tenant-storage-item.total .tenant-storage-type,
.tenant-storage-item.total .tenant-storage-size {
  font-weight: 700;
}

/* ÖDEME BİLGİLERİ ÖZEL STİLLERİ */

/* Ödeme Kartları */
.tenant-payment-cards .card {
  transition: transform 0.2s ease;
}

.tenant-payment-cards .card:hover {
  transform: translateY(-2px);
}

/* Ödeme Geçmişi Kartı */
.tenant-payment-history-card {
  margin-bottom: 1rem !important;
}

.tenant-payment-history-header {
  padding: 12px 16px !important;
}

/* Ödeme Tablosu */
.tenant-payments-table {
  font-size: 0.875rem;
}

.tenant-payments-table th {
  font-weight: 600;
  color: #495057;
  border-bottom: 2px solid #dee2e6;
  padding: 12px 8px !important;
  text-align: left !important;
  background-color: #f8f9fa;
}

.tenant-payments-table td {
  vertical-align: middle;
  padding: 12px 8px !important;
  text-align: left !important;
}

.tenant-payment-row {
  height: 65px !important;
  cursor: pointer;
}

.tenant-payment-row:hover {
  background-color: #f8f9fa !important;
}

/* Buton Grupları */
.tenant-payment-actions .btn-group-sm .btn {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
  border-radius: 0 !important;
  border: 1px solid #ddd;
}

.tenant-payment-actions .btn-group-sm .btn:first-child {
  border-top-left-radius: 0 !important;
  border-bottom-left-radius: 0 !important;
}

.tenant-payment-actions .btn-group-sm .btn:last-child {
  border-top-right-radius: 0 !important;
  border-bottom-right-radius: 0 !important;
}

.tenant-payment-actions .btn-group-sm .btn:hover {
  background-color: #f8f9fa !important;
  border-color: #adb5bd !important;
  color: #495057 !important;
}

.tenant-payment-actions .btn-outline-primary:hover {
  background-color: #0d6efd !important;
  border-color: #0d6efd !important;
  color: white !important;
}

.tenant-payment-actions .btn-outline-info:hover {
  background-color: #0dcaf0 !important;
  border-color: #0dcaf0 !important;
  color: white !important;
}

/* Ödeme Toplam Alanı */
.tenant-payment-summary {
  margin-top: 25px !important;
  margin-bottom: 0;
  clear: both;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  border-radius: 6px;
  overflow: hidden;
}

.tenant-payment-summary .r1 {
  background: #198754 !important;
}

.tenant-payment-summary .row {
  margin: 1px;
  color: #fff;
  font-weight: 700;
}

.tenant-payment-summary .r1 .sol {
  border-right: 1px solid #198754;
}

.tenant-payment-summary .row .sol {
  text-align: right;
  padding: 20px 15px !important;
  border: 1px solid #e0e0e0;
  float: left;
  background: #e8f5e8 !important;
  color: #000;
  width: calc(100% - 230px);
  font-size: 16px !important;
  font-weight: 700;
}

.tenant-payment-summary .row .sag {
  padding: 5px 15px;
  float: right;
  width: 230px;
  text-align: left;
  font-size: 13px!important;
}

.tenant-payment-summary .row .sag .tur span {
  width: 97px;
  display: inline-block;
  text-align: left;
}

.tenant-payment-summary .row .sag .t4 {
  font-weight: 900 !important;
  font-size: 14px !important;
  border-top: 1px solid rgba(255,255,255,0.3);
  padding-top: 8px !important;
  margin-top: 5px !important;
}

/* SERVİS İSTATİSTİKLERİ ÖZEL STİLLERİ */

/* Servis Toplam Footer */
.tenant-service-total-footer {
  border-top: 1px solid #eee;
  padding-top: 0.75rem;
  margin-top: 1rem;
  text-align: center;
}

.tenant-service-total-badge {
  text-align: center;
}

.tenant-service-total-number {
  display: block;
  font-size: 1.3rem;
  font-weight: 700;
  color: #2c3e50;
  line-height: 1.2;
}

.tenant-service-total-label {
  display: block;
  font-size: 0.75rem;
  color: #6c757d;
  font-weight: 500;
}

/* Period Navigation */
.tenant-service-period-nav {
  display: flex;
  gap: 0.25rem;
  flex-wrap: wrap;
}

.tenant-service-period-btn {
  flex: 1;
  min-width: 65px;
  background: #f8f9fa;
  border: 1px solid #dee2e6;
  border-radius: 0.25rem;
  padding: 0.4rem 0.2rem;
  text-align: center;
  transition: all 0.2s ease;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 43px;
}

.tenant-service-period-btn.active {
  background: #ffffff;
  border-color: #217aff;
  box-shadow: 0 0 0 0.1rem rgba(13, 110, 253, 0.1);
}

.tenant-period-label {
  font-size: 0.7rem;
  font-weight: 500;
  color: #495057;
  margin-bottom: 0.1rem;
  line-height: 1;
}

.tenant-period-count {
  font-size: 0.8rem;
  font-weight: 600;
  color: #2c3e50;
  line-height: 1;
}

/* Stats Cards */
.tenant-service-stats-card {
  background: #ffffff;
  border: 1px solid #e3e6f0;
  border-radius: 0.375rem;
  height: 100%;
}

.tenant-service-stats-card-header {
  background: #f8f9fc;
  border-bottom: 1px solid #e3e6f0;
  padding: 0.5rem 0.75rem;
  border-radius: 0.375rem 0.375rem 0 0;
}

.tenant-stats-title {
  font-size: 0.8rem;
  font-weight: 600;
  color: #5a5c69;
}

.tenant-service-stats-card-body {
  padding: 0.5rem 0.75rem;
  max-height: 180px;
  overflow-y: auto;
}

.tenant-stats-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.375rem 0;
  border-bottom: 1px solid #f1f1f1;
}

.tenant-stats-item:last-child {
  border-bottom: none;
}

.tenant-stats-name {
  flex: 1;
  font-size: 0.8rem;
  color: #495057;
  word-break: break-word;
}

.tenant-stats-count {
  background: #e9ecef;
  color: #495057;
  font-size: 0.7rem;
  font-weight: 600;
  padding: 0.2rem 0.4rem;
  border-radius: 0.25rem;
  min-width: 25px;
  text-align: center;
}

.tenant-stats-empty {
  font-size: 0.8rem;
  color: #6c757d;
  text-align: center;
  padding: 0.75rem 0;
  font-style: italic;
}

/* MODAL STİLLERİ */

/* Payment Detail Modal */
.tenant-payment-detail-modal .modal-dialog {
  max-width: 800px;
}

.tenant-payment-detail-modal .card {
  border: 1px solid #e3e6f0;
}

.tenant-payment-detail-modal .card-header {
  background-color: #f8f9fc !important;
  border-bottom: 1px solid #e3e6f0;
  font-weight: 600;
}

.tenant-payment-detail-modal .row.mb-2 {
  border-bottom: 1px solid #f1f1f1;
  padding-bottom: 0.5rem;
  margin-bottom: 0.5rem;
}

.tenant-payment-detail-modal .row.mb-2:last-child {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

/* Loading, Error states */
.tenant-payment-loading,
.tenant-payment-error {
  min-height: 200px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.tenant-payment-main-loading {
  min-height: 300px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.tenant-payment-main-error {
  min-height: 300px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

/* Modal title white color fix */
.tenant-payment-detail-modal .modal-header .modal-title {
  color: white !important;
}

/* Boş durum mesajı */
.tenant-no-payments-message i {
  opacity: 0.5;
}

/* RESPONSIVE DESIGN */
@media (max-width: 768px) {
  .tenant-subscription-content .row {
    flex-direction: column;
  }
  
  .tenant-detail-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
  }
  
  .tenant-detail-label {
    min-width: auto;
    font-weight: 600;
  }
  
  .tenant-detail-value {
    text-align: left;
  }
  
  .tenant-service-period-nav {
    flex-direction: column;
  }
  
  .tenant-service-period-btn {
    min-width: auto;
  }
  
  .tenant-payment-detail-modal .modal-dialog {
    margin: 1rem;
  }
  
  .tenant-payment-detail-modal .row.mb-2 .col-5,
  .tenant-payment-detail-modal .row.mb-2 .col-7 {
    flex: 0 0 100%;
    max-width: 100%;
  }
  
  .tenant-payment-detail-modal .row.mb-2 .col-5 {
    margin-bottom: 0.25rem;
    font-weight: 600;
  }
}

@media (max-width: 576px) {
  .tenant-info-card {
    padding: 12px;
  }
  
  .tenant-subscription-details {
    padding: 12px;
  }
  
  .row > [class*="col-lg-3"] {
    flex: 0 0 100%;
    max-width: 100%;
    margin-bottom: 1rem;
  }
  
  .d-flex.gap-2 {
    flex-direction: column;
    gap: 0.5rem !important;
  }
  
  .form-select-sm {
    width: 100% !important;
  }
  
  .tenant-service-stats-card-body {
    padding: 0.5rem;
  }
  
  .tenant-stats-item {
    padding: 0.375rem 0;
  }
}

/* GENEL STİLLER */
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

.badge.bg-opacity-10 {
  --bs-bg-opacity: 0.1;
}

.text-primary { color: #0d6efd !important; }
.text-success { color: #198754 !important; }
.text-warning { color: #ffc107 !important; }
.text-danger { color: #dc3545 !important; }
.text-info { color: #0dcaf0 !important; }

.border-success { border-color: #198754 !important; }
.border-warning { border-color: #ffc107 !important; }
.border-danger { border-color: #dc3545 !important; }
.border-info { border-color: #0dcaf0 !important; }

.spinner-border {
  width: 3rem;
  height: 3rem;
}

.pagination-sm .page-link {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

.tab-content > .tab-pane {
  padding: 0;
}
</style>