<div class="table-responsive">
  <table class="table table-bordered table-sm mb-0">
    <thead>
      <tr>
        <th>Personel</th>
        <th>Adet</th>
        <th>Tarih</th>
        <th>Detay</th>
      </tr>
    </thead>
    <tbody>
      @foreach($hareketler as $hareket)
        @php
          $personel = optional(\App\Models\User::find($hareket->personel))->name;
        @endphp
        <tr>
          <td>{{ $personel }}</td>
          <td>{{ $hareket->adet }}</td>
          <td>{{ $hareket->created_at->format('d.m.Y') }}</td>
          <td>
            <button type="button" class="btn btn-primary btn-sm detayBtn"
                    data-id="{{ $hareket->id }}"
                    data-personel="{{ $personel }}"
                    data-adet="{{ $hareket->adet }}"
                    data-tarih="{{ $hareket->created_at->format('d.m.Y H:i') }}">
              Detay
            </button>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="modal fade" id="personelStokModal" tabindex="-1" aria-labelledby="personelStokModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="personelStokModalLabel">Personel Stok Detayı</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
      </div>
      <div class="modal-body">
        <p><strong>Personel:</strong> <span id="modalPersonel"></span></p>
        <p><strong>Adet:</strong> <span id="modalAdet"></span></p>
        <p><strong>Tarih:</strong> <span id="modalTarih"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
      </div>
    </div>
  </div>
</div>
$(document).on('click', '.detayBtn', function() {
  $('#modalPersonel').text($(this).data('personel'));
  $('#modalAdet').text($(this).data('adet'));
  $('#modalTarih').text($(this).data('tarih'));
  $('#personelStokModal').modal('show');
});
