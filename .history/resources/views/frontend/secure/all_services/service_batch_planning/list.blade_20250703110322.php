        <div class="card-header cardBaslik" style="padding: 5px 10px;font-size: 14px">
    @if(!empty($persID))
        @php $selectedPersonnel = $personeller->find($persID) @endphp
        {{ $selectedPersonnel ? $selectedPersonnel->name . ' Servisleri' : 'Personel Servisleri' }}
    @else
        {{ request('planTarih') }} - Servisler
    @endif
    ({{ $services->count() }})
</div>
        <div class="card-body" style="padding: 0!important;height: 462px;overflow: auto;">
            <table class="table table-hover table-striped" id="serviceTable" width="100%" cellspacing="0">
                <thead class="title">
                    <tr>
                        <th>Seç</th>
                        <th>ID</th>
                        <th>Müşteri Adı</th>
                        <th>İlçe</th>
                        <th>Cihaz</th>
                        <th>Arıza</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                    <tr>
                        <td><input type="checkbox" class="selectService" value="{{ $service->id }}"></td>
                        <td >{{ $service->id }}</td>
                        <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;cursor:pointer;" class="personelServisDuzenle" data-id="{{$service->id}}" data-name=""><strong>{{ $service->musteri->adSoyad ?? '-' }}</strong></td>
                        <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;cursor:pointer;" class="personelServisDuzenle" data-id="{{$service->id}}" data-name=""><strong>{{ $service->musteri->ilce }}</strong></td>
                        <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;cursor:pointer;" class="personelServisDuzenle" data-id="{{$service->id}}" data-name=""><strong>{{ $service->markaCihaz->marka ?? '-'}}, {{$service->turCihaz->cihaz ?? '-'}}</strong></td>
                        <td style="vertical-align: middle;font-size: 11px; padding: 3px 10px;cursor:pointer;" class="personelServisDuzenle" data-id="{{$service->id}}" data-name=""><strong>{{ $service->cihazAriza ?? '-' }}</strong></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center">Kayıt bulunamadı</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer" style="padding: 5px 10px;font-size: 14px">
          <div class="form-group row">
              <label for="personel" class="col-md-2 col-form-label">Personel</label>
              <div class="col-md-4">
                <select id="personel" class="form-control personelList">
                  @foreach ($personeller as $pers)
                    @php
                        $count = $personelAtamaSayilari[$pers->user_id] ?? 0;
                    @endphp
                    <option value="{{ $pers->user_id }}">
                        {{ $pers->name }} ({{ $count }})
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <button type="button" class="btn btn-primary btn-block btn-sm personelServisListele">Servisleri Göster</button>
              </div>
          </div>
          @php
            use App\Models\User;
                // 1) gelenDurum dropdown’daki değer
                $gelenDurum = $statuses;

                // 2) varsayılan gidenDurum haritası
                $map = [
                    '237' => 250, '245' => 250,
                    '252' => 251, '246' => 251,
                    '240' => 262,
                    '235-2' => 264, '264' => 264,
                ];
                $gidenDurum = $map[$gelenDurum] ?? 236;   // default teknisyen yönlendir

                // 3) personele özel kural
                $dataPers = null;
                if (!empty($persID)) {
                    $dataPers =  $persID;
                    $perSec = User::find($persID);

                    if ($perSec->hasAnyRole(['Atölye Ustası', 'Atölye Çırak'])) {
                        $gidenDurum = 250;
                    } else {
                        $gidenDurum = 236;
                    }
                    
                }
            @endphp

          <button id="assignBtn" class="btn btn-success btn-sm mt-2 atamaBtn" data-id="{{ $gidenDurum }}" @if($dataPers) data-pers="{{ $dataPers }}" @endif>Atama Yap</button>

        </div>


<div id="servisPersonelAtamaModal" class="modal fade" style="padding-top: 50px;background: rgba(0, 0, 0, 0.50);">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" >Servis Planlama</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Yükleniyor...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade"  data-bs-backdrop="static" tabindex='-1' id="personelServisDuzenleModal">
    <div class="modal-dialog modal-lg" style="width: 980px;">
        <div class="modal-content">
            <div class="modal-header">
        <h6 class="modal-title" id="editCustomerLabel">Servis Bilgileri Düzenle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Yükleniyor...
      </div>
        </div>
    </div>
</div>

<script>
$(function(){
    // Personel servisleri listeleme
    $('.personelServisListele').on('click', function () {
        const persID = $('.personelList').val();
        const firma_id = {{ $firma->id }};

        if(!persID) {
            alert('Lütfen bir personel seçin!');
            return;
        }

        $('.servisListe').html('<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Yükleniyor...</div>');

        $.ajax({
            url: `/${firma_id}/servis-liste-getir/`,
            method: 'GET',
            data: { persID },
            success: function(res) {
                $('.servisListe').html(res);
            },
            error: function() {
                $('.servisListe').html('<div class="alert alert-danger">Liste alınamadı. Lütfen tekrar deneyin.</div>');
            }
        });
    });
});
</script>

<script>
$(function () {

  /* === Atama Yap === */
  $('#assignBtn').click(function () {

      /* Seçili servisler */
      const ids = $('input.selectService:checked').map((_,e)=>e.value).get();
      if (!ids.length) { alert('Servis seçiniz'); return; }

      const servisidler = ids.join(',');
      const gidenDurum  = $(this).data('id');
      const personelID  = $(this).data('pers') || null;
      const gelenDurum  = $('.durumlar').val();
      const tenantID    = {{ $firma->id }};

      /* ----- personel varsa: doğrudan güncelle ----- */
      if (personelID) {
          $.get("{{ route('service.plan.update.form', $firma->id) }}",
                { servisidler, personel: personelID, gidenDurum })
           .done(html => {
              $('#servisPersonelAtamaModal .modal-body').html(html);
              $('#servisPersonelAtamaModal').modal('show');
           });
          return;
      }

      /* ----- personel yok: planlama formu ----- */
      $('#servisPersonelAtamaModal .modal-body')
        .html('<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Form yükleniyor…</div>');
      $('#servisPersonelAtamaModal').modal('show');

      $.get("{{ route('service.plan.form', $firma->id) }}",
            { servisidler, gelenDurum, gidenDurum })
       .done(html => $('#servisPersonelAtamaModal .modal-body').html(html))
       .fail(()  => $('#servisPersonelAtamaModal .modal-body')
            .html('<div class="alert alert-danger">Form yüklenemedi.</div>'));
  });


});
</script>


