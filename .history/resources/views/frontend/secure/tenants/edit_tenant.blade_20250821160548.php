<div class="card border-0 shadow-sm">
  <div class="card-header bg-primary text-white">
    <h6 class="mb-0">
      <i class="mdi mdi-office-building me-1"></i> Firma Detayı
    </h6>
  </div>
  <div class="card-body">
    <div class="row mb-2">
      <div class="col-sm-4 text-muted">Ad Soyad:</div>
      <div class="col-sm-8 fw-bold">{{ $tenant->name }}</div>
    </div>

    <div class="row mb-2">
      <div class="col-sm-4 text-muted">Telefon:</div>
      <div class="col-sm-8 fw-bold">{{ $tenant->tel }}</div>
    </div>

    <div class="row mb-2">
      <div class="col-sm-4 text-muted">Adres:</div>
      <div class="col-sm-8">{{ $tenant->address }}</div>
    </div>

    <div class="row mb-2">
      <div class="col-sm-4 text-muted">Durum:</div>
      <div class="col-sm-8">
        @if($tenant->musteriTipi == 1)
          <span class="badge bg-info">Bireysel</span>
        @elseif($tenant->musteriTipi == 2)
          <span class="badge bg-success">Kurumsal</span>
        @else
          <span class="badge bg-secondary">Belirtilmemiş</span>
        @endif
      </div>
    </div>

    <div class="row mb-2">
      <div class="col-sm-4 text-muted">Oluşturma Tarihi:</div>
      <div class="col-sm-8">{{ $tenant->created_at->format('d.m.Y H:i') }}</div>
    </div>
  </div>
  <div class="card-footer text-end">
    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Kapat</button>
  </div>
</div>
