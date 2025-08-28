<!-- Tenant Detay Modal -->

      <!-- Header -->
      <div class="card-header bg-gradient bg-primary text-white d-flex align-items-center justify-content-between p-3" style="padding: 5px!important;">
        <div class="d-flex align-items-center">
          <svg xmlns="http://www.w3.org/2000/svg" style="margin-left: 10px;"  width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 w-12 h-12 me-3"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"></path><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"></path><path d="M10 6h4"></path><path d="M10 10h4"></path><path d="M10 14h4"></path><path d="M10 18h4"></path></svg>
          <div>
            <h5 class="mb-0">{{ $tenant->firma_adi }}</h5>
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
              <div>
                <small class="text-muted">Vergi No</small>
                <div class="fw-semibold">{{ $tenant->vergiNo }}</div>
              </div>
              <div>
                <small class="text-muted">Vergi Dairesi</small>
                <div class="fw-semibold">{{ $tenant->vergiDairesi }}</div>
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

      <!-- Footer -->
      <div class="card-footer bg-light d-flex justify-content-between align-items-center p-3">
        <div>
          <small class="text-muted">Toplam Servis Sayısı</small>
          <div class="fw-bold text-primary fs-5">{{ $tenant->servis_sayisi ?? 0 }}</div>
        </div>
        <div>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
          <button type="button" class="btn btn-warning">Düzenle</button>
          <button type="button" class="btn btn-danger">Sil</button>
        </div>
      </div>
  
