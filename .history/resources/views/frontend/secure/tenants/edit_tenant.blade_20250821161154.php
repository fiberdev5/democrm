<div class="card border-0 shadow-lg rounded-3 overflow-hidden">
  <div class="card-header bg-gradient bg-primary text-white d-flex align-items-center">
    <div class="me-3">
      <div class="rounded-circle bg-white text-primary d-flex justify-content-center align-items-center" 
           style="width:50px; height:50px; font-size:22px;">
        <i class="mdi mdi-domain"></i>
      </div>
    </div>
    <div>
      <h5 class="mb-0">{{ $tenant->firma_adi }}</h5>
      <small class="text-light">
        Bireysel Müşteri
      </small>
    </div>
  </div>
  <div class="card-body p-4">
    <div class="row g-3">
      <div class="col-md-6">
        <div class="d-flex align-items-center">
          <div class="icon bg-light text-primary rounded-circle me-2 d-flex justify-content-center align-items-center" style="width:40px; height:40px;">
            <i class="mdi mdi-phone"></i>
          </div>
          <div>
            <small class="text-muted">Telefon</small>
            <div class="fw-semibold">{{ $tenant->tel1 }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="d-flex align-items-center">
          <div class="icon bg-light text-success rounded-circle me-2 d-flex justify-content-center align-items-center" style="width:40px; height:40px;">
            <i class="mdi mdi-map-marker"></i>
          </div>
          <div>
            <small class="text-muted">Adres</small>
            <div class="fw-semibold">{{ $tenant->adres }}</div>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="d-flex align-items-center">
          <div class="icon bg-light text-warning rounded-circle me-2 d-flex justify-content-center align-items-center" style="width:40px; height:40px;">
            <i class="mdi mdi-calendar"></i>
          </div>
          <div>
            <small class="text-muted">Kayıt Tarihi</small>
            <div class="fw-semibold">{{ $tenant->created_at->format('d.m.Y H:i') }}</div>
          </div>
        </div>
      </div>

      
    </div>
  </div>
  <div class="card-footer bg-light d-flex justify-content-end">
    <button type="button" class="btn btn-sm btn-outline-secondary me-2" data-bs-dismiss="modal">Kapat</button>
  </div>
</div>
