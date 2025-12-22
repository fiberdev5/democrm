<div class="card border-0 shadow-sm mb-3">
  <div class="card-body">
    <h5 class="card-title mb-3">{{ $tenant->name }}</h5>
    <div class="row">
      <div class="col-md-6 mb-2">
        <p class="mb-1 text-muted"><i class="mdi mdi-phone me-1"></i> Telefon</p>
        <p class="fw-semibold">{{ $tenant->tel }}</p>
      </div>
      <div class="col-md-6 mb-2">
        <p class="mb-1 text-muted"><i class="mdi mdi-home me-1"></i> Adres</p>
        <p class="fw-semibold">{{ $tenant->address }}</p>
      </div>
      <div class="col-md-6 mb-2">
        <p class="mb-1 text-muted"><i class="mdi mdi-account-badge me-1"></i> Müşteri Tipi</p>
        <p class="fw-semibold">
          {{ $tenant->musteriTipi == 1 ? 'Bireysel' : 'Kurumsal' }}
        </p>
      </div>
      <div class="col-md-6 mb-2">
        <p class="mb-1 text-muted"><i class="mdi mdi-calendar me-1"></i> Kayıt Tarihi</p>
        <p class="fw-semibold">{{ $tenant->created_at->format('d.m.Y H:i') }}</p>
      </div>
    </div>
  </div>
</div>

<div class="text-end">
  <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
    Kapat
  </button>
  <a href="{{ route('tenants.edit', [$tenant->firma_id, $tenant->id]) }}" class="btn btn-sm btn-primary">
    Düzenle
  </a>
</div>
