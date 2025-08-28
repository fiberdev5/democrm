<!-- Company Detail Content - Bu içerik AJAX ile yüklenecek -->
<div class="company-detail-wrapper">
  <!-- Company Header Card -->
  <div class="company-info-card p-4 mb-4 position-relative">
    <div class="row align-items-center">
      <div class="col-auto">
        <div class="info-icon phone text-white">
          <i class="mdi mdi-domain"></i>
        </div>
      </div>
      <div class="col">
        <h4 class="mb-1 fw-bold text-dark">{{ $tenant->firma_adi ?? $tenant->name }}</h4>
        <div class="d-flex align-items-center">
          <span class="status-badge status-active me-2">
            <i class="mdi mdi-check-circle me-1"></i>
            Aktif Müşteri
          </span>
          <small class="text-muted">ID: {{ $tenant->id }}</small>
        </div>
      </div>
      <div class="col-auto">
        <div class="stats-card p-3 text-center">
          <div class="h4 mb-0 fw-bold">{{ $tenant->services_count ?? 0 }}</div>
          <small>Toplam Servis</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Contact Information -->
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="company-info-card p-3 h-100">
        <div class="d-flex align-items-center">
          <div class="info-icon phone text-white me-3">
            <i class="mdi mdi-phone"></i>
          </div>
          <div class="flex-grow-1">
            <small class="text-muted d-block">Telefon Numarası</small>
            <div class="fw-semibold text-dark">{{ $tenant->tel1 ?? $tenant->tel }}</div>
          </div>
          <button class="btn btn-sm btn-outline-primary copy-btn" 
                  onclick="copyToClipboard('{{ $tenant->tel1 ?? $tenant->tel }}', this)">
            <i class="mdi mdi-content-copy"></i>
          </button>
        </div>
      </div>
    </div>

    @if($tenant->email)
    <div class="col-md-6">
      <div class="company-info-card p-3 h-100">
        <div class="d-flex align-items-center">
          <div class="info-icon email text-white me-3">
            <i class="mdi mdi-email"></i>
          </div>
          <div class="flex-grow-1">
            <small class="text-muted d-block">E-posta Adresi</small>
            <div class="fw-semibold text-dark">{{ $tenant->email }}</div>
          </div>
          <button class="btn btn-sm btn-outline-primary copy-btn" 
                  onclick="copyToClipboard('{{ $tenant->email }}', this)">
            <i class="mdi mdi-content-copy"></i>
          </button>
        </div>
      </div>
    </div>
    @endif
  </div>

  <!-- Address and Date Information -->
  <div class="row g-3 mb-4">
    <div class="col-md-8">
      <div class="company-info-card p-3 h-100">
        <div class="d-flex align-items-start">
          <div class="info-icon address text-white me-3">
            <i class="mdi mdi-map-marker"></i>
          </div>
          <div class="flex-grow-1">
            <small class="text-muted d-block">Adres Bilgisi</small>
            <div class="fw-semibold text-dark">{{ $tenant->adres ?? $tenant->address }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="company-info-card p-3 h-100">
        <div class="d-flex align-items-center">
          <div class="info-icon date text-white me-3">
            <i class="mdi mdi-calendar"></i>
          </div>
          <div class="flex-grow-1">
            <small class="text-muted d-block">Kayıt Tarihi</small>
            <div class="fw-semibold text-dark">{{ $tenant->created_at->format('d.m.Y H:i') }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Additional Information (if available) -->
  @if($tenant->website || $tenant->contact_person)
  <div class="row g-3 mb-4">
    @if($tenant->website)
    <div class="col-md-6">
      <div class="company-info-card p-3 h-100">
        <div class="d-flex align-items-center">
          <div class="info-icon web text-white me-3">
            <i class="mdi mdi-web"></i>
          </div>
          <div class="flex-grow-1">
            <small class="text-muted d-block">Website</small>
            <div class="fw-semibold text-primary">{{ $tenant->website }}</div>
          </div>
          <a href="https://{{ $tenant->website }}" target="_blank" 
             class="btn btn-sm btn-outline-primary">
            <i class="mdi mdi-open-in-new"></i>
          </a>
        </div>
      </div>
    </div>
    @endif

    @if($tenant->contact_person)
    <div class="col-md-6">
      <div class="company-info-card p-3 h-100">
        <div class="d-flex align-items-center">
          <div class="info-icon person text-white me-3">
            <i class="mdi mdi-account"></i>
          </div>
          <div class="flex-grow-1">
            <small class="text-muted d-block">İletişim Kişisi</small>
            <div class="fw-semibold text-dark">{{ $tenant->contact_person }}</div>
          </div>
        </div>
      </div>
    </div>
    @endif
  </div>
  @endif

  <!-- Action Buttons -->
  <div class="d-flex justify-content-end gap-2 pt-3 border-top">
    <button type="button" class="btn action-btn btn-secondary" data-bs-dismiss="modal">
      <i class="mdi mdi-close me-1"></i>
      Kapat
    </button>
    <button type="button" class="btn action-btn btn-edit">
      <i class="mdi mdi-pencil me-1"></i>
      Düzenle
    </button>
    <button type="button" class="btn action-btn btn-delete">
      <i class="mdi mdi-delete me-1"></i>
      Sil
    </button>
  </div>
</div>