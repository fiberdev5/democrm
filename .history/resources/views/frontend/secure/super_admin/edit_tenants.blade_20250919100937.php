<!-- Mevcut modal body'ye eklenecek nav yapısı -->

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
        <div>
          <small class="text-muted">
            Toplam <span id="total-payments-count">0</span> ödeme kaydı
          </small>
        </div>
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
      <button class="btn btn-outline-primary" onclick="loadPaymentInfo()">
        <i class="fas fa-sync-alt me-1"></i>Tekrar Dene
      </button>
    </div>
  </div>
</div>

<script>
// Global değişkenler
let currentTenantId = null;
let allPayments = [];
let filteredPayments = [];
let currentPage = 1;
const itemsPerPage = 10;

// Modal açıldığında tetiklenir
$(document).on('shown.bs.tab', '#payment-info-tab', function () {
  const tenantId = $('.editTenant').data('bs-id');
  if (tenantId && tenantId !== currentTenantId) {
    currentTenantId = tenantId;
    loadPaymentInfo(tenantId);
  }
});

// Ödeme bilgilerini yükle
function loadPaymentInfo(tenantId) {
  showPaymentLoading();
  
  $.ajax({
    url: `/super-admin/tenant/${tenantId}/payments`,
    method: 'GET',
    success: function(response) {
      if (response.success) {
        allPayments = response.payments || [];
        updatePaymentSummary(response.summary);
        applyFilters();
        showPaymentContent();
      } else {
        showPaymentError();
      }
    },
    error: function(xhr) {
      console.error('Ödeme bilgileri yüklenirken hata:', xhr);
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
    buttons += `<a href="${payment.invoice_path}" class="btn btn-outline-primary btn-sm" title="Faturayı Görüntüle" target="_blank">
                  <i class="fas fa-file-pdf"></i>
                </a>`;
  }
  
  // Detay butonu
  buttons += `<button class="btn btn-outline-info btn-sm" onclick="showPaymentDetail('${payment.type}', ${payment.id})" title="Detayları Görüntüle">
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
      <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Önceki</a>
    </li>
  `);
  
  // Sayfa numaraları
  const startPage = Math.max(1, currentPage - 2);
  const endPage = Math.min(totalPages, currentPage + 2);
  
  for (let i = startPage; i <= endPage; i++) {
    const active = i === currentPage ? 'active' : '';
    pagination.append(`
      <li class="page-item ${active}">
        <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
      </li>
    `);
  }
  
  // Sonraki sayfa
  const nextDisabled = currentPage === totalPages ? 'disabled' : '';
  pagination.append(`
    <li class="page-item ${nextDisabled}">
      <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Sonraki</a>
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

// Ödeme detayını göster
function showPaymentDetail(type, paymentId) {
  // Modal veya detay sayfası açma işlemi
  console.log('Ödeme detayı:', type, paymentId);
  // Bu kısmı ihtiyacınıza göre implement edebilirsiniz
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

// Filtre event listener'ları
$(document).on('change', '#status-filter, #type-filter', function() {
  applyFilters();
});

// Sayfa değiştiğinde aktif sekmeyi kontrol et
$(document).on('shown.bs.modal', '#editTenantModal', function() {
  // Modal açıldığında eğer ödeme sekmesi aktifse verileri yükle
  if ($('#payment-info-tab').hasClass('active')) {
    const tenantId = $('.editTenant').data('bs-id');
    if (tenantId) {
      currentTenantId = tenantId;
      loadPaymentInfo(tenantId);
    }
  }
});

// Servis istatistikleri için mevcut period switching kodu
$(document).ready(function() {
    $('.nav-link[data-period]').on('click', function(e) {
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
});