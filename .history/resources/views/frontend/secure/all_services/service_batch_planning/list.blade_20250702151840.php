        <div class="card-header cardBaslik" style="padding: 5px 10px;font-size: 14px">
    @if(!empty($persID))
        @php $selectedPersonnel = $personnel->find($persID) @endphp
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
          <button id="assignBtn" class="btn btn-success btn-sm mt-2 atamaBtn">Atama Yap</button>

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
    $('#assignBtn').on('click', function () {

        const ids = $('.selectService:checked')
                       .map((_,el) => el.value).get();

        if (ids.length === 0) {
            alert('Lütfen en az bir servis seçin');
            return;
        }

        const url = "{{ route('tenant.servis.atama.form', ['tenant' => $firma->id]) }}";

        $('#servisPersonelAtamaModal .modal-body')
            .html('<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Yükleniyor…</div>');
        $('#servisPersonelAtamaModal').modal('show');

        $.get(url, {
            servisidler : ids.join(','),   // boşluk yok → "12,34,56"
            gelenDurum  : '235',
            gidenDurum  : '264'
        })
        .done(html => $('#servisPersonelAtamaModal .modal-body').html(html))
        .fail((xhr,s,e) => {
            console.error(xhr.responseText);
            $('#servisPersonelAtamaModal .modal-body')
              .html('<div class="alert alert-danger">Form yüklenemedi.</div>');
        });
    });
});
</script>