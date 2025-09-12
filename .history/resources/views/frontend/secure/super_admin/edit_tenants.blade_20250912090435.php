<!-- Tenant Detay Modal -->
      <!-- Header -->
     <div class="card-header bg-gradient bg-primary text-white d-flex align-items-center justify-content-between p-3" style="padding: 5px!important;">
        <div class="d-flex align-items-center">
          <svg xmlns="http://www.w3.org/2000/svg" style="margin-left: 10px;"  width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 w-12 h-12 me-3"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path><path d="M10 6h4"></path><path d="M10 10h4"></path><path d="M10 14h4"></path><path d="M10 18h4"></path></svg>
          <div>
            <h4 class="mb-0">{{ $tenant->firma_adi }}</h4>
            <div class="d-flex gap-2">
              <!-- Genel Durum Badge -->
              @if($tenant->status == 1)
                <span class="badge bg-success"><i class="mdi mdi-check-circle"></i> Aktif</span>
              @else
                <span class="badge bg-danger"><i class="mdi mdi-close-circle"></i> Pasif</span>
              @endif
              
              <!-- Abonelik Durumu Badge -->
              @switch($tenant->subscription_status)
                @case('trial')
                  <span class="badge bg-warning"><i class="mdi mdi-timer"></i> Deneme</span>
                  @break
                @case('active')
                  <span class="badge bg-primary"><i class="mdi mdi-crown"></i> Aboneli</span>
                  @break
                @case('expired')
                  <span class="badge bg-secondary"><i class="mdi mdi-clock-alert"></i> Süresi Doldu</span>
                  @break
                @case('canceled')
                  <span class="badge bg-dark"><i class="mdi mdi-cancel"></i> İptal Edildi</span>
                  @break
                @default
                  <span class="badge bg-light text-dark">Belirsiz</span>
              @endswitch
            </div>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white me-3" data-bs-dismiss="modal"></button>
      </div>

      <!-- Body -->
      <div class="card-body p-4">
        <div class="row g-4">
          <!-- İletişim Bilgileri -->
          <div class="col-md-6">
            <h6 class="mb-3"><i class="mdi mdi-phone me-2"></i>İletişim Bilgileri</h6>
            <div class="mb-3 d-flex align-items-center">
              <div class="icon bg-light text-primary rounded-circle d-flex justify-content-center align-items-center me-2" style="width:30px; height:30px;">
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
              <div class="icon bg-light text-danger rounded-circle d-flex justify-content-center align-items-center me-2" style="width:30px; height:30px;">
                <i class="mdi mdi-email"></i>
              </div>
              <div>
                <small class="text-muted">E-Posta</small>
                <div class="fw-semibold text-dark">{{ $tenant->eposta }}</div>
              </div>
            </div>
            <div class="d-flex align-items-center">
              <div class="icon bg-light text-info rounded-circle d-flex justify-content-center align-items-center me-2" style="width:30px; height:30px;">
                <i class="mdi mdi-web"></i>
              </div>
              <div >
                <small class="text-muted">Vergi No</small>
                <div class="fw-semibold text-dark">{{ $tenant->vergiNo ?? '-' }} </div>
              </div>
              <div class="ms-4">
                <small class="text-muted">Vergi Dairesi</small>
                <div class="fw-semibold text-dark">{{ $tenant->vergiDairesi ?? '-'  }}</div>
              </div>
            </div>
          </div>

          <!-- Firma Bilgileri -->
          <div class="col-md-6">
            <h6 class="mb-3"><i class="mdi mdi-office-building me-2"></i>Firma Bilgileri</h6>
            <div class="mb-3 d-flex align-items-center">
              <div class="icon bg-light text-success rounded-circle d-flex justify-content-center align-items-center me-2" style="width:30px; height:30px;">
                <i class="mdi mdi-map-marker"></i>
              </div>
              <div>
                <small class="text-muted">Adres</small>
                <div class="fw-semibold text-dark">{{ $tenant->adres }}</div>
              </div>
            </div>
            <div class="mb-3 d-flex align-items-center">
              <div class="icon bg-light text-warning rounded-circle d-flex justify-content-center align-items-center me-2" style="width:30px; height:30px;">
                <i class="mdi mdi-calendar"></i>
              </div>
              <div>
                <small class="text-muted">Kayıt Tarihi</small>
                <div class="fw-semibold text-dark">{{ $tenant->created_at->format('d.m.Y H:i') }}</div>
              </div>
              <div class="ms-4">
                <small class="text-muted">Bitiş Tarihi</small>
                <div class="fw-semibold text-dark">
                  @if($tenant->subscription_ends_at)
                    {{ $tenant->subscription_ends_at->format('d.m.Y') }}
                  @elseif($tenant->trial_ends_at && $tenant->subscription_status === 'trial')
                    {{ $tenant->trial_ends_at->format('d.m.Y') }} (Deneme)
                  @else
                    {{ $tenant->bitisTarihi ? \Carbon\Carbon::parse($tenant->bitisTarihi)->format('d.m.Y') : 'Belirsiz' }}
                  @endif
                </div>
              </div>
            </div>
            <div class="mb-3 d-flex align-items-center">
              <div class="icon bg-light text-secondary rounded-circle d-flex justify-content-center align-items-center me-2" style="width:30px; height:30px;">
                <i class="mdi mdi-account"></i>
              </div>
              <div>
                <small class="text-muted">İletişim Kişisi</small>
                <div class="fw-semibold text-dark">{{ $tenant->name ?? '-' }}</div>
              </div>
            </div>
            
          </div>
        </div>
      </div>
        <!-- Paket Bilgileri Bölümü -->
        <hr>
        <div class="row g-4">
          <div class="col-12">
            <h6 class="mb-3"><i class="fas fa-box-open me-2"></i>Paket Bilgileri</h6>
            @php
              // Tenant'ın subscription_status'una göre kontrol et
              $currentSubscription = null;
              $isTrialFromTenant = false;
              
              // Tenant aktif mi kontrol et (status = 1) ve abonelik durumu kontrol et
              if ($tenant->status == 1 && in_array($tenant->subscription_status, ['active', 'trial'])) {
                  // Firma aktif ve abonelik durumu geçerliyse
                  if ($tenant->subscription_status === 'trial' && $tenant->trial_ends_at && $tenant->trial_ends_at->isFuture()) {
                      $isTrialFromTenant = true;
                      // Trial durumunda currentSubscription kullan
                      $currentSubscription = $tenant->currentSubscription;
                  } else if ($tenant->subscription_status === 'active' && $tenant->activeSubscription) {
                      // Active subscription var
                      $currentSubscription = $tenant->activeSubscription;
                  } else {
                      // Son aboneliği göster
                      $currentSubscription = $tenant->currentSubscription;
                  }
              } else {
                  // Firma pasif veya abonelik durumu geçersizse de bilgileri gösterebiliriz
                  $currentSubscription = $tenant->currentSubscription;
              }
            @endphp
            
            @if($isTrialFromTenant)
            <!-- Trial durumu için özel kart -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="mb-3 d-flex align-items-center">
                                <div class="icon bg-light text-info rounded-circle d-flex justify-content-center align-items-center me-3" style="width:30px; height:30px;">
                                    <i class="fa fa-clock"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">Deneme Süresi</h5>
                                    <small class="text-muted">Ücretsiz Deneme</small>
                                </div>
                            </div>
                        </div>
                
                        <div class="col-md-2 text-center">
                            <div class="mb-1">
                                <span class="badge bg-warning"><i class="mdi mdi-timer"></i> Deneme Süresi</span>
                            </div>
                            <small class="text-muted">Durum</small>
                        </div>
                        
                        <div class="col-md-2 text-center">
                            <div class="fw-bold text-dark fs-5">Ücretsiz</div>
                            <small class="text-muted">Fiyat</small>
                        </div>
                        
                        <div class="col-md-2 text-center">
                            <div class="fw-bold text-dark">{{ $tenant->trial_ends_at->diffInDays(now()) }} gün</div>
                            <small class="text-muted">Deneme kaldı</small>
                        </div>
                        
                        <div class="col-md-3 text-center">
                            <div class="fw-bold text-dark">{{ $tenant->trial_ends_at->format('d.m.Y') }}</div>
                            <small class="text-muted">Deneme Bitiş</small>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <h6 class="mb-2">Deneme Süresi Paket Özellikleri:</h6>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon bg-light text-primary rounded-circle d-flex justify-content-center align-items-center me-2" style="width:30px; height:30px;">
                                        <i class="mdi mdi-account-multiple"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted">Kullanıcı Sayısı</small>
                                        <div class="fw-semibold text-dark">
                                            @if($tenant->personelSayisi == -1)
                                                Sınırsız
                                            @elseif($tenant->personelSayisi)
                                                {{ $tenant->personelSayisi }}
                                            @else
                                                Belirsiz
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon bg-light text-info rounded-circle d-flex justify-content-center align-items-center me-2" style="width:30px; height:30px;">
                                        <i class="mdi mdi-store"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted">Bayi Sayısı</small>
                                        <div class="fw-semibold text-dark">
                                            @if($tenant->bayiSayisi == -1)
                                                Sınırsız
                                            @elseif($tenant->bayiSayisi)
                                                {{ $tenant->bayiSayisi }}
                                            @else
                                                Belirsiz
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon bg-light text-success rounded-circle d-flex justify-content-center align-items-center me-2" style="width:30px; height:30px;">
                                        <i class="mdi mdi-package-variant"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted">Stok Ürün Sayısı</small>
                                        <div class="fw-semibold text-dark">
                                            @if($tenant->stokSayisi == -1)
                                                Sınırsız
                                            @elseif($tenant->stokSayisi)
                                                {{ $tenant->stokSayisi }}
                                            @else
                                                Belirsiz
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon bg-light text-warning rounded-circle d-flex justify-content-center align-items-center me-2" style="width:30px; height:30px;">
                                        <i class="mdi mdi-handshake"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted">Konsinye Ürün Sayısı</small>
                                        <div class="fw-semibold text-dark">
                                            @if($tenant->konsinyeSayisi == -1)
                                                Sınırsız
                                            @elseif($tenant->konsinyeSayisi)
                                                {{ $tenant->konsinyeSayisi }}
                                            @else
                                                Belirsiz
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @elseif($currentSubscription && $currentSubscription->plansubs)
              <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                  <div class="row align-items-center">
                    <div class="col-md-3">
                      <div class="d-flex align-items-center">
                        <div class="icon bg-light text-primary rounded-circle d-flex justify-content-center align-items-center me-3" style="width:30px; height:30px;">
                          <i class="{{ $currentSubscription->plansubs->icon ?? 'fa fa-box' }}"></i>
                        </div>
                        <div>
                          <h5 class="mb-0">{{ $currentSubscription->plansubs->name }}</h5>
                          <small class="text-muted">{{ $currentSubscription->plansubs->getBillingCycleText() }}</small>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-2 text-center">
                      <div class="mb-1">
                        @if($isTrialFromTenant)
                          <span class="badge bg-warning"><i class="mdi mdi-timer"></i> Deneme Süresi</span>
                        @elseif($tenant->subscription_status === 'active')
                          <span class="badge bg-primary"><i class="mdi mdi-crown"></i> Aktif</span>
                        @elseif($tenant->subscription_status === 'trial')
                          <span class="badge bg-warning"><i class="mdi mdi-timer"></i> Deneme</span>
                        @elseif($tenant->subscription_status === 'expired')
                          <span class="badge bg-secondary"><i class="mdi mdi-clock-alert"></i> Süresi Doldu</span>
                        @elseif($tenant->subscription_status === 'canceled')
                          <span class="badge bg-dark"><i class="mdi mdi-cancel"></i> İptal Edildi</span>
                        @else
                          <span class="badge bg-secondary">{{ ucfirst($tenant->subscription_status) }}</span>
                        @endif
                      </div>
                      <small class="text-muted">Abonelik Durumu</small>
                    </div>
                    
                    <div class="col-md-2 text-center">
                      <div class="fw-bold text-dark fs-5">{{ $currentSubscription->plansubs->getFormattedPrice() }}</div>
                      <small class="text-muted">Fiyat</small>
                    </div>
                    
                    <div class="col-md-2 text-center">
                      @if($tenant->subscription_status === 'trial' && $tenant->trial_ends_at)
                        <div class="fw-bold text-dark">{{ $tenant->trial_ends_at->diffInDays(now()) }} gün</div>
                        <small class="text-muted">Deneme kaldı</small>
                      @elseif($tenant->subscription_status === 'active' && $tenant->subscription_ends_at)
                        <div class="fw-bold text-dark">{{ $tenant->subscription_ends_at->diffInDays(now()) }} gün</div>
                        <small class="text-muted">Süre kaldı</small>
                      @elseif($currentSubscription->ends_at)
                        <div class="fw-bold text-dark">{{ $currentSubscription->ends_at->diffInDays(now()) }} gün</div>
                        <small class="text-muted">Süre kaldı</small>
                      @else
                        <div class="fw-bold text-danger">Belirsiz</div>
                        <small class="text-muted">Durum</small>
                      @endif
                    </div>
                    
                    <div class="col-md-3 text-center">
                      @if($tenant->subscription_status === 'trial' && $tenant->trial_ends_at)
                        <div class="fw-bold text-dark">{{ $tenant->trial_ends_at->format('d.m.Y') }}</div>
                        <small class="text-muted">Deneme Bitiş</small>
                      @elseif($tenant->subscription_status === 'active' && $tenant->subscription_ends_at)
                        <div class="fw-bold text-dark">{{ $tenant->subscription_ends_at->format('d.m.Y') }}</div>
                        <small class="text-muted">Abonelik Bitiş</small>
                      @elseif($currentSubscription->ends_at)
                        <div class="fw-bold text-dark">{{ $currentSubscription->ends_at->format('d.m.Y') }}</div>
                        <small class="text-muted">Bitiş Tarihi</small>
                      @else
                        <div class="fw-bold text-dark">Belirsiz</div>
                        <small class="text-muted">Bitiş Tarihi</small>
                      @endif
                    </div>
                  </div>
                  
                  <!-- Paket Özellikleri -->
                  @if($currentSubscription->plansubs->limits)
                    <div class="mt-3">
                      <h6 class="mb-2">Paket Özellikleri:</h6>
                      <div class="row g-2">
                        @if(isset($currentSubscription->plansubs->limits['users']))
                          <div class="col-md-3">
                            <div class="d-flex align-items-center">
                              <div class="icon bg-light text-primary rounded-circle d-flex justify-content-center align-items-center me-2" style="width:30px; height:30px;">
                                <i class="mdi mdi-account-multiple"></i>
                              </div>
                              <div>
                                <small class="text-muted">Kullanıcı Sayısı</small>
                                <div class="fw-semibold text-dark">
                                  @if($currentSubscription->plansubs->limits['users'] == -1)
                                    Sınırsız
                                  @elseif($currentSubscription->plansubs->limits['users'] == 0)
                                    Yok
                                  @else
                                    {{ number_format($currentSubscription->plansubs->limits['users']) }}
                                  @endif
                                </div>
                              </div>
                            </div>
                          </div>
                        @endif
                        
                        @if(isset($currentSubscription->plansubs->limits['dealers']))
                          <div class="col-md-3">
                            <div class="d-flex align-items-center">
                              <div class="icon bg-light text-info rounded-circle d-flex justify-content-center align-items-center me-2" style="width:30px; height:30px;">
                                <i class="mdi mdi-store"></i>
                              </div>
                              <div>
                                <small class="text-muted">Bayi Sayısı</small>
                                <div class="fw-semibold text-dark">
                                  @if($currentSubscription->plansubs->limits['dealers'] == -1)
                                    Sınırsız
                                  @elseif($currentSubscription->plansubs->limits['dealers'] == 0)
                                    Yok
                                  @else
                                    {{ number_format($currentSubscription->plansubs->limits['dealers']) }}
                                  @endif
                                </div>
                              </div>
                            </div>
                          </div>
                        @endif
                        
                        @if(isset($currentSubscription->plansubs->limits['stocks']))
                          <div class="col-md-3">
                            <div class="d-flex align-items-center">
                              <div class="icon bg-light text-success rounded-circle d-flex justify-content-center align-items-center me-2" style="width:30px; height:30px;">
                                <i class="mdi mdi-package-variant"></i>
                              </div>
                              <div>
                                <small class="text-muted">Stok Ürün Sayısı</small>
                                <div class="fw-semibold text-dark">
                                  @if($currentSubscription->plansubs->limits['stocks'] == -1)
                                    Sınırsız
                                  @elseif($currentSubscription->plansubs->limits['stocks'] == 0)
                                    Yok
                                  @else
                                    {{ number_format($currentSubscription->plansubs->limits['stocks']) }}
                                  @endif
                                </div>
                              </div>
                            </div>
                          </div>
                        @endif
                        
                        @if(isset($currentSubscription->plansubs->limits['konsinye']))
                          <div class="col-md-3">
                            <div class="d-flex align-items-center">
                              <div class="icon bg-light text-warning rounded-circle d-flex justify-content-center align-items-center me-2" style="width:30px; height:30px;">
                                <i class="mdi mdi-handshake"></i>
                              </div>
                              <div>
                                <small class="text-muted">Konsinye Ürün Sayısı</small>
                                <div class="fw-semibold text-dark">
                                  @if($currentSubscription->plansubs->limits['konsinye'] == -1)
                                    Sınırsız
                                  @elseif($currentSubscription->plansubs->limits['konsinye'] == 0)
                                    Yok
                                  @else
                                    {{ number_format($currentSubscription->plansubs->limits['konsinye']) }}
                                  @endif
                                </div>
                              </div>
                            </div>
                          </div>
                        @endif
                        
                        @if(isset($currentSubscription->plansubs->limits['tickets_per_month']))
                          <div class="col-md-3">
                            <div class="d-flex align-items-center">
                              <div class="icon bg-light text-danger rounded-circle d-flex justify-content-center align-items-center me-2" style="width:30px; height:30px;">
                                <i class="mdi mdi-ticket"></i>
                              </div>
                              <div>
                                <small class="text-muted">Aylık Destek Talebi Sayısı</small>
                                <div class="fw-semibold text-dark">
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
                          </div>
                        @endif
                        
                        @if(isset($currentSubscription->plansubs->limits['storage_gb']))
                          <div class="col-md-3">
                            <div class="d-flex align-items-center">
                              <div class="icon bg-light text-secondary rounded-circle d-flex justify-content-center align-items-center me-2" style="width:30px; height:30px;">
                                <i class="mdi mdi-harddisk"></i>
                              </div>
                              <div>
                                <small class="text-muted">Depolama (GB)</small>
                                <div class="fw-semibold text-dark">
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
                          </div>
                        @endif
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            @else
              <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                @if($tenant->status == 0)
                  Bu firma pasif durumdadır ve aktif bir paketi bulunmamaktadır.
                @elseif(!in_array($tenant->subscription_status, ['active', 'trial']))
                  Bu firmanın abonelik durumu: <strong>{{ ucfirst($tenant->subscription_status) }}</strong> - Aktif bir paketi bulunmamaktadır.
                @else
                  Bu firmanın aktif bir paketi bulunmamaktadır.
                @endif
              </div>
            @endif

            <!-- Paket Geçmişi -->
            @if(isset($subscriptionHistory) && $subscriptionHistory->count() > 1)
              <div class="mt-3">
                <h6 class="mb-2">Paket Geçmişi:</h6>
                <div class="table-responsive">
                  <table class="table table-sm">
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
                          <td class="text-dark">{{ $subscription->plansubs->name ?? 'Bilinmeyen' }}</td>
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
                          <td class="text-dark">{{ $subscription->starts_at->format('d.m.Y') }}</td>
                          <td class="text-dark">{{ $subscription->ends_at ? $subscription->ends_at->format('d.m.Y') : '-' }}</td>
                          <td class="text-dark">{{ $subscription->plansubs->getFormattedPrice() ?? '-' }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            @endif
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
        <div class="d-flex gap-2">
          <!-- Genel Durum Değiştirme Butonu -->
          <form action="{{ route('super.admin.tenant.toggle.status', [$tenant->id,$tenant->id]) }}" method="POST" style="display: inline;">
              @csrf
              @if($tenant->status == 1)
                <button type="submit" class="btn btn-danger btn-sm">
                  <i class="mdi mdi-close-circle me-1"></i>Pasif Yap
                </button>
              @else
                <button type="submit" class="btn btn-success btn-sm">
                  <i class="mdi mdi-check-circle me-1"></i>Aktif Et
                </button>
              @endif
          </form>

          <!-- Abonelik Durumu Değiştirme Dropdown -->
          <div class="dropdown">
            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
              <i class="mdi mdi-crown me-1"></i>Abonelik: {{ ucfirst($tenant->subscription_status) }}
            </button>
            <ul class="dropdown-menu">
              @if($tenant->subscription_status !== 'trial')
                <li><a class="dropdown-item subscription-change" href="#" data-status="trial">
                  <i class="mdi mdi-timer me-1"></i>Deneme Yap
                </a></li>
              @endif
              @if($tenant->subscription_status !== 'active')
                <li><a class="dropdown-item subscription-change" href="#" data-status="active">
                  <i class="mdi mdi-crown me-1"></i>Aktif Yap
                </a></li>
              @endif
              @if($tenant->subscription_status !== 'expired')
                <li><a class="dropdown-item subscription-change" href="#" data-status="expired">
                  <i class="mdi mdi-clock-alert me-1"></i>Süresi Doldu Yap
                </a></li>
              @endif
              @if($tenant->subscription_status !== 'canceled')
                <li><a class="dropdown-item subscription-change" href="#" data-status="canceled">
                  <i class="mdi mdi-cancel me-1"></i>İptal Et
                </a></li>
              @endif
            </ul>
          </div>
        </div>
      </div>
  
<script>
$(document).ready(function() {
    // Period tab functionality
    $('.period-tab-compact').on('click', function(e) {
        e.preventDefault();
        var $this = $(this);
        $('.period-tab-compact').removeClass('active');
        $this.addClass('active');
        var targetSelector = $this.data('target');
        $('.period-accordion .collapse').removeClass('show');
        $(targetSelector).addClass('show');
    });

    // Subscription status change functionality
    $('.subscription-change').on('click', function(e) {
        e.preventDefault();
        var newStatus = $(this).data('status');
        var tenantId = '{{ $tenant->id }}';
        
        if(confirm('Abonelik durumunu "' + newStatus + '" olarak değiştirmek istediğinizden emin misiniz?')) {
            $.ajax({
                url: '/super-admin/tenant/' + tenantId + '/change-subscription-status',
                method: 'POST',
                data: {
                    subscription_status: newStatus,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload(); // Sayfayı yenile
                },
                error: function(xhr) {
                    alert('Bir hata oluştu: ' + xhr.responseJSON.message);
                }
            });
        }
    });
});
</script>