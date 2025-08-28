<!-- Tenant Detay Modal -->

      <!-- Header -->
      <div class="card-header bg-gradient bg-primary text-white d-flex align-items-center justify-content-between p-3">
        <div class="d-flex align-items-center">
          <img src="" class="rounded-circle me-3" alt="Logo">
          <div>
            <h5 class="mb-0">{{ $tenant->firma_adi }}</h5>
            <span class="badge bg-success"><i class="mdi mdi-check-circle"></i> Active</span>
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
              <div>
                <small class="text-muted">Telefon</small>
                <div class="fw-semibold">{{ $tenant->tel1 }}</div>
              </div>
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
                <small class="text-muted">Website</small>
                <div class="fw-semibold"><a href="{{ $tenant->website }}" target="_blank">{{ $tenant->website }}</a></div>
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
            </div>
            <div class="mb-3 d-flex align-items-center">
              <div class="icon bg-light text-secondary rounded-circle d-flex justify-content-center align-items-center me-2" style="width:40px; height:40px;">
                <i class="mdi mdi-account"></i>
              </div>
              <div>
                <small class="text-muted">İletişim Kişisi</small>
                <div class="fw-semibold">{{ $tenant->iletisim_kisi ?? '-' }}</div>
              </div>
            </div>
            <div class="d-flex align-items-center">
              <div class="icon bg-light text-dark rounded-circle d-flex justify-content-center align-items-center me-2" style="width:40px; height:40px;">
                <i class="mdi mdi-timer-sand"></i>
              </div>
              <div>
                <small class="text-muted">Son Aktivite</small>
                <div class="fw-semibold">2 saat önce</div>
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
  
